<div class="formBlock">
    <div class="formBlockTitle">Gestion de fonctions fédérales</div>
    @if($action == 'update')
        <form method="POST" action="{{ route('fonctions.update', $fonction) }}">
            <input type="hidden" name="_method" value="PUT">
    @else
            <form method="POST" action="{{ route('fonctions.store') }}">
    @endif
        {{ csrf_field() }}
            <div class="formBlockWrapper inline">
                <div class="formUnit">
                    <div class="formLabel" style="width: 300px">* Libellé de la fonction</div>
                    <input value="{{ $fonction->libelle }}" class="inputFormAction" type="text" placeholder="Libellé de la fonction à ajouter / modifier" name="libelle" style="width: 300px" />
                </div>
            </div>
            <div class="formBlockWrapper inline">
                <div class="formUnit">
                    <div class="formLabel" style="width: 300px">Email de la fonction</div>
                    <input value="{{ $fonction->courriel }}" class="inputFormAction" type="text" placeholder="Email lié à la fonction" name="courriel" style="width: 300px" />
                </div>
            </div>
            <div class="formBlockWrapper inline">
                <div class="formUnit">
                    <div class="formLabel" style="width: 300px">Fonction liée au CE ?</div>
                    <input class="inputFormAction" type="checkbox" name="ce" {{ $fonction->ce == 1 ? 'checked=checked' : '' }} />
                </div>
            </div>
            <div class="formBlockWrapper inline">
                <div class="formUnit">
                    <div class="formLabel" style="width: 300px">Adhérent en charge de la fonction</div>
                    <input value="{{ $fonction->utilisateur ? $fonction->utilisateur->identifiant : '' }}" class="inputFormAction" type="text" placeholder="Identifiant adhérent lié à la fonction" name="identifiant" maxlength="12" style="width: 300px" />
                </div>
            </div>
            <div style="display: flex; justify-content: center;">
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
