@extends('layouts.app')

@section('title', 'Manajemen Gaji Karyawan')

@push('styles')
<style>
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

/* Garis vertikal untuk kolom tanggal */
.salary-table .date-columns::before {
  content: '';
  position: absolute;
  top: -1000px;
  bottom: -1000px;
  left: 0;
  width: 100%;
  background-image: repeating-linear-gradient(
    to right,
    transparent 0px,
    transparent 34px,
    #000 34px,
    #000 35px
  );
  background-size: 35px 100%;
  background-repeat: repeat-x;
  pointer-events: none;
  z-index: 5;
}

.salary-table .date-columns {
  position: relative;
  overflow: visible !important;
}

.salary-table .date-column {
  border: 0 !important;
  position: relative;
}

.salary-table td.date-grid {
  border-left: 0 !important;
  border-right: 0 !important;
  padding: 0;
  overflow: visible !important;
}

.salary-table {
  border-collapse: collapse !important;
}

.salary-table th,
.salary-table td {
  border: 1px solid rgba(0,0,0,0.6) !important;
}

.salary-table tbody tr {
  background: transparent !important;
}

/* Kontrol tinggi baris */
.salary-table {
  --row-h: 10px;
}

.salary-table th,
.salary-table td {
  padding-top: 2px !important;
  padding-bottom: 2px !important;
  line-height: 1.2 !important;
  font-size: 10px !important;
}

.salary-table tbody td,
.salary-table tbody th {
  height: var(--row-h) !important;
  line-height: var(--row-h) !important;
  padding-top: 0 !important;
  padding-bottom: 0 !important;
}

.salary-table .date-column { 
  height: var(--row-h) !important; 
}

