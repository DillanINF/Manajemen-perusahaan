<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <script>
            // Terapkan tema sedini mungkin TANPA transisi agar tidak ada lag/flash
            (function(){
                try {
                    const d = document.documentElement;
                    d.classList.add('theme-switching');
                    const pref = localStorage.getItem('theme');
                    if (pref === 'dark' || (!pref && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                        d.classList.add('dark');
                    } else {
                        d.classList.remove('dark');
                    }
                    requestAnimationFrame(() => d.classList.remove('theme-switching'));
                } catch(e) {}
            })();
        </script>
        <style>
            /* Nonaktifkan semua transition/animation selama switching tema (landing/login) */
            .theme-switching, .theme-switching *, .theme-switching *::before, .theme-switching *::after {
                transition: none !important;
                animation: none !important;
            }
            .no-theme-transition, .no-theme-transition * { transition: none !important; }
        </style>
        <script>
            // Utilitas setTheme agar toggle tema instan juga di guest/landing
            (function(){
                function setTheme(theme){
                    try {
                        const d = document.documentElement;
                        d.classList.add('theme-switching');
                        d.classList.add('no-theme-transition');
                        if (theme === 'dark') d.classList.add('dark'); else d.classList.remove('dark');
                        localStorage.setItem('theme', theme);
                        requestAnimationFrame(() => {
                            d.classList.remove('no-theme-transition');
                            d.classList.remove('theme-switching');
                        });
                    } catch(e) {}
                }
                window.setTheme = setTheme;
                window.addEventListener('storage', (e) => {
                    if (e.key === 'theme') {
                        setTheme(e.newValue === 'dark' ? 'dark' : 'light');
                    }
                });
                document.addEventListener('click', (ev) => {
                    const btn = ev.target.closest('[data-toggle-theme]');
                    if (!btn) return;
                    const current = (localStorage.getItem('theme') || (document.documentElement.classList.contains('dark') ? 'dark' : 'light'));
                    setTheme(current === 'dark' ? 'light' : 'dark');
                });
            })();
        </script>
    </head>
    <body class="font-sans text-gray-900 antialiased overflow-x-hidden">
        {{ $slot }}
        <script>
          (function(){
            try {
              const candidates = document.querySelectorAll('main table, table.min-w-full, table.table-auto, body > table');
              candidates.forEach(function(tbl){
                if (tbl.closest('.responsive-scroll, .overflow-x-auto')) return;
                const wrap = document.createElement('div');
                wrap.className = 'overflow-x-auto responsive-scroll';
                tbl.parentNode.insertBefore(wrap, tbl);
                wrap.appendChild(tbl);
              });
            } catch(e) {}
          })();
        </script>
    </body>
</html>
