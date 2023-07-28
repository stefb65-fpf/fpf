@extends('layouts.email')
@section('content')
    <div class="mailContent">
        <div class="text">
            Votre règlement {{ $reglement->reference }} d'un montant de {{ $reglement->montant }}€ a été validé.<br>
            <br>
            Vous pouvez dès à présent consulter les informations d'adhésions et / ou abonnements à partir de votre espace.<br>
        </div>
    </div>
@endsection
