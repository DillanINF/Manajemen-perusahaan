@extends('layouts.app')
@section('title', 'Data Invoice')
@section('body-classes', 'invoice-page')

@section('content')
@push('styles')
<style>
  /* Berlaku hanya jika body diberi class invoice-page */
  body.invoice-page header.sticky {
    height: 7rem !important; /* 80px: lebih lebar ke bawah */
    min-height: 4rem !important;
  }
  /* Pastikan konten tidak ketutup saat tinggi header bertambah */
  body.invoice-page .content-after-header { margin-top: 1rem; }
</style>
@endpush
<div class="min-h-screen bg-transparent py-6 content-after-header">
    <div class="w-full px-4 sm:px-6 lg:px-8">
        <!-- Header Section -->
        <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-xl border border-white/20 dark:border-gray-700/50 rounded-2xl shadow-xl p-6 mb-8">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                <div class="space-y-2">
                    <div class="flex items-center gap-3">
                        <div class="p-3 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-xl shadow-lg">
                            <i class="fas fa-file-invoice-dollar text-white text-xl"></i>
                        </div>

        <!-- Style spesifik halaman untuk merapikan tombol Aksi -->
        <style>
            /* Pastikan kolom aksi rapi dan tidak overlap header */
            td.action-col, th.action-col { width: 120px; text-align: center; white-space: nowrap !important; }
            td.action-col { overflow: visible; }
            .action-cell { display: flex; align-items: center; justify-content: center; gap: 12px; font-size: 0; }
            .action-cell > form { display: inline-flex; align-items: center; margin: 0; padding: 0; }
            /* Samakan ukuran tombol bulat menjadi 36px dan center isi */
            .action-cell .js-edit-btn,
            .action-cell form button {
                width: 36px !important;
                height: 36px !important;
                padding: 0 !important;
                line-height: 0 !important;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                border: 0;
                border-radius: 9999px;
                vertical-align: middle;
                box-shadow: 0 1px 2px rgba(0,0,0,.06) !important;
            }
            /* Khusus tombol delete (di dalam form) turunkan 2px agar segaris sempurna */
            .action-cell > form > button { transform: translateY(2px); }
            /* Pastikan tombol edit tetap tanpa pergeseran */
            .action-cell .js-edit-btn { transform: translateY(0); }
            /* Ikon di dalam tombol diseragamkan ukurannya dan di-center */
            .action-cell svg { width: 16px !important; height: 16px !important; display: block; margin: 0 !important; }
            /* Normalisasi box-sizing agar ukuran benar-benar identik */
            .action-cell *, .action-cell *::before, .action-cell *::after { box-sizing: border-box; }
            /* Universal selector untuk semua tombol di kolom aksi (termasuk baris dinamis) */
            td.action-col button { width: 36px !important; height: 36px !important; padding: 0 !important; line-height: 0 !important; display: inline-flex; align-items: center; justify-content: center; border-radius: 9999px; }
            td.action-col form { display: inline-flex; align-items: center; margin: 0; padding: 0; }
            td.action-col svg { width: 16px !important; height: 16px !important; }
            /* Sembunyikan tooltip default agar tidak naik ke area header */
            .action-cell .group span { display: none !important; }

            /* Tabel mengikuti ukuran layar; scroll horizontal hanya saat benar-benar perlu */
            table.invoice-table { table-layout: fixed; width: 100%; min-width: 1200px; }
            table.invoice-table th,
            table.invoice-table td { white-space: nowrap !important; }
            th.whitespace-nowrap { white-space: normal !important; }

            /* Sembunyikan tampilan scrollbar vertikal namun tetap bisa di-scroll */
            .hide-scrollbar {
                -ms-overflow-style: none; /* IE and Edge */
                scrollbar-width: none;   /* Firefox */
            }
            .hide-scrollbar::-webkit-scrollbar { display: none; } /* Chrome, Safari */

            /* Wrapper scroll horizontal khusus tabel */
            .table-scroll { overflow-x: auto; }
            .table-scroll { scrollbar-gutter: stable both-edges; }

            /* Turunkan ambang scroll di device sempit */
            @media (max-width: 1024px) { table.invoice-table { min-width: 1000px; } }
            @media (max-width: 768px)  { table.invoice-table { min-width: 900px; } }

            /* Scroll horizontal HANYA di sel PRODUCT */
            .custom-product-scroll {
                overflow-x: auto;
                -ms-overflow-style: none; /* IE/Edge - sembunyikan scrollbar */
                scrollbar-width: none; /* Firefox - sembunyikan scrollbar */
                max-width: 200px; /* batasi lebar sel product agar scroll muncul */
            }
            .custom-product-scroll::-webkit-scrollbar { display: none; } /* Chrome/Safari - sembunyikan scrollbar */
            
            /* Pastikan konten product bisa digeser horizontal */
            .product-content {
                white-space: nowrap;
                display: inline-flex;
                align-items: center;
                gap: 8px;
                min-width: max-content;
            }
        </style>
        <style>
            /* Lebar kolom default (desktop) */
            .col-date { width: 12%; }
            .col-order { width: 9%; min-width: 92px; }
            .col-customer { width: 16%; min-width: 160px; }
            .col-nopo { width: 12%; }
            .col-product { width: 28%; }
            .col-qty { width: 8%; }
            .col-total { width: 12%; }
            .col-action { width: 128px; }

            /* Tablet */
            @media (max-width: 1024px) {
                .col-date { width: 14%; }
                .col-order { width: 9%; min-width: 88px; }
                .col-customer { width: 15%; min-width: 150px; }
                .col-nopo { width: 10%; }
                .col-product { width: auto; }
                .col-qty { width: 8%; }
                .col-total { width: 12%; }
                .col-action { width: 112px; }
            }

            /* Mobile landscape/portrait */
            @media (max-width: 768px) {
                .col-date { width: 18%; }
                .col-order { width: 11%; min-width: 84px; }
                .col-customer { width: 20%; min-width: 140px; }
                .col-nopo { width: 10%; }
                .col-product { width: auto; }
                .col-qty { width: 8%; }
                .col-total { width: 12%; }
                .col-action { width: 104px; }
            }
        </style>
                        <div>
                            <h1 class="text-2xl lg:text-3xl font-bold bg-gradient-to-r from-gray-900 to-gray-600 dark:from-white dark:to-gray-300 bg-clip-text text-transparent">
                                Data Invoice
                            </h1>
                            <p class="text-gray-600 dark:text-gray-400 text-sm">Kelola invoice Purchase Order dengan mudah</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-4 text-sm text-gray-500 dark:text-gray-400">
                        @php
                            // Hitung jumlah invoice unik berdasarkan nomor urut (atau po_number)
                            $groupedCount = collect($invoices ?? [])->groupBy(function($r){
                                return $r->no_urut ?? ($r->po_number ?? null);
                            })->count();
                        @endphp
                        <span class="flex items-center gap-1">
                            <i class="fas fa-database text-xs"></i>
                            Total: <span id="total-count" class="font-semibold text-indigo-600 dark:text-indigo-400">{{ $groupedCount }}</span>
                        </span>
                        <span class="flex items-center gap-1">
                            <i class="fas fa-calendar text-xs"></i>
                            {{ now()->format('d M Y') }}
                        </span>
                    </div>
                </div>
                
                <div class="flex flex-wrap items-center gap-3">
                    <button id="btn-set-nomor" type="button" class="inline-flex items-center justify-center gap-2 h-10 leading-none min-w-[160px] bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white px-5 rounded-lg font-medium shadow-md hover:shadow-lg transition-all duration-200">
                        <i class="fas fa-cog"></i>
                        <span>Atur No Invoice</span>
                    </button>
                    <button id="btn-tambah" type="button" class="inline-flex items-center justify-center gap-2 h-10 leading-none min-w-[160px] bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white px-5 rounded-lg font-medium shadow-md hover:shadow-lg transition-all duration-200">
                        <i class="fas fa-plus-circle"></i>
                        <span>Tambah No Invoice</span>
                    </button>
                    <div class="relative">
                        <input id="search-number" type="text" inputmode="numeric" pattern="[0-9]*" placeholder="Cari no invoice..." class="w-48 md:w-60 h-10 leading-none px-4 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <span class="absolute right-3 top-2.5 text-gray-400"><i class="fas fa-search"></i></span>
                    </div>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 border border-green-200 dark:border-green-700/50 rounded-xl p-4 mb-6 shadow-lg">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-green-100 dark:bg-green-800/50 rounded-full">
                        <i class="fas fa-check-circle text-green-600 dark:text-green-400"></i>
                    </div>
                    <p class="text-green-800 dark:text-green-300 font-medium">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-gradient-to-r from-red-50 to-rose-50 dark:from-red-900/20 dark:to-rose-900/20 border border-red-200 dark:border-red-700/50 rounded-xl p-4 mb-6 shadow-lg">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-red-100 dark:bg-red-800/50 rounded-full">
                        <i class="fas fa-exclamation-triangle text-red-600 dark:text-red-400"></i>
                    </div>
                    <p class="text-red-800 dark:text-red-300 font-medium">{{ session('error') }}</p>
                </div>
            </div>
        @endif

        <!-- Main Table Container -->
        <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-xl border border-white/20 dark:border-gray-700/50 rounded-2xl shadow-xl overflow-hidden">
            <!-- Table Header Info -->
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700/50 bg-gradient-to-r from-gray-50 to-blue-50 dark:from-gray-800 dark:to-slate-800">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-blue-100 dark:bg-blue-900/50 rounded-lg">
                            <i class="fas fa-table text-blue-600 dark:text-blue-400"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Daftar Invoice</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Klik 2x pada baris untuk isi data invoice/PO dan masuk ke form input PO</p>
                        </div>
                    </div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">
                        <span id="badge-count" class="px-3 py-1 bg-blue-100 dark:bg-blue-900/50 rounded-full">
                            {{ $groupedCount }} data
                        </span>
                    </div>
                </div>
            </div>

            <!-- Table -->
            <div class="overflow-x-hidden">
                <div class="table-scroll hide-scrollbar max-h-[600px] overflow-y-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 invoice-table">
                        <colgroup>
                            <col class="col-date" />
                            <col class="col-order" />
                            <col class="col-customer" />
                            <col class="col-nopo" />
                            <col class="col-product" />
                            <col class="col-qty" />
                            <col class="col-total" />
                            <col class="col-action" />
                        </colgroup>
                        <thead class="bg-gradient-to-r from-gray-100 to-blue-100 dark:from-gray-700 dark:to-slate-700 sticky top-0 z-30">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                    <div class="flex items-center gap-2">
                                        <i class="fas fa-calendar text-blue-500"></i>
                                        Tanggal
                                    </div>
                                </th>
                                <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                    <div class="flex items-center gap-2">
                                        <i class="fas fa-hashtag text-indigo-500"></i>
                                        No Invoice
                                    </div>
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                    <div class="flex items-center gap-2">
                                        <i class="fas fa-building text-cyan-600"></i>
                                        Customer
                                    </div>
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                    <div class="flex items-center gap-2">
                                        <i class="fas fa-file-alt text-green-500"></i>
                                        No PO
                                    </div>
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                    <div class="flex items-center gap-2">
                                        <i class="fas fa-box text-orange-500"></i>
                                        Product
                                    </div>
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                    <div class="flex items-center gap-2">
                                        <i class="fas fa-cubes text-purple-500"></i>
                                        Qty
                                    </div>
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                    <div class="flex items-center gap-2">
                                        <i class="fas fa-money-bill-wave text-emerald-500"></i>
                                        Harga Total
                                    </div>
                                </th>
                                <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider action-col">
                                    <div class="flex items-center justify-center gap-2">
                                        <i class="fas fa-cogs text-red-500"></i>
                                        Aksi
                                    </div>
                                </th>
                            </tr>
                        </thead>
                        <tbody id="invoice-tbody" class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @php
                                // Kelompokkan berdasarkan No Urut agar tidak duplikat baris
                                $grouped = collect($invoices)->groupBy(function($r){
                                    return $r->no_urut ?? ($r->po_number ?? null);
                                });
                            @endphp
                            @forelse($grouped as $noUrut => $rows)
                                @php
                                    $first = $rows->first();
                                    $sumQty = (int) collect($rows)->sum(function($r){ return (int) ($r->qty ?? 0); });
                                    $sumTotal = (int) collect($rows)->sum(function($r){ return (int) ($r->total ?? 0); });
                                    $isMulti = $rows->count() > 1;
                                    $rowIdForDblClick = $first->id; // gunakan id pertama untuk open form
                                @endphp
                                <tr class="hover:bg-gradient-to-r hover:from-blue-50 hover:to-indigo-50 dark:hover:from-gray-700 dark:hover:to-slate-700 transition-all duration-200 cursor-pointer group" 
                                    data-id="{{ $rowIdForDblClick }}" 
                                    data-po-number="{{ (int)($noUrut ?? 0) }}"
                                    ondblclick="openEditForm({{ $rowIdForDblClick }}, {{ (int)($noUrut ?? 0) }})">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-3">
                                            <div class="p-2 bg-blue-100 dark:bg-blue-900/50 rounded-lg group-hover:bg-blue-200 dark:group-hover:bg-blue-800/50 transition-colors">
                                                <i class="fas fa-calendar-day text-blue-600 dark:text-blue-400 text-sm"></i>
                                            </div>
                                            <div>
                                                <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $first->tanggal }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 pr-10 whitespace-nowrap text-left">
                                        @php $badgeVal = $noUrut ?: '-'; @endphp
                                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-full text-sm font-semibold bg-gradient-to-r from-indigo-100 to-purple-100 dark:from-indigo-900/50 dark:to-purple-900/50 text-indigo-800 dark:text-indigo-200 align-middle shadow-sm">
                                            {{ $badgeVal }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white truncate max-w-[220px] md:max-w-[300px] lg:max-w-[420px] ml-1 mr-1">{{ $first->customer ?? 'N/A' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $isMulti ? '-' : ($first->no_po ?? '-') }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-2 w-full">
                                            <div class="p-1.5 bg-orange-100 dark:bg-orange-900/50 rounded shrink-0">
                                                <i class="fas fa-barcode text-orange-600 dark:text-orange-400 text-xs"></i>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <!-- Scroll horizontal HANYA di sel PRODUCT dengan scrollbar tersembunyi -->
                                                <div class="custom-product-scroll">
                                                    <div class="product-content">
                                                        @if($isMulti)
                                                            @foreach($rows as $r)
                                                                <div class="inline-flex items-center gap-2 mr-4">
                                                                    <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $r->barang ?? 'N/A' }}</span>
                                                                    <span class="text-xs text-gray-500 dark:text-gray-400">Produk ID: {{ $r->produk_id ?? '-' }}</span>
                                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-blue-50 text-blue-700 dark:bg-blue-900/40 dark:text-blue-200 border border-blue-200 dark:border-blue-800">
                                                                        No PO: {{ $r->no_po ?? '-' }}
                                                                    </span>
                                                                </div>
                                                            @endforeach
                                                        @else
                                                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $first->barang ?? 'N/A' }}</span>
                                                            <span class="text-xs text-gray-500 dark:text-gray-400 ml-2">Produk ID: {{ $first->produk_id ?? '-' }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-sm font-medium bg-purple-100 dark:bg-purple-900/50 text-purple-800 dark:text-purple-200">
                                            {{ $sumQty }} pcs
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-2">
                                            <div class="p-1.5 bg-emerald-100 dark:bg-emerald-900/50 rounded">
                                                <i class="fas fa-rupiah-sign text-emerald-600 dark:text-emerald-400 text-xs"></i>
                                            </div>
                                            <div>
                                                <div class="text-sm font-bold text-emerald-600 dark:text-emerald-400">
                                                    Rp {{ number_format($sumTotal, 0, ',', '.') }}
                                                </div>
                                                @if(!$isMulti)
                                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                                        @ Rp {{ number_format($first->harga ?? 0, 0, ',', '.') }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center action-col">
                                        <div class="action-cell">
                                            <x-table.action-buttons 
                                                onEdit="window.openEditForm({{ $rowIdForDblClick }})"
                                                deleteAction="{{ route('po.destroy', ['po' => $rowIdForDblClick, 'from' => 'invoice']) }}"
                                                confirmText="Yakin ingin menghapus data invoice ini?"
                                                :useMenu="true"
                                            />
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center gap-4">
                                            <div class="p-4 bg-gray-100 dark:bg-gray-700 rounded-full">
                                                <i class="fas fa-inbox text-gray-400 text-3xl"></i>
                                            </div>
                                            <div>
                                                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Belum ada data invoice</h3>
                                                <p class="text-gray-500 dark:text-gray-400 mt-1">Klik tombol "Tambah Invoice" untuk membuat invoice baru</p>
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
</div>
@endsection

<!-- Modal Atur Nomor Urut -->
<div id="modal-set-nomor" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="bg-white/95 dark:bg-gray-800/95 backdrop-blur-xl w-full max-w-lg rounded-2xl shadow-2xl border border-white/20 dark:border-gray-700/50 overflow-hidden">
            <!-- Header -->
            <div class="px-6 py-5 bg-gradient-to-r from-amber-500 to-orange-500 text-white">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-white/20 rounded-xl">
                        <i class="fas fa-cog text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold">Atur Nomor Urut Invoice</h3>
                        <p class="text-amber-100 text-sm">Tentukan nomor urut untuk invoice berikutnya</p>
                    </div>
                </div>
            </div>
            
            <!-- Content -->
            <div class="p-6">
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-xl p-4 mb-6">
                    <div class="flex items-start gap-3">
                        <div class="p-2 bg-blue-100 dark:bg-blue-800/50 rounded-lg">
                            <i class="fas fa-info-circle text-blue-600 dark:text-blue-400"></i>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900 dark:text-white mb-1">Informasi Penting</h4>
                            <p class="text-sm text-gray-600 dark:text-gray-300">
                                Nomor urut akan digunakan untuk invoice berikutnya dan akan otomatis bertambah setiap kali membuat invoice baru.
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="space-y-4">
                    <div>
                        <label for="next-number" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                            <i class="fas fa-hashtag text-indigo-500 mr-1"></i>
                            Nomor Urut Berikutnya
                        </label>
                        <div class="relative">
                            <input type="number" 
                                   id="next-number" 
                                   class="w-full px-4 py-3 pl-12 border-2 border-gray-200 dark:border-gray-600 rounded-xl shadow-sm focus:ring-2 focus:ring-amber-500 focus:border-amber-500 dark:bg-gray-700 dark:text-white text-lg font-semibold transition-all duration-200" 
                                   min="1" 
                                   placeholder="1000"
                                   required>
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <span class="text-gray-400 text-lg font-bold">#</span>
                            </div>
                        </div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                            Contoh: 1000, 2000, 5000
                        </p>
                    </div>
                </div>
            </div>
            
            <!-- Footer -->
            <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700/50 flex items-center justify-end gap-3">
                <button type="button" 
                        id="btn-cancel-set-nomor" 
                        class="inline-flex items-center gap-2 px-5 py-2.5 border-2 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 rounded-xl font-medium transition-all duration-200 transform hover:scale-105">
                    <i class="fas fa-times"></i>
                    Batal
                </button>
                <button type="button" 
                        id="btn-save-nomor" 
                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white rounded-xl font-medium shadow-lg hover:shadow-xl transition-all duration-200 transform hover:scale-105">
                    <i class="fas fa-save"></i>
                    Simpan Pengaturan
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Pilih Customer untuk Tambah/Atur No Invoice -->
<div id="modal-pilih-customer" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="bg-white/95 dark:bg-gray-800/95 backdrop-blur-xl w-full max-w-md rounded-2xl shadow-2xl border border-white/20 dark:border-gray-700/50 overflow-hidden">
            <div class="px-6 py-5 bg-gradient-to-r from-indigo-600 to-blue-600 text-white">
                <h3 class="text-lg font-bold" id="title-pilih-customer">Pilih Customer</h3>
                <p class="text-indigo-100 text-sm">Customer wajib dipilih sebelum melanjutkan</p>
            </div>
            <div class="p-6 space-y-4">
                @php
                    // Ambil daftar customer dari data customer (id + name)
                    $allCustomers = isset($customers) ? collect($customers)->map(fn($c) => ['id' => $c->id, 'name' => $c->name]) : collect([]);
                @endphp
                <label for="select-pilih-customer" class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Customer</label>
                <select id="select-pilih-customer" class="w-full h-11 px-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">-- Pilih Customer --</option>
                    @foreach($allCustomers as $cust)
                        <option value="{{ $cust['id'] }}" data-name="{{ $cust['name'] }}">{{ $cust['name'] }}</option>
                    @endforeach
                </select>
                @if(!isset($customers))
                    <p class="text-xs text-red-500">Data customer tidak tersedia di halaman ini. Pastikan controller mengirimkan variabel $customers.</p>
                @endif

                <!-- No Invoice Berikutnya (hanya untuk mode ATUR) -->
                <div id="wrap-next-number" class="hidden">
                    <label for="next-number" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        <i class="fas fa-hashtag text-indigo-500 mr-1"></i>
                        No Invoice Berikutnya
                    </label>
                    <div class="relative">
                        <input type="number" id="next-number-pick" min="1" placeholder="1000" class="w-full h-11 px-3 pl-10 rounded-lg border-2 border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" />
                        <span class="absolute left-3 inset-y-0 flex items-center text-gray-400 font-bold">#</span>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Contoh: 1000, 2000, 5000</p>
                </div>
            </div>
            <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700/50 flex items-center justify-end gap-3">
                <button type="button" id="btn-cancel-pilih-customer" class="px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600">Batal</button>
                <button type="button" id="btn-confirm-pilih-customer" class="px-4 py-2 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white">Lanjut</button>
            </div>
        </div>
    </div>
    <!-- simpan mode aksi: tambah | atur -->
    <input type="hidden" id="state-pilih-customer-mode" value="">
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const btnTambah = document.getElementById('btn-tambah');
    const btnSetNomor = document.getElementById('btn-set-nomor');
    const tbody = document.getElementById('invoice-tbody');
    const modalSetNomor = document.getElementById('modal-set-nomor');
    const btnCancelSetNomor = document.getElementById('btn-cancel-set-nomor');
    const btnSaveNomor = document.getElementById('btn-save-nomor');
    const inputNextNumber = document.getElementById('next-number');
    const totalCount = document.getElementById('total-count');
    const badgeCount = document.getElementById('badge-count');
    // Modal pilih customer
    const modalPilihCustomer = document.getElementById('modal-pilih-customer');
    const selectPilihCustomer = document.getElementById('select-pilih-customer');
    const btnCancelPilihCustomer = document.getElementById('btn-cancel-pilih-customer');
    const btnConfirmPilihCustomer = document.getElementById('btn-confirm-pilih-customer');
    const stateModeEl = document.getElementById('state-pilih-customer-mode');
    let pickedCustomerId = '';
    let pickedCustomerName = '';
    // Seed nomor berikutnya (diisi saat 'Atur No Invoice' atau dihitung dari tabel saat load)
    let nextInvoiceSeed = null;
    // Hitung seed awal dari data yang sudah ditampilkan (ambil nilai terbesar)
    try {
        const rows = tbody?.querySelectorAll('tr[data-po-number]') || [];
        let maxNum = 0;
        rows.forEach(r => {
            const n = parseInt(r.getAttribute('data-po-number') || '0');
            if (!isNaN(n) && n > maxNum) maxNum = n;
        });
        if (maxNum > 0) nextInvoiceSeed = maxNum;
    } catch (e) { /* ignore */ }

    // URL endpoints
    const editUrlTemplate = "{{ route('po.edit', 0) }}"; // tetap dipakai untuk tombol Edit di kolom Aksi
    const createUrl = "{{ route('po.create') }}";
    const quickCreateUrl = "{{ route('po.invoice.quick-create') }}";
    const setNextNumberUrl = "{{ route('po.invoice.set-next-number') }}";
    const deleteUrlTemplate = "{{ route('po.destroy', ['po' => 0, 'from' => 'invoice']) }}";

    // Function untuk membuka form edit PO
    function openEditForm(id, poNumber = null) {
        // Tampilkan notifikasi singkat sebelum pindah halaman
        showNotification('Membuka form input PO untuk melengkapi data invoice...', 'success');
        setTimeout(() => {
            // ARAHKAN KE FORM CREATE, bukan edit
            // Bawa informasi sumber + nomor urut agar ter-prefill dan nomor urut terjaga
            let poNum = poNumber || '';
            if (!poNum) {
                const row = document.querySelector(`tr[data-id="${id}"]`);
                if (row) poNum = row.getAttribute('data-po-number') || '';
            }
            const params = new URLSearchParams();
            params.set('from', 'invoice');
            if (poNum) params.set('po_number', poNum);
            const url = createUrl + (createUrl.includes('?') ? '&' : '?') + params.toString();
            window.location.href = url;
        }, 800);
    }
    window.openEditForm = openEditForm;

    // === Auto update counter (tanpa refresh) ===
    function recalcCounter() {
        try {
            const rows = tbody ? Array.from(tbody.querySelectorAll('tr')) : [];
            // Hitung hanya baris data (exclude placeholder empty rows)
            const dataRows = rows.filter(r => r.querySelector('td'));
            const n = dataRows.length;
            if (totalCount) totalCount.textContent = n;
            if (badgeCount) badgeCount.textContent = n + ' data';
        } catch (e) { /* no-op */ }
    }
    // Inisialisasi awal
    recalcCounter();
    // Amati perubahan pada tbody (baris bertambah/berkurang)
    if (tbody) {
        const obs = new MutationObserver(() => recalcCounter());
        obs.observe(tbody, { childList: true, subtree: false });
    }

    // Highlight baris berdasarkan parameter highlight_id
    try {
        const params = new URLSearchParams(window.location.search);
        const highlightId = params.get('highlight_id');
        if (highlightId) {
            const row = document.querySelector(`tr[data-id="${highlightId}"]`);
            if (row) {
                row.scrollIntoView({ behavior: 'smooth', block: 'center' });
                row.classList.add('ring-2','ring-amber-400','ring-offset-2');
                row.style.transition = 'background-color 0.6s ease';
                const originalBg = row.style.backgroundColor;
                row.style.backgroundColor = 'rgba(251, 191, 36, 0.15)'; // amber-300/15
                setTimeout(() => {
                    row.style.backgroundColor = originalBg || '';
                    row.classList.remove('ring-2','ring-amber-400','ring-offset-2');
                }, 2200);
            }
        }
    } catch (e) {
        console.warn('Highlight row failed:', e);
    }

    // Helper open customer picker
    function openCustomerPicker(mode) {
        stateModeEl.value = mode; // 'tambah' | 'atur'
        selectPilihCustomer.value = '';
        pickedCustomerId = '';
        pickedCustomerName = '';
        document.getElementById('title-pilih-customer').textContent = mode === 'atur' ? 'Pilih Customer untuk Atur No Invoice' : 'Pilih Customer untuk Tambah No Invoice';
        // toggle input nomor
        const wrapNum = document.getElementById('wrap-next-number');
        if (wrapNum) wrapNum.classList.toggle('hidden', mode !== 'atur');
        modalPilihCustomer.classList.remove('hidden');
        setTimeout(() => selectPilihCustomer.focus(), 50);
    }

    if (btnSetNomor) {
        btnSetNomor.addEventListener('click', function() { openCustomerPicker('atur'); });
    }

    if (btnCancelSetNomor) {
        btnCancelSetNomor.addEventListener('click', function() {
            modalSetNomor.classList.add('hidden');
            inputNextNumber.value = '';
        });
    }

    // Modal pilih customer actions
    btnCancelPilihCustomer?.addEventListener('click', () => { modalPilihCustomer.classList.add('hidden'); });
    btnConfirmPilihCustomer?.addEventListener('click', async () => {
        const sel = selectPilihCustomer;
        const custId = (sel?.value || '').trim();
        const custName = sel?.options[sel.selectedIndex]?.dataset?.name || '';
        if (!custId) { alert('Silakan pilih customer.'); sel?.focus(); return; }
        pickedCustomerId = custId;
        pickedCustomerName = custName;
        const mode = stateModeEl.value;
        if (mode === 'atur') {
            const numInput = document.getElementById('next-number-pick');
            const nextVal = (numInput?.value || '').trim();
            if (!nextVal || parseInt(nextVal) < 1) { alert('Masukkan No Invoice berikutnya yang valid (>=1).'); numInput?.focus(); return; }
            // kirim dan tutup modal setelah sukses
            await doSetNextNumber(pickedCustomerId, pickedCustomerName, parseInt(nextVal));
            modalPilihCustomer.classList.add('hidden');
        } else if (mode === 'tambah') {
            await doQuickCreate(pickedCustomerId, pickedCustomerName);
            modalPilihCustomer.classList.add('hidden');
        }
    });

    // Helper set next number (dipakai saat mode ATUR di modal ini)
    async function doSetNextNumber(custId, custName, nextNumber) {
        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        try {
            const response = await fetch(setNextNumberUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    next_number: parseInt(nextNumber),
                    customer_id: custId,
                    customer: custName
                })
            });
            const result = await response.json();
            if (result.success) {
                showNotification(result.message, 'success');
                const emptyRow = tbody.querySelector('td[colspan]');
                if (emptyRow) emptyRow.closest('tr').remove();
                if (result.id && result.po_number && result.tanggal_display) {
                    const newRow = createNewInvoiceRow({ id: result.id, po_number: result.po_number, tanggal_display: result.tanggal_display, customer: pickedCustomerName });
                    tbody.appendChild(newRow);
                    sortTableAscending();
                    const currentCount = parseInt(totalCount.textContent) || 0;
                    totalCount.textContent = currentCount + 1;
                }
                // Set seed agar penambahan berikutnya menjadi +1
                nextInvoiceSeed = parseInt(nextNumber);
            } else {
                throw new Error(result.message || 'Gagal menyimpan nomor urut');
            }
        } catch (error) {
            console.error('Error:', error);
            showNotification('Terjadi kesalahan: ' + error.message, 'error');
        }
    }

    // Tombol tambah invoice
    // Abstraksi proses quick-create agar bisa dipanggil dari modal
    async function doQuickCreate(custId, custName) {
        try {
            if (!custId) throw new Error('Customer belum dipilih');
            btnTambah.disabled = true;
            btnTambah.innerHTML = '<i class="fas fa-spinner fa-spin"></i> <span>Membuat...</span>';
            
            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
            const payload = { customer_id: custId, customer: custName };
            if (typeof nextInvoiceSeed === 'number') {
                // Kirim hint = nomor terbesar saat ini, agar server menghasilkan next = hint+1
                payload.next_hint = nextInvoiceSeed;
            }
            const res = await fetch(quickCreateUrl, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                },
                credentials: 'same-origin',
                body: JSON.stringify(payload)
            });
            
            const data = await res.json();
            if (!data || !data.success) {
                throw new Error(data?.message || 'Gagal membuat invoice');
            }

            // Hapus baris "Belum ada data" jika ada
            const emptyRow = tbody.querySelector('td[colspan]');
            if (emptyRow) {
                emptyRow.closest('tr').remove();
            }

            // Tambah baris baru dan urutkan
            if (!data.customer) { data.customer = custName; }
            const newRow = createNewInvoiceRow(data);
            tbody.appendChild(newRow);
            sortTableAscending();

            // Update counter
            const currentCount = parseInt(totalCount.textContent) || 0;
            totalCount.textContent = currentCount + 1;

            showNotification(`Invoice #${data.po_number} berhasil dibuat`, 'success');

            // Update seed ke nomor terbaru dari server
            if (data.po_number) {
                const n = parseInt(data.po_number);
                if (!isNaN(n)) nextInvoiceSeed = n;
            }

        } catch (error) {
            console.error('Error:', error);
            showNotification('Gagal membuat invoice: ' + error.message, 'error');
        } finally {
            btnTambah.disabled = false;
            btnTambah.innerHTML = '<i class="fas fa-plus-circle"></i> <span>Tambah No Invoice</span>';
        }
    }

    if (btnTambah) {
        btnTambah.addEventListener('click', async function() {
            try {
                openCustomerPicker('tambah');
            } catch (error) {
                console.error('Error open picker:', error);
                showNotification('Gagal membuka pemilihan customer: ' + error.message, 'error');
            }
        });
    }

    // Function untuk membuat baris invoice baru
    function createNewInvoiceRow(data) {
        const tr = document.createElement('tr');
        tr.className = 'hover:bg-gradient-to-r hover:from-blue-50 hover:to-indigo-50 dark:hover:from-gray-700 dark:hover:to-slate-700 transition-all duration-200 cursor-pointer group';
        tr.setAttribute('data-id', data.id);
        tr.setAttribute('data-po-number', String(data.po_number ?? ''));
        tr.ondblclick = () => openEditForm(data.id);
        
        tr.innerHTML = `
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-blue-100 dark:bg-blue-900/50 rounded-lg group-hover:bg-blue-200 dark:group-hover:bg-blue-800/50 transition-colors">
                        <i class="fas fa-calendar-day text-blue-600 dark:text-blue-400 text-sm"></i>
                    </div>
                    <div>
                        <div class="text-sm font-medium text-gray-900 dark:text-white">${data.tanggal_display}</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">Baru dibuat</div>
                    </div>
                </div>
            </td>
            <!-- No Invoice -->
            <td class="px-6 py-4 pr-10 whitespace-nowrap text-left">
                <span class="inline-flex items-center justify-center w-8 h-8 rounded-full text-sm font-semibold bg-gradient-to-r from-indigo-100 to-purple-100 dark:from-indigo-900/50 dark:to-purple-900/50 text-indigo-800 dark:text-indigo-200 align-middle shadow-sm">
                    ${data.po_number}
                </span>
            </td>
            <!-- Customer -->
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm font-medium text-gray-900 dark:text-white">${data.customer || '-'}</div>
            </td>
            <!-- No PO -->
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm font-medium text-gray-900 dark:text-white">-</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center gap-2">
                    <div class="p-1.5 bg-orange-100 dark:bg-orange-900/50 rounded">
                        <i class="fas fa-barcode text-orange-600 dark:text-orange-400 text-xs"></i>
                    </div>
                    <div>
                        <div class="text-sm font-medium text-gray-900 dark:text-white">N/A</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">Produk ID: -</div>
                    </div>
                </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-sm font-medium bg-purple-100 dark:bg-purple-900/50 text-purple-800 dark:text-purple-200">
                    0 pcs
                </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center gap-2">
                    <div class="p-1.5 bg-emerald-100 dark:bg-emerald-900/50 rounded">
                        <i class="fas fa-rupiah-sign text-emerald-600 dark:text-emerald-400 text-xs"></i>
                    </div>
                    <div>
                        <div class="text-sm font-bold text-emerald-600 dark:text-emerald-400">Rp 0</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">@ Rp 0</div>
                    </div>
                </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-center action-col">
                <div class="action-cell">
                    <div class="hidden sm:flex items-center justify-center gap-1.5">
                        <button type="button" onclick="event.stopPropagation(); openEditForm(${data.id})"
                                class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-[#2563EB] text-white shadow-sm hover:shadow-md transition-all duration-200 hover:bg-[#1D4ED8]"
                                aria-label="Edit">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-3.5 h-3.5">
                                <path d="M12 20h9"/>
                                <path d="M16.5 3.5a2.121 2.121 0 1 1 3 3L7 19l-4 1 1-4 12.5-12.5z"/>
                            </svg>
                        </button>
                        <form method="POST" action="${deleteUrlTemplate.replace('/0', '/' + data.id)}" class="inline-flex" onsubmit="event.stopPropagation(); return confirm('Yakin ingin menghapus data invoice ini?')">
                            <input type="hidden" name="_token" value="${document.querySelector('meta[name=csrf-token]')?.getAttribute('content') || ''}">
                            <input type="hidden" name="_method" value="DELETE">
                            <button type="submit"
                                    class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-[#DC2626] text-white shadow-sm hover:shadow-md transition-all duration-200 hover:bg-[#B91C1C]"
                                    aria-label="Hapus">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-3.5 h-3.5">
                                    <polyline points="3 6 5 6 21 6"/>
                                    <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                                    <path d="M10 11v6"/>
                                    <path d="M14 11v6"/>
                                    <path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/>
                                </svg>
                            </button>
                        </form>
                    </div>
                    <div class="flex sm:hidden items-stretch justify-center gap-2 w-full">
                        <button type="button" onclick="event.stopPropagation(); openEditForm(${data.id})"
                                class="flex-1 inline-flex items-center justify-center gap-2 px-3 py-2 rounded-lg bg-[#2563EB] text-white shadow-sm hover:shadow-md transition-all duration-200 active:scale-[.99]">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4">
                                <path d="M12 20h9"/>
                                <path d="M16.5 3.5a2.121 2.121 0 1 1 3 3L7 19l-4 1 1-4 12.5-12.5z"/>
                            </svg>
                            <span class="text-sm font-medium">Edit</span>
                        </button>
                        <form method="POST" action="${deleteUrlTemplate.replace('/0', '/' + data.id)}" class="flex-1" onsubmit="event.stopPropagation(); return confirm('Yakin ingin menghapus data invoice ini?')">
                            <input type="hidden" name="_token" value="${document.querySelector('meta[name=csrf-token]')?.getAttribute('content') || ''}">
                            <input type="hidden" name="_method" value="DELETE">
                            <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-3 py-2 rounded-lg bg-[#DC2626] text-white shadow-sm hover:shadow-md transition-all duration-200 active:scale-[.99]">
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
                    </div>
                </div>
            </td>
        `;
        
        return tr;
    }

    // Sort seluruh tabel ascending berdasarkan data-po-number
    function sortTableAscending() {
        const rows = Array.from(tbody.querySelectorAll('tr[data-po-number]'));
        rows.sort((a, b) => {
            const na = parseInt(a.getAttribute('data-po-number') || '0');
            const nb = parseInt(b.getAttribute('data-po-number') || '0');
            return na - nb; // kecil ke besar
        });
        rows.forEach(r => tbody.appendChild(r));
    }

    // Function untuk menampilkan notifikasi
    function showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 z-50 p-4 rounded-xl shadow-lg transform transition-all duration-300 translate-x-full`;
        
        if (type === 'success') {
            notification.className += ' bg-gradient-to-r from-green-500 to-emerald-500 text-white';
            notification.innerHTML = `
                <div class="flex items-center gap-3">
                    <i class="fas fa-check-circle text-xl"></i>
                    <span class="font-medium">${message}</span>
                </div>
            `;
        } else if (type === 'error') {
            notification.className += ' bg-gradient-to-r from-red-500 to-pink-500 text-white';
            notification.innerHTML = `
                <div class="flex items-center gap-3">
                    <i class="fas fa-exclamation-circle text-xl"></i>
                    <span class="font-medium">${message}</span>
                </div>
            `;
        }
        
        document.body.appendChild(notification);
        
        // Animate in
        setTimeout(() => {
            notification.classList.remove('translate-x-full');
        }, 100);
        
        // Auto remove after 4 seconds
        setTimeout(() => {
            notification.classList.add('translate-x-full');
            setTimeout(() => {
                document.body.removeChild(notification);
            }, 300);
        }, 4000);
    }

    // Pencarian nomor urut
    const searchInput = document.getElementById('search-number');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const term = (this.value || '').trim();
            const rows = tbody.querySelectorAll('tr[data-po-number]');
            if (!term) {
                rows.forEach(r => r.classList.remove('hidden'));
                // jaga urutan tetap ascending ketika reset
                sortTableAscending();
                return;
            }
            rows.forEach(r => {
                const n = r.getAttribute('data-po-number') || '';
                r.classList.toggle('hidden', !n.includes(term));
            });
        });
    }

    // Close modal when clicking outside
    modalSetNomor.addEventListener('click', function(e) {
        if (e.target === modalSetNomor) {
            modalSetNomor.classList.add('hidden');
            inputNextNumber.value = '';
        }
    });

    // Pastikan saat halaman pertama kali dibuka, urutan sudah ascending
    sortTableAscending();

    // Normalisasi label lama yang masih bertuliskan "Draft" pada DOM (tanpa ubah DB)
    (function normalizeDraftLabels() {
        const rows = tbody.querySelectorAll('tr');
        rows.forEach(tr => {
            // Ganti teks kecil "Draft" menjadi "Belum diisi"
            tr.querySelectorAll('div.text-xs, span.text-xs').forEach(el => {
                const t = (el.textContent || '').trim();
                if (t.toLowerCase() === 'draft') {
                    el.textContent = 'Belum diisi';
                }
            });
            // Jika label utama di kolom barang kebetulan "Draft", ubah jadi '-'
            tr.querySelectorAll('div.text-sm, span.text-sm').forEach(el => {
                const t = (el.textContent || '').trim();
                if (t.toLowerCase() === 'draft') {
                    el.textContent = '-';
                }
            });
        });
    })();
});
</script>
@endpush
