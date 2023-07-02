<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" class="custom-scrollbar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="locale" content="{{ app()->getLocale() }}">

    @yield('meta')

    <link rel="icon" href="./favicon.ico" type="image/x-icon">
    <title>{{ isset($title) ? $title.' | ' : '' }}{{ config('app.name') }}</title>

    {{-- fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=EB+Garamond:wght@400;700&display=swap" rel="stylesheet">

    {{-- normalize --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/8.0.1/normalize.min.css" integrity="sha512-NhSC1YmyruXifcj/KFRWoC561YpHpc5Jtzgvbuzx5VozKpWvQ+4nXhPdFgmx8xqexRcpAglTj9sIBWINXa8x5w==" crossorigin="anonymous" referrerpolicy="no-referrer" />
 
    {{-- jquery --}}
    <script src="https://code.jquery.com/jquery-3.6.4.min.js" integrity="sha256-oP6HI9z1XaZNBrJURtCoUT5SUnxFr8s3BzRl+cbzUq8=" crossorigin="anonymous"></script>

    <script>
        var alertTimeout
    </script>

    <script>
        var translations = JSON.parse('{!! addslashes(json_encode(__('js'))) !!}');
    </script>
    
    @vite(['resources/scss/app.scss'])
    @stack('css')
</head>
<body class="custom-scrollbar">
    @include('layout.header', ['hide_brand' => $hide_brand ?? false, 'sidebar' => $show_sidebar ?? false])

    <div id="content">
        @auth
            @includeWhen($show_sidebar ?? false, 'layout.sidebar')
        @endauth
        
        <main @if(isset($class)) class="{{$class}}" @endif>
            @yield('content')
        </main>
    </div>
    
    @include('layout.footer')

    <div id="alert"></div>

    @stack('modals')
    
    @include('layout.background')

    @stack('templates')

    @stack('js')
    @vite(['resources/js/app.js'])
</body>
</html>