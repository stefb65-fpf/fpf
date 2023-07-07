@if($errors && (count($errors) > 0))
    <div class="alertDanger">
        <strong>Attention</strong> Certains champs n'ont pas été correctement remplis
        <ul>
            @foreach($errors->all() as $error)
                <li>{{$error}}</li>
            @endforeach
        </ul>
    </div>
@endif

@if(Session::has('success'))
    <div class="alertSuccess">
        {!! Session::get('success') !!}
    </div>
@endif

@if(Session::has('error'))
    <div class="alertDanger">
        {{ Session::get('error') }}
    </div>
@endif
