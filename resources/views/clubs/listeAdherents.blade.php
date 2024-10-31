<div class="alertInfo w80">
    <span class="bold">Informations !</span>
    Vous pouvez gérer le renouvellement des adhésions et abonnements des membres de votre club ainsi que la commande de Florilège (si la période de commande est ouverte).
    Pour cela, dans la colonne de gauche du tableau, sélectionner les options de renouvellement et indiqué le nombre de Florilège puis cliquez sur le bouton "Renouveler".<br>
    L'abonnement d'un membre du club se fait au tarif "abonné club" lorsque l'adhésion du membre est également renouvelée ou que celui-ci est déjà adhérent.
    Dan,s le cas contraire, le tarif appliqué est celui de l'abonnement normal.<br>
    Le club est automatiquement renouvelé lors du premier renouvellement des adhérents dans la saison.<br>
    Vous pouvez également abonner votre club au France Photo ou commander des Florilèges à tout moment.<br>
    Après génération du bordereau de renouvellement, si vous n'effectuez pas le paiement immédiatement, les adhérents sont en statut pré-inscrits.
    Ils sont alors cochés par défaut dans la liste des adhérents. Si vous souhaitez modifier votre bordereau, vous devez changer la sélection et cliquer de nouveau sur "Renouveler".
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

        <button class="adminPrimary btnMedium ml10" type="text" id="renouvellementAdherents" data-statut="{{ $club->statut }}" data-club="{{$club->id}}" {{ $exist_reglement_en_cours == 0 ? 'disabled' : '' }}>Renouveler
        </button>
    </div>
    <div class="d-flex justify-between mt20 w100">
        <div style="flex: 1">
        @if ($club->numerofinabonnement < $numeroencours + 4)
            <input type="checkbox" class="mr10" id="abonnementClub" {{ $club->aboPreinscrit ? 'checked=checked' : '' }}>
            Abonner le club à France Photographie jusqu'au
            numéro {{ $club->numero_fin_reabonnement }}
        @else
            Club abonné à France Photographie jusqu'au numéro {{ $club->numerofinabonnement }}
        @endif
        </div>
        <div style="flex: 1">
            @if($florilege_actif)
            <div class="d-flex">
                <div>
                    <input type="number" min="0" value="{{ $club->florilegePreinscrit }}" style="width: 60px; text-align: center" id="florilegeClub" />
                </div>
                <div class="ml10">
                    Nombre de Florilèges à commander pour le club
                    @if($club->nb_florileges > 0)
                        <br>({{ $club->nb_florileges }} numéros déjà commandés pour le club)
                    @endif
                </div>

            </div>

            <div class="alertInfo" style="width: 100%; margin-top: 10px; font-size: 0.9rem;">
                <span class="bold">Attention</span> : les florilèges destinés aux adhérents doivent être commandés en regard de leur nom dans la liste des adhérents
            </div>
            @else
                La période de commande des Florilèges est terminée.
            @endif
        </div>
        {{--        <div>--}}
        {{--            <a class="adminPrimary btnMedium" href="{{ route('clubs.adherents.create') }}">Ajouter un adhérent</a>--}}
        {{--        </div>--}}
    </div>
    <small>* : nombre de Florilèges déjà commandés pour l'adhérent</small>
    <table class="styled-table tablesorter">
        <thead>
        <tr>
            <th>Adhésion / Abonnement</th>
            <th style="text-align: start">Nb Florilège <small>/ *</small></th>
            <th>
                <div class="innerThead">
                    <div class="mr5">N°carte</div>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-sort-down" viewBox="0 0 16 16">
                        <path d="M3.5 2.5a.5.5 0 0 0-1 0v8.793l-1.146-1.147a.5.5 0 0 0-.708.708l2 1.999.007.007a.497.497 0 0 0 .7-.006l2-2a.5.5 0 0 0-.707-.708L3.5 11.293zm3.5 1a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1-.5-.5M7.5 6a.5.5 0 0 0 0 1h5a.5.5 0 0 0 0-1zm0 3a.5.5 0 0 0 0 1h3a.5.5 0 0 0 0-1zm0 3a.5.5 0 0 0 0 1h1a.5.5 0 0 0 0-1z"/>
                    </svg>
                </div>
            </th>
            <th>
                <div class="innerThead">
                    <div class="mr5">Nom</div>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-sort-down" viewBox="0 0 16 16">
                        <path d="M3.5 2.5a.5.5 0 0 0-1 0v8.793l-1.146-1.147a.5.5 0 0 0-.708.708l2 1.999.007.007a.497.497 0 0 0 .7-.006l2-2a.5.5 0 0 0-.707-.708L3.5 11.293zm3.5 1a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1-.5-.5M7.5 6a.5.5 0 0 0 0 1h5a.5.5 0 0 0 0-1zm0 3a.5.5 0 0 0 0 1h3a.5.5 0 0 0 0-1zm0 3a.5.5 0 0 0 0 1h1a.5.5 0 0 0 0-1z"/>
                    </svg>
                </div>
            </th>
            <th>
                Statut
            </th>
            <th>
                <div class="innerThead">
                    <div class="mr5">Courriel</div>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-sort-down" viewBox="0 0 16 16">
                        <path d="M3.5 2.5a.5.5 0 0 0-1 0v8.793l-1.146-1.147a.5.5 0 0 0-.708.708l2 1.999.007.007a.497.497 0 0 0 .7-.006l2-2a.5.5 0 0 0-.707-.708L3.5 11.293zm3.5 1a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1-.5-.5M7.5 6a.5.5 0 0 0 0 1h5a.5.5 0 0 0 0-1zm0 3a.5.5 0 0 0 0 1h3a.5.5 0 0 0 0-1zm0 3a.5.5 0 0 0 0 1h1a.5.5 0 0 0 0-1z"/>
                    </svg>
                </div>
            </th>
            <th>
                <div class="innerThead">
                    <div class="mr5">Abonnement - N° fin</div>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-sort-down" viewBox="0 0 16 16">
                        <path d="M3.5 2.5a.5.5 0 0 0-1 0v8.793l-1.146-1.147a.5.5 0 0 0-.708.708l2 1.999.007.007a.497.497 0 0 0 .7-.006l2-2a.5.5 0 0 0-.707-.708L3.5 11.293zm3.5 1a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1-.5-.5M7.5 6a.5.5 0 0 0 0 1h5a.5.5 0 0 0 0-1zm0 3a.5.5 0 0 0 0 1h3a.5.5 0 0 0 0-1zm0 3a.5.5 0 0 0 0 1h1a.5.5 0 0 0 0-1z"/>
                    </svg>
                </div>
            </th>
            <th>
                Type carte
            </th>
            <th colspan="2">Actions</th>
        </tr>
        </thead>
        <tbody>
        @foreach($adherents as $adherent)
            <tr>
                <td style="text-align: left">
                    @if(in_array($adherent->statut, [2, 3]) && $adherent->fin >= $numeroencours + 4)
                        &nbsp;
                    @else
                        <select name="adh_abo" data-ref="{{ $adherent->id_utilisateur }}" data-identifiant="{{ $adherent->identifiant }}" style="width: 200px;">
                            <option value="0"></option>
                            @if(!in_array($adherent->statut, [2, 3]))
                                <option value="1" {{ $adherent->statut == 1 && $adherent->aboPreinscrit == 0 ? 'selected=selected' : '' }}>Adhésion seule</option>
                            @endif
                            @if($adherent->fin == '' || $adherent->fin < $numeroencours + 4)
                                <option value="2" {{ $adherent->statut != 1 && $adherent->aboPreinscrit == 1 ? 'selected=selected' : '' }}>Abonnement seul</option>
                            @endif
                            @if(!in_array($adherent->statut, [2, 3]) && ($adherent->fin == '' || $adherent->fin < $numeroencours + 4))
                                <option value="3" {{ $adherent->statut == 1 && $adherent->aboPreinscrit == 1 ? 'selected=selected' : '' }}>Adhésion et abonnement</option>
                            @endif
                        </select>
                    @endif
                </td>

