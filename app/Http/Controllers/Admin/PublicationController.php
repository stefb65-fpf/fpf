<?php

namespace App\Http\Controllers\Admin;

use App\Concern\Tools;
use App\Exports\FlorilegeExport;
use App\Exports\RoutageFedeExport;
use App\Exports\RoutageFpExport;
use App\Http\Controllers\Controller;
use App\Models\Abonnement;
use App\Models\Adresse;
use App\Models\Club;
use App\Models\Configsaison;
use App\Models\Personne;
use App\Models\Souscription;
use App\Models\Ur;
use App\Models\Utilisateur;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Maatwebsite\Excel\Facades\Excel;

class PublicationController extends Controller
{
    use Tools;
    public function __construct() {
        $this->middleware(['checkLogin', 'adminAccess'])->except(['createEtiquettes', 'createRoutageFede']);
    }

    public function index()
    {
        if (!$this->checkDroit('GESPUB')) {
            return redirect()->route('accueil');
        }
        return view('admin.publications.index');
    }

    public function routageFP()
    {
        if (!$this->checkDroit('GESPUB')) {
            return redirect()->route('accueil');
        }
        $config = Configsaison::where('id', 1)->selectRaw('numeroencours')->first();
        $numeroencours = $config->numeroencours;

        // on cherche le nombre d'abonnements où l'état est à 1
        $nbabos = Abonnement::where('etat', 1)->count();
        $nbclubsAbos = Club::where('numerofinabonnement', '>=', $numeroencours)->count();

        return view('admin.publications.routageFP', compact('nbabos', 'nbclubsAbos', 'numeroencours'));
    }

    public function generateRoutageFp($validate = 0)
    {
        $config = Configsaison::where('id', 1)->selectRaw('numeroencours')->first();
        $numeroencours = $config->numeroencours;

        $fichier = 'routageFP' . $numeroencours . '.xls';
        if (Excel::store(new RoutageFpExport($numeroencours), $fichier, 'xls')) {
            $file_to_download = env('APP_URL').'storage/app/public/xls/'.$fichier;

            if ($validate == 1) {
                // on change l'état des abonnments dont le dernier numéro est celui en cours
                $dataa = array('etat' => 2);
                $datap = array('is_abonne' => 0);
                $abonnements = Abonnement::where('fin', $numeroencours)->get();
                foreach ($abonnements as $abonnement) {
                    $abonnement->update($dataa);

                    $personne= Personne::where('id', $abonnement->personne_id)->first();
                    if ($personne) {
                        $personne->update($datap);
                    }
                }
//                Abonnement::where('fin', $numeroencours)->update($dataa);

                // on met à jour le numéro de fichier
                $datac = array('numeroencours' => $numeroencours + 1);
                Configsaison::where('id', 1)->update($datac);
            }

            return redirect()->route('admin.routage.france_photo')->with('success', "Le fichier Excel a bien été généré et peut être téléchargé en cliquant sur le lien ci-dessous. <br><a href='$file_to_download' target='_blank'>Télécharger le fichier Excel</a>");
        } else {
            return redirect()->route('admin.routage.france_photo')->with('error', "Une erreur est survenue lors de la génération du fichier Excel.");
        }
    }

    public function routageFede()
    {
        if (!$this->checkDroit('GESPUB')) {
            return redirect()->route('accueil');
        }
        $saison = (in_array(date('m'), ['09', '10', '11', '12']) ? date('Y') : date('Y') - 1);
        $nb_clubs = Club::where('statut', 2)->count();
        $nb_individuels = Utilisateur::whereIn('statut', [2, 3])->whereIn('ct', ['7', '8', '9', 'F'])->count();
        $nb_abonnes = Personne::where('is_abonne', 1)->where('is_adherent', 0)->count();
        $nb_ca = Utilisateur::where('ca', 1)->count();
        $nb_adherents_clubs = Utilisateur::whereIn('statut', [2, 3])->whereIn('ct', [2, 3, 4, 5, 6])->count();
        $nb_adherents_prec = Utilisateur::where('statut', 0)->where('urs_id', '<>', 0)->where('saison', $saison)->count();
        $ce = Utilisateur::join('fonctionsutilisateurs', 'fonctionsutilisateurs.utilisateurs_id', '=', 'utilisateurs.id')
            ->join('fonctions', 'fonctions.id', '=', 'fonctionsutilisateurs.fonctions_id')
            ->where('fonctions.ce', 1)
            ->selectRaw('DISTINCT utilisateurs.id')
            ->get();
        $nb_ce = sizeof($ce);
        $urs = Ur::orderBy('id')->get();
        return view('admin.publications.routageFede',
            compact('nb_clubs', 'nb_individuels', 'nb_ca', 'nb_ce', 'urs', 'nb_abonnes', 'nb_adherents_clubs', 'nb_adherents_prec'));
    }

