@extends('layouts.default')

@section('content')
    <div class="pageCanva">
        <h1 class="pageTitle">
            <div>
                Gestion Union Régionale - Statistiques votes AG FPF
                <div class="urTitle">{{ $ur->nom }}</div>
            </div>
            <a class="previousPage" title="Retour page précédente" href="{{ route('urs.statistiques_votes_phases') }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-reply-fill" viewBox="0 0 16 16">
                    <path d="M5.921 11.9 1.353 8.62a.719.719 0 0 1 0-1.238L5.921 4.1A.716.716 0 0 1 7 4.719V6c1.5 0 6 0 7 8-2.5-4.5-7-4-7-4v1.281c0 .56-.606.898-1.079.62z"/>
                </svg>
            </a>
        </h1>
        <div class="d-flex align-center">
            @if($menu['admin'] && in_array('VISUSTAT', $droits_fpf))
                <a class="tabIndex" href="{{ route('admin.statistiques') }}">Adhésions FPF</a>
                <a class="tabIndex" href="{{ route('admin.statistiques_votes') }}">Votes FPF</a>
            @endif
            <a class="tabIndex" href="{{ route('urs.statistiques') }}">Adhésions UR</a>
            <a class="tabIndex" href="{{ route('urs.statistiques_votes') }}">Votes UR</a>
            @if((!$menu['admin'] || !in_array('VISUSTAT', $droits_fpf)) && $exist_vote)
                <a class="tabIndex active" href="{{ route('urs.statistiques_votes_phases') }}">Stats Votes AG FPF</a>
            @endif
            @if($menu['club'])
                <a class="tabIndex" href="{{ route('clubs.statistiques') }}">Club</a>
            @endif
        </div>
        @include('admin.statistiques.detail_votes_by_club', ['level' => 'urs'])
    </div>
@endsection
