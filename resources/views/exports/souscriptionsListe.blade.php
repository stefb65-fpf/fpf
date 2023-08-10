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
        <th>Email</th>
        <th>Téléphone</th>
        <th>Nombre</th>
    </tr>
    </thead>
    <tbody>
    @foreach($souscriptions as $souscription)
        <tr>
            <td>{{ $souscription->club ? $souscription->club->nom : '' }}</td>
            <td>{{ $souscription->destinataire ? ($souscription->destinataire->sexe == 0 ? 'Mr' : 'Mme') : '' }}</td>
            <td>{{ $souscription->destinataire ? $souscription->destinataire->nom  : '' }}</td>
            <td>{{ $souscription->destinataire ? $souscription->destinataire->prenom  : '' }}</td>
            <td>{{ $souscription->destinataire ? $souscription->destinataire->adresses[0]->libelle1  : '' }}</td>
            <td>{{ $souscription->destinataire ? $souscription->destinataire->adresses[0]->libelle2  : '' }}</td>
            <td>{{ $souscription->destinataire ? $souscription->destinataire->adresses[0]->codepostal  : '' }}</td>
            <td>{{ $souscription->destinataire ? strtoupper($souscription->destinataire->adresses[0]->ville)  : '' }}</td>
            <td>{{ $souscription->destinataire ? strtoupper($souscription->destinataire->adresses[0]->pays)  : '' }}</td>
            <td>{{ $souscription->destinataire ? $souscription->destinataire->email  : '' }}</td>
            <td>{{ $souscription->destinataire ? $souscription->destinataire->phone_mobile  : '' }}</td>
            <td>{{ $souscription->nbexemplaires }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
