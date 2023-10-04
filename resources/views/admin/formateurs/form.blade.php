<div class="formBlock">
    @if($action == 'update')
        <form method="POST" action="{{ route('formateurs.update', $formateur->id) }}">
            <input type="hidden" name="_method" value="PUT">
    @else
        <form method="POST" action="{{ route('formateurs.store') }}">
    @endif
            {{ csrf_field() }}
            <div class="formBlockWrapper">
                <div class="formUnit w100">
                    <div class="formLabel ">* Nom</div>
                    <input value="{{ old('nom',  $formateur->personne->nom) }}" maxlength="250" class="inputFormAction formValue modifying w75" type="text" placeholder="Nom du formateur" name="nom"/>
                </div>
                <div class="formUnit w100">
                    <div class="formLabel ">* Prénom</div>
                    <input value="{{ old('prenom',  $formateur->personne->prenom) }}" maxlength="250" class="inputFormAction formValue modifying w75" type="text" placeholder="Prénom du formateur" name="prenom"/>
                </div>
                <div class="formUnit w100">
                    <div class="formLabel ">* Email</div>
                    <input value="{{ old('email',  $formateur->personne->email) }}" maxlength="250" class="inputFormAction formValue modifying w75" {{ $action == 'update' ? 'readonly' : '' }} type="text" placeholder="Email du formateur" name="email"/>
                </div>
                <div class="formUnit w100">
                    <div class="formLabel ">* Téléphone mobile</div>
                    <input value="{{ old('phone_mobile',  $formateur->personne->phone_mobile) }}" maxlength="25" class="inputFormAction formValue modifying w75" type="text" placeholder="Téléphone mobile du formateur" name="phone_mobile"/>
                </div>
                <div class="formUnit w100">
                    <div class="formLabel ">Titre</div>
                    <input value="{{ old('title',  $formateur->title) }}" maxlength="250" class="inputFormAction formValue modifying w75" type="text" placeholder="Titre du formateur" name="title"/>
                </div>
                <div class="formUnit w100 align-start">
                    <div class="formLabel">Biographie</div>
                    <div class="w75">
                        <textarea class="editor w75" name="cv" rows="5">{!! old('cv', $formateur->cv) !!}</textarea>
                    </div>
                </div>
                <div class="formUnit w100">
                    <div class="formLabel ">Site web</div>
                    <input value="{{ old('website',  $formateur->website) }}" maxlength="250" class="inputFormAction formValue modifying w75" type="text" placeholder="Site web du formateur" name="website"/>
                </div>
            </div>
            <button type="submit" class="adminSuccess">Valider</button>
        </form>
</div>
