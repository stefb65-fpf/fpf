<div class="formBlock">
    @if($action == 'update')
        <form method="POST" action="{{ route('evaluationsitems.update', $item->id) }}">
            <input type="hidden" name="_method" value="PUT">
    @else
        <form method="POST" action="{{ route('evaluationsitems.storeForTheme', $theme->id) }}">
    @endif
            {{ csrf_field() }}
            <div class="formBlockWrapper">
                <div class="formUnit w100">
                    <div class="formLabel ">* Item</div>
                    <input value="{{ old('libelle',  $item->name) }}" class="inputFormAction formValue modifying w75" type="text" placeholder="Nom de l'item" name="name"/>
                </div>
                <div class="formUnit w100">
                    <div class="formLabel ">* Position</div>
                    <input value="{{ old('libelle',  $item->position) }}" class="inputFormAction formValue modifying w75" type="text" placeholder="Position de l'item'" name="position"/>
                </div>
            </div>
            <button type="submit" class="adminSuccess">Valider</button>
        </form>
</div>
