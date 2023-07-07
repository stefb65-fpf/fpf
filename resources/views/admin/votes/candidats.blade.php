@extends('layouts.default')

@section('content')
    <div class="pageCanva">
        <h1 class="pageTitle">
            Liste des candidats pour l'élection {{ $election->nom }}
            <a class="previousPage" title="Retour page précédente" href="{{ route('votes.elections.index', $vote) }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor"
                     class="bi bi-reply-fill" viewBox="0 0 16 16">
                    <path
                        d="M5.921 11.9 1.353 8.62a.719.719 0 0 1 0-1.238L5.921 4.1A.716.716 0 0 1 7 4.719V6c1.5 0 6 0 7 8-2.5-4.5-7-4-7-4v1.281c0 .56-.606.898-1.079.62z"/>
                </svg>
            </a>
        </h1>
        {{--        <div class="mt25 flexEnd">--}}
        {{--            <a href="{{ route('votes.elections.create', $vote) }}" class="adminPrimary btnMedium">Créer une nouvelle élection</a>--}}
        {{--        </div>--}}
        <div class="formBlock">
            <div class="formBlockTitle">Ajout candidat</div>
            <form action="{{ route('votes.elections.candidats.store', [$vote, $election]) }}" method="POST"
                  enctype="multipart/form-data">
                {{ csrf_field() }}
                <div class="formBlockWrapper">
                    <div class="formUnit formUnitAdmin">
                        <div class="formLabel">Identifiant adhérent</div>
                        <input class="formValue formValueAdmin" type="text" name="identifiant" maxlength="12"/>
                    </div>
                    <button type="submit" class="formBtn success btnSmall">Ajouter</button>
                </div>
            </form>
        </div>

        <div style="width: 100%">
            <table class="styled-table" style="width: 100%">
                <thead>
                <tr>
                    <th>Ordre</th>
                    <th>Identifiant adhérent</th>
                    <th>Civilité</th>
                    <th>Email</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @foreach($candidats as $candidat)
                    <tr>
                        <td>{{ $candidat->ordre }}</td>
                        <td>{{ $candidat->utilisateur->identifiant }}</td>
                        <td>{{ $candidat->utilisateur->personne->prenom.' '.strtoupper($candidat->utilisateur->personne->prenom) }}</td>
                        <td>{{ $candidat->utilisateur->personne->email }}</td>
                        <td>
                            <div>
                                <a href="{{ route('votes.elections.candidats.delete', [$vote, $election, $candidat]) }}" data-method="delete" data-confirm="Voulez-vous vraiment supprimer ce candidat ? Toutes les données enregistrées seront supprimées et aucun retour en arrière ne sera possible." class="adminDanger btnSmall">Supprimer</a>
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
