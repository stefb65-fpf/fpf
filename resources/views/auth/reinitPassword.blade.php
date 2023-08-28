@extends('layouts.login')

@section('content')
    <div class="authWrapper">
        <div class="authLogo">
            <img src="{{ env('APP_URL').'storage/app/public/FPF-100-Logo-Seul.webp' }}"
                 alt="Fédération Photographique de France">
        </div>
        <div class="authTitle">Réinitialisation de votre <br> mot de passe</div>
        <div class="fosterRegister light">
            <div class="foster">Enregistrez votre nouveau mot de passe</div>
        </div>
        <form action="{{ route('resetPassword', $personne) }}" method="POST" class="authForm align-center">
            <input type="hidden" name="_method" value="put">
            {{ csrf_field() }}
            <div class="customField">

                <div class="group">
                    <input class="checkableInput original" type="password" name="password" value="{{ old('password', '') }}"/>
                    <div class="icons eye">
                        <div class="icon open">
                            <svg width="22" height="16" viewBox="0 0 22 16" fill="none"
                                 xmlns="http://www.w3.org/2000/svg">
                                <path d="M1 8C1 8 4 1 11 1C18 1 21 8 21 8C21 8 18 15 11 15C4 15 1 8 1 8Z" stroke="black"
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
                                      stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path
                                    d="M11 14C12.6569 14 14 12.6569 14 11C14 9.34315 12.6569 8 11 8C9.34315 8 8 9.34315 8 11C8 12.6569 9.34315 14 11 14Z"
                                    stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M3 20L19.0485 1.27673" stroke="black" stroke-width="2" stroke-linecap="round"/>
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="error">message erreur</div>
            </div>
            <div class="customField">
                <label>Confirmez</label>
                <div class="group">
                    <input class="checkableInput confirmation" type="password" name="password_confirmation" value="{{ old('password_confirmation', '') }}">
                    <div class="icons eye">
                        <div class="icon open">
                            <svg width="22" height="16" viewBox="0 0 22 16" fill="none"
                                 xmlns="http://www.w3.org/2000/svg">
                                <path d="M1 8C1 8 4 1 11 1C18 1 21 8 21 8C21 8 18 15 11 15C4 15 1 8 1 8Z" stroke="black"
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
                                      stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path
                                    d="M11 14C12.6569 14 14 12.6569 14 11C14 9.34315 12.6569 8 11 8C9.34315 8 8 9.34315 8 11C8 12.6569 9.34315 14 11 14Z"
                                    stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M3 20L19.0485 1.27673" stroke="black" stroke-width="2" stroke-linecap="round"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="error">message erreur</div>

            </div>
            <button id="resetPasswordBtn" type="submit" class="button customBtn" disabled > Réinitialisez</button>
            <div class="instructions">
                <div class="text">
                    Pour des raisons de sécurité, votre mot de passe doit répondre aux critères suivants:
                </div>
                <div class="list">
                    <div class="item width">
                        <div class="checkedBox">
                            <svg width="17" height="17" viewBox="0 0 17 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M7.14232 11.8335L3.86364 8.55409L4.95627 7.46145L7.14232 9.64672L11.5129 5.27541L12.6063 6.36881L7.14232 11.832V11.8335Z" fill="#55B074"/>
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M0 3.09091C0 2.27115 0.325648 1.48496 0.905306 0.905306C1.48496 0.325648 2.27115 0 3.09091 0H13.9091C14.7289 0 15.515 0.325648 16.0947 0.905306C16.6744 1.48496 17 2.27115 17 3.09091V13.9091C17 14.7289 16.6744 15.515 16.0947 16.0947C15.515 16.6744 14.7289 17 13.9091 17H3.09091C2.27115 17 1.48496 16.6744 0.905306 16.0947C0.325648 15.515 0 14.7289 0 13.9091V3.09091ZM3.09091 1.54545H13.9091C14.319 1.54545 14.7121 1.70828 15.0019 1.99811C15.2917 2.28794 15.4545 2.68103 15.4545 3.09091V13.9091C15.4545 14.319 15.2917 14.7121 15.0019 15.0019C14.7121 15.2917 14.319 15.4545 13.9091 15.4545H3.09091C2.68103 15.4545 2.28794 15.2917 1.99811 15.0019C1.70828 14.7121 1.54545 14.319 1.54545 13.9091V3.09091C1.54545 2.68103 1.70828 2.28794 1.99811 1.99811C2.28794 1.70828 2.68103 1.54545 3.09091 1.54545Z" fill="#55B074"/>
                            </svg>

                        </div>
                        Entre 8 et 30 caractères
                    </div>
                    <div class="item smallLetter">
                        <div class="checkedBox">
                            <svg width="17" height="17" viewBox="0 0 17 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M7.14232 11.8335L3.86364 8.55409L4.95627 7.46145L7.14232 9.64672L11.5129 5.27541L12.6063 6.36881L7.14232 11.832V11.8335Z" fill="#55B074"/>
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M0 3.09091C0 2.27115 0.325648 1.48496 0.905306 0.905306C1.48496 0.325648 2.27115 0 3.09091 0H13.9091C14.7289 0 15.515 0.325648 16.0947 0.905306C16.6744 1.48496 17 2.27115 17 3.09091V13.9091C17 14.7289 16.6744 15.515 16.0947 16.0947C15.515 16.6744 14.7289 17 13.9091 17H3.09091C2.27115 17 1.48496 16.6744 0.905306 16.0947C0.325648 15.515 0 14.7289 0 13.9091V3.09091ZM3.09091 1.54545H13.9091C14.319 1.54545 14.7121 1.70828 15.0019 1.99811C15.2917 2.28794 15.4545 2.68103 15.4545 3.09091V13.9091C15.4545 14.319 15.2917 14.7121 15.0019 15.0019C14.7121 15.2917 14.319 15.4545 13.9091 15.4545H3.09091C2.68103 15.4545 2.28794 15.2917 1.99811 15.0019C1.70828 14.7121 1.54545 14.319 1.54545 13.9091V3.09091C1.54545 2.68103 1.70828 2.28794 1.99811 1.99811C2.28794 1.70828 2.68103 1.54545 3.09091 1.54545Z" fill="#55B074"/>
                            </svg>

                        </div>
                        Au moins une lettre minuscule
                    </div>
                    <div class="item upperCaseLetter">
                        <div class="checkedBox">
                            <svg width="17" height="17" viewBox="0 0 17 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M7.14232 11.8335L3.86364 8.55409L4.95627 7.46145L7.14232 9.64672L11.5129 5.27541L12.6063 6.36881L7.14232 11.832V11.8335Z" fill="#55B074"/>
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M0 3.09091C0 2.27115 0.325648 1.48496 0.905306 0.905306C1.48496 0.325648 2.27115 0 3.09091 0H13.9091C14.7289 0 15.515 0.325648 16.0947 0.905306C16.6744 1.48496 17 2.27115 17 3.09091V13.9091C17 14.7289 16.6744 15.515 16.0947 16.0947C15.515 16.6744 14.7289 17 13.9091 17H3.09091C2.27115 17 1.48496 16.6744 0.905306 16.0947C0.325648 15.515 0 14.7289 0 13.9091V3.09091ZM3.09091 1.54545H13.9091C14.319 1.54545 14.7121 1.70828 15.0019 1.99811C15.2917 2.28794 15.4545 2.68103 15.4545 3.09091V13.9091C15.4545 14.319 15.2917 14.7121 15.0019 15.0019C14.7121 15.2917 14.319 15.4545 13.9091 15.4545H3.09091C2.68103 15.4545 2.28794 15.2917 1.99811 15.0019C1.70828 14.7121 1.54545 14.319 1.54545 13.9091V3.09091C1.54545 2.68103 1.70828 2.28794 1.99811 1.99811C2.28794 1.70828 2.68103 1.54545 3.09091 1.54545Z" fill="#55B074"/>
                            </svg>

                        </div>
                        Au moins une lettre majuscule
                    </div>
                    <div class="item number">
                        <div class="checkedBox">
                            <svg width="17" height="17" viewBox="0 0 17 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M7.14232 11.8335L3.86364 8.55409L4.95627 7.46145L7.14232 9.64672L11.5129 5.27541L12.6063 6.36881L7.14232 11.832V11.8335Z" fill="#55B074"/>
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M0 3.09091C0 2.27115 0.325648 1.48496 0.905306 0.905306C1.48496 0.325648 2.27115 0 3.09091 0H13.9091C14.7289 0 15.515 0.325648 16.0947 0.905306C16.6744 1.48496 17 2.27115 17 3.09091V13.9091C17 14.7289 16.6744 15.515 16.0947 16.0947C15.515 16.6744 14.7289 17 13.9091 17H3.09091C2.27115 17 1.48496 16.6744 0.905306 16.0947C0.325648 15.515 0 14.7289 0 13.9091V3.09091ZM3.09091 1.54545H13.9091C14.319 1.54545 14.7121 1.70828 15.0019 1.99811C15.2917 2.28794 15.4545 2.68103 15.4545 3.09091V13.9091C15.4545 14.319 15.2917 14.7121 15.0019 15.0019C14.7121 15.2917 14.319 15.4545 13.9091 15.4545H3.09091C2.68103 15.4545 2.28794 15.2917 1.99811 15.0019C1.70828 14.7121 1.54545 14.319 1.54545 13.9091V3.09091C1.54545 2.68103 1.70828 2.28794 1.99811 1.99811C2.28794 1.70828 2.68103 1.54545 3.09091 1.54545Z" fill="#55B074"/>
                            </svg>

                        </div>
                        Au moins un chiffre
                    </div>
                    <div class="item confirmation">
                        <div class="checkedBox">
                            <svg width="17" height="17" viewBox="0 0 17 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M7.14232 11.8335L3.86364 8.55409L4.95627 7.46145L7.14232 9.64672L11.5129 5.27541L12.6063 6.36881L7.14232 11.832V11.8335Z" fill="#55B074"/>
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M0 3.09091C0 2.27115 0.325648 1.48496 0.905306 0.905306C1.48496 0.325648 2.27115 0 3.09091 0H13.9091C14.7289 0 15.515 0.325648 16.0947 0.905306C16.6744 1.48496 17 2.27115 17 3.09091V13.9091C17 14.7289 16.6744 15.515 16.0947 16.0947C15.515 16.6744 14.7289 17 13.9091 17H3.09091C2.27115 17 1.48496 16.6744 0.905306 16.0947C0.325648 15.515 0 14.7289 0 13.9091V3.09091ZM3.09091 1.54545H13.9091C14.319 1.54545 14.7121 1.70828 15.0019 1.99811C15.2917 2.28794 15.4545 2.68103 15.4545 3.09091V13.9091C15.4545 14.319 15.2917 14.7121 15.0019 15.0019C14.7121 15.2917 14.319 15.4545 13.9091 15.4545H3.09091C2.68103 15.4545 2.28794 15.2917 1.99811 15.0019C1.70828 14.7121 1.54545 14.319 1.54545 13.9091V3.09091C1.54545 2.68103 1.70828 2.28794 1.99811 1.99811C2.28794 1.70828 2.68103 1.54545 3.09091 1.54545Z" fill="#55B074"/>
                            </svg>

                        </div>
                        Être identique à la confirmation
                    </div>
                </div>
            </div>
        </form>


    </div>
@endsection
