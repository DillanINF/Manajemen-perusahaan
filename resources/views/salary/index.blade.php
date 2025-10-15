@extends('layouts.app')

@section('title', 'Manajemen Gaji Karyawan')

@push('styles')
<style>
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
                <div class="flex items-center gap-2 px-4 py-2 bg-gray-50 dark:bg-slate-700/50 rounded-lg border border-gray-200 dark:border-slate-600">
                    <span class="text-sm text-gray-500 dark:text-slate-400">Total:</span>
                    <span id="totalHariIni" class="text-base font-bold text-gray-900 dark:text-slate-100">Rp 0</span>
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
                                <div class="flex items-center justify-center gap-2">
                                    <!-- Edit Button - Blue Circle -->
                                    <button onclick="window.location.href='{{ route('salary.edit', $salary->id) }}'"
                                            class="w-9 h-9 rounded-full bg-blue-500 hover:bg-blue-600 text-white flex items-center justify-center transition-colors shadow-sm"
                                            title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </button>
                                    
                                    <!-- Delete Button - Red Circle -->
                                    <form action="{{ route('salary.destroy', $salary->id) }}" method="POST" class="inline"
                                          onsubmit="return confirm('Yakin ingin menghapus data gaji ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="w-9 h-9 rounded-full bg-red-500 hover:bg-red-600 text-white flex items-center justify-center transition-colors shadow-sm"
                                                title="Hapus">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
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

<!-- Modal Input Gaji - Compact & Minimal -->
<div id="inputGajiModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm hidden z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl w-full overflow-hidden border border-gray-200 dark:border-slate-700" style="max-width: 28rem !important;">
        <!-- Modal Header - Gradient Blue -->
        <div class="px-6 py-4 bg-gradient-to-r from-blue-600 to-indigo-600 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-bold">Input Gaji</h2>
                    <p class="text-blue-100 text-xs">Lengkapi data gaji karyawan</p>
                </div>
                <button onclick="closeInputGajiModal()" class="text-white/80 hover:text-white">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Modal Body - Compact -->
        <form id="inputGajiForm" action="{{ route('salary.store-simple') }}" method="POST" class="p-6 space-y-4">
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

            <!-- Modal Footer - Compact -->
            <div class="flex items-center justify-end gap-2 pt-4 border-t border-gray-200 dark:border-slate-700 mt-4">
                <button type="button" onclick="closeInputGajiModal()" 
                        class="px-4 py-2 text-sm border border-gray-300 dark:border-slate-600 bg-white dark:bg-slate-700 text-gray-700 dark:text-slate-300 rounded-lg hover:bg-gray-50 dark:hover:bg-slate-600 transition-colors">
                    Batal
                </button>
                <button type="submit" 
                        class="px-4 py-2 text-sm bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white rounded-lg transition-colors shadow-sm">
                    Simpan
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
