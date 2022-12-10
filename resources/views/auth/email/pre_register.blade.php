@extends('auth.email.layout.layout')

@section('content')
<p>
    {{ config('app.name') }}<br><br>

    サイトへのアカウント仮登録が完了しました。<br>
    以下のURLからログインして、本登録を完了させてください。<br>
    <a href="{{ url('register/verify/' . $token) }}">
        {{ url('register/verify/' . $token) }}
    </a><br><br>

    もしこのメールにお心当たりが無いようでしたら、本メールを破棄していただきますようお願いいたします。
</p>
@endsection
