@extends('layouts.app')
@php($hideSidebar = true)

@section('title', 'Data Purchase Order')

@section('content')
<div class="px-2 sm:px-4 py-3 sm:py-4">
    <!-- Header Section -->
    <div class="rounded-lg shadow-lg p-3 sm:p-4 mb-3 sm:mb-4 bg-gradient-to-r from-slate-50 to-slate-100 border border-gray-200 dark:border-transparent dark:bg-gradient-to-r dark:from-gray-700 dark:to-gray-800">
        <div class="flex flex-col space-y-3 sm:flex-row sm:justify-between sm:items-center sm:space-y-0">
            <div class="flex items-center space-x-2">
                <svg class="w-5 h-5 sm:w-6 sm:h-6 text-gray-700 dark:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <div>
                    <h1 class="text-base sm:text-xl font-bold text-gray-900 dark:text-white">Data PO</h1>
                    <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-200">Kelola Data PO dengan mudah</p>
                </div>
            </div>
            </div>
            
            <!-- Export Controls -->
            <div class="flex flex-col space-y-2 sm:flex-row sm:items-center sm:justify-end sm:space-y-0 sm:space-x-2 w-full">
                <div id="exportControls" class="hidden bg-gray-100 text-gray-700 dark:bg-white/20 dark:text-white backdrop-blur-sm rounded-lg px-2 py-1 text-center sm:text-left">
                    <span id="selectedCount" class="font-medium text-xs">0 dipilih</span>
                </div>
                <!-- Export Surat Jalan -->
                <button id="exportBtn" onclick="exportSelected('surat_jalan')"
                        class="w-full sm:w-auto h-9 px-4 rounded-lg font-semibold text-white bg-green-600 hover:bg-green-700 transition-colors text-sm inline-flex items-center justify-center leading-none shadow-sm">
                    <span class="mr-2 inline-flex items-center px-1.5 py-0.5 rounded bg-white/20 text-[10px] font-bold tracking-wide">SJ</span>
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Export Surat Jalan
                </button>
                <!-- Export Tanda Terima -->
                <button id="exportBtnTT" onclick="exportSelected('tanda_terima')"
                        class="w-full sm:w-auto h-9 px-4 rounded-lg font-semibold text-emerald-700 dark:text-emerald-300 bg-transparent border border-emerald-600 hover:bg-emerald-50 dark:hover:bg-white/10 transition-colors text-sm inline-flex items-center justify-center leading-none shadow-sm">
                    <span class="mr-2 inline-flex items-center px-1.5 py-0.5 rounded bg-emerald-600 text-white text-[10px] font-bold tracking-wide">TT</span>
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Export Tanda Terima
                </button>
                <button type="button" onclick="generateInvoice()" class="w-full sm:w-auto h-9 px-4 rounded-lg font-medium text-white bg-blue-600 hover:bg-blue-700 transition-colors text-sm inline-flex items-center justify-center leading-none ml-0 sm:ml-0">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Invoice
                </button>
                <button type="button" onclick="downloadInvoicePDF()" class="w-full sm:w-auto h-9 px-4 rounded-lg font-medium text-white bg-red-600 hover:bg-red-700 transition-colors text-sm inline-flex items-center justify-center leading-none">
                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3M16 6l-4-4-4 4M4 8v10a2 2 0 002 2h12a2 2 0 002-2V8" />
                    </svg>
                    Download PDF
                </button>
            </div>
        </div>
    </div>

    <!-- Table Section -->
    
        <!-- Ringkasan Total PO per Bulan -->
        <div class="mb-3 sm:mb-4">
            @php($namaBulanFull=['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'])
            @php($tahunTerpilihLocal = (int) (request('year') ?? ($tahunNow ?? now()->format('Y'))))

            <!-- Header ringkasan + filter tahun + kembali -->
            <div class="flex items-center justify-between mb-2">
                <h2 class="text-sm sm:text-base font-semibold text-gray-800 dark:text-slate-100">Ringkasan Total PO per Bulan</h2>
                <div class="flex items-center gap-2">
                    <!-- Link Pilih Tahun -->
                    <button type="button" onclick="openYearModal()" 
                            class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-indigo-600 bg-indigo-50 border border-indigo-200 rounded-full hover:bg-indigo-100 hover:text-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:bg-indigo-900/30 dark:text-indigo-300 dark:border-indigo-700 dark:hover:bg-indigo-900/50">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        Pilih Tahun ({{ $tahunTerpilihLocal }})
                    </button>
                    <!-- Back to Data Invoice (di samping tombol tahun) -->
                    <a href="{{ route('po.invoice.index') }}" 
                       class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-full hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-300 dark:bg-slate-800 dark:text-gray-200 dark:border-slate-600 dark:hover:bg-slate-700">
                        <i class="fa-solid fa-arrow-left mr-1.5"></i>
                        Kembali ke Data Invoice
                    </a>
                </div>
            </div>

            <!-- Grid bulan yang bisa diklik -->
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-2">
                @for($m=1;$m<=12;$m++)
                    @php($stat = isset($monthlyStats) ? ($monthlyStats[$m] ?? null) : null)
                    @php($isActive = ((int)($bulanNow ?? now()->format('n'))) === $m)
                    <a href="{{ route('suratjalan.index', ['month' => $m, 'year' => $tahunTerpilihLocal]) }}" class="block focus:outline-none focus:ring-2 focus:ring-indigo-500 rounded-lg">
                        <div class="p-2 rounded-lg border text-xs sm:text-sm transition-colors hover:border-indigo-400 hover:bg-indigo-50 dark:hover:bg-slate-700/40
                                    {{ $isActive ? 'bg-yellow-50 border-yellow-300 dark:bg-yellow-900/20 dark:border-yellow-600' : 'bg-white border-gray-200 dark:bg-slate-800 dark:border-slate-700' }}">
                            <div class="flex items-center justify-between">
                                <span class="font-semibold text-gray-700 dark:text-slate-200">{{ $namaBulanFull[$m-1] }}</span>
                                @if($isActive)
                                    <span class="text-[10px] px-1.5 py-0.5 rounded bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100">Bulan dipilih</span>
                                @endif
                            </div>
                            <div class="mt-1 flex items-center justify-between">
                                <span class="text-[11px] text-gray-500 dark:text-slate-300">Transaksi</span>
                                <span class="font-medium text-gray-700 dark:text-slate-100">{{ (int)($stat->total_count ?? 0) }}</span>
                            </div>
                            <div class="mt-0.5 flex items-center justify-between">
                                <span class="text-[11px] text-gray-500 dark:text-slate-300">Total</span>
                                <span class="font-semibold text-green-700 dark:text-green-400">Rp {{ number_format((float)($stat->total_sum ?? 0), 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </a>
                @endfor
            </div>
        </div>
        
        
        <div class="w-full">
            <div class="overflow-x-auto responsive-scroll">
            <table class="w-full table-auto text-[11px] sm:text-xs break-words min-w-[720px]">
                <thead class="bg-gray-100 dark:bg-slate-700">
                    <tr>
                        <th class="py-1.5 px-1.5 text-left text-xs font-medium text-gray-600 dark:text-slate-200 uppercase tracking-tight border-r border-gray-200 dark:border-slate-700 w-8">
                            <span class="sr-only">Pilih</span>
                        </th>
                        <th class="py-1.5 px-1.5 text-left text-xs font-medium text-gray-600 dark:text-slate-200 uppercase tracking-tight border-r border-gray-200 dark:border-slate-700">
                            <div class="flex items-center space-x-1">
                                <svg class="w-3 h-3 text-gray-500 dark:text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <span class="hidden sm:inline">Tanggal</span>
                                <span class="sm:hidden">Tgl</span>
                            </div>
                        </th>
                        <th class="py-1.5 px-1.5 text-left text-xs font-medium text-gray-600 dark:text-slate-200 uppercase tracking-tight border-r border-gray-200 dark:border-slate-700 hidden sm:table-cell">
                            <div class="flex items-center space-x-1">
                                <svg class="w-3 h-3 text-gray-500 dark:text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <span class="hidden sm:inline">No PO</span>
                                <span class="sm:hidden">PO</span>
                            </div>
                        </th>
                        <th class="py-1.5 px-1.5 text-left text-xs font-medium text-gray-600 dark:text-slate-200 uppercase tracking-tight border-r border-gray-200 dark:border-slate-700">
                            <span>Customer</span>
                        </th>
                        <th class="py-1.5 px-1.5 text-left text-xs font-medium text-gray-600 dark:text-slate-200 uppercase tracking-tight border-r border-gray-200 dark:border-slate-700">
                            <div class="flex items-center space-x-1">
                                <svg class="w-3 h-3 text-gray-500 dark:text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <span class="hidden sm:inline">Surat Jalan</span>
                                <span class="sm:hidden">No SJ</span>
                            </div>
                        </th>
                        <th class="py-1.5 px-1.5 text-left text-xs font-medium text-gray-600 dark:text-slate-200 uppercase tracking-tight border-r border-gray-200 dark:border-slate-700 hidden sm:table-cell">
                            <div class="flex items-center space-x-1">
                                <svg class="w-3 h-3 text-gray-500 dark:text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <span class="hidden sm:inline">Invoice</span>
                                <span class="sm:hidden">Inv</span>
                            </div>
                        </th>
                        <th class="py-1.5 px-1.5 text-center text-xs font-medium text-gray-600 dark:text-slate-200 uppercase tracking-tight w-12">
                            <span>Aksi</span>
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-slate-800 divide-y divide-gray-200 dark:divide-slate-700">
                    @forelse($suratjalan as $index => $pos)
                    <tr class="hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors">
                        <!-- Made all table cells much more compact with minimal padding -->
                        <td class="py-1 px-1.5 whitespace-nowrap text-xs border-r border-gray-200 dark:border-slate-700">
                            <input type="checkbox" name="selected_ids[]" value="{{ $pos->id }}" class="row-radio border-gray-300 dark:border-slate-600 text-gray-600 dark:text-slate-200 focus:ring-gray-500 dark:focus:ring-slate-500 w-3 h-3 bg-white dark:bg-slate-800">
                        </td>
                        <td class="py-1 px-1.5 whitespace-nowrap text-xs text-gray-900 dark:text-slate-200 border-r border-gray-200 dark:border-slate-700">
                            <span class="inline-flex items-center px-1 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800 dark:bg-slate-600 dark:text-slate-200">
                                {{ \Carbon\Carbon::parse($pos->tanggal_po)->format('d/m/y') }}
                            </span>
                        </td>
                        <td class="py-1 px-1.5 whitespace-normal text-xs text-gray-900 dark:text-slate-200 border-r border-gray-200 dark:border-slate-700 hidden sm:table-cell">
                            <span class="inline-flex items-center px-1 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800 dark:bg-slate-600 dark:text-slate-200">
                                {{ $pos->no_po }}
                            </span>
                        </td>
                        <td class="py-1 px-1.5 whitespace-normal text-xs text-gray-900 dark:text-slate-200 border-r border-gray-200 dark:border-slate-700">
                            <span class="font-medium block">
                                {{ $pos->customer }}
                            </span>
                        </td>
                        <td class="py-1 px-1.5 whitespace-normal text-xs text-gray-900 dark:text-slate-200 border-r border-gray-200 dark:border-slate-700">
                            <span class="inline-flex items-center px-1 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800 dark:bg-slate-600 dark:text-slate-200">
                                {{ $pos->no_surat_jalan }}
                            </span>
                        </td>
                        <td class="py-1 px-1.5 whitespace-normal text-xs text-gray-900 dark:text-slate-200 border-r border-gray-200 dark:border-slate-700 hidden sm:table-cell">
                            <span class="inline-flex items-center px-1 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800 dark:bg-slate-600 dark:text-slate-200">
                                {{ $pos->no_invoice ?? '-' }}
                            </span>
                        </td>
                        <td class="py-1 px-1.5 text-xs font-medium text-center">
                            <x-table.action-buttons 
                                onEdit="window.editSuratJalan({{ $pos->id }}, {!! json_encode($pos->tanggal_po) !!}, {!! json_encode($pos->customer) !!}, {!! json_encode($pos->alamat_1) !!}, {!! json_encode($pos->alamat_2) !!}, {!! json_encode($pos->no_surat_jalan) !!}, {!! json_encode($pos->no_po) !!}, {!! json_encode($pos->kendaraan) !!}, {!! json_encode($pos->no_polisi) !!}, {{ $pos->qty ?? 'null' }}, {!! json_encode($pos->qty_jenis) !!}, {!! json_encode($pos->produk_id) !!}, {{ $pos->total ?? 'null' }}, {!! json_encode($pos->pengirim) !!})"
                                deleteAction="{{ url('/suratjalan/' . $pos->id) }}"
                                confirmText="Apakah Anda yakin ingin menghapus data surat jalan ini?"
                                :useMenu="true"
                            />
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-6 text-center">
                            <div class="flex flex-col items-center justify-center space-y-2">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <p class="text-gray-500 text-sm font-medium">Belum ada Data PO</p>
                                <p class="text-gray-400 text-xs">Data akan muncul setelah Anda menambahkan Data PO pertama</p>
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

<!-- Export Form -->
<form id="exportForm" action="{{ route('suratjalan.export') }}" method="POST" style="display: none;">
    @csrf
    <input type="hidden" name="selected_ids" id="selectedIds">
    <input type="hidden" name="export_type" id="exportType" value="surat_jalan">
</form>

<!-- PDF Download Form (persistent to avoid double-click issue) -->
<form id="pdfForm" action="{{ route('suratjalan.invoice.pdf') }}" method="POST" style="display: none;">
    @csrf
    <input type="hidden" name="selected_ids" id="selectedIdsPdf">
    <!-- keep persistent form to ensure stable submission in all browsers -->
    <button type="submit"></button>
    <!-- empty submit button for accessibility -->
    </form>

<!-- Delete Form dihapus: digantikan oleh komponen x-table.action-buttons -->

<!-- Edit Surat Jalan Modal -->
<div id="editModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Edit Data PO</h3>
                <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <form id="editForm" method="POST">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal PO</label>
                        <input type="date" id="edit_tanggal_po" name="tanggal_po" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Customer</label>
                        <input type="text" id="edit_customer" name="customer" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Alamat 1</label>
                        <input type="text" id="edit_alamat_1" name="alamat_1" required
                               placeholder="Masukkan alamat lengkap"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Alamat 2</label>
                        <input type="text" id="edit_alamat_2" name="alamat_2"
                               placeholder="Alamat tambahan (opsional)"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">No Surat Jalan</label>
                        <input type="text" id="edit_no_surat_jalan" name="no_surat_jalan" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">No PO</label>
                        <input type="text" id="edit_no_po" name="no_po" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kendaraan</label>
                        <input type="text" id="edit_kendaraan" name="kendaraan" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">No Polisi</label>
                        <input type="text" id="edit_no_polisi" name="no_polisi" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Qty</label>
                        <input type="number" id="edit_qty" name="qty" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Jenis</label>
                        <input type="text" id="edit_qty_jenis" name="qty_jenis" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Produk</label>
                        <select id="edit_produk_id" name="produk_id" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500">
                            <option value="">Pilih Produk</option>
                            @if(isset($produk))
                                @foreach($produk as $p)
                                    <option value="{{ $p->id }}">{{ $p->nama_produk }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Total</label>
                        <input type="number" id="edit_total" name="total" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500">
                    </div>
                    
                    <!-- Added Pengirim input field to edit modal -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Pengirim</label>
                        <input type="text" id="edit_pengirim" name="pengirim"
                               placeholder="Nama pengirim"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500">
                    </div>
                </div>
                
                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" onclick="closeEditModal()"
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition-colors">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-yellow-600 text-white rounded-md hover:bg-yellow-700 transition-colors">
                        Update
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Invoice Modal -->
<style>
    /* Force readable text colors inside invoice preview regardless of dark mode */
    #invoiceModal #invoiceContent, 
    #invoiceModal #invoiceContent * {
        color: #111827 !important; /* gray-900 */
    }
    #invoiceModal #invoiceContent h1, 
    #invoiceModal #invoiceContent h2, 
    #invoiceModal #invoiceContent strong {
        color: #111827 !important;
    }
</style>
<!-- Print-only rules: pastikan Ctrl+P hanya mencetak invoice frame A4 -->
<style>
@media print {
  /* Sembunyikan semua elemen halaman kecuali frame A4 invoice */
  body * { visibility: hidden !important; }
  .a4-page, .a4-page * { visibility: visible !important; }

  /* Posisikan frame A4 di pojok kiri-atas halaman cetak */
  .a4-page {
    position: absolute !important;
    left: 0; top: 0;
    width: 210mm !important;
    min-height: 297mm !important;
    margin: 0 !important;
    padding: 6mm !important; /* ruang konten lebih besar */
    box-shadow: 0 0 0 1px #9ca3af !important; /* outline tipis sebagai penanda area cetak */
    background: #fff !important;
    overflow: visible !important;
  }

  /* Konten invoice tetap 190mm dan ditengah */
  #invoiceContent {
    width: 180mm !important; /* sangat aman dari cut-off kanan/kiri */
    margin-left: 0 !important;  /* lebih condong ke kiri sesuai permintaan */
    margin-right: auto !important;
    padding: 0 !important;
    background: #fff !important;
    -webkit-print-color-adjust: exact !important;
    print-color-adjust: exact !important;
  }

  /* Hindari kepotong di tengah halaman untuk baris dan sel tabel */
  table { page-break-inside: auto !important; }
  thead { display: table-header-group; }
  tfoot { display: table-footer-group; }
  tr, td, th { page-break-inside: avoid !important; }

  /* Atur ukuran halaman dan margin lebih lega, khususnya kanan */
  /* Margin cetak: kurangi bottom agar tidak ada ruang kosong berlebih */
  @page { size: A4 portrait; margin: 16mm 20mm 10mm 10mm; }
}
</style>
<div id="invoiceModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-5 mx-auto p-5 border w-11/12 md:w-4/5 lg:w-3/4 xl:w-2/3 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <!-- Modal Header with Controls -->
            <div class="flex items-center justify-between mb-4">
                <div class="flex space-x-2 ml-auto items-center">
                    <button onclick="closeInvoiceModal()" class="text-gray-400 hover:text-gray-600" aria-label="Close">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                    <button onclick="printInvoice()" class="w-full sm:w-auto h-9 px-4 rounded-lg font-medium text-white bg-blue-600 hover:bg-blue-700 transition-colors text-sm inline-flex items-center justify-center leading-none">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                        </svg>
                        Print
                    </button>
                </div>
            </div>
            <!-- FRAME A4 untuk preview (selalu ukuran A4, konten 190mm di dalamnya) -->
            <div class="a4-page" style="width:210mm; min-height:297mm; margin:0 auto; padding:10mm; background:#fff; box-shadow: 0 0 0 1px #d0d0d0;">
            <div id="invoiceContent" style="width: 190mm; min-height: auto; margin: 0 auto; padding: 0; background: #fff; font-family: DejaVu Sans, Arial, sans-serif; font-size: 12px; line-height: 1.22; color:#000;">
                <style>
                    /* Selaraskan tampilan dengan PDF */
                    #invoiceContent table { width: 100%; table-layout: fixed; border-collapse: collapse; }
                    #invoiceContent thead th { border: 1px solid #000; padding: 4px !important; text-align: center; font-size: 10px; font-weight: 600; line-height: 1.05; }
                    #invoiceContent tbody td { border: 1px solid #000; padding: 4px !important; font-size: 10px; line-height: 1.05; }
                    #invoiceContent tfoot td { border: 1px solid #000; padding: 4px 6px !important; font-size: 10px; line-height: 1.05; }
                    #invoiceContent .info-row td { padding: 0 !important; border: 0; }
                    #invoiceContent .sign-box { width: 170px; margin: 36px auto 0 auto; text-align: center; }
                </style>
                <!-- Header -->
                <div style="display: flex; align-items: flex-start; margin-bottom: 12px; border-bottom: 2px solid #000; padding-bottom: 6px;">
                    <img src="{{ asset('image/LOGO.png') }}" alt="PT. CAM JAYA ABADI Logo" style="height:64px; width:auto; object-fit:contain; margin-right:16px;">
                    <div style="flex: 1;">
                        <h2 style="margin:0; font-size:16px; font-weight:bold; color:#d32f2f;">PT. CAM JAYA ABADI</h2>
                        <p style="margin:2px 0; font-size:9px; line-height:1.2; color: rgb(38,73,186);">
                            <strong>MANUFACTURING PROFESSIONAL WOODEN PALLET</strong><br>
                            <strong>KILN DRYING WOOD WORKING INDUSTRY</strong><br>
                            Factory & Office : Jl. Wahana Bakti No.28, Mangunjaya, Kec. Tambun Sel. Bekasi Jawa Barat<br>
                            17510<br>
                            Telp: (021) 6617 1626 - Fax: (021) 6617 3986
                        </p>
                    </div>
                </div>
                <!-- Customer Info -->
                <div style="margin-bottom: 10px;">
                    <div style="border: 1px solid #000; padding: 6px;">
                        <strong>Kepada Yth.</strong><br>
                        <span id="invoiceCustomer" style="font-weight: bold;"></span>
                        <br>
                        di .
                        <br>
                        <b><span id="invoiceAddress"></span></b>
                    </div>
                </div>
                <!-- Invoice Title -->
                <div style="text-align: center; margin: 10px 0;">
                    <h1 style="font-size: 28px; font-weight: bold; letter-spacing: 3px; margin: 0;">INVOICE</h1>
                </div>
                <!-- Info Row (sesuai PDF) -->
                <table class="info-row" style="width:100%; border-collapse:collapse; margin:0;">
                    <tr>
                        <td style="width:33.33%; text-align:left; vertical-align:bottom; padding:0;"><span style="font-weight:bold;">No. PO : <span id="invoiceNoPO"></span></span></td>
                        <td style="width:33.33%; text-align:center; vertical-align:bottom; padding:0;"><span style="font-weight:bold;">No : <span id="invoiceNo"></span></span></td>
                        <td style="width:33.33%; text-align:right; vertical-align:bottom; padding:0;"><span style="font-weight:bold;">Date : <span id="invoiceDate"></span></span></td>
                    </tr>
                </table>
                <!-- Invoice Table -->
                <table id="invoiceTable" style="width:100%; border-collapse: collapse; margin-top: 4px; margin-bottom: 0; table-layout: fixed;">
                    <thead>
                        <tr>
                            <th id="thDesc">DESCRIPTION</th>
                            <th id="thQty" style="width:13%;">QTY</th>
                            <th id="thUnit" style="width:18%;">UNIT PRICE</th>
                            <th id="thAmt" style="width:16%;">AMMOUNT</th>
                        </tr>
                    </thead>
                    <tbody id="invoiceItems"></tbody>
                    <tfoot>
                        <tr>
                            <td style="text-align:right; font-weight: bold;">SUB TOTAL :</td>
                            <td id="invoiceSubtotalQty" style="text-align: center; font-weight: bold;"></td>
                            <td></td>
                            <td id="invoiceSubtotal" style="text-align:right;"></td>
                        </tr>
                        <tr>
                            <td style="text-align:right; font-weight: bold;">PPN 11% :</td>
                            <td></td>
                            <td></td>
                            <td id="invoicePPN" style="text-align:right;"></td>
                        </tr>
                        <tr>
                            <td style="text-align:right; font-weight: bold;">GRAND TOTAL :</td>
                            <td></td>
                            <td></td>
                            <td id="invoiceGrandTotal" style="text-align:right; font-weight: bold;"></td>
                        </tr>
                    </tfoot>
                </table>
                <!-- Payment Info and Signature (sesuai PDF) -->
                <div style="display: flex; justify-content: space-between; margin-top: 0; align-items:flex-start; gap: 6mm;">
                    <div style="width: 60%;">
                        <p style="margin: 8px 0 0 0; font-size: 10px; line-height: 1.25;">
                            <strong>Pembayaran Mohon Di Transfer Ke rekening</strong><br>
                            <strong>Bank BRI PEJATEN</strong><br>
                            <strong>NO REK : 1182-01-000039-30-3</strong><br>
                            <strong>ATAS NAMA : PT. CAM JAYA ABADI</strong>
                        </p>
                    </div>
                    <div style="width: 35%; margin-left:auto; display:flex; flex-direction:column; align-items:flex-end;">
                        <!-- Wadah tetap agar signature center terhadap teks tanggal -->
                        <div style="width:170px; align-self:flex-end;">
                            <p style="margin: 8px 0 0 0; text-align:right;"><strong>Bekasi, <span id="invoiceDateLocation"></span></strong></p>
                            <!-- Spacer tetap untuk area tanda tangan agar simetris -->
                            <div style="height: 52px;"></div>
                            <div class="signature-stamp" style="display:none; margin: 0 auto; width: 130px; margin-bottom: 20px;">
                                <!-- Logo perusahaan di area tanda tangan disembunyikan sesuai permintaan -->
                                <img src="{{ asset('image/LOGO.png') }}" alt="Company Stamp" style="width: 130px; height: 90px; object-fit: contain; opacity: 0.9;">
                            </div>
                            <p style="margin: 0; font-size: 10px; text-align:center;">
                                <strong><u>NANIK PURWATI</u></strong><br>
                                <span style="font-size: 8px;">DIREKTUR UTAMA</span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Pilih Tahun -->
<div id="yearModal" class="fixed inset-0 z-50 flex items-center justify-center hidden">
    <div class="absolute inset-0 bg-black/40" onclick="closeYearModal()"></div>
    <div class="relative bg-white w-[92vw] max-w-lg rounded-2xl shadow-lg overflow-hidden dark:bg-gray-800">
        <div class="px-5 py-4 border-b flex items-center justify-between dark:border-gray-700">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Pilih Tahun</h3>
            <button type="button" onclick="closeYearModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <div class="p-5">
            <div class="grid grid-cols-4 gap-2 max-h-60 overflow-y-auto">
                @php($selectedYear = (int) (request('year') ?? ($tahunNow ?? now()->format('Y'))))
                @foreach(($allYears ?? []) as $year)
                    <button type="button" onclick="selectYear({{ $year }})" 
                            class="year-btn px-3 py-2 text-sm font-medium rounded-md border transition-colors
                                   {{ $selectedYear === (int) $year ? 
                                      'bg-indigo-600 text-white border-indigo-600' : 
                                      'bg-white text-gray-700 border-gray-300 hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-200 dark:border-gray-600 dark:hover:bg-gray-600' }}">
                        {{ $year }}
                    </button>
                @endforeach
            </div>
        </div>
        <div class="px-5 py-3 border-t bg-gray-50 text-right dark:border-gray-700 dark:bg-gray-900/40">
            <button type="button" onclick="closeYearModal()" 
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500 dark:bg-gray-600 dark:text-gray-200 dark:border-gray-500 dark:hover:bg-gray-500">
                Batal
            </button>
        </div>
    </div>
</div>

<script>
// Modal functions
function openYearModal() {
    document.getElementById('yearModal').classList.remove('hidden');
}

function closeYearModal() {
    document.getElementById('yearModal').classList.add('hidden');
}

function selectYear(year) {
    const currentMonth = {{ (int)($bulanNow ?? now()->format('n')) }};
    
    // Redirect dengan parameter tahun yang dipilih
    const url = new URL(window.location.href);
    url.searchParams.set('year', year);
    url.searchParams.set('month', currentMonth);
    
    window.location.href = url.toString();
}


// Close modal with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeYearModal();
    }
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const rowRadios = document.querySelectorAll('.row-radio');
    const exportBtn = document.getElementById('exportBtn');
    const exportControls = document.getElementById('exportControls');
    const selectedCount = document.getElementById('selectedCount');
    const exportForm = document.getElementById('exportForm');
    const selectedIdsInput = document.getElementById('selectedIds');

    // Handle individual row selection (checkbox)
    rowRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            updateExportControls();
        });
    });

    function updateExportControls() {
        const selected = document.querySelectorAll('.row-radio:checked');
        const count = selected.length;
        if (count > 0) {
            exportControls.classList.remove('hidden');
            selectedCount.textContent = `${count} dipilih`;
        } else {
            exportControls.classList.add('hidden');
        }
        // Always allow export (no selection => export all)
        exportBtn.disabled = false;
    }
});

function exportSelected(type = 'surat_jalan') {
    const selected = document.querySelector('.row-radio:checked');
    // Wajib pilih satu data seperti tombol Invoice dan PDF
    if (!selected) {
        alert(type === 'tanda_terima' ? 'Pilih satu data untuk export Tanda Terima' : 'Pilih satu data untuk export Surat Jalan');
        return;
    }
    const selectedIds = [selected.value];
    const exportForm = document.getElementById('exportForm');
    const selectedIdsInput = document.getElementById('selectedIds');
    const exportTypeInput = document.getElementById('exportType');

    // Tentukan tombol yang digunakan untuk loading state
    const btn = type === 'tanda_terima' ? document.getElementById('exportBtnTT') : document.getElementById('exportBtn');

    // Show loading state pada tombol terkait
    if (btn) {
        btn.innerHTML = `
            <svg class="animate-spin -ml-1 mr-1 h-3 w-3 text-yellow-100" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Mengekspor...
        `;
        btn.disabled = true;
    }

    // Set tipe export & selected IDs
    if (exportTypeInput) exportTypeInput.value = type;
    selectedIdsInput.value = JSON.stringify(selectedIds);
    exportForm.submit();

    // Reset tombol setelah beberapa detik (fallback bila browser tidak auto reset)
    setTimeout(() => {
        if (!btn) return;
        if (type === 'tanda_terima') {
            btn.innerHTML = `
                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Export Tanda Terima
            `;
        } else {
            btn.innerHTML = `
                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Export Surat Jalan
            `;
        }
        btn.disabled = false;
    }, 3000);
}

