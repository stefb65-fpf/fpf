@extends('layouts.email')
@section('content')
    <div class="mailContent">
        <div class="text" style="font-size: 16px;">
            Un membre de votre club vient de saisir une demande de contact sur la base en ligne de la FPF.
            <div>
                <b>Adhérent: </b>{{ $identifiant }} - {{ $personne->nom }} {{ $personne->prenom }} - <a href="mailto:{{ $personne->email }}">{{ $personne->email }}</a>
            </div>
            <div>
                <div style="font-weight: bold">Message</div>
                <div>{{ $text }}</div>
            </div>
        </div>

    </div>
@endsection
