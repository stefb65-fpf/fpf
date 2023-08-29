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
                <table style="width: 100%; position: fixed; bottom: 40px; left: 0;">
                    <tr>
                        <td style="width: 33%;">
                            <div style="height: 30px; background-color: #F2F2F2; border: 1px solid #3a3a3a; text-align: center; margin-left: 5px; margin-right: 5px;">
                                {{ in_array(date('m'), ['09', '10', '11', '12']) ? date('y').' - '.(date('y') + 1) : (date('y') - 1).' - '.date('y') }}<br>
                                <span style="font-size: 8px;">septembre - septembre</span>
                            </div>
                        </td>
                        <td style="width: 33%;">
                            <div style="height: 30px; background-color: #F2F2F2; border: 1px solid #3a3a3a; text-align: center; margin-left: 5px; margin-right: 5px;">
                                &nbsp;
                            </div>
                        </td>
                        <td style="width: 33%;">
                            <div style="height: 30px; background-color: #F2F2F2; border: 1px solid #3a3a3a; text-align: center; margin-left: 5px; margin-right: 5px;">
                                &nbsp;
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    @endforeach
@endsection
