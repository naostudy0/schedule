@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card register">
                    <div class="card-header">本会員登録完了</div>

                    <div class="card-body">
                        <p>ご登録ありがとうございます。<br>本会員登録が完了しました。</p>
                        <a href="{{ route('login') }}">ログインはこちら</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection