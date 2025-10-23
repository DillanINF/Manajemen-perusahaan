@extends('layouts.app')
@php($hideSidebar = true)

@section('title', 'Data Purchase Order')

@section('content')
<div class="px-2 sm:px-4 py-3 sm:py-4">
    <script>
        // Pemetaan kode customer dari server untuk header invoice
        window.customerCodes = @json($customerCodes ?? []);
        // invoice_number aktif dari server (bila ada)
        window.currentInvoiceNumber = @json($poNumber ?? null);
    </script>
    <!-- Header Section -->
    <div class="rounded-lg shadow-lg p-3 sm:p-4 mb-3 sm:mb-4 bg-gradient-to-r from-slate-50 to-slate-100 border border-gray-200 dark:border-transparent dark:bg-gradient-to-r dark:from-gray-700 dark:to-gray-800">
        <div class="flex flex-col space-y-3 sm:flex-row sm:justify-between sm:items-center sm:space-y-0">
            <div class="flex items-center space-x-2">
                <svg class="w-5 h-5 sm:w-6 sm:h-6 text-gray-700 dark:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <div>
                    <h1 class="text-base sm:text-xl font-bold text-gray-900 dark:text-white">Data Purchase order</h1>
                    <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-200">Kelola Data Purchase Order dengan mudah</p>
                </div>
            </div>
            <!-- Tombol Back ke Form Input PO -->
           
            </div>
            
            <!-- Export Controls -->
            <div class="flex flex-col space-y-2 sm:flex-row sm:items-center sm:justify-end sm:space-y-0 sm:space-x-2 w-full">
                <div id="exportControls" class="hidden bg-gray-100 text-gray-700 dark:bg-white/20 dark:text-white backdrop-blur-sm rounded-lg px-2 py-1 text-center sm:text-left">
                    <span id="selectedCount" class="font-medium text-xs">0 dipilih</span>
                </div>
                <!-- Professional icon buttons with tooltips -->
                <div class="flex items-center space-x-3">
                    <div class="relative group">
                        <button id="exportBtn" type="button"
                                class="p-3 text-green-600 hover:text-green-700 hover:bg-green-50 dark:hover:bg-green-900/20 transition-all duration-200 rounded-lg">
                            <i class="fas fa-file-excel text-3xl"></i>
                        </button>
                        <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-3 py-1 bg-gray-900 text-white text-xs rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none whitespace-nowrap z-50">
                            Export Data PO
                            <div class="absolute top-full left-1/2 transform -translate-x-1/2 w-0 h-0 border-l-4 border-r-4 border-t-4 border-transparent border-t-gray-900"></div>
                        </div>
                    </div>
                    
                    <div class="relative group">
                        <button id="exportBtnTT" type="button"
                                class="p-3 text-emerald-600 hover:text-emerald-700 hover:bg-emerald-50 dark:hover:bg-emerald-900/20 transition-all duration-200 rounded-lg">
                            <i class="fas fa-receipt text-3xl"></i>
                        </button>
                        <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-3 py-1 bg-gray-900 text-white text-xs rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none whitespace-nowrap z-50">
                            Export Tanda Terima
                            <div class="absolute top-full left-1/2 transform -translate-x-1/2 w-0 h-0 border-l-4 border-r-4 border-t-4 border-transparent border-t-gray-900"></div>
                        </div>
                    </div>
                    
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" type="button"
                                class="p-3 text-blue-600 hover:text-blue-700 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-all duration-200 rounded-lg">
                            <i class="fas fa-file-invoice text-3xl"></i>
                        </button>
                        <!-- Dropdown Menu -->
                        <div x-show="open" 
                             @click.away="open = false"
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="transform opacity-0 scale-95"
                             x-transition:enter-end="transform opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="transform opacity-100 scale-100"
                             x-transition:leave-end="transform opacity-0 scale-95"
                             class="absolute top-full mt-2 left-1/2 transform -translate-x-1/2 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 z-50"
                             style="display: none;">
                            <div class="py-1">
                                <button @click="generateInvoice(); open = false" 
                                        class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-blue-50 dark:hover:bg-blue-900/20 flex items-center gap-2">
                                    <i class="fas fa-eye text-blue-600"></i>
                                    Lihat Invoice
                                </button>
                                <button @click="generateAndPrintInvoice(); open = false" 
                                        class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-blue-50 dark:hover:bg-blue-900/20 flex items-center gap-2">
                                    <i class="fas fa-print text-blue-600"></i>
                                    Print Invoice
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="relative group">
                        <button id="pdfBtn" type="button" onclick="downloadInvoicePDF()"
                                class="p-3 text-red-600 hover:text-red-700 hover:bg-red-50 dark:hover:bg-red-900/20 transition-all duration-200 rounded-lg">
                            <i class="fas fa-file-pdf text-3xl"></i>
                        </button>
                        <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-3 py-1 bg-gray-900 text-white text-xs rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none whitespace-nowrap z-50">
                            Download PDF
                            <div class="absolute top-full left-1/2 transform -translate-x-1/2 w-0 h-0 border-l-4 border-r-4 border-t-4 border-transparent border-t-gray-900"></div>
                        </div>
                    </div>
                </div>
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
                <h2 class="text-sm sm:text-base font-semibold text-gray-800 dark:text-slate-100">
                    Ringkasan Total PO per Bulan
                    @if($poNumber)
                        <span class="ml-2 px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded-full dark:bg-blue-900 dark:text-blue-200">
                            No Invoice: {{ $poNumber }}
                        </span>
                    @endif
                </h2>
                <div class="flex items-center gap-2">
                    <!-- Link Pilih Tahun -->
                    <button type="button" onclick="openYearModal()" 
                            class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-indigo-600 bg-indigo-50 border border-indigo-200 rounded-full hover:bg-indigo-100 hover:text-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:bg-indigo-900/30 dark:text-indigo-300 dark:border-indigo-700 dark:hover:bg-indigo-900/50">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        Pilih Tahun ({{ $tahunTerpilihLocal }})
                    </button>
                    <!-- Navigation buttons (di samping tombol tahun) -->
                    <div class="flex items-center gap-2">
                        <a href="{{ route('invoice.index') }}" 
                           class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-full hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-300 dark:bg-slate-800 dark:text-gray-200 dark:border-slate-600 dark:hover:bg-slate-700">
                            <i class="fa-solid fa-arrow-left mr-1.5"></i>
                            Kembali ke Data Invoice
                        </a>
                        <a href="{{ route('po.create', [
                                'from' => 'invoice',
                                'invoice_number' => (request('invoice_number') ?? ($poNumber ?? (($suratjalan->first()->no_invoice ?? null)))),
                                'reset_fields' => '1'
                            ]) }}" 
                           class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-purple-700 bg-white border border-purple-300 rounded-full hover:bg-purple-50 focus:outline-none focus:ring-2 focus:ring-purple-300 dark:bg-slate-800 dark:text-purple-200 dark:border-purple-600 dark:hover:bg-slate-700">
                            <i class="fa-solid fa-plus mr-1.5"></i>
                            Tambah PO
                            </a>
                    </div>
                </div>
            </div>

            <!-- Grid bulan yang bisa diklik -->
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-2">
                @for($m=1;$m<=12;$m++)
                    @php($stat = isset($monthlyStats) ? ($monthlyStats[$m] ?? null) : null)
                    @php($isActive = ((int)($month ?? now()->format('n'))) === $m)
                    <a href="{{ route('suratjalan.index', ['month' => $m, 'year' => $tahunTerpilihLocal, 'invoice_number' => $poNumber]) }}" class="block focus:outline-none focus:ring-2 focus:ring-indigo-500 rounded-lg">
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
            <table class="w-full table-auto text-[11px] sm:text-xs break-words min-w-[720px] border border-gray-200 dark:border-slate-700">
                <thead class="bg-gray-100 dark:bg-slate-700">
                    <tr>
                        <th class="py-3 px-4 text-center align-middle text-sm font-medium text-gray-600 dark:text-slate-200 uppercase tracking-tight border-r border-gray-200 dark:border-slate-700 w-20">
                            <div class="flex items-center justify-center">
                                <input type="checkbox" id="selectAll" class="border-gray-300 dark:border-slate-600 text-indigo-600 dark:text-indigo-500 focus:ring-indigo-500 dark:focus:ring-indigo-400 w-4 h-4 rounded cursor-pointer">
                            </div>
                        </th>
                        
                        <th class="py-3 px-4 text-left text-sm font-medium text-gray-600 dark:text-slate-200 uppercase tracking-tight border-r border-gray-200 dark:border-slate-700 hidden sm:table-cell">
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
                        <th class="py-3 px-4 text-center text-sm font-medium text-gray-600 dark:text-slate-200 uppercase tracking-tight border-l border-gray-200 dark:border-slate-700 w-16">
                            <span>Aksi</span>
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-slate-800 divide-y divide-gray-200 dark:divide-slate-700">
                    @forelse($suratjalan as $index => $pos)
                    <tr class="hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors">
                        <!-- Made all table cells much more compact with minimal padding -->
                        <td class="py-3 px-4 text-center align-middle border-r border-b border-gray-200 dark:border-slate-700 w-20">
                            <div class="flex items-center justify-center">
                                <input type="checkbox" name="selected_ids[]" value="{{ $pos->id }}" class="row-radio border-gray-300 dark:border-slate-600 text-indigo-600 dark:text-indigo-500 focus:ring-indigo-500 dark:focus:ring-indigo-400 w-4 h-4 bg-white dark:bg-slate-800">
                            </div>
                        </td>
                        
                        <td class="py-3 px-4 whitespace-normal text-sm text-gray-900 dark:text-slate-200 border-r border-b border-gray-200 dark:border-slate-700 hidden sm:table-cell">
                            <span class="inline-flex items-center px-1 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800 dark:bg-slate-600 dark:text-slate-200">
                                {{ $pos->no_po }}
                            </span>
                        </td>
                        <td class="py-3 px-4 whitespace-normal text-sm text-gray-900 dark:text-slate-200 border-r border-b border-gray-200 dark:border-slate-700">
                            <span class="font-medium block">
                                {{ $pos->customer }}
                            </span>
                        </td>
                        <td class="py-3 px-4 whitespace-normal text-sm text-gray-900 dark:text-slate-200 border-r border-b border-gray-200 dark:border-slate-700">
                            <span class="inline-flex items-center px-1 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800 dark:bg-slate-600 dark:text-slate-200">
                                {{ $pos->no_surat_jalan }}
                            </span>
                        </td>
                        <td class="py-3 px-4 text-sm font-medium text-center border-b border-gray-200 dark:border-slate-700">
                            <x-table.action-buttons 
                                onEdit="window.handleEditClick(this)"
                                :editPayload="[
                                    'id' => $pos->id,
                                    'tanggal' => ($pos->tanggal_po ? \Carbon\Carbon::parse($pos->tanggal_po)->format('Y-m-d') : now()->format('Y-m-d')),
                                    'customer' => $pos->customer,
                                    'alamat1' => $pos->alamat_1,
                                    'alamat2' => $pos->alamat_2,
                                    'noSuratJalan' => $pos->no_surat_jalan,
                                    'noPo' => $pos->no_po,
                                    'kendaraan' => $pos->kendaraan,
                                    'noPolisi' => $pos->no_polisi,
                                    'qty' => $pos->qty,
                                    'jenis' => $pos->qty_jenis,
                                    'produkId' => $pos->produk_id,
                                    'total' => $pos->total,
                                    'pengirim' => $pos->pengirim
                                ]"
                                deleteAction="{{ route('suratjalan.destroy', $pos->id) }}"
                                confirmText="Apakah Anda yakin ingin menghapus Data PO ini?"
                                :useMenu="true"
                            />
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center">
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
                <div class="flex items-center gap-2">
                    <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
            
            <form id="editForm" method="POST" action="">
                @csrf
                @method('PUT')
                
                <!-- Hidden inputs untuk preserve filter saat redirect -->
                <input type="hidden" name="month" value="{{ request('month') ?? $month ?? '' }}">
                <input type="hidden" name="year" value="{{ request('year') ?? $year ?? '' }}">
                <input type="hidden" name="invoice_number" value="{{ request('invoice_number') ?? $poNumber ?? '' }}">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal PO</label>
                        <p class="text-xs text-gray-500 mb-1">Diisi otomatis dari waktu input PO, boleh diubah.</p>
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
                        <input type="text" 
                               id="edit_no_po" 
                               name="no_po"
                               placeholder="PO.123456789" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500" 
                               required>
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
                    <button type="submit" id="editSubmitBtn"
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
  /* Atur ukuran halaman A4 portrait dengan margin minimal */
  @page { 
    size: A4 portrait; 
    margin: 10mm 10mm 10mm 10mm; 
  }
  
  /* Reset body untuk print */
  html, body {
    width: 210mm !important;
    height: 297mm !important;
    margin: 0 !important;
    padding: 0 !important;
  }
  
  /* Sembunyikan semua elemen halaman kecuali modal invoice */
  body > *:not(#invoiceModal) { display: none !important; }
  
  /* Tampilkan modal invoice dan hilangkan overlay */
  #invoiceModal {
    display: block !important;
    position: static !important;
    background: white !important;
    overflow: visible !important;
    width: 100% !important;
    height: 100% !important;
    margin: 0 !important;
    padding: 0 !important;
  }
  
  /* Hilangkan wrapper modal dan tampilkan langsung konten */
  #invoiceModal > div {
    position: static !important;
    width: 100% !important;
    max-width: 100% !important;
    margin: 0 !important;
    padding: 0 !important;
    border: none !important;
    box-shadow: none !important;
    background: white !important;
  }
  
  #invoiceModal > div > div {
    margin: 0 !important;
    padding: 0 !important;
  }
  
  /* Sembunyikan tombol close dan print di modal */
  #invoiceModal button,
  #invoiceModal .flex.items-center.justify-between { display: none !important; }

  /* Frame A4 memenuhi halaman */
  .a4-page {
    position: static !important;
    width: 100% !important;
    max-width: 210mm !important;
    min-height: 297mm !important;
    margin: 0 auto !important;
    padding: 10mm 15mm !important;
    box-shadow: none !important;
    background: white !important;
    overflow: visible !important;
  }

  /* Konten invoice memenuhi lebar kertas */
  #invoiceContent {
    width: 100% !important;
    max-width: 180mm !important;
    margin: 0 auto !important;
    padding: 0 !important;
    background: white !important;
    -webkit-print-color-adjust: exact !important;
    print-color-adjust: exact !important;
    font-size: 11px !important;
  }
  
  /* Perbesar font untuk tabel */
  #invoiceContent table {
    font-size: 10px !important;
  }
  
  #invoiceContent thead th {
    font-size: 11px !important;
    padding: 6px 4px !important;
  }
  
  #invoiceContent tbody td {
    font-size: 10px !important;
    padding: 5px 4px !important;
  }
  
  #invoiceContent tfoot td {
    font-size: 10px !important;
    padding: 5px 4px !important;
  }

  /* Hindari kepotong di tengah halaman untuk baris dan sel tabel */
  table { 
    page-break-inside: auto !important;
    width: 100% !important;
  }
  thead { display: table-header-group; }
  tfoot { display: table-footer-group; }
  tr, td, th { page-break-inside: avoid !important; }
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
                            <!-- Spacer tetap untuk area tanda tangan agar simetris (diperbesar) -->
                            <div style="height: 80px;"></div>
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
    const currentMonth = {{ (int)($month ?? now()->format('n')) }};
    
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
    const selectAllCheckbox = document.getElementById('selectAll');
    const exportBtn = document.getElementById('exportBtn');
    const exportControls = document.getElementById('exportControls');
    const selectedCount = document.getElementById('selectedCount');
    const exportForm = document.getElementById('exportForm');
    const selectedIdsInput = document.getElementById('selectedIds');

    // Handle Select All checkbox
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            const isChecked = this.checked;
            rowRadios.forEach(radio => {
                radio.checked = isChecked;
            });
            updateExportControls();
            console.log(isChecked ? '✅ Select All checked' : '❌ Select All unchecked');
        });
    }

    // Handle individual row selection (checkbox)
    rowRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            updateExportControls();
            updateSelectAllState();
        });
    });

    // Update Select All checkbox state based on individual checkboxes
    function updateSelectAllState() {
        if (!selectAllCheckbox) return;
        
        const totalCheckboxes = rowRadios.length;
        const checkedCheckboxes = document.querySelectorAll('.row-radio:checked').length;
        
        if (checkedCheckboxes === 0) {
            selectAllCheckbox.checked = false;
            selectAllCheckbox.indeterminate = false;
        } else if (checkedCheckboxes === totalCheckboxes) {
            selectAllCheckbox.checked = true;
            selectAllCheckbox.indeterminate = false;
        } else {
            selectAllCheckbox.checked = false;
            selectAllCheckbox.indeterminate = true; // Show dash/minus for partial selection
        }
    }

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
    
    // Initial check
    updateSelectAllState();
});

