@extends('layouts.email')
@section('content')
    <div class="mailContent">
        <div class="text" style="font-size: 16px;">
            Bonjour.<br><br>
            Faute de participants, la session en date du {{ date("d/m/Y",strtotime($session->start_date)) }} de la formation {{ $session->formation->name }} est annulée.<br>
            @if($type == 0)
{{--                message pour les participants--}}
                @if(!$session->club_id && !$session->ur_id)
                    Votre compte adhérent sera recrédité sous forme d'avoir valable sur toute la saison.
                @endif
            @elseif($type == 1)
{{--                message UR ou Club--}}
                Votre compte {{ $session->club_id ? 'Club' : 'UR' }} sera recrédité sous forme d'avoir valable sur toute la saison.
            @else
{{--                message formateur--}}
                Les participants ont été prévenus en parrallèle de ce message.
            @endif
            <br><br>
            Bien cordialement.<br>
            Le Département Formation
        </div>
    </div>
@endsection
