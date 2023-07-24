@extends('layouts.default')
@section('content')
    <div class="pageCanva">
        <h1 class="pageTitle">
            @switch($view_type)
                @case('recherche')
                Résultat de votre recherche pour
                <div>term</div>
                @break
                @default
                Espace de gestion des {{$view_type}} pour la FPF
            @endswitch
            <a class="previousPage" title="Retour page précédente" href="{{ route('personnes.index') }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor"
                     class="bi bi-reply-fill" viewBox="0 0 16 16">
                    <path
                        d="M5.921 11.9 1.353 8.62a.719.719 0 0 1 0-1.238L5.921 4.1A.716.716 0 0 1 7 4.719V6c1.5 0 6 0 7 8-2.5-4.5-7-4-7-4v1.281c0 .56-.606.898-1.079.62z"/>
                </svg>
            </a>
        </h1>
    </div>
    <div class="alertInfo" style="width: 80% !important">
        <span class="bold">Informations !</span>
    </div>

    @if($view_type == "adhérents")
        <div class="filters d-flex">
            <div class="formBlock" style="max-width: 100%">
                <div class="formBlockTitle">Filtres</div>
                <div class="d-flex flexWrap">
                    <div class="formUnit mb0">
                        <div class="formLabel mr10 bold">Statut :</div>
                        <select class="formValue modifying" name="filter" data-ref="statut">
                            <option value="all">Tous</option>
                            <option value="2" {{$statut == 2? "selected":""}}>Validés</option>
                            <option value="1" {{$statut == 1? "selected":""}}>Pré-inscrits</option>
                            <option value="0" {{$statut == 0? "selected":""}}>Non renouvelés</option>
                            <option value="3" {{$statut == 3? "selected":""}}>Carte éditée</option>
                            <option value="4" {{$statut == 4? "selected":""}}>Anciens</option>
                        </select>
                    </div>
                    <div class="formUnit mb0">
                        <div class="formLabel mr10 bold">Type carte :</div>
                        <select class="formValue modifying" name="filter" data-ref="abonnement">
                            <option value="all">Tous</option>

                        </select>
                    </div>
                    <div class="formUnit mb0">
                        <div class="formLabel mr10 bold">Type d'adhérent :</div>
                        <select class="formValue modifying" name="filter" data-ref="abonnement">
                            <option value="all">Tous</option>
                            <option value="">Individuel</option>
                            <option value="">Adhérent de club</option>

                        </select>
                    </div>
                </div>
            </div>
        </div>
    @endif
    @if(!sizeof($utilisateurs))
        Aucun résultat ne répond aux critères selectionnés.
    @else

        <table class="styled-table">
            <thead>
            <tr>

                <th>Nom</th>
                <th>Courriel</th>
                {{--            <th>Abonnement - N° fin</th>--}}
                @if($view_type == "adhérents")
                    <th>Identifiant</th>
                    <th>Statut</th>
                    <th>Type carte</th>
                    <th>Nom de club</th>
                @endif
                <th></th>
            </tr>
            </thead>
            <tbody>
            @foreach($utilisateurs as $utilisateur)
                <tr>

                    <td>{{$utilisateur->personne->nom}} {{$utilisateur->personne->prenom}} </td>
                    <td><a href="mailto:{{$utilisateur->personne->email}}">{{$utilisateur->personne->email}}</a></td>
                    {{--                <td>--}}
                    {{--                    {{ $utilisateur->fin?:"" }}--}}
                    {{--                </td>--}}
                    @if($view_type == "adhérents")
                        <td>{{$utilisateurs->identifiant}}</td>
                        <td>
                            @switch($utilisateur->statut)
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
                                    <div class="sticker green" title="Carte éditée"></div>
                                </div>
                                @break
                                @case(4)
                                <div class="d-flex">
                                    <div class="sticker" title="Carte non renouvelée depuis plus d'un an"></div>
                                </div>
                                @break
                                @default
                                <div>Non renseigné</div>
                            @endswitch
                        </td>
                        <td>
                            <select name="selectCt" id="selectCt_{{ $utilisateur->id_utilisateur }}"
                                    style="padding-left: 5px; font-size: small; width: 120px">
                                <option value="2" {{ $utilisateur->ct == 2 ? 'selected' : '' }}>>25 ans</option>
                                <option value="3" {{ $utilisateur->ct == 3 ? 'selected' : '' }}>18 - 25 ans</option>
                                <option value="4" {{ $utilisateur->ct == 4 ? 'selected' : '' }}><18 ans</option>
                                <option value="5" {{ $utilisateur->ct == 5 ? 'selected' : '' }}>famille</option>
                                <option value="6" {{ $utilisateur->ct == 6 ? 'selected' : '' }}>2eme club</option>
                            </select>
                        </td>
                        <td>{{$utilisateur->club->nom}}</td>
                    @endif
                    <td>
                        <div style="margin-bottom: 3px;">
                            <a href="" class="adminPrimary btnSmall">Editer</a>
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>


    @endif
@endsection

@section('js')
    {{--    <script src="{{ asset('js/filters-club-liste-adherent.js') }}?t=<?= time() ?>"></script>--}}
    {{--    <script src="{{ asset('js/excel_adherent_file.js') }}?t=<?= time() ?>"></script>--}}
@endsection
