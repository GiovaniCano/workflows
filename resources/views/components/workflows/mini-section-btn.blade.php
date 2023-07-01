@props(['section'])

<div {{ $attributes->merge(['class' => 'mini-section-btn-wrapper js-sidebar-highlight-container']) }}>
    <button type="button"
        class="mini-section-btn font-title js-sidebar-highlight-target" 
        id="{{$section->id . '-' .$section->make_slug()}}"
        data-id="{{ $section->id }}"
        data-minisection-btn-id="{{ $section->id }}"
        tabindex="-1"
    >
        {{ $section->name }}
    </button>
</div>