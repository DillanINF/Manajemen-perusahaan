@extends('layouts.app')

@section('title', 'Manajemen Karyawan')

@section('content')
<div class="space-y-8">
    <style>
        /* Force modal width to be compact */
        .modal-employee {
            max-width: 28rem !important; /* 448px - sama dengan max-w-md */
        }
        
        /* Dark mode styles for employee modals (non-intrusive to light mode) */
        html.dark .modal-employee { background-color: #0f172a !important; color: #e5e7eb !important; } /* slate-900 bg, gray-200 text */
        html.dark .modal-employee .sticky { background-color: #0f172a !important; border-color: rgba(255,255,255,0.1) !important; }
        html.dark .modal-employee label { color: #e5e7eb !important; }
        html.dark .modal-employee input,
        html.dark .modal-employee textarea,
        html.dark .modal-employee select { background-color: #1f2937 !important; /* gray-800 */ color: #e5e7eb !important; border-color: #374151 !important; }
        html.dark .modal-employee input::placeholder,
        html.dark .modal-employee textarea::placeholder { color: #9ca3af !important; }
        /* Form content colors */
        html.dark .modal-employee form,
        html.dark .modal-employee form *:not(svg):not(path) { color: #e5e7eb !important; }
        html.dark .modal-employee form input,
        html.dark .modal-employee form textarea,
        html.dark .modal-employee form select { background-color: #1f2937 !important; color: #e5e7eb !important; border-color: #374151 !important; }
        html.dark .modal-employee form input::placeholder,
        html.dark .modal-employee form textarea::placeholder { color: #9ca3af !important; }
        /* Header text in modal */
        html.dark .modal-employee .sticky h3,
        html.dark .modal-employee .sticky p { color: #e5e7eb !important; }
    </style>
    <!-- Header dengan Statistik (tanpa Karyawan Aktif) -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        <div class="bg-gradient-to-br from-blue-50 to-blue-100 border border-blue-200 rounded-xl p-6 shadow-sm hover:shadow-md transition-shadow dark:bg-white/5 dark:border-white/10 dark:from-slate-900 dark:to-slate-800">
            <div class="flex items-center">
                <div class="bg-blue-500 text-white p-3 rounded-xl mr-4 shadow-lg dark:bg-blue-500/30 dark:text-blue-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-1 dark:text-gray-300">Total Karyawan</p>
                    <p class="text-2xl font-bold text-blue-600 dark:text-blue-300">{{ $totalKaryawan ?? 0 }}</p>
                </div>
            </div>
        </div>
        <div class="bg-gradient-to-br from-indigo-50 to-indigo-100 border border-indigo-200 rounded-xl p-6 shadow-sm hover:shadow-md transition-shadow dark:bg-white/5 dark:border-white/10 dark:from-slate-900 dark:to-slate-800">
            <div class="flex items-center">
                <div class="bg-indigo-500 text-white p-3 rounded-xl mr-4 shadow-lg dark:bg-indigo-500/30 dark:text-indigo-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.333 0-4 1-4 4s2.667 4 4 4 4-1 4-4z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-1 dark:text-gray-300">Total Gaji</p>
                    <p class="text-2xl font-bold text-indigo-600 dark:text-indigo-300">Rp {{ number_format($totalGaji ?? 0, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
        <div class="bg-gradient-to-br from-amber-50 to-amber-100 border border-amber-200 rounded-xl p-6 shadow-sm hover:shadow-md transition-shadow dark:bg-white/5 dark:border-white/10 dark:from-slate-900 dark:to-slate-800">
            <div class="flex items-center">
                <div class="bg-amber-500 text-white p-3 rounded-xl mr-4 shadow-lg dark:bg-amber-500/30 dark:text-amber-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600 mb-1 dark:text-gray-300">Rata-rata Gaji</p>
                    <p class="text-2xl font-bold text-amber-600 dark:text-amber-300">Rp {{ number_format($rataRataGaji ?? 0, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
    <div class="bg-green-50 dark:bg-green-900/20 border-l-4 border-green-500 p-4 mb-6 rounded-r-lg shadow-sm">
        <div class="flex items-center">
            <svg class="w-5 h-5 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span class="text-green-700 dark:text-green-300 font-medium">{{ session('success') }}</span>
        </div>
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 p-4 mb-6 rounded-r-lg shadow-sm">
        <div class="flex items-center">
            <svg class="w-5 h-5 text-red-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span class="text-red-700 dark:text-red-300 font-medium">{{ session('error') }}</span>
        </div>
    </div>
    @endif

    <!-- Header Section - Like Salary Page -->
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700 p-6 mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-slate-100">Data Karyawan</h1>
                    <p class="text-sm text-gray-500 dark:text-slate-400 mt-0.5">Kelola informasi karyawan perusahaan</p>
                </div>
            </div>
            <button onclick="openModal('tambahModal')" 
                    class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors shadow-sm flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah Karyawan
            </button>
        </div>
    </div>

    <!-- Daftar Karyawan (Table View) -->
    <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-gray-200 dark:border-slate-700">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 dark:bg-slate-700/50 text-gray-600 dark:text-slate-200">
                    <tr>
                        <th class="px-4 py-3 text-left w-16">No</th>
                        <th class="px-4 py-3 text-left">Nama</th>
                        <th class="px-4 py-3 text-left">No. Telepon</th>
                        <th class="px-4 py-3 text-left">Alamat</th>
                        <th class="px-4 py-3 text-left">Posisi</th>
                        <th class="px-4 py-3 text-center w-40">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-slate-700">
                    @forelse($employees ?? [] as $employee)
                    <tr class="hover:bg-gray-50 dark:hover:bg-slate-700/40">
                        <td class="px-4 py-3 text-gray-700 dark:text-slate-200">{{ $loop->iteration }}</td>
                        <td class="px-4 py-3 font-medium text-gray-900 dark:text-slate-100">{{ $employee->nama_karyawan }}</td>
                        <td class="px-4 py-3 text-gray-700 dark:text-slate-200">{{ $employee->no_telepon ?? '-' }}</td>
                        <td class="px-4 py-3 text-gray-700 dark:text-slate-200">{{ $employee->alamat ?? '-' }}</td>
                        <td class="px-4 py-3 text-gray-700 dark:text-slate-200">{{ $employee->posisi ?? '-' }}</td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-center gap-2">
                                <x-table.action-buttons 
                                    onEdit="editEmployee({{ json_encode($employee) }})"
                                    deleteAction="{{ route('employee.destroy', $employee) }}"
                                    confirmText="Yakin ingin menghapus karyawan {{ $employee->nama_karyawan }}?" />
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-gray-500 dark:text-slate-300">Belum ada data karyawan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah Karyawan -->
<div id="tambahModal" class="fixed inset-0 bg-black bg-opacity-50 hidden overflow-y-auto h-full w-full z-50 flex items-center justify-center p-4">
    <div class="modal-employee relative bg-white rounded-2xl shadow-2xl w-full max-w-md max-h-[90vh] overflow-y-auto">
        <!-- Header Simple Seperti Edit -->
        <div class="sticky top-0 bg-white border-b border-gray-200 px-8 py-6 rounded-t-2xl">
            <div class="flex justify-between items-center">
                <div>
                    <h3 class="text-2xl font-bold text-gray-900">Tambah Karyawan</h3>
                    <p class="text-gray-600 mt-1">Lengkapi informasi karyawan</p>
                </div>
                <button onclick="closeModal('tambahModal')" class="text-gray-400 hover:text-gray-600 hover:bg-gray-100 p-2 rounded-xl transition-all duration-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
        
        <!-- Form Content -->
        <div class="p-8">
            <form method="POST" action="{{ route('employee.store') }}" class="space-y-6">
                @csrf
                
                <!-- Error Display -->
                @if($errors->any())
                <div class="bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 p-4 rounded-lg mb-4">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-red-500 mt-0.5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div class="flex-1">
                            <h3 class="text-sm font-semibold text-red-800 dark:text-red-200 mb-1">Terjadi kesalahan:</h3>
                            <ul class="text-sm text-red-700 dark:text-red-300 list-disc list-inside space-y-1">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
                @endif
                
                <div class="space-y-6">
                    <!-- Nama Karyawan -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-user text-blue-500 mr-2"></i>
                            Nama Karyawan *
                        </label>
                        <input type="text" name="nama_karyawan" required 
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                               placeholder="Masukkan nama karyawan">
                    </div>

                    <!-- No. Telepon -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-phone text-green-500 mr-2"></i>
                            No. Telepon *
                        </label>
                        <input type="text" name="no_telepon" required 
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                               placeholder="Masukkan nomor telepon">
                    </div>

                    <!-- Alamat -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-map-marker-alt text-red-500 mr-2"></i>
                            Alamat *
                        </label>
                        <textarea name="alamat" required rows="3" 
                                  class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 resize-none"
                                  placeholder="Masukkan alamat lengkap"></textarea>
                    </div>

                    <!-- Posisi -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-briefcase text-purple-500 mr-2"></i>
                            Posisi *
                        </label>
                        <input type="text" name="posisi" required 
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                               placeholder="Masukkan posisi/jabatan">
                    </div>
                </div>
                
                <div class="flex justify-end space-x-4 pt-6 mt-8 border-t border-gray-200">
                    <button type="button" onclick="closeModal('tambahModal')" 
                            class="px-6 py-3 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition-all duration-200 font-medium">
                        Batal
                    </button>
                    <button type="submit" 
                            class="px-6 py-3 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-all duration-200 font-medium shadow-lg hover:shadow-xl">
                        <i class="fas fa-save mr-2"></i>
                        Simpan Karyawan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Added Edit Modal -->
<!-- Modal Edit Karyawan -->
<div id="editModal" class="fixed inset-0 bg-black bg-opacity-50 hidden overflow-y-auto h-full w-full z-50 flex items-center justify-center p-4">
    <div class="modal-employee relative bg-white rounded-2xl shadow-2xl w-full max-w-md max-h-[90vh] overflow-y-auto">
        <div class="sticky top-0 bg-white border-b border-gray-200 px-8 py-6 rounded-t-2xl">
            <div class="flex justify-between items-center">
                <div>
                    <h3 class="text-2xl font-bold text-gray-900">Edit Karyawan</h3>
                    <p class="text-gray-600 mt-1">Perbarui informasi karyawan</p>
                </div>
                <button onclick="closeModal('editModal')" class="text-gray-400 hover:text-gray-600 hover:bg-gray-100 p-2 rounded-xl transition-all duration-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
        
        <form id="editForm" method="POST" class="p-8">
            @csrf
            @method('PUT')
            
            <div class="space-y-6">
                <!-- Nama Karyawan -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-user text-blue-500 mr-2"></i>
                        Nama Karyawan *
                    </label>
                    <input type="text" id="edit_nama_karyawan" name="nama_karyawan" required 
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                           placeholder="Masukkan nama karyawan">
                </div>

                <!-- No. Telepon -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-phone text-green-500 mr-2"></i>
                        No. Telepon *
                    </label>
                    <input type="text" id="edit_no_telepon" name="no_telepon" required 
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                           placeholder="Masukkan nomor telepon">
                </div>

                <!-- Alamat -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-map-marker-alt text-red-500 mr-2"></i>
                        Alamat *
                    </label>
                    <textarea id="edit_alamat" name="alamat" required rows="3" 
                              class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 resize-none"
                              placeholder="Masukkan alamat lengkap"></textarea>
                </div>

                <!-- Posisi -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-briefcase text-purple-500 mr-2"></i>
                        Posisi *
                    </label>
                    <input type="text" id="edit_posisi" name="posisi" required 
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                           placeholder="Masukkan posisi/jabatan">
                </div>
            </div>
            
            <div class="flex justify-end space-x-4 pt-6 mt-8 border-t border-gray-200">
                <button type="button" onclick="closeModal('editModal')" 
                        class="px-6 py-3 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition-all duration-200 font-medium">
                    Batal
                </button>
                <button type="submit" 
                        class="px-6 py-3 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-all duration-200 font-medium shadow-lg hover:shadow-xl">
                    <i class="fas fa-save mr-2"></i>
                    Update Karyawan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openModal(modalId) {
    const modal = document.getElementById(modalId);
    const modalContent = modal.querySelector('.modal-employee');
    
    // Show modal
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    
    // Trigger animation
    setTimeout(() => {
        modal.classList.remove('opacity-0');
        modal.classList.add('opacity-100');
        modalContent.classList.remove('scale-95', 'translate-y-4');
        modalContent.classList.add('scale-100', 'translate-y-0');
    }, 10);
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    const modalContent = modal.querySelector('.modal-employee');
    
    // Trigger exit animation
    modal.classList.remove('opacity-100');
    modal.classList.add('opacity-0');
    modalContent.classList.remove('scale-100', 'translate-y-0');
    modalContent.classList.add('scale-95', 'translate-y-4');
    
    // Hide modal after animation
    setTimeout(() => {
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
    }, 300);
}

function editEmployee(employee) {
    // Populate form fields - hanya 4 field yang tersisa
    document.getElementById('edit_nama_karyawan').value = employee.nama_karyawan || '';
    document.getElementById('edit_no_telepon').value = employee.no_telepon || '';
    document.getElementById('edit_alamat').value = employee.alamat || '';
    document.getElementById('edit_posisi').value = employee.posisi || '';
    
    // Set form action URL
    document.getElementById('editForm').action = `{{ url('/employee') }}/${employee.id}`;
    
    // Open modal
    openModal('editModal');
}

// Close modal when clicking outside
document.addEventListener('click', function(event) {
    const tambahModal = document.getElementById('tambahModal');
    const editModal = document.getElementById('editModal');
    
    if (event.target === tambahModal) {
        closeModal('tambahModal');
    }
    if (event.target === editModal) {
        closeModal('editModal');
    }
});

// Close modal with Escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeModal('tambahModal');
        closeModal('editModal');
    }
});
</script>
@endsection
