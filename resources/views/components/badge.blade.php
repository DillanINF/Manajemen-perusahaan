@props([
    'type' => 'default', // success, error, warning, info, default
    'size' => 'md' // sm, md, lg
])

@php
    $typeClasses = [
        'success' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
        'error' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300',
        'warning' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300',
        'info' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300',
        'default' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
        'pending' => 'bg-orange-100 text-orange-800 dark:bg-orange-900/30 dark:text-orange-300',
        'accept' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
        'reject' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300',
    ];
    
    $sizeClasses = [
        'sm' => 'px-2 py-0.5 text-xs',
        'md' => 'px-2.5 py-1 text-sm',
        'lg' => 'px-3 py-1.5 text-base',
    ];
    
    $classes = ($typeClasses[$type] ?? $typeClasses['default']) . ' ' . ($sizeClasses[$size] ?? $sizeClasses['md']);
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center font-medium rounded-full {$classes}"]) }}>
    {{ $slot }}
</span>
