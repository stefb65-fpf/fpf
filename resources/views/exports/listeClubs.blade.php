<table>
    <thead>
    <tr>
        <th>Numéro Club</th>
        <th>Nom Club</th>
        <th>Statut</th>
        <th>Type</th>
        <th>Email club</th>
        <th>Adresse 1</th>
        <th>Adresse 2</th>
        <th>Code postal</th>
        <th>Ville</th>
        <th>Prénom Contact</th>
        <th>Nom Contact</th>
        <th>Email Contact</th>
    </tr>
    </thead>
    <tbody>
    @foreach($clubs as $k => $club)
        <tr>
            <td>{{ str_pad($club['numero'], 4, '0', STR_PAD_LEFT) }}</td>
            <td>{{ $club['nom'] }}</td>
            <td>
                @if($club['statut'] == 0)
                    Non renouvelé
                @elseif($club['statut'] == 1)
                    Pré-inscrit
                @elseif($club['statut'] == 2)
                    Validé
                @else
                    Désactivé
                @endif
            </td>
            <td>
                @if($club['second_year'] == 1)
                    Seconde année
                @elseif($club['ct'] == 'N')
                    Nouveau
                @elseif($club['ct'] == 'A')
                    Tous abonnés
                @elseif($club['ct'] == 'C')
                    Tous adhérents
                @else
                    Normal
                @endif
            </td>
            <td>{{ $club['courriel'] }}</td>
            <td>{{ $club['adresse'] ? $club['adresse']['libelle1']  : '' }}</td>
            <td>{{ $club['adresse'] ? $club['adresse']['libelle2']  : '' }}</td>
            <td>{{ $club['adresse'] ? str_pad($club['adresse']['codepostal'], 5, '0', STR_PAD_LEFT)  : '' }}</td>
            <td>{{ $club['adresse'] ? strtoupper($club['adresse']['ville'])  : '' }}</td>
            <td>{{ $club['contact'] ? $club['contact']['nom']  : '' }}</td>
            <td>{{ $club['contact'] ? $club['contact']['prenom']  : '' }}</td>
            <td>{{ $club['contact'] ? $club['contact']['email']  : '' }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