.salary-table .date-column input {
  height: var(--row-h) !important;
  line-height: var(--row-h) !important;
  padding: 0 !important;
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
function calculateRow(rowIndex) {
    let total = 0;
    
    // Hitung total dari kolom tanggal 1-31
    for (let day = 1; day <= 31; day++) {
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
    
    // Hitung grand total (total * harga)
    const priceInput = document.getElementById(`price_${rowIndex}`);
    const price = priceInput ? (parseInt(priceInput.value) || 10120) : 10120;
    const grandTotal = total * price;
    
    const grandTotalElement = document.getElementById(`grand_total_${rowIndex}`);
    if (grandTotalElement) {
        grandTotalElement.textContent = grandTotal.toLocaleString('id-ID');
    }
    
    // Update total kolom per hari
    updateDayTotals();
    
    // Update grand total keseluruhan
    updateGrandTotal();
}

function updateDayTotals() {
    for (let day = 1; day <= 31; day++) {
        let dayTotal = 0;
        
        // Hitung total per hari dari semua baris
        for (let row = 1; row <= 20; row++) {
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
    for (let row = 1; row <= 20; row++) {
        const totalElement = document.getElementById(`total_${row}`);
        if (totalElement) {
            const pieces = parseInt(totalElement.textContent) || 0;
            totalPieces += pieces;
            
            const priceInput = document.getElementById(`price_${row}`);
            const price = priceInput ? (parseInt(priceInput.value) || 10120) : 10120;
            grandTotal += pieces * price;
        }
    }
    
    // Update total pieces
    const grandPiecesElement = document.getElementById('grand_pieces');
    if (grandPiecesElement) {
        grandPiecesElement.textContent = totalPieces + ' pcs';
    }
    
    // Update final total
    const finalTotalElement = document.getElementById('final_total');
    if (finalTotalElement) {
        finalTotalElement.textContent = grandTotal.toLocaleString('id-ID');
    }
}

// Auto-load Excel Template
document.addEventListener('DOMContentLoaded', function() {
    loadExcelTemplate();
});

async function loadExcelTemplate() {
    try {
        const response = await fetch('/excel/generate');
        const data = await response.json();
        
        if (data.success) {
            const container = document.getElementById('excelTemplateContainer');
            container.innerHTML = data.html;
            
            // Add JavaScript untuk perhitungan otomatis
            addCalculationListeners();
            
            console.log('✅ Template Excel berhasil dimuat:', data.info);
        } else {
            throw new Error(data.error || 'Gagal memuat template Excel');
        }
    } catch (error) {
        console.error('❌ Error loading Excel template:', error);
        const container = document.getElementById('excelTemplateContainer');
        container.innerHTML = `
            <div class="p-4 text-center border-2 border-red-200 bg-red-50">
                <div class="text-red-600 mb-2">
                    <svg class="w-8 h-8 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                </div>
                <p class="text-red-700 font-medium">Gagal memuat template Excel</p>
                <p class="text-red-600 text-sm mt-1">${error.message}</p>
                <button onclick="loadExcelTemplate()" class="mt-3 px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                    Coba Lagi
                </button>
            </div>
        `;
    }
}

function addCalculationListeners() {
    // Add event listeners untuk semua input yang ada di template Excel
    const inputs = document.querySelectorAll('#excelTemplateContainer input[type="text"]');
    inputs.forEach((input, index) => {
        if (input.id && input.id.includes('day_')) {
            input.addEventListener('input', function() {
                const rowMatch = input.id.match(/day_(\d+)_/);
                if (rowMatch) {
                    calculateRow(parseInt(rowMatch[1]));
                }
            });
        }
    });
    
    console.log(`✅ Added calculation listeners to ${inputs.length} input fields`);
}

</script>
@endpush

@section('content')
<div class="space-y-6">
    <!-- Header dengan Statistik -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
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

    <!-- Header dan Tombol Aksi -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-3 sm:space-y-0 mb-4">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Data Gaji Karyawan</h1>
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-3">
                <input type="text" id="searchKaryawan" placeholder="Cari nama karyawan..." class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-slate-800/60 dark:border-white/10 dark:text-gray-100 dark:placeholder-gray-400 dark:focus:ring-blue-500/40" oninput="filterKaryawanTable()">
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
            </div>
        </div>
    </div>

    <!-- Excel Template Container (Auto-Generated) -->
    <div id="excelTemplateContainer" class="bg-white dark:bg-slate-900 shadow-lg overflow-hidden">
        <div class="p-4 text-center">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto mb-4"></div>
            <p class="text-gray-600">Memuat template Excel...</p>
        </div>
    </div>

    <!-- Tabel Lama (Hidden, akan diganti dengan Excel Template) -->
    <div class="hidden bg-white dark:bg-slate-900 shadow-lg overflow-hidden border-2 border-black dark:border-slate-400 salary-outline">
        <!-- Header Perusahaan -->
        <div class="border-b-2 border-black dark:border-slate-400 pl-4 pr-0 pt-0 pb-1">
            <div class="flex justify-between items-start">
                <div class="text-left text-xs text-black dark:text-white">
                    <div class="rules-box mb-1">
                        <div class="title">PERATURAN</div>
                        <div class="item">DILARANG BELAH PAPAN TANPA IJIN</div>
                        <div class="item">DILARANG BELAH BALOK TANPA IJIN</div>
                    </div>
                </div>
                <div class="flex-1 text-center">
                    <h2 class="company-title text-black dark:text-white">PT. CAM JAYA ABADI</h2>
                </div>
                <div class="text-right text-xs text-black dark:text-white salary-header-right">
                    <div class="salary-header-label">BIAYA / UPAH KERJA</div>
                    <div class="salary-header-date"><span class="font-bold">{{ now()->format('d-M-y') }}</span></div>
                </div>
            </div>
        </div>
        
        <!-- CSS untuk stabilitas tabel -->
        <style>
            .salary-table-container {
                width: 100%;
                overflow-x: visible;
                overflow-y: hidden;
                position: relative;
                zoom: 1 !important;
                transform: none !important;
            }
            
            .salary-table {
                font-size: 10px !important;
                table-layout: fixed !important;
                width: 100% !important;
                min-width: 1600px !important;
                max-width: none !important;
                border-collapse: collapse !important;
                zoom: 1 !important;
                transform: scale(1) !important;
                transform-origin: top left !important;
                transition: all 0.3s ease-in-out !important;
            }
            
            /* Ketika sidebar hidden, perlebar tabel salary */
            body.sidebar-hidden .salary-table {
                min-width: 100% !important;
                font-size: 11px !important;
            }
            
            body.sidebar-hidden .salary-table-container {
                overflow-x: visible !important;
            }
            
            body.sidebar-hidden .date-column {
                width: 45px !important;
                min-width: 45px !important;
            }
            
            /* Optimasi untuk konsistensi ukuran */
            .salary-table .date-column {
                width: 35px !important;
                min-width: 35px !important;
                flex-shrink: 0;
            }
            
            .salary-table input {
                font-family: inherit !important;
                border-radius: 0 !important;
            }
            
            /* Pastikan semua sel memiliki ukuran konsisten */
            .salary-table tbody tr {
                height: 10px !important;
            }
            
            .salary-table thead tr {
                height: 12px !important;
            }
            
            .salary-table tfoot tr {
                height: 12px !important;
            }
            
            .salary-table th,
            .salary-table td {
                box-sizing: border-box !important;
                white-space: nowrap !important;
                overflow: hidden !important;
                text-overflow: ellipsis !important;
            }
            
            @media (max-width: 1200px) {
                .salary-table-container {
                    overflow-x: scroll;
                }
            }
            
            /* Prevent zoom effects */
            .salary-table * {
                zoom: 1 !important;
                transform: none !important;
            }

            /* Garis tabel sedikit lebih tebal */
            .salary-table th,
            .salary-table td {
                border-color: #000 !important;
                border-width: 1.2px !important;
                border-style: solid !important;
            }

            /* Pemisah antara header dan body dibuat lebih tebal agar jelas */
            .salary-table thead th {
                border-bottom-width: 1.8px !important;
            }
            .salary-table tbody tr:first-child td {
                border-top-width: 1.8px !important;
            }

            /* Kolom DESKRIPSI abu-abu (body dan header) */
            .salary-table thead th:nth-child(2) {
                background-color:rgb(206, 206, 206) !important; /* gray-200 */
            }
            .salary-table tbody td:nth-child(2) {
                background-color:rgb(206, 206, 206) !important; /* gray-200 */
            }

            /* Zebra striping HANYA untuk area tanggal */
            .salary-table tbody tr:nth-child(odd) td.date-grid { 
                background-color: rgb(206, 206, 206) !important; 
            }
            .salary-table tbody tr:nth-child(even) td.date-grid { 
                background-color: #ffffff !important; 
            }
            /* Pastikan konten di dalam date-grid transparan agar warna td terlihat */
            .salary-table tbody td.date-grid .date-scroll-container,
            .salary-table tbody td.date-grid .date-columns,
            .salary-table tbody td.date-grid .date-column {
                background-color: transparent !important;
            }
        </style>

        <!-- Tabel Gaji -->
        <div class="salary-table-container" id="salaryTableContainer" style="position: relative;">
            <!-- Overlay garis vertikal untuk area TANGGAL -->
            <div id="dateGridOverlay" aria-hidden="true" style="position:absolute; top:0; left:0; height:0; width:0; pointer-events:none; z-index:50;
                 background-image: repeating-linear-gradient(to right, rgba(0,0,0,0.8) 0px, rgba(0,0,0,0.8) 1px, transparent 1px, transparent 35px);
                 background-repeat:repeat; background-size:35px 100%; opacity:1;"></div>
            <style>
                /* Outline luar (container utama) menjadi garis putus-putus */
                .salary-outline {
                    border-style: dashed !important;
                    border-width: 2px !important; /* lebih tebal */
                    border-color: #000 !important;
                }
            </style>
            <table class="salary-table border-collapse w-full">
                <thead>
                    <tr class="bg-gray-100 dark:bg-slate-700">
                        <th class="border border-black dark:border-slate-400 px-1 py-0 text-center font-bold bg-gray-100 dark:bg-slate-700 text-black dark:text-white" style="width: 6%; height: 12px; font-size: 9px;">ITEM</th>
                        <th class="border border-black dark:border-slate-400 px-1 py-0 text-center font-bold bg-gray-100 dark:bg-slate-700 text-black dark:text-white" style="width: 8%; height: 12px; font-size: 9px;">DESKRIPSI</th>
                        
                        <!-- Container scroll horizontal untuk tanggal -->
                        <td class="border border-black dark:border-slate-400 px-0 py-0 date-grid" style="width: 45%; height: 12px;">
                            <div class="date-scroll-container" style="width: 100%; height: 100%; overflow-x: auto;">
                                <div class="date-columns" style="width: 1085px; display: flex;">
                                    @for($i = 1; $i <= 31; $i++)
                                        <div class="date-column border border-black dark:border-slate-400 px-1 py-0 text-center font-bold text-black dark:text-white bg-gray-100 dark:bg-slate-700" style="font-size: 8px; height: 12px; display: flex; align-items: center; justify-content: center; width: 35px; min-width: 35px; flex-shrink: 0;">{{ $i }}</div>
                                    @endfor
                                </div>
                            </div>
                        </td>
                        
                        <th class="border border-black dark:border-slate-400 px-1 py-0 text-center font-bold bg-gray-100 dark:bg-slate-700 text-black dark:text-white" style="width: 4%; height: 12px; font-size: 9px;">JUMLAH</th>
                        <th class="border border-black dark:border-slate-400 px-1 py-0 text-center font-bold bg-gray-100 dark:bg-slate-700 text-black dark:text-white" style="width: 2%; height: 12px; font-size: 9px;"></th>
                        <th class="border border-black dark:border-slate-400 px-1 py-0 text-center font-bold bg-gray-100 dark:bg-slate-700 text-black dark:text-white" style="width: 6%; height: 12px; font-size: 9px;">HSL. PROD</th>
                        <th class="border border-black dark:border-slate-400 px-1 py-0 text-center font-bold bg-gray-100 dark:bg-slate-700 text-black dark:text-white" style="width: 2%; height: 12px; font-size: 9px;"></th>
                        <th class="border border-black dark:border-slate-400 px-1 py-0 text-center font-bold bg-gray-100 dark:bg-slate-700 text-black dark:text-white" style="width: 7%; height: 12px; font-size: 9px;">HARGA</th>
                        <th class="border border-black dark:border-slate-400 px-1 py-0 text-center font-bold bg-gray-100 dark:bg-slate-700 text-black dark:text-white" style="width: 6%; height: 12px; font-size: 9px;">TOTAL</th>
                        <th class="border border-black dark:border-slate-400 px-1 py-0 text-center font-bold bg-gray-100 dark:bg-slate-700 text-black dark:text-white" style="width: 14%; height: 12px; font-size: 9px;">KETERANGAN</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-slate-900">
                    @forelse($salaries ?? [] as $index => $salary)
                    <tr class="hover:bg-gray-50 dark:hover:bg-slate-800">
                        <td class="border border-black dark:border-slate-400 px-1 py-0 font-medium text-black dark:text-white bg-white dark:bg-slate-900" style="height: 10px; line-height: 10px; width: 6%; font-size: 9px;">
                            {{ strtoupper($salary->employee->nama_karyawan) }}
                        </td>
                        <td class="border border-black dark:border-slate-400 px-1 py-0 text-black dark:text-white bg-white dark:bg-slate-900" style="height: 10px; line-height: 10px; width: 8%; font-size: 9px;">
                            {{ strtoupper($salary->employee->posisi ?? 'KARYAWAN') }}
                        </td>
                        
                        <!-- Container scroll horizontal untuk input tanggal -->
                        <td class="border border-black dark:border-slate-400 px-0 py-0 date-grid" style="width: 45%; height: 10px;">
                            <div class="date-scroll-container" style="width: 100%; height: 100%; overflow-x: auto;">
                                <div class="date-columns" style="width: 1085px; display: flex;">
                                    @for($day = 1; $day <= 31; $day++)
                                        <div class="date-column border border-black dark:border-slate-400" style="height: 10px; padding: 0; width: 35px; min-width: 35px; flex-shrink: 0;">
                                            <input type="text" 
                                                   class="w-full h-full text-center border-0 bg-transparent dark:bg-slate-900 text-black dark:text-white focus:bg-yellow-100 dark:focus:bg-yellow-200 dark:focus:text-black focus:outline-none" 
                                                   style="font-size: 8px; padding: 0px; line-height: 10px; resize: none;"
                                                   placeholder=""
                                                   onchange="calculateRow({{ $index }})"
                                                   id="day_{{ $index }}_{{ $day }}">
                                        </div>
                                    @endfor
                                </div>
                            </div>
                        </td>
                        
                        <td class="border border-black dark:border-slate-400 px-1 py-0 text-center text-black dark:text-white" style="height: 10px; line-height: 10px; width: 4%; font-size: 9px;">
                            <span id="total_{{ $index }}">{{ rand(450, 500) }}</span>
                        </td>
                        <!-- Kolom kosong kiri HSL. PROD -->
                        <td class="border border-black dark:border-slate-400 px-1 py-0 text-black dark:text-white bg-white dark:bg-slate-900" style="height: 10px; line-height: 10px; width: 2%;"></td>
                        <td class="border border-black dark:border-slate-400 px-1 py-0 text-right text-black dark:text-white" style="height: 10px; line-height: 10px; width: 6%; font-size: 9px;">
                            {{ number_format(10120, 0, ',', '.') }}
                        </td>
                        <!-- Kolom kosong kanan HSL. PROD -->
                        <td class="border border-black dark:border-slate-400 px-1 py-0 text-black dark:text-white bg-white dark:bg-slate-900" style="height: 10px; line-height: 10px; width: 2%;"></td>
                        <td class="border border-black dark:border-slate-400 px-1 py-0 text-right text-black dark:text-white" style="height: 10px; line-height: 10px; width: 7%; font-size: 9px;">
                            {{ number_format(rand(100000, 200000), 0, ',', '.') }}
                        </td>
                        <td class="border border-black dark:border-slate-400 px-1 py-0 text-right font-bold text-black dark:text-white" style="height: 10px; line-height: 10px; width: 6%; font-size: 9px;">
                            <span id="grand_total_{{ $index }}">{{ number_format($salary->total_gaji, 0, ',', '.') }}</span>
                        </td>
                        <td class="border border-black dark:border-slate-400 px-1 py-0 text-left bg-white dark:bg-slate-900" style="height: 10px; line-height: 10px; width: 14%; padding-left: 2px; font-size: 9px;">
                            @if($salary->status_pembayaran === 'dibayar')
                                <span class="text-green-600 dark:text-green-400 font-bold">LUNAS</span>
                            @else
                                <span class="text-red-600 dark:text-red-400 font-bold">BELUM</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <!-- Baris kosong untuk input manual -->
                    @for($i = 1; $i <= 28; $i++)
                    <tr class="hover:bg-gray-50 dark:hover:bg-slate-800">
                        <td class="border border-black dark:border-slate-400 px-1 py-0" style="height: 10px; width: 6%;">
                            <input type="text" class="w-full h-full border-0 bg-transparent dark:bg-slate-900 text-black dark:text-white focus:bg-yellow-100 dark:focus:bg-yellow-200 dark:focus:text-black focus:outline-none" placeholder="" style="line-height: 10px; padding: 0; font-size: 9px;">
                        </td>
                        <td class="border border-black dark:border-slate-400 px-1 py-0" style="height: 10px; width: 8%;">
                            <input type="text" class="w-full h-full border-0 bg-transparent dark:bg-slate-900 text-black dark:text-white focus:bg-yellow-100 dark:focus:bg-yellow-200 dark:focus:text-black focus:outline-none" placeholder="" style="line-height: 10px; padding: 0; font-size: 9px;">
                        </td>
                        
                        <!-- Container scroll horizontal untuk input tanggal -->
                        <td class="border border-black dark:border-slate-400 px-0 py-0 date-grid" style="width: 45%; height: 10px;">
                            <div class="date-scroll-container" style="width: 100%; height: 100%;">
                                <div class="date-columns">
                                    @for($day = 1; $day <= 31; $day++)
                                        <div class="date-column border border-black dark:border-slate-400" style="height: 10px; padding: 0;">
                                            <input type="text" 
                                                   class="w-full h-full text-center border-0 bg-transparent dark:bg-slate-900 text-black dark:text-white focus:bg-yellow-100 dark:focus:bg-yellow-200 dark:focus:text-black focus:outline-none" 
                                                   style="font-size: 8px; padding: 0px; line-height: 10px; resize: none;"
                                                   placeholder=""
                                                   onchange="calculateRow({{ $i }})"
                                                   id="day_{{ $i }}_{{ $day }}">
                                        </div>
                                    @endfor
                                </div>
                            </div>
                        </td>
                        
                        <td class="border border-black dark:border-slate-400 px-1 py-0 text-black dark:text-white" style="height: 10px; line-height: 10px; width: 4%; font-size: 9px;">
                            <span id="total_{{ $i }}">0</span>
                        </td>
                        <!-- Kolom kosong kiri HSL. PROD -->
                        <td class="border border-black dark:border-slate-400 px-1 py-0 bg-white dark:bg-slate-900" style="height: 10px; width: 2%;"></td>
                        <td class="border border-black dark:border-slate-400 px-1 py-0" style="height: 10px; width: 6%;">
                            <input type="text" class="w-full h-full border-0 bg-transparent dark:bg-slate-900 text-black dark:text-white focus:bg-yellow-100 dark:focus:bg-yellow-200 dark:focus:text-black focus:outline-none text-right" placeholder="" id="hsl_prod_{{ $i }}" style="line-height: 10px; padding: 0; font-size: 9px;">
                        </td>
                        <!-- Kolom kosong kanan HSL. PROD -->
                        <td class="border border-black dark:border-slate-400 px-1 py-0 bg-white dark:bg-slate-900" style="height: 10px; width: 2%;"></td>
                        <td class="border border-black dark:border-slate-400 px-1 py-0" style="height: 10px; width: 7%;">
                            <input type="text" class="w-full h-full border-0 bg-transparent dark:bg-slate-900 text-black dark:text-white focus:bg-yellow-100 dark:focus:bg-yellow-200 dark:focus:text-black focus:outline-none text-right" placeholder="" id="biaya_prod_{{ $i }}" style="line-height: 10px; padding: 0; font-size: 9px;">
                        </td>
                        <td class="border border-black dark:border-slate-400 px-1 py-0 text-right font-bold text-black dark:text-white" style="height: 10px; line-height: 10px; width: 7%; font-size: 9px;">
                            <span id="grand_total_{{ $i }}">0</span>
                        </td>
                        <td class="border border-black dark:border-slate-400 px-1 py-0" style="height: 10px; width: 14%; border-top: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black;">
                            <input type="text" class="w-full h-full border-0 bg-transparent dark:bg-slate-900 text-black dark:text-white focus:bg-yellow-100 dark:focus:bg-yellow-200 dark:focus:text-black focus:outline-none" style="line-height: 10px; padding: 0; padding-left: 2px; font-size: 9px;">
                        </td>
                    </tr>
                    @endfor
                    @endforelse
                </tbody>
                <!-- Footer Total -->
                <tfoot>
                    <tr class="bg-gray-100 dark:bg-slate-700 font-bold">
                        <td colspan="2" class="border border-black dark:border-slate-400 px-1 py-0 text-black dark:text-white" style="height: 12px; font-size: 9px;">Hasil produksi pallet dan biaya borongan</td>
                        <!-- Container scroll horizontal untuk total per hari -->
                        <td class="border border-black dark:border-slate-400 px-0 py-0" style="width: 45%; height: 12px;">
                            <div class="date-scroll-container" style="width: 100%; height: 100%; overflow-x: auto;">
                                <div class="date-columns" style="width: 1085px; display: flex;">
                                    @for($day = 1; $day <= 31; $day++)
                                        <div class="date-column" id="day_total_{{ $day }}" style="font-size: 8px; height: 12px; display: flex; align-items: center; justify-content: center; text-align: center; padding: 0; width: 35px; min-width: 35px; flex-shrink: 0;">{{ $day <= 20 ? rand(20, 50) : 0 }}</div>
                                    @endfor
                                </div>
                            </div>
                        </td>
                        <td class="border border-black dark:border-slate-400 px-1 py-0 text-center text-black dark:text-white" style="height: 12px; font-size: 9px;">-</td>
                        <td class="border border-black dark:border-slate-400 px-1 py-0 text-black dark:text-white" style="height: 12px; font-size: 9px;">-</td>
                        <td class="border border-black dark:border-slate-400 px-1 py-0 text-center text-black dark:text-white" id="grand_pieces" style="height: 12px; font-size: 9px;">486</td>
                        <td class="border border-black dark:border-slate-400 px-1 py-0 text-black dark:text-white" style="height: 12px; font-size: 9px;">-</td>
                        <td colspan="2" class="border border-black dark:border-slate-400 px-1 py-0 text-right font-bold text-black dark:text-white" id="final_total" style="height: 12px; font-size: 9px;">4.937.120</td>
                        <td class="border border-black dark:border-slate-400 px-1 py-0 text-black dark:text-white" style="height: 12px; font-size: 9px;">-</td>
                    </tr>
                </tfoot>
            </table>
        </div>
        
        <!-- Footer dengan Signature -->
        <div class="border-t-2 border-black dark:border-slate-400 px-4 py-4">
            <div class="flex justify-between items-end">
                <div class="text-xs text-black dark:text-white">
                    <div class="mb-4">TOTAL BON: <span class="font-bold">0</span></div>
                    <div class="mb-1">GRAND TOTAL GAJI DITERIMA: <span class="font-bold">4.937.120</span></div>
                    <div class="grid grid-cols-1 gap-1 mt-4">
                        <div class="bg-yellow-200 dark:bg-yellow-300 px-2 py-1 text-center text-black">BON MANG DIDI</div>
                        <div class="bg-yellow-200 dark:bg-yellow-300 px-2 py-1 text-center text-black">BON JOKOWI</div>
                        <div class="bg-yellow-200 dark:bg-yellow-300 px-2 py-1 text-center text-black">BON MANG DIDI</div>
                    </div>
                </div>
                <div class="text-center">
                    <div class="border-2 border-black dark:border-slate-400 px-8 py-4 mb-2">
                        <div class="text-lg font-bold text-black dark:text-white">486 PALLET</div>
                    </div>
                    <div class="text-xs text-black dark:text-white">
                        <div>Reid Kubro Wahyudin</div>
                        <div class="font-bold">DIREKTUR</div>
                        <div class="mt-8 border-t border-black dark:border-slate-400">DIREKTUR UTAMA</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Toast Notifikasi Modern -->
<div id="toast-duplicate" class="fixed top-6 right-6 z-[60] hidden transform translate-x-full transition-transform duration-300">
    <div class="flex items-start gap-3 rounded-2xl border border-amber-200 bg-gradient-to-r from-amber-50 to-orange-50 px-6 py-4 text-amber-800 shadow-2xl backdrop-blur-sm dark:from-amber-900/30 dark:to-orange-900/30 dark:border-amber-700 dark:text-amber-300 min-w-[350px]">
        <div class="w-10 h-10 bg-gradient-to-br from-amber-500 to-orange-600 rounded-xl flex items-center justify-center flex-shrink-0">
            <i class="fa-solid fa-triangle-exclamation text-white text-lg"></i>
        </div>
        <div class="flex-1">
            <h4 class="font-bold text-amber-900 dark:text-amber-200 mb-1">Duplikasi Data!</h4>
            <p class="text-sm text-amber-700 dark:text-amber-300">Data gaji untuk karyawan dan bulan ini sudah ada. Gunakan menu edit untuk mengubah.</p>
        </div>
        <button onclick="closeToast()" class="w-6 h-6 bg-amber-200 dark:bg-amber-800 rounded-full flex items-center justify-center hover:bg-amber-300 dark:hover:bg-amber-700 transition-colors">
            <i class="fa-solid fa-times text-xs text-amber-700 dark:text-amber-300"></i>
        </button>
    </div>
</div>

<!-- Modal Peringatan Modern -->
<div id="duplicateCenterModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm hidden items-center justify-center z-[70] p-4">
    <div class="bg-white dark:bg-slate-800 rounded-3xl shadow-2xl w-full max-w-md transform scale-95 transition-all duration-300 border border-gray-200 dark:border-slate-600">
        <!-- Header -->
        <div class="bg-gradient-to-r from-amber-500 to-orange-600 p-6 rounded-t-3xl">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-white/20 rounded-2xl flex items-center justify-center">
                    <i class="fa-solid fa-triangle-exclamation text-white text-xl"></i>
                </div>
                <div>
                    <h4 class="text-xl font-bold text-white">Duplikasi Data</h4>
                    <p class="text-amber-100 text-sm">Data sudah ada dalam sistem</p>
                </div>
            </div>
        </div>
        <!-- Content -->
        <div class="p-6">
            <p class="text-gray-600 dark:text-gray-300 mb-6 leading-relaxed">
                Gaji untuk karyawan dan periode yang dipilih sudah tercatat dalam sistem. Silakan periksa kembali atau gunakan menu edit untuk mengubah data.
            </p>
            <div class="flex gap-3 justify-end">
                <button type="button" onclick="closeDuplicateCenterModal()" 
                        class="px-6 py-3 bg-gradient-to-r from-gray-100 to-gray-200 hover:from-gray-200 hover:to-gray-300 text-gray-700 font-semibold rounded-xl transition-all duration-200 transform hover:scale-105">
                    <i class="fa-solid fa-arrow-left mr-2"></i>Kembali
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Generate Payroll Modern -->
<div id="payrollModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm hidden overflow-y-auto h-full w-full z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-800 rounded-3xl shadow-2xl w-full max-w-lg transform scale-95 transition-all duration-300 border border-gray-200 dark:border-slate-600">
        <!-- Header dengan Gradient -->
        <div class="bg-gradient-to-r from-purple-500 to-indigo-600 p-6 rounded-t-3xl">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-white/20 rounded-2xl flex items-center justify-center">
                        <i class="fa-solid fa-magic-wand-sparkles text-white text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-white">Generate Payroll</h3>
                        <p class="text-purple-100 text-sm">Buat gaji otomatis untuk semua karyawan</p>
                    </div>
                </div>
                <button onclick="closeModal('payrollModal')" class="w-8 h-8 bg-white/20 hover:bg-white/30 rounded-lg flex items-center justify-center text-white transition-colors">
                    <i class="fa-solid fa-times"></i>
                </button>
            </div>
        </div>
        <!-- Content -->
        <div class="p-6">
        <form method="POST" action="{{ route('salary.generate-payroll') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Bulan</label>
                <select name="bulan" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500">
                    @for($i = 1; $i <= 12; $i++)
                    <option value="{{ $i }}" {{ $i == date('n') ? 'selected' : '' }}>
                        {{ DateTime::createFromFormat('!m', $i)->format('F') }}
                    </option>
                    @endfor
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tahun</label>
                <select name="tahun" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500">
                    @for($i = 2020; $i <= 2030; $i++)
                    <option value="{{ $i }}" {{ $i == date('Y') ? 'selected' : '' }}>{{ $i }}</option>
                    @endfor
                </select>
            </div>
            <div class="flex justify-end gap-3 pt-6 border-t border-gray-200 dark:border-slate-600">
                <button type="button" onclick="closeModal('payrollModal')" 
                        class="px-6 py-3 bg-gradient-to-r from-gray-100 to-gray-200 hover:from-gray-200 hover:to-gray-300 text-gray-700 font-semibold rounded-xl transition-all duration-200 transform hover:scale-105">
                    <i class="fa-solid fa-times mr-2"></i>Batal
                </button>
                <button type="submit" 
                        class="px-6 py-3 bg-gradient-to-r from-purple-500 to-indigo-600 hover:from-purple-600 hover:to-indigo-700 text-white font-semibold rounded-xl transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-xl">
                    <i class="fa-solid fa-magic-wand-sparkles mr-2"></i>Generate Payroll
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Tambah Gaji -->
<div id="tambahModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm dark:bg-black/80 hidden overflow-y-auto h-full w-full z-50 flex items-center justify-center p-4 transition-all duration-300 opacity-0">
    <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-y-auto border border-gray-200 dark:border-slate-700 transform scale-95 translate-y-4 transition-all duration-300">
        <!-- Header dengan gradient -->
        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 dark:from-blue-500 dark:to-indigo-500 p-6">
            <div class="flex justify-between items-center">
                <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                        <i class="fa-solid fa-money-bill-wave text-white text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-2xl font-bold text-white">Tambah Data Gaji</h3>
                        <p class="text-blue-100 text-sm">Lengkapi informasi gaji karyawan</p>
                    </div>
                </div>
                <button onclick="closeModal('tambahModal')" 
                        class="w-10 h-10 rounded-lg bg-white/20 hover:bg-white/30 text-white transition-all duration-200 flex items-center justify-center">
                    <i class="fa-solid fa-times text-lg"></i>
                </button>
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

function editSalary(salaryData) {
    console.log('[v0] Opening edit modal with data:', salaryData);
    
    // Set form action URL
    const editForm = document.getElementById('editForm');
    editForm.action = `/salary/${salaryData.id}`;
    
    // Populate form fields with formatted currency values
    document.getElementById('edit_employee_select').value = salaryData.employee_id;
    document.getElementById('edit_employee_id_hidden').value = salaryData.employee_id;
    document.getElementById('edit_bulan').value = salaryData.bulan;
    document.getElementById('edit_tahun').value = salaryData.tahun;
    
    // Format currency fields to remove decimals and display as whole numbers
    const gajiPokokValue = Math.round(parseFloat(salaryData.gaji_pokok) || 0);
    document.getElementById('edit_gaji_pokok').value = gajiPokokValue;
    
    document.getElementById('edit_tunjangan').value = Math.round(parseFloat(salaryData.tunjangan) || 0);
    document.getElementById('edit_bonus').value = Math.round(parseFloat(salaryData.bonus) || 0);
    document.getElementById('edit_lembur').value = Math.round(parseFloat(salaryData.lembur) || 0);
    document.getElementById('edit_potongan_pajak').value = Math.round(parseFloat(salaryData.potongan_pajak) || 0);
    document.getElementById('edit_potongan_bpjs').value = Math.round(parseFloat(salaryData.potongan_bpjs) || 0);
    document.getElementById('edit_potongan_lain').value = Math.round(parseFloat(salaryData.potongan_lain) || 0);
    
    document.getElementById('edit_status_pembayaran').value = salaryData.status_pembayaran;
    document.getElementById('edit_tanggal_bayar').value = salaryData.tanggal_bayar || '';
    document.getElementById('edit_keterangan').value = salaryData.keterangan || '';
    
    // Open modal
    openModal('editModal');
}

// Normalisasi nilai numerik sebelum submit form edit
document.addEventListener('DOMContentLoaded', function() {
    const editForm = document.getElementById('editForm');
    if (editForm) {
        editForm.addEventListener('submit', function() {
            const numericIds = [
                'edit_gaji_pokok',
                'edit_tunjangan',
                'edit_bonus',
                'edit_lembur',
                'edit_potongan_pajak',
                'edit_potongan_bpjs',
                'edit_potongan_lain'
            ];
            numericIds.forEach(id => {
                const el = document.getElementById(id);
                if (el && typeof el.value === 'string') {
                    el.value = (el.value || '').toString().replace(/[^\d]/g, '');
                }
            });
        });
    }
});

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

    if (selectedOption.value) {
        // Get raw gaji pokok value directly from data attribute
        const rawGajiPokok = selectedOption.getAttribute('data-gaji-pokok');
        console.log('[v0] Raw gaji pokok from employee:', rawGajiPokok);
        
        if (rawGajiPokok && rawGajiPokok !== '0') {
            // Convert to integer to ensure no decimal issues
            const gajiPokokInt = parseInt(rawGajiPokok);
            console.log('[v0] Converted gaji pokok:', gajiPokokInt);
            
            // Format for display using Indonesian locale
            const formattedGaji = 'Rp ' + gajiPokokInt.toLocaleString('id-ID');
            gajiPokokDisplay.value = formattedGaji;
            gajiPokokValue.value = gajiPokokInt;
            
            console.log('[v0] Final formatted gaji:', formattedGaji);
        } else {
            gajiPokokDisplay.value = '';
            gajiPokokValue.value = '';
        }
        
        console.log('[v0] Auto-filled employee data:', {
            nama: selectedOption.getAttribute('data-nama'),
            posisi: selectedOption.getAttribute('data-posisi'),
            gajiPokok: rawGajiPokok
        });
    } else {
        // Clear form if no employee selected
        gajiPokokDisplay.value = '';
        gajiPokokValue.value = '';
    }
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

// Set filter tahun ke tahun saat ini saat halaman dimuat
document.addEventListener('DOMContentLoaded', function() {
    const filterTahun = document.getElementById('filterTahun');
    if (filterTahun && filterTahun.value) {
        filterKaryawanTable();
    }
});

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

document.addEventListener('DOMContentLoaded', function() {
    const dateScrollContainers = document.querySelectorAll('.date-scroll-container');
    
    dateScrollContainers.forEach((container, index) => {
        container.addEventListener('scroll', function() {
            const scrollLeft = this.scrollLeft;
            dateScrollContainers.forEach((otherContainer, otherIndex) => {
                if (otherIndex !== index) {
                    otherContainer.scrollLeft = scrollLeft;
                }
            });
        });
    });
});
</script>

@endsection
