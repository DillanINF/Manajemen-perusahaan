@props([
    'align' => 'left'
])

@php
    $alignClass = [
        'left' => 'text-left',
        'center' => 'text-center',
        'right' => 'text-right',
    ][$align];
@endphp

<td {{ $attributes->merge([
    'class' => "px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100 {$alignClass}"
]) }}>
    {{ $slot }}
</td>
