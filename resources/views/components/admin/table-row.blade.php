@props([
    'striped' => false,
    'hover' => true,
])

<tr {{ $attributes }}>
    {{ $slot }}
</tr>
