@props(['big' => false, 'href' => '#', 'heading' => false])

<div {{ $attributes->merge(['class' => 'brand'])->class(['brand-big' => $big]) }}>
    <a class="logo-container" href="{{$href}}">
        <img src="/logo.png" alt="{{config('app.name')}}">
    </a>

    @if($heading)
        <h1><a href="{{$href}}" class="brand-text">{{config('app.name')}}</a></h1>
    @else
        <a class="brand-text" href="{{$href}}">{{config('app.name')}}</a>
    @endif
</div>