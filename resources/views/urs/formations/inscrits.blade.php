@extends('layouts.default')

@section('content')
    <div class="pageCanva">
        <h1 class="pageTitle">
            Liste des inscrits pour la session de formation {{ $session->formation->name }}
            <a class="previousPage" title="Retour page précédente" href="{{ route('urs.formations') }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-reply-fill" viewBox="0 0 16 16">
                    <path d="M5.921 11.9 1.353 8.62a.719.719 0 0 1 0-1.238L5.921 4.1A.716.716 0 0 1 7 4.719V6c1.5 0 6 0 7 8-2.5-4.5-7-4-7-4v1.281c0 .56-.606.898-1.079.62z"/>
                </svg>
            </a>
        </h1>
        <div class="d-flex justify-center">
            <a href="{{ route('urs.inscrits.export', $session) }}" class="btnSmall adminSuccess ml10">exporter au format excel</a>
        </div>
        <table class="styled-table">
            <thead>
            <tr>
                <th>Personne</th>
                <th>Email</th>
                <th>Liste principale</th>
                <th>Liste attente</th>
                <th>Attente paiement</th>
            </thead>
            <tbody>
            @foreach($session->inscrits->where('status', 1) as $inscrit)
                <tr>
                    <td>
                        <div title="{{ $inscrit->is_federe ? 'fédéré' : 'non fédéré' }}" style="display: flex; justify-content: flex-start; align-items: center; gap: 5px; color: {{ $inscrit->is_federe ? 'green' : '#800' }}">
                            @if($inscrit->is_federe)
                                <div>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="green" class="bi bi-check-circle" viewBox="0 0 16 16">
                                        <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/>
                                        <path d="m10.97 4.97-.02.022-3.473 4.425-2.093-2.094a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-1.071-1.05"/>
                                    </svg>
                                </div>
                            @else
                                <div>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#800" class="bi bi-exclamation-triangle" viewBox="0 0 16 16">
                                        <path d="M7.938 2.016A.13.13 0 0 1 8.002 2a.13.13 0 0 1 .063.016.15.15 0 0 1 .054.057l6.857 11.667c.036.06.035.124.002.183a.2.2 0 0 1-.054.06.1.1 0 0 1-.066.017H1.146a.1.1 0 0 1-.066-.017.2.2 0 0 1-.054-.06.18.18 0 0 1 .002-.183L7.884 2.073a.15.15 0 0 1 .054-.057m1.044-.45a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767z"/>
                                        <path d="M7.002 12a1 1 0 1 1 2 0 1 1 0 0 1-2 0M7.1 5.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0z"/>
                                    </svg>
                                </div>
                            @endif
                            <div>
                                {{ $inscrit->personne->nom.' '.$inscrit->personne->prenom }}
                                @if($inscrit->utilisateur_id)
                                    <span>&nbsp;({{ $inscrit->utilisateur->identifiant }})</span>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td>{{ $inscrit->personne->email }}</td>
                    <td>{{ $inscrit->attente == 0 ? 'OUI' : '' }} {{ $inscrit->attente_paiement == 1 ? 'NON PAYEE' : '' }}</td>
                    <td>{{ $inscrit->attente == 1 ? 'OUI' : '' }}</td>
                    <td>
                        {{ $inscrit->attente_paiement == 1 ? 'LIEN TRANSMIS' : '' }}
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection
@section('css')
    <link href="{{ asset('css/admin_fpf.css') }}" rel="stylesheet">
    <link href="{{ asset('css/formations_fpf.css') }}" rel="stylesheet">
@endsection
@section('js')
    <script src="{{ asset('js/admin_inscriptions.js') }}?t=<?= time() ?>"></script>
@endsection
