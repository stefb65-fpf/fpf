@extends('layouts.email')
@section('content')
    <div class="mailContent">
        <div class="text" style="font-size: 16px;">
            Bonjour.<br><br>
            La session en date du {{ date("d/m/Y",strtotime($session->start_date)) }} de la formation {{ $session->formation->name }} se tiendra comme prévu.<br>
            @if($session->type == 0)
                Le lien de connexion vous sera transmis 24h avant la tenue de la formation. Merci d'être ponctuel.
            @else
                La formation se tiendra à l'adresse {{ $session->location }}. Merci de vous présenter suffisamment à l'avance.
            @endif
            <br><br>
            Bien cordialement.<br>
            Le Département Formation
        </div>
    </div>
@endsection
