@extends('layouts.app')


@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card register">
                <div class="card-header">仮会員登録完了</div>

                <div class="card-body">
                    <p>ご登録ありがとうございます。</p>
                    <p>ご本人様確認のため、ご登録いただいたメールアドレスに、<br>本登録のご案内をお送りしました。</p>
                    <p>本文に記載されているURLにアクセスし、<br>アカウントの本登録を完了させてください。</p>
                    <a href="{{url('/')}}">トップページへ戻る</a>
                </div><!-- card-body -->
            </div><!-- card -->
        </div><!-- col -->
    </div><!-- row -->
</div><!-- container -->
@endsection

