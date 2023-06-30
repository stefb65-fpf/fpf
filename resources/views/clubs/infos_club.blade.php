@extends('layouts.default')

@section('content')
    <div class="pageCanva">
        <h1 class="pageTitle">
            Informations pour le club {{ $club->nom }}
            <a class="previousPage" title="Retour page précédente" href="{{ route('clubs.gestion') }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-reply-fill" viewBox="0 0 16 16">
                    <path d="M5.921 11.9 1.353 8.62a.719.719 0 0 1 0-1.238L5.921 4.1A.716.716 0 0 1 7 4.719V6c1.5 0 6 0 7 8-2.5-4.5-7-4-7-4v1.281c0 .56-.606.898-1.079.62z"/>
                </svg>
            </a>
        </h1>
        <div class="alertDanger" style="width: 80% !important">
            <p>
                <span class="bold">Attention !</span>
                Cette page est en cours de développement. Elle n'est pas encore fonctionnelle.
            </p>
            <p style="margin-top: 20px">
                on affiche ici informations du club par bloc (coordonnées, équipements, activités, logo), chaque bloc étant modifiable indépendamment<br>
                Les équiements et activités sont des cases à cocher parmi des activités et fonctions prédéfinies.<br>
            </p>
        </div>
        <div class="alertDanger" style="width: 80% !important">
            <div class="bold">on récupère les infos du club avec dans la variable $club</div>
            {{ $club }}
        </div>
        <div class="alertDanger" style="width: 80% !important">
            <div class="bold">on récupère les coordonnées dans $club->adresse</div>
            {{ $club->adresse }}
        </div>
        <div class="alertDanger" style="width: 80% !important">
            <div class="bold">on récupère l'ensemble des activités de la FPF dans $activites</div>
            {{ $activites }}
            <br>
            <div class="bold">et les activités proposées par le club dans $club->activites</div>
            {{ json_encode($club->activites) }}
        </div>
        <div class="alertDanger" style="width: 80% !important">
            <div class="bold">on récupère l'ensemble des équipements de la FPF dans $equipements</div>
            {{ $equipements }}
            <br>
            <div class="bold">et les équipements proposés par le club dans $club->equipements</div>
            {{ json_encode($club->equipements) }}
        </div>
    </div>
@endsection
