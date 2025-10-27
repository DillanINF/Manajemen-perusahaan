@extends('layouts.app')
@section('title', 'Pengaturan Akun')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl shadow-lg mb-4 overflow-hidden">
            <img src="{{ asset('image/LOGO.png') }}" alt="Logo" class="w-12 h-12 object-contain" />
        </div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Pengaturan Akun</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Terakhir diperbarui: {{ auth()->user()->updated_at?->diffForHumans() ?? '-' }}</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Update Profile Info -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700 p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Ubah Profil</h2>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Perbarui nama dan email akun Anda.</p>

            <form method="POST" action="{{ route('profile.update') }}" class="mt-6 space-y-4">
                @csrf
                @method('patch')

                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Nama</label>
                    <input id="name" name="name" type="text" class="mt-1 block w-full rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 placeholder-gray-400 focus:border-indigo-500 focus:ring-indigo-500" value="{{ old('name', auth()->user()->name) }}" required autofocus autocomplete="name">
                    @error('name')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Email</label>
                    <input id="email" name="email" type="email" class="mt-1 block w-full rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 placeholder-gray-400 focus:border-indigo-500 focus:ring-indigo-500" value="{{ old('email', auth()->user()->email) }}" required autocomplete="username">
                    @error('email')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>

                @if (session('status') === 'profile-updated')
                    <div class="rounded-md bg-green-50 border border-green-200 text-green-700 text-sm px-3 py-2">Profil berhasil diperbarui.</div>
                @endif

                <div class="flex items-center gap-3 justify-end">
                    <button class="w-full sm:w-auto inline-flex items-center justify-center px-5 py-2.5 rounded-xl bg-gradient-to-r from-indigo-600 to-blue-600 text-white font-semibold hover:from-indigo-700 hover:to-blue-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-indigo-500 dark:ring-offset-gray-800 transition">Simpan</button>
                </div>
            </form>
        </div>

        <!-- Update Password -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700 p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Ubah Password</h2>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Pastikan password baru kuat dan mudah diingat.</p>

            <form method="POST" action="{{ route('password.update') }}" class="mt-6 space-y-4">
                @csrf
                @method('put')

                <div>
                    <label for="current_password" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Password Saat Ini</label>
                    <input id="current_password" name="current_password" type="password" class="mt-1 block w-full rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 placeholder-gray-400 focus:border-indigo-500 focus:ring-indigo-500" autocomplete="current-password">
                    @error('current_password', 'updatePassword')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Password Baru</label>
                    <input id="password" name="password" type="password" class="mt-1 block w-full rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 placeholder-gray-400 focus:border-indigo-500 focus:ring-indigo-500" autocomplete="new-password">
                    @error('password', 'updatePassword')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Konfirmasi Password Baru</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 placeholder-gray-400 focus:border-indigo-500 focus:ring-indigo-500" autocomplete="new-password">
                </div>

                @if (session('status') === 'password-updated')
                    <div class="rounded-md bg-green-50 border border-green-200 text-green-700 text-sm px-3 py-2">Password berhasil diperbarui.</div>
                @endif

                @if ($errors->updatePassword->any())
                    <div class="rounded-md bg-red-50 border border-red-200 text-red-700 text-sm px-3 py-2">
                        <ul class="list-disc pl-5 space-y-1">
                            @foreach ($errors->updatePassword->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="flex items-center gap-3 justify-end">
                    <button class="w-full sm:w-auto inline-flex items-center justify-center px-5 py-2.5 rounded-xl bg-gradient-to-r from-indigo-600 to-blue-600 text-white font-semibold hover:from-indigo-700 hover:to-blue-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-indigo-500 dark:ring-offset-gray-800 transition">Perbarui Password</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