function exportSelected(type = 'surat_jalan') {
    const selectedChecks = Array.from(document.querySelectorAll('.row-radio:checked'));
    // Wajib pilih minimal satu data
    if (selectedChecks.length === 0) {
        alert(type === 'tanda_terima' ? 'Pilih minimal 1 data untuk export Tanda Terima' : 'Pilih minimal 1 data untuk export Surat Jalan');
        return;
    }
    const selectedIds = selectedChecks.map(el => el.value);
    const exportForm = document.getElementById('exportForm');
    const selectedIdsInput = document.getElementById('selectedIds');
    const exportTypeInput = document.getElementById('exportType');

    // Tentukan tombol yang digunakan untuk loading state
    const btn = type === 'tanda_terima' ? document.getElementById('exportBtnTT') : document.getElementById('exportBtn');

    // Show loading state pada tombol terkait (tanpa mengubah ikon)
    if (btn) {
        btn.classList.add('opacity-60', 'cursor-not-allowed');
        btn.disabled = true;
    }

    // Set tipe export & selected IDs
    if (exportTypeInput) exportTypeInput.value = type;
    selectedIdsInput.value = JSON.stringify(selectedIds);
    exportForm.submit();

    // Reset tombol setelah beberapa detik (fallback bila browser tidak auto reset)
    setTimeout(() => {
        if (!btn) return;
        btn.classList.remove('opacity-60', 'cursor-not-allowed');
        btn.disabled = false;
    }, 3000);
}

