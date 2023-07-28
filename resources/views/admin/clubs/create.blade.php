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
        <div class="alertInfo" style="width: 80% !important">
            <span class="bold">Informations !</span>
            Vous pouvez ici ajouter un club
        </div>
        <form action="{{ route('admin.clubs.store') }}" method="POST" enctype="multipart/form-data" style="width: 100%;">
            {{ csrf_field() }}
            <div class="formBlock" style="min-width: 100%">
                <div class="formBlockTitle">Club *</div>
                <div class="formBlockWrapper">
                    <div class="formUnit formUnitAdmin">
                        <div class="formLabel">Nom du club *</div>
                        <input class="formValue formValueAdmin w75" type="text" value="" name="nomClub" />
                    </div>
                    <div class="formUnit formUnitAdmin">
                        <div class="formLabel">UR *</div>
                        <select class="formValue formValueAdmin" name="urClub">
                            @foreach($urs as $ur)
                                <option value="{{$ur->id}}">{{$ur->nom}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="formUnit formUnitAdmin">
                        <div class="formLabel">Adresse</div>
                        <input class="formValue formValueAdmin w75" type="text" value="" name="libelle1Club"/>
                    </div>
                    <div class="formUnit formUnitAdmin">
                        <div class="formLabel">&nbsp;</div>
                        <input class="formValue formValueAdmin w75" type="text" value="" name="libelle2Club"/>
                    </div>
                    <div class="formUnit formUnitAdmin">
                        <div class="formLabel">Code postal *</div>
                        <input class="formValue formValueAdmin" type="text" value="" name="codepostalClub"/>
                    </div>
                    <div class="formUnit formUnitAdmin">
                        <div class="formLabel">Commune *</div>
                        <input class="formValue formValueAdmin w75" type="text" value="" name="villeClub"/>
                    </div>
                    <div class="formUnit formUnitAdmin">
                        <div class="formLabel">Pays</div>
                        <select class="formValue formValueAdmin" name="paysClub" id="paysClub">
                            @foreach($countries as $country)
                                <option value="{{ $country->id }}" data-indicatif="{{ $country->indicatif }}" {{ $country->id == 78 ? 'selected' : '' }}>{{ $country->nom }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="formUnit formUnitAdmin">
                        <div class="formLabel">Adresse email *</div>
                        <input class="formValue formValueAdmin w75" type="text" value="" name="emailClub"/>
                    </div>
                    <div class="formUnit formUnitAdmin">
                        <div class="formLabel">Téléphone mobile</div>
                        <div class="inputGroup" style="display: flex; justify-content: flex-start; align-items: flex-start;">
                            <div class="indicatif" name="indicatifClub">+33</div>
                            <input type="text" name="phoneMobileClub" id="phoneMobileClub" />
                        </div>
                    </div>
                    <div class="formUnit formUnitAdmin">
                        <div class="formLabel">Téléphone fixe</div>
                        <div class="inputGroup" style="display: flex; justify-content: flex-start; align-items: flex-start;">
                            <div class="indicatif" name="indicatifClub">+33</div>
                            <input type="text" name="phoneFixeClub" id="phoneFixeClub" />
                        </div>
                    </div>
                    <div class="formUnit formUnitAdmin">
                        <div class="formLabel">Abonnement</div>
                        <input type="checkbox" name="abonClub" /><span style="margin-left: 10px; color:#003d77">Abonner le club</span>
                    </div>
                </div>
            </div>
            <div class="formBlock" style="min-width: 100%">
                <div class="formBlockTitle">Contact</div>
                <div class="formBlockWrapper">
                    <div class="formUnit formUnitAdmin">
                        <div class="formLabel">Genre *</div>
                        <div style="display: flex;">
                            <div style="display: flex; justify-content: flex-start;">
                                <input type="radio" name="sexeContact" id="sexeContact" value="0" checked /> <span style="margin-left: 5px;">Mr</span>
                            </div>
                            <div style="display: flex; justify-content: flex-start; margin-left: 20px">
                                <input type="radio" name="sexeContact" id="sexeContact" value="1" /> <span style="margin-left: 5px;">Mme</span>
                            </div>
                        </div>
                    </div>
                    <div class="formUnit formUnitAdmin">
                        <div class="formLabel">Nom *</div>
                        <input class="formValue formValueAdmin w75" type="text" value="" name="nomContact"/>
                    </div>
                    <div class="formUnit formUnitAdmin">
                        <div class="formLabel">Prénom *</div>
                        <input class="formValue formValueAdmin w75" type="text" value="" name="prenomContact"/>
                    </div>
                    <div class="formUnit formUnitAdmin">
                        <div class="formLabel">Adresse</div>
                        <input class="formValue formValueAdmin w75" type="text" value="" name="libelle1Contact"/>
                    </div>
                    <div class="formUnit formUnitAdmin">
                        <div class="formLabel">&nbsp;</div>
                        <input class="formValue formValueAdmin w75" type="text" value="" name="libelle2Contact"/>
                    </div>
                    <div class="formUnit formUnitAdmin">
                        <div class="formLabel">Code postal *</div>
                        <input class="formValue formValueAdmin w75" type="text" value="" name="codepostalContact"/>
                    </div>
                    <div class="formUnit formUnitAdmin">
                        <div class="formLabel">Commune *</div>
                        <input class="formValue formValueAdmin w75" type="text" value="" name="villeContact"/>
                    </div>
                    <div class="formUnit formUnitAdmin">
                        <div class="formLabel">Pays</div>
                        <select class="formValue formValueAdmin" name="paysContact" id="paysContact">
                            @foreach($countries as $country)
                                <option value="{{ $country->id }}" data-indicatif="{{ $country->indicatif }}" {{ $country->id == 78 ? 'selected' : '' }}>{{ $country->nom }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="formUnit formUnitAdmin">
                        <div class="formLabel">Adresse email *</div>
                        <input class="formValue formValueAdmin w75" type="text" value="" name="emailContact"/>
                    </div>
                    <div class="formUnit formUnitAdmin">
                        <div class="formLabel">Téléphone mobile *</div>
                        <div class="inputGroup" style="display: flex; justify-content: flex-start; align-items: flex-start;">
                            <div class="indicatif" name="indicatifContact">+33</div>
                            <input type="text" name="phoneMobileContact" id="phoneMobileContact" />
                        </div>

                    </div>
                    <div class="formUnit formUnitAdmin">
                        <div class="formLabel">Téléphone fixe</div>
                        <div class="inputGroup" style="display: flex; justify-content: flex-start; align-items: flex-start;">
                            <div class="indicatif" name="indicatifContact">+33</div>
                            <input type="text" name="phoneFixeContact" id="phoneFixeContact" />
                        </div>
                    </div>
                    <div class="formUnit formUnitAdmin">
                        <div class="formLabel">Abonnement</div>
                        <input type="checkbox" name="abonContact" /><span style="margin-left: 10px; color:#003d77">Abonner le contact</span>
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
