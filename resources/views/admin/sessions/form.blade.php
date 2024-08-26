<div class="formBlock">
    @if($action == 'update')
        <form method="POST" action="{{ route('sessions.update', $session->id) }}">
            <input type="hidden" name="_method" value="PUT">
    @else
        <form method="POST" action="{{ route('sessions.store', $formation) }}">
        @endif
            {{ csrf_field() }}
            <div class="formBlockWrapper">
                <div class="formUnit w100">
                    <div class="formLabel ">UR</div>
                    <select name="ur_id" class="w75" style="padding: 5px;" >
                        <option value="0"></option>
                        @for($i=1; $i <= 25; $i++)
                            <option value="{{ $i }}" {{ old('ur_id', $session->ur_id) == $i ? 'selected=selected' : '' }}>{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}</option>
                        @endfor
                    </select>
                    <div class="w100 helper">
                        Ne saisir que si la session est organisée par une UR
                    </div>
                </div>
                <div class="formUnit w100">
                    <div class="formLabel ">Numéro de club</div>
                    <input value="{{ old('numero_club',  $session->numero_club) }}" class="inputFormAction formValue modifying w75" type="text" placeholder="Numéro du club organisateur" name="numero_club"/>
                    <div class="w100 helper">
                        Ne saisir que si la session est organisée par un club
                    </div>
                </div>
                <div class="formUnit w100">
                    <div class="formLabel ">* Type de session</div>
                    <select name="type" class="w75" style="padding: 5px;" id="typeFormation">
                        <option value="0" {{ old('type', $session->type) == 0 ? 'selected=selected' : '' }}>A distance</option>
                        <option value="1" {{ old('type', $session->type) == 1 ? 'selected=selected' : '' }}>Présentiel</option>
                        <option value="2" {{ old('type', $session->type) == 2 ? 'selected=selected' : '' }}>Les deux</option>
                    </select>
                </div>
                <div class="formUnit w100 {{ $session->type == 0 ? 'd-none-admin' : '' }}" id="divLocalisation">
                    <div class="formLabel ">Localisation</div>
                    <input value="{{ old('location',  $session->location) }}" class="inputFormAction formValue modifying w75" type="text" placeholder="Localisation de la session" name="location"/>
                </div>
                @if($formation->global_price > 0)
                    <div class="formUnit w100">
                        <div class="formLabel ">
                            Coût global formation: {{ $formation->global_price }}€ - Si la structure organisatrice prend en charge tout ou partie du financement,
                            vous devez modifier le prix d'inscription en conséquence selon la formule : (Coût global - Prise en charge) / Nombre de places
                        </div>
{{--                        <input value="{{ $formation->global_price }}" class="inputFormAction formValue modifying w75 disabled" readonly type="text" />--}}
                    </div>
                @endif
                <div class="formUnit w100">
                    <div class="formLabel ">Prise en charge</div>
                    <input value="{{ old('pec',  $session->pec) }}" class="inputFormAction formValue modifying w75" type="text" placeholder="Montant de la prise en charge" name="pec"/>
                    <div class="w100 helper">
                        Ne saisir que si la session est prise en charge par une structure. Ce montant de prise en charge sera indiqué sur l'interface des adhérents.
                    </div>
                </div>
                <div class="formUnit w100">
                    <div class="formLabel ">* Prix</div>
                    <input value="{{ old('price',  $session->price) }}" class="inputFormAction formValue modifying w75" type="text" placeholder="Prix de la formation" name="price"/>
                </div>
                <div class="formUnit w100">
                    <div class="formLabel ">* Prix non adhérent</div>
                    <input value="{{ old('price_not_member',  $session->price_not_member) }}" class="inputFormAction formValue modifying w75" type="text" placeholder="Prix de la formation pour non adhérent" name="price_not_member"/>
                </div>
                <div class="formUnit w100">
                    <div class="formLabel ">* Nombre de places</div>
                    <input value="{{ old('places',  $session->places) }}" class="inputFormAction formValue modifying w75" type="text" placeholder="Nombre de places la formation" name="places"/>
                </div>
                <div class="formUnit w100">
                    <div class="formLabel ">Places en attente</div>
                    <input value="{{ old('waiting_places',  $session->waiting_places) }}" class="inputFormAction formValue modifying w75" type="text" placeholder="Places en liste d'attente" name="waiting_places"/>
                </div>
                <div class="formUnit w100">
                    <div class="formLabel ">* Date de début</div>
                    <input value="{{ old('start_date',  $session->start_date) }}" class="inputFormAction formValue modifying w75" type="date" name="start_date"/>
                </div>
                <div class="formUnit w100">
                    <div class="formLabel ">Date de fin</div>
                    <input value="{{ old('end_date',  $session->end_date) }}" class="inputFormAction formValue modifying w75" type="date" name="end_date"/>
                    <div class="w100 helper">
                        Laissez vide si la formation se déroule sur une seule journée
                    </div>
                </div>

                <div class="formUnit w100">
                    <div class="formLabel ">Fin d'inscription</div>
                    <input value="{{ old('end_inscription',  $session->end_inscription) }}" class="inputFormAction formValue modifying w75" type="date" name="end_inscription"/>
                    <div class="w100 helper">
                        Si vous laisse vide, une date par défaut à J-2 sera attribuée. Ainsi, pour une formation commençant le 10/10/2025, la date de fin d'inscription sera le 08/10/2025 à 23h59
                    </div>
                </div>

            </div>
            <button type="submit" class="adminSuccess">Valider</button>
        </form>
</div>
