@extends('layouts.email')
@section('content')
    <div class="mailContent">
        <div class="text">
            Veuillez trouver ci-joint la facture référence {{ $invoice->numero }} d'un montant de {{ number_format($invoice->montant, 2, ',', ' ') }} € réglée le {{ $invoice->created_at->format('d/m/Y') }}.
        </div>
    </div>
@endsection
