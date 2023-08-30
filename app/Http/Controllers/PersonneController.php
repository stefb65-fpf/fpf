<?php

namespace App\Http\Controllers;

use App\Concern\Api;
use App\Concern\Hash;
use App\Concern\Invoice;
use App\Concern\Tools;
use App\Http\Requests\AdressesRequest;
use App\Http\Requests\CiviliteRequest;
use App\Http\Requests\EmailRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Mail\SendAnonymisationEmail;
use App\Mail\SendEmailChangeEmailAddress;
use App\Mail\SendEmailModifiedPassword;
use App\Models\Adresse;
use App\Models\Configsaison;
use App\Models\Historique;
use App\Models\Historiquemail;
use App\Models\Pays;
use App\Models\Personne;
use App\Models\Reglement;
use App\Models\Souscription;
use App\Models\Utilisateur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class PersonneController extends Controller
{
    use Tools;
    use Hash;
    use Api;
    use Invoice;

    public function __construct()
    {
        $this->middleware(['checkLogin', 'userAccessOnly']);
    }

    // affichage de la page mon compte (accueil pour toute personne non adminsitrative)
    public function accueil()
    {
        $personne = session()->get('user');
        $cartes = session()->get('cartes');

        $tarif = 0;
        $tarif_supp = 0;
        $ct = 0;
        if (in_array($personne->is_adherent, [1, 2])) {
            // on récupère le montant dur enouvellement pour l'année en cours:
            list($tarif, $tarif_supp, $ct) = $this->getTarifAdhesion($personne->datenaissance);
        }
        return view('personnes.mon_compte', compact('personne', 'tarif', 'tarif_supp', 'ct', 'cartes'));
    }

    // affichage des informations liées à la personne connectée
    public function monProfil()
    {
        $user = session()->get('user');
        $personne = Personne::where('id', $user->id)->first();
        $nbadresses = sizeof($personne->adresses);
        if ($personne->blacklist_date) {
            $personne->blacklist_date = date('d/m/Y', (strtotime($personne->blacklist_date)));
        } else {
            $personne->blacklist_date = null;
        }
        if (!$nbadresses) {
            $personne->adresses[0] = [];
        } elseif ($nbadresses == 1) {
            $personne->adresses[1] = [];
        }

        foreach ($personne->adresses as $adresse) {
            if ($adresse) {
                if ($adresse->pays) {
                    $country = Pays::where('nom', strtoupper(strtolower($adresse->pays)))->first();
                    $adresse->indicatif = $country->indicatif;
                } else {
                    $adresse->indicatif = "";
                }
            }
        }

        $countries = Pays::all();
        return view('personnes.mon_profil', compact('personne', 'nbadresses', 'countries'));
    }

    // affcihage de l'historique des actions effectuées par la personne
    public function mesActions()
    {
        $user = session()->get('user');
        $historiques = Historique::where('personne_id', $user->id)->orderByDesc('created_at')->paginate(50);
        return view('personnes.mes_actions', compact('historiques'));
    }

    public function mesFormations()
    {
        return view('personnes.mes_formations');
    }

    // affichage de l'historique des meils envoyés à la personne
    public function mesMails()
    {
        $user = session()->get('user');
        $mails = Historiquemail::where('personne_id', $user->id)->orderByDesc('created_at')->paginate(50);

        foreach ($mails as $mail) {
                $mail->contenu = $this->get_string_between($mail->contenu, '<main>', '</main>');
        }

        return view('personnes.mes_mails', compact('mails'));
    }

    // mise à jour du mot de passe à partir de la page profil
    public function updatePassword(ResetPasswordRequest $request, Personne $personne)
    {
        $datap = array('password' => $this->encodePwd($request->password), 'secure_code' => null);
        $personne->update($datap);

        $this->updateWpUser($personne->email, $request->password);

//        $request->session()->put('user', $personne);
        $this->registerAction($personne->id, 4, "Modification de votre mot de passe");

        $mailSent = Mail::to($personne->email)->send(new SendEmailModifiedPassword());
        $htmlContent = $mailSent->getOriginalMessage()->getHtmlBody();
        $mail = new \stdClass();
        $mail->titre = "Confirmation de modification de mot de passe";
        $mail->destinataire = $personne->email;
        $mail->contenu = $htmlContent;

        $this->registerMail($personne->id, $mail);
        return redirect()->route('mon-profil')->with('success', "Votre mot de passe a été modifié avec succès");
    }

    // mise à jour de l'email à partir de la page profil
    public function updateEmail(EmailRequest $request, Personne $personne)
    {
        //on crée un code sécurisé unique et on l'enregistre dans le champ secure_code de l'utilisateur
        $crypt = $this->encodeShortReinit();
        $personne->secure_code = $crypt;
        $personne->save();

        //on enregistre le nouvel email provisoire
        $datap = array('nouvel_email' => $request->email);
        $personne->update($datap);
//        $request->session()->put('user', $personne);

        //on enregistre l'action dans l'historique
        $this->registerAction($personne->id, 4, "Demande de modification d'email");

        // on envoie un mail à l'utilisateur avec le lien de confirmation de modification de l'adresse amil
        $link = "https://fpf-new.federation-photo.fr/changeEmail/" . $crypt;
        $mailSent = Mail::to($personne->email)->send(new SendEmailChangeEmailAddress($link));

        //on enregistre le mail dans l'historique des mails
        $htmlContent = $mailSent->getOriginalMessage()->getHtmlBody();
        $mail = new \stdClass();
        $mail->titre = "Demande de modification de votre adresse email";
        $mail->destinataire = $personne->email;
        $mail->contenu = $htmlContent;
        $this->registerMail($personne->id, $mail);

        return redirect()->route('mon-profil')->with('success', "Nous avons pris en compte votre demande de modification d'email. Pour valider ce changement, rendez-vous sur votre boîte mail actuelle");
    }

    // mise à jour de la partie Civilité à partir de la page profil
    public function updateCivilite(CiviliteRequest $request, Personne $personne)
    {
        // on récupère l'adresse de la personne pour formatter son téléphone
        $datap = array('nom' => $request->nom, 'prenom' => $request->prenom, 'datenaissance' => $request->datenaissance);
        $pays = Pays::where('nom', $personne->adresses[0]->pays)->first();
        if ($pays) {
            $phone_mobile = $this->format_mobile_for_base($request->phone_mobile, $pays->indicatif);
        } else {
            $phone_mobile = $this->format_mobile_for_base($request->phone_mobile);
        }
        if ($phone_mobile == -1) {
            return redirect()->route('mon-profil')->with('error', "Le numéro de téléphone saisi est incorrect.");
        }
        $datap['phone_mobile'] = $phone_mobile;
        $personne->update($datap);
        $this->registerAction($personne->id, 4, "Modification de vos informations de civilité");
        return redirect()->route('mon-profil')->with('success', "Vos informations de civilité ont été modifiées avec succès");
    }

    // mise à jour de l'adresse à partir de la page profil
    public function updateAdresse(AdressesRequest $request, Personne $personne, $form)
    {
        $selected_pays = Pays::where('id', $request->pays)->first();
//        dd($request);
        $indicatif = $selected_pays->indicatif;
        $datap_adresse = $request->all();
        unset($datap_adresse['_token']);
        unset($datap_adresse['_method']);
        unset($datap_adresse['enableBtn']);
        $datap_adresse['pays'] = $selected_pays->nom;
        if ($datap_adresse["telephonedomicile"]) {
            $datap_adresse["telephonedomicile"] = str_replace(" ", "", $datap_adresse["telephonedomicile"]);
            $datap_adresse["telephonedomicile"] = ltrim($datap_adresse["telephonedomicile"], '0');
            $datap_adresse["telephonedomicile"] = '+' . $indicatif . '.' . $datap_adresse["telephonedomicile"];
            $datap_adresse["telephonedomicile"] = '+' . $indicatif . '.' . $datap_adresse["telephonedomicile"];
        }
        if ($form == 1) {//$form = 1, c'est le formulaire d'adresse defaut / facturation
            if (!sizeof($personne->adresses)) { //la personne n'a aucune adresse en base. On en crée une.
                $new_adress = Adresse::create($datap_adresse);
                if ($new_adress) {
                    // on ajoute une ligne à la table pivot adresse_personne (le 'defaut' est à 1 pour "adresse de facturation"):

                    $data_ap = array('adresse_id' => $new_adress->id, 'personne_id' => $personne->id, 'defaut' => 1);
                    DB::table('adresse_personne')->insert($data_ap);
                }
            } else { //la personne a au moins une adresse en base. On met à jour l'adresse par defaut.
                $personne->adresses[0]->update($datap_adresse);
            }
        } else { //$form = 2, c'est le formulaire d'adresse de livraison
            if (sizeof($personne->adresses) == 2) { //la personne a déjà deux adresses (donc une de livraison). On la met à jour:
                $personne->adresses[1]->update($datap_adresse);
            } else { //la personne n'a pas encore d'adresse de livraison. On la crée:
                $new_adress = Adresse::create($datap_adresse);
                if ($new_adress) {
                    // on ajoute une ligne à la table pivot adresse_personne (le 'defaut' est à 2 pour "adresse de livraison"):
                    $data_ap = array('adresse_id' => $new_adress->id, 'personne_id' => $personne->id, 'defaut' => 2);
                    DB::table('adresse_personne')->insert($data_ap);
                }
            }
        }
        $this->registerAction($personne->id, 4, "Modification de vos adresses");
        return redirect()->route('mon-profil')->with('success', "Votre adresse a été modifiée avec succès");
    }

    public function cancelPaiementRenew(Request $request)
    {
        $reglement = Reglement::where('monext_token', $request->token)->first();
        if ($reglement) {
            $reglementutilisateur = DB::table('reglementsutilisateurs')->where('reglements_id', $reglement->id)->first();
            $reglement->delete();
            if ($reglementutilisateur) {
                $utilisateur = Utilisateur::where('id', $reglementutilisateur->utilisateurs_id)->first();
                DB::table('reglementsutilisateurs')->where('reglements_id', $reglement->id)->delete();
                if ($utilisateur) {
                    $datau = array('statut' => 0);
                    $utilisateur->update($datau);
                }
            }
        }
        return redirect()->route('accueil')->with('error', "Votre paiement a été annulé");
    }

    public function validationPaiementCarteRenew(Request $request)
    {
        $result = $this->getMonextResult($request->token);
        $code = 'ko';
        if ($result['code'] == '00000' && $result['message'] == 'ACCEPTED') {
            $reglement = Reglement::where('monext_token', $request->token)->first();
            if ($reglement) {
                if ($this->saveReglement($reglement)) {
                    $data = array('statut' => 1, 'numerocheque' => 'Monext ' . $reglement->monext_token, 'dateenregistrement' => date('Y-m-d H:i:s'),
                        'monext_token' => null, 'monext_link' => null);
                    $reglement->update($data);


                    // on récupère l'utilisateur concerné par le règlement
                    $reglementutilisateur = DB::table('reglementsutilisateurs')->where('reglements_id', $reglement->id)->first();
                    if ($reglementutilisateur) {
                        $utilisateur = Utilisateur::where('id', $reglementutilisateur->utilisateurs_id)->first();
                        if ($utilisateur) {
                            $personne = Personne::where('id', $utilisateur->personne_id)->first();
                            if ($personne) {
                                $personne = $this->getSituation($personne);
                                list($menu, $cartes) = $this->getMenu($personne);
                                $request->session()->put('user', $personne);
                                $request->session()->put('menu', $menu);
                                $request->session()->put('cartes', $cartes);
                                $description = "Renouvellement adhésion FPF référence " . $reglement->reference;
                                $datai = ['reference' => $reglement->reference, 'description' => $description, 'montant' => $reglement->montant, 'personne_id' => $personne->id];
                                $this->createAndSendInvoice($datai);
                            }
                        }
                    }
                    return redirect()->route('accueil')->with('success', "Votre paiement a été accepté et votre renouvellement a été enregistré avec succès.");
                }
            }
            return redirect()->route('accueil')->with('error', "Votre paiement a été validé mais une erreur est survenue lors de l'enregistrement de votre renouvellement. Veuillez contacter le secrétariat");
        }
        return redirect()->route('accueil')->with('error', "Votre paiement n'a pas été validé");
    }

    public function florilege()
    {
        $personne = session()->get('user');
        // on vérifie que le florilège est bien dispo à la commande
        $config = Configsaison::where('id', 1)->selectRaw('prixflorilegefrance, prixflorilegeetranger, datedebutflorilege, datefinflorilege')->first();
        if (!(date('Y-m-d') >= $config->datedebutflorilege && date('Y-m-d') <= $config->datefinflorilege)) {
            return redirect()->route('accueil');
        }
        return view('personnes.florilege', compact('config', 'personne'));
    }

    public function cancelPaiementFlorilege(Request $request)
    {
        $souscription = Souscription::where('monext_token', $request->token)->first();
        if ($souscription) {
            $souscription->delete();
        }
        return redirect()->route('florilege')->with('error', "Votre paiement a été annulé");
    }

    public function validationPaiementCarteFlorilege(Request $request)
    {
        $result = $this->getMonextResult($request->token);
        if ($result['code'] == '00000' && $result['message'] == 'ACCEPTED') {
            $souscription = Souscription::where('monext_token', $request->token)->first();
            if ($souscription) {
                // on enregistre la validation de la souscription
                $data = ['statut' => 1, 'monext_token' => null, 'monext_link' => null, 'ref_reglement' => 'Monext ' . $souscription->monext_token];
                $souscription->update($data);

                if ($souscription->personne_id) {
                    $description = "Commande $souscription->reference pour $souscription->nbexemplaires numéros Florilège";
                    $datai = ['reference' => $souscription->reference, 'description' => $description, 'montant' => $souscription->montanttotal, 'personne_id' => $souscription->personne_id];
                    $this->createAndSendInvoice($datai);
                } else {
                    if ($souscription->clubs_id) {
                        $description = "Commande $souscription->reference pour $souscription->nbexemplaires numéros Florilège";
                        $datai = ['reference' => $souscription->reference, 'description' => $description, 'montant' => $souscription->montanttotal, 'club_id' => $souscription->clubs_id];
                        $this->createAndSendInvoice($datai);
                    }
                }

                return redirect()->route('florilege')->with('success', "Votre paiement a été accepté et votre souscription a été enregistrée avec succès. Vous allez recevoir un mail récapitulatif de votre souscription.");
            }
            return redirect()->route('florilege')->with('error', "Votre paiement a été validé mais une erreur est survenue lors de l'enregistrement de votre souscription. Veuillez contacter le secrétariat");
        }
        return redirect()->route('florilege')->with('error', "Votre paiement n'a pas été validé");
    }

    public function factures()
    {
        $personne = session()->get('user');
        $invoices = \App\Models\Invoice::where('personne_id', $personne->id)->orderByDesc('created_at')->get();
        foreach ($invoices as $invoice) {
            list($tmp, $path) = explode('htdocs', $invoice->getStorageDir());
            $path .= '/' . $invoice->numero . '.pdf';
            $invoice->path = $path;
        }

        return view('personnes.factures', compact('invoices'));
    }

    public function anonymize()
    {
        $user = session()->get('user');
        $personne = Personne::where('id', $user->id)->first();
        try {
            DB::beginTransaction();
            $prev_email = $personne->email;
            $email = uniqid('anonyme_') . '@federationphoto.fr';
            $datau = array('nom' => 'anonyme', 'prenom' => 'anonyme', 'courriel' => $email);
            foreach ($personne->utilisateurs as $utilisateur) {
                $utilisateur->update($datau);
            }

            $data = array('nom' => 'anonyme', 'prenom' => 'anonyme', 'email' => $email, 'phone_mobile' => '', 'news' => 0, 'is_adherent' => 0, 'is_abonne' => 0,
                'is_formateur' => 0, 'is_administratif' => 0);
            $personne->update($data);

            $prev_email = 'contact@envolinfo.com'; // TODO : à enlever
            $mailSent = Mail::to($prev_email)->send(new SendAnonymisationEmail());
            $htmlContent = $mailSent->getOriginalMessage()->getHtmlBody();


            $mail = new \stdClass();
            $mail->titre = "Anonymisation des données personnelles";
            $mail->destinataire = $email;
            $mail->contenu = $htmlContent;
            $this->registerMail($personne->id, $mail);
            DB::commit();

            return redirect()->route('logout')->with('success', "Vos données ont été anonymisées avec succès");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('logout')->with('error', "Un problème est survenu lors de l'anonymisation");
        }
    }
}
