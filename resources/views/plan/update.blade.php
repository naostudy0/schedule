@extends('layouts.create')


@section('title','予定修正')


@section('action')
{{ route('plan.update.confirm') }}
@endsection


@section('id')
<input type="hidden" name="id" id="id" value="{{ old('id', is_array($plan_data) ? $plan_data['id'] : $plan_data->plan_id) }}">
@endsection


@section('btn')
<a class="btn btn-secondary" href="{{ route('schedule') }}?date={{ is_array($plan_data) ? substr($plan_data['start_datetime'], 0 ,7) : substr($plan_data->start_datetime, 0 ,7) }}">キャンセル</a>
@endsection