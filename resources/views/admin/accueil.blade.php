@extends('layouts.default')

@section('content')
    <div class="pageCanva">
        <h1 class="pageTitle">
            Espace de gestion FPF
        </h1>
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
                        <div class="cardTitle">Routage, éditions</div>
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
                        <div class="cardTitle">Reversements, Statistiques</div>
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
            @if(in_array('GESNEW', $droits_fpf))
                <a class="card">
                    <div class="wrapper">
                        <div class="cardTitle">Newsletter</div>
                    </div>
                </a>
            @endif
            <div class="card invisible">
            </div>
        </div>
    </div>
@endsection

