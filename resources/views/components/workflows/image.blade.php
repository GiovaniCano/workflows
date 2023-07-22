@props(['image', 'imgId' => null])

<div {{ $attributes->merge(['class' => 'img']) }} @if($imgId) data-img-id="{{$imgId}}" @endif >
    {{-- <span class="debug">{{ $image->pivot->position }}</span> --}}
    <img src="{{ ($image->name && strpos($image->name, "http") !== 0) ? Storage::url($image->name) : $image->name }}" alt="Image" loading="lazy">
    {{-- the above is to provide support to faker url images --}}
</div>