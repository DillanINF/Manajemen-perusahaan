@extends('layouts.app')
@section('title', 'Data Barang')

@section('content')
<div class="w-full px-2 sm:px-4 lg:px-8 py-4 sm:py-6">
    <!-- Header: white card style like Customer -->
    <div class="bg-white dark:bg-slate-800 border border-gray-200 dark:border-white/10 rounded-xl shadow-md p-4 sm:p-6 mb-4 sm:mb-6">
        <!-- Made header responsive with flex-col on mobile, flex-row on larger screens -->
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center space-y-4 sm:space-y-0">
            <div class="flex items-center space-x-3">
                <!-- Product Icon -->
                <div class="bg-red-50 dark:bg-slate-700 p-2 sm:p-3 rounded-full">
                    <svg class="w-6 h-6 sm:w-8 sm:h-8 text-red-600 dark:text-red-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-900 dark:text-slate-100">Data Barang</h1>
                    <p class="text-gray-500 dark:text-slate-400 mt-1 text-sm sm:text-base">Kelola data Barang perusahaan</p>
                </div>
            </div>
            <!-- Primary buttons -->
            <div class="w-full sm:w-auto flex flex-col sm:flex-row gap-2 sm:gap-3">
                <button onclick="openAddModal()" class="w-full sm:w-auto bg-blue-600 hover:bg-blue-700 text-white dark:bg-blue-600 dark:hover:bg-blue-500 px-4 sm:px-6 py-3 rounded-lg font-semibold shadow-md transition-all duration-200 flex items-center justify-center space-x-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    <span>Tambah Barang</span>
                </button>
                <a href="{{ route('barang.masuk.create') }}" class="w-full sm:w-auto bg-emerald-600 hover:bg-emerald-700 text-white px-4 sm:px-6 py-3 rounded-lg font-semibold shadow-md transition-all duration-200 flex items-center justify-center space-x-2 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5m0 0l5-5m-5 5V4"/></svg>
                    <span>Barang Masuk</span>
                </a>
                <a href="{{ route('barang.keluar.create') }}" class="w-full sm:w-auto bg-rose-600 hover:bg-rose-700 text-white px-4 sm:px-6 py-3 rounded-lg font-semibold shadow-md transition-all duration-200 flex items-center justify-center space-x-2 focus:outline-none focus:ring-2 focus:ring-rose-500">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 8v-2a2 2 0 00-2-2H6a2 2 0 00-2 2v2m13 6l-5-5m0 0l-5 5m5-5v12"/></svg>
                    <span>Barang Keluar</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Success Alert -->
    @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-400 dark:bg-emerald-900/30 dark:border-emerald-700 p-3 sm:p-4 mb-4 sm:mb-6 rounded-r-lg">
            <div class="flex items-center">
                <svg class="w-4 h-4 sm:w-5 sm:h-5 text-green-400 dark:text-emerald-400 mr-2 sm:mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="text-green-700 dark:text-emerald-300 font-medium text-sm sm:text-base">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    

    <!-- Error Alert -->
    @if(session('error'))
        <div class="bg-red-50 border-l-4 border-red-500 dark:bg-red-900/30 dark:border-red-700 p-3 sm:p-4 mb-4 sm:mb-6 rounded-r-lg">
            <div class="flex items-center">
                <svg class="w-4 h-4 sm:w-5 sm:h-5 text-red-500 dark:text-red-300 mr-2 sm:mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="text-red-700 dark:text-red-300 font-medium text-sm sm:text-base">{{ session('error') }}</span>
            </div>
        </div>
    @endif

    @if ($errors->any())
        <div class="bg-red-50 border-l-4 border-red-500 dark:bg-red-900/30 dark:border-red-700 p-3 sm:p-4 mb-4 sm:mb-6 rounded-r-lg">
            <div class="flex items-start space-x-2">
                <svg class="w-5 h-5 text-red-600 dark:text-red-300 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <div>
                    <p class="text-red-700 dark:text-red-300 font-semibold mb-1">Terjadi kesalahan pada input:</p>
                    <ul class="list-disc ml-5 text-red-700 dark:text-red-300 text-sm sm:text-base space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <!-- Toolbar: Search only (no dropdown) -->
    <div class="bg-white dark:bg-slate-800 border border-gray-200 dark:border-white/10 rounded-xl shadow-sm p-3 sm:p-4 mb-4 sm:mb-6">
        <div class="relative">
            <input id="searchInputProduk" type="text" placeholder="Cari Barang... (nama)" class="w-full pl-10 pr-3 py-2 rounded-lg border border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-gray-700 dark:text-slate-100 focus:ring-2 focus:ring-red-500 focus:border-red-500" />
            <span class="absolute left-3 top-2.5 text-gray-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M10 18a8 8 0 100-16 8 8 0 000 16z"></path>
                </svg>
            </span>
        </div>
    </div>

    <!-- Data Table Card -->
    <div class="bg-white dark:bg-slate-800 rounded-lg shadow-lg overflow-hidden">
        <!-- Added scroll indicator and improved mobile table handling -->
        <div class="relative">
            <div class="overflow-x-auto scrollbar-thin scrollbar-thumb-gray-300 scrollbar-track-gray-100">
                <!-- Added scroll indicator for mobile -->
                <div class="sm:hidden bg-gray-50 dark:bg-slate-800 px-4 py-2 text-xs text-gray-500 dark:text-slate-400 border-b dark:border-slate-700">
                    <div class="flex items-center space-x-1">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16l-4-4m0 0l4-4m-4 4h18"></path>
                        </svg>
                        <span>Geser untuk melihat lebih banyak</span>
                    </div>
                </div>
                <table class="min-w-full table-auto" id="produkTable">
                    <thead class="bg-gray-50 dark:bg-slate-700 text-gray-600 dark:text-slate-200">
                        <tr>
                            <!-- Made table headers responsive with smaller padding on mobile -->
                            <th class="px-3 sm:px-6 py-3 sm:py-4 text-left font-semibold text-xs sm:text-sm">
                                <div class="flex items-center space-x-1 sm:space-x-2">
                                    <svg class="w-3 h-3 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"></path>
                                    </svg>
                                    <span>No</span>
                                </div>
                            </th>
                            <th class="px-3 sm:px-6 py-3 sm:py-4 text-left font-semibold text-xs sm:text-sm min-w-[150px]">
                                <div class="flex items-center space-x-1 sm:space-x-2">
                                    <svg class="w-3 h-3 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                    </svg>
                                    <span>Nama Produk</span>
                                </div>
                            </th>
                            <th class="px-3 sm:px-6 py-3 sm:py-4 text-left font-semibold text-xs sm:text-sm min-w-[120px]">
                                <div class="flex items-center space-x-1 sm:space-x-2">
                                    <svg class="w-3 h-3 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                    </svg>
                                    <span>Harga</span>
                                </div>
                            </th>
                            <th class="px-3 sm:px-6 py-3 sm:py-4 text-left font-semibold text-xs sm:text-sm min-w-[100px]">
                                <div class="flex items-center space-x-1 sm:space-x-2">
                                    <svg class="w-3 h-3 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3v18h18M7 15l3-3 4 4 4-6" />
                                    </svg>
                                    <span>Sisa Stok</span>
                                </div>
                            </th>
                            <th class="px-3 sm:px-6 py-3 sm:py-4 text-center font-semibold text-xs sm:text-sm min-w-[140px]">
                                <div class="flex items-center justify-center space-x-1 sm:space-x-2">
                                    <svg class="w-3 h-3 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4"></path>
                                    </svg>
                                    <span>Aksi</span>
                                </div>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-slate-700">
                        @forelse($produks as $index => $produk)
                            <tr class="hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors duration-200" data-name="{{ strtolower($produk->nama_produk) }}">
                                <!-- Made table cells responsive with smaller padding on mobile -->
                                <td class="px-3 sm:px-6 py-3 sm:py-4 text-gray-900 dark:text-slate-200 font-medium text-sm sm:text-base">{{ $index + 1 }}</td>
                                <td class="px-3 sm:px-6 py-3 sm:py-4">
                                    <div class="flex items-center space-x-2 sm:space-x-3">
                                        <div class="bg-red-100 dark:bg-slate-700 p-1 sm:p-2 rounded-full flex-shrink-0">
                                            <svg class="w-3 h-3 sm:w-4 sm:h-4 text-red-600 dark:text-red-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                            </svg>
                                        </div>
                                        <span class="text-gray-900 dark:text-slate-200 font-medium text-sm sm:text-base truncate">{{ $produk->nama_produk }}</span>
                                    </div>
                                </td>
                                <td class="px-3 sm:px-6 py-3 sm:py-4">
                                    @php
                                        $displayHarga = ($produk->harga_set ?? 0) > 0 ? ($produk->harga_set) : ($produk->harga_pcs ?? 0);
                                        $labelHarga = ($produk->harga_set ?? 0) > 0 ? 'SET' : 'PCS';
                                    @endphp
                                    <span class="bg-gray-100 dark:bg-slate-700 text-gray-800 dark:text-slate-200 px-2 sm:px-3 py-1 rounded-full text-xs sm:text-sm font-medium whitespace-nowrap">
                                        {{ $displayHarga ? ('Rp ' . number_format($displayHarga, 0, ',', '.') . ' / ' . $labelHarga) : '-' }}
                                    </span>
                                </td>
                                <td class="px-3 sm:px-6 py-3 sm:py-4">
                                    <span class="bg-blue-50 dark:bg-slate-700 text-blue-700 dark:text-slate-200 px-2 sm:px-3 py-1 rounded-full text-xs sm:text-sm font-medium whitespace-nowrap">
                                        {{ number_format($produk->sisa_stok, 0, ',', '.') }}
                                    </span>
                                </td>
                                <td class="px-3 sm:px-6 py-3 sm:py-4 text-center">
                                    <x-table.action-buttons 
                                        onEdit="window.openEditModal({{ $produk->id }}, '{{ addslashes($produk->nama_produk) }}', {{ $produk->harga_pcs ?? 0 }}, {{ $produk->harga_set ?? 0 }})"
                                        deleteAction="{{ route('produk.destroy', $produk->id) }}"
                                        confirmText="Yakin ingin menghapus produk ini?" />
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-3 sm:px-6 py-8 sm:py-12 text-center">
                                    <!-- Made empty state responsive -->
                                    <div class="flex flex-col items-center space-y-3 sm:space-y-4">
                                        <div class="bg-gray-100 dark:bg-slate-700 p-4 sm:p-6 rounded-full">
                                            <svg class="w-8 h-8 sm:w-12 sm:h-12 text-gray-400 dark:text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                            </svg>
                                        </div>
                                        <div class="text-center">
                                            <h3 class="text-base sm:text-lg font-semibold text-gray-900 dark:text-slate-200 mb-2" style="font-style: normal;">Belum ada produk</h3>
                                            <p class="text-gray-500 dark:text-slate-400 text-sm sm:text-base" style="font-style: normal;">Mulai dengan menambahkan produk pertama Anda.</p>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add Produk Modal -->
