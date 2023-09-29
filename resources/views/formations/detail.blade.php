@extends('layouts.default')

@section('content')
    <div class="formationsPage pageCanva">
        <h1 class="pageTitle">
            {{ $formation->name }}
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
                        <div class="favorite md-mt-15 md-mx-auto pointer active mr25"
                        >
                            <svg width="29" height="24" viewBox="0 0 29 24" fill="none"
                                 xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M20.2548 0C17.6729 0 15.4124 1.11026 14.0033 2.98695C12.5942 1.11026 10.3337 0 7.75183 0C5.69663 0.00231647 3.72626 0.819769 2.27302 2.27302C0.819769 3.72626 0.00231647 5.69663 0 7.75183C0 16.5039 12.9768 23.5881 13.5294 23.8806C13.6751 23.959 13.8379 24 14.0033 24C14.1687 24 14.3315 23.959 14.4772 23.8806C15.0298 23.5881 28.0066 16.5039 28.0066 7.75183C28.0043 5.69663 27.1868 3.72626 25.7336 2.27302C24.2803 0.819769 22.31 0.00231647 20.2548 0ZM14.0033 21.8552C11.7203 20.5248 2.00047 14.4647 2.00047 7.75183C2.00246 6.22708 2.60904 4.76536 3.6872 3.6872C4.76536 2.60904 6.22708 2.00246 7.75183 2.00047C10.1837 2.00047 12.2254 3.29578 13.0781 5.37627C13.1534 5.55972 13.2816 5.71663 13.4464 5.82706C13.6111 5.93749 13.805 5.99645 14.0033 5.99645C14.2016 5.99645 14.3955 5.93749 14.5602 5.82706C14.725 5.71663 14.8532 5.55972 14.9285 5.37627C15.7812 3.29203 17.823 2.00047 20.2548 2.00047C21.7795 2.00246 23.2412 2.60904 24.3194 3.6872C25.3976 4.76536 26.0041 6.22708 26.0061 7.75183C26.0061 14.4547 16.2838 20.5236 14.0033 21.8552Z"
                                    fill="#454545"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
            <div class="flex-2 bgGreen">a</div>
        </div>

    </div>
@endsection
@section('css')
    <link href="{{ asset('css/formations_fpf.css') }}" rel="stylesheet">
@endsection
