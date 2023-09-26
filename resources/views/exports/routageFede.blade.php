<table>
    <thead>
    <tr>
        <th>Ur</th>
        <th>Club</th>
        <th>Nom Club</th>
        <th>Identifiant</th>
        <th>Genre</th>
        <th>Nom</th>
        <th>Prénom</th>
        <th>Adresse 1</th>
        <th>Adresse 2</th>
        <th>Code postal</th>
        <th>Ville</th>
        <th>Pays</th>
        <th>Email</th>
        <th>Mobile</th>
    </tr>
    </thead>
    <tbody>
    @foreach($personnes as $personne)
        <tr>
            <td>{{ $personne->urs_id }}</td>
            <td>{{ $personne->nunero_club }}</td>
            <td>{{ $personne->nom_club }}</td>
            <td>{{ $personne->identifiant }}</td>
            <td>{{ $personne->sexe === 0 ? 'Mr' : 'Mme' }}</td>
            <td>{{ $personne->nom }}</td>
            <td>{{ $personne->prenom }}</td>
            <td>{{ $personne->adresse->libelle1 }}</td>
            <td>{{ $personne->adresse->libelle2 }}</td>
            <td>{{ str_pad($personne->adresse->codepostal, 5, '0', STR_PAD_LEFT) }}</td>
            <td>{{ strtoupper($personne->adresse->ville) }}</td>
            <td>{{ strtoupper($personne->adresse->pays) }}</td>
            <td>{{ $personne->email }}</td>
            <td>{{ $personne->phone_mobile }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