{{--                @if(in_array($adherent->statut, [0, 1, 4]))--}}
{{--                    <input type="checkbox" name="adherer" data-ref="{{ $adherent->id_utilisateur }}"--}}
{{--                               data-identifiant="{{ $adherent->identifiant }}" {{ $adherent->statut == 1 ? 'checked=checked' : '' }} />--}}
{{--                @endif--}}
                <td>
                    <div class="d-flex">
                        @if($florilege_actif)
                            <input type="number" min="0" name="florilege" data-ref="{{ $adherent->id_utilisateur }}" value="{{ $adherent->florilegePreinscrit ?? 0 }}" style="width: 50px; text-align: center" />
                        @endif
                        @if($adherent->nb_florileges > 0)
                            <div class="small ml5">/ {{ $adherent->nb_florileges }}</div>
                        @endif
                    </div>

                </td>
{{--                <td><input type="checkbox" name="abonner" data-ref="{{ $adherent->id_utilisateur }}"  {{ $adherent->aboPreinscrit == 1 ? 'checked=checked' : '' }} /></td>--}}
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
                               class="adminDanger btnSmall">retirer de la liste</a>
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
                    Tout autre bordereau créé et n'ayant pas été validé par un règlement enregistré par la FPF sera supprimé.
                    Donc si vous avez un paiement en cours mais non pris en compte par la FPF, ne validez pas ce bordereau.<br>
                    En cas d'erreur de saisie de votre part, aucune correction ne sera effectuée par les services administratifs de la FPF.
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
                                                                                      id="montantRenouvellementAbonnement"></span>€<br>
                Commande de Florileges pour les adhérents: <span class="bold"
                                                                                      id="montantRenouvellementFlorilege"></span>€
            </div>
            <div class="mt25" id="divRenouvellementClub" class="d-none">
                <div id="divRenouvellementAdhesionClub" class="d-none">
                    Renouvellement de l'adhésion du club: <span class="bold" id="montantClubAdhesion"></span>€<br>
                    Adhésion du club à l'UR: <span class="bold" id="montantClubAdhesionUr"></span>€<br>
                </div>
                <div id="divRenouvellementAbonnementClub" class="d-none">
                    Abonnement du club: <span class="bold" id="montantClubAbonnement"></span>€
                </div>
                <div id="divRenouvellementFlorilegeClub" class="d-none">
                    Florileges pour le club: <span class="bold" id="montantClubFlorilege"></span>€
                </div>

            </div>
            <div class="mt25">
                <div class="d-flex w100 justify-around">
                    <div class="bold flex-2 small">Adhérent</div>
                    <div class="bold flex-1 small">Type carte</div>
                    <div class="bold flex-1 small">Adhésion</div>
                    <div class="bold flex-1 small">Abonnement</div>
                    <div class="bold flex-1 small">Florilège</div>
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
    <script src="{{ asset('js/jquery.tablesorter.js') }}"></script>*
    <script>
        $(".tablesorter").tablesorter({ headers: { 0: { sorter: false}, 1: {sorter: false}, 4: {sorter: false}, 7: {sorter: false}, 8: {sorter: false} }});
    </script>
@endsection
