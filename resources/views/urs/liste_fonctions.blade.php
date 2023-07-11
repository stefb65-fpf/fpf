@extends('layouts.default')

@section('content')
    <div class="pageCanva">
        <h1 class="pageTitle">
            Liste des fonctions de l'UR {{ $ur->nom }}
            <a class="previousPage" title="Retour page précédente" href="{{ route('urs.gestion') }}">
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
                On affiche ici la liste des fonctions de l'UR, la possibilité d'ajouter une fonction spécifique à l'UR,
                de lier un adhérent de l'UR à la fonction, de supprimer la liaison adhérent / fonction, de supprimer une fonction si elle spécifique à l'UR
            </p>
        </div>
    </div>
@endsection