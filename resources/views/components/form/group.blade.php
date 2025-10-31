@props([
    'label' => null,
    'for' => null,
    'required' => false,
    'error' => null,
    'hint' => null
])

<div {{ $attributes->merge(['class' => 'space-y-2']) }}>
    @if($label)
        <label @if($for) for="{{ $for }}" @endif 
               class="block text-sm font-medium text-gray-700 dark:text-gray-300">
            {{ $label }}
            @if($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif
    
    {{ $slot }}
    
    @if($hint && !$error)
        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $hint }}</p>
    @endif
    
    @if($error)
        <p class="text-sm text-red-600 dark:text-red-400">{{ $error }}</p>
    @endif
</div>
