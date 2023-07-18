@extends('layouts.default')

@section('content')
    <div class="pageCanva">
        <h1 class="pageTitle">
            Ajout d'une fonction pour l'UR {{ $ur->nom }}
            <a class="previousPage" title="Retour page précédente" href="{{ route('urs.fonctions.liste') }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-reply-fill" viewBox="0 0 16 16">
                    <path d="M5.921 11.9 1.353 8.62a.719.719 0 0 1 0-1.238L5.921 4.1A.716.716 0 0 1 7 4.719V6c1.5 0 6 0 7 8-2.5-4.5-7-4-7-4v1.281c0 .56-.606.898-1.079.62z"/>
                </svg>
            </a>
        </h1>

        <div class="formBlock">
            <div class="formBlockTitle">Gestion de fonctions régionales</div>
            <form action="{{ route('urs.fonctions.store') }}" method="POST" style="width: 100%">
                {{ csrf_field() }}
                <div class="formBlockWrapper inline">
                    <div class="formUnit">
                        <div class="formLabel" style="width: 300px">Fonction FPF non attribuée</div>
                        <select name="fonction_fpf">
                            <option value="0"></option>
                            @foreach($fonctions as $fonction)
                                <option value="{{ $fonction->id }}">{{ $fonction->libelle }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div style="font-weight: bolder">
                    OU
                </div>
                <div class="formBlockWrapper inline">
                    <div class="formUnit">
                        <div class="formLabel" style="width: 300px">Fonction spécifique à ajouter</div>
                        <input value="" class="inputFormAction" type="text" placeholder="Libellé de la fonction à ajouter" name="libelle" style="padding: 5px;width: 300px" />
                    </div>
                </div>
                <div class="formBlockWrapper inline">
                    <div class="formUnit">
                        <div class="formLabel" style="width: 300px">Identifiant adhérent</div>
                        <input value="" class="inputFormAction" type="text" placeholder="Identifiant adhérent" name="identifiant" maxlength="12" style="padding: 5px;width: 300px" />
                    </div>
                </div>
                <div style="display: flex; justify-content: center;">
                    <button class="adminSuccess btnMedium">
                        Ajouter la fonction
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
@section('css')
    <link href="{{asset('css/admin_fpf.css') }}" rel="stylesheet">
@endsection
