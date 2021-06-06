<!DOCTYPE html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <style>
        body {
            margin: 0;
        }
    
        .app-name {
            margin: 0;
            background-color: #F8FAFC;
            color: #BCBFC4;
            text-align: center;
            border: 1px solid #EDEFF3;
        }
    
        h1 {
            margin: 0;
            height: 76px;
            line-height: 76px;
            font-size: 18px;
            text-shadow: 0 1px 0 white;
        }
        
        div {
            margin: 35px;
            height: 300px;
            color: #3d4852;
            overflow-wrap: break-word;
        }
    
        h2 {
            font-size: 19px;
        }
    
        p {
            font-size: 16px;
        }
    
        p:first-child {
            margin-bottom: 30px;
        }
        
        small {
            font-size: 12px;
            height: 100px;
            line-height: 100px;
        }
    </style>
</head>
<body>
    <header class="app-name">
        <h1><a>{{ config('app.name') }}</a></h1>
    </header>
    <div>
        <h2>メールアドレスの変更依頼を受け付けました。</h2>
        <p>以下のURLをクリックして、変更を完了させてください。<br>
        <a href="{{url('/customer/registed_email/verify/'.$email_update_token)}}">{{url('/customer/registed_email/verify/'.$email_update_token)}}</a></p>

        <p>もしこのメールにお心当たりが無いようでしたら、本メールを破棄していただきますようお願いいたします。</p>
    </div>
    <footer class="app-name">
        <small>&copy;{{ date('Y') }} {{ config('app.name') }}. All rights reserved.</small>
    </footer>
</body>
</html>