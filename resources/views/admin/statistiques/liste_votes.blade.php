@foreach($votes as $vote)
    <div class="mt50">
        <h2>{{ $vote->nom }}</h2>
        <div>
            Date de début : {{ substr($vote->debut, 8, 2).'/'.substr($vote->debut, 5, 2).'/'.substr($vote->debut, 0, 4) }}
            - Date de fin : {{ substr($vote->fin, 8, 2).'/'.substr($vote->fin, 5, 2).'/'.substr($vote->fin, 0, 4) }}
            - Nombre de votants : {{ $vote->total_votes }}
        </div>
        <table class="styled-table">
            <thead>
            <tr>
                <th>&Eacute;lection</th>
                <th>Candidat / Motion</th>
                <th>Nombre de voix</th>
            </tr>
            </thead>
            <tbody>
            @foreach($vote->elections as $election)
                <tr>
                    <td colspan="3" class="bolder">{{ $election->nom }}</td>
                </tr>
                @if($election->type == 1)
                    @foreach($election->motions as $motion)
                        <tr>
                            <td></td>
                            <td>{{ $motion->reponse->libelle }}</td>
                            <td>{{ $motion->nb_votes }}</td>
                        </tr>
                    @endforeach
                @else
                    @foreach($election->candidats as $candidat)
                        @if($candidat->utilisateur->personne)
                            <tr>
                                <td></td>
                                <td>{{ $candidat->utilisateur->personne->nom.' '.$candidat->utilisateur->personne->prenom }}</td>
                                <td>{{ $candidat->nb_votes }}</td>
                            </tr>
                        @endif
                    @endforeach
                @endif
            @endforeach
            </tbody>

        </table>
    </div>
@endforeach
