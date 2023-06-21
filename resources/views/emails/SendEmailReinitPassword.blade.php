@extends('layouts.email')
@section('content')
    <div class="mailContent">
        {{--    monlien est {{ $link }}--}}
        <div class="text"> Nous avons reçu votre demande de réinitialisation de mot de passe.<br>
            Pour le modifier , cliquez sur <a class="link" href="{{ $link }}">ce lien</a>
        </div>
        <div class="notWorking">Ce lien ne fonctionne pas ?<br>
            Copiez ce lien dans votre barre de recherche: {{ $link }}</div>
    </div>
@endsection
