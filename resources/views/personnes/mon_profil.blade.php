@extends('layouts.account')
@section('contentaccount')
    <div class="accountContent mt25">
        {{--        <div class="formBlock">--}}
        {{--            <div class="formBlockTitle">Email et mot de passe</div>--}}
        {{--            <div class="formBlockWrapper">--}}
        {{--                <div class="formLine">--}}
        {{--                    <div class="formLabel">Email</div>--}}
        {{--                    <div class="formValue">{!! $personne->email !!}</div>--}}
        {{--                    <div class="formLineModify">Changement sécurisé</div>--}}

        {{--                </div>--}}
        {{--                <div class="formLine">--}}
        {{--                    <div class="formLabel">Mot de Passe</div>--}}
        {{--                    <div class="formValue">********</div>--}}
        {{--                    <div class="formLineModify">Modifier</div>--}}
        {{--                </div>--}}
        {{--            </div>--}}
        {{--        </div>--}}
        <form class="formBlock" action="{{ route('updateCivilite', $personne) }}" method="POST">
            <input type="hidden" name="_method" value="put">
            {{ csrf_field() }}
            <div class="formBlockTitle">Civilité</div>
            <div class="formBlockWrapper" data-form="1">
                <div class="formLine">
                    <div class="formLabel">Nom</div>
                    <input class="formValue capitalize" value="{{$personne->nom}}" disabled="true" name="nom"/>
                </div>
                <div class="formLine">
                    <div class="formLabel">Prénom</div>
                    <input class="formValue capitalize" value="{{$personne->prenom}}" disabled="true" name="prenom"/>
                </div>
                <div class="formLine">
                    <div class="formLabel">Date de naissance</div>
                    <input class="formValue" type="date" value="{{$personne->datenaissance?:""}}"
                           disabled="true" name="datenaissance"/>
                </div>
                {{--                <div class="formLine">--}}
                {{--                    <div class="formLabel">Type</div>--}}
                {{--                    <div class="formValue">Organisation</div>--}}
                {{--                </div>--}}
                {{--                <div class="formLine">--}}
                {{--                    <div class="formLabel">Nom d'organisation</div>--}}
                {{--                    <div class="formValue">Objectif Photo</div>--}}
                {{--                </div>--}}
                {{--                <div class="formLine">--}}
                {{--                    <div class="formLabel">SIRET</div>--}}
                {{--                    <div class="formValue">40388188100029</div>--}}
                {{--                </div>--}}
                <div class="formLine">
                    <div class="formLabel">Téléphone mobile</div>
                    <input class="formValue" value="{{$personne->phone_mobile?:""}}"
                           disabled="true" name="phone_mobile"/>
                </div>
            </div>
            <div>
                <button type="submit" class="formBtn d-none" name="enableBtn">Valider</button>
                <button class="formBtn" name="updateForm">Modifier</button>
            </div>

        </form>

        <div class="formBlock">
            <div class="formBlockTitle">Adresses</div>
            <form  action="{{ route('updateAdresse', [$personne,1]) }}" method="POST">
                <input type="hidden" name="_method" value="PUT">
                {{ csrf_field() }}
                <div class="formBlockWrapper" data-form="2">
                    @if(!$nbadresses)
                        <div class="addAddress">Vous voulez rajouter une adresse?</div>
                    @endif

                    <div class="formValueGroup {{ !$nbadresses ?" hideForm":""}}">
                        @if($nbadresses==2)
                            <div class="formTitle">Adresse de Facturation</div>
                        @else($nbadresses ==1)
                            <div class="formTitle">Adresse</div>
                        @endif
                        <div class="formLine">
                            <div class="formLabel">Libellé</div>
                            <input name="libelle2" class="formValue "
                                   type="text" value="{{$personne->adresses[0]->libelle2}}"
                                   disabled="true"/>
                        </div>
                        <div class="formLine">
                            <div class="formLabel">Rue</div>
                            <input name="libelle1" type="text" class="formValue "
                                   value="{{$personne->adresses[0]->libelle1}}"
                                   disabled="true"/>
                        </div>
                        <div class="formLine">
                            <div class="formLabel">Code Postal</div>
                            <input name="codepostal" type="text" class="formValue"
                                   value="{{$personne->adresses[0]->codepostal}}"
                                   disabled="true"/>
                        </div>
                        <div class="formLine">
                            <div class="formLabel">Ville</div>
                            <input name="ville" type="text" class="formValue"
                                   value="{{$personne->adresses[0]->ville}}"
                                   disabled="true"/>
                        </div>
                        <div class="formLine">
                            <div class="formLabel">Pays</div>
                            <input name="pays" type="text" class="formValue"
                                   value="{{$personne->adresses[0]->pays}}"
                                   disabled="true"/>
                        </div>
                        <div class="formLine">
                            <div class="formLabel">Téléphone fixe</div>
                            <input class="formValue" type="text"
                                   value="{{$personne->adresses[0]->telephonedomicile?:""}}"
                                   disabled="true" name="telephonedomicile"/>
                        </div>
                        <div>
                            <button type="submit" class="formBtn d-none" name="enableBtn">Valider</button>
                            <button class="formBtn" name="updateForm">Modifier</button>
                        </div>
                    </div> {{-- end formvaluegroup--}}

                </div>{{-- end formBlockWrapper--}}
            </form>
            @if($nbadresses)
                <form action="{{ route('updateAdresse', [$personne,2]) }}" method="POST">
                    <input type="hidden" name="_method" value="PUT">
                    {{ csrf_field() }}
                    {{$personne->mail}}
                    <div class="formBlockWrapper" data-form="3">
                        @if(!($nbadresses == 2))
                            <div class="addAddress" name="addAddress">Vous voulez rajouter une adresse de livraison?</div>
                        @endif
                        <div class="formValueGroup {{ $nbadresses < 2 ?" hideForm":""}}">
                            <div class="formTitle">Adresse de Livraison</div>
                            <div class="formLine">
                                <div class="formLabel">Libellé</div>
                                <input name="libelle2" type="text" class="formValue"
                                       value="{{$personne->adresses[1]?$personne->adresses[1]->libelle2:""}}"
                                       disabled="true"/>
                            </div>
                            <div class="formLine">
                                <div class="formLabel">Rue</div>
                                <input name="libelle1" type="text" class="formValue"
                                       value="{{$personne->adresses[1]?$personne->adresses[1]->libelle1:""}}"
                                       disabled="true"/>
                            </div>
                            <div class="formLine">
                                <div class="formLabel">Code Postal</div>
                                <input name="codepostal" type="text" class="formValue"
                                       value="{{$personne->adresses[1]?$personne->adresses[1]->codepostal:""}}"
                                       disabled="true"/>
                            </div>
                            <div class="formLine">
                                <div class="formLabel">Ville</div>
                                <input name="ville" type="text" class="formValue"
                                       value="{{$personne->adresses[1]?$personne->adresses[1]->ville:""}}"
                                       disabled="true"/>
                            </div>
                            <div class="formLine">
                                <div class="formLabel">Pays</div>
                                <input name="pays" type="text" class="formValue"
                                       value="{{$personne->adresses[1]?$personne->adresses[1]->pays:""}}"
                                       disabled="true"/>
                            </div>
                            <div class="formLine">
                                <div class="formLabel">Téléphone fixe</div>
                                <input class="formValue"
                                       value="{{$personne->adresses[1]?$personne->adresses[1]->telephonedomicile:""}}"
                                       disabled="true" name="telephonedomicile"/>
                            </div>
                            <div>
                                <button type="submit" class="formBtn d-none" name="enableBtn">Valider</button>
                                <button class="formBtn" name="updateForm">Modifier</button>
                            </div>
                        </div> {{-- end formvaluegroup--}}

                    </div>{{-- end formBlockWrapper--}}
                </form>
            @endif
        </div>


            </form>
            <div class="formLine newsletter" style="display: flex; justify-content: center; align-content: center">
                <div class="switch">
                    {{--                <input type="checkbox {{$personne->news?" active":""}}">--}}
                    <input type="checkbox" {{$personne->news?'checked=true':'checked=false'}}>
                    <span class="slider"></span>
                </div>

                <label for="subscribeNews"> Souhaitez-vous <span>recevoir les nouvelles</span> de la FPF ?<br> (Hors
                    lettre
                    de la fédé)</label>
            </div>

    </div>
@endsection

