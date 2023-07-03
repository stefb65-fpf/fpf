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
                <button type="submit" class="formBtn d-none success" name="enableBtn">Valider</button>
                <button class="formBtn primary" name="updateForm">Modifier</button>
            </div>
        </form>
        <div class="formBlock">
            <div class="formBlockTitle">Adresses</div>
            <form action="{{ route('updateAdresse', [$personne,1]) }}" method="POST">
                <input type="hidden" name="_method" value="PUT">
                {{ csrf_field() }}
                <div class="formBlockWrapper" data-form="2">
                    @if(!$nbadresses)
                        <div class="addAddress" name="addAddress">Vous voulez rajouter une adresse ?</div>
                    @endif
                    <div class="formValueGroup {{ !$nbadresses ?" hideForm":""}}">
                        @if($nbadresses==2)
                            <div class="formTitle">Adresse de Facturation</div>
                        @else($nbadresses ==1)
                            <div class="formTitle">Adresse</div>
                        @endif
                        <div class="formLine">
                            <div class="formLabel">Rue</div>
                            <input name="libelle1" type="text" class="formValue "
                                   value="{{$personne->adresses[0]?$personne->adresses[0]->libelle1:""}}"
                                   disabled="true" maxlength="120"/>
                        </div>
                        <div class="formLine">
                            <div class="formLabel"></div>
                            <input name="libelle2" class="formValue "
                                   type="text" value="{{$personne->adresses[0]?$personne->adresses[0]->libelle2:""}}"
                                   disabled="true" maxlength="120"/>
                        </div>
                        <div class="formLine">
                            <div class="formLabel">Code Postal</div>
                            <div class="suggestionWrapper">
                                <input name="codepostal" type="text" class="formValue"
                                       value="{{$personne->adresses[0]?$personne->adresses[0]->codepostal:""}}"
                                       disabled="true" maxlength="10" required/>
                                <div class="suggestion"></div>
                            </div>

                        </div>
                        <div class="formLine">
                            <div class="formLabel">Ville</div>
                            <div class="suggestionWrapper">
                            <input name="ville" type="text" class="formValue"
                                   value="{{$personne->adresses[0]?$personne->adresses[0]->ville:""}}"
                                   disabled="true" maxlength="50" required/>
                                <div class="suggestion"></div>
                            </div>
                        </div>
                        <div class="formLine">
                            <div class="formLabel">Pays</div>
                            <select  class="formValue pays" name="pays"  disabled="true" required>
                                <option value="">Selectionnez un pays</option>
                            @foreach($countries as $country)
                                @if($personne->adresses[0])
                                        <option value="{{$country->id}}" {{strtolower($country->nom) == strtolower($personne->adresses[0]->pays)? "selected":""}} data-indicator="{{$country->indicatif}}">{{$country->nom}}</option>
                                    @else
                                        <option value="{{$country->id}}" >{{$country->nom}}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="formLine">
                            <div class="formLabel">Téléphone fixe</div>
                            <div class="group">
                                <div class="indicator {{$personne->adresses[0] && $personne->adresses[0]->indicatif!==""?"":"d-none"}}">+{{$personne->adresses[0]?$personne->adresses[0]->indicatif:""}}</div>
                                <input class="formValue phoneInput" type="text" value="{{$personne->adresses[0]?$personne->adresses[0]->telephonedomicile:""}}"
                                       disabled="true" name="telephonedomicile"/>
                            </div>
                        </div>
                        <div>
                            <button type="submit" class="formBtn success d-none" name="enableBtn">Valider</button>
                            <button class="formBtn primary" name="updateForm">Modifier</button>
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
                            <div class="addAddress" name="addAddress">Vous voulez rajouter une adresse de livraison ?
                            </div>
                        @endif
                        <div class="formValueGroup {{ $nbadresses < 2 ?" hideForm":""}}">
                            <div class="formTitle">Adresse de Livraison</div>
                            <div class="formLine">
                                <div class="formLabel">Rue</div>
                                <input name="libelle1" type="text" class="formValue"
                                       value="{{$personne->adresses[1]?$personne->adresses[1]->libelle1:""}}"
                                       disabled="true"  maxlength="120"/>
                            </div>
                            <div class="formLine">
                                <div class="formLabel"></div>
                                <input name="libelle2" type="text" class="formValue"
                                       value="{{$personne->adresses[1]?$personne->adresses[1]->libelle2:""}}"
                                       disabled="true"  maxlength="120"/>
                            </div>
                            <div class="formLine">
                                <div class="formLabel">Code Postal</div>
                                <div class="suggestionWrapper">
                                    <input name="codepostal" type="text" class="formValue"
                                           value="{{$personne->adresses[1]?$personne->adresses[1]->codepostal:""}}"
                                           disabled="true" maxlength="10" required/>
                                    <div class="suggestion"></div>
                                </div>

                            </div>
                            <div class="formLine">
                                <div class="formLabel">Ville</div>
                                <div class="suggestionWrapper">
                                    <input name="ville" type="text" class="formValue"
                                           value="{{$personne->adresses[1]?$personne->adresses[1]->ville:""}}"
                                           disabled="true" maxlength="50" required/>
                                    <div class="suggestion"></div>
                                </div>
                            </div>
                            <div class="formLine">
                                <div class="formLabel">Pays</div>
                                <select  class="formValue pays" name="pays"  disabled="true" required>
                                    <option value="">Selectionnez un pays</option>
                                    @foreach($countries as $country)
                                        @if($personne->adresses[1])
                                            <option value="{{$country->id}}" {{strtolower($country->nom) == strtolower($personne->adresses[1]->pays)? "selected":""}} data-indicator="{{$country->indicatif}}">{{$country->nom}}</option>
                                        @else
                                            <option value="{{$country->id}}" >{{$country->nom}}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                            <div class="formLine">
                                <div class="formLabel">Téléphone fixe</div>
                                <div class="group">
                                    <div class="indicator {{$personne->adresses[1] && $personne->adresses[1]->indicatif!==""?"":"d-none"}}">+{{$personne->adresses[1]?$personne->adresses[1]->indicatif:""}}</div>
                                    <input class="formValue phoneInput" type="text" value="{{$personne->adresses[1]?$personne->adresses[1]->telephonedomicile:""}}"
                                           disabled="true" name="telephonedomicile"/>
                                </div>
                            </div>
                            <div>
                                <button type="submit" class="formBtn success d-none" name="enableBtn">Valider</button>
                                <button class="formBtn primary" name="updateForm">Modifier</button>
                            </div>
                        </div> {{-- end formvaluegroup--}}

                    </div>{{-- end formBlockWrapper--}}
                </form>
            @endif
        </div>


        </form>
        <div class="formLine newsletter" style="display: flex; justify-content: center; align-content: center">
            <div class="switch">

                <input type="checkbox" {{$personne->news?'checked=true':'checked=false'}} value={{$personne->news?1:0}}>
                <span class="slider"></span>
            </div>

            <label class="notSubscribing {{$personne->news?'d-none':''}}" for="subscribeNews">
                <div>Souhaitez-vous <span>recevoir les nouvelles</span> de la FPF ?<br> (Hors
                    lettre
                    de la fédé)</div>
                @if($personne->blacklist_date)
                    <div class="blacklist {{$personne->news?'d-none':''}}">Vous avez mis les nouvelles de la FPF en liste noir depuis le {{($personne->blacklist_date)}} </div>
                @endif</label>

            <label class="subscribing {{$personne->news?'':'d-none'}}" for="subscribeNews"> Vous <span>recevez actuellement les nouvelles</span> de la FPF.<br> (Hors
                lettre
                de la fédé)</label>

        </div>

    </div>
@endsection
@section('js')
    <script src="{{ asset('js/autocompleteCommune.js') }}?t=<?= time() ?>"></script>
@endsection
