@extends('layouts.default')

@section('content')
    <div class="pageCanva">
        <h1 class="pageTitle">
            Gestion Fédérale - Liste des formations
            <a class="previousPage" title="Retour page précédente" href="{{ route('formations.admin_accueil') }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-reply-fill" viewBox="0 0 16 16">
                    <path d="M5.921 11.9 1.353 8.62a.719.719 0 0 1 0-1.238L5.921 4.1A.716.716 0 0 1 7 4.719V6c1.5 0 6 0 7 8-2.5-4.5-7-4-7-4v1.281c0 .56-.606.898-1.079.62z"/>
                </svg>
            </a>
        </h1>
        <div class="d-flex justify-center">
            <a href="{{ route('formations.create') }}" class="btnMedium adminPrimary">Ajouter une formation</a>
            <a href="{{ route('formations.export') }}" class="btnMedium adminWarning ml10">Récapitulatif formations</a>
        </div>
        @foreach($formations as $formation)
            <div class="cardList mt30">
                <div class="card">
                    <div class="inlineMd">
                        <div class="left">
                            @if($formation->new)
                                <div class="tag bgOrange">Nouveau</div>
                            @endif
                            @if(in_array($formation->type, [0,2]))
                                <div class="tag bgBlueLight">À distance</div>
                            @endif
                            @if(in_array($formation->type, [1,2]))
                                <div class="tag bgPurpleLight">Présentiel</div>
                            @endif
                            <div class="tag bgGreen">{{ $formation->categorie->name }}</div>
                            @if(sizeof($formation->sessions->sortBy('start_date')->where('start_date', '>', date('Y-m-d'))) > 0)
                                <div class="tag" style="background-color: #3c3c3c">
                                    Prochaines dates
                                    @foreach($formation->sessions->where('start_date', '>', date('Y-m-d'))->take(5) as $session)
                                        <span class="ml10">{{ date("d/m/Y",strtotime($session->start_date)) }}</span>
                                    @endforeach
                                </div>
                            @endif
                            @if(isset($formation->formateurs[0]))
                                <div class="tag adminWarning">
                                    {{ $formation->formateurs[0]->personne->nom }} {{ $formation->formateurs[0]->personne->prenom }}
                                </div>
                            @endif
                        </div>
                        <div class="right">
                            <svg width="19" height="18" viewBox="0 0 19 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M19 6.88966H11.7483L9.5 0L7.25167 6.88966H0L5.85833 11.1414L3.64167 18L9.5 13.7483L15.3583 18L13.11 11.1103L19 6.88966Z"
                                    fill="{{ $formation->stars > 0 ? '#FFD84F' : '#B7B7B7' }}"/>
                            </svg>
                            <svg width="19" height="18" viewBox="0 0 19 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M19 6.88966H11.7483L9.5 0L7.25167 6.88966H0L5.85833 11.1414L3.64167 18L9.5 13.7483L15.3583 18L13.11 11.1103L19 6.88966Z"
                                    fill="{{ $formation->stars > 1 ? '#FFD84F' : '#B7B7B7' }}"/>
                            </svg>
                            <svg width="19" height="18" viewBox="0 0 19 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M19 6.88966H11.7483L9.5 0L7.25167 6.88966H0L5.85833 11.1414L3.64167 18L9.5 13.7483L15.3583 18L13.11 11.1103L19 6.88966Z"
                                    fill="{{ $formation->stars > 2 ? '#FFD84F' : '#B7B7B7' }}"/>
                            </svg>
                            <svg width="19" height="18" viewBox="0 0 19 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M19 6.88966H11.7483L9.5 0L7.25167 6.88966H0L5.85833 11.1414L3.64167 18L9.5 13.7483L15.3583 18L13.11 11.1103L19 6.88966Z"
                                    fill="{{ $formation->stars > 3 ? '#FFD84F' : '#B7B7B7' }}"/>
                                <defs>
{{--                                    <linearGradient id="paint0_linear_90_3" x1="-2.5" y1="9" x2="20.5" y2="9"--}}
{{--                                                    gradientUnits="userSpaceOnUse">--}}
{{--                                        <stop offset="0.521022" stop-color="#FFD84F"/>--}}
{{--                                        <stop offset="0.522962" stop-color="#B7B7B7"/>--}}
{{--                                    </linearGradient>--}}
                                </defs>
                            </svg>
                            <svg width="19" height="18" viewBox="0 0 19 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M19 6.88966H11.7483L9.5 0L7.25167 6.88966H0L5.85833 11.1414L3.64167 18L9.5 13.7483L15.3583 18L13.11 11.1103L19 6.88966Z"
                                    fill="{{ $formation->stars > 4 ? '#FFD84F' : '#B7B7B7' }}"/>
                            </svg>
                            <div class="score mr10">
                                <div class="bold">{{ $formation->stars }}</div>
                                <div class="on5 mr5">/5</div>
                                <div class="nbvotes">({{ $formation->reviews }} avis)</div>
                            </div>
                        </div>
                    </div>

                    @if($formation->exist_eval)
                        <div class="inlineMd" style="justify-content: flex-end !important; padding: 0 15px;">
                            <a href="{{ route('formations.evaluations', $formation) }}" class="btnSmall ml10 adminPrimary">Liste des évaluations</a>
                        </div>
                    @endif
                    <div class="formationTitle">{{ $formation->name }}</div>
                    <div class="formationChapo">
                        {!! $formation->shortDesc !!}
                    </div>
                    <div class="inlineMd mt10">
                        <div class="left">
                            <div class="badge mr25">
                                <img class="mr10" src="{{ env('APP_URL').'storage/app/public/dashboard.png' }}" alt="">
                                @switch($formation->level)
                                    @case(0)
                                        Débutant
                                        @break
                                    @case(1)
                                        Intermédiaire
                                        @break
                                    @case(2)
                                        Confirmé
                                        @break
                                    @case (5)
                                        Tous niveaux
                                        @break
                                    @case (4)
                                        Intermédiaire/Confirmé
                                        @break
                                    @case (3)
                                        Débutant/Intermédiaire
                                        @break
                                @endswitch
                            </div>
                            @if($formation->duration != '')
                                <div class="badge mr25">
                                    <img class="mr10" src="{{ env('APP_URL').'storage/app/public/clock.png' }}" alt="">
                                    {{ $formation->duration }}
                                </div>
                            @endif
                            <div class="badge mr25">
                                <img class="mr10" src="{{ env('APP_URL').'storage/app/public/map-marker-alt.png' }}" alt="">
                                @if(in_array($formation->type, [0, 2]))
                                    À distance
                                @endif
                                @if(in_array($formation->type, [1, 2]))
                                    {{ $formation->location }}
                                @endif
                            </div>
                        </div>
                        <div class="right">
                            @if($formation->interests > 0)
                                <div class="small">
                                    {{ $formation->interests }} personne(s) intéressée(s)
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="d-flex flexEnd align-center">
                        <a href="{{ route('sessions.index', $formation) }}" class="btnSmall ml10 adminYellow">Gestion des sessions</a>
                        <a href="{{ route('formateurs.liste', $formation) }}" class="btnSmall ml10 adminWarning">Gestion formateurs</a>
                        @if($formation->published == 1)
                            <a href="{{ route('formations.deactivate', $formation->id) }}" class="btnSmall ml10 adminDanger">Dépublier</a>
                        @else
                            <a href="{{ route('formations.activate', $formation->id) }}" class="btnSmall ml10 adminSuccess">Publier</a>
                        @endif
                        <a href="{{ route('formations.edit', $formation->id) }}" class="btnSmall ml10 adminPrimary">Voir / Modifier</a>
                        <a href="{{ route('formations.destroy', $formation->id) }}" data-method="delete"  data-confirm="Voulez-vous vraiment supprimer cette formation ?" class="btnSmall ml10 adminDanger">Supprimer</a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection
@section('css')
    <link href="{{ asset('css/admin_fpf.css') }}" rel="stylesheet">
    <link href="{{ asset('css/formations_fpf.css') }}" rel="stylesheet">
@endsection
