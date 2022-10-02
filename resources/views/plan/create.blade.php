@extends('layouts.create')

@section('title', '予定入力')

@section('action')
{{ route('plan.confirm') }}
@endsection


@section('btn')
<a class="btn btn-secondary" href="{{ route('schedule') }}?date={{ substr($plan_data['start_datetime'], 0, 7) }}">キャンセル</a>
@endsection