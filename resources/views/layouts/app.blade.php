<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('image/LOGO.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('image/LOGO.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('image/LOGO.png') }}">
    <script>
        (function(){
            try {
                const d = document.documentElement;
                // Matikan transisi/animasi saat inisialisasi tema untuk mencegah lag/flash
                d.classList.add('theme-switching');
                const pref = localStorage.getItem('theme');
                if (pref === 'dark' || (!pref && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                    d.classList.add('dark');
                } else {
                    d.classList.remove('dark');
                }
                // Lepas kelas setelah frame berikutnya
                requestAnimationFrame(() => d.classList.remove('theme-switching'));
            } catch(e) {}
        })();
    </script>
    <title>@yield('title', 'Dashboard')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.5.2/css/all.min.css" referrerpolicy="no-referrer" />
    <!-- Google Fonts: Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    
    <!-- CSS untuk Auto-Expand Forms ketika Sidebar Hidden -->
    <style>
        /* Transisi smooth untuk semua elemen form */
        .form-container,
        .table-container,
        .card-container,
        .content-wrapper,
        .rounded-xl,
        .shadow-lg,
        .border,
        .overflow-x-auto,
        .salary-table-container,
        table {
            /* Hanya transisi efek non-warna agar tidak menahan repaint saat ganti tema */
            transition-property: box-shadow, transform;
            transition-duration: 0.2s;
            transition-timing-function: ease-in-out;
        }

        /* Matikan transisi SEMENTARA saat ganti tema agar navbar tidak terasa telat */
        .no-theme-transition, .no-theme-transition * {
            transition: none !important;
        }
        /* Mode global: nonaktifkan SEMUA transition & animation saat switching tema */
        .theme-switching, .theme-switching *, .theme-switching *::before, .theme-switching *::after {
            transition: none !important;
            animation: none !important;
        }
        /* Navbar instant switch (hindari lag warna/blur) */
        header.theme-instant, header.theme-instant * {
            transition-property: none !important;
        }
        
        /* Ketika sidebar tersembunyi, perlebar konten */
        @media (min-width: 768px) {
            /* Ketika sidebar hidden, perlebar semua container form */
            body.sidebar-hidden main {
                padding-left: 3rem !important;
                padding-right: 3rem !important;
                max-width: none !important;
            }
            
            /* Perlebar HANYA area tabel salary ketika sidebar disembunyikan */
            body.sidebar-hidden #manualSalaryTable .bg-white,
            body.sidebar-hidden #manualSalaryTable .bg-gray-50,
            body.sidebar-hidden #manualSalaryTable .rounded-xl,
            body.sidebar-hidden #manualSalaryTable .shadow-lg,
            body.sidebar-hidden #manualSalaryTable .border {
                max-width: none !important;
                width: 100% !important;
            }
            
            /* Khusus untuk tabel salary */
            body.sidebar-hidden #manualSalaryTable,
            body.sidebar-hidden #manualSalaryTable .salary-table-container,
            body.sidebar-hidden #manualSalaryTable .overflow-x-auto,
            body.sidebar-hidden #manualSalaryTable table {
                min-width: 100% !important;
                width: 100% !important;
            }
            
            /* Perlebar grid layouts */
            body.sidebar-hidden .grid {
                gap: 2rem !important;
            }
            
            /* Perlebar input fields dan form elements */
            body.sidebar-hidden input,
            body.sidebar-hidden select,
            body.sidebar-hidden textarea {
                padding-left: 1rem !important;
                padding-right: 1rem !important;
            }
            
            /* Khusus untuk tabel salary - perlebar kolom tanggal */
            body.sidebar-hidden .salary-table {
                min-width: 100% !important;
                width: 100% !important;
            }
            
            body.sidebar-hidden .date-column {
                min-width: 40px !important;
                width: 40px !important;
            }
            
            /* Perlebar modal dan popup */
            body.sidebar-hidden .modal-content,
            body.sidebar-hidden .dropdown-content {
                max-width: 90vw !important;
            }
        }
        
        /* Alternatif menggunakan CSS custom property untuk kontrol yang lebih baik */
        :root {
            --sidebar-width: 16rem; /* 64 dalam rem */
            --content-padding: 2.5rem; /* 10 dalam rem */
        }
        
        /* Ketika sidebar tersembunyi */
        .sidebar-hidden {
            --sidebar-width: 0rem;
            --content-padding: 3rem; /* Tambah padding ketika sidebar hidden */
        }

        /* Tinggikan sedikit sel BON */
        .bon-box table tr { height: 22px !important; }
        .bon-box table td { height: 22px !important; line-height: 22px !important; }
        .bon-box table input { height: 22px !important; line-height: 22px !important; }

        /* Warna kolom BON: kolom panah (2) dan kolom kanan (3) */
        .bon-box table tr td:nth-child(2) { /* kolom panah */
            background-color: #FFFF00 !important;
        }
        .bon-box table tr td:nth-child(3) { /* kolom kanan */
            background-color: #D9D9D9 !important;
        }
        /* Hapus semua batasan max-width dari utilitas Tailwind (max-w-*) di seluruh halaman */
        [class*="max-w-"] { max-width: none !important; }
    </style>
    <script>
        // Utilitas untuk menerapkan tema TANPA transisi visual (instan)
        (function(){
            function setTheme(theme){
                try {
                    const d = document.documentElement;
                    // Matikan transisi & animasi di seluruh dokumen saat switch
                    d.classList.add('theme-switching');
                    d.classList.add('no-theme-transition');
                    if (theme === 'dark') d.classList.add('dark'); else d.classList.remove('dark');
                    localStorage.setItem('theme', theme);
                    // Lepas penonaktifan transisi di frame berikutnya
                    requestAnimationFrame(() => { 
                        d.classList.remove('no-theme-transition');
                        d.classList.remove('theme-switching');
                    });
                } catch(e) {}
            }
            // Ekspor ke window agar bisa dipakai tombol/fitur pengaturan
            window.setTheme = setTheme;

            // Sinkronkan jika ada perubahan dari tab lain atau dari kode lain yang menulis localStorage('theme')
            window.addEventListener('storage', (e) => {
                if (e.key === 'theme') {
                    setTheme(e.newValue === 'dark' ? 'dark' : 'light');
                }
            });

            // Listener opsional: jika ada elemen dengan data-toggle-theme
            document.addEventListener('click', (ev) => {
                const btn = ev.target.closest('[data-toggle-theme]');
                if (!btn) return;
                const current = (localStorage.getItem('theme') || (document.documentElement.classList.contains('dark') ? 'dark' : 'light'));
                setTheme(current === 'dark' ? 'light' : 'dark');
            });

            // Fallback global: jika ada skrip lain yang menambah/menghapus class 'dark',
            // nonaktifkan transisi sesaat agar perubahan tetap instan di seluruh halaman
            try {
                const d = document.documentElement;
                let ticking = false;
                const obs = new MutationObserver(() => {
                    if (ticking) return;
                    ticking = true;
                    d.classList.add('theme-switching');
                    requestAnimationFrame(() => {
                        d.classList.remove('theme-switching');
                        ticking = false;
                    });
                });
                obs.observe(d, { attributes: true, attributeFilter: ['class'] });
            } catch(e) {}
        })();
    </script>
    
    @stack('styles')
