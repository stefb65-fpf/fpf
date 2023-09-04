@extends('layouts.default')

@section('content')
    <div class="pageCanva">
        <h1 class="pageTitle">
            <div>Gestion Club - Commande de Florilège
                <div class="urTitle">
                    {{ $club->nom }}
                </div>
            </div>
            <a class="previousPage" title="Retour page précédente" href="{{ route('clubs.gestion') }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-reply-fill" viewBox="0 0 16 16">
                    <path d="M5.921 11.9 1.353 8.62a.719.719 0 0 1 0-1.238L5.921 4.1A.716.716 0 0 1 7 4.719V6c1.5 0 6 0 7 8-2.5-4.5-7-4-7-4v1.281c0 .56-.606.898-1.079.62z"/>
                </svg>
            </a>
        </h1>
        <div class="florilege">
            <div>
                <img src="{{ url('storage/app/public/uploads/florilege.webp') }}" alt="">
            </div>
            <div>
                <div>
                    Vous pouvez commander des numéros du Florilège au prix de {{ $config->prixflorilegefrance }}€
                    jusqu'au
                    {{ substr($config->datefinflorilege, 8, 2).'/'.substr($config->datefinflorilege, 5, 2).'/'.substr($config->datefinflorilege, 0, 4) }}
                    .<br>
                    Les commandes seront livrées à l'adresse du contact du club.
                    @if($adresse)
                        <div class="mt10 mb10 mxauto wMaxContent">
                            @if($contact)
                                <div>{{ $contact->personne->prenom.' '.$contact->personne->nom }}</div>
                            @else
                                <div>{{ $club->nom }}</div>
                            @endif
                            <div>{{ $adresse->libelle1 }}</div>
                            <div>{{ $adresse->libelle2 }}</div>
                            <div>{{ str_pad($adresse->codepostal, 5, '0', STR_PAD_LEFT).' '.$adresse->ville }}</div>
                        </div>
                    @endif
                </div>
                <div class="mt25">
                    Sélectionnez le nombre d'exemplaires à commander
                    <div class="text-center mt25">
                        <select id="selectFlorilege" class="p10">
                            @for($i=1; $i<21; $i++)
                                <option value="{{ $i }}">{{ $i }} exemplaire{{ $i > 1 ? 's' : '' }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="mt25">
                        <div>
                            Vous allez commander pour le club <span id="nbFlorilege"
                                                                    class="bold">1</span> exemplaires
                            pour un montant de <span name="priceFlorilege"
                                                     class="bold">{{ $config->prixflorilegefrance }}</span>
                            €.
                        </div>
                        <div>
                            <button class="primary btnRegister" name="orderFlorilegeClub" data-type="monext"
                                    data-club="{{ $club->id }}">Payer <span
                                    name="priceFlorilege">{{ $config->prixflorilegefrance }}</span>€ par carte bancaire
                            </button>
                            <button class="primary btnRegister" name="orderFlorilegeClub" data-type="bridge"
                                    data-club="{{ $club->id }}">Payer <span
                                    name="priceFlorilege">{{ $config->prixflorilegefrance }}</span>€ par virement
                            </button>
                        </div>
                    </div>
                    <span class="d-none" id="priceUnitFlorilege">{{ $config->prixflorilegefrance }}</span>
                </div>
            </div>

        </div>
    </div>
@endsection
@section('js')
    <script src="{{ asset('js/florilege.js') }}?t=<?= time() ?>"></script>
@endsection
