@extends('layouts.app')

@section('title', 'Jatuh Tempo Management')

@section('content')
@php(
    $tahunTerpilihLocal = (int) (request('year') ?? ($tahun ?? now()->format('Y')))
)
@php(
    $bulanDipilih = (int) (request('month') ?? ($bulan ?? now()->format('n')))
)
@php(
    $namaBulanFull=['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember']
)
@php(
    $allYears = range(2020, 2035)
)
<div x-data="jatuhTempoDashboard(@js($bulanDipilih), @js($tahunTerpilihLocal), @js($allYears))" x-init="init()">
<div class="space-y-6">
    <!-- Header Section (styled like Surat Jalan, without export buttons) -->
    <div class="rounded-lg shadow-lg p-3 sm:p-4 mb-3 sm:mb-4 bg-gradient-to-r from-slate-50 to-slate-100 border border-gray-200 dark:border-transparent dark:bg-gradient-to-r dark:from-gray-700 dark:to-gray-800">
        <div class="flex flex-col space-y-3 sm:flex-row sm:justify-between sm:items-center sm:space-y-0">
            <div class="flex items-center space-x-2">
                <svg class="w-5 h-5 sm:w-6 sm:h-6 text-gray-700 dark:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <div>
                    <h1 class="text-base sm:text-xl font-bold text-gray-900 dark:text-white">Jatuh Tempo</h1>
                    <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-200">Kelola data Jatuh Tempo dengan mudah</p>
                </div>
            </div>
            <!-- Right controls: (hapus semua dihilangkan) -->
        </div>
    </div>

    <!-- Success Message -->
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
            {{ session('success') }}
        </div>
    @endif

