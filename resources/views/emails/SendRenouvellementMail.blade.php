@extends('layouts.email')
@section('content')
    <div class="mailContent">
        <div class="text">
            Vous venez d'initier un renouvellement pour le club {{ $club->nom }} pour un montant de {{ $montant }}€.
            <br>
            <br>
            Pour rendre effectif les adhésions et abonnements, vous devez régler par CB ou virement instantané le montant de votre adhésion à partir de votre espace FPF.
            <br>
            Si toutefois vous ne pouvez pas régler par CB ou virement instantané, vous pouvez régler par chèque ou virement  en suivant les consignes indiquées dans le bordereau ci-joint.
            <br>
            <br>
            Pout tout règlement, indiquez le numéro de bordereau ({{ $ref }}) ou joignez celui-ci à votre envoi.
        </div>
    </div>
@endsection
