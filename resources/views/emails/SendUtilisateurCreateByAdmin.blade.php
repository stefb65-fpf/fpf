@extends('layouts.email')
@section('content')
    <div class="mailContent">
        <div class="text" style="font-size: 16px;">
            Votre compte adhérent FPF vient d'être créé par un administrateur.<br>
            Vous pouvez vous connecter sur l'outil de gestion de la FPF à l'adresse <a href="https://fpf.federation-photo.fr">https://fpf.federation-photo.fr</a> en utilisant votre adresse email <b>{{ $email }}</b>.<br>
            Lors de votre première connexion, vous devrez initialiser votre mot de passe avant de profiter des services proposés.
        </div>
    </div>
@endsection
