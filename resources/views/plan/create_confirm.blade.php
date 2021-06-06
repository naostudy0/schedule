@extends('layouts.confirm')


@section('style')
    @include('layouts.include.add_layout')
@endsection


@section('title', '登録内容確認')


@section('btn')
<button type="submit" formaction="{{ route('plan.recreate') }}" class="btn btn-secondary">修正</button>
<button type="submit" formaction="{{ route('plan.store') }}" class="btn btn-success float-right">登録</button>
@endsection