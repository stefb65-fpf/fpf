@extends('layouts.default')

@section('content')
    <div class="pageCanva">
        <h1 class="pageTitle">
            <div>Gestion Club - Fonctions
                <div class="urTitle">
                    {{ $club->nom }}
                </div>
            </div>

            <a class="previousPage" title="Retour page précédente" href="{{ route('admin.clubs.index')}}">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor"
                     class="bi bi-reply-fill" viewBox="0 0 16 16">
                    <path
                        d="M5.921 11.9 1.353 8.62a.719.719 0 0 1 0-1.238L5.921 4.1A.716.716 0 0 1 7 4.719V6c1.5 0 6 0 7 8-2.5-4.5-7-4-7-4v1.281c0 .56-.606.898-1.079.62z"/>
                </svg>
            </a>
        </h1>

        <div class="formBlock relative">
            <div class="formBlockTitle">Président</div>
            <div class="formBlockWrapper align-start">
                @if(isset($tab_fonctions[94]))
                    <div class="btnWrapper d-flex relative w100">
                        <form action="{{route('admin.updateFonctionClub',[$club->id,$tab_fonctions[94]->id_utilisateur,94])}}"
                              method="POST">
                            <input type="hidden" name="_method" value="put">
                            {{ csrf_field() }}
                            <button class="attention formBtn relative mr25 btnSmall" name="showSelect">changer</button>
                            <select class="formValue adherent modifying floating hidden" name="adherent_id" required
                                    onchange="this.form.submit()">
                                <option value="">Selectionnez un adhérent</option>
                                @foreach($adherents as $adherent)
                                    <option
                                        value="{{$adherent->id}}" {{$adherent->id == $tab_fonctions[94]->id_utilisateur? "selected":""}} >{{$adherent->nom ?:$adherent->nom}}
                                        - {{$adherent->prenom ?:$adherent->prenom}}
                                        - {{$adherent->identifiant ?:$adherent->identifiant}}</option>
                                @endforeach
                            </select>
                        </form>
                        <form action="{{route('admin.deleteFonctionClub',[$club->id, $tab_fonctions[94]->id_utilisateur,94])}}"
                              method="POST">
                            <input type="hidden" name="_method" value="delete">
                            {{ csrf_field() }}
                            <button class="danger formBtn relative btnSmall" type="submit">supprimer</button>
                        </form>
                    </div>
                    <div class="formBlockWrapper inline">
                        @if($tab_fonctions[94]->nom)
                            <div class="formUnit mr25 ml0">
                                <label class="formLabel" for="nom"> Nom </label>
                                <div> {{$tab_fonctions[94]->nom}} </div>
                            </div>
                        @endif
                        @if($tab_fonctions[94]->prenom)
                            <div class="formUnit mr25 ml0">
                                <label class="formLabel" for="nom"> Prénom </label>
                                <div> {{$tab_fonctions[94]->prenom}} </div>
                            </div>
                        @endif
                        @if($tab_fonctions[94]->identifiant)
                            <div class="formUnit mr25 ml0">
                                <label class="formLabel" for="nom"> Identifiant </label>
                                <div> {{$tab_fonctions[94]->identifiant}} </div>
                            </div>
                        @endif
                        @if($tab_fonctions[94]->email)
                            <div class="formUnit mr25 ml0">
                                <label class="formLabel" for="nom"> Courriel </label>
                                <div> {{$tab_fonctions[94]->email}} </div>
                            </div>
                        @endif

                    </div>
                @else
                    <div class="btnWrapper d-flex relative w100">
                        <form action="{{route('admin.addFonctionClub',[$club->id, 94])}}" method="POST">
                            <input type="hidden" name="_method" value="put">
                            {{ csrf_field() }}
                            <button class="success formBtn relative mr25 btnSmall" name="showSelect">Ajouter</button>
                            <select class="formValue adherent modifying floating hidden" name="adherent_id"
                                    onchange="this.form.submit()" required>
                                <option value="">Selectionnez un adhérent</option>
                                @foreach($adherents as $adherent)
                                    <option value="{{$adherent->id}}">{{$adherent->nom ?:$adherent->nom}}
                                        - {{$adherent->prenom ?:$adherent->prenom}}
                                        - {{$adherent->identifiant ?:$adherent->identifiant}}</option>
                                @endforeach
                            </select>
                        </form>
                    </div>
                @endif
            </div>
        </div>
        <div class="formBlock relative">
            <div class="formBlockTitle">Contact</div>
            <div class="formBlockWrapper align-start">
                @if(isset($tab_fonctions[97]))
                    <div class="btnWrapper d-flex relative w100">
                        <form action="{{route('admin.updateFonctionClub',[$club->id,$tab_fonctions[97]->id_utilisateur,97])}}"
                              method="POST">
                            <input type="hidden" name="_method" value="put">
                            {{ csrf_field() }}
                            <button class="attention formBtn relative mr25 btnSmall" name="showSelect">changer</button>
                            <select class="formValue adherent modifying floating hidden" name="adherent_id" required
                                    onchange="this.form.submit()">
                                <option value="">Selectionnez un adhérent</option>
                                @foreach($adherents as $adherent)
                                    <option
                                        value="{{$adherent->id}}" {{$adherent->id == $tab_fonctions[97]->id_utilisateur? "selected":""}} >{{$adherent->nom ?:$adherent->nom}}
                                        - {{$adherent->prenom ?:$adherent->prenom}}
                                        - {{$adherent->identifiant ?:$adherent->identifiant}}</option>
                                @endforeach
                            </select>
                        </form>
                    </div>
                    <div class="formBlockWrapper inline">
                        @if($tab_fonctions[97]->nom)
                            <div class="formUnit mr25 ml0">
                                <label class="formLabel" for="nom"> Nom </label>
                                <div> {{$tab_fonctions[97]->nom}} </div>
                            </div>
                        @endif
                        @if($tab_fonctions[97]->prenom)
                            <div class="formUnit mr25 ml0">
                                <label class="formLabel" for="nom"> Prénom </label>
                                <div> {{$tab_fonctions[97]->prenom}} </div>
                            </div>
                        @endif
                        @if($tab_fonctions[97]->identifiant)
                            <div class="formUnit mr25 ml0">
                                <label class="formLabel" for="nom"> Identifiant </label>
                                <div> {{$tab_fonctions[97]->identifiant}} </div>
                            </div>
                        @endif
                        @if($tab_fonctions[97]->email)
                            <div class="formUnit mr25 ml0">
                                <label class="formLabel" for="nom"> Courriel </label>
                                <div> {{$tab_fonctions[97]->email}} </div>
                            </div>
                        @endif

                    </div>
                @else
                    <div class="btnWrapper d-flex relative w100">
                        <form action="{{route('admin.addFonctionClub',[$club->id, 97])}}" method="POST">
                            <input type="hidden" name="_method" value="put">
                            {{ csrf_field() }}
                            <button class="success formBtn relative mr25 btnSmall" name="showSelect">Ajouter</button>
                            <select class="formValue adherent modifying floating hidden" name="adherent_id"
                                    onchange="this.form.submit()" required>
                                <option value="">Selectionnez un adhérent</option>
                                @foreach($adherents as $adherent)
                                    <option value="{{$adherent->id}}">{{$adherent->nom ?:$adherent->nom}}
                                        - {{$adherent->prenom ?:$adherent->prenom}}
                                        - {{$adherent->identifiant ?:$adherent->identifiant}}</option>
                                @endforeach
                            </select>
                        </form>
                    </div>
                @endif
            </div>
        </div>
        <div class="formBlock relative">
            <div class="formBlockTitle">Trésorier</div>
            <div class="formBlockWrapper align-start">
                @if(isset($tab_fonctions[95]))
                    <div class="btnWrapper d-flex relative w100">
                        <form action="{{route('admin.updateFonctionClub',[$club->id,$tab_fonctions[95]->id_utilisateur,95])}}"
                              method="POST">
                            <input type="hidden" name="_method" value="put">
                            {{ csrf_field() }}
                            <button class="attention formBtn relative mr25 btnSmall" name="showSelect">changer</button>
                            <select class="formValue adherent modifying floating hidden" name="adherent_id" required
                                    onchange="this.form.submit()">
                                <option value="">Selectionnez un adhérent</option>
                                @foreach($adherents as $adherent)
                                    <option
                                        value="{{$adherent->id}}" {{$adherent->id == $tab_fonctions[95]->id_utilisateur? "selected":""}} >{{$adherent->nom ?:$adherent->nom}}
                                        - {{$adherent->prenom ?:$adherent->prenom}}
                                        - {{$adherent->identifiant ?:$adherent->identifiant}}</option>
                                @endforeach
                            </select>
                        </form>
                        <form action="{{route('admin.deleteFonctionClub',[$club->id, $tab_fonctions[95]->id_utilisateur,95])}}"
                              method="POST">
                            <input type="hidden" name="_method" value="delete">
                            {{ csrf_field() }}
                            <button class="danger formBtn relative btnSmall" type="submit">supprimer</button>
                        </form>
                    </div>
                    <div class="formBlockWrapper inline">
                        @if($tab_fonctions[95]->nom)
                            <div class="formUnit mr25 ml0">
                                <label class="formLabel" for="nom"> Nom </label>
                                <div> {{$tab_fonctions[95]->nom}} </div>
                            </div>
                        @endif
                        @if($tab_fonctions[95]->prenom)
                            <div class="formUnit mr25 ml0">
                                <label class="formLabel" for="nom"> Prénom </label>
                                <div> {{$tab_fonctions[95]->prenom}} </div>
                            </div>
                        @endif
                        @if($tab_fonctions[95]->identifiant)
                            <div class="formUnit mr25 ml0">
                                <label class="formLabel" for="nom"> Identifiant </label>
                                <div> {{$tab_fonctions[95]->identifiant}} </div>
                            </div>
                        @endif
                        @if($tab_fonctions[95]->email)
                            <div class="formUnit mr25 ml0">
                                <label class="formLabel" for="nom"> Courriel </label>
                                <div> {{$tab_fonctions[95]->email}} </div>
                            </div>
                        @endif

                    </div>
                @else
                    <div class="btnWrapper d-flex relative w100">
                        <form action="{{route('admin.addFonctionClub',[$club->id, 95])}}" method="POST">
                            <input type="hidden" name="_method" value="put">
                            {{ csrf_field() }}
                            <button class="success formBtn relative mr25 btnSmall" name="showSelect">Ajouter</button>
                            <select class="formValue adherent modifying floating hidden" name="adherent_id"
                                    onchange="this.form.submit()" required>
                                <option value="">Selectionnez un adhérent</option>
                                @foreach($adherents as $adherent)
                                    <option value="{{$adherent->id}}">{{$adherent->nom ?:$adherent->nom}}
                                        - {{$adherent->prenom ?:$adherent->prenom}}
                                        - {{$adherent->identifiant ?:$adherent->identifiant}}</option>
                                @endforeach
                            </select>
                        </form>
                    </div>
                @endif
            </div>
        </div>
        <div class="formBlock relative">
            <div class="formBlockTitle">Secrétaire</div>
            <div class="formBlockWrapper align-start">
                @if(isset($tab_fonctions[96]))
                    <div class="btnWrapper d-flex relative w100">
                        <form action="{{route('admin.updateFonctionClub',[$club->id,$tab_fonctions[96]->id_utilisateur,96])}}"
                              method="POST">
                            <input type="hidden" name="_method" value="put">
                            {{ csrf_field() }}
                            <button class="attention formBtn relative mr25 btnSmall" name="showSelect">changer</button>
                            <select class="formValue adherent modifying floating hidden" name="adherent_id" required
                                    onchange="this.form.submit()">
                                <option value="">Selectionnez un adhérent</option>
                                @foreach($adherents as $adherent)
                                    <option
                                        value="{{$adherent->id}}" {{$adherent->id == $tab_fonctions[96]->id_utilisateur? "selected":""}} >{{$adherent->nom ?:$adherent->nom}}
                                        - {{$adherent->prenom ?:$adherent->prenom}}
                                        - {{$adherent->identifiant ?:$adherent->identifiant}}</option>
                                @endforeach
                            </select>
                        </form>
                        <form action="{{route('admin.deleteFonctionClub',[$club->id, $tab_fonctions[96]->id_utilisateur,96])}}"
                              method="POST">
                            <input type="hidden" name="_method" value="delete">
                            {{ csrf_field() }}
                            <button class="danger formBtn relative btnSmall" type="submit">supprimer</button>
                        </form>
                    </div>
                    <div class="formBlockWrapper inline">
                        @if($tab_fonctions[96]->nom)
                            <div class="formUnit mr25 ml0">
                                <label class="formLabel" for="nom"> Nom </label>
                                <div> {{$tab_fonctions[96]->nom}} </div>
                            </div>
                        @endif
                        @if($tab_fonctions[96]->prenom)
                            <div class="formUnit mr25 ml0">
                                <label class="formLabel" for="nom"> Prénom </label>
                                <div> {{$tab_fonctions[96]->prenom}} </div>
                            </div>
                        @endif
                        @if($tab_fonctions[96]->identifiant)
                            <div class="formUnit mr25 ml0">
                                <label class="formLabel" for="nom"> Identifiant </label>
                                <div> {{$tab_fonctions[96]->identifiant}} </div>
                            </div>
                        @endif
                        @if($tab_fonctions[96]->email)
                            <div class="formUnit mr25 ml0">
                                <label class="formLabel" for="nom"> Courriel </label>
                                <div> {{$tab_fonctions[96]->email}} </div>
                            </div>
                        @endif

                    </div>
                @else
                    <div class="btnWrapper d-flex relative w100">
                        <form action="{{route('admin.addFonctionClub',[$club->id, 96])}}" method="POST">
                            <input type="hidden" name="_method" value="put">
                            {{ csrf_field() }}
                            <button class="success formBtn relative mr25 btnSmall" name="showSelect">Ajouter</button>
                            <select class="formValue adherent modifying floating hidden" name="adherent_id"
                                    onchange="this.form.submit()" required>
                                <option value="">Selectionnez un adhérent</option>
                                @foreach($adherents as $adherent)
                                    <option value="{{$adherent->id}}">{{$adherent->nom ?:$adherent->nom}}
                                        - {{$adherent->prenom ?:$adherent->prenom}}
                                        - {{$adherent->identifiant ?:$adherent->identifiant}}</option>
                                @endforeach
                            </select>
                        </form>
                    </div>
                @endif
            </div>
        </div>
        <div class="formBlock relative">
            <div class="formBlockTitle">Webmaster</div>
            <div class="formBlockWrapper align-start">
                @if(isset($tab_fonctions[320]))
                    <div class="btnWrapper d-flex relative w100">
                        <form action="{{route('admin.updateFonctionClub',[$club->id,$tab_fonctions[320]->id_utilisateur,320])}}"
                              method="POST">
                            <input type="hidden" name="_method" value="put">
                            {{ csrf_field() }}
                            <button class="attention formBtn relative mr25 btnSmall" name="showSelect">changer</button>
                            <select class="formValue adherent modifying floating hidden" name="adherent_id" required
                                    onchange="this.form.submit()">
                                <option value="">Selectionnez un adhérent</option>
                                @foreach($adherents as $adherent)
                                    <option
                                        value="{{$adherent->id}}" {{$adherent->id == $tab_fonctions[320]->id_utilisateur? "selected":""}} >{{$adherent->nom ?:$adherent->nom}}
                                        - {{$adherent->prenom ?:$adherent->prenom}}
                                        - {{$adherent->identifiant ?:$adherent->identifiant}}</option>
                                @endforeach
                            </select>
                        </form>
                        <form action="{{route('admin.deleteFonctionClub',[$club->id, $tab_fonctions[320]->id_utilisateur,320])}}"
                              method="POST">
                            <input type="hidden" name="_method" value="delete">
                            {{ csrf_field() }}
                            <button class="danger formBtn relative btnSmall" type="submit">supprimer</button>
                        </form>
                    </div>
                    <div class="formBlockWrapper inline">
                        @if($tab_fonctions[320]->nom)
                            <div class="formUnit mr25 ml0">
                                <label class="formLabel" for="nom"> Nom </label>
                                <div> {{$tab_fonctions[320]->nom}} </div>
                            </div>
                        @endif
                        @if($tab_fonctions[320]->prenom)
                            <div class="formUnit mr25 ml0">
                                <label class="formLabel" for="nom"> Prénom </label>
                                <div> {{$tab_fonctions[320]->prenom}} </div>
                            </div>
                        @endif
                        @if($tab_fonctions[320]->identifiant)
                            <div class="formUnit mr25 ml0">
                                <label class="formLabel" for="nom"> Identifiant </label>
                                <div> {{$tab_fonctions[320]->identifiant}} </div>
                            </div>
                        @endif
                        @if($tab_fonctions[320]->email)
                            <div class="formUnit mr25 ml0">
                                <label class="formLabel" for="nom"> Courriel </label>
                                <div> {{$tab_fonctions[320]->email}} </div>
                            </div>
                        @endif

                    </div>
                @else
                    <div class="btnWrapper d-flex relative w100">
                        <form action="{{route('admin.addFonctionClub',[$club->id, 320])}}" method="POST">
                            <input type="hidden" name="_method" value="put">
                            {{ csrf_field() }}
                            <button class="success formBtn relative mr25 btnSmall" name="showSelect">Ajouter</button>
                            <select class="formValue adherent modifying floating hidden" name="adherent_id"
                                    onchange="this.form.submit()" required>
                                <option value="">Selectionnez un adhérent</option>
                                @foreach($adherents as $adherent)
                                    <option value="{{$adherent->id}}">{{$adherent->nom ?:$adherent->nom}}
                                        - {{$adherent->prenom ?:$adherent->prenom}}
                                        - {{$adherent->identifiant ?:$adherent->identifiant}}</option>
                                @endforeach
                            </select>
                        </form>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
