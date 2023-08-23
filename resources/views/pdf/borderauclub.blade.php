@extends('layouts.pdf-fpf')

@section('content')
    <div class="fs13px">
        <div class="text-center fs20px">
            Référence du règlement : {{ $ref }}
        </div>
        <div class="mt20">
            Relevé du club {{ str_pad($club->urs_id, 2, '0', STR_PAD_LEFT).'-'.str_pad($club->numero, 4, '0', STR_PAD_LEFT) }} : {{ $club->nom }}
        </div>
        <div class="mt10">
            Veuillez envoyer un chèque d'un montant de {{ number_format($total_montant, 2, ',', '') }} € pour le renouvellement ou effectuer un virement de ce montant à :<br>
            Fédération photographique de France<br>
            IBAN : FR76 1751 5900 0008 2229 9272 052<br>
            BIC : CEPAFRPP751
        </div>
        <div class="mt20">
            @if($total_club > 0)
            <table class="w100 border-spacing-0">
                <thead class="bgGrey">
                <tr>
                    <th class="text-center">Adhésion UR</th>
                    <th class="text-center">Adhésion club</th>
                    <th class="text-center">Abonnement club</th>
                    <th class="text-center">Total club</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td class="text-center">{{ $montant_adhesion_club == 0 ? '' : number_format($montant_adhesion_club, 2, ',', '').'€' }}</td>
                    <td class="text-center">{{ $montant_adhesion_club_ur == 0 ? '' : number_format($montant_adhesion_club_ur, 2, ',', '').'€' }}</td>
                    <td class="text-center">{{ $montant_abonnement_club == 0 ? '' : number_format($montant_abonnement_club, 2, ',', '').'€' }}</td>
                    <td class="text-center">{{ number_format($total_club, 2, ',', '').'€' }}</td>
                </tr>
                </tbody>
            </table>
            @endif
        </div>

        <div class="mt20">
            <table class="w100 border-spacing-0">
                <thead class="bgGrey">
                <tr>
                    <th class="text-left">Adhérent</th>
                    <th class="text-left">Type carte</th>
                    <th class="text-right">Adhésion</th>
                    <th class="text-right">Abonnement</th>
                    <th class="text-right">Total</th>
                </tr>
                </thead>
                <tbody>
                @foreach($tab_adherents as $adherent)
                    <tr class="lineH30">
                        <td>
                            {{ $adherent['adherent']['identifiant'].' - '.$adherent['adherent']['nom'].' '.$adherent['adherent']['prenom'] }}
                        </td>
                        <td>
                            {{ $adherent['adherent']['ct'] ?? '' }}
                        </td>
                        <td class="text-right">
                            {{ isset($adherent['adhesion']) ? number_format($adherent['adhesion'], 2, ',', '').'€' : '' }}
                        </td>
                        <td class="text-right">
                            {{ isset($adherent['abonnement']) ? number_format($adherent['abonnement'], 2, ',', '').'€' : '' }}
                        </td>
                        <td class="text-right">
                            {{ number_format($adherent['total'], 2, ',', '').'€' }}
                        </td>
                    </tr>
                @endforeach
                </tbody>
                <tfoot class="bgGrey">
                <tr class="lineH30">
                    <td colspan="2" class="text-right bold">Pour l'ensemble des adhérents</td>
                    <td class="text-right bold">
                        {{ $total_adhesion > 0 ? number_format($total_adhesion, 2, ',', '').'€' : '' }}
                    </td>
                    <td class="text-right bold">
                        {{ $total_abonnement > 0 ? number_format($total_abonnement, 2, ',', '').'€' : '' }}
                    </td>
                    <td class="text-right bold">
                        {{ number_format($total_adherents, 2, ',', '').'€' }}
                    </td>
                </tr>
                </tfoot>
            </table>
        </div>

    </div>

@endsection
