<div class="formBlock">
    <div class="formBlockTitle">Généralités</div>
    <div class="formBlockWrapper">
        <form action="{{ route($pathPrefixName.'updateGeneralite', $club) }}" method="POST" id="generaliteForm" enctype="multipart/form-data">
            <input type="hidden" name="_method" value="put">
            {{ csrf_field() }}
            <div class="formBlockWrapper">
                <div class="formLine center d-flex flex-column">
                    <label class="d-flex flex-column" for="file" style="cursor:pointer">
                        <img class="clubLogo"
                             src="{{ env('APP_URL').'storage/app/public/uploads/clubs/'.$club->numero.'/'.$club->logo }}"
                             alt="">
                        <span class="text underlineGrey grey relative modifyVisible" style="width: 120px;margin: auto;">Changer de logo</span>
                    </label>
                    <input class="formValue d-none" type="file" id="file" accept=".png,.jpeg,.jpg" name="logo"
                           disabled="true">
                </div>
            </div>
            {{--                @endif--}}
            <div class="formBlockWrapper inline">
                <div class="formUnit">
                    <div class="formLabel">Nom</div>
                    <input class="formValue capitalize" type="text" value="{{$club->nom?:""}}" disabled="true"
                           name="nom" maxlength="40" minlength="2" type="text"/>
                </div>
                <div class="formUnit">
                    <div class="formLabel">Courriel</div>
                    <input class="formValue" type="email" value="{{$club->courriel?:""}}" disabled="true"
                           name="courriel" maxlength="250" minlength="2" type="email"/>
                </div>
                <div class="formUnit">
                    <div class="formLabel">Site web</div>
                    <input class="formValue" type="text" value="{{$club->web?:""}}"
                           disabled="true" name="web" minlength="4"/>
                </div>
                <div class="formUnit">
                    <div class="formLabel">Statut</div>
                    @switch($club->statut)
                        @case(0)
                        <div class="d-flex">
                            <div class="sticker orange"></div>
                            <div>Non renouvelé</div>
                        </div>
                        @break
                        @case(1)
                        <div class="d-flex">
                            <div class="sticker yellow"></div>
                            <div>Préinscrit</div>
                        </div>
                        @break
                        @case(2)
                        <div class="d-flex">
                            <div class="sticker green"></div>
                            <div>Validé</div>
                        </div>
                        @break
                        @case(3)
                        <div class="d-flex">
                            <div class="sticker"></div>
                            <div>Désactivé</div>
                        </div>
                        @break
                        @default
                        <div>Non renseigné</div>
                    @endswitch
                </div>
                <div class="formUnit mr25">
                    <div class="formLabel">Nombre d'adhérents</div>
                    <div>{{$club->nbadherents?:""}}</div>
                </div>
            </div>

        </form>
        <div class="w100" data-formId="generaliteForm">
            <button class="formBtn mx16 relative d-none success" name="enableBtn" >
                Valider
            </button>
            <button class="formBtn mx16 relative  primary" name="updateForm">Modifier</button>
        </div>
    </div>
