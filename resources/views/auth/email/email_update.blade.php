@extends('auth.email.layout.layout')

@section('content')
<p>
    {{ config('app.name') }}<br><br>

    メールアドレスの変更依頼を受け付けました。<br>
    以下のURLをクリックして、変更を完了させてください。<br>
    <a href="{{ url('/customer/registed_email/verify/' . $email_update_token) }}">
        {{ url('/customer/registed_email/verify/'.$email_update_token) }}
    </a><br><br>

    もしこのメールにお心当たりが無いようでしたら、本メールを破棄していただきますようお願いいたします。
</p>
@endsection
