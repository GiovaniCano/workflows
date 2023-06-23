@props(['section'])

<div {{ $attributes->merge(['class' => 'mini-section-btn-wrapper']) }}>
    <button type="button"
        class="mini-section-btn font-title" 
        id="{{$section->id . '-' .$section->make_slug()}}"
        data-id="{{ $section->id }}"
        data-minisection-btn-id="{{ $section->id }}"
        tabindex="-1"
    >
        {{ $section->name }}
    </button>
</div>