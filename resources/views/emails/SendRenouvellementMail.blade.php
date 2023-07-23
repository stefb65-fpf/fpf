@extends('layouts.email')
@section('content')
    <div class="mailContent">
        <div class="text">
            Vous venez d'initier un renouvellement pour le club {{ $club->nom }} pour un montant de {{ $montant }}€.
            <br>
            <br>
            Pour rendre effectif les adhésions et abonnements, vous devez régler par chèque ou virement le montant de votre adhésion en suivant les consignes indiquées dans le bordereau ci-joint.
            <br>
            <br>
            Pout tout règlement, indiquez le numéro de bordereau ({{ $ref }}) ou joignez celui-ci à votre envoi.
        </div>
    </div>
@endsection
