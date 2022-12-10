<!DOCTYPE html>
<html lang="ja">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
</head>
<body>
    <div class="content">
        @yield('content')
    </div>
    <footer class="app-name">
        <small>&copy;{{ date('Y') }} {{ config('app.name') }}. All rights reserved.</small>
    </footer>
</body>
</html>
