@extends('layouts.default')

@section('content')
    <div class="pageCanva">
        <h1 class="pageTitle">
            Gérer les attributions pour la fonction {{ $fonction->libelle }} pour l'UR {{ $ur->nom }}
            <a class="previousPage" title="Retour page précédente" href="{{ route('admin.urs.fonctions', $ur) }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-reply-fill" viewBox="0 0 16 16">
                    <path d="M5.921 11.9 1.353 8.62a.719.719 0 0 1 0-1.238L5.921 4.1A.716.716 0 0 1 7 4.719V6c1.5 0 6 0 7 8-2.5-4.5-7-4-7-4v1.281c0 .56-.606.898-1.079.62z"/>
                </svg>
            </a>
        </h1>

        <div class="formBlock">
            <div class="formBlockTitle">Attribuer la fonction à un nouvel utilisateur</div>
            <form class="w100" action="{{ route('admin.urs.fonctions.attribuate', [$fonction, $ur->id]) }}" method="POST">
                {{ csrf_field() }}
                <div class="d-flex align-center">
                    <div class="formUnit w60" style="margin: 0">
                        <div class="formLabel mr5">Identifiant adhérent</div>
                        <input value="" class="inputFormAction p5 formValue modifying w75" type="text" placeholder="Identifiant adhérent" name="identifiant" maxlength="12"/>
                    </div>
                    <button class="adminSuccess btnMedium">
                        Attribuer la fonction
                    </button>
                </div>
            </form>
        </div>

        <div>
            <table class="table styled-table">
                <thead>
                <tr>
                    <th>Personne</th>
                    <th>Identifiant</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @foreach($utilisateurs as $utilisateur)
                    <tr>
                        <td>{{ $utilisateur->personne->prenom.' '.$utilisateur->personne->nom }}</td>
                        <td>{{ $utilisateur->identifiant }}</td>
                        <td>
                            <a href="{{ route('admin.urs.fonctions.delete_attribution_multiple', [$fonction, $utilisateur]) }}" class="adminDanger btnSmall"
                               style="background-color: #c75f09" data-method="delete"
                               data-confirm="Voulez-vous vraiment supprimer l'attribution de cette fonction ?">
                                Supprimer l'attribution
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    </div>
@endsection
@section('css')
    <link href="{{asset('css/admin_fpf.css') }}" rel="stylesheet">
@endsection