// Hapus fungsi deleteSuratJalan(): aksi hapus kini ditangani oleh komponen x-table.action-buttons

// Updated editSuratJalan function to handle pengirim parameter
function editSuratJalan(id, tanggal, customer, alamat1, alamat2, noSuratJalan, noPo, kendaraan, noPolisi, qty, jenis, produkId, total, pengirim) {
    // Set form action
    document.getElementById('editForm').action = "{{ url('/suratjalan') }}/" + id;
    
    // Fill form fields
    document.getElementById('edit_tanggal_po').value = tanggal;
    document.getElementById('edit_customer').value = customer;
    document.getElementById('edit_alamat_1').value = alamat1 || '';
    document.getElementById('edit_alamat_2').value = alamat2 || '';
    document.getElementById('edit_no_surat_jalan').value = noSuratJalan;
    document.getElementById('edit_no_po').value = noPo;
    document.getElementById('edit_kendaraan').value = kendaraan;
    document.getElementById('edit_no_polisi').value = noPolisi;
    document.getElementById('edit_qty').value = qty;
    document.getElementById('edit_qty_jenis').value = jenis;
    document.getElementById('edit_pengirim').value = pengirim || '';
    setTimeout(() => {
        document.getElementById('edit_produk_id').value = produkId;
    }, 100);
    document.getElementById('edit_total').value = total;
    
    // Show modal
    document.getElementById('editModal').classList.remove('hidden');
}

