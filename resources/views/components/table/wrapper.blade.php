@props([
    'responsive' => true,
    'minWidth' => '1000px'
])

<div class="@if($responsive) overflow-x-auto @endif rounded-lg border border-gray-200 dark:border-gray-700">
    <table {{ $attributes->merge(['class' => 'min-w-full divide-y divide-gray-200 dark:divide-gray-700']) }}
           @if($responsive) style="min-width: {{ $minWidth }}" @endif>
        {{ $slot }}
    </table>
</div>

@push('styles')
<style>
    /* Table scroll styling */
    .overflow-x-auto {
        scrollbar-width: thin;
        scrollbar-color: rgba(156, 163, 175, 0.5) transparent;
    }
    
    .overflow-x-auto::-webkit-scrollbar {
        height: 8px;
    }
    
    .overflow-x-auto::-webkit-scrollbar-track {
        background: transparent;
    }
    
    .overflow-x-auto::-webkit-scrollbar-thumb {
        background-color: rgba(156, 163, 175, 0.5);
        border-radius: 4px;
    }
    
    .overflow-x-auto::-webkit-scrollbar-thumb:hover {
        background-color: rgba(156, 163, 175, 0.7);
    }
</style>
@endpush