// Hapus fungsi deleteSuratJalan(): aksi hapus kini ditangani oleh komponen x-table.action-buttons

// Updated editSuratJalan function to handle pengirim parameter
function editSuratJalan(id, tanggal, customer, alamat1, alamat2, noSuratJalan, noPo, kendaraan, noPolisi, qty, jenis, produkId, total, pengirim) {
    try { console.debug('editSuratJalan()', {id, tanggal}); } catch (_) {}
    // Set form action dan method dengan force - GUNAKAN PREFIX YANG BENAR: data-po
    const formEl = document.getElementById('editForm');
    const actionUrl = "{{ url('/data-po') }}/" + id;
    formEl.setAttribute('action', actionUrl);
    formEl.setAttribute('method', 'POST');
    
    // Fill form fields
    // Normalisasi tanggal ke format YYYY-MM-DD agar cocok dengan input type=date
    try {
        const normalized = (tanggal || '').toString().substring(0, 10);
        document.getElementById('edit_tanggal_po').value = normalized;
    } catch (_) {
        document.getElementById('edit_tanggal_po').value = '';
    }
    document.getElementById('edit_customer').value = customer;
    document.getElementById('edit_alamat_1').value = alamat1 || '';
    document.getElementById('edit_alamat_2').value = alamat2 || '';
    document.getElementById('edit_no_surat_jalan').value = noSuratJalan;
    
    // Set No PO langsung tanpa split
    document.getElementById('edit_no_po').value = noPo || '';
    
    document.getElementById('edit_kendaraan').value = kendaraan;
    document.getElementById('edit_no_polisi').value = noPolisi;
    document.getElementById('edit_qty').value = qty;
    document.getElementById('edit_qty_jenis').value = jenis;
    document.getElementById('edit_pengirim').value = pengirim || '';
    setTimeout(() => {
        document.getElementById('edit_produk_id').value = produkId;
    }, 100);
    document.getElementById('edit_total').value = total;
    
    // Show modal (robust)
    const modal = document.getElementById('editModal');
    if (modal) {
        modal.classList.remove('hidden');
    } else {
        console.error('Edit modal not found');
        alert('Gagal membuka form edit. Muat ulang halaman dan coba lagi.');
    }
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

// Safe handler for action-buttons component to avoid inline JS syntax issues
window.handleEditClick = function(el) {
    try {
        // prefer data-edit payload if available
        const raw = el?.dataset?.edit;
        if (raw) {
            const p = JSON.parse(raw);
            return editSuratJalan(
                p.id, p.tanggal, p.customer, p.alamat1, p.alamat2,
                p.noSuratJalan, p.noPo, p.kendaraan, p.noPolisi,
                p.qty, p.jenis, p.produkId, p.total, p.pengirim
            );
        }
    } catch (err) {
        console.error('handleEditClick payload error:', err);
    }
    // fallback: if no payload (should not happen), do nothing
};

// Intercept form submit to ensure correct method and action
document.addEventListener('DOMContentLoaded', function() {
    const editForm = document.getElementById('editForm');
    if (editForm) {
        editForm.addEventListener('submit', function(e) {
            console.log('Form submit intercepted:', {
                method: this.method,
                action: this.action
            });
            
            // Pastikan form method adalah POST dan action tidak kosong
            if (this.method.toUpperCase() !== 'POST' || !this.action || this.action.endsWith('/data-po/')) {
                e.preventDefault();
                alert('Form belum siap. Pastikan Anda klik Edit terlebih dahulu.');
                return false;
            }
        });
    }
});

// Generate Invoice dari data yang dipilih
function generateInvoice() {
    const selectedChecks = Array.from(document.querySelectorAll('.row-radio:checked'));
    if (selectedChecks.length === 0) {
        alert('Pilih minimal 1 data untuk Invoice');
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
    })
    .catch(() => alert('Terjadi kesalahan saat mengambil data invoice'));
}