<div id="addModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm dark:bg-black/80 hidden overflow-y-auto h-full w-full z-50 flex items-center justify-center p-4 transition-all duration-300 opacity-0">
    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl w-full max-h-[90vh] overflow-y-auto border border-gray-200 dark:border-slate-700 transform scale-95 translate-y-4 transition-all duration-300" style="max-width: 28rem !important;">
        <!-- Header putih sederhana (sticky) -->
        <div class="sticky top-0 bg-white dark:bg-slate-800 border-b border-gray-200 dark:border-slate-700 px-6 py-5 rounded-t-2xl">
            <div class="flex justify-between items-center">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-blue-50 dark:bg-slate-700 rounded-xl flex items-center justify-center">
                        <i class="fa-solid fa-box text-blue-600 dark:text-blue-300"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-slate-100">Tambah Barang</h3>
                        <p class="text-gray-600 dark:text-slate-400 text-sm">Lengkapi informasi Barang baru</p>
                    </div>
                </div>
                <button onclick="closeAddModal()" 
                        class="w-10 h-10 rounded-lg text-gray-500 hover:bg-gray-100 dark:text-slate-300 dark:hover:bg-slate-700 transition-all duration-200 flex items-center justify-center">
                    <i class="fa-solid fa-times text-lg"></i>
                </button>
            </div>
        </div>
        
        <!-- Form Content -->
        <form id="addProdukForm" action="{{ route('produk.store') }}" method="POST" class="p-6">
            @csrf
            <div class="space-y-6">
                <!-- Nama Produk -->
                <div class="space-y-2">
                    <label for="add_nama_produk" class="flex items-center space-x-2 text-sm font-medium text-gray-700 dark:text-slate-300">
                        <i class="fa-solid fa-box text-blue-500"></i>
                        <span>Nama Barang</span>
                    </label>
                    <input type="text" id="add_nama_produk" name="nama_produk" required value="{{ old('nama_produk') }}"
                           class="w-full px-4 py-3 border border-gray-300 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 placeholder-gray-400 dark:placeholder-slate-500 rounded-xl focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 transition-all duration-200"
                           placeholder="Masukkan nama Barang">
                </div>
                
                <!-- Tipe Harga + Input Dinamis -->
                <div class="space-y-2">
                    <label class="flex items-center space-x-2 text-sm font-medium text-gray-700 dark:text-slate-300">
                        <i class="fa-solid fa-toggle-on text-blue-500"></i>
                        <span>Tipe Harga</span>
                    </label>
                    <select id="add_price_type" onchange="(function(sel){var lbl=document.getElementById('add_harga_label');var inp=document.getElementById('add_harga');if(!lbl||!inp)return;var isSet=sel.value==='set';lbl.textContent=isSet?'Harga (SET)':'Harga (PCS)';inp.name=isSet?'harga_set':'harga_pcs';inp.placeholder=isSet?'Masukkan harga per SET':'Masukkan harga per PCS';})(this); handleAddPriceTypeChange();" class="w-full px-4 py-3 border border-gray-300 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 rounded-xl focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400">
                        <option value="pcs" {{ old('price_type')==='pcs' ? 'selected' : '' }}>Harga PCS</option>
                        <option value="set" {{ old('price_type')==='set' ? 'selected' : '' }}>Harga SET</option>
                    </select>
                </div>
                
                <!-- Input Harga Dinamis -->
                <div class="space-y-2" id="wrap_add_harga">
                    <label for="add_harga" class="flex items-center space-x-2 text-sm font-medium text-gray-700 dark:text-slate-300">
                        <i class="fa-solid fa-tag text-blue-500"></i>
                        <span id="add_harga_label">Harga (PCS)</span>
                    </label>
                    <input type="number" id="add_harga" name="harga_pcs" value="{{ old('harga_pcs') }}"
                           class="w-full px-4 py-3 border border-gray-300 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 placeholder-gray-400 dark:placeholder-slate-500 rounded-xl focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 transition-all duration-200"
                           placeholder="Masukkan harga per PCS" min="0" required>
                </div>
            </div>
            
            <!-- Action Buttons -->
            <div class="flex justify-end space-x-3 mt-8 pt-6 border-t border-gray-200 dark:border-slate-700">
                <button type="button" onclick="closeAddModal()" 
                        class="px-6 py-3 text-gray-700 dark:text-slate-200 bg-gray-100 dark:bg-slate-700 hover:bg-gray-200 dark:hover:bg-slate-600 rounded-xl transition-all duration-200 font-medium">
                    <i class="fa-solid fa-times mr-2"></i>
                    Batal
                </button>
                <button type="submit" id="addSubmitBtn"
                        class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl transition-all duration-200 font-medium shadow-lg hover:shadow-xl flex items-center">
                    <i class="fa-solid fa-save mr-2"></i>
                    <span>Simpan Barang</span>
                    <span id="addLoading" class="hidden ml-3">
                        <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Produk Modal - Style sama seperti Customer -->
