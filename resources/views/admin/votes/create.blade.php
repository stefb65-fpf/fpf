@extends('layouts.default')

@section('content')
    <div class="pageCanva">
        <h1 class="pageTitle">
            Ajout d'une session de vote
            <a class="previousPage" title="Retour page précédente" href="{{ route('votes.index') }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor"
                     class="bi bi-reply-fill" viewBox="0 0 16 16">
                    <path
                        d="M5.921 11.9 1.353 8.62a.719.719 0 0 1 0-1.238L5.921 4.1A.716.716 0 0 1 7 4.719V6c1.5 0 6 0 7 8-2.5-4.5-7-4-7-4v1.281c0 .56-.606.898-1.079.62z"/>
                </svg>
            </a>
        </h1>
        <div class="alertInfo" style="width: 80% !important">
            <span class="bold">Informations !</span>
            Vous allez ajouter une session de vote. Une fois fait, vous pourrez éditer cette session à partir de la liste, ajouter plusieurs élections et les hiérarchiser.
            Vous pouvez saisir plusieurs types de sessions:
            <ul style="margin-left: 30px">
                <li>une session classique, nationale ou régionale, lors de laquelle tout le public cible (adhérents, présidents de clubs ou présidents d'UR) peut voter en même temps</li>
                <li>une session 3 phases pour les votes de type motion AG nationale pour laquelle les votes vont s'enchaîner en focntion des catégories: adhérents puis responsables de club puis présidents d'UR</li>
            </ul>
        </div>
        @include('admin.votes.form', ['action' => 'store'])
    </div>
@endsection
@section('css')
    <link href="{{ asset('css/admin_fpf.css') }}" rel="stylesheet">
@endsection
@section('js')
    <script src="{{ asset('js/admin_votes.js') }}"></script>
@endsection
