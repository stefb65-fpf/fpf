<div class="alertInfo w80">
    <span class="bold">Informations !</span>
    Vous pouvez gérer le renouvellement des adhésions et abonnements des membres de votre club. Pour cela, cochez les
    adhérents que vous souhaitez renouveler ou abonner puis cliquez sur le bouton "Renouveler".<br>
    L'abonnement d'un membre du club se fait au tarif "abonné club" lorsque l'adhésion du membre est également renouvelée ou que celui-ci est déjà adhérent.
    Dan,s le cas contraire, le tarif appliqué est celui de l'abonnement normal.<br>
    Le club est automatiquement renouvelé lors du premier renouvellement des adhérents dans la saison.<br>
    Vous pouvez également abonner votre club au France Photo à tout moment.<br>
    Après génération du bordereau de renouvellement, si vous n'effectuez pas le paiement immédiatement, les adhérents sont en statut pré-inscrits.
    Ils sont alors cochés par défaut dans la liste des adhérents. Si vous souhaitez modifier votre bordereau, vous devez
    <ul class="ml50">
        <li>les laisser cochés pour prendre en compte leur adhésion</li>
        <li>les décocher si vous ne voulez finalement pas les renouveler</li>
    </ul>

</div>
<div class="alertSuccess w80" style="display: none;" id="alertAdherentsList">
    Le fichier des adhérents a bien été généré. Vous pouvez le télécharger en cliquant sur le lient suivant: <a
        class="underline pointer" id="linkAdherentsList" target="_blank">Télécharger le fichier</a>
</div>
<div class="filters d-flex">
    <div class="formBlock maxW100">
        <div class="formBlockTitle">Filtres</div>
        <div class="d-flex flexWrap">
            <div class="formUnit mb0">
                <div class="formLabel mr10 bold">Statut :</div>
                <select class="formValue modifying" name="filter" data-ref="statut">
                    <option value=""></option>
                    <option value="all" {{$statut == 'all' ? "selected" : ""}}>Tous</option>
                    <option value="1" {{$statut == 1 ? "selected" : ""}}>Pré-inscrits</option>
                    <option value="2" {{$statut == 2 ? "selected" : ""}}>Validés</option>
                    <option value="3" {{$statut == 3 ? "selected" : ""}}>Carte éditée</option>
                    <option value="0" {{$statut == 0 ? "selected" : ""}}>Non renouvelés</option>
                    <option value="4" {{$statut == 4 ? "selected" : ""}}>Anciens (non renouvelés > 1 an)</option>
                </select>
            </div>
            <div class="formUnit mb0">
                <div class="formLabel mr10 bold">Abonnement :</div>
                <select class="formValue modifying" name="filter" data-ref="abonnement">
                    <option value="all">Tous</option>
                    <option value="1" {{$abonnement == 1 ? "selected" : ""}}>Avec</option>
                    <option value="0" {{$abonnement == 0 ? "selected" : ""}}>Sans</option>
                </select>
            </div>
        </div>
    </div>
</div>
@if(!sizeof($adherents))
    <div class="w100 text-center"> Ce club ne possède aucun adhérent répondant aux critères selectionnés.</div>

