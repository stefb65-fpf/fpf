@extends('layouts.default')

@section('content')
    <div class="pageCanva">
        <h1 class="pageTitle">
            <div>Gestion Club - Statistiques vote AG FPF
                <div class="urTitle">
                    {{ $club->nom }}
                </div>
            </div>

            <a class="previousPage" title="Retour page précédente" href="{{ route('clubs.statistiques') }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-reply-fill" viewBox="0 0 16 16">
                    <path d="M5.921 11.9 1.353 8.62a.719.719 0 0 1 0-1.238L5.921 4.1A.716.716 0 0 1 7 4.719V6c1.5 0 6 0 7 8-2.5-4.5-7-4-7-4v1.281c0 .56-.606.898-1.079.62z"/>
                </svg>
            </a>
        </h1>
        <div class="d-flex align-center">
            <a class="tabIndex" href="{{ route('clubs.statistiques') }}">Club</a>
            <a class="tabIndex active">Stats Votes AG FPF</a>
        </div>
        <h2>{{ $vote->nom }}</h2>
        <h3>Liste des adhérents du club n'ayant pas encore voté</h3>
        @if(sizeof($adherents) == 0)
            <p>Tous les adhérents du club ont voté</p>
        @else
            <table  class="styled-table">
                <thead>
                <tr>
                    <th>Nom</th>
                    <th>Prénom</th>
                </tr>
                </thead>
                <tbody>
                @foreach($adherents as $adherent)
                    <tr>
                        <td>{{ $adherent->nom }}</td>
                        <td>{{ $adherent->prenom }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @endif
    </div>
@endsection
