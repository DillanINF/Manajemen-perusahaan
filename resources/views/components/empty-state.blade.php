@props([
    'icon' => 'fas fa-inbox',
    'title' => 'Tidak ada data',
    'description' => 'Belum ada data yang tersedia saat ini.',
    'actionText' => null,
    'actionUrl' => null
])

<div class="text-center py-12">
    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 dark:bg-gray-800 mb-4">
        <i class="{{ $icon }} text-2xl text-gray-400 dark:text-gray-500"></i>
    </div>
    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">{{ $title }}</h3>
    <p class="text-sm text-gray-500 dark:text-gray-400 mb-6 max-w-sm mx-auto">{{ $description }}</p>
    
    @if($actionText && $actionUrl)
        <a href="{{ $actionUrl }}" 
           class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg shadow-sm transition-colors">
            <i class="fas fa-plus"></i>
            {{ $actionText }}
        </a>
    @endif
    
    {{ $slot }}
</div>
