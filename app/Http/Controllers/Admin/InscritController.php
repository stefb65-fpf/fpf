<?php

namespace App\Http\Controllers\Admin;

use App\Concern\Hash;
use App\Concern\Tools;
use App\Exports\FlorilegeExport;
use App\Exports\InscritExport;
use App\Http\Controllers\Controller;
use App\Mail\SendFormationPaymentLink;
use App\Models\Inscrit;
use App\Models\Personne;
use App\Models\Session;
use App\Models\Utilisateur;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;

class InscritController extends Controller
{
    use Hash;
    use Tools;
    public function __construct() {
        $this->middleware(['checkLogin', 'adminAccess']);
    }

    public function liste(Session $session) {
        if (!$this->checkDroit('GESFOR')) {
            return redirect()->route('accueil');
        }
        return view('admin.inscrits.liste', compact('session'));
    }

    public function destroy(Inscrit $inscrit) {
        $session = $inscrit->session;
        $inscrit->delete();
        return redirect()->route('inscrits.liste', $session)->with('success', 'Inscription supprimée avec succès');
    }

    public function destroyWithCredit(Inscrit $inscrit) {
        $session = $inscrit->session;
        $personne = Personne::where('id', $inscrit->personne_id)->first();
        if (!$personne) {
            return redirect()->route('inscrits.liste', $session)->with('error', 'Une erreur est survenue');
        }
        $amount = $inscrit->amount;
        // on supprime l'inscription
        $inscrit->delete();

        // on crédite le compte du user
        $personne->update(['avoir_formation' => $personne->avoir_formation + $amount]);

        return redirect()->route('inscrits.liste', $session)->with('success', 'Inscription supprimée avec succès');
    }

    public function sendPaymentLink(Inscrit $inscrit) {
        $data = array('attente' => 0, 'attente_paiement' => 1, 'secure_code' => $this->encodeShortReinit());
        $inscrit->update($data);

        $email = $inscrit->personne->email;
//        $email = 'contact@envolinfo.com';
        $mailSent = Mail::to($email)->send(new SendFormationPaymentLink($inscrit));
        $htmlContent = $mailSent->getOriginalMessage()->getHtmlBody();

        $sujet = "FPF // Lien de paiement pour la formation ".$inscrit->session->formation->name;
        $mail = new \stdClass();
        $mail->titre = $sujet;
        $mail->destinataire = $email;
        $mail->contenu = $htmlContent;
        $this->registerMail($inscrit->personne->id, $mail);

        return redirect()->route('inscrits.liste', $inscrit->session)->with('success', 'Lien de paiement envoyé avec succès');
    }
    public function export(Session $session) {
        // on récupère tous les inscrits de la session
        $inscrits = $session->inscrits->where('attente', 0)->where('status', 1);
        foreach ($inscrits as $inscrit) {
            $utilisateur = Utilisateur::where('personne_id', $inscrit->personne_id)
                ->whereIn('statut', [0,1,2,3])
                ->orderByDesc('statut')
                ->selectRaw('identifiant')
                ->first();
            if ($utilisateur) {
                $inscrit->identifiant = $utilisateur->identifiant;
            }
        }
        $fichier = 'session_'.$session->id.'_inscrits_' . date('YmdHis') . '.xls';
        if (Excel::store(new InscritExport($inscrits), $fichier, 'xls')) {
            $file_to_download = env('APP_URL') . 'storage/app/public/xls/' . $fichier;
            $texte = "Vous pouvez télécharger le fichier en cliquant sur le lien suivant : <a href='" . $file_to_download . "'>Télécharger</a>";
            return redirect()->route('inscrits.liste', $session)->with('success', $texte);
        } else {
            return redirect()->route('inscrits.liste', $session)->with('success', "Un problème est survenu lors de l'export");
        }
    }
}
