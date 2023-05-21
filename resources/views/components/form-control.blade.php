@if ($type === 'checkbox')
    
    <div class="form-checkbox m-t-2">
        <input class="@error('terms') is-invalid @enderror" 
            type="{{ $type }}" 
            name="{{ $name }}" 
            id="{{ $name }}"
            {{ $attributes }}
        >
        <label for="{{ $name }}">
            {{ $slot }}
        </label>
    </div>
    @error($name)
        <span class="error-feedback">{{$message}}</span>
    @enderror

@else

    <input class="form-control m-t-2 @error($name) is-invalid @enderror" 
        type="{{ $type }}" 
        name="{{ $name }}" 
        placeholder="{{ __($placeholder) }}"
        value="{{ $type === 'password' ? '' : old($name, $value) }}" 
        {{ $attributes }} 
    >
    @error($name)
        <span class="error-feedback">{{$message}}</span>
    @enderror

@endif