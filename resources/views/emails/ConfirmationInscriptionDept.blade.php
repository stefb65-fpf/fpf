@extends('layouts.email')
@section('content')
    <div class="mailContent">
        <div class="text" style="font-size: 16px;">
            Bonjour.<br><br>
            Je vous confirme que {{ $personne->prenom }} {{ $personne->nom }} ({{ $personne->email }}) s'est inscrit à la formation <b>{{ $session->formation->name }}</b> pour la session du {{ date("d/m/Y",strtotime($session->start_date)) }}.<br><br>
            Bien cordialement.<br>
            Le Département Formation
        </div>
    </div>
@endsection
