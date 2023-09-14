@extends('layouts.email')
@section('content')
    <div class="mailContent">
        <div class="text"  style="font-size: 16px;">
            Pour effectuer votre vote, veuillez renseigner sur le site le code ci-après
        </div>
        <div style="text-align: center; font-size: 20px; font-weight: bolder; margin-top: 30px;">{{ $code }}</div>
    </div>
@endsection
