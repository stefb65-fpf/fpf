<div class="topbar">
    <a href="{{ env('APP_URL') }}">
        <div class="logoMobile">
            <img src="{{ env('APP_URL').'storage/app/public/FPF-100-Logo-Seul.webp' }}"
                 alt="Fédération Photographique de France">
            <div class="logoMobileRight">Fédération Photographique de France</div>
        </div>
    </a>

    @if($menu['admin'] || $menu['ur'])
        <div class="actionsTopbar">
            <div class="searchContainer">
                <div class="foster"> Je cherche ...</div>
                <div class="searchBoxContainer">
                    <div class="searchItem">
                        <div class="icon" name="searchBtn">
                            <svg width="34" height="34" viewBox="0 0 34 34" fill="none"
                                 xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M17 17C14.7083 17 12.7465 16.184 11.1146 14.5521C9.48264 12.9201 8.66667 10.9583 8.66667 8.66666C8.66667 6.37499 9.48264 4.41319 11.1146 2.78125C12.7465 1.1493 14.7083 0.333328 17 0.333328C19.2917 0.333328 21.2535 1.1493 22.8854 2.78125C24.5174 4.41319 25.3333 6.37499 25.3333 8.66666C25.3333 10.9583 24.5174 12.9201 22.8854 14.5521C21.2535 16.184 19.2917 17 17 17ZM0.333332 33.6667V27.8333C0.333332 26.6528 0.637499 25.5674 1.24583 24.5771C1.85417 23.5868 2.66111 22.8319 3.66667 22.3125C5.81944 21.2361 8.00694 20.4285 10.2292 19.8896C12.4514 19.3507 14.7083 19.0819 17 19.0833C19.2917 19.0833 21.5486 19.3528 23.7708 19.8917C25.9931 20.4305 28.1806 21.2375 30.3333 22.3125C31.3403 22.8333 32.1479 23.5889 32.7563 24.5792C33.3646 25.5694 33.6681 26.6542 33.6667 27.8333V33.6667H0.333332Z"
                                    fill="#2F2F2F"/>
                            </svg>
                            <div class="label">Une personne</div>
                        </div>
                        <div class="searchBox person">
                            <input type="text" placeholder="identifiant , email ou nom">
                        </div>

                    </div>
                    <div class="searchItem">
                        <div class="icon" name="searchBtn">
                            <svg width="54" height="50" viewBox="0 0 54 50" fill="none"
                                 xmlns="http://www.w3.org/2000/svg">
                                <circle cx="26.8084" cy="3.68936" r="3.68936" fill="#2F2F2F"/>
                                <path
                                    d="M18.7786 9.69362C18.8943 7.72596 22.5403 6.14894 22.5403 6.14894L23.4807 6.94469C26.4901 9.25958 29.6296 7.74043 31.2211 5.42554C35.5037 7.33533 37.0807 13.9617 35.7062 17.3617C35.3204 18.1092 34.5488 18.3745 33.6807 17.3617C33.2467 15.7702 33.6807 13.8894 32.3786 12.2979H24.4211C23.0467 10.634 20.8764 10.0553 18.7786 9.69362Z"
                                    fill="#2F2F2F"/>
                                <circle cx="45.4277" cy="14.3804" r="3.68936" transform="rotate(60 45.4277 14.3804)"
                                        fill="#2F2F2F"/>
                                <path
                                    d="M36.2129 10.4286C37.9748 9.54498 41.1636 11.914 41.1636 11.914L40.9446 13.1263C40.4446 16.8899 43.33 18.8493 46.1305 19.0701C46.6178 23.7338 41.6677 28.4127 38.036 28.9224C37.1957 28.962 36.5802 28.4264 37.0232 27.1682C38.1845 25.9966 40.0304 25.4321 40.7576 23.5086L36.7788 16.6173C37.5325 14.5951 36.9486 12.4262 36.2129 10.4286Z"
                                    fill="#2F2F2F"/>
                                <circle cx="45.5782" cy="35.6795" r="3.68936" transform="rotate(120 45.5782 35.6795)"
                                        fill="#2F2F2F"/>
                                <path
                                    d="M44.3933 25.7234C46.0394 26.8075 45.5822 30.7535 45.5822 30.7535L44.4229 31.17C40.9134 32.6188 40.6592 36.0973 41.8683 38.633C38.0731 41.3869 31.5459 39.4395 29.2887 36.5491C28.8342 35.8413 28.9903 35.0404 30.3014 34.795C31.8967 35.2148 33.3086 36.5311 35.3379 36.1992L39.3166 29.3079C41.4448 28.9495 43.0311 27.3594 44.3933 25.7234Z"
                                    fill="#2F2F2F"/>
                                <circle cx="26.5682" cy="46.3106" r="3.68936" transform="rotate(-180 26.5682 46.3106)"
                                        fill="#2F2F2F"/>
                                <path
                                    d="M34.5979 40.3064C34.4822 42.274 30.8362 43.8511 30.8362 43.8511L29.8958 43.0553C26.8865 40.7404 23.7469 42.2596 22.1554 44.5745C17.8728 42.6647 16.2958 36.0383 17.6703 32.6383C18.0561 31.8908 18.8277 31.6255 19.6958 32.6383C20.1299 34.2298 19.6958 36.1106 20.9979 37.7021L28.9554 37.7021C30.3299 39.3659 32.5001 39.9447 34.5979 40.3064Z"
                                    fill="#2F2F2F"/>
                                <circle cx="7.88563" cy="35.1879" r="3.68936" transform="rotate(-120 7.88563 35.1879)"
                                        fill="#2F2F2F"/>
                                <path
                                    d="M17.1004 39.1398C15.3384 40.0234 12.1497 37.6544 12.1497 37.6544L12.3686 36.4421C12.8687 32.6785 9.98331 30.7191 7.1828 30.4983C6.69545 25.8346 11.6456 21.1556 15.2773 20.646C16.1176 20.6063 16.7331 21.1419 16.29 22.4001C15.1288 23.5718 13.2829 24.1363 12.5557 26.0597L16.5344 32.9511C15.7807 34.9733 16.3647 37.1421 17.1004 39.1398Z"
                                    fill="#2F2F2F"/>
                                <circle cx="7.4694" cy="14.1647" r="3.68936" transform="rotate(-60 7.4694 14.1647)"
                                        fill="#2F2F2F"/>
                                <path
                                    d="M8.65433 24.1208C7.00816 23.0367 7.4654 19.0907 7.4654 19.0907L8.62475 18.6742C12.1342 17.2254 12.3884 13.7469 11.1793 11.2112C14.9745 8.45729 21.5017 10.4047 23.7589 13.2951C24.2134 14.003 24.0573 14.8038 22.7462 15.0492C21.1509 14.6294 19.739 13.3131 17.7097 13.645L13.731 20.5363C11.6028 20.8948 10.0165 22.4849 8.65433 24.1208Z"
                                    fill="#2F2F2F"/>
                            </svg>
                            <div class="label">Un club</div>
                        </div>
                        <div class="searchBox club">
                            <input type="text" placeholder="identifiant ou nom">
                        </div>
                    </div>

                    @if($menu['admin'])
                        <div class="searchItem" data-target="3">
                            <div class="icon" name="searchBtn">
                                <svg width="40" height="48" viewBox="0 0 40 48" fill="none"
                                     xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M37.8571 43.9643C35.6022 44.9384 33.1706 45.4369 30.7143 45.4286C25.2727 45.1782 20.1516 42.7835 16.4707 38.7679C12.7898 34.7524 10.8485 29.4428 11.0714 24C10.8485 18.5572 12.7898 13.2476 16.4707 9.23204C20.1516 5.21651 25.2727 2.82177 30.7143 2.57143C32.6954 2.56033 34.664 2.88642 36.5357 3.53571M2.14286 18.6429H23.5714M2.14286 29.3571H23.5714"
                                        stroke="#2F2F2F" stroke-width="4" stroke-linecap="round"
                                        stroke-linejoin="round"/>
                                </svg>
                                <div class="label">Un règlement</div>
                            </div>
                            <div class="searchBox payment">
                                <input type="text" placeholder="identifiant ou nom adh ou num club">
                            </div>
                        </div>

                        <div class="searchItem" data-target="3">
                            <div class="icon" name="searchBtn">
                                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="currentColor" class="bi bi-file-earmark-pdf" viewBox="0 0 16 16">
                                    <path d="M14 14V4.5L9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2M9.5 3A1.5 1.5 0 0 0 11 4.5h2V14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h5.5z"/>
                                    <path d="M4.603 14.087a.8.8 0 0 1-.438-.42c-.195-.388-.13-.776.08-1.102.198-.307.526-.568.897-.787a7.7 7.7 0 0 1 1.482-.645 20 20 0 0 0 1.062-2.227 7.3 7.3 0 0 1-.43-1.295c-.086-.4-.119-.796-.046-1.136.075-.354.274-.672.65-.823.192-.077.4-.12.602-.077a.7.7 0 0 1 .477.365c.088.164.12.356.127.538.007.188-.012.396-.047.614-.084.51-.27 1.134-.52 1.794a11 11 0 0 0 .98 1.686 5.8 5.8 0 0 1 1.334.05c.364.066.734.195.96.465.12.144.193.32.2.518.007.192-.047.382-.138.563a1.04 1.04 0 0 1-.354.416.86.86 0 0 1-.51.138c-.331-.014-.654-.196-.933-.417a5.7 5.7 0 0 1-.911-.95 11.7 11.7 0 0 0-1.997.406 11.3 11.3 0 0 1-1.02 1.51c-.292.35-.609.656-.927.787a.8.8 0 0 1-.58.029m1.379-1.901q-.25.115-.459.238c-.328.194-.541.383-.647.547-.094.145-.096.25-.04.361q.016.032.026.044l.035-.012c.137-.056.355-.235.635-.572a8 8 0 0 0 .45-.606m1.64-1.33a13 13 0 0 1 1.01-.193 12 12 0 0 1-.51-.858 21 21 0 0 1-.5 1.05zm2.446.45q.226.245.435.41c.24.19.407.253.498.256a.1.1 0 0 0 .07-.015.3.3 0 0 0 .094-.125.44.44 0 0 0 .059-.2.1.1 0 0 0-.026-.063c-.052-.062-.2-.152-.518-.209a4 4 0 0 0-.612-.053zM8.078 7.8a7 7 0 0 0 .2-.828q.046-.282.038-.465a.6.6 0 0 0-.032-.198.5.5 0 0 0-.145.04c-.087.035-.158.106-.196.283-.04.192-.03.469.046.822q.036.167.09.346z"/>
                                </svg>
                                <div class="label">Une facture</div>
                            </div>
                            <div class="searchBox invoice">
                                <input type="text" placeholder="identifiant ou nom adh ou num club">
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif

    <div class="userAccount dropdownParent dropdownCall" id="dropdownHeader">
        {{--    <div class="userAccount dropdownParent dropdownCall"  data-dropdown-id="13">--}}
        {{ $user->prenom }}
        <div class="useAccountIcon">
            <svg width="13" height="9" viewBox="0 0 13 9" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M6.5 9L0.870834 0.75L12.1292 0.75L6.5 9Z" fill="#003d77"/>
            </svg>
        </div>
        <div class="dropdown">
            <ul>
                <li>
                    <a name="linkDropdownHeader" href="/">Mon Compte</a>
                </li>
                <li>
                    <a id="connectConcours">Outil concours</a>
                </li>
                <li>
                    <a name="linkDropdownHeader" href="https://federation-photo.atlassian.net/servicedesk/customer/portals" target="_blank">Support</a>
{{--                    <a name="linkDropdownHeader" href="/support">Support</a>--}}
                </li>
                <li>
                    <a name="linkDropdownHeader" href="/logout">Se déconnecter</a>
                </li>
            </ul>
        </div>
    </div>
</div>
{{--@section('js')--}}
{{--    <script src="{{ asset('js/topbar_searchbox.js') }}?t=<?= time() ?>"></script>--}}
{{--@endsection--}}
