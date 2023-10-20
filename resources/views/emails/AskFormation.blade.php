@extends('layouts.email')
@section('content')
    <div class="mailContent">
        <div class="text" style="font-size: 16px;">
            Une demande d'organisation de session pour la formation {{ $formation->name }} a été faite par {{ $structure }}.
        </div>
    </div>
@endsection
