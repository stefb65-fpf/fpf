@extends('layouts.default')

@section('content')
    <div class="pageCanva">
        <h1 class="pageTitle">
            Liste des sessions pour la formation {{ $formation->name }}
            <a class="previousPage" title="Retour page précédente" href="{{ route('formations.index') }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-reply-fill" viewBox="0 0 16 16">
                    <path d="M5.921 11.9 1.353 8.62a.719.719 0 0 1 0-1.238L5.921 4.1A.716.716 0 0 1 7 4.719V6c1.5 0 6 0 7 8-2.5-4.5-7-4-7-4v1.281c0 .56-.606.898-1.079.62z"/>
                </svg>
            </a>
        </h1>
        <div class="d-flex justify-center">
            <a href="{{ route('sessions.create', $formation) }}" class="btnMedium adminPrimary">Ajouter une session</a>
        </div>
        @if(sizeof($formation->demandes) > 0)
            <div>
                Demandes d'organisation de session pour :
                <ul class="ml50">
                    @foreach($formation->demandes as $demande)
                        <li>
                            {{ $demande->club_id ? 'Club '.$demande->club->nom : 'UR '.$demande->ur->nom }}
                            @if($demande->pec > 0)
                                - Montant de la prise en charge {{ $demande->pec }} €
                                - Prix restant à charge des adhérents: {{ round(($formation->global_price - $demande->pec) / $formation->places, 2) }} €
                                (sur la base de {{ $formation->places }} places)
                            @endif
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif
        @if(sizeof($sessions) == 0)
            <div class="emptyList">Aucune session pour cette formation</div>
        @else
            <table class="styled-table">
                <thead>
                <tr>
                    <th>Date de début</th>
                    <th>Fin inscription</th>
                    <th>UR / Club</th>
                    <th>Type</th>
                    <th>Prix</th>
                    <th>Prise en charge</th>
                    <th>Places</th>
                    <th>Places en attente</th>
                    <th>Inscrits</th>
                    <th>Statut facturation</th>
                    <th>Paiement organisateur</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @foreach($sessions as $session)
                    <tr>
                        <td>{{ substr($session->start_date, 8, 2).'/'.substr($session->start_date, 5, 2).'/'.substr($session->start_date, 0, 4) }}</td>
                        <td>{{ substr($session->end_inscription, 8, 2).'/'.substr($session->end_inscription, 5, 2).'/'.substr($session->end_inscription, 0, 4) }}</td>
                        <td>
                            @if($session->ur_id)
                                UR {{ str_pad($session->ur_id, 2, '0', STR_PAD_LEFT) }}
                            @elseif($session->numero_club)
                                Club {{ $session->numero_club }}
                            @endif
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
                        <td>{{ $session->price }} €</td>
                        <td>{{ $session->pec }} €</td>
                        <td>{{ $session->places }}</td>
                        <td>{{ $session->waiting_places }}</td>
                        <td>
                            @if(sizeof($session->inscrits->where('status', 1)) > 0)
                                <a href="{{ route('inscrits.liste', $session) }}" class="btnSmall adminPrimary">{{ sizeof($session->inscrits->where('status', 1)->where('attente', 0)) }} inscrits - Gérer</a>
                            @endif
                        </td>
                        <td>
                            @switch($session->invoice_status)
                                @case(0)
                                Non facturée
                                @break
                                @case(1)
                                Facture transmise
                                @break
                                @case(2)
                                Facture payée
                                @break
                            @endswitch
                        </td>
                        <td>
                            @if($session->pec > 0)
                                @if($session->paiement_status == 1)
                                    <span class="alertSuccess">Paiement effectué</span>
                                @else
                                    @if($session->attente_paiement == 1)
                                        <span class="alertWarning">Paiement en cours</span>
                                    @else
                                        <span class="alertDanger">Paiement non effectué</span>
                                    @endif
                                @endif
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('sessions.edit', $session) }}" class="btnSmall adminPrimary">Modifier</a>
                            <a href="{{ route('sessions.destroy', $session) }}" data-method="delete" data-confirm="Voulez-vous vraiment supprimer cette session ?" class="btnSmall adminDanger mt5">Supprimer</a>
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
    <link href="{{ asset('css/formations_fpf.css') }}" rel="stylesheet">
@endsection
