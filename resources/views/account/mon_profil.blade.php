@extends('layouts.account')
@section('contentaccount')
    <div class="accountContent">
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
                    <input class="formValue changeable"  placeholder="Hochet" disabled="true"/>
                           </div>
                <div class="formLine">
                    <div class="formLabel">Prénom</div>
                    <input class="formValue changeable" placeholder="Julie" disabled="true"/>
                </div>
                <div class="formLine">
                    <div class="formLabel">Date de naissance</div>
                    <input class="formValue changeable" placeholder="08/12/1980" disabled="true"/>
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
                    <input class="formValue changeable" placeholder="0658455425" disabled="true"/>
                </div>
            </div>
            <div class="formBtn" data-form="1" data-action="modify">Modifier</div>
        </div>
        <div class="formBlock">
            <div class="formBlockTitle">Adresses</div>
            <div class="formBlockWrapper" data-form="2">

                <div class="formLine">
                    <div class="formLabel">Adresse par défaut</div>
                    <input class="formValue changeable" placeholder="495 route de l'Eyrieux 07190 SAINT-SAUVEUR-DE-MONTAGUT" disabled="true"/>
                </div>
                <div class="formLine">
                    <div class="formLabel">Adresse de facturation</div>
                    <input class="formValue changeable" placeholder="2 rue Gambetta 07190 SAINT-SAUVEUR-DE-MONTAGUT" disabled="true"/>
                </div>
                <div class="formLine">
                    <div class="formLabel">Téléphone  fixe</div>
                    <input class="formValue changeable" placeholder="0965668745" disabled="true"/>
                </div>
            </div>
            <div class="formBtn" data-form="2"data-action="modify">Modifier</div>
        </div>

        <div class="formLine" style="display: flex; justify-content: center; align-content: center">
            <input type="checkbox" id="subscribeNews" name="subscribe" value="newsletter" style="margin-right: 10px; cursor:pointer;">
            <label for="subscribeNews">Souhaitez-vous recevoir les nouvelles de la FPF ? (Hors lettre de la fédé)</label>
        </div>
    </div>
@endsection

