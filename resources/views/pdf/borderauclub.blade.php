@extends('layouts.pdf-facture')

@section('content')
    <div style="font-size: 11px;">
        <div class="blue-font" style="text-align: left; font-size: 16px; text-transform: uppercase; font-weight: bold;">
            facture proforma n° {{ $ref }}
        </div>
        <div class="blue-font" style="font-weight: lighter; text-transform: uppercase;">
            (référence à rappeler lors de votre règlement)
        </div>
        <div style="width: 100%; position: relative; margin-top: 25px;">
            <div style="position: absolute; right: 0;">
                <div class="blue-font" style="text-transform: uppercase; font-weight: bolder;">{{ $club->nom }}</div>
                @if($club->adresse->libelle1)
                    <div class="blue-font">{{ $club->adresse->libelle1 }}</div>
                @endif
                <div class="blue-font">{{ str_pad($club->adresse->codepostal, '0', 5, STR_PAD_LEFT).' '.$club->adresse->ville }}</div>
            </div>
        </div>
{{--        <div style="margin-top: 20px;">--}}
{{--            Relevé du club {{ str_pad($club->urs_id, 2, '0', STR_PAD_LEFT).'-'.str_pad($club->numero, 4, '0', STR_PAD_LEFT) }} : {{ $club->nom }}--}}
{{--        </div>--}}

        <div style="margin-top: 80px">
            @if($total_club > 0)
                <table class="facture-table" style="width: 100%; border-spacing: 0px;">
                    <thead class="bg-blue">
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
            <table class="facture-table" style="width: 100%; border-spacing: 0px;">
                <thead class="bg-blue">
                <tr>
                    <th style="text-align: center">Adhérent</th>
                    <th style="text-align: center">Type carte</th>
                    <th style="text-align: center">Adhésion</th>
                    <th style="text-align: center">Abonnement</th>
                    <th style="text-align: center">Florilège</th>
                    <th style="text-align: center">Total</th>
                </tr>
                </thead>
                <tbody>
                @foreach($tab_adherents as $adherent)
                    <tr>
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
                <tfoot class="bg-blue" style="color: white">
                <tr>
                    <td colspan="2" style="font-weight: bolder;">Total</td>
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

        <div class="blue-font" style="margin-top: 50px; text-transform: uppercase; font-weight: bolder; font-size: 16px;">
            Montant global à régler: {{ number_format($total_montant, 2, ',', '') }} €
        </div>

        <div style="margin-top: 10px;">
            Veuillez effectuer un virement d'un montant de {{ number_format($total_montant, 2, ',', '') }} € en indiquant en référence le numéro de facture proforma à :
            <div style="margin-top: 20px; margin-left: 10px;">
                Fédération photographique de France<br>
                IBAN : FR76 1751 5900 0008 2229 9272 052<br>
                BIC : CEPAFRPP751
            </div>
        </div>
    </div>
@endsection
