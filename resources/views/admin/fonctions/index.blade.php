@extends('layouts.default')

@section('content')
    <div class="pageCanva">
        <h1 class="pageTitle">
            Espace de gestion des fonctions pour la FPF
            <a class="previousPage" title="Retour page précédente" href="{{ route('admin.structures') }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-reply-fill" viewBox="0 0 16 16">
                    <path d="M5.921 11.9 1.353 8.62a.719.719 0 0 1 0-1.238L5.921 4.1A.716.716 0 0 1 7 4.719V6c1.5 0 6 0 7 8-2.5-4.5-7-4-7-4v1.281c0 .56-.606.898-1.079.62z"/>
                </svg>
            </a>
        </h1>
        <div style="width: 100%; padding-top: 60px;" id="fonctions_federales">
            <div class="pageTitle">Fonctions fédérales <a href="#fonctions_regionales" style="font-size: 1rem; margin-left: 50px;">(fonctions régionales)</a></div>
            <div style="display: flex; justify-content: flex-end; margin-top: 20px; width: 100%">
                <a href="{{ route('fonctions.create') }}" class="adminPrimary btnMedium">Ajouter une fonction fédérale</a>
            </div>
        </div>

        <table class="styled-table">
            <thead>
            <tr>
                <th>Fonction fédérale</th>
                <th>Droits liés</th>
                <th>Adhérent</th>
                <th>Email fonction</th>
                <th>CE</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            @foreach($admin_fonctions as $fonction)
                <tr>
                    <td>{{ $fonction->libelle }}</td>
                    <td>
                        @foreach($fonction->droits as $droit)
                            {{ $droit->nom }}<br>
                        @endforeach
                    </td>
                    <td>{{ $fonction->utilisateur ? $fonction->utilisateur->personne->prenom.' '.$fonction->utilisateur->personne->nom.' ('.$fonction->utilisateur->identifiant.')' : '' }}</td>
                    <td>
                        <a href="mailto:{{ $fonction->courriel }}">{{ $fonction->courriel }}</a>
                    </td>
                    <td>
                        <input type="checkbox" name="ceFonction" {{ $fonction->ce == 1 ? 'checked' : '' }} data-ref="{{ $fonction->id }}">
                    </td>
                    <td>
                        <div style="margin-bottom: 3px;">
                            <a href="{{ route('fonctions.edit', $fonction) }}" class="adminPrimary btnSmall">Éditer</a>
                        </div>
                        <div style="margin-bottom: 3px;">
                            <a href="{{route('fonctions.destroy',$fonction)}}" class="adminDanger btnSmall"  data-method="delete"  data-confirm="Voulez-vous vraiment supprimer cette fonction ? Toutes les fonctionnalités liées seront supprimées de manière irréversible">Supprimer</a>
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

        <div style="width: 100%; padding-top: 60px;" id="fonctions_regionales">
            <div class="pageTitle">Fonctions régionales <a href="#fonctions_federales" style="font-size: 1rem; margin-left: 50px;">(fonctions fédérales)</a></div>
            <div style="display: flex; justify-content: flex-end; margin-top: 20px; width: 100%">
                <a href="{{ route('fonctions.create_ur') }}" class="adminPrimary btnMedium">Ajouter une fonction régionale</a>
            </div>
            <table class="styled-table">
                <thead>
                <tr>
                    <th>Fonction régionale</th>
                    <th>URs ayant déclaré la fonction</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @foreach($ur_fonctions as $fonction)
                    <tr>
                        <td>{{ $fonction->libelle }}</td>
                        <td>
                            <div>
                                <span>{{ sizeof($fonction->urs) }}</span> <span class="adminPrimary" style="font-size: 0.7rem" data-expand="0" name="toExpandUr">voir</span>
                            </div>
                            <div style="display: none; margin-top: 20px;" name="expandUr">
                                @if(sizeof($fonction->urs) == 0)
                                    Aucune UR
                                @else
                                    @foreach($fonction->urs as $ur)
                                        {{ $ur->nom }}<br>
                                    @endforeach
                                @endif

                            </div>
                        </td>
                        <td>
                            <div style="margin-bottom: 3px;">
                                <a href="{{ route('fonctions.edit_ur', $fonction) }}" class="adminPrimary btnSmall">Éditer</a>
                            </div>
                            <div style="margin-bottom: 3px;">
                                <a href="{{route('fonctions.destroy',$fonction)}}" class="adminDanger btnSmall"  data-method="delete"  data-confirm="Voulez-vous vraiment supprimer cette fonction ? Toutes les fonctionnalités liées seront supprimées de manière irréversible">Supprimer</a>
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
@section('js')
    <script src="{{ asset('js/admin_fonctions.js') }}?t=<?= time() ?>"></script>
@endsection
