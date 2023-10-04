@extends('layouts.default')

@section('content')
    <div class="pageCanva">
        <h1 class="pageTitle">
            Liste des formateurs
            <a class="previousPage" title="Retour page précédente" href="{{ route('formations.admin_accueil') }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-reply-fill" viewBox="0 0 16 16">
                    <path d="M5.921 11.9 1.353 8.62a.719.719 0 0 1 0-1.238L5.921 4.1A.716.716 0 0 1 7 4.719V6c1.5 0 6 0 7 8-2.5-4.5-7-4-7-4v1.281c0 .56-.606.898-1.079.62z"/>
                </svg>
            </a>
        </h1>

        <div class="d-flex justify-center">
            <a href="{{ route('formateurs.create') }}" class="btnMedium adminPrimary">Ajouter un formateur</a>
        </div>
        @if(sizeof($formateurs) == 0)
            <div class="emptyList">Aucun formateur</div>
        @else
            <table class="styled-table">
                <thead>
                    <tr>
                        <th>Formateur</th>
                        <th>Email</th>
                        <th>Téléphone</th>
                        <th>Titre</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                @foreach($formateurs as $formateur)
                    <tr>
                        <td>{{ $formateur->nom }} {{ $formateur->prenom }}</td>
                        <td>
                            <a href="{{ $formateur->email }}">{{ $formateur->email }}</a>
                        </td>
                        <td>{{ $formateur->phone_mobile }}</td>
                        <td>{{ $formateur->title }}</td>
                        <td>
                            <a href="{{ route('formateurs.edit', $formateur) }}" class="btnSmall adminPrimary">Modifier</a>
                            @if(sizeof($formateur->formations) == 0)
                                <a href="{{ route('formateurs.destroy', $formateur) }}" data-method="delete" data-confirm="Voulez-vous vraiment supprimer ce formateur ?" class="btnSmall adminDanger mt5">Supprimer</a>
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @endif
    </div>
@endsection
@section('css')
    <link href="{{ asset('css/admin_fpf.css') }}" rel="stylesheet">
@endsection
