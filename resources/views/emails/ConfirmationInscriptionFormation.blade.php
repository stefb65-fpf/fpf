@extends('layouts.email')
@section('content')
    <div class="mailContent">
        <div class="text" style="font-size: 16px;">
            Nous vous confirmons votre inscription à la formation {{ $session->formation->name }} pour la session du {{ date("d/m/Y",strtotime($session->start_date)) }}.<br>
            Vous recevrez une information complémentaire par mail quelques jours avant le début de la formation.<br>
        </div>
    </div>
@endsection