@push('scripts')
<!-- Script hapus semua dihilangkan -->
@endpush

    <!-- Tips/Peringatan: Pilih Bulan -->
    
    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-orange-50 p-4 rounded-lg border border-orange-200 dark:bg-orange-900/20 dark:border-orange-800">
            <div class="flex items-center">
                <div class="p-2 bg-orange-500 rounded-lg dark:bg-orange-600">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-orange-600 dark:text-orange-300">Customer Pending</p>
                    <p class="text-2xl font-bold text-orange-900 dark:text-orange-100">{{ $totalCustomerPending ?? 0 }} Customer</p>
                </div>
            </div>
        </div>
        <!-- Total Tagihan Pending (Merah - Urgent) -->
        <div class="bg-red-50 p-4 rounded-lg border border-red-200 dark:bg-red-900/20 dark:border-red-800">
            <div class="flex items-center">
                <div class="p-2 bg-red-500 rounded-lg dark:bg-red-600">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-red-600 dark:text-red-300">Total Tagihan</p>
                    <p class="text-2xl font-bold text-red-900 dark:text-red-100">Rp {{ number_format($totalTagihanPending ?? 0, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
        
        <!-- Total Terbayar (Hijau - Success) -->
        
        <!-- Customer Pending (Orange - Warning) -->
        
        <!-- Customer Accept (Blue - Info) -->
        <div class="bg-blue-50 p-4 rounded-lg border border-blue-200 dark:bg-blue-900/20 dark:border-blue-800">
            <div class="flex items-center">
                <div class="p-2 bg-blue-500 rounded-lg dark:bg-blue-600">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20v-2a4 4 0 00-4-4H7a4 4 0 00-4 4v2m14-10a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-blue-600 dark:text-blue-300">Customer Accept</p>
                    <p class="text-2xl font-bold text-blue-900 dark:text-blue-100">{{ $totalCustomerAccept ?? 0 }} Customer</p>
                </div>
            </div>
        </div>
        <div class="bg-green-50 p-4 rounded-lg border border-green-200 dark:bg-green-900/20 dark:border-green-800">
            <div class="flex items-center">
                <div class="p-2 bg-green-500 rounded-lg dark:bg-green-600">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-green-600 dark:text-green-300">Total Terbayar</p>
                    <p class="text-2xl font-bold text-green-900 dark:text-green-100">Rp {{ number_format($totalTerbayar ?? 0, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="mt-2 rounded-md border border-amber-200 bg-amber-50 px-3 py-2 text-amber-800 flex items-start gap-2 dark:border-amber-700 dark:bg-amber-900/20 dark:text-amber-200">
        <svg class="w-5 h-5 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M12 18a9 9 0 110-18 9 9 0 010 18z" />
        </svg>
        <div class="text-xs sm:text-sm">
            <p class="font-semibold">Petunjuk</p>
            <p class="mt-0.5">Silakan pilih bulan pada panel "Ringkasan Jatuh Tempo per Bulan" untuk menampilkan data Jatuh Tempo pada bulan tersebut. Tahun aktif: <span class="font-medium">{{ $tahunTerpilihLocal }}</span>.</p>
        </div>
        <button type="button" class="ml-auto text-amber-700/70 hover:text-amber-900 dark:text-amber-300 dark:hover:text-amber-100" onclick="this.parentElement.classList.add('hidden')" aria-label="Tutup">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>
    <!-- Ringkasan per Bulan + Pilih Tahun -->

    <div class="mb-3 sm:mb-4">
        <div class="flex items-center justify-between mb-2">
            <h2 class="text-sm sm:text-base font-semibold text-gray-800 dark:text-slate-100">Ringkasan Jatuh Tempo per Bulan</h2>
            <div class="flex items-end gap-3">
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-gray-200">Status Pembayaran</label>
                    <select x-model="filters.status" @change="applyFilters()" class="mt-1 w-40 px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-yellow-500 bg-white dark:bg-slate-800 dark:border-slate-600 dark:text-slate-100">
                        <option value="">Semua</option>
                        <option value="Belum Bayar">Pending</option>
                        <option value="Lunas">Accept</option>
                    </select>
                </div>
                <button type="button" @click="openYearModal()" 
                        class="inline-flex items-center px-3 py-2 text-sm font-medium text-indigo-600 bg-indigo-50 border border-indigo-200 rounded-md hover:bg-indigo-100 hover:text-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:bg-indigo-900/30 dark:text-indigo-300 dark:border-indigo-700 dark:hover:bg-indigo-900/50">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <span x-text="'Pilih Tahun (' + filters.year + ')'"></span>
                </button>
            </div>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-2">
            @for($m=1;$m<=12;$m++)
                @php($isActive = $bulanDipilih === $m)
                <a href="{{ route('jatuh-tempo.index', ['month' => $m, 'year' => $tahunTerpilihLocal, 'status' => request('status')]) }}" class="block focus:outline-none focus:ring-2 focus:ring-indigo-500 rounded-lg">
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
                            <span class="font-medium text-gray-700 dark:text-slate-100">{{ (int)($monthlyStats[$m]->total_count ?? 0) }}</span>
                        </div>
                        <div class="mt-0.5 flex items-center justify-between">
                            <span class="text-[11px] text-gray-500 dark:text-slate-300">Total</span>
                            <span class="font-semibold text-green-700 dark:text-green-400">Rp {{ number_format((float)($monthlyStats[$m]->total_sum ?? 0), 0, ',', '.') }}</span>
                        </div>
                    </div>
                </a>
            @endfor
        </div>
    </div>

    <!-- Jatuh Tempo Table -->
    <div class="bg-white dark:bg-slate-800 rounded-lg shadow overflow-hidden">
        <div class="overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-slate-700">
                <thead class="bg-gray-50 dark:bg-slate-700">
                    <tr>
                        <th class="py-1 px-1.5 text-left text-xs font-medium text-gray-500 dark:text-slate-200 uppercase tracking-wider">Tgl Invoice</th>
                        <th class="py-1 px-1.5 text-left text-xs font-medium text-gray-500 dark:text-slate-200 uppercase tracking-wider">No Invoice</th>
                        <th class="py-1 px-1.5 text-left text-xs font-medium text-gray-500 dark:text-slate-200 uppercase tracking-wider">Customer</th>
                        <th class="py-1 px-1.5 text-left text-xs font-medium text-gray-500 dark:text-slate-200 uppercase tracking-wider">No PO</th>
                        <th class="py-1 px-1.5 text-left text-xs font-medium text-gray-500 dark:text-slate-200 uppercase tracking-wider">Tagihan</th>
                        <th class="py-1 px-1.5 text-left text-xs font-medium text-gray-500 dark:text-slate-200 uppercase tracking-wider">Deadline</th>
                        <th class="py-1 px-1.5 text-center text-xs font-medium text-gray-500 dark:text-slate-200 uppercase tracking-wider">Status</th>
                        <th class="py-1 px-1.5 text-center text-xs font-medium text-gray-500 dark:text-slate-200 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-slate-800 divide-y divide-gray-200 dark:divide-slate-700">
                    @forelse($jatuhTempos as $jt)
                        @php($overdue = ($jt->status_pembayaran !== 'Lunas') && (\Carbon\Carbon::parse($jt->tanggal_jatuh_tempo)->lt(\Carbon\Carbon::today())))
                        <tr class="hover:bg-gray-50 dark:hover:bg-slate-700 {{ $overdue ? 'bg-red-50 dark:bg-red-900/20' : '' }}">
                            <td class="py-1 px-1.5 text-xs text-gray-900 dark:text-slate-200">{{ $jt->tanggal_invoice->format('d/m/Y') }}</td>
                            <td class="py-1 px-1.5 text-xs text-gray-900 dark:text-slate-200">{{ $jt->no_invoice ?: '-' }}</td>
                            <td class="py-1 px-1.5 text-xs text-gray-900 dark:text-slate-200">{{ Str::limit($jt->customer, 15) }}</td>
                            <td class="py-1 px-1.5 text-xs text-gray-900 dark:text-slate-200">{{ $jt->no_po ?: '-' }}</td>
                            <td class="py-1 px-1.5 text-xs text-gray-900 dark:text-slate-200">Rp {{ number_format($jt->jumlah_tagihan, 0, ',', '.') }}</td>
                            <td class="py-1 px-1.5 text-xs">
                                @php($deadline = \Carbon\Carbon::parse($jt->tanggal_jatuh_tempo))
                                @php($today = \Carbon\Carbon::today())
                                @php($daysLeft = $today->diffInDays($deadline, false))
                                
                                <div class="flex flex-col">
                                    <span class="text-gray-900 dark:text-slate-200">{{ $deadline->format('d/m/Y') }}</span>
                                    @if($jt->status_pembayaran !== 'Lunas')
                                        <!-- Notifikasi hanya muncul saat status Pending -->
                                        @if($daysLeft < 0)
                                            <div class="flex items-center space-x-1">
                                                <span class="text-red-600 dark:text-red-400 text-[10px] font-medium">
                                                    Terlambat {{ abs($daysLeft) }} hari
                                                </span>
                                                <div class="relative">
                                                    <span class="flex h-2 w-2">
                                                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                                                        <span class="relative inline-flex rounded-full h-2 w-2 bg-red-500"></span>
                                                    </span>
                                                </div>
                                            </div>
                                        @elseif($daysLeft == 0)
                                            <div class="flex items-center space-x-1">
                                                <span class="text-orange-600 dark:text-orange-400 text-[10px] font-medium">
                                                    Jatuh tempo hari ini
                                                </span>
                                                <div class="relative">
                                                    <span class="flex h-2 w-2">
                                                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-orange-400 opacity-75"></span>
                                                        <span class="relative inline-flex rounded-full h-2 w-2 bg-orange-500"></span>
                                                    </span>
                                                </div>
                                            </div>
                                        @elseif($daysLeft <= 7)
                                            <div class="flex items-center space-x-1">
                                                <span class="text-yellow-600 dark:text-yellow-400 text-[10px] font-medium">
                                                    {{ $daysLeft }} hari lagi
                                                </span>
                                                <div class="relative">
                                                    <span class="flex h-2 w-2">
                                                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-yellow-400 opacity-75"></span>
                                                        <span class="relative inline-flex rounded-full h-2 w-2 bg-yellow-500"></span>
                                                    </span>
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-green-600 dark:text-green-400 text-[10px] font-medium">
                                                {{ $daysLeft }} hari lagi
                                            </span>
                                        @endif
                                    @else
                                        <!-- Saat status Accept, notifikasi hilang -->
                                        <div class="inline-flex items-center px-2 py-1 rounded-full text-[10px] font-medium bg-emerald-100 text-emerald-800 border border-emerald-200 dark:bg-emerald-900/30 dark:text-emerald-400 dark:border-emerald-700">
                                            <svg class="w-2.5 h-2.5 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                            </svg>
                                            Accept
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td class="py-2 px-2 text-xs">
                                <div class="flex items-center justify-center">
                                    <button type="button" 
                                            onclick="toggleStatus({{ $jt->id }}, '{{ $jt->status_pembayaran }}')"
                                            class="relative inline-flex items-center justify-center w-[90px] px-3 py-2 text-xs font-medium rounded-md border transition-all duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-offset-1
                                            @if($jt->status_pembayaran == 'Lunas') 
                                                bg-emerald-50 text-emerald-700 border-emerald-200 hover:bg-emerald-100 hover:border-emerald-300 focus:ring-emerald-500 dark:bg-emerald-900/20 dark:text-emerald-400 dark:border-emerald-700 dark:hover:bg-emerald-900/30
                                            @else 
                                                bg-amber-50 text-amber-700 border-amber-200 hover:bg-amber-100 hover:border-amber-300 focus:ring-amber-500 dark:bg-amber-900/20 dark:text-amber-400 dark:border-amber-700 dark:hover:bg-amber-900/30
                                            @endif">
                                        <div class="flex items-center justify-center space-x-1.5">
                                            @if($jt->status_pembayaran == 'Lunas')
                                                <svg class="w-3.5 h-3.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                </svg>
                                                <span class="font-medium">Accept</span>
                                            @else
                                                <svg class="w-3.5 h-3.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                                </svg>
                                                <span class="font-medium">Pending</span>
                                            @endif
                                        </div>
                                    </button>
                                </div>
                            </td>
                            <td class="py-1 px-1.5 text-xs text-center">
                                <x-table.action-buttons 
                                    onEdit="editJatuhTempo({{ $jt->id }})"
                                    deleteAction="{{ route('jatuh-tempo.destroy', ['jatuhTempo' => $jt->id, 'month' => request('month', $bulan ?? now()->format('n')), 'year' => request('year', $tahun ?? now()->format('Y')), 'status' => request('status')]) }}"
                                    confirmText="Yakin ingin menghapus invoice {{ $jt->no_invoice ?: '-' }}?"
                                />
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-4 px-6 text-center text-gray-500 dark:text-slate-400">Belum ada data jatuh tempo</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
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

<!-- Create/Edit Modal -->
<div id="jatuhTempoModal" class="fixed inset-0 hidden z-50 bg-slate-900/60 dark:bg-black/70">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white dark:bg-slate-800 dark:text-slate-100 rounded-lg shadow-xl max-w-3xl w-full max-h-screen overflow-y-auto">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 id="modalTitle" class="text-lg font-medium dark:text-slate-100">Tambah Jatuh Tempo</h3>
                    <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Form untuk Create (lengkap) -->
                <form id="jatuhTempoForm" method="POST" action="{{ route('jatuh-tempo.store') }}" class="space-y-4" style="display: block;">
                    @csrf
                    <div id="methodField"></div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">No Invoice</label>
                            <!-- Hidden full value: akan diisi otomatis mengikuti tanggal (No PO) -->
                            <input type="hidden" name="no_invoice" id="no_invoice">
                            <div class="mt-1 text-xs text-gray-600">No Invoice akan mengikuti nilai <span class="font-medium">Tanggal (Surat Jalan)</span>.</div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Tanggal (Surat Jalan)</label>
                            <input type="date" name="no_po" id="no_po" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500 bg-white dark:bg-slate-900 dark:border-slate-600 dark:text-slate-100 date-input" placeholder="Pilih tanggal">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Customer</label>
                        <select name="customer" id="customer" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500 bg-white dark:bg-slate-900 dark:border-slate-600 dark:text-slate-100">
                            <option value="">-- Pilih Customer --</option>
                            @foreach($customers as $c)
                                <option value="{{ $c->name }}" data-payment-terms="{{ $c->payment_terms_days ?? 30 }}">
                                    {{ $c->name }}
                                </option>
                            @endforeach
                        </select>
                        <div id="customer-terms-info" class="mt-1 text-xs text-gray-500 hidden">
                            <i class="fas fa-info-circle"></i>
                            <span id="terms-text"></span>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Tanggal Invoice</label>
                            <input type="date" name="tanggal_invoice" id="tanggal_invoice" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500 bg-white dark:bg-slate-900 dark:border-slate-600 dark:text-slate-100 date-input">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Tanggal Jatuh Tempo</label>
                            <input type="date" name="tanggal_jatuh_tempo" id="tanggal_jatuh_tempo" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500 bg-white dark:bg-slate-900 dark:border-slate-600 dark:text-slate-100 date-input">
                            <div class="mt-2 space-y-2">
                                <label class="inline-flex items-center text-sm">
                                    <input type="checkbox" id="auto_plus_month" class="mr-2">
                                    Auto +1 bulan dari Tanggal Invoice
                                </label>
                                <div class="text-xs text-gray-600">
                                    <strong>Custom Terms:</strong>
                                    <button type="button" onclick="setCustomDays(7)" class="ml-1 px-2 py-1 text-xs bg-blue-100 text-blue-700 rounded hover:bg-blue-200">7 hari</button>
                                    <button type="button" onclick="setCustomDays(14)" class="ml-1 px-2 py-1 text-xs bg-blue-100 text-blue-700 rounded hover:bg-blue-200">14 hari</button>
                                    <button type="button" onclick="setCustomDays(21)" class="ml-1 px-2 py-1 text-xs bg-blue-100 text-blue-700 rounded hover:bg-blue-200">21 hari</button>
                                    <button type="button" onclick="setCustomDays(45)" class="ml-1 px-2 py-1 text-xs bg-blue-100 text-blue-700 rounded hover:bg-blue-200">45 hari</button>
                                    <button type="button" onclick="setCustomDays(60)" class="ml-1 px-2 py-1 text-xs bg-blue-100 text-blue-700 rounded hover:bg-blue-200">60 hari</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Jumlah Tagihan</label>
                            <input type="number" name="jumlah_tagihan" id="jumlah_tagihan" required min="0" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500 bg-white dark:bg-slate-900 dark:border-slate-600 dark:text-slate-100">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Jumlah Terbayar</label>
                            <input type="number" name="jumlah_terbayar" id="jumlah_terbayar" min="0" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500 bg-white dark:bg-slate-900 dark:border-slate-600 dark:text-slate-100">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Denda</label>
                            <input type="number" name="denda" id="denda" min="0" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500 bg-white dark:bg-slate-900 dark:border-slate-600 dark:text-slate-100">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Status Pembayaran</label>
                        <select name="status_pembayaran" id="status_pembayaran" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500 bg-white dark:bg-slate-900 dark:border-slate-600 dark:text-slate-100">
                            <option value="Pending" selected>Pending</option>
                            <option value="Accept">Accept</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Status Approval</label>
                        <select name="status_approval" id="status_approval" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500 bg-white dark:bg-slate-900 dark:border-slate-600 dark:text-slate-100">
                            <option value="Pending" selected>Pending</option>
                            <option value="ACC">ACC</option>
                            <option value="Reject">Reject</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Catatan</label>
                        <textarea name="catatan" id="catatan" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500 bg-white dark:bg-slate-900 dark:border-slate-600 dark:text-slate-100"></textarea>
                    </div>

                    <div class="flex justify-end space-x-3 pt-4">
                        <button type="button" onclick="closeModal()" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 dark:border-slate-600 dark:text-slate-200 dark:hover:bg-slate-700/50">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 bg-orange-600 text-white rounded-md hover:bg-orange-700 dark:bg-orange-500 dark:hover:bg-orange-400">
                            Simpan
                        </button>
                    </div>
                </form>

                <script>
                (function(){
                    const form = document.getElementById('jatuhTempoForm');
                    if (!form) return;
                    const full = document.getElementById('no_invoice');
                    const noPo = document.getElementById('no_po');
                    function toShortDdMMyy(iso) {
                        if (!iso) return '';
                        const d = new Date(iso);
                        if (isNaN(d.getTime())) return iso; // fallback raw
                        const dd = String(d.getDate()).padStart(2,'0');
                        const mm = String(d.getMonth()+1).padStart(2,'0');
                        const yy = String(d.getFullYear()).slice(-2);
                        return `${dd}/${mm}/${yy}`;
                    }
                    function syncNoInvoice() {
                        if (full && noPo) {
                            const iso = (noPo.value || '').trim();
                            full.value = toShortDdMMyy(iso);
                        }
                    }
                    if (noPo) {
                        noPo.addEventListener('input', syncNoInvoice);
                        noPo.addEventListener('change', syncNoInvoice);
                    }
                    form.addEventListener('submit', syncNoInvoice);
                    // initial sync
                    syncNoInvoice();
                })();
                </script>

                <!-- Form untuk Edit Deadline (hanya deadline) -->
                <form id="editDeadlineForm" method="POST" class="space-y-4" style="display: none;">
                    @csrf
                    <div id="editMethodField"></div>
                    <!-- Preserve current filters -->
                    <input type="hidden" name="month" value="{{ request('month', $bulan ?? now()->format('n')) }}">
                    <input type="hidden" name="year" value="{{ request('year', $tahun ?? now()->format('Y')) }}">
                    <input type="hidden" name="status" value="{{ request('status') }}">
                    
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 dark:bg-slate-800/60 dark:border-slate-700">
                        <h4 class="text-sm font-medium text-gray-700 dark:text-slate-200 mb-3">Informasi Invoice</h4>
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="text-gray-500 dark:text-slate-300">No Invoice:</span>
                                <span id="edit_display_no_invoice" class="font-medium dark:text-slate-100"></span>
                            </div>
                            <div>
                                <span class="text-gray-500 dark:text-slate-300">Customer:</span>
                                <span id="edit_display_customer" class="font-medium dark:text-slate-100"></span>
                            </div>
                            <div>
                                <span class="text-gray-500 dark:text-slate-300">Tanggal Invoice:</span>
                                <span id="edit_display_tanggal_invoice" class="font-medium dark:text-slate-100"></span>
                            </div>
                            <div>
                                <span class="text-gray-500 dark:text-slate-300">Deadline Saat Ini:</span>
                                <span id="edit_display_current_deadline" class="font-medium text-red-600 dark:text-red-400"></span>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-200 mb-2">Ubah Deadline</label>
                        <input type="date" name="tanggal_jatuh_tempo" id="edit_tanggal_jatuh_tempo" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500 bg-white dark:bg-slate-900 dark:border-slate-600 dark:text-slate-100 date-input">
                        
                        <div class="mt-3">
                            <p class="text-sm text-gray-600 dark:text-slate-300 mb-2"><strong>Quick Terms dari Tanggal Invoice:</strong></p>
                            <div id="edit_terms_buttons" class="flex flex-wrap gap-2">
                                <button type="button" data-days="7" onclick="setEditCustomDays(7)" class="px-3 py-2 text-sm rounded bg-blue-100 text-blue-700 hover:bg-blue-200 dark:bg-slate-700 dark:text-slate-200 dark:hover:bg-slate-600">7 hari</button>
                                <button type="button" data-days="14" onclick="setEditCustomDays(14)" class="px-3 py-2 text-sm rounded bg-blue-100 text-blue-700 hover:bg-blue-200 dark:bg-slate-700 dark:text-slate-200 dark:hover:bg-slate-600">14 hari</button>
                                <button type="button" data-days="21" onclick="setEditCustomDays(21)" class="px-3 py-2 text-sm rounded bg-blue-100 text-blue-700 hover:bg-blue-200 dark:bg-slate-700 dark:text-slate-200 dark:hover:bg-slate-600">21 hari</button>
                                <button type="button" data-days="30" onclick="setEditCustomDays(30)" class="px-3 py-2 text-sm rounded bg-blue-100 text-blue-700 hover:bg-blue-200 dark:bg-slate-700 dark:text-slate-200 dark:hover:bg-slate-600">30 hari</button>
                                <button type="button" data-days="45" onclick="setEditCustomDays(45)" class="px-3 py-2 text-sm rounded bg-blue-100 text-blue-700 hover:bg-blue-200 dark:bg-slate-700 dark:text-slate-200 dark:hover:bg-slate-600">45 hari</button>
                                <button type="button" data-days="60" onclick="setEditCustomDays(60)" class="px-3 py-2 text-sm rounded bg-blue-100 text-blue-700 hover:bg-blue-200 dark:bg-slate-700 dark:text-slate-200 dark:hover:bg-slate-600">60 hari</button>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3 pt-4">
                        <button type="button" onclick="closeModal()" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 dark:border-slate-600 dark:text-slate-200 dark:hover:bg-slate-700/50">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 bg-orange-600 text-white rounded-md hover:bg-orange-700 dark:bg-orange-500 dark:hover:bg-orange-400">
                            Update Deadline
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</div>

@push('scripts')
<script>
function jatuhTempoDashboard(month, year, allYears) {
    return {
        allYears: allYears,
        yearModalOpen: false,
        filters: { 
            month: String(month), 
            year: String(year),
            status: @json(request('status') ?? '')
        },
        init() {
            // Initialization if needed
        },
        filterUrl() {
            const p = new URLSearchParams();
            p.set('month', this.filters.month);
            p.set('year', this.filters.year);
            if (this.filters.status) {
                p.set('status', this.filters.status);
            }
            return `{{ route('jatuh-tempo.index') }}?` + p.toString();
        },
        applyFilters() { 
            window.location = this.filterUrl(); 
        },
        openYearModal() { 
            this.yearModalOpen = true; 
        },
        closeYearModal() { 
            this.yearModalOpen = false; 
        },
        selectYear(year) {
            this.filters.year = String(year);
            this.closeYearModal();
            this.applyFilters();
        }
    }
}

function openCreateModal() {
    document.getElementById('modalTitle').textContent = 'Tambah Jatuh Tempo';
    document.getElementById('jatuhTempoForm').action = '{{ route("jatuh-tempo.store") }}';
    document.getElementById('methodField').innerHTML = '';
    document.getElementById('jatuhTempoForm').reset();
    document.getElementById('jatuhTempoModal').classList.remove('hidden');
    setupAutoPlusMonth();
}

function editJatuhTempo(id) {
    document.getElementById('modalTitle').textContent = 'Edit Deadline';
    
    // Hide create form, show edit deadline form
    document.getElementById('jatuhTempoForm').style.display = 'none';
    document.getElementById('editDeadlineForm').style.display = 'block';
    
    document.getElementById('editDeadlineForm').action = `/jatuh-tempo/${id}/update-deadline`;
    document.getElementById('editMethodField').innerHTML = '@method("PUT")';
    
    // Fetch data untuk edit
    fetch(`/jatuh-tempo/${id}/edit`, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                const jt = data.data;
                // Display info (read-only)
                document.getElementById('edit_display_no_invoice').textContent = jt.no_invoice || '';
                document.getElementById('edit_display_customer').textContent = jt.customer || '';
                document.getElementById('edit_display_tanggal_invoice').textContent = formatDate(jt.tanggal_invoice) || '';
                document.getElementById('edit_display_current_deadline').textContent = formatDate(jt.tanggal_jatuh_tempo) || '';
                
                // Editable deadline
                document.getElementById('edit_tanggal_jatuh_tempo').value = jt.tanggal_jatuh_tempo || '';
                
                // Store invoice date for calculations
                window.editInvoiceDate = jt.tanggal_invoice;

                // Highlight quick term based on current difference (if it matches preset terms)
                try {
                    if (jt.tanggal_invoice && jt.tanggal_jatuh_tempo) {
                        const inv = new Date(jt.tanggal_invoice);
                        const due = new Date(jt.tanggal_jatuh_tempo);
                        const diffMs = due - inv;
                        const diffDays = Math.round(diffMs / (1000 * 60 * 60 * 24));
                        updateEditTermButtons(diffDays);
                    } else {
                        updateEditTermButtons(null);
                    }
                } catch (e) {
                    updateEditTermButtons(null);
                }
            }
        })
        .catch(error => {
            console.error('Error fetching data:', error);
            alert('Gagal mengambil data untuk edit');
        });
    
    document.getElementById('jatuhTempoModal').classList.remove('hidden');
}

function closeModal() {
    document.getElementById('jatuhTempoModal').classList.add('hidden');
    // Reset forms visibility
    document.getElementById('jatuhTempoForm').style.display = 'block';
    document.getElementById('editDeadlineForm').style.display = 'none';
}

function setupAutoPlusMonth() {
    const autoCb = document.getElementById('auto_plus_month');
    const tglInv = document.getElementById('tanggal_invoice');
    const tglJt = document.getElementById('tanggal_jatuh_tempo');
    if (!autoCb || !tglInv || !tglJt) return;

    const applyState = () => {
        if (autoCb.checked && tglInv.value) {
            const invDate = new Date(tglInv.value);
            invDate.setMonth(invDate.getMonth() + 1);
            const yyyy = invDate.getFullYear();
            const mm = String(invDate.getMonth() + 1).padStart(2, '0');
            const dd = String(invDate.getDate()).padStart(2, '0');
            tglJt.value = `${yyyy}-${mm}-${dd}`;
            tglJt.setAttribute('disabled', 'disabled');
        } else {
            tglJt.removeAttribute('disabled');
        }
    };
    autoCb.onchange = applyState;
    tglInv.onchange = applyState;
    applyState();
}

    // Function to set custom deadline based on days from invoice date
    function setCustomDeadline(days) {
        const tglInv = document.getElementById('tanggal_invoice');
        const tglJt = document.getElementById('tanggal_jatuh_tempo');
        
        if (!tglInv.value) {
            alert('Pilih tanggal invoice terlebih dahulu');
            return;
        }
        
        const invDate = new Date(tglInv.value);
        invDate.setDate(invDate.getDate() + days);
        
        const yyyy = invDate.getFullYear();
        const mm = String(invDate.getMonth() + 1).padStart(2, '0');
        const dd = String(invDate.getDate()).padStart(2, '0');
        
        tglJt.value = `${yyyy}-${mm}-${dd}`;
        tglJt.removeAttribute('disabled');
    }

    // Function to auto-populate deadline based on customer payment terms
    function autoPopulateDeadline() {
        const customerSelect = document.getElementById('customer');
        const invoiceDate = document.getElementById('tanggal_invoice').value;
        const deadlineInput = document.getElementById('tanggal_jatuh_tempo');
        const termsInfo = document.getElementById('customer-terms-info');
        const termsText = document.getElementById('terms-text');
        
        if (customerSelect.value) {
            const selectedOption = customerSelect.options[customerSelect.selectedIndex];
            const paymentTerms = parseInt(selectedOption.getAttribute('data-payment-terms')) || 30;
            
            // Show customer terms info
            termsText.textContent = `Default payment terms: ${paymentTerms} hari`;
            termsInfo.classList.remove('hidden');
            
            if (invoiceDate) {
                const deadline = new Date(invoiceDate);
                deadline.setDate(deadline.getDate() + paymentTerms);
                
                const yyyy = deadline.getFullYear();
                const mm = String(deadline.getMonth() + 1).padStart(2, '0');
                const dd = String(deadline.getDate()).padStart(2, '0');
                
                deadlineInput.value = `${yyyy}-${mm}-${dd}`;
                
                // Visual feedback
                deadlineInput.style.backgroundColor = '#f0fdf4';
                deadlineInput.style.borderColor = '#22c55e';
                setTimeout(() => {
                    deadlineInput.style.backgroundColor = '';
                    deadlineInput.style.borderColor = '';
                }, 2000);
                
                // Update terms info with calculated date
                termsText.textContent = `Auto-calculated: ${paymentTerms} hari dari tanggal invoice`;
            }
        } else {
            // Hide terms info when no customer selected
            termsInfo.classList.add('hidden');
        }
    }

    // Event listeners for auto-populate functionality
    document.addEventListener('DOMContentLoaded', function() {
        const customerSelect = document.getElementById('customer');
        const invoiceDateInput = document.getElementById('tanggal_invoice');
        
        if (customerSelect) {
            customerSelect.addEventListener('change', autoPopulateDeadline);
        }
        if (invoiceDateInput) {
            invoiceDateInput.addEventListener('change', autoPopulateDeadline);
        }
    });

// Function untuk set custom payment terms (Edit form)
function setEditCustomDays(days) {
    if (!window.editInvoiceDate) {
        alert('Data tanggal invoice tidak tersedia');
        return;
    }
    
    // Calculate new due date from stored invoice date
    const invDate = new Date(window.editInvoiceDate);
    invDate.setDate(invDate.getDate() + days);
    
    const yyyy = invDate.getFullYear();
    const mm = String(invDate.getMonth() + 1).padStart(2, '0');
    const dd = String(invDate.getDate()).padStart(2, '0');
    
    document.getElementById('edit_tanggal_jatuh_tempo').value = `${yyyy}-${mm}-${dd}`;

    // Highlight selected term button
    updateEditTermButtons(days);
}

// Toggle highlight untuk tombol Quick Terms pada form Edit
function updateEditTermButtons(selectedDays) {
    const container = document.getElementById('edit_terms_buttons');
    if (!container) return;
    const buttons = container.querySelectorAll('button[data-days]');
    buttons.forEach(btn => {
        // reset ke style biru
        btn.classList.remove('bg-green-100', 'text-green-700', 'hover:bg-green-200');
        btn.classList.remove('ring-2', 'ring-green-300');
        if (!btn.classList.contains('bg-blue-100')) {
            btn.classList.add('bg-blue-100');
        }
        if (!btn.classList.contains('text-blue-700')) {
            btn.classList.add('text-blue-700');
        }
        if (!btn.classList.contains('hover:bg-blue-200')) {
            btn.classList.add('hover:bg-blue-200');
        }
    });

    if (selectedDays == null) return;
    const active = container.querySelector(`button[data-days="${selectedDays}"]`);
    if (active) {
        // set ke style hijau untuk yang aktif
        active.classList.remove('bg-blue-100', 'text-blue-700', 'hover:bg-blue-200');
        active.classList.add('bg-green-100', 'text-green-700', 'hover:bg-green-200', 'ring-2', 'ring-green-300');
    }
}

// Helper function to format date
function formatDate(dateString) {
    if (!dateString) return '';
    const date = new Date(dateString);
    return date.toLocaleDateString('id-ID');
}

// Fungsi untuk toggle status antara Pending dan Accept
function toggleStatus(id, currentStatus) {
    // currentStatus adalah nilai DB ('Lunas', 'Belum Bayar', 'Sebagian')
    // Tentukan status UI baru: jika DB='Lunas' => UI='Pending', selain itu => UI='Accept'
    const newStatus = currentStatus === 'Lunas' ? 'Pending' : 'Accept';
    
    // Tambahkan loading state pada button
    const button = event.target.closest('button');
    const originalContent = button.innerHTML;
    button.disabled = true;
    button.innerHTML = `
        <div class="flex items-center space-x-1.5">
            <svg class="w-3 h-3 animate-spin" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd"></path>
            </svg>
            <span>Loading...</span>
        </div>
    `;
    
    // Kirim request AJAX untuk update status
    fetch(`/jatuh-tempo/${id}/update-status`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            status_pembayaran: newStatus
        })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            // Reload halaman untuk menampilkan perubahan dengan animasi smooth
            window.location.reload();
        } else {
            // Restore button state jika gagal
            button.disabled = false;
            button.innerHTML = originalContent;
            alert('Gagal mengupdate status: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        // Restore button state jika error
        button.disabled = false;
        button.innerHTML = originalContent;
        
        // Tampilkan pesan error yang lebih informatif
        if (error.message.includes('404')) {
            alert('Route tidak ditemukan. Pastikan route sudah terdaftar dengan benar.');
        } else if (error.message.includes('500')) {
            alert('Terjadi kesalahan server. Periksa log aplikasi.');
        } else {
            alert('Terjadi kesalahan saat mengupdate status: ' + error.message);
        }
    });
}
</script>
@endpush
@endsection
