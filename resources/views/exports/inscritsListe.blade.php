<table>
    <thead>
    <tr>
        <th>Nom</th>
        <th>Prénom</th>
        <th>Email</th>
        <th>Téléphone mobile</th>
        <th>Date d'inscription</th>
    </tr>
    </thead>
    <tbody>
    @foreach($inscrits as $inscrit)
        <tr>
            <td>{{ $inscrit->personne->nom }}</td>
            <td>{{ $inscrit->personne->prenom }}</td>
            <td>{{ $inscrit->personne->email }}</td>
            <td>{{ $inscrit->personne->phone_mobile }}</td>
            <td>{{ date('d/m/Y', (strtotime($inscrit->created_at))) }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
