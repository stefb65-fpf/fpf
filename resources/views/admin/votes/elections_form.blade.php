<div class="formBlock">
    <div class="formBlockTitle">Session de vote</div>
    @if($action == 'store')
        <form action="{{ route('votes.elections.store', $vote) }}" method="POST" enctype="multipart/form-data">
    @elseif($action == 'update')
            <form action="{{ route('votes.elections.update', [$vote, $election]) }}" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="_method" value="put">
    @endif
            {{ csrf_field() }}
            <div class="formBlockWrapper">
                <div class="formUnit formUnitAdmin">
                    <div class="formLabel">Nom</div>
                    <input class="formValue modifying formValueAdmin w75" type="text" value="{{ old('nom', $election->nom) }}" name="nom"/>
                </div>
                <div class="formUnit formUnitAdmin">
                    <div class="formLabel">Type d'élection'</div>
                    <select class="formValue modifying formValueAdmin" name="type" id="typeElection">
                        <option value="1" {{ $election->type === 1 ? 'selected=selected' : '' }}>Adoption d'une motion</option>
                        <option value="2" {{ $election->type === 2 ? 'selected=selected' : '' }}>&Eacute;lection de candidats</option>
                    </select>
                </div>
                <div class="formUnit formUnitAdmin" id="nbPostesElection" style="{{ $action === 'update' && $election->type === 2 ? '' : 'display: none' }}">
                    <div class="formLabel">Nombre de postes</div>
                    <input class="formValue modifying formValueAdmin" type="number" min="0" value="{{ old('nb_postes', $election->nb_postes) }}" name="nb_postes"/>
                </div>
                <div class="formUnit formUnitAdmin" id="nbPostesElection">
                    <div class="formLabel">Description</div>
                    <textarea class="editor w75" name="contenu" rows="10">{!! old('contenu', $election->contenu) !!}</textarea>
                </div>
                <div class="formUnit formUnitAdmin">
                    <div class="formLabel">Ordre d'apparition</div>
                    <input class="formValue modifying formValueAdmin" type="number" min="0" value="{{ old('ordre', $election->ordre) }}" name="ordre"/>
                </div>
            </div>
            <div class="mt25">
                <button type="submit" class="formBtn success">Valider
                </button>
            </div>
        </form>
    </div>
</div>
