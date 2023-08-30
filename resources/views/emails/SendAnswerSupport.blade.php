@extends('layouts.email')
@section('content')
    <div class="mailContent">
        <div class="text"  style="font-size: 16px;">
            Votre demande de support a bien été traitée. Veuillez trouver ci-dessous la réponse apportée à votre demande.
        </div>
        <hr>
        <div class="text"  style="font-size: 14px; font-weight: bolder; text-decoration: underline;">
            Votre demande
        </div>
        <div class="text"  style="font-size: 14px;">
            {!! $support->contenu !!}
        </div>
        <hr>
        <div class="text"  style="font-size: 14px; font-weight: bolder; text-decoration: underline;">
            Réponse
        </div>
        <div class="text"  style="font-size: 14px;">
            {!! $support->answer !!}
        </div>
        <hr>
        <div class="text"  style="font-size: 12px;">
            Demande traitée par {{ $support->answer_name }}
        </div>
    </div>
@endsection
