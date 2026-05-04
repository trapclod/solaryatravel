@props([
    'type' => 'text',
    'name',
    'label' => null,
    'placeholder' => '',
    'value' => '',
    'required' => false,
    'disabled' => false,
    'error' => null,
    'hint' => null,
])

@php
    $errMessage = $error ?? $errors->first($name);
@endphp

<div {{ $attributes->only('class')->merge(['class' => 'mb-3']) }}>
    @if($label)
        <label for="{{ $name }}" class="form-label fw-medium">
            {{ $label }}
            @if($required)<span class="text-danger">*</span>@endif
        </label>
    @endif

    <input
        type="{{ $type }}"
        name="{{ $name }}"
        id="{{ $name }}"
        value="{{ old($name, $value) }}"
        placeholder="{{ $placeholder }}"
        {{ $required ? 'required' : '' }}
        {{ $disabled ? 'disabled' : '' }}
        {{ $attributes->except('class')->merge([
            'class' => 'form-control' . ($errMessage ? ' is-invalid' : '')
        ]) }}
    >

    @if($hint && !$errMessage)
        <div class="form-text">{{ $hint }}</div>
    @endif

    @if($errMessage)
        <div class="invalid-feedback d-block">{{ $errMessage }}</div>
    @endif
</div>
