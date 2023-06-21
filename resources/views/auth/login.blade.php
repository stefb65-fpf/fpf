@extends('layouts.login')

@section('content')
    <div class="authWrapper">
        <div class="authLogo">
            <img src="{{ env('APP_URL').'storage/app/public/FPF-100-Logo-Seul.webp' }}" alt="Fédération Photographique de France">
        </div>
        <div class="authTitle">Connexion</div>
        <div class="fosterContainer">
            <a class="firstConnexion" href="/forgotPassword">
                Vous avez un compte FPF mais c'est votre première connexion ?
            </a> <div class="separator"></div>
            <a  href="/register" class="fosterRegister">
                <div class="foster">Vous n'avez pas encore de compte ?</div>
                <span style="text-decoration: underline; font-weight: 600">Enregistrez-vous !</span>
            </a>
        </div>
        <form action="{{ action('App\Http\Controllers\LoginController@login') }}" method="POST" class="authForm">
            {{ csrf_field() }}
            <div class="customField">
                <label>E-mail</label>
                <input  type="email" name="email" maxlength="100" />
                <div class="error">message erreur</div>
            </div>
            <div class="customField">
                <label>Mot de passe</label>
                <div class="group">
                    <input type="password" name="password" maxlength="50" />
                    <div class="icons eye">
                        <div class="icon open">
                            <svg width="22" height="16" viewBox="0 0 22 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M1 8C1 8 4 1 11 1C18 1 21 8 21 8C21 8 18 15 11 15C4 15 1 8 1 8Z" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M11 11C12.6569 11 14 9.65685 14 8C14 6.34315 12.6569 5 11 5C9.34315 5 8 6.34315 8 8C8 9.65685 9.34315 11 11 11Z" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                        <div class="icon closed dark hidden">
                            <svg width="22" height="21" viewBox="0 0 22 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M1 11C1 11 4 4 11 4C18 4 21 11 21 11C21 11 18 18 11 18C4 18 1 11 1 11Z" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M11 14C12.6569 14 14 12.6569 14 11C14 9.34315 12.6569 8 11 8C9.34315 8 8 9.34315 8 11C8 12.6569 9.34315 14 11 14Z" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M3 20L19.0485 1.27673" stroke="black" stroke-width="2" stroke-linecap="round"/>
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="error">message erreur</div>
            </div>
            <button type="submit" class="button customBtn">Connectez-vous</button>
        </form>
        <a class="forgottenPswd" href="/forgotPassword">Vous avez oublié votre mot de passe ?</a>
        <a class="findClub" target="_blank" href="https://federation-photo.fr/les-clubs/">Vous recherchez <span class="accent">un club</span> ?</a>
    </div>
@endsection
