@extends('layouts.default')

@section('content')
    <div class="pageCanva">
        <h1 class="pageTitle">
            Ajouter un club
            <a class="previousPage" title="Retour page précédente" href="{{ route('admin.clubs.index') }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor"
                     class="bi bi-reply-fill" viewBox="0 0 16 16">
                    <path
                        d="M5.921 11.9 1.353 8.62a.719.719 0 0 1 0-1.238L5.921 4.1A.716.716 0 0 1 7 4.719V6c1.5 0 6 0 7 8-2.5-4.5-7-4-7-4v1.281c0 .56-.606.898-1.079.62z"/>
                </svg>
            </a>
        </h1>
        <div class="alertInfo w80">
            <span class="bold">Informations !</span>
            Vous pouvez ici ajouter un club
        </div>
        <form action="{{ route('admin.clubs.store') }}" method="POST" enctype="multipart/form-data" class="w100">
            {{ csrf_field() }}
            <div class="formBlock minW100" >
                <div class="formBlockTitle">Club *</div>
                <div class="formBlockWrapper">
                    <div class="formUnit formUnitAdmin justify-start">
                        <div class="formLabel">Nom du club *</div>
                        <input class="formValue modifying modiformValueAdmin w75" type="text" value="" name="nomClub" />
                    </div>
                    <div class="formUnit formUnitAdmin justify-start">
                        <div class="formLabel">UR *</div>
                        <select class="formValue modifying formValueAdmin" name="urClub">
                            @foreach($urs as $ur)
                                <option value="{{$ur->id}}">{{$ur->nom}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="formUnit formUnitAdmin justify-start">
                        <div class="formLabel">Adresse</div>
                        <input class="formValue modifying formValueAdmin w75 " type="text" value="" name="libelle1Club"/>
                    </div>
                    <div class="formUnit formUnitAdmin justify-start">
                        <div class="formLabel">Complément</div>
                        <input class="formValue modifying formValueAdmin w75" type="text" value="" name="libelle2Club"/>
                    </div>
                    <div class="formUnit formUnitAdmin justify-start">
                        <div class="formLabel">Code postal *</div>
                        <div class="suggestionWrapper">
                            <input name="codepostalClub" type="text" class="formValue modifying w70"
                                   value="{{$ur->adresse?$ur->adresse->codepostal:""}}"
                                   maxlength="10" required/>
                            <div class="suggestion"></div>
                        </div>

                    </div>
                    <div class="formUnit formUnitAdmin justify-start">
                        <div class="formLabel">Commune *</div>
                        <div class="suggestionWrapper">
                            <input name="villeClub" type="text" class="formValue modifying w70"
                                   value="{{$ur->adresse?$ur->adresse->ville:""}}"
                                   maxlength="50" required/>
                            <div class="suggestion"></div>
                        </div>
                    </div>
                    <div class="formUnit formUnitAdmin justify-start">
                        <div class="formLabel">Pays</div>
                        <select class="formValue modifying formValueAdmin pays" name="paysClub" id="paysClub">
                            @foreach($countries as $country)
                                <option value="{{ $country->id }}" data-indicatif="{{ $country->indicatif }}" {{ $country->id == 78 ? 'selected' : '' }}>{{ $country->nom }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="formUnit formUnitAdmin justify-start">
                        <div class="formLabel">Adresse email *</div>
                        <input class="formValue modifying formValueAdmin w75" type="text" value="" name="emailClub"/>
                    </div>
                    <div class="formUnit formUnitAdmin justify-start">
                        <div class="formLabel">Téléphone mobile</div>
                        <div class="inputGroup d-flex justify-start align-start">
                            <div class="indicator" name="indicatifClub">+33</div>
                            <input type="text" name="phoneMobileClub" id="phoneMobileClub" maxlength="25" class="formValue modifying"/>
                        </div>
                    </div>
                    <div class="formUnit formUnitAdmin justify-start">
                        <div class="formLabel">Téléphone fixe</div>
                        <div class="inputGroup d-flex justify-start align-start">
                            <div class="indicator" name="indicatifClub">+33</div>
                            <input type="text" name="phoneFixeClub" id="phoneFixeClub" maxlength="25" class="formValue modifying"/>
                        </div>
                    </div>
                    <div class="formUnit formUnitAdmin justify-start">
                        <div class="formLabel">Abonnement</div>
                        <input type="checkbox" name="abonClub" /><span class="ml10 blue">Abonner le club</span>
                    </div>
                </div>
            </div>
            <div class="formBlock minW100">
                <div class="formBlockTitle">Contact</div>
                <div class="formBlockWrapper">
                    <div class="formUnit formUnitAdmin justify-start">
                        <div class="formLabel">Genre *</div>
                        <div class="d-flex">
                            <div class="d-flex justify-start">
                                <input type="radio" name="sexeContact" id="sexeContact" value="0" checked /> <span class="ml5">Mr</span>
                            </div>
                            <div class="d-flex justify-start ml20">
                                <input type="radio" name="sexeContact" id="sexeContact" value="1" /> <span class="ml5">Mme</span>
                            </div>
                        </div>
                    </div>
                    <div class="formUnit formUnitAdmin justify-start">
                        <div class="formLabel">Nom *</div>
                        <input class="formValue modifying formValueAdmin w75" type="text" value="" name="nomContact"/>
                    </div>
                    <div class="formUnit formUnitAdmin justify-start">
                        <div class="formLabel">Prénom *</div>
                        <input class="formValue modifying formValueAdmin w75" type="text" value="" name="prenomContact"/>
                    </div>
                    <div class="formUnit formUnitAdmin justify-start">
                        <div class="formLabel">Adresse</div>
                        <input class="formValue modifying formValueAdmin w75" type="text" value="" name="libelle1Contact"/>
                    </div>
                    <div class="formUnit formUnitAdmin justify-start">
                        <div class="formLabel">&nbsp;</div>
                        <input class="formValue modifying formValueAdmin w75" type="text" value="" name="libelle2Contact"/>
                    </div>
                    <div class="formUnit formUnitAdmin justify-start">
                        <div class="formLabel">Code postal *</div>
                        <div class="suggestionWrapper">
                            <input name="codepostalContact" type="text" class="formValue modifying w70"
                                   value="{{$ur->adresse?$ur->adresse->codepostal:""}}"
                                   maxlength="10" required/>
                            <div class="suggestion"></div>
                        </div>
                    </div>
                    <div class="formUnit formUnitAdmin justify-start">
                        <div class="formLabel">Commune *</div>
                        <div class="suggestionWrapper">
                            <div class="suggestionWrapper">
                                <input name="villeContact" type="text" class="formValue modifying w70"
                                       value="{{$ur->adresse?$ur->adresse->ville:""}}"
                                       maxlength="50" required/>
                                <div class="suggestion"></div>
                            </div>
                        </div>


                    </div>
                    <div class="formUnit formUnitAdmin justify-start">
                        <div class="formLabel">Pays</div>
                        <select class="formValue modifying formValueAdmin pays" name="paysContact" id="paysContact">
                            @foreach($countries as $country)
                                <option value="{{ $country->id }}" data-indicatif="{{ $country->indicatif }}" {{ $country->id == 78 ? 'selected' : '' }}>{{ $country->nom }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="formUnit formUnitAdmin justify-start">
                        <div class="formLabel">Adresse email *</div>
                        <input class="formValue modifying formValueAdmin w75" type="text" value="" name="emailContact"/>
                    </div>
                    <div class="formUnit formUnitAdmin justify-start">
                        <div class="formLabel">Téléphone mobile *</div>
                        <div class="inputGroup d-flex justify-start align-start">
                            <div class="indicator" name="indicatifContact">+33</div>
                            <input type="text" name="phoneMobileContact" id="phoneMobileContact" class="formValue modifying" maxlength="25"/>
                        </div>

                    </div>
                    <div class="formUnit formUnitAdmin justify-start">
                        <div class="formLabel">Téléphone fixe</div>
                        <div class="inputGroup d-flex justify-start align-start">
                            <div class="indicator" name="indicatifContact">+33</div>
                            <input type="text" name="phoneFixeContact" id="phoneFixeContact" maxlength="25" class="formValue modifying"/>
                        </div>
                    </div>
                    <div class="formUnit formUnitAdmin justify-start">
                        <div class="formLabel">Abonnement</div>
                        <input type="checkbox" name="abonContact" /><span class="ml10 blue">Abonner le contact</span>
                    </div>
                </div>
            </div>
            <div>
                <button class="adminPrimary btnMedium" type="submit">Enregistrer le club</button>
            </div>
        </form>
    </div>
@endsection
@section('css')
    <link href="{{asset('css/admin_fpf.css') }}" rel="stylesheet">
@endsection
@section('js')
    <script src="{{ asset('js/autocompleteCommune.js') }}"></script>
    <script src="{{ asset('js/admin_clubs.js') }}"></script>
@endsection
