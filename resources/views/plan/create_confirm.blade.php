@extends('layouts.confirm')

@section('title', '登録内容確認')

@section('hidden')
    @csrf
    <input type="hidden" name="start_date" id="start_date" value="{{ $plan_data['start_date'] }}">                   
    <input type="hidden" name="start_time" id="start_time" value="{{ $plan_data['start_time'] }}">
    <input type="hidden" name="end_date" id="end_date" value="{{ $plan_data['end_date'] }}">
    <input type="hidden" name="end_time" id="end_time" value="{{ $plan_data['end_time'] }}">
    <input type="hidden" name="color" id="color" value="{{ $plan_data['color'] }}">
    <input type="hidden" name="content" id="content" value="{{ $plan_data['content'] }}">
    <input type="hidden" name="detail" id="detail" value="{{ $plan_data['detail'] }}">

    @if( array_key_exists('share_users', $plan_data))
        @if ($plan_data['share_users'] == 1)
            @foreach ($plan_data['share_user'] as $share_user )
            <input type="hidden" name="share_user[]" id="share_user" value="{{ $share_user }}">
            @endforeach
        @endif
    @endif
@endsection


@section('btn')
<button type="submit" formaction="{{ route('plan.recreate') }}" class="btn btn-secondary">修正</button>
<button type="submit" formaction="{{ route('plan.store') }}" class="btn btn-success float-right">登録</button>
@endsection