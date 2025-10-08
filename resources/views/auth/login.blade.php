<x-guest-layout>
    <script>
        // Force light theme on login page only
        document.addEventListener('DOMContentLoaded', function () {
            const root = document.documentElement;
            // Remove global dark class so any `dark:` utilities won't apply here
            root.classList.remove('dark');
        });
    </script>
    <div class="min-h-screen flex items-center px-4 md:px-6 py-6 px-safe pt-safe pb-safe bg-gradient-to-br from-indigo-50 via-white to-rose-50 dark:from-gray-900 dark:via-gray-900 dark:to-gray-800">
        <div class="mx-auto w-full max-w-6xl grid md:grid-cols-2 gap-6 sm:gap-8 md:gap-10 items-center">

            <!-- Branding / Sisi Kiri -->
            <div class="flex flex-col gap-6 justify-self-center w-full max-w-md">
                <div class="flex items-center gap-3">
                    <img src="{{ asset('image/LOGO.png') }}" alt="Logo" class="h-10 w-auto">
                    <span class="text-xl font-semibold text-gray-800 dark:text-gray-100">Aplikasi Operasional</span>
                </div>
                <h2 class="text-2xl md:text-3xl font-bold leading-tight text-gray-900 dark:text-white">
                    Selamat Datang Kembali
                </h2>
                <p class="text-gray-600 dark:text-gray-300">
                    Kelola PO, Surat Jalan, Invoice, dan data operasional Anda dalam satu tempat yang cepat dan modern.
                </p>
                <ul class="space-y-3 text-sm">
                    <li class="flex items-start gap-3 text-gray-700 dark:text-gray-300">
                        <span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-indigo-100 text-indigo-600 dark:bg-indigo-900/40">✓</span>
                        Dashboard ringkas dan informatif
                    </li>
                    <li class="flex items-start gap-3 text-gray-700 dark:text-gray-300">
                        <span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-indigo-100 text-indigo-600 dark:bg-indigo-900/40">✓</span>
                        Input data cepat dengan validasi
                    </li>
                    <li class="flex items-start gap-3 text-gray-700 dark:text-gray-300">
                        <span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-indigo-100 text-indigo-600 dark:bg-indigo-900/40">✓</span>
                        Mode gelap otomatis
                    </li>
                </ul>
            </div>

            <!-- Kartu Login / Sisi Kanan -->
            <div class="justify-self-center w-full max-w-md bg-white backdrop-blur rounded-2xl shadow-xl border border-gray-200 p-6 sm:p-7 md:p-8" x-data="{ locked: true, open: false, clickFx: false, ripple: 0 }">
                <div class="max-w-sm mx-auto w-full">
                    <!-- Session Status -->
                    <x-auth-session-status class="mb-4" :status="session('status')" />

                    <div class="mb-6">
                        <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Masuk ke Akun</h1>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Silakan masukkan kredensial Anda untuk melanjutkan.</p>
                    </div>

                    <!-- Toggle Lock/Unlock yang membuka form -->
                    <div x-show="locked && !open" x-transition.opacity.duration.200ms class="flex flex-col items-center gap-4 py-2">
                        <button type="button"
                                @click="locked = false; clickFx = true; ripple++; setTimeout(() => { open = true }, 300); setTimeout(() => clickFx = false, 420)"
                                class="relative w-28 h-11 rounded-full bg-gray-100 border border-gray-200 shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 transition overflow-hidden"
                                :class="clickFx ? 'animate-press-pop animate-glow' : ''">
                            <!-- Track gradient soft -->
                            <span class="absolute inset-0 rounded-full bg-gradient-to-br from-white/60 to-black/5"></span>
                            <!-- Ripple effect -->
                            <span class="ripple-circle" :key="ripple" :class="clickFx ? 'animate-ripple' : ''"></span>
                            <!-- Icons -->
                            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-indigo-600 h-5 w-5">
                                <!-- Stack both icons and animate between them -->
                                <!-- Unlock icon (visible while locked is false i.e., after unlock) -->
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                     class="absolute h-5 w-5 transition-all duration-300"
                                     :class="locked ? 'opacity-0 rotate-6 scale-90' : 'opacity-100 rotate-0 scale-100'">
                                    <path d="M17 8V7a5 5 0 10-10 0h2a3 3 0 116 0v1h1a2 2 0 012 2v8a2 2 0 01-2 2H6a2 2 0 01-2-2v-8a2 2 0 012-2h11zM8 13a4 4 0 108 0 4 4 0 00-8 0z"/>
                                </svg>
                                <!-- Lock icon (visible while locked is true i.e., before unlock) -->
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                     class="absolute h-5 w-5 transition-all duration-300"
                                     :class="locked ? 'opacity-100 rotate-0 scale-100' : 'opacity-0 -rotate-6 scale-90'">
                                    <path d="M12 1a5 5 0 00-5 5v2H6a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2v-8a2 2 0 00-2-2h-1V6a5 5 0 00-5-5zm-3 7V6a3 3 0 016 0v2H9z"/>
                                </svg>
                            </span>
                            <!-- Knob -->
                            <span class="absolute top-1 left-1 h-9 w-9 rounded-full bg-white shadow-md transition-transform duration-300 grid place-items-center" :class="locked ? 'translate-x-0' : 'translate-x-16'">
                                <!-- Lock closed icon inside knob -->
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-600 transition-all duration-300" viewBox="0 0 24 24" fill="currentColor"
                                     :class="locked ? 'opacity-100 rotate-0' : 'opacity-70 rotate-12'">
                                    <path d="M12 1a5 5 0 00-5 5v2H6a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2v-8a2 2 0 00-2-2h-1V6a5 5 0 00-5-5zm-3 7V6a3 3 0 016 0v2H9z"/>
                                </svg>
                            </span>
                        </button>
                        <p class="text-xs text-gray-500">Klik toggle untuk membuka form login.</p>
                    </div>

                    <!-- Form Login: muncul setelah unlock -->
                    <form x-show="open"
                          x-transition:enter="transition ease-out duration-500"
                          x-transition:enter-start="opacity-0 translate-y-2 scale-[0.99]"
                          x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                          x-transition:leave="transition ease-in duration-400"
                          x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                          x-transition:leave-end="opacity-0 -translate-y-2 scale-[0.98]"
                          method="POST" action="{{ route('login') }}" class="space-y-5" @keydown.escape.window="open=false"
                          :class="open ? 'animate-form-in' : ''">
                        @csrf

                        <!-- Toggle to close (re-lock) the form -->
                        <div class="flex items-center justify-end -mt-1 animate-form-item anim-delay-0">
                            <button type="button"
                                    @click="open=false; setTimeout(() => { locked = true }, 320)"
                                    class="relative inline-flex items-center gap-2 px-3 h-9 rounded-full text-sm bg-gray-100 hover:bg-gray-200 dark:bg-gray-800/70 dark:hover:bg-gray-700 border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-200 transition overflow-hidden"
                                    :class="open ? '' : 'pointer-events-none opacity-60'"
                                    aria-label="Tutup form login">
                                <!-- subtle ripple on click -->
                                <span class="ripple-circle" :class="open ? '' : ''"></span>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M17 8V7a5 5 0 10-10 0h2a3 3 0 116 0v1h1a2 2 0 012 2v8a2 2 0 01-2 2H6a2 2 0 01-2-2v-8a2 2 0 012-2h11z"/>
                                </svg>
                                <span>Tutup</span>
                            </button>
                        </div>

                        <!-- Email Address -->
                        <div class="animate-form-item anim-delay-100">
                            <x-input-label for="email" :value="__('Email')" />
                            <x-text-input id="email" class="block mt-1 w-full h-11" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <!-- Password -->
                        <div class="animate-form-item anim-delay-200">
                            <x-input-label for="password" :value="__('Password')" />

                            <x-text-input id="password" class="block mt-1 w-full h-11"
                                          type="password"
                                          name="password"
                                          required autocomplete="current-password" />
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />

                            @if (Route::has('password.request'))
                                <div class="mt-2 text-right">
                                    <a class="text-sm text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300" href="{{ route('password.request') }}">
                                        {{ __('Forgot your password?') }}
                                    </a>
                                </div>
                            @endif
                        </div>

                        <!-- Remember Me -->
                        <div class="flex flex-col-reverse sm:flex-row items-stretch sm:items-center justify-between gap-3 sm:gap-0 animate-form-item anim-delay-300">
                            <label for="remember_me" class="inline-flex items-center">
                                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                                <span class="ms-2 text-sm text-gray-600 dark:text-gray-300">{{ __('Remember me') }}</span>
                            </label>

                            <x-primary-button class="ms-0 w-full sm:w-auto h-11 justify-center">
                                {{ __('Log in') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</x-guest-layout>
