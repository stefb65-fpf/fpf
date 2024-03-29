@extends('layouts.default')

@section('content')
    <div class="pageCanva">
        <h1 class="pageTitle">
            Gestion Fédérale - Unions régionales
            <a class="previousPage" title="Retour page précédente" href="{{ route('admin.structures') }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-reply-fill" viewBox="0 0 16 16">
                    <path d="M5.921 11.9 1.353 8.62a.719.719 0 0 1 0-1.238L5.921 4.1A.716.716 0 0 1 7 4.719V6c1.5 0 6 0 7 8-2.5-4.5-7-4-7-4v1.281c0 .56-.606.898-1.079.62z"/>
                </svg>
            </a>
        </h1>
        <div class="alertInfo w80">
            <span class="bold">Informations !</span>
            Ici vous avez la possibilité d'afficher la liste des différentes UR de France et de consulter leur informations.
            <br>
            Vous pouvez également modifier les informations de chacune et visualiser les fonctions s'y rapportant.
        </div>
        <div>
            <table class="styled-table">
                <thead>
                <tr>
                    <th>UR</th>
                    <th>Départements</th>
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
                        <td> @foreach($ur->departements as $departement)
                                <p>
                                    {{$departement->numero}} - {{$departement->nom}}
                                </p>
                            @endforeach</td>
                        <td><a href="mailto:{{$ur->courriel}}">{{$ur->courriel}}</a></td>
                        <td><a href="{{ $ur->web}}" target="_blank">{{$ur->web}}</a></td>
                        <td>
                            <div>{{$ur->adresse->libelle1}}</div>
                            <div>{{$ur->adresse->libelle2}}</div>
                            <div>{{ str_pad($ur->adresse->codepostal, 5, '0', STR_PAD_LEFT) }}</div>
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
                            <div class="mb3">
                                <a href="{{ route('urs.edit', $ur) }}" class="adminPrimary btnSmall">Modifier</a>
                            </div>
                            <div class="mb3">
                                <a href="{{ route('admin.urs.fonctions', $ur) }}" class="adminSuccess btnSmall">Fonctions</a>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
@section('css')
    <link href="{{ asset('css/admin_fpf.css') }}" rel="stylesheet">
@endsection
