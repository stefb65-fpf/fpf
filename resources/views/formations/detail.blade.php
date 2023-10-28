@extends('layouts.default')

@section('content')
    <div class="formationsPage pageCanva">
        <h1 class="pageTitle">
            {{ $formation->name }}
            <a class="previousPage" title="Retour page précédente" href="{{ route('formations.accueil') }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor"
                     class="bi bi-reply-fill" viewBox="0 0 16 16">
                    <path
                        d="M5.921 11.9 1.353 8.62a.719.719 0 0 1 0-1.238L5.921 4.1A.716.716 0 0 1 7 4.719V6c1.5 0 6 0 7 8-2.5-4.5-7-4-7-4v1.281c0 .56-.606.898-1.079.62z"/>
                </svg>
            </a>
        </h1>
        <div class="d-flex maxW1400 w100 mxauto flexWrap gap20 flex-lg-column">
            <div class="flex-3">
                <div class="card">
                    <div class="inlineMd">
                        <div class="left">
                            @if($formation->new)
                                <div class="tag bgOrange">Nouveau</div>
                            @endif
                            @if($formation->type == 0 || $formation->type == 2)
                                <div class="tag bgBlueLight">À distance</div>
                            @endif
                            @if($formation->type == 1 || $formation->type == 2)
                                <div class="tag bgPurpleLight">Présentiel</div>
                            @endif
                            {{--                                TODO: gérer le tag dernières places en fonction des places réservées et des places disponibles--}}
                            @if($formation->places <  5)
                                <div class="tag bgRed">Dernières Places</div>
                            @endif
                            @if($formation->categorie)
                                <div class="tag bgGreen">{{$formation->categorie->name}}</div>
                            @endif
                        </div>
                        @if($formation->reviews)
                            <div class="right hoverable" name="reviews" data-id="{{ $formation->id }}">
                                @if($formation->reviews)
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= floor($formation->stars))
                                            <svg width="19" height="18" viewBox="0 0 19 18" fill="none"
                                                 xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                    d="M19 6.88966H11.7483L9.5 0L7.25167 6.88966H0L5.85833 11.1414L3.64167 18L9.5 13.7483L15.3583 18L13.11 11.1103L19 6.88966Z"
                                                    fill="#FFD84F"/>
                                            </svg>
                                        @elseif(($formation->stars - floor($formation->stars))&&($i == (floor($formation->stars)+1)))
                                            <svg width="19" height="18" viewBox="0 0 19 18" fill="none"
                                                 xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                    d="M19 6.88966H11.7483L9.5 0L7.25167 6.88966H0L5.85833 11.1414L3.64167 18L9.5 13.7483L15.3583 18L13.11 11.1103L19 6.88966Z"
                                                    fill="url(#paint0_linear_90_3)"/>
                                                <defs>
                                                    <linearGradient id="paint0_linear_90_3" x1="-2.5" y1="9" x2="20.5"
                                                                    y2="9"
                                                                    gradientUnits="userSpaceOnUse">
                                                        <stop offset="0.521022" stop-color="#FFD84F"/>
                                                        <stop offset="0.522962" stop-color="#B7B7B7"/>
                                                    </linearGradient>
                                                </defs>
                                            </svg>
                                        @else
                                            <svg width="19" height="18" viewBox="0 0 19 18" fill="none"
                                                 xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                    d="M19 6.88966H11.7483L9.5 0L7.25167 6.88966H0L5.85833 11.1414L3.64167 18L9.5 13.7483L15.3583 18L13.11 11.1103L19 6.88966Z"
                                                    fill="#B7B7B7"/>
                                            </svg>
                                        @endif
                                    @endfor
                                    <div class="score mr10">
                                        <div class="bold">
                                            {{($formation->stars - floor($formation->stars)) == 0 ? floor($formation->stars):$formation->stars}}
                                        </div>
                                        <div class="on5 mr5">/5</div>
                                        <div class="nbvotes">({{$formation->reviews}} avis)</div>
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                    <div class="formationTitle">{{ $formation->name }}</div>
                    <div class="formationShortDesc">
                        {!! $formation->shortDesc !!}
                    </div>
                    <div class="inlineMd mt10">
                        <div class="left">
                            <div class="badge mr25">
                                <img class="mr10" src="{{ env('APP_URL').'storage/app/public/dashboard.png' }}"
                                     alt="">
                                @switch($formation->level)
                                    @case (2)
                                    confirmé
                                    @break
                                    @case(1)
                                    intermédiaire
                                    @break
                                    @default
                                    initiation
                                @endswitch
                            </div>
                            @if($formation->duration)
                                <div class="badge mr25">
                                    <img class="mr10" src="{{ env('APP_URL').'storage/app/public/clock.png' }}"
                                         alt="">
                                    {{$formation->duration}}
                                </div>
                            @endif

                            <div class="badge mr25">
                                <img class="mr10" src="{{ env('APP_URL').'storage/app/public/map-marker-alt.png' }}"
                                     alt="">
                                @if(!($formation->type == 1))
                                    À distance

                                @endif
                                @if($formation->location)
                                    @if($formation->type == 2)
                                        /
                                    @endif
                                    {{ $formation->location}}
                                @endif
                            </div>
                        </div>
                        <div class="favorite md-mt-15 md-ml-auto pointer mr20 {{ $formation->interest ? 'active' : '' }}" title='{{ $formation->interest ? "Supprimer votre intérêt pour cette formation" : "Marquer votre intérêt pour la formation" }}' data-formation="{{ $formation->id }}">
                            <svg width="29" height="24" viewBox="0 0 29 24" fill="none"
                                 xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M14.803 3.58738C16.0101 1.97971 17.9648 1.00016 20.2542 1C22.0444 1.00217 23.7606 1.71427 25.0265 2.98012C26.2925 4.24611 27.0046 5.96258 27.0066 7.75295C27.006 11.6606 24.0741 15.3604 20.7903 18.2362C17.5589 21.0661 14.2518 22.8685 14.0093 22.9968L14.0093 22.9968L14.0034 23L14.0033 23L14.0032 23L14.0032 23L13.9973 22.9968C13.7548 22.8685 10.4477 21.0661 7.21631 18.2362C3.93234 15.3603 1.00027 11.6603 1 7.75238C1.00217 5.96221 1.71427 4.24598 2.98012 2.98012C4.24597 1.71427 5.96221 1.00217 7.75238 1C10.0418 1.00016 11.9965 1.97971 13.2036 3.58738L14.0033 4.65244L14.803 3.58738Z"
                                    stroke="#454545" stroke-width="2"/>
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="card p0">
                    <div class="cardTitle">Descriptif de la formation</div>
                    <div class="cardContent">
                        {!! $formation->longDesc !!}
                    </div>
                </div>
                <div class="card p0">
                    <div class="cardTitle">Dates des futures sessions</div>
                    <div class="cardContent">
                        @if($menu['ur'] || $menu['club'])
                            <div class="mb10">
                                @if($menu['ur'])
                                    <a class="redBtn mr10" name="askFormation" data-formation="{{ $formation->id }}" data-level="ur" style="width: max-content; cursor:pointer;">Demande UR pour organiser une session</a>
                                @endif
                                @if($menu['club'])
                                    <a class="redBtn mr10" name="askFormation" data-formation="{{ $formation->id }}" data-level="club" style="width: max-content; cursor:pointer;">Demande Club pour organiser une session</a>
                                @endif
                            </div>
                        @endif
                        @foreach($formation->sessions as $k => $session)
                            @if($session->start_date >= date('Y-m-d'))
                                <div class="sessionContainer">
                                    <div class="start">
                                        <div class="icon mr10">
                                            <svg width="22" height="22" viewBox="0 0 22 22" fill="none"
                                                 xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                    d="M1 11C1 7.229 1 5.343 2.172 4.172C3.343 3 5.229 3 9 3H13C16.771 3 18.657 3 19.828 4.172C21 5.343 21 7.229 21 11V13C21 16.771 21 18.657 19.828 19.828C18.657 21 16.771 21 13 21H9C5.229 21 3.343 21 2.172 19.828C1 18.657 1 16.771 1 13V11Z"
                                                    stroke="#454545" stroke-width="1.5"/>
                                                <path d="M6 3V1.5M16 3V1.5M1.5 8H20.5" stroke="#454545"
                                                      stroke-width="1.5"
                                                      stroke-linecap="round"/>
                                                <path
                                                    d="M17 16C17 16.2652 16.8946 16.5196 16.7071 16.7071C16.5196 16.8946 16.2652 17 16 17C15.7348 17 15.4804 16.8946 15.2929 16.7071C15.1054 16.5196 15 16.2652 15 16C15 15.7348 15.1054 15.4804 15.2929 15.2929C15.4804 15.1054 15.7348 15 16 15C16.2652 15 16.5196 15.1054 16.7071 15.2929C16.8946 15.4804 17 15.7348 17 16ZM17 12C17 12.2652 16.8946 12.5196 16.7071 12.7071C16.5196 12.8946 16.2652 13 16 13C15.7348 13 15.4804 12.8946 15.2929 12.7071C15.1054 12.5196 15 12.2652 15 12C15 11.7348 15.1054 11.4804 15.2929 11.2929C15.4804 11.1054 15.7348 11 16 11C16.2652 11 16.5196 11.1054 16.7071 11.2929C16.8946 11.4804 17 11.7348 17 12ZM12 16C12 16.2652 11.8946 16.5196 11.7071 16.7071C11.5196 16.8946 11.2652 17 11 17C10.7348 17 10.4804 16.8946 10.2929 16.7071C10.1054 16.5196 10 16.2652 10 16C10 15.7348 10.1054 15.4804 10.2929 15.2929C10.4804 15.1054 10.7348 15 11 15C11.2652 15 11.5196 15.1054 11.7071 15.2929C11.8946 15.4804 12 15.7348 12 16ZM12 12C12 12.2652 11.8946 12.5196 11.7071 12.7071C11.5196 12.8946 11.2652 13 11 13C10.7348 13 10.4804 12.8946 10.2929 12.7071C10.1054 12.5196 10 12.2652 10 12C10 11.7348 10.1054 11.4804 10.2929 11.2929C10.4804 11.1054 10.7348 11 11 11C11.2652 11 11.5196 11.1054 11.7071 11.2929C11.8946 11.4804 12 11.7348 12 12ZM7 16C7 16.2652 6.89464 16.5196 6.70711 16.7071C6.51957 16.8946 6.26522 17 6 17C5.73478 17 5.48043 16.8946 5.29289 16.7071C5.10536 16.5196 5 16.2652 5 16C5 15.7348 5.10536 15.4804 5.29289 15.2929C5.48043 15.1054 5.73478 15 6 15C6.26522 15 6.51957 15.1054 6.70711 15.2929C6.89464 15.4804 7 15.7348 7 16ZM7 12C7 12.2652 6.89464 12.5196 6.70711 12.7071C6.51957 12.8946 6.26522 13 6 13C5.73478 13 5.48043 12.8946 5.29289 12.7071C5.10536 12.5196 5 12.2652 5 12C5 11.7348 5.10536 11.4804 5.29289 11.2929C5.48043 11.1054 5.73478 11 6 11C6.26522 11 6.51957 11.1054 6.70711 11.2929C6.89464 11.4804 7 11.7348 7 12Z"
                                                    fill="#454545"/>
                                            </svg>
                                        </div>
                                        {{ date("d/m/Y",strtotime($session->start_date)) }}
                                    </div>
                                    <div class="places">
                                        @if(sizeof($session->inscrits->where('status', 1)->where('attente', 0)) < $session->places)
                                            <div class="green">Places disponibles</div>
                                        @else
                                            @if(sizeof($session->inscrits->where('status', 1)->where('attente', 1)) < $session->waiting_places )
                                                <div class="green">Places disponibles en liste d'attente</div>
                                            @else
                                                <div class="red">Complet</div>
                                            @endif
                                        @endif
                                    </div>
                                    <div class="price justify-center">
                                        @if($session->price_not_member != $session->price)
                                            <div style="font-size: 0.8rem">
                                                <span>
                                                    Adhérent: {{ floatval($session->price) == intval($session->price) ? intval($session->price) : floatval($session->price) }} €
                                                </span>
                                                <br>
                                                <span>Non adhérent: {{ floatval($session->price_not_member) == intval($session->price_not_member) ? intval($session->price_not_member) : floatval($session->price_not_member) }} €</span>
                                            </div>
                                        @else
                                            {{ floatval($session->price) == intval($session->price) ? intval($session->price) : floatval($session->price) }} €
                                        @endif
                                    </div>
                                    <div class="locations">
                                        <img class="mr10"
                                             src="{{ env('APP_URL').'storage/app/public/map-marker-alt.png' }}">
                                        @if(!$session->type)
                                            À distance
                                        @elseif($session->type == 1)
                                            @if($session->location)
                                                {{$session->location}}
                                            @else
                                                {{$formation->location}}
                                            @endif
                                        @else
                                            À distance
                                            @if($session->location)
                                                / {{$session->location}}
                                            @else
                                                /  {{$formation->location}}
                                            @endif
                                        @endif

                                    </div>
                                    <div class="inscription">
                                        @if(in_array($session->id, $inscriptions))
                                            @if($session->inscrits->where('personne_id',$personne->id) && ($session->inscrits->where('personne_id',$personne->id)->first()->attente_paiement == 1))
                                                <a class="orangeBtn" href="{{ route('formations.payWithSecureCode', $personne->inscrits->where('session_id', $session->id)->first()->secure_code) }}">En attente de paiement</a>
                                            @elseif($session->inscrits->where('personne_id',$personne->id)->first()->personne_id == $personne->id)
                                                <div class="bold">Vous êtes inscrit.e à cette session</div>
                                            @endif
                                        @else
                                            @if((sizeof($session->inscrits->where('status', 1)->where('attente', 0)) < $session->places) && $session->full == 0)
                                                <a name="paiementInscription" data-session="{{ $session->id }}"
                                                   data-price="{{ $personne->price_adherent == 1 ? $session->price : $session->price_not_member }}" data-avoir="{{ $personne->avoir_formation }}" class="redBtn uppercase"
                                                   style="cursor: pointer;">S'inscrire</a>
                                            @else
                                                @if(sizeof($session->inscrits->where('status', 1)->where('attente', 1)) < $session->waiting_places || $session->full == 1)
                                                    {{--                                            @if(sizeof($session->inscrits->where('status', 1)) >= $session->places && sizeof($session->inscrits->where('status', 1)) < $session->places + $session->waiting_places )--}}
                                                    <a name="attenteInscription" data-session="{{ $session->id }}"
                                                       class="redBtn uppercase bgOrange hMaxContent"
                                                       style="cursor: pointer;">S'inscrire en liste d'attente</a>
                                                @else
                                                    <span class="bold">
                                                      Inscription terminée
                                                </span>
                                                @endif
                                            @endif
                                        @endif
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="flex-2">
                <div class="card p0">
                    <div class="cardTitle">Nos intervenants</div>
                    <div class="cardContent flexWrap d-flex gap20 justify-center align-center">
                        @foreach($formation->formateurs as $formateur)
                            <div class="formateur" name="formateur" data-id="{{ $formateur->id }}">
                                @if($formateur->image)
                                    <img
                                        src="{{ env('APP_URL').'storage/app/public/uploads/formateurs/'.$formateur->image }}"
                                        alt="">
                                @else
                                    <img
                                        src="{{ env('APP_URL').'storage/app/public/default_image_intervenant.png'}}"
                                        alt="">
                                @endif
                                <div class="name">
                                    {{$formateur->personne->prenom." ".$formateur->personne->nom}}
                                </div>
                                <div class="function">
                                    {{$formateur->title}}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="card p0">
                    <div class="cardTitle">Programme</div>
                    <div class="cardContent">
                        {!! $formation->program !!}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modalEdit d-none" id="modalPaiementFormation">
        <div class="modalEditHeader">
            <div class="modalEditTitle">Inscription à une session de formation</div>
            <div class="modalEditClose">
                X
            </div>
        </div>
        <div class="modalEditBody">
            Vous allez vous inscrire pour la session de formation <span class="bold">{{ $formation->name }}</span>.<br>
            @if($personne->avoir_formation > 0)
                <div>
                    Vous disposez d'un avoir de <span class="bold">{{ $personne->avoir_formation }} €</span> sur votre compte.
                </div>
            @endif
            <div id="paiementFormationNeeded">
                Pour valider votre inscription, vous devez payer la somme de <span id="priceModalPaiementFormation"
                                                                                   class="bold"></span> par virement
                immédiat ou carte bancaire.<br>
                Aucune autre méthode de paiement ne sera acceptée.
            </div>
            <div id="paiementFormationNotNeeded" class="d-none">
                Votre avoir étant supérieur au prix de la formation, vous n'avez pas besoin de payer pour valider votre inscription.<br>
                Vous pouvez valider votre inscription en cliquant sur le bouton ci-dessous.
            </div>
        </div>
        <div class="modalEditFooter">
            <div class="adminDanger btnMedium mr10 modalEditClose">Annuler</div>
            <div class="adminPrimary btnMedium mr10" id="formationPayVirement" data-link="" data-ref="">Payer par virement</div>
            <div class="adminPrimary btnMedium mr10" id="formationPayCb" data-link="" data-ref="">Payer par CB</div>
            <div class="adminPrimary btnMedium mr10 d-none" id="saveFormationWithoutPaiement" data-ref="">Valider mon inscription</div>
        </div>
    </div>

    <div class="modalEdit d-none" id="modalAttenteFormation">
        <div class="modalEditHeader">
            <div class="modalEditTitle">Inscription à une session de formation</div>
            <div class="modalEditClose">
                X
            </div>
        </div>
        <div class="modalEditBody">
            Vous allez vous inscrire sur liste d'attente pour la session de formation <span
                class="bold">{{ $formation->name }}</span>.<br>
            Aucun paiement ne sera exigé pour le moment. Si une place se libère en liste principale, un lien de paiement
            vous sera envoyé par mail.
        </div>
        <div class="modalEditFooter">
            <div class="adminDanger btnMedium mr10 modalEditClose">Annuler</div>
            <div class="adminPrimary btnMedium mr10" id="formationInscriptionAttente" data-ref="">Confirmer
                l'inscription en liste d'attente
            </div>
        </div>
    </div>

    <div class="modalEdit d-none" id="modalAskFormation">
        <div class="modalEditHeader">
            <div class="modalEditTitle">Demande d'organisation de session</div>
            <div class="modalEditClose">
                X
            </div>
        </div>
        <div class="modalEditBody">
            Vous souhaitez soumettre une demande d'organisation de session de formation <span
                class="bold">{{ $formation->name }}</span>.<br>
            Une fois votre demande validée, vous serez contacté par les responsables du département formation.
        </div>
        <div class="modalEditFooter">
            <div class="adminDanger btnMedium mr10 modalEditClose">Annuler</div>
            <div class="adminPrimary btnMedium mr10" id="confirmAskFormation" data-formation="{{ $formation->id }}">Valider la demande</div>
        </div>
    </div>
@endsection
@section('css')
    <link href="{{ asset('css/admin_fpf.css') }}" rel="stylesheet">
    <link href="{{ asset('css/formations_fpf.css') }}" rel="stylesheet">
@endsection
@section('js')
    <script src="{{ asset('js/formation.js') }}?t=<?= time() ?>"></script>
@endsection
