<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <!-- Scripts -->
    @yield('head_script')
    <!-- Styles -->
    <link href="{{ asset('css/reset.css') }}?{{ date('Ymd') }}" rel="stylesheet">
    @yield('style')
</head>
<body>
    <div id="app">
        <nav>
            <ul>
                <li class="logo">
                    <button class="link-open" type="button" style="background-color:#fff;" onClick="linkOpen()">
                        <span class="link-icon">
                           <img src="{{ asset('image/bars_24.png') }}">
                        </span>
                    </button>
                    <a class="" href="{{ url('schedule-new/calendar') }}">{{ config('app.name') }}</a>
                </li>
                <li class="link">
                    <a href="{{ route('share.index') }}" class="nav-menu">予定共有管理</a>
                </li>
                <li class="link has-child">
                    <a href="#">会員情報管理</a>
                    <ul>
                        <li>
                            <a class="" href="{{ route('customer.index') }}">会員情報確認</a>
                        </li>
                        <li>
                            <a class="" href="{{ route('logout') }}"
                                onclick="event.preventDefault();
                                document.getElementById('logout-form').submit();"
                            >
                                ログアウト
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                        </li>
                    </ul>
                </li>
            </ul>
        </nav>
        @yield('content')
    </div>

    @yield('body_script')
    <script
        src="https://code.jquery.com/jquery-3.4.1.min.js"
        integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo="
        crossorigin="anonymous"
    ></script>
    <script>
        $(window).resize(function() {
            mediaQueriesWin();
        });
        $(window).on('load',function() {
            mediaQueriesWin();
        });

        /**
         * ヘッダーサブメニューの表示・非表示切り替え
         * @return {void}
         */
        function mediaQueriesWin() {
        let width = $(window).width();
            if(width <= 768) {
                $(".has-child>a").on('click', function() {
                    let parentElem = $(this).parent();
                    $(parentElem).toggleClass('active');
                    $(parentElem).children('ul').stop().slideToggle(500);
                    return false;
                });
            } else {
                $(".has-child>a").off('click');
                $(".has-child").removeClass('active');
                $('.has-child').children('ul').css("display","");
            }
        }

        /**
         * スマホ表示のヘッダーメニュー表示・非表示切り替え
         * @return {void}
         */
        function linkOpen() {
            let links = document.getElementsByClassName('link');
            for(i = 0; i < links.length; i++) {
                if (links[i].classList.contains('display-block')) {
                    links[i].classList.remove('display-block');
                } else {
                    links[i].classList.add('display-block');
                }
            }
        }
    </script>
</body>
</html>
