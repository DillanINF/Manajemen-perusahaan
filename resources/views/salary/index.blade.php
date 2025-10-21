@extends('layouts.app')

@section('title', 'Manajemen Gaji Karyawan')

@push('styles')
<style>
/* Force modal salary width to be compact - sama dengan employee */
.modal-gaji-container {
    max-width: 28rem !important; /* 448px - max-w-md */
    width: 100% !important;
}

#inputGajiModal .modal-gaji-container,
#editGajiModal .modal-gaji-container {
    max-width: 28rem !important;
}

/* Sembunyikan panah native agar tidak dobel */
#filterMonth, #filterYear {
  -webkit-appearance: none;
  -moz-appearance: none;
  appearance: none;
  background-image: none;
}
#filterMonth::-ms-expand, #filterYear::-ms-expand { display: none; }

/* Custom arrow untuk dropdown Bulan dan Tahun */
#filterMonth, #filterYear {
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24'%3E%3Cpath d='M7 10l5 5 5-5z'/%3E");
  background-repeat: no-repeat;
  background-position: right 0.5rem center;
}
</style>
@endpush

@section('content')
<div class="min-h-screen py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header Section - Like Screenshot -->
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 p-6 mb-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-slate-100">Data Gaji Karyawan</h1>
                        <p class="text-sm text-gray-500 dark:text-slate-400 mt-0.5">Kelola data gaji karyawan perusahaan</p>
                    </div>
                </div>
                <button onclick="openInputGajiModal()" 
                        class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors shadow-sm flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Tambah Gaji
                </button>
            </div>
        </div>

        <!-- Filter Bar -->
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 p-5 mb-8">
            <div class="flex items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="relative">
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Bulan</label>
                        <select id="filterMonth" onchange="filterByMonthYear()" 
                                class="px-3 py-2 pr-8 border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200">
                            @for($i = 1; $i <= 12; $i++)
                                <option value="{{ $i }}" {{ $i == date('n') ? 'selected' : '' }}>
                                    {{ DateTime::createFromFormat('!m', $i)->format('F') }}
                                </option>
                            @endfor
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-500 dark:text-slate-300 mt-6">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </div>
                    </div>
                    <div class="relative">
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1.5">Tahun</label>
                        <select id="filterYear" onchange="filterByMonthYear()" 
                                class="px-3 py-2 pr-8 border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200">
                            @for($y = 2020; $y <= 2035; $y++)
                                <option value="{{ $y }}" {{ $y == date('Y') ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-500 dark:text-slate-300 mt-6">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabel Gaji - Clean & Simple -->
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-slate-700 bg-gray-50 dark:bg-slate-900/50">
                            <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-600 dark:text-slate-300 uppercase tracking-wider w-20"># No</th>
                            <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-600 dark:text-slate-300 uppercase tracking-wider">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    Bulan/Tahun
                                </div>
                            </th>
                            <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-600 dark:text-slate-300 uppercase tracking-wider">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                                    </svg>
                                    Nominal
                                </div>
                            </th>
                            <th class="px-6 py-3.5 text-center text-xs font-semibold text-gray-600 dark:text-slate-300 uppercase tracking-wider">
                                <div class="flex items-center justify-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Status
                                </div>
                            </th>
                            <th class="px-6 py-3.5 text-center text-xs font-semibold text-gray-600 dark:text-slate-300 uppercase tracking-wider w-32">
                                <div class="flex items-center justify-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/>
                                    </svg>
                                    Aksi
                                </div>
                            </th>
                        </tr>
                    </thead>
                    <tbody id="salaryTableBody" class="divide-y divide-gray-100 dark:divide-slate-700">
                        @forelse($salaries ?? [] as $index => $salary)
                        <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/50 transition-colors" data-month="{{ $salary->bulan }}" data-year="{{ $salary->tahun }}">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-slate-200">{{ $index + 1 }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-3 py-1 rounded-md text-sm font-medium bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400">
                                    {{ DateTime::createFromFormat('!m', $salary->bulan)->format('F') }} {{ $salary->tahun }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap" data-total>
                                <div class="text-sm font-bold text-gray-900 dark:text-slate-200">Rp {{ number_format($salary->total_gaji, 0, ',', '.') }}</div>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-center">
                                @if($salary->status_pembayaran === 'dibayar')
                                    <span class="inline-flex items-center px-2.5 py-0.5 text-xs font-medium rounded bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">
                                        Dibayar
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 text-xs font-medium rounded bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                        Pending
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <x-table.action-buttons 
                                    onEdit="openEditModal({{ $salary->id }}, {{ $salary->bulan }}, {{ $salary->tahun }}, {{ $salary->gaji_pokok }})"
                                    deleteAction="{{ route('salary.destroy', $salary->id) }}"
                                    confirmText="Yakin ingin menghapus data gaji ini?" />
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-16 h-16 rounded-full bg-gray-100 dark:bg-slate-700 flex items-center justify-center mb-4">
                                        <svg class="w-8 h-8 text-gray-400 dark:text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                    </div>
                                    <p class="text-base font-medium text-gray-900 dark:text-slate-300 mb-1">Belum ada data gaji</p>
                                    <p class="text-sm text-gray-500 dark:text-slate-400">Klik tombol "Tambah Gaji" untuk menambah data baru</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Input Gaji - Style Seperti Employee -->
<div id="inputGajiModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
    <div class="modal-gaji-container bg-white dark:bg-slate-900 dark:text-slate-200 rounded-2xl shadow-2xl w-full max-w-md max-h-[90vh] overflow-y-auto border border-transparent dark:border-slate-700">
        <!-- Modal Header - Putih Simple -->
        <div class="sticky top-0 bg-white dark:bg-slate-900 border-b border-gray-200 dark:border-slate-700 px-8 py-6 rounded-t-2xl">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Tambah Gaji</h2>
                    <p class="text-gray-600 mt-1">Lengkapi data gaji karyawan</p>
                </div>
                <button onclick="closeInputGajiModal()" class="text-gray-400 hover:text-gray-600 hover:bg-gray-100 p-2 rounded-xl transition-all duration-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Modal Body -->
        <form id="inputGajiForm" action="{{ route('salary.store-simple') }}" method="POST" class="p-8 space-y-6">
            @csrf
            
            <!-- Bulan & Tahun dalam 1 baris -->
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-slate-300 mb-1.5">Bulan</label>
                    <select name="bulan" required
                            class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-400">
                        @for($i = 1; $i <= 12; $i++)
                            <option value="{{ $i }}" {{ $i == date('n') ? 'selected' : '' }}>
                                {{ DateTime::createFromFormat('!m', $i)->format('F') }}
                            </option>
                        @endfor
                    </select>
                </div>
                
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-slate-300 mb-1.5">Tahun</label>
                    <select name="tahun" required
                            class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-400">
                        @for($y = 2020; $y <= 2035; $y++)
                            <option value="{{ $y }}" {{ $y == date('Y') ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
            </div>
            
            <!-- Nominal Gaji - Compact -->
            <div>
                <label class="block text-xs font-medium text-gray-700 dark:text-slate-300 mb-1.5">Nominal Gaji</label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 dark:text-slate-400 text-sm">Rp</span>
                    <input type="text" name="nominal_gaji" inputmode="numeric" autocomplete="off" required
                           class="w-full pl-10 pr-3 py-2 text-sm border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:focus:ring-blue-400"
                           placeholder="0" oninput="formatNominal(this)" onkeyup="formatNominal(this)">
                    <input type="hidden" name="nominal_gaji_raw" id="nominalGajiRaw">
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="flex justify-end space-x-4 pt-6 mt-8 border-t border-gray-200">
                <button type="button" onclick="closeInputGajiModal()" 
                            class="px-6 py-3 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition-all duration-200 font-medium dark:bg-slate-700 dark:text-slate-200 dark:hover:bg-slate-600">
                    Batal
                </button>
                <button type="submit" 
                        class="px-6 py-3 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-all duration-200 font-medium shadow-lg hover:shadow-xl">
                    <i class="fas fa-save mr-2"></i>
                    Simpan Gaji
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Gaji - Style Seperti Employee -->
<div id="editGajiModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
    <div class="modal-gaji-container bg-white dark:bg-slate-900 dark:text-slate-200 rounded-2xl shadow-2xl w-full max-w-md max-h-[90vh] overflow-y-auto border border-transparent dark:border-slate-700">
        <!-- Modal Header -->
        <div class="sticky top-0 bg-white border-b border-gray-200 px-8 py-6 rounded-t-2xl">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Edit Gaji</h2>
                    <p class="text-gray-600 mt-1">Perbarui data gaji karyawan</p>
                </div>
                <button onclick="closeEditGajiModal()" class="text-gray-400 hover:text-gray-600 hover:bg-gray-100 p-2 rounded-xl transition-all duration-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Modal Body -->
        <form id="editGajiForm" method="POST" class="p-8 space-y-6">
            @csrf
            @method('PUT')
            
            <!-- Data Hidden -->
            <input type="hidden" name="jenis_gaji" id="edit_jenis_gaji" value="borongan">
            <input type="hidden" name="jumlah_hari" id="edit_jumlah_hari" value="1">
            <input type="hidden" name="tarif_harian" id="edit_tarif_harian" value="0">
            <input type="hidden" name="jumlah_unit" id="edit_jumlah_unit" value="1">
            <input type="hidden" name="tarif_per_unit" id="edit_tarif_per_unit" value="0">
            <input type="hidden" name="tunjangan" value="0">
            <input type="hidden" name="bonus" value="0">
            <input type="hidden" name="lembur" value="0">
            <input type="hidden" name="potongan_pajak" value="0">
            <input type="hidden" name="potongan_bpjs" value="0">
            <input type="hidden" name="potongan_lain" value="0">
            <input type="hidden" name="status_pembayaran" value="dibayar">
            <input type="hidden" name="tanggal_bayar" id="edit_tanggal_bayar">
            <input type="hidden" name="keterangan" value="">
            
            <!-- Bulan & Tahun -->
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-calendar text-blue-500 mr-2"></i>
                        Bulan *
                    </label>
                    <select name="bulan" id="edit_bulan" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @for($i = 1; $i <= 12; $i++)
                            <option value="{{ $i }}">{{ DateTime::createFromFormat('!m', $i)->format('F') }}</option>
                        @endfor
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-calendar-alt text-green-500 mr-2"></i>
                        Tahun *
                    </label>
                    <select name="tahun" id="edit_tahun" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @for($y = 2020; $y <= 2035; $y++)
                            <option value="{{ $y }}">{{ $y }}</option>
                        @endfor
                    </select>
                </div>
            </div>
            
            <!-- Gaji Pokok -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-money-bill-wave text-green-500 mr-2"></i>
                    Gaji Pokok *
                </label>
                <div class="relative">
                    <span class="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-500">Rp</span>
                    <input type="number" name="gaji_pokok" id="edit_gaji_pokok" required
                           class="w-full pl-12 pr-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="0" oninput="updateTarifPerUnit()">
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="flex justify-end space-x-4 pt-6 mt-8 border-t border-gray-200">
                <button type="button" onclick="closeEditGajiModal()" 
                        class="px-6 py-3 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition-all duration-200 font-medium dark:bg-slate-700 dark:text-slate-200 dark:hover:bg-slate-600">
                    Batal
                </button>
                <button type="submit" 
                        class="px-6 py-3 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-all duration-200 font-medium shadow-lg hover:shadow-xl">
                    <i class="fas fa-save mr-2"></i>
                    Update Gaji
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
// Modal Functions
function openInputGajiModal() {
    const modal = document.getElementById('inputGajiModal');
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    
    // Focus pada input tanggal
    setTimeout(() => {
        document.querySelector('input[name="tanggal_gaji"]').focus();
    }, 100);
}

function closeInputGajiModal() {
    const modal = document.getElementById('inputGajiModal');
    modal.classList.add('hidden');
    document.body.style.overflow = '';
    
    // Reset form
    document.getElementById('inputGajiForm').reset();
}

// Edit Modal Functions - Parameter langsung seperti employee
function openEditModal(id, bulan, tahun, gaji) {
    const modal = document.getElementById('editGajiModal');
    const form = document.getElementById('editGajiForm');
    
    // Set form action
    form.action = `{{ url('/salary') }}/${id}`;
    
    // Fill form fields
    document.getElementById('edit_bulan').value = bulan;
    document.getElementById('edit_tahun').value = tahun;
    document.getElementById('edit_gaji_pokok').value = gaji;
    document.getElementById('edit_tarif_per_unit').value = gaji;
    document.getElementById('edit_tanggal_bayar').value = new Date().toISOString().split('T')[0];
    
    // Show modal
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeEditGajiModal() {
    const modal = document.getElementById('editGajiModal');
    modal.classList.add('hidden');
    document.body.style.overflow = '';
    document.getElementById('editGajiForm').reset();
}

// Update tarif per unit saat gaji pokok berubah
function updateTarifPerUnit() {
    const gajiPokok = document.getElementById('edit_gaji_pokok').value;
    document.getElementById('edit_tarif_per_unit').value = gajiPokok;
}

// Submit form edit via AJAX
document.addEventListener('DOMContentLoaded', function() {
    const editForm = document.getElementById('editGajiForm');
    if (editForm) {
        editForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const url = this.action;
            
            // Debug: Log form data
            console.log('=== SUBMIT EDIT GAJI ===');
            console.log('URL:', url);
            for (let pair of formData.entries()) {
                console.log(pair[0] + ': ' + pair[1]);
            }
            
            fetch(url, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                console.log('Response status:', response.status);
                return response.json();
            })
            .then(data => {
                console.log('Response data:', data);
                if (data.success) {
                    alert('Gaji berhasil diperbarui!');
                    closeEditGajiModal();
                    window.location.reload();
                } else {
                    // Tampilkan detail error jika ada
                    let errorMsg = 'Gagal update: ' + (data.message || 'Unknown error');
                    if (data.errors) {
                        errorMsg += '\n\nDetail Error:\n';
                        for (let field in data.errors) {
                            errorMsg += '- ' + field + ': ' + data.errors[field].join(', ') + '\n';
                        }
                    }
                    alert(errorMsg);
                }
            })
            .catch(error => {
                console.error('Fetch Error:', error);
                alert('Terjadi kesalahan saat update gaji: ' + error.message);
            });
        });
    }
});

// Format nominal gaji saat user mengetik
function formatNominal(input) {
    // Ambil posisi caret sebelum format
    const start = input.selectionStart;
    const end = input.selectionEnd;
    const prev = input.value;

    // Ambil hanya digit, jangan reset jika kosong
    const digits = prev.replace(/\D/g, '');

    if (digits === '') {
        // Biarkan kosong (jangan paksa ke 0)
        input.dataset.raw = '';
        // Tetap tampilkan kosong agar dinamis
        input.value = '';
        document.getElementById('nominalGajiRaw').value = '';
        return;
    }

    // Simpan nilai mentah untuk kebutuhan lain jika perlu
    input.dataset.raw = digits;
    document.getElementById('nominalGajiRaw').value = digits;

    // Format dengan pemisah ribuan tanpa menghilangkan leading zero
    // Contoh: "0001234" => "0.001.234"
    const formatted = digits.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    input.value = formatted;

    // Hitung perubahan panjang untuk menjaga posisi caret
    const diff = input.value.length - prev.length;
    const newPos = Math.max(0, (start ?? input.value.length) + diff);
    try {
        input.setSelectionRange(newPos, newPos);
    } catch (_) {
        // Abaikan jika browser tidak mendukung pada saat tertentu
    }
}

// Filter tabel berdasarkan bulan dan tahun
function filterByMonthYear() {
    const selectedMonth = document.getElementById('filterMonth').value;
    const selectedYear = document.getElementById('filterYear').value;
    const rows = document.querySelectorAll('#salaryTableBody tr[data-month]');
    let totalBulanIni = 0;
    
    rows.forEach(row => {
        const rowMonth = row.getAttribute('data-month');
        const rowYear = row.getAttribute('data-year');
        
        let showRow = true;
        
        // Filter berdasarkan bulan dan tahun
        if (selectedMonth && rowMonth != selectedMonth) {
            showRow = false;
        }
        if (selectedYear && rowYear != selectedYear) {
            showRow = false;
        }
        
        if (showRow) {
            row.style.display = '';
            // Hitung total gaji untuk bulan dan tahun ini
            const totalCell = row.querySelector('td[data-total] div');
            const gajiText = totalCell ? totalCell.textContent : '0';
            const gaji = parseInt(gajiText.replace(/[^\d]/g, '')) || 0;
            totalBulanIni += gaji;
        } else {
            row.style.display = 'none';
        }
    });
    
    // Update total bulan ini
    document.getElementById('totalHariIni').textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(totalBulanIni);
}


// Toggle status pembayaran (Hapus jika sudah dibayar)
async function toggleStatus(salaryId) {
    if (!confirm('Apakah Anda yakin ingin menghapus data gaji ini?')) {
        return;
    }
    
    try {
        const response = await fetch(`/salary/${salaryId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            showNotification('Data gaji berhasil dihapus', 'success');
            // Reload halaman
            setTimeout(() => location.reload(), 1000);
        } else {
            showNotification('Gagal menghapus data gaji', 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        showNotification('Terjadi kesalahan saat menghapus data', 'error');
    }
}

// Show notification
function showNotification(message, type = 'success') {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-50 px-4 py-3 rounded-lg shadow-lg text-white ${
        type === 'success' ? 'bg-green-500' : 'bg-red-500'
    } transform translate-x-full opacity-0 transition-all duration-300`;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.classList.remove('translate-x-full', 'opacity-0');
    }, 100);
    
    setTimeout(() => {
        notification.classList.add('translate-x-full', 'opacity-0');
        setTimeout(() => {
            document.body.removeChild(notification);
        }, 300);
    }, 3000);
}

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    // Initial filter - DINONAKTIFKAN agar semua data tampil
    // filterByMonthYear();
    
    // Close modal dengan ESC key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeInputGajiModal();
        }
    });
    
    // Close modal saat klik di luar
    document.getElementById('inputGajiModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeInputGajiModal();
        }
    });
    
    // Handle form submit
    document.getElementById('inputGajiForm').addEventListener('submit', function(e) {
        const nominal = document.querySelector('input[name="nominal_gaji"]').value.replace(/[^\d]/g, '');
        
        if (!nominal) {
            e.preventDefault();
            showNotification('Mohon isi nominal gaji', 'error');
            return;
        }
        
        // Set raw value before submit
        document.getElementById('nominalGajiRaw').value = nominal;
    });
});

</script>
@endpush

@endsection
