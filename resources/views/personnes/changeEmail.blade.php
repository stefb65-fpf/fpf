@extends('layouts.login')

@section('content')
    <div class="authWrapper">
        <div class="authLogo">
            <img src="{{ env('APP_URL').'storage/app/public/FPF-100-Logo-Seul.webp' }}"
                 alt="Fédération Photographique de France">
        </div>
        <div class="authTitle">Confirmation de changement de votre <br> adresse email</div>
        <div class="fosterRegister light">
           <div class="foster">Cous avez demandé à changer votre adresse mail actuelle pour l'adresse mail <div class="bold"> {{$personne->nouvel_email}}</div>   </div>
        </div>
        <form action="{{ route('resetEmail', $personne) }}" method="POST" class="authForm">
            <input type="hidden" name="_method" value="put">
            {{ csrf_field() }}
          <div class="foster">Souhaitez-vous confirmer ce changement ?</div>
            <button id="resetPasswordBtn" type="submit" class="button customBtn" >Valider</button>

        </form>


    </div>
@endsection
