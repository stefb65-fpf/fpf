@extends('layouts.account')
@section('contentaccount')
    <div class="accountContent">
       <div class="accountListWrapper">
           @foreach($mails as $mail)
               <div class="listLine mailList">
                   <div class="listLineLeft">
                       <div class="listMailObject blue bold">{{$mail->titre}}</div>
                       <div class="listMailReceiver italic">{{$mail->destinataire}}</div>
                   </div>
                   <div class="listLineRight">
                       <div class="seeMore underline  underlineBlue blue">Voir le contenu</div>
                       <div class="date dark">
                           <div class="day">{{$mail->date}}</div>
                           <div class="time">{{$mail->hour}}</div>
                       </div>
                   </div>
               </div>
           @endforeach

    </div>
        <nav>
            {{ $mails->render( "pagination::default") }}
        </nav>
@endsection

