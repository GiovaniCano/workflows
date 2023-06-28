<section
    {{ $attributes->merge([
        'class' => 'grey-container'
    ]) }}
    data-id="{{ $section->id }}"
>
<header class="section-header">
    <x-form-control
        data-id="{{ $section->id }}"
        data-mini="{{ $type === 2 ? true : false }}"
        id="{{ $type === 2 ? '' : $section->id . '-' .$section->make_slug() }}"
        class="m-0 h2"
        type="text"
        maxlength="25"
        :placeholder="__('workflows.section-placeholder')"
        :value="$section->name"
        name=""
        required
    />
</header>

<div>
    <div class="section-top-button-hook"></div>

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
                        <x-workflows.section-form :section="$item" class="section-nested section-form js-action-target" />
                        @break

                    {{-- SECTION MINI --}}
                    @case(2)
                        @unless (
                            isset($section_items[$loop->index-1]) 
                            && $section_items[$loop->index-1]::class === $models['section'] 
                            && $section_items[$loop->index-1]->type == 2
                        )
                            <section class="container-sections container-form m-b-1">
                        @endunless
                                <x-workflows.mini-section-btn :section="$item" class="js-action-target" />
                                @push('mini-sections') 
                                    {{-- mini-sections stack is inside workflows.form --}}
                                    <x-modal data-minisection-modal-id="{{ $item->id }}" class="modal-mini-section js-action-target">
                                        <x-workflows.section-form :section="$item" class="section-mini section-form" />
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
                <x-workflows.wysiwyg-form :wysiwyg="$item" />
                @break

            {{-- IMAGE --}}
            @case($models['image'])
                @unless (isset($section_items[$loop->index-1]) && $section_items[$loop->index-1]::class === $models['image'])
                    <section class="container-images container-form m-b-1">
                @endunless

                        <x-workflows.image :image="$item" class="js-action-target" />

                @unless (isset($section_items[$loop->index+1]) && $section_items[$loop->index+1]::class === $models['image'])
                    </section>
                @endunless
                @break

            @default
        @endswitch

    @endforeach
</div>
</section>