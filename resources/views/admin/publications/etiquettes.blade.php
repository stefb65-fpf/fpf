@extends('layouts.default')

@section('content')
    <div class="pageCanva">
        <h1 class="pageTitle">
            Impression des étiquettes
            <a class="previousPage" title="Retour page précédente" href="{{ route('admin.gestion_publications') }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-reply-fill" viewBox="0 0 16 16">
                    <path d="M5.921 11.9 1.353 8.62a.719.719 0 0 1 0-1.238L5.921 4.1A.716.716 0 0 1 7 4.719V6c1.5 0 6 0 7 8-2.5-4.5-7-4-7-4v1.281c0 .56-.606.898-1.079.62z"/>
                </svg>
            </a>
        </h1>
        <div class="alertInfo" style="width: 80% !important">
            <p>
                <span class="bold">Informations !</span>
                Vous pouvez éditer un fichier PDF avec les étiquettes par groupe d'utilisateurs
            </p>
        </div>

        <table class="styled-table" style="width: 100%">
            <thead>
                <tr>
                    <th>Groupe d'utilisateurs</th>
                    <th></th>
                    <th colspan="2">Actions</th>
                </tr>
            <tbody>
{{--            <tr>--}}
{{--                <td>--}}
{{--                    Cubs avec statut validé ({{ $nb_clubs }})--}}
{{--                </td>--}}
{{--                <td></td>--}}
{{--                <td>--}}
{{--                    <a name="editEtiquettes" data-ref="0" class="adminPrimary btnSmall">Éditer les étiquettes</a>--}}
{{--                </td>--}}
{{--                <td style="width: 220px">--}}
{{--                    <a name="viewEtiquettes" class="adminSuccess btnSmall" style="display: none">Visualiser le ficher</a>--}}
{{--                </td>--}}
{{--            </tr>--}}
            <tr>
                <td>
                    Adhérents individuels avec statut validé ({{ $nb_individuels }})
                </td>
                <td></td>
                <td>
                    <a name="editEtiquettes" data-ref="1" class="adminPrimary btnSmall">Éditer les étiquettes</a>
                </td>
                <td>
                    <a name="viewEtiquettes" class="adminSuccess btnSmall" style="display: none" target="_blank">Visualiser le ficher</a>
                </td>
            </tr>
            <tr>
                <td>
                    Membres du CA ({{ $nb_ca }})
                </td>
                <td></td>
                <td>
                    <a name="editEtiquettes" data-ref="2" class="adminPrimary btnSmall">Éditer les étiquettes</a>
                </td>
                <td>
                    <a name="viewEtiquettes" class="adminSuccess btnSmall" style="display: none" target="_blank">Visualiser le ficher</a>
                </td>
            </tr>
            <tr>
                <td>
                    Membres du CE ({{ $nb_ce }})
                </td>
                <td></td>
                <td>
                    <a name="editEtiquettes" data-ref="3" class="adminPrimary btnSmall">Éditer les étiquettes</a>
                </td>
                <td>
                    <a name="viewEtiquettes" class="adminSuccess btnSmall" style="display: none" target="_blank">Visualiser le ficher</a>
                </td>
            </tr>
            <tr>
                <td>
                    Présidents d'UR ({{ sizeof($urs) }})
                </td>
                <td></td>
                <td>
                    <a name="editEtiquettes" data-ref="4" class="adminPrimary btnSmall">Éditer les étiquettes</a>
                </td>
                <td>
                    <a name="viewEtiquettes" class="adminSuccess btnSmall" style="display: none" target="_blank">Visualiser le ficher</a>
                </td>
            </tr>
            <tr>
                <td>
                    Contacts de clubs
                </td>
                <td>
                    <select id="selectUrForEtiquettesContact">
                        <option value="0">Toutes urs</option>
                        @foreach($urs as $ur)
                            <option value="{{ $ur->id }}">{{ $ur->nom }}</option>
                        @endforeach
                    </select>
                </td>
                <td>
                    <a id="etiquettesContact" name="editEtiquettes" data-ref="5" data-ur="0" class="adminPrimary btnSmall">Éditer les étiquettes</a>
                </td>
                <td>
                    <a name="viewEtiquettes" class="adminSuccess btnSmall" style="display: none" target="_blank">Visualiser le ficher</a>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
@endsection
@section('css')
    <link href="{{ asset('css/admin_fpf.css') }}" rel="stylesheet">
@endsection
@section('js')
    <script src="{{ asset('js/admin_etiquettes.js') }}?t=<?= time() ?>"></script>
@endsection
