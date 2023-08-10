@extends('layouts.login')
@section('content')
    <div class="pageCanva">
        <h1 class="pageTitle">
            Règlement par carte bancaire
        </h1>
        @if($code == 'ok')
            <div class="alertSuccess" style="width: 80% !important">
                <span class="bold">Informations !</span>
                Votre paiement par carte bancaire a bien été accepté et votre règlement validé.
                Un mail de confirmation vous a été envoyé.
            </div>
            <div style="margin-top: 30px;">
                <a class="customBtn" href="/login">Connectez-vous !</a>
            </div>
        @else
            <div class="alertDanger" style="width: 80% !important">
                <span class="bold">Informations !</span>
                Votre paiement par carte bancaire n'a pas été accepté.<br>
                Vous pouvez renouveler votre demande et choisir un autre moyend e paiement.
            </div>
        @endif
    </div>
@endsection
