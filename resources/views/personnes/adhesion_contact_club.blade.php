@extends('layouts.account')
@section('contentaccount')
    <div class="mt10">
        <p>Vous avez une carte d'adhésion pour le club <b>{{ $club->nom }}</b>. Vous pouvez envoyer une demande au contact du club en utilisant le formulaire ci-dessous.</p>
        <div class="">
            <form action="{{ route('adhesion.sendMessage') }}" method="POST">
                @csrf
                <div class="mt10">
                    <label style="font-weight: bold">Votre message</label>
                    <textarea name="message" id="" cols="30" rows="10" style="width: 100%; padding: 10px" placeholder="Saisir votre message"></textarea>
                </div>
                <div>
                    <button type="submit" class="btnNormal accountColor">Envoyer la demande</button>
                </div>
            </form>
        </div>
    </div>
@endsection
