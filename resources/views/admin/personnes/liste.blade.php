@extends('layouts.default')
@section('content')
    <div class="pageCanva">
        <h1 class="pageTitle">
            @switch($view_type)
                @case('ur_adherents')
                <div>
                    Gestion Union Régionale - Adhérents
                    <div class="urTitle">{{ $ur->nom }}</div>
                </div>
                    <div class="currentUr d-none" id="currentUr">{{$ur->id}}</div>
                    <a class="previousPage" title="Retour page précédente" href="{{ route('urs.gestion') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor"
                             class="bi bi-reply-fill" viewBox="0 0 16 16">
                            <path
                                d="M5.921 11.9 1.353 8.62a.719.719 0 0 1 0-1.238L5.921 4.1A.716.716 0 0 1 7 4.719V6c1.5 0 6 0 7 8-2.5-4.5-7-4-7-4v1.281c0 .56-.606.898-1.079.62z"/>
                        </svg>
                    </a>
                @break
                @case('recherche')
                    Résultats pour votre recherche de personnes
                    @if($level == 'admin')
                        <a class="previousPage" title="Retour page précédente" href="{{ route('personnes.index') }}">
                            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor"
                                 class="bi bi-reply-fill" viewBox="0 0 16 16">
                                <path
                                    d="M5.921 11.9 1.353 8.62a.719.719 0 0 1 0-1.238L5.921 4.1A.716.716 0 0 1 7 4.719V6c1.5 0 6 0 7 8-2.5-4.5-7-4-7-4v1.281c0 .56-.606.898-1.079.62z"/>
                            </svg>
                        </a>
                    @else
                        <a class="previousPage" title="Retour page précédente" href="{{ route('urs.gestion') }}">
                            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor"
                                 class="bi bi-reply-fill" viewBox="0 0 16 16">
                                <path
                                    d="M5.921 11.9 1.353 8.62a.719.719 0 0 1 0-1.238L5.921 4.1A.716.716 0 0 1 7 4.719V6c1.5 0 6 0 7 8-2.5-4.5-7-4-7-4v1.281c0 .56-.606.898-1.079.62z"/>
                            </svg>
                        </a>
                    @endif
                @break
                @default
                Gestion Fédérale -  {{$view_type}}
                    <a class="previousPage" title="Retour page précédente" href="{{ route('personnes.index') }}">
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor"
                             class="bi bi-reply-fill" viewBox="0 0 16 16">
                            <path
                                d="M5.921 11.9 1.353 8.62a.719.719 0 0 1 0-1.238L5.921 4.1A.716.716 0 0 1 7 4.719V6c1.5 0 6 0 7 8-2.5-4.5-7-4-7-4v1.281c0 .56-.606.898-1.079.62z"/>
                        </svg>
                    </a>
            @endswitch

        </h1>
        @if($term)
            <div class="searchedTerm mt25">
                <div class="title mt25">Vous avez cherché les personnes contenant l'expression :</div>
                <div class="d-flex mt25 center">
                    <div class="value">{{$term}}</div>
                </div>
            </div>
        @endif
        @if($view_type == "adherents" || $view_type == "ur_adherents")
            <div class="filters d-flex">
                <div class="formBlock maxW100">
                    <div class="formBlockTitle">Filtres</div>
                    <div class="d-flex flexWrap">
                        @if($view_type == "adherents")
                            <div class="formUnit mb0">
                                <div class="formLabel mr10 bold">UR :</div>
                                <select class="formValue modifying" name="filter" id="urFilterPersonnes" data-ref="ur"
                                        required>
                                    <option value="all">Toutes</option>
                                    @foreach($urs as $ur)
                                        <option
                                            value="{{$ur->id}}" {{$ur_id == $ur->id? "selected":""}}>{{$ur->nom}}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                        <div class="formUnit mb0">
                            <div class="formLabel mr10 bold">Statut :</div>
                            <select class="formValue modifying" name="filter" id="statutFilterPersonnes"
                                    data-ref="statut">
                                <option value="all">Tous</option>
                                <option value="1" {{$statut == 1? "selected":""}}>Pré-inscrits</option>
                                <option value="2" {{$statut == 2? "selected":""}}>Validés</option>
                                <option value="3" {{$statut == 3? "selected":""}}>Carte éditée</option>
                                <option value="0" {{$statut == 0? "selected":""}}>Non renouvelés</option>
                                <option value="4" {{$statut == 4? "selected":""}}>Anciens</option>
                            </select>
                        </div>
                        <div class="formUnit mb0">
                            <div class="formLabel mr10 bold">Type carte :</div>
                            <select class="formValue modifying" name="filter" id="typeCarteFilterPersonnes"
                                    data-ref="typeCarte">
                                <option value="all">Tous</option>
                                <option value="2" {{ $type_carte == 2 ? 'selected' : '' }}>>25 ans</option>
                                <option value="3" {{  $type_carte == 3 ? 'selected' : '' }}>18 - 25 ans</option>
                                <option value="4" {{  $type_carte == 4 ? 'selected' : '' }}><18 ans</option>
                                <option value="5" {{  $type_carte == 5 ? 'selected' : '' }}>famille</option>
                                <option value="6" {{  $type_carte == 6 ? 'selected' : '' }}>2eme club</option>
                                <option value="7" {{  $type_carte == 7 ? 'selected' : '' }}>individuel >25ans</option>
                                <option value="8" {{  $type_carte == 8 ? 'selected' : '' }}> individuel 18-25</option>
                                <option value="9" {{  $type_carte == 9 ? 'selected' : '' }}> individuel <18ans</option>
                                <option value="F" {{  $type_carte == "F" ? 'selected' : '' }}>individuel Famille
                                </option>
                            </select>
                        </div>
                        <div class="formUnit mb0">
                            <div class="formLabel mr10 bold">Type d'adhérent :</div>
                            <select class="formValue modifying" name="filter" id="typeAdherentFilterPersonnes"
                                    data-ref="typeAdherent">
                                <option value="all">Tous</option>
                                <option value="1" {{ $type_adherent == 1 ? 'selected' : '' }}>Individuel</option>
                                <option value="2" {{ $type_adherent == 2 ? 'selected' : '' }}>Adhérent de club</option>
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
                    @if(in_array($view_type,["adherents","ur_adherents","recherche"]))
                        @if($view_type == "adherents")
                            <th>UR</th>
                        @endif
                        <th>Identifiant</th>
                        <th>Statut</th>
                        <th>Type carte</th>
                        @if($view_type != "recherche")
                            <th>Nom de club</th>
                        @endif
                    @endif
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @foreach($utilisateurs as $utilisateur)
                    <tr>

                        <td>{{$utilisateur->personne? $utilisateur->personne->nom:$utilisateur->nom}} {{$utilisateur->personne? $utilisateur->personne->prenom :$utilisateur->prenom}} </td>
                        <td>
                            <a href="mailto:{{$utilisateur->personne?$utilisateur->personne->email:$utilisateur->email}}">{{$utilisateur->personne?$utilisateur->personne->email:$utilisateur->email}}</a>
                        </td>

                        @if(in_array($view_type,["adherents","ur_adherents","recherche"]))
                            @if($view_type == "adherents")
                                <td>{{$utilisateur->urs_id ? $utilisateur->ur ->nom:""}}</td>
                            @endif
                            <td>{{$utilisateur->identifiant}}</td>
                            <td>
                                @switch($utilisateur->statut)
                                    @case(0)
                                        <div class="d-flex">
                                            <div class="sticker orange" title="Non renouvelé"></div>
                                        </div>
                                        @break
                                    @case(1)
                                        <div class="d-flex">
                                            <div class="sticker yellow" title="Pré-inscrit"></div>
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
                                @switch($utilisateur->ct)
                                    @case(2)
                                        <div>>25 ans</div>
                                        @break
                                    @case(3)
                                        <div>18 - 25 ans</div>
                                        @break
                                    @case(4)
                                        <div><18 ans</div>
                                        @break
                                    @case(5)
                                        <div>famille</div>
                                        @break
                                    @case(6)
                                        <div>2eme club</div>
                                        @break
                                    @case(7)
                                        <div>>25 ans</div>
                                        @break
                                    @case(8)
                                        <div>18 - 25 ans</div>
                                        @break
                                    @case(9)
                                        <div><18 ans</div>
                                        @break
                                    @case('F')
                                        <div>famille</div>
                                        @break
                                    @default
                                        <div></div>
                                @endswitch
                            </td>
                            @if($view_type != "recherche")
                                <td>{{ $utilisateur->club ? $utilisateur->club->nom : '' }}{{$utilisateur->clubs_id}}</td>
                            @endif
                        @endif
                        <td>
                            <div class="mb3">
                                @if($level == 'admin')
                                    @if (in_array($view_type, ["adherents", "recherche"]))
                                        <a href="{{ route('admin.personnes.edit', [$utilisateur->personne_id, 'adherents']) }}" class="adminPrimary btnSmall">Editer</a>
                                    @else
{{--                                        @if($view_type == "recherche")--}}
{{--                                            <a href="{{ route('admin.personnes.edit', [$utilisateur->personne_id, 'adherents']) }}" class="adminPrimary btnSmall">Editer</a>--}}
{{--                                        @else--}}
                                            <a href="{{ route('urs.personnes.edit', [$utilisateur->id, $view_type]) }}" class="adminPrimary btnSmall">Editer</a>
{{--                                        @endif--}}
                                    @endif
                                @else
                                    <a href="{{ route('urs.personnes.edit', [$utilisateur->personne_id, $view_type]) }}" class="adminPrimary btnSmall">Editer</a>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            <div class="pagination">
                {{ $utilisateurs->render( "pagination::default") }}
            </div>
        @endif
        <span class="d-none" id="viewType">{{ $view_type }}</span>
    </div>
@endsection
@section('css')
    <link href="{{ asset('css/admin_fpf.css') }}" rel="stylesheet">
@endsection
@section('js')
    <script src="{{ asset('js/filters_personnes.js') }}?t=<?= time() ?>"></script>
@endsection