</div>
<div class="formBlock">
    <div class="formBlockTitle">Coordonnées</div>
    <div class="formBlockWrapper">
        <form action="{{route($pathPrefixName.'updateClubAddress', $club)}}" method="POST" id="adresseForm">
            <input type="hidden" name="_method" value="put">
            {{ csrf_field() }}
            <div class="formBlockWrapper inline">
                @if(!$club->adresse)
                    <div class="addAddress" name="addAddress">Vous voulez rajouter une adresse ?</div>
                @endif
                <div class="formValueGroup inline{{ !$club->adresse ?" hideForm":""}}">

                    <div class="formUnit">
                        <div class="formLabel">Rue</div>
                        <input name="libelle1" type="text" class="formValue "
                               value="{{$club->adresse?$club->adresse->libelle1:""}}"
                               disabled="true" maxlength="120"/>
                    </div>
                    <div class="formUnit">
                        <div class="formLabel"></div>
                        <input name="libelle2" class="formValue "
                               type="text" value="{{$club->adresse?$club->adresse->libelle2:""}}"
                               disabled="true" maxlength="120"/>
                    </div>
                    <div class="formUnit">
                        <div class="formLabel">Code Postal</div>
                        <div class="suggestionWrapper">
                            <input name="codepostal" type="text" class="formValue"
                                   value="{{$club->adresse?$club->adresse->codepostal:""}}"
                                   disabled="true" maxlength="10" required/>
                            <div class="suggestion"></div>
                        </div>
                    </div>
                    <div class="formUnit">
                        <div class="formLabel">Ville</div>
                        <div class="suggestionWrapper">
                            <input name="ville" type="text" class="formValue"
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
                                   disabled="true" name="telephonedomicile"/>
                        </div>
                    </div>
                    <div class="formUnit">
                        <div class="formLabel">Téléphone mobile</div>
                        <div class="group">
                            {{--                                                        <div class="indicator {{$club->adresse && $club->adresse->indicatif_mobile!==""?"":"d-none"}}">+{{$club->adresse?$club->adresse->indicatif_mobile:""}}</div>--}}
                            <input class="formValue phoneInput" type="text"
                                   value="{{$club->adresse?$club->adresse->telephonemobile:""}}"
                                   disabled="true" name="telephonemobile"/>
                        </div>
                    </div>
                </div> {{-- end formvaluegroup--}}
            </div>{{-- end formBlockWrapper--}}
        </form>
    </div>
    <div class="w100" data-formId="adresseForm">
        <button class="formBtn relative mx16 success d-none" name="enableBtn">Valider</button>
        <button class="formBtn relative primary mx16" name="updateForm">Modifier</button>
    </div>
</div>
<div class="formBlock">
    <div class="formBlockTitle">Abonnement</div>
    <div class="formBlockWrapper inline">
        <div class="formUnit mr25">
            <div class="formLabel">État de l'abonnement :</div>
            @if($club->is_abonne)
                <div>Abonné</div>
            @else
                <div>Sans abonnement</div>
            @endif
        </div>
        @if($club->is_abonne)
            <div class="formUnit mr25">
                <div class="formLabel">Numéro de fin d'abonnement :</div>
                <div>{{$club->numerofinabonnement}}</div>
            </div>
        @endif

    </div>
</div>
<div class="formBlock">
    <div class="formBlockTitle">Réunions</div>
    <div class="formBlockWrapper">
        <form action="{{route($pathPrefixName .'updateReunion', $club)}}" method="POST" id="reunionForm">
            <input type="hidden" name="_method" value="put">
            {{ csrf_field() }}
            <div class="formBlockWrapper inline">
                <div class="formUnit mr25">
                    <div class="formLabel">Réunions</div>
                    <input class="formValue" value="{{$club->reunions?:""}}"
                           disabled="true" name="reunions"/>
                </div>
                <div class="formUnit mr25">
                    <div class="formLabel">Fréquence des réunions</div>
                    <input class="formValue" value="{{$club->frequencereunions?:""}}"
                           disabled="true" name="frequencereunions"/>
                </div>
                <div class="formUnit mr25">
                    <div class="formLabel">Horaires des réunions</div>
                    <input class="formValue" value="{{$club->horairesreunions?:""}}"
                           disabled="true" name="horairesreunions"/>
                </div>
            </div>
        </form>
    </div>
    <div class="w100" data-formId="reunionForm">
        <button class="formBtn relative mx16 d-none success" name="enableBtn">                            Valider                        </button>
        <button class="formBtn relative mx16 primary" name="updateForm">Modifier</button>
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
@section('js')
    <script src="{{ asset('js/autocompleteCommune.js') }}?t=<?= time() ?>"></script>
    <script src="{{ asset('js/club_preferences.js') }}?t=<?= time() ?>"></script>
@endsection
