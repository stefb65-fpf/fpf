<nav class="navbar hidden">
    <div class="top">
        <div class="logo"><img src=".../../storage/app/public/fpf-logo.png" alt=""></div>
    </div>
    <div class="bottom">
        <div class="featuresWrapper">
            <div class="name">
                <div class="welcome">Bienvenue <span>Julie Hochet</span> !</div>
                <div class="idManagement">
                    <svg width="13" height="9" viewBox="0 0 13 9" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M6.5 9L0.870834 0.75L12.1292 0.75L6.5 9Z" fill="#6D6D6D"/>
                    </svg>
                    <span>Se déconnecter</span>
                </div>
            </div>
            <div class="card" id="navMemberFeatures">


            </div>
        </div>

        <ul class="menu" id="navMenu">

            <li class="menuItem{{ Request::is('mon-profil')?" active":""}}">
                <a href="mon-profil">
                    <div class="title">
                        Mon Profil
                    </div>
                </a>
            </li>

            <li class="menuItem{{ Request::is('formations')?" active":""}}">
                <a href="formations">
                    <div class="title">
                        formations
                    </div>
                </a>
            </li>
            <li class="menuItem{{ Request::is('gestion-club')?" active":""}}">
                <a href="gestion-club">
                    <div class="title">
                        Gestion club
                    </div>
                </a>
            </li>
            <li class="menuItem{{ Request::is('gestion-ur')?" active":""}}">
                <a href="gestion-ur">
                    <div class="title">
                        Gestion UR
                    </div>
                </a>
            </li>
            <li class="menuItem{{ Request::is('gestion-fpf')?" active":""}}">
                <a href="gestion-fpf">
                    <div class="title">
                        Gestion FPF
                    </div>
                </a>
            </li>
        </ul>

    </div>

</nav>
