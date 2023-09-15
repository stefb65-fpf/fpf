<div class="formBlock">
    <div class="formBlockTitle">Session de vote</div>
@if($action == 'store')
    <form action="{{ route('votes.store') }}" method="POST" enctype="multipart/form-data">
@elseif($action == 'update')
    <form action="{{ route('votes.update', $vote) }}" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="_method" value="put">
@endif
{{ csrf_field() }}
        <div class="formBlockWrapper">
            <div class="formUnit formUnitAdmin">
                <div class="formLabel">Nom</div>
                <input class="formValue modifying formValueAdmin w75" type="text" value="{{ old('nom', $vote->nom) }}" name="nom"/>
            </div>
            <div class="formUnit formUnitAdmin">
                <div class="formLabel">Type de vote</div>
                <select class="formValue modifying formValueAdmin" name="type" id="typeVote">
                    <option value="-1"></option>
                    <option value="0" {{ $vote->type === 0 ? 'selected=selected' : '' }}>Classique</option>
                    <option value="1" {{ $vote->type === 1 ? 'selected=selected' : '' }}>3 phases</option>
                </select>
            </div>
        </div>
        <div id="bloc3phases" style="{{ $vote->type === 1 ? '' : 'display: none' }}" class="formBlockWrapper">
            <div class="formUnit formUnitAdmin">
                <div class="formLabel">Début phase 1</div>
                <input class="formValue modifying formValueAdmin"  value="{{ old('debut_phase1', $vote->debut) }}" name="debut_phase1" type="date"/>
            </div>
            <div class="formUnit formUnitAdmin">
                <div class="formLabel">Fin phase 1</div>
                <input class="formValue modifying formValueAdmin"  value="{{ old('fin_phase1', $vote->fin_phase1) }}" name="fin_phase1" type="date"/>
            </div>
            <div class="formUnit formUnitAdmin">
                <div class="formLabel">Début phase 2</div>
                <input class="formValue modifying formValueAdmin"  value="{{ old('debut_phase2', $vote->debut_phase2) }}" name="debut_phase2" type="date"/>
            </div>
            <div class="formUnit formUnitAdmin">
                <div class="formLabel">Fin phase 2</div>
                <input class="formValue modifying formValueAdmin"  value="{{ old('fin_phase2', $vote->fin_phase2) }}" name="fin_phase2" type="date"/>
            </div>
            <div class="formUnit formUnitAdmin">
                <div class="formLabel">Début phase 3</div>
                <input class="formValue modifying formValueAdmin"  value="{{ old('debut_phase3', $vote->debut_phase3) }}" name="debut_phase3" type="date"/>
            </div>
            <div class="formUnit formUnitAdmin">
                <div class="formLabel">Fin phase 3</div>
                <input class="formValue modifying formValueAdmin"  value="{{ old('fin_phase3', $vote->fin) }}" name="fin_phase3" type="date"/>
            </div>
        </div>

        <div id="blocclassique" style="{{ $vote->type === 0 ? '' : 'display: none' }}"  class="formBlockWrapper">
            <div class="formUnit formUnitAdmin">
                <div class="formLabel">Date de début</div>
                <input class="formValue modifying formValueAdmin"  value="{{ old('debut', $vote->debut) }}" name="debut" type="date"/>
            </div>
            <div class="formUnit formUnitAdmin">
                <div class="formLabel">Date de fin</div>
                <input class="formValue modifying formValueAdmin"  value="{{ old('fin', $vote->fin) }}" name="fin" type="date"/>
            </div>
            <div class="formUnit formUnitAdmin">
                <div class="formLabel">Portée du vote</div>
                <select class="formValue modifying formValueAdmin" name="urs_id">
                    <option value="0" {{ $vote->urs_id == 0 ? 'selected=selected' : '' }}>Vote national</option>
                    @for($i = 1; $i <= 25; $i++)
                        <option value="{{ $i }}"  {{ $vote->urs_id == $i ? 'selected=selected' : '' }}>Vote UR {{ str_pad($i, 2, '0', STR_PAD_LEFT) }}</option>
                    @endfor
                </select>
            </div>
            <div class="formUnit formUnitAdmin">
                <div class="formLabel">Fonctions</div>
                <select class="formValue modifying formValueAdmin" name="fonctions_id">
                    <option value="0" {{ $vote->fonctions_id === 0 ? 'selected=selected' : '' }}>Tous les adhérents</option>
                    <option value="57" {{ $vote->fonctions_id === 57 ? 'selected=selected' : '' }}>Présidents d'UR</option>
                    <option value="94" {{ $vote->fonctions_id === 94 ? 'selected=selected' : '' }}>Présidents de club</option>
                    <option value="9999" {{ $vote->fonctions_id === 9999 ? 'selected=selected' : '' }}>CA</option>
                </select>
            </div>
            <div class="formUnit formUnitAdmin">
                <div class="formLabel">Vote club</div>
                <select class="formValue modifying formValueAdmin" name="vote_club">
                    <option value="0" {{ $vote->vote_club === 0 ? 'selected=selected' : '' }}>Pas de vote club</option>
                    <option value="1" {{ $vote->vote_club === 1 ? 'selected=selected' : '' }}>Vote club par le président ou le contact</option>
                </select>
            </div>
        </div>

        <button type="submit" class="formBtn success" style="{{ $action == 'store' ? 'display: none' : '' }}" id="validateVote">Valider
        </button>
    </form>

    </div>
</div>
