@extends('layouts.email')
@section('content')
    <div class="mailContent">
        <div class="text" style="font-size: 16px;">
            Bonjour.<br><br>
            La session en date du {{ date("d/m/Y",strtotime($session->start_date)) }} de la formation {{ $session->formation->name }} est prévue dans moins de {{ $session->type == 0 ? '2' : '10' }} jours mais aucune décision (confirmation / annulation) n'a été enregistrée.<br>
            Merci de vous rendre sur votre outil de gestion et d'indiquer la décision sur la tenue de la formation.
            <br><br>
            Bien cordialement.<br>
            Le Département Formation
        </div>
    </div>
@endsection
