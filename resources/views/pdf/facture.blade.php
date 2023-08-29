@extends('layouts.pdf-fpf')

@section('content')
    <div style="font-size: 13px; margin-top: 50px;">
        <table style="width: 100%">
            <tr>
                <td style="width: 60%">
                    <div style="font-weight: bolder; font-size: 18px;">Facture n° {{ $invoice->numero }}</div>
                    <div style="font-weight: bolder; font-size: 15px;">Référence: {{ $invoice->reference }}</div>
                </td>
                <td style="width: 40%">
                    @if($personne)
                        <div style="font-weight: bolder; font-size: 15px;">{{ $personne->nom.' '.$personne->prenom }}</div>
                    @endif
                    @if($club)
                        <div style="font-weight: bolder; font-size: 15px;">{{ $club->nom }}</div>
                    @endif
                    <div>{{ $adresse->libelle1 }}</div>
                    <div>{{ $adresse->libelle2 }}</div>
                    <div>{{ $adresse->codepostal.' '.$adresse->ville }}</div>
                </td>
            </tr>
        </table>
    </div>
    <div style="font-size: 14px; margin-top: 60px;">
        <table style="width: 100%">
            <tr>
                <td style="width: 80%">
                    <div style="border-bottom: 1px solid #aaa; padding-bottom: 5px; margin-bottom: 5px;">Description</div>
                </td>
                <td style="width: 20%; text-align: right;">
                    <div style="border-bottom: 1px solid #aaa; padding-bottom: 5px; margin-bottom: 5px;">Montant</div>
                </td>
            </tr>
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
        </table>
    </div>
    <div style="margin-top: 60px; text-align: center; font-size: 15px;">
        Règlement de {{ number_format($invoice->montant, 2, ',', ' ') }} € effectué en date du {{ $invoice->created_at->format('d/m/Y') }}.
    </div>
@endsection
