@extends('layouts.default')

@section('content')
    <div class="pageCanva">
        <h1 class="pageTitle">
            Liste des élections liées au vote {{ $vote->nom }}
            <a class="previousPage" title="Retour page précédente" href="{{ route('votes.index') }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor"
                     class="bi bi-reply-fill" viewBox="0 0 16 16">
                    <path
                        d="M5.921 11.9 1.353 8.62a.719.719 0 0 1 0-1.238L5.921 4.1A.716.716 0 0 1 7 4.719V6c1.5 0 6 0 7 8-2.5-4.5-7-4-7-4v1.281c0 .56-.606.898-1.079.62z"/>
                </svg>
            </a>
        </h1>
        <div class="alertInfo" style="width: 80% !important">
            <span class="bold">Informations !</span>
            Vous pouvez ici ajouter les élections pour une session de vote. Vous pouvez paramétrer l'ordre de présentation des élections. Elles seront alors présentées dans cet ordre à l'ensemble des votants.
            Pour chaque élection, vous allez pouvoir ajouter un descriptif du sujet concerné et, le cas échéant, saisir les candidats.
        </div>
        <div class="mt25 flexEnd">
            <a href="{{ route('votes.elections.create', $vote) }}" class="adminPrimary btnMedium">Créer une nouvelle élection</a>
        </div>
        <div style="width: 100%">
            <table class="styled-table" style="width: 100%">
                <thead>
                <tr>
                    <th>&Eacute;lection</th>
                    <th>Ordre de présentation</th>
                    <th>Type</th>
                    <th>Nombre de postes</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @foreach($elections as $election)
                    <tr>
                        <td>{{ $election->nom }}</td>
                        <td>{{ $election->ordre }}</td>
                        <td>{{ $election->type == 1 ? 'Motion' : 'Election de candidats' }}</td>
                        <td>{{ $election->type == 1 ? '' : $election->nb_postes }}</td>
                        <td>
                            <div style="margin-bottom: 3px;">
                                <a href="{{ route('votes.elections.edit', [$vote, $election]) }}" class="adminPrimary btnSmall">Modifier</a>
                            </div>
                            @if($election->type == 2)
                                <div style="margin-bottom: 3px;">
                                    <a href="{{ route('votes.elections.candidats.index', [$vote, $election]) }}" class="adminSuccess btnSmall">Candidats</a>
                                </div>
                            @endif
                            <div>
                                <a href="{{ route('votes.elections.delete', [$vote, $election]) }}" data-method="delete" data-confirm="Voulez-vous vraiment supprimer cette élection ? Toutes les données enregistrées seront supprimées et aucun retour en arrière ne sera possible." class="adminDanger btnSmall">Supprimer</a>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
@section('css')
    <link href="{{ asset('css/admin_fpf.css') }}" rel="stylesheet">
@endsection
