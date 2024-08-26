@extends('layouts.default')

@section('content')
    <div class="pageCanva">
        <h1 class="pageTitle">
            <div>
                Gestion Union Régionale - Formations
                <div class="urTitle">{{ $ur->nom }}</div>
            </div>
            <a class="previousPage" title="Retour page précédente" href="{{ route('urs.gestion') }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-reply-fill" viewBox="0 0 16 16">
                    <path d="M5.921 11.9 1.353 8.62a.719.719 0 0 1 0-1.238L5.921 4.1A.716.716 0 0 1 7 4.719V6c1.5 0 6 0 7 8-2.5-4.5-7-4-7-4v1.281c0 .56-.606.898-1.079.62z"/>
                </svg>
            </a>
        </h1>

        @if(sizeof($sessions) == 0)
            <div class="emptyList">Aucune session pour cette formation</div>
        @else
            <div class="alertInfo">
                <div>Si vous prenez financièrement en charge certaines des formations listées ci-dessous, attendez la confirmation de l'organisation de la session pour effectuer le paiement</div>
            </div>
            <table class="styled-table">
                <thead>
                <tr>
                    <th>Nom</th>
                    <th>Date de début</th>
                    <th>UR / Club</th>
                    <th>Type</th>
                    <th>Prise en charge</th>
                    <th>Prix adhérent</th>
                    <th>Places</th>
                    <th>Places en attente</th>
                    <th>Inscrits</th>
                    <th>Voir les inscrits</th>
                    <th>Paiement</th>
                </tr>
                </thead>
                <tbody>
                @foreach($sessions as $session)
                    <tr>
                        <td>{{ $session->formation->name }}</td>
                        <td>{{ substr($session->start_date, 8, 2).'/'.substr($session->start_date, 5, 2).'/'.substr($session->start_date, 0, 4) }}</td>
                        <td>
                            {{ $session->numero_club ?? 'UR' }}
                        </td>
                        <td>
                            @if($session->type == 0)
                                A distance
                            @elseif($session->type == 1)
                                Présentiel
                            @else
                                Les deux
                            @endif
                            @if($session->location)
                                <br>
                                {{ $session->location }}
                            @endif
                        </td>
                        <td>{{ $session->pec }} €</td>
                        <td>{{ $session->price }} €</td>
                        <td>{{ $session->places }}</td>
                        <td>{{ $session->waiting_places }}</td>
                        <td>
                            @if(sizeof($session->inscrits->where('status', 1)) > 0)
                                {{ sizeof($session->inscrits->where('status', 1)->where('attente', 0)) }} inscrits
                            @endif
                        </td>
                        <td>
                            @if(sizeof($session->inscrits->where('status', 1)) > 0)
                                <a href="{{ route('urs.sessions.inscrits', $session) }}" class="adminPrimary btnSmall">Voir les inscrits</a>
                            @endif
                        </td>
                        <td>
                            @if($session->pec > 0)
                                @if($session->paiement_status == 1)
                                    <span class="alertSuccess">Paiement effectué</span>
                                @else
                                    @if($session->attente_paiement == 1)
                                        <span class="alertWarning">En attente de paiement</span>
                                    @else
                                        <div style="display: flex; flex-direction: column; align-items: center; gap: 0.3rem;">
                                            <a class="adminPrimary btnSmall mr10" id="sessionPayVirement" data-ref="{{ $session->id }}">payer par virement</a>
                                            <a class="adminPrimary btnSmall" id="sessionPayCb" data-ref="{{ $session->id }}">payer par CB</a>
                                        </div>
                                    @endif
                                @endif
                            @endif
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
    <script src="{{ asset('js/sessions.js') }}"></script>
@endsection
