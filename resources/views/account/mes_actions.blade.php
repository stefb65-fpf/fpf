@extends('layouts.account')
@section('contentaccount')
    <div class="accountContent">
        <div class="accountListWrapper">
            @foreach($actions as $action)
            <div class="listLine actionList">
                <div class="listActionType blue bold uppercase mr25">{!! $action->action !!}
                </div>
                <div class="date italic">{!! $action->date !!}</div>
            </div>
            @endforeach
        </div>

    </div>
@endsection

