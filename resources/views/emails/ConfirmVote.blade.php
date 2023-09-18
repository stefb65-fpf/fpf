@extends('layouts.email')
@section('content')
    <div class="mailContent">
        <div class="text" style="font-size: 16px;">
            Nous vous confirmons la prise en compte de votre vote ou décision pour la session {{ $vote->nom }}.
        </div>
    </div>
@endsection
