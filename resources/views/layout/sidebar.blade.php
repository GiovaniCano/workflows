<aside id="sidebar" class="glass">
    <div>
        <header>
            <x-brand href="{{route('workflow.index')}}" />
        </header>

        <div class="separator separator-nomargin"></div>
        
        <nav id="sidebar-content" class="custom-scrollbar">
            <a href="{{ route('workflow.create') }}" class="btn-sidebar">{{ __('workflows.new') }}</a>

            {{-- workflows list --}}
            @if(isset($workflows) && count($workflows))
                <div class="separator"></div>

                <div class="sidebar-list" id="sidebar-list-workflows">
                    <p class="text-center">{{ __('workflows.my') }}</p>
                    <ul class="unstyled-list">
                        @foreach ($workflows as $workflow_foreach)
                            <li><a 
                                    href="{{ route('workflow.show', ['workflow' => $workflow_foreach, 'slug' => $workflow_foreach->make_slug()]) }}" 
                                    title="{{ $workflow_foreach->name }}" 
                                    @if(isset($workflow) && ($workflow_foreach->id == $workflow->id)) class="current" @endif
                                >
                                    {{ Str::limit($workflow_foreach->name, 20) }}
                                </a></li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- sections list --}}
            @if (isset($workflow))
                <div class="separator"></div>

                <div class="sidebar-list" id="sidebar-list-sections">
                    <p class="text-center" title="{{ $workflow->name }}">{{ Str::limit($workflow->name, 20) }}</p>
                    {{-- main sections --}}
                    <ol class="unstyled-list">
                        @foreach ($workflow->sections as $main_section)
                            <li>
                                <x-workflows.sidebar-el :section="$main_section" :maxlen="18" />
                                
                                {{-- nested sections --}}
                                @if (count($main_section->sections))
                                    @php
                                        $nested_sections = $main_section->nestedSections();
                                    @endphp
                                    <ol class="unstyled-list">
                                        @foreach ($nested_sections as $nested_section)
                                            <li>
                                                <x-workflows.sidebar-el :section="$nested_section" :maxlen="15" />
                                            </li>
                                        @endforeach
                                    </ol>
                                @endif
                            </li>
                        @endforeach
                    </ol>
                </div>
            @endif
        </nav>

        <div class="separator separator-nomargin"></div>
    
        <footer>
            <nav id="profile-menu" style="display: none">
                <ul class="unstyled-list">
                    <li><a href="{{ route('user.profile') }}">{{ __('Profile') }}</a></li>
                    <li><a href="{{ route('user.payment') }}">{{ __('Payment') }}</a></li>

                    <li class="separator"></li>

                    <li>
                        <form action="{{route('logout')}}" method="POST">
                            @csrf
                            <input type="submit" value="{{__('Logout')}}">
                        </form>
                    </li>
                </ul>
            </nav>
            
            <button id="profile-btn" class="btn-sidebar" title="{{ auth()->user()->name }}" type="button">
                {{ Str::limit(auth()->user()->name, 15) }}
            </button>
        </footer>
    </div>
</aside>