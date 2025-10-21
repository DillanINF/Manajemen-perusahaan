@extends('layouts.app')
@section('title', 'PO Belum Terkirim')

@push('styles')
<!-- Optional modern font -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
  :root { --brand-blue: #2563EB; }
  .fade-in { animation: fadeIn 220ms ease-out; }
  @keyframes fadeIn { from { opacity: 0; transform: translateY(6px); } to { opacity: 1; transform: translateY(0); } }
  .bump { animation: bump 260ms ease-out; }
  @keyframes bump { 0% { transform: scale(1); } 50% { transform: scale(1.025); } 100% { transform: scale(1); } }
  .font-inter { font-family: 'Inter', ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, 'Apple Color Emoji','Segoe UI Emoji'; }
  .status-badge { @apply inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium; }
  .status-warning { @apply bg-yellow-100 text-yellow-800 border border-yellow-200; }
  .status-danger { @apply bg-red-100 text-red-800 border border-red-200; }
  .table-row-hover { @apply hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors duration-150; }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-transparent py-4 sm:py-8 font-inter">
    <div class="max-w-7xl mx-auto px-2 sm:px-4">
        <!-- Header Section -->
        <div class="bg-white/95 dark:bg-white/5 backdrop-blur-sm border border-gray-200 dark:border-white/10 rounded-xl shadow-lg p-4 sm:p-6 mb-4 sm:mb-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-800 dark:text-gray-100">
                        <i class="fas fa-exclamation-triangle text-yellow-600 mr-2"></i>PO Belum Terkirim
                    </h1>
                    <p class="text-gray-600 dark:text-gray-300 mt-1 text-sm sm:text-base">
                        Monitoring barang yang belum terinput lengkap ke data PO
                    </p>
                </div>
                <!-- Real-time clock -->
                <div class="text-left sm:text-right">
                    <div class="text-sm text-gray-500 dark:text-gray-400" id="current-date">{{ date('d M Y') }}</div>
                    <div class="text-xs text-gray-400 dark:text-gray-500" id="current-time">{{ date('H:i') }} WIB</div>
                </div>
            </div>
        </div>

        <!-- Action Bar: Back + Export -->
        <div class="mb-4">
            <div class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-gradient-to-r from-white to-blue-50/60 dark:from-slate-900/60 dark:to-slate-800/60 shadow-sm p-3 sm:p-4">
                <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-300">
                        <i class="fa-solid fa-circle-info text-blue-600"></i>
                        <span>Data menampilkan barang yang permintaan PO melebihi stok tersedia.</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="bg-white/95 dark:bg-white/5 backdrop-blur-sm border border-gray-200 dark:border-white/10 rounded-xl shadow-lg p-4 sm:p-6 mb-4 sm:mb-6">
            <form method="GET" action="{{ route('sisa-data-po.index') }}" class="space-y-4">
                <div class="flex flex-col sm:flex-row gap-4">
                    <!-- Filter Customer (Autocomplete kustom, tanpa panah dropdown) -->
                    <div class="flex-1 relative">
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-2">
                            <i class="fas fa-user text-blue-500 mr-1"></i>Customer
                        </label>
                        <input type="text" name="customer" id="customerSearch" value="{{ $customer ?? '' }}" 
                               placeholder="Ketik nama customer..." 
                               autocomplete="off"
                               class="w-full border-2 border-gray-200 dark:border-gray-700 rounded-lg px-3 py-2 text-sm bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-100 focus:border-blue-500 focus:ring-2 focus:ring-blue-200" />
                        <!-- Panel sugesti -->
                        <div id="customerSuggestions" 
                             class="hidden absolute z-20 mt-1 w-full bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-lg max-h-56 overflow-auto">
                            <!-- items diisi via JS -->
                        </div>
                    </div>
                    
                    <!-- Filter Produk -->
                    <div class="flex-1">
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-2">
                            <i class="fas fa-boxes text-green-500 mr-1"></i>Produk
                        </label>
                        <select name="produk_id" class="w-full border-2 border-gray-200 dark:border-gray-700 rounded-lg px-3 py-2 text-sm bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-100 focus:border-blue-500 focus:ring-2 focus:ring-blue-200">
                            <option value="">-- Semua Produk --</option>
                            @foreach($produks as $produk)
                                <option value="{{ $produk->id }}" @selected($produkId == $produk->id)>{{ $produk->nama_produk }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <!-- Filter Buttons -->
                    <div class="flex items-end gap-2">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-all duration-200 text-sm font-medium">
                            <i class="fas fa-search mr-1"></i>Filter
                        </button>
                        <a href="{{ route('sisa-data-po.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-all duration-200 text-sm font-medium">
                            <i class="fas fa-refresh mr-1"></i>Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <!-- Statistics Cards -->
        <div class="mb-6">
            <div class="bg-gradient-to-r from-yellow-400 to-orange-400 dark:from-yellow-600 dark:to-orange-600 rounded-xl p-6 shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-800 dark:text-white text-sm font-medium mb-1">Total Items</p>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $sisaData->total() }}</p>
                    </div>
                    <div class="bg-white/30 dark:bg-white/20 rounded-full p-4">
                        <i class="fas fa-exclamation-triangle text-2xl text-gray-800 dark:text-white"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Data Table -->
        <div class="bg-white/95 dark:bg-white/5 backdrop-blur-sm border border-gray-200 dark:border-white/10 rounded-xl shadow-lg overflow-hidden">
            @if($sisaData->count() > 0)
                <!-- Desktop Table -->
                <div class="hidden md:block overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-100 dark:bg-gray-800/80">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Produk</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">No PO</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Customer</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Tipe Harga</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Sisa PO</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($sisaData as $item)
                                <tr class="table-row-hover">
                                    <td class="px-4 py-4">
                                        <div class="flex items-center">
                                            <div class="bg-green-100 dark:bg-green-900/30 rounded-lg p-2 mr-3">
                                                <i class="fas fa-boxes text-green-600 dark:text-green-400"></i>
                                            </div>
                                            <div>
                                                <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $item->nama_produk }}</div>
                                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ strtoupper($item->satuan ?? 'PCS') }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="bg-blue-100 dark:bg-blue-900/30 rounded-lg p-2 mr-3">
                                                <i class="fas fa-file-invoice text-blue-600 dark:text-blue-400"></i>
                                            </div>
                                            <div>
                                                <div class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $item->no_po }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4">
                                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $item->customer ?? '-' }}</div>
                                    </td>
                                    <td class="px-4 py-4 text-center">
                                        @if($item->tipe_harga == 'Berbayar')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">
                                                <i class="fas fa-money-bill mr-1"></i>Berbayar
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400">
                                                <i class="fas fa-gift mr-1"></i>Gratis
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-4 text-center">
                                        <span class="text-sm font-bold text-red-600 dark:text-red-400">{{ number_format($item->sisa_belum_terinput) }}</span>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">dari {{ number_format($item->total_qty_po) }}</div>
                                    </td>
                                    <td class="px-4 py-4 text-center">
                                        <div class="flex items-center justify-center space-x-2">
                                            <x-table.action-buttons 
                                                onEdit="openSisaEditModal(this)"
                                                :editPayload="[
                                                    'id' => $item->sisa_id,
                                                    'produk_id' => (int) $item->produk_id,
                                                    'no_po' => $item->no_po,
                                                    'customer' => $item->customer,
                                                    'nama_produk' => $item->nama_produk,
                                                    'satuan' => $item->satuan,
                                                    'qty_po' => (int) $item->total_qty_po,
                                                    'sisa_stok' => (int) $item->sisa_stok,
                                                    'qty_sisa' => (int) $item->sisa_belum_terinput,
                                                    'harga' => (int) $item->harga,
                                                ]"
                                                deleteAction="{{ route('sisa-data-po.destroy', $item->sisa_id) }}"
                                                confirmText="Apakah Anda yakin ingin menghapus sisa data PO ini?"
                                            />
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Cards -->
                <div class="md:hidden space-y-4 p-4">
                    @foreach($sisaData as $item)
                        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4 shadow-sm">
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex items-center">
                                    <div class="bg-green-100 dark:bg-green-900/30 rounded-lg p-2 mr-3">
                                        <i class="fas fa-boxes text-green-600 dark:text-green-400"></i>
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $item->nama_produk }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ strtoupper($item->satuan ?? 'PCS') }}</div>
                                    </div>
                                </div>
                                @if($item->tipe_harga == 'Berbayar')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">
                                        <i class="fas fa-money-bill mr-1"></i>Berbayar
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400">
                                        <i class="fas fa-gift mr-1"></i>Gratis
                                    </span>
                                @endif
                            </div>
                            
                            <div class="space-y-2">
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">No PO:</span>
                                    <span class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $item->no_po }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Customer:</span>
                                    <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $item->customer ?? '-' }}</span>
                                </div>
                                <div class="flex justify-between border-t border-gray-200 dark:border-gray-700 pt-2">
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Sisa PO:</span>
                                    <div class="text-right">
                                        <span class="text-sm font-bold text-red-600 dark:text-red-400">{{ number_format($item->sisa_belum_terinput) }}</span>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">dari {{ number_format($item->total_qty_po) }}</div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Action Buttons -->
                            <div class="flex items-center justify-center space-x-2 mt-4 pt-3 border-t border-gray-200 dark:border-gray-700">
                                <x-table.action-buttons 
                                    onEdit="openSisaEditModal(this)"
                                    :editPayload="[
                                        'id' => $item->sisa_id,
                                        'produk_id' => (int) $item->produk_id,
                                        'no_po' => $item->no_po,
                                        'customer' => $item->customer,
                                        'nama_produk' => $item->nama_produk,
                                        'satuan' => $item->satuan,
                                        'qty_po' => (int) $item->total_qty_po,
                                        'sisa_stok' => (int) $item->sisa_stok,
                                        'qty_sisa' => (int) $item->sisa_belum_terinput,
                                        'harga' => (int) $item->harga,
                                    ]"
                                    deleteAction="{{ route('sisa-data-po.destroy', $item->sisa_id) }}"
                                    confirmText="Apakah Anda yakin ingin menghapus sisa data PO ini?"
                                />
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">
                    {{ $sisaData->links() }}
                </div>
            @else
                <!-- Empty State -->
                <div class="text-center py-12">
                    <div class="bg-green-100 dark:bg-green-900/30 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-check-circle text-2xl text-green-600 dark:text-green-400"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2" style="font-style: normal;">Tidak Ada Sisa Data</h3>
                    <p class="text-gray-600 dark:text-gray-400" style="font-style: normal;">
                        Semua barang dalam PO sudah terinput dengan lengkap atau stok mencukupi.
                    </p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal Edit Sisa Data PO -->
