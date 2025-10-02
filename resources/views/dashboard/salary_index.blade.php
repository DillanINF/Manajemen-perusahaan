@extends('layouts.app')

@section('title', 'Manajemen Gaji Karyawan')

@push('styles')
<style>
/* VERSION: {{ now()->timestamp }} - FORCE RELOAD */

/* Force height untuk semua sel tabel - SUPER KUAT */
#manualSalaryTable table tbody tr,
#manualSalaryTable table tbody tr td,
#manualSalaryTable table tbody tr th {
  height: 12px !important;
  max-height: 12px !important;
  min-height: 12px !important;
  line-height: 12px !important;
}

#manualSalaryTable table tbody td {
  height: 12px !important;
  max-height: 12px !important;
  min-height: 12px !important;
  padding: 0 2px !important;
  vertical-align: middle !important;
  font-size: 12px !important;
}

#manualSalaryTable table tbody td input,
#manualSalaryTable table tbody td span {
  height: 12px !important;
  max-height: 12px !important;
  min-height: 12px !important;
  line-height: 12px !important;
  padding: 0px 2px !important;
  font-size: 12px !important;
  box-sizing: border-box !important;
}

#manualSalaryTable table thead tr {
  height: 12px !important;
  max-height: 12px !important;
  min-height: 12px !important;
}

#manualSalaryTable table thead th {
  height: 12px !important;
  max-height: 12px !important;
  min-height: 12px !important;
  line-height: 12px !important;
  padding: 0px 2px !important;
  font-size: 12px !important;
}

#manualSalaryTable table tfoot tr {
  height: 12px !important;
  max-height: 12px !important;
  min-height: 12px !important;
}

#manualSalaryTable table tfoot td {
  height: 12px !important;
  max-height: 12px !important;
  min-height: 12px !important;
  line-height: 1 !important;
  padding: 0px 2px !important;
  font-size: 11px !important;
  vertical-align: middle !important;
}

.report-header .side-box {
  height: 28px;
  display: flex;
  flex-direction: column;
  justify-content: center;
}
.report-header .title-main { letter-spacing: 0.5px; }
.report-header .title-sub { letter-spacing: 0.2px; }

.report-header {
  margin-bottom: 0 !important;
  border-bottom: 0 !important; /* move line to table top to avoid line above table */
  width: 100% !important;
}
.table-wrapper {
  margin-top: 0 !important;
  padding-top: 0 !important;
}
.table-wrapper > table {
  border-collapse: collapse !important;
  border-top: 2px solid #000 !important;   /* single line at top of table */
  border-bottom: 2px solid #000 !important;/* meets footer top   */
  width: 100% !important;
}
.report-footer {
  margin-top: auto !important; /* dorong footer ke bawah wrapper */
  border-top: 0 !important; /* rely on table bottom border */
  width: 100% !important;
  min-width: 100% !important;
  padding-bottom: 0 !important; /* pastikan tidak ada jarak bawah */
}

/* Force header and footer to use full available width */
.table-wrapper {
  display: flex !important;
  flex-direction: column !important;
  width: 100% !important;
}

.report-header,
.report-footer {
  display: block !important;
  width: 100% !important;
  box-sizing: border-box !important;
}

.report-header > div,
.report-footer > div {
  width: 100% !important;
  display: flex !important;
}


/* Ensure print keeps the block seamless */
@media print {
  /* Set page to A4 landscape */
  @page {
    size: A4 landscape;
    margin: 0;
  }
  
  /* Hide ALL elements by default */
  body * {
    visibility: hidden !important;
  }
  
  /* Show ONLY the printable area and its children */
  #printableArea,
  #printableArea * {
    visibility: visible !important;
  }
  
  /* Position printable area at top left */
  #printableArea {
    position: absolute !important;
    left: 0 !important;
    top: 0 !important;
  }
  
  /* Hide non-printable elements */
  .no-print {
    display: none !important;
    visibility: hidden !important;
  }
  
  /* Force body and html to use full page */
  html, body {
    width: 297mm !important;
    height: 210mm !important;
    margin: 0 !important;
    padding: 0 !important;
    overflow: hidden !important;
  }
  
  /* Main container untuk tabel */
  #manualSalaryTable {
    width: 297mm !important;
    max-width: 297mm !important;
    height: 210mm !important;
    max-height: 210mm !important;
    margin: 0 !important;
    padding: 0 !important;
    overflow: hidden !important;
    page-break-after: avoid !important;
    page-break-inside: avoid !important;
  }
  
  /* Wrapper tabel - fit to A4 landscape */
  .table-wrapper {
    display: flex !important;
    flex-direction: column !important;
    width: 297mm !important;
    max-width: 297mm !important;
    height: 210mm !important;
    max-height: 210mm !important;
    margin: 0 !important;
    padding: 0 !important;
    box-sizing: border-box !important;
    overflow: hidden !important;
    page-break-inside: avoid !important;
  }
  
  /* Header */
  .report-header { 
    border-bottom: 2px solid #000 !important; 
    width: 297mm !important;
    max-width: 297mm !important;
    margin: 0 !important;
    padding: 8px !important;
    box-sizing: border-box !important;
  }
  
  /* Table */
  .table-wrapper > table { 
    border-top: 2px solid #000 !important; 
    border-bottom: 2px solid #000 !important; 
    width: 297mm !important;
    max-width: 297mm !important;
    margin: 0 !important;
    table-layout: fixed !important;
    border-collapse: collapse !important;
  }
  
  /* Footer */
  .report-footer { 
    border-top: 0 !important; 
    width: 297mm !important;
    max-width: 297mm !important;
    margin: 0 !important;
    /* top right bottom left (bottom set to 0) */
    padding: 8px 8px 0 8px !important;
    box-sizing: border-box !important;
  }
  
  /* Ensure all cells print correctly */
  table.report-table td,
  table.report-table th {
    page-break-inside: avoid !important;
    -webkit-print-color-adjust: exact !important;
    print-color-adjust: exact !important;
  }
  
  /* Print background colors */
  * {
    -webkit-print-color-adjust: exact !important;
    print-color-adjust: exact !important;
  }
  
  /* Hide outer container styling */
  .space-y-6 {
    margin: 0 !important;
    padding: 0 !important;
  }
  
  /* Ensure only table wrapper is visible */
  .table-wrapper {
    border: none !important;
    box-shadow: none !important;
  }
  
  /* Scale content to fit A4 landscape if needed */
  #manualSalaryTable .table-wrapper {
    transform-origin: top left;
    /* Uncomment below if content is too large and needs scaling */
    /* transform: scale(0.95); */
  }
  
  /* Remove border and shadow from printable area when printing */
  #printableArea {
    border: none !important;
    box-shadow: none !important;
    background: white !important;
  }
  
  /* Ensure proper spacing and layout */
  #manualSalaryTable {
    background: white !important;
  }
}