<div id="editModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4">
    <div class="bg-white dark:bg-slate-800 rounded-lg shadow-xl w-full mx-auto max-h-[90vh] overflow-y-auto"
         style="max-width: 560px;">
        <div class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-slate-700">
            <h3 class="text-base md:text-lg font-semibold text-gray-900 dark:text-slate-100 flex items-center space-x-2">
                <svg class="w-4 h-4 md:w-5 md:h-5 text-blue-600 dark:text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                <span>Edit Produk</span>
            </h3>
            <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-slate-300 transition-colors duration-200">
                <svg class="w-5 h-5 md:w-6 md:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        
        <form id="editProdukForm" method="POST" class="p-6">
            @csrf
            @method('PUT')
            <div class="space-y-6">
                <div class="space-y-2">
                    <label for="edit_nama_produk" class="flex items-center space-x-2 text-sm font-medium text-gray-700 dark:text-slate-300">
                        <i class="fa-solid fa-box text-blue-500"></i>
                        <span>Nama Barang</span>
                    </label>
                    <input type="text" id="edit_nama_produk" name="nama_produk" required
                           class="w-full px-4 py-3 border border-gray-300 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 placeholder-gray-400 dark:placeholder-slate-500 rounded-xl focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 transition-all duration-200"
                           placeholder="Masukkan nama barang">
                </div>
                
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">Tipe Harga</label>
                    <select id="edit_price_type" class="w-full px-3 py-2 border border-gray-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 rounded-lg focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 transition-colors duration-200 text-sm">
                        <option value="pcs">Harga PCS</option>
                        <option value="set">Harga SET</option>
                    </select>
                </div>

                <div class="space-y-2">
                    <label for="edit_harga" class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">
                        <span id="edit_harga_label">Harga (PCS)</span>
                    </label>
                    <input type="number" id="edit_harga" name="harga_pcs"
                           class="w-full px-3 py-2 border border-gray-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100 placeholder-gray-400 dark:placeholder-slate-500 rounded-lg focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 transition-colors duration-200 text-sm"
                           placeholder="Masukkan harga" min="0" required>
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
        document.getElementById('add_nama_produk').focus();
        // Sinkronkan label input harga sesuai pilihan tipe saat modal dibuka
        if (typeof handleAddPriceTypeChange === 'function') {
            handleAddPriceTypeChange();
        }
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
        document.getElementById('addProdukForm').reset();
    }, 300);
}

