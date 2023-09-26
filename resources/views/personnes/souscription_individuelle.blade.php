@extends('layouts.account')
@section('contentaccount')
    <div class="accountContent">
        <h1 class="mt25">SOUSCRIPTION D'UNE NOUVELLE CARTE D'ADH&Eacute;RENT INDIVIDUEL</h1>
        <div class="w100">
            @if($available == 0)
                <div class="alertInfo w80">
                    <span class="bold">Informations !</span>
                    Pour souscrire une adhésion individuelle, votre adresse doit être complètement renseignée. Veuillez compléter votre profil avant de réaliser cette action.
                    <a href="/mon-profil" class="bolder">Compléter mon profil</a>
                </div>
            @else

                <div class="alertInfo w80">
                    <span class="bold">Informations !</span>
                    <div>
                        Vous pouvez souscrire une carte d'adhérent individuelle en étant rattaché à l'UR <b>{{ $ur ? $ur->nom : "Région Parisienne (le numéro d'ur sera déterminé après adhésion en fonction de votre adresse exacte)" }}</b>.<br>
                        Si cette UR ne correspond pas à votre attente, veuillez corriger votre adresse dans votre profil. <br>
                        Cette adhésion individuelle vous attribuera un nouvel identifiant FPF mais vous conserverez également les identifiants existants.
                    </div>
                    <div class="d-flex justify-around align-start mt20">
                        <div class="text-center flex-1 bgWhite p10 m10">
                            <div class="bolder">
                                Souscrire une adhésion individuelle<br>pour la somme de {{ number_format($tarif, 2, ',', ' ') }} €
                            </div>
                            <button class="primary btnRegister" data-adhesion="adh" name="btnAddIndividuel" data-personne="{{ $personne->id }}" data-montant="{{ $tarif }}" data-type="monext">Payer par carte bancaire</button>
                            <button class="primary btnRegister" data-adhesion="adh" name="btnAddIndividuel" data-personne="{{ $personne->id }}" data-montant="{{ $tarif }}" data-type="bridge">Payer par virement</button>
                        </div>
                    </div>

                </div>
            @endif
        </div>
    </div>
@endsection
@section('js')
    <script src="{{ asset('js/souscription_individuelle.js') }}?t=<?= time() ?>"></script>
@endsection
