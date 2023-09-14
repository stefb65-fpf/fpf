@extends('layouts.account')
@section('contentaccount')
    <div class="accountContent">
        <div class="welcome">Bienvenue sur votre compte <span class="bold capitalize">{{ $user->prenom.' '.$user->nom }}</span></div>
        @if(isset($cartes[0]) && is_null($cartes[0]->clubs_id) && !in_array($cartes[0]->statut, [2,3]))
            @if($bad_profil == 1)
                <div class="alertInfo w80">
                    <span class="bold">Informations !</span>
                    Votre profil n'est pas complet. Vous devez le compléter avant de pouvoir adhérer à la FPF.
                    <a href="/mon-profil" class="bolder">Compléter mon profil</a>
                </div>
            @else
                <div class="alertInfo w80">
                    <span class="bold">Informations !</span>
                    Votre adhésion en tant qu'individuel de la FPF avec votre carte {{ $cartes[0]->identifiant }} a expirée. Vous pouvez la renouveler en cliquant sur l'un des boutons ci-dessous.
                    <div class="d-flex justify-around align-start mt20">
                        <div class="text-center flex-1 bgWhite p10 m10">
                            <div class="bolder">
                                Renouveler mon adhésion<br>pour la somme de {{ number_format($tarif, 2, ',', ' ') }} €
                            </div>
                            <button class="primary btnRegister" data-carte="{{ $cartes[0]->id }}" data-adhesion="adh" name="btnRenewIndividuel" data-personne="{{ $personne->id }}" data-montant="{{ $tarif }}" data-type="monext">Payer par carte bancaire</button>
                            <button class="primary btnRegister" data-carte="{{ $cartes[0]->id }}" data-adhesion="adh" name="btnRenewIndividuel" data-personne="{{ $personne->id }}" data-montant="{{ $tarif }}" data-type="bridge">Payer par virement</button>
                        </div>
                        @if($tarif_supp != 0)
                            <div class="text-center flex-1 bgWhite p10 m10">
                                <div class="bolder">
                                    Renouveler mon adhésion et mon abonnement<br> pour la somme de {{ number_format(floatval($tarif) + floatval($tarif_supp), 2, ',', ' ') }} €
                                </div>
                                <button class="primary btnRegister" data-carte="{{ $cartes[0]->id }}" data-adhesion="all" name="btnRenewIndividuel" data-personne="{{ $personne->id }}" data-montant="{{ floatval($tarif) + floatval($tarif_supp) }}" data-type="monext">Payer par carte bancaire</button>
                                <button class="primary btnRegister" data-carte="{{ $cartes[0]->id }}" data-adhesion="all" name="btnRenewIndividuel" data-personne="{{ $personne->id }}" data-montant="{{ floatval($tarif) + floatval($tarif_supp) }}" data-type="bridge">Payer par virement</button>
                            </div>
                        @endif
                    </div>

                </div>
            @endif
        @else
            @if($bad_profil == 1)
                <div class="alertInfo w80">
                    <span class="bold">Informations !</span>
                    Votre profil n'est pas complet. Vous devez le compléter pour pouvoir bénéficier de l'ensemble des services de la FPF.
                    <a href="/mon-profil" class="bolder">Compléter mon profil</a>
                </div>
            @endif
        @endif

        @if($votes_encours && sizeof($votes_encours) > 0)
            <div class="alertInfo w80">
                <span class="bold">Informations !</span>
                Vous pouvez actuellement voter pour les sessions d'élections FPF suivantes:
                <div>
                    Pour valider les votes, nous envoyons un code par SMS ou email.
                    @if($personne->phone_mobile != '')
                        Votre numéro de téléphone mobile renseigné est {{ $personne->phone_mobile }}
                        <div>
                            Si le numéro de téléphone est incorrect, vous pouvez le modifier en vous rendant sur votre <a href="mon-profil">profil</a>.
                        </div>
                    @else
                        Vous n'avez pas renseigné de numéro de téléphone mobile. Vous pouvez le renseignant en vous rendant sur votre <a href="mon-profil">profil</a>.
                    @endif
                </div>
                @foreach($votes_encours as $v)
                    <div class="mt25 borderTopBlue pt10">
                        @if($v->type == 0)
                            <div>
                                <span class="bolder">{{ $v->nom }}</span>: du <?= substr($v->debut, 8, 2).'/'.substr($v->debut, 5, 2).'/'.substr($v->debut, 0, 4) ?> 0h00 au <?= substr($v->fin, 8, 2).'/'.substr($v->fin, 5, 2).'/'.substr($v->fin, 0, 4) ?> 23h59
                            </div>
                        @else
                            <div>
                                <span class="bolder">{{ $v->nom }}</span>
                                <ul class="ml40">
                                    <li>
                                        vote de tous les adhérents : du <?= substr($v->debut, 8, 2).'/'.substr($v->debut, 5, 2).'/'.substr($v->debut, 0, 4) ?> 3h00 au <?= substr($v->fin_phase1, 8, 2).'/'.substr($v->fin_phase1, 5, 2).'/'.substr($v->fin_phase1, 0, 4) ?> 23h59. Lors de cette phase, vous pouvez voter directement. Si vous ne votez pas, votre voix sera transférée directement au président de club pour la seconde phase. Si vous souhaitez que ce ne soit pas le cas, cliquez sur le bouton "je ne donne pas mon pouvoir".
                                    </li>
                                    <li>
                                        vote responsables clubs (voix du club et pouvoirs des adhérents): du <?= substr($v->debut_phase2, 8, 2).'/'.substr($v->debut_phase2, 5, 2).'/'.substr($v->debut_phase2, 0, 4) ?> 3h00 au <?= substr($v->fin_phase2, 8, 2).'/'.substr($v->fin_phase2, 5, 2).'/'.substr($v->fin_phase2, 0, 4) ?> 23h59. Lors de cette phase, vous pouvez voter ou transférer vos voix au président d'UR.
                                    </li>
                                    <li>
                                        vote des présidents d'UR (pouvoirs issus du vote des clubs) : du <?= substr($v->debut_phase3, 8, 2).'/'.substr($v->debut_phase3, 5, 2).'/'.substr($v->debut_phase3, 0, 4) ?> 3h00 au <?= substr($v->fin, 8, 2).'/'.substr($v->fin, 5, 2).'/'.substr($v->fin, 0, 4) ?> 23h59
                                    </li>
                                </ul>
                            </div>
                        @endif
                        <div style="display: flex; justify-content: space-around; align-items: center;">
                            <a class="btnRegister success" href="{{ route('utilisateur.vote', $v->id) }}">JE VOTE</a>
                            @if($v->type == 1 && $v->phase == 2)
                                <button class="btnRegister primary" name="givePouvoir" data-vote="{{ $v->id }}">JE DONNE MES POUVOIRS AU PR&Eacute;SIDENT D'UR</button>
                            @endif
                            @if($v->type == 1 && $v->phase == 1)
                                <button class="btnRegister primary" name="cancelPouvoir" data-vote="{{ $v->id }}">JE NE DONNE PAS MON POUVOIR</button>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
        @if($votes_futurs && sizeof($votes_futurs) > 0)
            <div class="alertInfo w80">
                <span class="bold">Informations !</span>
                Vous pourrez très prochainement accéder à des votes organisés par la FPF. Veuillez trouver ci-après la liste des votes à venir.
                <table class="styled-table w100">
                    <thead>
                    <tr>
                        <th>Date de début</th>
                        <th>Vote</th>
                        <th>Election</th>
                        <th>Type</th>
                        <th>Description</th>
                    </tr>
                    </thead>
                    @foreach($votes_futurs as $vote)
                        <tr>
                            <td>{{ substr($vote->debut, 8, 2).'/'.substr($vote->debut, 5, 2).'/'.substr($vote->debut, 0, 4) }}</td>
                            <td colspan="3" style="font-weight: bolder; font-size: large">{{ $vote->nom }}</td>

                        </tr>
                        @foreach($vote->elections as $election)
                            <tr>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>{{ $election->nom }}</td>
                                <td>{{ $election->type == 1 ? 'Motion' : 'Candidats' }}</td>
                                <td>{!! $election->contenu  !!}</td>
                            </tr>
                        @endforeach
                    @endforeach
                </table>
            </div>
        @endif
        <div class="modalEdit d-none" id="modalVoteSendCode">
            <div class="modalEditHeader">
                <div class="modalEditTitle">VOTE FPF</div>
                <div class="modalEditClose">
                    X
                </div>
            </div>
            <div class="modalEditBody">
                <div class="d-none" id="sentence-club">
                    Pour ce vote, vous décidez de ne pas donner votre pouvoir au président du club.
                </div>
                <div class="d-none" id="sentence-ur">
                    Pour ce vote, vous décidez de donner votre pouvoir au président de l'UR.
                </div>
                Pour confirmer votre décision, nous allons vous envoyer un code d'une durée de validité de 20 minutes.
                Choisissez comment vous souhaitez recevoir votre code.
                <div class="ml40">
                    @if($personne->phone_mobile != '')
                        <div>
                            <input type="radio" name="moyenVote" value="1" checked="checked"> par SMS au numéro <b>{{ $personne->phone_mobile }}</b>
                        </div>
                    @endif
                    <div>
                        <input type="radio" name="moyenVote" value="2" {{ $personne->phone_mobile == '' ? 'checked=checked' : '' }}> par email à l'adresse <b>{{ $personne->email }}</b>
                    </div>
                </div>
            </div>
            <div class="modalEditFooter">
                <div class="customBtn danger mr10 modalEditClose">Annuler</div>
                <div class="customBtn primary mr10" id="confirmSendCode">Envoyer le code</div>
            </div>
        </div>

        <div class="modalEdit d-none" id="modalSaisieCode">
            <div class="modalEditHeader">
                <div class="modalEditTitle">VOTE FPF</div>
                <div class="modalEditClose">
                    X
                </div>
            </div>
            <div class="modalEditBody">
                Veuillez indiquer le code que vous avez reçu par SMS ou email et validez pour prendre en compte votre décision.
                <div class="ml40 text-center">
                    <input type="text" style="height: 35px;padding:10px;" maxlength="6" id="codeForVote" placeholder="saisissez votre code">
                </div>
            </div>
            <div class="modalEditFooter">
                <div class="customBtn danger mr10 modalEditClose">Annuler</div>
                <div class="customBtn primary mr10" id="confirmSaisieCode">Valider</div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script src="{{ asset('js/moncompte.js') }}?t=<?= time() ?>"></script>
@endsection
