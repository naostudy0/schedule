@extends('layouts.create')


@section('title', '予定入力')


@section('action')
{{ route('plan.confirm') }}
@endsection


@section('btn')
<a class="btn btn-secondary" href="{{ route('schedule') }}?date={{ $plan_data['cancel_date'] }}">キャンセル</a>
@endsection