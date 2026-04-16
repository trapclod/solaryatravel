@props([
    'striped' => false,
    'hover' => true,
])

<tr {{ $attributes->merge(['class' => ($hover ? 'hover:bg-gray-50 transition-colors' : '') . ' ' . ($striped ? 'even:bg-gray-50' : '')]) }}>
    {{ $slot }}
</tr>
