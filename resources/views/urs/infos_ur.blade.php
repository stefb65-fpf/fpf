@extends('layouts.default')

@section('content')
    <div class="pageCanva">
        <h1 class="pageTitle">
            <div>
              Gestion Union Régionale - Informations
                <div class="urTitle">{{ $ur->nom }}</div>
            </div>
            <a class="previousPage" title="Retour page précédente" href="{{ route('urs.gestion') }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-reply-fill" viewBox="0 0 16 16">
                    <path d="M5.921 11.9 1.353 8.62a.719.719 0 0 1 0-1.238L5.921 4.1A.716.716 0 0 1 7 4.719V6c1.5 0 6 0 7 8-2.5-4.5-7-4-7-4v1.281c0 .56-.606.898-1.079.62z"/>
                </svg>
            </a>
        </h1>
        <div class="alertInfo w80">
            <span class="bold">Informations !</span>
            Vous pouvez ici paramétrer les informations de l'UR:
        </div>
        <div class="formBlock">
            <div class="formBlockTitle">Informations d'union régionale</div>
            <div class="formBlockWrapper">
                <h2 class="formSubtitle w100">Généralités</h2>
                <form class="w100" action="{{route('urs.infos.update')}}" method="POST">
                    <input type="hidden" name="_method" value="PUT">
                    {{ csrf_field() }}
                    <div class="formBlockWrapper">
                        <div class="formUnit w100">
                            <div class="formLabel">Nom</div>
                            <input name="nom" class="formValue modifying w70"
                                   type="text" value="{{$ur->nom?$ur->nom:""}}"
                                   maxlength="120" required>
                        </div>
                        <div class="formUnit w100">
                            <div class="formLabel">Courriel</div>
                            <input name="courriel" class="formValue modifying w70"
                                   type="email" value="{{$ur->courriel?$ur->courriel:""}}"
                                   maxlength="120">
                        </div>
                        <div class="formUnit w100">
                            <div class="formLabel">Site web</div>
                            <input name="web" class="formValue modifying w70"
                                   type="text" value="{{$ur->web?$ur->web:""}}"
                                   maxlength="120">
                        </div>
                    </div>
                    <h2 class="formSubtitle">Adresse</h2>
                    <div class="formBlockWrapper">
                        <div class="formValueGroup">
                            <div class="formUnit">
                                <div class="formLabel">Adresse</div>
                                <input name="libelle1" type="text" class="formValue modifying w70"
                                       value="{{$ur->adresse?$ur->adresse->libelle1:""}}"
                                       maxlength="120"/>
                            </div>
                            <div class="formUnit">
                                <div class="formLabel">Complément</div>
                                <input name="libelle2" class="formValue modifying w70"
                                       type="text" value="{{$ur->adresse?$ur->adresse->libelle2:""}}"
                                       maxlength="120"/>
                            </div>
                            <div class="formUnit">
                                <div class="formLabel">Code Postal</div>
                                <div class="suggestionWrapper">
                                    <input name="codepostal" type="text" class="formValue modifying w70"
                                           value="{{$ur->adresse?$ur->adresse->codepostal:""}}"
                                           maxlength="10" required/>
                                    <div class="suggestion"></div>
                                </div>
                            </div>
                            <div class="formUnit">
                                <div class="formLabel">Ville</div>
                                <div class="suggestionWrapper">
                                    <input name="ville" type="text" class="formValue modifying w70"
                                           value="{{$ur->adresse?$ur->adresse->ville:""}}"
                                           maxlength="50" required/>
                                    <div class="suggestion"></div>
                                </div>
                            </div>
                            <div class="formUnit">
                                <div class="formLabel">Pays</div>
                                <select class="formValue pays modifying" name="pays" required>
                                    <option value="">Selectionnez un pays</option>
                                    @foreach($countries as $country)
                                        @if($ur->adresse)
                                            <option value="{{$country->id}}"
                                                    {{strtolower($country->nom) == strtolower($ur->adresse->pays)? "selected":""}} data-indicator="{{$country->indicatif}}">{{$country->nom}}</option>
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
                                        class="indicator {{$ur->adresse && $ur->adresse->indicatif_fixe!==""?"":"d-none"}}">
                                        +{{$ur->adresse?$ur->adresse->indicatif_fixe:""}}</div>
                                    <input class="formValue phoneInput modifying" type="text"
                                           value="{{$ur->adresse?$ur->adresse->telephonedomicile:""}}" name="telephonedomicile" maxlength="25"/>
                                </div>
                            </div>
                            <div class="formUnit">
                                <div class="formLabel">Téléphone mobile</div>
                                <div class="group">
                                    {{--                                                        <div class="indicator {{$ur->adresse && $ur->adresse->indicatif_mobile!==""?"":"d-none"}}">+{{$ur->adresse?$ur->adresse->indicatif_mobile:""}}</div>--}}
                                    <input class="formValue modifying" type="text"
                                           value="{{$ur->adresse?$ur->adresse->telephonemobile:""}}"
                                           name="telephonemobile" maxlength="25"/>
                                </div>
                            </div>

                        </div> {{-- end formvaluegroup--}}
                        <div class="formUnit align-start mt25">
                            <div class="formLabel">Départements</div>
                            <div>
                                @foreach($ur->departements as $departement)
                                    <p>
                                        {{$departement->numerodepartement}} - {{$departement->libelle}}
                                    </p>
                                @endforeach
                            </div>
                        </div>
                    </div>{{-- end formBlockWrapper--}}
                    <button class="formBtn success" type="submit">Valider</button>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('css')
    <link href="{{asset('css/admin_fpf.css') }}" rel="stylesheet">
@endsection
@section('js')
    <script src="{{ asset('js/autocompleteCommune.js') }}"></script>

@endsection
