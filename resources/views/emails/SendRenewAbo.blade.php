@extends('layouts.email')
@section('content')
    <div class="mailContent">
        <div class="text" style="font-size: 16px;">
            Le numéro <b>{{ $numero_encours }}</b> de France Photographie a été envoyé.<br>
            Votre abonnement expire au numéro <b>{{ $fin_abonnement }}</b>.<br>
            Vous pouvez dès à présent vous réabonner en allant sur votre espace personnel de la base en ligne ou en signalant votre souhait de réabonnement à votre club.
        </div>

    </div>
@endsection
