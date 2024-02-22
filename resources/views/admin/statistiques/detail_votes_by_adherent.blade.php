<h2 class="urTitle">
    Liste des adhérents du club {{ $club->nom }} n'ayant pas voté pour le vote "{{ $vote->nom }}"
</h2>
@if(sizeof($adherents) == 0)
    <p>Tous les adhérents du club ont voté</p>
@else
    <table  class="styled-table">
        <thead>
        <tr>
            <th>Nom</th>
            <th>Prénom</th>
            <th>Identifiant</th>
        </tr>
        </thead>
        <tbody>
        @foreach($adherents as $adherent)
            <tr>
                <td>{{ $adherent->nom }}</td>
                <td>{{ $adherent->prenom }}</td>
                <td>{{ $adherent->identifiant }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endif
