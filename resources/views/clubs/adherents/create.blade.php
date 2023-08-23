@extends('layouts.default')

@section('content')
    <div class="pageCanva">
        <h1 class="pageTitle">
            Ajout d'un adhérent pour le club {{ $club->nom }}
            <a class="previousPage" title="Retour page précédente" href="{{ route('clubs.adherents.index') }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor"
                     class="bi bi-reply-fill" viewBox="0 0 16 16">
                    <path
                        d="M5.921 11.9 1.353 8.62a.719.719 0 0 1 0-1.238L5.921 4.1A.716.716 0 0 1 7 4.719V6c1.5 0 6 0 7 8-2.5-4.5-7-4-7-4v1.281c0 .56-.606.898-1.079.62z"/>
                </svg>
            </a>
        </h1>
        <div class="w100">
            @include('clubs.adherents.form', ['action' => 'store'])
        </div>
    </div>
    <div class="modalEdit d-none" id="modalSameEmail">
        <div class="modalEditHeader">
            <div class="modalEditTitle">Ajout d'un adhérent</div>
            <div class="modalEditClose">
                X
            </div>
        </div>
        <div class="modalEditBody">
            <div class="alertDanger mt10 mxauto mb0">
                Cette adresse email est déjà connue dans notre base. Elle correspond à la personne suivante :
                <span id="nameSameEmail"></span><br><br>
                Confirmez-vous que l'adhérent que vous souhaitez ajouter est bien cette personne ?<br>
                Une nouvelle carte sera créée en récupérant les données déjà existantes de la personne.<br>
                Cette personne conservera également les identifiants FPF déjà existants.<br>
            </div>
        </div>
        <div class="modalEditFooter">
            <div class="adminDanger btnMedium mr10 modalEditClose">Fermer</div>
            <div class="adminSuccess btnMedium mr10" id="confirmSameEmail" data-personne="">Confirmer</div>
        </div>
    </div>

    <div class="modalEdit d-none" id="modalSameName">
        <div class="modalEditHeader">
            <div class="modalEditTitle">Ajout d'un adhérent</div>
            <div class="modalEditClose">
                X
            </div>
        </div>
        <div class="modalEditBody">
            <div class="alertDanger  mt10 mxauto mb0">
                Il existe dans la base plusieurs personne avec les mêmes nom et prénom que la personne que vous souhaitez ajouter.<br>
                Toutefois, les adresses email sont différentes.<br>
                Vous trouverez ci-dessous la liste des personnes correspondantes. Si l'adhérent que vous souhaitez ajouter est bien l'une de ces personnes, cliquez sur le nom de cette personne.<br>
                Sinon, cliquez sur "Poursuivre sans sélectionner de personne".
                <div id="nameSameName" class="ml20 mt20"></div>
            </div>
        </div>
        <div class="modalEditFooter">
            <div class="adminDanger btnMedium mr10 modalEditClose">Fermer</div>
            <div class="adminSuccess btnMedium mr10" id="confirmSameName">Poursuivre sans sélectionner de personne</div>
        </div>
    </div>
@endsection
@section('css')
    <link href="{{ asset('css/admin_fpf.css') }}" rel="stylesheet">
@endsection
@section('js')
    <script src="{{ asset('js/gestion_adherents.js') }}?t=<?= time() ?>"></script>
@endsection
