<!-- Mobile Overlay -->
<div id="sidebar-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 lg:hidden hidden"></div>

<!-- Sidebar -->
<div id="sidebar" class="fixed inset-y-0 left-0 z-50 w-64 bg-gradient-to-b from-blue-600 to-blue-800 text-white transform -translate-x-full transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static lg:inset-0">
    <!-- Header -->
    <div class="flex items-center justify-between px-6 py-5 border-b border-blue-500/30">
        <div class="flex items-center">
            <div class="w-8 h-8 bg-white/10 rounded-lg flex items-center justify-center mr-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
            </div>
            <h1 class="text-xl font-bold">Manajemen</h1>
        </div>
        <!-- Close button for mobile -->
        <button id="sidebar-close" class="lg:hidden p-1 rounded-md hover:bg-white/10 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>

    <!-- Menu -->
    <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto">
        @php
            // Hapus link 'Input PO' dari sidebar. Form Input PO hanya bisa diakses via double click Data Invoice.
            $menus = [
                ['name' => 'Dashboard', 'icon' => 'home', 'route' => 'dashboard'],
                // ['name' => 'Input PO', 'icon' => 'file-plus', 'route' => 'po.create'], // disabled
                ['name' => 'Data PO', 'icon' => 'truck', 'route' => 'suratjalan.index'],
                ['name' => 'Customer', 'icon' => 'users', 'route' => 'customer.index'],
                ['name' => 'Produk', 'icon' => 'package', 'route' => 'produk.index', 'subroutes' => [
                    'barang.masuk.index','barang.masuk.create','barang.masuk.edit','barang.masuk.update','barang.masuk.store',
                    'barang.keluar.index','barang.keluar.create','barang.keluar.edit','barang.keluar.update','barang.keluar.store'
                ]],
                ['name' => 'Kendaraan', 'icon' => 'truck', 'route' => 'kendaraan.index'],
            ];
            
            // Purchase Order dropdown menu
            $purchaseOrderMenus = [
                ['name' => 'Data Invoice', 'route' => 'invoice.index'],
                ['name' => 'Sisa Data Purchase Order', 'route' => 'sisa-data-po.index'],
            ];
            
            $currentRoute = Route::currentRouteName();
            $isPOActive = in_array($currentRoute, ['invoice.index', 'sisa-data-po.index', 'po.index', 'po.create', 'po.edit']);
        @endphp

        @foreach ($menus as $menu)
            @php
                $isActive = $currentRoute === $menu['route'] || 
                           (isset($menu['subroutes']) && in_array($currentRoute, $menu['subroutes']));
            @endphp
            <a href="{{ route($menu['route']) }}"
               class="group flex items-center px-4 py-3 rounded-xl transition-all duration-200 {{ $isActive ? 'bg-white/20 text-white shadow-lg' : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
                <div class="w-5 h-5 mr-4 flex-shrink-0">
                    @if($menu['icon'] === 'home')
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                    @elseif($menu['icon'] === 'file-plus')
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    @elseif($menu['icon'] === 'truck')
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                    @elseif($menu['icon'] === 'users')
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                        </svg>
                    @elseif($menu['icon'] === 'package')
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                    @endif
                </div>
                <span class="font-medium text-sm">{{ $menu['name'] }}</span>
                @if($isActive)
                    <div class="ml-auto w-2 h-2 bg-white rounded-full"></div>
                @endif
            </a>
        @endforeach
        
        <!-- Purchase Order Dropdown -->
        <div x-data="{ open: {{ $isPOActive ? 'true' : 'false' }} }" class="space-y-1">
            <button @click="open = !open" 
                    class="group w-full flex items-center px-4 py-3 rounded-xl transition-all duration-200 {{ $isPOActive ? 'bg-white/20 text-white shadow-lg' : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
                <div class="w-5 h-5 mr-4 flex-shrink-0">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <span class="font-medium text-sm flex-1 text-left">Purchase Order</span>
                <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                </svg>
                @if($isPOActive)
                    <div class="w-2 h-2 bg-white rounded-full ml-2"></div>
                @endif
            </button>
            
            <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 transform scale-100" x-transition:leave-end="opacity-0 transform scale-95" class="ml-6 space-y-1">
                @foreach($purchaseOrderMenus as $poMenu)
                    @php
                        $isSubActive = $currentRoute === $poMenu['route'];
                    @endphp
                    <a href="{{ route($poMenu['route']) }}"
                       class="group flex items-center px-4 py-2 rounded-lg transition-all duration-200 {{ $isSubActive ? 'bg-white/15 text-white' : 'text-white/70 hover:bg-white/10 hover:text-white' }}">
                        <div class="w-2 h-2 rounded-full mr-3 {{ $isSubActive ? 'bg-white' : 'bg-white/30' }}"></div>
                        <span class="text-sm">{{ $poMenu['name'] }}</span>
                    </a>
                @endforeach
            </div>
        </div>
    </nav>

    <!-- Footer -->
    <div class="px-6 py-4 border-t border-blue-500/30">
        <div class="flex items-center space-x-3">
            <div class="w-8 h-8 rounded-full overflow-hidden border border-white/20 flex items-center justify-center bg-white/10">
                @if(Auth::user() && Auth::user()->profile_photo)
                    <img src="{{ asset('storage/' . Auth::user()->profile_photo) }}" alt="{{ Auth::user()->name }}" class="w-full h-full object-cover">
                @else
                    <svg class="w-4 h-4 text-white/80" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                @endif
            </div>
            <div class="flex-1 min-w-0">
                <div class="text-sm font-medium text-white truncate">{{ Auth::user()->name ?? 'User' }}</div>
                <div class="text-xs text-white/70 truncate">{{ Auth::user()->email ?? 'user@example.com' }}</div>
            </div>
        </div>
    </div>
</div>

<!-- Mobile Menu Button -->
<button id="sidebar-toggle" class="fixed top-4 left-4 z-50 lg:hidden bg-blue-600 text-white p-2 rounded-lg shadow-lg hover:bg-blue-700 transition-colors">
    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
    </svg>
</button>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebar-overlay');
    const toggleBtn = document.getElementById('sidebar-toggle');
    const closeBtn = document.getElementById('sidebar-close');

    function openSidebar() {
        sidebar.classList.remove('-translate-x-full');
        overlay.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }

    function closeSidebar() {
        sidebar.classList.add('-translate-x-full');
        overlay.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }

    toggleBtn?.addEventListener('click', openSidebar);
    closeBtn?.addEventListener('click', closeSidebar);
    overlay?.addEventListener('click', closeSidebar);

    // Close sidebar on escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && !sidebar.classList.contains('-translate-x-full')) {
            closeSidebar();
        }
    });

    // Handle window resize
    window.addEventListener('resize', function() {
        if (window.innerWidth >= 1024) {
            closeSidebar();
        }
    });
});
</script>
