<div class="formBlock">
    <div class="formBlockTitle">Généralités</div>
    <div class="formBlockWrapper">
        <form class="w100" action="{{ route($pathPrefixName.'updateGeneralite', $club) }}" method="POST" id="generaliteForm" enctype="multipart/form-data">
            <input type="hidden" name="_method" value="put">
            {{ csrf_field() }}
            <div class="formBlockWrapper m0 p0">
                <div class="formLine center d-flex flex-column">
                    <label class="d-flex flex-column" for="file" style="cursor:pointer">
                        @if($club->logo)
                        <img class="clubLogo"
                             src="{{ env('APP_URL').'storage/app/public/uploads/clubs/'.$club->numero.'/'.$club->logo }}"
                             alt="">
                        @else
                            <img class="clubLogo"
                                 src="{{ env('APP_URL').'storage/app/public/FPF-club-default-image.jpg'}}"
                                 alt="">
                        @endif
                        <span class="text underlineGrey grey relative modifyVisible" style="width: 120px;margin: auto;">Changer de logo</span>
                    </label>
                    <input class="formValue d-none" type="file" id="file" accept=".png,.jpeg,.jpg" name="logo"
                           disabled="true">
                </div>
            </div>
            {{--                @endif--}}
            <div class="formBlockWrapper m0 p0">
                <div class="formUnit w100">
                    <div class="formLabel">Nom</div>
                    <input class="formValue capitalize w75" type="text" value="{{$club->nom?:""}}" disabled="true"
                           name="nom" maxlength="40" minlength="2" type="text"/>
                </div>
                <div class="formUnit w100">
                    <div class="formLabel">Courriel</div>
                    <input class="formValue w75" type="email" value="{{$club->courriel?:""}}" disabled="true"
                           name="courriel" maxlength="250" minlength="2" type="email"/>
                </div>
                <div class="formUnit w100">
                    <div class="formLabel">Site web</div>
                    <input class="formValue w75" type="text" value="{{$club->web?:""}}"
                           disabled="true" name="web" minlength="4"/>
                </div>
                <div class="formUnit w100">
                    <div class="formLabel mr25">Statut</div>
                    @switch($club->statut)
                        @case(0)
                        <div class="d-flex">
                            <div class="sticker orange ml0"></div>
                            <div>Non renouvelé</div>
                        </div>
                        @break
                        @case(1)
                        <div class="d-flex">
                            <div class="sticker yellow ml0"></div>
                            <div>Préinscrit</div>
                        </div>
                        @break
                        @case(2)
                        <div class="d-flex">
                            <div class="sticker green ml0"></div>
                            <div>Validé</div>
                        </div>
                        @break
                        @case(3)
                        <div class="d-flex">
                            <div class="sticker ml0"></div>
                            <div>Désactivé</div>
                        </div>
                        @break
                        @default
                        <div>Non renseigné</div>
                    @endswitch
                </div>
{{--                <div class="formUnit w100 ">--}}
{{--                    <div class="formLabel mr25">Nombre d'adhérents</div>--}}
{{--                    <div>{{$club->nbadherents?:""}}</div>--}}
{{--                </div>--}}
            </div>

        </form>
        @if($level != 'admin' || in_array('GESINFO', $droits_fpf))
        <div class="w100" data-formId="generaliteForm">
            <button class="formBtn mx16 relative d-none success" name="enableBtn" >
                Valider
            </button>
            <button class="formBtn mx16 relative  primary" name="updateForm">Modifier</button>
        </div>
        @endif
    </div>
</div>
<div class="formBlock">
    <div class="formBlockTitle">Coordonnées</div>
    <div class="formBlockWrapper">
        <form class="w100" action="{{route($pathPrefixName.'updateClubAddress', $club)}}" method="POST" id="adresseForm">
            <input type="hidden" name="_method" value="put">
            {{ csrf_field() }}
            <div class="formBlockWrapper m0 p0">
                @if(!$club->adresse)
                    <div class="addAddress" name="addAddress">Vous voulez rajouter une adresse ?</div>
                @endif
                <div class="formValueGroup align-start{{ !$club->adresse ?" hideForm":""}}">

                    <div class="formUnit w100">
                        <div class="formLabel">Adresse</div>
                        <input name="libelle1" type="text" class="formValue w75"
                               value="{{$club->adresse?$club->adresse->libelle1:""}}"
                               disabled="true" maxlength="120"/>
                    </div>
                    <div class="formUnit w100">
                        <div class="formLabel">Complément</div>
                        <input name="libelle2" class="formValue w75"
                               type="text" value="{{$club->adresse?$club->adresse->libelle2:""}}"
                               disabled="true" maxlength="120"/>
                    </div>
                    <div class="formUnit w100">
                        <div class="formLabel">Code Postal</div>
                        <div class="suggestionWrapper">
                            <input name="codepostal" type="text" class="formValue"
                                   value="{{ $club->adresse ? str_pad($club->adresse->codepostal, 5, '0', STR_PAD_LEFT) : ""}}"
                                   disabled="true" maxlength="10" required/>
                            <div class="suggestion"></div>
                        </div>
                    </div>
                    <div class="formUnit w100">
                        <div class="formLabel">Ville</div>
                        <div class="suggestionWrapper">
                            <input name="ville" type="text" class="formValue w75"
                                   value="{{$club->adresse?$club->adresse->ville:""}}"
                                   disabled="true" maxlength="50" required/>
                            <div class="suggestion"></div>
                        </div>
                    </div>
                    <div class="formUnit">
                        <div class="formLabel">Pays</div>
                        <select class="formValue pays" name="pays" disabled="true" required>
                            <option value="">Selectionnez un pays</option>
                            @foreach($countries as $country)
                                @if($club->adresse)
                                    <option value="{{$country->id}}"
                                            {{strtolower($country->nom) == strtolower($club->adresse->pays)? "selected":""}} data-indicator="{{$country->indicatif}}">{{$country->nom}}</option>
                                @else
                                    <option value="{{$country->id}}"
                                            data-indicator="{{$country->indicatif}}">{{$country->nom}}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="formUnit">
                        <div class="formLabel">Téléphone fixe</div>
                        <div class="group">
                            <div
                                class="indicator {{$club->adresse && $club->adresse->indicatif_fixe!==""?"":"d-none"}}">
                                +{{$club->adresse?$club->adresse->indicatif_fixe:""}}</div>
                            <input class="formValue phoneInput" type="text"
                                   value="{{$club->adresse?$club->adresse->telephonedomicile:""}}"
                                   disabled="true" name="telephonedomicile" maxlength="25"/>
                        </div>
                    </div>
                    <div class="formUnit">
                        <div class="formLabel">Téléphone mobile</div>
                        <div class="group">
                            <div
                                class="indicator {{$club->adresse && $club->adresse->indicatif_fixe!==""?"":"d-none"}}">
                                +{{$club->adresse?$club->adresse->indicatif_fixe:""}}</div>
                            <input class="formValue phoneInput" type="text"
                                   value="{{$club->adresse?$club->adresse->telephonemobile:""}}"
                                   disabled="true" name="telephonemobile" maxlength="25"/>
                        </div>
                    </div>
                </div> {{-- end formvaluegroup--}}
            </div>{{-- end formBlockWrapper--}}
        </form>
    </div>
    @if($level != 'admin' || in_array('GESINFO', $droits_fpf))
        <div class="w100" data-formId="adresseForm">
            <button class="formBtn relative mx16 success d-none" name="enableBtn">Valider</button>
            <button class="formBtn relative primary mx16" name="updateForm">Modifier</button>
        </div>
    @endif
</div>
{{--<div class="formBlock">--}}
{{--    <div class="formBlockTitle">Abonnement</div>--}}
{{--    <div class="formBlockWrapper">--}}
{{--        <div class="formUnit mr25">--}}
{{--            <div class="formLabel">État de l'abonnement :</div>--}}
{{--            @if($club->is_abonne)--}}
{{--                <div>Abonné</div>--}}
{{--            @else--}}
{{--                <div>Sans abonnement</div>--}}
{{--            @endif--}}
{{--        </div>--}}
{{--        @if($club->is_abonne)--}}
{{--            <div class="formUnit mr25">--}}
{{--                <div class="formLabel">Numéro de fin d'abonnement :</div>--}}
{{--                <div>{{$club->numerofinabonnement}}</div>--}}
{{--            </div>--}}
{{--        @endif--}}

{{--    </div>--}}
{{--</div>--}}
<div class="formBlock">
    <div class="formBlockTitle">Réunions</div>
    <div class="formBlockWrapper">
        <form class="w100" action="{{route($pathPrefixName .'updateReunion', $club)}}" method="POST" id="reunionForm">
            <input type="hidden" name="_method" value="put">
            {{ csrf_field() }}
            <div class="formBlockWrapper">
                <div class="formUnit mr25 w100">
                    <div class="formLabel">Réunions</div>
                    <input class="formValue w70" value="{{$club->reunions?:""}}"
                           disabled="true" name="reunions"  maxlength="255" />
                </div>
                <div class="formUnit mr25 w100">
                    <div class="formLabel">Fréquence des réunions</div>
                    <input class="formValue w70" value="{{$club->frequencereunions?:""}}"
                           disabled="true" name="frequencereunions"  maxlength="255" />
                </div>
                <div class="formUnit mr25 w100">
                    <div class="formLabel">Horaires des réunions</div>
                    <input class="formValue w70" value="{{$club->horairesreunions?:""}}"
                           disabled="true" name="horairesreunions" maxlength="255" />
                </div>
            </div>
        </form>
    </div>
    @if($level != 'admin' || in_array('GESINFO', $droits_fpf))
        <div class="w100" data-formId="reunionForm">
            <button class="formBtn relative mx16 d-none success" name="enableBtn">                            Valider                        </button>
            <button class="formBtn relative mx16 primary" name="updateForm">Modifier</button>
        </div>
    @endif
</div>
<div class="formBlock relative checkbox">
    <div class="message success"
         style="left: calc( 50% - (250px / 2));
                 position: absolute;
                 width: 250px;
                 text-align: center;">
        Votre choix a été pris en compte
    </div>
    <div class="formBlockTitle">Activités</div>
    <div class="formBlockWrapper inline" data-form="activites" data-club="{{$club->id}}">
        @foreach($activites as $activite)
            <div class="formUnit mr25" name="ajaxCheckbox">
                <label class="formLabel pointer" for="{{$activite->libelle}}">{{$activite->libelle}} </label>
                @if(in_array($activite->id, $club->activites))
                    <input class="formValue pointer" type="checkbox" value="{{$activite->id}}" checked
                           name="{{$activite->libelle}}"/>
                @else
                    <input class="formValue pointer" type="checkbox" value="{{$activite->id}}"
                           name="{{$activite->libelle}}"/>
                @endif
            </div>
        @endforeach
    </div>
</div>
<div class="formBlock relative checkbox">
    <div class="message success"
         style="left: calc( 50% - (250px / 2));
                 position: absolute;
                 width: 250px;
                 text-align: center;">
        Votre choix a été pris en compte
    </div>
    <div class="formBlockTitle">Équipements</div>
    <div class="formBlockWrapper inline" data-form="equipements" data-club="{{$club->id}}">
        @foreach($equipements as $equipement)
            <div class="formUnit mr25" name="ajaxCheckbox">
                <label class="formLabel pointer"
                       for="{{$equipement->libelle}}">{{$equipement->libelle}} </label>
                @if(in_array($equipement->id, $club->equipements))
                    <input class="formValue pointer" type="checkbox" value="{{$equipement->id}}" checked
                           name="{{$equipement->libelle}}"/>
                @else
                    <input class="formValue pointer" type="checkbox" value="{{$equipement->id}}"
                           name="{{$equipement->libelle}}"/>
                @endif
            </div>
        @endforeach
    </div>
</div>
<div class="formBlock relative checkbox">
    <div class="message success" id="messageAffichagePhoto"
         style="left: calc( 50% - (250px / 2));
                 position: absolute;
                 width: 250px;
                 text-align: center;">
        Votre choix a été pris en compte
    </div>
    <div class="formBlockTitle">Gestion concours</div>
    <div class="formBlockWrapper inline">
        <div class="formUnit mr25">
            <label class="formLabel pointer"
                   for="affichage_photo_club">Affichage pour tout adhérent des photos club</label>
                <input class="formValue pointer" type="checkbox" {{ $club->affichage_photo_club == 1 ? 'checked' : '' }}
                       name="affichage_photo_club" data-ref="{{ $club->id }}" />
        </div>
    </div>
</div>
<div class="formBlock relative checkbox" style="text-align: center">
    @if($club->closed == 0)
        @if($level != 'admin' || in_array('GESINFO', $droits_fpf))
        <button id="askClosed" class="formBtn relative mx16 danger" style="width: auto">Déclarer le club comme fermé</button>
        @endif
    @else
        <div style="font-size: large; color: #ac4848">Le club est déclaré comme fermé</div>
    @endif
</div>

<div class="modalEdit d-none" id="modalConfirmClosed">
    <div class="modalEditHeader">
        <div class="modalEditTitle">Changement d'état d'un club</div>
        <div class="modalEditClose">
            X
        </div>
    </div>
    <div class="modalEditBody">
        Souhaitez-vous réellement déclaré le club comme fermé ?<br>
        <span class="bold">Attention, cette action est irréversible !</span><br>
        Cette action empêchera les adhérents du club de s'inscrire à des concours nationaux et régionaux avec une carte de ce club.<br>
    </div>
    <div class="modalEditFooter">
        <div class="btnMedium danger mr10 modalEditClose p5">Annuler</div>
        <div class="btnMedium primary mr10 p5" style="cursor:pointer;" id="confirmClosedClub" data-club="{{ $club->id }}">Valider
        </div>
    </div>
</div>
@section('js')
    <script src="{{ asset('js/autocompleteCommune.js') }}?t=<?= time() ?>"></script>
    <script src="{{ asset('js/club_preferences.js') }}?t=<?= time() ?>"></script>
@endsection
