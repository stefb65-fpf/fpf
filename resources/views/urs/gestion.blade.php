@extends('layouts.default')

@section('content')
    <div class="adminUrPage pageCanva">
        <h1 class="pageTitle">
            Espace de gestion de l'UR {{ $ur->nom }}
        </h1>
        <div class="cardContainer">
            <a class="card" href="{{ route('urs.infos_ur') }}">
                <div class="wrapper">
                    <div class="cardTitle">Infos UR</div>
                </div>
            </a>
            <a class="card" href="{{ route('urs.liste_clubs') }}">
                <div class="wrapper">
                    <div class="cardTitle">Clubs</div>
                </div>
            </a>
            <a class="card" href="/urs/personnes/ur_adherents">
                <div class="wrapper">
                    <div class="cardTitle">Adhérents</div>
                </div>
            </a>
            <a class="card" href="{{ route('urs.fonctions.liste') }}">
                <div class="wrapper">
                    <div class="cardTitle">Fonctions</div>
                </div>
            </a>
            <a class="card" href="{{ route('urs.liste_reversements') }}">
                <div class="wrapper">
                    <div class="cardTitle">Trésorerie, Reversements</div>
                </div>
            </a>
            @if(in_array('GESNEWUR', $droits_fpf) || in_array('GESNEWURCA', $droits_fpf))
                <a class="card" id="connectNewsletter">
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