    public function etiquettes()
    {
        if (!$this->checkDroit('GESPUB')) {
            return redirect()->route('accueil');
        }
        $nb_clubs = Club::where('statut', 2)->count();
        $nb_individuels = Utilisateur::whereIn('statut', [2, 3])->whereIn('ct', ['7', '8', '9', 'F'])->count();
        $nb_ca = Utilisateur::where('ca', 1)->count();
        $ce = Utilisateur::join('fonctionsutilisateurs', 'fonctionsutilisateurs.utilisateurs_id', '=', 'utilisateurs.id')
            ->join('fonctions', 'fonctions.id', '=', 'fonctionsutilisateurs.fonctions_id')
            ->where('fonctions.ce', 1)
            ->selectRaw('DISTINCT utilisateurs.id')
            ->get();
        $nb_ce = sizeof($ce);
        $urs = Ur::orderBy('id')->get();
        return view('admin.publications.etiquettes', compact('nb_clubs', 'nb_individuels', 'nb_ca', 'nb_ce', 'urs'));
    }

    public function createEtiquettes(Request $request) {
        $ref = $request->ref;
        switch ($ref) {
//            case 0 : // clubs
//                $etiquettes = $this->getEtiquettesClub();
//                break;
            case 1 : // individuels
                $etiquettes = $this->getIndividuels();
                break;
            case 2 : // CA
                $etiquettes = $this->getCa();
                break;
            case 3 : // CE
                $etiquettes = $this->getCe();
                break;
            case 4: // présidents d'ur
                $etiquettes = $this->getPresidentsUr();
                break;
            case 5 : // contacts club
                $urs_id = $request->ur;
                $etiquettes = $this->getContactsClub($urs_id);
                break;
            default :
                $etiquettes = [];
                break;
        }
        if (sizeof($etiquettes) == 0) {
            return new JsonResponse(['error' => 'Aucune étiquette à imprimer'], 400);
        } else {
            $name = 'etiquettes_'.date('YmdHis').'.pdf';
            $pdf = App::make('dompdf.wrapper');
            $pdf->loadView('pdf.etiquettes', compact('etiquettes'))
                ->setWarnings(false)
                ->setPaper('a4', 'portrait')
                ->save(storage_path().'/app/public/uploads/etiquettes/'.$name);

            return new JsonResponse(['file' => $name], 200);
        }

    }

