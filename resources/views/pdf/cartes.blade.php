@extends('layouts.pdf')

@section('content')
    <style>
        *{margin:0;padding:0}
        .wrapper-page {
            page-break-after: always;
            width: 100%;
            font-size: 12px;
            height: 100%;
        }
        .wrapper-page:last-child {
            page-break-after: avoid;
        }
        .title {
            text-align: center;
            font-weight: bolder;
            font-size: 13px;
            position: fixed;
            top: 20px;
            width: 100%;
        }
        .identite {
            width: 100%;
            margin-top: 50px;
            text-align: center;
        }
        .identifiant {
            position: fixed;
            bottom: 45px;
            width: 100%;
            text-align: center;
        }
        .club {
            position: fixed;
            bottom: 65px;
            width: 100%;
            text-align: center;
        }
    </style>
    @foreach($tab_cartes as $carte)
    <div class="wrapper-page">
        <div class="title">PHOTOGRAPHE FPF</div>
        <div class="identite">
            <div>
                {{ $carte->personne->sexe == 0 ? 'Mr' : 'Mme' }} {{ $carte->personne->nom }} {{ $carte->personne->prenom }}
            </div>
            @if($carte->personne->adresses[0]->libelle1)
                <div>{{ $carte->personne->adresses[0]->libelle1 }}</div>
            @endif
            @if($carte->personne->adresses[0]->libelle2)
                <div>{{ $carte->personne->adresses[0]->libelle2 }}</div>
            @endif
            <div>
                {{ str_pad($carte->personne->adresses[0]->codepostal, 5, '0', STR_PAD_LEFT) }} {{ strtoupper($carte->personne->adresses[0]->ville) }}
            </div>
        </div>


        @if($carte->clubs_id)
            <div class="club">{{ $carte->club->nom.' ('.str_pad($carte->club->numero, 4, '0', STR_PAD_LEFT).')' }}</div>
        @endif

        <div class="identifiant">Adhérent {{ $carte->identifiant }}</div>
        <div>
            <table class="w100 fixed b40 l0" >
                <tr>
                    <td class="w33">
                        <div class="h30 bgLightGrey borderDarkGrey text-center ml5 mr5">
                            {{ in_array(date('m'), ['09', '10', '11', '12']) ? date('y').' - '.(date('y') + 1) : (date('y') - 1).' - '.date('y') }}<br>
                            <span class="small">septembre - septembre</span>
                        </div>
                    </td>
                    <td class="w33">
                        <div class="h30 bgLightGrey borderDarkGrey text-center ml5 mr5">
                            &nbsp;
                        </div>
                    </td>
                    <td class="w33">
                        <div class="h30 bgLightGrey borderDarkGrey text-center ml5 mr5">
                            &nbsp;
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    </div>
    @endforeach
@endsection
