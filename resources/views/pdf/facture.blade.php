@extends('layouts.pdf-fpf')

@section('content')
    <div class="fs13px mt50">
        <table class="w100">
            <tr>
                <td class="w60">
                    <div class="bolder fs18px">Facture n° {{ $invoice->numero }}</div>
                    <div class="bolder fs15px">Référence: {{ $invoice->reference }}</div>
                </td>
                <td class="w40">
                    @if($personne)
                    <div class="bolder fs15px">{{ $personne->nom.' '.$personne->prenom }}</div>
                    @endif
                    @if($club)
                        <div class="bolder fs15px">{{ $club->nom }}</div>
                    @endif
                    <div>{{ $adresse->libelle1 }}</div>
                    <div>{{ $adresse->libelle2 }}</div>
                    <div>{{ $adresse->codepostal.' '.$adresse->ville }}</div>
                </td>
            </tr>
        </table>
    </div>
    <div class="fs13px mt50">
        <table class="w100">
            <tr>
                <td class="w80">
                    <div class="borderBottomGrey pb5 mb5">Description</div>
                </td>
                <td class="w20 text-right">
                    <div class="borderBottomGrey pb5 mb5">Montant</div>
                </td>
            </tr>
            <tr>
                <td class="w80">
                    <div class="pr20">
                        {{ $invoice->description }}
                    </div>
                </td>
                <td class="w20 text-right">
                    {{ number_format($invoice->montant, 2, ',', ' ') }} €
                </td>
            </tr>
        </table>
    </div>
    <div class="mt60 text-center fs15px">
        Règlement de {{ number_format($invoice->montant, 2, ',', ' ') }} € effectué en date du {{ $invoice->created_at->format('d/m/Y') }}.
    </div>
@endsection
