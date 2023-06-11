@extends('layouts.account')
@section('contentaccount')
    <div class="accountContent mt25">
      <div class="formBlock">
          <div class="formBlockTitle">Email et mot de passe</div>
          <div class="formBlockWrapper">
              <div class="formLine">
                  <div class="formLabel">Email</div>
                  <div class="formValue">juliehochet@gmail.com</div>
                  <div class="formLineModify">Changement sécurisé</div>

              </div>
              <div class="formLine">
                  <div class="formLabel">Mot de Passe</div>
                  <div class="formValue">********</div>
                  <div class="formLineModify">Modifier</div>
              </div>
          </div>
      </div>
        <div class="formBlock" >
            <div class="formBlockTitle">Civilité</div>
            <div class="formBlockWrapper" data-form="1">
                <div class="formLine">
                    <div class="formLabel">Nom</div>
                    <input class="formValue changeable"  value="Hochet" disabled="true"/>
                           </div>
                <div class="formLine">
                    <div class="formLabel">Prénom</div>
                    <input class="formValue changeable" value="Julie" disabled="true"/>
                </div>
                <div class="formLine">
                    <div class="formLabel">Date de naissance</div>
                    <input class="formValue changeable" value="08/12/1980" disabled="true"/>
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
                    <input class="formValue changeable" value="0658455425" disabled="true"/>
                </div>
            </div>
            <div class="formBtn" data-form="1" data-action="modify">Modifier</div>
        </div>
        <div class="formBlock">
            <div class="formBlockTitle">Adresses</div>
            <div class="formBlockWrapper" data-form="2">

                <div class="formLine">
                    <div class="formLabel">Adresse par défaut</div>
                    <div class="formValueGroup">
                        <input class="formValue changeable" value="495 route de l'Eyrieux" disabled="true"/>
                        <input class="formValue changeable" value="07 190" disabled="true"/>
                        <input class="formValue changeable" value="SAINT-SAUVEUR-DE-MONTAGUT" disabled="true"/>
                    </div>

                </div>
                <div class="formLine">
                    <div class="formLabel">Adresse de facturation</div>
                    <div class="formValueGroup">
                        <input class="formValue changeable" value="2 rue Gambetta" disabled="true"/>
                        <input class="formValue changeable" value="07 190" disabled="true"/>
                        <input class="formValue changeable" value="SAINT-SAUVEUR-DE-MONTAGUT" disabled="true"/>
                    </div>
                </div>
                <div class="formLine">
                    <div class="formLabel">Téléphone  fixe</div>
                    <input class="formValue changeable" value="0965668745" disabled="true"/>
                </div>
            </div>
            <div class="formBtn" data-form="2"data-action="modify">Modifier</div>
        </div>

        <div class="formLine newsletter" style="display: flex; justify-content: center; align-content: center">
            <div class="switch">
                <input type="checkbox">
                <span class="slider"></span>
            </div>

            <label for="subscribeNews">Souhaitez-vous <span>recevoir les nouvelles</span> de la FPF ?<br> (Hors lettre de la fédé)</label>
        </div>
    </div>
@endsection