// Pastikan fungsi tersedia di global scope untuk dipanggil dari inline handler/komponen
window.editSuratJalan = editSuratJalan;

function closeEditModal() {
    document.getElementById('editModal').classList.add('hidden');
}

document.getElementById('editModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeEditModal();
    }
});

// Generate Invoice dengan format PT. CAM JAYA ABADI
function generateInvoice() {
    const selectedChecks = Array.from(document.querySelectorAll('.row-radio:checked'));
    if (selectedChecks.length === 0) {
        alert('Pilih minimal 1 data untuk membuat invoice');
        return;
    }
    const selectedIds = selectedChecks.map(el => el.value);

    fetch("{{ route('suratjalan.invoice.data') }}", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": "{{ csrf_token() }}"
        },
        body: JSON.stringify({ ids: selectedIds })
    })
    .then(res => res.json())
    .then(res => {
        if (res.data && res.data.length > 0) {
            populateInvoice(res.data);
            document.getElementById('invoiceModal').classList.remove('hidden');
        } else {
            alert('Data tidak ditemukan!');
        }
    });
}

function populateInvoice(data) {
    // Data customer & alamat dari suratjalan pertama (PO header)
    const first = data[0];
    // Tentukan No Invoice terbaru dari semua data terpilih
    const latestPO = data.reduce((best, cur) => {
        const getNum = (o) => {
            const s = (o && o.no_invoice ? String(o.no_invoice) : '').split('/')[0];
            const n = parseInt(s, 10);
            return Number.isFinite(n) ? n : null;
        };
        const nb = getNum(best);
        const nc = getNum(cur);
        if (nb !== null && nc !== null) {
            return nc > nb ? cur : best;
        }
        // Fallback ke tanggal_po jika nomor tidak bisa diparsing
        if (best && best.tanggal_po && cur && cur.tanggal_po) {
            return new Date(cur.tanggal_po) > new Date(best.tanggal_po) ? cur : best;
        }
        // Fallback terakhir: pilih yang memiliki no_invoice terisi
        if (!best || !best.no_invoice) return cur;
        return best;
    }, first);
    document.getElementById('invoiceCustomer').textContent = first.customer;
    let addressText = '';
    if (first.alamat_1) addressText += first.alamat_1;
    if (first.alamat_2) addressText += (addressText ? ', ' : '') + first.alamat_2;
    
    // Format alamat dengan koma dan titik yang proper
    if (addressText) {
        // Pastikan ada titik di akhir jika belum ada
        if (!addressText.endsWith('.')) {
            addressText += '.';
        }
        // Capitalize first letter setelah koma
        addressText = addressText.replace(/,\s*([a-z])/g, ', $1'.toUpperCase());
        // Capitalize first letter
        addressText = addressText.charAt(0).toUpperCase() + addressText.slice(1);
    }
    
    document.getElementById('invoiceAddress').textContent = addressText || '-';

    // Invoice detail - gunakan tanggal dari database
    const dbDate = new Date(first.tanggal_po);
    const monthNames = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 
                       'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    const invoiceDate = `${dbDate.getDate()} ${monthNames[dbDate.getMonth()]} ${dbDate.getFullYear()}`;
    
    // No PO dari database column no_po
    document.getElementById('invoiceNoPO').textContent = first.no_po || '-';
    // No Invoice pilih yang terbaru (lihat latestPO)
    document.getElementById('invoiceNo').textContent = (latestPO && latestPO.no_invoice) ? latestPO.no_invoice : (first.no_invoice || '-');
    document.getElementById('invoiceDate').textContent = invoiceDate;
    document.getElementById('invoiceDateLocation').textContent = invoiceDate;

    // Tabel item - flatten all PO items
    const itemsContainer = document.getElementById('invoiceItems');
    itemsContainer.innerHTML = '';
    let subtotal = 0, totalQty = 0;
    let unitTypes = new Set(); // Track different unit types

    // Flatten items dari tiap PO sambil membawa No PO parent
    const allItems = [];
    data.forEach(po => {
        if (po.items && Array.isArray(po.items)) {
            po.items.forEach(it => {
                const cloned = { ...it };
                // simpan no_po parent agar bisa ditampilkan di DESCRIPTION
                if (!cloned.no_po && po.no_po) cloned.no_po = po.no_po;
                allItems.push(cloned);
            });
        }
    });

    // Deteksi apakah multi-PO (lebih dari satu No PO terpilih)
    const uniquePO = new Set();
    data.forEach(po => { if (po.no_po) uniquePO.add(String(po.no_po)); });
    const multiPO = uniquePO.size > 1;
    // Set header info No. PO sesuai aturan: kosong saat multi-PO
    document.getElementById('invoiceNoPO').textContent = multiPO ? '' : (first.no_po || '-');

    // Tentukan mode tampilan berdasarkan jumlah baris yang akan dirender (minimal 20)
    const count = allItems.length;
    const renderCount = Math.max(count, 20);
    const mode = renderCount <= 14 ? 'normal' : (renderCount <= 20 ? 'compact' : 'ultra');

    // Skala mengikuti pdf.blade.php agar tetap 1 halaman A4
    const fs  = mode === 'normal' ? '11.2px' : (mode === 'compact' ? '8.8px' : '8.2px');
    const pad = mode === 'normal' ? '6px'    : (mode === 'compact' ? '2.4px' : '2.2px');
    const lh  = mode === 'normal' ? 1.22     : (mode === 'compact' ? 1.05   : 1.04);
    const wQty = mode === 'normal' ? '15%' : (mode === 'compact' ? '13%' : '12%');
    const wUnit = mode === 'normal' ? '20%' : (mode === 'compact' ? '18%' : '16%');
    const wAmt = mode === 'normal' ? '20%' : (mode === 'compact' ? '18%' : '16%');

    // Set table fixed layout dan lebar kolom dinamis
    const tableEl = document.getElementById('invoiceTable');
    if (tableEl) tableEl.style.tableLayout = 'fixed';
    const thDesc = document.getElementById('thDesc');
    const thQty = document.getElementById('thQty');
    const thUnit = document.getElementById('thUnit');
    const thAmt = document.getElementById('thAmt');
    if (thQty) thQty.style.width = wQty;
    if (thUnit) thUnit.style.width = wUnit;
    if (thAmt) thAmt.style.width = wAmt;

    allItems.forEach(it => {
        const qty = parseInt(it.qty || 0);
        const totalAmount = parseInt(it.total || 0);
        const unitPrice = qty > 0 ? Math.round(totalAmount / qty) : 0;
        subtotal += isNaN(totalAmount) ? 0 : totalAmount;
        totalQty += isNaN(qty) ? 0 : qty;

        // Product name from relation 'produk' + No PO di kanan nama
        const produkNameBase = it.produk && (it.produk.nama_produk || it.produk.nama || it.produk.name)
            ? (it.produk.nama_produk || it.produk.nama || it.produk.name)
            : '-';
        const noPoItem = it.no_po || '';
        const produkName = (multiPO && noPoItem) ? `${produkNameBase} (${noPoItem})` : produkNameBase;

        // Use exact database value for qty_jenis
        const jenis = (it.qty_jenis && String(it.qty_jenis).trim() !== '' && String(it.qty_jenis) !== '0') ? it.qty_jenis : 'PCS';
        unitTypes.add(jenis);

        const row = document.createElement('tr');
        row.innerHTML = `
            <td style="border: 1px solid #000; padding: ${pad}; vertical-align: top; font-weight: bold; font-size:${fs}; line-height:${lh}; word-break: break-word;">${produkName}</td>
            <td style="border: 1px solid #000; padding: ${pad}; text-align: center; vertical-align: top; font-weight: bold; font-size:${fs}; line-height:${lh};">${qty} ${jenis}</td>
            <td style="border: 1px solid #000; padding: ${pad}; text-align: right; vertical-align: top; font-weight: bold; font-size:${fs}; line-height:${lh};">Rp. ${unitPrice.toLocaleString('id-ID')}</td>
            <td style="border: 1px solid #000; padding: ${pad}; text-align: right; vertical-align: top; font-weight: bold; font-size:${fs}; line-height:${lh};">Rp. ${totalAmount.toLocaleString('id-ID')}</td>
        `;
        itemsContainer.appendChild(row);
    });
    
    // Tambah baris kosong agar total minimal 20 baris (selaras PDF)
    const minRows = 20;
    const fillerCount = Math.max(0, minRows - count);
    for (let i = 0; i < fillerCount; i++) {
        const emptyRow = document.createElement('tr');
        emptyRow.innerHTML = `
            <td style=\"border: 1px solid #000; padding: ${pad}; height: 22px;\">&nbsp;</td>
            <td style=\"border: 1px solid #000; padding: ${pad}; text-align: center;\">&nbsp;</td>
            <td style=\"border: 1px solid #000; padding: ${pad}; text-align: right;\">&nbsp;</td>
            <td style=\"border: 1px solid #000; padding: ${pad}; text-align: right;\">&nbsp;</td>
        `;
        itemsContainer.appendChild(emptyRow);
    }
    
    // Totals - use mixed units or most common unit instead of hardcoded "SET"
    const ppn = Math.round(subtotal * 0.11);
    const grandTotal = subtotal + ppn;
    
    // Display total qty with appropriate unit
    let qtyDisplay;
    if (unitTypes.size === 1) {
        // All items have same unit
        qtyDisplay = `${totalQty} ${Array.from(unitTypes)[0]}`;
    } else {
        // Mixed units - show as "Items" or keep original logic but don't hardcode "SET"
        qtyDisplay = `${totalQty} Items`;
    }
    
    document.getElementById('invoiceSubtotalQty').textContent = qtyDisplay;
    document.getElementById('invoiceSubtotal').textContent = subtotal.toLocaleString('id-ID');
    document.getElementById('invoicePPN').textContent = ppn.toLocaleString('id-ID');
    document.getElementById('invoiceGrandTotal').textContent = grandTotal.toLocaleString('id-ID');
}

