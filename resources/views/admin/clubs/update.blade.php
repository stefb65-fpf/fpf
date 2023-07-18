@extends('layouts.default')

@section('content')
    <div class="pageCanva">
        <h1 class="pageTitle">
            Informations pour le club {{ $club->nom }}
            <a class="previousPage" title="Retour page précédente" href="{{ route('clubs.gestion') }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor"
                     class="bi bi-reply-fill" viewBox="0 0 16 16">
                    <path
                        d="M5.921 11.9 1.353 8.62a.719.719 0 0 1 0-1.238L5.921 4.1A.716.716 0 0 1 7 4.719V6c1.5 0 6 0 7 8-2.5-4.5-7-4-7-4v1.281c0 .56-.606.898-1.079.62z"/>
                </svg>
            </a>
        </h1>
        <div class="alertInfo" style="width: 80% !important">
            Ici vous avez la possibilité de modifier les informations relative au club sélectionné : informations générales, coordonnées, réunions, activités et équipement.
            <br>
            Vous pouvez aussi consulter l'état de l'abonnement du club, son statut et le nombre d'adhérents.
        </div>
@include('clubs.update_form_club', ['pathPrefixName' => 'FPFGestion_'])
    </div>
@endsection

