<?php

namespace App\Http\Controllers\Api;

use App\Concern\Api;
use App\Concern\Tools;
use App\Http\Controllers\Controller;
use App\Mail\ConfirmVote;
use App\Mail\RelanceReglement;
use App\Mail\SendAlertSupport;
use App\Mail\SendCodeForVote;
use App\Models\Candidat;
use App\Models\Election;
use App\Models\Motion;
use App\Models\Pays;
use App\Models\Vote;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class VoteController extends Controller
{
    use Api;
    use Tools;
    public function sendCode(Request $request) {
        $code = rand(100000, 999999);
        $personne = session()->get('user');
        $cartes = session()->get('cartes');

        if (isset($personne->adresses)) {
            // on regarde
            $pays = Pays::where('nom', $personne->adresses[0]->pays)->first();
            if ($pays) {
                $phone_mobile = $this->format_mobile_for_base($personne->phone_mobile, $pays->indicatif);
            } else {
                $phone_mobile = $this->format_mobile_for_base($personne->phone_mobile);
            }
        } else {
            $phone_mobile = $this->format_mobile_for_base($personne->phone_mobile);
        }
//        $phone_mobile = preg_replace('/[^0-9]/', '', $phone_mobile);
//        $phone_mobile = '00'.$phone_mobile;


        // on supprime de la table votes_utilisateurs les enregistremnts pour cette carte et ce vote avec le statut 0
        DB::table('votes_utilisateurs')
            ->where('utilisateurs_id', $cartes[0]->id)
            ->where('votes_id', $request->vote)
            ->where('statut', 0)
            ->delete();

        // on insère le nouveau code
        $date = date_create(date('Y-m-d H:i:s'));
        date_add($date, date_interval_create_from_date_string('20 minutes'));
        $date_sql = date_format($date, 'Y-m-d H:i:s');
        $data = array('utilisateurs_id' => $cartes[0]->id, 'votes_id' => $request->vote, 'statut' => 0, 'code' => $code,
            'date_validite_code' => $date_sql, 'date_validation' => date('Y-m-d H:i:s'));
        DB::table('votes_utilisateurs')->insert($data);

        // on regarde comment on envoie le code
        if ($request->moyen == 2) {
            // on envoie le code par mail à l'adresse de la personne
            Mail::to($personne->email)->send(new SendCodeForVote($code));
        } else {
            // on envoie le code par SMS
            $message = "Bonjour. Pour valider votre vote, veuillez utiliser le code $code";
            $return = $this->callOctopush($phone_mobile, $message, 'FPF VOTE');
            if ($return != '0') {
                return new JsonResponse(['erreur' => 'Erreur lors de l\'envoi du code par SMS'], 400);
            }
        }
        return new JsonResponse(['success' => 'Le code a bien été envoyé'], 200);
    }

    public function confirmCancelCode(Request $request) {
        $cartes = session()->get('cartes');
        $user = session()->get('user');
        if (!isset($cartes[0])) {
            return new JsonResponse(['erreur' => 'Aucune carte trouvée'], 400);
        }
        $vote = Vote::where('id', $request->vote)->first();
        if (!$vote) {
            return new JsonResponse(['erreur' => 'Vote non trouvé'], 400);
        }
        // on regarde si le code est bon
        $vote_utilisateur = DB::table('votes_utilisateurs')
            ->where('utilisateurs_id', $cartes[0]->id)
            ->where('votes_id', $request->vote)
            ->where('statut', 0)
            ->where('code', $request->code)
            ->first();
        if (!$vote_utilisateur) {
            return new JsonResponse(['erreur' => 'Le code est incorrect'], 400);
        }

        // on change le statut du vote utilisateur
        $datau = array('statut' => 1, 'date_validation' => date('Y-m-d H:i:s'));
        DB::table('votes_utilisateurs')
            ->where('utilisateurs_id', $cartes[0]->id)
            ->where('votes_id', $request->vote)
            ->where('statut', 0)->update($datau);

        $mailSent = Mail::to($user->email)->send(new ConfirmVote($vote));
        $htmlContent = $mailSent->getOriginalMessage()->getHtmlBody();

        $mail = new \stdClass();
        $mail->titre = "Confirmation de la prise en compte de votre vote";
        $mail->destinataire = $user->email;
        $mail->contenu = $htmlContent;
        $this->registerMail($user->id, $mail);

        return new JsonResponse(['succes' => 'Le vote a été sauvegardé'], 200);
    }

    public function saveVote(Request $request)
    {
        $user = session()->get('user');
        $cartes = session()->get('cartes');
        if (!isset($cartes[0])) {
            return new JsonResponse(['erreur' => 'Aucune carte trouvée'], 400);
        }
        $vote = Vote::where('id', $request->vote)->first();
        if (!$vote) {
            return new JsonResponse(['erreur' => 'Vote non trouvé'], 400);
        }
        // on regarde si le code est bon
        $vote_utilisateur = DB::table('votes_utilisateurs')
            ->where('utilisateurs_id', $cartes[0]->id)
            ->where('votes_id', $request->vote)
            ->where('statut', 0)
            ->where('code', $request->code)
            ->first();
        if (!$vote_utilisateur) {
            return new JsonResponse(['erreur' => 'Le code est incorrect'], 400);
        }

        $nb_voix = $request->voix;
        try {
            DB::beginTransaction();
            if ($request->motions) {
                foreach ($request->motions as $v) {
                    $motion = Motion::where('id', $v['reponse'])->where('elections_id', $v['election'])->first();
                    if ($motion) {
                        $data = ['nb_votes' => $motion->nb_votes + $nb_voix];
                        $motion->update($data);
                    }
                }
            }

            if ($request->candidats) {
                $tab_elections = [];
                foreach ($request->candidats as $candidat) {
                    $tab_elections[$candidat['election']][] = $candidat['candidat'];
                }

                foreach ($tab_elections as $election_id => $liste_candidats) {
                    $election = Election::where('id', $election_id)->first();
                    if ($election) {
                        $nb_postes = $election->nb_postes;
                        for ($j = 0; $j < $nb_postes; $j++) {
                            if (isset($liste_candidats[$j])) {
                                // on enregistre les voix pour ce candidat
                                $candidat = Candidat::where('id', $liste_candidats[$j])->where('elections_id', $election_id)->first();
                                if ($candidat) {
                                    $datac = array('nb_votes' => $candidat->nb_votes + $nb_voix);
                                    $candidat->update($datac);
                                }
                            }
                        }
                    }
                }
            }

            // on change le statut du vote utilisateur
            $datau = array('statut' => 1, 'date_validation' => date('Y-m-d H:i:s'));
            DB::table('votes_utilisateurs')
                ->where('utilisateurs_id', $cartes[0]->id)
                ->where('votes_id', $request->vote)
                ->where('statut', 0)->update($datau);

            // si c'est un vote classique et vote club, on enregistre le vote club
            if ($vote->type == 0 && $nb_voix > 1) {
                $datacl = array('votes_id' => $vote->id, 'clubs_id' => $cartes[0]->clubs_id, 'date_validation' => date('Y-m-d H:i:s'));
                DB::table('votes_clubs')->insert($datacl);
            }

            // si c'est un vote 3 phases et que c'est la phase club
            if (in_array($vote->type, [1, 2]) && $vote->phase == 2) {
                // on met à jour la table cumul_votes_club
                $datacvc = array('statut' => 1, 'updated_at' => date('Y-m-d'));
                DB::table('cumul_votes_clubs')
                    ->where('votes_id', $vote->id)
                    ->where('clubs_id', $cartes[0]->clubs_id)
                    ->where('statut', 0)
                    ->update($datacvc);
            }

            //si c'est un vote 3 phases et que c'est la phase UR
            if ($vote->type == 1 && $vote->phase == 3) {
                // on met à jour la table cumul_votes_urs
                $datacvc = array('statut' => 1, 'updated_at' => date('Y-m-d'));
                DB::table('cumul_votes_urs')
                    ->where('votes_id', $vote->id)
                    ->where('urs_id', $cartes[0]->urs_id)
                    ->where('statut', 0)
                    ->update($datacvc);
            }

            $datav = ['total_votes' => $vote->total_votes + $nb_voix];
            $vote->update($datav);

//            $mailSent = Mail::to('contact@envolinfo.com')->send(new ConfirmVote($vote));
            $mailSent = Mail::to($user->email)->send(new ConfirmVote($vote));
            $htmlContent = $mailSent->getOriginalMessage()->getHtmlBody();

            $mail = new \stdClass();
            $mail->titre = "Confirmation de la prise en compte de votre vote";
            $mail->destinataire = $user->email;
            $mail->contenu = $htmlContent;
            $this->registerMail($user->id, $mail);

            DB::commit();
        } catch (\Exception $e) {
            dd($e);
            DB::rollBack();
        }

        return new JsonResponse(['succes' => 'Le vote a été sauvegardé'], 200);
    }

    public function confirmGiveCode(Request $request) {
        $cartes = session()->get('cartes');
        $user = session()->get('user');
        if (!isset($cartes[0])) {
            return new JsonResponse(['erreur' => 'Aucune carte trouvée'], 400);
        }
        $vote = Vote::where('id', $request->vote)->first();
        if (!$vote) {
            return new JsonResponse(['erreur' => 'Vote non trouvé'], 400);
        }
        // on regarde si le code est bon
        $vote_utilisateur = DB::table('votes_utilisateurs')
            ->where('utilisateurs_id', $cartes[0]->id)
            ->where('votes_id', $request->vote)
            ->where('statut', 0)
            ->where('code', $request->code)
            ->first();
        if (!$vote_utilisateur) {
            return new JsonResponse(['erreur' => 'Le code est incorrect'], 400);
        }
        try {
            DB::beginTransaction();

            // on met à jour le statut et le pouvoir du cumul_votes_clubs
            $datacvc = array('statut' => 1, 'updated_at' => date('Y-m-d'), 'pouvoir' => 1);
            DB::table('cumul_votes_clubs')
                ->where('clubs_id', $cartes[0]->clubs_id)
                ->where('statut', 0)
                ->where('votes_id', $request->vote)
                ->update($datacvc);

            // on change le statut du vote utilisateur
            $datau = array('statut' => 1, 'date_validation' => date('Y-m-d H:i:s'));
            DB::table('votes_utilisateurs')
                ->where('utilisateurs_id', $cartes[0]->id)
                ->where('votes_id', $request->vote)
                ->where('statut', 0)->update($datau);

            $mailSent = Mail::to($user->email)->send(new ConfirmVote($vote));
            $htmlContent = $mailSent->getOriginalMessage()->getHtmlBody();

            $mail = new \stdClass();
            $mail->titre = "Confirmation de la prise en compte de votre vote";
            $mail->destinataire = $user->email;
            $mail->contenu = $htmlContent;
            $this->registerMail($user->id, $mail);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
        }
        return new JsonResponse(['succes' => 'Le vote a été sauvegardé'], 200);
    }
}
