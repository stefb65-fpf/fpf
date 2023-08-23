@extends('layouts.default')

@section('content')
    <div class="pageCanva">
        <h1 class="pageTitle">
            Routage France Photo
            <a class="previousPage" title="Retour page précédente" href="{{ route('admin.gestion_publications') }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-reply-fill" viewBox="0 0 16 16">
                    <path d="M5.921 11.9 1.353 8.62a.719.719 0 0 1 0-1.238L5.921 4.1A.716.716 0 0 1 7 4.719V6c1.5 0 6 0 7 8-2.5-4.5-7-4-7-4v1.281c0 .56-.606.898-1.079.62z"/>
                </svg>
            </a>
        </h1>
        <div class="text-center">
                <div class="bold mt10">Prochain numéro à éditer: <span class="fs2rem">{{ $numeroencours }}</span></div>
                <div class="bold mt10">Nombre d'adhérents recevant le numéro: {{ $nbabos }}</div>
                <div class="bold mt10">Nombre de clubs recevant le numéro: {{ $nbclubsAbos }}</div>
                <div class="bold mt10">Nombre total d'exemplaires à éditer: {{ $nbabos + $nbclubsAbos }}</div>
                <div class="d-flex justify-around mt100;">
                    <a href="{{ route('admin.generateRoutageFp', 0) }}" class="adminSuccess mr10">Fichier de contrôle</a>
                    <a data-method="get" data-confirm="Voulez-vous vraiment valider le routage ? Le numéro en cours va être modifié et le statut des abonnements sera affecté" href="{{ route('admin.generateRoutageFp', 1) }}" class="adminPrimary" class="ml10">Valider le routage et éditer le fichier</a>
                </div>

        </div>
    </div>
@endsection
@section('css')
    <link href="{{ asset('css/admin_fpf.css') }}" rel="stylesheet">
@endsection
