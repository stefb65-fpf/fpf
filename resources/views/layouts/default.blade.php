<!DOCTYPE html>
<html prefix="og: https://ogp.me/ns#" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    @yield('seo')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link href="{{ asset('css/frontend.css') }}?t=<?= time() ?>" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    @yield('css')
    @yield('headjs')

    @viteReactRefresh
    @vite('resources/js/index.jsx')
</head>
<body >
@include('layouts.header')
<div class="hamburger">
    <div class="wrapper">
        <div class="line"></div>
        <div class="line"></div>
        <div class="line"></div>
    </div>
</div>
<div class="mainContainer">
    @yield('content')
    @include('layouts.footer')
    <div id="hello-react"></div>
</div>
@yield('js')
<script type="text/javascript" src="{{ asset('js/vanilla-tilt.min.js') }}"></script>
<script src="https://kit.fontawesome.com/78d4dad418.js" crossorigin="anonymous"></script>
<script src="{{ asset('js/frontend.js') }}"></script>
</body>
</html>