    public function createRoutageFede(Request $request) {
        $tab_personnes = array();
        foreach ($request->tab as $ref) {
            $personnes = [];
            $utilisateurs = [];
            switch ($ref) {
                case 0 : // individuels
                    $utilisateurs = $this->getIndividuels();
                    break;
                case 1 : // adhérents club
                    $utilisateurs = $this->getAdherentsClub();
                    break;
                case 2 : // adhérents non renouvelés n-1
                    $utilisateurs = $this->getAdherentsPrec();
                    break;
                case 3 : // abonnés seuls
                    $personnes = $this->getAbonnesSeuls();
                    break;
                case 4 : // CA
                    $utilisateurs = $this->getCa();
                    break;
                case 5 : // CE
                    $utilisateurs = $this->getCe();
                    break;
                case 6: // présidents d'ur
                    $utilisateurs = $this->getPresidentsUr();
                    break;
                case 7 : // contacts club
                    $urs_id = $request->ur;
                    $utilisateurs = $this->getContactsClub($urs_id);
                    break;
                default : break;
            }

            if ($ref == 3) {
                foreach ($personnes as $personne) {
                    $personne->urs_id = '';
                    $personne->clubs_id = '';
                    $personne->identifiant = '';
                    $personne->nom_club = '';
                    $personne->nunero_club = '';
                    $tab_personnes[] = $personne;
                }
            } else {
                foreach ($utilisateurs as $utilisateur) {
                    $personne = Personne::where('id', $utilisateur->personne_id)->first();
                    if ($personne) {
                        $personne->urs_id = $utilisateur->urs_id;
                        $personne->clubs_id = $utilisateur->clubs_id;
                        $personne->identifiant = $utilisateur->identifiant;
                        if ($utilisateur->clubs_id) {
                            $club = Club::where('id', $utilisateur->clubs_id)->first();
                            if ($club) {
                                $personne->nom_club = $club->nom;
                                $personne->nunero_club = $club->numero;
                            }
                        } else {
                            $personne->nom_club = '';
                            $personne->nunero_club = '';
                        }
                        $tab_personnes[] = $personne;
                    }
                }
            }
        }
        foreach ($tab_personnes as $personne) {
            $adresse = Adresse::join('adresse_personne', 'adresse_personne.adresse_id', '=', 'adresses.id')
                ->where('adresse_personne.personne_id', $personne->id)
                ->orderByDesc('adresse_personne.defaut')
                ->selectRaw('adresses.libelle1, adresses.libelle2, adresses.codepostal, adresses.ville, adresses.pays')
                ->first();
            if (!$adresse) {
                $adresse = new Adresse();
                $adresse->libelle1 = '5 rue Jules Vallès';
                $adresse->libelle2 = '';
                $adresse->codepostal = '75011';
                $adresse->ville = 'PARIS';
                $adresse->pays = 'FRANCE';
            }
            $personne->adresse = $adresse;
        }
        $fichier = 'routage_' . date('YmdHis') . '.xls';
        if (Excel::store(new RoutageFedeExport($tab_personnes), $fichier, 'xls')) {
            $file_to_download = env('APP_URL') . 'storage/app/public/xls/' . $fichier;
            return new JsonResponse(['file' => $file_to_download], 200);
        } else {
            return new JsonResponse(['erreur' => 'impossible de récupérer le fichier'], 400);
        }

    }

    public function florilege() {
        if (!$this->checkDroit('GESPUB')) {
            return redirect()->route('accueil');
        }
        // on rcupère le nb d'exemplaires à imprimer
        $total = Souscription::where('statut', 1)->selectRaw('SUM(nbexemplaires) as nb')->first();
        $nb_exemplaires = $total->nb;
        $souscriptions = Souscription::orderByDesc('id')->where('statut', 1)->paginate(100);
        foreach ($souscriptions as $souscription) {
            if ($souscription->personne_id) {
                $personne = Personne::where('id', $souscription->personne_id)->first();
                if ($personne) {
                    $souscription->destinataire = $personne->prenom.' '.$personne->nom;
                }
            } else {
                $club = Club::where('id', $souscription->clubs_id)->first();
                if ($club) {
                    $souscription->destinataire = $club->nom;
                }
            }
        }
        return view('admin.publications.florilege', compact('souscriptions', 'nb_exemplaires'));
    }


