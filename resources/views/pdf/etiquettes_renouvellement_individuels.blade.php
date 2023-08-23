@extends('layouts.pdf')

@section('content')
    <table class="w100 fs13px">
        @php $nb = 1 @endphp
        @foreach($tab_individuels as $etiquette)
            @if($nb%2 == 1)
                <tr>
                    @endif
                    <td>
                        <div class="p5" style="width:330px;height: 132px;{{ $nb%2 === 0 ? 'margin-left:30px' : 'margin-left:0px' }}">
                            <table class="w100">
                                <tr class="p0 m0">
                                    <td class="p0 m0">**C**</td>
                                    <td class="p0 m0">{{ $etiquette->identifiant }}</td>
                                </tr>
                                <tr class="h10">
                                    <td colspan="2" class="p0 m0">
                                        {{ ($etiquette->personne->sexe == 0) ? 'Mr' : 'Mme'  }}
                                        {{ $etiquette->personne->nom.' '.$etiquette->personne->prenom }}
                                    </td>
                                </tr>
                                <tr class="h10">
                                    <td colspan="2">{{ $etiquette->personne->adresses[0]->libelle1 }}</td>
                                </tr>
                                <tr class="h10">
                                    <td colspan="2">{{ $etiquette->personne->adresses[0]->libelle2 }}</td>
                                </tr>
                                <tr class="h10">
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
