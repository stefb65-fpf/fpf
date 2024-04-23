@extends('layouts.default')
@section('content')
    <div class="pageCanva">
        <h1 class="pageTitle">
            Ajout d'un formateur
            <a class="previousPage" title="Retour page précédente" href="{{ route('formateurs.index') }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor"
                     class="bi bi-reply-fill" viewBox="0 0 16 16">
                    <path
                        d="M5.921 11.9 1.353 8.62a.719.719 0 0 1 0-1.238L5.921 4.1A.716.716 0 0 1 7 4.719V6c1.5 0 6 0 7 8-2.5-4.5-7-4-7-4v1.281c0 .56-.606.898-1.079.62z"/>
                </svg>
            </a>
        </h1>
        <div class="w100">
            @include('admin.formateurs.searchForm')
            @include('admin.formateurs.form', ['action' => 'store'])
        </div>
    </div>
@endsection
@section('css')
    <link href="{{asset('css/admin_fpf.css') }}" rel="stylesheet">
@endsection
@section('js')
    <script src="https://cdn.tiny.cloud/1/0tfgoauksbtjlp4sye52sgt384u2fu2caznkjrvnvtncd64s/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>
    <script src="{{ asset('/js/editor.js') }}"></script>
    <script src="{{ asset('/js/admin_formateur.js') }}"></script>
@endsection
