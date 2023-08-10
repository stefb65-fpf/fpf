{{--@if($action == 'store')--}}
{{--    <form action="{{ route('admin.personnes.store') }}" method="POST" style="width: 100%;">--}}
{{--@else--}}
<form action="{{ route($level.'.personnes.update', [$personne, $view_type]) }}" method="POST" style="width: 100%;">
    <input type="hidden" name="_method" value="PUT">
{{--@endif--}}

    {{ csrf_field() }}
    <div class="formBlock" style="min-width: 100%">
        <div class="formBlockTitle">Civilité</div>
        <div class="formBlockWrapper">
            <div class="formUnit formUnitAdmin">
                <div class="formLabel">Genre *</div>
                <div style="display: flex;">
                    <div style="display: flex; justify-content: flex-start;">
                        <input type="radio" name="sexe" value="0" {{ $personne->sexe == 0 ? 'checked' : '' }} /> <span style="margin-left: 5px;">Mr</span>
                    </div>
                    <div style="display: flex; justify-content: flex-start; margin-left: 20px">
                        <input type="radio" name="sexe" value="1" {{ $personne->sexe == 1 ? 'checked' : '' }} /> <span style="margin-left: 5px;">Mme</span>
                    </div>
                </div>
            </div>
            <div class="formUnit formUnitAdmin">
                <div class="formLabel">Prénom *</div>
                <input class="formValue formValueAdmin w75" type="text" value="{{ old('prenom', $personne->prenom) }}" name="prenom" />
            </div>
            <div class="formUnit formUnitAdmin">
                <div class="formLabel">Nom *</div>
                <input class="formValue formValueAdmin w75" type="text" value="{{ old('nom', $personne->nom) }}" name="nom" />
            </div>
            @if($view_type == 'adherents')
                <div class="formUnit formUnitAdmin">
                    <div class="formLabel">Date de naissance</div>
                    <input class="formValue formValueAdmin w75" type="date" value="{{ old('datenaissance', $personne->datenaissance) }}" name="datenaissance"/>
                </div>
            @endif
        </div>
    </div>
    <div class="formBlock" style="min-width: 100%">
        <div class="formBlockTitle">Adresses</div>
        @if($personne->adresses[0])
            <div class="formBlockWrapper">
                <div class="formUnit formUnitAdmin">
                    <div class="formLabel">Adresse</div>
                    <input class="formValue formValueAdmin w75" type="text" value="{{ old('libelle1', $personne->adresses[0]->libelle1) }}" name="libelle1" />
                </div>
                <div class="formUnit formUnitAdmin">
                    <div class="formLabel"></div>
                    <input class="formValue formValueAdmin w75" type="text" value="{{ old('libelle2', $personne->adresses[0]->libelle2) }}" name="libelle2"/>
                </div>
                <div class="formUnit formUnitAdmin">
                    <div class="formLabel">Code postal *</div>
                    <input class="formValue formValueAdmin" type="text" value="{{ old('codepostal', $personne->adresses[0]->codepostal) }}" name="codepostal"/>
                </div>
                <div class="formUnit formUnitAdmin">
                    <div class="formLabel">Commune *</div>
                    <input class="formValue formValueAdmin w75" type="text" value="{{ old('ville', $personne->adresses[0]->ville) }}" name="ville"/>
                </div>
                <div class="formUnit formUnitAdmin">
                    <div class="formLabel">Pays</div>
                    <select class="formValue formValueAdmin" name="pays" id="paysPersonne">
                        @foreach($countries as $country)
                            <option value="{{ $country->id }}" data-indicatif="{{ $country->indicatif }}" {{ $country->nom == $personne->adresses[0]->pays ? 'selected' : '' }}>{{ $country->nom }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="formUnit formUnitAdmin">
                    <div class="formLabel">Téléphone fixe</div>
                    <div class="inputGroup" style="display: flex; justify-content: flex-start; align-items: flex-start;">
                        <div class="indicatif" name="indicatifDomicile" id="indicatifDomicile">+{{ $personne->adresses[0]->indicatif }}</div>
                        <input class="formValue formValueAdmin w75" type="text" value="{{ old('telephonedomicile', $personne->adresses[0]->telephonedomicile) }}" name="telephonedomicile"/>
                    </div>
                </div>
            </div>
        @endif

        @if(isset($utilisateur->personne->adresses[1]))
            <div class="formBlockWrapper">
                <div class="formTitle" style="text-align: left; border-top: 1px solid #003d77; padding-top: 10px;">Adresse de livraison</div>
                <div class="formUnit formUnitAdmin">
                    <div class="formLabel">Adresse</div>
                    <input class="formValue formValueAdmin w75" type="text" value="{{ old('libelle1Livraison', $personne->adresses[1]->libelle1) }}" name="libelle1Livraison" />
                </div>
                <div class="formUnit formUnitAdmin">
                    <div class="formLabel"></div>
                    <input class="formValue formValueAdmin w75" type="text" value="{{ old('libelle2Livraison', $personne->adresses[1]->libelle2) }}" name="libelle2Livraison"/>
                </div>
                <div class="formUnit formUnitAdmin">
                    <div class="formLabel">Code postal *</div>
                    <input class="formValue formValueAdmin" type="text" value="{{ old('codepostalLivraison', $personne->adresses[1]->codepostal) }}" name="codepostalLivraison"/>
                </div>
                <div class="formUnit formUnitAdmin">
                    <div class="formLabel">Commune *</div>
                    <input class="formValue formValueAdmin w75" type="text" value="{{ old('villeLivraison', $personne->adresses[1]->ville) }}" name="villeLivraison"/>
                </div>
                <div class="formUnit formUnitAdmin">
                    <div class="formLabel">Pays</div>
                    <select class="formValue formValueAdmin" name="paysLivraison" id="paysPersonneLivraison">
                        @foreach($countries as $country)
                            <option value="{{ $country->id }}" data-indicatif="{{ $country->indicatif }}" {{ $country->nom == $personne->adresses[1]->pays ? 'selected' : '' }}>{{ $country->nom }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="formUnit formUnitAdmin">
                    <div class="formLabel">Téléphone fixe</div>
                    <div class="inputGroup" style="display: flex; justify-content: flex-start; align-items: flex-start;">
                        <div class="indicatif" name="indicatifDomicile" id="indicatifDomicileLivraison">+{{ $personne->adresses[1]->indicatif }}</div>
                        <input class="formValue formValueAdmin w75" type="text" value="{{ old('telephonedomicileLivraison', $personne->adresses[1]->telephonedomicile) }}" name="telephonedomicileLivraison"/>
                    </div>
                </div>
            </div>
        @endif
    </div>
    <div class="formBlock" style="min-width: 100%">
        <div class="formBlockTitle">Coorodonnées numériques</div>
        <div class="formBlockWrapper">
            <div class="formUnit formUnitAdmin">
                <div class="formLabel">Adresse email *</div>
                <input class="formValue formValueAdmin w75" type="email" value="{{ old('email', $personne->email) }}" name="email"/>
            </div>
            <div class="formUnit formUnitAdmin">
                <div class="formLabel">Téléphone mobile *</div>
                <div class="inputGroup" style="display: flex; justify-content: flex-start; align-items: flex-start;">
                    <div class="indicatif" name="indicatifMobile" id="indicatifMobile">+{{ $personne->adresses[0]->indicatif }}</div>
                    <input class="formValue formValueAdmin w75" type="text" value="{{ old('phone_mobile', $personne->phone_mobile) }}" name="phone_mobile"/>
                </div>
            </div>
        </div>
    </div>

                <div style="display: flex; align-items: flex-start;">
                    <button class="adminPrimary btnMedium" type="submit">
                        @if($action == 'store')
                            Ajouter la personne
                        @else
                            Enregistrer les modifications
                        @endif
                    </button>
                    @if($level == 'admin' && $action == 'update' && in_array('ANONYM', $droits_fpf))
                        <a href="{{ route('admin.personnes.anonymize', [$personne, $view_type]) }}" class="adminDanger btnMedium" style="margin-left: 50px" href="" data-method="delete" data-confirm="Voulez-vous vraiment anonymiser cette personne ? Toutes les cartes et autres informations liées perdront les informations d'identité. Il ne sera plus possible de voir apparaître les noms, prénoms, email et téléphone de la personne et cette action est irréversible">Anonymiser l'utilisateur</a>
                    @endif
                </div>
</form>
