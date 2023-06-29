@extends('layouts.account')
@section('contentaccount')
    <div class="accountContent">
        <div class="welcome">   Bienvenue sur votre compte <span style="font-weight: bold;text-transform: capitalize"> {{ $user->prenom.' '.$user->nom }}</span></div>
    </div>
@endsection