/* Hide scrollbar for date columns */
.overflow-x-auto::-webkit-scrollbar {
    display: none;
}

.overflow-x-auto {
    -ms-overflow-style: none;
    scrollbar-width: none;
}

/* Scroll horizontal khusus untuk bagian tanggal */
.date-scroll-container {
    overflow-x: auto;
    overflow-y: hidden;
    scrollbar-width: none;
    -ms-overflow-style: none;
}

.date-scroll-container::-webkit-scrollbar {
    display: none;
}

.date-columns {
    display: flex;
    min-width: max-content;
}

.date-column {
    flex: 0 0 auto;
    width: 35px;
    min-width: 35px;
}

 /* Header Salary: gaya label biaya/upah kerja dan tanggal */
 .salary-header-right {
   width: 280px;
   align-self: flex-start;
   margin-left: auto; /* mentokin ke kanan */
 }
 .salary-header-label {
  border: 1px solid #000; /* kembalikan outline seperti semula */
  padding: 0 6px;
  font-weight: 700;
  font-size: 8px;
  color: #000;
  text-align: left; /* teks ke kiri */
  background: #e5e7eb; /* buat sisi kiri sama gray seperti kanan */
  position: relative; /* untuk garis pemisah */
 }
 .salary-header-label::before { /* kolom kanan abu-abu */
  content: '';
  position: absolute;
  top: 0;
  right: 1px;
  left: calc(50% + 17px); /* mulai tepat di kanan separator baru */
  height: 100%;
  background-color:#e5e7eb; /* gray solid, tanpa tekstur */
 }
 .salary-header-label::after { /* garis pemisah vertikal sisi kanan */
  content: '';
  position: absolute;
  top: -1px;             /* overlap 1px agar menyambung dengan outline */
  left: calc(50% + 16px); /* geser sedikit lebih ke kanan */
  transform: none;
  width: 1px;            /* lebih tipis, tidak bold */
  height: calc(100% + 2px); /* overlap bawah juga */
  background: #000;
 }
 .salary-header-date {
   border: 1px solid #000; /* kembalikan outline seperti semula */
   margin-top: 2px;
   padding: 1px 6px;
   text-align: center;
   font-weight: 700;
   font-size: 10px;
   color: #000;
 }

 /* Header kecil & profesional */
 .company-title {
   font-size: 28px; /* lebih kecil dari 4xl */
   font-weight: 800;
   letter-spacing: 0.02em;
 }
 .rules-box {
   border: 1px solid #000;
   padding: 4px 6px;
   line-height: 1.15;
   background: #fff;
 }
 .rules-box .title { 
   color: #dc2626; /* red-600 */
   font-weight: 800;
   font-size: 10px;
   letter-spacing: 0.02em;
 }
 .rules-box .item { 
   color: #b91c1c; /* red-700 */
   font-weight: 800;
   font-size: 10px;
 }
</style>
@endpush

@push('scripts')
<script>
// Fungsi perhitungan untuk tabel baru (kolom tanggal: 5-20)
function calculateRow(rowIndex) {
    let total = 0;
    
    // Hitung total dari kolom tanggal 5-20
    for (let day = 5; day <= 20; day++) {
        const input = document.getElementById(`day_${rowIndex}_${day}`);
        if (input && input.value) {
            total += parseFloat(input.value) || 0;
        }
    }
    
    // Update total pieces
    const totalElement = document.getElementById(`total_${rowIndex}`);
    if (totalElement) {
        totalElement.textContent = total;
    }
    
    // Hitung total baris (total * harga)
    const priceInput = document.getElementById(`price_${rowIndex}`);
    const price = priceInput ? (parseInt(priceInput.value.replace(/\D/g, '')) || 0) : 0;
    const rowTotal = total * price;
    
    const rowTotalElement = document.getElementById(`row_total_${rowIndex}`);
    if (rowTotalElement) {
        rowTotalElement.textContent = rowTotal.toLocaleString('id-ID');
    }
    
    // Update total kolom per hari
    updateDayTotals();
    
    // Update grand total keseluruhan
    updateGrandTotal();
}

function updateDayTotals() {
    // Total untuk tanggal 5-20
    for (let day = 5; day <= 20; day++) {
        let dayTotal = 0;
        for (let row = 1; row <= 30; row++) {
            const input = document.getElementById(`day_${row}_${day}`);
            if (input && input.value) {
                dayTotal += parseFloat(input.value) || 0;
            }
        }
        const dayTotalElement = document.getElementById(`day_total_${day}`);
        if (dayTotalElement) {
            dayTotalElement.textContent = dayTotal;
        }
    }
}

function updateGrandTotal() {
    let grandTotal = 0;
    let totalPieces = 0;
    
    // Hitung total dari semua baris
    for (let row = 1; row <= 30; row++) {
        const totalElement = document.getElementById(`total_${row}`);
        if (totalElement) {
            const pieces = parseInt(totalElement.textContent) || 0;
            totalPieces += pieces;
            
            const rowTotalElement = document.getElementById(`row_total_${row}`);
            if (rowTotalElement) {
                const rowTotal = parseInt(rowTotalElement.textContent.replace(/\D/g, '')) || 0;
                grandTotal += rowTotal;
            }
        }
    }
    
    // Update displays
    const grandPiecesElement = document.getElementById('grand_pieces');
    if (grandPiecesElement) {
        grandPiecesElement.textContent = totalPieces + ' pcs';
    }
    
    const finalTotalElement = document.getElementById('final_total');
    if (finalTotalElement) {
        finalTotalElement.textContent = grandTotal.toLocaleString('id-ID');
    }
    
    const totalPalletDisplay = document.getElementById('total_pallet_display');
    if (totalPalletDisplay) {
        totalPalletDisplay.textContent = totalPieces;
    }
    
    const totalGajiDisplay = document.getElementById('total_gaji_display');
    if (totalGajiDisplay) {
        totalGajiDisplay.textContent = grandTotal.toLocaleString('id-ID');
    }
    
    // Hitung grand total diterima (total gaji - total bon)
    const totalBonDisplay = document.getElementById('total_bon_display');
    const totalBon = totalBonDisplay ? parseInt(totalBonDisplay.textContent.replace(/\D/g, '')) || 0 : 0;
    const grandTotalDiterima = grandTotal - totalBon;
    
    const grandTotalDisplay = document.getElementById('grand_total_display');
    if (grandTotalDisplay) {
        grandTotalDisplay.textContent = grandTotalDiterima.toLocaleString('id-ID');
    }
}

