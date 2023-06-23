@props(['image'])

<div {{ $attributes->merge(['class' => 'img']) }}>
    {{-- <span class="debug">{{ $image->pivot->position }}</span> --}}
    <img src="{{ $image->url }}" alt="Image">
</div>