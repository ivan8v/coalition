<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Coalition</title>

    <script src="{{ mix('js/app.js') }}" defer></script>
</head>
<body>
<nav class="navbar navbar-light bg-light">
    <span class="navbar-brand mb-0 h1">Coalition Skill Test</span>
</nav>
<div id="app">
    @yield('content')
</div>
</body>
<link href="{{ mix('css/app.css') }}" rel="stylesheet">
</html>
