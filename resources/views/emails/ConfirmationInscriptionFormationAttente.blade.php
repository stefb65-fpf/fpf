@extends('layouts.email')
@section('content')
    <div class="mailContent">
        <div class="text" style="font-size: 16px;">
            Nous vous confirmons votre inscription sur liste d'attente à la formation {{ $session->formation->name }} pour la session du {{ date("d/m/Y",strtotime($session->start_date)) }}.<br>
            Si une place se libère en liste principale, vous recevrez un email vous invitant à effectuer un paiement  pour réserver votre place.<br>
        </div>
    </div>
@endsection
