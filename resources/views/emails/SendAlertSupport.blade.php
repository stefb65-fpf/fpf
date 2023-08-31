@extends('layouts.email')
@section('content')
    <div class="mailContent">
        <div class="text" style="font-size: 16px;">
            Une nouvelle demande de support avec la référence <b>{{ str_pad($support->id, 5, '0', STR_PAD_LEFT) }}</b> et l'objet <b>{{ $support->objet }}</b> a été saisie sur la base en ligne. <br>
            Vous pouvez la consulter et la traiter en vous connectant à l'administration de la base en ligne.<br>
        </div>
    </div>
@endsection
