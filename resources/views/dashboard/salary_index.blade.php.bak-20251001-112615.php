@extends('layouts.app')

@section('title', 'Manajemen Gaji Karyawan')

@push('styles')
<style>

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
  margin-top: 0 !important;
  border-top: 0 !important; /* rely on table bottom border */
  width: 100% !important;
  min-width: 100% !important;
}

/* Force header and footer to use full available width */
.table-wrapper {
  display: block !important;
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
  .report-header { border-bottom: 2px solid #000 !important; width: 100% !important; }
  .table-wrapper > table { border-top: 2px solid #000 !important; border-bottom: 2px solid #000 !important; width: 100% !important; }
  .report-footer { border-top: 0 !important; width: 100% !important; }
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
// Fungsi perhitungan untuk tabel manual
function calculateManualRow(rowIndex) {
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
    updateManualDayTotals();
    
    // Update grand total keseluruhan
    updateManualGrandTotal();
}

function updateManualDayTotals() {
    for (let day = 1; day <= 31; day++) {
        let dayTotal = 0;
        
        // Hitung total per hari dari semua baris
        for (let row = 1; row <= 25; row++) {
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

function updateManualGrandTotal() {
    let grandTotal = 0;
    let totalPieces = 0;
    
    // Hitung total dari semua baris
    for (let row = 1; row <= 25; row++) {
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
    
    // Update final salary
    const finalSalaryElement = document.getElementById('final_salary');
    if (finalSalaryElement) {
        finalSalaryElement.textContent = grandTotal.toLocaleString('id-ID');
    }
    
    // Update total pallet
    const totalPalletElement = document.getElementById('total_pallet');
    if (totalPalletElement) {
        totalPalletElement.textContent = totalPieces + ' PALLET';
    }
}

// fungsi simpan template dihapus sesuai permintaan

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
    <div class="bg-white dark:bg-slate-900 shadow-lg rounded-lg overflow-hidden w-full max-w-full">
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
            <!-- Wrapper untuk Header + Tabel + Footer -->
            <div class="overflow-hidden table-wrapper">
                <!-- Header Perusahaan - Rapi (simple black, natural height) -->
                <div class="report-header p-2 pb-1 border-b-2 border-black dark:border-white bg-white dark:bg-slate-800 print:bg-white mb-0" style="width: 100%;">
                    <div class="flex justify-between items-center gap-3" style="width: 100%;">
                        <!-- Peraturan (Kiri) -->
                        <div class="flex-shrink-0">
                            <div class="rules-box border-2 border-black dark:border-white p-1 bg-white dark:bg-slate-700" style="width: 150px;">
                                <div class="text-black dark:text-white font-bold text-[10px] leading-tight">PERATURAN</div>
                                <div class="text-black dark:text-white text-[10px] leading-tight">DILARANG BELAH PAPAN/BALOK TANPA IJIN</div>
                            </div>
                        </div>

                        <!-- Nama Perusahaan (Tengah) -->
                        <div class="flex-1 text-center px-2">
                            <div class="flex flex-col items-center justify-center leading-tight">
                                <h1 class="text-base font-extrabold text-black dark:text-white whitespace-nowrap">PT. CAM JAYA ABADI</h1>
                                <div class="text-[11px] font-semibold text-gray-800 dark:text-gray-200">LAPORAN PENGGAJIAN KARYAWAN</div>
                            </div>
                        </div>

                        <!-- Info Biaya (Kanan) -->
                        <div class="flex-shrink-0 text-right">
                            <div class="border-2 border-black dark:border-white bg-white dark:bg-slate-700" style="width: 140px; height: 30px; display: flex; flex-direction: column; justify-content: center;">
                                <div class="border-b border-black dark:border-white p-0.5">
                                    <div class="font-bold text-[10px] text-center dark:text-white">BIAYA / UPAH KERJA</div>
                                </div>
                                <div class="p-0.5">
                                    <div class="font-bold text-[10px] text-center dark:text-white">{{ now()->format('d-M-Y') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabel Utama - Responsive -->
                <table class="w-full border-collapse" style="table-layout: fixed; margin-top: 4px;">
                    <!-- Header Tabel - Sesuai Referensi -->
                    <thead>
                        <tr class="bg-gray-100 dark:bg-slate-600 print:bg-gray-200">
                            <th class="border border-black dark:border-white p-1 text-[10px] font-bold text-center dark:text-white print:border-black" style="width: 8%; height: 12px;">ITEM</th>
                            <th class="border border-black dark:border-white p-1 text-[10px] font-bold text-center bg-gray-200 dark:bg-slate-500 dark:text-white print:bg-gray-300" style="width: 15%; height: 12px;">DESKRIPSI</th>
                            
                            <!-- Kolom Tanggal 1-31 -->
                            @for($day = 1; $day <= 31; $day++)
                                <th class="border border-black dark:border-white p-0 text-[9px] font-bold text-center dark:text-white print:border-black" style="width: 22px; height: 12px; line-height: 12px;">{{ $day }}</th>
                            @endfor
                            
                            <th class="border border-black dark:border-white p-1 text-[10px] font-bold text-center dark:text-white print:border-black" style="width: 5%; height: 12px;">JUMLAH</th>
                            <th class="border border-black dark:border-white p-1 text-[10px] font-bold text-center dark:text-white print:border-black" style="width: 8%; height: 12px;">HSL. PROD</th>
                            <th class="border border-black dark:border-white p-1 text-[10px] font-bold text-center dark:text-white print:border-black" style="width: 8%; height: 12px;">HARGA</th>
                            <th class="border border-black dark:border-white p-1 text-[10px] font-bold text-center dark:text-white print:border-black" style="width: 10%; height: 12px;">TOTAL</th>
                            <th class="border border-black dark:border-white p-1 text-[10px] font-bold text-center dark:text-white print:border-black" style="width: 12%; height: 12px;">KETERANGAN</th>
                        </tr>
                    </thead>
                    
                    <!-- Body Tabel -->
                    <tbody>
                        <!-- Baris Data Karyawan -->
                        @for($row = 1; $row <= 25; $row++)
                        <tr class="{{ $row % 2 == 0 ? 'bg-gray-50 dark:bg-slate-700' : 'bg-white dark:bg-slate-800' }}" style="height: 14px;">
                            <!-- ITEM -->
                            <td class="border border-black dark:border-white p-0" style="height: 14px;">
                                <input type="text" class="w-full h-full border-0 bg-transparent text-[9px] dark:text-white focus:bg-yellow-100 dark:focus:bg-yellow-200 focus:outline-none" 
                                       placeholder="Karyawan {{ $row }}" id="item_{{ $row }}" style="height: 14px; padding: 1px;">
                            </td>
                            
                            <!-- DESKRIPSI -->
                            <td class="border border-black dark:border-white p-0 bg-gray-200 dark:bg-slate-600" style="height: 14px;">
                                <input type="text" class="w-full h-full border-0 bg-transparent text-[9px] dark:text-white focus:bg-yellow-100 dark:focus:bg-yellow-200 focus:outline-none" 
                                       placeholder="Posisi" id="desc_{{ $row }}" style="height: 14px; padding: 1px;">
                            </td>
                            
                            <!-- Kolom Tanggal 1-31 -->
                            @for($day = 1; $day <= 31; $day++)
                                <td class="border border-black dark:border-white p-0 {{ $row % 2 == 0 ? 'bg-gray-100 dark:bg-slate-700' : 'bg-white dark:bg-slate-800' }}" style="height: 14px;">
                                    <input type="text" class="w-full h-full border-0 bg-transparent text-[8px] dark:text-white text-center focus:bg-yellow-100 dark:focus:bg-yellow-200 focus:outline-none" 
                                           onchange="calculateManualRow({{ $row }})" id="day_{{ $row }}_{{ $day }}" style="height: 14px;">
                                </td>
                            @endfor
                            
                            <!-- JUMLAH -->
                            <td class="border border-black dark:border-white p-0 text-center" style="height: 14px;">
                                <span class="text-[9px] font-bold dark:text-white" id="total_{{ $row }}">0</span>
                            </td>
                            
                            <!-- HSL. PROD -->
                            <td class="border border-black dark:border-white p-0" style="height: 14px;">
                                <input type="number" class="w-full h-full border-0 bg-transparent text-[8px] dark:text-white text-center focus:bg-yellow-100 dark:focus:bg-yellow-200 focus:outline-none" 
                                       placeholder="0" onchange="calculateManualRow({{ $row }})" id="prod_{{ $row }}" style="height: 14px;">
                            </td>
                            
                            <!-- HARGA -->
                            <td class="border border-black dark:border-white p-0" style="height: 14px;">
                                <input type="number" class="w-full h-full border-0 bg-transparent text-[8px] dark:text-white text-center focus:bg-yellow-100 dark:focus:bg-yellow-200 focus:outline-none" 
                                       placeholder="0" onchange="calculateManualRow({{ $row }})" id="price_{{ $row }}" style="height: 14px;">
                            </td>
                            
                            <!-- TOTAL -->
                            <td class="border border-black dark:border-white p-0 text-center" style="height: 14px;">
                                <span class="text-[9px] font-bold dark:text-white" id="row_total_{{ $row }}">0</span>
                            </td>
                            
                            <!-- KETERANGAN -->
                            <td class="border border-black dark:border-white p-0" style="height: 14px;">
                                <input type="text" class="w-full h-full border-0 bg-transparent text-[8px] dark:text-white focus:bg-yellow-100 dark:focus:bg-yellow-200 focus:outline-none" 
                                       placeholder="Keterangan" id="note_{{ $row }}" style="height: 14px; padding: 1px;">
                            </td>
                        </tr>
                        @endfor
                    </tbody>
                    
                    <!-- Footer Tabel -->
                    <tfoot>
                        <tr class="bg-gray-100 dark:bg-slate-700 font-bold">
                            <td colspan="2" class="border border-black dark:border-white p-2 text-xs dark:text-white">Hasil produksi pallet dan biaya borongan</td>
                            
                            <!-- Total per hari 1-31 -->
                            @for($day = 1; $day <= 31; $day++)
                                <td class="border border-black dark:border-white p-1 text-center text-xs dark:text-white" id="day_total_{{ $day }}">0</td>
                            @endfor
                            
                            <td class="border border-black dark:border-white p-1 text-center text-xs dark:text-white">-</td>
                            <td class="border border-black dark:border-white p-1 text-xs">-</td>
                            <td class="border border-black dark:border-white p-1 text-center text-xs dark:text-white" id="grand_pieces">0 pcs</td>
                            <td class="border border-black dark:border-white p-1 text-xs">-</td>
                            <td colspan="2" class="border border-black dark:border-white p-1 text-right text-xs font-bold dark:text-white" id="final_total">0</td>
                            <td class="border border-black dark:border-white p-1 text-xs">-</td>
                        </tr>
                    </tfoot>
                </table>

                <!-- Footer PERSIS Seperti Referensi -->
                <div class="report-footer border-t-0 bg-white dark:bg-slate-800 print:bg-white p-1 mt-0" style="width: 100%;">
                    <div class="flex items-start gap-2" style="width: 100%;">
                        <!-- Kiri: Signature Area (pindah ke kiri, tanpa outline) -->
                        <div class="flex-shrink-0" style="width: 35%;">
                            <div class="flex gap-1 mb-1">
                                <!-- Report by (Kiri) -->
                                <div class="text-center" style="width: 50%;">
                                    <div class="text-[7px] mb-1 dark:text-white">Report by</div>
                                    <div class="bg-white dark:bg-slate-700 p-1" style="height: 40px;">
                                        <img src="{{ asset('image/LOGO.png') }}" alt="CAM Logo" class="w-8 h-8 mx-auto object-contain"
                                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                        <div class="border-2 border-blue-600 bg-blue-100 rounded-full w-8 h-8 mx-auto hidden items-center justify-center">
                                            <span class="text-[10px] font-bold text-blue-700">CAM</span>
                                        </div>
                                    </div>
                                    <div class="text-[6px] font-bold mt-1 dark:text-white">Reid Kubro Wahyudin</div>
                                    <div class="text-[5px] dark:text-gray-300">DIREKTUR</div>
                                </div>
                                
                                <!-- Approved by (Kanan) -->
                                <div class="text-center" style="width: 50%;">
                                    <div class="text-[7px] mb-1 dark:text-white">Approved by</div>
                                    <div class="bg-white dark:bg-slate-700 p-1" style="height: 40px;">
                                        <img src="{{ asset('image/LOGO.png') }}" alt="CAM Logo" class="w-8 h-8 mx-auto object-contain"
                                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                        <div class="border-2 border-blue-600 bg-blue-100 rounded-full w-8 h-8 mx-auto hidden items-center justify-center">
                                            <span class="text-[10px] font-bold text-blue-700">CAM</span>
                                        </div>
                                    </div>
                                    <div class="text-[6px] font-bold mt-1 dark:text-white">Panji Purnadi</div>
                                    <div class="text-[5px] dark:text-gray-300">DIREKTUR UTAMA</div>
                                </div>
                            </div>
                            
                            <!-- Warning Text Merah -->
                            <div class="text-center">
                                <div class="text-[6px] text-red-600 font-bold leading-none">JIKA ADA TOLERANSI PALLET MAKA</div>
                                <div class="text-[6px] text-red-600 font-bold leading-none">MASA PERBAIKAN PALLET TOLERANSI STANDAR</div>
                                <div class="text-[6px] text-red-600 font-bold leading-none">DAN JIKA REPAIR TIDAK MASUK HITUNGAN !!!</div>
                            </div>
                        </div>
                        
                        <!-- Tengah: 595 PALLET -->
                        <div class="flex-1 text-center">
                            <div class="text-6xl font-black dark:text-white">595</div>
                            <div class="text-xl font-bold -mt-2 dark:text-white">PALLET</div>
                            <div class="mt-2 text-[8px] text-right">
                                <div class="font-bold dark:text-white">TOTAL QTY:</div>
                                <div class="font-bold dark:text-white">5.292.000</div>
                            </div>
                        </div>
                        
                        <!-- Kanan: Box Total Persegi Panjang (dipindah ke kanan) -->
                        <div class="flex-shrink-0" style="width: 30%;">
                            <div class="border-2 border-black dark:border-white bg-white dark:bg-slate-700">
                                <!-- Baris 1: TOTAL BON -->
                                <div class="border-b border-black dark:border-white p-1">
                                    <div class="text-[8px] font-bold dark:text-white">TOTAL BON:</div>
                                    <div class="text-[8px] font-bold dark:text-white">1.500.000</div>
                                </div>
                                <!-- Baris 2: GRAND TOTAL DITERIMA -->
                                <div class="border-b border-black dark:border-white p-1">
                                    <div class="text-[8px] font-bold dark:text-white">GRAND TOTAL DITERIMA:</div>
                                    <div class="text-[8px] font-bold dark:text-white">3.792.000</div>
                                </div>
                                <!-- Baris 3: BON GRUPPPP + Rp (Kuning) -->
                                <div class="flex bg-yellow-300 dark:bg-yellow-400 print:bg-yellow-300">
                                    <div class="border-r border-black dark:border-white p-1 text-center" style="width: 60%;">
                                        <div class="text-[8px] font-bold dark:text-black">BON GRUPPPP</div>
                                    </div>
                                    <div class="p-1 text-center" style="width: 40%;">
                                        <div class="text-[8px] font-bold dark:text-black">Rp 1.500.000</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    

    {{-- Bagian render template Excel dinonaktifkan sesuai permintaan: gunakan tabel manual saja --}}

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
            
            #manualSalaryTable table {
                font-size: 11px !important;
                table-layout: fixed !important; /* gunakan fixed layout untuk kontrol lebar */
            }
            
            #manualSalaryTable th,
            #manualSalaryTable td {
                padding: 1px 2px !important; /* padding minimal */
                height: 18px !important; /* tinggi baris normal */
                line-height: 1.1 !important;
                vertical-align: middle !important;
            }
            
            #manualSalaryTable table {
                table-layout: fixed !important;
                width: 100% !important;
            }
            
            #manualSalaryTable th,
            #manualSalaryTable td {
                overflow: hidden !important;
                text-overflow: ellipsis !important;
                white-space: nowrap !important;
            }
            
            #manualSalaryTable input {
                height: 16px !important; /* tinggi input normal */
                padding: 0px 2px !important;
                font-size: 10px !important;
                line-height: 1.1 !important;
            }
            
            /* CSS KHUSUS UNTUK PRINT */
            @media print {
                /* Sembunyikan elemen yang tidak perlu saat print */
                .no-print, button, .bg-gradient-to-r {
                    display: none !important;
                }
                
                /* Optimasi untuk print */
                body {
                    background: white !important;
                    color: black !important;
                    font-size: 10px !important;
                }
                
                #manualSalaryTable {
                    background: white !important;
                    box-shadow: none !important;
                }
                
                #manualSalaryTable table {
                    width: 100% !important;
                    border-collapse: collapse !important;
                }
                
                #manualSalaryTable th,
                #manualSalaryTable td {
                    border: 2px solid black !important;
                    padding: 2px !important;
                    font-size: 9px !important;
                    line-height: 1.1 !important;
                }
                
                #manualSalaryTable input {
                    border: none !important;
                    background: transparent !important;
                    font-size: 9px !important;
                    padding: 0 !important;
                }
                
                /* Header perusahaan untuk print */
                .print\\:bg-white {
                    background: white !important;
                }
                
                .print\\:bg-gray-200 {
                    background: #e5e7eb !important;
                }
                
                .print\\:bg-gray-300 {
                    background: #d1d5db !important;
                }
                
                .print\\:border-black {
                    border-color: black !important;
                }
            }
            
            /* Responsive untuk tabel manual ketika sidebar hidden */
            body.sidebar-hidden #manualSalaryTable table {
                width: 100% !important;
                min-width: 100% !important;
                table-layout: auto !important; /* auto layout untuk responsive */
            }
            
            body.sidebar-hidden #manualSalaryTable .overflow-x-auto {
                overflow-x: visible !important; /* hilangkan scroll horizontal */
            }
            
            body.sidebar-hidden #manualSalaryTable {
                width: 100% !important;
                max-width: none !important;
            }
            
            body.sidebar-hidden #manualSalaryTable th,
            body.sidebar-hidden #manualSalaryTable td {
                width: auto !important; /* biarkan semua kolom menyesuaikan */
                min-width: 0 !important;
                max-width: none !important;
            }
            
            /* Sidebar hidden - table stays responsive */
            body.sidebar-hidden #manualSalaryTable table {
                width: 100% !important;
                table-layout: fixed !important;
            }
            
        </style>
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

    if (selectedOption.value) {
        // Get raw gaji pokok value directly from data attribute
        const rawGajiPokok = selectedOption.getAttribute('data-gaji-pokok');
        
        
        if (rawGajiPokok && rawGajiPokok !== '0') {
            // Convert to integer to ensure no decimal issues
            const gajiPokokInt = parseInt(rawGajiPokok);
            
            
            // Format for display using Indonesian locale
            const formattedGaji = 'Rp ' + gajiPokokInt.toLocaleString('id-ID');
            gajiPokokDisplay.value = formattedGaji;
            gajiPokokValue.value = gajiPokokInt;
            
            
        } else {
            gajiPokokDisplay.value = '';
            gajiPokokValue.value = '';
        }
        
        
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