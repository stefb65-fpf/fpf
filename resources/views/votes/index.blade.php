@extends('layouts.account')
@section('contentaccount')
    <div class="accountContent">
        <h1>{{ $vote->nom }}</h1>
        <div class="alertInfo w80 mt25">
            Pour cette session de votes, vous avez {{ sizeof($elections) }}  élection(s).
            Vous devez indiquer votre choix pour chacune d'entre elles et valider votre vote final en bas de page.<br>
            Un code vous sera alors envoyé par email ou SMS et vous devrez renseigner ce code afin de valider votre vote.
        </div>

        <div>
            @foreach($elections as $k => $election)
                <div class="mt30 pt10 borderTopBlue">
                    <div class="bolder">Election n° {{ $k + 1 }} - {{ $election->nom }}</div>
                    <div class="ml40 mb25">
                        {!! $election->contenu !!}
                    </div>
                    <div class="ml40">
                        @if($election->type == 1)
                            <div>
                                Cette élection est une motion et vous pouvez choisir une réponse parmi celles proposées.
                            </div>
                            <div class="ml50">
                                @foreach($election->motions as $j => $motion)
                                    <div class="mt10">
                                        <input type="radio" data-motion="1" name="motion_{{ $election->id }}" value="{{$motion->id}}" checked="checked">
                                        <label>{{ $motion->reponse->libelle }}</label>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div>
                                Cette élection doit pourvoir {{ $election->nb_postes }} postes et vous pouvez donc voter pour un maximum de {{ $election->nb_postes }} candidats parmi les candidats qui se présentent.<br>
                                Il vous faut donc cocher 0 à {{ $election->nb_postes }} cases dans la liste suivante. Si vous cochez plus de candidats que le nombre de postes à pourvoir, seuls les {{ $election->nb_postes }} premiers dans l'ordre d'apparition seront retenus.
                            </div>
                            <div class="ml50">
                                @foreach($election->candidats as $j => $candidat)
                                    <div class="mt10">
                                        <input type="checkbox" name="election_{{ $election->id }}" value="{{$candidat->id}}">
                                        <label>{{ $candidat->personne->nom }} {{ $candidat->personne->prenom }}</label>
                                    </div>
                                @endforeach
                            </div>

                        @endif
                    </div>
                    <div>

                    </div>
                </div>
            @endforeach
        </div>
        <div class="mt60">
            @if($vote_club)
                <a class="customBtn primary" name="saveVote" data-vote="{{ $vote->id }}" data-voix="2">J'enregistre mon vote et le vote du club (2 voix)</a>
                <a class="customBtn primary" name="saveVote" data-vote="{{ $vote->id }}" data-voix="1">J'enregistre uniquement mon vote (1 voix)</a>
            @else
                <div>
                    @if($vote->type == 1 && $vote->phase == 2)
                        Pour cette phase de vote destinée aux clubs, vous disposez de {{ $nb_voix }} voix
                    @endif
                    @if($vote->type == 1 && $vote->phase == 3)
                        Pour cette phase de vote destinée aux URs, vous disposez de {{ $nb_voix }} voix
                    @endif
                </div>
                <div class="mt25 text-center">
                    <a class="customBtn primary" name="saveVote" data-vote="{{ $vote->id }}" data-voix="{{ (in_array($vote->phase, [2,3]) && $vote->type == 1) ? $nb_voix : 1 }}">J'enregistre mon vote</a>
                </div>
            @endif

        </div>



        <div class="modalEdit d-none" id="modalVoteSendCode">
            <div class="modalEditHeader">
                <div class="modalEditTitle">VOTE FPF</div>
                <div class="modalEditClose">
                    X
                </div>
            </div>
            <div class="modalEditBody">
                Pour confirmer votre vote, nous allons vous envoyer un code d'une durée de validité de 20 minutes.
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
                <div class="customBtn primary mr10" id="confirmSendCodeVote">Envoyer le code</div>
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
                <div class="customBtn primary mr10" id="confirmSaisieCodeVote">Valider</div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script src="{{ asset('js/moncompte.js') }}?t=<?= time() ?>"></script>
@endsection