function closeInvoiceModal() {
    document.getElementById('invoiceModal').classList.add('hidden');
}

function printInvoice() {
    const a4 = document.querySelector('.a4-page');
    const outer = a4 ? a4.outerHTML : `<div class="a4-page"><div id=\"invoiceContent\">${document.getElementById('invoiceContent').innerHTML}</div></div>`;
    const html = `
        <html>
        <head>
            <meta charset="utf-8" />
            <title>Print Invoice</title>
            <style>
                /* Samakan aturan dengan preview agar identik */
                @page { size: A4 portrait; margin: 16mm 20mm 16mm 10mm; }
                html, body { margin: 0; padding: 0; background:#fff; }
                body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
                .a4-page { width:210mm; min-height:297mm; margin:0 auto; padding:6mm; box-shadow: 0 0 0 1px #999; background:#fff; }
                #invoiceContent { width: 180mm; margin-left:0; margin-right:auto; padding:0; font-family: DejaVu Sans, Arial, sans-serif; font-size:12px; line-height:1.22; color:#000; }
                #invoiceContent table { width:100%; table-layout:fixed; border-collapse:collapse; }
                #invoiceContent thead th { border:1px solid #000; padding:4px; text-align:center; font-size:10px; font-weight:600; line-height:1.05; }
                #invoiceContent tbody td { border:1px solid #000; padding:4px; font-size:10px; line-height:1.05; }
                #invoiceContent tfoot td { border:1px solid #000; padding:4px 6px; font-size:10px; line-height:1.05; }
                #invoiceContent .info-row td { padding:0; border:0; }
                /* Hindari kepotongan antar baris */
                thead { display: table-header-group; }
                tfoot { display: table-footer-group; }
                tr, td, th { page-break-inside: avoid; }
            </style>
        </head>
        <body onload="window.print(); window.onafterprint = function(){ window.close(); }">${outer}</body>
        </html>`;
    const w = window.open('', '_blank');
    w.document.open();
    w.document.write(html);
    w.document.close();
}

