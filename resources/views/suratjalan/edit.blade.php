@extends('layouts.app')
@php($hideSidebar = true)
@section('title', 'Edit Data PO')

@section('content')
<div class="min-h-screen bg-gray-100 py-6">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="bg-white shadow-md rounded-lg p-6 mb-6">
            <div class="flex justify-between items-center">
                <h1 class="text-2xl font-bold text-gray-800">Edit Data PO</h1>
                <div class="flex space-x-2">
                    <a href="{{ route('po.create', ['from' => 'invoice', 'po_number' => request('po_number')]) }}" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">
                        Kembali ke Form Input PO
                    </a>
                    <a href="{{ route('suratjalan.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                        Kembali ke Daftar
                    </a>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="bg-green-100 text-green-800 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-100 text-red-800 px-4 py-3 rounded mb-4">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Form Data PO -->
        <div class="bg-white shadow-md rounded-lg p-6">
            <form action="{{ route('suratjalan.update', $suratjalan->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <!-- Header Data PO -->
                <div class="border-b-2 border-gray-300 pb-4 mb-6">
                    <div class="grid grid-cols-2 gap-6">
                        <!-- Kiri -->
                        <div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">NOMOR PO</label>
                                <input type="text" name="no_surat_jalan" value="{{ old('no_surat_jalan', $suratjalan->no_surat_jalan) }}" 
                                       class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">NOMOR PURCHASE ORDER</label>
                                <input type="text" name="no_po" value="{{ old('no_po', $suratjalan->no_po) }}" 
                                       class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>
                        
                        <!-- Kanan -->
                        <div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">TANGGAL</label>
                                <input type="date" name="tanggal_po" value="{{ old('tanggal_po', $suratjalan->tanggal_po) }}" 
                                       class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">KEPADA YTH.</label>
                                <input type="text" name="customer" value="{{ old('customer', $suratjalan->customer) }}" 
                                       class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Informasi Kendaraan -->
                <div class="border-b-2 border-gray-300 pb-4 mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Informasi Kendaraan</h3>
                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Kendaraan</label>
                            <select name="kendaraan" class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">-- Pilih Kendaraan --</option>
                                @foreach($kendaraans as $kendaraan)
                                    <option value="{{ $kendaraan->nama_kendaraan }}" 
                                            @if(old('kendaraan', $suratjalan->kendaraan) == $kendaraan->nama_kendaraan) selected @endif>
                                        {{ $kendaraan->nama_kendaraan }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">No. Polisi</label>
                            <input type="text" name="no_polisi" value="{{ old('no_polisi', $suratjalan->no_polisi) }}" 
                                   class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                </div>

                <!-- Detail Barang -->
                <div class="border-b-2 border-gray-300 pb-4 mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Detail Barang</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full border border-gray-300">
                            <thead>
                                <tr class="bg-gray-100">
                                    <th class="border border-gray-300 px-4 py-2 text-left">QTY</th>
                                    <th class="border border-gray-300 px-4 py-2 text-left">NAMA BARANG</th>
                                    <th class="border border-gray-300 px-4 py-2 text-left">HARGA</th>
                                    <th class="border border-gray-300 px-4 py-2 text-left">TOTAL</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="border border-gray-300 px-4 py-2">
                                        <div class="flex gap-2">
                                            <input type="number" name="qty" value="{{ old('qty', $suratjalan->qty) }}" 
                                                   class="w-20 border border-gray-300 rounded px-2 py-1 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                            <select name="qty_jenis" class="border border-gray-300 rounded px-2 py-1 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                                <option value="PCS" @if(old('qty_jenis', $suratjalan->qty_jenis) == 'PCS') selected @endif>PCS</option>
                                                <option value="SET" @if(old('qty_jenis', $suratjalan->qty_jenis) == 'SET') selected @endif>SET</option>
                                            </select>
                                        </div>
                                    </td>
                                    <td class="border border-gray-300 px-4 py-2">
                                        <select name="produk_id" class="w-full border border-gray-300 rounded px-2 py-1 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                            <option value="">-- Pilih Produk --</option>
                                            @foreach($produks as $produk)
                                                <option value="{{ $produk->id }}" 
                                                        @if(old('produk_id', $suratjalan->produk_id) == $produk->id) selected @endif>
                                                    {{ $produk->nama_produk }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="border border-gray-300 px-4 py-2">
                                        <input type="number" name="harga" value="{{ old('harga', $suratjalan->harga) }}" 
                                               class="w-full border border-gray-300 rounded px-2 py-1 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    </td>
                                    <td class="border border-gray-300 px-4 py-2">
                                        <input type="number" name="total" value="{{ old('total', $suratjalan->total) }}" 
                                               class="w-full border border-gray-300 rounded px-2 py-1 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Tombol Aksi -->
                <div class="flex justify-end space-x-4">
                    <button type="button" onclick="window.location.href='{{ route('suratjalan.index') }}'" 
                            class="bg-gray-500 text-white px-6 py-2 rounded hover:bg-gray-600">
                        Batal
                    </button>
                    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const qtyInput = document.querySelector('input[name="qty"]');
    const hargaInput = document.querySelector('input[name="harga"]');
    const totalInput = document.querySelector('input[name="total"]');
    const produkSelect = document.querySelector('select[name="produk_id"]');

    // Auto-calculate total when qty or harga changes
    function calculateTotal() {
        const qty = parseFloat(qtyInput.value) || 0;
        const harga = parseFloat(hargaInput.value) || 0;
        totalInput.value = qty * harga;
    }

    qtyInput.addEventListener('input', calculateTotal);
    hargaInput.addEventListener('input', calculateTotal);

    // Auto-fill harga when produk is selected
    produkSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        if (selectedOption.value) {
            // You can add AJAX call here to get harga from server
            // For now, we'll just trigger calculation
            calculateTotal();
        }
    });
});
</script>
@endsection
