@props([
    'label' => null,
    'type' => 'text',
    'name',
    'id' => null,
    'placeholder' => null,
    'required' => false,
    'disabled' => false,
    'error' => null,
    'hint' => null,
    'prefix' => null,
    'suffix' => null,
])

@php
    $inputId = $id ?? $name;
    $hasError = $error || $errors->has($name);
@endphp

<div {{ $attributes->only(['class'])->merge(['class' => 'mb-3']) }}>
    @if($label)
        <label for="{{ $inputId }}" class="form-label fw-medium small">
            {{ $label }}
            @if($required)<span class="text-danger">*</span>@endif
        </label>
    @endif

    @if($prefix || $suffix)
        <div class="input-group">
            @if($prefix)<span class="input-group-text">{{ $prefix }}</span>@endif
            <input
                type="{{ $type }}"
                name="{{ $name }}"
                id="{{ $inputId }}"
                placeholder="{{ $placeholder }}"
                {{ $required ? 'required' : '' }}
                {{ $disabled ? 'disabled' : '' }}
                {{ $attributes->except(['class']) }}
                class="form-control{{ $hasError ? ' is-invalid' : '' }}"
            >
            @if($suffix)<span class="input-group-text">{{ $suffix }}</span>@endif
        </div>
    @else
        <input
            type="{{ $type }}"
            name="{{ $name }}"
            id="{{ $inputId }}"
            placeholder="{{ $placeholder }}"
            {{ $required ? 'required' : '' }}
            {{ $disabled ? 'disabled' : '' }}
            {{ $attributes->except(['class']) }}
            class="form-control{{ $hasError ? ' is-invalid' : '' }}"
        >
    @endif

    @if($hasError)
        <div class="invalid-feedback d-block d-flex align-items-center gap-1">
            <i class="bi bi-exclamation-circle"></i>{{ $error ?? $errors->first($name) }}
        </div>
    @elseif($hint)
        <div class="form-text">{{ $hint }}</div>
    @endif
</div>