    public function generateSouscriptionsList() {
        $souscriptions = Souscription::orderBy('id')->where('statut', 1)->get();
        foreach ($souscriptions as $souscription) {
            $personne = null; $club = null;
            if ($souscription->personne_id) {
                $personne = Personne::where('id', $souscription->personne_id)->first();
            } else {
                $club = Club::where('id', $souscription->clubs_id)->first();
                if ($club) {
                    $user = Utilisateur::join('fonctionsutilisateurs', 'fonctionsutilisateurs.utilisateurs_id', '=', 'utilisateurs.id')
                        ->where('fonctionsutilisateurs.fonctions_id', 97)
                        ->where('utilisateurs.clubs_id', $club->id)
                        ->first();
                    if ($user) {
                        $personne = Personne::where('id', $user->personne_id)->first();
                    }
                }
            }
            $souscription->destinataire = $personne;
            $souscription->club = $club;

        }
        $fichier = 'florilege_' . date('YmdHis') . '.xls';
        if (Excel::store(new FlorilegeExport($souscriptions), $fichier, 'xls')) {
            $file_to_download = env('APP_URL') . 'storage/app/public/xls/' . $fichier;
            return new JsonResponse(['file' => $file_to_download], 200);
        } else {
            return new JsonResponse(['erreur' => 'impossible de récupérer le fichier'], 400);
        }
    }

    protected function getIndividuels() {
        // on récupère les individuels
        return Utilisateur::whereIn('statut', [2, 3])->whereIn('ct', ['7', '8', '9', 'F'])->whereNotNull('utilisateurs.personne_id')->orderBy('identifiant')->get();
    }

    protected function getAdherentsClub() {
        // on récupère les adhérents non renouvelés n-1
        $saison = (in_array(date('m'), ['09', '10', '11', '12']) ? date('Y') : date('Y') - 1);
        return Utilisateur::where('statut', 0)->where('urs_id', '<>', 0)->where('saison', $saison)->orderBy('identifiant')->get();
    }

    protected function getAdherentsPrec() {
        // on récupère les adhérents de club
        return Utilisateur::whereIn('statut', [2, 3])->whereIn('ct', [2, 3, 4, 5, 6])->whereNotNull('utilisateurs.personne_id')->orderBy('identifiant')->get();
    }

    protected function getAbonnesSeuls() {
        // on récupère les abonnés seuls
        return Personne::where('is_abonne', 1)->where('is_adherent', 0)->get();
    }
    protected function getCa() {
        // on récupère les membres du CA
        return Utilisateur::where('ca', 1)->whereNotNull('utilisateurs.personne_id')->orderBy('identifiant')->get();
    }

    protected function getCe() {
        // on récupère les membres du CE
        return Utilisateur::join('fonctionsutilisateurs', 'fonctionsutilisateurs.utilisateurs_id', '=', 'utilisateurs.id')
            ->join('fonctions', 'fonctions.id', '=', 'fonctionsutilisateurs.fonctions_id')
            ->where('fonctions.ce', 1)
            ->whereNotNull('utilisateurs.personne_id')
            ->selectRaw('DISTINCT utilisateurs.id, utilisateurs.*')
            ->orderBy('utilisateurs.identifiant')
            ->get();
    }

    protected function getPresidentsUr() {
        // on récupère les présidents d'ur
        return Utilisateur::join('fonctionsutilisateurs', 'fonctionsutilisateurs.utilisateurs_id', '=', 'utilisateurs.id')
            ->join('fonctions', 'fonctions.id', '=', 'fonctionsutilisateurs.fonctions_id')
            ->where('fonctions.id', 57)
            ->whereNotNull('utilisateurs.personne_id')
            ->selectRaw('DISTINCT utilisateurs.id, utilisateurs.*')
            ->orderBy('utilisateurs.urs_id')
            ->get();
    }

    protected function getContactsClub($urs_id) {
        // on récupère les contacts des clubs
        $query =  Utilisateur::join('clubs', 'utilisateurs.clubs_id', '=', 'clubs.id')
            ->join('fonctionsutilisateurs', 'fonctionsutilisateurs.utilisateurs_id', '=', 'utilisateurs.id')
            ->join('fonctions', 'fonctions.id', '=', 'fonctionsutilisateurs.fonctions_id')
            ->where('clubs.statut', 2)
            ->whereNotNull('utilisateurs.personne_id')
            ->where('fonctions.id', 97);
        if ($urs_id != 0) {
            $query->where('clubs.urs_id', $urs_id);
        }
        return  $query->selectRaw('DISTINCT utilisateurs.id, utilisateurs.*')->orderBy('identifiant')->get();
    }


}
