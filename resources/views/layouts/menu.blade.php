<nav class="navbar hidden">
    <div class="navbarWrapper">
        <a href="{{ env('APP_URL') }}">
            <div class="top">
                <div class="logo"><img src="{{ env('APP_URL').'storage/app/public/FPF-100-Logo-Seul.webp' }}"
                                       alt="Fédération Photographique de France"></div>
                <div class="topTitle">Fédération Photographique de France</div>
            </div>
        </a>
        <div class="coords">
            <div>5 rue Jules Vallès 75011 PARIS</div>
            <div><a href="tel: +33143713040">(+33) 01 43 71 30 40</a></div>
            <div><a href="mailto:fpf@federation-photo.fr">fpf@federation-photo.fr</a></div>
        </div>
        <div class="bottom">

            <div class="memberCard">
                <div class="memberCardTop">
                    <div>
                        <svg width="40" height="40" viewBox="0 0 40 40" fill="none"
                             xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M36.6667 5H3.33333C1.51667 5.06667 0.0666667 6.51667 0 8.33333V31.6667C0.0666667 33.4833 1.51667 34.9333 3.33333 35H36.6667C38.4833 34.9333 39.9333 33.4833 40 31.6667V8.33333C39.9693 7.45904 39.6083 6.6289 38.9897 6.01031C38.3711 5.39171 37.541 5.03068 36.6667 5ZM36.6667 31.6667H3.33333V8.33333H36.6667V31.6667ZM23.3333 28.3333V26.25C23.3333 23.4833 17.7667 22.0833 15 22.0833C12.2333 22.0833 6.66667 23.4833 6.66667 26.25V28.3333H23.3333ZM15 11.6667C13.8949 11.6667 12.8351 12.1057 12.0537 12.8871C11.2723 13.6685 10.8333 14.7283 10.8333 15.8333C10.8333 16.3805 10.9411 16.9223 11.1505 17.4278C11.3599 17.9334 11.6668 18.3927 12.0537 18.7796C12.8351 19.561 13.8949 20 15 20C15.5472 20 16.089 19.8922 16.5945 19.6828C17.1 19.4734 17.5594 19.1665 17.9463 18.7796C18.3332 18.3927 18.6401 17.9334 18.8495 17.4278C19.0589 16.9223 19.1667 16.3805 19.1667 15.8333C19.1667 15.2862 19.0589 14.7443 18.8495 14.2388C18.6401 13.7333 18.3332 13.274 17.9463 12.8871C17.5594 12.5001 17.1 12.1932 16.5945 11.9838C16.089 11.7744 15.5472 11.6667 15 11.6667ZM23.3333 11.6667V13.3333H33.3333V11.6667H23.3333ZM23.3333 15V16.6667H33.3333V15H23.3333ZM23.3333 18.3333V20H30V18.3333H23.3333Z"
                                fill="#003d77"/>
                        </svg>
                    </div>
                    <div class="memberCardRight">
                        @if($cartes && sizeof($cartes) > 0)
                            <div>{{ $cartes[0]->identifiant }}</div>
                        @endif
                        @if($user->is_abonne == 1)
                            <div class="bold italic uppercase fs12px">
                                abonné jusqu'au n°{{ $user->abonnement->fin }}
                            </div>
                        @endif
                    </div>
                </div>
                @if($cartes && sizeof($cartes) > 1)
                    <div class="memberCardBottom dropdownParent">
                        <div class="fosterChange dropdownCall" id="dropdownLink">changer de carte
                            <svg class="dropdownCall" data-dropdown-id="12" width="13" height="9" viewBox="0 0 13 9"
                                 fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M6.5 9L0.870834 0.75L12.1292 0.75L6.5 9Z" fill="#003d77"/>
                            </svg>
                        </div>
                        <div class="dropdown">
                            <ul>
                                @foreach($cartes as $k => $carte)
                                    @if($k > 0)
                                        <li>
                                            <a name="changeCardUser"
                                               data-ref="{{ $carte->id }}">{{ $carte->identifiant }}</a>
                                        </li>
                                    @endif
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif
                @if($cartes)
                    <div class="mt10">
                        <a class="btnCard" href="{{ route('souscription-individuelle') }}">souscrire une carte individuelle</a>
                    </div>
                    @if($user->renew_abo)
                        <div class="mt10">
                            <a class="btnCard" href="{{ route('souscription-abonnement') }}">{{ $user->is_abonne ? 'prolonger mon abonnement' : 'ajouter un abonnement' }}</a>
                        </div>
                    @endif
                @endif
            </div>
            <ul class="leftMenu" id="navMenu">
                @if($menu['formation'])
                    <li class="leftMenuItem">
                        <a href="{{ route('formations.accueil') }}">
                            formations
                        </a>
                    </li>
                @endif
                @if($menu['club'])
                    <li class="leftMenuItem{{ Request::is('gestion-club')?" active":""}}">
                        <a href="{{ route('clubs.gestion') }}">
                            Gestion club
                        </a>
                    </li>
                @endif
                @if($menu['ur'])
                    <li class="leftMenuItem{{ Request::is('gestion-ur')?" active":""}}">
                        <a href="{{ route('urs.gestion') }}">
                            Gestion UR
                        </a>
                    </li>
                @endif
                @if($menu['admin'])
                    <li class="leftMenuItem{{ Request::is('gestion-fpf')?" active":""}}">
                        <a href="{{ route('admin') }}">
                            Gestion FPF
                        </a>
                    </li>
                @endif
                @if(in_array('VISUSTAT', $droits_fpf))
                    <li class="leftMenuItem">
                        <a href="{{ route('admin.statistiques') }}">
                            Tableau de bord
                        </a>
                    </li>
                @else
                    @if($menu['ur'] && in_array('VISUSTATUR', $droits_fpf))
                        <li class="leftMenuItem">
                            <a href="{{ route('urs.statistiques') }}">
                                Tableau de bord UR
                            </a>
                        </li>
                    @else
                        @if($menu['club'])
                            <li class="leftMenuItem">
                                <a href="{{ route('clubs.statistiques') }}">
                                    Tableau de bord Club
                                </a>
                            </li>
                        @endif
                    @endif
                @endif

                @if(in_array('SUPPORT', $droits_fpf))
                    <li class="leftMenuItem">
                        <a href="{{ route('supports.index') }}">
                            Support
                        </a>
                    </li>
                @endif
            </ul>
        </div>

        <div class="hamburgerIcon">
            <svg width="50" height="29" viewBox="0 0 50 29" fill="none" xmlns="http://www.w3.org/2000/svg">
                <line y1="1" x2="50" y2="1" stroke="white" stroke-width="2"/>
                <line y1="14.5" x2="50" y2="14.5" stroke="white" stroke-width="2"/>
                <line y1="28" x2="50" y2="28" stroke="white" stroke-width="2"/>
            </svg>
        </div>
    </div>
</nav>
