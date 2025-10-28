@extends('layouts.app')
@section('title', 'Pengaturan Akun')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-6 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">Pengaturan Akun</h1>
        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Kelola informasi profil dan keamanan akun Anda</p>
    </div>

    <div class="space-y-6">
        <!-- Update Profile Info -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-base font-semibold text-gray-900 dark:text-gray-100">Informasi Profil</h2>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Perbarui nama dan email akun Anda</p>
            </div>
            <div class="px-6 py-5">

                <form method="POST" action="{{ route('profile.update') }}" class="space-y-5">
                    @csrf
                    @method('patch')

                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Nama Lengkap</label>
                        <input id="name" name="name" type="text" class="block w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 placeholder-gray-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-500/30 transition" value="{{ old('name', auth()->user()->name) }}" required autofocus autocomplete="name">
                        @error('name')<p class="text-sm text-red-600 dark:text-red-400 mt-1.5">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Alamat Email</label>
                        <input id="email" name="email" type="email" class="block w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 placeholder-gray-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-500/30 transition" value="{{ old('email', auth()->user()->email) }}" required autocomplete="username">
                        @error('email')<p class="text-sm text-red-600 dark:text-red-400 mt-1.5">{{ $message }}</p>@enderror
                    </div>

                    @if (session('status') === 'profile-updated')
                        <div class="rounded-lg bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 text-sm px-4 py-3">
                            <i class="fas fa-check-circle mr-2"></i>Profil berhasil diperbarui
                        </div>
                    @endif

                    <div class="flex items-center justify-end pt-2">
                        <button type="submit" class="inline-flex items-center justify-center px-5 py-2.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:ring-offset-gray-800 transition">
                            <i class="fas fa-save mr-2"></i>Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Update Password -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-base font-semibold text-gray-900 dark:text-gray-100">Keamanan Akun</h2>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Perbarui password untuk menjaga keamanan akun</p>
            </div>
            <div class="px-6 py-5">

                <form method="POST" action="{{ route('password.update') }}" class="space-y-5">
                    @csrf
                    @method('put')

                    <div>
                        <label for="current_password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Password Saat Ini</label>
                        <input id="current_password" name="current_password" type="password" class="block w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 placeholder-gray-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-500/30 transition" autocomplete="current-password">
                        @error('current_password', 'updatePassword')<p class="text-sm text-red-600 dark:text-red-400 mt-1.5">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Password Baru</label>
                        <input id="password" name="password" type="password" class="block w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 placeholder-gray-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-500/30 transition" autocomplete="new-password">
                        @error('password', 'updatePassword')<p class="text-sm text-red-600 dark:text-red-400 mt-1.5">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Konfirmasi Password Baru</label>
                        <input id="password_confirmation" name="password_confirmation" type="password" class="block w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 placeholder-gray-400 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-500/30 transition" autocomplete="new-password">
                    </div>

                    @if (session('status') === 'password-updated')
                        <div class="rounded-lg bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 text-sm px-4 py-3">
                            <i class="fas fa-check-circle mr-2"></i>Password berhasil diperbarui
                        </div>
                    @endif

                    @if ($errors->updatePassword->any())
                        <div class="rounded-lg bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-400 text-sm px-4 py-3">
                            <ul class="list-disc pl-5 space-y-1">
                                @foreach ($errors->updatePassword->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="flex items-center justify-end pt-2">
                        <button type="submit" class="inline-flex items-center justify-center px-5 py-2.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:ring-offset-gray-800 transition">
                            <i class="fas fa-lock mr-2"></i>Perbarui Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
