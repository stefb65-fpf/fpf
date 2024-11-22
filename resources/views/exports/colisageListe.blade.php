<table>
    <thead>
    <tr>
        <th>UR</th>
        <th>Numéro Club</th>
        <th>Nom Club</th>
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
    @foreach($souscriptions as $k => $souscription)
        <tr>
            <td>{{ $souscription['ur'] }}</td>
            <td>{{ $k < 100000 ? str_pad($k, 4, '0', STR_PAD_LEFT) : '' }}</td>
            <td>{{ $souscription['club'] }}</td>
            <td>{{ $souscription['contact'] ? ($souscription['contact']['sexe'] == 0 ? 'Mr' : 'Mme') : '' }}</td>
            <td>{{ $souscription['contact'] ? $souscription['contact']['nom']  : '' }}</td>
            <td>{{ $souscription['contact'] ? $souscription['contact']['prenom']  : '' }}</td>
            <td>{{ $souscription['adresse'] ? $souscription['adresse']['libelle1']  : '' }}</td>
            <td>{{ $souscription['adresse'] ? $souscription['adresse']['libelle2']  : '' }}</td>
            <td>{{ $souscription['adresse'] ? str_pad($souscription['adresse']['codepostal'], 5, '0', STR_PAD_LEFT)  : '' }}</td>
            <td>{{ $souscription['adresse'] ? strtoupper($souscription['adresse']['ville'])  : '' }}</td>
            <td>{{ $souscription['adresse'] ? strtoupper($souscription['adresse']['pays'])  : '' }}</td>
            <td>{{ $souscription['contact'] ? $souscription['contact']['email']  : '' }}</td>
            <td>{{ $souscription['contact'] ? $souscription['contact']['phone_mobile']  : '' }}</td>
            <td>{{ $souscription['nbexemplaires'] }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
