@extends('layouts.email')
@section('content')
    <div class="mailContent">
        {{--    monlien est {{ $link }}--}}
        <div class="text"> Votre changement d'adresse mail a été enregistré.<br>
           À bientôt sur le site Fédération Photo !
        </div>
        <div class="notWorking">Rendez-vous sur votre espace personnel pour vous connecter.<br>
           </div>
    </div>
@endsection
