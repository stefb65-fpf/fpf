<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SessionRequest;
use App\Models\Club;
use App\Models\Formation;
use App\Models\Session;
use Illuminate\Http\Request;

class SessionController extends Controller
{
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
        return view('admin.sessions.create', compact('formation', 'session'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SessionRequest $request, Formation $formation)
    {
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
}
