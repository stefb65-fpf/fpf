@extends('layouts.default')

@section('content')
    <div class="pageCanva">
        <h1 class="pageTitle">
            Espace de gestion des urs pour la FPF
            <a class="previousPage" title="Retour page précédente" href="{{ route('admin.structures') }}">
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
                On affiche ici la liste des 25 urs avec la possibilité de modifier les informations UR et les fonctions comme le ferait un administrateur de l'UR.<br>
                Pas de possibilité d'ajout ou de suppression d'urs.<br>
            </p>
        </div>

        <div class="alertDanger" style="width: 80% !important">
            <div class="bold">on récupère la liste des urs dans $urs</div>
            <div>pour chaque ur, on affiche les informations nom UR, email avec lien mailto, site web avec lien,
                un petit bloc coordonnées + telephone ($ur->adresse), nom, prénom et identifiant du président ($ur->president) plus deux actions Editer et Fonctions</div>
        </div>


{{--        <a data-method="delete"  data-confirm="Voulez-vous vraiment supprimer bla bla ?">test delete HELLEBORE</a>--}}
    </div>
@endsection
