@extends('layouts.default')

@section('content')
    <div class="pageCanva">
        <h1 class="pageTitle">
            <div>
                Gestion Union Régionale - Fonctions
                <div class="urTitle">{{ $ur->nom }}</div>
            </div>
            <a class="previousPage" title="Retour page précédente" href="{{ route('urs.gestion') }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-reply-fill" viewBox="0 0 16 16">
                    <path d="M5.921 11.9 1.353 8.62a.719.719 0 0 1 0-1.238L5.921 4.1A.716.716 0 0 1 7 4.719V6c1.5 0 6 0 7 8-2.5-4.5-7-4-7-4v1.281c0 .56-.606.898-1.079.62z"/>
                </svg>
            </a>
        </h1>

        <div class="d-flex justify-end mt20 w100">
            <a href="{{ route('urs.fonctions.create') }}" class="adminPrimary btnMedium">Ajouter une fonction</a>
        </div>
        <table class="styled-table">
            <thead>
            <tr>
                <th>Fonction</th>
                <th>Type</th>
                <th>Adhérent</th>
                <th>Email</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            @foreach($fonctions as $fonction)
                <tr>
                    <td>{{ $fonction->libelle }}</td>
                    <td>{{ ($fonction->urs_id === 0) ? 'fonction FPF' : 'spécifique UR '.$ur->id }}</td>
                    <td>
                        @foreach($fonction->utilisateurs as $utilisateur)
                            {{ $utilisateur->personne->prenom.' '.$utilisateur->personne->nom.' ('.$utilisateur->identifiant.')' }}<br>
                        @endforeach
{{--                        {{ $fonction->utilisateur->personne->prenom.' '.$fonction->utilisateur->personne->nom.' ('.$fonction->utilisateur->identifiant.')' }}--}}
                    </td>
                    <td>
                        @foreach($fonction->utilisateurs as $utilisateur)
                            <a href="mailto:{{ $utilisateur->personne->email }}">{{ $utilisateur->personne->email }}</a><br>
                        @endforeach
{{--                        <a href="mailto:{{ $fonction->utilisateur->personne->email }}">{{ $fonction->utilisateur->personne->email }}</a>--}}
                    </td>
                    <td>
                        <div class="mb3">
                            @if($fonction->multiple == 0)
                                <a href="{{ route('urs.fonctions.change_attribution', $fonction) }}" class="adminPrimary btnSmall">Changer l'attribution</a>
                            @else
                                <a href="{{ route('urs.fonctions.manage_attribution', $fonction) }}" class="adminPrimary btnSmall">Gérer les attributions</a>
                            @endif
                        </div>
                        <div class="mb3">
                            @if($fonction->multiple == 0)
                                <a href="{{ route('urs.fonctions.delete_attribution', [$fonction, $fonction->utilisateurs[0]]) }}" class="adminDanger btnSmall"
                                   style="background-color: #c75f09" data-method="delete"
                                   data-confirm="Voulez-vous vraiment supprimer l'attribution de cette fonction ?">
                                    Supprimer l'attribution
                                </a>
                            @endif
                        </div>
                        @if($fonction->urs_id !== 0)
                            <div  class="mb3">
                                <a href="{{route('urs.fonctions.destroy',$fonction)}}" class="adminDanger btnSmall"  data-method="delete"  data-confirm="Voulez-vous vraiment supprimer cette fonction ? Toutes les fonctionnalités liées seront supprimées de manière irréversible">Supprimer la fonction</a>
                            </div>
                        @endif

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
