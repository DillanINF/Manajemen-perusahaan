@extends('layouts.app')

@section('title', 'Data Customer')

@push('styles')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
  .font-inter{font-family:'Inter',ui-sans-serif,system-ui,-apple-system,'Segoe UI',Roboto,Helvetica,Arial,'Apple Color Emoji','Segoe UI Emoji'}
  .hover-card:hover{box-shadow:0 10px 20px -10px rgba(2,6,23,0.2)}
  .badge{display:inline-flex;align-items:center;border-radius:9999px;padding:.25rem .5rem;font-weight:600;font-size:.75rem}
  .badge-blue{background:#DBEAFE;color:#1E40AF}
  .badge-purple{background:#EDE9FE;color:#5B21B6}
  .divider{border-color:rgba(17,24,39,.08)}
</style>
@endpush

@section('content')
<div class="w-full px-4 md:px-6 lg:px-8 py-6 font-inter">
    <!-- Header - White, clean, consistent with dashboard -->
    <div class="bg-white dark:bg-slate-800 border border-gray-200 dark:border-white/10 rounded-xl shadow-lg p-4 md:p-6 mb-6">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
            <div class="flex items-center gap-3">
                <div class="h-10 w-10 rounded-full bg-blue-50 dark:bg-slate-700 flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-xl md:text-3xl font-bold text-gray-900 dark:text-slate-100">Data Customer</h1>
                    <p class="text-gray-500 dark:text-slate-400 mt-1 text-sm md:text-base">Kelola data customer perusahaan</p>
                </div>
            </div>
            <button onclick="openAddModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 md:px-6 py-2 md:py-3 rounded-lg font-semibold shadow-md transition-all duration-200 flex items-center justify-center gap-2 text-sm md:text-base w-full sm:w-auto focus:outline-none focus:ring-2 focus:ring-blue-200">
                <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                <span>Tambah Customer</span>
            </button>
        </div>
    </div>

    <!-- Success Alert -->
    @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-400 dark:bg-emerald-900/30 dark:border-emerald-700 p-4 mb-6 rounded-r-lg">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-green-400 dark:text-emerald-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l4-16m2 16l4-16M6 9h14M4 15h14"></path>
                </svg>
                <span class="text-green-700 dark:text-emerald-300 font-medium">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    <!-- Toolbar: Search only (no dropdown) -->
    <div class="bg-white dark:bg-slate-800 border border-gray-200 dark:border-white/10 rounded-xl shadow-sm p-4 mb-4">
        <div class="grid grid-cols-1 gap-3">
            <div>
                <label class="sr-only" for="searchInput">Cari</label>
                <div class="relative">
                    <input id="searchInput" type="text" placeholder="Cari customer... (nama atau alamat)" class="w-full pl-10 pr-3 py-2 rounded-lg border border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-gray-700 dark:text-slate-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
                    <span class="absolute left-3 top-2.5 text-gray-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M10 18a8 8 0 100-16 8 8 0 000 16z"></path>
                        </svg>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table (desktop/tablet) -->
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-lg overflow-hidden hidden md:block">
        <div class="overflow-x-auto" id="tableContainer">
            <table class="min-w-full table-auto" id="customerTable">
                <thead class="bg-gray-50 dark:bg-slate-700/60 text-gray-700 dark:text-slate-200">
                    <tr>
                        <th class="px-3 md:px-6 py-3 md:py-4 text-left font-semibold text-xs md:text-sm border-b divider">
                            <div class="flex items-center space-x-1 md:space-x-2">
                                <svg class="w-3 h-3 md:w-4 md:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"></path>
                                </svg>
                                <span>No</span>
                            </div>
                        </th>
                        <th class="px-3 md:px-6 py-3 md:py-4 text-left font-semibold text-xs md:text-sm min-w-[150px] border-b divider">
                            <div class="flex items-center space-x-1 md:space-x-2">
                                <svg class="w-3 h-3 md:w-4 md:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                <span>Nama Customer</span>
                            </div>
                        </th>
                        <th class="px-3 md:px-6 py-3 md:py-4 text-left font-semibold text-xs md:text-sm min-w-[140px] border-b divider">
                            <div class="flex items-center space-x-1 md:space-x-2">
                                <svg class="w-3 h-3 md:w-4 md:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M3 12h18M3 17h18" />
                                </svg>
                                <span>Kode Number</span>
                            </div>
                        </th>
                        <th class="px-3 md:px-6 py-3 md:py-4 text-left font-semibold text-xs md:text-sm min-w-[180px] border-b divider">
                            <div class="flex items-center space-x-1 md:space-x-2">
                                <svg class="w-3 h-3 md:w-4 md:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0z" />
                                </svg>
                                <span>Email</span>
                            </div>
                        </th>
                        <th class="px-3 md:px-6 py-3 md:py-4 text-left font-semibold text-xs md:text-sm min-w-[120px] border-b divider">
                            <div class="flex items-center space-x-1 md:space-x-2">
                                <span>Telepon</span>
                            </div>
                        </th>
                        <th class="px-3 md:px-6 py-3 md:py-4 text-left font-semibold text-xs md:text-sm min-w-[120px] border-b divider">
                            <div class="flex items-center space-x-1 md:space-x-2">
                                <span>Terms</span>
                            </div>
                        </th>
                        <!-- Made address columns responsive with min-width -->
                        <th class="px-3 md:px-6 py-3 md:py-4 text-left font-semibold text-xs md:text-sm min-w-[120px] border-b divider">
                            <div class="flex items-center space-x-1 md:space-x-2">
                                <svg class="w-3 h-3 md:w-4 md:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                <span>Alamat 1</span>
                            </div>
                        </th>
                        <th class="px-3 md:px-6 py-3 md:py-4 text-left font-semibold text-xs md:text-sm min-w-[120px] border-b divider">
                            <div class="flex items-center space-x-1 md:space-x-2">
                                <svg class="w-3 h-3 md:w-4 md:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                <span>Alamat 2</span>
                            </div>
                        </th>
                        <th class="px-3 md:px-6 py-3 md:py-4 text-center font-semibold text-xs md:text-sm min-w-[140px] border-b divider">
                            <div class="flex items-center justify-center space-x-1 md:space-x-2">
                                <svg class="w-3 h-3 md:w-4 md:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4"></path>
                                </svg>
                                <span>Aksi</span>
                            </div>
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-slate-700">
                    @forelse($customers as $index => $customer)
                        <tr class="hover:bg-blue-50/70 dark:hover:bg-slate-700 transition-colors duration-200" data-name="{{ Str::lower($customer->name) }}" data-address="{{ Str::lower(($customer->address_1 ?? '') . ' ' . ($customer->address_2 ?? '')) }}">
                            <td class="px-3 md:px-6 py-3 md:py-4 text-gray-900 dark:text-slate-200 font-medium text-sm">{{ $index + 1 }}</td>
                            <td class="px-3 md:px-6 py-3 md:py-4">
                                <div class="flex items-center space-x-2 md:space-x-3">
                                    <div class="bg-blue-50 dark:bg-slate-700 h-8 w-8 rounded-full flex items-center justify-center">
                                        <svg class="w-4 h-4 text-blue-600 dark:text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                    </div>
                                    <span class="text-gray-900 dark:text-slate-200 font-medium text-sm">{{ $customer->name }}</span>
                                </div>
                            </td>
                            <td class="px-3 md:px-6 py-3 md:py-4">
                                <span class="bg-indigo-100 dark:bg-indigo-900/40 text-indigo-800 dark:text-indigo-200 px-2 md:px-3 py-1 rounded-full text-xs font-medium">
                                    {{ $customer->code_number ?? '-' }}
                                </span>
                            </td>
                            <td class="px-3 md:px-6 py-3 md:py-4">
                                <span class="bg-gray-100 dark:bg-slate-700 text-gray-800 dark:text-slate-200 px-2 md:px-3 py-1 rounded-full text-xs font-medium">
                                    {{ $customer->email ?? '-' }}
                                </span>
                            </td>
                            <td class="px-3 md:px-6 py-3 md:py-4">
                                <span class="bg-gray-100 dark:bg-slate-700 text-gray-800 dark:text-slate-200 px-2 md:px-3 py-1 rounded-full text-xs font-medium">
                                    {{ $customer->phone ?? '-' }}
                                </span>
                            </td>
                            <td class="px-3 md:px-6 py-3 md:py-4">
                                <span class="bg-amber-50 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300 px-2 md:px-3 py-1 rounded-full text-xs font-medium">
                                    {{ $customer->payment_terms_days ? ($customer->payment_terms_days . ' hari') : '-' }}
                                </span>
                            </td>
                            
                            <td class="px-3 md:px-6 py-3 md:py-4">
                                <span class="bg-gray-100 dark:bg-slate-700 text-gray-800 dark:text-slate-200 px-2 md:px-3 py-1 rounded-full text-xs font-medium">
                                    {{ $customer->address_1 ?? '-' }}
                                </span>
                            </td>
                            <td class="px-3 md:px-6 py-3 md:py-4">
                                <span class="bg-gray-100 dark:bg-slate-700 text-gray-800 dark:text-slate-200 px-2 md:px-3 py-1 rounded-full text-xs font-medium">
                                    {{ $customer->address_2 ?? '-' }}
                                </span>
                            </td>
                            <td class="px-3 md:px-6 py-3 md:py-4">
                                <x-table.action-buttons 
                                    onEdit="window.openEditModal({{ $customer->id }}, {!! json_encode($customer->name) !!}, {!! json_encode($customer->email) !!}, {!! json_encode($customer->phone) !!}, {!! json_encode($customer->address_1) !!}, {!! json_encode($customer->address_2) !!}, {!! json_encode($customer->code_number) !!}, {!! json_encode($customer->payment_terms_days) !!})"
                                    deleteAction="{{ route('customer.destroy', $customer->id) }}"
                                    confirmText="Yakin ingin menghapus customer ini?"
                                />
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center space-y-4">
                                    <div class="bg-gray-100 dark:bg-slate-700 p-6 rounded-full">
                                        <svg class="w-12 h-12 text-gray-400 dark:text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20l4-16m2 16l4-16M6 9h14M4 15h14"></path>
                                        </svg>
                                    </div>
                                    <div class="text-center">
                                        <h3 class="text-lg font-semibold text-gray-900 dark:text-slate-200 mb-2" style="font-style: normal;">Belum ada customer</h3>
                                        <p class="text-gray-500 dark:text-slate-400" style="font-style: normal;">Mulai dengan menambahkan customer pertama Anda.</p>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Mobile Card List View -->
    <div class="md:hidden space-y-3">
        @forelse($customers as $index => $customer)
            <div class="bg-white dark:bg-slate-800 border border-gray-200 dark:border-white/10 rounded-xl shadow-sm p-4 hover-card" data-name="{{ Str::lower($customer->name) }}" data-address="{{ Str::lower(($customer->address_1 ?? '') . ' ' . ($customer->address_2 ?? '')) }}">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="inline-flex items-center justify-center h-6 w-6 rounded-full bg-blue-50 text-blue-600 text-xs font-semibold">{{ $index + 1 }}</span>
                            <span class="text-gray-900 dark:text-slate-100 font-semibold">{{ $customer->name }}</span>
                        </div>
                        <div class="mt-2 space-y-1 text-sm">
                            <div class="text-gray-700 dark:text-slate-300">Email: <span class="font-medium">{{ $customer->email ?? '-' }}</span></div>
                            <div class="text-gray-700 dark:text-slate-300">Alamat 1: <span class="font-medium">{{ $customer->address_1 ?? '-' }}</span></div>
                            <div class="text-gray-700 dark:text-slate-300">Alamat 2: <span class="font-medium">{{ $customer->address_2 ?? '-' }}</span></div>
                        </div>
                    </div>
                    <div class="flex flex-col gap-2">
                        <x-table.action-buttons 
                            onEdit="window.openEditModal({{ $customer->id }}, {!! json_encode($customer->name) !!}, {!! json_encode($customer->email) !!}, {!! json_encode($customer->phone) !!}, {!! json_encode($customer->address_1) !!}, {!! json_encode($customer->address_2) !!}, {!! json_encode($customer->code_number) !!}, {!! json_encode($customer->payment_terms_days) !!})"
                            deleteAction="{{ route('customer.destroy', $customer->id) }}"
                            confirmText="Yakin ingin menghapus customer ini?"
                        />
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white dark:bg-slate-800 border border-gray-200 dark:border-white/10 rounded-xl shadow-sm p-6 text-center">
                <p class="text-gray-600 dark:text-slate-300" style="font-style: normal;">Belum ada customer</p>
            </div>
        @endforelse
    </div>
</div>



<!-- Add Customer Modal - Responsive -->
<div id="addModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm dark:bg-black/80 hidden overflow-y-auto h-full w-full z-50 flex items-center justify-center p-4 transition-all duration-300 opacity-0">
    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl w-full max-h-[90vh] overflow-y-auto border border-gray-200 dark:border-slate-700 transform scale-95 translate-y-4 transition-all duration-300" style="max-width: 28rem !important;">
        <!-- Header putih sederhana (sticky) -->
        <div class="sticky top-0 bg-white dark:bg-slate-800 border-b border-gray-200 dark:border-slate-700 px-6 py-5 rounded-t-2xl">
            <div class="flex justify-between items-center">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-blue-50 dark:bg-slate-700 rounded-xl flex items-center justify-center">
                        <i class="fa-solid fa-user-plus text-blue-600 dark:text-blue-300"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-slate-100">Tambah Customer</h3>
                        <p class="text-gray-600 dark:text-slate-400 text-sm">Lengkapi informasi customer baru</p>
                    </div>
                </div>
                <button onclick="closeAddModal()" 
                        class="w-10 h-10 rounded-lg text-gray-500 hover:bg-gray-100 dark:text-slate-300 dark:hover:bg-slate-700 transition-all duration-200 flex items-center justify-center">
                    <i class="fa-solid fa-times text-lg"></i>
                </button>
            </div>
        </div>
        
        <form id="addCustomerForm" action="{{ route('customer.store') }}" method="POST" class="p-6">
            @csrf
            <div class="space-y-3">
                <div>
                    <label for="add_name" class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">Nama Customer</label>
                    <input type="text" id="add_name" name="name" required
                           class="w-full px-3 py-2 border border-gray-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 placeholder-gray-400 dark:placeholder-slate-500 rounded-lg focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 transition-colors duration-200 text-sm"
                           placeholder="Masukkan nama customer">
                    <p class="text-xs text-gray-500 dark:text-slate-400 mt-1">💡 Nama customer boleh sama/duplikat</p>
                </div>

                <div>
                    <label for="add_email" class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">Email Perusahaan (Opsional)</label>
                    <input type="email" id="add_email" name="email"
                           class="w-full px-3 py-2 border border-gray-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 placeholder-gray-400 dark:placeholder-slate-500 rounded-lg focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 transition-colors duration-200 text-sm"
                           placeholder="contoh: finance@perusahaan.com">
                </div>
                <div>
                    <label for="add_phone" class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">Telepon (Opsional)</label>
                    <input type="text" id="add_phone" name="phone"
                           class="w-full px-3 py-2 border border-gray-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 placeholder-gray-400 dark:placeholder-slate-500 rounded-lg focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 transition-colors duration-200 text-sm"
                           placeholder="contoh: 021-xxxxxxx / 08xx-xxxx-xxxx">
                </div>

                <!-- Kode Number (2 input: BAG1-BAG2) -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">Kode Number</label>
                    <div class="flex items-center gap-2">
                        <input type="text" id="add_code_1" class="w-20 px-3 py-2 border border-gray-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 rounded-lg text-sm" placeholder="XXX">
                        <span class="text-gray-500 dark:text-slate-400">-</span>
                        <input type="text" id="add_code_2" class="w-24 px-3 py-2 border border-gray-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 rounded-lg text-sm" placeholder="YYY">
                    </div>
                    <!-- Hidden field untuk gabungan -->
                    <input type="hidden" name="code_number" id="add_code_number" value="">
                    <p class="text-xs text-gray-500 dark:text-slate-400 mt-1">Format: BAG1-BAG2</p>
                </div>

                

                <div>
                    <label for="add_payment_terms_days" class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">Terms Pembayaran (Hari)</label>
                    <div class="relative">
                        <input type="number" id="add_payment_terms_days" name="payment_terms_days" min="1" max="365" value="30"
                               class="w-full px-3 py-2 pr-12 border border-gray-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 placeholder-gray-400 dark:placeholder-slate-500 rounded-lg focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 transition-colors duration-200 text-sm"
                               placeholder="30">
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                            <span class="text-gray-500 dark:text-slate-400 text-sm">hari</span>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-slate-400 mt-1">Default jangka waktu pembayaran untuk customer ini</p>
                </div>

                <div class="space-y-2">
                    <div>
                        <label for="add_address_1" class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">Alamat 1</label>
                        <input type="text" id="add_address_1" name="address_1"
                               class="w-full px-3 py-2 border border-gray-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 placeholder-gray-400 dark:placeholder-slate-500 rounded-lg focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 transition-colors duration-200 text-sm"
                               placeholder="Masukkan alamat 1">
                    </div>

                    <div>
                        <label for="add_address_2" class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">Alamat 2</label>
                        <input type="text" id="add_address_2" name="address_2"
                               class="w-full px-3 py-2 border border-gray-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 placeholder-gray-400 dark:placeholder-slate-500 rounded-lg focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 transition-colors duration-200 text-sm"
                               placeholder="Masukkan alamat 2">
                    </div>
                </div>
            </div>
            
            <!-- Made button section responsive -->
            <div class="flex flex-col sm:flex-row justify-end gap-2 sm:gap-3 mt-4 pt-3 border-t border-gray-200 dark:border-slate-700">
                <button type="button" onclick="closeAddModal()" 
                        class="px-4 py-2 text-gray-700 dark:text-slate-200 bg-gray-100 dark:bg-slate-700 hover:bg-gray-200 dark:hover:bg-slate-600 rounded-lg font-medium transition-colors duration-200 text-sm order-2 sm:order-1">
                    Batal
                </button>
                <button type="submit" id="addSubmitBtn"
                        class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors duration-200 flex items-center justify-center space-x-2 text-sm order-1 sm:order-2 focus:outline-none focus:ring-2 focus:ring-blue-200">
                    <span>Simpan</span>
                    <div id="addLoading" class="hidden">
                        <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </div>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Customer Modal - Responsive -->
<div id="editModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white dark:bg-slate-800 rounded-lg shadow-xl w-full mx-auto max-h-[90vh] overflow-y-auto"
         style="max-width: 560px;">
        <div class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-slate-700">
            <h3 class="text-base md:text-lg font-semibold text-gray-900 dark:text-slate-100 flex items-center space-x-2">
                <svg class="w-4 h-4 md:w-5 md:h-5 text-blue-600 dark:text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                <span>Edit Customer</span>
            </h3>
            <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-slate-300 transition-colors duration-200">
                <svg class="w-5 h-5 md:w-6 md:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        
        <form id="editCustomerForm" method="POST" class="p-6">
            @csrf
            @method('PUT')
            <div class="space-y-6">
                <div class="space-y-2">
                    <label for="edit_name" class="flex items-center space-x-2 text-sm font-medium text-gray-700 dark:text-slate-300">
                        <i class="fa-solid fa-user text-blue-500"></i>
                        <span>Nama Customer</span>
                    </label>
                    <input type="text" id="edit_name" name="name" required
                           class="w-full px-4 py-3 border border-gray-300 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 placeholder-gray-400 dark:placeholder-slate-500 rounded-xl focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 transition-all duration-200"
                           placeholder="Masukkan nama customer">
                    <p class="text-xs text-gray-500 dark:text-slate-400 mt-1">💡 Nama customer boleh sama/duplikat</p>
                </div>

                <!-- Kode Number (Edit) -->
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300">Kode Number</label>
                    <div class="flex items-center gap-2">
                        <input type="text" id="edit_code_1" class="w-20 px-3 py-2 border border-gray-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 rounded-lg text-sm" placeholder="XXX">
                        <span class="text-gray-500 dark:text-slate-400">-</span>
                        <input type="text" id="edit_code_2" class="w-24 px-3 py-2 border border-gray-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 rounded-lg text-sm" placeholder="YYY">
                    </div>
                    <input type="hidden" name="code_number" id="edit_code_number" value="">
                </div>

                <div class="space-y-2">
                    <label for="edit_email" class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">Email Perusahaan</label>
                    <input type="email" id="edit_email" name="email"
                           class="w-full px-3 py-2 border border-gray-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 placeholder-gray-400 dark:placeholder-slate-500 rounded-lg focus:ring-2 focus:ring-green-500 dark:focus:ring-green-400 focus:border-green-500 transition-colors duration-200 text-sm"
                           placeholder="contoh: finance@perusahaan.com">
                </div>
                
                <div class="space-y-2">
                    <div>
                        <label for="edit_phone" class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">Telepon (Opsional)</label>
                        <input type="text" id="edit_phone" name="phone"
                               class="w-full px-3 py-2 border border-gray-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 placeholder-gray-400 dark:placeholder-slate-500 rounded-lg focus:ring-2 focus:ring-green-500 dark:focus:ring-green-400 focus:border-green-500 transition-colors duration-200 text-sm"
                               placeholder="contoh: 021-xxxxxxx / 08xx-xxxx-xxxx">
                    </div>
                    <div>
                        <label for="edit_address_1" class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">Alamat 1</label>
                        <input type="text" id="edit_address_1" name="address_1"
                               class="w-full px-3 py-2 border border-gray-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 placeholder-gray-400 dark:placeholder-slate-500 rounded-lg focus:ring-2 focus:ring-green-500 dark:focus:ring-green-400 focus:border-green-500 transition-colors duration-200 text-sm"
                               placeholder="Masukkan alamat 1">
                    </div>
                    <div>
                        <label for="edit_address_2" class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">Alamat 2</label>
                        <input type="text" id="edit_address_2" name="address_2"
                               class="w-full px-3 py-2 border border-gray-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 placeholder-gray-400 dark:placeholder-slate-500 rounded-lg focus:ring-2 focus:ring-green-500 dark:focus:ring-green-400 focus:border-green-500 transition-colors duration-200 text-sm"
                               placeholder="Masukkan alamat 2">
                    </div>
                    <div>
                        <label for="edit_payment_terms_days" class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">Terms Pembayaran (Hari)</label>
                        <div class="relative">
                            <input type="number" id="edit_payment_terms_days" name="payment_terms_days" min="1" max="365"
                                   class="w-full px-3 py-2 pr-12 border border-gray-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 placeholder-gray-400 dark:placeholder-slate-500 rounded-lg focus:ring-2 focus:ring-green-500 dark:focus:ring-green-400 focus:border-green-500 transition-colors duration-200 text-sm"
                                   placeholder="30">
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <span class="text-gray-500 dark:text-slate-400 text-sm">hari</span>
                            </div>
                        </div>
                        <p class="text-xs text-gray-500 dark:text-slate-400 mt-1">Atur jangka waktu pembayaran untuk customer ini</p>
                    </div>
                </div>
            </div>
            
            <div class="flex flex-col sm:flex-row justify-end gap-2 sm:gap-3 mt-4 pt-3 border-t border-gray-200 dark:border-slate-700">
                <button type="button" onclick="closeEditModal()" 
                        class="px-4 py-2 text-gray-700 dark:text-slate-200 bg-gray-100 dark:bg-slate-700 hover:bg-gray-200 dark:hover:bg-slate-600 rounded-lg font-medium transition-colors duration-200 text-sm order-2 sm:order-1">
                    Batal
                </button>
                <button type="submit" id="editSubmitBtn"
                        class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors duration-200 flex items-center justify-center space-x-2 text-sm order-1 sm:order-2 focus:outline-none focus:ring-2 focus:ring-blue-200">
                    <span>Update</span>
                    <div id="editLoading" class="hidden">
                        <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </div>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Modal functions dengan animasi
function openAddModal() {
    const modal = document.getElementById('addModal');
    const modalContent = modal.querySelector('div');
    
    // Tampilkan modal
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    document.body.style.overflow = 'hidden';
    
    // Animasi masuk
    setTimeout(() => {
        modal.classList.remove('opacity-0');
        modal.classList.add('opacity-100');
        modalContent.classList.remove('scale-95', 'translate-y-4');
        modalContent.classList.add('scale-100', 'translate-y-0');
    }, 10);
    
    // Focus ke input pertama
    setTimeout(() => {
        document.getElementById('add_name').focus();
    }, 300);
}

function closeAddModal() {
    const modal = document.getElementById('addModal');
    const modalContent = modal.querySelector('div');
    
    // Animasi keluar
    modal.classList.remove('opacity-100');
    modal.classList.add('opacity-0');
    modalContent.classList.remove('scale-100', 'translate-y-0');
    modalContent.classList.add('scale-95', 'translate-y-4');
    
    // Sembunyikan modal setelah animasi selesai
    setTimeout(() => {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = '';
        document.getElementById('addCustomerForm').reset();
    }, 300);
}

function openEditModal(id, name, email, phone, address1, address2, codeNumber, paymentTermsDays) {
    document.getElementById('editModal').classList.remove('hidden');
    document.getElementById('editModal').classList.add('flex');
    // Set action menggunakan route Laravel yang benar
    const form = document.getElementById('editCustomerForm');
    if (!form) {
        console.warn('editCustomerForm tidak ditemukan');
        return;
    }
    // Gunakan route Laravel untuk customer.update
    const baseUrl = "{{ route('customer.index') }}";
    form.action = baseUrl + '/' + id;
    const nameEl = document.getElementById('edit_name');
    if (nameEl) nameEl.value = name;
    const emailEl = document.getElementById('edit_email');
    if (emailEl) emailEl.value = email || '';
    const phoneEl = document.getElementById('edit_phone');
    if (phoneEl) phoneEl.value = phone || '';
    const addr1El = document.getElementById('edit_address_1');
    if (addr1El) addr1El.value = address1 || '';
    const addr2El = document.getElementById('edit_address_2');
    if (addr2El) addr2El.value = address2 || '';
    const termsEl = document.getElementById('edit_payment_terms_days');
    if (termsEl) termsEl.value = paymentTermsDays || 30;
    // Prefill Kode Number jika ada (format: PART1-PART2)
    try {
        const ec1 = document.getElementById('edit_code_1');
        const ec2 = document.getElementById('edit_code_2');
        const hidden = document.getElementById('edit_code_number');
        const str = (codeNumber || '').toString();
        if (str) {
            const [part1 = '', part2 = ''] = str.split('-');
            if (ec1) ec1.value = part1 || '';
            if (ec2) ec2.value = part2 || '';
            if (hidden) hidden.value = [part1, part2].filter(Boolean).join('-');
        } else {
            if (ec1) ec1.value = '';
            if (ec2) ec2.value = '';
            if (hidden) hidden.value = '';
        }
    } catch (e) { console.warn('Gagal set kode number:', e); }
    if (nameEl) nameEl.focus();
}

function closeEditModal() {
    document.getElementById('editModal').classList.add('hidden');
    document.getElementById('editModal').classList.remove('flex');
    document.getElementById('editCustomerForm').reset();
}

// Form submissions with loading states
document.getElementById('addCustomerForm').addEventListener('submit', function() {
    document.getElementById('addSubmitBtn').disabled = true;
    document.getElementById('addLoading').classList.remove('hidden');
    // Set gabungan code_number saat submit
    if (window.combineAddCodeNumber) window.combineAddCodeNumber();
});

document.getElementById('editCustomerForm').addEventListener('submit', function(e) {
    e.preventDefault(); // Prevent default form submission
    
    // Combine code number sebelum submit
    if (window.combineEditCodeNumber) window.combineEditCodeNumber();
    
    const form = this;
    const submitBtn = document.getElementById('editSubmitBtn');
    const loadingSpinner = document.getElementById('editLoading');
    
    // Disable button dan show loading
    submitBtn.disabled = true;
    loadingSpinner.classList.remove('hidden');
    
    // Get form data
    const formData = new FormData(form);
    
    // Debug log
    console.log('Form edit submit via AJAX:', {
        action: form.action,
        data: Object.fromEntries(formData)
    });
    
    // Submit via AJAX
    fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(err => {
                throw new Error(err.message || 'Update gagal');
            });
        }
        return response.json();
    })
    .then(data => {
        console.log('Update berhasil:', data);
        // Show success message
        alert('Customer berhasil diperbarui!');
        // Close modal
        closeEditModal();
        // Reload page to show updated data
        window.location.reload();
    })
    .catch(error => {
        console.error('Error update customer:', error);
        alert('Gagal update customer: ' + error.message);
    })
    .finally(() => {
        // Re-enable button dan hide loading
        submitBtn.disabled = false;
        loadingSpinner.classList.add('hidden');
    });
});

