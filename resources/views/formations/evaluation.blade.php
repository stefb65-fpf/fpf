@extends('layouts.default')

@section('content')
    <div class="formationsPage pageCanva">
        <h1 class="pageTitle">
            Evaluation pour la formation {{ $session->formation->name }}<br>en date du {{ date("d/m/Y",strtotime($session->start_date)) }}
        </h1>
        <div class="alertInfo w80">
            <span class="bold">Informations</span> Afin d'évaluer cette formation, voua=s devez noter chaque ci-dessous sur une échelle de 1 à 5. 1 étant la note la plus basse et 5 la note la plus haute.<br>
            Vous pouvez également nous faire part de vos remarques en bas de page. N'oubliez pas de valider votre évaluation en cliquant sur le bouton en bas de page.
        </div>
        <form action="{{ route('formations.saveEvaluation', [$user->id, $session->id]) }}" method="POST">
            {{ csrf_field() }}
            @foreach($themes as $theme)
                <div style="border-top: 1px solid grey; padding-top: 25px">
                    <h2>{{ $theme->name }}</h2>
                    @foreach($theme->evaluationsitems->sortBy('position') as $item)
                        <div class="ml50 d-flex mb25 mt30 d-flex justify-center">
                            <div style="width: 300px">{{ $item->name }}</div>
                            <div style="width: 300px">
                                @if($item->type == 1)
                                <input name="rangeeval_{{ $item->id }}" type="range" value="3" min="1" max="5" step="1" list="values" style="width: 100%" />
                                <datalist id="values" style="display: flex;justify-content: space-between;width: 100%">
                                    <option value="1" label="1"></option>
                                    <option value="2" label="2"></option>
                                    <option value="3" label="3"></option>
                                    <option value="4" label="4"></option>
                                    <option value="5" label="5"></option>
                                </datalist>
                                @else
                                    <textarea name="texteval_{{ $item->id }}" id="" cols="30" rows="5"></textarea>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endforeach
            <div class="d-flex justify-center mt25">
                <button class="btnMedium adminSuccess" type="submit">Enregistrer mon évaluation</button>
            </div>

        </form>
    </div>
@endsection
@section('css')
    <link rel="stylesheet" href="{{ asset('css/admin_fpf.css') }}">
@endsection
