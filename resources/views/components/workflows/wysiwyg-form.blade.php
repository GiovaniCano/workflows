@props(['wysiwyg', 'editorId' => ''])

{{-- display none to show it only when the wysiwyg is ready, show it with javascript --}}
<section {{ $attributes->merge(['class' => 'wysiwyg-content js-action-target']) }} 
    style="display:none"
    data-editor-id="{{ $editorId }}"
>
    <div class="wysiwyg-content-buttons">
        <span class="wysiwyg-char-counter"><span></span> / <span></span></span>
    </div>
    {{-- <span class="debug">{{ $item->pivot->position }}</span> --}}
    <div class="editor">
        {!! $wysiwyg->content !!}
    </div>
</section>