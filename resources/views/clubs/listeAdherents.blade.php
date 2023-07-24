<div class="alertInfo" style="width: 80% !important">
    <span class="bold">Informations !</span>
    Vous pouvez gérer le renouvellement des adhésions et abonnements des membres de votre club. Pour cela, cochez les adhérents que vous souhaitez renouveler ou abonner puis cliquez sur le bouton "Renouveler".<br>
    Le club est automatiquement renouvelé lors du premier renouvellement des adhérents dans la saison.<br>
    Vous pouvez également abonner votre club au France Photo à tout moment.
</div>
<div class="alertSuccess" style="width: 80% !important; display: none;" id="alertAdherentsList">
    Le fichier des adhérents a bien été généré. Vous pouvez le télécharger en cliquant sur le lient suivant: <a id="linkAdherentsList" target="_blank" style="cursor: pointer;text-decoration: underline;">Télécharger le fichier</a>
</div>
<div class="filters d-flex">
    <div class="formBlock" style="max-width: 100%">
        <div class="formBlockTitle">Filtres</div>
        <div class="d-flex flexWrap">
            <div class="formUnit mb0">
                <div class="formLabel mr10 bold">Statut :</div>
                <select class="formValue modifying" name="filter" data-ref="statut">
                    <option value="all">Tous</option>
                    <option value="2" {{$statut == 2? "selected":""}}>Validés</option>
                    <option value="1" {{$statut == 1? "selected":""}}>Pré-inscrits</option>
                    <option value="0" {{$statut == 0? "selected":""}}>Non renouvelés</option>
                    <option value="3" {{$statut == 3? "selected":""}}>Carte éditée</option>
                    <option value="4" {{$statut == 4? "selected":""}}>Anciens</option>
                </select>
            </div>
            <div class="formUnit mb0">
                <div class="formLabel mr10 bold">Abonnement :</div>
                <select class="formValue modifying" name="filter" data-ref="abonnement">
                    <option value="all">Tous</option>
                    <option value="1" {{$abonnement== 1? "selected":""}}>Avec</option>
                    <option value="0" {{$abonnement== 0? "selected":""}}>Sans</option>
                </select>
            </div>
        </div>
    </div>
</div>
@if(!sizeof($adherents))
    Ce club ne possède aucun adhérent répondant aux critères selectionnés.
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
        <button class="adminPrimary btnMedium" type="text" id="btnAdherentsList" data-club="{{$club->id}}">Liste des adhérents au format Excel</button>
        <button class="adminPrimary btnMedium" type="text" id="btnAdherentsAjout" data-club="{{$club->id}}" style="margin-left: 10px">Ajouter un adhérent</button>
        <button class="adminPrimary btnMedium" type="text" id="renouvellementAdherents" data-club="{{$club->id}}" disabled style="margin-left: 10px">Renouveler</button>
    </div>
    <div style="display: flex; justify-content: flex-start; margin-top: 20px; width: 100%;">
        <input type="checkbox" class="mr10" id="abonnementClub"> Abonner le club jusqu'au numéro {{ $club->numero_fin_reabonnement }}
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
            <th></th>
        </tr>
        </thead>
        <tbody>
        @foreach($adherents as $adherent)
            <tr>
                <td>
                    @if(in_array($adherent->statut, [0, 1, 4]))
                        <input type="checkbox" name="adherer" data-ref="{{ $adherent->id_utilisateur }}" data-identifiant="{{ $adherent->identifiant }}" />
                    @endif
                <td><input type="checkbox" name="abonner" data-ref="{{ $adherent->id_utilisateur }}" /></td>
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
                    <select name="selectCt" id="selectCt_{{ $adherent->id_utilisateur }}" style="padding-left: 5px; font-size: small; width: 120px">
                        <option value="2" {{ $adherent->ct == 2 ? 'selected' : '' }}>>25 ans</option>
                        <option value="3" {{ $adherent->ct == 3 ? 'selected' : '' }}>18 - 25 ans</option>
                        <option value="4" {{ $adherent->ct == 4 ? 'selected' : '' }}><18 ans</option>
                        <option value="5" {{ $adherent->ct == 5 ? 'selected' : '' }}>famille</option>
                        <option value="6" {{ $adherent->ct == 6 ? 'selected' : '' }}>2eme club</option>
                    </select>
                    <div name="divSecondeCarte" {{ !in_array($adherent->ct, [5,6]) ? 'class=d-none' : '' }}>
                        <input name="inputSecondeCarte" maxlength="12" id="secondeCarte_{{ $adherent->id_utilisateur }}" data-ref="{{ $adherent->id_utilisateur }}" type="text" value="{{ $adherent->premierecarte }}" style="width: 120px; margin-top: 5px; padding-left: 5px; font-size: small;" placeholder="première carte">
                    </div>
                </td>
                <td>
                    <div style="margin-bottom: 3px;">
                        <a href="" class="adminPrimary btnSmall">action</a>
                    </div>
                    <div style="margin-bottom: 3px;">
                        <a href="" class="adminSuccess btnSmall">action</a>
                    </div>
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
            <div class="alertDanger" style="margin: 10px auto 0">
                Veuillez contrôler attentivement les informations ci-dessous. Pour l'instant, aucune donnée n'a été enregistrée.
                Si vous annulez, votre saisie ne sera pas prise en compte. Si vous validez le renouvellement, les informations seront enregistrées et vous
                pourrez télécharger le bordereau club. Tout autre bordereau créé et n'ayant pas été validé par un règlement enregistré par la FPF sera supprimé.
            </div>
            <div class="mt25 bold">
                Le coût total des adhésions et abonnements adhérents et club est de <span id="montantRenouvellement"></span>€.<br>
                Cela correspond au montant que vous devez règler.
            </div>
            <div class="mt25">
                Renouvellement des adhésions pour les adhérents sélectionnés: <span class="bold" id="montantRenouvellementAdhesion"></span>€<br>
                Renouvellement des abonnements pour les adhérents sélectionnés: <span class="bold" id="montantRenouvellementAbonnement"></span>€
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
            <div class="alertSuccess" style="margin: 10px auto 0">
                Le bordereau pour le renouvellement a bien été généré. Vous pouvez le télécharger en cliquant sur le lien suivant: <a id="lienBordereauClub" target="_blank">bordreau de renouvellement</a>.<br>
                Le bordereau vous a également été transmis par mail et vous pouvez le retrouver dans votre espace "Bordereaux et règlements".
            </div>
        </div>
        <div class="modalEditFooter">
            <div class="adminDanger btnMedium mr10 modalEditCloseReload">Fermer</div>
        </div>
    </div>
@endif
@section('js')
    <script src="{{ asset('js/filters-club-liste-adherent.js') }}?t=<?= time() ?>"></script>
    <script src="{{ asset('js/excel_adherent_file.js') }}?t=<?= time() ?>"></script>
@endsection
