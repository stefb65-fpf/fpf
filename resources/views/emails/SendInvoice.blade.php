@extends('layouts.email')
@section('content')
    <div class="mailContent" style="text-align: center;">
        <div class="text" style="font-size: 16px;">
            Veuillez trouver ci-joint la facture référence {{ $invoice->numero }} d'un montant de {{ number_format($invoice->montant, 2, ',', ' ') }} € réglée le {{ $invoice->created_at->format('d/m/Y') }}.
        </div>
    </div>
@endsection
