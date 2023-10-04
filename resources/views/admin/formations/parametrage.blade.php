@extends('layouts.default')

@section('content')
    <div class="pageCanva">
        <h1 class="pageTitle">
            Gestion Fédérale - Formations - Paramétrage
            <a class="previousPage" title="Retour page précédente" href="{{ route('formations.admin_accueil') }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-reply-fill" viewBox="0 0 16 16">
                    <path d="M5.921 11.9 1.353 8.62a.719.719 0 0 1 0-1.238L5.921 4.1A.716.716 0 0 1 7 4.719V6c1.5 0 6 0 7 8-2.5-4.5-7-4-7-4v1.281c0 .56-.606.898-1.079.62z"/>
                </svg>
            </a>
        </h1>
        <div>
            <div class="d-flex">
                <h2>Catégories de formation</h2>
                <a href="{{ route('categoriesformations.create') }}" class="adminSuccess btnSmall ml50 mt5">Ajouter</a>
            </div>

            <table class="styled-table">
                <tbody>
                @foreach($categories as $categorie)
                    <tr>
                        <td>{{ $categorie->name }}</td>
                        <td>
                            <div class="d-flex">
                                <a href="{{ route('categoriesformations.edit', $categorie->id) }}" class="adminPrimary btnSmall">Modifier</a>
                                <a class="adminDanger btnSmall ml40" href="{{ route('categoriesformations.destroy', $categorie->id) }}" data-method="delete"  data-confirm="Voulez-vous vraiment cette catégorie ? Toutes les formations liées seront également supprimées.">Supprimer</a>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt60">
            <div class="d-flex">
                <h2>Items d'évaluation pour la formation</h2>
                <a href="{{ route('evaluationsthemes.create') }}" class="adminSuccess btnSmall ml50 mt5">Ajouter une catégorie</a>
            </div>
            <table class="styled-table">
                <thead>
                <tr>
                    <th>Catégorie</th>
                    <th>Elément d'évaluation</th>
                    <th>Type</th>
                    <th>Position</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @foreach($evalthemes as $evaltheme)
                    <tr>
                        <td>{{ $evaltheme->name }}</td>
                        <td></td>
                        <td></td>
                        <td>{{ $evaltheme->position }}</td>
                        <td>
                            <div class="d-flex">
                                <a href="{{ route('evaluationsthemes.edit', $evaltheme->id) }}" class="adminPrimary btnSmall">Modifier</a>
                                @if($evaltheme->id !== 3)
                                    <a href="{{ route('evaluationsitems.createForTheme', $evaltheme->id) }}" class="adminSuccess btnSmall ml40">Ajouter un item</a>
                                    <a class="adminDanger btnSmall ml40" href="{{ route('evaluationsthemes.destroy', $evaltheme->id) }}" data-method="delete"  data-confirm="Voulez-vous vraiment cette catégorie ? Toutes les formations liées seront également supprimées.">Supprimer</a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @foreach($evaltheme->evaluationsitems as $evalitem)
                        <tr>
                            <td></td>
                            <td>{{ $evalitem->name }}</td>
                            <td>{{ $evalitem->type == 0 ? "Saisie libre de texte" : "Notation" }}</td>
                            <td><span class="ml10">{{ $evalitem->position }}</span></td>
                            <td>
                                <div class="d-flex">
                                    <a href="{{ route('evaluationsitems.edit', $evalitem->id) }}" class="adminPrimary btnSmall">Modifier</a>
                                    @if($evalitem->type !== 0)
                                        <a class="adminDanger btnSmall ml40" href="{{ route('evaluationsitems.destroy', $evalitem->id) }}" data-method="delete"  data-confirm="Voulez-vous vraiment cette catégorie ? Toutes les formations liées seront également supprimées.">Supprimer</a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
@section('css')
    <link href="{{ asset('css/admin_fpf.css') }}" rel="stylesheet">
@endsection
