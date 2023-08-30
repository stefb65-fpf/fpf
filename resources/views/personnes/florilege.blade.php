@extends('layouts.account')
@section('contentaccount')
    <div class="accountContent">
        <div class="mt25">
            <h1>FLORILEGE</h1>
        </div>
        <div class="florilege">
            <div>
                <img src="{{ url('storage/app/public/uploads/florilege.webp') }}" alt="">
            </div>
            <div>
                <div>
                    Vous pouvez commander des numéros du Florilège au prix de {{ $config->prixflorilegefrance }}€ jusqu'au
                    {{ substr($config->datefinflorilege, 8, 2).'/'.substr($config->datefinflorilege, 5, 2).'/'.substr($config->datefinflorilege, 0, 4) }}
                </div>
                <div>
                    Sélectionnez le nombre d'exemplaires à commander
                    <div class="text-center mt25">
                        <select class="p10 formValue modifying" id="selectFlorilege">
                            @for($i=1; $i<21; $i++)
                                <option value="{{ $i }}">{{ $i }} exemplaire{{ $i > 1 ? 's' : '' }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="mt25">
                        <div>
                            Vous allez commander à titre individuel <span id="nbFlorilege" class="bolder">1</span> exemplaires pour un montant de <span name="priceFlorilege" style="font-weight: bolder">{{ $config->prixflorilegefrance }}</span> €.
                        </div>
                        <div>
                            <button class="primary btnRegister" name="orderFlorilege" data-type="monext" data-personne="{{ $personne->id }}">Payer <span name="priceFlorilege">{{ $config->prixflorilegefrance }}</span>€ par carte bancaire</button>
                            <button class="primary btnRegister" name="orderFlorilege" data-type="bridge" data-personne="{{ $personne->id }}">Payer <span name="priceFlorilege">{{ $config->prixflorilegefrance }}</span>€ par virement</button>
                        </div>
                    </div>
                    <span class="d-none" id="priceUnitFlorilege">{{ $config->prixflorilegefrance }}</span>
                </div>
            </div>

        </div>
    </div>
@endsection
@section('js')
    <script src="{{ asset('js/florilege.js') }}?t=<?= time() ?>"></script>
@endsection
