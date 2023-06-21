@extends('layouts.login')

@section('content')
        <div class="authWrapper">
            <div class="authLogo">
                <img src="{{ env('APP_URL').'storage/app/public/FPF-100-Logo-Seul.webp' }}"
                     alt="Fédération Photographique de France">
            </div>
            <div class="authTitle">Enregistrement pour abonnement</div>
            <div class="fosterRegister">
                <div class="foster">Vous avez déjà un compte ?</div>
                <a href="/login"> Connectez-vous !</a>
            </div>
            <div class="authForm">
                <div class="doubleInput">
                    <div class="customField">
                        <label>Nom</label>
                        <input type="text" name="name">
                        <div class="error">message erreur</div>
                    </div>
                    <div class="customField">
                        <label>Prénom</label>
                        <input type="text" name="firstName">
                        <div class="error">message erreur</div>
                    </div>
                </div>
                <div class="separator"></div>
                <div class="doubleInput">
                    <div class="customField">
                        <label>E-mail</label>
                        <input type="email" name="email">
                        <div class="error">message erreur</div>
                    </div>
                    <div class="customField">
                        <label>Mot de passe</label>
                        <div class="group">
                            <input type="password" name="password">
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
                        <div class="error">message erreur</div>
                    </div>
                </div>
                <div class="separator"></div>
                <div class="doubleInput autosuggestContainer">
                    <div class="customField">
                        <label>Code postal</label>
                        <input type="text" name="town" class="autosuggestCFA">
                        <div class="error">message erreur</div>
                    </div>
                    <div class="customField">
                        <label>Ville</label>
                        <div class="autoSuggestWrapper">
                            <input type="text" name="town" class="autosuggestCFA">
                            <ul class="autosuggest">
                                @foreach($communes as $commune)
                                    <li>{{ $commune->nom }}</li>
                                @endforeach
                                <li>DIJON</li>
                                <li>AISEREY</li>
                                <li>BEIRE-LE-FORT</li>
                                <li>BESSEY-LES-CITEAUX</li>
                                <li>BRETENIERE</li>
                                <li>CESSEY-SU-TILLE</li>
                                <li>CHAMBEIRE</li>
                                <li>COLLONGES-LES-PREMIERES</li>
                                <li>EYCHIGEY</li>
                                <li>FAUVERNEY</li>
                                <li>GENLIS</li>
                            </ul>
                        </div>

                        <div class="error">message erreur</div>
                    </div>
                </div>
                <div class="button customBtn"> Enregistrez-vous</div>
            </div>
        </div>
@endsection
