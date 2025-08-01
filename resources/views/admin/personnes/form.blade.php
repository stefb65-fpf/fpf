@if($action == 'store')
    <form class="w100" action="{{ route($level.'.personnes.store', $view_type) }}" method="POST">
        @else
            <form class="w100" action="{{ route($level.'.personnes.update', [$personne, $view_type]) }}" method="POST">
                <input type="hidden" name="_method" value="PUT">
                @endif

                {{ csrf_field() }}
                <div class="formBlock minW100">
                    <div class="formBlockTitle">Civilité</div>
                    <div class="formBlockWrapper">
                        <div class="formUnit formUnitAdmin">
                            <div class="formLabel">Genre *</div>
                            <div class="d-flex justify-start">
                                <div class="d-flex justify-start">
                                    <input type="radio" name="sexe"
                                           value="0" {{ $personne->sexe == 0 ? 'checked' : '' }} /> <span
                                        class="ml5">Mr</span>
                                </div>
                                <div class="d-flex justify-start ml20">
                                    <input type="radio" name="sexe"
                                           value="1" {{ $personne->sexe == 1 ? 'checked' : '' }} /> <span class="ml5">Mme</span>
                                </div>
                            </div>
                        </div>
                        <div class="formUnit formUnitAdmin">
                            <div class="formLabel">Prénom *</div>
                            <input class="formValue formValueAdmin w75 modifying" type="text"
                                   value="{{ old('prenom', $personne->prenom) }}" name="prenom"/>
                        </div>
                        <div class="formUnit formUnitAdmin">
                            <div class="formLabel">Nom *</div>
                            <input class="formValue modifying formValueAdmin w75" type="text"
                                   value="{{ old('nom', $personne->nom) }}" name="nom"/>
                        </div>
                        @if($view_type == 'adherents')
                            <div class="formUnit formUnitAdmin">
                                <div class="formLabel">Date de naissance *</div>
                                <input class="formValue modifying formValueAdmin w75" type="date"
                                       value="{{ old('datenaissance', $personne->datenaissance) }}"
                                       name="datenaissance"/>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="formBlock minW100">
                    <div class="formBlockTitle">Adresses</div>
                    @if($personne->adresses[0])
                        <div class="formBlockWrapper">
                            <div class="formUnit formUnitAdmin">
                                <div class="formLabel">Adresse</div>
                                <input class="formValue modifying formValueAdmin w75" type="text"
                                       value="{{ old('libelle1', $personne->adresses[0]->libelle1) }}" name="libelle1"/>
                            </div>
                            <div class="formUnit formUnitAdmin">
                                <div class="formLabel">Complément</div>
                                <input class="formValue modifying formValueAdmin w75" type="text"
                                       value="{{ old('libelle2', $personne->adresses[0]->libelle2) }}" name="libelle2"/>
                            </div>
                            <div class="formUnit formUnitAdmin">
                                <div class="formLabel">Code postal *</div>
                                <div class="suggestionWrapper">
                                    <input class="formValue modifying formValueAdmin" type="text"
                                           value="{{ old('codepostal', str_pad($personne->adresses[0]->codepostal, 5, '0', STR_PAD_LEFT)) }}"
                                           name="codepostal" maxlength="10"/>
                                    <div class="suggestion"></div>
                                </div>

                            </div>
                            <div class="formUnit formUnitAdmin">
                                <div class="formLabel">Commune *</div>
                                <div class="suggestionWrapper">
                                    <input class="formValue modifying formValueAdmin w75" type="text"
                                           value="{{ old('ville', $personne->adresses[0]->ville) }}" name="ville"/>
                                    <div class="suggestion"></div>
                                </div>

                            </div>
                            <div class="formUnit formUnitAdmin">
                                <div class="formLabel">Pays</div>
                                <select class="formValue modifying formValueAdmin pays" name="pays" id="paysPersonne">
                                    @foreach($countries as $country)
                                        <option value="{{ $country->id }}"
                                                data-indicatif="{{ $country->indicatif }}" {{ $country->nom == $personne->adresses[0]->pays ? 'selected' : '' }}>{{ $country->nom }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="formUnit formUnitAdmin">
                                <div class="formLabel">Téléphone fixe</div>
                                <div class="inputGroup d-flex justify-start align-start">
                                    <div class="indicator" name="indicatifDomicile" id="indicatifDomicile">
                                        +{{ $personne->adresses[0]->indicatif }}</div>
                                    <input class="formValue modifying formValueAdmin w75" type="text"
                                           value="{{ old('telephonedomicile', $personne->adresses[0]->telephonedomicile) }}"
                                           name="telephonedomicile" maxlength="25"/>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if(isset($utilisateur->personne->adresses[1]))
                        <div class="formBlockWrapper">
                            <div class="formTitle text-left borderTopBlue pt10">Adresse de livraison</div>
                            <div class="formUnit formUnitAdmin">
                                <div class="formLabel">Adresse</div>
                                <input class="formValue modifying formValueAdmin w75" type="text"
                                       value="{{ old('libelle1Livraison', $personne->adresses[1]->libelle1) }}"
                                       name="libelle1Livraison"/>
                            </div>
                            <div class="formUnit formUnitAdmin">
                                <div class="formLabel"></div>
                                <input class="formValue modifying formValueAdmin w75" type="text"
                                       value="{{ old('libelle2Livraison', $personne->adresses[1]->libelle2) }}"
                                       name="libelle2Livraison"/>
                            </div>
                            <div class="formUnit formUnitAdmin">
                                <div class="formLabel">Code postal *</div>
                                <div class="suggestionWrapper">
                                    <input class="formValue modifying formValueAdmin" type="text"
                                           value="{{ old('codepostalLivraison', str_pad($personne->adresses[1]->codepostal, 5, '0', STR_PAD_LEFT)) }}"
                                           name="codepostalLivraison" maxlength="10"/>
                                    <div class="suggestion"></div>
                                </div>
                            </div>
                            <div class="formUnit formUnitAdmin">
                                <div class="formLabel">Commune *</div>
                                <div class="suggestionWrapper">
                                    <input class="formValue modifying formValueAdmin w75" type="text"
                                           value="{{ old('villeLivraison', $personne->adresses[1]->ville) }}"
                                           name="villeLivraison"/>
                                    <div class="suggestion"></div>
                                </div>
                            </div>
                            <div class="formUnit formUnitAdmin">
                                <div class="formLabel">Pays</div>
                                <select class="formValue modifying formValueAdmin pays" name="paysLivraison"
                                        id="paysPersonneLivraison">
                                    @foreach($countries as $country)
                                        <option value="{{ $country->id }}"
                                                data-indicatif="{{ $country->indicatif }}" {{ $country->nom == $personne->adresses[1]->pays ? 'selected' : '' }}>{{ $country->nom }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="formUnit formUnitAdmin">
                                <div class="formLabel">Téléphone fixe</div>
                                <div class="inputGroup d-flex justify-start align-start">
                                    <div class="indicator" name="indicatifDomicile" id="indicatifDomicileLivraison">
                                        +{{ $personne->adresses[1]->indicatif }}</div>
                                    <input class="formValue modifying formValueAdmin w75" type="text"
                                           value="{{ old('telephonedomicileLivraison', $personne->adresses[1]->telephonedomicile) }}"
                                           name="telephonedomicileLivraison" maxlength="25"/>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
                <div class="formBlock minW100">
                    <div class="formBlockTitle">Coordonnées numériques</div>
                    <div class="formBlockWrapper">
                        <div class="formUnit formUnitAdmin">
                            <div class="formLabel">Adresse email *</div>
                            <input class="formValue modifying formValueAdmin w75" type="email"
                                   value="{{ old('email', $personne->email) }}" name="email"/>
                        </div>
                        <div class="formUnit formUnitAdmin">
                            <div class="formLabel">Téléphone mobile *</div>
                            <div class="inputGroup d-flex justify-start align-start">
                                <div class="indicator" name="indicatifMobile" id="indicatifMobile">
                                    +{{ $personne->adresses[0]->indicatif }}</div>
                                <input class="formValue modifying formValueAdmin w75" type="text"
                                       value="{{ old('phone_mobile', $personne->phone_mobile) }}" name="phone_mobile"
                                       maxlength="25"/>
                            </div>
                        </div>
                        @if($view_type == 'abonnes')
                            <div class="formUnit formUnitAdmin">
                                <div class="formLabel">Numéro de fin</div>
                                <input class="formValue modifying formValueAdmin w75" type="text" maxlength="3"
                                       value="{{ old('fin', $personne->fin) }}"
                                       name="fin"/>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="formBlock minW100">
                    <div class="formBlockTitle">Newsletter</div>
                    <div class="formBlockWrapper">
                        <div class="formLine">
                            <input class="inputFormAction modifying" name="news" type="checkbox" {{ $personne->news === 1 ? 'checked' : '' }} />
                            <div>Réception des informations de la FPF (hors lettre de la fédé)</div>
                        </div>
                    </div>
                </div>

                <div class="d-flex align-start">
                    @if($level == 'urs' || ($level = 'admin' && in_array('GESINFO', $droits_fpf)))
                    <button class="adminPrimary btnMedium" type="submit">

                            @if($action == 'store')
                                Ajouter la personne
                            @else
                                Enregistrer les modifications
                            @endif
                    </button>
                    @endif
                    @if($level == 'admin' && $action == 'update' && in_array('GESINFO', $droits_fpf))
                        <a href="{{ route('admin.personnes.renewAbo', [$personne, $view_type]) }}" class="adminSuccess btnMedium ml50" data-method="put" data-confirm="Vous allez générer un règlement pour prolonger l'abonnement de l'adhérent de 5 numéros. Si celui-ci n'a pas d'abonnement en cours, la fin de son abonnement sera 5 numéros après celui en cours. Le règlement généré devra être validé pour la prise en compte.  Confirmez-vous votre demande ?">Abonner l'utilisateur pour un montant de {{ $montant_abonnement }}€</a>
                        <a href="{{ route('admin.personnes.addFreeAbo', [$personne, $view_type]) }}" class="adminSuccess btnMedium ml50" data-method="put" data-confirm="Voulez-vous vraiment ajouter un abonnement gratuit de 5 numéros ? Si l'utilisateur est déjà abonné, vous allez prolonger son abonnement de 5 numéros. Si celui-ci n'a pas d'abonnement en cours, la fin de son abonnement sera 5 numéros après celui en cours.">Ajouter un abonnement gratuit</a>
                        <a href="{{ route('admin.personnes.addCarteIndividuelle', [$personne, $view_type]) }}" class="adminSuccess btnMedium ml50" data-method="put" data-confirm="Voulez-vous vraiment créer une nouvelle carte individuelle pour cette personne ? L'identifiant ajouté sera basé sur l'adresse déjà enregistrée. Si cette adresse n'est pas valable, modifier là avant de créer la carte.">Ajouter une carte individuelle</a>
                    @endif
                    @if($level == 'admin' && $action == 'update' && in_array('ANONYM', $droits_fpf))
                        <a href="{{ route('admin.personnes.anonymize', [$personne, $view_type]) }}"
                           class="adminDanger btnMedium ml50" href="" data-method="delete"
                           data-confirm="Voulez-vous vraiment anonymiser cette personne ? Toutes les cartes et autres informations liées perdront les informations d'identité. Il ne sera plus possible de voir apparaître les noms, prénoms, email et téléphone de la personne et cette action est irréversible">Anonymiser
                            l'utilisateur</a>

                    @endif
                </div>
            </form>
        @section('js')
            <script src="{{ asset('js/autocompleteCommune.js') }}"></script>
    @endsection
