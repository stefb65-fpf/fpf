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
    @foreach($adherents as $adherent)
        <tr>
            <td>{{ $adherent->identifiant }}</td>
            <td>{{ $adherent->nom }}</td>
            <td>{{ $adherent->prenom }}</td>
            <td>{{ $adherent->adresse->libelle1 }}</td>
            <td>{{ $adherent->adresse->libelle2 }}</td>
            <td>{{ str_pad($adherent->adresse->codepostal, 5, '0', STR_PAD_LEFT) }}</td>
            <td>{{ strtoupper($adherent->adresse->ville) }}</td>
            <td>{{ strtoupper($adherent->adresse->pays) }}</td>
            <td>{{ $adherent->email }}</td>
            <td>{{ $adherent->phone_mobile }}</td>
            <td>
                @switch($adherent->statut)
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
            <td>{{ $adherent->is_abonne?"Avec":"Sans" }}</td>
            <td>{{ $adherent->fin }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
