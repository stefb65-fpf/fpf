@extends('layouts.pdf-etiquettes')

@section('content')
    <table style="width: 100%; font-size: 13px">
        @php $nb = 1 @endphp
        @foreach($etiquettes as $etiquette)
            @if($nb%2 == 1)
                <tr>
                    @endif
                    <td>
                        <div style="width:330px;height: 132px;padding: 10px;{{ $nb%2 === 0 ? 'margin-left:30px' : 'margin-left:0px' }}">
                            <table style="width: 100%; padding: 25px 0px 0px">
                                <tr style="padding: 0; margin: 0;">
                                    <td style="padding: 0; margin: 0">**C**</td>
                                    <td style="padding: 0; margin: 0">{{ $etiquette->identifiant }}</td>
                                </tr>
                                <tr style="height: 10px;">
                                    <td colspan="2" style="padding: 0; margin: 0">
                                        {{ ($etiquette->personne->sexe == 0) ? 'Mr' : 'Mme'  }}
                                        {{ $etiquette->personne->nom.' '.$etiquette->personne->prenom }}
                                    </td>
                                </tr>
                                <tr style="height: 10px;">
                                    <td colspan="2">{{ $etiquette->personne->adresses[0]->libelle1 }}</td>
                                </tr>
                                <tr style="height: 10px;">
                                    <td colspan="2">{{ $etiquette->personne->adresses[0]->libelle2 }}</td>
                                </tr>
                                <tr style="height: 10px;">
                                    <td colspan="2">{{ str_pad($etiquette->personne->adresses[0]->codepostal, 5, '0', STR_PAD_LEFT ).' '.strtoupper($etiquette->personne->adresses[0]->ville) }}</td>
                                </tr>
                            </table>
                        </div>
                    @if($nb%2 == 0)
                </tr>
                @endif
                @php $nb++ @endphp
                </td>
                @endforeach

    </table>
@endsection
@section('css')
    <link href="{{ asset('css/pdf/etiquettes.css') }}" rel="stylesheet">
@endsection
