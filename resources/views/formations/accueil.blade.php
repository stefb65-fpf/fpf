@extends('layouts.default')

@section('content')
    <div class="formationsPage pageCanva">
        <h1 class="pageTitle">
            Formations
        </h1>
        <div class="cardList">
            @foreach($formations as $formation)
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
                        <div class="right">
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
                                @foreach($formation->cities as $city)
                                    / {{$city}}
                                @endforeach
                            </div>
                        </div>
                        <a class="seeMore md-mt-15 md-mx-auto"
                           href="{{ route('formations.detail', [$formation->id, 'formation']) }}">Voir la Formation</a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection
@section('css')
    <link href="{{ asset('css/formations_fpf.css') }}" rel="stylesheet">
@endsection