</script>
@endpush

@section('content')
<div class="space-y-6 overflow-hidden">
    <!-- Header dengan Statistik -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6 no-print">
        <div class="bg-green-50 border border-green-200 rounded-lg p-4 dark:bg-white/5 dark:border-white/10">
            <div class="flex items-center">
                <div class="bg-green-500 text-white p-2 rounded-full mr-3 dark:bg-green-500/25 dark:text-green-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-300">Total Gaji Dibayar</p>
                    <p class="text-xl font-bold text-green-600 dark:text-green-300">Rp <span id="statTotalDibayar">{{ number_format($totalGajiDibayar ?? 0, 0, ',', '.') }}</span></p>
                </div>
            </div>
        </div>

        <div class="bg-red-50 border border-red-200 rounded-lg p-4 dark:bg-white/5 dark:border-white/10">
            <div class="flex items-center">
                <div class="bg-red-500 text-white p-2 rounded-full mr-3 dark:bg-red-500/25 dark:text-red-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-300">Gaji Belum Dibayar</p>
                    <p class="text-xl font-bold text-red-600 dark:text-red-300">Rp <span id="statBelumDibayar">{{ number_format($totalGajiBelumDibayar ?? 0, 0, ',', '.') }}</span></p>
                </div>
            </div>
        </div>

        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 dark:bg-white/5 dark:border-white/10">
            <div class="flex items-center">
                <div class="bg-blue-500 text-white p-2 rounded-full mr-3 dark:bg-blue-500/25 dark:text-blue-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-300">Karyawan Dibayar</p>
                    <p class="text-xl font-bold text-blue-600 dark:text-blue-300"><span id="statKaryawanDibayar">{{ $jumlahKaryawanDibayar ?? 0 }}</span></p>
                </div>
            </div>
        </div>

        <div class="bg-purple-50 border border-purple-200 rounded-lg p-4 dark:bg-white/5 dark:border-white/10">
            <div class="flex items-center">
                <div class="bg-purple-500 text-white p-2 rounded-full mr-3 dark:bg-purple-500/25 dark:text-purple-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-300">Rata-rata Gaji</p>
                    <p class="text-xl font-bold text-purple-600 dark:text-purple-300">Rp <span id="statRataRata">{{ number_format($rataRataGaji ?? 0, 0, ',', '.') }}</span></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Card Container Utama -->
    <div class="bg-white dark:bg-slate-900 shadow-lg rounded-lg overflow-hidden w-full max-w-full print:shadow-none print:rounded-none print:bg-transparent">
        <!-- Header dan Tombol Aksi -->
        <div class="p-6 border-b border-gray-200 dark:border-slate-700 no-print mb-6 print:mb-0">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-3 sm:space-y-0">
                <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Data Gaji Karyawan</h1>
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-3">
                        <!-- Filter Bulan -->
                        <select id="filterBulan" class="appearance-none no-arrow px-3 py-2 border border-gray-300 rounded-md dark:bg-slate-800/60 dark:border-white/10 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-500/40" onchange="filterKaryawanTable()">
                            <option value="">Semua Bulan</option>
                            @for($i = 1; $i <= 12; $i++)
                                <option value="{{ $i }}">{{ DateTime::createFromFormat('!m', $i)->format('F') }}</option>
                            @endfor
                        </select>
                        <!-- Filter Tahun (hidden untuk filtering, tapi tetap ada) -->
                        <select id="filterTahun" class="hidden" onchange="filterKaryawanTable()">
                            <option value="">Semua Tahun</option>
                            @for($i = 2020; $i <= 2035; $i++)
                                <option value="{{ $i }}" {{ $i == now()->format('Y') ? 'selected' : '' }}>{{ $i }}</option>
                            @endfor
                        </select>
                        <!-- Link Pilih Tahun -->
                        <button type="button" onclick="openYearModal()" 
                                class="inline-flex items-center px-3 py-2 text-sm font-medium text-indigo-600 bg-indigo-50 border border-indigo-200 rounded-md hover:bg-indigo-100 hover:text-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:bg-indigo-900/30 dark:text-indigo-300 dark:border-indigo-700 dark:hover:bg-indigo-900/50">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <span id="yearButtonText">Pilih Tahun ({{ now()->format('Y') }})</span>
                        </button>
                        
                        <!-- Tombol Print -->
                        <button type="button" onclick="window.print()"
                                class="inline-flex items-center px-3 py-2 text-sm font-semibold text-white bg-blue-600 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 no-print">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                            </svg>
                            Print Laporan
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabel Manual Salary (disembunyikan di landing page) -->
        <div id="manualSalaryTable" class="overflow-hidden mt-12 pt-0 bg-white dark:bg-slate-800 print:mt-0 print:pt-0 print:bg-white">
            <!-- Wrapper A4 Landscape untuk Header + Tabel + Footer -->
            <div class="overflow-hidden table-wrapper" style="width: 297mm; max-width: 297mm; height: 210mm; margin: 0; border: 2px solid #000; box-shadow: 0 4px 6px rgba(0,0,0,0.1); background: white;" id="printableArea">
                <!-- Header Perusahaan - Sesuai Foto 2 -->
                <div class="report-header p-3 border-b-2 border-black dark:border-white bg-white dark:bg-slate-800 print:bg-white mb-0" style="width: 297mm; max-width: 297mm;">
                    <div class="flex justify-between items-start">
                        <!-- Kiri: Peraturan Merah -->
                        <div class="text-left">
                            <div class="text-red-600 dark:text-red-400 font-bold text-[10px] leading-tight">PERATURAN</div>
                            <div class="text-red-600 dark:text-red-400 font-bold text-[9px] leading-tight">DILARANG BELAH PAPAN TANPA IJIN</div>
                            <div class="text-red-600 dark:text-red-400 font-bold text-[9px] leading-tight">DILARANG BELAH BALOK TANPA IJIN</div>
                        </div>
                        
                        <!-- Tengah: Nama Perusahaan -->
                        <div class="flex-1 text-center">
                            <h1 class="text-3xl font-black text-black dark:text-white tracking-wider">PT. CAM JAYA ABADI</h1>
                        </div>
                        
                        <!-- Kanan: Info Tanggal -->
                        <div class="text-right">
                            <div class="text-[10px] font-bold dark:text-white">BIAYA UPAH KERJA</div>
                            <div class="text-[10px] font-bold dark:text-white">{{ now()->format('d-M-y') }}</div>
                        </div>
                    </div>
                </div>

                <!-- Tabel Utama - Layout Vertikal Sesuai Foto 2 -->
                <style>
                    /* Paksa perkecil font di seluruh isi tabel (override inline) */
                    table.report-table, table.report-table * {
                        font-size: 9px !important;
                        line-height: 1 !important;
                    }
                </style>
                <table class="border-collapse report-table" style="table-layout: auto; margin-top: 0; width: 297mm; max-width: 297mm;">
                    <!-- Header Tabel - Layout Vertikal: ITEM di atas, KODE & DESKRIPSI di bawah -->
                    <thead>
                        <!-- Baris 1: ITEM -->
                        <tr class="bg-gray-200 dark:bg-slate-600" style="height: 12px !important;">
                            <th rowspan="2" class="border border-black dark:border-white text-xs font-black text-center dark:text-white" style="width: 80px; height: 12px !important; max-height: 12px !important; line-height: 1; font-size: 9px; font-weight: 700; padding: 0px 2px; vertical-align: middle; background-color: #D9D9D9; border-left: none;">ITEM</th>
                            <th rowspan="2" class="border border-black dark:border-white text-xs font-black text-center dark:text-white" style="width: 150px; height: 12px !important; max-height: 12px !important; line-height: 1; font-size: 9px; font-weight: 700; padding: 0px 2px; vertical-align: middle; background-color: #D9D9D9;"></th>
                            
                            <!-- Header TANGGAL (colspan untuk semua tanggal) -->
                            <th colspan="16" rowspan="2" class="border border-black dark:border-white text-xs font-black text-center dark:text-white" style="line-height: 1; font-size: 9px; font-weight: 700; padding: 0px 2px; vertical-align: middle; background-color: #D9D9D9;">TANGGAL</th>
                            
                            <!-- Header JUMLAH (text di atas) -->
                            <th rowspan="2" class="border border-black dark:border-white text-xs font-black text-center dark:text-white" style="width: 60px; line-height: 1; font-size: 9px; font-weight: 700; padding: 0px 2px; vertical-align: middle; background-color: #D9D9D9;">JUMLAH</th>
                            
                            <!-- Kolom kosong kiri -->
                            <th rowspan="2" class="border border-black dark:border-white text-xs font-black text-center dark:text-white" style="width: 40px; line-height: 1; font-size: 9px; font-weight: 700; padding: 0px 2px; vertical-align: middle; background-color: #D9D9D9;"></th>
                            
                            <!-- HSL. PROD (kosong di atas) -->
                            <th rowspan="2" class="border border-black dark:border-white text-xs font-black text-center dark:text-white" style="width: 70px; line-height: 1; font-size: 9px; font-weight: 700; padding: 0px 2px; vertical-align: middle; background-color: #D9D9D9;"></th>
                            
                            <!-- Kolom kosong kanan -->
                            <th rowspan="2" class="border border-black dark:border-white text-xs font-black text-center dark:text-white" style="width: 40px; line-height: 1; font-weight: 900; padding: 0px 2px; vertical-align: middle; background-color: #D9D9D9;"></th>
                            
                            <!-- HARGA (atas diisi BIAYA PRODUKSI) -->
                            <th rowspan="2" class="border border-black dark:border-white text-xs font-black text-center dark:text-white" style="width: 70px; line-height: 1; font-size: 9px; font-weight: 700; padding: 0px 2px; vertical-align: middle; background-color: #D9D9D9;">BIAYA PRODUKSI</th>
                            
                            <!-- TOTAL (kosong di atas) -->
                            <th rowspan="2" class="border border-black dark:border-white text-xs font-black text-center dark:text-white" style="width: 80px; line-height: 1; font-size: 9px; font-weight: 700; padding: 0px 2px; vertical-align: middle; background-color: #D9D9D9;"></th>
                            
                            <th rowspan="3" class="border border-black dark:border-white text-xs font-black text-center dark:text-white" style="width: 100px; line-height: 1; font-size: 9px; font-weight: 700; padding: 0px 2px; vertical-align: middle; background-color: #D9D9D9; border-right: none;">KETERANGAN</th>
                        </tr>
                        
                        <!-- Baris 2: KODE -->
                        <tr class="bg-gray-200 dark:bg-slate-600" style="height: 12px !important;">
                            <!-- KODE dan DESKRIPSI sudah ada di baris 1 dengan rowspan="2" -->
                            <!-- KODE dan DESKRIPSI sudah ada di baris 1 dengan rowspan="2" -->
                        </tr>
                        
                        <!-- Baris 3: KODE & Angka tanggal & JUMLAH & sub-header BIAYA PRODUKSI -->
                        <tr class="bg-gray-200 dark:bg-slate-600" style="height: 10px !important;">
                            <!-- KODE -->
                            <th class="border border-black dark:border-white text-xs font-black text-center dark:text-white" style="width: 80px; height: 10px !important; max-height: 10px !important; line-height: 1; font-size: 9px; font-weight: 700; padding: 0px 2px; vertical-align: middle; background-color: #FFFFFF; border-left: none;">KODE</th>
                            <!-- DESKRIPSI (dipindah ke baris 3) -->
                            <th class="border border-black dark:border-white text-xs font-black text-center dark:text-white" style="width: 150px; height: 10px !important; max-height: 10px !important; line-height: 1; font-size: 9px; font-weight: 700; padding: 0px 2px; vertical-align: middle; background-color: #FFFFFF;">DESKRIPSI</th>
                            
                            <!-- Sub-header: Angka tanggal 5-20 -->
                            @for($day = 5; $day <= 20; $day++)
                                <th class="border border-black dark:border-white text-xs font-black text-center dark:text-white" style="width: 32px; height: 10px !important; max-height: 10px !important; line-height: 1; font-size: 9px; font-weight: 700; padding: 0px; vertical-align: middle; background-color: #FFFFFF;">{{ $day }}</th>
                            @endfor
                            
                            <!-- JUMLAH (text di bawah) -->
                            <th class="border border-black dark:border-white text-xs font-black text-center dark:text-white" style="width: 60px; height: 10px !important; max-height: 10px !important; line-height: 1; font-size: 9px; font-weight: 700; padding: 0px 2px; vertical-align: middle; background-color: #FFFFFF;">JUMLAH</th>
                            
                            <!-- Sub-header BIAYA PRODUKSI -->
                            <th class="border border-black dark:border-white text-xs font-black text-center dark:text-white" style="width: 40px; height: 10px !important; max-height: 10px !important; line-height: 1; font-size: 9px; font-weight: 700; padding: 0px 2px; vertical-align: middle; background-color: #FFFFFF;"></th>
                            <th class="border border-black dark:border-white text-xs font-black text-center dark:text-white" style="width: 70px; height: 10px !important; max-height: 10px !important; line-height: 1; font-size: 9px; font-weight: 700; padding: 0px 2px; vertical-align: middle; background-color: #FFFFFF;">HSL. PROD</th>
                            <th class="border border-black dark:border-white text-xs font-black text-center dark:text-white" style="width: 40px; height: 10px !important; max-height: 10px !important; line-height: 1; font-weight: 900; padding: 0px 2px; vertical-align: middle; background-color: #FFFFFF;"></th>
                            <th class="border border-black dark:border-white text-xs font-black text-center dark:text-white" style="width: 70px; height: 10px !important; max-height: 10px !important; line-height: 1; font-size: 9px; font-weight: 700; padding: 0px 2px; vertical-align: middle; background-color: #FFFFFF;">HARGA</th>
                            <th class="border border-black dark:border-white text-xs font-black text-center dark:text-white" style="width: 80px; height: 10px !important; max-height: 10px !important; line-height: 1; font-size: 9px; font-weight: 700; padding: 0px 2px; vertical-align: middle; background-color: #FFFFFF;">TOTAL</th>
                            
                        </tr>
                    </thead>
                    
                    <!-- Body Tabel -->
                    <tbody>
                        <!-- Baris Data (30 baris kosong untuk input manual) -->
                        @for($row = 1; $row <= 30; $row++)
                        <tr style="height: 12px !important; max-height: 12px !important; background-color: {{ $row % 2 == 1 ? '#D9D9D9' : '#FFFFFF' }};">
                            <!-- KODE -->
                            <td class="border border-black dark:border-white" style="height: 12px !important; max-height: 12px !important; padding: 0; vertical-align: middle; background-color: #FFFFFF; border-left: none;">
                                <input type="text" class="w-full h-full border-0 bg-transparent dark:text-white focus:bg-yellow-100 dark:focus:bg-yellow-200 focus:outline-none" style="font-weight: 500; line-height: 1; padding: 0px 2px; height: 12px; max-height: 12px; font-size: 10px;" 
                                       id="kode_{{ $row }}">
                            </td>
                            
                            <!-- DESKRIPSI -->
                            <td class="border border-black dark:border-white" style="height: 12px !important; max-height: 12px !important; padding: 0; vertical-align: middle; background-color: #D9D9D9;">
                                <input type="text" class="w-full h-full border-0 bg-transparent dark:text-white focus:bg-yellow-100 dark:focus:bg-yellow-200 focus:outline-none" style="font-weight: 500; line-height: 1; padding: 0px 2px; height: 12px; max-height: 12px; font-size: 10px;" 
                                       id="desc_{{ $row }}">
                            </td>
                            
                            <!-- Kolom Tanggal (5-20) -->
                            @for($day = 5; $day <= 20; $day++)
                                <td class="border border-black dark:border-white" style="height: 12px !important; max-height: 12px !important; padding: 0; vertical-align: middle;">
                                    <input type="text" class="w-full h-full border-0 bg-transparent dark:text-white text-center focus:bg-yellow-100 dark:focus:bg-yellow-200 focus:outline-none" style="font-weight: 500; line-height: 1; padding: 0px; height: 12px; max-height: 12px; font-size: 10px;" 
                                           onchange="calculateRow({{ $row }})" id="day_{{ $row }}_{{ $day }}">
                                </td>
                            @endfor
                            
                            <!-- JUMLAH -->
                            <td class="border border-black dark:border-white text-center" style="height: 12px !important; max-height: 12px !important; padding: 0px 2px; vertical-align: middle; background-color: #FFFFFF;">
                                <span class="text-xs font-black dark:text-white" style="font-weight: 900; line-height: 1;" id="total_{{ $row }}">0</span>
                            </td>
                            
                            <!-- KOLOM KOSONG KIRI -->
                            <td class="border border-black dark:border-white" style="height: 12px !important; max-height: 12px !important; padding: 0; vertical-align: middle; background-color: #FFFFFF;">
                            </td>
                            
                            <!-- HSL. PROD -->
                            <td class="border border-black dark:border-white" style="height: 12px !important; max-height: 12px !important; padding: 0; vertical-align: middle; background-color: #D9E1F2;">
                                <input type="text" class="w-full h-full border-0 bg-transparent dark:text-white text-center focus:bg-yellow-100 dark:focus:bg-yellow-200 focus:outline-none" style="font-weight: 500; line-height: 1; padding: 0px; height: 12px; max-height: 12px; font-size: 10px;" 
                                       onchange="calculateRow({{ $row }})" id="prod_{{ $row }}">
                            </td>
                            
                            <!-- KOLOM KOSONG KANAN -->
                            <td class="border border-black dark:border-white" style="height: 12px !important; max-height: 12px !important; padding: 0; vertical-align: middle; background-color: #D9D9D9;">
                            </td>
                            
                            <!-- HARGA -->
                            <td class="border border-black dark:border-white" style="height: 12px !important; max-height: 12px !important; padding: 0; vertical-align: middle; background-color: #FFFFFF;">
                                <input type="text" class="w-full h-full border-0 bg-transparent dark:text-white text-center focus:bg-yellow-100 dark:focus:bg-yellow-200 focus:outline-none" style="font-weight: 500; line-height: 1; padding: 0px; height: 12px; max-height: 12px; font-size: 10px;" 
                                       onchange="calculateRow({{ $row }})" id="price_{{ $row }}">
                            </td>
                            
                            <!-- TOTAL -->
                            <td class="border border-black dark:border-white text-center" style="height: 12px !important; max-height: 12px !important; padding: 0px 2px; vertical-align: middle; background-color: #FFFFFF;">
                                <span class="text-xs font-black dark:text-white" style="font-weight: 900; line-height: 1;" id="row_total_{{ $row }}">0</span>
                            </td>
                            
                            <!-- KETERANGAN (satu kolom) -->
                            <td class="border border-black dark:border-white" style="height: 12px !important; max-height: 12px !important; padding: 0; vertical-align: middle; background-color: #FFFFFF; border-right: none;" colspan="2">
                                <input type="text" class="w-full h-full border-0 bg-transparent dark:text-white focus:bg-yellow-100 dark:focus:bg-yellow-200 focus:outline-none" style="font-weight: 500; line-height: 1; padding: 0px 2px; height: 12px; max-height: 12px; font-size: 10px;">
                            </td>
                        </tr>
                        @endfor
                    </tbody>
                    
                    <!-- Footer Tabel -->
                    <tfoot>
                        <tr style="background-color: #FFFFFF;">
                            <td colspan="2" class="text-xs font-semibold dark:text-white" style="font-weight: 600; background-color: #FFFFFF; border: none; border-left: none;">Hasil produksi pallet dan biaya borongan</td>
                            
                            <!-- Total per hari (5-20) -->
                            @for($day = 5; $day <= 20; $day++)
                                <td class="border border-black dark:border-white text-center text-xs font-bold dark:text-white" style="font-weight: 700; background-color: #D9D9D9;" id="day_total_{{ $day }}">0</td>
                            @endfor
                            
                            <td class="border border-black dark:border-white text-center text-xs font-bold dark:text-white" style="font-weight: 700; background-color: #FFFFFF;" id="grand_pieces">595 pcs</td>
                            <!-- Footer cells aligned to: spacer kiri, HSL.PROD, spacer kanan, HARGA, TOTAL -->
                            <td class="border border-black dark:border-white" style="background-color: #D9D9D9;"></td>
                            <td class="border border-black dark:border-white" style="background-color: #FFFFFF;" id="footer_hsl_prod"></td>
                            <td class="border border-black dark:border-white" style="background-color: #D9D9D9;"></td>
                            <td colspan="2" class="border border-black dark:border-white text-right text-xs font-bold dark:text-white" style="font-weight: 700; background-color: #FFFFFF; color: #0000FF;" id="final_total">5,292,000</td>
                            <td class="dark:border-white text-xs dark:text-white" style="background-color: #FFFFFF; border: none; border-right: none;"></td>
                        </tr>
                        <tr style="background-color: #FFFFFF;">
                            <td colspan="2" style="border: none; text-align: left; font-style: italic; font-weight: 600; padding-top: 2px; padding-left: 48px;">
                                
                            </td>
                            <td colspan="23" style="border: none;"></td>
                        </tr>
                    </tfoot>
                </table>

                <!-- Footer Sesuai Foto -->
                <div class="report-footer border-t-2 border-black dark:border-white bg-white dark:bg-slate-800 print:bg-white pt-3 px-3 pb-0 mt-0 justify-end" style="width: 297mm; max-width: 297mm; height: 180px; display: flex; flex-direction: column; padding-right: 0;">
                    <div class="flex items-stretch gap-0" style="width: 100%; flex: 1; height: 100%;">
                        <!-- Kiri: Signature Area -->
                        <div class="flex-shrink-0" style="width: 20%;">
                            <div class="flex gap-2 mb-2">
                                <!-- Report by -->
                                <div class="text-center flex-1">
                                    <div class="text-[8px] mb-1 dark:text-white">Report by</div>
                                    <div class="p-2" style="height: 80px;">
                                        <img src="{{ asset('image/Signature1.png') }}" alt="Signature 1" class="w-20 h-20 mx-auto object-contain"
                                             onerror="this.style.display='none';">
                                    </div>
                                    <div class="text-[7px] font-bold mt-1 dark:text-white">Reid Kubro Wahyudin</div>
                                    <div class="text-[6px] dark:text-gray-300">DIREKTUR</div>
                                </div>
                                
                                <!-- Approved by -->
                                <div class="text-center flex-1">
                                    <div class="text-[8px] mb-1 dark:text-white">Approved by</div>
                                    <div class="p-2" style="height: 80px;">
                                        <img src="{{ asset('image/Signature2.png') }}" alt="Signature 2" class="w-20 h-20 mx-auto object-contain"
                                             onerror="this.style.display='none';">
                                    </div>
                                    <div class="text-[7px] font-bold mt-1 dark:text-white">Panji Purnadi</div>
                                    <div class="text-[6px] dark:text-gray-300">DIREKTUR UTAMA</div>
                                </div>
                            </div>
                            
                            <!-- Warning Text Merah -->
                            <div class="text-center mt-2">
                                <div class="text-[7px] text-red-600 font-bold leading-tight">JIKA ADA TOLERANSI PALLET MAKA</div>
                                <div class="text-[7px] text-red-600 font-bold leading-tight">MAKA PERBAIKAN PALLET TOLERANSI STANDAR</div>
                                <div class="text-[7px] text-red-600 font-bold leading-tight">DAN JIKA REPAIR TIDAK MASUK HITUNGAN !!!</div>
                            </div>
                        </div>
                        
                        <!-- Tengah Kiri: 595 PALLET -->
                        <div class="flex-shrink-0" style="width: 25%; ">
                            <div class="border-2 border-black dark:border-white border-r-0 flex items-center justify-center gap-4" style="background-color: #D9D9D9; height: 80px; padding: 10px; margin-right: 0;">
                                <div class="font-bold dark:text-gray-900" id="total_pallet_display" style="font-size: 48px; line-height: 1; font-weight: 700;">595</div>
                                <div class="font-bold dark:text-gray-900" style="font-size: 28px; line-height: 1; font-weight: 700;">PALLET</div>
                            </div>
                        </div>
                        
                        <!-- Kanan: Box Total dengan layout vertikal -->
                        <div class="flex-1 flex flex-col" style="position: relative;">
                            <div class="bon-box border-2 border-black dark:border-white bg-white dark:bg-slate-700" style="position: absolute; bottom: 0; right: 0; width: 84%; border-right: 0; border-bottom: 0; margin-bottom: 0;">
                                <!-- BON GRUPPPP dengan baris kosong -->
                                <table class="w-full border-collapse">
                                    <colgroup>
                                        <col style="width: 40px;" />
                                        <col style="width: 40px;" />
                                        <col style="width: 40px;" />
                                        <col style="width: 150px;" />
                                    </colgroup>
                                    @for ($i = 1; $i <= 7; $i++)
                                    <tr style="height:24px !important;">
                                      <!-- KOLOM KIRI (INPUT), gabung 2 kolom -->
                                      <td colspan="2" class="{{ $i == 7 ? '' : 'border-b' }} border-black dark:border-white p-0" style="height:24px !important;">
                                        <input type="text"
                                               class="w-full h-full border-0 bg-transparent dark:text-white focus:bg-yellow-100 dark:focus:bg-yellow-200 focus:outline-none"
                                               style="height:24px !important; line-height:24px !important; padding:0 2px;"
                                               id="bon_left_{{ $i }}">
                                      </td>

                                      <!-- KOLOM KUNING (NON-INPUT) -->
                                      <td class="{{ $i == 7 ? '' : 'border-b' }} border-l border-black dark:border-white bg-yellow-300 dark:bg-yellow-400 print:bg-yellow-300 text-center"
                                          style="width: 40px; height:24px !important; line-height:24px !important;">
                                        <div class="text-[11px] font-bold dark:text-black">=&gt;</div>
                                      </td>

                                      <!-- KOLOM KANAN (INPUT ANGKA) -->
                                      <td class="{{ $i == 7 ? '' : 'border-b' }} border-black dark:border-white p-0 text-right" style="width:150px; height:24px !important;">
                                        <input type="text"
                                               class="w-full h-full border-0 bg-transparent dark:text-white text-right focus:bg-yellow-100 dark:focus:bg-yellow-200 focus:outline-none"
                                               style="height:24px !important; line-height:24px !important; padding:0 2px;"
                                               id="bon_right_{{ $i }}">
                                      </td>
                                    </tr>
                                    @endfor
                                
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>





