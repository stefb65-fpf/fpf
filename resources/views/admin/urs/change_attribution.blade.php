@extends('layouts.default')

@section('content')
    <div class="pageCanva">
        <h1 class="pageTitle">
            Changer l'attribution pour la fonction {{ $fonction->libelle }} pour l'UR {{ $ur->nom }}
            <a class="previousPage" title="Retour page précédente" href="{{ route('admin.urs.fonctions', $ur) }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-reply-fill" viewBox="0 0 16 16">
                    <path d="M5.921 11.9 1.353 8.62a.719.719 0 0 1 0-1.238L5.921 4.1A.716.716 0 0 1 7 4.719V6c1.5 0 6 0 7 8-2.5-4.5-7-4-7-4v1.281c0 .56-.606.898-1.079.62z"/>
                </svg>
            </a>
        </h1>
        <div class="alertInfo w80">
            <span class="bold">Informations !</span>
            Vous pouvez saisir un identifiant adhérent pour attribuer la fonction à un nouvel adhérent de votre UR.
        </div>
        <div class="formBlock">
            <div class="formBlockTitle">Gestion de fonctions régionales</div>
            <form class="w100" action="{{ route('admin.urs.fonctions.update', [$fonction, $ur->id]) }}" method="POST">
                {{ csrf_field() }}
                <div class="formBlockWrapper">
                    <div class="formUnit w100">
                        <div class="formLabel">Identifiant adhérent</div>
                        <input value="" class="inputFormAction p5 formValue modifying w75" type="text" placeholder="Identifiant adhérent" name="identifiant" maxlength="12"/>
                    </div>
                </div>
                <div class="d-flex justify-center">
                    <button class="adminSuccess btnMedium">
                        Attribuer la fonction
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
@section('css')
    <link href="{{asset('css/admin_fpf.css') }}" rel="stylesheet">
@endsection
