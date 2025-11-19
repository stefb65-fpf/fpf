@extends('layouts.default')

@section('content')
    <div class="pageCanva">
        <h1 class="pageTitle">
            Gestion Fédérale - Rapprochement comptable formations
            <a class="previousPage" title="Retour page précédente" href="{{ route('admin') }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-reply-fill" viewBox="0 0 16 16">
                    <path d="M5.921 11.9 1.353 8.62a.719.719 0 0 1 0-1.238L5.921 4.1A.716.716 0 0 1 7 4.719V6c1.5 0 6 0 7 8-2.5-4.5-7-4-7-4v1.281c0 .56-.606.898-1.079.62z"/>
                </svg>
            </a>
        </h1>

        <form method="GET" action="{{ route('admin.rapprochements.formations') }}" style="display: flex; justify-content: center; gap: 5px; margin-bottom: 20px;">
            <div style="position: relative">
                <input type="text" name="term" placeholder="Filtrer par référence exacte ou nom de la personne ou nom de la formation" value="{{ $term }}" style="padding: 5px;width: 550px;max-width: 90vw;" />
                @if($term != '')
                    <a href="{{ route('admin.rapprochements.formations') }}" style="position: absolute;right: 10px;top: 0;cursor: pointer;">x</a>
                @endif
            </div>
        </form>

        <div class="w100">
            <table class="styled-table">
                <thead>
                <tr>
                    <th>Personne</th>
                    <th>Session</th>
                    <th>Montant</th>
                    <th>Référence</th>
                    <th>Type paiement</th>
                </tr>
                </thead>
                <tbody>
                @foreach($inscrits as $inscrit)
                    <tr>
                        <td>
                            {{ $inscrit->personne->nom }} {{ $inscrit->personne->prenom }}
                        </td>
                        <td>
                            {{ $inscrit->session->formation->name }} -
                            session du {{ substr($inscrit->session->start_date, 8, 2).'/'.substr($inscrit->session->start_date, 5, 2).'/'.substr($inscrit->session->start_date, 0, 4) }}
                            @if($inscrit->session->end_date)
                                au {{ substr($inscrit->session->end_date, 8, 2).'/'.substr($inscrit->session->end_date, 5, 2).'/'.substr($inscrit->session->end_date, 0, 4) }}
                            @endif
                            @if($inscrit->session->location)
                                - {{ $inscrit->session->location }}
                            @endif
                        </td>
                        <td>{{ $inscrit->amount }} €</td>
                        <td>FORMATION-{{ $inscrit->personne->id }}-{{ $inscrit->session->id }}</td>
                        <td>
                            @if($inscrit->monext_token)
                                Payline
                            @else
                                Virement
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <div class="pagination">
            {{ $inscrits->render( "pagination::default") }}
        </div>

@endsection
@section('css')
    <link href="{{ asset('css/admin_fpf.css') }}" rel="stylesheet">
@endsection