<script>
function openModal(modalId) {
    const modal = document.getElementById(modalId);
    modal.classList.remove('hidden');
    modal.classList.remove('opacity-0');
    modal.classList.add('opacity-100');
    
    // Animasi untuk modal content
    setTimeout(() => {
        const content = modal.querySelector('.transform');
        if (content) {
            content.classList.remove('scale-95', 'translate-y-4');
            content.classList.add('scale-100', 'translate-y-0');
        }
    }, 10);
    
    // Initialize toggle untuk modal tambah
    if (modalId === 'tambahModal') {
        setTimeout(() => {
            toggleJenisGaji();
            hitungGajiPokok();
        }, 100);
    }
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    const content = modal.querySelector('.transform');
    
    // Animasi keluar
    modal.classList.remove('opacity-100');
    modal.classList.add('opacity-0');
    
    if (content) {
        content.classList.remove('scale-100', 'translate-y-0');
        content.classList.add('scale-95', 'translate-y-4');
    }
    
    // Hide setelah animasi selesai
    setTimeout(() => {
        modal.classList.add('hidden');
    }, 300);
}

// Hapus fitur edit modal yang tidak digunakan (form edit_* tidak ada di halaman)

// Toggle jenis gaji dan hitung otomatis
function toggleJenisGaji() {
    const borongan = document.getElementById('jg_borongan').checked;
    const sectionBorongan = document.getElementById('section_borongan');
    const sectionHarian = document.getElementById('section_harian');
    const labelBorongan = document.getElementById('label_borongan');
    const labelHarian = document.getElementById('label_harian');
    
    if (borongan) {
        sectionBorongan.classList.remove('hidden');
        sectionHarian.classList.add('hidden');
        labelBorongan.classList.add('bg-green-100', 'text-green-800', 'dark:bg-green-900/30', 'dark:text-green-300');
        labelBorongan.classList.remove('bg-white', 'dark:bg-slate-700');
        labelHarian.classList.remove('bg-green-100', 'text-green-800', 'dark:bg-green-900/30', 'dark:text-green-300');
        labelHarian.classList.add('bg-white', 'dark:bg-slate-700');
    } else {
        sectionHarian.classList.remove('hidden');
        sectionBorongan.classList.add('hidden');
        labelHarian.classList.add('bg-blue-100', 'text-blue-800', 'dark:bg-blue-900/30', 'dark:text-blue-300');
        labelHarian.classList.remove('bg-white', 'dark:bg-slate-700');
        labelBorongan.classList.remove('bg-green-100', 'text-green-800', 'dark:bg-green-900/30', 'dark:text-green-300');
        labelBorongan.classList.add('bg-white', 'dark:bg-slate-700');
    }
    hitungGajiPokok();
}

