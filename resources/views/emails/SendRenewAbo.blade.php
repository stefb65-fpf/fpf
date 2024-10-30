@extends('layouts.email')
@section('content')
    <div class="mailContent">
        <div class="text" style="font-size: 16px;">
{{--            Le numéro <b>{{ $numero_encours }}</b> de France Photographie a été envoyé.<br>--}}
            Votre abonnement à la revue France Photographie est arrivé à échéance avec le numéro <b>{{ $fin_abonnement }}</b> (que vous avez déjà reçu ou en cours d'envoi).<br>
            Vous pouvez dès à présent vous réabonner en allant sur votre espace personnel de la base en ligne ou en signalant votre souhait de réabonnement à votre club.<br>
            Vous pouvez accéder à votre espace personnel, avec votre adresse email, en cliquant sur le lien suivant : <a href="https://fpf.federation-photo.fr">https://fpf.federation-photo.fr</a><br>
        </div>

    </div>
@endsection
