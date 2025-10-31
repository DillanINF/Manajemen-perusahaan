@props([
    'title' => null,
    'padding' => true,
    'shadow' => true
])

@php
    $classes = 'bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700';
    if ($shadow) $classes .= ' shadow-sm';
    if ($padding) $classes .= ' p-6';
@endphp

<div {{ $attributes->merge(['class' => $classes]) }}>
    @if($title)
        <div class="mb-4 pb-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $title }}</h3>
        </div>
    @endif
    
    {{ $slot }}
</div>
