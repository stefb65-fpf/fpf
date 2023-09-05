@extends('layouts.default')

@section('content')
    <div class="pageCanva">
        <h1 class="pageTitle">
            Gestion Fédérale - Règlements
            <a class="previousPage" title="Retour page précédente" href="{{ route('admin') }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-reply-fill" viewBox="0 0 16 16">
                    <path d="M5.921 11.9 1.353 8.62a.719.719 0 0 1 0-1.238L5.921 4.1A.716.716 0 0 1 7 4.719V6c1.5 0 6 0 7 8-2.5-4.5-7-4-7-4v1.281c0 .56-.606.898-1.079.62z"/>
                </svg>
            </a>
        </h1>
        @if($term)
            <div class="searchedTerm mt25">
                <div class="title mt25">Vous avez cherché les règlements contenant l'expression :</div>
                <div class="d-flex mt25 center">
                    <div class="value">{{$term}}</div>
                    <div class="close">X</div>
                </div>
            </div>
        @endif
@if(sizeof($reglements))
        <div class="w100">
            <div class="pagination">
                {{ $reglements->render( "pagination::default") }}
            </div>
            <table class="styled-table">
                <thead>
                <tr>
                    <th>Référence</th>
                    <th>Club / Adhérent</th>
                    <th>Statut</th>
                    <th>Date de création</th>
                    <th>Montant</th>
                    <th>Date de validation FPF</th>
                    <th>Référence paiement</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @foreach($reglements as $reglement)
                    <tr>
                        <td>{{ $reglement->reference }}</td>
                        <td>{{ $reglement->clubs_id ? 'Club: '.$reglement->nom_club : $reglement->nom }}</td>
                        <td>{{ $reglement->statut === 0 ? 'En attente' : 'Traité' }}</td>
                        <td>{{ $reglement->created_at }}</td>
                        <td>{{ $reglement->montant }}€</td>
                        <td>{{ $reglement->dateenregistrement ?? '' }}</td>
                        <td>{{ $reglement->numerocheque ?? '' }}</td>
                        <td>
                            @if($reglement->bordereau)
                                <div class="mb3">
                                    <a class="adminPrimary btnSmall" target="_blank" href="{{ $reglement->bordereau }}">bordereau</a>
                                </div>
                            @endif
                            @if($reglement->statut === 0)
                                <div class="mb3">
                                    <a class="adminSuccess btnSmall" target="_blank" name="validerReglement" data-id="{{ $reglement->id }}" data-reference="{{ $reglement->reference }}" data-montant="{{ $reglement->montant }}">valider</a>
                                </div>
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>

        </div>
        <div class="pagination">
            {{ $reglements->render( "pagination::default") }}
        </div>
        @else
<div class="mt25">Nous n'avons pas trouvé de résultat correspondant à votre recherche de règlements.</div>
        @endif
    </div>
    <div class="modalEdit d-none" id="modalValidationRenouvellement">
        <div class="modalEditHeader">
            <div class="modalEditTitle">Renouvellement des adhésions et abonnements</div>
            <div class="modalEditCloseReload">
                X
            </div>
        </div>
        <div class="modalEditBody">
            Le règlement ayant pour référence <span id="referenceValidationRenouvellement"></span> est exigé pour un montant <span id="montantValidationRenouvellement"></span>€.<br>
            <br>
            Saisir les information de règlement dans le champ ci-dessous. Ne validez que si le montant correspond à celui attendu.<br>
            <br>
            <div class="text-center">
                <label for="infoValidationRenouvellement">Informations de règlement</label>
                <input type="text" id="infoValidationRenouvellement">
            </div>
        </div>
        <div class="modalEditFooter">
            <div class="adminDanger btnMedium mr10 modalEditClose">Fermer</div>
            <div class="adminPrimary btnMedium mr10" id="validRenouvellement" data-id="">Valider le règlement</div>
        </div>
    </div>

    <div class="modalEdit d-none" id="modalReglementOk">
        <div class="modalEditHeader">
            <div class="modalEditTitle">Renouvellement des adhésions et abonnements</div>
            <div class="modalEditCloseReload">
                X
            </div>
        </div>
        <div class="modalEditBody">
            <div class="alertSuccess mt10 mb0 mxauto" >
                Le règlement a bien pris en compte
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
