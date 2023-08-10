@extends('layouts.account')
@section('contentaccount')
    <div class="accountContent">
        <div class="welcome">Bienvenue sur votre compte <span style="font-weight: bold;text-transform: capitalize">{{ $user->prenom.' '.$user->nom }}</span></div>
        @if(isset($cartes[0]) && is_null($cartes[0]->clubs_id) && !in_array($cartes[0]->statut, [2,3]))
            <div class="alertInfo" style="width: 80% !important">
                <span class="bold">Informations !</span>
                Votre adhésion en tant qu'individuel de la FPF avec votre carte {{ $cartes[0]->identifiant }} a expirée. Vous pouvez la renouveler en cliquant sur l'un des boutons ci-dessous.
                <div style="display: flex; justify-content: space-around; align-items: flex-start; margin-top: 20px;">
                    <div style="text-align: center; flex: 1; background-color: white; padding: 10px; margin: 10px;">
                        <div style="font-weight: bolder;">
                            Renouveler mon adhésion<br>pour la somme de {{ number_format($tarif, 2, ',', ' ') }} €
                        </div>
                        <button class="primary btnRegister" data-carte="{{ $cartes[0]->id }}" data-adhesion="adh" name="btnRenewIndividuel" data-personne="{{ $personne->id }}" data-montant="{{ $tarif }}" data-type="monext">Payer par carte bancaire</button>
                        <button class="primary btnRegister" data-carte="{{ $cartes[0]->id }}" data-adhesion="adh" name="btnRenewIndividuel" data-personne="{{ $personne->id }}" data-montant="{{ $tarif }}" data-type="bridge">Payer par virement</button>
                    </div>
                    @if($tarif_supp != 0)
                        <div style="text-align: center; flex: 1; background-color: white; padding: 10px; margin: 10px;">
                            <div style="font-weight: bolder;">
                                Renouveler mon adhésion et mon abonnement<br> pour la somme de {{ number_format(floatval($tarif) + floatval($tarif_supp), 2, ',', ' ') }} €
                            </div>
                            <button class="primary btnRegister" data-carte="{{ $cartes[0]->id }}" data-adhesion="all" name="btnRenewIndividuel" data-personne="{{ $personne->id }}" data-montant="{{ floatval($tarif) + floatval($tarif_supp) }}" data-type="monext">Payer par carte bancaire</button>
                            <button class="primary btnRegister" data-carte="{{ $cartes[0]->id }}" data-adhesion="all" name="btnRenewIndividuel" data-personne="{{ $personne->id }}" data-montant="{{ floatval($tarif) + floatval($tarif_supp) }}" data-type="bridge">Payer par virement</button>
                        </div>
                    @endif
                </div>

            </div>
        @endif
    </div>
@endsection
@section('js')
    <script src="{{ asset('js/moncompte.js') }}?t=<?= time() ?>"></script>
@endsection
