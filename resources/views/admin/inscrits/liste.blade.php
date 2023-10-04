@extends('layouts.default')

@section('content')
    <div class="pageCanva">
        <h1 class="pageTitle">
            Liste des inscrits pour la session de formation {{ $session->formation->name }}
            <a class="previousPage" title="Retour page précédente" href="{{ route('sessions.index', $session->formation) }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-reply-fill" viewBox="0 0 16 16">
                    <path d="M5.921 11.9 1.353 8.62a.719.719 0 0 1 0-1.238L5.921 4.1A.716.716 0 0 1 7 4.719V6c1.5 0 6 0 7 8-2.5-4.5-7-4-7-4v1.281c0 .56-.606.898-1.079.62z"/>
                </svg>
            </a>
        </h1>
        <table class="styled-table">
            <thead>
            <tr>
                <th>Personne</th>
                <th>Email</th>
                <th>Liste principale</th>
                <th>Liste attente</th>
                <th>Attente paiement</th>
                <th></th>
            </thead>
            <tbody>
            @foreach($session->inscrits->where('status', 1) as $inscrit)
                <tr>
                    <td>{{ $inscrit->personne->nom.' '.$inscrit->personne->prenom }}</td>
                    <td>{{ $inscrit->personne->email }}</td>
                    <td>{{ $inscrit->attente == 0 ? 'OUI' : '' }} {{ $inscrit->attente_paiement == 1 ? 'NON PAYEE' : '' }}</td>
                    <td>{{ $inscrit->attente == 1 ? 'OUI' : '' }}</td>
                    <td>
                        {{ $inscrit->attente_paiement == 1 ? 'LIEN TRANSMIS' : '' }}
                    </td>
                    <td>
                        @if($inscrit->attente == 0)
                            <a href="{{ route('inscrits.destroy', $inscrit) }}" data-method="delete" data-confirm="Voulez-vous vraiment supprimer cet inscrit ?" class="btnSmall adminDanger">Supprimer</a>
                        @endif
                        @if($inscrit->attente == 1)
                            <a href="" class="btnSmall adminPrimary">Envoyer lien paiement</a>
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
    <link href="{{ asset('css/formations_fpf.css') }}" rel="stylesheet">
@endsection