@extends('layouts.email')
@section('content')
    <div class="mailContent">
        <div class="text" style="font-size: 16px;">
            Nous vous confirmons la prise en charge financière par votre structure pour la session de formation {{ $session->formation->name }}
            pour la date du {{ date("d/m/Y",strtotime($session->start_date)) }}.<br>
        </div>
    </div>
@endsection
