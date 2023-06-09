@extends('layouts.default')
@section('content')

    <div class="profilePage pageCanva accountPage">
        <h1 class="pageTitle">
            Mon compte
        </h1>
        <h2 class="accountSubtitle"><span class="statusLabel">Statut :</span> Abonné</h2>
@include('layouts.accountMenu')
        @yield('contentaccount')
    </div>
@endsection



