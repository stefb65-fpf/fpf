@extends('layouts.email')
@section('content')
    <div class="mailContent">
        {{--    monlien est {{ $link }}--}}
        <div class="text"> Vous avez réinitialisé votre de mot de passe avec succès.<br>
           À bientôt sur le site Fédération Photo !
        </div>
        <div class="notWorking">Rendez-vous sur votre espace personnel pour vous connecter.<br>
           </div>
    </div>
@endsection
