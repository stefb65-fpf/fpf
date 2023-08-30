@extends('layouts.email')
@section('content')
    <div class="mailContent">
        <div class="text" style="font-size: 16px;">
            Nous avons enregistré une action depuis votre compte de gestion concernant le sujet suivant:<br>
           <span style="font-weight: 600;color: #003d77;">{{ $modification_type }} </span><br>
            Si cette action ne correspond pas à votre demande, merci de contacter très rapidement le secrétariat de la FPF.<br>
        </div>
    </div>
@endsection
