@extends('layouts.default')

@section('content')
    <div class="pageCanva">
        <h1 class="pageTitle">
            Gestion Fédérale - Tableau de bord des formations
            <a class="previousPage" title="Retour page précédente" href="{{ route('formations.admin_accueil') }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-reply-fill" viewBox="0 0 16 16">
                    <path d="M5.921 11.9 1.353 8.62a.719.719 0 0 1 0-1.238L5.921 4.1A.716.716 0 0 1 7 4.719V6c1.5 0 6 0 7 8-2.5-4.5-7-4-7-4v1.281c0 .56-.606.898-1.079.62z"/>
                </svg>
            </a>
        </h1>

        <div>
            <table class="styled-dashboard">
                <thead>
                <tr>
                    <td>Formation / Session</td>
                    <td>UR / Club</td>
                    <td>Prix (membre / non membre)</td>
                    <td>Places (normales / en attente)</td>
                    <td>Inscrits (validés / attente)</td>
                    <td></td>
                </tr>
                </thead>
                <tbody>
                @foreach($formations as $formation)
                    <tr class="formation-dashboard ">
                        <td style="font-weight: bold">{{ $formation->name }}</td>
                        <td colspan="4"></td>
                        <td>
                            <a href="{{ route('formations.edit', $formation->id) }}" class="btnSmall adminPrimary" target="_blank">éditer la formation</a>
                            @if(sizeof($formation->sessions) == 0)
                                <a href="{{ route('formations.delete_dashboard', $formation->id) }}" data-method="delete"  data-confirm="Voulez-vous vraiment supprimer cette formation ?" class="btnSmall adminDanger mt5">supprimer la formation</a>
                            @endif
                        </td>
                    </tr>
                    @if(sizeof($formation->sessions) > 0)
                        @foreach($formation->sessions as $session)
                            <tr>
                                <td>
                                    session du {{ substr($session->start_date, 8, 2).'/'.substr($session->start_date, 5, 2).'/'.substr($session->start_date, 0, 4) }}
                                    @if($session->end_date)
                                        au {{ substr($session->end_date, 8, 2).'/'.substr($session->end_date, 5, 2).'/'.substr($session->end_date, 0, 4) }}
                                    @endif
                                    @if($session->location)
                                        - {{ $session->location }}
                                    @endif
                                    @if($session->type == 0)
                                        - à distance
                                    @elseif($session->type == 1)
                                        - présentiel
                                    @else
                                        - à distance et présentiel
                                    @endif
                                </td>
                                <td>
                                    @if($session->ur_id)
                                        UR {{ str_pad($session->ur_id, 2, '0', STR_PAD_LEFT) }}
                                    @elseif($session->club_id)
                                        Club {{ $session->numero_club }} - {{ $session->nom_club }}
                                    @endif
                                    @if($session->pec > 0)
                                        <br>
                                        PEC : {{ $session->pec }}€
                                    @endif
                                </td>
                                <td>{{ $session->price }}€ / {{ $session->price_not_member }}€</td>
                                <td>{{ $session->places }} / {{ $session->waiting_places }}</td>
                                <td>
                                    {{ $session->inscrits }} / {{ $session->inscrits_attente }}
                                </td>
                                <td>
                                    <a href="{{ route('sessions.edit', $session->id) }}" class="btnSmall adminWarning" target="_blank">éditer la session</a>
                                    @if($session->inscrits + $session->inscrits_attente > 0)
                                        <a href="{{ route('inscrits.liste', $session) }}" class="btnSmall adminYellow mt5" target="_blank">voir les inscrits</a>
                                    @else
                                        <a href="{{ route('sessions.delete_dashboard', $session) }}" data-method="delete" data-confirm="Voulez-vous vraiment supprimer cette session ?" class="btnSmall adminDanger mt5">supprimer la session</a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                @else
                        <tr>
                            <td colspan="3">Aucune session déclarée</td>
                        </tr>
                @endif
                @endforeach
            </table>
        </div>
    </div>
@endsection
@section('css')
    <link href="{{ asset('css/admin_fpf.css') }}" rel="stylesheet">
    <link href="{{ asset('css/formations_fpf.css') }}" rel="stylesheet">
@endsection
