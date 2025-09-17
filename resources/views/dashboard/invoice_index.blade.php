@extends('layouts.app')

@section('title', 'Invoice Management')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-900">Invoice Management</h1>
        <button onclick="openCreateModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium">
            Buat Invoice Baru
        </button>
    </div>

    <!-- Success Message -->
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
            {{ session('success') }}
        </div>
    @endif

    <!-- Invoice Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto responsive-scroll">
            <table class="min-w-full min-w-[800px] divide-y divide-gray-200 table-compact">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="py-1 px-1.5 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No Invoice</th>
                        <th class="py-1 px-1.5 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No PO</th>
                        <th class="py-1 px-1.5 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                        <th class="py-1 px-1.5 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                        <th class="py-1 px-1.5 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jatuh Tempo</th>
                        <th class="py-1 px-1.5 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                        <th class="py-1 px-1.5 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="py-1 px-1.5 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-12 sm:w-20">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($invoices as $inv)
                        <tr class="hover:bg-gray-50">
                            <td class="py-1 px-1.5 text-xs">{{ $inv->no_invoice }}</td>
                            <td class="py-1 px-1.5 text-xs">{{ $inv->no_po }}</td>
                            <td class="py-1 px-1.5 text-xs">{{ Str::limit($inv->customer, 15) }}</td>
                            <td class="py-1 px-1.5 text-xs">{{ $inv->tanggal_invoice->format('d/m/Y') }}</td>
                            <td class="py-1 px-1.5 text-xs">{{ $inv->tanggal_jatuh_tempo->format('d/m/Y') }}</td>
                            <td class="py-1 px-1.5 text-xs">Rp {{ number_format($inv->grand_total, 0, ',', '.') }}</td>
                            <td class="py-1 px-1.5 text-xs">
                                @php
                                    // Map Draft => Pending untuk tampilan UI
                                    $uiStatus = $inv->status === 'Draft' ? 'Pending' : $inv->status;
                                @endphp
                                <span class="px-1 py-0.5 text-xs rounded-full
                                    @if($uiStatus === 'Paid') bg-green-100 text-green-800
                                    @elseif($uiStatus === 'Sent') bg-blue-100 text-blue-800
                                    @elseif($uiStatus === 'Overdue') bg-red-100 text-red-800
                                    @elseif($uiStatus === 'Pending') bg-yellow-100 text-yellow-800
                                    @else bg-gray-100 text-gray-800 @endif">
                                    {{ $uiStatus }}
                                </span>
                            </td>
                            <td class="py-1 px-1.5 text-xs text-center">
                                <x-table.action-buttons 
                                    onEdit="editInvoice({{ $inv->id }})"
                                    deleteAction="{{ route('invoice.destroy', $inv) }}"
                                    confirmText="Yakin hapus invoice {{ $inv->no_invoice }}?"
                                    :forceFull="true"
                                />
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-4 px-6 text-center text-gray-500">Belum ada data invoice</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Create/Edit Modal -->
<div id="invoiceModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-screen overflow-y-auto">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 id="modalTitle" class="text-lg font-medium">Buat Invoice Baru</h3>
                    <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <form id="invoiceForm" method="POST" action="{{ route('invoice.store') }}" class="space-y-4" onsubmit="return handleInvoiceSubmit(event)">
                    @csrf
                    <div id="methodField"></div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">No Invoice</label>
                            <input type="text" name="no_invoice" id="no_invoice" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">No PO</label>
                            <input type="text" name="no_po" id="no_po" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Customer</label>
                        <input type="text" name="customer" id="customer" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Alamat 1</label>
                            <input type="text" name="alamat_1" id="alamat_1" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Alamat 2</label>
                            <input type="text" name="alamat_2" id="alamat_2" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Tanggal Invoice</label>
                            <input type="date" name="tanggal_invoice" id="tanggal_invoice" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Tanggal Jatuh Tempo</label>
                            <input type="date" name="tanggal_jatuh_tempo" id="tanggal_jatuh_tempo" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Produk</label>
                            <select name="produk_id" id="produk_id" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Pilih Produk</option>
                                @foreach($produks as $produk)
                                    <option value="{{ $produk->id }}">{{ $produk->nama_produk }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Qty</label>
                            <input type="number" name="qty" id="qty" required min="1" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Jenis</label>
                            <select name="qty_jenis" id="qty_jenis" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                <option value="PCS">PCS</option>
                                <option value="SET">SET</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Harga</label>
                            <input type="number" name="harga" id="harga" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Total</label>
                            <input type="number" name="total" id="total" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Pajak</label>
                            <input type="number" name="pajak" id="pajak" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Grand Total</label>
                            <input type="number" name="grand_total" id="grand_total" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Status</label>
                        <select name="status" id="status" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="Draft">Draft</option>
                            <option value="Sent">Sent</option>
                            <option value="Paid">Paid</option>
                            <option value="Overdue">Overdue</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Keterangan</label>
                        <textarea name="keterangan" id="keterangan" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"></textarea>
                    </div>

                    <div class="flex justify-end space-x-3 pt-4">
                        <button type="button" onclick="closeModal()" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function openCreateModal() {
    document.getElementById('modalTitle').textContent = 'Buat Invoice Baru';
    document.getElementById('invoiceForm').action = '{{ route("invoice.store") }}';
    document.getElementById('methodField').innerHTML = '';
    document.getElementById('invoiceForm').reset();
    document.getElementById('invoiceModal').classList.remove('hidden');
}

function editInvoice(id) {
    // Implementation for edit functionality
    document.getElementById('modalTitle').textContent = 'Edit Invoice';
    document.getElementById('invoiceForm').action = `/invoice/${id}`;
    document.getElementById('methodField').innerHTML = '@method("PUT")';
    document.getElementById('invoiceModal').classList.remove('hidden');
}

function closeModal() {
    document.getElementById('invoiceModal').classList.add('hidden');
}

async function handleInvoiceSubmit(e) {
    e.preventDefault();
    const form = e.target;
    const submitBtn = form.querySelector('button[type="submit"]');
    if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.textContent = 'Menyimpan...';
    }

    try {
        const resp = await fetch(form.action, {
            method: form.method || 'POST',
            body: new FormData(form),
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            redirect: 'follow'
        });

        // Apapun hasilnya, navigasi ulang ke halaman index agar tabel ter-update
        window.location.href = "{{ route('invoice.index') }}";
        return false;
    } catch (err) {
        // Fallback jika error jaringan
        window.location.reload();
        return false;
    } finally {
        if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.textContent = 'Simpan';
        }
    }
}
</script>
@endsection
