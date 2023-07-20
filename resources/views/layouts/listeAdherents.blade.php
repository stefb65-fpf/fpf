<div class="alertInfo" style="width: 80% !important">
    <span class="bold">Informations !</span>
    Ici vous avez la possibilité d'afficher la liste des adhérents du club et de les filtrer en fonction de leur statut et de leur abonnement.
</div>
<div class="alertSuccess" style="width: 80% !important; display: none;" id="alertAdherentsList">
    Le fichier de routage a bien été généré. Vous pouvez le télécharger en cliquant sur le lient suivant: <a id="linkAdherentsList" target="_blank" style="cursor: pointer;text-decoration: underline;">Télécharger le fichier</a>
</div>
<div class="filters d-flex">
    <div class="formBlock" style="max-width: 100%">
        <div class="formBlockTitle">Filtres</div>
        <div class="d-flex flexWrap">
            <div class="formUnit mb0">
                <div class="formLabel mr10 bold">Statut :</div>
                <select class="formValue modifying" name="filter" data-ref="statut">
                    <option value="all">Tous</option>
                    <option value="2" {{$statut == 2? "selected":""}}>Validé</option>
                    <option value="1" {{$statut == 1? "selected":""}}>Pré-inscrit</option>
                    <option value="0" {{$statut == 0? "selected":""}}>Non renouvelé</option>
                    <option value="3" {{$statut == 3? "selected":""}}>Carte éditée</option>
                    <option value="4" {{$statut == 4? "selected":""}}>Anciens</option>
                </select>
            </div>
            <div class="formUnit mb0">
                <div class="formLabel mr10 bold">Abonnement :</div>
                <select class="formValue modifying" name="filter" data-ref="abonnement">
                    <option value="all">Tous</option>
                    <option value="1" {{$abonnement== 1? "selected":""}}>Avec</option>
                    <option value="0" {{$abonnement== 0? "selected":""}}>Sans</option>
                    {{--                        <option value="G" {{$abonnement== "G"? "selected":""}}>Gratuits</option>--}}
                </select>
            </div>
        </div>
    </div>
</div>
@if(!sizeof($adherents))
    Ce club ne possède aucun adhérent répondant aux critères selectionnés.
@else
    <div class="mt25 flexEnd">
        <button class="adminPrimary btnMedium" type="text" id="btnAdherentsList" data-club="{{$club->id}}">Exporter au format Excel</button>
    </div>
    <table class="styled-table">
        <thead>
        <tr>
            <th>N°carte</th>
            <th>Nom</th>
            <th>Statut</th>
            <th>Courriel</th>
            <th>Abonnement - N° fin</th>
            <th>Type carte</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        @foreach($adherents as $adherent)
            <tr>
                <td>{{$adherent->identifiant}}</td>
                <td>{{$adherent->personne->nom}} {{$adherent->personne->prenom}} </td>
                <td>
                    @switch($adherent->statut)
                        @case(0)
                        <div class="d-flex">
                            <div class="sticker orange" title="Non renouvelé"></div>
                        </div>
                        @break
                        @case(1)
                        <div class="d-flex">
                            <div class="sticker yellow" title="Préinscrit"></div>
                        </div>
                        @break
                        @case(2)
                        <div class="d-flex">
                            <div class="sticker green" title="Validé"></div>
                        </div>
                        @break
                        @case(3)
                        <div class="d-flex">
                            <div class="sticker green" title="Carte éditée"></div>
                        </div>
                        @break
                        @case(4)
                        <div class="d-flex">
                            <div class="sticker" title="Carte non renouvelée depuis plus d'un an"></div>
                        </div>
                        @break
                        @default
                        <div>Non renseigné</div>
                    @endswitch
                </td>
                <td><a href="mailto:{{$adherent->personne->email}}">{{$adherent->personne->email}}</a></td>
                <td>
                    {{ $adherent->fin?:"" }}
                </td>
                <td>
                    @switch($adherent->ct)
                        @case(2)
                        <div> > 25ans</div>
                        @break
                        @case(3)
                        <div>adhérent 18-25 ans</div>
                        @break
                        @case(4)
                        <div>adhérent<18ans</div>
                        @break
                        @case(5)
                        <div>adhérent famille</div>
                        @break
                        @case(6)
                        <div>adhérent 2eme club</div>
                        @break
                        @default
                        <div>Non renseigné</div>
                    @endswitch
                </td>
                <td>
                    <div style="margin-bottom: 3px;">
                        <a href="" class="adminPrimary btnSmall">action</a>
                    </div>
                    <div style="margin-bottom: 3px;">
                        <a href="" class="adminSuccess btnSmall">action</a>
                    </div>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endif
@section('js')
    <script src="{{ asset('js/filters-club-liste-adherent.js') }}?t=<?= time() ?>"></script>
    <script src="{{ asset('js/excel_adherent_file.js') }}?t=<?= time() ?>"></script>
@endsection
