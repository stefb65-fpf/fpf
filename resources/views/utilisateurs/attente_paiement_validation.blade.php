@extends('layouts.login')
@section('content')
    <div class="pageCanva">
        <h1 class="pageTitle">
            Règlement par virement
        </h1>
        <div class="alertInfo w80">
            <span class="bold">Informations !</span>
            Vous avez procédé au paiement par virement de votre adhésion / abonnement. Votre règlement sera validé par la FPF dès confirmation de votre virement, ce qui ne devrait prendre que quelques minutes.<br>
            Dès réception, vous recevrez un mail de confirmation de la FPF.
        </div>
{{--        <div>--}}
{{--            <a class="customBtn" href="/login">Connectez-vous !</a>--}}
{{--        </div>--}}
    </div>
@endsection
