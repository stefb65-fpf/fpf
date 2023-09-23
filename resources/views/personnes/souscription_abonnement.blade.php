@extends('layouts.account')
@section('contentaccount')
    <div class="accountContent">
        <h1 class="mt25">SOUSCRIPTION D'UN ABONNEMENT FRANCE PHOTO</h1>
        <div class="alertInfo w80">
            <span class="bold">Informations !</span>
            <div>
                Vous pouvez souscrire un abonnement à la revue FRANCE PHOTO pour 5 numéros au tarif de {{ number_format($tarif, 2, ',', ' ') }} €. <br>
                Si vous êtes actuellement abonné, votre abonnement sera prolongé de 5 numéros à compter de la fin de votre abonnement actuel. <br>
                @if($tarif_reduit == 0)
                    En étant adhérent d'un club, vous pouvez bénéficier d'un tarif réduit en renouvelant votre adhésion FPF par le club. Pour cela, rapprochez-vous de votre club.
                @endif
            </div>
            <div class="d-flex justify-around align-start mt20">
                <div class="text-center flex-1 bgWhite p10 m10">
                    <div class="bolder">
                        Souscrire un abonnement FP <br>pour la somme de {{ number_format($tarif, 2, ',', ' ') }} €
                    </div>
                    <button class="primary btnRegister" data-adhesion="adh" name="btnAddAbonnement" data-personne="{{ $personne->id }}" data-montant="{{ $tarif }}" data-type="monext">Payer par carte bancaire</button>
                    <button class="primary btnRegister" data-adhesion="adh" name="btnAddAbonnement" data-personne="{{ $personne->id }}" data-montant="{{ $tarif }}" data-type="bridge">Payer par virement</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script src="{{ asset('js/souscription_individuelle.js') }}?t=<?= time() ?>"></script>
@endsection
