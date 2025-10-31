@props([
    'sortable' => false,
    'align' => 'left'
])

@php
    $alignClass = [
        'left' => 'text-left',
        'center' => 'text-center',
        'right' => 'text-right',
    ][$align];
@endphp

<th {{ $attributes->merge([
    'class' => "px-6 py-3 {$alignClass} text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider bg-gray-50 dark:bg-gray-800/50"
]) }}>
    @if($sortable)
        <button class="flex items-center gap-1 hover:text-gray-700 dark:hover:text-gray-100 transition-colors">
            {{ $slot }}
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/>
            </svg>
        </button>
    @else
        {{ $slot }}
    @endif
</th>
