<div class="formBlock">
    <div class="formBlockTitle">Gestion de fonctions fédérales</div>
    @if($action == 'update')
        <form method="POST" action="{{ route('fonctions.update', $fonction) }}">
            <input type="hidden" name="_method" value="PUT">
    @else
            <form method="POST" action="{{ route('fonctions.store') }}">
    @endif
        {{ csrf_field() }}
            <div class="formBlockWrapper">
                <div class="formUnit w100">
                    <div class="formLabel ">* Libellé de la fonction</div>
                    <input value="{{ $fonction->libelle }}" class="inputFormAction formValue modifying w75" type="text" placeholder="Libellé de la fonction à ajouter / modifier" name="libelle"/>
                </div>
            </div>
            <div class="formBlockWrapper">
                <div class="formUnit w100">
                    <div class="formLabel ">Email de la fonction</div>
                    <input value="{{ $fonction->courriel }}" class="inputFormAction formValue modifying w75" type="text" placeholder="Email lié à la fonction" name="courriel" />
                </div>
            </div>
            <div class="formBlockWrapper">
                <div class="formUnit w100">
                    <div class="formLabel ">Fonction liée au CE ?</div>
                    <input class="inputFormAction" type="checkbox" name="ce" {{ $fonction->ce == 1 ? 'checked=checked' : '' }} />
                </div>
            </div>
            <div class="formBlockWrapper">
                <div class="formUnit w100">
                    <div class="formLabel ">Adhérent en charge de la fonction</div>
                    <input value="{{ $fonction->utilisateur ? $fonction->utilisateur->identifiant : '' }}" class="inputFormAction formValue modifying w75" type="text" placeholder="Identifiant adhérent lié à la fonction" name="identifiant" maxlength="12"/>
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
