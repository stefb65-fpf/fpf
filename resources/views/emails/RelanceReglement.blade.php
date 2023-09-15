@extends('layouts.email')
@section('content')
    <div class="mailContent">
        <div class="text" style="font-size: 16px;">
            Il y a quelques jours, vous avez initié un renouvellement pour le club {{ $club->nom }} pour un montant de {{ $montant }}€. Le règlement n'a pas encore été effectué.
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
