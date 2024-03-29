@extends('layouts.default')

@section('content')
    <div class="pageCanva">
        <h1 class="pageTitle">
            <div>
                Gestion Union Régionale - Liste clubs
                <div class="urTitle">{{ $ur->nom }}</div>
            </div>
            <a class="previousPage" title="Retour page précédente" href="{{ route('urs.gestion') }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor"
                     class="bi bi-reply-fill" viewBox="0 0 16 16">
                    <path
                        d="M5.921 11.9 1.353 8.62a.719.719 0 0 1 0-1.238L5.921 4.1A.716.716 0 0 1 7 4.719V6c1.5 0 6 0 7 8-2.5-4.5-7-4-7-4v1.281c0 .56-.606.898-1.079.62z"/>
                </svg>
            </a>
        </h1>
        <div class="alertInfo w80">
            <span class="bold">Informations !</span>
            Ici vous avez la possibilité d'afficher la liste des clubs de votre UR et de les filtrer en fonction de leur
            statut, de leur type de carte et de leur abonnement.<br>
        </div>
        <div class="filters d-flex">
            <div class="formBlock maxW100">
                <div class="formBlockTitle">Filtres</div>
                <div class="d-flex flexWrap">
                    <div class="formUnit mb0">
                        <div class="formLabel mr10 bold">Statut :</div>
                        <select class="formValue modifying" name="filter" data-ref="statut">
                            <option value="all">Tous</option>
                            <option value="2" {{$statut == 2 ? "selected" : ""}}>Validé</option>
                            <option value="1" {{$statut == 1 ? "selected" : ""}}>Pré-inscrit</option>
                            <option value="0" {{$statut == 0 ? "selected" : ""}}>Non renouvelé</option>
                            <option value="3" {{$statut == 3 ? "selected" : ""}}>Désactivé</option>
                        </select>
                    </div>
                    <div class="formUnit mb0">
                        <div class="formLabel mr10 bold">Type carte :</div>
                        <select class="formValue modifying" name="filter" data-ref="typeCarte">
                            <option value="all">Tous</option>
                            <option value="1" {{$type_carte == 1 ? "selected" : ""}}>Normaux</option>
                            <option value="N" {{$type_carte == "N" ? "selected" : ""}}>Nouveau</option>
                            <option value="C" {{$type_carte == "C" ? "selected" : ""}}>Tous adhérents</option>
                            <option value="A" {{$type_carte == "A" ? "selected" : ""}}>Tous abonnés</option>
                        </select>
                    </div>
                    <div class="formUnit mb0">
                        <div class="formLabel mr10 bold">Abonnement :</div>
                        <select class="formValue modifying" name="filter" data-ref="abonnement">
                            <option value="all">Tous</option>
                            <option value="1" {{$abonnement== 1 ? "selected" : ""}}>Avec</option>
                            <option value="0" {{$abonnement== 0 ? "selected" : ""}}>Sans</option>
                            {{--                            <option value="G" {{$abonnement== "G" ? "selected":""}}>Gratuits</option>--}}
                        </select>
                    </div>
                </div>
            </div>
        </div>
        @if($term)
            <div class="searchedTerm mt25">
                <div class="title mt25">Vous avez cherché les Clubs contenant l'expression :</div>
                <div class="d-flex mt25 center">
                    <div class="value">{{$term}}</div>
                    <div class="close">X</div>
                </div>
            </div>
        @endif

        @if(!sizeof($clubs))
            <div class="text-center w100">
                Aucun club ne correspond aux critères selectionnés. Changer la valeur des filtres ci-dessus.
            </div>

        @else
            <table class="styled-table">
                <thead>
                <tr>
                    <th>N°</th>
                    {{--                    <th>UR</th>--}}
                    <th>Nom</th>
                    <th>Statut</th>
                    <th>Courriel</th>
                    <th>Coordonnées</th>
                    <th>Contact</th>
                    <th>Abonnement - N° fin</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @foreach($clubs as $club)
                    <tr>
                        <td>{{ $club->numero }}</td>
                        {{--                        <td>{{$club->urs_id}}</td>--}}
                        <td>{{$club->nom}}</td>
                        <td>
                            @switch($club->statut)
                                @case(0)
                                <div class="d-flex">
                                    <div class="sticker orange" title="Non renouvelé"></div>

                                </div>
                                @break
                                @case(1)
                                <div class="d-flex">
                                    <div class="sticker yellow" title="Préinscrit"></div>

                                </div>
                                @break
                                @case(2)
                                <div class="d-flex">
                                    <div class="sticker green" title="Validé"></div>

                                </div>
                                @break
                                @case(3)
                                <div class="d-flex">
                                    <div class="sticker" title="Désactivé"></div>

                                </div>
                                @break
                                @default
                                <div>Non renseigné</div>
                            @endswitch
                        </td>
                        <td><a href="mailto:{{$club->courriel}}">{{$club->courriel}}</a></td>

                        <td>
                            <div>{{$club->adresse->libelle1}}</div>
                            <div>{{$club->adresse->libelle2}}</div>
                            <div>{{ str_pad($club->adresse->codepostal, 5, '0', STR_PAD_LEFT) }}</div>
                            <div>{{$club->adresse->ville}}</div>
                            <div>{{$club->adresse->pays}}</div>
                            <div><a href="tel:{{$club->adresse->callable_fixe}}">{{$club->adresse->visual_fixe}}</a>
                            </div>
                            <div><a href="tel:{{$club->adresse->callable_mobile}}">{{$club->adresse->visual_mobile}}</a>
                            </div>
                        </td>
                        <td>
                            @if($club->contact)
                                <div>{{ $club->contact->nom?:"" }}</div>
                                <div>{{ $club->contact->prenom?:"" }}</div>
                                <div>{{ $club->contact->identifiant?:"" }}</div>
                            @endif
                        </td>
                        <td>
                            {{ $club->numerofinabonnement && $club->numerofinabonnement >= $numeroencours ? $club->numerofinabonnement : "" }}
                        </td>
                        <td>
                            <div class="mb3">
                                {{--                                <a href="" class="adminPrimary btnSmall">Éditer</a>--}}
                                <a href="{{ route('UrGestion_updateClub', $club) }}" class="adminPrimary btnSmall">Éditer</a>
                            </div>
                            <div class="mb3">
                                <a href="{{route('urs.liste_adherents_club',$club)}}" class="adminSuccess btnSmall">Liste
                                    des adhérents</a>
                            </div>
                            <div class="mb3">
                                <a href="{{route('urs.clubs.liste_fonctions',$club)}}" class="adminDanger btnSmall">Liste
                                    des fonctions</a>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            <div class="pagination">
                {{ $clubs->render( "pagination::default") }}
            </div>
        @endif
    </div>
@endsection
@section('css')
    <link href="{{ asset('css/admin_fpf.css') }}" rel="stylesheet">
@endsection
@section('js')
    <script src="{{ asset('js/filters_club_ur.js') }}?t=<?= time() ?>"></script>
@endsection
