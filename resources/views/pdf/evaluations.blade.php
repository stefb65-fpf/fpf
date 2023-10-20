@extends('layouts.pdf-fpf')

@section('content')
    <div>
        <h1>&Eacute;valuations pour la formation {{ $formation->name }}</h1>
        <div>
            <table style="border-collapse: collapse;margin: 25px 0;font-size: 0.8em;font-family: sans-serif;min-width: 400px;box-shadow: 0 0 20px rgba(0, 0, 0, 0.15);width: 100%;">
                <thead>
                <tr style="background-color: #003d77;color: #ffffff;text-align: left;">
                    <th style="padding: 12px 15px;">Item</th>
                    <th style="padding: 12px 15px;">Nombre d'évaluations</th>
                    <th style="padding: 12px 15px;">Note moyenne</th>
                </tr>
                </thead>
                <tbody>
                @foreach($tab_evaluations as $evaluation)
                    <tr style="border-bottom: 1px solid #dddddd;">
                        <td style="padding: 12px 15px;">{{ $evaluation['name'] }}</td>
                        <td style="padding: 12px 15px;">{{ $evaluation['nb'] }}</td>
                        <td style="padding: 12px 15px;">{{ $evaluation['note'] }} / 5</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            <div>
                <h3>Liste des commentaires saisis</h3>
                @foreach($tab_reviews as $review)
                    <div style="margin-top: 10px;margin-bottom: 10px;padding: 10px;border-bottom: 1px solid #ccc;">{{ $review }}</div>
                @endforeach
            </div>
        </div>
    </div>
@endsection
