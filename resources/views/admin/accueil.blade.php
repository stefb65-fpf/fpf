@extends('layouts.default')

@section('content')
    <div class="pageCanva">
        <h1 class="pageTitle">
            Gestion Fédérale
        </h1>
        <div>
        </div>
        @if(sizeof($affectations) > 0)
            <div class="alertDanger w80">
                <span class="bold">Informations !</span>
                Il existe des adhérents de la région parisienne en attente d'affectation. Veuillez les affecter à une UR
                avant d'éditer les cartes.
                @foreach($affectations as $affectation)
                    <div class="alertList d-flex justify-between align-start mt10 mb10 pb5">
                        <div>
                            {{ $affectation->personne->prenom.' '.$affectation->personne->nom }}
                        </div>
                        <div>
                            {{ $affectation->personne->email }}<br>
                            {{ $affectation->personne->phone_mobile }}
                        </div>
                        <div>
                            {!! $affectation->personne->adresses[0]->libelle1 ? $affectation->personne->adresses[0]->libelle1.'<br>' : '' !!}
                            {!! $affectation->personne->adresses[0]->libelle2 ? $affectation->personne->adresses[0]->libelle2.'<br>' : '' !!}
                            {{ $affectation->personne->adresses[0]->codepostal.' '.$affectation->personne->adresses[0]->ville }}
                            <br>
                        </div>
                        <div>
                            <select class="p5" name="selectAffectationUr">
                                <option value="15" selected>UR 15</option>
                                <option value="16">UR 16</option>
                                <option value="17">UR 17</option>
                                <option value="18">UR 18</option>
                            </select>
                        </div>
                        <div>
                            <button class="btnSmall adminDanger" name="validAffectationUr" data-ur="15"
                                    data-identifiant="{{ $affectation->identifiant }}">Affecter
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
        <div class="cardContainer">
            @if(in_array('GESADH', $droits_fpf))
                <a class="card" href="{{ route('personnes.index') }}">
                    <div class="wrapper">
                        <svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M15 0C6.72875 0 0 6.72875 0 15C0 23.2713 6.72875 30 15 30C23.2713 30 30 23.2713 30 15C30 6.72875 23.2713 0 15 0ZM8.75 27.2463V26.25C8.75 22.8038 11.5538 20 15 20C18.4462 20 21.25 22.8038 21.25 26.25V27.2463C19.3737 28.2075 17.2488 28.75 15 28.75C12.7512 28.75 10.6263 28.2075 8.75 27.2463ZM22.5 26.5188V26.25C22.5 22.1137 19.1363 18.75 15 18.75C10.8637 18.75 7.5 22.1137 7.5 26.25V26.5188C3.74 24.0625 1.25 19.8162 1.25 15C1.25 7.41875 7.41875 1.25 15 1.25C22.5813 1.25 28.75 7.41875 28.75 15C28.75 19.8162 26.26 24.0625 22.5 26.5188ZM15 6.25C12.2425 6.25 10 8.4925 10 11.25C10 14.0075 12.2425 16.25 15 16.25C17.7575 16.25 20 14.0075 20 11.25C20 8.4925 17.7575 6.25 15 6.25ZM15 15C12.9325 15 11.25 13.3175 11.25 11.25C11.25 9.1825 12.9325 7.5 15 7.5C17.0675 7.5 18.75 9.1825 18.75 11.25C18.75 13.3175 17.0675 15 15 15Z"
                                fill="#2F2F2F"/>
                        </svg>
                        <div class="cardTitle">Utilisateurs Base en Ligne</div>
                    </div>
                </a>
            @endif
            @if(in_array('GESSTR', $droits_fpf))
                <a class="card" href="{{ route('admin.structures') }}">
                    <div class="wrapper">
                        <svg width="30" height="28" viewBox="0 0 30 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M26.875 20V15.625C26.875 13.9 25.475 12.5 23.75 12.5H15.625V7.5H18.75V0H11.25V7.5H14.375V12.5H6.25C4.525 12.5 3.125 13.9 3.125 15.625V20H0V27.5H7.5V20H4.375V15.625C4.375 14.5875 5.2125 13.75 6.25 13.75H14.375V20H11.25V27.5H18.75V20H15.625V13.75H23.75C24.7875 13.75 25.625 14.5875 25.625 15.625V20H22.5V27.5H30V20H26.875ZM12.5 6.25V1.25H17.5V6.25H12.5ZM6.25 21.25V26.25H1.25V21.25H6.25ZM17.5 21.25V26.25H12.5V21.25H17.5ZM28.75 26.25H23.75V21.25H28.75V26.25Z"
                                fill="#2F2F2F"/>
                        </svg>
                        <div class="cardTitle">Structures & Fonctions</div>
                    </div>
                </a>
            @endif
            @if(in_array('GESREG', $droits_fpf))
                <a class="card" href="{{ route('reglements.index') }}">
                    <div class="wrapper">
                        <svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <g clip-path="url(#clip0_201_16)">
                                <path
                                    d="M21.25 0C16.3438 0 12.5 2.19625 12.5 5V10.4725C11.3675 10.1725 10.1012 10 8.75 10C3.84375 10 0 12.1962 0 15V25C0 27.8038 3.84375 30 8.75 30C13.6562 30 17.5 27.8038 17.5 25V24.5187C18.6663 24.8287 19.9438 25 21.25 25C26.1562 25 30 22.8038 30 20V5C30 2.19625 26.1562 0 21.25 0ZM28.75 15C28.75 17.0325 25.315 18.75 21.25 18.75C19.9238 18.75 18.6425 18.5613 17.5 18.22V15C17.5 14.8363 17.4838 14.675 17.4588 14.5162C18.6313 14.8325 19.9187 15 21.25 15C24.4763 15 27.2337 14.0475 28.75 12.6V15ZM1.25 17.6C2.76625 19.0475 5.52375 20 8.75 20C11.9763 20 14.7337 19.0475 16.25 17.6V20C16.25 22.0325 12.815 23.75 8.75 23.75C4.685 23.75 1.25 22.0325 1.25 20V17.6ZM21.25 1.25C25.315 1.25 28.75 2.9675 28.75 5C28.75 7.0325 25.315 8.75 21.25 8.75C17.185 8.75 13.75 7.0325 13.75 5C13.75 2.9675 17.185 1.25 21.25 1.25ZM13.75 7.6C15.2663 9.0475 18.0237 10 21.25 10C24.4763 10 27.2337 9.0475 28.75 7.6V10C28.75 12.0325 25.315 13.75 21.25 13.75C19.6287 13.75 18.0862 13.4838 16.78 12.9838C16.1325 12.1238 15.08 11.3963 13.75 10.8738V7.6ZM8.75 11.25C12.815 11.25 16.25 12.9675 16.25 15C16.25 17.0325 12.815 18.75 8.75 18.75C4.685 18.75 1.25 17.0325 1.25 15C1.25 12.9675 4.685 11.25 8.75 11.25ZM8.75 28.75C4.685 28.75 1.25 27.0325 1.25 25V22.6C2.76625 24.0475 5.52375 25 8.75 25C11.9763 25 14.7337 24.0475 16.25 22.6V25C16.25 27.0325 12.815 28.75 8.75 28.75ZM21.25 23.75C19.9287 23.75 18.6425 23.5637 17.5 23.2225V19.52C18.665 19.83 19.9412 19.9987 21.25 19.9987C24.4763 19.9987 27.2337 19.0463 28.75 17.5988V19.9987C28.75 22.0312 25.315 23.75 21.25 23.75Z"
                                    fill="#2F2F2F"/>
                            </g>
                            <defs>
                                <clipPath id="clip0_201_16">
                                    <rect width="30" height="30" fill="white"/>
                                </clipPath>
                            </defs>
                        </svg>
                        <div class="cardTitle">Règlements</div>
                    </div>
                </a>
            @endif
            @if(in_array('GESREG', $droits_fpf))
                <a class="card" href="{{ route('reglements.cartes') }}">
                    <div class="wrapper">
                        <svg width="36" height="30" viewBox="0 0 36 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M29.25 30H6.75C3.0285 30 0 26.9715 0 23.25V6.75C0 3.0285 3.0285 0 6.75 0H29.25C32.9715 0 36 3.0285 36 6.75V23.25C36 26.9715 32.9715 30 29.25 30ZM6.75 1.5C3.855 1.5 1.5 3.855 1.5 6.75V23.25C1.5 26.145 3.855 28.5 6.75 28.5H29.25C32.145 28.5 34.5 26.145 34.5 23.25V6.75C34.5 3.855 32.145 1.5 29.25 1.5H6.75ZM27.75 16.5H20.25C19.0095 16.5 18 15.4905 18 14.25V9.75C18 8.5095 19.0095 7.5 20.25 7.5H27.75C28.9905 7.5 30 8.5095 30 9.75V14.25C30 15.4905 28.9905 16.5 27.75 16.5ZM20.25 9C19.836 9 19.5 9.336 19.5 9.75V14.25C19.5 14.664 19.836 15 20.25 15H27.75C28.164 15 28.5 14.664 28.5 14.25V9.75C28.5 9.336 28.164 9 27.75 9H20.25ZM15 15.75C15 15.336 14.664 15 14.25 15H6.75C6.336 15 6 15.336 6 15.75C6 16.164 6.336 16.5 6.75 16.5H14.25C14.664 16.5 15 16.164 15 15.75ZM30 21.75C30 21.336 29.664 21 29.25 21H6.75C6.336 21 6 21.336 6 21.75C6 22.164 6.336 22.5 6.75 22.5H29.25C29.664 22.5 30 22.164 30 21.75Z"
                                fill="#2F2F2F"/>
                        </svg>
                        <div class="cardTitle">Edition des cartes</div>
                    </div>
                </a>
            @endif
            @if(in_array('GESPARAM', $droits_fpf))
                <a class="card" href="{{ route('admin.config') }}">
                    <div class="wrapper">
                        <svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M15.855 8.625L17.08 8.875L14.58 21.375L13.355 21.125L15.855 8.625ZM11.88 11.95L10.9925 11.0625L8.25498 13.8C7.52998 14.525 7.52998 15.7 8.25498 16.425L10.955 19.125L11.8425 18.2375L9.14248 15.5375C8.90498 15.3 8.90498 14.9125 9.14248 14.675L11.88 11.9375V11.95ZM19.4675 11.0125L18.58 11.9L21.305 14.625C21.5425 14.8625 21.5425 15.25 21.305 15.4875L18.58 18.2125L19.4675 19.1L22.1925 16.375C22.9175 15.65 22.9175 14.475 22.1925 13.75L19.4675 11.025V11.0125ZM26.205 17.375L29.955 19.6875L26.0175 26.075L22.2675 23.7625C21.2675 24.575 20.1675 25.1875 18.9675 25.6125V30H11.4675V25.6125C10.28 25.1875 9.16748 24.575 8.16748 23.7625L4.41748 26.075L0.47998 19.6875L4.22998 17.375C4.05498 16.575 3.96748 15.775 3.96748 15C3.96748 14.225 4.05498 13.425 4.22998 12.625L0.47998 10.3125L4.41748 3.925L8.16748 6.2375C9.16748 5.425 10.2675 4.8125 11.4675 4.3875V0H18.9675V4.3875C20.155 4.8125 21.2675 5.425 22.2675 6.2375L26.0175 3.925L29.955 10.3125L26.205 12.625C26.38 13.425 26.4675 14.225 26.4675 15C26.4675 15.775 26.38 16.575 26.205 17.375ZM24.7675 17.9625L24.88 17.5125C25.105 16.6625 25.2175 15.825 25.2175 15C25.2175 14.175 25.105 13.3375 24.88 12.4875L24.7675 12.0375L28.23 9.9L25.605 5.65L22.1425 7.7875L21.7925 7.4875C20.7175 6.55 19.4925 5.8625 18.155 5.45L17.7175 5.3125V1.25H12.7175V5.3L12.28 5.4375C10.9425 5.85 9.71748 6.5375 8.64248 7.475L8.29248 7.775L4.82998 5.6375L2.20498 9.9L5.66748 12.0375L5.55498 12.4875C5.32998 13.3375 5.21748 14.175 5.21748 15C5.21748 15.825 5.32998 16.6625 5.55498 17.5125L5.66748 17.9625L2.20498 20.1L4.82998 24.35L8.29248 22.2125L8.64248 22.5125C9.71748 23.45 10.9425 24.1375 12.28 24.55L12.7175 24.6875V28.7375H17.7175V24.6875L18.155 24.55C19.4925 24.1375 20.7175 23.45 21.7925 22.5125L22.1425 22.2125L25.605 24.35L28.23 20.1L24.7675 17.9625Z"
                                fill="#2F2F2F"/>
                        </svg>
                        <div class="cardTitle">Paramétrage</div>
                    </div>
                </a>
            @endif
            @if(in_array('GESDRO', $droits_fpf))
                <a class="card" href="{{ route('droits.index') }}">
                    <div class="wrapper">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <g clip-path="url(#clip0_201_42)">
                                <path
                                    d="M21.944 11.115L18.657 10.025C18.555 9.991 18.444 9.991 18.343 10.025L15.056 11.115C13.827 11.523 13 12.667 13 13.963V17.394C13 21.007 16.868 23.224 18.054 23.817L18.278 23.929C18.348 23.964 18.424 23.982 18.502 23.982C18.565 23.982 18.629 23.97 18.69 23.946L18.921 23.852C20.112 23.373 24.001 21.491 24.001 17.394V13.963C24.001 12.667 23.174 11.523 21.944 11.115ZM23 17.393C23 20.862 19.592 22.503 18.546 22.923L18.524 22.933L18.502 22.922C17.446 22.394 14.001 20.433 14.001 17.394V13.963C14.001 13.099 14.552 12.336 15.371 12.065L18.501 11.027L21.631 12.065C22.45 12.336 23.001 13.1 23.001 13.963V17.394L23 17.393ZM8 0C4.691 0 2 2.691 2 6C2 9.309 4.691 12 8 12C11.309 12 14 9.309 14 6C14 2.691 11.309 0 8 0ZM8 11C5.243 11 3 8.757 3 6C3 3.243 5.243 1 8 1C10.757 1 13 3.243 13 6C13 8.757 10.757 11 8 11ZM10.972 15.093C10.879 15.354 10.596 15.489 10.333 15.398C9.586 15.133 8.801 15 8 15C4.141 15 1 18.14 1 22V23.5C1 23.776 0.776 24 0.5 24C0.224 24 0 23.776 0 23.5V22C0 17.589 3.589 14 8 14C8.914 14 9.812 14.153 10.667 14.455C10.927 14.547 11.063 14.833 10.972 15.093Z"
                                    fill="#2F2F2F"/>
                            </g>
                            <defs>
                                <clipPath id="clip0_201_42">
                                    <rect width="24" height="24" fill="white"/>
                                </clipPath>
                            </defs>
                        </svg>
                        <div class="cardTitle">Gestion des droits</div>
                    </div>
                </a>
            @endif
            @if(in_array('GESPUB', $droits_fpf))
                <a class="card" href="{{ route('admin.gestion_publications') }}">
                    <div class="wrapper">
                        <svg width="30" height="28" viewBox="0 0 30 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M30 13.9363C30 13.3313 29.9025 12.7325 29.7112 12.1575L28.0375 7.13625C27.6113 5.85875 26.42 5 25.0725 5H21.2488V3.125C21.2488 1.4025 19.8462 0 18.1237 0H3.125C1.4025 0 0 1.4025 0 3.125V23.75C0 25.8175 1.6825 27.5 3.75 27.5C5.05375 27.5 6.2025 26.8313 6.875 25.82C7.5475 26.8313 8.69625 27.5 10 27.5C12.0675 27.5 13.75 25.8175 13.75 23.75V22.5H20V23.75C20 25.8175 21.6825 27.5 23.75 27.5C25.8175 27.5 27.5 25.8175 27.5 23.75V22.5H30V13.9363ZM25.0738 6.25C25.8813 6.25 26.5963 6.765 26.8525 7.5325L28.5075 12.5H21.25V6.25H25.0738ZM1.25 3.125C1.25 2.09125 2.09125 1.25 3.125 1.25H18.125C19.1588 1.25 20 2.09125 20 3.125V21.25H1.25V3.125ZM3.75 26.25C2.37125 26.25 1.25 25.1288 1.25 23.75V22.5H6.25V23.75C6.25 25.1288 5.12875 26.25 3.75 26.25ZM12.5 23.75C12.5 25.1288 11.3788 26.25 10 26.25C8.62125 26.25 7.5 25.1288 7.5 23.75V22.5H12.5V23.75ZM26.25 23.75C26.25 25.1288 25.1288 26.25 23.75 26.25C22.3712 26.25 21.25 25.1288 21.25 23.75V22.5H26.25V23.75ZM28.75 21.25H21.25V13.75H28.7462C28.7487 13.8125 28.75 13.8738 28.75 13.9363V21.25Z"
                                fill="#2F2F2F"/>
                        </svg>
                        <div class="cardTitle">Routage, éditions, Florilège</div>
                    </div>
                </a>
            @endif
            @if(in_array('GESFOR', $droits_fpf))
                <a class="card" href="{{ route('formations.admin_accueil') }}">
                    <div class="wrapper">
                        <svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M8.75 17.5C11.5125 17.5 13.75 15.2625 13.75 12.5C13.75 9.7375 11.5125 7.5 8.75 7.5C5.9875 7.5 3.75 9.7375 3.75 12.5C3.75 15.2625 5.9875 17.5 8.75 17.5ZM8.75 8.75C10.8125 8.75 12.5 10.4375 12.5 12.5C12.5 14.5625 10.8125 16.25 8.75 16.25C6.6875 16.25 5 14.5625 5 12.5C5 10.4375 6.6875 8.75 8.75 8.75ZM17.5 23.125V30H16.25V23.125C16.25 22.0875 15.4125 21.25 14.375 21.25H3.125C2.0875 21.25 1.25 22.0875 1.25 23.125V30H0V23.125C0 21.4 1.4 20 3.125 20H14.375C16.1 20 17.5 21.4 17.5 23.125ZM25 8.3875L18.125 15.2625L16.2 13.3375C16.225 13.0625 16.25 12.7875 16.25 12.5C16.25 12.175 16.225 11.8625 16.1875 11.5625L18.125 13.5L24.1125 7.5125H18.75V6.2625H24.375C25.4125 6.2625 26.25 7.1 26.25 8.1375V13.7625H25V8.3875ZM30 3.125V22.5H19.875C19.7875 22.0625 19.65 21.65 19.4625 21.25H28.75V3.125C28.75 2.0875 27.9125 1.25 26.875 1.25H9.375C8.3375 1.25 7.5 2.0875 7.5 3.125V5.1125C7.075 5.1875 6.65 5.3 6.25 5.4375V3.125C6.25 1.4 7.65 0 9.375 0H26.875C28.6 0 30 1.4 30 3.125Z"
                                fill="#2F2F2F"/>
                        </svg>
                        <div class="cardTitle">Formations</div>
                    </div>
                </a>
            @endif
            {{--            @if(in_array('VISUSTAT', $droits_fpf))--}}
            {{--                <a class="card">--}}
            {{--                    <div class="wrapper">--}}
            {{--                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">--}}
            {{--                            <g clip-path="url(#clip0_201_38)">--}}
            {{--                                <path d="M24 23.5C24 23.776 23.776 24 23.5 24H4.5C2.019 24 0 21.981 0 19.5V0.5C0 0.224 0.224 0 0.5 0C0.776 0 1 0.224 1 0.5V19.5C1 21.43 2.57 23 4.5 23H23.5C23.776 23 24 23.224 24 23.5ZM18.5 20C18.776 20 19 19.776 19 19.5V6.5C19 6.224 18.776 6 18.5 6C18.224 6 18 6.224 18 6.5V19.5C18 19.776 18.224 20 18.5 20ZM14.5 20C14.776 20 15 19.776 15 19.5V11.5C15 11.224 14.776 11 14.5 11C14.224 11 14 11.224 14 11.5V19.5C14 19.776 14.224 20 14.5 20ZM10.5 20C10.776 20 11 19.776 11 19.5V6.5C11 6.224 10.776 6 10.5 6C10.224 6 10 6.224 10 6.5V19.5C10 19.776 10.224 20 10.5 20ZM6.5 20C6.776 20 7 19.776 7 19.5V11.5C7 11.224 6.776 11 6.5 11C6.224 11 6 11.224 6 11.5V19.5C6 19.776 6.224 20 6.5 20Z" fill="#2F2F2F"/>--}}
            {{--                            </g>--}}
            {{--                            <defs>--}}
            {{--                                <clipPath id="clip0_201_38">--}}
            {{--                                    <rect width="24" height="24" fill="white"/>--}}
            {{--                                </clipPath>--}}
            {{--                            </defs>--}}
            {{--                        </svg>--}}
            {{--                        <div class="cardTitle">Statistiques</div>--}}
            {{--                    </div>--}}
            {{--                </a>--}}
            {{--            @endif--}}
            @if(in_array('GESTRE', $droits_fpf))
                <a class="card" href="{{ route('reversements.index') }}">
                    <div class="wrapper">
                        <svg width="28" height="30" viewBox="0 0 28 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M16.0368 6.58598C17.4243 5.84601 19.113 4.60856 19.8617 2.72614C20.1017 2.11866 20.0267 1.45369 19.6517 0.903712C19.2679 0.337486 18.6342 0 17.9543 0H9.54335C8.86338 0 8.22841 0.337486 7.84592 0.903712C7.47094 1.45369 7.39594 2.11866 7.63593 2.72739C8.38465 4.60856 10.0733 5.84601 11.4608 6.58598C5.29728 8.27591 0 16.3681 0 22.8128C0 26.7751 3.22362 30 7.1872 30H20.3117C24.2752 30 27.4989 26.7751 27.4989 22.8128C27.4989 16.3681 22.2016 8.27591 16.0368 6.58598ZM8.79838 2.26616C8.71339 2.04991 8.74214 1.80867 8.87963 1.60618C9.03087 1.38369 9.27836 1.24995 9.54335 1.24995H17.9543C18.2192 1.24995 18.4667 1.38244 18.618 1.60618C18.7555 1.80867 18.7855 2.04866 18.6992 2.26491C17.7143 4.7423 14.5294 5.95475 13.7482 6.21974C12.967 5.95475 9.78334 4.7423 8.79838 2.26616ZM20.3117 28.7501H7.1872C3.91359 28.7501 1.24995 26.0864 1.24995 22.8128C1.24995 16.1268 7.3122 7.50094 13.7494 7.50094C20.1867 7.50094 26.2489 16.1268 26.2489 22.8128C26.2489 26.0864 23.5853 28.7501 20.3117 28.7501Z"
                                fill="#2F2F2F"/>
                        </svg>
                        <div class="cardTitle">Reversements</div>
                    </div>
                </a>
            @endif
            @if(in_array('GESTRE', $droits_fpf))
                <a class="card" href="{{ route('admin.factures') }}">
                    <div class="wrapper">
                        <svg width="25" height="30" viewBox="0 0 25 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M5 23.75H20V16.25H5V23.75ZM6.25 17.5H18.75V22.5H6.25V17.5ZM11.25 12.5H5V11.25H11.25V12.5ZM11.25 7.5H5V6.25H11.25V7.5ZM15.8838 0H3.125C1.4025 0 0 1.4025 0 3.125V30H25V9.11625L15.8838 0ZM16.25 2.13375L22.8663 8.75H16.25V2.13375ZM1.25 28.75V3.125C1.25 2.09125 2.09125 1.25 3.125 1.25H15V10H23.75V28.75H1.25Z"
                                fill="#2F2F2F"/>
                        </svg>
                        <div class="cardTitle">Factures émises</div>
                    </div>
                </a>
            @endif
            @if(in_array('GESVOT', $droits_fpf))
                <a class="card" href="{{ route('votes.index') }}">
                    <div class="wrapper">
                        <svg width="30" height="25" viewBox="0 0 30 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M25.625 12.5H25V4.375C25 1.9625 23.0375 0 20.625 0H9.375C6.9625 0 5 1.9625 5 4.375V12.5H4.375C1.9625 12.5 0 14.4625 0 16.875V20.625C0 23.0375 1.9625 25 4.375 25H25.625C28.0375 25 30 23.0375 30 20.625V16.875C30 14.4625 28.0375 12.5 25.625 12.5ZM6.25 4.375C6.25 2.65 7.65 1.25 9.375 1.25H20.625C22.35 1.25 23.75 2.65 23.75 4.375V18.75H6.25V4.375ZM28.75 20.625C28.75 22.35 27.35 23.75 25.625 23.75H4.375C2.65 23.75 1.25 22.35 1.25 20.625V16.875C1.25 15.15 2.65 13.75 4.375 13.75H5V19.375C5 19.725 5.275 20 5.625 20H24.375C24.725 20 25 19.725 25 19.375V13.75H25.625C27.35 13.75 28.75 15.15 28.75 16.875V20.625ZM10.25 11C10 10.75 10 10.3625 10.25 10.1125C10.5 9.8625 10.8875 9.8625 11.1375 10.1125L13.1625 12.1375C13.65 12.625 14.45 12.625 14.925 12.1375L19.4625 7.6C19.7125 7.35 20.1 7.35 20.35 7.6C20.6 7.85 20.6 8.2375 20.35 8.4875L15.8125 13.025C15.3375 13.5 14.7125 13.7625 14.05 13.7625C13.3875 13.7625 12.75 13.5 12.2875 13.025L10.2625 11H10.25Z"
                                fill="#2F2F2F"/>
                        </svg>
                        <div class="cardTitle">Votes</div>
                    </div>
                </a>
            @endif
            @if(in_array('GESNEW', $droits_fpf) || in_array('GESNEWCA', $droits_fpf) || in_array('GESNEWBU', $droits_fpf))
                <a class="card" id="connectNewsletter">
                    <div class="wrapper">
                        <svg width="30" height="27" viewBox="0 0 30 27" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M26.875 0H6.875C5.15 0 3.75 1.4 3.75 3.125V22.5H30V3.125C30 1.4 28.6 0 26.875 0ZM6.875 1.25H26.875C27.7375 1.25 28.475 1.8375 28.6875 2.65L20.1125 11.225C18.4625 12.875 15.575 12.875 13.925 11.225L5.1375 2.425C5.4125 1.7375 6.0875 1.25 6.875 1.25ZM5 21.25V4.0625L13.05 12.1C14.1125 13.1625 15.525 13.75 17.025 13.75C18.525 13.75 19.9375 13.1625 21 12.1L28.75 4.35V21.25H5ZM1.25 25H23.75V26.25H0V6.875C0 5.85 0.4875 4.95 1.25 4.375V25Z"
                                fill="#2F2F2F"/>
                        </svg>
                        <div class="cardTitle">Newsletters</div>
                    </div>
                </a>
            @endif
            @if(in_array('SUPPORT', $droits_fpf))
                <a class="card" href="{{ route('supports.index') }}">
                    <div class="wrapper">
                        <svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M15.0008 29.7465C6.86929 29.7465 0.25354 23.1315 0.25354 15C0.25354 6.86854 6.86929 0.25354 15.0008 0.25354C23.1315 0.25354 29.7465 6.86854 29.7465 15C29.7465 23.1315 23.1315 29.7465 15.0008 29.7465ZM15.0008 1.00354C7.28254 1.00354 1.00354 7.28254 1.00354 15C1.00354 22.7175 7.28254 28.9965 15.0008 28.9965C22.7183 28.9965 28.9965 22.7175 28.9965 15C28.9965 7.28254 22.7183 1.00354 15.0008 1.00354Z"
                                fill="black"/>
                            <path
                                d="M15 21.1875C11.5883 21.1875 8.8125 18.4117 8.8125 15C8.8125 11.5883 11.5883 8.8125 15 8.8125C18.4117 8.8125 21.1875 11.5883 21.1875 15C21.1875 18.4117 18.4117 21.1875 15 21.1875ZM15 9.5625C12.0015 9.5625 9.5625 12.0015 9.5625 15C9.5625 17.9985 12.0015 20.4375 15 20.4375C17.9985 20.4375 20.4375 17.9985 20.4375 15C20.4375 12.0015 17.9985 9.5625 15 9.5625Z"
                                fill="black"/>
                            <path
                                d="M29.22 16.8863H20.5628C20.4633 16.8863 20.3679 16.8468 20.2976 16.7764C20.2273 16.7061 20.1878 16.6107 20.1878 16.5113C20.1878 16.4118 20.2273 16.3164 20.2976 16.2461C20.3679 16.1758 20.4633 16.1363 20.5628 16.1363H29.22C29.3195 16.1363 29.4149 16.1758 29.4852 16.2461C29.5555 16.3164 29.595 16.4118 29.595 16.5113C29.595 16.6107 29.5555 16.7061 29.4852 16.7764C29.4149 16.8468 29.3195 16.8863 29.22 16.8863ZM29.22 13.8638H20.8125C20.7131 13.8638 20.6177 13.8243 20.5474 13.7539C20.477 13.6836 20.4375 13.5882 20.4375 13.4888C20.4375 13.3893 20.477 13.2939 20.5474 13.2236C20.6177 13.1533 20.7131 13.1138 20.8125 13.1138H29.22C29.3195 13.1138 29.4149 13.1533 29.4852 13.2236C29.5555 13.2939 29.595 13.3893 29.595 13.4888C29.595 13.5882 29.5555 13.6836 29.4852 13.7539C29.4149 13.8243 29.3195 13.8638 29.22 13.8638ZM9.18753 13.8638H0.780029C0.680573 13.8638 0.58519 13.8243 0.514864 13.7539C0.444538 13.6836 0.405029 13.5882 0.405029 13.4888C0.405029 13.3893 0.444538 13.2939 0.514864 13.2236C0.58519 13.1533 0.680573 13.1138 0.780029 13.1138H9.18753C9.28699 13.1138 9.38237 13.1533 9.45269 13.2236C9.52302 13.2939 9.56253 13.3893 9.56253 13.4888C9.56253 13.5882 9.52302 13.6836 9.45269 13.7539C9.38237 13.8243 9.28699 13.8638 9.18753 13.8638ZM9.33678 16.8863H0.780029C0.680573 16.8863 0.58519 16.8468 0.514864 16.7764C0.444538 16.7061 0.405029 16.6107 0.405029 16.5113C0.405029 16.4118 0.444538 16.3164 0.514864 16.2461C0.58519 16.1758 0.680573 16.1363 0.780029 16.1363H9.33678C9.43624 16.1363 9.53162 16.1758 9.60194 16.2461C9.67227 16.3164 9.71178 16.4118 9.71178 16.5113C9.71178 16.6107 9.67227 16.7061 9.60194 16.7764C9.53162 16.8468 9.43624 16.8863 9.33678 16.8863ZM16.5113 9.68253C16.4118 9.68253 16.3164 9.64302 16.2461 9.57269C16.1758 9.50237 16.1363 9.40699 16.1363 9.30753V0.780029C16.1363 0.680573 16.1758 0.58519 16.2461 0.514864C16.3164 0.444538 16.4118 0.405029 16.5113 0.405029C16.6107 0.405029 16.7061 0.444538 16.7764 0.514864C16.8468 0.58519 16.8863 0.680573 16.8863 0.780029V9.30753C16.8863 9.40699 16.8468 9.50237 16.7764 9.57269C16.7061 9.64302 16.6107 9.68253 16.5113 9.68253ZM13.4888 9.56253C13.3893 9.56253 13.2939 9.52302 13.2236 9.45269C13.1533 9.38237 13.1138 9.28699 13.1138 9.18753V0.780029C13.1138 0.680573 13.1533 0.58519 13.2236 0.514864C13.2939 0.444538 13.3893 0.405029 13.4888 0.405029C13.5882 0.405029 13.6836 0.444538 13.7539 0.514864C13.8243 0.58519 13.8638 0.680573 13.8638 0.780029V9.18753C13.8638 9.28699 13.8243 9.38237 13.7539 9.45269C13.6836 9.52302 13.5882 9.56253 13.4888 9.56253ZM13.4888 29.595C13.3893 29.595 13.2939 29.5555 13.2236 29.4852C13.1533 29.4149 13.1138 29.3195 13.1138 29.22V20.8125C13.1138 20.7131 13.1533 20.6177 13.2236 20.5474C13.2939 20.477 13.3893 20.4375 13.4888 20.4375C13.5882 20.4375 13.6836 20.477 13.7539 20.5474C13.8243 20.6177 13.8638 20.7131 13.8638 20.8125V29.22C13.8638 29.3195 13.8243 29.4149 13.7539 29.4852C13.6836 29.5555 13.5882 29.595 13.4888 29.595ZM16.5113 29.595C16.4118 29.595 16.3164 29.5555 16.2461 29.4852C16.1758 29.4149 16.1363 29.3195 16.1363 29.22V20.6633C16.1363 20.5638 16.1758 20.4684 16.2461 20.3981C16.3164 20.3278 16.4118 20.2883 16.5113 20.2883C16.6107 20.2883 16.7061 20.3278 16.7764 20.3981C16.8468 20.4684 16.8863 20.5638 16.8863 20.6633V29.22C16.8863 29.3195 16.8468 29.4149 16.7764 29.4852C16.7061 29.5555 16.6107 29.595 16.5113 29.595Z"
                                fill="black"/>
                        </svg>
                        <div class="cardTitle">Supports</div>
                    </div>
                </a>
            @endif
            <a class="card" href="{{ route('admin.informations') }}">
                <div class="wrapper">
                    <svg width="30" height="30" viewBox="0 0 30 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M16.2498 8.74982C16.2498 9.43982 15.6898 9.99982 14.9998 9.99982C14.3098 9.99982 13.7498 9.43982 13.7498 8.74982C13.7498 8.05982 14.3098 7.49982 14.9998 7.49982C15.6898 7.49982 16.2498 8.05982 16.2498 8.74982ZM29.9998 24.3748V15.4436C29.9998 7.33607 23.871 0.567322 16.0473 0.0348219C11.711 -0.260178 7.45227 1.32982 4.38602 4.39982C1.32102 7.47107 -0.263984 11.7223 0.0360158 16.0673C0.587266 24.0098 7.62852 29.9998 16.416 29.9998H24.3748C27.476 29.9998 29.9998 27.4761 29.9998 24.3748ZM15.9623 1.28232C23.1335 1.76982 28.7498 7.98982 28.7498 15.4436V24.3748C28.7498 26.7873 26.7873 28.7498 24.3748 28.7498H16.416C8.16102 28.7498 1.79727 23.3798 1.28477 15.9823C1.00852 11.9973 2.46102 8.09732 5.27102 5.28357C7.85852 2.69232 11.3685 1.24982 15.021 1.24982C15.3335 1.24982 15.6485 1.25982 15.9623 1.28107V1.28232ZM16.2498 23.1248V14.3748C16.2498 13.3411 15.4085 12.4998 14.3748 12.4998H13.1248C12.7798 12.4998 12.4998 12.7798 12.4998 13.1248C12.4998 13.4698 12.7798 13.7498 13.1248 13.7498H14.3748C14.7185 13.7498 14.9998 14.0311 14.9998 14.3748V23.1248C14.9998 23.4698 15.2798 23.7498 15.6248 23.7498C15.9698 23.7498 16.2498 23.4698 16.2498 23.1248Z"
                            fill="#2F2F2F"/>
                    </svg>
                    <div class="cardTitle">Informations</div>
                </div>
            </a>
            <div class="card invisible">
            </div>
            <div class="card invisible">
            </div>
        </div>
    </div>
    <div class="modalEdit d-none" id="modalAffectation">
        <div class="modalEditHeader">
            <div class="modalEditTitle">Affectation UR</div>
            <div class="modalEditClose">
                X
            </div>
        </div>
        <div class="modalEditBody">
            Voulez-vous vraiment affecter cet utilisateur à l'UR <span id="urAffectation"></span> ?
        </div>
        <div class="modalEditFooter">
            <div class="adminDanger btnMedium mr10 modalEditClose">Annuler</div>
            <div class="adminPrimary btnMedium mr10" id="confirmAffectationUr" data-identifiant="" data-ur="">Valider
            </div>
        </div>
    </div>
@endsection
@section('css')
    <link href="{{ asset('css/admin_fpf.css') }}" rel="stylesheet">
@endsection
