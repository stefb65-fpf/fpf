@extends('layouts.default')

@section('content')
    <div class="pageCanva">
        <h1 class="pageTitle">
            Bodereaux et règlements pour le club {{ $club->nom }}
            <a class="previousPage" title="Retour page précédente" href="{{ route('clubs.gestion') }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-reply-fill" viewBox="0 0 16 16">
                    <path d="M5.921 11.9 1.353 8.62a.719.719 0 0 1 0-1.238L5.921 4.1A.716.716 0 0 1 7 4.719V6c1.5 0 6 0 7 8-2.5-4.5-7-4-7-4v1.281c0 .56-.606.898-1.079.62z"/>
                </svg>
            </a>
        </h1>
        @if(sizeof($reglements) == 0)
            <div class="center">Aucun borderau de renouvellement n'a été généré pour le club actuellement</div>
        @else
            <table class="styled-table">
                <thead>
                <tr>
                    <th>Référence</th>
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
                        <td>{{ $reglement->statut === 0 ? 'En attente' : 'Traité' }}</td>
                        <td>{{ $reglement->created_at }}</td>
                        <td>{{ $reglement->montant }}€</td>
                        <td>{{ $reglement->dateenregistrement ?? '' }}</td>
                        <td>{{ $reglement->numerocheque ?? '' }}</td>
                        <td>
                            <div class="d-flex">
                            @if(file_exists($dir.'/'.$reglement->reference.'.pdf'))
                                <a class="adminPrimary btnSmall mr10" target="_blank" href="{{ $dir_club.'/'.$reglement->reference.'.pdf' }}">bordereau</a>
                            @endif
                            @if($reglement->statut === 0)
                                <a class="adminPrimary btnSmall mr10" id="clubPayVirement" data-ref="{{ $reglement->id }}">payer par virement</a>
                                <a class="adminPrimary btnSmall" id="clubPayCb" data-ref="{{ $reglement->id }}">payer par CB</a>
                            @endif
                            </div>
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
@section('js')
    <script src="{{ asset('js/club_paiement.js') }}?t=<?= time() ?>"></script>
@endsection
