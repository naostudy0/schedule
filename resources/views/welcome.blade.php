<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>予定管理n.u</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@200;600&display=swap" rel="stylesheet">

        <!-- Styles -->
        <link href="{{ asset('css/reset.css') }}" rel="stylesheet">
        <link href="{{ asset('css/welcome.css') }}" rel="stylesheet">
    </head>
    <body>
        <div class="main">
            <header>
                <h1>予定管理n.u</h1>
                <nav class="links">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/schedule') }}">予定管理へ</a>
                        @else
                            <a href="{{ route('login') }}">ログイン</a>

                            @if (Route::has('register'))
                                <a href="{{ route('register') }}">会員登録</a>
                            @endif
                        @endauth
                    @endif
                </nav><!-- links -->
            </header>

            <div class="content">
                <section class="sec1">
                    <img src="{{ asset('image/schedule_1280.jpg') }}" alt="予定表">
                </section><!-- sec1 -->

                <section class="sec sec2">
                    <h2>予定を色分けして管理</h2>
                    <div class="explanation">
                        <p>カテゴリごとに予定を色分けして登録できます。</p>
                        <img src="{{ asset('image/post-it_640.png') }}" class="post-it" alt="付箋">
                    </div>
                    <img src="{{ asset('image/device_1280.png') }}" class="screen" alt="アプリのイメージ">
                </section><!-- sec2 -->

                <section class="sec sec3">
                    <h2>本アプリは就職活動用のポートフォリオです</h2>
                    <p>どなたでも無料でご利用いただけますが、以下の点にご注意ください。</p>
                    <ul>
                        <li>予告なく機能の追加・削除、レイアウトの変更を行う場合があります。</li>
                        <li>登録した予定や会員情報の修正・削除は、基本的に行いません。万が一行う場合は、アプリ内で事前告知をした上で行います。</li>
                    </ul>
                </section><!-- sec3 -->
            </div><!-- content -->
        </div><!-- main -->
        <footer>
            <small>&copy 2021 予定管理n.u</small>
        </footer>
    </body>
</html>
