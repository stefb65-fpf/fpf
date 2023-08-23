@extends('layouts.default')

@section('content')
    <div class="pageCanva">
        <h1 class="pageTitle">
            Espace de gestion des votes pour la FPF
            <a class="previousPage" title="Retour page précédente" href="{{ route('admin') }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor"
                     class="bi bi-reply-fill" viewBox="0 0 16 16">
                    <path
                        d="M5.921 11.9 1.353 8.62a.719.719 0 0 1 0-1.238L5.921 4.1A.716.716 0 0 1 7 4.719V6c1.5 0 6 0 7 8-2.5-4.5-7-4-7-4v1.281c0 .56-.606.898-1.079.62z"/>
                </svg>
            </a>
        </h1>
        <div class="alertInfo w80">
            <span class="bold">Informations !</span>
            Vous pouvez ici paramétrer les élections FPF qui sont organisées comme suit:
            <ul class="ml30">
                <li>Une session de votes se déroule sur une période précise et peut comprendre plusieurs élections
                    (approbation rapport moral, élections collège, ...)
                </li>
                <li>Chaque élection d'une même session sera accessible par les mêmes votants. Toutes les élections
                    d'une session se votent en une seul fois
                </li>
                <li>Pour chaque élection, vous devrez paramétrer le type (vote d'une motion ou élections de
                    candidats), l'accès des clubs au vote et une éventuelle restriction à des fonctions FPF (présidents
                    d'UR, CA, ...)
                </li>
            </ul>
        </div>
        <div class="mt25 flexEnd">
            <a href="{{ route('votes.create') }}" class="adminPrimary btnMedium">Créer une nouvelle session de votes</a>
        </div>
        <div>
            <table class="styled-table">
                <thead>
                <tr>
                    <th>Session</th>
                    <th>Année</th>
                    <th>Date de début</th>
                    <th>Date de fin</th>
                    <th>Portée</th>
                    <th>Fonctions</th>
                    <th>Vote club</th>
                    <th>Votants</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @foreach($votes as $vote)
                    <tr>
                        <td>{{ $vote->nom }}</td>
                        <td>{{ $vote->annee }}</td>
                        <td>{{ substr($vote->debut, 8, 2).'/'.substr($vote->debut, 5, 2).'/'.substr($vote->debut, 0, 4) }} à partir de 0h00</td>
                        <td>{{ substr($vote->fin, 8, 2).'/'.substr($vote->fin, 5, 2).'/'.substr($vote->fin, 0, 4) }} jusqu'à 23h59</td>
                        <td>{{ ($vote->urs_id == 0) ? 'National' : 'UR '.$vote->urs_id }}</td>
                        <td>
                            @switch($vote->fonctions_id)
                                @case(0)
                                Tout adhérent
                                @break
                                @case(57)
                                Présidents d'UR
                                @break
                                @case(94)
                                Présidents de club
                                @break
                                @case(9999)
                                CA
                                @break
                            @endswitch
                        </td>
                        <td>{{ ($vote->vote_club == 0) ? 'Non' : 'Vote club' }}</td>
                        <td>{{ $vote->total_votes }}</td>
                        <td>
                            <div class="mb3">
                                <a href="{{ route('votes.edit', $vote) }}" class="adminPrimary btnSmall">Modifier</a>
                            </div>
                            <div class="mb3">
                                <a href="{{ route('votes.elections.index', $vote) }}" class="adminSuccess btnSmall">Elections liées</a>
                            </div>
                            <div>
                                <a href="{{ route('votes.destroy', $vote) }}" data-method="delete" data-confirm="Voulez-vous vraiment supprimer ce vote ? Toutes les données enregistrées seront supprimées et aucun retour en arrière ne sera possible." class="adminDanger btnSmall">Supprimer</a>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>

            </table>
        </div>
        {{ $votes->render( "pagination::default") }}
    </div>
@endsection
@section('css')
    <link href="{{ asset('css/admin_fpf.css') }}" rel="stylesheet">
@endsection