function hitungGajiPokok() {
    const borongan = document.getElementById('jg_borongan').checked;
    const display = document.getElementById('gaji_pokok_display');
    const value = document.getElementById('gaji_pokok_value');
    let total = 0;
    
    if (borongan) {
        const unit = parseInt(document.getElementById('jumlah_unit').value) || 0;
        const tarif = parseInt(document.getElementById('tarif_per_unit').value) || 0;
        total = unit * tarif;
    } else {
        const hari = parseInt(document.getElementById('jumlah_hari').value) || 0;
        const tarif = parseInt(document.getElementById('tarif_harian').value) || 0;
        total = hari * tarif;
    }
    
    display.value = 'Rp ' + total.toLocaleString('id-ID');
    value.value = total;
}

function autoFillEmployeeData() {
    // Hapus auto-fill dari employee karena sekarang pakai sistem borongan/harian
    hitungGajiPokok();
}

function formatRupiah(number) {
    if (!number) return '';
    const num = parseInt(number.toString().replace(/[^\d]/g, ''));
    if (isNaN(num) || num === 0) return '';
    return 'Rp ' + num.toLocaleString('id-ID');
}

function formatCurrencyInput(input) {
    // Get the raw numeric value, removing all non-digits
    let rawValue = input.value.replace(/[^\d]/g, '');
    
    // If empty, clear both fields
    if (!rawValue) {
        input.value = '';
        updateHiddenField(input, '');
        return;
    }
    
    // Convert to number and format
    const numericValue = parseInt(rawValue);
    if (!isNaN(numericValue)) {
        // Format for display
        input.value = formatRupiah(numericValue);
        // Store raw numeric value in hidden field
        updateHiddenField(input, numericValue.toString());
    }
}

