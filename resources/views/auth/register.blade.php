@extends('layouts.login')
@section('content')
    <div class="authWrapper">
        <div class="authLogo">
            <img src="{{ env('APP_URL').'storage/app/public/FPF-100-Logo-Seul.webp' }}" alt="Fédération Photographique de France">
        </div>
        <div class="authTitle">Enregistrement</div>
        <div class="fosterRegister light">
            <div class="foster">Vous avez déjà un compte ?</div>
            <a href="/login"> Connectez-vous !</a>
        </div>
        <div class="cardContainer">
            <div class="card">
                <a href="/registerAdhesion" class="wrapper">
                    <div class="cardTitle">Je souhaite devenir adhérent de la FPF</div>
                    <div class="type">Je peux ainsi participer aux concours et bénéficier d'avantages</div>
                </a>
            </div>
            <div class="card">
                <a href="/registerAbonnement" class="wrapper">
                    <div class="cardTitle">Je souhaite m'abonner à la revue France Photographie</div>
                    <div class="type">Je reçois 5 numéros de la revue</div>
                </a>
            </div>
{{--            <div class="card">--}}
{{--                <a href="/registerFormation" class="wrapper">--}}
{{--                    <div class="cardTitle">Je souhaite m'inscrire à une formation</div>--}}
{{--                    <div class="type">J'accède à la liste des formations dans toute la France</div>--}}
{{--                </a>--}}
{{--            </div>--}}
        </div>
    </div>
@endsection