</head>
<body x-data="{ mobileSidebarOpen: false, desktopSidebarOpen: true }" x-init="
    (() => {
      const mq = window.matchMedia('(min-width: 768px)');
      // Initialize state based on current viewport (accounts for browser zoom as well)
      desktopSidebarOpen = mq.matches;
      if (mq.matches) { mobileSidebarOpen = false; }
      const onChange = e => {
        desktopSidebarOpen = e.matches;
        // When switching to desktop, ensure mobile sidebar is closed to avoid overlap
        if (e.matches) { mobileSidebarOpen = false; }
      };
      if (mq.addEventListener) { mq.addEventListener('change', onChange); } else { mq.addListener(onChange); }
      
      // Watch for sidebar state changes and update body class
      $watch('desktopSidebarOpen', (value) => {
        document.body.classList.toggle('sidebar-hidden', !value);
        // Trigger resize event untuk chart dan komponen lain
        setTimeout(() => {
          window.dispatchEvent(new Event('resize'));
          window.dispatchEvent(new Event('forcechartresize'));
        }, 300);
      });
    })();
  " class="flex font-sans bg-white text-gray-800 dark:bg-gray-900 dark:text-gray-100 overflow-x-hidden @yield('body-classes')"
  :class="{ 'sidebar-hidden': !desktopSidebarOpen }">

      <aside id="sidebar" class="w-64 bg-white dark:bg-gray-900 border-r border-gray-200 dark:border-gray-800 min-h-screen flex flex-col transform transition-transform duration-300 fixed inset-y-0 left-0 z-40 -translate-x-full md:fixed md:inset-y-0 md:left-0 overflow-hidden"
           x-bind:class="{
                '-translate-x-full': !mobileSidebarOpen,
                'translate-x-0': mobileSidebarOpen,
                'md:translate-x-0': desktopSidebarOpen,
                'md:-translate-x-full': !desktopSidebarOpen
           }">
        <div class="h-16 border-b border-gray-200 dark:border-gray-800 flex items-center justify-between px-4">
            <img src="{{ asset('image/cam.png') }}" alt="Logo Perusahaan" class="h-10 ml-2 md:ml-3">
            
            <button type="button"
                    class="inline-flex items-center justify-center h-10 w-10 rounded-md text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 ml-auto"
                    @click="if (window.matchMedia('(min-width: 768px)').matches) { desktopSidebarOpen = !desktopSidebarOpen; window.dispatchEvent(new Event('forcechartresize')); } else { mobileSidebarOpen = !mobileSidebarOpen }"
                    :aria-expanded="(window.matchMedia('(min-width: 768px)').matches ? desktopSidebarOpen : mobileSidebarOpen).toString()"
                    aria-label="Toggle sidebar">
                
                <svg class="hidden md:block w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
              
                <svg class="md:hidden w-6 h-6" x-show="!mobileSidebarOpen" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
                
                <svg class="md:hidden w-6 h-6" x-show="mobileSidebarOpen" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <nav class="flex-1 overflow-y-auto p-6 space-y-2 text-lg font-medium sidebar-scroll" @click="$event.target.closest('a') && (mobileSidebarOpen=false)"
            x-data="{ 
                // Initialize state from localStorage or route-based defaults
                open: localStorage.getItem('sidebar_po_open') !== null ? 
                    localStorage.getItem('sidebar_po_open') === 'true' : 
                    {{ request()->routeIs('po') || request()->routeIs('po.*') || request()->routeIs('suratjalan.*') ? 'true' : 'false' }}, 
                employeeOpen: localStorage.getItem('sidebar_employee_open') !== null ? 
                    localStorage.getItem('sidebar_employee_open') === 'true' : 
                    {{ request()->routeIs('employee.*') || request()->routeIs('salary.*') ? 'true' : 'false' }}, 
                barangOpen: localStorage.getItem('sidebar_barang_open') !== null ? 
                    localStorage.getItem('sidebar_barang_open') === 'true' : 
                    {{ request()->routeIs('barang.*') || request()->routeIs('produk.*') ? 'true' : 'false' }}, 
                userOpen: localStorage.getItem('sidebar_user_open') !== null ? 
                    localStorage.getItem('sidebar_user_open') === 'true' : 
                    {{ request()->routeIs('users.*') ? 'true' : 'false' }},
                financeOpen: localStorage.getItem('sidebar_finance_open') !== null ? 
                    localStorage.getItem('sidebar_finance_open') === 'true' : 
                    {{ request()->routeIs('finance.*') ? 'true' : 'false' }},
                
                // Methods to toggle and persist state
                togglePO() {
                    this.open = !this.open;
                    localStorage.setItem('sidebar_po_open', this.open);
                },
                toggleEmployee() {
                    this.employeeOpen = !this.employeeOpen;
                    localStorage.setItem('sidebar_employee_open', this.employeeOpen);
                },
                toggleBarang() {
                    this.barangOpen = !this.barangOpen;
                    localStorage.setItem('sidebar_barang_open', this.barangOpen);
                },
                toggleUser() {
                    this.userOpen = !this.userOpen;
                    localStorage.setItem('sidebar_user_open', this.userOpen);
                },
                toggleFinance() {
                    this.financeOpen = !this.financeOpen;
                    localStorage.setItem('sidebar_finance_open', this.financeOpen);
                }
            }">

            <a href="{{ route('dashboard') }}"
               class="group flex items-center gap-3 px-4 py-2 rounded-lg transition-all duration-200 {{ request()->routeIs('dashboard') ? 'bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-gray-100 font-semibold' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800' }}">
                <svg class="w-5 h-5 text-gray-500 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0h6" />
                </svg>
                <span>Dashboard</span>
            </a>

            <button @click="togglePO()"
                    class="w-full text-left px-4 py-2 rounded-lg transition-all duration-200 flex justify-between items-center {{ request()->routeIs('po') || request()->routeIs('po.*') || request()->routeIs('suratjalan.*') ? 'bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-gray-100 font-semibold' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800' }}">
                <span class="inline-flex items-center gap-3">
                    <svg class="w-5 h-5 text-gray-500 dark:text-gray-400 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7h18M3 12h18M3 17h18"/></svg>
                    Purchase Order
                </span>
                <svg x-bind:class="{ 'rotate-90': open }" class="w-4 h-4 min-w-[1rem] min-h-[1rem] transition-transform duration-300 transform shrink-0 flex-none"
                     fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                </svg>
            </button>
    
      
            <div x-show="open" x-transition.duration.300ms class="ml-6 pl-2 border-l border-gray-300 dark:border-gray-700 space-y-1 text-base overflow-hidden">
                <a href="{{ route('invoice.index') }}"
                   class="group flex items-center gap-2 px-3 py-1 rounded transition-all duration-200 {{ request()->routeIs('invoice.*') ? 'bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-gray-100 font-semibold' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800' }}">
                    <svg class="w-4 h-4 text-gray-400 dark:text-gray-500 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 14l2 2 4-4M7 7h10a2 2 0 012 2v8a2 2 0 01-2 2H7a2 2 0 01-2-2V9a2 2 0 012-2z"/></svg>
                    <span>Data Invoice</span>
                </a>
                <a href="{{ route('sisa-data-po.index') }}"
                   class="group flex items-center gap-2 px-3 py-1 rounded transition-all duration-200 {{ request()->routeIs('sisa-data-po.*') ? 'bg-yellow-50 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-300 font-semibold' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800' }}">
                    <svg class="w-4 h-4 text-gray-400 dark:text-gray-500 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>PO Belum Terkirim</span>
                </a>
                {{-- Link Input PO di-nonaktifkan: akses form hanya lewat double click Data Invoice --}}
                {{-- Link Data PO dihapus: akses Surat Jalan hanya dari alur Data Invoice --}}
                {{-- Data Invoice --}}
            </div>
            
            <!-- Menu Data Master (moved up to Finance position) -->
            <button @click="toggleBarang()"
                    class="w-full text-left px-4 py-2 rounded-lg transition-all duration-200 flex justify-between items-center {{ request()->routeIs('barang.*') || request()->routeIs('produk.*') ? 'bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-gray-100 font-semibold' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800' }}">
                <span class="inline-flex items-center gap-3">
                    <svg class="w-5 h-5 text-gray-500 dark:text-gray-400 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    Data Master
                </span>
                <svg x-bind:class="{ 'rotate-90': barangOpen }" class="w-4 h-4 min-w-[1rem] min-h-[1rem] transition-transform duration-300 transform shrink-0 flex-none"
                        fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                </svg>
            </button>
            
            <div x-show="barangOpen" x-transition.duration.300ms class="ml-6 pl-2 border-l border-gray-300 dark:border-gray-700 space-y-1 text-base overflow-hidden">
                <a href="{{ route('customer.index') }}"
                   class="group flex items-center gap-2 px-3 py-1 rounded transition-all duration-200 {{ request()->routeIs('customer.*') ? 'bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-gray-100 font-semibold' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800' }}">
                    <svg class="w-4 h-4 text-gray-400 dark:text-gray-500 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 11c1.657 0 3-1.567 3-3.5S17.657 4 16 4s-3 1.567-3 3.5 1.343 3.5 3 3.5zM8 11c1.657 0 3-1.567 3-3.5S9.657 4 8 4 5 5.567 5 7.5 6.343 11 8 11zm0 2c-2.761 0-5 2.015-5 4.5V20h10v-2.5c0-2.485-2.239-4.5-5-4.5zm8 0c-.725 0-1.414.131-2.047.364 1.22.903 2.047 2.235 2.047 3.886V20h6v-2.75c0-2.351-2.239-4.25-6-4.25z"/></svg>
                    <span>Data Customer</span>
                </a>
                <a href="{{ route('produk.index') }}"
                   class="group flex items-center gap-2 px-3 py-1 rounded transition-all duration-200 {{ request()->routeIs('produk.*') ? 'bg-red-50 dark:bg-red-900/30 text-red-700 dark:text-red-300 font-semibold' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800' }}">
                    <svg class="w-4 h-4 text-gray-400 dark:text-gray-500 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 13V7a2 2 0 00-2-2h-3V3H9v2H6a2 2 0 00-2 2v6m16 0v6a2 2 0 01-2 2H6a2 2 0 01-2-2v-6m16 0H4"/></svg>
                    <span>Data Barang</span>
                </a>
                <a href="{{ route('pengirim.index') }}"
                   class="group flex items-center gap-2 px-3 py-1 rounded transition-all duration-200 {{ request()->routeIs('pengirim.*') ? 'bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-gray-100 font-semibold' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800' }}">
                    <svg class="w-4 h-4 text-gray-400 dark:text-gray-500 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h18v4H3zM3 7l3 13h12l3-13"/></svg>
                    <span>Data Pengirim</span>
                </a>
            </div>

            <button @click="toggleEmployee()"
                    class="w-full text-left px-4 py-2 rounded-lg transition-all duration-200 flex justify-between items-center {{ request()->routeIs('employee.*') || request()->routeIs('salary.*') ? 'bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-gray-100 font-semibold' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800' }}">
                <span class="inline-flex items-center gap-3">
                    <svg class="w-5 h-5 text-gray-500 dark:text-gray-400 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 11c1.657 0 3-1.567 3-3.5S17.657 4 16 4s-3 1.567-3 3.5 1.343 3.5 3 3.5zM8 11c1.657 0 3-1.567 3-3.5S9.657 4 8 4 5 5.567 5 7.5 6.343 11 8 11zm0 2c-2.761 0-5 2.015-5 4.5V20h10v-2.5c0-2.485-2.239-4.5-5-4.5zm8 0c-.725 0-1.414.131-2.047.364 1.22.903 2.047 2.235 2.047 3.886V20h6v-2.75c0-2.351-2.239-4.25-6-4.25z"/>
                    </svg>
                    Karyawan
                </span>
                <svg x-bind:class="{ 'rotate-90': employeeOpen }" class="w-4 h-4 min-w-[1rem] min-h-[1rem] transition-transform duration-300 transform shrink-0 flex-none"
                        fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                </svg>
            </button>

            <div x-show="employeeOpen" x-transition.duration.300ms class="ml-6 pl-2 border-l border-gray-300 dark:border-gray-700 space-y-1 text-base overflow-hidden">
                <a href="{{ route('employee.index') }}"
                   class="group flex items-center gap-2 px-3 py-1 rounded transition-all duration-200 {{ request()->routeIs('employee.*') ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 font-semibold' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800' }}">
                    <svg class="w-4 h-4 text-gray-400 dark:text-gray-500 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    <span>Data Karyawan</span>
                </a>

                <a href="{{ route('salary.index') }}"
                   class="group flex items-center gap-2 px-3 py-1 rounded transition-all duration-200 {{ request()->routeIs('salary.*') ? 'bg-green-50 dark:bg-green-900/30 text-green-700 dark:text-green-300 font-semibold' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800' }}">
                    <svg class="w-4 h-4 text-gray-400 dark:text-gray-500 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-3.866 0-7 2.015-7 4.5S8.134 17 12 17s7-2.015 7-4.5S15.866 8 12 8zm0 0V5m0 12v2"/></svg>
                    <span>Gaji Karyawan</span>
                </a>
            </div>

            <!-- Finance (moved down to Data Master position) -->
            <button @click="toggleFinance()"
                    class="w-full text-left px-4 py-2 rounded-lg transition-all duration-200 flex justify-between items-center {{ request()->routeIs('finance.*') ? 'bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-gray-100 font-semibold' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800' }}">
                <span class="inline-flex items-center gap-3">
                    <svg class="w-5 h-5 text-gray-500 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 11V3m0 8a4 4 0 100 8h4a4 4 0 100-8h-4z" />
                    </svg>
                    Finance
                </span>
                <svg x-bind:class="{ 'rotate-90': financeOpen }" class="w-4 h-4 min-w-[1rem] min-h-[1rem] transition-transform duration-300 transform shrink-0 flex-none"
                        fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                </svg>
            </button>
            <div x-show="financeOpen" x-transition.duration.300ms class="ml-6 pl-2 border-l border-gray-300 dark:border-gray-700 space-y-1 text-base overflow-hidden">
                <a href="{{ route('finance.income') }}"
                   class="group flex items-center gap-2 px-3 py-1 rounded transition-all duration-200 {{ request()->routeIs('finance.income') ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 font-semibold' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800' }}">
                    <svg class="w-4 h-4 text-gray-400 dark:text-gray-500 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 11V3m0 8a4 4 0 100 8h4a4 4 0 100-8h-4z"/></svg>
                    <span>Pendapatan</span>
                </a>
                <a href="{{ route('finance.expense') }}"
                   class="group flex items-center gap-2 px-3 py-1 rounded transition-all duration-200 {{ request()->routeIs('finance.expense') ? 'bg-green-50 dark:bg-green-900/30 text-green-700 dark:text-green-300 font-semibold' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800' }}">
                    <svg class="w-4 h-4 text-gray-400 dark:text-gray-500 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v8m-4-4h8"/></svg>
                    <span>Pengeluaran</span>
                </a>
            </div>

            <!-- Parent Menu with Toggle -->
            
            <!-- Manajemen Pengguna -->
            <button @click="toggleUser()"
                    class="w-full text-left px-4 py-2 rounded-lg transition-all duration-200 flex justify-between items-center {{ request()->routeIs('users.*') ? 'bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-gray-100 font-semibold' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800' }}">
                <span class="inline-flex items-center gap-3">
                    <svg class="w-6 h-6 text-gray-500 dark:text-gray-400 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 19a7 7 0 0114 0"/>
                    </svg>
                    Manajemen Pengguna
                </span>
                <svg x-bind:class="{ 'rotate-90': userOpen }" class="w-4 h-4 min-w-[1rem] min-h-[1rem] transition-transform duration-300 transform shrink-0 flex-none"
                        fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                    </svg>
                </button>
                <div id="user-section" x-show="userOpen" x-transition.duration.300ms class="ml-6 pl-2 border-l border-gray-300 dark:border-gray-700 space-y-1 text-base overflow-hidden">
                    <a href="{{ Route::has('users.create') ? route('users.create') : url('/users/create') }}"
                   class="group flex items-center gap-2 px-3 py-1 rounded transition-all duration-200 {{ request()->routeIs('users.create') ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 font-semibold' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800' }}">
                    <svg class="w-5 h-5 text-gray-400 dark:text-gray-500 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 19a7 7 0 0114 0"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 8v6m3-3h-6"/>
                    </svg>
                    <span>Tambah User</span>
                </a>

                @if(auth()->user()?->is_admin)
                <a href="{{ route('users.index') }}"
                class="group flex items-center gap-2 px-3 py-1 rounded transition-all duration-200 {{ request()->routeIs('users.index') ? 'bg-green-50 dark:bg-green-900/30 text-green-700 dark:text-green-300 font-semibold' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800' }}">
                <svg class="w-5 h-5 text-gray-400 dark:text-gray-500 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5V9H2v11h5M7 9V7a5 5 0 0110 0v2"/>
                </svg>
                <span>Daftar User</span>
            </a>
            @endif
        </div>

            <!-- Link-link Data digabung dalam submenu Data Master di atas -->

            

            

            <a href="{{ route('jatuh-tempo.index') }}"
               class="group flex items-center gap-3 px-4 py-2 rounded-lg transition-all duration-200 {{ request()->routeIs('jatuh-tempo.*') ? 'bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-gray-100 font-semibold' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800' }}">
                <svg class="w-5 h-5 text-gray-500 dark:text-gray-400 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3M12 3a9 9 0 100 18 9 9 0 000-18z"/></svg>
                <span>Jatuh Tempo</span>
            </a>

            
        </nav>

        <!-- Sidebar bottom: User + Logout -->
        <div class="mt-auto px-4 py-4 border-t border-gray-200 dark:border-gray-800 bg-gray-50 dark:bg-gray-800/60">
            <div class="flex items-center justify-between">
                <!-- Klik bagian profil untuk membuka Pengaturan -->
                <a href="{{ route('settings') }}" class="flex items-center gap-3 min-w-0 hover:bg-gray-100 dark:hover:bg-gray-700/60 rounded-lg -mx-2 px-2 py-2" title="Buka Pengaturan">
                    <div class="h-10 w-10 rounded-full bg-indigo-600 text-white flex items-center justify-center font-semibold">
                        {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                    </div>
                    <div class="min-w-0">
                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">{{ auth()->user()->name }}</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ auth()->user()->email }}</div>
                    </div>
                </a>
                <form method="POST" action="{{ route('logout') }}" x-data="{ running:false }">
                    @csrf
                    <button type="submit"
                            class="inline-flex items-center justify-center h-9 w-9 rounded-md bg-rose-600 text-white hover:bg-rose-700 transition transform hover:translate-x-0.5"
                            :class="running ? 'opacity-80 cursor-wait' : ''"
                            title="Keluar"
                            @click.prevent="running = true; setTimeout(() => $el.closest('form').submit(), 350)">
                        <template x-if="!running">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 01-2 2H7a2 2 0 01-2-2V7a2 2 0 012-2h4a2 2 0 012 2v1"/></svg>
                        </template>
                        <template x-if="running">
                            <svg class="w-5 h-5 animate-spin" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                            </svg>
                        </template>
                    </button>
                </form>
            </div>
        </div>

    </aside>

    <!-- Overlay for mobile sidebar -->
    <div x-show="mobileSidebarOpen" class="fixed inset-0 z-30 bg-black/40 md:hidden" @click="mobileSidebarOpen=false" x-transition.opacity></div>

    

    <!-- Main Content -->
    <div class="flex-1 flex flex-col transition-all duration-300 overflow-x-hidden"
         :class="{
             'md:ml-64': desktopSidebarOpen,
             'md:ml-0': !desktopSidebarOpen
         }">
        <header class="sticky top-0 z-30 h-16 bg-white/90 dark:bg-gray-900/80 backdrop-blur border-b border-gray-200 dark:border-gray-800 px-4 md:px-6 flex items-center justify-between shadow-sm theme-instant">
            <div class="flex items-center gap-3">
                <!-- Hamburger (desktop only) to restore sidebar when hidden -->
                <button type="button" class="hidden md:inline-flex items-center justify-center h-10 w-10 rounded-md text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                        x-show="!desktopSidebarOpen" @click="desktopSidebarOpen = true" aria-label="Tampilkan sidebar" title="Tampilkan sidebar">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <!-- Hamburger (mobile only) toggles open/close -->
                <button type="button" class="md:hidden inline-flex items-center justify-center h-10 w-10 rounded-md text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                        @click="mobileSidebarOpen = !mobileSidebarOpen" aria-controls="sidebar" :aria-expanded="mobileSidebarOpen.toString()" aria-label="Toggle sidebar">
                    <!-- Icon: hamburger when closed -->
                    <svg x-show="!mobileSidebarOpen" class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    <!-- Icon: close when open -->
                    <svg x-show="mobileSidebarOpen" class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
                <h1 class="text-lg md:text-xl font-semibold text-gray-800 dark:text-gray-100">@yield('title', 'Dashboard')</h1>
            </div>
            <div class="flex items-center gap-2 md:gap-3">
                @php(
                    $userRoleLabel = (auth()->user()?->is_admin ?? false) ? 'Admin' : 'User'
                )
                @php(
                    $routeName = optional(request()->route())->getName()
                )
                @php(
                    $firstSegment = $routeName ? explode('.', $routeName)[0] : ''
                )
                @php(
                    $labelMap = [
                        'dashboard' => 'Dashboard',
                        'invoice' => 'Invoice',
                        'sisa-data-po' => 'PO Belum Terkirim',
                        'po' => 'Purchase Order',
                        'suratjalan' => 'Surat Jalan',
                        'customer' => 'Customer',
                        'produk' => 'Barang',
                        'pengirim' => 'Pengirim',
                        'employee' => 'Karyawan',
                        'salary' => 'Gaji Karyawan',
                        'finance' => 'Finance',
                        'users' => 'Users',
                        'jatuh-tempo' => 'Jatuh Tempo',
                    ]
                )
                @php(
                    $sectionLabel = $labelMap[$firstSegment] ?? ($firstSegment ? ucwords(str_replace('-', ' ', $firstSegment)) : null)
                )
                @php(
                    $showSection = $sectionLabel && $sectionLabel !== 'Dashboard'
                )
                <nav class="hidden sm:flex text-xs md:text-sm text-gray-600 dark:text-gray-300 items-center gap-1 md:gap-2 mr-2">
                    <span class="font-semibold text-gray-800 dark:text-gray-100">{{ $userRoleLabel }}</span>
                    <span class="opacity-40">/</span>
                    <a href="{{ route('dashboard') }}" class="font-semibold hover:text-indigo-600 dark:hover:text-indigo-400">Dashboard</a>
                    @if($showSection)
                        <span class="opacity-40">/</span>
                        <span>{{ $sectionLabel }}</span>
                    @endif
                </nav>
                <!-- Notification Bell -->
                <div x-data="notificationBell()" x-init="init()" class="relative">
                    <button @click="toggleDropdown()" 
                            class="relative flex items-center justify-center w-10 h-10 rounded-full text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-colors duration-200"
                            title="Notifikasi Jatuh Tempo">
                        <i class="fa-solid fa-bell text-lg"></i>
                        <span x-show="hasOverdue || hasToday || hasUpcoming" 
                              class="absolute -top-1 -right-1 flex items-center justify-center w-5 h-5 text-xs font-bold text-white bg-red-500 rounded-full"
                              x-text="totalNotifications"></span>
                    </button>
                    
                    <!-- Dropdown -->
                    <div x-show="dropdownOpen" 
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         @click.away="dropdownOpen = false"
                         class="absolute right-0 top-12 w-80 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 z-50">
                        
                        <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Notifikasi Jatuh Tempo</h3>
                        </div>
                        
                        <div class="max-h-80 overflow-y-auto">
                            <!-- Overdue notifications -->
                            <template x-for="item in overdueItems" :key="item.id">
                                <div class="p-3 border-b border-gray-100 dark:border-gray-700 bg-red-50 dark:bg-red-900/20">
                                    <div class="flex items-start gap-3">
                                        <i class="fa-solid fa-exclamation-triangle text-red-500 mt-1"></i>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900 dark:text-gray-100" x-text="item.no_invoice"></p>
                                            <p class="text-xs text-gray-600 dark:text-gray-400" x-text="item.customer"></p>
                                            <p class="text-xs text-red-600 dark:text-red-400 font-medium" x-text="'Terlambat ' + Math.abs(item.daysLeft) + ' hari'"></p>
                                        </div>
                                    </div>
                                </div>
                            </template>
                            
                            <!-- Today notifications -->
                            <template x-for="item in todayItems" :key="item.id">
                                <div class="p-3 border-b border-gray-100 dark:border-gray-700 bg-orange-50 dark:bg-orange-900/20">
                                    <div class="flex items-start gap-3">
                                        <i class="fa-solid fa-clock text-orange-500 mt-1"></i>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900 dark:text-gray-100" x-text="item.no_invoice"></p>
                                            <p class="text-xs text-gray-600 dark:text-gray-400" x-text="item.customer"></p>
                                            <p class="text-xs text-orange-600 dark:text-orange-400 font-medium">Jatuh tempo hari ini</p>
                                        </div>
                                    </div>
                                </div>
                            </template>
                            
                            <!-- Upcoming notifications -->
                            <template x-for="item in upcomingItems" :key="item.id">
                                <div class="p-3 border-b border-gray-100 dark:border-gray-700 bg-yellow-50 dark:bg-yellow-900/20">
                                    <div class="flex items-start gap-3">
                                        <i class="fa-solid fa-calendar-days text-yellow-500 mt-1"></i>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900 dark:text-gray-100" x-text="item.no_invoice"></p>
                                            <p class="text-xs text-gray-600 dark:text-gray-400" x-text="item.customer"></p>
                                            <p class="text-xs text-yellow-600 dark:text-yellow-400 font-medium" x-text="item.daysLeft + ' hari lagi'"></p>
                                        </div>
                                    </div>
                                </div>
                            </template>
                            
                            <!-- No notifications -->
                            <div x-show="!hasOverdue && !hasToday && !hasUpcoming" class="p-4 text-center">
                                <i class="fa-solid fa-check-circle text-green-500 text-2xl mb-2"></i>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Tidak ada jatuh tempo mendesak</p>
                            </div>
                        </div>
                        
                        <div class="p-3 border-t border-gray-200 dark:border-gray-700">
                            <a href="{{ route('jatuh-tempo.index') }}" 
                               class="block w-full text-center text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 font-medium">
                                Lihat Semua Jatuh Tempo
                            </a>
                        </div>
                    </div>
                </div>
                
                <button id="theme-toggle" title="Toggle Tema" class="relative flex items-center w-[3.75rem] h-8 rounded-full bg-gray-200 dark:bg-gray-700 p-1 transition-colors duration-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <span class="absolute top-1 left-1 flex items-center justify-center h-6 w-6 rounded-full bg-white shadow-md transform transition-transform duration-300 translate-x-7 dark:translate-x-0"></span>
                    <span class="relative z-10 w-6 h-6 flex items-center justify-center">
                        <i class="fa-solid fa-moon text-gray-400 dark:text-indigo-500"></i>
                    </span>
                    <span class="relative z-10 w-6 h-6 flex items-center justify-center">
                        <i class="fa-solid fa-sun text-yellow-500 dark:text-gray-400"></i>
                    </span>
                </button>
            </div>
        </header>

        @if (request()->routeIs('settings'))
        <main class="p-10 bg-white dark:bg-gray-900">
            <div x-data="{ show: false }" x-init="requestAnimationFrame(()=> show = true)"
                 x-show="show"
                 x-transition:enter="transform transition ease-out duration-500"
                 x-transition:enter-start="opacity-0 translate-y-4 scale-[0.98]"
                 x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                 class="will-change-transform">
                @yield('content')
            </div>
        </main>
        @else
        <main class="p-10 bg-white dark:bg-gray-900">
            @yield('content')
        </main>
        @endif
    </div>

    <!-- Auto-wrap tables to ensure horizontal scrolling on small screens -->
    <script>
      (function(){
        try {
          const candidates = document.querySelectorAll('main table, .flex-1 table, table.min-w-full, table.table-auto');
          candidates.forEach(function(tbl){
            // Skip if table already inside a scroll container
            if (tbl.closest('.responsive-scroll, .overflow-x-auto')) return;
            const wrap = document.createElement('div');
            wrap.className = 'overflow-x-auto responsive-scroll';
            tbl.parentNode.insertBefore(wrap, tbl);
            wrap.appendChild(tbl);
          });
        } catch(e) { /* no-op */ }
      })();
    </script>

    <script>
        (function(){
            const btn = document.getElementById('theme-toggle');
            if (!btn) return;
            btn.addEventListener('click', function(){
                const d = document.documentElement;
                const willDark = !d.classList.contains('dark');
                d.classList.toggle('dark', willDark);
                try { localStorage.setItem('theme', willDark ? 'dark' : 'light'); } catch(e) {}
                try { window.dispatchEvent(new CustomEvent('themechange', { detail: { dark: willDark } })); } catch(e) {}
            });
        })();
    </script>

    <script>
        function notificationBell() {
            return {
                dropdownOpen: false,
                overdueItems: [],
                todayItems: [],
                upcomingItems: [],
                hasOverdue: false,
                hasToday: false,
                hasUpcoming: false,
                totalNotifications: 0,

                init() {
                    this.fetchNotifications();
                    // Refresh notifications every 5 minutes
                    setInterval(() => {
                        this.fetchNotifications();
                    }, 300000);
                    // Listen for global refresh event
                    document.addEventListener('refresh-notifications', () => {
                        this.fetchNotifications();
                    });
                    // Expose global helper once
                    if (!window.refreshNotifications) {
                        window.refreshNotifications = () => {
                            try {
                                document.dispatchEvent(new CustomEvent('refresh-notifications'));
                            } catch (e) { /* ignore */ }
                        };
                    }
                },

                toggleDropdown() {
                    this.dropdownOpen = !this.dropdownOpen;
                },

                async fetchNotifications() {
                    try {
                        const url = `/api/jatuh-tempo/notifications?t=${Date.now()}`;
                        const response = await fetch(url, { cache: 'no-store', headers: { 'Cache-Control': 'no-cache' } });
                        const data = await response.json();
                        
                        this.overdueItems = data.overdue || [];
                        this.todayItems = data.today || [];
                        this.upcomingItems = data.upcoming || [];
                        
                        this.hasOverdue = this.overdueItems.length > 0;
                        this.hasToday = this.todayItems.length > 0;
                        this.hasUpcoming = this.upcomingItems.length > 0;
                        
                        this.totalNotifications = this.overdueItems.length + this.todayItems.length + this.upcomingItems.length;
                    } catch (error) {
                        console.error('Error fetching notifications:', error);
                    }
                }
            }
        }
    </script>

    @if (app()->environment('production'))
    <!-- Silence non-critical console output in production -->
    <script>
        (function(){
            try {
                const noop = function(){};
                if (window && window.console) {
                    console.log = noop;
                    console.info = noop;
                    console.debug = noop;
                }
            } catch(e) {}
        })();
    </script>
    @endif

    @stack('modals')
    @stack('scripts')
</body>
</html>



