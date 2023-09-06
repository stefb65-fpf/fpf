@extends('layouts.supportDefault')
@section('content')
    <div class="alertInfo mb25 w80">
        <div>
            <span class="bold">Problème de connexion ?</span>
            Vous n'arrivez pas à vous connecter avec vos indentifiants FPF ?
            C'est normal, nous avons changé de système d'authentification. Désormais vous allez devoir vous connecter avec votre adresse e-mail et votre mot de passe.
            A votre première connexion, vous devrez initialiser votre mot de passe en cliquant sur le lien "Vous avez un compte FPF mais c'est votre première connexion ?"
            sur la <a href="{{ env('APP_URL') }}login" style="text-decoration: underline; font-weight: bolder;">page d'authentification</a>.
        </div>
        <div style="margin-top: 15px;">
            <span class="bold">Avant de solliciter le suport !</span>
            Il est possible que la réponse à votre problème soit contenue dans le <a href="{{ env('APP_URL') }}storage/app/public/guide-adherents-2023.pdf" style="text-decoration: underline; font-weight: bolder;">guide d'utilisation des adhérents</a>.
            Si vous êtes responsable de club, vous pouvez également consulter le <a href="{{ env('APP_URL') }}storage/app/public/guide-clubs-2023.pdf" style="text-decoration: underline; font-weight: bolder;">guide d'utilisation des clubs</a>.
            Si vous ne trouvez pas la réponse à votre question, vous pouvez alors contacter le support.
        </div>
    </div>
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