window.openEditModal = function(id, nama, hargaPcs, hargaSet) {
    try {
        const modal = document.getElementById('editModal');
        const form = document.getElementById('editProdukForm');
        if (!modal || !form) {
            console.error('[Produk] Modal/Form edit tidak ditemukan');
            return;
        }
        form.action = `{{ url('produk') }}/${id}`;
        const namaInput = document.getElementById('edit_nama_produk');
        const editHargaInput = document.getElementById('edit_harga');
        const editHargaLabel = document.getElementById('edit_harga_label');
        const editPriceType = document.getElementById('edit_price_type');
        if (namaInput) namaInput.value = nama ?? '';
        // Tentukan tipe berdasarkan nilai yang ada (prioritaskan SET jika >0)
        const useSet = (parseFloat(hargaSet ?? 0) > 0);
        if (editPriceType) editPriceType.value = useSet ? 'set' : 'pcs';
        // Sinkronkan label + name input dan set value
        if (useSet) {
            if (editHargaLabel) editHargaLabel.textContent = 'Harga (SET)';
            if (editHargaInput) {
                editHargaInput.name = 'harga_set';
                editHargaInput.placeholder = 'Masukkan harga per SET';
                editHargaInput.value = (hargaSet ?? '') === 0 ? '' : (hargaSet ?? '');
            }
        } else {
            if (editHargaLabel) editHargaLabel.textContent = 'Harga (PCS)';
            if (editHargaInput) {
                editHargaInput.name = 'harga_pcs';
                editHargaInput.placeholder = 'Masukkan harga per PCS';
                editHargaInput.value = (hargaPcs ?? '') === 0 ? '' : (hargaPcs ?? '');
            }
        }
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
        setTimeout(() => namaInput?.focus(), 100);
    } catch (e) {
        console.error('[Produk] Gagal membuka modal edit:', e);
        // Jangan tampilkan alert mengganggu, cukup log error
    }
}

