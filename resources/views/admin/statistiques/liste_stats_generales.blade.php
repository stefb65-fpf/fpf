<div class="mt50 mb50 bolder">
    <div class="text-center"><h2>Adhérents</h2></div>
    <div class="text-center" style="font-size: large">
        {{ $nb_adherents }} / {{ $nb_adherents_previous }}
        @if($ratio_adherents > 0)
            <span style="color: green">(+{{ $ratio_adherents }}%)</span>
        @else
            <span style="color: red">({{ $ratio_adherents }}%)</span>
        @endif
    </div>
</div>
<div class="mt50 mb50 bolder">
    <div class="text-center"><h2>Clubs</h2></div>
    <div class="text-center" style="font-size: large">
        {{ $nb_clubs }} / {{ $nb_clubs_previous }}
        @if($ratio_clubs > 0)
            <span style="color: green">(+{{ $ratio_clubs }}%)</span>
        @else
            <span style="color: red">({{ $ratio_clubs }}%)</span>
        @endif
    </div>
</div>
<div class="mt50 mb50 bolder">
    <div class="text-center"><h2>Abonnements</h2></div>
    <div class="text-center" style="font-size: large">
        {{ $nb_abonnements }} personnes<br>
        {{ $nb_abonnements_clubs }} clubs
    </div>
</div>

<div class="mt50 mb50 bolder">
    <div class="text-center"><h2>Florilège</h2></div>
    <div class="text-center" style="font-size: large">
        {{ $nb_souscriptions }}
    </div>
</div>
