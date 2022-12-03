@extends('layouts.customer')

@section('title', '退会確認')

@section('content_customer')
<div class="offset-md-2 col-md-8">
<p class="confirm">登録した予定が全て削除されます。本当に退会しますか？</p>
</div><!-- col -->
<a href="{{ route('customer.delete.store') }}" class="btn delete float-right">退会</a>
@endsection


@section('btn_footer')
<a href="{{ route('customer.index') }}" class="btn btn-secondary">戻る</a>
@endsection