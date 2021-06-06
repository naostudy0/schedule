@extends('layouts.confirm')


@section('style')
    @include('layouts.include.add_layout')
@endsection


@section('title', '削除内容確認')


@section('id')
<input type="hidden" name="id" id="id" value="{{ $plan_data['id'] }}">
@endsection


@section('btn')
<a href="{{ route('schedule') }}?date={{ substr($plan_data['start_date'],0 ,7) }}" class="btn btn-success">キャンセル</a>
<button type="submit" formaction="{{ route('plan.delete') }}" formmethod="GET"  class="btn btn-danger float-right">削除</button>
@endsection