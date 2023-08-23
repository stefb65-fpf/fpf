@extends('layouts.email')
@section('content')
    <div class="mailContent">
        <div class="text">Nous avons enregistré une nouvelle demande de support de votre part<br>

        </div>
      <div><span style="font-weight: bold;margin-right: 10px">Objet : </span> {{$objet}}</div>
      <div><span style="font-weight: bold;margin-right: 10px">Contenu : </span> {{$contenu}}</div>
        <br>
        <div>Votre demande sera traitée dans les meilleurs délais.</div>
        <div> À bientôt sur le site Fédération Photo !</div>
    </div>
@endsection
