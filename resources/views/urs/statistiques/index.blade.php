@extends('layouts.default')

@section('content')
    <div class="pageCanva">
        <h1 class="pageTitle">
            <div>
                Gestion Union Régionale - Statistiques
                <div class="urTitle">{{ $ur->nom }}</div>
            </div>
            <a class="previousPage" title="Retour page précédente" href="{{ route('urs.gestion') }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-reply-fill" viewBox="0 0 16 16">
                    <path d="M5.921 11.9 1.353 8.62a.719.719 0 0 1 0-1.238L5.921 4.1A.716.716 0 0 1 7 4.719V6c1.5 0 6 0 7 8-2.5-4.5-7-4-7-4v1.281c0 .56-.606.898-1.079.62z"/>
                </svg>
            </a>
        </h1>
        <div class="d-flex align-center">
            @if($menu['admin'] && in_array('VISUSTAT', $droits_fpf))
                <a class="tabIndex" href="{{ route('admin.statistiques') }}">Adhésions FPF</a>
                <a class="tabIndex" href="{{ route('admin.statistiques_votes') }}">Votes FPF</a>
                @if($exist_vote)
                    <a class="tabIndex" href="{{ route('admin.statistiques_votes_phases') }}">Stats Votes AG</a>
                @endif
            @endif

            <a class="tabIndex active">Adhésions UR</a>
            <a class="tabIndex" href="{{ route('urs.statistiques_votes') }}">Votes UR</a>
            @if(!$menu['admin'] && $exist_vote)
                <a class="tabIndex" href="{{ route('urs.statistiques_votes_phases') }}">Stats Votes AG FPF</a>
            @endif
            @if($menu['club'])
                <a class="tabIndex" href="{{ route('clubs.statistiques') }}">Club</a>
            @endif

        </div>
        <div class="d-flex">
            <div class="flex-1 p10" style="background-color: white">
                @include('admin.statistiques.liste_stats_generales')
                <div class="mt50 mb50 bolder">
                    <div class="text-center"><h2>Reversements</h2></div>
                    <div class="text-center" style="font-size: large">
                        {{ $montant_reversements }}€
                    </div>
                </div>
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
                    <th>Club / UR</th>
                    <th>Adh > 25 ans</th>
                    <th>Adh 18 - 25 ans</th>
                    <th>Adh < 18 ans</th>
                    <th>Adh 2nde carte</th>
                    <th>Adh famille</th>
                    <th>Total</th>
                    <th>Préinscrits</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>UR</td>
                    <td style="text-align: right">{{ $tab_total['ct2'] + $tab_total['ct7'] }}</td>
                    <td style="text-align: right">{{ $tab_total['ct3'] + $tab_total['ct8'] }}</td>
                    <td style="text-align: right">{{ $tab_total['ct4'] + $tab_total['ct9'] }}</td>
                    <td style="text-align: right">{{ $tab_total['ct6'] }}</td>
                    <td style="text-align: right">{{ $tab_total['ct5'] + $tab_total['ctF'] }}</td>
                    <td style="text-align: right">{{ $tab_total['total'] }}</td>
                    <td style="text-align: right">{{ $tab_total['preinscrits'] }}</td>
                </tr>
                @if(isset($tab_repartition[0]))
                    <tr>
                        <td>Individuels</td>
                        <td style="text-align: right">{{ $tab_repartition[0]['ct7'] ?? '' }}</td>
                        <td style="text-align: right">{{ $tab_repartition[0]['ct8'] ?? '' }}</td>
                        <td style="text-align: right">{{ $tab_repartition[0]['ct9'] ?? '' }}</td>
                        <td style="text-align: right"></td>
                        <td style="text-align: right">{{ $tab_repartition[0]['ctF'] ?? '' }}</td>
                        <td style="text-align: right">{{ $tab_repartition[0]['total'] ?? '' }}</td>
                        <td style="text-align: right">{{ $tab_repartition[0]['preinscrit'] ?? '' }}</td>
                    </tr>
                @endif
                @foreach($tab_repartition as $k => $repartition)
                    @if($k !== 0)
                        <tr>
                            <td>{{ $repartition['club'].' ('.str_pad($repartition['numero'], 4, '0', STR_PAD_LEFT).')' }}</td>
                            <td style="text-align: right">{{ $repartition['ct2'] ?? '' }}</td>
                            <td style="text-align: right">{{ $repartition['ct3'] ?? '' }}</td>
                            <td style="text-align: right">{{ $repartition['ct4'] ?? '' }}</td>
                            <td style="text-align: right">{{ $repartition['ct6'] ?? '' }}</td>
                            <td style="text-align: right">{{ $repartition['ct5'] ?? '' }}</td>
                            <td style="text-align: right">{{ $repartition['total'] ?? '' }}</td>
                            <td style="text-align: right">{{ $repartition['preinscrit'] ?? '' }}</td>
                        </tr>
                    @endif
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <span style="display: none" id="levelStat">ur</span>
    <span style="display: none" id="urStat">{{ $ur->id }}</span>
@endsection
@section('js')
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script src="{{ asset('js/admin_statistiques.js') }}?t=<?= time() ?>"></script>
@endsection
