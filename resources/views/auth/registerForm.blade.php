<div class="authForm" id="authForm" style="width: 540px; margin-top: 20px">
    <div class="customField2" style="width: 100%; min-width: 100%; padding-right: 20px;">
        <div class="groupCustom">
            <label>Genre</label>
            <div style="display: flex;">
                <div style="display: flex; justify-content: flex-start;">
                    <input type="radio" name="sexe" id="sexeRegister" value="0" checked /> <span style="margin-left: 5px;">Mr</span>
                </div>
                <div style="display: flex; justify-content: flex-start; margin-left: 20px">
                    <input type="radio" name="sexe" id="sexeRegister" value="1" /> <span style="margin-left: 5px;">Mme</span>
                </div>
            </div>

        </div>
    </div>
    <div class="doubleInput">
        <div class="customField">
            <label>Nom</label>
            <input type="text" name="name" id="lastnameRegister" />
            <div name="error" class="error"></div>
        </div>
        <div class="customField">
            <label>Prénom</label>
            <input type="text" name="firstName" id="firstnameRegister" />
            <div name="error" class="error"></div>
        </div>
    </div>
    <div class="separator"></div>
    <div class="doubleInput">
        <div class="customField">
            <label>E-mail</label>
            <input type="email" name="email" id="emailRegister" />
            <div name="error" class="error"></div>
        </div>
        <div class="customField">
            <label>Mot de passe *</label>
            <div class="group">
                <input type="password" name="password" id="passwordRegister" />
                <div class="icons eye">
                    <div class="icon open">
                        <svg width="22" height="16" viewBox="0 0 22 16" fill="none"
                             xmlns="http://www.w3.org/2000/svg">
                            <path d="M1 8C1 8 4 1 11 1C18 1 21 8 21 8C21 8 18 15 11 15C4 15 1 8 1 8Z"
                                  stroke="black"
                                  stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path
                                d="M11 11C12.6569 11 14 9.65685 14 8C14 6.34315 12.6569 5 11 5C9.34315 5 8 6.34315 8 8C8 9.65685 9.34315 11 11 11Z"
                                stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    <div class="icon closed dark hidden">
                        <svg width="22" height="21" viewBox="0 0 22 21" fill="none"
                             xmlns="http://www.w3.org/2000/svg">
                            <path d="M1 11C1 11 4 4 11 4C18 4 21 11 21 11C21 11 18 18 11 18C4 18 1 11 1 11Z"
                                  stroke="black" stroke-width="2" stroke-linecap="round"
                                  stroke-linejoin="round"/>
                            <path
                                d="M11 14C12.6569 14 14 12.6569 14 11C14 9.34315 12.6569 8 11 8C9.34315 8 8 9.34315 8 11C8 12.6569 9.34315 14 11 14Z"
                                stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M3 20L19.0485 1.27673" stroke="black" stroke-width="2"
                                  stroke-linecap="round"/>
                        </svg>
                    </div>
                </div>
            </div>
            <div name="error" class="error"></div>
        </div>
    </div>
    <div style="font-size: 10px; margin-left: 15px;">* mot de passe entre 8 et 30 caractères, au moins 1 majuscule, 1 minuscule et 1 chiffre </div>
</div>
<div class="button customBtn" id="checkNewUser" data-type="{{ $type }}">Continuer</div>
<div class="d-none" id="registerPart2">
    <div class="authForm" style="width: 540px">
        <div class="customField" style="width: 100%; min-width: 100%; padding-right: 20px;">
            <label>Adresse</label>
            <input type="text" name="name" id="libelle1Register" />
            <div name="error" class="error"></div>
        </div>
        <div class="separator"></div>
        <div class="customField" style="width: 100%; min-width: 100%; padding-right: 20px;">
            <label></label>
            <input type="text" name="name" id="libelle2Register" />
            <div name="error" class="error"></div>
        </div>
        <div class="separator"></div>
        <div style="font-size: 10px; margin-left: 15px; margin-top: 10px;">saisir les deux premiers caractères du code poostal ou de la ville pour bénéficier de l'autocomplétion</div>
        <div class="doubleInput autosuggestContainer">
            <div class="customField">
                <label>Code postal</label>
                <div class="autoSuggestWrapper">
                    <input type="text" name="codepostal" id="codepostalRegister" class="autosuggestCFA" />
                    <ul class="autosuggest">
                    </ul>
                </div>
                <div class="error">message erreur</div>
            </div>
            <div class="customField">
                <label>Ville</label>
                <div class="autoSuggestWrapper">
                    <input type="text" name="ville" id="villeRegister" class="autosuggestCFA" />
                    <ul class="autosuggest">
                    </ul>
                </div>
                <div class="error">message erreur</div>
            </div>
        </div>
        <div class="customField" style="width: 100%; min-width: 100%; padding-right: 20px;">
            <label>Pays</label>
            <select name="" id="paysRegister">
                @foreach($countries as $country)
                    <option value="{{ $country->id }}" data-indicatif="{{ $country->indicatif }}" {{ $country->id == 78 ? 'selected' : '' }}>{{ $country->nom }}</option>
                @endforeach
            </select>
            <div name="error" class="error"></div>
        </div>
        <div class="separator"></div>
        <div class="customField2" style="width: 100%; min-width: 100%; padding-right: 20px;">
            <div class="groupCustom">
                <label>Téléphone mobile</label>
                <div class="inputGroup" style="display: flex; justify-content: flex-start; align-items: flex-start;">
                    <div class="indicatif" id="indicatifRegister">+33</div>
                    <input type="text" name="name" id="phoneRegister" />
                </div>
            </div>
            <div name="error" class="error"></div>
        </div>
        <div class="customField" style="width: 100%; min-width: 100%; padding-right: 20px;">
            <label>Date de naissance</label>
            <input type="date" name="name" id="datenaissanceRegister" />
            <div name="error" class="error"></div>
        </div>
    {{--    <div class="separator"></div>--}}
    {{--    <div class="customField" style="width: 100%; min-width: 100%; padding-right: 20px;">--}}
    {{--        <div style="display: flex; justify-content: flex-start; align-items: center; margin-top: 20px">--}}
    {{--            <input type="checkbox" name="name" id="lastnameRegister" style="width: max-content" />--}}
    {{--            <div style="font-size: 0.9rem; color: #003d77; margin-left: 10px">Je souhaite m'abonner à la revue France Phot pour 5 numéros (€)</div>--}}
    {{--        </div>--}}

    {{--        <div name="error" class="error"></div>--}}
    {{--    </div>--}}
    </div>
    <div class="button customBtn" id="checkTarifForNewUser" data-type="{{ $type }}">Continuer</div>
</div>
<div class="d-none" id="registerPart3">
    <div style="font-weight: bolder; margin-top: 30px">
        Pour votre adhésion, vous devez vous acquitter de la cotisation annuelle de <span style="font-size: large" id="tarifAdhesion"></span>€. <br>
        Veuillez choisir votre mode de paiement ci-dessous pour terminer votre adhésion.<br>
    </div>

    <div style="margin-bottom: 50px;">
        <button class="primary btnRegister" data-type="{{ $type }}">Carte bancaire</button>
        <button class="primary btnRegister" id="payByVirement" data-type="{{ $type }}">Virement instantanné</button>
    </div>
</div>
