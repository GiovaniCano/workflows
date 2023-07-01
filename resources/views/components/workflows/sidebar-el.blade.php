@props(['section', 'maxlen'])

<a 
    href="#{{ $section->id . '-' . $section->make_slug() }}"
    title="{{ $section->name }}"
    id="sidebar-section-link-{{ $section->id }}"
    data-id="{{ $section->id }}"
    data-maxlen="{{ $maxlen }}"
    {{ $attributes->merge(['class' => 'sidebar-section']) }}
>
    # {{ Str::limit($section->name, $maxlen) }}
</a>