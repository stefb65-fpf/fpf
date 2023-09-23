@extends('layouts.account')
@section('contentaccount')
    <div class="accountContent">
        @if($code == 'ok')
            <div class="alertSuccess w80" >
                <span class="bold">Informations !</span>
                Votre paiement par carte bancaire a bien été accepté et votre règlement validé.
                Un mail de confirmation vous a été envoyé.<br>
                Pour voir apparaître votre nouvelle carte, il est nécessaire de vous déconnecter et de vous reconnecter à votre compte.
            </div>
        @else
            <div class="alertDanger w80">
                <span class="bold">Informations !</span>
                Votre paiement par carte bancaire n'a pas été accepté.<br>
                Vous pouvez renouveler votre demande et choisir un autre moyen de paiement.
            </div>
        @endif
    </div>
@endsection
