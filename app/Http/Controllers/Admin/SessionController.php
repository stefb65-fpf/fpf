<?php

namespace App\Http\Controllers\Admin;

use App\Concern\Tools;
use App\Http\Controllers\Controller;
use App\Http\Requests\SessionRequest;
use App\Mail\AnnulationFormation;
use App\Models\Club;
use App\Models\Formation;
use App\Models\Invoice;
use App\Models\Personne;
use App\Models\Session;
use App\Models\Ur;
use App\Models\Utilisateur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class SessionController extends Controller
{
    use Tools;
    use \App\Concern\Invoice;
    public function __construct() {
        $this->middleware(['checkLogin', 'adminAccess']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Formation $formation)
    {
        $sessions = Session::where('formation_id', $formation->id)->orderByDesc('created_at')->get();
        foreach ($sessions as $session) {
            if ($session->club_id != null) {
                $club = Club::where('id', $session->club_id)->first();
                if ($club) {
                    $session->numero_club = $club->numero;
                }
            }
        }
        return view('admin.sessions.index', compact('sessions', 'formation'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Formation $formation)
    {
        $session = new Session();
        $session->price = $formation->price;
        $session->price_not_member = $formation->price_not_member;
        $session->places = $formation->places;
        $session->waiting_places = $formation->waiting_places;
        $session->type = $formation->type;
        $session->location = $formation->location;
        $session->reste_a_charge = $formation->global_price;
        return view('admin.sessions.create', compact('formation', 'session'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SessionRequest $request, Formation $formation)
    {
        $data = $request->only('price', 'price_not_member', 'places', 'start_date', 'end_date', 'waiting_places', 'type', 'location', 'end_inscription',
            'frais_deplacement', 'pec_fpf', 'reste_a_charge');
        if ($request->numero_club != null) {
            $club = Club::where('numero', $request->numero_club)->first();
            if (!$club) {
                return redirect()->back()->with('error', 'Le n\'existe pas')->withInput();
            }
            $data['club_id'] = $club->id;
        }
        if ($request->pec) {
            $data['pec'] = $request->pec;
        } else {
            $data['pec'] = 0;
        }
        // si la date d'inscription n'est pas renseignée, on prend la date de début de la session moins deux jours
        if ($request->end_inscription == null) {
            $data['end_inscription'] = date('Y-m-d', strtotime($request->start_date . ' -2 days'));
        } else {
            // si la date d'inscription est renseignée, on vérifie si elle est inférieure à la date de début de la session
            if ($request->end_inscription >= $request->start_date) {
                return redirect()->back()->with('error', 'La date de fin d\'inscription doit être inférieure à la date de début de la session')->withInput();
            }
        }

        if ($request->ur_id != 0) {
            $data['ur_id'] = $request->ur_id;
        }

        if ($request->numero_club != null || $request->ur_id != 0) {
            $data['price'] = 0;
            $data['price_not_member'] = 0;
        } else {
            $data['frais_deplacement'] = 0;
        }
        if ($request->type == 0) {
            $data['frais_deplacement'] = 0;
        }
        if (!$request->pec_fpf) {
            $data['pec_fpf'] = 0;
        }

        $data['formation_id'] = $formation->id;

        Session::create($data);
        return redirect()->route('sessions.index', $formation->id)->with('success', 'Session créée avec succès');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Session $session)
    {
        $formation  = Formation::where('id', $session->formation_id)->first();
        if ($session->club_id != null) {
            $club = Club::where('id', $session->club_id)->first();
            if ($club) {
                $session->numero_club = $club->numero;
            }
        }
        return view('admin.sessions.edit', compact('formation', 'session'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Session $session)
    {
        $formation  = Formation::where('id', $session->formation_id)->first();
        $data = $request->only('price', 'price_not_member', 'places', 'start_date', 'end_date', 'waiting_places', 'type', 'location', 'end_inscription');
        if ($request->numero_club != null) {
            $club = Club::where('numero', $request->numero_club)->first();
            if (!$club) {
                return redirect()->back()->with('error', 'Le n\'existe pas')->withInput();
            }
            $data['club_id'] = $club->id;
        }
        if ($request->pec) {
            $data['pec'] = $request->pec;
        } else {
            $data['pec'] = 0;
        }
        // si la date d'inscription n'est pas renseignée, on prend la date de début de la session moins deux jours
        if ($request->end_inscription == null) {
            $data['end_inscription'] = date('Y-m-d', strtotime($request->start_date . ' -2 days'));
        } else {
            // si la date d'inscription est renseignée, on vérifie si elle est inférieure à la date de début de la session
            if ($request->end_inscription >= $request->start_date) {
                return redirect()->back()->with('error', 'La date de fin d\'inscription doit être inférieure à la date de début de la session')->withInput();
            }
        }
        if ($request->ur_id != 0) {
            $data['ur_id'] = $request->ur_id;
        }
        $session->update($data);
        return redirect()->route('sessions.index', $formation->id)->with('success', 'Session modifiée avec succès');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Session $session)
    {
        $session->delete();
        return redirect()->route('sessions.index', $session->formation_id)->with('success', 'Session supprimée avec succès');
    }

    public function delete_dashboard(Session $session)
    {
        $session->delete();
        return redirect()->route('formations.dashboard')->with('success', 'Session supprimée avec succès');
    }

    public function cancel(Session $session)
    {
        // on envoie un mail aux inscrits (status = 1)
        foreach ($session->inscrits->where('status', 1)->where('attente', 0) as $inscrit) {
            Mail::mailer('smtp2')->to($inscrit->personne->email)->send(new AnnulationFormation($session, 0));
            if (!$session->club_id && !$session->ur_id) {
                $personne = Personne::where('id', $inscrit->personne->id)->first();
                $datap = ['creance' => $personne->creance + $inscrit->amount];
                $personne->update($datap);
                $reference = 'FORMATION-'.$inscrit->personne_id.'-'.$inscrit->session_id;
                $primary_invoice = Invoice::where('reference', $reference)
                    ->first();
                // on génère une facture d'avoir pour les inscrits
//                $description = "Avoir pour annulation d'une inscription à la formation ".$session->formation->name." pour ".$personne->nom.' '.$personne->prenom;
                $description = "Avoir pour annulation d'une inscription à la formation ".$session->formation->name." pour la session du ".date("d/m/Y",strtotime($session->start_date))." pour ".$personne->nom.' '.$personne->prenom;
                $datai = [
                    'reference' => $reference,
                    'description' => $description,
                    'montant' => $inscrit->amount,
                    'personne_id' => $personne->id,
                    'invoice' => $primary_invoice ? $primary_invoice->numero : null,
                ];
                $this->createAndSendAvoir($datai);
            }
        }

        // si formation club ou UR, on envoie un mail structure. Si paiement de la session effectué, on fait une créance structure
        if ($session->club_id) {
            $club = Club::where('id', $session->club->id)->first();
            if ($session->paiement_status == 1) {
                $datac = ['creance' => $club->creance + $session->paid];
                $club->update($datac);

                // on génère une facture d'avoir pour le club
                $description = "Avoir pour annulation d'une session de la formation ".$session->formation->name." pour le club ".$club->nom;
                $reference = 'SESSION-FORMATION-'.$session->club_id.'-'.$session->id;
                $primary_invoice = Invoice::where('reference', $reference)
                    ->first();
                $datai = [
                    'reference' => $reference,
                    'description' => $description,
                    'montant' => $session->paid,
                    'club_id' => $club->id,
                    'invoice' => $primary_invoice->numero,
                ];
                $this->createAndSendAvoir($datai);
            }

            // on cherche le mail du contact du club
            $contact = Utilisateur::join('fonctionsutilisateurs', 'fonctionsutilisateurs.utilisateurs_id', '=', 'utilisateurs.id')
                ->where('utilisateurs.clubs_id', $session->club->id)
                ->where('fonctionsutilisateurs.fonctions_id', 97)
                ->first();
            if ($contact) {
                $send_mail = Mail::mailer('smtp2')->to($contact->personne->email);
                if ($session->club->courriel != '') {
                    $send_mail->cc($session->club->courriel);
                }
                $send_mail->send(new AnnulationFormation($session, 1));
            }
        } elseif ($session->ur_id) {
            $ur = Ur::where('id', $session->ur_id)->first();
            if ($session->paiement_status == 1) {
                $datac = ['creance' => $ur->creance + $session->paid];
                $ur->update($datac);

                // on génère une facture d'acoir pour l'UR
                $description = "Avoir pour annulation d'une session de la formation ".$session->formation->name." pour l'UR ".$ur->nom;
                $reference = 'SESSION-FORMATION-'.$ur->id.'-'.$session->id;
                $primary_invoice = Invoice::where('reference', $reference)
                    ->first();
                $datai = [
                    'reference' => $reference,
                    'description' => $description,
                    'montant' => $session->paid,
                    'ur_id' => $ur->id,
                    'invoice' => $primary_invoice->numero,
                ];
                $this->createAndSendAvoir($datai);
            }
            // on cherche le responsable formation de l'ur
            $contact = Utilisateur::join('fonctionsutilisateurs', 'fonctionsutilisateurs.utilisateurs_id', '=', 'utilisateurs.id')
                ->where('utilisateurs.urs_id', $ur->id)
                ->where('fonctionsutilisateurs.fonctions_id', 65)
                ->first();

            // on cherche le président d'ur
            $president = Utilisateur::join('fonctionsutilisateurs', 'fonctionsutilisateurs.utilisateurs_id', '=', 'utilisateurs.id')
                ->where('utilisateurs.urs_id',$ur->id)
                ->where('fonctionsutilisateurs.fonctions_id', 57)
                ->first();

            $send_mail = Mail::mailer('smtp2')->to($ur->courriel);
            if ($contact) {
                $send_mail->cc($contact->personne->email);
            }
            if ($president) {
                $send_mail->cc($president->personne->email);
            }
            $send_mail->send(new AnnulationFormation($session, 1));
        }

        // on envoie un mail aux formateurs avec le dept formations en copie
        foreach ($session->formation->formateurs as $formateur) {
            if (filter_var($formateur->personne->email, FILTER_VALIDATE_EMAIL)) {
                Mail::mailer('smtp2')->to($formateur->personne->email)->cc('formations@federation-photo.fr')->send(new AnnulationFormation($session, 2));
            }
        }


        $data = ['status' => 99];
        $session->update($data);
        return redirect()->route('sessions.index', $session->formation_id)->with('success', 'Session annulée avec succès et mails transmis aux inscrits, formateurs et organisateurs');
    }

    public function confirm(Session $session)
    {
        $data = ['status' => 1];
        $session->update($data);
        return redirect()->route('sessions.index', $session->formation_id)->with('success', 'Session confirmée avec succès');
    }

    public function end(Session $session)
    {
        $data = ['status' => 3];
        $session->update($data);
        return redirect()->route('sessions.index', $session->formation_id)->with('success', 'Session marquée comme terminée avec succès');
    }
}
