<table>
    <thead>
    <tr>

        <th>Identifiant</th>
        <th>Nom</th>
        <th>Prénom</th>
        <th>Adresse 1</th>
        <th>Adresse 2</th>
        <th>Code postal</th>
        <th>Ville</th>
        <th>Pays</th>
        <th>Email</th>
        <th>Mobile</th>
        <th>Statut</th>
        <th>Abonnement</th>
        <th>Numéro de fin d'abonnement</th>
    </tr>
    </thead>
    <tbody>
    @foreach($utilisateurs as $utilisateur)
        <tr>
            <td>{{ $utilisateur->identifiant }}</td>
            <td>{{ $utilisateur->personne->nom }}</td>
            <td>{{ $utilisateur->personne->prenom }}</td>
            <td>{{ sizeof($utilisateur->personne->adresses) > 0 ? $utilisateur->personne->adresses[0]->libelle1 : '' }}</td>
            <td>{{ sizeof($utilisateur->personne->adresses) > 0 ? $utilisateur->personne->adresses[0]->libelle2 : '' }}</td>
            <td>{{ sizeof($utilisateur->personne->adresses) > 0 ? str_pad($utilisateur->personne->adresses[0]->codepostal, 5, '0', STR_PAD_LEFT) : '' }}</td>
            <td>{{ sizeof($utilisateur->personne->adresses) > 0 ? strtoupper($utilisateur->personne->adresses[0]->ville) : '' }}</td>
            <td>{{ sizeof($utilisateur->personne->adresses) > 0 ? strtoupper($utilisateur->personne->adresses[0]->pays) : '' }}</td>
            <td>{{ $utilisateur->personne->email }}</td>
            <td>{{ $utilisateur->personne->phone_mobile }}</td>
            <td>
                @switch($utilisateur->statut)
                    @case(0)
                    Non renouvelé
                    @break
                    @case(1)
                   Préinscrit
                    @break
                    @case(2)
                    Validé (mais carte pas encore éditée)
                    @break
                    @case(3)
                    Carte éditée
                    @break
                    @case(4)
                    Carte non renouvelée depuis plus d'un an (ancien)
                    @break
                    @default
                   Non renseigné
                @endswitch

            </td>
            <td>{{ $utilisateur->personne->is_abonne ? "Avec" : "Sans" }}</td>
            <td>{{ $utilisateur->fin }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
