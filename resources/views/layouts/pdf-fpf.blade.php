<!DOCTYPE html>
<html prefix="og: https://ogp.me/ns#" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <style>
        @page { margin: 100px 25px; }
        header { position: fixed; top: -100px; left: 0px; right: 0px; color: #003d77; height: 90px; font-size:13px; }
        footer { position: fixed; bottom: -50px; left: 0px; right: 0px; height: 40px; width: 100%; text-align: center; font-size:12px; }
        p { page-break-after: always; }
        p:last-child { page-break-after: never; }
    </style>
</head>
<body style="background: transparent">
    <header>
        <table>
            <tr>
                <td>
                    <img src="{{ env('APP_URL').'storage/app/public/fpf-logo.png' }}"
                         alt="Fédération Photographique de France" width="80px" height="80px">
                </td>
                <td>
                    Fédération Photographique de France<br>
                    5, rue Jules-Vallès - 75011 PARIS<br>
                    Tél. 01 43 71 30 40 - Fax : 01 43 71 38 77<br>
                    Courriel : fpf@federation-photo.fr - Internet : www.federation-photo.fr
                </td>
            </tr>
        </table>
    </header>
    @yield('content')
    <footer>
        Association loi 1901 déclarée à Paris, en 1892, sous le N° 15 382 P<br>
        SIRET : 784 717 464 00027 - APE 9412 Z - N° Intracommunautaire : FR 40 784 717 464
    </footer>
</body>

</html>
