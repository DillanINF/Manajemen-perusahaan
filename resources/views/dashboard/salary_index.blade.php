@extends('layouts.app')

@section('title', 'Manajemen Gaji Karyawan')

@push('styles')
<style>
/* Animasi Custom untuk UI Modern */
@keyframes slideInRight {
    from { transform: translateX(100%); opacity: 0; }
    to { transform: translateX(0); opacity: 1; }
}

@keyframes slideOutRight {
    from { transform: translateX(0); opacity: 1; }
    to { transform: translateX(100%); opacity: 0; }
}

@keyframes pulse-glow {
    0%, 100% { box-shadow: 0 0 5px rgba(59, 130, 246, 0.5); }
    50% { box-shadow: 0 0 20px rgba(59, 130, 246, 0.8), 0 0 30px rgba(59, 130, 246, 0.6); }
}

.animate-slide-in { animation: slideInRight 0.3s ease-out; }
.animate-slide-out { animation: slideOutRight 0.3s ease-in; }
.animate-pulse-glow { animation: pulse-glow 2s infinite; }

/* Hover effects untuk tabel */
.table-row-hover:hover {
    background: linear-gradient(90deg, rgba(59, 130, 246, 0.05) 0%, rgba(99, 102, 241, 0.05) 100%);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

/* Button hover effects */
.btn-modern {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    color: white;
    padding: 12px 24px;
    border-radius: 12px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.btn-modern:before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
    transition: left 0.5s;
}

.btn-modern:hover:before {
    left: 100%;
}

.btn-modern:hover {
    transform: translateY(-2px);
    box-shadow: 0 12px 35px rgba(102, 126, 234, 0.4);
}

.btn-modern:active {
    transform: translateY(0);
    box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
}

.loading-spinner {
    border: 2px solid #f3f3f3;
    border-top: 2px solid #3498db;
    border-radius: 50%;
    width: 20px;
    height: 20px;
    animation: spin 1s linear infinite;
    display: inline-block;
    margin-right: 10px;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
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

/* === HAPUS SEMUA CSS LAMA YANG BERTENTANGAN === */

/* === SOLUSI FINAL: GARIS VERTIKAL UTUH === */

/* 1. HAPUS SEMUA BORDER PADA DATE-COLUMN */
.salary-table .date-column,
.salary-table .date-column.border,
.salary-table .date-column.border-black,
.salary-table thead .date-column,
.salary-table tbody .date-column,
.salary-table tfoot .date-column {
  border: 0 !important;
  border-top: 0 !important;
  border-bottom: 0 !important;
  border-left: 0 !important;
  border-right: 0 !important;
  border-width: 0 !important;
  box-shadow: none !important;
  position: relative;
}

/* 2. BUAT GARIS VERTIKAL YANG MENEMBUS SELURUH TABEL */
.salary-table .date-columns {
  position: relative;
}

/* Buat garis vertikal dengan pseudo-element yang menembus dari atas ke bawah */
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
  background-attachment: local;
  /* Bantu browser merender garis tetap tajam saat zoom */
  transform: translateZ(0);
  backface-visibility: hidden;
  will-change: background-position;
  pointer-events: none;
  z-index: 5;
}

/* 3. PASTIKAN CONTAINER TIDAK MEMOTONG GARIS */
.salary-table .date-columns {
  overflow: visible !important;
  position: relative;
  z-index: 10;
  /* Override box-shadow dari date-column individual */
}

/* FORCE RESET: Hilangkan SEMUA border pada date-column dengan prioritas tertinggi */
.salary-table .date-column.border.border-black,
.salary-table thead .date-column.border.border-black,
.salary-table tbody .date-column.border.border-black,
.salary-table tfoot .date-column.border.border-black,
.salary-table .date-column.border,
.salary-table .date-column {
  border: 0 !important;
  border-width: 0 !important;
  border-style: none !important;
  border-color: transparent !important;
  border-left: 0 !important;
  border-right: 0 !important;
  border-top: 0 !important;
  border-bottom: 0 !important;
  border-left-width: 0 !important;
  border-right-width: 0 !important;
  border-top-width: 0 !important;
  border-bottom-width: 0 !important;
  box-shadow: none !important;
  outline: 0 !important;
}

.salary-table td.date-grid {
  border-left: 0 !important;
  border-right: 0 !important;
  border-top: 0 !important;
  border-bottom: 0 !important;
  padding: 0;
  overflow: visible !important;
  position: relative;
  box-shadow: none !important;
}

/* 4. SAMAKAN GARIS HORIZONTAL: gunakan border-collapse agar tidak dobel */
.salary-table {
  border-collapse: collapse !important;
  border-spacing: 0 !important;
}

/* Semua sel default 1px tipis (seperti KODE/ITEM) */
.salary-table th,
.salary-table td {
  border: 1px solid rgba(0,0,0,0.6) !important;
  position: relative;
}

/* KHUSUS: Hilangkan SEMUA border pada kolom tanggal agar garis vertikal menyatu */
.salary-table tbody td.date-grid,
.salary-table thead td.date-grid,
.salary-table tfoot td.date-grid {
  border: 0 !important;
  border-top: 0 !important;
  border-bottom: 0 !important;
  border-left: 0 !important;
  border-right: 0 !important;
  background: transparent !important;
}

/* Hilangkan border horizontal pada SEMUA baris yang mengandung kolom tanggal */
.salary-table tbody tr,
.salary-table thead tr,
.salary-table tfoot tr {
  background: transparent !important;
}

/* Khusus untuk sel yang berisi date-grid: hilangkan border yang memotong garis vertikal */
.salary-table tr td.date-grid,
.salary-table tr th.date-grid {
  border-top: 0 !important;
  border-bottom: 0 !important;
}

/* Pastikan overflow visible pada semua container */
.salary-table,
.salary-table tbody,
.salary-table thead,
.salary-table tfoot {
  overflow: visible !important;
}

/* Pastikan garis horizontal tabel tidak memotong garis vertikal */
.salary-table tr {
  position: relative;
  z-index: 1;
}

.salary-table .date-column {
  position: relative;
  z-index: 10;
}

/* 5. INPUT TRANSPARAN DAN TIDAK MENGHALANGI GARIS */
.salary-table .date-column input {
  background: transparent !important;
  border: 0 !important;
  outline: 0 !important;
  box-shadow: none !important;
  position: relative;
  z-index: 10;
  pointer-events: auto;
}

/* Pastikan garis vertikal selalu di atas elemen lain */
.salary-table .date-columns::before {
  z-index: 15 !important;
}

/* 6. PERBAIKI SEMUA GARIS AGAR TIPIS DAN SERAGAM */
/* FORCE: Hilangkan semua border tebal dan ganti dengan tipis */
.salary-table .border-b-2,
.salary-table .border-b-black,
.salary-table th.border-b-2,
.salary-table td.border-b-2,
.salary-table th.border-b-2.border-b-black,
.salary-table td.border-b-2.border-b-black {
  border-bottom-width: 1px !important;
  border-bottom-style: solid !important;
  border-bottom-color: rgba(0,0,0,0.6) !important;
}

/* Reset semua border pada tabel agar seragam */
.salary-table th,
.salary-table td {
  border-width: 1px !important;
  border-color: rgba(0,0,0,0.6) !important;
}

/* Khusus untuk header tanggal: hilangkan border bawah */
.salary-table thead .date-column.border-b-2,
.salary-table thead .date-column.border-b-black,
.salary-table thead .date-column {
  border-bottom: 0 !important;
}

/* Garis horizontal header yang tipis seperti KODE dan ITEM */
.salary-table thead th,
.salary-table thead td:not(.date-grid) {
  border-bottom: 1px solid rgba(0,0,0,0.6) !important;
}

/* Khusus kolom tanggal di header: tidak ada border bawah */
.salary-table thead td.date-grid {
  border-bottom: 0 !important;
}

/* HINDARI DOUBLE BORDER antar baris header */
.salary-table thead tr:not(:first-child) th,
.salary-table thead tr:not(:first-child) td {
  border-top: 0 !important;
}

.salary-table thead tr:first-child th,
.salary-table thead tr:first-child td {
  border-top: 1px solid rgba(0,0,0,0.6) !important;
}

.salary-table thead tr:not(:last-child) th,
.salary-table thead tr:not(:last-child) td {
  border-bottom: 1px solid rgba(0,0,0,0.6) !important;
}

.salary-table thead tr:last-child th,
.salary-table thead tr:last-child td {
  border-bottom: 1px solid rgba(0,0,0,0.6) !important;
}

/* Override inline style yang mungkin ada */
.salary-table th[style*="border-bottom: 2px"],
.salary-table td[style*="border-bottom: 2px"] {
  border-bottom: 1px solid rgba(0,0,0,0.6) !important;
}

/* 7. KONTROL TINGGI BARIS BODY AGAR TIDAK TERLALU PANJANG */
.salary-table {
  --row-h: 10px; /* ubah angka ini jika ingin lebih tinggi/rendah */
}

/* Terapkan tinggi baris ke semua sel tbody */
.salary-table tbody td,
.salary-table tbody th {
  height: var(--row-h) !important;
  line-height: var(--row-h) !important;
  padding-top: 0 !important;
  padding-bottom: 0 !important;
}

/* Khusus kolom tanggal */
.salary-table .date-column { 
  height: var(--row-h) !important; 
}

.salary-table .date-column input {
  height: var(--row-h) !important;
  line-height: var(--row-h) !important;
  padding: 0 !important;
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

// Event listener untuk input harga
document.addEventListener('DOMContentLoaded', function() {
    // Add event listeners untuk semua input harga
    for (let i = 1; i <= 20; i++) {
        const priceInput = document.getElementById(`price_${i}`);
        if (priceInput) {
            priceInput.addEventListener('input', function() {
                calculateRow(i);
            });
        }
    }
});

// Fungsi untuk menyimpan data (opsional)
function saveData() {
    const data = [];
    
    for (let row = 1; row <= 20; row++) {
        const rowData = {
            name: document.querySelector(`#row_${row} input[placeholder="Nama Karyawan"]`)?.value || '',
            description: document.querySelector(`#row_${row} input[placeholder="Deskripsi Pekerjaan"]`)?.value || '',
            days: {},
            total: document.getElementById(`total_${row}`)?.textContent || '0',
            price: document.getElementById(`price_${row}`)?.value || '10120',
            grandTotal: document.getElementById(`grand_total_${row}`)?.textContent || '0'
        };
        
        for (let day = 1; day <= 31; day++) {
            const input = document.getElementById(`day_${row}_${day}`);
            if (input && input.value) {
                rowData.days[day] = input.value;
            }
        }
        
        if (rowData.name || Object.keys(rowData.days).length > 0) {
            data.push(rowData);
        }
    }
    
    console.log('Data yang akan disimpan:', data);
    // Di sini bisa ditambahkan AJAX call untuk menyimpan ke database
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
            <div class="flex gap-3">
                <button onclick="openModal('tambahModal')" 
                        class="btn-modern group inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white font-medium rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105">
                    <div class="w-5 h-5 rounded-full bg-white/20 flex items-center justify-center group-hover:rotate-180 transition-transform duration-300">
                        <i class="fa-solid fa-plus text-xs"></i>
                    </div>
                    <span class="text-sm font-semibold">Tambah Gaji</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Tabel Gaji Karyawan -->
    <div class="bg-white dark:bg-slate-900 shadow-lg overflow-hidden border-2 border-black dark:border-slate-400">
        <!-- Header Perusahaan -->
        <div class="border-b-2 border-black dark:border-slate-400 px-4 py-2">
            <div class="flex justify-between items-start">
                <div class="text-left text-xs text-black dark:text-white">
                    <div class="border border-black dark:border-slate-400 px-2 py-1 mb-1">
                        <div class="text-red-500 font-bold">PERATURAN</div>
                        <div class="text-red-700 font-bold">DILARANG BELAH PAPAN TANPA IJIN</div>
                        <div class="text-red-700 font-bold">DILARANG BELAH BALOK TANPA IJIN</div>
                    </div>
                </div>
                <div class="flex-1 text-center">
                    <h2 class="text-4xl font-bold text-black dark:text-white">PT. CAM JAYA ABADI</h2>
                </div>
                <div class="text-right text-xs text-black dark:text-white">
                    <div class="border border-black dark:border-slate-400 px-2 py-1 mb-1 text-center">
                        <div class="font-bold">BIAYA / UPAH KERJA</div>
                    </div>
                    <div class="border border-black dark:border-slate-400 px-2 py-1 text-center">
                        <div class="font-bold">{{ now()->format('d-M-y') }}</div>
                    </div>
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
                min-width: 1400px !important;
                max-width: none !important;
                border-collapse: collapse !important;
                zoom: 1 !important;
                transform: scale(1) !important;
                transform-origin: top left !important;
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
        </style>

        <!-- Tabel Gaji -->
        <div class="salary-table-container" id="salaryTableContainer" style="position: relative;">
            <!-- Overlay garis vertikal untuk area TANGGAL -->
            <div id="dateGridOverlay" aria-hidden="true" style="position:absolute; top:0; left:0; height:0; width:0; pointer-events:none; z-index:50;
                 background-image: repeating-linear-gradient(to right, rgba(0,0,0,0.8) 0px, rgba(0,0,0,0.8) 1px, transparent 1px, transparent 35px);
                 background-repeat:repeat; background-size:35px 100%; opacity:1;"></div>
            <table class="salary-table border-collapse w-full">
                <thead>
                    <tr>
                        <th class="border border-black dark:border-slate-400 px-1 py-1 text-center font-bold bg-gray-100 dark:bg-slate-700 text-black dark:text-white" style="width: 8%;">ITEM</th>
                        <th class="border border-black dark:border-slate-400 px-1 py-1 text-center font-bold bg-gray-100 dark:bg-slate-700 text-black dark:text-white" style="width: 10%;"></th>
                        
                        <!-- Tanggal yang bisa scroll horizontal -->
                        <th class="border border-black dark:border-slate-400 px-1 py-1 text-center font-bold bg-gray-100 dark:bg-slate-700 text-black dark:text-white" style="width: 35%;">TANGGAL</th>
                        
                        <th class="border border-black dark:border-slate-400 px-1 py-1 text-center font-bold bg-gray-100 dark:bg-slate-700 text-black dark:text-white" style="width: 6%;">JUMLAH</th>
                        <th class="border border-black dark:border-slate-400 px-1 py-1 text-center font-bold bg-gray-100 dark:bg-slate-700 text-black dark:text-white" style="width: 3%;"></th>
                        <th class="border border-black dark:border-slate-400 px-1 py-1 text-center font-bold bg-gray-100 dark:bg-slate-700 text-black dark:text-white" style="width: 6%;"></th>
                        <th class="border border-black dark:border-slate-400 px-1 py-1 text-center font-bold bg-gray-100 dark:bg-slate-700 text-black dark:text-white" style="width: 3%;"></th>
                        <th class="border border-black dark:border-slate-400 px-1 py-1 text-center font-bold bg-gray-100 dark:bg-slate-700 text-black dark:text-white" style="width: 8%;">BIAYA PRODUKSI</th>
                        <th class="border border-black dark:border-slate-400 px-1 py-1 text-center font-bold bg-gray-100 dark:bg-slate-700 text-black dark:text-white" style="width: 6%;">TOTAL</th>
                        <th class="px-1 py-1 text-center font-bold bg-gray-100 dark:bg-slate-700 text-black dark:text-white" style="width: 17%; border-top: 1px solid rgba(0,0,0,0.6); border-bottom: 1px solid rgba(0,0,0,0.6); border-right: 1px solid rgba(0,0,0,0.6);">KETERANGAN</th>
                    </tr>
                    <tr class="bg-gray-100 dark:bg-slate-700">
                        <th class="border border-black dark:border-slate-400 px-1 py-1 text-center font-bold bg-gray-100 dark:bg-slate-700 text-black dark:text-white" style="width: 8%;">KODE</th>
                        <th class="border border-black dark:border-slate-400 px-1 py-1 text-center font-bold bg-gray-100 dark:bg-slate-700 text-black dark:text-white" style="width: 10%;">DESKRIPSI</th>
                        
                        <!-- Container scroll horizontal untuk tanggal -->
                        <td class="border border-black dark:border-slate-400 px-0 py-0 date-grid" style="width: 35%;">
                            <div class="date-scroll-container" style="width: 100%; height: 100%;">
                                <div class="date-columns">
                                    @for($i = 1; $i <= 31; $i++)
                                        <div class="date-column border border-black dark:border-slate-400 px-1 py-1 text-center font-bold text-black dark:text-white bg-gray-100 dark:bg-slate-700" style="font-size: 8px; height: 100%; display: flex; align-items: center; justify-content: center;">{{ $i }}</div>
                                    @endfor
                                </div>
                            </div>
                        </td>
                        <th class="border border-black dark:border-slate-400 px-1 py-1 text-center font-bold text-black dark:text-white" style="width: 6%;">JUMLAH</th>
                        <!-- Kolom kosong kiri HSL. PROD -->
                        <th class="border border-black dark:border-slate-400 px-1 py-1 text-center font-bold bg-gray-100 dark:bg-slate-700 text-black dark:text-white" style="width: 3%;"></th>
                        <th class="border border-black dark:border-slate-400 px-1 py-1 text-center font-bold text-black dark:text-white" style="width: 6%;">HSL. PROD</th>
                        <!-- Kolom kosong kanan HSL. PROD -->
                        <th class="border border-black dark:border-slate-400 px-1 py-1 text-center font-bold bg-gray-100 dark:bg-slate-700 text-black dark:text-white" style="width: 3%;"></th>
                        <!-- HARGA di bawah BIAYA PRODUKSI -->
                        <th class="border border-black dark:border-slate-400 px-1 py-1 text-center font-bold text-black dark:text-white" style="width: 8%;">HARGA</th>
                        <!-- TOTAL -->
                        <th class="border border-black dark:border-slate-400 px-1 py-1 text-center font-bold text-black dark:text-white" style="width: 6%;">TOTAL</th>
                        <!-- KETERANGAN (tanpa garis kiri) -->
                        <th class="px-1 py-1 text-center font-bold bg-gray-100 dark:bg-slate-700 text-black dark:text-white" style="width: 17%; border-top: 1px solid rgba(0,0,0,0.6); border-bottom: 1px solid rgba(0,0,0,0.6); border-right: 1px solid rgba(0,0,0,0.6);"></th>
                    </tr>
                    <tr class="bg-gray-100 dark:bg-slate-700">
                        <th class="border border-black dark:border-slate-400 px-1 py-1 text-center font-bold bg-gray-100 dark:bg-slate-700 text-black dark:text-white" style="width: 8%;"></th>
                        <th class="border border-black dark:border-slate-400 px-1 py-1 text-center font-bold bg-gray-100 dark:bg-slate-700 text-black dark:text-white" style="width: 10%;"></th>
                        
                        <!-- Baris kosong untuk tanggal -->
                        <td class="border border-black dark:border-slate-400 px-0 py-0" style="width: 35%;">
                            <div class="date-scroll-container" style="width: 100%; height: 100%;">
                                <div class="date-columns">
                                    @for($i = 1; $i <= 31; $i++)
                                        <div class="date-column border border-black dark:border-slate-400 px-1 py-1 text-center font-bold text-black dark:text-white bg-gray-100 dark:bg-slate-700" style="font-size: 8px; height: 100%;"></div>
                                    @endfor
                                </div>
                            </div>
                        </td>
                        <th class="border border-black dark:border-slate-400 px-1 py-1 text-center font-bold text-black dark:text-white" style="width: 6%;"></th>
                        <th class="border border-black dark:border-slate-400 px-1 py-1 text-center font-bold bg-gray-100 dark:bg-slate-700 text-black dark:text-white" style="width: 3%;"></th>
                        <th class="border border-black dark:border-slate-400 px-1 py-1 text-center font-bold text-black dark:text-white" style="width: 6%;"></th>
                        <th class="border border-black dark:border-slate-400 px-1 py-1 text-center font-bold bg-gray-100 dark:bg-slate-700 text-black dark:text-white" style="width: 3%;"></th>
                        <th class="border border-black dark:border-slate-400 px-1 py-1 text-center font-bold text-black dark:text-white" style="width: 8%;"></th>
                        <th class="border border-black dark:border-slate-400 px-1 py-1 text-center font-bold text-black dark:text-white" style="width: 6%;"></th>
                        <th class="px-1 py-1 text-center font-bold bg-gray-100 dark:bg-slate-700 text-black dark:text-white" style="width: 17%; border-top: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black;"></th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-slate-900">
                    @forelse($salaries ?? [] as $index => $salary)
                    <tr class="hover:bg-gray-50 dark:hover:bg-slate-800">
                        <td class="border border-black dark:border-slate-400 px-1 py-0 text-xs font-medium text-black dark:text-white bg-white dark:bg-slate-900" style="height: 12px; line-height: 12px; width: 8%;">
                            {{ strtoupper($salary->employee->nama_karyawan) }}
                        </td>
                        <td class="border border-black dark:border-slate-400 px-1 py-0 text-xs text-black dark:text-white bg-white dark:bg-slate-900" style="height: 12px; line-height: 11px; width: 10%;">
                            <div style="font-size: 7px;">KUBOTA 110 X 110 EKSPOR</div>
                            <div style="font-size: 7px;">{{ strtoupper($salary->employee->posisi ?? 'KARYAWAN') }}</div>
                        </td>
                        
                        <!-- Container scroll horizontal untuk input tanggal -->
                        <td class="border border-black dark:border-slate-400 px-0 py-0 date-grid" style="width: 35%; height: 12px;">
                            <div class="date-scroll-container" style="width: 100%; height: 100%;">
                                <div class="date-columns">
                                    @for($day = 1; $day <= 31; $day++)
                                        <div class="date-column border border-black dark:border-slate-400" style="height: 12px; padding: 0;">
                                            <input type="text" 
                                                   class="w-full h-full text-center text-xs border-0 bg-transparent dark:bg-slate-900 text-black dark:text-white focus:bg-yellow-100 dark:focus:bg-yellow-200 dark:focus:text-black focus:outline-none" 
                                                   style="font-size: 8px; padding: 0px; line-height: 12px; resize: none;"
                                                   placeholder=""
                                                   onchange="calculateRow({{ $index }})"
                                                   id="day_{{ $index }}_{{ $day }}">
                                        </div>
                                    @endfor
                                </div>
                            </div>
                        </td>
                        
                        <td class="border border-black dark:border-slate-400 px-1 py-0 text-center text-xs text-black dark:text-white" style="height: 12px; line-height: 12px; width: 6%;">
                            <span id="total_{{ $index }}">{{ rand(450, 500) }}</span> pcs
                        </td>
                        <!-- Kolom kosong kiri HSL. PROD -->
                        <td class="border border-black dark:border-slate-400 px-1 py-0 text-xs text-black dark:text-white bg-white dark:bg-slate-900" style="height: 12px; line-height: 12px; width: 3%;"></td>
                        <td class="border border-black dark:border-slate-400 px-1 py-0 text-right text-xs text-black dark:text-white" style="height: 12px; line-height: 12px; width: 6%;">
                            {{ number_format(10120, 0, ',', '.') }}
                        </td>
                        <!-- Kolom kosong kanan HSL. PROD -->
                        <td class="border border-black dark:border-slate-400 px-1 py-0 text-xs text-black dark:text-white bg-white dark:bg-slate-900" style="height: 12px; line-height: 12px; width: 3%;"></td>
                        <td class="border border-black dark:border-slate-400 px-1 py-0 text-right text-xs text-black dark:text-white" style="height: 12px; line-height: 12px; width: 8%;">
                            {{ number_format(rand(100000, 200000), 0, ',', '.') }}
                        </td>
                        <td class="border border-black dark:border-slate-400 px-1 py-0 text-right text-xs font-bold text-black dark:text-white" style="height: 12px; line-height: 12px; width: 6%;">
                            <span id="grand_total_{{ $index }}">{{ number_format($salary->total_gaji, 0, ',', '.') }}</span>
                        </td>
                        <td class="px-1 py-0 text-left text-xs bg-white dark:bg-slate-900" style="height: 12px; line-height: 12px; width: 17%; padding-left: 4px; border-top: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black;">
                            @if($salary->status_pembayaran === 'dibayar')
                                <span class="text-green-600 dark:text-green-400 font-bold" style="font-size: 7px;">LUNAS</span>
                            @else
                                <span class="text-red-600 dark:text-red-400 font-bold" style="font-size: 7px;">BELUM LUNAS</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <!-- Baris kosong untuk input manual -->
                    @for($i = 1; $i <= 28; $i++)
                    <tr class="hover:bg-gray-50 dark:hover:bg-slate-800">
                        <td class="border border-black dark:border-slate-400 px-1 py-0" style="height: 12px; width: 8%;">
                            <input type="text" class="w-full h-full text-xs border-0 bg-transparent dark:bg-slate-900 text-black dark:text-white focus:bg-yellow-100 dark:focus:bg-yellow-200 dark:focus:text-black focus:outline-none" placeholder="Kode Karyawan" style="line-height: 12px; padding: 0;">
                        </td>
                        <td class="border border-black dark:border-slate-400 px-1 py-0" style="height: 12px; width: 10%;">
                            <input type="text" class="w-full h-full text-xs border-0 bg-transparent dark:bg-slate-900 text-black dark:text-white focus:bg-yellow-100 dark:focus:bg-yellow-200 dark:focus:text-black focus:outline-none" placeholder="Deskripsi Pekerjaan" style="line-height: 12px; padding: 0;">
                        </td>
                        
                        <!-- Container scroll horizontal untuk input tanggal -->
                        <td class="border border-black dark:border-slate-400 px-0 py-0" style="width: 35%; height: 12px;">
                            <div class="date-scroll-container" style="width: 100%; height: 100%;">
                                <div class="date-columns">
                                    @for($day = 1; $day <= 31; $day++)
                                        <div class="date-column border border-black dark:border-slate-400" style="height: 12px; padding: 0;">
                                            <input type="text" 
                                                   class="w-full h-full text-center text-xs border-0 bg-transparent dark:bg-slate-900 text-black dark:text-white focus:bg-yellow-100 dark:focus:bg-yellow-200 dark:focus:text-black focus:outline-none" 
                                                   style="font-size: 8px; padding: 0px; line-height: 12px; resize: none;"
                                                   placeholder=""
                                                   onchange="calculateRow({{ $i }})"
                                                   id="day_{{ $i }}_{{ $day }}">
                                        </div>
                                    @endfor
                                </div>
                            </div>
                        </td>
                        
                        <td class="border border-black dark:border-slate-400 px-1 py-0 text-black dark:text-white" style="height: 12px; line-height: 12px; width: 6%;">
                            <span id="total_{{ $i }}">0</span> pcs
                        </td>
                        <!-- Kolom kosong kiri HSL. PROD -->
                        <td class="border border-black dark:border-slate-400 px-1 py-0 bg-white dark:bg-slate-900" style="height: 12px; width: 3%;"></td>
                        <td class="border border-black dark:border-slate-400 px-1 py-0" style="height: 12px; width: 6%;">
                            <input type="text" class="w-full h-full text-xs border-0 bg-transparent dark:bg-slate-900 text-black dark:text-white focus:bg-yellow-100 dark:focus:bg-yellow-200 dark:focus:text-black focus:outline-none text-right" placeholder="10.120" id="hsl_prod_{{ $i }}" style="line-height: 12px; padding: 0;">
                        </td>
                        <!-- Kolom kosong kanan HSL. PROD -->
                        <td class="border border-black dark:border-slate-400 px-1 py-0 bg-white dark:bg-slate-900" style="height: 12px; width: 3%;"></td>
                        <td class="border border-black dark:border-slate-400 px-1 py-0" style="height: 12px; width: 8%;">
                            <input type="text" class="w-full h-full text-xs border-0 bg-transparent dark:bg-slate-900 text-black dark:text-white focus:bg-yellow-100 dark:focus:bg-yellow-200 dark:focus:text-black focus:outline-none text-right" placeholder="0" id="biaya_prod_{{ $i }}" style="line-height: 12px; padding: 0;">
                        </td>
                        <td class="border border-black dark:border-slate-400 px-1 py-0 text-right text-xs font-bold text-black dark:text-white" style="height: 12px; line-height: 12px; width: 6%;">
                            <span id="grand_total_{{ $i }}">0</span>
                        </td>
                        <td class="px-1 py-0" style="height: 12px; width: 17%; border-top: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black;">
                            <input type="text" class="w-full h-full text-xs border-0 bg-transparent dark:bg-slate-900 text-black dark:text-white focus:bg-yellow-100 dark:focus:bg-yellow-200 dark:focus:text-black focus:outline-none" style="line-height: 12px; padding: 0; padding-left: 4px;">
                        </td>
                    </tr>
                    @endfor
                    @endforelse
                </tbody>
                <!-- Footer Total -->
                <tfoot>
                    <tr class="bg-gray-100 dark:bg-slate-700 font-bold">
                        <td colspan="2" class="border border-black dark:border-slate-400 px-1 py-1 text-xs text-black dark:text-white">Hasil produksi pallet dan biaya borongan</td>
                        <!-- Container scroll horizontal untuk total per hari -->
                        <td class="border border-black dark:border-slate-400 px-0 py-0" style="width: 35%;">
                            <div class="date-scroll-container" style="width: 100%; height: 100%;">
                                <div class="date-columns">
                                    @for($day = 1; $day <= 31; $day++)
                                        <div class="date-column" id="day_total_{{ $day }}" style="font-size: 8px; height: 100%; display: flex; align-items: center; justify-content: center; text-align: center; padding: 2px;">{{ $day <= 20 ? rand(20, 50) : 0 }}</div>
                                    @endfor
                                </div>
                            </div>
                        </td>
                        <td class="border border-black dark:border-slate-400 px-1 py-1 text-center text-xs text-black dark:text-white" id="grand_pieces">486 pcs</td>
                        <td class="border border-black dark:border-slate-400 px-1 py-1 text-xs text-black dark:text-white">TOTAL</td>
                        <td class="border border-black dark:border-slate-400 px-1 py-1 text-xs text-black dark:text-white">-</td>
                        <td class="border border-black dark:border-slate-400 px-1 py-1 text-xs text-black dark:text-white">-</td>
                        <td class="border border-black dark:border-slate-400 px-1 py-1 text-xs text-black dark:text-white">-</td>
                        <td class="border border-black dark:border-slate-400 px-1 py-1 text-right text-xs font-bold text-black dark:text-white" id="final_total">4.937.120</td>
                        <td class="px-1 py-1 text-xs text-black dark:text-white" style="border-top: 1px solid black; border-bottom: 1px solid black; border-right: 1px solid black;">-</td>
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
        <!-- Form Content -->
        <div class="p-8 bg-gray-50 dark:bg-slate-800">
        <form method="POST" action="{{ route('salary.store') }}" class="space-y-6" onsubmit="return prepareFormSubmission(this)">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Karyawan</label>
                    <select name="employee_id" id="employee_select" required onchange="autoFillEmployeeData()" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 dark:focus:ring-green-400 bg-white dark:bg-slate-700 text-gray-900 dark:text-gray-100">
                        <option value="">Pilih Karyawan</option>
                        @foreach($employees ?? [] as $employee)
                        <option value="{{ $employee->id }}" data-gaji-pokok="{{ $employee->gaji_pokok ?? 0 }}" data-nama="{{ $employee->nama_karyawan }}" data-posisi="{{ $employee->posisi }}">
                            {{ $employee->nama_karyawan }} - {{ $employee->posisi }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Bulan</label>
                    <select name="bulan" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 dark:focus:ring-green-400 bg-white dark:bg-slate-700 text-gray-900 dark:text-gray-100">
                        @for($i = 1; $i <= 12; $i++)
                        <option value="{{ $i }}" {{ $i == date('n') ? 'selected' : '' }}>
                            {{ DateTime::createFromFormat('!m', $i)->format('F') }}
                        </option>
                        @endfor
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tahun</label>
                    <select name="tahun" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 dark:focus:ring-green-400 bg-white dark:bg-slate-700 text-gray-900 dark:text-gray-100">
                        @for($i = 2020; $i <= 2030; $i++)
                        <option value="{{ $i }}" {{ $i == date('Y') ? 'selected' : '' }}>{{ $i }}</option>
                        @endfor
                    </select>
                </div>
                <!-- Jenis Gaji -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Jenis Gaji</label>
                    <div class="flex rounded-lg border border-gray-300 dark:border-gray-600 overflow-hidden">
                        <label class="flex-1 px-4 py-2 text-center cursor-pointer bg-white dark:bg-slate-700 text-gray-700 dark:text-gray-200 border-r border-gray-300 dark:border-gray-600 transition-colors hover:bg-gray-50 dark:hover:bg-slate-600" id="label_borongan">
                            <input type="radio" name="jenis_gaji" value="borongan" class="hidden" id="jg_borongan" checked onchange="toggleJenisGaji()">
                            <span class="font-medium">🏭 Borongan</span>
                        </label>
                        <label class="flex-1 px-4 py-2 text-center cursor-pointer bg-white dark:bg-slate-700 text-gray-700 dark:text-gray-200 transition-colors hover:bg-gray-50 dark:hover:bg-slate-600" id="label_harian">
                            <input type="radio" name="jenis_gaji" value="harian" class="hidden" id="jg_harian" onchange="toggleJenisGaji()">
                            <span class="font-medium">📅 Harian</span>
                        </label>
                    </div>
                </div>
                <!-- Input Borongan -->
                <div id="section_borongan" class="grid grid-cols-1 md:grid-cols-2 gap-4 md:col-span-2">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Jumlah Unit</label>
                        <input type="number" min="0" name="jumlah_unit" id="jumlah_unit" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 dark:focus:ring-green-400 bg-white dark:bg-slate-700 text-gray-900 dark:text-gray-100" placeholder="0" oninput="hitungGajiPokok()">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tarif per Unit (Rp)</label>
                        <input type="number" min="0" name="tarif_per_unit" id="tarif_per_unit" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 dark:focus:ring-green-400 bg-white dark:bg-slate-700 text-gray-900 dark:text-gray-100" placeholder="0" oninput="hitungGajiPokok()">
                    </div>
                </div>
                <!-- Input Harian -->
                <div id="section_harian" class="grid grid-cols-1 md:grid-cols-2 gap-4 md:col-span-2 hidden">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Jumlah Hari</label>
                        <input type="number" min="0" name="jumlah_hari" id="jumlah_hari" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 dark:focus:ring-green-400 bg-white dark:bg-slate-700 text-gray-900 dark:text-gray-100" placeholder="0" oninput="hitungGajiPokok()">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tarif Harian (Rp)</label>
                        <input type="number" min="0" name="tarif_harian" id="tarif_harian" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 dark:focus:ring-green-400 bg-white dark:bg-slate-700 text-gray-900 dark:text-gray-100" placeholder="0" oninput="hitungGajiPokok()">
                    </div>
                </div>
                <!-- Gaji Pokok (otomatis) -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Gaji Pokok (otomatis)</label>
                    <div class="relative">
                        <input type="text" id="gaji_pokok_display" readonly class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-gray-100 dark:bg-slate-600 text-gray-700 dark:text-gray-200 font-bold text-lg">
                        <input type="hidden" name="gaji_pokok" id="gaji_pokok_value">
                        <div class="absolute right-3 top-1/2 transform -translate-y-1/2 text-green-600 dark:text-green-400">
                            <i class="fa-solid fa-calculator"></i>
                        </div>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tunjangan</label>
                    <input type="text" id="tunjangan_display" class="currency-input w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 dark:focus:ring-green-400 bg-white dark:bg-slate-700 text-gray-900 dark:text-gray-100" oninput="formatCurrencyInput(this)" onblur="formatCurrencyInput(this)">
                    <input type="hidden" name="tunjangan" id="tunjangan_value">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Bonus</label>
                    <input type="text" id="bonus_display" class="currency-input w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 dark:focus:ring-green-400 bg-white dark:bg-slate-700 text-gray-900 dark:text-gray-100" oninput="formatCurrencyInput(this)" onblur="formatCurrencyInput(this)">
                    <input type="hidden" name="bonus" id="bonus_value">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Lembur</label>
                    <input type="text" id="lembur_display" class="currency-input w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 dark:focus:ring-green-400 bg-white dark:bg-slate-700 text-gray-900 dark:text-gray-100" oninput="formatCurrencyInput(this)" onblur="formatCurrencyInput(this)">
                    <input type="hidden" name="lembur" id="lembur_value">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Potongan Pajak</label>
                    <input type="text" id="potongan_pajak_display" class="currency-input w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 dark:focus:ring-green-400 bg-white dark:bg-slate-700 text-gray-900 dark:text-gray-100" oninput="formatCurrencyInput(this)" onblur="formatCurrencyInput(this)">
                    <input type="hidden" name="potongan_pajak" id="potongan_pajak_value">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Potongan BPJS</label>
                    <input type="text" id="potongan_bpjs_display" class="currency-input w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 dark:focus:ring-green-400 bg-white dark:bg-slate-700 text-gray-900 dark:text-gray-100" oninput="formatCurrencyInput(this)" onblur="formatCurrencyInput(this)">
                    <input type="hidden" name="potongan_bpjs" id="potongan_bpjs_value">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Potongan Lain</label>
                    <input type="text" id="potongan_lain_display" class="currency-input w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 dark:focus:ring-green-400 bg-white dark:bg-slate-700 text-gray-900 dark:text-gray-100" oninput="formatCurrencyInput(this)" onblur="formatCurrencyInput(this)">
                    <input type="hidden" name="potongan_lain" id="potongan_lain_value">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status Pembayaran</label>
                    <select name="status_pembayaran" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 dark:focus:ring-green-400 bg-white dark:bg-slate-700 text-gray-900 dark:text-gray-100">
                        <option value="belum_dibayar">Belum Dibayar</option>
                        <option value="dibayar">Dibayar</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tanggal Bayar</label>
                    <input type="date" name="tanggal_bayar" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 dark:focus:ring-green-400 bg-white dark:bg-slate-700 text-gray-900 dark:text-gray-100">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Keterangan</label>
                <textarea name="keterangan" rows="3" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 dark:focus:ring-green-400 bg-white dark:bg-slate-700 text-gray-900 dark:text-gray-100"></textarea>
            </div>
            <!-- Action Buttons -->
            <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200 dark:border-slate-600">
                <button type="button" onclick="closeModal('tambahModal')" 
                        class="px-6 py-3 bg-gray-100 dark:bg-slate-700 text-gray-700 dark:text-gray-300 rounded-xl hover:bg-gray-200 dark:hover:bg-slate-600 transition-all duration-200 font-medium">
                    <i class="fa-solid fa-times mr-2"></i>Batal
                </button>
                <button type="submit" 
                        class="px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white rounded-xl transition-all duration-200 font-medium shadow-lg hover:shadow-xl">
                    <i class="fa-solid fa-save mr-2"></i>Simpan Gaji
                </button>
            </div>
        </form>
        </div>
    </div>
</div>

<!-- Modal Edit Gaji -->
<div id="editModal" class="fixed inset-0 bg-black/50 dark:bg-black/70 hidden flex items-center justify-center z-50 p-4">
    <div class="bg-white dark:bg-slate-800 rounded-lg shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-y-auto">
        <div class="sticky top-0 bg-white dark:bg-slate-800 p-6 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
            <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">Edit Data Gaji</h3>
            <button onclick="closeModal('editModal')" class="text-gray-400 hover:text-gray-600 dark:text-gray-300 dark:hover:text-gray-100">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <div class="p-6">
        <form method="POST" id="editForm" class="space-y-4">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Karyawan</label>
                    <select name="employee_id" id="edit_employee_select" required disabled class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 bg-gray-100 dark:bg-slate-600 text-gray-600 dark:text-gray-300 cursor-not-allowed">
                        <option value="">Pilih Karyawan</option>
                        @foreach($employees ?? [] as $employee)
                        <option value="{{ $employee->id }}" data-gaji-pokok="{{ $employee->gaji_pokok ?? 0 }}" data-nama="{{ $employee->nama_karyawan }}" data-posisi="{{ $employee->posisi }}">
                            {{ $employee->nama_karyawan }} - {{ $employee->posisi }}
                        </option>
                        @endforeach
                    </select>
                    <input type="hidden" name="employee_id" id="edit_employee_id_hidden">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Bulan</label>
                    <select name="bulan" id="edit_bulan" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 bg-white dark:bg-slate-700 text-gray-900 dark:text-gray-100">
                        @for($i = 1; $i <= 12; $i++)
                        <option value="{{ $i }}">
                            {{ DateTime::createFromFormat('!m', $i)->format('F') }}
                        </option>
                        @endfor
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tahun</label>
                    <select name="tahun" id="edit_tahun" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 bg-white dark:bg-slate-700 text-gray-900 dark:text-gray-100">
                        @for($i = 2020; $i <= 2030; $i++)
                        <option value="{{ $i }}">{{ $i }}</option>
                        @endfor
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Gaji Pokok</label>
                    <!-- readonly (bukan disabled) agar ikut terkirim saat submit -->
                    <input type="text" name="gaji_pokok" id="edit_gaji_pokok" required min="0" readonly class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 bg-gray-100 dark:bg-slate-600 text-gray-700 dark:text-gray-200">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tunjangan</label>
                    <input type="text" name="tunjangan" id="edit_tunjangan" min="0" class="currency-input w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 bg-white dark:bg-slate-700 text-gray-900 dark:text-gray-100" oninput="formatCurrencyInput(this)" onblur="formatCurrencyInput(this)">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Bonus</label>
                    <input type="text" name="bonus" id="edit_bonus" min="0" class="currency-input w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 bg-white dark:bg-slate-700 text-gray-900 dark:text-gray-100" oninput="formatCurrencyInput(this)" onblur="formatCurrencyInput(this)">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Lembur</label>
                    <input type="text" name="lembur" id="edit_lembur" min="0" class="currency-input w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 bg-white dark:bg-slate-700 text-gray-900 dark:text-gray-100" oninput="formatCurrencyInput(this)" onblur="formatCurrencyInput(this)">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Potongan Pajak</label>
                    <input type="text" name="potongan_pajak" id="edit_potongan_pajak" min="0" class="currency-input w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 bg-white dark:bg-slate-700 text-gray-900 dark:text-gray-100" oninput="formatCurrencyInput(this)" onblur="formatCurrencyInput(this)">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Potongan BPJS</label>
                    <input type="text" name="potongan_bpjs" id="edit_potongan_bpjs" min="0" class="currency-input w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 bg-white dark:bg-slate-700 text-gray-900 dark:text-gray-100" oninput="formatCurrencyInput(this)" onblur="formatCurrencyInput(this)">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Potongan Lain</label>
                    <input type="text" name="potongan_lain" id="edit_potongan_lain" min="0" class="currency-input w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 bg-white dark:bg-slate-700 text-gray-900 dark:text-gray-100" oninput="formatCurrencyInput(this)" onblur="formatCurrencyInput(this)">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status Pembayaran</label>
                    <select name="status_pembayaran" id="edit_status_pembayaran" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 bg-white dark:bg-slate-700 text-gray-900 dark:text-gray-100">
                        <option value="belum_dibayar">Belum Dibayar</option>
                        <option value="dibayar">Dibayar</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tanggal Bayar</label>
                    <input type="date" name="tanggal_bayar" id="edit_tanggal_bayar" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 bg-white dark:bg-slate-700 text-gray-900 dark:text-gray-100">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Keterangan</label>
                <textarea name="keterangan" id="edit_keterangan" rows="3" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 bg-white dark:bg-slate-700 text-gray-900 dark:text-gray-100"></textarea>
            </div>
            <div class="flex justify-end space-x-3 pt-4">
                <button type="button" onclick="closeModal('editModal')" class="px-4 py-2 bg-gray-300 dark:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-md hover:bg-gray-400 dark:hover:bg-gray-500 transition-colors">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                    Update
                </button>
            </div>
        </form>
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
    console.log('[v0] Preparing form submission...');
    
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
    } catch (e) { console.warn('Duplicate check error:', e); }

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

// Tampilkan toast duplikasi dengan animasi modern
function showDuplicateToast() {
    const toast = document.getElementById('toast-duplicate');
    if (!toast) return;
    
    // Reset dan tampilkan
    toast.classList.remove('hidden');
    toast.classList.remove('translate-x-full');
    toast.classList.add('translate-x-0');
    
    // Auto hide setelah 4 detik
    setTimeout(() => {
        toast.classList.add('translate-x-full');
        setTimeout(() => toast.classList.add('hidden'), 300);
    }, 4000);
}

// Tutup toast manual
function closeToast() {
    const toast = document.getElementById('toast-duplicate');
    if (!toast) return;
    toast.classList.add('translate-x-full');
    setTimeout(() => toast.classList.add('hidden'), 300);
}

// Modal functions dengan animasi
function openDuplicateCenterModal() { 
    const modal = document.getElementById('duplicateCenterModal');
    modal.classList.remove('hidden');
    setTimeout(() => {
        modal.querySelector('div > div').classList.remove('scale-95');
        modal.querySelector('div > div').classList.add('scale-100');
    }, 10);
}

function closeDuplicateCenterModal() { 
    const modal = document.getElementById('duplicateCenterModal');
    modal.querySelector('div > div').classList.remove('scale-100');
    modal.querySelector('div > div').classList.add('scale-95');
    setTimeout(() => modal.classList.add('hidden'), 200);
}

// Cek duplikasi berdasarkan pilihan form saat ini (non-blokir)
function checkDuplicateSelection() {
    const empEl = document.getElementById('employee_select');
    const bulanEl = document.querySelector('#tambahModal [name="bulan"]');
    const tahunEl = document.querySelector('#tambahModal [name="tahun"]');
    if (!empEl || !bulanEl || !tahunEl) return;
    const employeeId = parseInt(empEl.value);
    const bulan = parseInt(bulanEl.value);
    const tahun = parseInt(tahunEl.value);
    if (isNaN(employeeId) || isNaN(bulan) || isNaN(tahun)) return;
    const exists = (existingSalariesData || []).some(s => parseInt(s.employee_id) === employeeId && parseInt(s.bulan) === bulan && parseInt(s.tahun) === tahun);
    if (exists) showDuplicateToast();
}

// Data salary untuk validasi payroll (ambil dari backend, array of {bulan, tahun})
const existingPayrolls = @json(($salaries ?? [])->map(fn($s) => ['bulan' => $s->bulan, 'tahun' => $s->tahun])->unique());

// Validasi generate payroll agar hanya untuk bulan/tahun yang belum ada
document.addEventListener('DOMContentLoaded', function() {
    const payrollForm = document.querySelector('#payrollModal form');
    if (payrollForm) {
        payrollForm.addEventListener('submit', function(e) {
            const bulan = parseInt(payrollForm.querySelector('[name="bulan"]').value);
            const tahun = parseInt(payrollForm.querySelector('[name="tahun"]').value);
            const exists = existingPayrolls.some(p => parseInt(p.bulan) === bulan && parseInt(p.tahun) === tahun);
            if (exists) {
                alert('Payroll untuk bulan dan tahun tersebut sudah ada!');
                e.preventDefault();
            }
        });
    }

    // Peringatan hanya saat submit (tidak pada perubahan input)
});
</script>

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
// Modal functions
function openModal(modalId) {
    const modal = document.getElementById(modalId);
    const modalContent = modal.querySelector('.bg-white, .dark\\:bg-slate-900');
    
    // Show modal
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    
    // Trigger animation
    setTimeout(() => {
        modal.classList.remove('opacity-0');
        modal.classList.add('opacity-100');
        modalContent.classList.remove('scale-95', 'translate-y-4');
        modalContent.classList.add('scale-100', 'translate-y-0');
    }, 10);
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    const modalContent = modal.querySelector('.bg-white, .dark\\:bg-slate-900');
    
    // Trigger exit animation
    modal.classList.remove('opacity-100');
    modal.classList.add('opacity-0');
    modalContent.classList.remove('scale-100', 'translate-y-0');
    modalContent.classList.add('scale-95', 'translate-y-4');
    
    // Hide modal after animation
    setTimeout(() => {
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
    }, 300);
}

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
    // Update filter tahun dengan tahun yang dipilih
    const filterTahun = document.getElementById('filterTahun');
    const yearButtonText = document.getElementById('yearButtonText');
    
    if (filterTahun) {
        filterTahun.value = year;
        filterKaryawanTable();
    }
    
    // Update text pada tombol untuk menampilkan tahun yang dipilih
    if (yearButtonText) {
        yearButtonText.textContent = 'Pilih Tahun (' + year + ')';
    }
    
    // Update highlighting pada modal
    document.querySelectorAll('.year-btn').forEach(btn => {
        if (btn.dataset.year == year) {
            btn.className = 'year-btn px-3 py-2 text-sm font-medium rounded-md border transition-colors bg-indigo-600 text-white border-indigo-600';
        } else {
            btn.className = 'year-btn px-3 py-2 text-sm font-medium rounded-md border transition-colors bg-white text-gray-700 border-gray-300 hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-200 dark:border-gray-600 dark:hover:bg-gray-600';
        }
    });
    
    closeYearModal();
}

// Close modal with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeYearModal();
    }
});

// Sync scroll untuk semua container tanggal
document.addEventListener('DOMContentLoaded', function() {
    const dateScrollContainers = document.querySelectorAll('.date-scroll-container');
    
    // Sinkronisasi scroll horizontal untuk semua container tanggal
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
