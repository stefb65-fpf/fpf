<table>
    <thead>
    <tr>
        <th>Club</th>
        <th>Genre</th>
        <th>Nom</th>
        <th>Prénom</th>
        <th>Adresse 1</th>
        <th>Adresse 2</th>
        <th>Code postal</th>
        <th>Ville</th>
        <th>Pays</th>
        <th>Infos</th>
        <th>Email</th>
    </tr>
    </thead>
    <tbody>
    @foreach($abonnes as $abonne)
        <tr>
            <td></td>
            <td>{{ $abonne->personne->sexe === 0 ? 'Mr' : 'Mme' }}</td>
            <td>{{ $abonne->personne->nom }}</td>
            <td>{{ $abonne->personne->prenom }}</td>
            <td>{{ $abonne->adresse->libelle1 }}</td>
            <td>{{ $abonne->adresse->libelle2 }}</td>
            <td>{{ str_pad($abonne->adresse->codepostal, 5, '0', STR_PAD_LEFT) }}</td>
            <td>{{ strtoupper($abonne->adresse->ville) }}</td>
            <td>{{ strtoupper($abonne->adresse->pays) }}</td>
            <td>{{ $abonne->fin === $numeroencours ? 'Ceci est le dernier numéro de votre abonnement' : '' }}</td>
            <td>{{ $abonne->personne->email }}</td>
        </tr>
    @endforeach
    @foreach($clubs as $club)
        <tr>
            <td>{{ $club->nom }}</td>
            <td>{{ isset($club->personne) ? ($club->personne->sexe === 0 ? 'Mr' :  'Mme') : '' }}</td>
            <td>{{ isset($club->personne) ? $club->personne->nom : $club->nom }}</td>
            <td>{{ isset($club->personne) ? $club->personne->prenom : '' }}</td>
            <td>{{ $club->adresse->libelle1 }}</td>
            <td>{{ $club->adresse->libelle2 }}</td>
            <td>{{ str_pad($club->adresse->codepostal, 5, '0', STR_PAD_LEFT) }}</td>
            <td>{{ strtoupper($club->adresse->ville) }}</td>
            <td>{{ strtoupper($club->adresse->pays) }}</td>
            <td>{{ $abonne->numerofinabonnement === $numeroencours ? 'Ceci est le dernier numéro de votre abonnement' : '' }}</td>
            <td>{{ isset($club->personne) ? $club->personne->email : $club->courriel }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
