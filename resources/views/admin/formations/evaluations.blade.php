@extends('layouts.default')

@section('content')
    <div class="pageCanva">
        <h1 class="pageTitle">
            Liste des évaluations pour la formation {{ $formation->name }}
            <a class="previousPage" title="Retour page précédente" href="{{ route('formations.index') }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-reply-fill" viewBox="0 0 16 16">
                    <path d="M5.921 11.9 1.353 8.62a.719.719 0 0 1 0-1.238L5.921 4.1A.716.716 0 0 1 7 4.719V6c1.5 0 6 0 7 8-2.5-4.5-7-4-7-4v1.281c0 .56-.606.898-1.079.62z"/>
                </svg>
            </a>
        </h1>
        <div class="d-flex justify-end">
            <a id="generatePdfEvaluations" data-formation="{{ $formation->id }}" class="btnMedium ml10 adminPrimary">générer pdf évaluations</a>
        </div>
        <div>
            <table class="styled-table">
                <thead>
                <tr>
                    <th>Item</th>
                    <th>Nombre d'évaluations</th>
                    <th>Note moyenne</th>
                </tr>
                </thead>
                <tbody>
                @foreach($tab_evaluations as $evaluation)
                    <tr>
                        <td>{{ $evaluation['name'] }}</td>
                        <td>{{ $evaluation['nb'] }}</td>
                        <td>{{ $evaluation['note'] }} / 5</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            <div>
                <h3>Liste des commentaires saisis</h3>
                @foreach($tab_reviews as $review)
                    <div class="reviewStat">{{ $review }}</div>
                @endforeach
            </div>
        </div>
    </div>
    <div class="modalEdit d-none" id="modalEvaluationPdf">
        <div class="modalEditHeader">
            <div class="modalEditTitle">Génération du pdf des évaluations</div>
            <div class="modalEditClose">
                X
            </div>
        </div>
        <div class="modalEditBody">
            Le fichier a été généré avec succès. <a href="" id="linkEvaluationPdf" target="_blank">Cliquez ici pour le télécharger</a>.
        </div>
        <div class="modalEditFooter">
            <div class="adminDanger btnMedium mr10 modalEditClose">Fermer</div>
        </div>
    </div>
@endsection
@section('css')
    <link href="{{ asset('css/admin_fpf.css') }}" rel="stylesheet">
@endsection
@section('js')
    <script src="{{ asset('js/admin_formation.js') }}?t=<?= time() ?>"></script>
@endsection
