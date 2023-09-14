@extends('layouts.default')

@section('content')
    <div class="pageCanva">
        <h1 class="pageTitle">
            Gestion Fédérale - Reversements effectués
            <a class="previousPage" title="Retour page précédente" href="{{ route('reversements.index') }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-reply-fill" viewBox="0 0 16 16">
                    <path d="M5.921 11.9 1.353 8.62a.719.719 0 0 1 0-1.238L5.921 4.1A.716.716 0 0 1 7 4.719V6c1.5 0 6 0 7 8-2.5-4.5-7-4-7-4v1.281c0 .56-.606.898-1.079.62z"/>
                </svg>
            </a>
        </h1>
        <div>
            <div class="pagination">
                {{ $reversements->render( "pagination::default") }}
            </div>
            <table class="styled-table">
                <thead>
                <tr>
                    <th>UR</th>
                    <th>Date</th>
                    <th>Montant</th>
                    <th>Référence</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @foreach($reversements as $reversement)
                    <tr>
                        <td>{{ str_pad($reversement->urs_id, 2, '0', STR_PAD_LEFT) }}</td>
                        <td>{{ substr($reversement->created_at, 8, 2).'/'.substr($reversement->created_at, 5, 2).'/'.substr($reversement->created_at, 0, 4) }}</td>
                        <td>{{ number_format($reversement->montant, 2, ',', ' ').'€' }}</td>
                        <td>{{ $reversement->reference }}</td>
                        <td>
                            <a class="adminPrimary btnSmall" target="_blank" href="{{ $reversement->bordereau }}">bordereau</a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            <div class="pagination">
                {{ $reversements->render( "pagination::default") }}
            </div>
        </div>
    </div>
@endsection
@section('css')
    <link href="{{ asset('css/admin_fpf.css') }}" rel="stylesheet">
@endsection
