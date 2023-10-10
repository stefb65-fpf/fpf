@extends('layouts.email')
@section('content')
    <div class="mailContent">
        <div class="text" style="font-size: 16px;">
            Vous avez participé à la formation {{ $session->formation->name }} pour la session du {{ date("d/m/Y",strtotime($session->start_date)) }}.<br>
            Afin de pouvoir améliorer notre offre, nous vous invitons à remplir un formulaire d'évaluation en cliquant sur le lien suivant : <br>
            <div style="text-align: center; font-size: large; font-weight: bolder;">
                <a href="{{ route('formations.evaluation', md5($session->id)) }}">&Eacute;valuer la formation</a>
            </div>

            <br><br>
            Après avoir cliqué sur le lien, si vous n'êtes pas connecté à la base en ligne, vous devrez vous identifier.<br>
        </div>
    </div>
@endsection
