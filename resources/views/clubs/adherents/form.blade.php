@if($action === 'update')
    <form action="{{ route($prev.'.adherents.update', $utilisateur) }}" method="POST">
        <input type="hidden" name="_method" value="put">
@else
    @if($prev == 'clubs')
        <form action="{{ route('clubs.adherents.store') }}" method="POST" id="storeNewAdherent">
    @else
        <form action="{{ route($prev.'.adherents.store', $club) }}" method="POST" id="storeNewAdherent">
    @endif
@endif
    {{ csrf_field() }}
    <div class="formBlock">
        <input type="hidden" name="prev" value="{{ $prev }}">
        <div class="formBlockTitle">Civilité</div>
        <div class="formBlockWrapper">
            <div class="formLine">
                <div class="formLabel">Genre *</div>
                <select class="inputFormAction formValue modifying" name="sexe">
                    <option value="0" {{ $utilisateur->personne->sexe == 0 ? 'selected' : '' }}>Homme</option>
                    <option value="1" {{ $utilisateur->personne->sexe == 1 ? 'selected' : '' }}>Femme</option>
                </select>
            </div>
            <div class="formLine">
                <div class="formLabel">Nom *</div>
                <input class="inputFormAction formValue modifying w70" id="personneNom" value="{{ old('nom', $utilisateur->personne->nom) }}" name="nom" maxlength="70" minlength="2" type="text" required />
            </div>
            <div class="formLine">
                <div class="formLabel">Prénom *</div>
                <input class="inputFormAction formValue modifying w70" id="personnePrenom" value="{{ old('prenom', $utilisateur->personne->prenom) }}" name="prenom" maxlength="70" minlength="2" type="text" required />
            </div>
            <div class="formLine">
                <div class="formLabel">Date de naissance *</div>
                <input class="inputFormAction formValue modifying" id="personneDateNaissance" type="date" value="{{ old('datenaissance', $utilisateur->personne->datenaissance) }}" name="datenaissance" required />
            </div>
        </div>
    </div>
    <div class="formBlock">
        <div class="formBlockTitle">Adresses</div>
        <div class="formBlockWrapper">
            <div class="formLine">
                <div class="formLabel">Adresse</div>
                <input name="libelle1" type="text" value="{{ old('libelle1', $utilisateur->personne->adresses[0]->libelle1) }}" class="inputFormAction formValue modifying w70" maxlength="120"/>
            </div>
            <div class="formLine">
                <div class="formLabel">Complément</div>
                <input name="libelle2" type="text" value="{{ old('libelle2', $utilisateur->personne->adresses[0]->libelle2) }}"  class="inputFormAction formValue modifying w70" maxlength="120"/>
            </div>
            <div class="formLine">
                <div class="formLabel">Code Postal *</div>
                <input name="codepostal" type="text" id="adresseCodepostal" value="{{ old('codepostal', $utilisateur->personne->adresses[0]->codepostal) }}" class="inputFormAction formValue modifying" maxlength="10" required/>
            </div>
            <div class="formLine">
                <div class="formLabel">Ville *</div>
                <input name="ville" type="text" id="adresseVille" value="{{ old('ville', $utilisateur->personne->adresses[0]->ville) }}" class="inputFormAction formValue modifying w70" value="" maxlength="70" required/>
            </div>
            <div class="formLine">
                <div class="formLabel">Pays *</div>
                <select class="inputFormAction formValue modifying" name="pays" id="selectPays">
                    @foreach($countries as $country)
                            <option value="{{$country->id}}" {{strtolower($country->nom) == strtolower($utilisateur->personne->adresses[0]->pays)? "selected" : ""}}
                            data-indicator="{{$country->indicatif}}">{{$country->nom}}</option>
                    @endforeach
                </select>
            </div>
            <div class="formLine">
                <div class="formLabel">Téléphone fixe</div>
                <div class="group">
                    <div class="indicator" id="indicator1">+{{ $utilisateur->personne->adresses[0]->indicatif ?? '' }}</div>
                    <input class="formValue phoneInput bgWhite modifying" type="text" value="{{ old('telephonedomicile', $utilisateur->personne->adresses[0]->telephonedomicile) }}" name="telephonedomicile" maxlength="25"/>
                </div>
            </div>
        </div>
        @if(isset($utilisateur->personne->adresses[1]))
        <div class="formBlockWrapper">
            <div class="formTitle text-left borderTopBlue pt10">Adresse de livraison</div>
            <div class="formLine">
                <div class="formLabel">Adresse</div>
                <input name="adresse2_libelle1" type="text" value="{{ old('adresse2_libelle1', $utilisateur->personne->adresses[1]->libelle1) }}" class="inputFormAction formValue modifying w70" maxlength="120"/>
            </div>
            <div class="formLine">
                <div class="formLabel"></div>
                <input name="adresse2_libelle2" type="text" value="{{ old('adresse2_libelle2', $utilisateur->personne->adresses[1]->libelle2) }}"  class="inputFormAction formValue modifying w70" maxlength="120"/>
            </div>
            <div class="formLine">
                <div class="formLabel">Code Postal *</div>
                <input name="adresse2_codepostal" type="text" value="{{ old('adresse2_codepostal', $utilisateur->personne->adresses[1]->codepostal) }}" class="inputFormAction formValue modifying" maxlength="10" required/>
            </div>
            <div class="formLine">
                <div class="formLabel">Ville *</div>
                <input name="adresse2_ville" type="text" value="{{ old('adresse2_ville', $utilisateur->personne->adresses[1]->ville) }}" class="inputFormAction formValue modifying w70" value="" maxlength="70" required/>
            </div>
            <div class="formLine">
                <div class="formLabel">Pays *</div>
                <select class="inputFormAction formValue modifying" name="adresse2_pays" id="selectPaysAdresse2">
                    @foreach($countries as $country)
                        <option value="{{$country->id}}" {{strtolower($country->nom) == strtolower($utilisateur->personne->adresses[1]->pays)? "selected" : ""}}
                        data-indicator="{{$country->indicatif}}">{{$country->nom}}</option>
                    @endforeach
                </select>
            </div>
            <div class="formLine">
                <div class="formLabel">Téléphone fixe</div>
                <div class="group">
                    <div class="indicator" id="indicator3">+{{ $utilisateur->personne->adresses[1]->indicatif ?? '' }}</div>
                    <input class="formValue phoneInput bgWhite modifying" type="text" value="{{ old('adresse2_telephonedomicile', $utilisateur->personne->adresses[1]->telephonedomicile) }}" name="adresse2_telephonedomicile" maxlength="25"/>
                </div>
            </div>
        </div>
        @endif
    </div>
    <div class="formBlock">
        <div class="formBlockTitle">Coordonnées numériques</div>
        <div class="formBlockWrapper">
            <div class="formLine">
                <div class="formLabel">Adresse email *</div>
                <input class="inputFormAction formValue modifying w70" id="personneEmail" value="{{ old('email', $utilisateur->personne->email) }}" name="email" required {{ $action == 'update' ? 'disabled' : '' }} />
            </div>
            <div class="formLine">
                <div class="formLabel">Téléphone mobile *</div>
                <div class="group">
                    <div class="indicator" id="indicator2">+{{ $utilisateur->personne->adresses[0]->indicatif ?? '' }}</div>
                    <input class="formValue phoneInput bgWhite modifying" id="personneMobile" value="{{ old('phone_mobile', str_replace(' ', '', $utilisateur->personne->phone_mobile)) }}" name="phone_mobile" required maxlength="25"/>
                </div>

            </div>
        </div>
    </div>
    <div class="formBlock">
        <div class="formBlockTitle">Newsletter</div>
        <div class="formBlockWrapper">
            <div class="formLine">
                <input class="inputFormAction modifying" name="news" type="checkbox" {{ $utilisateur->personne->news === 1 ? 'checked' : '' }} />
                <div>Réception des informations de la FPF (hors lettre de la fédé)</div>
            </div>
        </div>
    </div>
        <input type="hidden" name="personne_id" id="existantPersonneId">
    <div>
        @if($action == 'update')
            <button type="submit" class="adminSuccess btnMedium">Enregistrer les modifications</button>
        @else
            <button class="adminSuccess btnMedium" id="checkBeforeInsertion">Ajouter l'adhérent</button>
        @endif
    </div>
</form>
