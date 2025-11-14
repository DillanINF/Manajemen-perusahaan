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

  /* Samakan gaya modal dengan Employee (ukuran compact dan dark mode) */
  .modal-employee { max-width: 28rem !important; }
  html.dark .modal-employee { background-color: #0f172a !important; color: #e5e7eb !important; }
  html.dark .modal-employee .sticky { background-color: #0f172a !important; border-color: rgba(255,255,255,0.1) !important; }
  html.dark .modal-employee label { color: #e5e7eb !important; }
  html.dark .modal-employee input,
  html.dark .modal-employee textarea,
  html.dark .modal-employee select { background-color: #1f2937 !important; color: #e5e7eb !important; border-color: #374151 !important; }
  html.dark .modal-employee input::placeholder,
  html.dark .modal-employee textarea::placeholder { color: #9ca3af !important; }
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

            /* Scroll horizontal untuk sel No PO - hanya tampil 1 badge */
            .custom-nopo-scroll {
                overflow-x: auto;
                overflow-y: hidden;
                -ms-overflow-style: none; /* IE/Edge - sembunyikan scrollbar */
                scrollbar-width: none; /* Firefox - sembunyikan scrollbar */
                width: 160px; /* Lebih lebar untuk No PO panjang */
                max-width: 160px;
                scroll-snap-type: x mandatory; /* Snap scroll horizontal */
                scroll-behavior: smooth; /* Smooth scrolling */
                display: block;
            }
            .custom-nopo-scroll::-webkit-scrollbar { display: none; } /* Chrome/Safari - sembunyikan scrollbar */
            
            /* Pastikan konten no po bisa digeser horizontal */
            .nopo-content {
                display: inline-flex;
                align-items: center;
                gap: 0;
            }
            
            /* Setiap badge No PO akan snap - margin presisi agar tidak terlihat badge lain */
            .nopo-content > span {
                flex: 0 0 auto;
                scroll-snap-align: start; /* Snap ke start untuk kontrol lebih baik */
                scroll-snap-stop: always; /* PENTING: Selalu berhenti di setiap badge, tidak skip */
                margin-right: 20px; /* Jarak cukup agar badge lain tidak terlihat */
                min-width: fit-content;
                width: 140px; /* Width tetap untuk setiap badge */
                text-align: center;
            }
            
            /* Badge terakhir dengan padding agar bisa di-scroll penuh */
            .nopo-content > span:last-child {
                margin-right: 140px; /* Padding agar badge terakhir bisa ter-snap sempurna */
            }
        </style>
        <style>
            /* Lebar kolom default (desktop) */
            .col-date { width: 12%; }
            .col-order { width: 9%; min-width: 92px; }
            .col-customer { width: 18%; min-width: 160px; }
            .col-nopo { width: 20%; } /* Cukup untuk container 160px scroll */
            .col-qty { width: 8%; }
            .col-total { width: 15%; }
            .col-action { width: 128px; }

            /* Tablet */
            @media (max-width: 1024px) {
                .col-date { width: 14%; }
                .col-order { width: 9%; min-width: 88px; }
                .col-customer { width: 15%; min-width: 150px; }
                .col-nopo { width: auto; }
                .col-qty { width: 8%; }
                .col-total { width: 12%; }
                .col-action { width: 112px; }
            }

            /* Mobile landscape/portrait */
            @media (max-width: 768px) {
                .col-date { width: 18%; }
                .col-order { width: 11%; min-width: 84px; }
                .col-customer { width: 20%; min-width: 140px; }
                .col-nopo { width: auto; }
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
                            // Normalisasi koleksi dan singkirkan entri kosong/null
                            $invoiceCollection = collect($invoices ?? [])->filter(function ($r) {
                                return !empty($r) && (is_object($r) || is_array($r));
                            });
                            // Kelompokkan berdasarkan no_urut atau no_invoice, singkirkan key null/""
                            $grouped = $invoiceCollection
                                ->groupBy(function($r){
                                    return $r->no_urut ?? null;
                                })
                                ->filter(function($rows, $key){
                                    return $key !== null && $key !== '' && $rows->count() > 0;
                                });
                            $groupedCount = $grouped->count();
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

        <!-- Peringatan: gaya serupa form gaji karyawan -->
        <div class="rounded-xl p-4 mb-6 shadow-lg border bg-amber-50 text-amber-800 border-amber-300 dark:bg-amber-900/20 dark:text-amber-300 dark:border-amber-700">
            <span class="font-semibold">Peringatan:</span>
            Setelah tanda terima ditandatangani, ubah status invoice pada kolom <span class="font-semibold">Status</span> menjadi <span class="font-semibold">Accept</span>.
            Sistem akan menyinkronkan data ke Jatuh Tempo agar penagihan berjalan tepat waktu.
        </div>

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
            <div class="overflow-x-auto responsive-scroll">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 invoice-table" style="min-width: 1400px;">
                        <colgroup>
                            <col class="col-date" />
                            <col class="col-order" />
                            <col class="col-customer" />
                            <col class="col-nopo" />
                            <col class="col-qty" />
                            <col class="col-total" />
                            <col class="col-status" style="width: 120px;" />
                            <col class="col-action" style="width: 100px;" />
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
                                <th class="px-3 py-4 text-left text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider" style="width: 90px;">
                                    <div class="flex items-center gap-2">
                                        <i class="fas fa-file-alt text-green-500"></i>
                                        Total PO
                                    </div>
                                </th>
                                <th class="px-4 py-4 text-left text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                    <div class="flex items-center gap-2">
                                        <i class="fas fa-cubes text-purple-500"></i>
                                        Qty
                                    </div>
                                </th>
                                
                                <th class="px-4 py-4 pr-40 text-left text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                    <div class="flex items-center gap-2">
                                        <i class="fas fa-money-bill-wave text-emerald-500"></i>
                                        Total Pembayaran
                                    </div>
                                </th>
                                <th class="px-6 py-4 pl-16 text-center text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider" style="width: 120px;">
                                    <div class="flex items-center justify-center gap-2">
                                        <i class="fas fa-toggle-on text-blue-500"></i>
                                        Status
                                    </div>
                                </th>
                                <th class="px-6 py-4 pl-8 text-center text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider" style="width: 100px;">
                                    <div class="flex items-center justify-center gap-2">
                                        <i class="fas fa-cogs text-red-500"></i>
                                        Aksi
                                    </div>
                                </th>
                            </tr>
                        </thead>
                        <tbody id="invoice-tbody" class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @php
                                // Gunakan $grouped yang sudah dihitung pada header agar konsisten
                            @endphp
                            @forelse($grouped as $noUrut => $rows)
                                @php
                                    $first = $rows->first();
                                    $sumQty = (int) collect($rows)->sum(function($r){ return (int) ($r->qty ?? 0); });
                                    $sumTotal = (int) collect($rows)->sum(function($r){ return (int) ($r->total ?? 0); });
                                    $transaksiCount = (int) ($rows->count());
                                    $isMulti = $rows->count() > 1;
                                    $rowIdForDblClick = $first->id; // gunakan id pertama untuk open form
                                @endphp
                                @php
                                    // Safely prepare due date (Y-m-d) for data attribute
                                    $jtRaw = data_get($first, 'tanggal_jatuh_tempo');
                                    if ($jtRaw instanceof \Carbon\Carbon) {
                                        $jtAttr = $jtRaw->format('Y-m-d');
                                    } elseif (is_string($jtRaw) && $jtRaw !== '') {
                                        try { $jtAttr = \Carbon\Carbon::parse($jtRaw)->format('Y-m-d'); } catch (\Throwable $e) { $jtAttr = ''; }
                                    } else { $jtAttr = ''; }
                                    $invAttr = (string) ($first->no_invoice ?? ($noUrut ?: '-'));
                                @endphp
                                <tr class="hover:bg-gradient-to-r hover:from-blue-50 hover:to-indigo-50 dark:hover:from-gray-700 dark:hover:to-slate-700 transition-all duration-200 cursor-pointer group" 
                                    data-id="{{ $rowIdForDblClick }}" 
                                    data-po-number="{{ (int)($noUrut ?? 0) }}"
                                    data-invoice-no="{{ $invAttr }}"
                                    data-due-date="{{ $jtAttr }}"
                                    ondblclick="goToInputPO('{{ $invAttr }}')">
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
                                        <div class="text-sm font-medium text-gray-900 dark:text-white truncate max-w-[220px] md:max-w-[300px] lg:max-w-[420px] ml-1 mr-1">{{ $first->customer ?? '' }}</div>
                                    </td>
                                    <td class="px-3 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-semibold bg-blue-50 text-blue-700 dark:bg-blue-900/40 dark:text-blue-200 border border-blue-200 dark:border-blue-700">
                                            <i class="fas fa-file-alt text-blue-500 text-[10px]"></i>
                                            <span>{{ $first->total_po ?? 0 }} PO</span>
                                        </span>
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-sm font-medium bg-purple-100 dark:bg-purple-900/50 text-purple-800 dark:text-purple-200">
                                            {{ $sumQty }} pcs
                                        </span>
                                    </td>
                                    
                                    <td class="px-4 py-4 pr-40 whitespace-nowrap">
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
                                    <td class="px-4 py-4 pl-16 whitespace-nowrap text-center" style="width: 120px;">
                                        @php
                                            $currentStatus = $first->status_approval ?? 'Pending';
                                        @endphp
                                        <div class="flex items-center justify-center" x-data="{
                                                status: '{{ trim($currentStatus) }}',
                                                loading: false,
                                                dueDate: '',
                                                invNo: '-',
                                                init() {
                                                    try {
                                                        const row = this.$el.closest('tr');
                                                        this.dueDate = row?.dataset?.dueDate || '';
                                                        this.invNo = row?.dataset?.invoiceNo || '-';
                                                    } catch(e) { /* ignore */ }
                                                },
                                                isAccept() { return (this.status ?? '').toString().trim().toLowerCase() === 'accept'; },
                                                isOverdue() {
                                                    if (!this.dueDate) return false;
                                                    const today = new Date(); today.setHours(0,0,0,0);
                                                    const d = new Date(this.dueDate + 'T00:00:00');
                                                    return !isNaN(d) && d.getTime() <= today.getTime();
                                                },
                                                async toggle() {
                                                    if (this.loading) return;
                                                    this.loading = true;
                                                    try {
                                                        const res = await fetch('{{ route('po.toggle-status', $rowIdForDblClick) }}', {
                                                            method: 'POST',
                                                            headers: {
                                                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                                                'X-Requested-With': 'XMLHttpRequest',
                                                                'Accept': 'application/json',
                                                                'Content-Type': 'application/json'
                                                            },
                                                            credentials: 'same-origin',
                                                            body: JSON.stringify({_token: '{{ csrf_token() }}'})
                                                        });
                                                        if (!res.ok) {
                                                            const txt = await res.text();
                                                            console.error('Toggle status HTTP error', res.status, txt);
                                                            // Handle 403 Forbidden (Jatuh Tempo sudah Lunas)
                                                            if (res.status === 403) {
                                                                try {
                                                                    const errorData = JSON.parse(txt);
                                                                    alert(errorData.message || '❌ Status tidak dapat diubah karena Jatuh Tempo sudah LUNAS.');
                                                                } catch(e) {
                                                                    alert('❌ Status tidak dapat diubah karena Jatuh Tempo sudah LUNAS (Accept).');
                                                                }
                                                                return;
                                                            }
                                                            throw new Error('HTTP ' + res.status);
                                                        }
                                                        let data;
                                                        try { data = await res.json(); } catch (e) {
                                                            const txt = await res.text();
                                                            console.error('Toggle status non-JSON response:', txt);
                                                            throw new Error('Response bukan JSON');
                                                        }
                                                        console.log('Toggle status response:', data, 'status=', data?.status);
                                                        if (!data?.success || !(data?.status || data?.status_approval)) {
                                                            console.error('Toggle status invalid payload:', data);
                                                            throw new Error('Toggle gagal');
                                                        }
                                                        this.status = (data.status ?? data.status_approval ?? '').toString().trim();
                                                        // Refresh global notification bell count
                                                        if (window && typeof window.refreshNotifications === 'function') {
                                                            try { window.refreshNotifications(); } catch(_) {}
                                                        }
                                                        // Show overdue notice instantly without refresh
                                                        if (this.isOverdue()) {
                                                            if (this.isAccept()) {
                                                                if (window && typeof window.showOverdueToast === 'function') {
                                                                    window.showOverdueToast(this.invNo, this.dueDate);
                                                                } else {
                                                                    alert(`Invoice ${this.invNo} sudah jatuh tempo per ${this.dueDate}`);
                                                                }
                                                            } else {
                                                                // toggled to Pending while overdue
                                                                if (window && typeof window.showOverdueToast === 'function') {
                                                                    window.showOverdueToast(this.invNo, this.dueDate);
                                                                } else {
                                                                    alert(`Status invoice ${this.invNo} kini Pending. (Jatuh tempo: ${this.dueDate})`);
                                                                }
                                                            }
                                                        }
                                                    } catch (e) {
                                                        alert('Gagal mengubah status. Silakan coba lagi.');
                                                    } finally {
                                                        this.loading = false;
                                                    }
                                                }
                                            }">
                                            <button type="button"
                                                    @click="toggle()"
                                                    :class="isAccept()
                                                        ? 'bg-emerald-50 text-emerald-700 border-emerald-200 hover:bg-emerald-100 hover:border-emerald-300 focus:ring-emerald-500 dark:bg-emerald-900/20 dark:text-emerald-400 dark:border-emerald-700 dark:hover:bg-emerald-900/30'
                                                        : 'bg-amber-50 text-amber-700 border-amber-200 hover:bg-amber-100 hover:border-amber-300 focus:ring-amber-500 dark:bg-amber-900/20 dark:text-amber-400 dark:border-amber-700 dark:hover:bg-amber-900/30'"
                                                    class="relative inline-flex items-center justify-center w-[90px] px-3 py-2 mt-0 text-xs font-medium rounded-md border transition-all duration-300 ease-in-out focus:outline-none focus:ring-2 focus:ring-offset-1 hover:scale-105 active:scale-95 disabled:opacity-60 disabled:cursor-not-allowed"
                                                    :disabled="loading">
                                                <div class="flex items-center justify-center space-x-1.5">
                                                    <template x-if="isAccept()">
                                                        <svg class="w-3 h-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                        </svg>
                                                    </template>
                                                    <template x-if="!isAccept()">
                                                        <svg class="w-3 h-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                                        </svg>
                                                    </template>
                                                    <span class="font-medium text-xs" x-text="isAccept() ? 'Accept' : 'Panding'"></span>
                                                </div>
                                            </button>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 pl-8 whitespace-nowrap text-center" style="width: 100px;" onclick="event.stopPropagation()">
                                        <div class="action-cell">
                                            <x-table.action-buttons 
                                                onEdit="openEditModal({{ $rowIdForDblClick }}, {{ (int)($noUrut ?? 0) }}, '{{ addslashes($first->customer ?? '') }}', '{{ $first->tanggal ?? '' }}', {{ $first->customer_id ?? 'null' }})"
                                                deleteAction="{{ route('po.destroy', ['po' => $rowIdForDblClick, 'from' => 'invoice', 'group' => 1]) }}"
                                                confirmText="Yakin ingin menghapus seluruh data untuk No Invoice ini?"
                                                :useMenu="true"
                                            />
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-12 text-center">
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

@push('scripts')
<script>
// Script untuk Data Invoice - fungsi openEditForm didefinisikan di bawah (line ~740)
</script>
@endpush

    <input type="hidden" id="state-edit-customer-id" value="" />
    <script>
    (function(){
        const modal = document.getElementById('modal-edit-invoice');
        const form  = document.getElementById('form-edit-invoice');
        const inputNo = document.getElementById('edit-no-invoice');
        const inputTgl = document.getElementById('edit-tanggal-invoice');
        const routeTpl = document.getElementById('route-update-invoice-template');
        const stateId = document.getElementById('state-edit-po-id');
        const stateNoOld = document.getElementById('state-edit-no-invoice-old');
        const btnCancel = document.getElementById('btn-cancel-edit-invoice');

        function parseTanggalDisplayToYmd(display){
            // Expect dd/mm/YYYY -> YYYY-mm-dd
            if (!display) return '';
            const parts = (display || '').split('/');
            if (parts.length === 3) {
                const [dd, mm, yyyy] = parts;
                if (dd && mm && yyyy) return `${yyyy.padStart(4,'0')}-${mm.padStart(2,'0')}-${dd.padStart(2,'0')}`;
            }
            // fallback: try Date()
            const d = new Date(display);
            if (!isNaN(d.getTime())) {
                const y = d.getFullYear();
                const m = String(d.getMonth()+1).padStart(2,'0');
                const day = String(d.getDate()).padStart(2,'0');
                return `${y}-${m}-${day}`;
            }
            return '';
        }

        // Dihapus: versi lama openEditModal agar tidak bentrok dengan versi terbaru di bawah
    })();
    </script>
</div>

<!-- Modal Atur Nomor Urut -->
<div id="modal-set-nomor" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="bg-white/95 dark:bg-gray-800/95 backdrop-blur-xl w-full rounded-2xl shadow-2xl border border-white/20 dark:border-gray-700/50 overflow-hidden" style="max-width: 28rem !important;">
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
                            <i class="fas fa-info-circle text-amber-600 dark:text-amber-400"></i>
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
                            <i class="fas fa-hashtag text-amber-500 mr-1"></i>
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
        <div class="bg-white/95 dark:bg-gray-800/95 backdrop-blur-xl w-full rounded-2xl shadow-2xl border border-white/20 dark:border-gray-700/50 overflow-hidden" style="max-width: 28rem !important;">
            <div id="header-pilih-customer" class="px-6 py-5 bg-gradient-to-r from-amber-500 to-orange-500 text-white">
                <h3 class="text-lg font-bold" id="title-pilih-customer">Pilih Customer</h3>
                <p id="subtitle-pilih-customer" class="text-amber-100 text-sm">Customer wajib dipilih sebelum melanjutkan</p>
            </div>
            <div class="p-6 space-y-4">
                @php
                    // Ambil daftar customer dari data customer (id + name)
                    $allCustomers = isset($customers) ? collect($customers)->map(fn($c) => ['id' => $c->id, 'name' => $c->name]) : collect([]);
                @endphp
                <label for="select-pilih-customer" class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Customer</label>
                <select id="select-pilih-customer" class="w-full h-11 px-3 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-100 focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
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
                        <i class="fas fa-hashtag text-amber-500 mr-1"></i>
                        No Invoice Berikutnya
                    </label>
                    <div class="relative">
                        <input type="number" id="next-number-pick" min="1" placeholder="1000" class="w-full h-11 px-3 pl-10 rounded-lg border-2 border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-100 focus:ring-2 focus:ring-amber-500 focus:border-amber-500" />
                        <span class="absolute left-3 inset-y-0 flex items-center text-gray-400 font-bold">#</span>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Contoh: 1000, 2000, 5000</p>
                </div>
            </div>
            <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700/50 flex items-center justify-end gap-3">
                <button type="button" id="btn-cancel-pilih-customer" class="px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600">Batal</button>
                <button type="button" id="btn-confirm-pilih-customer" class="px-4 py-2 rounded-lg bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white">Lanjut</button>
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
    const createUrl = "{{ route('po.create') }}";
    const quickCreateUrl = "{{ route('invoice.quick-create') }}";
    const setNextNumberUrl = "{{ route('invoice.set-next-number') }}";
    const deleteUrlTemplate = "{{ route('po.destroy', ['po' => 0, 'from' => 'invoice', 'group' => 1]) }}";


    // === Auto update counter (tanpa refresh) ===
    function recalcCounter() {
        try {
            // Hitung hanya baris data yang benar (punya attribute data-po-number)
            const dataRows = tbody ? Array.from(tbody.querySelectorAll('tr[data-po-number]')) : [];
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
        const titleEl = document.getElementById('title-pilih-customer');
        const headerEl = document.getElementById('header-pilih-customer');
        const subtitleEl = document.getElementById('subtitle-pilih-customer');
        const confirmBtn = document.getElementById('btn-confirm-pilih-customer');
        const selectEl = document.getElementById('select-pilih-customer');
        titleEl.textContent = mode === 'atur' ? 'Pilih Customer untuk Atur No Invoice' : 'Pilih Customer untuk Tambah No Invoice';
        // Toggle theme: biru utk tambah, oranye utk atur
        if (mode === 'tambah') {
            headerEl.className = 'px-6 py-5 bg-gradient-to-r from-indigo-600 to-blue-600 text-white';
            subtitleEl.className = 'text-indigo-100 text-sm';
            confirmBtn.className = 'px-4 py-2 rounded-lg bg-gradient-to-r from-indigo-600 to-blue-600 hover:from-indigo-700 hover:to-blue-700 text-white';
            // focus ring biru
            selectEl.classList.remove('focus:ring-amber-500','focus:border-amber-500');
            selectEl.classList.add('focus:ring-indigo-500','focus:border-indigo-500');
        } else {
            headerEl.className = 'px-6 py-5 bg-gradient-to-r from-amber-500 to-orange-500 text-white';
            subtitleEl.className = 'text-amber-100 text-sm';
            confirmBtn.className = 'px-4 py-2 rounded-lg bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white';
            // focus ring oranye
            selectEl.classList.remove('focus:ring-indigo-500','focus:border-indigo-500');
            selectEl.classList.add('focus:ring-amber-500','focus:border-amber-500');
        }
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
                if (result.id && result.no_invoice && result.tanggal_display) {
                    const newRow = createNewInvoiceRow({ id: result.id, no_invoice: result.no_invoice, tanggal_display: result.tanggal_display, customer: pickedCustomerName });
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

            showNotification(`Invoice #${data.no_invoice} berhasil dibuat`, 'success');

            // Update seed ke nomor terbaru dari server
            if (data.no_invoice) {
                const n = parseInt(data.no_invoice);
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
        tr.setAttribute('data-invoice-number', String(data.no_invoice ?? ''));
        tr.setAttribute('data-invoice-no', String(data.no_invoice ?? '-'));
        tr.setAttribute('data-customer', String(data.customer || ''));
        
        // Pasang handler double-click agar langsung bisa ke form input PO
        tr.ondblclick = function() {
            if (typeof goToInputPO === 'function') {
                goToInputPO(String(data.no_invoice || ''), String(data.customer || ''));
            }
        };
        
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
                    ${data.no_invoice}
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
            <td class="hidden">
                <!-- Kolom placeholder (sebelumnya N/A) disembunyikan untuk baris draft invoice -->
            </td>
            <td class="px-4 py-4 whitespace-nowrap">
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-sm font-medium bg-purple-100 dark:bg-purple-900/50 text-purple-800 dark:text-purple-200">
                    0 pcs
                </span>
            </td>
            <td class="px-4 py-4 whitespace-nowrap">
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
            <!-- Status (default: Panding) -->
            <td class="px-4 py-4 pl-6 pr-8 whitespace-nowrap text-center" style="width: 120px;">
                <button type="button"
                        class="inline-flex items-center justify-center w-[90px] px-3 py-2 text-xs font-medium rounded-md border bg-amber-50 text-amber-700 border-amber-300 dark:bg-amber-900/20 dark:text-amber-400 dark:border-amber-600"
                        disabled>
                    <div class="flex items-center justify-center space-x-1.5">
                        <svg class="w-3 h-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="font-medium text-xs">Panding</span>
                    </div>
                </button>
            </td>
            <td class="px-4 py-3 pl-8 whitespace-nowrap text-center action-col" style="width: 100px;">
                <div class="action-cell">
                    <div class="hidden sm:flex items-center justify-center gap-1.5">
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
                        <form method="POST" action="${deleteUrlTemplate.replace('/0', '/' + data.id)}" class="flex-1" onsubmit="event.stopPropagation(); return confirm('Yakin ingin menghapus data invoice ini?')">
                            <input type="hidden" name="_token" value="${document.querySelector('meta[name=csrf-token]')?.getAttribute('content') || ''}">
                            <input type="hidden" name="_method" value="DELETE">
                            <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-3 py-2 rounded-lg bg-[#DC2626] text-white shadow-sm hover:shadow-md transition-all duration-200 active:scale-[.99]">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4">
                                    <polyline points="3 6 5 6 21 6"/>
                                    <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                                    <path d="M10 11v6"/>
                                    <path d="M14 11v6"/>
                                    <path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0  0 1 1 1v2"/>
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

    // Sort seluruh tabel ascending berdasarkan data-invoice-number
    function sortTableAscending() {
        const rows = Array.from(tbody.querySelectorAll('tr[data-invoice-number]'));
        rows.sort((a, b) => {
            const na = parseInt(a.getAttribute('data-invoice-number') || '0');
            const nb = parseInt(b.getAttribute('data-invoice-number') || '0');
            return na - nb; // kecil ke besar (1, 2, 3, ... 10, 11)
        });
        rows.forEach(r => tbody.appendChild(r));
    }

    // Function untuk scroll ke baris baru dan highlight
    function scrollToAndHighlightRow(row) {
        // Tunggu sebentar agar sort selesai
        setTimeout(() => {
            // Scroll ke baris
            row.scrollIntoView({ behavior: 'smooth', block: 'center' });
            
            // Tambahkan highlight sementara
            row.classList.add('bg-yellow-100', 'dark:bg-yellow-900/30');
            
            // Hilangkan highlight setelah 3 detik
            setTimeout(() => {
                row.classList.remove('bg-yellow-100', 'dark:bg-yellow-900/30');
            }, 3000);
        }, 300);
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

    // Function untuk double-click row -> masuk ke halaman Input PO
    window.goToInputPO = function(invoiceNumber) {
        if (!invoiceNumber) {
            showNotification('Nomor invoice tidak valid', 'error');
            return;
        }
        const url = '{{ route("po.create") }}?from=invoice&invoice_number=' + invoiceNumber;
        window.location.href = url;
    };

    // Function untuk buka modal edit (tetap di halaman Data Invoice)
    window.openEditModal = function(id, invoiceNumber, customer, tanggalInvoice, customerId) {
        // Tampilkan modal edit
        const modal = document.getElementById('modal-edit-invoice');
        const form = document.getElementById('form-edit-invoice');
        if (modal && form) {
            // Set form action dengan ID
            form.action = `/po/${id}/update-invoice`;
            
            // Isi data ke form modal
            document.getElementById('edit-invoice-id').value = id;
            document.getElementById('edit-invoice-number').value = invoiceNumber;
            
            // Set tanggal invoice (format Y-m-d untuk input date)
            if (tanggalInvoice) {
                // Convert dari d/m/Y ke Y-m-d
                const parts = tanggalInvoice.split('/');
                if (parts.length === 3) {
                    const tanggalFormatted = `${parts[2]}-${parts[1].padStart(2, '0')}-${parts[0].padStart(2, '0')}`;
                    document.getElementById('edit-tanggal-invoice').value = tanggalFormatted;
                }
            }
            
            // Tampilkan modal
            modal.classList.remove('hidden');
        }
    };

    // Function untuk tutup modal edit
    window.closeEditModal = function() {
        const modal = document.getElementById('modal-edit-invoice');
        if (modal) {
            modal.classList.add('hidden');
        }
    };
});
</script>
@endpush

<!-- Modal Edit Invoice (Tampilan seperti form employee) -->
<div id="modal-edit-invoice" class="fixed inset-0 z-50 hidden" style="background-color: rgba(0, 0, 0, 0.5);">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="modal-employee relative bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-md max-h-[90vh] overflow-y-auto">
            <!-- Header -->
            <div class="sticky top-0 flex items-center justify-between p-5 border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 rounded-t-2xl">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                    <i class="fas fa-edit text-blue-500 mr-2"></i>Edit Invoice
                </h3>
                <button type="button" onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <!-- Content -->
            <form id="form-edit-invoice" method="POST" action="">
                @csrf
                @method('PUT')
                <input type="hidden" id="edit-invoice-id" name="id">
                
                <div class="p-6 space-y-4">
                    <!-- No Invoice (readonly) -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            No Invoice
                        </label>
                        <input type="text" id="edit-invoice-number" name="no_invoice" readonly
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white cursor-not-allowed">
                    </div>

                    

                    <!-- Tanggal Invoice -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Tanggal Invoice <span class="text-red-500">*</span>
                        </label>
                        <input type="date" id="edit-tanggal-invoice" name="tanggal_invoice" required
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               style="color-scheme: dark;">
                    </div>
                </div>

                <!-- Footer -->
                <div class="flex items-center justify-end gap-3 p-5 border-t border-gray-200 dark:border-gray-700">
                    <button type="button" onclick="closeEditModal()"
                            class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i class="fas fa-save mr-1"></i>Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
