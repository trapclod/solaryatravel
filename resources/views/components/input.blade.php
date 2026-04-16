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

<div {{ $attributes->only('class')->merge(['class' => 'space-y-1']) }}>
    @if($label)
        <label for="{{ $name }}" class="block text-sm font-medium text-gray-700">
            {{ $label }}
            @if($required)
                <span class="text-red-500">*</span>
            @endif
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
            'class' => 'w-full px-4 py-3 border rounded-xl shadow-sm transition-colors focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent ' . 
                      ($error ?? $errors->first($name) ? 'border-red-300 text-red-900 placeholder-red-300' : 'border-gray-300 text-gray-900 placeholder-gray-400') .
                      ($disabled ? ' bg-gray-100 cursor-not-allowed' : ' bg-white')
        ]) }}
    >
    
    @if($hint && !($error ?? $errors->first($name)))
        <p class="text-sm text-gray-500">{{ $hint }}</p>
    @endif
    
    @if($error ?? $errors->first($name))
        <p class="text-sm text-red-600">{{ $error ?? $errors->first($name) }}</p>
    @endif
</div>
