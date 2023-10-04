@extends('layouts.default')

@section('content')
    <div class="pageCanva">
        <h1 class="pageTitle">
            Liste des formateurs pour la formation {{ $formation->name }}
            <a class="previousPage" title="Retour page précédente" href="{{ route('formations.index') }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-reply-fill" viewBox="0 0 16 16">
                    <path d="M5.921 11.9 1.353 8.62a.719.719 0 0 1 0-1.238L5.921 4.1A.716.716 0 0 1 7 4.719V6c1.5 0 6 0 7 8-2.5-4.5-7-4-7-4v1.281c0 .56-.606.898-1.079.62z"/>
                </svg>
            </a>
        </h1>

        <form method="POST" action="{{ route('formateurs.add', $formation) }}">
            {{ csrf_field() }}
            <div class="d-flex justify-center">
                    <select name="formateur_id">
                        <option value="-1"></option>
                        @foreach($formateurs as $formateur)
                            <option value="{{ $formateur->id }}">{{ strtoupper($formateur->nom).' '.$formateur->prenom }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="btnMedium adminPrimary ml5">Ajouter le formateur</button>
            </div>
        </form>
        @if(sizeof($formation->formateurs) == 0)
            <div class="emptyList text-center mt30">Aucun formateur pour cette formation</div>
        @else
            <table class="styled-table">
                <thead>
                <tr>
                    <th>Formateur</th>
                    <th>Email</th>
                    <th>Téléphone</th>
                    <th>Titre</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @foreach($formation->formateurs as $formateur)
                    <tr>
                        <td>{{ strtoupper($formateur->personne->nom).' '.$formateur->personne->prenom }}</td>
                        <td>{{ $formateur->personne->email }}</td>
                        <td>{{ $formateur->personne->phone_mobile }}</td>
                        <td>{{ $formateur->title }}</td>
                        <td>
                            <a href="{{ route('formateurs.remove', [$formation, $formateur]) }}" class="btnSmall adminDanger" data-method="delete" data-confirm="Voulez-vous enlever ce formateur pour la formation ?">Enlever</a>
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
