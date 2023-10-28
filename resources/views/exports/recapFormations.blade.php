<table>
    <thead>
    <tr>
        <th>Formation</th>
        <th>Session</th>
        <th>Places</th>
        <th>Places en liste d'attente</th>
        <th>Inscrits liste principale</th>
        <th>Inscrits liste d'attente'</th>
        <th>Places libres</th>
    </tr>
    </thead>
    <tbody>
        @foreach($formations as $formation)
            <tr>
                <td>{{ $formation->name }}</td>
                <td colspan="6"></td>
            </tr>
            @foreach($formation->sessions->sortBy('start_date') as $session)
                <tr>
                    <td></td>
                    <td>
                        {{ date("d/m/Y",strtotime($session->start_date)) }} -
                        @if(in_array($session->type, [0,2]))
                            A distance -
                        @endif
                        @if($session->location)
                            {{ $session->location }}
                        @endif
                    </td>
                    <td>{{ $session->places }}</td>
                    <td>{{ $session->waiting_places }}</td>
                    <td>{{ sizeof($session->inscrits->where('status', 1)->where('attente', 0)) }}</td>
                    <td>{{ sizeof($session->inscrits->where('status', 1)->where('attente', 1)) }}</td>
                    <td>{{ $session->places - sizeof($session->inscrits->where('status', 1)->where('attente', 0)) }}</td>
                </tr>
            @endforeach
        @endforeach
    </tbody>
</table>
