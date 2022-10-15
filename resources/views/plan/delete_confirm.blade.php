@extends('layouts.confirm')

@section('title', '削除内容確認')


@section('id')
<input type="hidden" name="id" id="id" value="{{ $plan_data->plan_id }}">
@endsection


@section('btn')
<a href="{{ route('schedule') }}?date={{ substr($plan_data->start_datetime, 0, 7) }}" class="btn btn-success">キャンセル</a>
<button type="submit" formaction="{{ route('plan.delete') }}" formmethod="GET"  class="btn btn-danger float-right">削除</button>
@endsection