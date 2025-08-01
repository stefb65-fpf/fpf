<!DOCTYPE html>
<html prefix="og: https://ogp.me/ns#" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Liste des clubs FPF</title>
    <style>
        @font-face {
            font-family: 'Calibri';
            src: url('{{ resource_path('fonts/Calibri.ttf') }}') format('truetype');
            font-weight: normal;
            font-style: normal;
        }

        @page { margin: 100px 25px; }
        header { position: fixed; top: -40px; left: 0; right: 0; color: black; height: 30px; font-size:13px; text-align: center; }
        footer { position: fixed; bottom: -30px; left: 0; right: 0; height: 20px; width: 100%; text-align: center; font-size:10px; }

        body {
            font-family: 'Calibri', sans-serif;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            page-break-inside: auto;
            font-family: 'Calibri', sans-serif;
        }

        thead {
            display: table-header-group;
        }

        tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }

        th, td {
            border-bottom-width: thin;
            border-color: #dddddd;
            border-style: solid;
            border-left-width: 0;
            border-right-width: 0;
            border-top-width: 0;
            padding: 4px;
        }
        td {
            font-size: 9px;
            text-align: left;
        }

        th {
            background-color: black;
            color: white;
            font-weight: bold;
            font-size: 8px;
            text-align: center;
        }

        .cover {
            text-align: center;
            margin-top: 10px;
            font-size: 24px;
            width: 100%;
        }

        .sub {
            font-size: 16px;
            margin-top: 20px;
        }

        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body class="bgTransparent">
    <div class="cover">
        <div style="color:#0070c0; font-size: 30px; margin-bottom: 80px;">Fédération Photographique de France</div>
        <div style="color:#0070c0; font-size: 24px; background-color: #b6dde8; padding: 20px; margin-bottom: 20px;">Liste des Clubs FPF</div>
        <div style="color:#0070c0; font-size: 24px; margin-bottom: 80px;">{{ date('Y') }}</div>
        <div>
            <img src="{{ env('APP_URL').'storage/app/public/FPF-logo-800.png' }}"
                 alt="Fédération Photographique de France" width="300px" height="300px">
        </div>

        <div style="font-size: 14px; margin-bottom: 3px; color:#0070c0; margin-top: 80px;">5, rue Jules Vallès - 75011 PARIS</div>
        <div style="font-size: 14px; margin-bottom: 3px; color:#0070c0;">Tél. 01 43 71 30 40</div>
        <div style="font-size: 14px; margin-bottom: 3px; color:#0070c0;">Courriel : fpf@federation-photo.fr</div>
        <div style="font-size: 14px; color:#0070c0;">https://federation-photo.fr</div>
    </div>

    <div class="page-break"></div>

    <header>
        Fédération Photographique de France - Liste des Clubs {{ date('Y') }}
    </header>
    @yield('content')
{{--    <footer>--}}
{{--        édition du {{ date('d/m/Y') }} - Page <span class="pagenum"></span>--}}
{{--    </footer>--}}


<script type="text/php">
    if (isset($pdf)) {
        $pdf->page_script('
            $text = __("page :pageNum/:pageCount", ["pageNum" => $PAGE_NUM, "pageCount" => $PAGE_COUNT]);
            $font = null;
            $size = 9;
            $color = array(0,0,0);
            $word_space = 0.0;  //  default
            $char_space = 0.0;  //  default
            $angle = 0.0;   //  default

            // Compute text width to center correctly
            $textWidth = $fontMetrics->getTextWidth($text, $font, $size);

            $x = ($pdf->get_width() - $textWidth) / 2;
            $y = $pdf->get_height() - 35;

            $pdf->text($x, $y, $text, $font, $size, $color, $word_space, $char_space, $angle);
        ');
    }
</script>
</body>

</html>
