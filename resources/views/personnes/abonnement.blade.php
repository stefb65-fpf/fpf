@extends('layouts.account')
@section('contentaccount')
    <div class="accountContent justify-start align-start mt10">
        @if($user->is_abonne == 1)
            <div class="mt5">
                Votre abonnement France Photographie est valable jusqu'au numéro: <b>{{ $user->abonnement->fin }}</b>.
            </div>
        @else
            <div class="mt5">
                Vous n'êtes pas actuellement abonné au magazine France Photographie.
            </div>
        @endif
        <div class="mt5">
            Le numéro actuel de France Photographie est le numéro: <b>{{ $numero_encours }}</b>.
        </div>

        @if($user->is_abonne == 1 && $user->abonnement->fin > $numero_encours + 1)
            <div class="mt5">
                Il sera possible de vous réabonner en cliquant sur le bouton ci-dessous à partir du numéro: <b>{{ $user->abonnement->fin - 1 }}</b>.<br>
                Le bouton de réabonnement restera inactif jusqu'à la sortie de ce numéro. Vous serez également notifié de cette possibilité de réabonnement par courriel.
            </div>
            <div class="mt20">
                <button class="btnNormal accountColor w100 mt10" disabled="disabled">S'abonner</button>
            </div>
        @else
            <div class="mt20">
                Vous pouvez d'ores et déjà vous (ré)abonner en cliquant sur le bouton ci-dessous.
            </div>
            <div class="mt20">
                <a class="btnNormal accountColor" href="{{ route('souscription-abonnement') }}">S'abonner</a>
            </div>
        @endif
    </div>
@endsection
