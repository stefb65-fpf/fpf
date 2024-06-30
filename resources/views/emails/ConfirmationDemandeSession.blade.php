@extends('layouts.email')
@section('content')
    <div class="mailContent">
        <div class="text" style="font-size: 16px;">
            Votre demande d'organisation de session pour la formation {{ $formation->name }} pour le compte de {{ $structure }} a bien été transmise au département formation de la FPF.<br>
            Ce mail va être traité dans les plus brefs délais.
        </div>
    </div>
@endsection
