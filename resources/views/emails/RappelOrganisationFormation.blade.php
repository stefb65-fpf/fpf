@extends('layouts.email')
@section('content')
    <div class="mailContent">
        <div class="text" style="font-size: 16px;">
            Un session doit être organisée pour la formation {{ $session->formation->name }} à la date du {{ date("d/m/Y",strtotime($session->start_date)) }}.<br>
            Cette session comporte actuellement {{ count($session->inscrits->where('status', 1)) }} inscrits.<br>
            Elle est organisée par
            @if($session->club_id)
                le club {{ $session->club->numero }} - {{ $session->club->nom }}
            @elseif($session->ur_id)
                l'UR {{ $session->ur_id }}
            @else
                la FPF.
            @endif
            <br><br>
            Veuillez effectuer les actions nécessaires à l'organisation de cette formation.
        </div>
    </div>
@endsection