function updateHiddenField(displayInput, value) {
    const fieldName = displayInput.id.replace('_display', '_value');
    const hiddenField = document.getElementById(fieldName);
    if (hiddenField) {
        hiddenField.value = value;
    }
}

function prepareFormSubmission(form) {
    // Cek duplikasi: 1 karyawan hanya boleh 1 gaji per bulan & tahun
    try {
        const employeeId = parseInt(form.querySelector('#employee_select')?.value);
        const bulan = parseInt(form.querySelector('[name="bulan"]')?.value);
        const tahun = parseInt(form.querySelector('[name="tahun"]')?.value);
        if (!isNaN(employeeId) && !isNaN(bulan) && !isNaN(tahun)) {
            const exists = (existingSalariesData || []).some(s => parseInt(s.employee_id) === employeeId && parseInt(s.bulan) === bulan && parseInt(s.tahun) === tahun);
            if (exists) {
                // Tampilkan modal tengah dan batalkan submit
                openDuplicateCenterModal();
                return false;
            }
        }
    } catch (e) { /* ignore */ }

    // Normalisasi angka sebelum submit
    const currencyFields = ['tunjangan', 'bonus', 'lembur', 'potongan_pajak', 'potongan_bpjs', 'potongan_lain'];
    currencyFields.forEach(field => {
        const displayField = form.querySelector(`[name="${field}_display"]`);
        const hiddenField = form.querySelector(`[name="${field}"]`);
        if (displayField && hiddenField) {
            const numericValue = displayField.value.replace(/[^\d]/g, '') || '0';
            hiddenField.value = numericValue;
        }
    });

    return true;
}

