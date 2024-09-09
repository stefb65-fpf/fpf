<!DOCTYPE html>
<html prefix="og: https://ogp.me/ns#" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <style>
        @font-face {
            font-family: 'Aptos';
            src: url({{storage_path('fonts/Aptos.ttf')}});
        }
        @font-face {
            font-family: 'Aptos Bold';
            src: url({{storage_path('fonts/Aptos-Bold.ttf')}});
        }
        @font-face {
            font-family: 'Museo';
            src: url({{storage_path('fonts/Museo500-Regular.otf')}});
        }

        {{--@font-face {--}}
        {{--    font-family: 'Lato Bold';--}}
        {{--    src: url({{storage_path('app/public/fonts/Lato-Bold.ttf')}}) format("truetype");--}}
        {{--    /*font-weight: 700;*/--}}
        {{--    /*font-style: normal;*/--}}
        {{--}--}}
        @page { margin: 120px 25px 50px;
            font-family: Aptos, sans-serif; }
        header { position: fixed; top: -120px; left: 0px; right: 0px; color: #3e4c85; height: 120px;
            font-family: "Museo", sans-serif; }
        footer { position: fixed; bottom: -50px; left: 0px; right: 0px; height: 80px; width: 100%; text-align: center; font-size:10px; }
        p { page-break-after: always; }
        p:last-child { page-break-after: never; }
        .blue-font {
            color: #3e4c85;
        }
        .bg-blue {
            background-color: #3e4c85;
        }
        .facture-table, .facture-table th, .facture-table td {
            border: 1px solid black;
            border-collapse: collapse;
        }
        .facture-table td, .facture-table th {
            padding-left: 5px;
        }
        .facture-table th {
            color: white;
            font-weight: normal;
        }
    </style>
</head>
<body class="bgTransparent">
    <header>
        <table style="width: 100%;">
            <tr>
                <td>
                    <div>
                        <img src="{{ env('APP_URL').'storage/app/public/fpf-logo.png' }}"
                             alt="Fédération Photographique de France" width="60px" height="60px" />
                    </div>

                </td>
                <td>
                    <div style="font-family: 'Museo',sans-serif; font-size: 1.8rem; text-align: center; line-height: 1.8rem; padding-left: 30px;">
                        Fédération Photographique de France
                    </div>
                </td>
            </tr>
            <tr style="width: 100%;">
                <td style="width: 100%;" colspan="2">
                    <div style="width: 100%; border-bottom: 4px solid #3e4c85; margin-top: -20px;">&nbsp;</div>
                </td>
            </tr>
        </table>
    </header>
    @yield('content')
    <footer>
        5, rue Jules Vallès – 75011 Paris - France<br>
        Association loi 1901 déclarée à Paris en 1892 sous le n° 15382P – RNA W751015382<br>
        SIRET : 784 717 464 00027 - APE 9412Z – N° TVA Intracommunautaire : FR 40 784 717 464<br>
        https://federation-photo.fr – fpf@federation-photo.fr – 01 43 71 30 40<br>
    </footer>
</body>

</html>
