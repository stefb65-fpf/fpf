@extends('layouts.pdf-facture')

@section('content')
    <div style="font-size: 11px; margin-top: 50px;">
        <div class="blue-font" style="text-align: left; font-size: 16px; text-transform: uppercase; font-weight: bold;">
            Facture n° {{ $invoice->numero }}
        </div>
        <div class="blue-font" style="text-align: left; font-size: 12px; text-transform: uppercase; font-weight: bold;">
            PROFORMA n° {{ $invoice->reference }}
        </div>
        <div style="width: 100%; position: relative; margin-top: 25px;">
            <div style="position: absolute; right: 0;">
                @if($personne)
                    <div class="blue-font" style="text-transform: uppercase; font-weight: bolder;">{{ $personne->nom.' '.$personne->prenom }}</div>
                @endif
                @if($club)
                    <div class="blue-font" style="text-transform: uppercase; font-weight: bolder;">{{ $club->nom }}</div>
                @endif
                @if($ur)
                    <div class="blue-font" style="text-transform: uppercase; font-weight: bolder;">{{ $ur->nom }}</div>
                @endif
                <div class="blue-font">{{ $adresse->libelle1 }}</div>
                <div class="blue-font">{{ $adresse->libelle2 }}</div>
                <div class="blue-font">{{ $adresse->codepostal.' '.$adresse->ville }}</div>
            </div>
        </div>
    </div>

    <div style="font-size: 12px; margin-top: 100px;">
        @if($renew_club == 0)
        <table class="facture-table" style="width: 100%">
            <thead class="bg-blue">
                <tr>
                    <th>Description</th>
                    <th>Montant</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="width: 80%">
                        <div style="padding-right:20px; ">
                            {{ $invoice->description }}
                        </div>
                    </td>
                    <td style="width: 20%; text-align: right;">
                        {{ number_format($invoice->montant, 2, ',', ' ') }} €
                    </td>
                </tr>
            </tbody>
        </table>
        @else
            @if($tab_reglements['total_club'] > 0)
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
                        <td style="text-align: center">{{ $tab_reglements['montant_adhesion_club'] == 0 ? '' : number_format($tab_reglements['montant_adhesion_club'], 2, ',', '').'€' }}</td>
                        <td style="text-align: center">{{ $tab_reglements['montant_adhesion_club_ur'] == 0 ? '' : number_format($tab_reglements['montant_adhesion_club_ur'], 2, ',', '').'€' }}</td>
                        <td style="text-align: center">{{ $tab_reglements['montant_abonnement_club'] == 0 ? '' : number_format($tab_reglements['montant_abonnement_club'], 2, ',', '').'€' }}</td>
                        <td style="text-align: center">{{ $tab_reglements['montant_florilege_club'] == 0 ? '' : number_format($tab_reglements['montant_florilege_club'], 2, ',', '').'€' }}</td>
                        <td style="text-align: center">{{ number_format($tab_reglements['total_club'], 2, ',', '').'€' }}</td>
                    </tr>
                    </tbody>
                </table>
            @endif
            <div style="margin-top: 20px;">
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
                                {{ $adherent['identifiant'].' - '.$adherent['nom'].' '.$adherent['prenom'] }}
                            </td>
                            <td>
                                {{ $adherent['ct'] ?? '' }}
                            </td>
                            <td style="text-align: right">
                                {{ $adherent['adhesion'] > 0 ? number_format($adherent['adhesion'], 2, ',', '').'€' : '' }}
                            </td>
                            <td style="text-align: right">
                                {{ $adherent['abonnement'] > 0 ? number_format($adherent['abonnement'], 2, ',', '').'€' : '' }}
                            </td>
                            <td style="text-align: right">
                                {{ $adherent['florilege'] > 0 ? number_format($adherent['florilege'], 2, ',', '').'€' : '' }}
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
                            {{ $tab_reglements['montant_adhesion_adherents'] > 0 ? number_format($tab_reglements['montant_adhesion_adherents'], 2, ',', '').'€' : '' }}
                        </td>
                        <td style="text-align: right; font-weight: bolder;">
                            {{ $tab_reglements['montant_abonnement_adherents'] > 0 ? number_format($tab_reglements['montant_abonnement_adherents'], 2, ',', '').'€' : '' }}
                        </td>
                        <td style="text-align: right; font-weight: bolder;">
                            {{ $tab_reglements['montant_florilege_adherents'] > 0 ? number_format($tab_reglements['montant_florilege_adherents'], 2, ',', '').'€' : '' }}
                        </td>
                        <td style="text-align: right; font-weight: bolder;">
                            {{ number_format($tab_reglements['montant_total_adherents'], 2, ',', '').'€' }}
                        </td>
                    </tr>
                    </tfoot>
                </table>
            </div>
        @endif
    </div>

    <div class="blue-font" style="margin-top: 60px; text-transform: uppercase; font-size: 13px; font-weight: bolder;">
        Règlement de {{ number_format($invoice->montant, 2, ',', ' ') }} € effectué en date du {{ $invoice->created_at->format('d/m/Y') }}.
    </div>
@endsection
