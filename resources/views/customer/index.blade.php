@extends('layouts.customer')

@section('title', '会員情報')

@section('content_customer')
<dl>
    <div class="offset-md-2 col-md-8">
        <dt class="label">名前</dt>
        <dd>{{ Auth::user()->name }}</dd>
    </div>
    <a href="{{ route('customer.name.update') }}">
        <div class="float-right btn btn-success change">変更</div>
    </a>

    <div class="clear"></div>
    <hr>

    <div class="offset-md-2 col-md-8">
        <dt class="label">パスワード</dt>
        <dd>******<span class="attention">（表示されません）</span></dd>
    </div>
    <a href="{{ route('customer.password.update') }}">
        <div class="float-right btn btn-success change">変更</div>
    </a>

    <div class="clear"></div>
    <hr>

    <div class="offset-md-2 col-md-8">
        <dt class="label">メールアドレス</dt>
        <dd>{{ Auth::user()->email }}</dd>
    </div>
    <a href="{{ route('customer.email.update') }}">
        <div class="float-right btn btn-success change">変更</div>
    </a>
</dl>
@endsection


@section('btn_footer')
<a href="{{ route('customer.delete.confirm') }}"><p class="float-right delete btn btn-light btn_footer">退会する</p></a>
@endsection