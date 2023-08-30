@extends('layouts.supportDefault')
@section('content')
        <form action="{{ route('support.submit') }}" method="POST" class="authForm align-center">
            {{ csrf_field() }}
            <input type="hidden" name="_method" value="PUT">
            <div class="customField maxW100 w540">
                <label>E-mail <span class="bold">(obligatoire)</span></label>
                <input type="email" name="email" maxlength="100" required value="{{$user? $user->email:""}}"/>
                <div class="error">message erreur</div>
            </div>
            <div class="customField maxW100 w540 ">
                <label>Numéro de carte <span class="italic">(facultatif)</span></label>
                <input type="text" name="identifiant" maxlength="50" value="{{$user? $user->identifiant:""}}"/>
                <div class="error">message erreur</div>
            </div>
            <div class="customField maxW100 w540 ">
                <label>Objet <span class="bold">(obligatoire)</span></label>
                <select class="formValue" name="objet" data-ref="objet" required>
                    <option value="Problème de Connexion">Problème de connexion</option>
                    <option value="Problème de paiement">Problème de paiement</option>
                    <option value="Enregistrement adhésion ou abonnement">Enregistrement adhésion ou abonnement</option>
                    <option value="Anonymisation des données">Anonymisation des données</option>
                    <option value="Autre">Autre</option>
                </select>
                <div name="error" class="error"></div>
            </div>
            <div class="customField maxW100 w540 pt10">
                <label>Votre demande <span class="bold">(obligatoire)</span></label>
                <textarea class="editor w100" name="contenu" rows="10" required></textarea>
{{--                <textarea class="editor w75" name="contenu" rows="10">{!! old('contenu', $election->contenu) !!}</textarea>--}}
                <div class="error">message erreur</div>
            </div>
            <button type="submit" class="button customBtn">Envoyez votre demande</button>
        </form>
{{--    </div>--}}
@endsection
