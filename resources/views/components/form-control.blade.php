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
    @error($name, $bag)
        <span class="error-feedback">{{$message}}</span>
    @enderror

@else

    <input
        type="{{ $type }}" 
        name="{{ $name }}" 
        placeholder="{{ __($placeholder) }}"
        value="{{ $type === 'password' ? '' : old($name, $value) }}" 
        {{ $attributes->class(['form-control', 'is-invalid' => $errors->has($name)]) }} 
    >
    @error($name, $bag)
        <span class="error-feedback">{{$message}}</span>
    @enderror

@endif