// Show notification (Global function)
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 px-4 py-2 rounded-lg text-white z-50 transition-opacity duration-300 ${
        type === 'success' ? 'bg-green-600' : 
        type === 'error' ? 'bg-red-600' : 'bg-blue-600'
    }`;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.opacity = '0';
        setTimeout(() => {
            document.body.removeChild(notification);
        }, 300);
    }, 3000);
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
    // Set "No :" sesuai template -> no_invoice/kode customer/bulan/tahun
    const invMonth = String(dbDate.getMonth() + 1).padStart(2, '0');
    const invYear = String(dbDate.getFullYear());
    const customerName = (first.customer || '').trim();
    const customerCode = (window.customerCodes && window.customerCodes[customerName]) ? window.customerCodes[customerName] : '';
    // Gunakan invoice_number dari filter jika ada, fallback ke field pada baris pertama
    const invoiceNumber = (window.currentInvoiceNumber ?? first.no_invoice ?? '') + '';
    const parts = [];
    if (invoiceNumber) parts.push(invoiceNumber);
    if (customerCode) parts.push(customerCode);
    parts.push(invMonth, invYear);
    document.getElementById('invoiceNo').textContent = parts.join(' / ');
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
    // Langsung trigger print dialog browser (Ctrl+P) tanpa membuka window baru
    window.print();
}

// Generate invoice dan langsung print tanpa menampilkan modal
function generateAndPrintInvoice() {
    const selectedChecks = Array.from(document.querySelectorAll('.row-radio:checked'));
    if (selectedChecks.length === 0) {
        alert('Pilih minimal 1 data untuk Print Invoice');
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
            // Populate invoice data tanpa menampilkan modal
            populateInvoice(res.data);
            // Tunggu sebentar agar data ter-render, lalu print
            setTimeout(() => {
                window.print();
            }, 300);
        } else {
            alert('Data tidak ditemukan');
        }
    })
    .catch(err => {
        console.error('Error:', err);
        alert('Terjadi kesalahan saat memuat data invoice');
    });
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

// Expose functions to global scope for inline onclick buttons
window.exportSelected = typeof exportSelected === 'function' ? exportSelected : window.exportSelected;
window.generateInvoice = typeof generateInvoice === 'function' ? generateInvoice : window.generateInvoice;
window.generateAndPrintInvoice = typeof generateAndPrintInvoice === 'function' ? generateAndPrintInvoice : window.generateAndPrintInvoice;
window.downloadInvoicePDF = typeof downloadInvoicePDF === 'function' ? downloadInvoicePDF : window.downloadInvoicePDF;
window.printInvoice = typeof printInvoice === 'function' ? printInvoice : window.printInvoice;

// Defensive: also bind click handlers so buttons work even if inline events are blocked
document.addEventListener('DOMContentLoaded', () => {
  try {
    // invoiceBtn sekarang menggunakan dropdown Alpine.js, tidak perlu event listener
    const pdfBtn = document.getElementById('pdfBtn');
    if (pdfBtn) {
      pdfBtn.addEventListener('click', (e) => { e.preventDefault(); if (typeof window.downloadInvoicePDF === 'function') window.downloadInvoicePDF(); });
    }
    const excelBtn = document.getElementById('exportBtn');
    if (excelBtn) {
      excelBtn.addEventListener('click', (e) => { e.preventDefault(); if (typeof window.exportSelected === 'function') window.exportSelected('surat_jalan'); });
    }
    const ttBtn = document.getElementById('exportBtnTT');
    if (ttBtn) {
      ttBtn.addEventListener('click', (e) => { e.preventDefault(); if (typeof window.exportSelected === 'function') window.exportSelected('tanda_terima'); });
    }
  } catch (_) {}
  
  // No PO sudah menggunakan 1 input saja, tidak perlu event listener untuk menggabungkan
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