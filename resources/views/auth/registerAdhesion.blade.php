@extends('layouts.login')

@section('content')
    <div class="authWrapper">
        <div class="authLogo">
            <img src="{{ env('APP_URL').'storage/app/public/FPF-100-Logo-Seul.webp' }}"
                 alt="Fédération Photographique de France">
        </div>
        <div class="authTitle">Enregistrement pour adhésion</div>
        <div class="fosterRegister">
            <div class="foster">Vous avez déjà un compte ?</div>
            <a href="/login"> Connectez-vous !</a>
        </div>
        <div style="color: #800; max-width: 800px; margin: 20px auto;">
            Vous allez procéder à <b>votre inscription individuelle à la FPF</b>, détachée de tout club ou collectif.<br>
            Votre adhésion intègre un abonnement à France Photographie pour 5 numéros:&nbsp;&nbsp;de manière obligatoire si vous avez plus de 25 ans, de manière facultative sinon.<br><br>
            <ul class="ml40">
                <li><b>Si vous souhaitez rejoindre un club</b>, merci de consulter leur liste sur <a class="blue" href="https://federation-photo.fr/les-clubs/" target="_blank">notre site</a> et de vous rapprocher du club choisi.</li>
                <li><b>Si vous êtes déjà membre d’un club</b>, rapprochez-vous du contact de votre club pour adhérer.</li>
                <li><b>Si vous êtes déjà adhérent individuel</b>, connectez-vous à votre compte pour renouveler votre adhésion.</li>
            </ul>
            <br>

            Si vous n'êtes pas sûr(e) du type de votre adhésion,
            contactez le secrétariat de la FPF (<a href="mailto:fpf@federation-photo.fr">fpf@federation-photo.fr</a>), car il n’est <b>pas possible</b> d’annuler ou modifier cette adhésion une fois réglée.
        </div>
        @include('auth.registerForm', ['type' => 'adhesion'])
    </div>
@endsection
@section('js')
    <script src="{{ asset('js/register.js') }}?t=<?= time() ?>"></script>
@endsection
