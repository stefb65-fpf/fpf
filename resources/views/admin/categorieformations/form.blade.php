<div class="formBlock">
    @if($action == 'update')
        <form method="POST" action="{{ route('categoriesformations.update', $categorie->id) }}">
            <input type="hidden" name="_method" value="PUT">
    @else
        <form method="POST" action="{{ route('categoriesformations.store') }}">
    @endif
            {{ csrf_field() }}
            <div class="formBlockWrapper">
                <div class="formUnit w100">
                    <div class="formLabel ">* Catégorie</div>
                    <input value="{{ old('libelle',  $categorie->name) }}" class="inputFormAction formValue modifying w75" type="text" placeholder="Nom de la catégorie" name="name"/>
                </div>
            </div>
            <button type="submit" class="adminSuccess">Valider</button>
        </form>
</div>
