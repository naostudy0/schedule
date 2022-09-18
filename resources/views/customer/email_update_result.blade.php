@extends('layouts.customer')


@section('title')
メールアドレス変更
@endsection


@section('content_customer')
<div class="offset-md-2 col-md-8">
    {{ $result }}
</div><!-- col -->
@endsection


@section('btn_footer')
<a href="{{ route('customer.index') }}" class="btn btn-secondary">戻る</a>
@endsection