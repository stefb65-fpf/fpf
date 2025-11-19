<table>
    <thead>
    <tr>
        <th>Formation</th>
        <th>Formateurs</th>
        <th>Date session</th>
        <th>Type session</th>
        <th>Ur / Club</th>
        <th>Cout global UR / Club</th>
        <th>Frais déplacement</th>
        <th>Prise en charge FPF</th>
        <th>Prise en charge UR / Club</th>
        <th>Prix fédéré</th>
        <th>Prix non fédéré</th>
        <th>Nb places normales</th>
        <th>Nb places en attente</th>
        <th>Nb inscrits validés</th>
        <th>Nb inscrits en attente</th>
    </tr>
    </thead>
    <tbody>
    @foreach($formations as $formation)
        @foreach($formation->sessions as $k => $session)
            <tr>
                <td>{{ $formation->name }}</td>
                <td>
                  @foreach($formation->formateurs as $formateur)
                    {{ $formateur->title }}<br>
                  @endforeach
                </td>
                <td>{{ date("d/m/Y",strtotime($session->start_date)) }}</td>
                <td>
                    @if($session->type == 0)
                        distanciel
                    @elseif($session->type == 1)
                        présentiel
                    @else
                        distanciel et présentiel
                    @endif
                </td>
                <td>
                    @if($session->ur_id)
                        UR {{ $session->ur_id }}
                    @elseif($session->club_id)
                        Club {{ $session->club->numero }} - {{ $session->club->nom }}
                    @endif
                </td>
                <td>{{ ($session->ur_id || $session->club_id) ? $formation->global_price : '' }}</td>
                <td>{{ (($session->ur_id || $session->club_id) && $session->frais_deplacement > 0) ? $session->frais_deplacement : '' }}</td>
                <td>{{ $session->pec_fpf > 0 ? $session->pec_fpf : '' }}</td>
                <td>
                    @if($session->ur_id || $session->club_id)
                        @if($session->reste_a_charge > 0)
                            {{ $session->reste_a_charge }}
                        @else
                            {{ $session->pec }}
                        @endif
                    @endif
                </td>
                <td>{{ $session->price }}</td>
                <td>{{ $session->price_not_member }}</td>
                <td>{{ $session->places }}</td>
                <td>{{ $session->waiting_places }}</td>
                <td>
                    {{ $session->inscrits->where('status', 1)->where('attente', 0)->count() }}
                </td>
                <td>
                    {{ $session->inscrits->where('status', 1)->where('attente', 1)->count() }}
                </td>
            </tr>
        @endforeach
    @endforeach
    </tbody>
</table>
