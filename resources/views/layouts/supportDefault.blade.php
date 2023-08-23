<!DOCTYPE html>
<html prefix="og: https://ogp.me/ns#" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="{{ asset('css/frontend.css') }}?t=<?= time() ?>" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
@yield('css')
@yield('headjs')
<!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-615483TZKY"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', 'G-615483TZKY');
    </script>

</head>
<body >
@if($user)
@include('layouts.header')
@include('layouts.menu')
@include('layouts.modal')
<div class="mainContainer">
    @include('layouts.flash')
        <div class="supportContainer pageCanva">

            <h1 class="pageTitle">
                Demande de support
                <a class="previousPage" title="Retour page précédente" href="{{ url()->previous() == "https://fpf-new.federation-photo.fr/support" ? "https://fpf-new.federation-photo.fr/mes-mails":url()->previous()}}">

                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor"
                         class="bi bi-reply-fill" viewBox="0 0 16 16">
                        <path
                            d="M5.921 11.9 1.353 8.62a.719.719 0 0 1 0-1.238L5.921 4.1A.716.716 0 0 1 7 4.719V6c1.5 0 6 0 7 8-2.5-4.5-7-4-7-4v1.281c0 .56-.606.898-1.079.62z"/>
                    </svg>
                </a>
            </h1>

    @yield('content')
        </div>
    <div class="infoSuccess" id="infoSuccess"></div>
    @include('layouts.footer')
</div>
@else
    @include('layouts.headerLogin')
    <div class="mainContainer fullWidth fullHeight ">
        @include('layouts.flash')
        <div class="supportContainer pageCanva">
            <h1 class="pageTitle">
                Demande de support
                <a class="previousPage" title="Retour page précédente" href="{{ url()->previous() == "https://fpf-new.federation-photo.fr/support" ? "https://fpf-new.federation-photo.fr/login":url()->previous()}}">
                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor"
                         class="bi bi-reply-fill" viewBox="0 0 16 16">
                        <path
                            d="M5.921 11.9 1.353 8.62a.719.719 0 0 1 0-1.238L5.921 4.1A.716.716 0 0 1 7 4.719V6c1.5 0 6 0 7 8-2.5-4.5-7-4-7-4v1.281c0 .56-.606.898-1.079.62z"/>
                    </svg>
                </a>
            </h1>
            @yield('content')
        </div>
    </div>
    @include('layouts.footer')
@endif
<span id="app_url" class="d-none">{{ env('APP_URL') }}</span>
<script src="https://code.jquery.com/jquery-3.7.0.min.js" integrity="sha256-2Pmvv0kuTBOenSvLm6bvfBSSHrUJ+3A7x6P5Ebd07/g=" crossorigin="anonymous"></script>
<script src="{{ asset('js/frontend.js') }}?t=<?= time() ?>"></script>
<script src="{{ asset('js/topbar_searchbox.js') }}?t=<?= time() ?>"></script>
<script src="{{ asset('js/laravel.js') }}"></script>
@yield('js')
</body>
</html>
