@props([
    'title',
    'subtitle' => null,
    'icon' => 'fas fa-file',
    'iconBg' => 'from-indigo-500 to-purple-600'
])

<div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-xl border border-white/20 dark:border-gray-700/50 rounded-2xl shadow-xl p-6 mb-8">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
        <div class="space-y-2">
            <div class="flex items-center gap-3">
                <div class="p-3 bg-gradient-to-r {{ $iconBg }} rounded-xl shadow-lg">
                    <i class="{{ $icon }} text-white text-xl"></i>
                </div>
                <div>
                    <h1 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white">
                        {{ $title }}
                    </h1>
                    @if($subtitle)
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $subtitle }}</p>
                    @endif
                </div>
            </div>
        </div>
        
        @isset($actions)
            <div class="flex flex-wrap items-center gap-3">
                {{ $actions }}
            </div>
        @endisset
    </div>
</div>
