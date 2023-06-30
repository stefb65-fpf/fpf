@extends('layouts.default')

@section('content')
    <div class="pageCanva">
        <h1 class="pageTitle">
            Espace de gestion des clubs pour la FPF
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
                On affiche ici la liste des clubs, triés par numéro, avec pagination.<br>
                Pour chaque club, possibilité de modifier les informations du club comme le ferait un responsable club.<br>
                Possibilité d'ajout de clubs (attention, il est nécessaire de saisir un contact lors de l'ajout du club).<br>
                Filtre à mettre en place pour l'affichage des clubs (par UR, statut, type de carte, abonnements).<br>
            </p>
        </div>
        <div class="alertDanger" style="width: 80% !important">
            <div class="bold">on récupère la liste des clubs dans $clubs</div>
            <div>pour chaque club, on affiche une ligne avec les informations numéro (complémenté à 4), UR (complémenté à 2), nom, email si présent avec lien mailto,
                un petit bloc coordonnées + telephone ($club->adresse), nom, prénom du contact ($club->contact) plus deux actions Editer et listes des adhérents</div>
            <div>On prévoir également sur la ligne d'afficher le statut de manière visuelle (petit rond de couleur): orange (0 non renouvelé), jaune (1 pré inscrit), vert (2 validé) et gris (3 désactivés)</div>
        </div>
        <div class="alertDanger" style="width: 80% !important">
            <div class="bold">on prévoit un système de filtre avec plusieurs possibilité de filtrer l'affichage par ur, par statut, par type</div>
        </div>
        <div class="alertDanger" style="width: 80% !important">
            <div class="bold">il faut prévoir de pouvoir ajouter un club don un botuon / icône bien visible (avec redirection vers formulaire d'ajout ensuite)</div>
        </div>

    </div>
@endsection
