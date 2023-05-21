@props([
    'title',
    'desckey',
    'route',
])

<meta name="description" content="{{ __("metadesc.{$desckey}", ['app_name' => config('app.name')]) }}">

<meta property="og:title" content="{{ isset($title) ? $title.' | ' : '' }}{{ config('app.name') }}">
<meta property="og:description" content="{{ __("metadesc.{$desckey}", ['app_name' => config('app.name')]) }}">
<meta property="og:url" content="{{ route($route) }}">
<meta property="og:type" content="website">
{{-- <meta property="og:image" content="https://www.yourapp.com/images/login.jpg"> --}}

<meta name="twitter:card" content="summary">
<meta name="twitter:title" content="{{ isset($title) ? $title.' | ' : '' }}{{ config('app.name') }}">
<meta name="twitter:description" content="{{ __("metadesc.{$desckey}", ['app_name' => config('app.name')]) }}">
{{-- <meta name="twitter:image" content="https://www.yourapp.com/images/login.jpg"> --}}