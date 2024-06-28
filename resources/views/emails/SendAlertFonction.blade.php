@extends('layouts.email')
@section('content')
    <div class="mailContent">
        <div class="text" style="font-size: 16px;">
            Une nouvelle fonction spécifique UR a été créée par l'UR <b>{{ str_pad($urs_id, 2, '0', STR_PAD_LEFT) }}</b> avec le libellé <b>{{ $libelle }}</b>.
        </div>
    </div>
@endsection
