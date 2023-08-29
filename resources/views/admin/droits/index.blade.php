@extends('layouts.default')

@section('content')
    <div class="pageCanva">
        <h1 class="pageTitle">
            Gestion Fédérale - Droits d'accès
            <a class="previousPage" title="Retour page précédente" href="{{ route('admin') }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-reply-fill" viewBox="0 0 16 16">
                    <path d="M5.921 11.9 1.353 8.62a.719.719 0 0 1 0-1.238L5.921 4.1A.716.716 0 0 1 7 4.719V6c1.5 0 6 0 7 8-2.5-4.5-7-4-7-4v1.281c0 .56-.606.898-1.079.62z"/>
                </svg>
            </a>
        </h1>
        <div class="alertInfo w80">
            <p>
                <span class="bold">Informations !</span>
                Vous pouvez ajouter des droits d'accès à l'amdinistration de la base en ligne pour les utilisateurs via leur identifiant ou les fonctions qu'ils occupent.
                L'attribution de droits à une fonction concernera tous les utilisateurs ayant cette fonction.<br>
                Pour ajouter un droit, sélectionnez une fonction ou un utilisateur et cliquez sur le bouton "ajouter".
            </p>
        </div>

        <div class="mt25 mb25 w100">
            @foreach($droits as $droit)
                <div class="rowDroit">
                    <div class="columnDroitNom">{{ $droit->nom }}</div>
                    <div class="columnDroitListe">
                        <div class="columnDroitItems">
                            @foreach($droit->fonctions as $fonction)
                                <div class="columnDroitItem">
                                    <div>{{ $fonction->libelle }}</div>
                                    <div class="deleteDroit">
                                        <a href="{{ route('droits.deleteFonction', [$droit->id, $fonction->id]) }}" data-method="delete"  data-confirm="Voulez-vous vraiment enlever ce droit pour cette fonction ?">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-circle-fill" viewBox="0 0 16 16">
                                                <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM5.354 4.646a.5.5 0 1 0-.708.708L7.293 8l-2.647 2.646a.5.5 0 0 0 .708.708L8 8.707l2.646 2.647a.5.5 0 0 0 .708-.708L8.707 8l2.647-2.646a.5.5 0 0 0-.708-.708L8 7.293 5.354 4.646z"/>
                                            </svg>
                                        </a>
                                    </div>
                                </div>

                            @endforeach
                        </div>
                        <div class="columnDroitItems">
                            @foreach($droit->utilisateurs as $utilisateur)
                                <div class="columnDroitItem">
                                    <div>
                                        {{ $utilisateur->identifiant.' - '.$utilisateur->personne->nom.' '.$utilisateur->personne->prenom }}
                                    </div>
                                    <div class="deleteDroit">
                                        <a href="{{ route('droits.deleteUtilisateur', [$droit->id, $utilisateur->id]) }}" data-method="delete"  data-confirm="Voulez-vous vraiment enlever ce droit à l'utilisateur ?">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-circle-fill" viewBox="0 0 16 16">
                                                <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM5.354 4.646a.5.5 0 1 0-.708.708L7.293 8l-2.647 2.646a.5.5 0 0 0 .708.708L8 8.707l2.646 2.647a.5.5 0 0 0 .708-.708L8.707 8l2.647-2.646a.5.5 0 0 0-.708-.708L8 7.293 5.354 4.646z"/>
                                            </svg>
                                        </a>
                                    </div>
                                </div>
                            @endforeach

                        </div>
                    </div>

                        <form action="{{ route('droits.store') }}"  class="columnDroitAction" method="POST">
                            {{ csrf_field() }}
                            <div>
                                <select name="fonction" class="selectFormAction formValue modifying">
                                    <option value="-1">-- Fonction à choisir --</option>
                                    @if(in_array($droit->label, ['GESNEWUR', 'GESNEWURCA'])))
                                        @foreach($fonctions_urs as $fonction)
                                            <option value="{{ $fonction->id }}">{{ $fonction->libelle }}</option>
                                        @endforeach
                                    @else
                                        @foreach($fonctions as $fonction)
                                            <option value="{{ $fonction->id }}">{{ $fonction->libelle }}</option>
                                        @endforeach
                                    @endif

                                </select>
                                <input type="text" name="utilisateur" value="" placeholder="identifiant utilisateur" class="inputFormAction  formValue modifying" maxlength="12">
                                <input type="hidden" name="droit" value="{{ $droit->id }}">
                            </div>

                            <button class="adminPrimary">ajouter</button>
                        </form>

                </div>
            @endforeach
        </div>
    </div>
@endsection
@section('css')
    <link href="{{ asset('css/admin_fpf.css') }}" rel="stylesheet">
@endsection
@section('js')
    <script src="{{ asset('js/admin_tarif.js') }}"></script>
@endsection
