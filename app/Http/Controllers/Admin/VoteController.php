<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ElectionRequest;
use App\Http\Requests\VoteRequest;
use App\Models\Candidat;
use App\Models\Election;
use App\Models\Motion;
use App\Models\Reponse;
use App\Models\Utilisateur;
use App\Models\Vote;
use Illuminate\Http\Request;

class VoteController extends Controller
{
    public function __construct() {
        $this->middleware(['checkLogin', 'adminAccess']);
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $votes = Vote::orderByDesc('annee')->orderByDesc('debut')->paginate(20);
        return view('admin.votes.index', compact('votes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $vote = new Vote();
        return view('admin.votes.create', compact('vote'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(VoteRequest $request)
    {
        if ($request->type == 0) {
            if ($request->debut == null || $request->fin == null) {
                return redirect()->route('votes.create')->with('error', 'Les date de début et de fin doivent être renseignées.');
            }
            $data = $request->only('nom', 'type', 'debut', 'fin', 'urs_id', 'fonctions_id', 'vote_club');
        } else {
            if ($request->debut_phase1 == null || $request->debut_phase2 == null || $request->debut_phase3 == null
                || $request->fin_phase1 == null || $request->fin_phase2 == null || $request->fin_phase3 == null) {
                return redirect()->route('votes.create')->with('error', 'Les date de début et de fin doivent être renseignées.');
            }
            $data = $request->only('nom', 'type', 'fin_phase1', 'debut_phase2', 'fin_phase2', 'debut_phase3');
            $data['debut'] = $request->debut_phase1;
            $data['fin'] = $request->fin_phase3;
            $data['urs_id'] = 0;
            $data['fonctions_id'] = 0;
            $data['vote_club'] = 0;
        }
        $data['annee'] = date('Y');

        $vote = Vote::where('nom', $request->nom)->first();
        if ($vote) {
            return redirect()->route('votes.create')->with('error', 'Un vote avec ce nom existe déjà.');
        }

        Vote::create($data);
        return redirect()->route('votes.index')->with('success', 'Le vote a bien été créé.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Vote $vote)
    {
        return view('admin.votes.edit', compact('vote'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Vote $vote)
    {
        if ($request->type == 0) {
            if ($request->debut == null || $request->fin == null) {
                return redirect()->route('votes.edit', $vote)->with('error', 'Les date de début et de fin doivent être renseignées.');
            }
            $data = $request->only('nom', 'type', 'debut', 'fin', 'urs_id', 'fonctions_id', 'vote_club');
        } else {
            if ($request->debut_phase1 == null || $request->debut_phase2 == null || $request->debut_phase3 == null
                || $request->fin_phase1 == null || $request->fin_phase2 == null || $request->fin_phase3 == null) {
                return redirect()->route('votes.edit', $vote)->with('error', 'Les date de début et de fin doivent être renseignées.');
            }
            $data = $request->only('nom', 'type', 'fin_phase1', 'debut_phase2', 'fin_phase2', 'debut_phase3');
            $data['debut'] = $request->debut_phase1;
            $data['fin'] = $request->fin_phase3;
        }

        $vote->update($data);
        return redirect()->route('votes.index')->with('success', 'Le vote a bien été modifié.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Vote $vote)
    {
        $vote->delete();
        return redirect()->route('votes.index')->with('success', 'Le vote a bien été supprimé.');
    }

    public function electionsList(Vote $vote) {
        $elections = Election::where('votes_id', $vote->id)->orderBy('ordre')->get();
        return view('admin.votes.elections', compact('elections', 'vote'));
    }

    public function electionsCreate(Vote $vote) {
        $election = new Election();
        return view('admin.votes.elections_create', compact('election', 'vote'));
    }

    public function electionsStore(ElectionRequest $request, Vote $vote) {
        $data = $request->only('nom', 'ordre', 'type', 'contenu');
        $data['votes_id'] = $vote->id;
        if ($request->type == 2) {
            if ($request->nb_postes == null) {
                return redirect()->route('votes.elections.create', $vote)->with('error', 'Le nombre de postes doit être renseigné.');
            }
            $data['nb_postes'] = $request->nb_postes;
        }
        if ($request->ordre == '') {
            $max_election = Election::where('votes_id', $vote->id)->max('ordre');
            if ($max_election == null) {
                $data['ordre'] = 1;
            } else {
                $data['ordre'] = $max_election + 1;
            }
        }
        $election = Election::where('nom', $request->nom)->where('votes_id', $vote->id)->first();
        if ($election) {
            return redirect()->route('votes.elections.create', $vote)->with('error', 'Une élection avec ce nom existe déjà pour ce vote.');
        }
        $election = Election::create($data);
        if ($election->type == 1) {
            // on récupère les réponses pour ce type de vote
            $reponses = Reponse::where('type_vote', $vote->type)->orderBy('id')->get();
            foreach ($reponses as $reponse) {
                // on crée la motion correspondante
                $data = [
                    'elections_id' => $election->id,
                    'reponses_id' => $reponse->id
                ];
                Motion::create($data);
            }
        }

        return redirect()->route('votes.elections.index', $vote)->with('success', 'L\'élection a bien été créée.');
    }

    public function electionsEdit(Vote $vote, Election $election) {
        return view('admin.votes.elections_edit', compact('election', 'vote'));
    }

    public function electionsUpdate(Request $request, Vote $vote, Election $election) {
        $data = $request->only('nom', 'ordre');
        $election->update($data);
        return redirect()->route('votes.elections.index', $vote)->with('success', 'L\'élection a bien été modifiée.');
    }

    public function electionsDestroy(Vote $vote, Election $election) {
        $election->delete();
        return redirect()->route('votes.elections.index', $vote)->with('success', 'L\'élection a bien été supprimée.');
    }

    public function candidatsList(Vote $vote, Election $election) {
        $candidats = Candidat::where('elections_id', $election->id)->orderBy('ordre')->get();
        foreach ($candidats as $candidat) {
            $candidat->utilisateur = Utilisateur::where('id', $candidat->utilisateurs_id)->first();
        }
        return view('admin.votes.candidats', compact('election', 'vote', 'candidats'));
    }

    public function candidatsStore(Request $request, Vote $vote, Election $election) {
        $identifiant = $request->identifiant;
        if (strlen($identifiant) != 12) {
            return redirect()->route('votes.elections.candidats.index', [$vote, $election])->with('error', 'L\'identifiant doit être composé de 12 caractères.');
        }
        $utilisateur = Utilisateur::where('identifiant', $identifiant)->first();
        if (!$utilisateur) {
            return redirect()->route('votes.elections.candidats.index', [$vote, $election])->with('error', 'Aucun adhérent n\'a été trouvé avec cet identifiant.');
        }
        // on cherche l'ordre pour les candidats
        $max_candidat = Candidat::where('elections_id', $election->id)->max('ordre');
        if ($max_candidat == null) {
            $ordre = 1;
        } else {
            $ordre = $max_candidat + 1;
        }
        // on vérifie que l'utilisateur n'est pas déjà candidat
        $candidat = Candidat::where('utilisateurs_id', $utilisateur->id)->where('elections_id', $election->id)->first();
        if ($candidat) {
            return redirect()->route('votes.elections.candidats.index', [$vote, $election])->with('error', 'Cet adhérent est déjà candidat pour cette élection.');
        }
        // on insère le candidat
        $data = [
            'utilisateurs_id' => $utilisateur->id,
            'elections_id' => $election->id,
            'ordre' => $ordre
        ];
        Candidat::create($data);
        return redirect()->route('votes.elections.candidats.index', [$vote, $election])->with('success', 'Le candidat a bien été ajouté.');
    }

    public function candidatsDestroy(Vote $vote, Election $election, Candidat $candidat) {
        $candidat->delete();
        return redirect()->route('votes.elections.candidats.index', [$vote, $election])->with('success', 'Le candidat a bien été supprimé.');
    }

    public function resultats(Vote $vote, Election $election) {
        $candidats = null;
        $motions = null;
        if ($election->type == 2) {
            // on récupère les candidats classés par niombre de voix
            $candidats = Candidat::where('elections_id', $election->id)->orderByDesc('nb_votes')->get();
            foreach ($candidats as $candidat) {
                $candidat->utilisateur = Utilisateur::where('id', $candidat->utilisateurs_id)->first();
            }
        } else {
            // on récupère les résultats pour chaque motion
            $motions = Motion::where('elections_id', $election->id)->orderBy('id')->get();
            foreach ($motions as $motion) {
                $motion->reponse = Reponse::where('id', $motion->reponses_id)->first();
            }
        }
        return view('admin.votes.resultats', compact('vote', 'election', 'candidats', 'motions'));
    }
}
