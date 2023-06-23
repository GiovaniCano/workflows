@props(['section', 'maxlen'])

<a 
    href="#{{ $section->id . '-' . $section->make_slug() }}"
    title="{{ $section->name }}"
    id="sidebar-section-link-{{ $section->id }}"
    data-id="{{ $section->id }}"
    class="sidebar-section"
    data-maxlen="{{ $maxlen }}"
>
    # {{ Str::limit($section->name, $maxlen) }}
</a>