<div id="sisaEditOverlay" class="fixed inset-0 z-50 hidden flex items-center justify-center">
  <div class="absolute inset-0 bg-black/40" onclick="closeSisaEditModal()"></div>
  <div class="relative z-10 max-w-2xl w-[92vw] sm:w-[640px] mx-auto bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-2xl overflow-hidden">
    <div class="px-4 sm:px-6 py-3 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
      <h3 class="text-base sm:text-lg font-semibold text-gray-900 dark:text-white">Edit Sisa Data PO</h3>
      <button type="button" class="w-9 h-9 inline-flex items-center justify-center rounded-full hover:bg-gray-100 dark:hover:bg-gray-700" onclick="closeSisaEditModal()">
        <i class="fas fa-times text-gray-500"></i>
      </button>
    </div>
    <form id="sisaEditForm" method="POST" action="#">
      @csrf
      @method('PUT')
      <input type="hidden" name="_id" id="sisa_edit_id">
      <div class="p-4 sm:p-6 space-y-4">
        <div class="grid grid-cols-1 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Produk</label>
            <select name="produk_id" id="sisa_edit_produk_id" class="w-full px-3 py-2 rounded-lg border-2 border-gray-300 dark:border-gray-600 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-500/30 bg-white dark:bg-gray-800 text-gray-900 dark:text-white" required>
              <option value="">-- Pilih Produk --</option>
              @foreach($produks as $p)
                <option value="{{ $p->id }}">{{ $p->nama_produk }}</option>
              @endforeach
            </select>
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"><span class="text-red-600">*</span> Sisa PO</label>
            <input name="qty_sisa" id="sisa_edit_qty_sisa" type="number" min="1" required class="w-full px-3 py-2 rounded-lg border-2 border-gray-300 dark:border-gray-600 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-500/30 bg-white dark:bg-gray-800 text-gray-900 dark:text-white">
          </div>
        </div>
      </div>
      <div class="px-4 sm:px-6 py-3 border-t border-gray-200 dark:border-gray-700 flex items-center justify-end gap-2">
        <button type="button" class="inline-flex items-center justify-center px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600" onclick="closeSisaEditModal()">
          <i class="fas fa-times mr-2"></i>Batal
        </button>
        <button type="submit" class="inline-flex items-center justify-center px-4 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white">
          <i class="fas fa-save mr-2"></i>Simpan Perubahan
        </button>
      </div>
    </form>
  </div>
  <template id="sisaEditRouteBase" data-base="{{ url('/sisa-data-po') }}"></template>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // Real-time clock
    function updateClock() {
        const now = new Date();
        const dateOptions = { day: '2-digit', month: 'short', year: 'numeric' };
        const timeOptions = { hour: '2-digit', minute: '2-digit', hour12: false };
        
        const currentDate = now.toLocaleDateString('id-ID', dateOptions);
        const currentTime = now.toLocaleTimeString('id-ID', timeOptions);
        
        document.getElementById('current-date').textContent = currentDate;
        document.getElementById('current-time').textContent = currentTime + ' WIB';
    }
    
    // Update clock immediately and then every second
    updateClock();
    setInterval(updateClock, 1000);
    
    // Add fade-in animation to table rows
    const tableRows = document.querySelectorAll('tbody tr, .md\\:hidden > div');
    tableRows.forEach((row, index) => {
        row.style.animationDelay = `${index * 50}ms`;
        row.classList.add('fade-in');
    });

    // Autocomplete kustom untuk input Customer
    const customerInput = document.getElementById('customerSearch');
    const suggestionBox = document.getElementById('customerSuggestions');
    const customersData = @json($customers ?? []);

    function renderSuggestions(items) {
        if (!suggestionBox) return;
        suggestionBox.innerHTML = '';
        if (!items || items.length === 0) {
            suggestionBox.classList.add('hidden');
            return;
        }
        items.slice(0, 20).forEach((name, idx) => {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'w-full text-left px-3 py-2 text-sm hover:bg-blue-50 dark:hover:bg-slate-700 focus:bg-blue-100 dark:focus:bg-slate-700';
            btn.textContent = name;
            btn.setAttribute('data-value', name);
            btn.addEventListener('click', () => {
                customerInput.value = name;
                suggestionBox.classList.add('hidden');
                // Submit form otomatis setelah memilih
                customerInput.form?.submit();
            });
            suggestionBox.appendChild(btn);
        });
        suggestionBox.classList.remove('hidden');
    }

    function filterCustomers(query) {
        const q = (query || '').toLowerCase().trim();
        if (!q) return [];
        return customersData.filter(c => (c || '').toLowerCase().includes(q));
    }

    if (customerInput && suggestionBox) {
        customerInput.addEventListener('input', (e) => {
            const val = e.target.value;
            const results = filterCustomers(val);
            if (val && results.length) {
                renderSuggestions(results);
            } else {
                suggestionBox.classList.add('hidden');
            }
        });

        customerInput.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                suggestionBox.classList.add('hidden');
                return;
            }
            if (e.key === 'Enter') {
                // Enter akan submit form, biarkan default
                suggestionBox.classList.add('hidden');
            }
        });

        document.addEventListener('click', (e) => {
            if (!suggestionBox.contains(e.target) && e.target !== customerInput) {
                suggestionBox.classList.add('hidden');
            }
        });

        customerInput.addEventListener('focus', () => {
            const val = customerInput.value;
            const results = filterCustomers(val);
            if (val && results.length) {
                renderSuggestions(results);
            }
        });
    }
    // ===== Modal Edit Sisa Data PO =====
    window.openSisaEditModal = function(btn){
        try{
            const dataRaw = btn?.getAttribute('data-edit') || btn?.dataset?.edit || '{}';
            const data = JSON.parse(dataRaw);
            const overlay = document.getElementById('sisaEditOverlay');
            const base = document.getElementById('sisaEditRouteBase')?.dataset?.base || '';
            const form = document.getElementById('sisaEditForm');
            const id = data.id;
            form.action = base && id ? (base + '/' + id) : '#';
            document.getElementById('sisa_edit_id').value = id || '';
            const sel = document.getElementById('sisa_edit_produk_id');
            if (sel) {
              sel.value = (data.produk_id ?? '').toString();
              if (!sel.value) sel.selectedIndex = 0;
            }
            document.getElementById('sisa_edit_qty_sisa').value = data.qty_sisa ?? 1;
            overlay.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        }catch(e){ console.error('Gagal buka modal:', e); }
    }
    window.closeSisaEditModal = function(){
        const overlay = document.getElementById('sisaEditOverlay');
        overlay.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }
});
</script>
@endsection
