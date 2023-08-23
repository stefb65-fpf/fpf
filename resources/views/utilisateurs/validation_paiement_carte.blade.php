@extends('layouts.login')
@section('content')
    <div class="pageCanva">
        <h1 class="pageTitle">
            Règlement par carte bancaire
        </h1>
        @if($code == 'ok')
            <div class="alertSuccess w80" >
                <span class="bold">Informations !</span>
                Votre paiement par carte bancaire a bien été accepté et votre règlement validé.
                Un mail de confirmation vous a été envoyé.
            </div>
            <div class="mt30">
                <a class="customBtn" href="/login">Connectez-vous !</a>
            </div>
        @else
            <div class="alertDanger w80">
                <span class="bold">Informations !</span>
                Votre paiement par carte bancaire n'a pas été accepté.<br>
                Vous pouvez renouveler votre demande et choisir un autre moyend e paiement.
            </div>
        @endif
    </div>
@endsection