// Trigger server-side PDF generation for selected Surat Jalan
function downloadInvoicePDF() {
    const selectedChecks = Array.from(document.querySelectorAll('.row-radio:checked'));
    if (selectedChecks.length === 0) {
        alert('Pilih minimal 1 data untuk membuat invoice PDF');
        return;
    }
    const selectedIds = selectedChecks.map(el => el.value);
    const pdfForm = document.getElementById('pdfForm');
    const pdfIds = document.getElementById('selectedIdsPdf');
    pdfIds.value = JSON.stringify(selectedIds);
    pdfForm.submit();
}

// Close modal when clicking outside
document.getElementById('invoiceModal').addEventListener('click', function(e) {
    if (e.target.id === 'invoiceModal') {
        closeInvoiceModal();
    }
});

// Intersep Ctrl+P agar sama dengan tombol Print (pakai konten invoice saja)
window.addEventListener('keydown', function(e) {
    const key = e.key ? e.key.toLowerCase() : '';
    if ((e.ctrlKey || e.metaKey) && key === 'p') {
        e.preventDefault();
        e.stopPropagation();
        try {
            const modal = document.getElementById('invoiceModal');
            const isOpen = modal && !modal.classList.contains('hidden');
            if (isOpen) {
                // Jika modal sudah terbuka, langsung print kontennya
                printInvoice();
            } else {
                // Jika belum terbuka, coba generate dulu (jika ada pilihan data)
                const selected = document.querySelectorAll('.row-radio:checked');
                if (typeof generateInvoice === 'function') {
                    if (selected && selected.length > 0) {
                        generateInvoice();
                        // beri waktu render konten, lalu print
                        setTimeout(() => { printInvoice(); }, 400);
                    } else {
                        // Tidak ada data terpilih, tetap coba print konten jika ada
                        printInvoice();
                    }
                } else {
                    printInvoice();
                }
            }
        } catch (err) {
            // fallback
            printInvoice();
        }
    }
});

</script>

<style>
/* Tooltip style */
.table-tooltip {
    position: relative;
    cursor: pointer;
}
.table-tooltip .tooltip-text {
    visibility: hidden;
    opacity: 0;
    width: max-content;
    max-width: 300px;
    background: #222;
    color: #fff;
    text-align: left;
    border-radius: 4px;
    padding: 6px 10px;
    position: absolute;
    z-index: 10;
    left: 50%;
    top: 110%;
    transform: translateX(-50%);
    font-size: 12px;
    transition: opacity 0.2s;
    white-space: pre-line;
    word-break: break-all;
}
.table-tooltip:hover .tooltip-text,
.table-tooltip:focus .tooltip-text {
    visibility: visible;
    opacity: 1;
}
</style>
@endsection