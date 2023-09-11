@extends('layouts.default')

@section('content')
    <div class="pageCanva">
        <h1 class="pageTitle">
            Gestion Fédérale - Historique des éditions de cartes
            <a class="previousPage" title="Retour page précédente" href="{{ route('admin') }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-reply-fill" viewBox="0 0 16 16">
                    <path d="M5.921 11.9 1.353 8.62a.719.719 0 0 1 0-1.238L5.921 4.1A.716.716 0 0 1 7 4.719V6c1.5 0 6 0 7 8-2.5-4.5-7-4-7-4v1.281c0 .56-.606.898-1.079.62z"/>
                </svg>
            </a>
        </h1>
        <table class="styled-table">
            <thead>
            <tr>
                <th>Date</th>
                <th>Fichier carte</th>
                <th>Fichier étiquettes club</th>
                <th>Fichier étiquettes individuels</th>
            </tr>
            </thead>
            <tbody>
            @foreach($tab_files as $k => $v)
                <tr>
                    <td>{{ substr($k, 6, 2).'/'.substr($k, 4, 2).'/'.substr($k, 0, 4).' '.substr($k, 8, 2).':'.substr($k, 10, 2) }}</td>
                    <td>
                        @if(isset($v['cartes']))
                            <a href="{{ url($v['path'].'/'.$v['cartes']) }}" style="color: #003d77">fichier cartes</a>
                        @endif
                    </td>
                    <td>
                        @if(isset($v['clubs']))
                            <a href="{{ url($v['path'].'/'.$v['clubs']) }}" style="color: #003d77">étiquettes club</a>
                        @endif
                    </td>
                    <td>
                        @if(isset($v['indiv']))
                            <a href="{{ url($v['path'].'/'.$v['indiv']) }}" style="color: #003d77">étiquettes individuels</a>
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection
