<div class="authForm mt20 w540 mt10" id="authForm">
    <div class="customField2 w100 minW100  pr20">
        <div class="groupCustom mxauto maxW100 w100 mt10 d-flex">
            <label>Genre</label>
            <div class="d-flex justify-center w100 text-center">
                <div class="d-flex justify-start">
                    <input class="w20px h20 pointer" type="radio" name="sexe" id="sexeRegister" value="0" checked/>
                    <span class="ml5">Mr</span>
                    </divclass>
                    <div class="d-flex justify-start ml20">
                        <input class="w20px h20 pointer" type="radio" name="sexe" id="sexeRegister" value="1"/> <span
                            class="ml5">Mme</span>
                    </div>
                </div>

            </div>
        </div>
        <div class="doubleInput">
            <div class="customField w100 maxW100 m0">
                <label>Nom</label>
                <input type="text" name="name" id="lastnameRegister"/>
                <div name="error" class="error"></div>
            </div>
            <div class="customField w100 maxW100 m0">
                <label>Prénom</label>
                <input type="text" name="firstName" id="firstnameRegister"/>
                <div name="error" class="error"></div>
            </div>
        </div>
        <div class="separator"></div>
        <div class="doubleInput">
            <div class="customField w100 maxW100 m0">
                <label>E-mail</label>
                <input type="email" name="email" id="emailRegister"/>
                <div name="error" class="error"></div>
            </div>
            <div class="customField w100 maxW100 m0">
                <label>Mot de passe *</label>
                <div class="group">
                    <input type="password" name="password" id="passwordRegister"/>
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
        <div class="fs10px ml15 mxauto">* mot de passe entre 8 et 30 caractères, au moins 1 majuscule, 1 minuscule et 1
            chiffre
        </div>
    </div>
    <div class="button customBtn" id="checkNewUser" data-type="{{ $type }}">Continuer</div>
    <div class="d-none" id="registerPart2">
        <div class="authForm w540 overflowyHidden">
            <div class="customField w100 minW100 pr20">
                <label>Adresse</label>
                <input type="text" name="name" id="libelle1Register"/>
                <div name="error" class="error"></div>
            </div>
            <div class="separator"></div>
            <div class="customField w100 minW100 pr20">
                <label></label>
                <input type="text" name="name" id="libelle2Register"/>
                <div name="error" class="error"></div>
            </div>
            <div class="separator"></div>
            <div class="doubleInput autosuggestContainer">
                <div class="customField">
                    <label>Code postal **</label>
                    <div class="autoSuggestWrapper">
                        <input type="text" name="codepostal" id="codepostalRegister" class="autosuggestCFA"/>
                        <ul class="autosuggest">
                        </ul>
                    </div>
                    <div class="error"></div>
                </div>
                <div class="customField">
                    <label>Ville **</label>
                    <div class="autoSuggestWrapper">
                        <input type="text" name="ville" id="villeRegister" class="autosuggestCFA"/>
                        <ul class="autosuggest">
                        </ul>
                    </div>
                    <div class="error"></div>
                </div>
            </div>
            <div class="fs10px ml15">** saisir les deux premiers caractères du code postal ou de la ville pour bénéficier
                de l'autocomplétion
            </div>
            <div class="customField w100 minW100 pr20">
                <label>Pays</label>
                <select name="" id="paysRegister">
                    @foreach($countries as $country)
                        <option value="{{ $country->id }}"
                                data-indicatif="{{ $country->indicatif }}" {{ $country->id == 78 ? 'selected' : '' }}>{{ $country->nom }}</option>
                    @endforeach
                </select>
                <div name="error" class="error"></div>
            </div>
            <div class="separator"></div>
            <div class="customField2 w100 minW100 pr20">
                <div class="groupCustom">
                    <label>Téléphone mobile</label>
                    <div class="inputGroup d-flex justify-start align-start group">
                        <div class="indicator" id="indicatifRegister">+33</div>
                        <input class="phoneInput formValue modifying" type="text" name="name" id="phoneRegister"/>
                    </div>
                </div>
                <div name="error" class="error"></div>
            </div>
            @if($type == 'adhesion')
                <div class="customField w100 minW100 pr20">
                    <label>Date de naissance</label>
                    <input type="date" name="name" id="datenaissanceRegister" />
                    <div name="error" class="error"></div>
                </div>
                <div class="customField w100 minW100 pr20">
                    <label>Carte famille - Identifiant première carte ***</label>
                    <input type="text" name="name" id="premierecarteRegister" maxlength="12" />
                    <div name="error" class="error"></div>
                </div>
                <div class="fs10px ml15">*** Si vous souscrivez une carte famille, indiquez un numéro valide de première carte d'adhérent à tarif normal. La réduction de tarif ne sera prise en compte que si le numéro de première carte est valide.
                </div>

            @endif
        </div>
        <div class="button customBtn" id="checkTarifForNewUser" data-type="{{ $type }}">Continuer</div>
    </div>

    <div class="d-none" id="registerPart3">
        @if($type == 'adhesion')
            <div class="bold mt30">
                Pour votre adhésion, vous devez vous acquitter de la cotisation annuelle de <span class="fsLarge"
                                                                                                  id="tarifAdhesion"></span>€.
                <br>
                Veuillez choisir votre mode de paiement ci-dessous pour terminer votre adhésion.<br>
            </div>
            <div class="d-none" id="aboSuppAdhesion">
                <div class="customField w100 minW100 pr20">
                    <div class="d-flex justify-start align-center mt20">
                        <input type="checkbox" name="aboFpRegister" id="aboFpRegister" class="wMaxContent"/>
                        <div class="small blue ml10 bold">Je souhaite également m'abonner à la revue France Photo pour 5
                            numéros (<span id="prixAboFp"></span>€)
                        </div>
                    </div>

                    <div name="error" class="error"></div>
                </div>
            </div>
        @endif
        @if($type == 'abonnement')
            <div class="bold mt30">
                Pour votre abonnement, vous devez vous acquitter du montant de <span class="fsLarge"
                                                                                     id="tarifAbonnement"></span>€. <br>
                Veuillez choisir votre mode de paiement ci-dessous pour terminer votre abonnement.<br>
            </div>
        @endif

        <div class="mb50">
            <button class="primary btnRegister" name="payByVirement" data-paiement="cb" data-type="{{ $type }}">Carte
                bancaire
            </button>
            <button class="primary btnRegister" name="payByVirement" data-paiement="bridge" data-type="{{ $type }}">
                Virement instantané
            </button>
        </div>
    </div>
</div>
