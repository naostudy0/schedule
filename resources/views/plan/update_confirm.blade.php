@extends('layouts.confirm')


@section('style')
    @include('layouts.include.add_layout')
@endsection


@section('title', '修正内容確認')


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


@section('id')
<input type="hidden" name="id" id="id" value="{{ old('id', $plan_data['id']) }}">
@endsection


@section('btn')
<button type="submit" formaction="{{ route('plan.update') }}?id={{ $plan_data['id'] }}" class="btn btn-secondary">戻る</button>
<button type="submit" formaction="{{ route('plan.update.store') }}" class="btn btn-success float-right">更新</button>
@endsection