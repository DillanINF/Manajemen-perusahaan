@props([
    'name',
    'title',
    'maxWidth' => 'md', // sm, md, lg, xl, 2xl
    'show' => false,
    'compact' => false // untuk modal compact seperti employee
])

@php
    $maxWidthClass = [
        'sm' => 'sm:max-w-sm',
        'md' => 'sm:max-w-md',
        'lg' => 'sm:max-w-lg',
        'xl' => 'sm:max-w-xl',
        '2xl' => 'sm:max-w-2xl',
    ][$maxWidth];
    
    // Untuk modal compact (28rem = 448px)
    if ($compact) {
        $maxWidthClass = 'modal-employee';
    }
@endphp

<div
    x-data="{ show: @js($show) }"
    x-on:open-modal-{{ $name }}.window="show = true"
    x-on:close-modal.window="show = false"
    x-on:close-modal-{{ $name }}.window="show = false"
    x-on:keydown.escape.window="show = false"
    x-show="show"
    class="fixed inset-0 overflow-y-auto px-4 py-6 sm:px-0 z-50"
    style="display: none;"
>
    <div x-show="show" class="fixed inset-0 transform transition-all" x-on:click="show = false" x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        <div class="absolute inset-0 bg-gray-900/75 dark:bg-black/80 backdrop-blur-sm"></div>
    </div>

    <div x-show="show" class="mb-6 bg-white dark:bg-slate-900 rounded-2xl overflow-hidden shadow-2xl transform transition-all sm:w-full sm:mx-auto {{ $maxWidthClass }}"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
         x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
         x-on:click.stop="">
        
        <!-- Modal Header -->
        <div class="sticky top-0 z-10 bg-white dark:bg-slate-900 border-b border-gray-200 dark:border-slate-700 px-6 py-4">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    {{ $title }}
                </h3>
                <button x-on:click="show = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Modal Content -->
        <div class="px-6 py-6">
            {{ $slot }}
        </div>
    </div>
</div>

@push('styles')
<style>
    /* Compact modal styling */
    .modal-employee {
        max-width: 28rem !important; /* 448px */
    }
    
    /* Dark mode untuk modal */
    html.dark .modal-employee { 
        background-color: #0f172a !important; 
        color: #e5e7eb !important; 
    }
    html.dark .modal-employee .sticky { 
        background-color: #0f172a !important; 
        border-color: rgba(255,255,255,0.1) !important; 
    }
    html.dark .modal-employee label { 
        color: #e5e7eb !important; 
    }
    html.dark .modal-employee input,
    html.dark .modal-employee textarea,
    html.dark .modal-employee select { 
        background-color: #1f2937 !important; 
        color: #e5e7eb !important; 
        border-color: #374151 !important; 
    }
    html.dark .modal-employee input::placeholder,
    html.dark .modal-employee textarea::placeholder { 
        color: #9ca3af !important; 
    }
</style>
@endpush