function closeEditModal() {
    const modal = document.getElementById('editModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    document.getElementById('editProdukForm').reset();
    // Restore body scroll
    document.body.style.overflow = '';
}

// Form submissions with loading states
document.getElementById('addProdukForm').addEventListener('submit', function() {
    const submitBtn = document.getElementById('addSubmitBtn');
    const loading = document.getElementById('addLoading');
    if (submitBtn) submitBtn.disabled = true;
    if (loading) loading.classList.remove('hidden');
});

document.getElementById('editProdukForm').addEventListener('submit', function() {
    const submitBtn = document.getElementById('editSubmitBtn');
    const loading = document.getElementById('editLoading');
    submitBtn.disabled = true;
    loading.classList.remove('hidden');
});

// Close modal when clicking outside
document.getElementById('addModal').addEventListener('click', function(e) {
    if (e.target === this) closeAddModal();
});

document.getElementById('editModal').addEventListener('click', function(e) {
    if (e.target === this) closeEditModal();
});

// Close modal with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeAddModal();
        closeEditModal();
        
    }
});

// Handle orientation change on mobile
window.addEventListener('orientationchange', function() {
    setTimeout(function() {
        // Recalculate modal positioning if needed
        const addModal = document.getElementById('addModal');
        const editModal = document.getElementById('editModal');
        if (!addModal.classList.contains('hidden') || !editModal.classList.contains('hidden')) {
            // Force reflow
            document.body.offsetHeight;
        }
    }, 100);
});

