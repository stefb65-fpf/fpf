@extends('layouts.default')

@section('content')
    <div class="pageCanva">
        <h1 class="pageTitle">
            Espace de gestion du club {{ $club->nom }}
        </h1>
        <div class="cardContainer">
                <a class="card" href="{{ route('clubs.infos_club') }}">
                    <div class="wrapper">
                        <div class="cardTitle">Infos club</div>
                    </div>
                </a>
                <a class="card" href="{{ route('clubs.adherents.index') }}">
                    <div class="wrapper">
                        <div class="cardTitle">Gestion adhésions et abonnements</div>
                    </div>
                </a>
            <a class="card" href="{{ route('clubs.fonctions.index') }}">
                <div class="wrapper">
                    <div class="cardTitle">Fonctions club</div>
                </div>
            </a>
            <a class="card" href="{{ route('clubs.reglements.index') }}">
                <div class="wrapper">
                    <div class="cardTitle">Bordereaux</div>
                </div>
            </a>
            @if($florilege->active)
                <a class="card" href="{{ route('clubs.florilege') }}">
                    <div class="wrapper">
                        <div class="cardTitle">Florilège</div>
                    </div>
                </a>
            @endif
            @if($club_invoices > 0)
                <a class="card" href="{{ route('clubs.factures') }}">
                    <div class="wrapper">
                        <div class="cardTitle">Factures</div>
                    </div>
                </a>
            @endif
            <div class="card invisible">
                <div class="wrapper">
                    <div class="cardTitle"></div>
                </div>
            </div>
            <div class="card invisible">
                <div class="wrapper">
                    <div class="cardTitle"></div>
                </div>
            </div>
        </div>
    </div>
@endsection
