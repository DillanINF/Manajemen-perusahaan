@extends('layouts.app')

@section('content')
<div class="w-full px-2 sm:px-6 lg:px-8 py-4 sm:py-6 space-y-5">
    <!-- Header -->
    <div class="bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-700 rounded-2xl p-5 flex items-center justify-between shadow">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-rose-100 dark:bg-rose-900/40 flex items-center justify-center">
                <svg class="w-5 h-5 text-rose-700 dark:text-rose-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 8v-2a2 2 0 00-2-2H6a2 2 0 00-2 2v2m13 6l-5-5m0 0l-5 5m5-5v12"/></svg>
            </div>
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-slate-100">Laporan Barang Keluar</h1>
                <p class="text-gray-500 dark:text-slate-400 text-sm">Data otomatis dari PO (tanpa input manual)</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('produk.index') }}" class="inline-flex items-center px-3 py-2 rounded-lg bg-gray-100 dark:bg-slate-700 text-gray-700 dark:text-slate-200 hover:bg-gray-200 dark:hover:bg-slate-600">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Kembali ke Data Barang
            </a>
        </div>
    </div>

    <!-- Filter Bar -->
    <form method="GET" class="bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-700 rounded-2xl p-4 shadow">
        <div class="grid grid-cols-1 sm:grid-cols-5 gap-3">
            <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">Customer</label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M10 18a8 8 0 100-16 8 8 0 000 16z"/></svg>
                    </span>
                    <input type="text" name="customer" value="{{ $customer ?? '' }}" placeholder="Cari customer..." class="w-full pl-9 pr-3 py-2 rounded-xl border border-gray-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-rose-500">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">Tanggal Keluar</label>
                <input type="date" name="date" value="{{ $date ?? '' }}" class="w-full px-3 py-2 rounded-xl border border-gray-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-rose-500">
            </div>
            <div class="flex items-end gap-2">
                <button class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-xl">Filter</button>
                <a href="{{ route('barang.keluar.index') }}" class="px-4 py-2 bg-gray-100 dark:bg-slate-700 text-gray-700 dark:text-slate-200 rounded-xl">Reset</a>
            </div>
        </div>
    </form>

    <!-- Table -->
    <div class="bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-700 rounded-2xl overflow-hidden shadow">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-rose-50 dark:bg-rose-900/20 text-rose-800 dark:text-rose-200">
                    <tr>
                        <th class="px-4 sm:px-6 py-3 text-left text-xs sm:text-sm font-semibold">Tanggal</th>
                        <th class="px-4 sm:px-6 py-3 text-left text-xs sm:text-sm font-semibold">Customer</th>
                        <th class="px-4 sm:px-6 py-3 text-left text-xs sm:text-sm font-semibold">Barang</th>
                        <th class="px-4 sm:px-6 py-3 text-left text-xs sm:text-sm font-semibold">Qty</th>
                        <th class="px-4 sm:px-6 py-3 text-left text-xs sm:text-sm font-semibold">Keterangan</th>
                        <th class="px-4 sm:px-6 py-3 text-center text-xs sm:text-sm font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-slate-800">
                    @forelse ($items as $row)
                        <tr class="hover:bg-gray-50 dark:hover:bg-slate-800">
                            <td class="px-4 sm:px-6 py-3 whitespace-nowrap text-gray-800 dark:text-slate-200">{{ \Carbon\Carbon::parse($row->tanggal)->format('d/m/Y') }}</td>
                            <td class="px-4 sm:px-6 py-3">
                                <span class="inline-flex items-center gap-2">
                                    <span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span>
                                    <span class="text-gray-900 dark:text-slate-200 font-medium">{{ $row->customer_name ?? '-' }}</span>
                                </span>
                            </td>
                            <td class="px-4 sm:px-6 py-3 text-gray-800 dark:text-slate-200">{{ $row->produk->nama_produk ?? '-' }}</td>
                            <td class="px-4 sm:px-6 py-3">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs sm:text-sm font-semibold bg-rose-100 text-rose-700 dark:bg-rose-900/50 dark:text-rose-200">{{ number_format($row->qty, 0, ',', '.') }}</span>
                            </td>
                            <td class="px-4 sm:px-6 py-3 text-gray-600 dark:text-slate-300">{{ $row->keterangan }}</td>
                            <td class="px-4 sm:px-6 py-3">
                                <div class="flex justify-end">
                                    <form action="{{ route('barang.keluar.destroy', $row->id) }}" method="POST" onsubmit="return confirm('Hapus data ini?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 hover:text-red-700 transition-colors dark:bg-red-900/20 dark:text-red-400 dark:hover:bg-red-900/40" title="Hapus">
                                            <i class="fa-solid fa-trash text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 sm:px-6 py-10 text-center text-gray-500 dark:text-slate-400">
                                <div class="flex flex-col items-center gap-2">
                                    <div class="w-12 h-12 rounded-full bg-gray-100 dark:bg-slate-800 flex items-center justify-center">
                                        <svg class="w-6 h-6 text-gray-400 dark:text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                    </div>
                                    <div>Tidak ada data untuk filter yang dipilih.</div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Footer: pagination -->
        <div class="px-4 sm:px-6 py-3 border-t border-gray-100 dark:border-slate-800">
            {{ $items->links() }}
        </div>
    </div>
</div>
@endsection
