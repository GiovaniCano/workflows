<div {{ $attributes->merge(['class' => 'modal']) }} style="display: none">
    <button class="modal-btn-close">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke-linecap="round" stroke-linejoin="round">
            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
            <path d="M18 6l-12 12" />
            <path d="M6 6l12 12" />
        </svg>
    </button>

    <div class="modal-content custom-scrollbar">
        {{ $slot }}
    </div>
</div>