@extends('layouts.default')

@section('content')
    <div class="pageCanva">
        <h1 class="pageTitle">
            Gestion Fédérale - Statistiques
            <a class="previousPage" title="Retour page précédente" href="{{ route('admin') }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor"
                     class="bi bi-reply-fill" viewBox="0 0 16 16">
                    <path
                        d="M5.921 11.9 1.353 8.62a.719.719 0 0 1 0-1.238L5.921 4.1A.716.716 0 0 1 7 4.719V6c1.5 0 6 0 7 8-2.5-4.5-7-4-7-4v1.281c0 .56-.606.898-1.079.62z"/>
                </svg>
            </a>
        </h1>
        <div class="d-flex align-center">
            <a class="tabIndex active">Adhésions FPF</a>
            <a class="tabIndex" href="{{ route('admin.statistiques_votes') }}">Votes FPF</a>
            @if($menu['ur'])
                <a class="tabIndex" href="{{ route('urs.statistiques') }}">Adhésions UR</a>
                <a class="tabIndex" href="{{ route('urs.statistiques_votes') }}">Votes UR</a>
            @endif
            @if($menu['club'])
                <a class="tabIndex" href="{{ route('clubs.statistiques') }}">Club</a>
            @endif
        </div>
        <div class="d-flex">
            <div class="flex-1 p10" style="background-color: white">
                @include('admin.statistiques.liste_stats_generales')
            </div>
            <div style="flex: 4">
                <div class="d-flex">
                    <div id="piechartClubs" style="width: 50%; height: 500px"></div>
                    <div id="piechartAdherents" style="width: 50%; height: 500px"></div>
                </div>
                <div class="d-flex">
                    <div id="piechartRepartitionAdherents" style="width: 50%; height: 500px"></div>
{{--                    <div id="linechartEvolution" style="width: 50%; height: 500px"></div>--}}
                </div>
            </div>
        </div>

        <div class="mt50">
            <h2>Répartition des cartes</h2>
            <table class="styled-table">
                <thead>
                <tr>
                    <th>UR / FPF</th>
                    <th>Adh Club > 25 ans</th>
                    <th>Adh Club 18 - 25 ans</th>
                    <th>Adh Club < 18 ans</th>
                    <th>Adh Club 2nde carte</th>
                    <th>Adh Club famille</th>
                    <th>Individuel > 25 ans</th>
                    <th>Individuel 18 -25 ans</th>
                    <th>Individuel < 18 ans</th>
                    <th>Individuel famille</th>
                    <th>Total</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>FPF</td>
                    <td>{{ $tab_total['ct2'] }}</td>
                    <td>{{ $tab_total['ct3'] }}</td>
                    <td>{{ $tab_total['ct4'] }}</td>
                    <td>{{ $tab_total['ct5'] }}</td>
                    <td>{{ $tab_total['ct6'] }}</td>
                    <td>{{ $tab_total['ct7'] }}</td>
                    <td>{{ $tab_total['ct8'] }}</td>
                    <td>{{ $tab_total['ct9'] }}</td>
                    <td>{{ $tab_total['ctF'] }}</td>
                    <td>{{ $tab_total['total'] }}</td>
                </tr>
                @foreach($tab_repartition as $k => $repartition)
                    <tr>
                        <td>{{ $k }}</td>
                        <td>{{ $repartition['ct2'] ?? '' }}</td>
                        <td>{{ $repartition['ct3'] ?? '' }}</td>
                        <td>{{ $repartition['ct4'] ?? '' }}</td>
                        <td>{{ $repartition['ct5'] ?? '' }}</td>
                        <td>{{ $repartition['ct6'] ?? '' }}</td>
                        <td>{{ $repartition['ct7'] ?? '' }}</td>
                        <td>{{ $repartition['ct8'] ?? '' }}</td>
                        <td>{{ $repartition['ct9'] ?? '' }}</td>
                        <td>{{ $repartition['ctF'] ?? '' }}</td>
                        <td>{{ $repartition['total'] ?? '' }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <span style="display: none" id="levelStat">fpf</span>
    <span style="display: none" id="urStat">0</span>
@endsection
@section('js')
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script src="{{ asset('js/admin_statistiques.js') }}?t=<?= time() ?>"></script>
@endsection
