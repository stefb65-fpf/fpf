@extends('layouts.default')

@section('content')
    <div class="pageCanva">
        <h1 class="pageTitle">
            Gestion Fédérale - Édition des cartes
            <a class="previousPage" title="Retour page précédente" href="{{ route('admin') }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-reply-fill" viewBox="0 0 16 16">
                    <path d="M5.921 11.9 1.353 8.62a.719.719 0 0 1 0-1.238L5.921 4.1A.716.716 0 0 1 7 4.719V6c1.5 0 6 0 7 8-2.5-4.5-7-4-7-4v1.281c0 .56-.606.898-1.079.62z"/>
                </svg>
            </a>
        </h1>
        @if(sizeof($utilisateurs) > 0)
            <h2>Liste des cartes à éditer</h2>
            <div class="d-flex justify-end w100">
                <a class="adminPrimary btnSmall" id="editerCartes">Editer les cartes</a>
            </div>
            <table class="styled-table">
                <thead>
                <tr>
                    <th>Personne</th>
                    <th>Identifiant</th>
                    <th>Email</th>
                    <th>Ur</th>
                    <th>Club</th>
                    <th>Carte / vignettes</th>
                </thead>
                <tbody>
                @foreach($utilisateurs as $utilisateur)
                    <tr>
                        <td>{{ $utilisateur->personne->nom.' '.$utilisateur->personne->prenom }}</td>
                        <td>{{ $utilisateur->identifiant }}</td>
                        <td>{{ $utilisateur->personne->email }}</td>
                        <td>{{ str_pad($utilisateur->urs_id, 2, '0', STR_PAD_LEFT) }}</td>
                        <td>{{ $utilisateur->clubs_id ? $utilisateur->club->nom : '' }}</td>
                        <td>{{ in_array($utilisateur->nb_cases_carte, [0,3]) ? 'carte' : 'vignette' }}</td>
                    </tr>
                @endforeach
                </tbody>

            </table>
        @endif
    </div>
    <div class="modalEdit d-none" id="modalCartesOk">
        <div class="modalEditHeader">
            <div class="modalEditTitle">Edition des cartes</div>
            <div class="modalEditCloseReload">
                X
            </div>
        </div>
        <div class="modalEditBody">
            <div class="alertSuccess mt10 mxauto mb0">
                Les cartes ont bien été éditées. Vous pouvez télécharger
                <ul id="listeCartesEditees" class="ml30"></ul>
            </div>
        </div>
        <div class="modalEditFooter">
            <div class="adminDanger btnMedium mr10 modalEditCloseReload">Fermer</div>
        </div>
    </div>
@endsection
@section('css')
    <link href="{{ asset('css/admin_fpf.css') }}" rel="stylesheet">
@endsection
@section('js')
    <script src="{{ asset('js/admin_reglements.js') }}?t=<?= time() ?>"></script>
@endsection
