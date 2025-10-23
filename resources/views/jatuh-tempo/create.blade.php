@extends('layouts.app')

@section('title', 'Tambah Jatuh Tempo')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-3xl mx-auto">
        <div class="bg-white dark:bg-slate-800 rounded-lg shadow-lg p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Tambah Jatuh Tempo</h2>
                <a href="{{ route('jatuh-tempo.index') }}" class="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </a>
            </div>

            <form method="POST" action="{{ route('jatuh-tempo.store') }}" class="space-y-4">
                @csrf

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">No Invoice</label>
                        <input type="text" name="no_invoice" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500 dark:bg-slate-900 dark:border-slate-600 dark:text-slate-100">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">No PO</label>
                        <input type="text" name="no_po" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500 dark:bg-slate-900 dark:border-slate-600 dark:text-slate-100">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Customer</label>
                    <input type="text" name="customer" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500 dark:bg-slate-900 dark:border-slate-600 dark:text-slate-100">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tanggal Invoice</label>
                        <input type="date" name="tanggal_invoice" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500 dark:bg-slate-900 dark:border-slate-600 dark:text-slate-100">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tanggal Jatuh Tempo</label>
                        <input type="date" name="tanggal_jatuh_tempo" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500 dark:bg-slate-900 dark:border-slate-600 dark:text-slate-100">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Jumlah Tagihan</label>
                        <input type="number" name="jumlah_tagihan" required min="0" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500 dark:bg-slate-900 dark:border-slate-600 dark:text-slate-100">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Jumlah Terbayar</label>
                        <input type="number" name="jumlah_terbayar" min="0" value="0" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500 dark:bg-slate-900 dark:border-slate-600 dark:text-slate-100">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status Pembayaran</label>
                    <select name="status_pembayaran" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500 dark:bg-slate-900 dark:border-slate-600 dark:text-slate-100">
                        <option value="Pending">Pending</option>
                        <option value="Accept">Accept</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status Approval</label>
                    <select name="status_approval" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500 dark:bg-slate-900 dark:border-slate-600 dark:text-slate-100">
                        <option value="Pending">Pending</option>
                        <option value="ACC">ACC</option>
                        <option value="Reject">Reject</option>
                    </select>
                </div>

                <div class="flex justify-end space-x-3 pt-4">
                    <a href="{{ route('jatuh-tempo.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 dark:border-slate-600 dark:text-slate-200 dark:hover:bg-slate-700/50">
                        Batal
                    </a>
                    <button type="submit" class="px-4 py-2 bg-orange-600 text-white rounded-md hover:bg-orange-700 dark:bg-orange-500 dark:hover:bg-orange-400">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
