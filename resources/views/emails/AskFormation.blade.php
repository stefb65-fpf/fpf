@extends('layouts.email')
@section('content')
    <div class="mailContent">
        <div class="text" style="font-size: 16px;">
            Une demande d'organisation de session pour la formation {{ $formation->name }} a été faite par {{ $structure }}.<br>
            Demande effectuée par :
            <ul>
                <li>{{ $personne->prenom.' '.$personne->nom }}</li>
                <li>{{ $personne->phone_mobile }}</li>
                @if(isset($personne->adresses[0]))
                    <li>{{ $personne->adresses[0]->libelle1 }}</li> - {{ $personne->adresses[0]->codepostal.' '.$personne->adresses[0]->ville }}</li>
                @endif
            </ul>
        </div>
    </div>
@endsection
