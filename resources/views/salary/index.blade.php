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
        <!-- Header Section -->
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-slate-100">Manajemen Gaji Karyawan</h1>
                <p class="text-gray-600 dark:text-slate-400 mt-1">Kelola data gaji dan pengeluaran karyawan</p>
            </div>
            <button onclick="openInputGajiModal()" 
                    class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-colors duration-200 shadow-sm">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                </svg>
                Input Gaji
            </button>
        </div>

        <!-- Filter & Controls -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
            <div class="flex items-center space-x-4">
                <div class="relative">
                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">Bulan</label>
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
                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-1">Tahun</label>
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
            <div class="text-right">
                <p class="text-sm text-gray-600 dark:text-slate-400">Total Gaji Bulan Ini</p>
                <p id="totalHariIni" class="text-2xl font-bold text-green-600 dark:text-green-400">Rp 0</p>
            </div>
        </div>

        <!-- Tabel Gaji -->
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-slate-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-slate-100">Data Gaji Karyawan</h3>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-slate-400 uppercase tracking-wider">No</th>
                            <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 dark:text-slate-400 uppercase tracking-wider">Tanggal</th>
                            <th class="px-3 py-3 text-right text-xs font-medium text-gray-500 dark:text-slate-400 uppercase tracking-wider">Total Gaji</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-slate-400 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-slate-400 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="salaryTableBody" class="bg-white dark:bg-slate-800 divide-y divide-gray-200 dark:divide-slate-700">
                        @forelse($salaries ?? [] as $index => $salary)
                        <tr class="hover:bg-gray-50 dark:hover:bg-slate-700 transition-colors duration-200" data-date="{{ $salary->tanggal_bayar }}" data-month="{{ $salary->bulan }}" data-year="{{ $salary->tahun }}">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-slate-200">{{ $index + 1 }}</td>
                            <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-slate-200">
                                @if($salary->tanggal_bayar)
                                    {{ $salary->tanggal_bayar->format('d/') . strtolower($salary->tanggal_bayar->format('M')) . $salary->tanggal_bayar->format('/Y') }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-3 py-4 whitespace-nowrap text-right" data-total>
                                <div class="text-sm font-bold text-gray-900 dark:text-slate-200">Rp {{ number_format($salary->total_gaji, 0, ',', '.') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @if($salary->status_pembayaran === 'dibayar')
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                        Dibayar
                                    </span>
                                @else
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        Belum Dibayar
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-center">
                                <x-table.action-buttons 
                                    onEdit="window.location.href='{{ route('salary.edit', $salary->id) }}'"
                                    deleteAction="{{ route('salary.destroy', $salary->id) }}"
                                    confirmText="Yakin ingin menghapus data gaji ini?"
                                />
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500 dark:text-slate-400">
                                <div class="flex flex-col items-center">
                                    <svg class="w-12 h-12 text-gray-300 dark:text-slate-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    <p class="text-lg font-medium dark:text-slate-300">Belum ada data gaji</p>
                                    <p class="text-sm">Klik "Input Gaji" untuk menambah data baru</p>
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

<!-- Modal Input Gaji - Dropdown dengan styling yang sama -->
<div id="inputGajiModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-2xl max-w-md w-full">
        <!-- Modal Header -->
        <div class="px-6 py-4 border-b border-gray-200 dark:border-slate-700">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-bold text-gray-900 dark:text-slate-100">Input Gaji</h2>
                <button onclick="closeInputGajiModal()" class="p-1 hover:bg-gray-100 dark:hover:bg-slate-700 rounded-lg transition-colors duration-200">
                    <svg class="w-5 h-5 text-gray-500 dark:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Modal Body -->
        <form id="inputGajiForm" action="{{ route('salary.store-simple') }}" method="POST" class="p-6 space-y-4">
            @csrf
            
            <!-- Bulan -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-2">Bulan</label>
                <select name="bulan" required
                        class="w-full px-4 py-3 border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors duration-200">
                    @for($i = 1; $i <= 12; $i++)
                        <option value="{{ $i }}" {{ $i == date('n') ? 'selected' : '' }}>
                            {{ DateTime::createFromFormat('!m', $i)->format('F') }}
                        </option>
                    @endfor
                </select>
            </div>
            
            <!-- Tahun -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-2">Tahun</label>
                <select name="tahun" required
                        class="w-full px-4 py-3 border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors duration-200">
                    @for($y = 2020; $y <= 2035; $y++)
                        <option value="{{ $y }}" {{ $y == date('Y') ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            
            <!-- Nominal Gaji -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-2">Nominal Gaji</label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 dark:text-slate-400 text-sm font-medium">Rp</span>
                    <input type="text" name="nominal_gaji" inputmode="numeric" autocomplete="off" required
                           class="w-full pl-10 pr-4 py-3 border border-gray-300 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors duration-200"
                           placeholder="0" oninput="formatNominal(this)" onkeyup="formatNominal(this)">
                    <input type="hidden" name="nominal_gaji_raw" id="nominalGajiRaw">
                </div>
                <p class="text-xs text-gray-500 dark:text-slate-400 mt-1">Masukkan total gaji yang akan dibayarkan</p>
            </div>

            <!-- Modal Footer -->
            <div class="flex items-center justify-end space-x-3 pt-4">
                <button type="button" onclick="closeInputGajiModal()" 
                        class="px-4 py-2 border border-gray-300 dark:border-slate-600 text-gray-700 dark:text-slate-300 rounded-lg hover:bg-gray-50 dark:hover:bg-slate-700 font-medium transition-colors duration-200">
                    Batal
                </button>
                <button type="submit" 
                        class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition-colors duration-200 shadow-sm">
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
    // Initial filter
    filterByMonthYear();
    
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
