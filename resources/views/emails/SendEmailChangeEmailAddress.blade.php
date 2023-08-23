@extends('layouts.email')
@section('content')
    <div class="mailContent" style="text-align: center;">
        {{--    monlien est {{ $link }}--}}
        <div class="text"  style="font-size: 16px;"> Nous avons reçu votre demande de changement d'adresse mail.<br>
            Pour valider la modification , cliquez sur <a class="link" href="{{ $link }}" target="_blank" style="font-weight: 600;">ce lien</a>
        </div>
        <div class="notWorking" style="color: #9a9a9a;font-size: 14px;margin-top: 15px;">Ce lien ne fonctionne pas ?<br>
            Copiez ce lien dans votre barre de recherche: {{ $link }}</div>
    </div>
@endsection
