@extends('layouts.default')

@section('content')
    <div class="pageCanva">
        <h1 class="pageTitle">
            Espace de gestion du CA pour la FPF
            <a class="previousPage" title="Retour page précédente" href="{{ route('admin.structures') }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-reply-fill" viewBox="0 0 16 16">
                    <path d="M5.921 11.9 1.353 8.62a.719.719 0 0 1 0-1.238L5.921 4.1A.716.716 0 0 1 7 4.719V6c1.5 0 6 0 7 8-2.5-4.5-7-4-7-4v1.281c0 .56-.606.898-1.079.62z"/>
                </svg>
            </a>
        </h1>
        <form action="{{ route('fonctions.add_ca') }}" method="POST" style="width: 100%">
            {{ csrf_field() }}
            <div style="display: flex; justify-content: flex-end; margin-top: 20px; width: 100%;">
                <input type="text" placeholder="Identifiant de l'adhérent à ajouter" style="padding: 5px" maxlength="12" name="identifiant" />
                <button type="submit" class="adminPrimary btnSmall">Ajouter au CA</button>
            </div>
        </form>

        <table class="styled-table">
            <thead>
            <tr>
                <th>Adhérent</th>
                <th>Identifiant</th>
                <th>Email</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            @foreach($utilisateurs as $utilisateur)
                <tr>
                    <td>{{ $utilisateur->personne->prenom.' '.$utilisateur->personne->nom }}</td>
                    <td>{{ $utilisateur->identifiant }}</td>
                    <td><a href="mailto:{{ $utilisateur->personne->email }}">{{ $utilisateur->personne->email }}</a></td>
                    <td>
                            <a href="{{route('fonctions.destroy_ca', $utilisateur)}}" class="adminDanger btnSmall"  data-method="delete"  data-confirm="Voulez-vous vraiment enelever cet utilisateur du CA ?">Supprimer du CA</a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection
@section('css')
    <link href="{{ asset('css/admin_fpf.css') }}" rel="stylesheet">
@endsection
