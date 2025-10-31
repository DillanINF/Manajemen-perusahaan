@props([
    'label',
    'value',
    'icon',
    'iconColor' => 'text-blue-600 dark:text-blue-400'
])

<div class="bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-xl p-6 shadow-sm">
    <div class="flex items-center">
        <div class="bg-gray-100 dark:bg-slate-700 p-3 rounded-xl mr-4">
            {!! $icon !!}
        </div>
        <div>
            <p class="text-sm font-medium text-gray-600 mb-1 dark:text-gray-300">{{ $label }}</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-slate-100">{{ $value }}</p>
        </div>
    </div>
</div>
