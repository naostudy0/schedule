@extends('layouts.create')


@section('title','予定修正')


@section('action')
{{ route('plan.update.confirm') }}
@endsection


@section('id')
<input type="hidden" name="id" id="id" value="{{ old('id', $plan_data['id']) }}">
@endsection


@section('btn')
<a class="btn btn-secondary" href="{{ route('schedule') }}?date={{ substr($plan_data['start_date'],0 ,7) }}">キャンセル</a>
@endsection