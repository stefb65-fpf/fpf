<div class="formBlock">
    <div class="formBlockTitle">Gestion de fonctions régionales</div>
    @if($action == 'update')
        <form method="POST" action="{{ route('fonctions.update_ur', $fonction) }}">
            <input type="hidden" name="_method" value="PUT">
    @else
            <form method="POST" action="{{ route('fonctions.store_ur') }}">
    @endif
        {{ csrf_field() }}
            <div class="formBlockWrapper inline">
                <div class="formUnit">
                    <div class="formLabel w300">* Libellé de la fonction</div>
                    <input value="{{ $fonction->libelle }}" class="inputFormAction w300" type="text" placeholder="Libellé de la fonction à ajouter / modifier" name="libelle"/>
                </div>
            </div>
            <div class="d-flex justify-center">
                <button class="adminSuccess btnMedium">
                    @if($action == 'update')
                        Modifier la fonction
                    @else
                        Ajouter la fonction
                    @endif
                </button>
            </div>
        </form>
    </div>
</div>
