<!DOCTYPE html>
<html prefix="og: https://ogp.me/ns#" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    {{--    <link href="{{ asset('css/mail.css') }}?t=<?= time() ?>" rel="stylesheet">--}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">
</head>
<style>
    .mail .authLogo img {
        object-fit: contain;
        height: 60px;
    }
    .mail .coords {
        font-size: small;
    }
    .mail .coords a {
        color: inherit;
    }
    .mail .header .topTitle {
        font-size: small;
        max-width: 130px;
        text-align: center;
        font-weight: 600;
        color: #3c3c3c;
        margin: auto;
    }
    /*    style other mails*/
    .mail .mailContent{
        padding: 10px;
        margin: 20px 0;
    }
    /*.mail .mailContent .text{*/
    /*    font-size: 16px;*/
    /*}*/
    .mail  .mailContent .link{
        font-weight: 600;
    }
    /*.mail .mailContent .notWorking{*/
    /*    color: #9a9a9a;*/
    /*    font-size: 14px;*/
    /*    margin-top: 15px;*/
    /*}*/
    .mail .im{
        color: black!important;
    }
</style>
<body class="mail" style=" box-sizing: border-box;
        margin: 0;
        padding: 0;
        font-family: 'Poppins', sans-serif;
        max-width: 100%;
        /*overflow: scroll;*/
        max-width: min(750px, 100%);
        margin: auto;">

<main>
    @include('layouts.headerEmail')
    <div class="mainContent" style="      display: flex;
        justify-content: center;
        align-items: center;
        width: 100%;
        padding: 20px 0;">
        @yield('content')
    </div>
    <div style="display: flex; align-items: center">
        <div style="margin-right: 20px; height: 82px;">
            <img src="{{ env('APP_URL').'storage/app/public/logo-fpf-mail.jpg' }}" style="object-fit: contain;height: 100%"  alt="logo FPF">
        </div>
        <div>
<div style="font-weight: bold; font-size: 16px;margin-bottom: 15px">Fédération Photographique de France</div>
            <div style="margin-bottom: 10px">La passion des images partagées</div>
            <div style="margin-bottom: 10px">5, rue Jules Vallès · 75011 Paris</div>
            <div style="display: flex; color: grey">
                <span style="padding-right:5px">Mob. :</span>
                <a style="color: inherit; text-decoration: underline;cursor: pointer" href="tel:+33143713040"> +33 (0) 1 43 71 30 40</a>
                <span style="padding:0 5px;"> . </span>
                <a style="color: inherit; text-decoration: underline;cursor: pointer" href="mailto:fpf@federation-photo.fr"> fpf@federation-photo.fr </a>
                <span style="padding:0 5px;"> . </span>
                <a style="color: inherit; text-decoration: underline;cursor: pointer" href="https://federation-photo.fr"> www.federation-photo.fr </a>
            </div>

        </div>
    </div>

    @include('layouts.footerEmail')

</main>

</body>
</html>
