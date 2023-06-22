@extends('layouts.login')

@section('content')
    <div class="authWrapper">
        <div class="authLogo">
            <img src="{{ env('APP_URL').'storage/app/public/FPF-100-Logo-Seul.webp' }}" alt="Fédération Photographique de France">
        </div>
        <div class="authTitle">Récupération de votre <br>mot de passe</div>
        <div class="fosterRegister light text">

Nous allons vous envoyer des instructions pour recréer votre mot de passe associé à l'email <span style="font-weight: 500;color: #003d77">{{$email}}</span>.
            <br>
            Si vous ne recevez pas de courriel de notre part, vérifiez votre dossier spam et placez-nous dans votre liste blanche afin de recevoir nos courriels à l'avenir.
        </div>

    </div>
@endsection
