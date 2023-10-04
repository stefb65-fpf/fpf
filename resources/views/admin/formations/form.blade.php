<div class="formBlock">
    @if($action == 'update')
        <form method="POST" action="{{ route('formations.update', $formation->id) }}">
            <input type="hidden" name="_method" value="PUT">
    @else
        <form method="POST" action="{{ route('formations.store') }}">
    @endif
            {{ csrf_field() }}
            <div class="formBlockWrapper">
                <div class="formUnit w100">
                    <div class="formLabel ">* Nom</div>
                    <input value="{{ old('name',  $formation->name) }}" class="inputFormAction formValue modifying w75" type="text" placeholder="Titre de la formation" name="name"/>
                </div>
                <div class="formUnit w100">
                    <div class="formLabel ">* Catégorie</div>
                    <select name="categories_formation_id" class="w75" style="padding: 5px;">
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('categories_formation_id', $formation->categories_formation_id) == $category->id ? 'selected=selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="formUnit w100">
                    <div class="formLabel ">* Type de formation</div>
                    <select name="type" class="w75" style="padding: 5px;" id="typeFormation">
                        <option value="0" {{ old('type', $formation->type) == 0 ? 'selected=selected' : '' }}>A distance</option>
                        <option value="1" {{ old('type', $formation->type) == 1 ? 'selected=selected' : '' }}>Présentiel</option>
                        <option value="2" {{ old('type', $formation->type) == 2 ? 'selected=selected' : '' }}>Les deux</option>
                    </select>
                    <div class="w100 helper">
                        Le type pourra être redéfini pour chaque session
                    </div>
                </div>
                <div class="formUnit w100 d-none-admin" id="divLocalisation">
                    <div class="formLabel ">Localisation</div>
                    <input value="{{ old('location',  $formation->location) }}" class="inputFormAction formValue modifying w75" type="text" placeholder="Localisation de la formation" name="location"/>
                    <div class="w100 helper">
                        La localisation pourra être redéfinie pour chaque session
                    </div>
                </div>
                <div class="formUnit w100">
                    <div class="formLabel ">* Niveau</div>
                    <select name="level" class="w75" style="padding: 5px;">
                        <option value="0" {{ old('level', $formation->level) == 0 ? 'selected=selected' : '' }}>Débutant</option>
                        <option value="1" {{ old('level', $formation->level) == 1 ? 'selected=selected' : '' }}>Intermédiaire</option>
                        <option value="2" {{ old('level', $formation->level) == 2 ? 'selected=selected' : '' }}>Confirmé</option>
                    </select>
                </div>
                <div class="formUnit w100 align-start">
                    <div class="formLabel">* Chapeau</div>
                    <div class="w75">
                        <textarea class="editor w75" name="shortDesc" rows="5">{!! old('shortDesc', $formation->shortDesc) !!}</textarea>
                    </div>
                </div>
                <div class="formUnit w100 align-start">
                    <div class="formLabel">Suite description</div>
                    <div class="w75">
                        <textarea class="editor w75" name="longDesc" rows="10">{!! old('longDesc', $formation->longDesc) !!}</textarea>
                    </div>
                </div>
                <div class="formUnit w100 align-start">
                    <div class="formLabel">Programme</div>
                    <div class="w75">
                        <textarea class="editor w75" name="program" rows="10">{!! old('program', $formation->program) !!}</textarea>
                    </div>
                </div>
                <div class="formUnit w100">
                    <div class="formLabel ">* Prix</div>
                    <input value="{{ old('price',  $formation->price) }}" class="inputFormAction formValue modifying w75" type="text" placeholder="Prix de la formation" name="price"/>
                    <div class="w100 helper">
                        Le prix pourra être redéfini pour chaque session
                    </div>
                </div>

                <div class="formUnit w100">
                    <div class="formLabel ">* Nombre de places</div>
                    <input value="{{ old('places',  $formation->places) }}" class="inputFormAction formValue modifying w75" type="text" placeholder="Nombre de places la formation" name="places"/>
                    <div class="w100 helper">
                        Le nombre de places pourra être redéfini pour chaque session
                    </div>
                </div>
                <div class="formUnit w100">
                    <div class="formLabel ">Places en attente</div>
                    <input value="{{ old('waiting_places',  $formation->waiting_places) }}" class="inputFormAction formValue modifying w75" type="text" placeholder="Places en liste d'attente" name="waiting_places"/>*
                    <div class="w100 helper">
                        Le nombre de places en liste d'attente pourra être redéfini pour chaque session
                    </div>
                </div>
                <div class="formUnit w100">
                    <div class="formLabel ">Durée</div>
                    <input value="{{ old('duration',  $formation->duration) }}" class="inputFormAction formValue modifying w75" type="text" placeholder="Durée de la formation" name="duration"/>
                </div>
                <div class="formUnit w100">
                    <div class="formLabel "></div>
                    <input class="mr25" type="checkbox" name="new" {{ old('new', $formation->new) == 1 ? 'checked=checked' : '' }} /> Afficher cette formation comme nouvelle
                </div>
            </div>
            <button type="submit" class="adminSuccess">Valider</button>
        </form>
</div>
