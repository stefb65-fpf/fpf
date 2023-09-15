@extends('layouts.default')

@section('content')
    <div class="pageCanva">
        <h1 class="pageTitle">
            Gestion Fédérale - Reversements en attente
            <a class="previousPage" title="Retour page précédente" href="{{ route('reversements.index') }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-reply-fill" viewBox="0 0 16 16">
                    <path d="M5.921 11.9 1.353 8.62a.719.719 0 0 1 0-1.238L5.921 4.1A.716.716 0 0 1 7 4.719V6c1.5 0 6 0 7 8-2.5-4.5-7-4-7-4v1.281c0 .56-.606.898-1.079.62z"/>
                </svg>
            </a>
        </h1>
        <div class="alertInfo w80">
            <p>
                <span class="bold">Informations !</span>
                Vous retrouvez ici la liste des reversements à effectués. Ceux-ci sont basés sur l'ensemble des règlements effectués au moment de la consultation de la page.
                Il faut valider chaque reversement à effectuer en cliquant sur le bouton "Valider le reversement".
                Une fois validé, un bordereau est édité et vous pouvez le retrouver dans la liste des reversments effectués.
            </p>
        </div>
        <div>
            @foreach($tab_reversements as $k => $reversement)
                <div class="mt60 bolder" style="font-size: 1.3rem; display: flex">
                    <div>
                        Reversement pour l'UR {{ str_pad($k, 2, '0', STR_PAD_LEFT) }}: {{ number_format($reversement['total']['total'], 2, ',', ' ').'€' }}
                    </div>
                    <div class="ml50">
                        <a class="btnMedium adminSuccess" name="validReversementUr" data-ur="{{ $k }}" data-montant="{{ $reversement['total']['total'] }}">Valider le reversement</a>
                    </div>
                </div>
                <table class="styled-table">
                    <thead>
                    <tr>
                        <th>Numéro club</th>
                        <th>Nom club</th>
                        <th style="text-align: right">Cartes</th>
                        <th style="text-align: right">Adhésions UR</th>
                        <th style="text-align: right">Abonnement</th>
                        <th style="text-align: right">Total</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr style="font-weight: bolder; font-size: 1.1rem">
                        <td colspan="2">Total reversement UR</td>
                        <td style="text-align: right">{{ isset($reversement['total']['cartes']) ? number_format($reversement['total']['cartes'], 2, ',', '') : '' }}</td>
                        <td style="text-align: right">{{ isset($reversement['total']['adhesion_ur']) ? number_format($reversement['total']['adhesion_ur'], 2, ',', '') : '' }}</td>
                        <td style="text-align: right">{{ isset($reversement['total']['abonnements']) ? number_format($reversement['total']['abonnements'], 2, ',', '') : '' }}</td>
                        <td style="text-align: right">{{ isset($reversement['total']['total']) ? number_format($reversement['total']['total'], 2, ',', '') : '' }}</td>
                    </tr>
                    @foreach($reversement as $j => $v)
                        @if($j != 'total')
                            <tr>
                                <td>{{ $v['numero'] }}</td>
                                <td>{{ $v['nom'] }}</td>
                                <td style="text-align: right">{{ isset($v['cartes']) ? number_format($v['cartes'], 2, ',', '') : '' }}</td>
                                <td style="text-align: right">{{ isset($v['adhesion_ur']) ? number_format($v['adhesion_ur'], 2, ',', '') : '' }}</td>
                                <td style="text-align: right">{{ isset($v['abonnements']) ?number_format($v['abonnements'], 2, ',', '') : '' }}</td>
                                <td style="text-align: right">{{ isset($v['total']) ? number_format($v['total'], 2, ',', '') : '' }}</td>
                            </tr>
                        @endif
                    @endforeach
                    </tbody>
                </table>
            @endforeach
        </div>

        <div class="modalEdit d-none" id="modalReversementUr">
            <div class="modalEditHeader">
                <div class="modalEditTitle">Reversement UR</div>
                <div class="modalEditClose">
                    X
                </div>
            </div>
            <div class="modalEditBody">
                Voulez-vous vraiment effectuer le reversement de <span class="bolder" id="montantReversementUr"></span>€ pour
                l'UR <span class="bolder" id="urIdReversement"></span> ?<br>
                Un bordereau sera alors généré et vous pourrez le retrouver dans la liste des reversements effectués.
            </div>
            <div class="modalEditFooter">
                <div class="adminDanger btnMedium mr10 modalEditClose">Annuler</div>
                <div class="adminPrimary btnMedium mr10" id="confirmReversementUr" data-montant="" data-ur="">Valider</div>
            </div>
        </div>
    </div>
@endsection
@section('css')
    <link href="{{ asset('css/admin_fpf.css') }}" rel="stylesheet">
@endsection
@section('js')
    <script src="{{ asset('js/admin_reversements.js') }}?t=<?= time() ?>"></script>
@endsection
