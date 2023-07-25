@extends('layouts.default')

@section('content')
    <div class="pageCanva">
        <h1 class="pageTitle">
            Espace de gestion des personnes
            <a class="previousPage" title="Retour page précédente" href="{{ route('admin') }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-reply-fill" viewBox="0 0 16 16">
                    <path d="M5.921 11.9 1.353 8.62a.719.719 0 0 1 0-1.238L5.921 4.1A.716.716 0 0 1 7 4.719V6c1.5 0 6 0 7 8-2.5-4.5-7-4-7-4v1.281c0 .56-.606.898-1.079.62z"/>
                </svg>
            </a>
        </h1>
        <div class="cardContainer">
            <a class="card" href="/admin/personnes/adherents">
                <div class="wrapper">
                    <div class="cardTitle">Adhérents</div>
                </div>
            </a>
            <a class="card" href="/admin/personnes/abonnes">
{{--            <a class="card" href="{{ route('personnes.liste_abonnes') }}">--}}
                <div class="wrapper">
                    <div class="cardTitle">Abonnés</div>
                </div>
            </a>
            <a class="card" href="/admin/personnes/formateurs">
{{--            <a class="card" href="{{ route('personnes.liste_formateurs') }}">--}}
                <div class="wrapper">
                    <div class="cardTitle">Formateurs</div>
                </div>
            </a>
        </div>
    </div>
@endsection
