@if($prev == 'clubs')
    <form action="{{ route('clubs.adherents.storeExistingAdherent') }}" method="POST">
@else
    <form action="{{ route($prev.'.adherents.storeExistingAdherent', $club) }}" method="POST">
@endif
            {{ csrf_field() }}
            <div style="max-width: 1200px; margin: 20px auto">
                L'adhérent que vous souhaitez ajouter possède déjà un identifiant FPF (carte autre club ou individuelle) et vous connaissez cet identifiant ?<br>
                Vous pouvez l'inscrire en indiquant son identifiant FPF sans saisir l'ensemble des informations.
            </div>
            <div class="formBlock">
                <input type="hidden" name="prev" value="{{ $prev }}">
                <div class="formBlockTitle">Adhérent possédant déjà une carte ?</div>
                <div class="formBlockWrapper">

                    <div class="formLine">
                        <div class="formLine">
                            <div class="formLabel">Identifiant *</div>
                            <input class="inputFormAction formValue modifying w70" value="" name="identifiant" maxlength="12" type="text" required />
                        </div>
                    </div>
                </div>
                <div class="d-flex justify-center">
                    <button class="adminSuccess btnMedium" type="submit">Ajouter l'adhérent</button>
                </div>

            </div>
            <div style="border-bottom: 2px solid #003d77; max-width: 1200px; margin: 30px auto;">
            </div>
</form>
