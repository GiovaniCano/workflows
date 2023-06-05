<section
    {{ $attributes->merge([
        'class' => 'grey-container'
    ]) }}
>
<header>
    <h2>
        {{-- <div class="debug">{{ $section->pivot->position }}</div> --}}
        <a href="#{{$section->id . '-' .$section->make_slug()}}"
            id="{{$section->id . '-' .$section->make_slug()}}"
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
                        <x-workflows.section :section="$item" class="section-nested"></x-workflows.section>
                        @break

                    {{-- SECTION MINI --}}
                    @case(2)
                        @unless (
                            isset($section_items[$loop->index-1]) 
                            && $section_items[$loop->index-1]::class === $models['section'] 
                            && $section_items[$loop->index-1]->type == 2
                        )
                            <section class="container-sections m-b-1">
                        @endunless
                                <div>
                                    <button class="mini-section-btn grey-container font-title" 
                                        id="{{$item->id . '-' .$item->make_slug()}}"
                                        data-modal="{{ $item->id }}"
                                    >
                                            # {{ $item->name }}
                                    </button>
                                    @push('modals')
                                        <x-modal id="{{ $item->id }}" class="modal-mini-section">
                                            <x-workflows.section :section="$item" class="section-mini"></x-workflows.section>
                                        </x-modal>
                                    @endpush
                                </div>
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
                    <section class="container-images m-b-1">
                @endunless

                    <div>
                        {{-- <span class="debug">{{ $item->pivot->position }}</span> --}}
                        <img src="{{ $item->url }}" alt="Image">
                    </div>

                @unless (isset($section_items[$loop->index+1]) && $section_items[$loop->index+1]::class === $models['image'])
                    </section>
                @endunless
                @break

            @default
        @endswitch

    @endforeach
</div>
</section>

@pushOnce('templates')
    <script id="modal-template" type="text/template">
        <x-modal/>
    </script>
@endPushOnce