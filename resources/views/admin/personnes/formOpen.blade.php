<form class="w100" action="{{ route($level.'.personnes.storeOpen', $view_type) }}" method="POST">
    {{ csrf_field() }}
    <div class="formBlock minW100">
        <div class="formBlockTitle">Civilité</div>
        <div class="formBlockWrapper">
            <div class="formUnit formUnitAdmin">
                <div class="formLabel">Genre *</div>
                <div class="d-flex justify-start">
                    <div class="d-flex justify-start">
                        <input type="radio" name="sexe"
                               value="0" {{ $personne->sexe == 0 ? 'checked' : '' }} /> <span
                            class="ml5">Mr</span>
                    </div>
                    <div class="d-flex justify-start ml20">
                        <input type="radio" name="sexe"
                               value="1" {{ $personne->sexe == 1 ? 'checked' : '' }} /> <span class="ml5">Mme</span>
                    </div>
                </div>
            </div>
            <div class="formUnit formUnitAdmin">
                <div class="formLabel">Prénom *</div>
                <input class="formValue formValueAdmin w75 modifying" type="text"
                       value="{{ old('prenom', $personne->prenom) }}" name="prenom"/>
            </div>
            <div class="formUnit formUnitAdmin">
                <div class="formLabel">Nom *</div>
                <input class="formValue modifying formValueAdmin w75" type="text"
                       value="{{ old('nom', $personne->nom) }}" name="nom"/>
            </div>
        </div>
    </div>
    <div class="formBlock minW100">
        <div class="formBlockTitle">Coordonnées numériques</div>
        <div class="formBlockWrapper">
            <div class="formUnit formUnitAdmin">
                <div class="formLabel">Adresse email *</div>
                <input class="formValue modifying formValueAdmin w75" type="email"
                       value="{{ old('email', $personne->email) }}" name="email"/>
            </div>
            <div class="formUnit formUnitAdmin">
                <div class="formLabel">Téléphone mobile *</div>
                <div class="inputGroup d-flex justify-start align-start">
                    <div class="indicator" name="indicatifMobile" id="indicatifMobile">
                        +33</div>
                    <input class="formValue modifying formValueAdmin w75" type="text"
                           value="{{ old('phone_mobile', $personne->phone_mobile) }}" name="phone_mobile"
                           maxlength="25"/>
                </div>
            </div>
        </div>
    </div>
    @if($level == 'admin')
        <div class="formBlock minW100">
            <div class="formBlockTitle">UR</div>
            <div class="formBlockWrapper">
                <div class="formUnit formUnitAdmin">
                    <div class="formLabel">UR *</div>
                    <select name="urs_id">
                        @for($i=1; $i <26; $i++)
                            <option value="{{ $i }}">UR {{ str_pad($i, 2, '0', STR_PAD_LEFT) }}</option>
                        @endfor
                    </select>
                </div>
            </div>
        </div>
    @else
        <input type="hidden" name="urs_id" value="{{ $ur->id }}">
    @endif

    <div class="d-flex align-start">
        <button class="adminPrimary btnMedium" type="submit">
            Ajouter la personne
        </button>
    </div>
</form>
