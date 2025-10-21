@props([
    // JS expression to run for Edit, e.g. "editSalary({...})"
    'onEdit' => null,
    // Optional payload (array/object) that will be attached to the edit button as data-edit JSON
    'editPayload' => null,
    // Delete form action URL, e.g. route('salary.destroy', $salary)
    'deleteAction' => null,
    // Confirmation text for delete
    'confirmText' => 'Yakin ingin menghapus data ini? ',
    // If true, show 3-dots menu on mobile instead of horizontal big buttons
    'useMenu' => false,
    // If true, always show full Edit/Delete buttons on all screens
    'forceFull' => false,
])

<div class="w-full" x-data="{ open: false }">
    <!-- Desktop: compact icon-only circular buttons with tooltip -->
    @unless($forceFull)
    <div class="hidden sm:flex items-center justify-center gap-1.5">
        @if($onEdit)
            <!-- Edit button -->
            <button type="button"
                    @if($onEdit) onclick="event.preventDefault(); event.stopPropagation(); {!! $onEdit !!};" @endif
                    @if($onEdit) data-editcall='{!! $onEdit !!}' @endif
                    @if($editPayload) data-edit='@json($editPayload, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)' @endif
                    class="js-edit-btn group relative z-10 pointer-events-auto cursor-pointer inline-flex items-center justify-center w-9 h-9 aspect-square min-w-[36px] min-h-[36px] rounded-full bg-[#2563EB] text-white shadow-sm hover:shadow-md transition-all duration-200 hover:bg-[#1D4ED8] focus:outline-none focus:ring-2 focus:ring-blue-300"
                    aria-label="Edit">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-3.5 h-3.5">
                    <path d="M12 20h9"/>
                    <path d="M16.5 3.5a2.121 2.121 0 1 1 3 3L7 19l-4 1 1-4 12.5-12.5z"/>
                </svg>
                <span class="pointer-events-none absolute -top-8 left-1/2 -translate-x-1/2 whitespace-nowrap text-[11px] px-2 py-1 rounded bg-gray-900 text-white/90 opacity-0 group-hover:opacity-100 shadow transition-opacity">Edit</span>
            </button>
        @endif

        @if($deleteAction)
            <!-- Delete button -->
            <form method="POST" action="{{ $deleteAction }}" class="inline-flex" onsubmit="return confirm('{{ $confirmText }}')">
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="group relative inline-flex items-center justify-center w-9 h-9 aspect-square min-w-[36px] min-h-[36px] rounded-full bg-[#DC2626] text-white shadow-sm hover:shadow-md transition-all duration-200 hover:bg-[#B91C1C] focus:outline-none focus:ring-2 focus:ring-red-300">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-3.5 h-3.5">
                        <polyline points="3 6 5 6 21 6"/>
                        <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                        <path d="M10 11v6"/>
                        <path d="M14 11v6"/>
                        <path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/>
                    </svg>
                    <span class="pointer-events-none absolute -top-8 left-1/2 -translate-x-1/2 whitespace-nowrap text-[11px] px-2 py-1 rounded bg-gray-900 text-white/90 opacity-0 group-hover:opacity-100 shadow transition-opacity">Hapus</span>
                </button>
            </form>
        @endif
    </div>
    @endunless

    <!-- Mobile: either big horizontal buttons or 3-dots menu -->
    @if($forceFull || !$useMenu)
        <div class="flex items-stretch justify-center gap-2 @unless($forceFull) sm:hidden @endunless">
            @if($onEdit)
                <button type="button"
                        @if($onEdit) onclick="event.preventDefault(); event.stopPropagation(); {!! $onEdit !!};" @endif
                        @if($onEdit) data-editcall='{!! $onEdit !!}' @endif
                        @if($editPayload) data-edit='@json($editPayload, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)' @endif
                        class="js-edit-btn relative z-10 pointer-events-auto cursor-pointer flex-1 inline-flex items-center justify-center gap-2 px-3 py-2 rounded-lg bg-[#2563EB] text-white shadow-sm hover:shadow-md transition-all duration-200 active:scale-[.99]"
                        aria-label="Edit">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4">
                        <path d="M12 20h9"/>
                        <path d="M16.5 3.5a2.121 2.121 0 1 1 3 3L7 19l-4 1 1-4 12.5-12.5z"/>
                    </svg>
                    <span class="text-sm font-medium">Edit</span>
                </button>
            @endif
            @if($deleteAction)
                <form method="POST" action="{{ $deleteAction }}" class="flex-1" onsubmit="return confirm('{{ $confirmText }}')">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="w-full inline-flex items-center justify-center gap-2 px-3 py-2 rounded-lg bg-[#DC2626] text-white shadow-sm hover:shadow-md transition-all duration-200 active:scale-[.99]">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4">
                            <polyline points="3 6 5 6 21 6"/>
                            <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                            <path d="M10 11v6"/>
                            <path d="M14 11v6"/>
                            <path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/>
                        </svg>
                        <span class="text-sm font-medium">Hapus</span>
                    </button>
                </form>
            @endif
        </div>
    @elseif(!$forceFull)
        <!-- 3-dots menu variant for mobile: use fixed overlay (action sheet) to avoid clipping -->
        <div class="sm:hidden flex justify-center relative">
            <button type="button" @click="open = true" class="inline-flex items-center justify-center w-9 h-9 rounded-full bg-white border border-gray-200 text-gray-700 shadow-sm hover:shadow-md transition-all z-10">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM18 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
            </button>
            <!-- Overlay -->
            <div x-show="open" x-transition.opacity class="fixed inset-0 z-40 bg-black/40" @click="open=false"></div>
            <!-- Action sheet -->
            <div x-show="open" x-transition
                 class="fixed z-50 bottom-4 left-1/2 -translate-x-1/2 w-[92vw] max-w-xs rounded-2xl bg-white border border-gray-200 shadow-2xl overflow-hidden">
                <div class="py-1">
                    @if($onEdit)
                        <button type="button" @click='open=false'
                                @if($onEdit) onclick="event.preventDefault(); event.stopPropagation(); {!! $onEdit !!};" @endif
                                @if($editPayload) data-edit='@json($editPayload, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)' @endif
                                class="w-full text-left px-4 py-3 hover:bg-gray-50 text-gray-700 flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4 text-[#2563EB]">
                                <path d="M12 20h9"/>
                                <path d="M16.5 3.5a2.121 2.121 0 1 1 3 3L7 19l-4 1 1-4 12.5-12.5z"/>
                            </svg>
                            Edit
                        </button>
                    @endif
                    @if($deleteAction)
                        <form method="POST" action="{{ $deleteAction }}" onsubmit="return confirm('{{ $confirmText }}')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full text-left px-4 py-3 hover:bg-gray-50 text-gray-700 flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4 text-[#DC2626]">
                                    <polyline points="3 6 5 6 21 6"/>
                                    <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                                    <path d="M10 11v6"/>
                                    <path d="M14 11v6"/>
                                    <path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/>
                                </svg>
                                Hapus
                            </button>
                        </form>
                    @endif
                </div>
                <div class="border-t border-gray-200">
                    <button type="button" @click="open=false" class="w-full text-center px-4 py-3 text-gray-600 hover:bg-gray-50">Tutup</button>
                </div>
            </div>
        </div>
    @endif
</div>
