@extends('layouts.app')

@section('title', 'Laporan Pengeluaran')

@push('styles')
<style>
/* Sembunyikan panah native agar tidak muncul dobel di beberapa browser */
.no-native-arrow {
  -webkit-appearance: none !important;
  -moz-appearance: none !important;
  appearance: none !important;
  background-image: none !important;
}
.no-native-arrow::-ms-expand { display: none; }

/* Dark mode: jadikan icon kalender (picker indicator) putih */
.dark input[type="date"]::-webkit-calendar-picker-indicator {
  filter: invert(1) brightness(1.6);
}
</style>
@endpush

@section('content')
<div x-data="expensePage({{ json_encode(['month' => $bulanNow ?? now()->month, 'year' => $tahunNow ?? now()->year]) }}, @js($salaryByEmployee ?? []), @js($otherExpensesMonthly ?? []), @js($monthlySalaryTotal ?? 0), @js($monthlyOtherExpenseTotal ?? 0), @js($salaryByMonth ?? []), @js($expensesByMonth ?? []))" x-init="init()" class="min-h-[70vh]">
    <!-- Filters -->
    <div class="sticky top-16 z-20 -mx-6 md:-mx-10 px-6 md:px-10 py-3 bg-white/90 backdrop-blur border-b border-gray-100 dark:bg-gray-900/80 dark:border-gray-800">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100">Laporan Pengeluaran</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">Gaji karyawan dan pengeluaran lain</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <div class="relative">
                    <select x-model="filters.month" @change="onFilterChange" class="no-native-arrow no-arrow appearance-none bg-white border border-gray-200 text-gray-700 text-sm rounded-lg pl-9 pr-9 py-2 shadow-sm hover:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-200 dark:hover:border-blue-400 dark:focus:ring-blue-400">
                        <template x-for="(m, idx) in months" :key="idx">
                            <option :value="String(idx+1)" :selected="String(idx+1)===filters.month" x-text="m"></option>
                        </template>
                    </select>
                    <span class="pointer-events-none absolute inset-y-0 left-2 flex items-center text-gray-400 dark:text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </span>
                    <span class="pointer-events-none absolute inset-y-0 right-2 flex items-center text-gray-400 dark:text-gray-400">
                        <i class="fa-solid fa-chevron-down text-xs"></i>
                    </span>
                </div>
                <!-- Filter Tahun (hidden untuk filtering, tapi tetap ada) -->
                <select x-model="filters.year" @change="onFilterChange" class="hidden">
                    <template x-for="y in allYears" :key="y">
                        <option :value="String(y)" :selected="String(y)===filters.year" x-text="y"></option>
                    </template>
                </select>
                <!-- Link Pilih Tahun -->
                <button type="button" @click="openYearModal()" 
                        class="inline-flex items-center px-3 py-2 text-sm font-medium text-indigo-600 bg-indigo-50 border border-indigo-200 rounded-lg hover:bg-indigo-100 hover:text-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:bg-indigo-900/30 dark:text-indigo-300 dark:border-indigo-700 dark:hover:bg-indigo-900/50">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <span x-text="'Pilih Tahun (' + filters.year + ')'"></span>
                </button>
                <a :href="filterUrl()" class="inline-flex items-center gap-2 px-3 py-2 rounded-lg text-sm font-medium bg-blue-600 text-white hover:bg-blue-700 shadow-sm hover:shadow transition dark:bg-blue-600/80 dark:hover:bg-blue-500">
                    <i class="fa-solid fa-rotate"></i>
                    <span>Terapkan</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Summary cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mt-6">
        <div class="group bg-white rounded-2xl p-5 shadow-sm hover:shadow-md border border-gray-100 transition dark:bg-gray-800 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <p class="text-sm text-gray-500 dark:text-gray-400">Gaji (Bulan Ini)</p>
                <span class="inline-flex items-center justify-center h-9 w-9 rounded-xl bg-emerald-50 text-emerald-600 group-hover:bg-emerald-100 transition dark:bg-gray-700 dark:text-emerald-300 dark:group-hover:bg-gray-600"><i class="fa-solid fa-users"></i></span>
            </div>
            <div class="mt-3 text-2xl font-semibold text-gray-800 dark:text-gray-100" x-text="formatCurrency(monthlySalaryTotal)"></div>
        </div>
        <div class="group bg-white rounded-2xl p-5 shadow-sm hover:shadow-md border border-gray-100 transition dark:bg-gray-800 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <p class="text-sm text-gray-500 dark:text-gray-400">Pengeluaran Lain (Bulan Ini)</p>
                <span class="inline-flex items-center justify-center h-9 w-9 rounded-xl bg-rose-50 text-rose-600 group-hover:bg-rose-100 transition dark:bg-gray-700 dark:text-rose-300 dark:group-hover:bg-gray-600"><i class="fa-solid fa-file-invoice-dollar"></i></span>
            </div>
            <div class="mt-3 text-2xl font-semibold text-gray-800 dark:text-gray-100" x-text="formatCurrency(monthlyOtherExpenseTotal)"></div>
        </div>
        <div class="group bg-white rounded-2xl p-5 shadow-sm hover:shadow-md border border-gray-100 transition dark:bg-gray-800 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <p class="text-sm text-gray-500 dark:text-gray-400">Total Pengeluaran (Bulan Ini)</p>
                <span class="inline-flex items-center justify-center h-9 w-9 rounded-xl bg-indigo-50 text-indigo-600 group-hover:bg-indigo-100 transition dark:bg-gray-700 dark:text-indigo-300 dark:group-hover:bg-gray-600"><i class="fa-solid fa-scale-balanced"></i></span>
            </div>
            <div class="mt-3 text-2xl font-semibold text-gray-800 dark:text-gray-100" x-text="formatCurrency(monthlySalaryTotal + monthlyOtherExpenseTotal)"></div>
        </div>
    </div>

    

    <!-- Rekap Tahunan -->
    <div class="mt-6 grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden dark:bg-gray-800 dark:border-gray-700">
            <div class="px-5 py-4">
                <h3 class="text-base font-semibold text-gray-800 dark:text-gray-100">Rekap Gaji per Bulan ({{ $tahunNow }})</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-50 text-slate-600 uppercase text-xs dark:bg-gray-900/40 dark:text-gray-300">
                        <tr>
                            <th class="text-left px-5 py-3 font-semibold">Bulan</th>
                            <th class="text-right px-5 py-3 font-semibold">Total</th>
                            <th class="px-5 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        <template x-for="(label, idx) in months" :key="idx">
                            <tr>
                                <td class="px-5 py-3 dark:text-gray-300" x-text="label"></td>
                                <td class="px-5 py-3 text-right dark:text-gray-200" x-text="formatCurrency(salaryByMonth[idx+1]||0)"></td>
                                <td class="px-5 py-3 text-right">
                                    <a href="#" @click.prevent="openExpenseDetail('salary', idx+1, Number(filters.year))" class="text-xs text-blue-600 hover:underline dark:text-blue-400">Lihat detail</a>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden dark:bg-gray-800 dark:border-gray-700">
            <div class="px-5 py-4 flex justify-between items-center">
                <h3 class="text-base font-semibold text-gray-800 dark:text-gray-100">Rekap Pengeluaran Lain per Bulan ({{ $tahunNow }})</h3>
                <button @click="openAddExpenseModal()" 
                        class="group relative inline-flex items-center justify-center w-10 h-10 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 text-white hover:from-blue-700 hover:to-indigo-700 shadow-lg hover:shadow-xl transition-all duration-300 dark:from-blue-500 dark:to-indigo-500 dark:hover:from-blue-600 dark:hover:to-indigo-600 hover:scale-105"
                        title="Tambah Pengeluaran">
                    <i class="fa-solid fa-plus text-sm group-hover:rotate-90 transition-all duration-300"></i>
                </button>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-50 text-slate-600 uppercase text-xs dark:bg-gray-900/40 dark:text-gray-300">
                        <tr>
                            <th class="text-left px-5 py-3 font-semibold">Bulan</th>
                            <th class="text-right px-5 py-3 font-semibold">Total</th>
                            <th class="text-right px-5 py-3 font-semibold"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        <template x-for="(label, idx) in months" :key="idx">
                            <tr>
                                <td class="px-5 py-3 dark:text-gray-300" x-text="label"></td>
                                <td class="px-5 py-3 text-right dark:text-gray-200" x-text="formatCurrency(expensesByMonth[idx+1]||0)"></td>
                                <td class="px-5 py-3 text-right">
                                    <a href="#" @click.prevent="openExpenseDetail('other', idx+1, Number(filters.year))" class="text-xs text-blue-600 hover:underline dark:text-blue-400">Lihat detail</a>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Modal Detail (dipindah ke dalam scope x-data agar state Alpine terbaca) -->
    <div x-show="detailOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="absolute inset-0 bg-black/40" @click="closeDetail()"></div>
        <div class="relative bg-white w-[92vw] max-w-5xl rounded-2xl shadow-lg overflow-hidden dark:bg-gray-800 dark:text-gray-100">
            <div class="px-5 py-3 border-b flex items-center justify-between dark:border-gray-700">
                <h3 class="text-base font-semibold text-gray-800 dark:text-gray-100" x-text="detailTitle"></h3>
                <button class="h-8 w-8 inline-flex items-center justify-center rounded-md hover:bg-gray-100 dark:hover:bg-gray-700" @click="closeDetail()"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <div class="p-5">
                <template x-if="detailLoading">
                    <div class="py-10 text-center text-gray-500 dark:text-gray-400">Memuat data...</div>
                </template>
                <template x-if="!detailLoading">
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead class="bg-slate-50 text-slate-600 uppercase text-xs dark:bg-gray-900/40 dark:text-gray-300">
                                <tr x-show="detailType==='salary'">
                                    <th class="text-left px-3 py-2">Tanggal</th>
                                    <th class="text-left px-3 py-2">Bulan</th>
                                    <th class="text-left px-3 py-2">Tahun</th>
                                    <th class="text-right px-3 py-2">Total Gaji</th>
                                </tr>
                                <tr x-show="detailType==='other'">
                                    <th class="text-left px-3 py-2">Tanggal</th>
                                    <th class="text-left px-3 py-2">Jenis</th>
                                    <th class="text-left px-3 py-2">Deskripsi</th>
                                    <th class="text-right px-3 py-2">Jumlah</th>
                                    <th class="text-center px-3 py-2">Aksi</th>
                                </tr>
                            </thead>
                            <!-- Tabel body untuk tipe salary -->
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700" x-show="detailType==='salary'">
                                <template x-if="!detailRows.length">
                                    <tr>
                                        <td colspan="4" class="px-3 py-6 text-center text-gray-500 dark:text-gray-400">Tidak ada data.</td>
                                    </tr>
                                </template>
                                <template x-for="(r, i) in detailRows" :key="i">
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                        <td class="px-3 py-2 dark:text-gray-300" x-text="(() => { const d = r.tanggal_bayar ? new Date(r.tanggal_bayar) : null; return d ? String(d.getDate()).padStart(2,'0') : '-' })()"></td>
                                        <td class="px-3 py-2 dark:text-gray-300" x-text="(() => { const d = r.tanggal_bayar ? new Date(r.tanggal_bayar) : null; return d ? months[d.getMonth()] : months[Number(filters.month)-1] })()"></td>
                                        <td class="px-3 py-2 dark:text-gray-300" x-text="(() => { const d = r.tanggal_bayar ? new Date(r.tanggal_bayar) : null; return d ? d.getFullYear() : Number(filters.year) })()"></td>
                                        <td class="px-3 py-2 text-right dark:text-gray-200" x-text="formatCurrency(r.total_gaji)"></td>
                                    </tr>
                                </template>
                            </tbody>
                            <!-- Tabel body untuk tipe other -->
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700" x-show="detailType==='other'">
                                <template x-if="!detailRows.length">
                                    <tr>
                                        <td colspan="5" class="px-3 py-6 text-center text-gray-500 dark:text-gray-400">Tidak ada data.</td>
                                    </tr>
                                </template>
                                <template x-for="(r, i) in detailRows" :key="i">
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                        <td class="px-3 py-2 dark:text-gray-300" x-text="new Date(r.tanggal).toLocaleDateString('id-ID')"></td>
                                        <td class="px-3 py-2 dark:text-gray-300" x-text="r.jenis"></td>
                                        <td class="px-3 py-2 dark:text-gray-300" x-text="r.deskripsi"></td>
                                        <td class="px-3 py-2 text-right dark:text-gray-200" x-text="formatCurrency(r.amount)"></td>
                                        <td class="px-3 py-2 text-center">
                                            <div class="flex items-center justify-center gap-2">
                                                <button @click="editExpense(r)" 
                                                        class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 dark:bg-blue-900/30 dark:text-blue-400 dark:hover:bg-blue-900/50 transition-all duration-200"
                                                        title="Edit">
                                                    <i class="fa-solid fa-pen-to-square text-sm"></i>
                                                </button>
                                                <button @click="deleteExpense(r.id)" 
                                                        class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 dark:bg-red-900/30 dark:text-red-400 dark:hover:bg-red-900/50 transition-all duration-200"
                                                        title="Hapus">
                                                    <i class="fa-solid fa-trash text-sm"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </template>
            </div>
            <div class="px-5 py-3 border-t bg-gray-50 text-right dark:border-gray-700 dark:bg-gray-900/40">
                <button class="inline-flex items-center gap-2 px-3 py-1.5 rounded-md text-sm bg-gray-700 text-white hover:bg-gray-800 dark:bg-gray-700 dark:hover:bg-gray-600" @click="closeDetail()">Tutup</button>
            </div>
        </div>
    </div>
    
    <!-- Modal Pilih Tahun -->
    <div x-show="yearModalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center">
        <div class="absolute inset-0 bg-black/40" @click="closeYearModal()"></div>
        <div class="relative bg-white w-[92vw] max-w-lg rounded-2xl shadow-lg overflow-hidden dark:bg-gray-800">
            <div class="px-5 py-4 border-b flex items-center justify-between dark:border-gray-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Pilih Tahun</h3>
                <button type="button" @click="closeYearModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="p-5">
                <div class="grid grid-cols-4 gap-2 max-h-60 overflow-y-auto">
                    <template x-for="year in allYears" :key="year">
                        <button type="button" @click="selectYear(year)" 
                                class="px-3 py-2 text-sm font-medium rounded-md border transition-colors"
                                :class="String(year) === filters.year ? 
                                        'bg-indigo-600 text-white border-indigo-600' : 
                                        'bg-white text-gray-700 border-gray-300 hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-200 dark:border-gray-600 dark:hover:bg-gray-600'"
                                x-text="year">
                        </button>
                    </template>
                </div>
            </div>
            <div class="px-5 py-3 border-t bg-gray-50 text-right dark:border-gray-700 dark:bg-gray-900/40">
                <button type="button" @click="closeYearModal()" 
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500 dark:bg-gray-600 dark:text-gray-200 dark:border-gray-500 dark:hover:bg-gray-500">
                    Batal
                </button>
            </div>
        </div>
    </div>

    <!-- Modal Tambah Pengeluaran -->
    <div x-show="addExpenseModalOpen" x-cloak 
         x-transition:enter="transition ease-out duration-300" 
         x-transition:enter-start="opacity-0" 
         x-transition:enter-end="opacity-100" 
         x-transition:leave="transition ease-in duration-200" 
         x-transition:leave-start="opacity-100" 
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm dark:bg-black/80 z-0" @click="closeAddExpenseModal()"></div>
        <div x-show="addExpenseModalOpen" 
             x-transition:enter="transition ease-out duration-300 transform" 
             x-transition:enter-start="opacity-0 scale-95 translate-y-4" 
             x-transition:enter-end="opacity-100 scale-100 translate-y-0" 
             x-transition:leave="transition ease-in duration-200 transform" 
             x-transition:leave-start="opacity-100 scale-100 translate-y-0" 
             x-transition:leave-end="opacity-0 scale-95 translate-y-4"
             class="relative z-10 pointer-events-auto bg-white dark:bg-slate-900 rounded-2xl shadow-2xl w-full border border-gray-200 dark:border-slate-700 max-h-[90vh] overflow-y-auto"
             style="max-width: 28rem !important;">
            
            <!-- Header dengan gradient -->
            <div class="bg-gradient-to-r from-blue-600 to-indigo-600 dark:from-blue-500 dark:to-indigo-500 p-6">
                <div class="flex justify-between items-center">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                            <i class="fa-solid fa-receipt text-white text-lg"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-white" x-text="expenseForm.id ? 'Edit Pengeluaran' : 'Tambah Pengeluaran'"></h3>
                            <p class="text-blue-100 text-sm" x-text="expenseForm.id ? 'Perbarui data pengeluaran' : 'Catat pengeluaran baru'"></p>
                        </div>
                    </div>
                    <button @click="closeAddExpenseModal()" 
                            class="w-8 h-8 rounded-lg bg-white/20 hover:bg-white/30 text-white transition-all duration-200 flex items-center justify-center">
                        <i class="fa-solid fa-times text-sm"></i>
                    </button>
                </div>
            </div>
            
            <!-- Form Content -->
            <div class="p-6 bg-gray-50 dark:bg-slate-800">
                <form @submit.prevent="submitExpense()" class="space-y-5">
                    <!-- Tanggal -->
                    <div class="space-y-2">
                        <label class="flex items-center text-sm font-semibold text-gray-700 dark:text-gray-300">
                            <i class="fa-solid fa-calendar-days text-blue-500 mr-2"></i>
                            Tanggal Pengeluaran
                        </label>
                        <input type="date" x-model="expenseForm.tanggal" required 
                               class="w-full px-4 py-3 border border-gray-300 dark:border-slate-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent bg-white dark:bg-slate-700 text-gray-900 dark:text-gray-100 transition-all duration-200">
                    </div>
                    
                    <!-- Jenis -->
                    <div class="space-y-2">
                        <label class="flex items-center text-sm font-semibold text-gray-700 dark:text-gray-300">
                            <i class="fa-solid fa-tags text-indigo-500 mr-2"></i>
                            Jenis Pengeluaran
                        </label>
                        <div class="relative">
                            <select x-model="expenseForm.jenis" required 
                                    class="no-native-arrow w-full px-4 py-3 border border-gray-300 dark:border-slate-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent bg-white dark:bg-slate-700 text-gray-900 dark:text-gray-100 transition-all duration-200 appearance-none">
                                <option value="">Pilih Jenis Pengeluaran</option>
                                <option value="Operasional">🏢 Operasional</option>
                                <option value="Pemeliharaan">🔧 Pemeliharaan</option>
                                <option value="Transportasi">🚗 Transportasi</option>
                                <option value="Konsumsi">🍽️ Konsumsi</option>
                                <option value="Utilitas">⚡ Utilitas</option>
                                <option value="Peralatan">🛠️ Peralatan</option>
                                <option value="Lain-lain">📋 Lain-lain</option>
                            </select>
                            <i class="fa-solid fa-chevron-down absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400 pointer-events-none"></i>
                        </div>
                    </div>
                    
                    <!-- Deskripsi -->
                    <div class="space-y-2">
                        <label class="flex items-center text-sm font-semibold text-gray-700 dark:text-gray-300">
                            <i class="fa-solid fa-file-text text-green-500 mr-2"></i>
                            Deskripsi
                        </label>
                        <textarea x-model="expenseForm.deskripsi" rows="3" required 
                                  class="w-full px-4 py-3 border border-gray-300 dark:border-slate-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent bg-white dark:bg-slate-700 text-gray-900 dark:text-gray-100 transition-all duration-200 resize-none" 
                                  placeholder="Masukkan deskripsi pengeluaran..."></textarea>
                    </div>
                    
                    <!-- Jumlah -->
                    <div class="space-y-2">
                        <label class="flex items-center text-sm font-semibold text-gray-700 dark:text-gray-300">
                            <i class="fa-solid fa-money-bill-wave text-yellow-500 mr-2"></i>
                            Jumlah (Rp)
                        </label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-500 dark:text-gray-400 font-medium pointer-events-none">Rp</span>
                            <input type="text" x-model="expenseForm.amount_display" @input="formatAmountInput($event)" required 
                                   class="w-full pl-12 pr-4 py-3 border border-gray-300 dark:border-slate-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent bg-white dark:bg-slate-700 text-gray-900 dark:text-gray-100 transition-all duration-200" 
                                   placeholder="0">
                        </div>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200 dark:border-slate-600">
                        <button type="button" @click="closeAddExpenseModal()" 
                                class="px-6 py-3 bg-gray-100 dark:bg-slate-700 text-gray-700 dark:text-gray-300 rounded-xl hover:bg-gray-200 dark:hover:bg-slate-600 transition-all duration-200 font-medium">
                            <i class="fa-solid fa-times mr-2"></i>Batal
                        </button>
                        <button type="submit" :disabled="submitting" 
                                class="px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white rounded-xl transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed font-medium shadow-lg hover:shadow-xl">
                            <span x-show="!submitting" class="flex items-center">
                                <i class="fa-solid fa-save mr-2"></i>Simpan
                            </span>
                            <span x-show="submitting" class="flex items-center">
                                <i class="fa-solid fa-spinner fa-spin mr-2"></i>Menyimpan...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function expensePage(initialFilters, salaryRows, otherExpRows, salaryTotal, otherTotal, salaryByMonth, expensesByMonth) {
    return {
        months: ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'],
        allYears: @js($allYears ?? []),
        yearModalOpen: false,
        filters: { month: String(initialFilters.month), year: String(initialFilters.year) },
        salaryByMonth: salaryByMonth || {},
        expensesByMonth: expensesByMonth || {},
        monthlySalaryTotal: Number(salaryTotal||0),
        monthlyOtherExpenseTotal: Number(otherTotal||0),
        // Modal state
        detailOpen: false,
        detailLoading: false,
        detailTitle: '',
        detailType: 'salary',
        detailRows: [],
        // Add expense modal state
        addExpenseModalOpen: false,
        submitting: false,
        expenseForm: {
            tanggal: new Date().toISOString().split('T')[0],
            jenis: '',
            deskripsi: '',
            amount: 0,
            amount_display: ''
        },
        init(){},
        formatCurrency(v){ try { return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(v||0); } catch(e){ return `Rp ${Number(v||0).toLocaleString('id-ID')}` } },
        filterUrl(){
            const p = new URLSearchParams();
            p.set('month', Number(this.filters.month));
            p.set('year', Number(this.filters.year));
            return `{{ route('finance.expense') }}` + '?' + p.toString();
        },
        onFilterChange(){ /* noop: user must click Terapkan */ },
        async openExpenseDetail(type, month, year){
            try{
                this.detailOpen = true; this.detailLoading = true; this.detailRows = []; this.detailType = type;
                const p = new URLSearchParams();
                p.set('type', type); p.set('month', month); p.set('year', year);
                const url = `{{ route('finance.expense.detail') }}` + '?' + p.toString();
                const res = await fetch(url, { headers: { 'Accept': 'application/json' }});
                const json = await res.json();
                this.detailTitle = `Detail Pengeluaran - ${this.months[month-1]} ${year}` + (type==='salary'? ' · Gaji' : ' · Lainnya');
                this.detailRows = Array.isArray(json.data)? json.data : [];
            }catch(e){ console.error(e); }
            finally{ this.detailLoading = false; }
        },
        closeDetail(){ this.detailOpen = false; this.detailRows = []; },
        openYearModal(){ this.yearModalOpen = true; },
        closeYearModal(){ this.yearModalOpen = false; },
        selectYear(year){
            this.filters.year = String(year);
            this.closeYearModal();
        },
        // Add expense methods
        openAddExpenseModal(){
            this.addExpenseModalOpen = true;
            this.resetExpenseForm();
        },
        closeAddExpenseModal(){
            this.addExpenseModalOpen = false;
            this.resetExpenseForm();
        },
        resetExpenseForm(){
            this.expenseForm = {
                tanggal: new Date().toISOString().split('T')[0],
                jenis: '',
                deskripsi: '',
                amount: 0,
                amount_display: ''
            };
        },
        formatAmountInput(event){
            let value = event.target.value.replace(/[^0-9]/g, '');
            if (value) {
                this.expenseForm.amount = parseInt(value);
                this.expenseForm.amount_display = new Intl.NumberFormat('id-ID').format(parseInt(value));
            } else {
                this.expenseForm.amount = 0;
                this.expenseForm.amount_display = '';
            }
        },
        async submitExpense(){
            if (this.submitting) return;
            
            try {
                this.submitting = true;
                
                const formData = new FormData();
                formData.append('tanggal', this.expenseForm.tanggal);
                formData.append('jenis', this.expenseForm.jenis);
                formData.append('deskripsi', this.expenseForm.deskripsi);
                formData.append('amount', this.expenseForm.amount);
                formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
                
                // Jika ada ID, berarti ini adalah update
                let url = '{{ route("finance.expense.store") }}';
                if (this.expenseForm.id) {
                    url = `/finance/expense/${this.expenseForm.id}`;
                    formData.append('_method', 'PUT');
                }
                
                const response = await fetch(url, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'Accept': 'application/json'
                    }
                });
                
                if (response.ok) {
                    this.closeAddExpenseModal();
                    // Refresh page to show new data
                    window.location.reload();
                } else {
                    const errorData = await response.json();
                    alert('Error: ' + (errorData.message || 'Gagal menyimpan pengeluaran'));
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat menyimpan pengeluaran');
            } finally {
                this.submitting = false;
            }
        },
        // Edit expense
        editExpense(expense){
            this.expenseForm = {
                id: expense.id,
                tanggal: expense.tanggal,
                jenis: expense.jenis,
                deskripsi: expense.deskripsi,
                amount: expense.amount,
                amount_display: new Intl.NumberFormat('id-ID').format(expense.amount)
            };
            this.closeDetail();
            this.addExpenseModalOpen = true;
        },
        // Delete expense
        async deleteExpense(id){
            if (!confirm('Apakah Anda yakin ingin menghapus pengeluaran ini?')) {
                return;
            }
            
            try {
                const formData = new FormData();
                formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
                formData.append('_method', 'DELETE');
                
                const response = await fetch(`/finance/expense/${id}`, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'Accept': 'application/json'
                    }
                });
                
                if (response.ok) {
                    // Refresh page to show updated data
                    window.location.reload();
                } else {
                    const errorData = await response.json();
                    alert('Error: ' + (errorData.message || 'Gagal menghapus pengeluaran'));
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat menghapus pengeluaran');
            }
        }
    }
}
</script>
@endpush
@endsection

