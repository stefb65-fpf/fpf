@extends('layouts.email')
@section('content')
    <div class="mailContent">
        <div class="text" style="font-size: 16px;">
            Vous venez d'initier un renouvellement pour le club {{ $club->nom }} pour un montant de {{ $montant }}€.
            <br>
            <br>
            @if($creance > 0)
                Votre club dispose d'un avoir de {{ $creance }}€.
                <br>
                @if($montant_paye == 0)
                    L'avoir a été utilisé pour payer l'intégralité du renouvellement. Les adhésions sont donc d'ores et déjà effectives.
                @else
                    Pour rendre effectif les adhésions et abonnements, vous devez régler par CB ou virement instantané le montant de votre adhésion à partir de votre espace FPF.
                    <br>
                    Si toutefois vous ne pouvez pas régler par CB ou virement instantané, vous pouvez régler par chèque ou virement  en suivant les consignes indiquées dans le bordereau ci-joint.
                    <br>
                    <br>
                    Pout tout règlement, indiquez le numéro de bordereau ({{ $ref }}) ou joignez celui-ci à votre envoi.
                @endif
            @else
                Pour rendre effectif les adhésions et abonnements, vous devez régler par CB ou virement instantané le montant de votre adhésion à partir de votre espace FPF.
                <br>
                Si toutefois vous ne pouvez pas régler par CB ou virement instantané, vous pouvez régler par chèque ou virement  en suivant les consignes indiquées dans le bordereau ci-joint.
                <br>
                <br>
                Pout tout règlement, indiquez le numéro de bordereau ({{ $ref }}) ou joignez celui-ci à votre envoi.
            @endif
        </div>
    </div>
@endsection
