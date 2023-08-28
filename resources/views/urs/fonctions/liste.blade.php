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
        <div class="alertDanger w80">
            <p>
                <span class="bold">Attention !</span>
                Cette page est en cours de développement. Elle n'est pas encore fonctionnelle.
            </p>
            <p class="mt20">
                On affiche ici la liste des fonctions de l'UR, la possibilité d'ajouter une fonction spécifique à l'UR,
                de lier un adhérent de l'UR à la fonction, de supprimer la liaison adhérent / fonction, de supprimer une fonction si elle spécifique à l'UR
            </p>
        </div>

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
                    <td>{{ $fonction->utilisateur->personne->prenom.' '.$fonction->utilisateur->personne->nom.' ('.$fonction->utilisateur->identifiant.')' }}</td>
                    <td><a href="mailto:{{ $fonction->utilisateur->personne->email }}">{{ $fonction->utilisateur->personne->email }}</a></td>
                    <td>
                        <div class="mb3">
                            <a href="{{ route('urs.fonctions.change_attribution', $fonction) }}" class="adminPrimary btnSmall">Changer l'attribution</a>
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
