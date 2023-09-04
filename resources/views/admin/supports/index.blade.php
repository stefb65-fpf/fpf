@extends('layouts.default')

@section('content')
    <div class="pageCanva">
        <h1 class="pageTitle">
            Espace de gestion des demandes de support
            <a class="previousPage" title="Retour page précédente" href="{{ route('admin') }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-reply-fill" viewBox="0 0 16 16">
                    <path d="M5.921 11.9 1.353 8.62a.719.719 0 0 1 0-1.238L5.921 4.1A.716.716 0 0 1 7 4.719V6c1.5 0 6 0 7 8-2.5-4.5-7-4-7-4v1.281c0 .56-.606.898-1.079.62z"/>
                </svg>
            </a>
        </h1>
        @if(sizeof($supports))
            <div class="pagination">
                {{ $supports->render( "pagination::default") }}
            </div>
            <div class="w100">
                <table class="styled-table">
                    <thead>
                    <tr>
                        <th></th>
                        <th>Provenance</th>
                        <th>Objet</th>
                        <th>Email</th>
                        <th>Identifiant</th>
                        <th>Contenu</th>
                        <th>Statut</th>
                        <th>Traité par</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($supports as $support)
                        <tr>
                            <td>{{ str_pad($support->id, 5, '0', STR_PAD_LEFT) }}</td>
                            <td>{{ $support->provenance }}</td>
                            <td>{{ $support->objet }}</td>
                            <td>{{ $support->email }}</td>
                            <td>{{ $support->identifiant }}</td>
                            <td>{!!   nl2br($support->contenu) !!}</td>
                            <td>
                                @switch($support->statut)
                                    @case(0)
                                    <span>Non traité</span>
                                    @break
                                    @case(1)
                                    <span>Pris en charge</span>
                                    @break
                                    @case(2)
                                    <span>Traité</span>
                                    @break
                                @endswitch
                            </td>
                            <td>
                                @if($support->statut === 2)
                                    <span>{{ $support->answer_name }}</span>
                                @endif
                            <td>
                                @switch($support->statut)
                                    @case(0)
                                        <a class="adminPrimary btnSmall" name="prendreCharge" data-ref="{{ $support->id }}" data-email="{{ $support->email }}" data-content="{!! nl2br(str_replace('"', '', $support->contenu)) !!}">prendre en charge</a>
                                        @break
                                    @case(1)
                                        <span></span>
                                        @break
                                    @case(2)
                                        <a class="adminSuccess btnSmall" name="seeAnswer" data-content="{!! strip_tags($support->answer) !!}">voir la réponse</a>
                                        @break
                                @endswitch
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>

            </div>
            <div class="pagination">
                {{ $supports->render( "pagination::default") }}
            </div>
        @endif
    </div>
    <div class="modalEdit d-none" id="modalAnswerSupport">
        <div class="modalEditHeader">
            <div class="modalEditTitle">Réponse demande de support</div>
{{--            <div class="modalEditClose">--}}
{{--                X--}}
{{--            </div>--}}
        </div>
        <div class="modalEditBody">
            <div class="d-flex">
                <div class="bolder">Email</div>
                <div id="emailAnswerSupport" class="small ml15"></div>
            </div>
            <div class="d-flex">
                <div class="bolder">Demande</div>
                <div id="demandeAnswerSupport" class="italic small ml15"></div>
            </div>
            <div class="d-flex mt25">
                <div class="bolder">Réponse</div>
                <div class="ml15 w100">
                    <textarea id="contentAnswerSupport" class="w100 editor" rows="10"></textarea>
                </div>
            </div>
        </div>
        <div class="modalEditFooter">
            <div class="adminDanger btnMedium mr10 modalEditClose" id="cancelAnswerSupport">Annuler</div>
            <div class="adminSuccess btnMedium" id="sendAnswerSupport">Envoyer la réponse</div>
        </div>
    </div>


    <div class="modalEdit d-none" id="modalSeeAnswerSupport">
        <div class="modalEditHeader">
            <div class="modalEditTitle">Réponse demande de support</div>
            <div class="modalEditClose">
                X
            </div>
        </div>
        <div class="modalEditBody">
            <div class="mt25" id="bodySeeAnswerSupport">
            </div>
        </div>
        <div class="modalEditFooter">
            <div class="adminDanger btnMedium mr10 modalEditClose">Fermer</div>
        </div>
    </div>
@endsection
@section('css')
    <link href="{{ asset('css/admin_fpf.css') }}" rel="stylesheet">
@endsection
@section('js')
    <script src="https://cdn.tiny.cloud/1/40cakvroazrt9qmtcvc4jhwddimpi2cj26v8c03jxkfbc499/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>
    <script src="{{ asset('/js/editor.js') }}"></script>
    <script src="{{ asset('js/admin_supports.js') }}?t=<?= time() ?>"></script>
@endsection
