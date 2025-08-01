@extends('layouts.default')

@section('content')
    <div class="pageCanva">
        <h1 class="pageTitle">
            Gestion Fédérale - Visualisation des avoirs en cours
            <a class="previousPage" title="Retour page précédente" href="{{ route('admin') }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-reply-fill" viewBox="0 0 16 16">
                    <path d="M5.921 11.9 1.353 8.62a.719.719 0 0 1 0-1.238L5.921 4.1A.716.716 0 0 1 7 4.719V6c1.5 0 6 0 7 8-2.5-4.5-7-4-7-4v1.281c0 .56-.606.898-1.079.62z"/>
                </svg>
            </a>
        </h1>
        @if(count($personnes) > 0)
        <div class="w100" style="margin-bottom: 50px">
            <h2>Liste des personnes pour lesquelles un avoir est en cours</h2>
            <table class="styled-table">
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Prénom</th>
                        <th>Email</th>
                        <th class="text-right">Montant de l'avoir</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($personnes as $personne)
                        <tr>
                            <td>{{ $personne->nom }}</td>
                            <td>{{ $personne->prenom }}</td>
                            <td>{{ $personne->email }}</td>
                            <td class="text-right">{{ number_format($personne->creance, 2, ',', ' ') }}€</td>
                            <td>
                                <a class="adminDanger btnSmall" name="remboursementAvoir" data-type="personne" data-ref="{{ $personne->id }}">
                                    Rembourser et supprimer la créance
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        @if(count($clubs) > 0)
            <div class="w100">
                <h2>Liste des clubs pour lesquels un avoir est en cours</h2>
                <table class="styled-table">
                    <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Numéro</th>
                        <th>UR</th>
                        <th class="text-right">Montant de l'avoir</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($clubs as $club)
                        <tr>
                            <td>{{ $club->nom }}</td>
                            <td>{{ $club->numero }}</td>
                            <td>{{ str_pad($club->urs_id, 2, '0', STR_PAD_LEFT) }}</td>
                            <td class="text-right">{{ number_format($club->creance, 2, ',', ' ') }}€</td>
                            <td>
                                <a class="adminDanger btnSmall" name="remboursementAvoir" data-type="club" data-ref="{{ $club->id }}">
                                    Rembourser et supprimer la créance
                                </a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <div class="modalEdit d-none" id="modalRemboursementAvoir">
        <div class="modalEditHeader">
            <div class="modalEditTitle">Annuler la créance et rembourser</div>
            <div class="modalEditCloseReload">
                X
            </div>
        </div>
        <div class="modalEditBody">
            Attention, cette action va annuler la créance et rembourser l'adhérent ou le club.
            Merci de confirmer l'action en cliquant sur le bouton "Valider" ci-dessous.
        </div>
        <div class="modalEditFooter">
            <div class="adminDanger btnMedium mr10 modalEditClose">Annuler</div>
            <div class="adminPrimary btnMedium mr10" id="btnValidateRemboursementAvoir">Valider</div>
        </div>
    </div>
@endsection

@section('css')
    <link href="{{ asset('css/admin_fpf.css') }}" rel="stylesheet">
@endsection
@section('js')
    <script src="{{ asset('js/admin_avoirs.js') }}?t=<?= time() ?>"></script>
@endsection
