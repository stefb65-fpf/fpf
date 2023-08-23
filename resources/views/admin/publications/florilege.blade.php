@extends('layouts.default')

@section('content')
    <div class="pageCanva">
        <h1 class="pageTitle">
            Souscriptions Florilège
            <a class="previousPage" title="Retour page précédente" href="{{ route('admin.gestion_publications') }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-reply-fill" viewBox="0 0 16 16">
                    <path d="M5.921 11.9 1.353 8.62a.719.719 0 0 1 0-1.238L5.921 4.1A.716.716 0 0 1 7 4.719V6c1.5 0 6 0 7 8-2.5-4.5-7-4-7-4v1.281c0 .56-.606.898-1.079.62z"/>
                </svg>
            </a>
        </h1>
        <div class="alertInfo w80">
            <p>
                <span class="bold">Informations !</span>
                {{ $nb_exemplaires }} numéro de Florilège commandés et payés
            </p>
        </div>
        <div id="alertFlorilege" class="alertSuccess d-none mb25 w80">
            Le fichier Excel a été généré avec succès. Vous pouvez le <a class="blue" id="linkAlertFlorilege"  target="_blank">télécharger</a>.
        </div>
        <div class="flexEnd">
            <button class="adminPrimary btnMedium mt10" type="text" id="btnSouscriptionsList">Liste des souscriptions au format Excel</button>
        </div>
        <div class="w100">
            <table class="styled-table">
                <thead>
                <tr>
                    <th>Personne / Club</th>
                    <th>Référence</th>
                    <th>Nb exemplaires</th>
                    <th>Montant payé</th>
                    <th>Date souscription</th>
                </tr>
                </thead>
                <tobdy>
                    @foreach($souscriptions as $souscription)
                        <tr>
                            <td>{{ $souscription->destinataire }}</td>
                            <td>{{ $souscription->reference }}</td>
                            <td>{{ $souscription->nbexemplaires }}</td>
                            <td class="text-right">{{ $souscription->montanttotal }}€</td>
                            <td>{{ $souscription->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                    @endforeach
                </tobdy>
            </table>
        </div>
        {{ $souscriptions->render( "pagination::default") }}
    </div>
@endsection
@section('css')
    <link href="{{ asset('css/admin_fpf.css') }}" rel="stylesheet">
@endsection
@section('js')
    <script src="{{ asset('js/admin_souscriptions.js') }}?t=<?= time() ?>"></script>
@endsection
