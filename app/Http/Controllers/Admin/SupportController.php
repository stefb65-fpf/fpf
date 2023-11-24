<?php

namespace App\Http\Controllers\Admin;

use App\Concern\Tools;
use App\Http\Controllers\Controller;
use App\Mail\AnswerSupport;
use App\Mail\SendEmailChangeEmailAddress;
use App\Models\Personne;
use App\Models\Supportmessage;
use App\Models\Utilisateur;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class SupportController extends Controller
{
    use Tools;
    public function __construct() {
        $this->middleware(['checkLogin', 'adminAccess'])->except(['updateStatus', 'sendAnswer']);
    }

    public function index($type = null) {
        if (!$this->checkDroit('SUPPORT')) {
            return redirect()->route('accueil');
        }
        $query = Supportmessage::orderByDesc('id');
        if ($type == 'non-traites') {
            $query->where('statut', 0);
        }
        $supports = $query->paginate(100);
        return view('admin.supports.index', compact('supports'));
    }

    public function updateStatus(Request $request) {
        $support = Supportmessage::where('id', $request->ref)->first();
        if (!$support) {
            return new JsonResponse([], 404);
        }
        $support->update(['statut' => $request->status]);
        return new JsonResponse([], 200);
    }

    public function sendAnswer(Request $request) {
        $user = session()->get('user');
        if (!$user) {
            return new JsonResponse([], 401);
        }
        $support = Supportmessage::where('id', $request->ref)->first();
        if (!$support) {
            return new JsonResponse([], 404);
        }

        // on enregistre les informations de la réponse
        $support->update([
            'answer' => $request->answer,
            'answer_name' => $user->prenom.' '.$user->nom,
            'statut' => 2
        ]);

        // on envoie le mail de réponse du support
        $mailSent = Mail::to($support->email)->send(new AnswerSupport($support));
        if ($support->identifiant != '') {
            $carte = Utilisateur::where('identifiant', $support->identifiant)->first();
            if ($carte && $carte->personne_id) {
                $personne = Personne::where('id', $carte->personne_id)->first();
                if ($personne) {
                    $htmlContent = $mailSent->getOriginalMessage()->getHtmlBody();

                    $mail = new \stdClass();
                    $mail->titre = "FPF // Votre demande de support a été traitée";
                    $mail->destinataire = $support->email;
                    $mail->contenu = $htmlContent;
                    $this->registerMail($personne->id, $mail);
                }
            }
        }


        return new JsonResponse([], 200);
    }
}
