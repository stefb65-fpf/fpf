@extends('layouts.email')
@section('content')
    <div class="mailContent">
        <div class="text" style="font-size: 16px;">
            Vous étiez inscrit sur liste d'attente à la formation {{ $inscrit->session->formation->name }} pour la session du {{ date("d/m/Y",strtotime($inscrit->session->start_date)) }}.<br>
            Une place se libère en liste principale et nous vous proposons donc la place vaquante. Pour cela, vous devez confirmer votre inscription en effectuant le paiement via le lien suivant : <br>
            <div style="text-align: center; font-size: large; font-weight: bolder;">
                <a href="{{ route('formations.payWithSecureCode', $inscrit->secure_code) }}">Réserver ma place</a>
            </div>

            <br><br>
            Après avoir cliqué sur le lien, si vous n'êtes pas connecté à la base en ligne, vous devrez vous identifier.<br>
            Le lien de paiement est valable 48h. Passé ce délai, la place sera libérée.<br>
            Si vous ne souhaitez pas confirmer votre inscription, merci de ne pas tenir compte de ce mail.<br><br>
        </div>
    </div>
@endsection
