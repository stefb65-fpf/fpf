<?php

namespace App\Http\Controllers;


use App\Concern\Tools;
use App\Http\Requests\SupportRequest;
use App\Mail\SendSupportNotification;
use App\Models\Supportmessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class SupportController extends Controller
{
    use Tools;
    // affichage de la page de support
    public function index()
    {
        $user = session()->get('user');
        if($user) {
            $user->identifiant = $user->cartes[0]->identifiant;
        }
        return view('support.form', compact('user'));
    }

    //envoyer la demande de support
    public function  submit(SupportRequest $request){
        $user = session()->get('user');
        $data = $request->all();
        unset($data['_token']);
        unset($data['_method']);
        unset($data['enableBtn']);
        Supportmessage::create($data);
        $email = $request->email;
        $mailSent = Mail::to($email)->send(new SendSupportNotification($request->contenu,$request->objet));

        if($user){
//        enregistrement de l'action et du mail dans l'historique
            $this->registerAction($user->id, 4,"Nouvelle demande de support" );
            $htmlContent = $mailSent->getOriginalMessage()->getHtmlBody();
//         enregistrement du mail de la personne
            $mail = new \stdClass();
            $mail->titre ="Nouvelle demande de support";
            $mail->destinataire = $email;
            $mail->contenu = $htmlContent;
            $this->registerMail($user->id, $mail);
        }

        return view('support.success', compact('user', 'email'));
    }
}
