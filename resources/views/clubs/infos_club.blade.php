@extends('layouts.default')

@section('content')
    <div class="pageCanva">
        <h1 class="pageTitle">
            Informations pour le club {{ $club->nom }}
            <a class="previousPage" title="Retour page précédente" href="{{ route('clubs.gestion') }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor"
                     class="bi bi-reply-fill" viewBox="0 0 16 16">
                    <path
                        d="M5.921 11.9 1.353 8.62a.719.719 0 0 1 0-1.238L5.921 4.1A.716.716 0 0 1 7 4.719V6c1.5 0 6 0 7 8-2.5-4.5-7-4-7-4v1.281c0 .56-.606.898-1.079.62z"/>
                </svg>
            </a>
        </h1>
        <div class="formBlock">
            <div class="formBlockTitle">Coordonnées</div>
            <form action="" method="POST">
                <input type="hidden" name="_method" value="put">
                {{ csrf_field() }}
                @if($club->logo)
                    <div class="formBlockWrapper">
                        <div class="formLine center d-flex flex-column">
                            <label class="d-flex flex-column" for="file" style="cursor:pointer">
                                <img src="{{ env('APP_URL').'storage/app/public/FPF-100-Logo-Seul.webp' }}" alt="">
                                {{--                    <img src="{{ env('APP_URL').'storage/app/public/'.$club->logo }}" alt="">--}}
                                <span class="text underlineGrey grey relative">Changer de logo</span>
                            </label>
                            <input style="display: none;" type="file" id="file" accept=".png,.jpeg,.jpg">
                            {{--                            <div>--}}
                            {{--                                <button type="submit" class="formBtn relative d-none success" name="enableBtn">Valider</button>--}}
                            {{--                                <button class="formBtn relative primary" name="updateForm">Modifier</button>--}}
                            {{--                            </div>--}}
                        </div>
                    </div>
                @endif
                <div class="formBlockWrapper inline">
                    <div class="formUnit">
                        <div class="formLabel">Nom</div>
                        <input class="formValue capitalize" type="text" value="{{$club->nom?:""}}" disabled="true"
                               name="nom"/>
                    </div>
                    <div class="formUnit">
                        <div class="formLabel">Courriel</div>
                        <input class="formValue" type="email" value="{{$club->courriel?:""}}" disabled="true"
                               name="courriel"/>
                    </div>
                    <div class="formUnit">
                        <div class="formLabel">Site web</div>
                        <input class="formValue" type="text" value="{{$club->web?:""}}"
                               disabled="true" name="web"/>
                    </div>
                    <div class="formUnit">
                        <div class="formLabel">Statut</div>
                        @switch($club->statut)
                            @case(0)
                            {{--                        <input class="formValue unchangeable" value="Non renouvelé"--}}
                            {{--                               disabled="true" name="statut"/>--}}
                            <div>Non renouvelé</div>
                            @break
                            @case(1)
                            {{--                        <input class="formValue unchangeable" value="Préinscrit"--}}
                            {{--                               disabled="true" name="statut"/>--}}
                            <div>Préinscrit</div>
                            @break
                            @case(2)
                            {{--                        <input class="formValue unchangeable" value="Validé"--}}
                            {{--                               disabled="true" name="statut"/>--}}
                            <div>Validé</div>
                            @break
                            @case(3)
                            {{--                        <input class="formValue unchangeable" value="Désactivé"--}}
                            {{--                               disabled="true" name="statut"/>--}}
                            <div>Désactivé</div>
                            @break
                            @default
                            {{--                        <input class="formValue unchangeable" value="Non renseigné"--}}
                            {{--                               disabled="true" name="statut"/>--}}
                            <div>Non renseigné</div>
                        @endswitch
                    </div>
                    <div class="formUnit mr25">
                        <div class="formLabel">Nombre d'adhérents</div>
                        {{--                    <input class="formValue" value="{{$club->nbadherents?:""}}"--}}
                        {{--                           disabled="true" name="nbadherents"/>--}}
                        <div>{{$club->nbadherents?:""}}</div>
                    </div>
                    <div class="md-inline">
                        <button type="submit" class="formBtn relative d-none success" name="enableBtn">Valider</button>
                        <button class="formBtn relative primary" name="updateForm">Modifier</button>
                    </div>
                </div>
            </form>
            <h2 class="formSubtitle">Adresse</h2>
            <form action="" method="POST">
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
                                        <option value="{{$country->id}}">{{$country->nom}}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="formUnit">
                            <div class="formLabel">Téléphone fixe</div>
                            <div class="group">
                                <div class="indicator {{$club->adresse && $club->adresse->indicatif!==""?"":"d-none"}}">
                                    +{{$club->adresse?$club->adresse->indicatif:""}}</div>
                                <input class="formValue phoneInput" type="text"
                                       value="{{$club->adresse?$club->adresse->telephonedomicile:""}}"
                                       disabled="true" name="telephonedomicile"/>
                            </div>
                        </div>
                        {{--                        <div class="formUnit">--}}
                        {{--                            <div class="formLabel">Téléphone mobile</div>--}}
                        {{--                            <div class="group">--}}
                        {{--                                <div class="indicator {{$club->adresse && $club->adresse->indicatif!==""?"":"d-none"}}">+{{$club->adresse?$club->adresse->indicatif:""}}</div>--}}
                        {{--                                <input class="formValue phoneInput" type="text" value="{{$club->adresse?$club->adresse->telephonemobile:""}}"--}}
                        {{--                                       disabled="true" name="telephonmobile"/>--}}
                        {{--                            </div>--}}
                        {{--                        </div>--}}
                        <div>
                            <button type="submit" class="formBtn relative success d-none" name="enableBtn">Valider
                            </button>
                            <button class="formBtn primary relative" name="updateForm">Modifier</button>
                        </div>
                    </div> {{-- end formvaluegroup--}}
                </div>{{-- end formBlockWrapper--}}
            </form>
            <h2 class="formSubtitle">Abonnement</h2>
            <div class="formBlockWrapper inline">
                <div class="formUnit mr25">
                    <div class="formLabel">État de l'abonnement :</div>
                    @switch($club->abon)
                        @case(1)
                        <div>Avec abonnement</div>
                        @break
                        @case("G")
                        <div>Avec abonnement gratuit</div>
                        @break
                        @default
                        <div>Sans abonnement</div>
                    @endswitch
                </div>
                <div class="formUnit mr25">
                    <div class="formLabel">Date de début d'abonnement :</div>
                    <div>{{$club->datedebutabonnement}}</div>
                </div>
                <div class="formUnit mr25">
                    <div class="formLabel">Numéro de début d'abonnement :</div>
                    <div>{{$club->numerodebutabonnement}}</div>
                </div>
                <div class="formUnit mr25">
                    <div class="formLabel">Numéro de fin d'abonnement :</div>
                    <div>{{$club->numerofinabonnement}}</div>
                </div>
            </div>
            <h2 class="formSubtitle">Réunions</h2>
            <form action="" method="POST">
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
                    <div class="md-inline">
                        <button type="submit" class="formBtn relative d-none success" name="enableBtn">Valider</button>
                        <button class="formBtn relative primary" name="updateForm">Modifier</button>
                    </div>
                </div>
            </form>

        </div>
        <div class="formBlock">
            <div class="formBlockTitle">Activités</div>
            <form class="formBlockWrapper inline newsletter" action="" method="POST">
                @foreach($activites as $activite)
                    <div class="formUnit mr25">
                        <label class="formLabel">{{$activite->libelle}}</label>
                        @if(in_array($activite->id, $club->activites))
                            <input class="formValue" type="checkbox" value="1"/>
                        @else
                            <input class="formValue" type="checkbox" value="0"/>
                        @endif
                    </div>
                @endforeach
            </form>

        </div>
        <div class="formBlock">
            <div class="formBlockTitle">Équipements</div>
            <form class="formBlockWrapper inline newsletter" action="" method="POST">
                @foreach($equipements as $equipement)
                    <div class="formUnit mr25">
                        <label class="formLabel">{{$equipement->libelle}}</label>
                        @if(in_array($equipement->id, $club->equipements))
                            <input class="formValue" type="checkbox" value="1"/>
                        @else
                            <input class="formValue" type="checkbox" value="0"/>
                        @endif
                    </div>
                @endforeach
            </form>
        </div>
        <div class="alertDanger" style="width: 80% !important">
            <p>
                <span class="bold">Attention !</span>
                Cette page est en cours de développement. Elle n'est pas encore fonctionnelle.
            </p>
            <p style="margin-top: 20px">
                on affiche ici informations du club par bloc (coordonnées, équipements, activités, logo), chaque bloc
                étant modifiable indépendamment<br>
                Les équiements et activités sont des cases à cocher parmi des activités et fonctions prédéfinies.<br>
            </p>
        </div>
        <div class="alertDanger" style="width: 80% !important">
            <div class="bold">on récupère les infos du club avec dans la variable $club</div>
            {{ $club }}
        </div>
        <div class="alertDanger" style="width: 80% !important">
            <div class="bold">on récupère les coordonnées dans $club->adresse</div>
            {{ $club->adresse }}
        </div>
        <div class="alertDanger" style="width: 80% !important">
            <div class="bold">on récupère l'ensemble des activités de la FPF dans $activites</div>
            {{ $activites }}
            <br>
            <div class="bold">et les activités proposées par le club dans $club->activites</div>
            {{ json_encode($club->activites) }}
        </div>
        <div class="alertDanger" style="width: 80% !important">
            <div class="bold">on récupère l'ensemble des équipements de la FPF dans $equipements</div>
            {{ $equipements }}
            <br>
            <div class="bold">et les équipements proposés par le club dans $club->equipements</div>
            {{ json_encode($club->equipements) }}
        </div>
    </div>
@endsection
@section('js')
    <script src="{{ asset('js/autocompleteCommune.js') }}?t=<?= time() ?>"></script>

@endsection
