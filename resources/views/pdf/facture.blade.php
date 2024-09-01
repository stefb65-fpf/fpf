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
{{--        <table style="width: 100%">--}}
{{--            <tr>--}}
{{--                <td style="width: 60%">--}}
{{--                    <div style="font-weight: bolder; font-size: 18px;"></div>--}}
{{--                    <div style="font-weight: bolder; font-size: 15px;">Référence: {{ $invoice->reference }}</div>--}}
{{--                </td>--}}
{{--                <td style="width: 40%">--}}
{{--                    @if($personne)--}}
{{--                        <div style="font-weight: bolder; font-size: 15px;">{{ $personne->nom.' '.$personne->prenom }}</div>--}}
{{--                    @endif--}}
{{--                    @if($club)--}}
{{--                        <div style="font-weight: bolder; font-size: 15px;">{{ $club->nom }}</div>--}}
{{--                    @endif--}}
{{--                    @if($ur)--}}
{{--                        <div style="font-weight: bolder; font-size: 15px;">{{ $ur->nom }}</div>--}}
{{--                    @endif--}}
{{--                    <div>{{ $adresse->libelle1 }}</div>--}}
{{--                    <div>{{ $adresse->libelle2 }}</div>--}}
{{--                    <div>{{ $adresse->codepostal.' '.$adresse->ville }}</div>--}}
{{--                </td>--}}
{{--            </tr>--}}
{{--        </table>--}}
    </div>
    <div style="font-size: 12px; margin-top: 100px;">
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
    </div>
    <div class="blue-font" style="margin-top: 60px; text-transform: uppercase; font-size: 13px; font-weight: bolder;">
        Règlement de {{ number_format($invoice->montant, 2, ',', ' ') }} € effectué en date du {{ $invoice->created_at->format('d/m/Y') }}.
    </div>
@endsection
