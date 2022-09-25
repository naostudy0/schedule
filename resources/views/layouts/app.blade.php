<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}?{{ date('Ymd') }}" rel="stylesheet">
    <link href="{{ asset('css/register.css') }}?{{ date('Ymd') }}" rel="stylesheet">
    <link href="{{ asset('css/schedule.css') }}?{{ date('Ymd') }}" rel="stylesheet">
    <link href="{{ asset('css/customer.css') }}?{{ date('Ymd') }}" rel="stylesheet">
    <link href="{{ asset('css/share.css') }}?{{ date('Ymd') }}" rel="stylesheet">
    @yield('style')
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/schedule') }}">
                    {{ config('app.name', 'Laravel') }}
                </a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav mr-auto">

                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <!-- Authentication Links -->
                        @guest
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                            </li>
                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                </li>
                            @endif
                        @else
                            <li class="nav-list">
                                <a href="{{ route('share.index') }}" class="nav-menu"
                                href="{{ route('share.index') }}";>
                                予定共有管理</a>
                            </li>
                            <li class="nav-item dropdown nav-list">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle nav-menu" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    会員情報 <span class="caret"></span>
                                </a>


                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                
                                    <p class="user-name">{{ Auth::user()->name }}さん</p>

                                    <a class="dropdown-item" href="{{ route('customer.index') }}"
                                       onclick="location.href='{{ route('customer.index') }}'";>
                                        会員情報確認
                                    </a>

                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        ログアウト
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-4">
            @yield('content')
        </main>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    @yield('script')

</body>
</html>
