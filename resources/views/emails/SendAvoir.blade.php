@extends('layouts.email')
@section('content')
    <div class="mailContent">
        <div class="text" style="font-size: 16px;">
            Veuillez trouver ci-joint la facture d'avoir référence {{ $invoice->numero }} d'un montant de {{ number_format($invoice->montant, 2, ',', ' ') }} € et générant une créance du même montant.
        </div>
    </div>
@endsection