@else
    <div class="mt25 flexEnd">
        @switch($club->statut)
            @case(0)
                <div class="statutClub orange">Non renouvelé</div>
                @break
            @case(1)
                <div class="statutClub yellow">En cours d'inscription</div>
                @break
            @case(2)
                <div class="statutClub green">Club validé</div>
                @break
        @endswitch
        @if($club->is_abonne)
            <div class="statutClub green">Abonné</div>
        @else
            <div class="statutClub">Non abonné</div>
        @endif
        <button class="adminPrimary btnMedium" type="text" id="btnAdherentsList" data-club="{{$club->id}}">Liste des
            adhérents au format Excel
        </button>
        {{--        <button class="adminPrimary btnMedium ml10" type="text" id="btnAdherentsAjout" data-club="{{$club->id}}" >Ajouter un adhérent</button>--}}
        @if($prefix == '')
            <a class="adminPrimary btnMedium ml10" href="{{ route('clubs.adherents.create') }}">Ajouter un adhérent</a>
        @else
            <a class="adminPrimary btnMedium ml10" href="{{ route($prefix.'clubs.adherents.create', $club->id) }}">Ajouter
                un adhérent</a>
        @endif

        <button class="adminPrimary btnMedium ml10" type="text" id="renouvellementAdherents" data-club="{{$club->id}}" {{ $club->statut != 1 ? 'disabled' : '' }}>Renouveler
        </button>
    </div>
    <div class="d-flex justify-between mt20 w100">
        <div>
            <input type="checkbox" class="mr10" id="abonnementClub" {{ $club->aboPreinscrit ? 'checked=checked' : '' }}>
            Abonner le club jusqu'au
            numéro {{ $club->numero_fin_reabonnement }}
        </div>
        {{--        <div>--}}
        {{--            <a class="adminPrimary btnMedium" href="{{ route('clubs.adherents.create') }}">Ajouter un adhérent</a>--}}
        {{--        </div>--}}
    </div>
    <table class="styled-table">
        <thead>
        <tr>
            <th>Adhérer</th>
            <th>Abonner</th>
            <th>N°carte</th>
            <th>Nom</th>
            <th>Statut</th>
            <th>Courriel</th>
            <th>Abonnement - N° fin</th>
            <th>Type carte</th>
            <th colspan="2">Actions</th>
        </tr>
        </thead>
        <tbody>
        @foreach($adherents as $adherent)
            <tr>
                <td>
                @if(in_array($adherent->statut, [0, 1, 4]))
                    <input type="checkbox" name="adherer" data-ref="{{ $adherent->id_utilisateur }}"
                               data-identifiant="{{ $adherent->identifiant }}" {{ $adherent->statut == 1 ? 'checked=checked' : '' }} />
                @endif
                <td><input type="checkbox" name="abonner" data-ref="{{ $adherent->id_utilisateur }}"  {{ $adherent->aboPreinscrit == 1 ? 'checked=checked' : '' }} /></td>
                <td>{{$adherent->identifiant}}</td>
                <td>{{$adherent->personne->nom}} {{$adherent->personne->prenom}} </td>
                <td>
                    @switch($adherent->statut)
                        @case(0)
                            <div class="d-flex">
                                <div class="sticker orange" title="Non renouvelé"></div>
                            </div>
                            @break
                        @case(1)
                            <div class="d-flex">
                                <div class="sticker yellow" title="Préinscrit"></div>
                            </div>
                            @break
                        @case(2)
                            <div class="d-flex">
                                <div class="sticker green" title="Validé"></div>
                            </div>
                            @break
                        @case(3)
                            <div class="d-flex">
                                <div class="sticker green" title="Carte éditée"></div>
                            </div>
                            @break
                        @case(4)
                            <div class="d-flex">
                                <div class="sticker" title="Carte non renouvelée depuis plus d'un an"></div>
                            </div>
                            @break
                        @default
                            <div>Non renseigné</div>
                    @endswitch
                </td>
                <td><a href="mailto:{{$adherent->personne->email}}">{{$adherent->personne->email}}</a></td>
                <td>
                    {{ $adherent->fin?:"" }}
                </td>
                <td>
                    <select name="selectCt" id="selectCt_{{ $adherent->id_utilisateur }}" class="pl5 small w120">
                        <option value="2" {{ $adherent->ct == 2 ? 'selected' : '' }}>>25 ans</option>
                        <option value="3" {{ $adherent->ct == 3 ? 'selected' : '' }}>18 - 25 ans</option>
                        <option value="4" {{ $adherent->ct == 4 ? 'selected' : '' }}><18 ans</option>
                        <option value="5" {{ $adherent->ct == 5 ? 'selected' : '' }}>famille</option>
                        <option value="6" {{ $adherent->ct == 6 ? 'selected' : '' }}>2nde carte</option>
                    </select>
                    <div name="divSecondeCarte" {{ !in_array($adherent->ct, [5,6]) ? 'class=d-none' : '' }}>
                        <input name="inputSecondeCarte" maxlength="12" id="secondeCarte_{{ $adherent->id_utilisateur }}"
                               data-ref="{{ $adherent->id_utilisateur }}" type="text"
                               value="{{ $adherent->premierecarte }}" class="w120 mt5 pl5 pl5 small"
                               placeholder="première carte">
                    </div>
                </td>
                <td>
                    <a href="{{ route('clubs.sendReinitLink', $adherent->personne->id) }}" data-confirm="Confirmez-vous l'envoi d'un lien d'initialisation du mot de passe ?" data-method="post" style="transform: rotate(90deg); width: 25px; height: 25px; cursor: pointer; display: block"  title="{{ $adherent->personne->premiere_connexion == 1 ? "Mot de passe non initialisé" : "Mot de passe initialisé" }} - Envoyer un lien d'initialisation">
                        @if($adherent->personne->premiere_connexion == 1)
                            <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="#800" class="bi bi-key-fill" viewBox="0 0 16 16">
                                <path d="M3.5 11.5a3.5 3.5 0 1 1 3.163-5H14L15.5 8 14 9.5l-1-1-1 1-1-1-1 1-1-1-1 1H6.663a3.5 3.5 0 0 1-3.163 2zM2.5 9a1 1 0 1 0 0-2 1 1 0 0 0 0 2z"/>
                            </svg>
                        @else
                            <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="#080" class="bi bi-key-fill" viewBox="0 0 16 16" >
                                <path d="M3.5 11.5a3.5 3.5 0 1 1 3.163-5H14L15.5 8 14 9.5l-1-1-1 1-1-1-1 1-1-1-1 1H6.663a3.5 3.5 0 0 1-3.163 2zM2.5 9a1 1 0 1 0 0-2 1 1 0 0 0 0 2z"/>
                            </svg>
                        @endif
                    </a>
                </td>
                <td>
                    <a href="{{ route($prefix.'clubs.adherents.edit', $adherent->id_utilisateur) }}"
                       class="adminPrimary btnSmall">éditer</a>
                    @if(in_array($adherent->statut, [0,4]))
                        <div class="mt5">
                            <a href="{{ route('clubs.removeAdherent', $adherent->id_utilisateur) }}" data-confirm="Cet adhérent ne sera plus visible dans votre liste d'adhérent club. Vous ne pourrez plus le réactiver par la suite mais la carte est conservée et le nom apparaitra toujours dans les résultats des concours. Confirmez-vous votre demande ? " data-method="delete"
                               class="adminDanger btnSmall">plus dans club</a>
                        </div>

                    @endif

                </td>
            </tr>
        @endforeach
        </tbody>
    </table>


    <div class="modalEdit d-none" id="modalRenouvellement">
        <div class="modalEditHeader">
            <div class="modalEditTitle">Renouvellement des adhésions et abonnements</div>
            <div class="modalEditClose">
                X
            </div>
        </div>
        <div class="modalEditBody">
            <div class="alertDanger mt10 mxauto mb0">
                Veuillez contrôler attentivement les informations ci-dessous. Pour l'instant, aucune donnée n'a été
                enregistrée.
                Si vous annulez, votre saisie ne sera pas prise en compte. Si vous validez le renouvellement, les
                informations seront enregistrées et vous
                pourrez télécharger le bordereau club.
                <span class="bolder">
                Tout autre bordereau créé et n'ayant pas été validé par un
                règlement enregistré par la FPF sera supprimé. Donc si vous avez un paiement en cours mais non pris en compte par la FPF, ne validez pas ce bordereau.
                </span>
            </div>
            <div class="mt25 bold">
                Le coût total des adhésions et abonnements adhérents et club est de <span
                    id="montantRenouvellement"></span>€.<br>
                Cela correspond au montant que vous devez règler.
            </div>
            <div class="mt25">
                Renouvellement des adhésions pour les adhérents sélectionnés: <span class="bold"
                                                                                    id="montantRenouvellementAdhesion"></span>€<br>
                Renouvellement des abonnements pour les adhérents sélectionnés: <span class="bold"
                                                                                      id="montantRenouvellementAbonnement"></span>€
            </div>
            <div class="mt25" id="divRenouvellementClub" class="d-none">
                <div id="divRenouvellementAdhesionClub" class="d-none">
                    Renouvellement de l'adhésion du club: <span class="bold" id="montantClubAdhesion"></span>€<br>
                    Adhésion du club à l'UR: <span class="bold" id="montantClubAdhesionUr"></span>€<br>
                </div>
                <div id="divRenouvellementAbonnementClub" class="d-none">
                    Abonnement du club: <span class="bold" id="montantClubAbonnement"></span>€
                </div>

            </div>
            <div class="mt25">
                <div class="d-flex w100 justify-around">
                    <div class="bold flex-2 small">Adhérent</div>
                    <div class="bold flex-1 small">Type carte</div>
                    <div class="bold flex-1 small">Adhésion</div>
                    <div class="bold flex-1 small">Abonnement</div>
                    <div class="bold flex-1 small">Total</div>
                </div>
                <div id="renouvellementListe"></div>
            </div>
        </div>
        <div class="modalEditFooter">
            <div class="adminDanger btnMedium mr10 modalEditClose" id="btnAnnulerRenouvellement">Annuler</div>
            <div class="adminPrimary btnMedium" id="btnRenouvellement">Valider le renouvellement</div>

        </div>
    </div>



    <div class="modalEdit d-none" id="modalRenouvellementOk">
        <div class="modalEditHeader">
            <div class="modalEditTitle">Renouvellement des adhésions et abonnements</div>
            <div class="modalEditCloseReload">
                X
            </div>
        </div>
        <div class="modalEditBody">
            <div class="alertSuccess mt10 mb0 mxauto">
                Le bordereau pour le renouvellement a bien été généré. Vous pouvez le télécharger en cliquant sur le
                lien suivant: <a class="blue " id="lienBordereauClub" target="_blank">bordreau de
                    renouvellement</a>.<br>
                Le bordereau vous a également été transmis par mail et vous pouvez le retrouver dans votre espace
                "Bordereaux et règlements".<br><br>
                Vous pouvez régler directement en ligne, par virement instantané ou CB, votre règlement en cliquant sur
                les boutons ci-dessous.<br>
                Si ce n'est pas possible, vous pouvez régler virement en nous transmettant en référence le bordereau.
                Vous pouvez également règler plus tard en vous rendant dans votre espace "Bordereaux et règlements".<br>
            </div>
        </div>
        <div class="modalEditFooter">
            <div class="adminDanger btnMedium mr10 modalEditCloseReload">Fermer</div>
            <div class="adminPrimary btnMedium mr10" id="clubPayVirement" data-ref="">Payer par virement</div>
            <div class="adminPrimary btnMedium mr10" id="clubPayCb" data-ref="">Payer par CB</div>
        </div>
    </div>
@endif
@section('js')
    <script src="{{ asset('js/filters_club_liste-adherent.js') }}?t=<?= time() ?>"></script>
    <script src="{{ asset('js/excel_adherent_file.js') }}?t=<?= time() ?>"></script>
    <script src="{{ asset('js/club_paiement.js') }}?t=<?= time() ?>"></script>
@endsection
