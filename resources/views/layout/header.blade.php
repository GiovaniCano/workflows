<header class="container @if($sidebar) sidebar-present @endif" id="header">
    @if ($sidebar)
        <button id="sidebar-btn">
            <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" stroke-width="3" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                <path d="M4 6l16 0" />
                <path d="M4 12l16 0" />
                <path d="M4 18l16 0" />
            </svg>
        </button>
    @endif

    <div>
        @unless ($hide_brand ?? false)
            <x-brand :href="route(auth()->check() ? 'workflow.index' : 'landing-page')" />
        @endunless
    </div>

    <nav>
        @unless ($sidebar)
            @guest
                <a class="btn btn-secondary" href="{{ route('register') }}">{{ __('Register') }}</a>
                <a class="btn btn-primary" href="{{ route('login') }}">{{ __('Login') }}</a>
            @endguest
            @auth
                <a class="btn btn-secondary m-0" href="{{ route('workflow.index') }}">Workflows</a>
            @endauth
        @endunless
    </nav>
</header>