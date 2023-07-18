@extends('layouts.default')

@section('content')
<div class="pageCanva">
    <h1 class="pageTitle" data-club ={{$club->id}}>
        Liste des adhérents du club {{ $club->nom }}
        <a class="previousPage" title="Retour page précédente" href="{{ route('urs.liste_clubs') }}">
            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-reply-fill" viewBox="0 0 16 16">
                <path d="M5.921 11.9 1.353 8.62a.719.719 0 0 1 0-1.238L5.921 4.1A.716.716 0 0 1 7 4.719V6c1.5 0 6 0 7 8-2.5-4.5-7-4-7-4v1.281c0 .56-.606.898-1.079.62z"/>
            </svg>
        </a>
    </h1>
    <div class="filters d-flex">
        <div class="formBlock" style="max-width: 100%">
            <div class="formBlockTitle">Filtres</div>
            <div class="d-flex flexWrap">
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
                    <div class="formLabel mr10 bold">Abonnement :</div>
                    <select class="formValue modifying" name="filter" data-ref="abonnement">
                        <option value="all">Tous</option>
                        <option value="1" {{$abonnement== 1? "selected":""}}>Avec</option>
                        <option value="0" {{$abonnement== 0? "selected":""}}>Sans</option>
{{--                        <option value="G" {{$abonnement== "G"? "selected":""}}>Gratuits</option>--}}
                    </select>
                </div>
            </div>
        </div>
    </div>
    @if(!sizeof($adherents))
        Ce club ne possède aucun adhérent répondant aux critères selectionnés.
    @else

        <table class="styled-table">
            <thead>
            <tr>
                <th>N°carte</th>
                <th>Nom</th>
                <th>Statut</th>
                <th>Courriel</th>
                <th>Abonnement - N° fin</th>
                <th>Type carte</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            @foreach($adherents as $adherent)
                <tr>
                    <td>{{$adherent->identifiant}}</td>
                    <td>{{$adherent->nom}} {{$adherent->prenom}} </td>
                    <td>
                        @switch($adherent->statut)
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
                                <div class="sticker" title="Carte éditée"></div>
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
                    <td><a href="mailto:{{$adherent->courriel}}">{{$adherent->courriel}}</a></td>
                    <td>
                        {{ $adherent->numerofinabonnement?:"" }}
                    </td>
                    <td>
                        @switch($adherent->ct)
                            @case(2)
                            <div> > 25ans</div>
                            @break
                            @case(3)
                            <div>adhérent 18-25 ans</div>
                            @break
                            @case(4)
                           <div>adhérent<18ans</div>
                            @break
                            @case(5)
                            <div>adhérent famille</div>
                            @break
                            @case(6)
                            <div>adhérent 2eme club</div>
                            @break
                            @case(7)
                            <div>individuel > 25ans</div>
                            @break
                            @case(8)
                            <div>individuel 18-25 ans</div>
                            @break
                            @case(9)
                            <div>individuel < 18ans</div>
                            @break
                            @case("F")
                            <div>individuel famille</div>
                            @break
                            @default
                            <div>Non renseigné</div>
                        @endswitch
                    </td>
                    <td>
                        <div style="margin-bottom: 3px;">
                            <a href="" class="adminPrimary btnSmall">action</a>
                        </div>
                        <div style="margin-bottom: 3px;">
                            <a href="" class="adminSuccess btnSmall">action</a>
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <div class="pagination">
            @if(sizeof($adherents)>$limit_pagination)
                {{ $adherents->render( "pagination::default") }}
                 {{ $adherents->links() }}
            @endif
        </div>
    @endif
</div>
@endsection
@section('css')
    <link href="{{ asset('css/admin_fpf.css') }}" rel="stylesheet">
@endsection
@section('js')
    <script src="{{ asset('js/filters-club-liste-adherent.js') }}?t=<?= time() ?>"></script>
@endsection
