@extends('layouts.login')

@section('content')
    <div class="authWrapper">
        <div class="authLogo">
            <img src="{{ env('APP_URL').'storage/app/public/FPF-100-Logo-Seul.webp' }}" alt="Fédération Photographique de France">
        </div>
        <div class="authTitle">Récupération de votre <br>mot de passe</div>
        <div class="fosterRegister light">
            <div class="foster">Renseignez ici votre adresse mail de compte FPF. <br> Nous vous enverrons un lien pour réinitialiser votre mot de passe.</div>

        </div>
        <form action="{{ action('App\Http\Controllers\LoginController@sendResetAccountPasswordLink') }}" method="POST"  class="authForm align-center">
            {{ csrf_field() }}
            <div class="customField">
                <label >E-mail</label>
                <input  type="email" name="email"  >
                <div class="error">message erreur</div>
            </div>
            <button type="submit" class="button customBtn">Envoyez-moi un lien</button>
        </form>
    </div>
@endsection
