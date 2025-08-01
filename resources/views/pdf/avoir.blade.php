@extends('layouts.pdf-facture')

@section('content')
    <div style="font-size: 11px; margin-top: 50px;">
        <div class="blue-font" style="text-align: left; font-size: 16px; text-transform: uppercase; font-weight: bold;">
            Facture d'avoir n° {{ $invoice->numero }}
        </div>
        @if($primary_invoice)
        <div class="blue-font" style="text-align: left; font-size: 12px; text-transform: uppercase; font-weight: bold;">
            Facture d'avoir en remboursement par créance de la facture n° {{ $primary_invoice }}
        </div>
        @endif
        <div style="width: 100%; position: relative; margin-top: 25px;">
            <div style="position: absolute; right: 0;">
                @if($personne)
                    <div class="blue-font" style="text-transform: uppercase; font-weight: bolder;">{{ $personne->nom.' '.$personne->prenom }}</div>
                @endif
                @if($club)
                    <div class="blue-font" style="text-transform: uppercase; font-weight: bolder;">{{ $club->nom }}</div>
                @endif
                <div class="blue-font">{{ $adresse->libelle1 }}</div>
                <div class="blue-font">{{ $adresse->libelle2 }}</div>
                <div class="blue-font">{{ $adresse->codepostal.' '.$adresse->ville }}</div>
            </div>
        </div>
    </div>

    <div style="font-size: 12px; margin-top: 100px;">
        @if($type == 'personne')
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
            <div style="margin-top: 20px;">
                <table class="facture-table" style="width: 100%; border-spacing: 0px;">
                    <thead class="bg-blue">
                    <tr>
                        <th style="text-align: center">Adhérent</th>
                        <th style="text-align: center">Type carte</th>
                        <th style="text-align: center">Adhésion</th>
                        <th style="text-align: center">Abonnement</th>
                        <th style="text-align: center">Non remboursé</th>
                        <th style="text-align: center">Créance</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($remboursements as $remboursement)
                        <tr>
                            <td>
                                {{ $remboursement['adherent'] }}
                            </td>
                            <td>
                                {{ $remboursement['ct'] ?? '' }}
                            </td>
                            <td style="text-align: right">
                                {{ $remboursement['adhesion'] > 0 ? '-'.number_format($remboursement['adhesion'], 2, ',', '').'€' : '' }}
                            </td>
                            <td style="text-align: right">
                                {{ $remboursement['abonnement'] > 0 ? '-'.number_format($remboursement['abonnement'], 2, ',', '').'€' : '' }}
                            </td>
                            <td style="text-align: right">
                                {{ $remboursement['montant_non_rembourse'] > 0 ? number_format($remboursement['montant_non_rembourse'], 2, ',', '').'€' : '' }}
                            </td>
                            <td style="text-align: right">
                                {{ '-'.number_format($remboursement['montant_creance'], 2, ',', '').'€' }}
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                    <tfoot class="bg-blue" style="color: white">
                    <tr>
                        <td colspan="5" style="font-weight: bolder;">Total</td>

                        <td style="text-align: right; font-weight: bolder;">
                            {{ number_format($invoice->montant, 2, ',', '').'€' }}
                        </td>
                    </tr>
                    </tfoot>
                </table>
            </div>
        @endif
    </div>

    <div class="blue-font" style="margin-top: 60px; text-transform: uppercase; font-size: 13px; font-weight: bolder;">
        @if($type == 'personne')
            Création d'une créance utilisateur d'un montant de {{ number_format(-$invoice->montant, 2, ',', ' ') }} € effectué en date du {{ $invoice->created_at->format('d/m/Y') }}.
        @else
            Création d'une créance club d'un montant de {{ number_format(-$invoice->montant, 2, ',', ' ') }} € effectué en date du {{ $invoice->created_at->format('d/m/Y') }}.
        @endif

    </div>
@endsection
