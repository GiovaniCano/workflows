<header class="container" id="header">
    <div>
        @unless ($hide_brand ?? false)
            <x-brand :href="route(auth()->check() ? 'workflow.index' : 'landing-page')" />
        @endunless
    </div>

    <nav>
        @guest
            <a class="btn btn-secondary" href="{{route('register')}}">{{__('Register')}}</a>
            <a class="btn btn-primary" href="{{route('login')}}">{{__('Login')}}</a>
        @endguest
        @auth
            <a class="btn btn-secondary" href="{{route('workflow.index')}}">{{__('Workflows')}}</a>
        @endauth
    </nav>
</header>