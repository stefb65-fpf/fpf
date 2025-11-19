@extends('layouts.default')

@section('content')
    <div class="formationsPage pageCanva">
        <h1 class="pageTitle">
            Formations
        </h1>
        <div>
            Le lien qui vous a été fourni vous permet de vous inscrire à la formation <b>{{ $inscrit->session->formation->name }}</b> pour la session du <b>{{ date("d/m/Y",strtotime($inscrit->session->start_date)) }}</b>.<br>
            Vous devez impérativement régler la somme de {{ number_format($inscrit->session->price, 2, ',', ' ') }}€ par un des deux moyens de paiement proposés ci-dessous. Aucun autre moyen de paiement ne sera accepté.<br>
        </div>
        <div class="d-flex justify-center align-center mt50 w80">
            <div data-return="detail" class="primary btnRegister" id="formationPayVirement" data-link="{{ $inscrit->secure_code }}" data-ref="{{ $inscrit->session->id }}">Payer par virement<br>la somme de {{ number_format($inscrit->session->price, 2, ',', ' ') }}€</div>
            <div data-return="detail" class="primary btnRegister" id="formationPayCb" data-link="{{ $inscrit->secure_code }}" data-ref="{{ $inscrit->session->id }}">Payer par CB<br>la somme de {{ number_format($inscrit->session->price, 2, ',', ' ') }}€</div>
        </div>
    </div>
@endsection
@section('js')
    <script src="{{ asset('js/formation.js') }}?t=<?= time() ?>"></script>
@endsection
