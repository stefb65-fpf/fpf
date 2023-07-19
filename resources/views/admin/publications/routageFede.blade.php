@extends('layouts.default')

@section('content')
    <div class="pageCanva">
        <h1 class="pageTitle">
            Autres routages
            <a class="previousPage" title="Retour page précédente" href="{{ route('admin.gestion_publications') }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-reply-fill" viewBox="0 0 16 16">
                    <path d="M5.921 11.9 1.353 8.62a.719.719 0 0 1 0-1.238L5.921 4.1A.716.716 0 0 1 7 4.719V6c1.5 0 6 0 7 8-2.5-4.5-7-4-7-4v1.281c0 .56-.606.898-1.079.62z"/>
                </svg>
            </a>
        </h1>
        <div class="alertInfo" style="width: 80% !important">
            <p>
                <span class="bold">Informations !</span>
                Vous pouvez sélectionner un ou plusieurs groupes d'utilisateurs pour générer un fichier de routage
            </p>
        </div>

        <div class="alertSuccess" style="width: 80% !important; display: none;" id="alertFedeRoutage">
            Le fichier de routage a bien été généré. Vous pouvez le télécharger en cliquant sur le lient suivant: <a id="linkFedeRoutage" target="_blank" style="cursor: pointer">Télécharger le fichier</a>
        </div>

        <table class="styled-table">
            <thead>
            <tr>
                <th></th>
                <th>Groupe d'utilisateurs</th>
            </tr>
            <tbody>
            <tr>
                <td>
                    <input type="checkbox" name="ckbRoutageFede" data-ref="0">
                </td>
                <td>
                    Adhérents individuels avec statut validé ({{ $nb_individuels }})
                </td>
            </tr>
            <tr>
                <td>
                    <input type="checkbox" name="ckbRoutageFede" data-ref="1">
                </td>
                <td>
                    Adhérents clubs avec statut validé ({{ $nb_adherents_clubs }})
                </td>
            </tr>
            <tr>
                <td>
                    <input type="checkbox" name="ckbRoutageFede" data-ref="2">
                </td>
                <td>
                    Adhérents non renouvelés n - 1 ({{ $nb_adherents_prec }})
                </td>
            </tr>
            <tr>
                <td>
                    <input type="checkbox" name="ckbRoutageFede" data-ref="3">
                </td>
                <td>
                    Abonnés seuls ({{ $nb_abonnes }})
                </td>
            </tr>

            <tr>
                <td>
                    <input type="checkbox" name="ckbRoutageFede" data-ref="4">
                </td>
                <td>
                    Membres du CA ({{ $nb_ca }})
                </td>
            </tr>
            <tr>
                <td>
                    <input type="checkbox" name="ckbRoutageFede" data-ref="5">
                </td>
                <td>
                    Membres du CE ({{ $nb_ce }})
                </td>
            </tr>
            <tr>
                <td>
                    <input type="checkbox" name="ckbRoutageFede" data-ref="6">
                </td>
                <td>
                    Présidents d'UR ({{ sizeof($urs) }})
                </td>
            </tr>
            <tr>
                <td>
                    <input type="checkbox" name="ckbRoutageFede" data-ref="7">
                </td>
                <td>
                    Contacts de clubs
                </td>
            </tr>
            </tbody>
        </table>
        <button class="adminPrimary btnMedium" id="btnRoutageFede">Générer le fichier de routage</button>
    </div>
@endsection
@section('css')
    <link href="{{ asset('css/admin_fpf.css') }}" rel="stylesheet">
@endsection
@section('js')
    <script src="{{ asset('js/admin_etiquettes.js') }}?t=<?= time() ?>"></script>
@endsection
