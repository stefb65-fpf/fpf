@extends('layouts.pdf-liste')

@section('content')
    <table style="width: 100%; font-size: 13px">
        <thead>
        <tr>
            <th>DPT</th>
            <th>UR</th>
            <th>CLUB</th>
            <th>CP</th>
            <th>VILLE</th>
            <th>TÉLÉPHONE</th>
            <th>E-MAIL</th>
        </tr>
        </thead>
        <tbody>
        @php $dpt_prev = 0; @endphp
        {{-- Sort clubs by department --}}
        @foreach($clubs as $club)
            <tr>
                <td style="background-color: #e9f1ed; color: #abcdbe; font-weight: bold">{{ $club->dpt != $dpt_prev ? $club->dpt : '' }}</td>
                <td style="background-color: #abcdbe">{{ $club->urs_id }}</td>
                <td style="text-transform: uppercase">{{ strtoupper($club->nom) }}</td>
                <td>{{ $club->codepostal }}</td>
                <td style="text-transform: uppercase">{{ strtoupper($club->ville) }}</td>
                <td style="width: 80px;">
                    @if($club->telephonemobile)
                        {{ $club->telephonemobile }}
                    @elseif($club->telephonedomicile)
                        {{ $club->telephonedomicile }}
                    @endif
                </td>
                <td>{{ $club->courriel }}</td>
            </tr>
            @php $dpt_prev = $club->dpt; @endphp
        @endforeach
        </tbody>
    </table>
@endsection
@section('css')
    <link href="{{ asset('css/pdf/etiquettes.css') }}" rel="stylesheet">
@endsection
