<section
    {{ $attributes->merge([
        'class' => 'grey-container'
    ]) }}
>
<header @class(['js-sidebar-highlight-container' => $type !== 2])>
    <h2>
        {{-- <div class="debug">{{ $section->pivot->position }}</div> --}}
        <a href="#{{$section->id . '-' .$section->make_slug()}}"
            id="{{ $type === 2 ? '' : $section->id . '-' .$section->make_slug() }}"
            @class(['js-sidebar-highlight-target' => $type !== 2])
        >
            # {{ $section->name }}
        </a>
    </h2>
</header>

<div>
    @php
        $section_items = $section->getAllContent();
    @endphp
    @foreach ($section_items as $item)
    
        @switch($item::class)
            {{-- SECTION --}}
            @case($models['section'])
                @switch($item->type)
                    {{-- SECTION NORMAL --}}
                    @case(1)
                        <x-workflows.section :section="$item" class="section-nested" />
                        @break

                    {{-- SECTION MINI --}}
                    @case(2)
                        @unless (
                            isset($section_items[$loop->index-1]) 
                            && $section_items[$loop->index-1]::class === $models['section'] 
                            && $section_items[$loop->index-1]->type == 2
                        )
                            <section class="container-sections">
                        @endunless
                                <x-workflows.mini-section-btn :section="$item" />
                                @push('modals') 
                                    <x-modal data-minisection-modal-id="{{ $item->id }}" class="modal-mini-section">
                                        <x-workflows.section :section="$item" class="section-mini" />
                                    </x-modal>
                                @endpush
                        @unless (
                            isset($section_items[$loop->index+1]) 
                            && $section_items[$loop->index+1]::class === $models['section'] 
                            && $section_items[$loop->index+1]->type == 2
                        )
                            </section>
                        @endunless
                        @break
                
                    @default
                @endswitch
                @break

            {{-- WYSIWYG --}}
            @case($models['wysiwyg'])
                <section class="wysiwyg-content">
                    {{-- <span class="debug">{{ $item->pivot->position }}</span> --}}
                    {!! $item->content !!}
                </section>
                @break

            {{-- IMAGE --}}
            @case($models['image'])
                @unless (isset($section_items[$loop->index-1]) && $section_items[$loop->index-1]::class === $models['image'])
                    <section class="container-images">
                @endunless

                        <x-workflows.image :image="$item" />

                @unless (isset($section_items[$loop->index+1]) && $section_items[$loop->index+1]::class === $models['image'])
                    </section>
                @endunless
                @break

            @default
        @endswitch

    @endforeach
</div>
</section>