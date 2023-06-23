@props(['wysiwyg'])

{{-- display none to show it only when the wysiwyg is ready, show it with javascript --}}
<section {{ $attributes->merge(['class' => 'wysiwyg-content js-action-target']) }} style="display:none">
    <div class="wysiwyg-content-buttons"></div>
    {{-- <span class="debug">{{ $item->pivot->position }}</span> --}}
    <div class="editor">
        {!! $wysiwyg->content !!}
    </div>
</section>