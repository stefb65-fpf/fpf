@extends('layouts.account')
@section('contentaccount')
    <div class="accountContent">
        <div class="accountListWrapper">
            @foreach($historiques as $historique)
            <div class="listLine actionList">
                <div class="listActionType blue bold uppercase mr25">{!! $historique->action !!}
                </div>
                <div class="date dark">{!! $historique->created_at !!}</div>
            </div>
            @endforeach
        </div>
        {{ $historiques->render( "pagination::default") }}
    </div>
@endsection

