@extends('layouts.pdf-colis')

@section('content')
    <style>
        /**{margin:0;padding:0}*/
        .wrapper-page {
            page-break-after: always;
            width: 100%;
            font-size: 12px;
            height: 100%;
        }

        .wrapper-page:last-child {
            page-break-after: never;
        }
    </style>
    @foreach($souscriptions as $club)
        <div class="wrapper-page">
            <div style="margin-left: 450px; padding-top: 30px;">
                {{ $club[0]->contact->prenom }} {{ $club[0]->contact->nom }}<br>
                {{ $club[0]->contact->adresses[0]->libelle1 }}<br>
                @if($club[0]->contact->adresses[0]->libelle2 != '')
                    {{ $club[0]->contact->adresses[0]->libelle2 }}<br>
                @endif
                {{ $club[0]->contact->adresses[0]->codepostal }} {{ $club[0]->contact->adresses[0]->ville }}
            </div>
            <div style="text-align: center; font-weight: bolder; padding-top: 30px;">
                Liste des souscriptions Florilège pour le club {{ $club[0]->club }} - {{ str_pad($club[0]->urs_id, 2, '0', STR_PAD_LEFT).'-'.str_pad($club[0]->numero, 4, '0', STR_PAD_LEFT) }}
            </div>
            <div style="padding-top: 20px;">
                <table style="width: 100%;">
                    <thead>
                    <tr>
                        <th style="text-align: left">Club / Adhérent</th>
                        <th style="text-align: left">Identifiant</th>
                        <th style="text-align: center">Nombre d'exemplaires</th>
                    </tr>
                    </thead>
                    <tbody>
                        @foreach($club as $souscription)
                            <tr>
                                <td style="text-align: left">
                                    @if($souscription->nom)
                                        {{ $souscription->nom }} {{ $souscription->prenom }}
                                    @else
                                        {{ $souscription->club }}
                                    @endif
                                </td>
                                <td style="text-align: left">
                                    @if($souscription->nom)
                                        {{ $souscription->identifiant }}
                                    @else
                                        {{ str_pad($souscription->urs_id, 2, '0', STR_PAD_LEFT).'-'.str_pad($souscription->numero, 4, '0', STR_PAD_LEFT) }}
                                    @endif
                                </td>
                                <td style="text-align: center">
                                    {{ $souscription->nbexemplaires }}
                                </td>
        {{--                        @if($souscription->nom)--}}
        {{--                            Adhérent {{ $souscription->nom }} {{ $souscription->prenom }} - {{ $souscription->identifiant }}: {{ $souscription->nbexemplaires }} exemplaires--}}
        {{--                        @else--}}
        {{--                            Club {{ $souscription->club }} - {{ str_pad($souscription->urs_id, 2, '0', STR_PAD_LEFT).'-'.str_pad($souscription->numero, 4, '0', STR_PAD_LEFT) }}: {{ $souscription->nbexemplaires }} exemplaires--}}
        {{--                        @endif--}}

                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
{{--            <div style="text-align: center; margin-top: 50px; font-size: 16px; font-weight: bolder">--}}
{{--                Club {{ $souscription->nom }} - {{ str_pad($souscription->urs_id, 2, '0', STR_PAD_LEFT).'-'.str_pad($souscription->id, 4, '0', STR_PAD_LEFT) }}:--}}
{{--                <span style="font-size: 20px;">--}}
{{--                    {{ $souscription->nbexemplaires }} exemplaires--}}
{{--                </span>--}}

{{--            </div>--}}

        </div>
{{--        <div class="wrapper-page ">--}}
{{--            <div>--}}
{{--                Club {{ $souscription->nom }}--}}
{{--            </div>--}}
{{--        </div>--}}
    @endforeach

@endsection
