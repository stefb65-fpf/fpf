@extends('layouts.pdf-fpf')

@section('content')
    <style>
        .wrapper-page {
            page-break-after: always;
            width: 100%;
            font-size: 12px;
            height: 100%;
        }
        .wrapper-page:last-child {
            page-break-after: avoid;
        }
        .title {
            text-align: center;
            font-weight: bolder;
        }
    </style>
    @foreach($tab_clubs as $tab_club)
        <div class="wrapper-page">
            <div class="title">Renouvellement pour le club {{ str_pad($tab_club['club']->numero, 4, '0', STR_PAD_LEFT) }} - {{ $tab_club['club']->nom }}</div>
            @if($tab_club['contact'])
                <div style="margin-left: 60%; margin-top: 20px;">
                    <table>
                        <tr>
                            <td>**C**</td>
                            <td>{{ $tab_club['contact']->identifiant }}</td>
                        </tr>
                        <tr>
                            <td colspan="=2">
                                {{ $tab_club['contact']->personne->sexe == 0 ? 'Mr' : 'Mme' }} {{ $tab_club['contact']->personne->nom }} {{ $tab_club['contact']->personne->prenom }}
                            </td>
                        </tr>
                        @if($tab_club['contact']->personne->adresses[0]->libelle1)
                            <tr>
                                <td colspan="2">{{ $tab_club['contact']->personne->adresses[0]->libelle1 }}</td>
                            </tr>
                        @endif
                        @if($tab_club['contact']->personne->adresses[0]->libelle2)
                            <tr>
                                <td colspan="2">{{ $tab_club['contact']->personne->adresses[0]->libelle2 }}</td>
                            </tr>
                        @endif
                        <tr>
                            <td colspan="2">
                                {{ str_pad($tab_club['contact']->personne->adresses[0]->codepostal, 5, '0', STR_PAD_LEFT) }} {{ strtoupper($tab_club['contact']->personne->adresses[0]->ville) }}
                            </td>
                        </tr>
                    </table>
                </div>
            @endif

            @if(isset($tab_club['cartes']))
                <div style="margin-top: 30px; font-weight: bolder;">Liste des cartes éditées</div>
                <div style="margin-left: 40px;">
                    @foreach($tab_club['cartes'] as $carte)
                        <div>{{ $carte->identifiant }}: {{ $carte->personne->nom }} {{ $carte->personne->prenom }}</div>
                    @endforeach
                </div>
            @endif

            @if(isset($tab_club['vignettes']))
                <div style="margin-top: 30px; font-weight: bolder;">Liste des vignettes ({{ sizeof($tab_club['vignettes']) }})</div>
                <div style="margin-left: 40px;">
                    @foreach($tab_club['vignettes'] as $carte)
                        <div>{{ $carte->identifiant }}: {{ $carte->personne->nom }} {{ $carte->personne->prenom }}</div>
                    @endforeach
                </div>
            @endif
        </div>

    @endforeach
@endsection
