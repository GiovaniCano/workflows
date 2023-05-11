<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <link rel="icon" href="./favicon.ico" type="image/x-icon">
    <title>{{ isset($title) ? $title.' | ' : '' }}{{ config('app.name') }}</title>

    @vite(['resources/scss/app.scss', 'resources/js/app.js'])
    @stack('css')
    @stack('js')
</head>
<body>
    <main>
        @yield('content')
    </main>
</body>
</html>