// Fitur pencarian nama karyawan
function filterKaryawanTable() {
    const input = document.getElementById('searchKaryawan').value.toLowerCase();
    const filterBulan = document.getElementById('filterBulan').value;
    const filterTahun = document.getElementById('filterTahun').value;
    const rows = document.querySelectorAll('table tbody tr');

    // 1) Tampilkan/sembunyikan baris sesuai pencarian + filter bulan/tahun
    rows.forEach(row => {
        const namaCell = row.querySelector('td');
        if (!namaCell) return;
        const nama = namaCell.textContent.toLowerCase();

        const periodeCell = row.querySelectorAll('td')[1];
        let show = nama.includes(input);

        if (periodeCell) {
            const periodeText = periodeCell.textContent.trim();
            if (filterBulan) {
                const bulanNama = periodeText.split(' ')[0];
                const bulanIndex = new Date(Date.parse(bulanNama + " 1, 2020")).getMonth() + 1;
                if (parseInt(filterBulan) !== bulanIndex) show = false;
            }
            if (filterTahun) {
                const tahun = periodeText.split(' ')[1];
                if (tahun !== filterTahun) show = false;
            }
        }
        row.style.display = show ? '' : 'none';
    });

    // 2) Hitung ulang kartu statistik berbasis filter bulan/tahun SAJA (abaikan pencarian nama)
    let totalDibayar = 0;
    let totalBelumDibayar = 0;
    let countDibayar = 0;
    let totalGajiSemua = 0;
    let countSemua = 0;

    rows.forEach(row => {
        const tds = row.querySelectorAll('td');
        if (tds.length < 9) return;
        const periodeText = tds[1].textContent.trim();
        const totalGajiText = tds[7].textContent; // kolom Total Gaji
        const statusText = tds[8].textContent.trim(); // kolom Status

        // Cocokkan bulan/tahun
        let match = true;
        if (filterBulan) {
            const bulanNama = periodeText.split(' ')[0];
            const bulanIndex = new Date(Date.parse(bulanNama + " 1, 2020")).getMonth() + 1;
            if (parseInt(filterBulan) !== bulanIndex) match = false;
        }
        if (filterTahun) {
            const tahun = periodeText.split(' ')[1];
            if (tahun !== filterTahun) match = false;
        }
        if (!match) return;

        const nilai = parseInt((totalGajiText || '').replace(/[^\d]/g, '')) || 0;
        totalGajiSemua += nilai;
        countSemua += 1;
        if (statusText.toLowerCase().includes('dibayar')) {
            totalDibayar += nilai;
            countDibayar += 1;
        } else {
            totalBelumDibayar += nilai;
        }
    });

    // Rata-rata berdasarkan baris yang cocok
    const rata = countSemua > 0 ? Math.round(totalGajiSemua / countSemua) : 0;

    // Update kartu
    const elDibayar = document.getElementById('statTotalDibayar');
    const elBelum = document.getElementById('statBelumDibayar');
    const elKaryawan = document.getElementById('statKaryawanDibayar');
    const elRata = document.getElementById('statRataRata');
    if (elDibayar) elDibayar.textContent = formatRupiahInt(totalDibayar);
    if (elBelum) elBelum.textContent = formatRupiahInt(totalBelumDibayar);
    if (elKaryawan) elKaryawan.textContent = countDibayar.toString();
    if (elRata) elRata.textContent = formatRupiahInt(rata);
}

