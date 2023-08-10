@extends('layouts.default')

@section('content')
    <div class="pageCanva">
        <h1 class="pageTitle">
            Espace de gestion FPF
        </h1>
        @if(sizeof($affectations) > 0)
            <div class="alertDanger" style="width: 80% !important">
                <span class="bold">Informations !</span>
                Il existe des adhérents de la région parisienne en attente d'affecation. Veuillez les affecter à une UR
                avant d'éditer les cartes.
                @foreach($affectations as $affectation)
                    <div
                        style="display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 1px solid darkred; margin-top: 10px; margin-bottom: 5px;padding-bottom: 5px;">
                        <div>
                            {{ $affectation->personne->prenom.' '.$affectation->personne->nom }}
                        </div>
                        <div>
                            {{ $affectation->personne->email }}<br>
                            {{ $affectation->personne->phone_mobile }}
                        </div>
                        <div>
                            {!! $affectation->personne->adresses[0]->libelle1 ? $affectation->personne->adresses[0]->libelle1.'<br>' : '' !!}
                            {!! $affectation->personne->adresses[0]->libelle2 ? $affectation->personne->adresses[0]->libelle2.'<br>' : '' !!}
                            {{ $affectation->personne->adresses[0]->codepostal.' '.$affectation->personne->adresses[0]->ville }}
                            <br>
                        </div>
                        <div>
                            <select name="selectAffectationUr" style="padding: 5px">
                                <option value="15" selected>UR 15</option>
                                <option value="16">UR 16</option>
                                <option value="17">UR 17</option>
                                <option value="18">UR 18</option>
                            </select>
                        </div>
                        <div>
                            <button class="btnSmall adminDanger" name="validAffectationUr" data-ur="15" data-identifiant="{{ $affectation->identifiant }}">Affecter</button>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
        <div class="cardContainer">
            @if(in_array('GESADH', $droits_fpf))
                <a class="card" href="{{ route('personnes.index') }}">
                    <div class="wrapper">
                        <div class="cardTitle">Utilisateurs base en ligne</div>
                    </div>
                </a>
            @endif
            @if(in_array('GESSTR', $droits_fpf))
                <a class="card" href="{{ route('admin.structures') }}">
                    <div class="wrapper">
                        <div class="cardTitle">Structures & Fonctions</div>
                    </div>
                </a>
            @endif
            @if(in_array('GESREG', $droits_fpf))
                <a class="card" href="{{ route('reglements.index') }}">
                    <div class="wrapper">
                        <div class="cardTitle">Règlements</div>
                    </div>
                </a>
            @endif
            @if(in_array('GESREG', $droits_fpf))
                <a class="card" href="{{ route('reglements.cartes') }}">
                    <div class="wrapper">
                        <div class="cardTitle">Edition des cartes</div>
                    </div>
                </a>
            @endif
            @if(in_array('GESPARAM', $droits_fpf))
                <a class="card" href="{{ route('admin.config') }}">
                    <div class="wrapper">
                        <div class="cardTitle">Paramétrage</div>
                    </div>
                </a>
            @endif
            @if(in_array('GESDRO', $droits_fpf))
                <a class="card" href="{{ route('droits.index') }}">
                    <div class="wrapper">
                        <div class="cardTitle">Gestion des droits</div>
                    </div>
                </a>
            @endif
            @if(in_array('GESPUB', $droits_fpf))
                <a class="card" href="{{ route('admin.gestion_publications') }}">
                    <div class="wrapper">
                        <div class="cardTitle">Routage, éditions, Florilège</div>
                    </div>
                </a>
            @endif
            @if(in_array('GESFOR', $droits_fpf))
                <a class="card" href="{{ route('formations.index') }}">
                    <div class="wrapper">
                        <div class="cardTitle">Formations</div>
                    </div>
                </a>
            @endif
            @if(in_array('VISUSTAT', $droits_fpf))
                <a class="card">
                    <div class="wrapper">
                        <div class="cardTitle">Statistiques</div>
                    </div>
                </a>
            @endif
            @if(in_array('GESTRE', $droits_fpf))
                <a class="card">
                    <div class="wrapper">
                        <div class="cardTitle">Reversements</div>
                    </div>
                </a>
            @endif
            @if(in_array('GESTRE', $droits_fpf))
                <a class="card" href="{{ route('admin.factures') }}">
                    <div class="wrapper">
                        <div class="cardTitle">Factures émises</div>
                    </div>
                </a>
            @endif
            @if(in_array('GESVOT', $droits_fpf))
                <a class="card" href="{{ route('votes.index') }}">
                    <div class="wrapper">
                        <div class="cardTitle">Votes</div>
                    </div>
                </a>
            @endif
            @if(in_array('GESNEW', $droits_fpf) || in_array('GESNEWCA', $droits_fpf) || in_array('GESNEWBU', $droits_fpf))
                <a class="card" id="connectNewsletter">
                    <div class="wrapper">
                        <div class="cardTitle">Newsletter</div>
                    </div>
                </a>
            @endif
            <a class="card" href="{{ route('admin.informations') }}">
                <div class="wrapper">
                    <div class="cardTitle">Informations</div>
                </div>
            </a>
            <div class="card invisible">
            </div>
            <div class="card invisible">
            </div>
        </div>
    </div>
    <div class="modalEdit d-none" id="modalAffectation">
        <div class="modalEditHeader">
            <div class="modalEditTitle">Affectation UR</div>
            <div class="modalEditClose">
                X
            </div>
        </div>
        <div class="modalEditBody">
            Voulez-vous vraiment affecter cet utilisateur à l'UR <span id="urAffectation"></span> ?
        </div>
        <div class="modalEditFooter">
            <div class="adminDanger btnMedium mr10 modalEditClose">Annuler</div>
            <div class="adminPrimary btnMedium mr10" id="confirmAffectationUr" data-identifiant="" data-ur="">Valider</div>
        </div>
    </div>
@endsection
@section('css')
    <link href="{{ asset('css/admin_fpf.css') }}" rel="stylesheet">
@endsection
