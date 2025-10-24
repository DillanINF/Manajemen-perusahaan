@extends('layouts.app')

@section('content')
<div class="w-full px-2 sm:px-6 lg:px-8 py-4 sm:py-6 space-y-5">
    <!-- Header -->
    <div class="bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-700 rounded-2xl p-5 flex items-center justify-between shadow">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-indigo-100 dark:bg-indigo-900/40 flex items-center justify-center">
                <svg class="w-5 h-5 text-indigo-700 dark:text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5m0 0l5-5m-5 5V4"/></svg>
            </div>
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-slate-100">Data Barang Masuk</h1>
                <p class="text-gray-500 dark:text-slate-400 text-sm">Riwayat stok masuk</p>
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
                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">Nama Barang</label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M10 18a8 8 0 100-16 8 8 0 000 16z"/></svg>
                    </span>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama barang..." class="w-full pl-9 pr-3 py-2 rounded-xl border border-gray-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">Tanggal Masuk</label>
                <input type="date" name="date" value="{{ request('date') }}" class="w-full px-3 py-2 rounded-xl border border-gray-300 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-indigo-500">
            </div>
            <div class="flex items-end gap-2">
                <button class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl">Filter</button>
                <a href="{{ route('barang.masuk.index') }}" class="px-4 py-2 bg-gray-100 dark:bg-slate-700 text-gray-700 dark:text-slate-200 rounded-xl">Reset</a>
            </div>
        </div>
    </form>

    <!-- Table -->
    <div class="bg-white dark:bg-slate-900 border border-gray-200 dark:border-slate-700 rounded-2xl overflow-hidden shadow">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-indigo-50 dark:bg-indigo-900/20 text-indigo-800 dark:text-indigo-200">
                    <tr>
                        <th class="px-4 sm:px-6 py-3 text-left text-xs sm:text-sm font-semibold">Tanggal</th>
                        <th class="px-4 sm:px-6 py-3 text-left text-xs sm:text-sm font-semibold">Barang</th>
                        <th class="px-4 sm:px-6 py-3 text-left text-xs sm:text-sm font-semibold">Qty</th>
                        <th class="px-4 sm:px-6 py-3 text-right text-xs sm:text-sm font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-slate-800">
                    @forelse ($items as $row)
                        <tr class="hover:bg-gray-50 dark:hover:bg-slate-800">
                            <td class="px-4 sm:px-6 py-3 whitespace-nowrap text-gray-800 dark:text-slate-200">{{ \Carbon\Carbon::parse($row->tanggal)->format('d/m/Y') }}</td>
                            <td class="px-4 sm:px-6 py-3 text-gray-800 dark:text-slate-200">{{ $row->produk->nama_produk ?? '-' }}</td>
                            <td class="px-4 sm:px-6 py-3">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs sm:text-sm font-semibold bg-indigo-100 text-indigo-700 dark:bg-indigo-900/50 dark:text-indigo-200">{{ number_format($row->qty, 0, ',', '.') }}</span>
                            </td>
                            <td class="px-4 sm:px-6 py-3">
                                <div class="flex justify-end">
                                    <form action="{{ route('barang.masuk.destroy', $row) }}" method="POST" onsubmit="return confirm('Hapus data ini?');" class="inline">
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
                            <td colspan="4" class="px-4 sm:px-6 py-10 text-center text-gray-500 dark:text-slate-400">
                                <div class="flex flex-col items-center gap-2">
                                    <div class="w-12 h-12 rounded-full bg-gray-100 dark:bg-slate-800 flex items-center justify-center">
                                        <svg class="w-6 h-6 text-gray-400 dark:text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                    </div>
                                    <div>Belum ada data.</div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Footer: pagination -->
        @if ($items->hasPages())
        <div class="px-4 sm:px-6 py-3 border-t border-gray-100 dark:border-slate-800">
            {{ $items->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
