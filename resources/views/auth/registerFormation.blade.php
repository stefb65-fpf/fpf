@extends('layouts.login')
@section('content')
    <div class="authWrapper">
        <div class="authLogo">
            <img src="{{ env('APP_URL').'storage/app/public/FPF-100-Logo-Seul.webp' }}"
                 alt="Fédération Photographique de France">
        </div>
        <div class="authTitle">Enregistrement pour une formation</div>
        <div class="fosterRegister">
            <div class="foster">Vous avez déjà un compte ?</div>
            <a href="/login"> Connectez-vous !</a>
        </div>
        <div class="authForm" style="padding-bottom: 200px;">
            <div class="customField2" style="margin: 0 auto">
                <div class="groupCustom mxauto maxW100 w100 mt10 d-flex">
                    <label>Genre</label>
                    <div class="d-flex justify-center w100 text-center">
                        <div class="d-flex justify-start">
                            <input class="w20px h20 pointer" type="radio" name="sexe" id="sexeRegister" value="0" checked/>
                            <span class="ml5">Mr</span>
                            <div class="d-flex justify-start ml20">
                                <input class="w20px h20 pointer" type="radio" name="sexe" id="sexeRegister" value="1"/> <span
                                    class="ml5">Mme</span>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <div class="doubleInput" style="align-items: flex-start">
                <div class="customField">
                    <label>Nom</label>
                    <input type="text" name="name" id="lastnameRegister"/>
                    <div name="error" class="error">message erreur</div>
                </div>
                <div class="customField">
                    <label>Prénom</label>
                    <input type="text" name="firstName" id="firstnameRegister"/>
                    <div name="error" class="error">message erreur</div>
                </div>
            </div>
            <div class="separator"></div>
            <div class="doubleInput" style="align-items: flex-start">
                <div class="customField">
                    <label>E-mail</label>
                    <input type="email" name="email" id="emailRegister"/>
                    <div name="error" class="error">message erreur</div>
                </div>
                <div class="customField">
                    <label>Mot de passe</label>
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
                    <div name="error" class="error">message erreur</div>
                </div>
            </div>
            <div class="separator"></div>
            <div class="doubleInput autosuggestContainer" style="align-items: flex-start">
{{--                <div class="customField">--}}
{{--                    <label>Code postal</label>--}}
{{--                        <input type="text" name="town" class="autosuggestCFA">--}}
{{--                    <div class="error">message erreur</div>--}}
{{--                </div>--}}
                <div class="customField">
                    <label>Code postal **</label>
                    <div class="autoSuggestWrapper">
                        <input type="text" name="codepostal" id="codepostalRegister" class="autosuggestCFA"/>
                        <ul class="autosuggest">
                        </ul>
                    </div>
                    <div name="error" class="error"></div>
                </div>
                <div class="customField">
                    <label>Ville **</label>
                    <div class="autoSuggestWrapper">
                        <input type="text" name="ville" id="villeRegister" class="autosuggestCFA"/>
                        <ul class="autosuggest">
                        </ul>
                    </div>
                    <div name="error" class="error"></div>
                </div>
            </div>
            <div class="separator"></div>
            <div class="doubleInput" style="align-items: flex-start">
                <div class="customField">
                    <label>Téléphone mobile</label>
                    <input type="text" name="phone" id="phoneRegister"/>
                    <div name="error" class="error"></div>
                </div>
                <div class="customField">
                </div>

            </div>
            <div class="button customBtn" id="registerFormation"> Enregistrez-vous</div>
        </div>
    </div>

    <div class="modalEdit d-none" id="modalSameName">
        <div class="modalEditBody">
                Il existe dans la base plusieurs personne avec les mêmes nom et prénom que vous avez indiqués.<br>
                Toutefois, les adresses email sont différentes.<br>
                Vous trouverez ci-dessous la liste des personnes correspondantes. Si une des personnes indiquées vous correspond, vous pourrez utiliser l'adresse email pour vous <a style="text-decoration: underline; color: #1c6ca1;" href="/login">connecter</a>.<br>
                Sinon, cliquez sur "Poursuivre mon inscription".
                <div id="nameSameName" class="ml20 mt20"></div>
        </div>
        <div class="modalEditFooter">
            <div class="button customBtn modalEditClose">Fermer</div>
            <div class="button customBtn mr10" id="confirmSameNameRegister">Poursuivre mon inscription</div>
        </div>
    </div>

    <div class="modalEdit d-none" id="modalConfirmRegister">
        <div class="modalEditBody">
            Votre compte a bien été créé. Cliquez sur le bouton ci-dessous pour vous connecter à votre compte
        </div>
        <div class="modalEditFooter">
            <div class="button customBtn modalEditClose">Annuler</div>
            <a class="button customBtn mr10" href="/login">Me connecter</a>
        </div>
    </div>
@endsection
@section('js')
    <script src="{{ asset('js/register.js') }}?t=<?= time() ?>"></script>
@endsection
