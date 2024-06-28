@extends('layouts.pdf-fpf')

@section('content')
    <div style="font-size: 13px;">
        <div style="text-align: center; font-size: 20px;">
            Référence du règlement : {{ $ref }}
        </div>
        <div style="margin-top: 20px;">
            Relevé du club {{ str_pad($club->urs_id, 2, '0', STR_PAD_LEFT).'-'.str_pad($club->numero, 4, '0', STR_PAD_LEFT) }} : {{ $club->nom }}
        </div>
        <div style="margin-top: 10px;">
            Veuillez effectuer un virement d’un montant de {{ number_format($total_montant, 2, ',', '') }} € en indiquant en référence le numéro de bordereau {{ $ref }} à :<br>
            Fédération photographique de France<br>
            IBAN : FR76 1751 5900 0008 2229 9272 052<br>
            BIC : CEPAFRPP751
        </div>
        <div style="margin-top: 20px">
            @if($total_club > 0)
                <table style="width: 100%; border-spacing: 0px;">
                    <thead style="background-color: #cccccc">
                    <tr>
                        <th style="text-align: center">Adhésion club</th>
                        <th style="text-align: center">Adhésion UR</th>
                        <th style="text-align: center">Abonnement club</th>
                        <th style="text-align: center">Florilège</th>
                        <th style="text-align: center">Total club</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td style="text-align: center">{{ $montant_adhesion_club == 0 ? '' : number_format($montant_adhesion_club, 2, ',', '').'€' }}</td>
                        <td style="text-align: center">{{ $montant_adhesion_club_ur == 0 ? '' : number_format($montant_adhesion_club_ur, 2, ',', '').'€' }}</td>
                        <td style="text-align: center">{{ $montant_abonnement_club == 0 ? '' : number_format($montant_abonnement_club, 2, ',', '').'€' }}</td>
                        <td style="text-align: center">{{ $montant_florilege_club == 0 ? '' : number_format($montant_florilege_club, 2, ',', '').'€' }}</td>
                        <td style="text-align: center">{{ number_format($total_club, 2, ',', '').'€' }}</td>
                    </tr>
                    </tbody>
                </table>
            @endif
        </div>

        <div style="margin-top: 20px">
            <table style="width: 100%; border-spacing: 0px;">
                <thead style="background-color: #cccccc">
                <tr>
                    <th style="text-align: left">Adhérent</th>
                    <th style="text-align: left">Type carte</th>
                    <th style="text-align: right">Adhésion</th>
                    <th style="text-align: right">Abonnement</th>
                    <th style="text-align: right">Florilège</th>
                    <th style="text-align: right">Total</th>
                </tr>
                </thead>
                <tbody>
                @foreach($tab_adherents as $adherent)
                    <tr style="line-height: 20px;">
                        <td>
                            {{ $adherent['adherent']['identifiant'].' - '.$adherent['adherent']['nom'].' '.$adherent['adherent']['prenom'] }}
                        </td>
                        <td>
                            {{ $adherent['adherent']['ct'] ?? '' }}
                        </td>
                        <td style="text-align: right">
                            {{ isset($adherent['adhesion']) ? number_format($adherent['adhesion'], 2, ',', '').'€' : '' }}
                        </td>
                        <td style="text-align: right">
                            {{ isset($adherent['abonnement']) ? number_format($adherent['abonnement'], 2, ',', '').'€' : '' }}
                        </td>
                        <td style="text-align: right">
                            {{ isset($adherent['florilege']) ? number_format($adherent['florilege'], 2, ',', '').'€' : '' }}
                        </td>
                        <td style="text-align: right">
                            {{ number_format($adherent['total'], 2, ',', '').'€' }}
                        </td>
                    </tr>
                @endforeach
                </tbody>
                <tfoot style="background-color: #cccccc">
                <tr style="line-height: 30px;">
                    <td colspan="2" style="text-align: right; font-weight: bolder;">Pour l'ensemble des adhérents</td>
                    <td style="text-align: right; font-weight: bolder;">
                        {{ $total_adhesion > 0 ? number_format($total_adhesion, 2, ',', '').'€' : '' }}
                    </td>
                    <td style="text-align: right; font-weight: bolder;">
                        {{ $total_abonnement > 0 ? number_format($total_abonnement, 2, ',', '').'€' : '' }}
                    </td>
                    <td style="text-align: right; font-weight: bolder;">
                        {{ $total_florilege > 0 ? number_format($total_florilege, 2, ',', '').'€' : '' }}
                    </td>
                    <td style="text-align: right; font-weight: bolder;">
                        {{ number_format($total_adherents, 2, ',', '').'€' }}
                    </td>
                </tr>
                </tfoot>
            </table>
        </div>

        <div style="margin-top: 50px; text-align: center; font-weight: bolder; font-size: 20px;">
            Total à régler: {{ number_format($total_montant, 2, ',', '') }} €
        </div>
    </div>
@endsection
