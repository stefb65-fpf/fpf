<!DOCTYPE html>
<html prefix="og: https://ogp.me/ns#" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <style>
        @page { margin-top: 10px; margin-bottom: 0px; }
    </style>
</head>
<body class="m0 p0 bgTransparent">
    @yield('content')
</body>
</html>
