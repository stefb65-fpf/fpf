@extends('layouts.default')

@section('content')
    <div class="pageCanva d-block">
        <h1 class="pageTitle">
            Espace de gestion des paramétrages pour la FPF
            <a class="previousPage" title="Retour page précédente" href="{{ route('admin') }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-reply-fill" viewBox="0 0 16 16">
                    <path d="M5.921 11.9 1.353 8.62a.719.719 0 0 1 0-1.238L5.921 4.1A.716.716 0 0 1 7 4.719V6c1.5 0 6 0 7 8-2.5-4.5-7-4-7-4v1.281c0 .56-.606.898-1.079.62z"/>
                </svg>
            </a>
        </h1>
        <div class="alertInfo w80">
            <p>
                <span class="bold">Informations !</span>
                Vous pouvez modifier les paramètres et tarifs de la saison en cours et de la saison prochaine. L'application des paramètres est immédiate.<br>
                Pour modifier une valeur, rentrez la nouvelle valeur dans le champ et appuyez sur la touche "Entrée" de votre clavier.
            </p>
        </div>

        <div class="mt25 mb25">
            <h2>Paramétrage de la saison courante</h2>
            <div class="rowForColumns">
                <div class="column50">
                    <h3>Tarifs</h3>
                    <div>
                        @foreach($tarifs as $tarif)
                            <div class="rowTarif">
                                <div class="columnTarif">{{ $tarif->libelle }}</div>
                                <div class="columnTarif">
                                    <input type="number" name="tarif" data-ref="{{ $tarif->id }}" data-statut="{{ $tarif->statut }}"  value="{{ $tarif->tarif }}" class="inputTarif"/>
                                     €</div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="column50">
                    <h3>Configuration saison</h3>
                    <div>
                        <div class="rowTarif">
                            <div class="columnTarif">Début de saison</div>
                            <div class="columnTarif">
                                <input type="date" name="config" data-ref="datedebut" data-id="1"  value="{{ substr($config->datedebut, 0, 10) }}" class="inputTarif"/>
                            </div>
                        </div>
                        <div class="rowTarif">
                            <div class="columnTarif">Fin de saison</div>
                            <div class="columnTarif">
                                <input type="date" name="config" data-ref="datefin" data-id="1"  value="{{ substr($config->datefin, 0, 10) }}" class="inputTarif"/>
                            </div>
                        </div>
                        <div class="rowTarif">
                            <div class="columnTarif">Fin adhésion</div>
                            <div class="columnTarif">
                                <input type="date" name="config" data-ref="datefinadhesion" data-id="1"  value="{{ substr($config->datefinadhesion, 0, 10) }}" class="inputTarif"/>
                            </div>
                        </div>
                        <div class="rowTarif">
                            <div class="columnTarif">Début Florilège</div>
                            <div class="columnTarif">
                                <input type="date" name="config" data-ref="datedebutflorilege" data-id="1"  value="{{ substr($config->datedebutflorilege, 0, 10) }}" class="inputTarif"/>
                            </div>
                        </div>
                        <div class="rowTarif">
                            <div class="columnTarif">Fin Florilège</div>
                            <div class="columnTarif">
                                <input type="date" name="config" data-ref="datefinflorilege" data-id="1"  value="{{ substr($config->datefinflorilege, 0, 10) }}" class="inputTarif"/>
                            </div>
                        </div>
                        <div class="rowTarif">
                            <div class="columnTarif">Nombre France Photo</div>
                            <div class="columnTarif">
                                <input type="number" name="config" data-ref="nombreFP" data-id="1"  value="{{ $config->nombreFP }}" class="inputTarif"/>
                            </div>
                        </div>
                        <div class="rowTarif">
                            <div class="columnTarif">Premier numéro France Photo</div>
                            <div class="columnTarif">
                                <input type="number" name="config" data-ref="premiernumeroFP" data-id="1"  value="{{ $config->premiernumeroFP }}" class="inputTarif"/>
                            </div>
                        </div>
                        <div class="rowTarif">
                            <div class="columnTarif">Numéro France Photo en cours</div>
                            <div class="columnTarif">
                                <input type="number" name="config" data-ref="numeroencours" data-id="1"  value="{{ $config->numeroencours }}" class="inputTarif"/>
                            </div>
                        </div>
                        <div class="rowTarif">
                            <div class="columnTarif">Taux reversion adhérent</div>
                            <div class="columnTarif">
                                <input type="number" name="config" data-ref="tauxreversadh" data-id="1"  value="{{ $config->tauxreversadh }}" class="inputTarif"/>
                            </div>
                        </div>
                        <div class="rowTarif">
                            <div class="columnTarif">Taux reversion club</div>
                            <div class="columnTarif">
                                <input type="number" name="config" data-ref="tauxreversclub" data-id="1"  value="{{ $config->tauxreversclub }}" class="inputTarif"/>
                            </div>
                        </div>
                        <div class="rowTarif">
                            <div class="columnTarif">Taux reversion abonnement</div>
                            <div class="columnTarif">
                                <input type="number" name="config" data-ref="tauxreversabt" data-id="1"  value="{{ $config->tauxreversabt }}" class="inputTarif"/>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <hr>
        <div class="mt25">
            <h2>Paramétrage de la saison suivante</h2>
            <div class="rowForColumns">
                <div class="column50">
                    <h3>Tarifs</h3>
                    <div>
                        @foreach($tarifsNext as $tarif)
                            <div class="rowTarif">
                                <div class="columnTarif">{{ $tarif->libelle }}</div>
                                <div class="columnTarif">
                                    <input type="text" name="tarif" data-ref="{{ $tarif->id }}" data-statut="{{ $tarif->statut }}" value="{{ $tarif->tarif }}" class="inputTarif"/>
                                    €</div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="column50">
                    <h3>Configuration saison</h3>
                    <div>
                        <div class="rowTarif">
                            <div class="columnTarif">Début de saison</div>
                            <div class="columnTarif">
                                <input type="date" name="config" data-ref="datedebut" data-id="2"  value="{{ substr($configNext->datedebut, 0, 10) }}" class="inputTarif"/>
                            </div>
                        </div>
                        <div class="rowTarif">
                            <div class="columnTarif">Fin de saison</div>
                            <div class="columnTarif">
                                <input type="date" name="config" data-ref="datefin" data-id="2"  value="{{ substr($configNext->datefin, 0, 10) }}" class="inputTarif"/>
                            </div>
                        </div>
                        <div class="rowTarif">
                            <div class="columnTarif">Fin adhésion</div>
                            <div class="columnTarif">
                                <input type="date" name="config" data-ref="datefinadhesion" data-id="2"  value="{{ substr($configNext->datefinadhesion, 0, 10) }}" class="inputTarif"/>
                            </div>
                        </div>
                        <div class="rowTarif">
                            <div class="columnTarif">Début Florilège</div>
                            <div class="columnTarif">
                                <input type="date" name="config" data-ref="datedebutflorilege" data-id="2"  value="{{ substr($configNext->datedebutflorilege, 0, 10) }}" class="inputTarif"/>
                            </div>
                        </div>
                        <div class="rowTarif">
                            <div class="columnTarif">Fin Florilège</div>
                            <div class="columnTarif">
                                <input type="date" name="config" data-ref="datefinflorilege" data-id="2"  value="{{ substr($configNext->datefinflorilege, 0, 10) }}" class="inputTarif"/>
                            </div>
                        </div>
                        <div class="rowTarif">
                            <div class="columnTarif">Nombre France Photo</div>
                            <div class="columnTarif">
                                <input type="number" name="config" data-ref="nombreFP" data-id="2"  value="{{ $configNext->nombreFP }}" class="inputTarif"/>
                            </div>
                        </div>
                        <div class="rowTarif">
                            <div class="columnTarif">Premier numéro France Photo</div>
                            <div class="columnTarif">
                                <input type="number" name="config" data-ref="premiernumeroFP" data-id="2"  value="{{ $configNext->premiernumeroFP }}" class="inputTarif"/>
                            </div>
                        </div>
                        <div class="rowTarif">
                            <div class="columnTarif">Numéro France Photo en cours</div>
                            <div class="columnTarif">
                                <input type="number" name="config" data-ref="numeroencours" data-id="2"  value="{{ $configNext->numeroencours }}" class="inputTarif"/>
                            </div>
                        </div>
                        <div class="rowTarif">
                            <div class="columnTarif">Taux reversion adhérent</div>
                            <div class="columnTarif">
                                <input type="number" name="config" data-ref="tauxreversadh" data-id="2"  value="{{ $configNext->tauxreversadh }}" class="inputTarif"/>
                            </div>
                        </div>
                        <div class="rowTarif">
                            <div class="columnTarif">Taux reversion club</div>
                            <div class="columnTarif">
                                <input type="number" name="config" data-ref="tauxreversclub" data-id="2"  value="{{ $configNext->tauxreversclub }}" class="inputTarif"/>
                            </div>
                        </div>
                        <div class="rowTarif">
                            <div class="columnTarif">Taux reversion abonnement</div>
                            <div class="columnTarif">
                                <input type="number" name="config" data-ref="tauxreversabt" data-id="2"  value="{{ $configNext->tauxreversabt }}" class="inputTarif"/>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('css')
    <link href="{{ asset('css/admin_fpf.css') }}" rel="stylesheet">
@endsection
@section('js')
    <script src="{{ asset('js/admin_tarif.js') }}"></script>
@endsection

