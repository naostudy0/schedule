@extends('layouts.confirm')


@section('style')
    @include('layouts.include.add_layout')
@endsection


@section('title', '修正内容確認')


@section('id')
<input type="hidden" name="id" id="id" value="{{ old('id', $plan_data['id']) }}">
@endsection


@section('btn')
<button type="submit" formaction="{{ route('plan.update') }}?id={{ $plan_data['id'] }}" class="btn btn-secondary">戻る</button>
<button type="submit" formaction="{{ route('plan.update.store') }}" class="btn btn-success float-right">更新</button>
@endsection