@extends('layouts.app')
@section('title', 'PURCHASE ORDER VENDOR')

@push('styles')
<style>
  .fade-in { animation: fadeIn 220ms ease-out; }
  @keyframes fadeIn { from { opacity: 0; transform: translateY(6px); } to { opacity: 1; transform: translateY(0); } }
  .bump { animation: bump 260ms ease-out; }
  @keyframes bump { 0% { transform: scale(1); } 50% { transform: scale(1.025); } 100% { transform: scale(1); } }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-transparent py-4 sm:py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-6 sm:mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center shadow-lg">
                        <i class="fa-solid fa-plus text-white text-lg sm:text-xl"></i>
                    </div>
                    <div>
                        <h1 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white">Input Purchase Order</h1>
                        <p class="text-sm text-gray-600 dark:text-gray-300">Kelola data purchase order dengan mudah</p>
                    </div>
                </div>
                <div class="text-left sm:text-right">
                    <div class="text-sm text-gray-500 dark:text-gray-400" id="current-date">{{ date('d M Y') }}</div>
                    <div class="text-xs text-gray-400 dark:text-gray-500" id="current-time">{{ date('H:i') }} WIB</div>
                </div>
            </div>
        </div>

        @if(session('success') && !session('split_messages'))
            <div class="bg-green-50 dark:bg-green-900/20 border-l-4 border-green-400 dark:border-green-700 p-4 mb-4 sm:mb-6 rounded-r-lg shadow-sm">
                <div class="flex items-start gap-3">
                    <div class="flex-shrink-0">
                        <i class="fas fa-check-circle text-green-500 dark:text-green-300 text-lg"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-green-800 dark:text-green-300 font-semibold">{{ session('success') }}</p>
                        <p class="text-green-700 dark:text-green-200 text-sm mt-1">
                            <i class="fas fa-info-circle mr-1"></i>
                            Data telah tersimpan dan siap untuk dicetak atau dilihat di Surat Jalan.
                        </p>
                    </div>
                    <button type="button" class="flex-shrink-0 text-green-400 hover:text-green-600 dark:text-green-300 dark:hover:text-green-100 transition-colors" onclick="this.parentElement.parentElement.style.display='none'">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>
            </div>
        @endif

        @if(session('split_messages'))
            <div class="bg-orange-50 dark:bg-orange-900/20 border-l-4 border-orange-400 dark:border-orange-700 p-4 mb-4 sm:mb-6 rounded-r-lg shadow-sm">
                <div class="flex items-start gap-3">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-triangle text-orange-500 dark:text-orange-300 text-lg"></i>
                    </div>
                    <div class="flex-1">
                        <h6 class="text-orange-800 dark:text-orange-300 font-semibold mb-2">⚠️ Stok tidak mencukupi!</h6>
                        <p class="text-orange-700 dark:text-orange-200 text-sm mb-3">
                            <strong>Beberapa produk memiliki stok terbatas, sehingga PO dibagi menjadi:</strong>
                        </p>
                        <div class="space-y-1 mb-3">
                            @foreach(session('split_messages') as $message)
                                <p class="text-orange-700 dark:text-orange-200 text-sm">• {{ $message }}</p>
                            @endforeach
                        </div>
                        <div class="bg-orange-100 dark:bg-orange-800/30 rounded-lg p-3 mt-3">
                            <p class="text-orange-800 dark:text-orange-200 text-xs">
                                <i class="fas fa-info-circle mr-1"></i>
                                Produk yang masuk ke Surat Jalan sudah otomatis tercatat sebagai Barang Keluar. Sisa yang belum terpenuhi dapat dilihat di menu 
                                <a href="{{ route('sisa-data-po.index') }}" class="underline hover:no-underline font-medium">Sisa Data PO</a>.
                            </p>
                        </div>
                    </div>
                    <button type="button" class="flex-shrink-0 text-orange-400 hover:text-orange-600 dark:text-orange-300 dark:hover:text-orange-100 transition-colors" onclick="this.parentElement.parentElement.style.display='none'">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-50 dark:bg-red-900/20 border-l-4 border-red-400 dark:border-red-700 p-4 mb-4 sm:mb-6 rounded-r-lg shadow-sm">
                <div class="flex items-start gap-3">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-circle text-red-500 dark:text-red-300 text-lg"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-red-800 dark:text-red-300 font-semibold">{{ session('error') }}</p>
                    </div>
                    <button type="button" class="flex-shrink-0 text-red-400 hover:text-red-600 dark:text-red-300 dark:hover:text-red-100 transition-colors" onclick="this.parentElement.parentElement.style.display='none'">
                        <i class="fas fa-times text-lg"></i>
                    </button>
                </div>
            </div>
        @endif

        @if ($errors->any())
            <div class="bg-red-50 dark:bg-red-900/20 border-l-4 border-red-400 dark:border-red-700 p-4 mb-4 sm:mb-6 rounded-r-lg">
                <div class="flex items-start gap-3">
                    <i class="fas fa-exclamation-triangle text-red-500 dark:text-red-300 mt-0.5"></i>
                    <div>
                        <p class="text-red-800 dark:text-red-300 font-semibold mb-1">Perbaiki input berikut:</p>
                        <ul class="list-disc list-inside text-red-700 dark:text-red-200 text-sm">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <div class="mb-4">
            <div class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-slate-900 shadow-sm p-3 sm:p-4">
                <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-300">
                        <i class="fa-solid fa-circle-info text-blue-600"></i>
                        <span>Gunakan tombol berikut untuk navigasi cepat.</span>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <a href="{{ route('invoice.index') }}"
                           class="inline-flex items-center gap-2 px-3 py-2 rounded-lg text-sm font-medium border transition bg-white text-blue-700 border-blue-300 hover:bg-blue-50 shadow-sm dark:bg-slate-900 dark:text-blue-300 dark:border-blue-800 dark:hover:bg-slate-800">
                            <i class="fa-solid fa-arrow-left"></i>
                            Kembali ke Data Invoice
                        </a>
                        <a id="btn-next-po" href="{{ route('suratjalan.index', ['po_number' => request('po_number') ?? ($po->po_number ?? null)]) }}"
                           class="inline-flex items-center gap-2 px-3 py-2 rounded-lg text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 border border-indigo-700/70 shadow-md focus:outline-none focus:ring-2 focus:ring-indigo-400">
                            <i class="fa-solid fa-arrow-right text-white"></i>
                            Lanjut ke Form Data PO
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-xl shadow-lg border border-gray-200 dark:border-white/10 p-4 sm:p-6 lg:p-8">
            <!-- Header dengan No Urut Invoice -->
            @if(request('from') === 'invoice' && (request('po_number') || (isset($po) && $po->po_number)))
            <div class="mb-6 pb-4 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-orange-100 dark:bg-orange-900/50 rounded-lg">
                        <i class="fas fa-hashtag text-orange-600 dark:text-orange-400"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Form Input PO</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            No Invoice: <span class="font-bold text-orange-600 dark:text-orange-400">{{ request('po_number') ?? ($po->po_number ?? '-') }}</span>
                        </p>
                    </div>
                </div>
            </div>
            @endif

            @php
                $customerCollection = isset($customers) ? $customers : collect();
                $currentCustomerId = old('customer_id', $po->customer_id ?? null);
                $currentCustomerName = old('customer_name', $po->customer ?? null);
                $activeCustomer = null;
                if ($currentCustomerId) {
                    $activeCustomer = $customerCollection->firstWhere('id', (int) $currentCustomerId);
                }
                if (!$activeCustomer && $currentCustomerName) {
                    $trimmedName = trim((string) $currentCustomerName);
                    $activeCustomer = $customerCollection->first(function ($cust) use ($trimmedName) {
                        return isset($cust->name) && strcasecmp(trim($cust->name), $trimmedName) === 0;
                    });
                }
                $customerCodeNumber = $activeCustomer->code_number ?? '';
                $codeNumberParts = ['pt' => '', 'tahun' => ''];
                if (!empty($customerCodeNumber)) {
                    $slashParts = array_values(array_filter(array_map('trim', explode('/', $customerCodeNumber)), fn($v) => $v !== ''));
                    if (!empty($slashParts)) {
                        if (count($slashParts) > 1) {
                            $codeNumberParts['tahun'] = array_pop($slashParts);
                        }
                        $codeNumberParts['pt'] = implode('/', $slashParts);
                    }
                }
                $prefillAddress1 = old('address_1', $po->alamat_1 ?? ($activeCustomer->address_1 ?? ''));
                $prefillAddress2 = old('address_2', $po->alamat_2 ?? ($activeCustomer->address_2 ?? ''));
            @endphp

            <form method="POST" action="{{ isset($po) && request('from') !== 'invoice' ? route('po.update', $po->id) : route('po.store') }}" class="space-y-6">
                @csrf
                @if(isset($po) && request('from') !== 'invoice')
                    @method('PUT')
                @endif
                
                <!-- Hidden field untuk po_number agar tetap konsisten -->
                @if(request('po_number'))
                    <input type="hidden" name="po_number" value="{{ request('po_number') }}">
                @elseif(isset($po) && $po->po_number)
                    <input type="hidden" name="po_number" value="{{ $po->po_number }}">
                @endif

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-200">
                            <i class="fas fa-building text-blue-500 mr-1"></i>Customer
                        </label>
                        <div class="w-full border-2 border-gray-200 dark:border-gray-700 rounded-lg px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-base text-gray-800 dark:text-gray-100 bg-gray-100 dark:bg-gray-800/70">
                            {{ old('customer_name', $po->customer ?? '-') }}
                        </div>
                        <input type="hidden" name="customer_id" id="customer"
                               value="{{ old('customer_id', $po->customer_id ?? '') }}"
                               data-code-number="{{ $customerCodeNumber }}"
                               data-address1="{{ $activeCustomer->address_1 ?? '' }}"
                               data-address2="{{ $activeCustomer->address_2 ?? '' }}"
                               data-sj-pt="{{ $codeNumberParts['pt'] }}"
                               data-sj-tahun="{{ $codeNumberParts['tahun'] }}"
                               required>
                    </div>

                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-200">
                            <i class="fas fa-file-invoice text-green-500 mr-1"></i>No PO
                        </label>
                        <input type="text"
                               name="no_po"
                               id="no_po"
                               placeholder="PO.123456789"
                               value="{{ old('no_po', (isset($po) && ($po->no_po ?? '') === '-') ? '' : ($po->no_po ?? '')) }}"
                               class="w-full border-2 border-gray-200 dark:border-gray-700 rounded-lg px-3 py-2 sm:py-3 text-sm sm:text-base bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-100 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-500/30 transition-all duration-200"
                               required>
                    </div>

                    <!-- Tanggal PO -->
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-200">
                            <i class="fas fa-calendar text-red-500 mr-1"></i>Tanggal PO
                        </label>
                        <div class="relative">
                            <input type="date" name="tanggal_po" class="date-input w-full border-2 border-gray-200 dark:border-gray-700 rounded-lg pl-3 pr-12 sm:pl-4 sm:pr-12 py-2 sm:py-3 text-sm sm:text-base bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-100 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-500/30 transition-all duration-200" value="{{
                                old('tanggal_po',
                                    request('from') === 'invoice'
                                        ? now()->format('Y-m-d')
                                        : (isset($po) && $po->tanggal_po ? \Carbon\Carbon::parse($po->tanggal_po)->format('Y-m-d') : (request('tanggal_po') ?? ''))
                                )
                            }}" required>
                            <span class="pointer-events-none absolute inset-y-0 right-3 flex items-center text-gray-500 dark:text-gray-200">
                                <i class="fa-regular fa-calendar text-base"></i>
                            </span>
                        </div>
                    </div>


                    <!-- No Surat Jalan -->
                    <!-- Made no surat jalan responsive with better mobile layout -->
                    <div class="space-y-2 md:col-span-2 lg:col-span-3 md:col-start-1 lg:col-start-1">
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-200">
                            <i class="fas fa-truck text-purple-500 mr-1"></i>No Surat Jalan
                        </label>
                        <div class="flex flex-col sm:flex-row gap-2 sm:items-center w-full min-w-0">
                            @php
                                $noSuratJalanParts = [];
                                if (isset($po) && $po->no_surat_jalan && trim($po->no_surat_jalan) !== '-') {
                                    $noSuratJalanParts = explode('/', $po->no_surat_jalan);
                                } elseif (request('from') === 'invoice' && isset($sjCodeParts) && is_array($sjCodeParts)) {
                                    // sjCodeParts: [nomor, pt, tahun]
                                    $noSuratJalanParts = $sjCodeParts;
                                }
                                // Normalisasi: jika bagian '-' maka kosongkan agar tidak error pada input number
                                foreach ($noSuratJalanParts as $k => $v) {
                                    if (is_string($v) && trim($v) === '-') $noSuratJalanParts[$k] = '';
                                }
                            @endphp
                            <div class="flex gap-2 items-center w-full">
                                <input type="number" name="no_surat_jalan_nomor" id="delivery_nomor" class="border-2 border-gray-200 dark:border-gray-700 rounded-lg px-2 sm:px-3 py-2 sm:py-3 text-sm sm:text-base flex-[1] basis-1/4 min-w-0 bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-100" placeholder="Nomor" value="{{ old('no_surat_jalan_nomor', $noSuratJalanParts[0] ?? '') }}" required>
                                <span class="text-gray-400 font-bold text-sm sm:text-base">/</span>
                                <input type="text" name="no_surat_jalan_pt" id="delivery_pt" class="border-2 border-gray-200 dark:border-gray-700 rounded-lg px-2 sm:px-3 py-2 sm:py-3 text-sm sm:text-base flex-[2] basis-1/2 min-w-0 bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-100" placeholder="PT" value="{{ old('no_surat_jalan_pt', $noSuratJalanParts[1] ?? ($codeNumberParts['pt'] ?: 'CAM-WPB')) }}" required readonly>
                                <span class="text-gray-400 font-bold text-sm sm:text-base">/</span>
                                <input type="number" name="no_surat_jalan_tahun" id="delivery_tahun" class="border-2 border-gray-200 dark:border-gray-700 rounded-lg px-2 sm:px-3 py-2 sm:py-3 text-sm sm:text-base flex-[1] basis-1/4 min-w-0 bg-gray-50 dark:bg-gray-800 text-gray-800 dark:text-gray-100" placeholder="Tahun" value="{{ old('no_surat_jalan_tahun', $noSuratJalanParts[2] ?? ($codeNumberParts['tahun'] ?: '')) }}" required readonly>
                            </div>
                        </div>
                    </div>



                    <!-- Made alamat_1 required and editable, not readonly -->
                    <!-- Alamat 1 -->
                    <div class="space-y-2 md:col-start-1 lg:col-start-1 md:col-span-2 lg:col-span-3">
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-200">
                            <i class="fas fa-map-marker-alt text-blue-500 mr-1"></i>Alamat 1 <span class="text-red-500"></span>
                        </label>
                        <input type="text" name="address_1" id="address_1" class="w-full border-2 border-gray-200 dark:border-gray-700 rounded-lg px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-base text-gray-800 dark:text-gray-100 bg-gray-50 dark:bg-gray-800" value="{{ $prefillAddress1 }}" required placeholder="Masukkan alamat lengkap" readonly>
                    </div>

                    <!-- Made alamat_2 editable, not readonly -->
                    <!-- Alamat 2 -->
                    <div class="space-y-2 md:col-start-1 lg:col-start-1 md:col-span-2 lg:col-span-3">
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-200">
                            <i class="fas fa-map-marker-alt text-blue-500 mr-1"></i>Alamat 2
                        </label>
                        <input type="text" name="address_2" id="address_2" class="w-full border-2 border-gray-200 dark:border-gray-700 rounded-lg px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-base text-gray-800 dark:text-gray-100 bg-gray-50 dark:bg-gray-800" value="{{ $prefillAddress2 }}" placeholder="Alamat tambahan (opsional)" readonly>
                    </div>

                    <!-- Pengiriman: gaya mengikuti No Surat Jalan (3 kolom sejajar, tanpa slash) -->
                    <div class="space-y-2 md:col-span-2 lg:col-span-3">
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-200">
                            <i class="fas fa-truck-fast text-purple-600 mr-1"></i>Pengiriman
                        </label>
                        <div class="flex flex-col sm:flex-row gap-2 sm:items-center w-full min-w-0">
                            <div class="flex gap-2 items-center w-full">
                                <!-- Pengirim (lebih panjang) -->
                                <select name="pengirim" id="pengirim" class="border-2 border-gray-200 dark:border-gray-700 rounded-lg px-2 sm:px-3 py-2 sm:py-3 text-sm sm:text-base flex-[2] basis-1/2 min-w-0 bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-100 focus:border-orange-500 focus:ring-2 focus:ring-orange-200 dark:focus:ring-orange-500/30" >
                                    <option value="">-- Pilih Pengirim --</option>
                                    @foreach($pengirims as $p)
                                        <option value="{{ $p->nama }}" data-kendaraan="{{ $p->kendaraan ?? '' }}" data-nopol="{{ $p->no_polisi ?? '' }}" @selected(old('pengirim', $po->pengirim ?? '') == $p->nama)>
                                            {{ $p->nama }}
                                        </option>
                                    @endforeach
                                </select>

                                <!-- Kendaraan (readonly) -->
                                <input type="text" name="kendaraan" id="kendaraan" class="border-2 border-gray-200 dark:border-gray-700 rounded-lg px-2 sm:px-3 py-2 sm:py-3 text-sm sm:text-base flex-[1] basis-1/4 min-w-0 bg-gray-50 dark:bg-gray-800 text-gray-800 dark:text-gray-100" value="{{ old('kendaraan', $po->kendaraan ?? '') }}" readonly>

                                <!-- No Polisi (readonly) -->
                                <input type="text" name="no_polisi" id="no_polisi" class="border-2 border-gray-200 dark:border-gray-700 rounded-lg px-2 sm:px-3 py-2 sm:py-3 text-sm sm:text-base flex-[1] basis-1/4 min-w-0 bg-gray-50 dark:bg-gray-800 text-gray-800 dark:text-gray-100" value="{{ old('no_polisi', $po->no_polisi ?? '') }}" readonly>
                            </div>
                        </div>
                    </div>

                    <!-- Produk (Dynamic Items) -->
                    <div class="md:col-span-2 lg:col-span-3 space-y-4" id="items-container">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 border-b border-gray-200 dark:border-gray-700 pb-2">
                            <i class="fas fa-boxes text-indigo-500 mr-2"></i>Produk Items
                        </h3>

                        <!-- Table-like header for items (desktop) -->
                        <div class="hidden md:grid md:grid-cols-12 gap-4 text-xs font-semibold text-gray-500 dark:text-gray-400 px-1">
                            <div class="md:col-span-4">Produk</div>
                            <div class="md:col-span-2">Quantity</div>
                            <div class="md:col-span-2">Harga</div>
                            <div class="md:col-span-3">Total</div>
                            <div class="md:col-span-1 text-right pr-4">Aksi</div>
                        </div>

                        <!-- Item Row Template (first row) -->
                        <div class="item-row grid grid-cols-1 md:grid-cols-12 gap-4 items-end p-4 border rounded-lg bg-gray-50 dark:bg-white/5 border-gray-200 dark:border-white/10">
                            <!-- Produk -->
                            <div class="space-y-2 md:col-span-4">
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-200">Produk</label>
                                <select name="items[0][produk_id]" class="produk-select w-full border-2 border-gray-200 dark:border-gray-700 rounded-lg px-3 py-2 text-sm bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-100 focus:border-green-500 focus:ring-2 focus:ring-green-200 dark:focus:ring-green-500/30" required>
                                    <option value="">-- Pilih Produk --</option>
                                    @foreach($produks as $p)
                                        @php
                                            $stokMasuk = \DB::table('barang_masuks')->where('produk_id', $p->id)->sum('qty') ?? 0;
                                            $stokKeluar = \DB::table('barang_keluars')->where('produk_id', $p->id)->sum('qty') ?? 0;
                                            $stokTersedia = $stokMasuk - $stokKeluar;
                                        @endphp
                                        <option value="{{ $p->id }}" 
                                                data-harga-pcs="{{ $p->harga_pcs ?? 0 }}" 
                                                data-harga-set="{{ $p->harga_set ?? 0 }}" 
                                                data-harga="{{ $p->harga ?? 0 }}" 
                                                data-satuan="{{ strtoupper($p->satuan ?? 'PCS') }}"
                                                data-stok="{{ $stokTersedia }}">
                                            {{ $p->nama_produk }} (Stok: {{ $stokTersedia }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <!-- Quantity -->
                            <div class="space-y-2 md:col-span-2">
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-200">Quantity</label>
                                <div class="flex w-full min-w-0">
                                    <input type="number" name="items[0][qty]" class="item-qty border-2 border-gray-200 dark:border-gray-700 rounded-l-lg px-3 py-2 text-sm flex-auto min-w-0 bg-white dark:bg-gray-800/80 text-gray-800 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-400 focus:border-green-500 focus:ring-2 focus:ring-green-200 dark:focus:ring-green-500/30" min="1" required>
                                    <input type="text" class="item-qty-jenis-display border-2 border-l-0 border-gray-200 dark:border-gray-700 rounded-r-lg px-2 py-2 text-[10px] w-[68px] shrink-0 bg-gray-50 dark:bg-gray-800/80 text-gray-800 dark:text-gray-100" value="PCS" readonly>
                                    <input type="hidden" name="items[0][qty_jenis]" class="item-qty-jenis-hidden" value="PCS">
                                </div>
                            </div>
                            <!-- Harga -->
                            <div class="space-y-2 md:col-span-2">
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-200">Harga</label>
                                <input type="number" name="items[0][harga]" class="item-harga w-full border-2 border-gray-200 dark:border-gray-700 rounded-lg px-3 py-2 text-sm bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-100 focus:border-yellow-500 focus:ring-2 focus:ring-yellow-200 dark:focus:ring-yellow-500/30" min="0" step="0.01" required>
                            </div>
                            <!-- Total -->
                            <div class="space-y-2 md:col-span-3">
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-200">Total</label>
                                <input type="number" name="items[0][total]" class="item-total w-full border-2 border-gray-200 dark:border-gray-700 rounded-lg px-3 py-2 text-sm bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-100" readonly>
                            </div>
                            <!-- Remove Button -->
                            <div class="md:col-span-1 flex md:justify-end pr-4">
                                <button type="button" class="remove-item-btn inline-flex items-center justify-center w-9 h-9 rounded-lg text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-400">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                        <!-- Toolbar -->
                        <div class="flex flex-col sm:flex-row gap-3 sm:items-center sm:justify-between">
                            <button type="button" id="add-item-btn" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-all duration-200 text-sm w-full sm:w-auto">
                                <i class="fas fa-plus-circle mr-1"></i>Tambah Produk
                            </button>
                            <!-- Grand Total Summary Card -->
                            <div id="grand-summary" class="mt-2 sm:mt-0 w-full sm:w-auto">
                                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm px-4 py-3 flex items-center justify-between gap-4">
                                    <span class="text-base sm:text-lg font-semibold text-gray-800 dark:text-gray-100">Grand Total</span>
                                    <input type="number" name="grand_total" id="grand_total" class="w-40 sm:w-56 border-2 border-gray-300 dark:border-gray-700 rounded-lg px-3 py-2 text-sm sm:text-base bg-gray-100 dark:bg-gray-900/40 text-gray-800 dark:text-gray-100 font-bold text-right transition-all duration-200" readonly>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <!-- Made buttons responsive with flex-col on mobile -->
                    <div class="mt-6 sm:mt-8 pt-4 sm:pt-6 border-t border-gray-200">
                        <div class="flex flex-col sm:flex-row flex-wrap gap-3 items-stretch">
                            <button type="submit" class="w-full sm:w-auto bg-blue-600 hover:bg-blue-700 text-white px-4 sm:px-6 py-2 sm:py-3 text-sm sm:text-base rounded-lg transition-all duration-200 flex items-center justify-center shadow-lg focus:outline-none focus:ring-2 focus:ring-blue-200">
                                <i class="fas fa-save mr-2"></i>
                                Simpan
                            </button>

                            <!-- Tombol hapus dihilangkan sesuai permintaan -->
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // Guard: hanya berlaku jika tombol mengarah ke po.create (saat diperlukan)
    const nextBtn = document.getElementById('btn-next-po');
    if (nextBtn) {
        nextBtn.addEventListener('click', (e) => {
            try {
                const url = new URL(nextBtn.href, window.location.origin);
                if (url.pathname.includes('/po/create')) {
                    const poNum = (url.searchParams.get('po_number') || '').trim();
                    if (!poNum) {
                        e.preventDefault();
                        alert('Nomor urut invoice (po_number) belum tersedia. Silakan buka dari halaman Data Invoice (double click pada nomor) agar po_number terisi.');
                    }
                }
            } catch (_) {
                // Biarkan default untuk link biasa
            }
        });
    }

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


    const kendaraanInput = document.getElementById('kendaraan');
    const noPolisiInput = document.getElementById('no_polisi');

    const customerSelect = document.getElementById('customer');
    const address1Input = document.getElementById('address_1');
    const address2Input = document.getElementById('address_2');

    const deliveryNomorInput = document.getElementById('delivery_nomor');
    const deliveryPtInput = document.getElementById('delivery_pt');
    const deliveryTahunInput = document.getElementById('delivery_tahun');

    const pengirimSelect = document.getElementById('pengirim');

    const tanggalPOInput = document.querySelector('input[name="tanggal_po"]');

    function getCustomerData() {
        if (!customerSelect) return {};
        if (customerSelect.tagName === 'SELECT') {
            const opt = customerSelect.options[customerSelect.selectedIndex];
            if (!opt) return {};
            return {
                address1: opt.getAttribute('data-address1') || '',
                address2: opt.getAttribute('data-address2') || '',
                codeNumber: opt.getAttribute('data-code-number') || opt.getAttribute('data-code_number') || '',
                sjPt: opt.getAttribute('data-sj-pt') || opt.getAttribute('data-sj_pt') || opt.getAttribute('data-code-pt') || '',
                sjTahun: opt.getAttribute('data-sj-tahun') || opt.getAttribute('data-sj_tahun') || opt.getAttribute('data-code-year') || ''
            };
        }
        const ds = customerSelect.dataset || {};
        return {
            address1: ds.address1 || '',
            address2: ds.address2 || '',
            codeNumber: ds.codeNumber || ds.code_number || '',
            sjPt: ds.sjPt || ds.sj_pt || '',
            sjTahun: ds.sjTahun || ds.sj_tahun || ''
        };
    }

    // Batasi input hanya angka untuk field nomor dan tahun
    function enforceDigitOnly(el) {
        if (!el) return;
        el.addEventListener('input', () => {
            el.value = (el.value || '').replace(/[^0-9]/g, '');
        });
        el.setAttribute('inputmode', 'numeric');
        el.setAttribute('pattern', '[0-9]*');
    }
    // Nomor dan Tahun wajib angka
    enforceDigitOnly(deliveryNomorInput);
    enforceDigitOnly(deliveryTahunInput);

    // Autofill Kendaraan & No Polisi berdasarkan pilihan Pengirim (field kendaraan sekarang readonly input)
    function fillFromPengirim() {
        if (!pengirimSelect) return;
        const opt = pengirimSelect.options[pengirimSelect.selectedIndex];
        const kendaraan = opt?.dataset?.kendaraan || '';
        const nopol = opt?.dataset?.nopol || '';
        if (kendaraanInput) kendaraanInput.value = kendaraan;
        if (noPolisiInput) noPolisiInput.value = nopol;
    }
    if (pengirimSelect) {
        pengirimSelect.addEventListener('change', fillFromPengirim);
        // Trigger sekali saat load jika sudah ada pilihan
        setTimeout(fillFromPengirim, 0);
    }

    function addAutoFillEffect(element) {
        if (element && element.value) {
            element.classList.add('bg-green-50', 'border-green-300');
            setTimeout(() => {
                element.classList.remove('bg-green-50', 'border-green-300');
            }, 2000);
        }
    }

    // Auto-fill Tahun dari Tanggal PO
    function fillYearFromTanggalPO() {
        if (!tanggalPOInput || !tanggalPOInput.value) return;
        const d = new Date(tanggalPOInput.value);
        if (isNaN(d.getTime())) return;
        const year = d.getFullYear();

        if (deliveryTahunInput && (!deliveryTahunInput.value || deliveryTahunInput.value === '')) {
            deliveryTahunInput.value = year;
            addAutoFillEffect(deliveryTahunInput);
        }
    }

    // Dynamic multi-item logic
    const itemsContainer = document.getElementById('items-container');
    const addItemBtn = document.getElementById('add-item-btn');
    let itemIndex = itemsContainer ? itemsContainer.querySelectorAll('.item-row').length : 0;

    // Per-row calculation menyesuaikan qty_jenis dari produk (tanpa dropdown)
    function calculateRowTotal(row) {
        const produkSelect = row.querySelector('.produk-select');
        const qtyInput = row.querySelector('.item-qty');
        const qtyJenisHidden = row.querySelector('.item-qty-jenis-hidden');
        const qtyJenisDisplay = row.querySelector('.item-qty-jenis-display');
        const hargaInput = row.querySelector('.item-harga');
        const totalInput = row.querySelector('.item-total');

        if (!produkSelect || !qtyInput || !qtyJenisHidden || !hargaInput || !totalInput) return;

        const selected = produkSelect.options[produkSelect.selectedIndex];
        // Set qty_jenis berdasarkan satuan produk (default PCS)
        let qtyJenis = 'PCS';
        // Aturan sederhana & tegas: jika harga_set > 0 maka SET, selain itu PCS
        const hSet = parseInt(selected?.dataset?.hargaSet || '0');
        qtyJenis = hSet > 0 ? 'SET' : 'PCS';
        qtyJenisHidden.value = qtyJenis;
        if (qtyJenisDisplay) qtyJenisDisplay.value = qtyJenis;

        let harga = 0;
        if (selected && selected.value) {
            const hargaAny = parseInt(selected.dataset.harga || 0);
            if (qtyJenis === 'PCS') {
                const hPcs = parseInt(selected.dataset.hargaPcs || 0);
                harga = hPcs > 0 ? hPcs : hargaAny;
            } else {
                const hSet = parseInt(selected.dataset.hargaSet || 0);
                harga = hSet > 0 ? hSet : hargaAny;
            }
        }
        const qty = parseInt(qtyInput.value || 0);
        const total = (harga || 0) * (qty || 0);
        hargaInput.value = harga || 0;
        totalInput.value = total || 0;

        if (total > 0) addAutoFillEffect(totalInput);
    }

    function updateGrandTotal() {
        const rows = document.querySelectorAll('.item-row');
        let grand = 0;
        rows.forEach(r => {
            const t = r.querySelector('.item-total');
            grand += parseInt((t && t.value) ? t.value : 0);
        });
        const grandInput = document.getElementById('grand_total');
        if (grandInput) {
            grandInput.value = grand;
            const summaryCard = document.getElementById('grand-summary');
            if (summaryCard) {
                summaryCard.classList.add('bump');
                setTimeout(() => summaryCard.classList.remove('bump'), 260);
            }
        }
    }

    function renumberRows() {
        const rows = itemsContainer.querySelectorAll('.item-row');
        rows.forEach((row, idx) => {
            const sel = row.querySelector('.produk-select');
            const qty = row.querySelector('.item-qty');
            const jenisHidden = row.querySelector('.item-qty-jenis-hidden');
            const harga = row.querySelector('.item-harga');
            const total = row.querySelector('.item-total');
            if (sel) sel.name = `items[${idx}][produk_id]`;
            if (qty) qty.name = `items[${idx}][qty]`;
            if (jenisHidden) jenisHidden.name = `items[${idx}][qty_jenis]`;
            if (harga) harga.name = `items[${idx}][harga]`;
            if (total) total.name = `items[${idx}][total]`;
        });
        itemIndex = rows.length;
        // Show remove buttons only if more than one row
        const removeButtons = itemsContainer.querySelectorAll('.remove-item-btn');
        removeButtons.forEach(btn => btn.classList.toggle('hidden', rows.length <= 1));
    }

    function attachRowEvents(row) {
        const produkSelect = row.querySelector('.produk-select');
        const qtyInput = row.querySelector('.item-qty');
        const removeBtn = row.querySelector('.remove-item-btn');
        if (produkSelect) produkSelect.addEventListener('change', () => { calculateRowTotal(row); updateGrandTotal(); });
        if (qtyInput) qtyInput.addEventListener('input', () => { calculateRowTotal(row); updateGrandTotal(); });
        if (removeBtn) removeBtn.addEventListener('click', () => {
            row.remove();
            renumberRows();
            updateGrandTotal();
        });
    }

    if (addItemBtn) {
        addItemBtn.addEventListener('click', () => {
            const template = itemsContainer.querySelector('.item-row');
            if (!template) return;
            const newRow = template.cloneNode(true);
            // Reset values
            const sel = newRow.querySelector('.produk-select');
            if (sel) sel.selectedIndex = 0;
            newRow.querySelectorAll('input').forEach(inp => inp.value = '');
            // Prevent duplicate listeners by replacing node
            const cleanRow = newRow.cloneNode(true);
            itemsContainer.insertBefore(cleanRow, itemsContainer.querySelector('.flex.flex-col') || null);
            cleanRow.classList.add('fade-in');
            attachRowEvents(cleanRow);
            renumberRows();
            calculateRowTotal(cleanRow);
            updateGrandTotal();
        });
    }


    function updateCustomerAddresses() {
        const data = getCustomerData();
        if (!data) return;

        const addr1 = data.address1 || '';
        const addr2 = data.address2 || '';
        if (address1Input) {
            address1Input.value = addr1;
            address1Input.readOnly = true;
            if (addr1) addAutoFillEffect(address1Input);
        }
        if (address2Input) {
            address2Input.value = addr2;
            address2Input.readOnly = true;
            if (addr2) addAutoFillEffect(address2Input);
        }

        if (deliveryPtInput) {
            const ptVal = data.sjPt || 'CAM-WPB';
            deliveryPtInput.value = ptVal;
            deliveryPtInput.readOnly = true;
        }

        if (deliveryTahunInput) {
            const tahunVal = data.sjTahun || '';
            if (!deliveryTahunInput.value && tahunVal) {
                deliveryTahunInput.value = tahunVal;
                addAutoFillEffect(deliveryTahunInput);
            }
            deliveryTahunInput.readOnly = false;
        }

        if (deliveryNomorInput) {
            deliveryNomorInput.placeholder = 'Nomor';
            deliveryNomorInput.readOnly = false;
        }

        if (!addr1 && address1Input) {
            address1Input.classList.add('border-yellow-400', 'bg-yellow-50');
            address1Input.placeholder = 'Customer tidak memiliki alamat, silakan isi manual';
            address1Input.readOnly = false;
        } else if (address1Input) {
            address1Input.classList.remove('border-yellow-400', 'bg-yellow-50');
        }
    }

    // Tambahkan event hanya jika customerSelect adalah SELECT
    if (customerSelect && customerSelect.tagName === 'SELECT') {
        customerSelect.addEventListener('change', function() {
            updateCustomerAddresses();
        });
    }


    // Attach to initial row dan hitung awal
    document.querySelectorAll('.item-row').forEach(row => { 
        attachRowEvents(row); 
        calculateRowTotal(row);
    });
    renumberRows();
    updateGrandTotal();
    // Prefill No Surat Jalan jika #customer bukan SELECT
    updateCustomerAddresses();

    // Hook: saat tanggal PO berubah, isi Tahun otomatis
    if (tanggalPOInput) {
        tanggalPOInput.addEventListener('change', fillYearFromTanggalPO);
        // Prefill sekali di awal jika ada nilai
        fillYearFromTanggalPO();
    }
});
</script>