// Close modal when clicking outside
document.getElementById('addModal').addEventListener('click', function(e) {
    if (e.target !== this) return;
    try {
        const sel = window.getSelection ? String(window.getSelection().toString()) : '';
        if (sel && sel.length > 0) {
            e.preventDefault();
            return; // Jangan tutup modal saat ada teks terseleksi (drag keluar)
        }
    } catch (_) {}
    closeAddModal();
});

document.getElementById('editModal').addEventListener('click', function(e) {
    if (e.target !== this) return;
    try {
        const sel = window.getSelection ? String(window.getSelection().toString()) : '';
        if (sel && sel.length > 0) {
            e.preventDefault();
            return; // Jangan tutup modal saat ada teks terseleksi (kasus drag keluar)
        }
    } catch (_) {}
    closeEditModal();
});

// Close modal with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeAddModal();
        closeEditModal();
    }
});

// Pastikan fungsi tersedia di global scope untuk inline onclick
window.openEditModal = openEditModal;
window.closeEditModal = closeEditModal;

// Responsive table scroll indicator
window.addEventListener('DOMContentLoaded', function() {
    const tableContainer = document.getElementById('tableContainer');
    const table = document.getElementById('customerTable');
    const searchInput = document.getElementById('searchInput');
    const mobileCards = document.querySelectorAll('.md\\:hidden .hover-card');

    function checkScroll() {
        if (tableContainer.scrollWidth > tableContainer.clientWidth) {
            tableContainer.classList.add('shadow-inner');
        } else {
            tableContainer.classList.remove('shadow-inner');
        }
    }

    checkScroll();
    window.addEventListener('resize', checkScroll);

    function matchFilter(name, address, query) {
        if (!query) return true;
        query = query.toLowerCase();
        return name.includes(query) || address.includes(query);
    }

    function applyFilter() {
        const q = (searchInput?.value || '').toLowerCase().trim();

        // Table rows
        document.querySelectorAll('#customerTable tbody tr').forEach(tr => {
            const name = (tr.dataset.name || '').toLowerCase();
            const address = (tr.dataset.address || '').toLowerCase();
            const visible = matchFilter(name, address, q);
            tr.classList.toggle('hidden', !visible);
        });

        // Mobile cards
        document.querySelectorAll('.md\\:hidden .hover-card').forEach(card => {
            const name = (card.dataset.name || '').toLowerCase();
            const address = (card.dataset.address || '').toLowerCase();
            const visible = matchFilter(name, address, q);
            card.classList.toggle('hidden', !visible);
        });
    }

    if (searchInput) {
        searchInput.addEventListener('input', applyFilter);
    }
    applyFilter();

    // Listener untuk menyusun kode number (Add)
    const ac1 = document.getElementById('add_code_1');
    const ac2 = document.getElementById('add_code_2');
    const addHidden = document.getElementById('add_code_number');
    function combineAdd() {
        if (!addHidden) return;
        const p1 = (ac1?.value || '').trim();
        const p2 = (ac2?.value || '').trim();
        addHidden.value = [p1, p2].filter(Boolean).join('-');
    }
    window.combineAddCodeNumber = combineAdd;
    [ac1, ac2].forEach(el => el && el.addEventListener('input', combineAdd));

    // Listener untuk menyusun kode number (Edit)
    const ec1 = document.getElementById('edit_code_1');
    const ec2 = document.getElementById('edit_code_2');
    const editHidden = document.getElementById('edit_code_number');
    function combineEdit() {
        if (!editHidden) return;
        const p1 = (ec1?.value || '').trim();
        const p2 = (ec2?.value || '').trim();
        editHidden.value = [p1, p2].filter(Boolean).join('-');
    }
    window.combineEditCodeNumber = combineEdit;
    [ec1, ec2].forEach(el => el && el.addEventListener('input', combineEdit));
});

// Delegasi klik: pastikan tombol Edit selalu memanggil handler
document.addEventListener('click', function(e) {
    const btn = e.target.closest('.js-edit-btn');
    if (!btn) return;
    const call = btn.getAttribute('data-editcall');
    if (!call) return;
    try {
        // Eksekusi ekspresi handler yang sudah dibentuk di Blade (mis. window.openEditModal(...))
        new Function(call)();
    } catch (err) {
        console.error('Gagal menjalankan handler edit:', err);
    }
});
</script>
@endsection
