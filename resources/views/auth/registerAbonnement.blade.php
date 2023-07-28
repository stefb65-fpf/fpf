@extends('layouts.login')

@section('content')
        <div class="authWrapper">
            <div class="authLogo">
                <img src="{{ env('APP_URL').'storage/app/public/FPF-100-Logo-Seul.webp' }}"
                     alt="Fédération Photographique de France">
            </div>
            <div class="authTitle">Enregistrement pour abonnement</div>
            <div class="fosterRegister">
                <div class="foster">Vous avez déjà un compte ?</div>
                <a href="/login"> Connectez-vous !</a>
            </div>
            @include('auth.registerForm', ['type' => 'abonnement'])
        </div>
@endsection
@section('js')
    <script src="{{ asset('js/register.js') }}?t=<?= time() ?>"></script>
@endsection
