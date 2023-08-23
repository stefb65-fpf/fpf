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
                       <div class="seeMore underline underlineBlue blue modalTrigger" data-modal-contenu='{{$mail->contenu}}'>Voir le contenu</div>
                       <div class="date dark">
                           <div>{{ $mail->created_at }}</div>
{{--                           <div class="day">{{$mail->date}}</div>--}}
{{--                           <div class="time">{{$mail->hour}}</div>--}}
                       </div>
                   </div>
               </div>
           @endforeach
       </div>
            {{ $mails->render( "pagination::default") }}
    </div>
@endsection

