@extends('layouts.supportDefault')
@section('content')
    <div class="d-flex flex-column align-center justify-center text-center">
        <div>Merci pour votre message.</div>
        <div class="mt10">
            Un mail récapitulatif vous en a été envoyé à l'adresse mail:
            <div class="bold">{{$email}}</div>
        </div>
        <div class="mt10">
            Nous avons enregistré votre demande et la FPF la traitera dans les meilleurs délais.
        </div>
        <div class="italic"> À bientôt!</div>
    </div>
@endsection
