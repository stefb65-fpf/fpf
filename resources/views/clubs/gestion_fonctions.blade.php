@extends('layouts.default')

@section('content')
    <div class="pageCanva">
        <h1 class="pageTitle">
            Gestion des fonctions pour le club {{ $club->nom }}
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
                on affiche ici les fonctions du club (président, trésorier, secrétaire, contact et webmaster.)<br>
                Comme le nombre de fonction est limité, on va afficher une ligne par fonction.<br>
                Président (+ nom, prénom, identifiant, email si président renseigné - correspond à la fonction 94)<br>
                Contact (+ nom, prénom, identifiant, email si contact renseigné - correspond à la fonction 97)<br>
                Trésorier (+ nom, prénom, identifiant, email si trésorie renseigné - correspond à la fonction 95)<br>
                Secrétaire (+ nom, prénom, identifiant, email si secrétaire renseigné - correspond à la fonction 96)<br>
                Webmaster (+ nom, prénom, identifiant, email si webmaster renseigné - correspond à la fonction 320)<br><br>
                Si la ligne est vide, on permet l'ajout d'un adhérent pour cette fonction. L'adhérent doit être choisi parmi les adhérents du club. <br><br>
                Si la fonction est renseignée, on peut supprimer l'attribution à l'adhérent ou le remplacer par un autre adhérent du club (à choisir parmi une liste)<br>
                Dans le contrôleur, on récupère tous les adhérents du club et on les affiche dans une liste déroulante utilisable pour chaque fonction.<br>
                On peut remplacer le contact mais pas le supprimer.<br>
                Pour nous, toutes les fonctions peuvent être occupées par une même personne
            </p>
        </div>
        <div class="alertDanger" style="width: 80% !important">
            <p>
            <div class="bold">on récupère les fonctions attribuées dans $fonctions</div>
            {{ $fonctions }}
            </p>
        </div>
        <div class="alertDanger" style="width: 80% !important">
            <p>
            <div class="bold">on récupère la liste de tous les adherents dans $adherents</div>
            {{ $adherents }}
            </p>
        </div>
    </div>
@endsection
