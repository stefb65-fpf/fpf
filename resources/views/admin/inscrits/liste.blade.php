@extends('layouts.default')

@section('content')
    <div class="pageCanva">
        <h1 class="pageTitle">
            Liste des inscrits pour la session de formation {{ $session->formation->name }}
            <a class="previousPage" title="Retour page précédente" href="{{ route('sessions.index', $session->formation) }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-reply-fill" viewBox="0 0 16 16">
                    <path d="M5.921 11.9 1.353 8.62a.719.719 0 0 1 0-1.238L5.921 4.1A.716.716 0 0 1 7 4.719V6c1.5 0 6 0 7 8-2.5-4.5-7-4-7-4v1.281c0 .56-.606.898-1.079.62z"/>
                </svg>
            </a>
        </h1>
        <div class="alertInfo w80">
            <p>
                <span class="bold">Informations !</span>
                Si des personnes sont inscrites en liste d'attente, vous pouvez leur transmettre un lien de paiement qu'elles recevront par email.
                Ce lien est valable 48h. Si le paiement n'est pas effectué dans ce délai, la place sera libérée.<br>
                Lorsque vous transmettez le lien, si la formation est complète, aucun autre adhérent ne pourra s'inscrire.
            </p>
        </div>
        <div class="d-flex justify-center">
            <input id="emailAddInscription" type="email" placeholder="Email de l'adhérent à ajouter à la formation" style="width: 300px; margin-right: 5px;" />
            <a id="addInscription" class="btnSmall adminPrimary mr10" data-session="{{ $session->id }}">inscrire l'adhérent</a>
            <a href="{{ route('inscrits.export', $session) }}" class="btnSmall adminSuccess ml10">exporter au format excel</a>
        </div>
        <table class="styled-table">
            <thead>
            <tr>
                <th>Personne</th>
                <th>Email</th>
                <th>Liste principale</th>
                <th>Liste attente</th>
                <th>Attente paiement</th>
                <th></th>
            </thead>
            <tbody>
            @foreach($session->inscrits->where('status', 1) as $inscrit)
                <tr>
                    <td>{{ $inscrit->personne->nom.' '.$inscrit->personne->prenom }}</td>
                    <td>{{ $inscrit->personne->email }}</td>
                    <td>{{ $inscrit->attente == 0 ? 'OUI' : '' }} {{ $inscrit->attente_paiement == 1 ? 'NON PAYEE' : '' }}</td>
                    <td>{{ $inscrit->attente == 1 ? 'OUI' : '' }}</td>
                    <td>
                        {{ $inscrit->attente_paiement == 1 ? 'LIEN TRANSMIS' : '' }}
                    </td>
                    <td>
                        @if($inscrit->attente == 0)
                            <a href="{{ route('inscrits.destroy', $inscrit) }}" data-method="delete" data-confirm="Voulez-vous vraiment supprimer cet inscrit sans compensation ?" class="btnSmall adminDanger">Supprimer</a>
                            <a href="{{ route('inscrits.destroyWithCredit', $inscrit) }}" data-method="delete" data-confirm="Voulez-vous vraiment supprimer cet inscrit ? Un avoir formation du montant de son incription lui sera accordé." class="btnSmall adminWarning mt5">Supprimer avec avoir</a>
                        @endif
                        @if($inscrit->attente == 1)
                            <a href="{{ route('inscrits.sendPaymentLink', $inscrit) }}" data-method="post" data-confirm="Voulez-vous vraiement envoyer le lien de paiement à cette personne ?" class="btnSmall adminPrimary">Envoyer lien paiement</a>
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection
@section('css')
    <link href="{{ asset('css/admin_fpf.css') }}" rel="stylesheet">
    <link href="{{ asset('css/formations_fpf.css') }}" rel="stylesheet">
@endsection
@section('js')
    <script src="{{ asset('js/admin_inscriptions.js') }}?t=<?= time() ?>"></script>
@endsection