// (Import Cepat dihapus)
</script>

<script>
// Client-side search filter for Produk
document.addEventListener('DOMContentLoaded', function () {
    const input = document.getElementById('searchInputProduk');
    if (!input) return;
    const rows = () => document.querySelectorAll('#produkTable tbody tr');

    input.addEventListener('input', function (e) {
        const term = (e.target.value || '').toLowerCase().trim();
        rows().forEach(tr => {
            const name = (tr.getAttribute('data-name') || '').toLowerCase();
            const show = !term || name.includes(term);
            tr.style.display = show ? '' : 'none';
        });
    });

    // Auto-open Add Modal jika ada error validasi atau session error
    const hasErrors = {{ $errors->any() ? 'true' : 'false' }};
    const hasSessionError = {{ session('error') ? 'true' : 'false' }};
    if (hasErrors || hasSessionError) {
        openAddModal();
    }

    // === Harga type toggle (Add) - SINGLE INPUT DYNAMIC ===
    window.handleAddPriceTypeChange = function() {
        const addPriceType = document.getElementById('add_price_type');
        const addHarga = document.getElementById('add_harga');
        const addHargaLabel = document.getElementById('add_harga_label');
        
        const type = addPriceType?.value || 'pcs';
        
        // Update label dan atribut input sesuai tipe
        if (type === 'set') {
            if (addHargaLabel) addHargaLabel.textContent = 'Harga (SET)';
            if (addHarga) {
                addHarga.name = 'harga_set';
                addHarga.placeholder = 'Masukkan harga per SET';
            }
        } else {
            if (addHargaLabel) addHargaLabel.textContent = 'Harga (PCS)';
            if (addHarga) {
                addHarga.name = 'harga_pcs';
                addHarga.placeholder = 'Masukkan harga per PCS';
            }
        }
        
        setTimeout(() => addHarga?.focus(), 100);
    };
    
    // Panggil saat DOM ready
    handleAddPriceTypeChange();
    // Binding robust: update saat dropdown berubah
    const addPriceTypeEl = document.getElementById('add_price_type');
    if (addPriceTypeEl) {
        addPriceTypeEl.addEventListener('change', window.handleAddPriceTypeChange);
        addPriceTypeEl.addEventListener('input', window.handleAddPriceTypeChange);
    }
});

// === Harga type toggle (Edit) - Single Input Dynamic ===
function handleEditPriceTypeChange() {
    const selectEl = document.getElementById('edit_price_type');
    const editHarga = document.getElementById('edit_harga');
    const editHargaLabel = document.getElementById('edit_harga_label');
    const type = selectEl?.value || 'pcs';
    if (type === 'set') {
        if (editHargaLabel) editHargaLabel.textContent = 'Harga (SET)';
        if (editHarga) {
            editHarga.name = 'harga_set';
            editHarga.placeholder = 'Masukkan harga per SET';
        }
    } else {
        if (editHargaLabel) editHargaLabel.textContent = 'Harga (PCS)';
        if (editHarga) {
            editHarga.name = 'harga_pcs';
            editHarga.placeholder = 'Masukkan harga per PCS';
        }
    }
}
const editTypeEl = document.getElementById('edit_price_type');
if (editTypeEl) {
    editTypeEl.addEventListener('change', handleEditPriceTypeChange);
    editTypeEl.addEventListener('input', handleEditPriceTypeChange);
}
// Sinkronkan sekali saat script dimuat
handleEditPriceTypeChange();
</script>
@endsection
