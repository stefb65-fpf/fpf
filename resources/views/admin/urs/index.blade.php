@extends('layouts.default')

@section('content')
    <div class="pageCanva">
        <h1 class="pageTitle">
            Espace de gestion des urs pour la FPF
            <a class="previousPage" title="Retour page précédente" href="{{ route('admin.structures') }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-reply-fill" viewBox="0 0 16 16">
                    <path d="M5.921 11.9 1.353 8.62a.719.719 0 0 1 0-1.238L5.921 4.1A.716.716 0 0 1 7 4.719V6c1.5 0 6 0 7 8-2.5-4.5-7-4-7-4v1.281c0 .56-.606.898-1.079.62z"/>
                </svg>
            </a>
        </h1>
        <div>
            <table class="styled-table">
                <thead>
                <tr>
                    <th>UR</th>
                    <th>Courriel</th>
                    <th>Site web</th>
                    <th>Coordonnées</th>
                    <th>Président</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @foreach($urs as $ur)
                    <tr>
                        <td>{{ $ur->nom }}</td>
                        <td><a href="mailto:{{$ur->courriel}}">{{$ur->courriel}}</a></td>
                        <td><a href="{{ $ur->web}}" target="_blank">{{$ur->web}}</a></td>
                        <td>
                            <div>{{$ur->adresse->libelle1}}</div>
                            <div>{{$ur->adresse->libelle2}}</div>
                            <div>{{$ur->adresse->codepostal}}</div>
                            <div>{{$ur->adresse->ville}}</div>
                            <div>{{$ur->adresse->pays}}</div>
                            <div><a href="tel:{{$ur->adresse->callable_fixe}}">{{$ur->adresse->visual_fixe}}</a></div>
                            <div><a href="tel:{{$ur->adresse->callable_mobile}}">{{$ur->adresse->visual_mobile}}</a></div>
                        </td>
                        <td>
                            <div>{{ $ur->president->nom }}</div>
                            <div>{{ $ur->president->prenom }}</div>
                            <div>{{ $ur->president->identifiant }}</div>
                        </td>
                        <td>
                            <div style="margin-bottom: 3px;">
                                <a href="{{ route('urs.edit', $ur) }}" class="adminPrimary btnSmall">Modifier</a>
                            </div>
                            <div style="margin-bottom: 3px;">
                                <a href="{{ route('urs.edit', $ur) }}" class="adminSuccess btnSmall">Fonctions</a>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <div class="alertDanger" style="width: 80% !important">
            <p>
                <span class="bold">Attention !</span>
                Cette page est en cours de développement. Elle n'est pas encore fonctionnelle.
            </p>
            <p style="margin-top: 20px">
                On affiche ici la liste des 25 urs avec la possibilité de modifier les informations UR et les fonctions comme le ferait un administrateur de l'UR.<br>
                Pas de possibilité d'ajout ou de suppression d'urs.<br>
            </p>
        </div>
        <div class="alertDanger" style="width: 80% !important">
            <div class="bold">on récupère la liste des urs dans $urs</div>
            <div>pour chaque ur, on affiche les informations nom UR, email avec lien mailto, site web avec lien,
                un petit bloc coordonnées + telephone ($ur->adresse), nom, prénom et identifiant du président ($ur->president) plus deux actions Editer et Fonctions</div>
        </div>
{{--        @foreach($urs as $ur)--}}
{{--            {{$ur->president?$ur->president->nom:""}}--}}
{{--            {{$ur->adresse}}--}}
{{--            {{$ur}}--}}
{{--        @endforeach--}}
{{--        <a data-method="delete"  data-confirm="Voulez-vous vraiment supprimer bla bla ?">test delete HELLEBORE</a>--}}
    </div>
@endsection
@section('css')
    <link href="{{ asset('css/admin_fpf.css') }}" rel="stylesheet">
@endsection