<script>
// Validasi front-end sederhana sebelum submit untuk mencegah kegagalan yang tidak terlihat
document.addEventListener('DOMContentLoaded', () => {
    // Reset field tertentu saat kembali dari Data PO jika parameter reset_fields=1
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('reset_fields') === '1') {
        // Reset No PO
        const noPo = document.getElementById('no_po');
        if (noPo) noPo.value = '';
        
        // Reset No Surat Jalan Nomor (yang bisa diinput saja)
        const deliveryNomor = document.getElementById('delivery_nomor');
        if (deliveryNomor) deliveryNomor.value = '';
        
        // Reset Pengiriman (pengirim, kendaraan, no polisi)
        const pengirim = document.getElementById('pengirim');
        if (pengirim) pengirim.value = '';
        
        const kendaraan = document.getElementById('kendaraan');
        if (kendaraan) kendaraan.value = '';
        
        const noPolisi = document.getElementById('no_polisi');
        if (noPolisi) noPolisi.value = '';
        
        // Reset Item Rows - hapus semua row kecuali yang pertama
        const itemsContainer = document.getElementById('items-container');
        if (itemsContainer) {
            const rows = itemsContainer.querySelectorAll('.item-row');
            // Hapus semua row kecuali yang pertama
            rows.forEach((row, index) => {
                if (index > 0) row.remove();
            });
            
            // Reset field di row pertama
            if (rows[0]) {
                const firstRow = rows[0];
                const produkSelect = firstRow.querySelector('.produk-select');
                const qtyInput = firstRow.querySelector('.item-qty');
                const hargaInput = firstRow.querySelector('.item-harga');
                const totalInput = firstRow.querySelector('.item-total');
                
                if (produkSelect) produkSelect.value = '';
                if (qtyInput) qtyInput.value = '';
                if (hargaInput) hargaInput.value = '';
                if (totalInput) totalInput.value = '';
            }
            
            // Reset grand total
            const grandTotal = document.getElementById('grand_total');
            if (grandTotal) grandTotal.value = '0';
        }
        
        console.log('✅ Fields reset: No PO, No SJ Nomor, Pengiriman, Items');
        
        // Tampilkan notifikasi bahwa form sudah direset
        const notification = document.createElement('div');
        notification.className = 'fixed top-4 right-4 bg-blue-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 transition-all duration-300';
        notification.innerHTML = '<i class="fas fa-refresh mr-2"></i>Form berhasil direset untuk input PO berikutnya';
        document.body.appendChild(notification);
        
        // Hilangkan notifikasi setelah 3 detik
        setTimeout(() => {
            notification.style.opacity = '0';
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    }
    
    // No PO sudah menjadi satu input (name="no_po"), tidak perlu penggabungan bagian

    // Update link Data PO (Surat Jalan) mengikuti tanggal_po
    const tanggalInput = document.querySelector('input[name="tanggal_po"]');
    const btnSJ = document.getElementById('btn-to-sj');
    function updateSuratJalanLink() {
        if (!tanggalInput || !btnSJ || !tanggalInput.value) return;
        const d = new Date(tanggalInput.value);
        if (isNaN(d.getTime())) return;
        const month = (d.getMonth() + 1);
        const year = d.getFullYear();
        const base = "{{ route('suratjalan.index') }}";
        const poNumber = "{{ request('po_number') }}";
        btnSJ.href = base + `?month=${month}&year=${year}${poNumber ? '&po_number=' + poNumber : ''}`;
    }
    updateSuratJalanLink();
    tanggalInput?.addEventListener('change', updateSuratJalanLink);

    const form = document.getElementById('po-form');
    if (!form) return;
    form.addEventListener('submit', (e) => {
        // Pastikan No PO digabungkan sebelum submit
        combineNoPO();
        
        const customer = document.getElementById('customer');
        const noPo = form.querySelector('input[name="no_po"]');
        const tgl = form.querySelector('input[name="tanggal_po"]');
        const rows = document.querySelectorAll('.item-row');
        let itemValid = false;
        rows.forEach(r => {
            const produk = r.querySelector('.produk-select');
            const qty = r.querySelector('.item-qty');
            if (produk && produk.value && qty && parseInt(qty.value || '0') > 0) {
                itemValid = true;
            }
        });

        const errors = [];
        if (!customer || !customer.value) errors.push('Customer wajib dipilih');
        if (!noPo || !noPo.value || noPo.value.trim() === '-' ) errors.push('No PO wajib diisi dan tidak boleh "-"');
        if (!tgl || !tgl.value) errors.push('Tanggal PO wajib diisi');
        if (!itemValid) errors.push('Minimal 1 item produk dengan Qty > 0');

        if (errors.length > 0) {
            e.preventDefault();
            alert('Form belum lengkap:\n- ' + errors.join('\n- '));
        }
    });
});
</script>
@endsection