// Data gaji yang sudah ada (untuk cek duplikasi karyawan+bulan+tahun)
const existingSalariesData = @json(($salaries ?? collect())->map->only(['employee_id','bulan','tahun'])->values());

// Helper: format ribuan
function formatRupiahInt(n) {
    return (n || 0).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
}

// Set default filter ke bulan/tahun saat ini ketika halaman dibuka
document.addEventListener('DOMContentLoaded', function() {
    const fb = document.getElementById('filterBulan');
    const ft = document.getElementById('filterTahun');
    const now = new Date();
    if (fb && !fb.value) fb.value = String(now.getMonth() + 1);
    if (ft && !ft.value) ft.value = String(now.getFullYear());
    // Terapkan filter awal dan hitung kartu
    filterKaryawanTable();
});
</script>

<!-- Modal Pilih Tahun -->
<div id="yearModal" class="fixed inset-0 z-50 flex items-center justify-center hidden">
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
                @for($year = 2020; $year <= 2035; $year++)
                    <button type="button" onclick="selectYear({{ $year }})" 
                            class="year-btn px-3 py-2 text-sm font-medium rounded-md border transition-colors
                                   {{ $year == now()->format('Y') ? 
                                      'bg-indigo-600 text-white border-indigo-600' : 
                                      'bg-white text-gray-700 border-gray-300 hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-200 dark:border-gray-600 dark:hover:bg-gray-600' }}"
                            data-year="{{ $year }}">
                        {{ $year }}
                    </button>
                @endfor
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

function openYearModal() {
    document.getElementById('yearModal').classList.remove('hidden');
}


function closeYearModal() {
    document.getElementById('yearModal').classList.add('hidden');
}

function selectYear(year) {
    const filterTahun = document.getElementById('filterTahun');
    const yearButtonText = document.getElementById('yearButtonText');
    
    if (filterTahun) {
        filterTahun.value = year;
        filterKaryawanTable();
    }
    
    if (yearButtonText) {
        yearButtonText.textContent = 'Pilih Tahun (' + year + ')';
    }
    
    document.querySelectorAll('.year-btn').forEach(btn => {
        if (btn.dataset.year == year) {
            btn.className = 'year-btn px-3 py-2 text-sm font-medium rounded-md border transition-colors bg-indigo-600 text-white border-indigo-600';
        } else {
            btn.className = 'year-btn px-3 py-2 text-sm font-medium rounded-md border transition-colors bg-white text-gray-700 border-gray-300 hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-200 dark:border-gray-600 dark:hover:bg-gray-600';
        }
    });
    
    closeYearModal();
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeYearModal();
    }
});
</script>

@endsection
