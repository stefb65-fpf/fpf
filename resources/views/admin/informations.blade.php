@extends('layouts.default')

@section('content')
    <div class="pageCanva">
        <h1 class="pageTitle">
            Informations générales sur les procédure base en ligne
            <a class="previousPage" title="Retour page précédente" href="{{ route('admin') }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-reply-fill" viewBox="0 0 16 16">
                    <path d="M5.921 11.9 1.353 8.62a.719.719 0 0 1 0-1.238L5.921 4.1A.716.716 0 0 1 7 4.719V6c1.5 0 6 0 7 8-2.5-4.5-7-4-7-4v1.281c0 .56-.606.898-1.079.62z"/>
                </svg>
            </a>
        </h1>

        <div class="mt25 w100">
            <h2>Traitements batch</h2>
            <div>
                Ci-après la liste des traitements se déroulant en arrière plan à intervalle régulier:
                <ul>
                    <li class="ml20"><b>toutes les 5 minutes</b>: traitement des <b>paiements effectués via Bridge</b> (virement instantanné). On interroge la plateforme et si le statut du paiement est OK, on traite l'objet du paiement</li>
                    <li class="ml20"><b>toutes les jours à 22h</b>: mise à jour sur le site fédéral des informations modifiées pour les clubs (activités, fonctions, ...)</li>
                    <li class="ml20"><b>toutes les jours à 2h</b>: mise à jour ses sessions de vote en cours si les votes sont en 3 phases</li>
                    <li class="ml20"><b>toutes les jours à 4h</b>: synchronisation des contacts Sendinblue</li>
                    <li class="ml20"><b>le 1er Septembre à 4h</b>: sauvegarde des tables clubs, personnes et utilisateurs. Glissement des paramètres de config. Mise à jour des statuts utilisateurs et clubs.</li>
                    <li class="ml20"><b>le 1er Janvier à 3h</b>: suppression sur le site fédéral des utilisateurs et clubs non renouvelés.</li>
                </ul>
            </div>
        </div>
        <div class="mt25 w100">
            <h2>Mise à jour site fédéral</h2>
            En plus des mises à jour quotidienne des informations des clubs, les informations de la base en ligne sont mises à jour sur le site fédéral de la manière suivante:
            <ul>
                <li class="ml20">lors de l'ajout d'un utilisateur (par club, adhésion individuelle), les informations sont enregistrées immédiatement sur le site fédéral</li>
                <li class="ml20">lorsque l'utilisateur modifie son mot de passe, celui-ci est mis à jour immédiatement sur le site fédéral</li>
            </ul>
        </div>
        <div class="mt25 w100">
            <h2>Workflow de paiement</h2>
            <div>
                Les paiements sur le site interviennent pour les adhésions individuelles et clubs, la souscription au Florilège. Les paiements peuvent être effectués via CB (Monext) ou via virement instantannée (Bridge).<br>
                Dans les deux cas, les boutons de paiement renvoient vers la plateforme de paiement.
                <h3>Bridge</h3>
                <ul>
                    <li class="ml20">lors d'un paiement validé, si l'utilisateur revient sur le site (comportement normal), nous ne connaissons pas encore le résultat de son virement. L'information affichée est donc que la prise en compte de son paiement va prendre quelques minutes, après lesquelles il recevra une notification par mail.</li>
                    <li class="ml20">toutes les 5 minutes, un traitement interroge la plateforme Bridge
                        <ul>
                            <li class="ml20">si le paiement est approuvé (statut COMPLETED), les informations sont traitées</li>
                            <li class="ml20">si le paiement est rejeté ou expiré, des informations enregistrées temporairement (règlement en attente) sont supprimées</li>
                            <li class="ml20">si la plateforme ne renvoie aucun de ces statuts, aucune action n'est effectuée</li>
                            <li class="ml20">le lien de validation d'un paiement expire au bout de 24h</li>
                        </ul>
                    </li>
                </ul>
                <h3>Monext</h3>
                <ul>
                    <li class="ml20">lors d'un paiement validé, si l'utilisateur revient sur le site (comportement normal), le résultat du paiement par CB est connu. Les informations liées peuvent donc être mises à jour immédiatement</li>
                    <li class="ml20">si l'utilisateur ne revient pas sur le site après un paiement validé, une notification est envoyée par Monext vers le site. Cette notification met à jour les informations liées au paiement. Elle n'est envoyée que si l'utilisateur n'est pas revenu sur le site.</li>
                    <li class="ml20">si le paiement est refusé, lors du retour de l'utilisateur sur le site, une informations mentionne le refus ou l'abandon de paiement</li>
                </ul>
            </div>
        </div>
    </div>
@endsection
