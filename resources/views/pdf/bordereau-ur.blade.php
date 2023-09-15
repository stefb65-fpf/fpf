@extends('layouts.pdf-fpf')

@section('content')
    <div style="font-size: 13px;">
        <div style="margin-left: 400px; margin-top: 50px;">
            <div>{{ $ur->nom }}</div>
            <div>{{ strtoupper($president_ur->personne->nom).' '.$president_ur->personne->prenom }}</div>
            @if($president_ur->personne->adresses[0]->libelle1)
                <div>{{ $president_ur->personne->adresses[0]->libelle1 }}</div>
            @endif
            @if($president_ur->personne->adresses[0]->libelle1)
                <div>{{ $president_ur->personne->adresses[0]->libelle2 }}</div>
            @endif
            <div>{{ str_pad($president_ur->personne->adresses[0]->codepostal, 5, '0', STR_PAD_LEFT).' '.$president_ur->personne->adresses[0]->ville }}</div>
        </div>
        <div style="margin-top: 20px; margin-top: 40px;">
            Relevé de l'UR {{ str_pad($ur->id, 2, '0', STR_PAD_LEFT) }} : {{ $ur->nom }}
        </div>
        <div style="margin-top: 30px;">
            <table style="width: 100%; border-spacing: 0px;">
                <thead style="background-color: #cccccc">
                <tr>
                    <th>
                        <div style="padding-top: 10px; padding-bottom: 10px">
                            Numéro club
                        </div>
                    </th>
                    <th>Nom club</th>
                    <th style="text-align: right">Cartes</th>
                    <th style="text-align: right">Adhésions UR</th>
                    <th style="text-align: right">Abonnement</th>
                    <th style="text-align: right">Total</th>
                </tr>
                </thead>
                <tbody>
                <tr style="font-weight: bolder; border-bottom: 1px solid #cccccc">
                    <td colspan="2">
                        <div style="padding-top: 10px; padding-bottom: 10px">
                            Total reversement UR
                        </div>
                    </td>
                    <td style="text-align: right">{{ isset($tab_reversements['total']['cartes']) ? number_format($tab_reversements['total']['cartes'], 2, ',', '') : '' }}</td>
                    <td style="text-align: right">{{ isset($tab_reversements['total']['adhesion_ur']) ? number_format($tab_reversements['total']['adhesion_ur'], 2, ',', '') : '' }}</td>
                    <td style="text-align: right">{{ isset($tab_reversements['total']['abonnements']) ? number_format($tab_reversements['total']['abonnements'], 2, ',', '') : '' }}</td>
                    <td style="text-align: right">{{ isset($tab_reversements['total']['total']) ? number_format($tab_reversements['total']['total'], 2, ',', '') : '' }}</td>
                </tr>
                @foreach($tab_reversements as $j => $v)
                    @if($j != 'total')
                        <tr>
                            <td>
                                <div style="padding-top: 10px; padding-bottom: 10px">
                                    {{ $v['numero'] }}
                                </div>
                            </td>
                            <td>{{ $v['nom'] }}</td>
                            <td style="text-align: right">{{ isset($v['cartes']) ? number_format($v['cartes'], 2, ',', '') : '' }}</td>
                            <td style="text-align: right">{{ isset($v['adhesion_ur']) ? number_format($v['adhesion_ur'], 2, ',', '') : '' }}</td>
                            <td style="text-align: right">{{ isset($v['abonnements']) ?number_format($v['abonnements'], 2, ',', '') : '' }}</td>
                            <td style="text-align: right">{{ isset($v['total']) ? number_format($v['total'], 2, ',', '') : '' }}</td>
                        </tr>
                    @endif
                @endforeach
                </tbody>
            </table>
        </div>
        <div style="margin-top: 20px">
            Total du reversement pour l'UR : <span style="font-weight: bolder">{{ number_format($tab_reversements['total']['total'], 2, ',', '') }}</span>€
        </div>
        <div>
            Règlement effectué le : <span style="font-weight: bolder">{{ date('d/m/Y') }}</span>
        </div>
    </div>
@endsection
