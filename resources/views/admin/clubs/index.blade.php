@extends('layouts.default')

@section('content')
    <div class="pageCanva">
        <h1 class="pageTitle">
            Espace de gestion des clubs pour la FPF
            <a class="previousPage" title="Retour page précédente" href="{{ route('admin.structures') }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor"
                     class="bi bi-reply-fill" viewBox="0 0 16 16">
                    <path
                        d="M5.921 11.9 1.353 8.62a.719.719 0 0 1 0-1.238L5.921 4.1A.716.716 0 0 1 7 4.719V6c1.5 0 6 0 7 8-2.5-4.5-7-4-7-4v1.281c0 .56-.606.898-1.079.62z"/>
                </svg>
            </a>
        </h1>
        <div class="filters d-flex">
            <div class="formBlock" style="max-width: 100%">
                <div class="formBlockTitle">Filtres</div>
                <div class="d-flex flexWrap">
                    <div class="formUnit mb0">
                        <div class="formLabel mr10 bold">UR :</div>
                        <select class="formValue modifying" name="filter" data-ref="ur" required>
                            <option value="all">Toutes</option>
                            @foreach($urs as $ur)
                                @if($ur)
                                    <option
                                        value="{{$ur->id}}" {{$ur_id == $ur->id? "selected":""}}>{{$ur->nom}}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="formUnit mb0">
                        <div class="formLabel mr10 bold">Statut :</div>
                        <select class="formValue modifying" name="filter" data-ref="statut">
                            <option value="all">Tous</option>
                            <option value="2" {{$statut == 2? "selected":""}}>Validé</option>
                            <option value="1" {{$statut == 1? "selected":""}}>Pré-inscrit</option>
                            <option value="0" {{$statut == 0? "selected":""}}>Non renouvelé</option>
                            <option value="3" {{$statut == 3? "selected":""}}>Désactivé</option>
                        </select>
                    </div>
                    <div class="formUnit mb0">
                        <div class="formLabel mr10 bold">Type carte :</div>
                        <select class="formValue modifying" name="filter" data-ref="typeCarte">
                            <option value="all">Tous</option>
                            <option value="1" {{$type_carte == 1? "selected":""}}>Normaux</option>
                            <option value="N" {{$type_carte == "N"? "selected":""}}>Nouveau</option>
                            <option value="C" {{$type_carte == "C"? "selected":""}}>Tous adhérents</option>
                            <option value="A" {{$type_carte == "A"? "selected":""}}>Tous abonnés</option>
                        </select>
                    </div>
                    <div class="formUnit mb0">
                        <div class="formLabel mr10 bold">Abonnement :</div>
                        <select class="formValue modifying" name="filter" data-ref="abonnement">
                            <option value="all">Tous</option>
                            <option value="1" {{$abonnement== 1? "selected":""}}>Avec</option>
                            <option value="0" {{$abonnement== 0? "selected":""}}>Sans</option>
                            <option value="G" {{$abonnement== "G"? "selected":""}}>Gratuits</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="mt25 flexEnd">
            <a href="" class="adminPrimary btnMedium">Ajouter un club</a>
            {{--            <a href="{{ route('votes.create') }}" class="adminPrimary btnMedium">Créer une nouvelle session de votes</a>--}}
        </div>
        <div class="filters d-flex"></div>
        @if(!sizeof($clubs))
            Aucun club ne correspond aux critères selectionnés. Changer la valeur des filtres ci-dessus.
        @else
            <table class="styled-table">
                <thead>
                <tr>
                    <th>N°</th>
                    <th>UR</th>
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
                        <td>{{$club->urs_id}}</td>
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
                            <div>{{$club->adresse->codepostal}}</div>
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
                            {{ $club->numerofinabonnement }}
                        </td>
                        <td>
                            <div style="margin-bottom: 3px;">
                                <a href="" class="adminPrimary btnSmall">Éditer</a>
                            </div>
                            <div style="margin-bottom: 3px;">
                                <a href="" class="adminSuccess btnSmall">Liste des adhérents</a>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        <div class="pagination">
        @if(sizeof($clubs)>1)
            {{ $clubs->render( "pagination::default") }}
            {{-- {{ $clubs->links() }} --}}
            @endif
        </div>

        @endif


        {{--        <div class="alertDanger" style="width: 80% !important">--}}
        {{--            <p>--}}
        {{--                <span class="bold">Attention !</span>--}}
        {{--                Cette page est en cours de développement. Elle n'est pas encore fonctionnelle.--}}
        {{--            </p>--}}
        {{--            <p style="margin-top: 20px">--}}
        {{--                On affiche ici la liste des clubs, triés par numéro, avec pagination.<br>--}}
        {{--                Pour chaque club, possibilité de modifier les informations du club comme le ferait un responsable--}}
        {{--                club.<br>--}}
        {{--                Possibilité d'ajout de clubs (attention, il est nécessaire de saisir un contact lors de l'ajout du--}}
        {{--                club).<br>--}}
        {{--                Filtre à mettre en place pour l'affichage des clubs (par UR, statut, type de carte, abonnements).<br>--}}
        {{--            </p>--}}
        {{--        </div>--}}
        {{--        <div class="alertDanger" style="width: 80% !important">--}}
        {{--            <div class="bold">on récupère la liste des clubs dans $clubs</div>--}}
        {{--            <div>pour chaque club, on affiche une ligne avec les informations numéro (complémenté à 4), UR (complémenté--}}
        {{--                à 2), nom, email si présent avec lien mailto,--}}
        {{--                un petit bloc coordonnées + telephone ($club->adresse), nom, prénom du contact ($club->contact) plus--}}
        {{--                deux actions Editer et listes des adhérents--}}
        {{--            </div>--}}
        {{--            <div>On prévoir également sur la ligne d'afficher le statut de manière visuelle (petit rond de couleur):--}}
        {{--                orange (0 non renouvelé), jaune (1 pré inscrit), vert (2 validé) et gris (3 désactivés)--}}
        {{--            </div>--}}
        {{--        </div>--}}
        {{--        <div class="alertDanger" style="width: 80% !important">--}}
        {{--            <div class="bold">on prévoit un système de filtre avec plusieurs possibilité de filtrer l'affichage par ur,--}}
        {{--                par statut, par type--}}
        {{--            </div>--}}
        {{--        </div>--}}
        {{--        <div class="alertDanger" style="width: 80% !important">--}}
        {{--            <div class="bold">il faut prévoir de pouvoir ajouter un club don un botuon / icône bien visible (avec--}}
        {{--                redirection vers formulaire d'ajout ensuite)--}}
        {{--            </div>--}}
        {{--        </div>--}}

    </div>
@endsection
@section('css')
    <link href="{{ asset('css/admin_fpf.css') }}" rel="stylesheet">
@endsection
@section('js')
    <script src="{{ asset('js/filters.js') }}?t=<?= time() ?>"></script>
@endsection
