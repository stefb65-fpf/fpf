<?php

namespace App\Http\Controllers\Api;

use App\Concern\Invoice;
use App\Concern\Tools;
use App\Http\Controllers\Controller;
use App\Mail\RelanceReglement;
use App\Mail\SendRenouvellementMail;
use App\Models\Abonnement;
use App\Models\Club;
use App\Models\Configsaison;
use App\Models\Personne;
use App\Models\Photo;
use App\Models\Reglement;
use App\Models\Rphoto;
use App\Models\Utilisateur;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class ReglementController extends Controller
{
    use Tools;
    use Invoice;

    public function validReglement(Request $request) {
        $reglement = Reglement::where('id', $request->ref)->where('statut', 0)->first();
        if(!$reglement) {
            return new JsonResponse(['erreur' => 'règlement introubale'], 400);
        }

        if ($this->saveReglement($reglement)) {
            // on met à jour le règlement
            $datar = array('statut' => 1, 'numerocheque' => $request->infos, 'dateenregistrement' => date('Y-m-d H:i:s'));
            $reglement->update($datar);

            if ($reglement->clubs_id) {
                $club = Club::where('id', $reglement->clubs_id)->first();
                if ($club) {
                    if ($club->creance > 0) {
                        $montant_creance_utilisee = $reglement->montant - $reglement->montant_paye;
                        $new_creance = $club->creance - $montant_creance_utilisee > 0 ? $club->creance - $montant_creance_utilisee : 0;
                        $club->update(['creance' => $new_creance]);
                    }
                }
            }

            $this->saveInvoiceForReglement($reglement);

            return new JsonResponse(['success' => "Le règlement a été validé"], 200);
        } else {
            return new JsonResponse(['erreur' => "Une erreur est survenue lors de la validation du règlement"], 400);
        }

    }

    public function editCartes() {
        $utilisateurs = Utilisateur::where('statut', 2)->whereNotNull('personne_id')->where('urs_id', '<>', 0)->orderBy('clubs_id')->get();
        $tab_cartes = []; $table_vignettes = [];
        $tab_individuels = []; $tab_clubs = [];
        foreach ($utilisateurs as $utilisateur) {
            if (in_array($utilisateur->nb_cases_carte, [0,3])) {
                $tab_cartes[] = $utilisateur;
                $utilisateur->type_edition = 'carte';
            } else {
                $table_vignettes[] = $utilisateur;
                $utilisateur->type_edition = 'vignette';
            }
            if ($utilisateur->clubs_id == null) {
                // individuel
                $tab_individuels[] = $utilisateur;
            } else {
                // club
                if ($utilisateur->type_edition == 'carte') {
                    $tab_clubs[$utilisateur->clubs_id]['cartes'][] = $utilisateur;
                } else {
                    $tab_clubs[$utilisateur->clubs_id]['vignettes'][] = $utilisateur;
                }

            }
        }
        foreach ($tab_clubs as $k => $tab_club) {
            // on recherche le contact du club
            $club = Club::where('id', $k)->selectRaw('nom, numero')->first();
            $tab_clubs[$k]['club'] = $club;

            $contact = Utilisateur::join('fonctionsutilisateurs', 'fonctionsutilisateurs.utilisateurs_id', '=', 'utilisateurs.id')
                ->where('fonctionsutilisateurs.fonctions_id', 97)
                ->where('utilisateurs.clubs_id', $k)
                ->whereNotNull('utilisateurs.personne_id')
                ->first();
            if ($contact) {
                $tab_clubs[$k]['contact'] = $contact;
            }
        }

        $dir = storage_path().'/app/public/cartes/'.date('Y').'/'.date('m');
        if (!is_dir($dir)) mkdir($dir, 0777, true);

        $file_cartes = '';
        $rand = rand(100000, 999999);
        $name = 'cartes_'.$rand.'_'.date('YmdHis').'.pdf';
        if (sizeof($tab_cartes) > 0) {
            $pdf = App::make('dompdf.wrapper');
            $pdf->loadView('pdf.cartes', compact('tab_cartes'))
                ->setWarnings(false)
                ->setPaper([0.0, 0.0, 153, 241], 'landscape')
                ->save($dir.'/'.$name);
            list($tmp, $file_cartes) = explode('htdocs/', $dir.'/'.$name);
        }

        $file_etiquettes_individuels = '';
        if (sizeof($tab_individuels) > 0) {
            // on imprime un fichier d'étiquettes carte
            $name = 'etiquettes_renouvellement_individuels_'.$rand.'_'.date('YmdHis').'.pdf';
//            if (sizeof($tab_cartes) > 0) {
                $pdf = App::make('dompdf.wrapper');
                $pdf->loadView('pdf.etiquettes_renouvellement_individuels', compact('tab_individuels'))
                    ->setWarnings(false)
                    ->setPaper('a4', 'portrait')
                    ->save($dir.'/'.$name);
                list($tmp, $file_etiquettes_individuels) = explode('htdocs/', $dir.'/'.$name);
//            }
        }

        $file_etiquettes_club = '';
        if (sizeof($tab_clubs) > 0) {
            // on imprime un fichier de livraison par club
            $name = 'etiquettes_renouvellement_clubs_'.$rand.'_'.date('YmdHis').'.pdf';
//            if (sizeof($tab_cartes) > 0) {
                $pdf = App::make('dompdf.wrapper');
                $pdf->loadView('pdf.etiquettes_renouvellement_clubs', compact('tab_clubs'))
                    ->setWarnings(false)
                    ->setPaper('a4', 'portrait')
                    ->save($dir.'/'.$name);

                list($tmp, $file_etiquettes_club) = explode('htdocs/', $dir.'/'.$name);
//            }
        }

        // une fois les cartes éditées, on met à jour les utilisateurs
        foreach ($utilisateurs as $utilisateur) {
            unset($utilisateur->type_edition);
            $last_saison_case = (in_array(date('m'), ['09', '10', '11', '12'])) ? date('Y') : date('Y') - 1;
            $new_nb_cases_carte = in_array($utilisateur->nb_cases_carte, [0, 3]) ? 1 : $utilisateur->nb_cases_carte + 1;
            $datau = array('statut' => 3, 'saison' => date('Y'), 'last_saison_case' => $last_saison_case,
                'nb_cases_carte' => $new_nb_cases_carte);
            $utilisateur->update($datau);
        }

        return new JsonResponse(['file_cartes' => $file_cartes, 'file_etiquettes_club' => $file_etiquettes_club, 'file_etiquettes_individuels' => $file_etiquettes_individuels], 200);
    }

    public function relanceReglement(Request $request) {
        $reglement = Reglement::where('id', $request->ref)->where('statut', 0)->first();
        if (!$reglement) {
            return new JsonResponse(['erreur' => 'règlement introuvable'], 400);
        }
        $club = Club::where('id', $reglement->clubs_id)->first();
        if (!$club) {
            return new JsonResponse(['erreur' => 'club introuvable'], 400);
        }
        $contact = Utilisateur::join('fonctionsutilisateurs', 'fonctionsutilisateurs.utilisateurs_id', '=', 'utilisateurs.id')
            ->where('fonctionsutilisateurs.fonctions_id', 97)
            ->where('utilisateurs.clubs_id', $club->id)
            ->whereNotNull('utilisateurs.personne_id')
            ->first();
        if ($contact) {
            $name = $reglement->reference.'.pdf';
            $dir = $club->getImageDir();
            $mailSent = Mail::to($contact->personne->email)->send(new RelanceReglement($club, $dir.'/'.$name, $reglement->reference, $reglement->montant));
            return new JsonResponse([], 200);
        }
        return new JsonResponse(['erreur' => 'probleme envoi'], 400);
    }

    public function reEditCarte(Request $request) {
        $utilisateur = Utilisateur::where('identifiant', $request->ref)->first();
        if (!$utilisateur) {
            return new JsonResponse(['erreur' => 'utilisateur introuvable'], 400);
        }
        $data = ['statut' => 2, 'nb_cases_carte' => 0];
        $utilisateur->update($data);
        return new JsonResponse([], 200);
    }

    public function checkCancelReglement(Request $request) {
        $reglement = Reglement::where('id', $request->ref)->first();
        if (!$reglement) {
            return new JsonResponse(['erreur' => 'règlement introuvable'], 400);
        }
        $tab_reglement  = [
            'id' => $reglement->id,
            'reference' => $reglement->reference,
            'montant' => $reglement->montant,
            'montant_rembourse' => $reglement->montant_rembourse,
        ];
        // on regarde si pour le reglement il y a au moins un reglement_utilisateur ($reglement->utilisateurs) pour lequel le champ adhésion est à 1 et le champ reversement_a_faire est à 0
        $count = DB::table('reglementsutilisateurs')
            ->where('reglements_id', '=', $reglement->id)
//            ->where('adhesion', '=', 1)
            ->where('reversement_a_faire', '=', 1)
            ->count();
        if ($count == 0) {
            return new JsonResponse(['cancel' => 0], 200);
        }

        $is_individuel = !$reglement->clubs_id ? true : false;
        $total_non_rembourse = 0;

        $config = Configsaison::where('id', 1)->first();
        $numero_en_cours = $config->numeroencours;

        $tab_utilisateurs = [];
        foreach ($reglement->utilisateurs as $utilisateur) {
            $reglement_utilisateur = DB::table('reglementsutilisateurs')
                ->where('reglements_id', '=', $reglement->id)
                ->where('utilisateurs_id', '=', $utilisateur->id)
                ->first();
            if ($reglement_utilisateur->reversement_a_faire == 1) {
//            if ($reglement_utilisateur->adhesion == 1 && $reglement_utilisateur->reversement_a_faire == 1) {
                list($tarif, $tarif_supp) = $is_individuel ? $this->getTarifByCt($utilisateur->ct) : $this->getTarifByCtAdherentsClub($utilisateur->ct);
                $nb_numeros_restant = 0;
                $nb_numeros_envoyes = 0;
                $montant_non_rembourse = 0;
                if ($reglement_utilisateur->abonnement == 1) {
                    $abonnement = Abonnement::where('personne_id', $utilisateur->personne->id)->where('etat', 1)->first();
                    if ($abonnement) {
                        $numero_fin = $abonnement->fin;
                        if ($numero_fin > $numero_en_cours) {
                            $nb_numeros_restant = $numero_fin - $numero_en_cours + 1;
                            if ($nb_numeros_restant < 5) {
                                $nb_numeros_envoyes = 5 - $nb_numeros_restant;
                                $montant_non_rembourse = $nb_numeros_envoyes * 7;
                                $total_non_rembourse += $montant_non_rembourse;
                            }
                        }
                    }
                }
                if ($reglement_utilisateur->adhesion == 0) {
                    $tarif = 0;
                }

                $ligne = [
                    'id' => $utilisateur->id,
                    'nom' => $utilisateur->personne->nom.' '.$utilisateur->personne->prenom,
                    'identifiant' => $utilisateur->identifiant,
                    'tarif' => $tarif,
                    'tarif_abo' => $tarif_supp,
                    'ct' => $utilisateur->ct,
                    'abonnement' => $reglement_utilisateur->abonnement,
                    'nb_numeros_restant' => $nb_numeros_restant,
                    'nb_numeros_envoyes' => $nb_numeros_envoyes,
                    'montant_non_rembourse' => $montant_non_rembourse,
                    'photos' => $this->photosSaisonUtilisateur($utilisateur),
                ];
                $tab_utilisateurs[] = $ligne;
            }
        }

        $tab_reglement['total_non_rembourse'] = $total_non_rembourse;
        $tab_reglement['total_rembourse'] = $reglement->montant - $reglement->montant_rembourse - $total_non_rembourse;

        $tab_club = [
            'id' => '',
            'nom' => '',
            'numero' => '',
        ];
        if (!$is_individuel) {
            $club = Club::where('id', $reglement->clubs_id)->first();
            if ($club) {
                $tab_club = [
                    'id' => $club->id,
                    'nom' => $club->nom,
                    'numero' => $club->numero,
                ];
            }
        }


        return new JsonResponse([
            'cancel' => 1,
            'is_individuel' =>$is_individuel,
            'utilisateurs' => $tab_utilisateurs,
            'reglement' => $tab_reglement,
            'club' => $tab_club
        ], 200);
    }

    public function annulationAdhesionIndividuel(Request $request) {
        $tabct = [
            7 => '> 25 ans',
            8 => '18 - 25 ans',
            9 => '< 18 ans',
            'F' => 'famille',
        ];
        $reglement = Reglement::where('id', $request->ref)->first();
        if (!$reglement) {
            return new JsonResponse(['erreur' => 'règlement introuvable'], 400);
        }
        $utilisateur = $reglement->utilisateurs->first();
        if (!$utilisateur) {
            return new JsonResponse(['erreur' => 'utilisateur introuvable'], 400);
        }
        $reglement_utilisateur = DB::table('reglementsutilisateurs')
            ->where('reglements_id', '=', $reglement->id)
            ->where('utilisateurs_id', '=', $utilisateur->id)
            ->first();
        if (!$reglement_utilisateur) {
            return new JsonResponse(['erreur' => 'règlement utilisateur introuvable'], 400);
        }
        if ($reglement_utilisateur->adhesion == 0 || $reglement_utilisateur->reversement_a_faire == 0) {
            return new JsonResponse(['erreur' => 'règlement déjà traité ou non valable'], 400);
        }

        // on cherche la facture de type 0 avec la ref du règlement
        $primary_invoice = DB::table('invoices')->where('reference', $reglement->reference)
            ->where('type', 0)
            ->first();

        $montant_non_rembourse = 0;
        $config = Configsaison::where('id', 1)->first();
        $numero_en_cours = $config->numeroencours;

        if ($reglement_utilisateur->abonnement == 1) {
            $abonnement = Abonnement::where('personne_id', $utilisateur->personne->id)->where('etat', 1)->first();
            if ($abonnement) {
                $numero_fin = $abonnement->fin;
                if ($numero_fin > $numero_en_cours) {
                    $nb_numeros_restant = $numero_fin - $numero_en_cours + 1;
                    if ($nb_numeros_restant < 5) {
                        $nb_numeros_envoyes = 5 - $nb_numeros_restant;
                        $montant_non_rembourse = $nb_numeros_envoyes * 7;
                    }
                }
            }
        }
        $montant_creance = $reglement->montant - $reglement->montant_rembourse - $montant_non_rembourse;
        list($tarif, $tarif_supp) = $this->getTarifByCt($utilisateur->ct);

        $tab_remboursements[] = [
            'adherent' => $utilisateur->identifiant.' - '.$utilisateur->personne->nom.' '.$utilisateur->personne->prenom,
            'adhesion' => $tarif,
            'abonnement' => $reglement_utilisateur->abonnement == 1 ? $tarif_supp : 0,
            'ct' => $tabct[$utilisateur->ct],
            'montant_non_rembourse' => $montant_non_rembourse,
            'montant_creance' => $montant_creance,
        ];

        try {
            DB::beginTransaction();
            // on met à jour le règlement utilisateur
            $data = [
                'reversement_a_faire' => 0,
            ];
            DB::table('reglementsutilisateurs')
                ->where('reglements_id', '=', $reglement->id)
                ->where('utilisateurs_id', '=', $utilisateur->id)
                ->update($data);

            // on met à jour le règlement
            $data = [
                'montant_rembourse' => $reglement->montant_rembourse + $montant_creance,
            ];
            $reglement->update($data);

            // on met à jour l'utilisateur
            $old_saison = (in_array(date('m'), ['09', '10', '11', '12']) ? date('Y') - 1 : date('Y') - 2);
            $datau = [
                'statut' => 0,
                'saison' => $old_saison,
            ];
            $utilisateur->update($datau);

            // on met à jour la personne
            $datap = [
                'creance' => $utilisateur->personne->creance + $montant_creance,
            ];
            $utilisateur->personne->update($datap);

            // on met à jour l'abonnement si existant
            $abonnement = Abonnement::where('personne_id', $utilisateur->personne->id)->where('etat', 1)->first();
            if ($abonnement) {
                $dataa = [
                    'etat' => 2,
                    'fin' => $numero_en_cours - 1,
                ];
                $abonnement->update($dataa);
            }

            // on crée une facture d'avoir
            $description = "Avoir pour annulation de l'adhésion de l'utilisateur ".$utilisateur->personne->nom.' '.$utilisateur->personne->prenom." pour la saison courante";
            $datai = [
                'reference' => $reglement->reference,
                'description' => $description,
                'montant' => $montant_creance,
                'personne_id' => $utilisateur->personne->id,
                'invoice' => $primary_invoice->numero,
                'remboursements' => $tab_remboursements,
            ];
            $this->createAndSendAvoir($datai);
            DB::commit();

            return new JsonResponse(['message' => "", 'success' => true], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return new JsonResponse(['erreur' => 'Une erreur est survenue lors de l\'annulation de l\'adhésion ' . $e->getMessage()], 500);
        }
    }

    public function annulationAdhesionClub(Request $request) {
        $tabct = [
            2 => '> 25 ans',
            3 => '18 - 25 ans',
            4 => '< 18 ans',
            5 => 'famille',
            6 => '2nde carte',
        ];
        $reglement = Reglement::where('id', $request->ref)->first();
        if (!$reglement) {
            return new JsonResponse(['erreur' => 'règlement introuvable'], 400);
        }
        $club = Club::where('id', $reglement->clubs_id)->first();
        if (!$club) {
            return new JsonResponse(['erreur' => 'club introuvable'], 400);
        }

        // on cherche la facture de type 0 avec la ref du règlement
        $primary_invoice = DB::table('invoices')->where('reference', $reglement->reference)
            ->where('type', 0)
            ->first();

        $tab_adherents = $request->adherents;
        $config = Configsaison::where('id', 1)->first();
        $numero_en_cours = $config->numeroencours;

        $utilisateurs = Utilisateur::whereIn('id', $tab_adherents)->get();

        $tab_remboursements = [];

        try {
            DB::beginTransaction();
            $montant_creance = 0;
            foreach ($utilisateurs as $utilisateur) {
                $reglement_utilisateur = DB::table('reglementsutilisateurs')
                    ->where('reglements_id', '=', $reglement->id)
                    ->where('utilisateurs_id', '=', $utilisateur->id)
                    ->first();
                if (!$reglement_utilisateur) {
                    return new JsonResponse(['erreur' => 'règlement utilisateur introuvable pour l\'utilisateur '.$utilisateur->identifiant], 400);
                }
                if ($reglement_utilisateur->reversement_a_faire == 0) {
//                if ($reglement_utilisateur->adhesion == 0 || $reglement_utilisateur->reversement_a_faire == 0) {
                    return new JsonResponse(['erreur' => 'règlement déjà traité ou non valable pour l\'utilisateur '.$utilisateur->identifiant], 400);
                }

                // on met à jour le règlement utilisateur
                $data = [
                    'reversement_a_faire' => 0,
                ];
                DB::table('reglementsutilisateurs')
                    ->where('reglements_id', '=', $reglement->id)
                    ->where('utilisateurs_id', '=', $utilisateur->id)
                    ->update($data);

                // on met à jour l'utilisateur
                list($tarif, $tarif_supp) = $this->getTarifByCtAdherentsClub($utilisateur->ct);
                if ($reglement_utilisateur->adhesion == 1) {
                    $old_saison = (in_array(date('m'), ['09', '10', '11', '12']) ? date('Y') - 1 : date('Y') - 2);
                    $datau = [
                        'statut' => 0,
                        'saison' => $old_saison,
                    ];
                    $utilisateur->update($datau);
                } else {
                    $tarif = 0;
                }



                $montant_creance_utilisateur = $tarif;
                $montant_creance += $tarif;

                $montant_non_rembourse = 0;
                if ($reglement_utilisateur->abonnement == 1) {
                    $montant_creance += $tarif_supp;
                    $montant_creance_utilisateur += $tarif_supp;
                    $abonnement = Abonnement::where('personne_id', $utilisateur->personne->id)->where('etat', 1)->first();
                    if ($abonnement) {
                        $numero_fin = $abonnement->fin;
                        if ($numero_fin > $numero_en_cours) {
                            $nb_numeros_restant = $numero_fin - $numero_en_cours + 1;
                            if ($nb_numeros_restant < 5) {
                                $nb_numeros_envoyes = 5 - $nb_numeros_restant;
                                $montant_non_rembourse = $nb_numeros_envoyes * 7;
                                $montant_creance -= $montant_non_rembourse;
                                $montant_creance_utilisateur -= $montant_non_rembourse;
                            }
                        }
                        $dataa = [
                            'etat' => 2,
                            'fin' => $numero_en_cours - 1,
                        ];
                        $abonnement->update($dataa);
                    }
                }

                $tab_remboursements[] = [
                    'adherent' => $utilisateur->identifiant.' - '.$utilisateur->personne->nom.' '.$utilisateur->personne->prenom,
                    'adhesion' => $tarif,
                    'abonnement' => $reglement_utilisateur->abonnement == 1 ? $tarif_supp : 0,
                    'ct' => $tabct[$utilisateur->ct],
                    'montant_non_rembourse' => $montant_non_rembourse,
                    'montant_creance' => $montant_creance_utilisateur,
                ];

            }


            // on met à jour le règlement
            $data = [
                'montant_rembourse' => $reglement->montant_rembourse + $montant_creance,
            ];
            $reglement->update($data);

            // on ajoute la créance club
            $datac = [
                'creance' => $club->creance + $montant_creance,
            ];
            $club->update($datac);

            // on crée la facture d'avoir
            $description = "Avoir pour annulation d'adhésions pour le club ".$club->nom." pour la saison courante";
            $datai = [
                'reference' => $reglement->reference,
                'description' => $description,
                'montant' => $montant_creance,
                'club_id' => $club->id,
                'invoice' => $primary_invoice->numero,
                'remboursements' => $tab_remboursements,
            ];
            $this->createAndSendAvoir($datai);

            DB::commit();

            return new JsonResponse(['message' => "", 'success' => true], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return new JsonResponse(['erreur' => 'Une erreur est survenue lors de l\'annulation de l\'adhésion ' . $e->getMessage()], 500);
        }
    }

    protected function photosSaisonUtilisateur($utilisateur) {
        $saison = (in_array(date('m'), ['09', '10', '11', '12']) ? date('Y') : date('Y') - 1);

        $count_photos = Photo::join('competitions', 'competitions.id', '=', 'photos.competitions_id')
            ->where('photos.participants_id', $utilisateur->identifiant)
            ->where('competitions.saison', $saison)
            ->where('competitions.open', 0)
            ->count();

        $count_rphotos = Rphoto::join('rcompetitions', 'rcompetitions.id', '=', 'rphotos.competitions_id')
            ->where('rphotos.participants_id', $utilisateur->identifiant)
            ->where('rcompetitions.saison', $saison)
            ->where('rcompetitions.open', 0)
            ->where('rcompetitions.urs_id', $utilisateur->urs_id)
            ->count();

        return $count_photos + $count_rphotos;
    }

    public function cancelAvoir(Request $request) {
        if ($request->type == 'personne') {
            $personne = Personne::where('id', $request->ref)->first();
            if (!$personne) {
                return new JsonResponse(['erreur' => 'personne introuvable'], 400);
            }
            // on annule la créance de la personne
            $personne->update(['creance' => 0]);
        } else {
            $club = Club::where('id', $request->ref)->first();
            if (!$club) {
                return new JsonResponse(['erreur' => 'club introuvable'], 400);
            }
            // on annule la créance du club
            $club->update(['creance' => 0]);
        }
        return new JsonResponse(['message' => "", 'success' => true], 200);
    }
}
