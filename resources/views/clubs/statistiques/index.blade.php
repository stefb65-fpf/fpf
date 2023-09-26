@extends('layouts.default')

@section('content')
    <div class="pageCanva">
        <h1 class="pageTitle">
            <div>Gestion Club - Statistiques
                <div class="urTitle">
                    {{ $club->nom }}
                </div>
            </div>

            <a class="previousPage" title="Retour page précédente" href="{{ route('clubs.gestion') }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-reply-fill" viewBox="0 0 16 16">
                    <path d="M5.921 11.9 1.353 8.62a.719.719 0 0 1 0-1.238L5.921 4.1A.716.716 0 0 1 7 4.719V6c1.5 0 6 0 7 8-2.5-4.5-7-4-7-4v1.281c0 .56-.606.898-1.079.62z"/>
                </svg>
            </a>
        </h1>
        @if($menu['ur'] || $menu['admin'])
            <div class="d-flex align-center">
                @if($menu['admin'])
                    <a class="tabIndex" href="{{ route('admin.statistiques') }}">Adhésions FPF</a>
                    <a class="tabIndex" href="{{ route('admin.statistiques_votes') }}">Votes FPF</a>
                @endif
                @if($menu['ur'])
                    <a class="tabIndex" href="{{ route('urs.statistiques') }}">Adhésions UR</a>
                    <a class="tabIndex" href="{{ route('urs.statistiques_votes') }}">Votes UR</a>
                @endif
                <a class="tabIndex active">Club</a>
            </div>
        @endif
        <div class="d-flex">
            <div class="flex-1 p10" style="background-color: white">
                <div class="mt50 mb50 bolder">
                    <div class="text-center"><h2>Adhérents</h2></div>
                    <div class="text-center" style="font-size: large">
                        {{ $nb_adherents }} / {{ $nb_adherents_previous }}
                        @if($ratio_adherents > 0)
                            <span style="color: green">(+{{ $ratio_adherents }}%)</span>
                        @else
                            <span style="color: red">({{ $ratio_adherents }}%)</span>
                        @endif
                    </div>
                </div>
                <div class="mt50 mb50 bolder">
                    <div class="text-center"><h2>Abonnements</h2></div>
                    <div class="text-center" style="font-size: large">
                        {{ $nb_abonnements }} personnes<br>
                    </div>
                </div>
                <div class="mt50 mb50 bolder">
                    <div class="text-center"><h2>Florilège</h2></div>
                    <div class="text-center" style="font-size: large">
                        {{ $nb_souscriptions }}
                    </div>
                </div>
            </div>
            <div style="flex: 4">
                <div class="p10 ml10" style="background-color: white">
                    <h2>Classement compétitions</h2>
                    <div class="ml40">
                        @if(sizeof($classements_nationaux) + sizeof($classements_regionaux) == 0)
                            <span>Aucun classement n'est actuellemment disponible pour le club</span>
                        @else
                            <table class="styled-table">
                                <thead>
                                <tr>
                                    <th>Compétition</th>
                                    <th>Points</th>
                                    <th>Place</th>
                                </tr>
                                </thead>
                                @foreach($classements_nationaux as $national)
                                    <tr>
                                        <td>{{ $national->nom }}</td>
                                        <td>{{ $national->total }}</td>
                                        <td>{{ $national->place }}</td>
                                    </tr>
                                @endforeach
                                @foreach($classements_regionaux as $regional)
                                    <tr>
                                        <td>{{ $regional->nom }}</td>
                                        <td>{{ $regional->total }}</td>
                                        <td>{{ $regional->place }}</td>
                                    </tr>
                                @endforeach
                            </table>

                        @endif
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
