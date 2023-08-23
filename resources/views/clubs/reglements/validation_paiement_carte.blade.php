@extends('layouts.default')

@section('content')
    <div class="pageCanva">
        <h1 class="pageTitle">
            Règlement par Carte Bancaire
            <a class="previousPage" title="Retour page précédente" href="{{ route('clubs.gestion') }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-reply-fill" viewBox="0 0 16 16">
                    <path d="M5.921 11.9 1.353 8.62a.719.719 0 0 1 0-1.238L5.921 4.1A.716.716 0 0 1 7 4.719V6c1.5 0 6 0 7 8-2.5-4.5-7-4-7-4v1.281c0 .56-.606.898-1.079.62z"/>
                </svg>
            </a>
        </h1>
        @if($code == 'ok')
            <div class="alertSuccess w80" >
                <span class="bold">Informations !</span>
                Votre paiement par carte bancaire a bien été accepté et votre règlement validé.
                Un mail de confirmation vous a été envoyé.<br>
                Les adhérents du club sont désormais inscrits et vont recevoir leur carte prochainement.
            </div>
        @else
            <div class="alertDanger w80">
                <span class="bold">Informations !</span>
                Votre paiement par carte bancaire n'a pas été accepté.<br>
                Vous pouvez retrouver votre bordereau de règlement dans votre espace "Bordereaux et règlements". Vous pourrez alors essayer un autre moyen de paiement.
            </div>
        @endif
    </div>
@endsection
