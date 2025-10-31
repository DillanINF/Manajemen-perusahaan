# Blade Components Guide

## 📚 Daftar Komponen Reusable

Dokumentasi lengkap untuk semua Blade Components yang telah dibuat untuk konsistensi UI.

---

## 1. Page Header Component

**Lokasi:** `resources/views/components/page-header.blade.php`

### Penggunaan:
```blade
<x-page-header 
    title="Data Invoice" 
    subtitle="Kelola semua invoice dan pesanan"
    icon="fas fa-file-invoice-dollar"
    iconBg="from-indigo-500 to-purple-600">
    
    <x-slot name="actions">
        <x-button variant="primary" icon="fas fa-plus">
            Tambah Invoice
        </x-button>
    </x-slot>
</x-page-header>
```

### Props:
- `title` (required): Judul halaman
- `subtitle` (optional): Subtitle/deskripsi
- `icon` (optional): Font Awesome icon class, default: `fas fa-file`
- `iconBg` (optional): Gradient background, default: `from-indigo-500 to-purple-600`

### Slot:
- `actions`: Slot untuk tombol atau action di kanan header

---

## 2. Statistic Card Component

**Lokasi:** `resources/views/components/stat-card.blade.php`

### Penggunaan:
```blade
<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <x-stat-card 
        label="Total Karyawan" 
        value="25">
        <x-slot name="icon">
            <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path d="M17 20h5v-2a3 3 0 00-5.356-1.857"/>
            </svg>
        </x-slot>
    </x-stat-card>
    
    <x-stat-card 
        label="Total Gaji" 
        value="Rp {{ number_format($totalGaji, 0, ',', '.') }}">
        <x-slot name="icon">
            <svg class="w-6 h-6 text-green-600 dark:text-green-400">...</svg>
        </x-slot>
    </x-stat-card>
</div>
```

### Props:
- `label` (required): Label kartu
- `value` (required): Nilai yang ditampilkan
- `iconColor` (optional): Class warna icon

### Slot:
- `icon`: SVG icon untuk kartu

---

## 3. Alert Component

**Lokasi:** `resources/views/components/alert.blade.php`

### Penggunaan:
```blade
<!-- Flash messages otomatis -->
@if(session('success'))
    <x-alert type="success" :message="session('success')" />
@endif

@if(session('error'))
    <x-alert type="error" :message="session('error')" />
@endif

<!-- Custom content -->
<x-alert type="warning" :dismissible="false">
    <strong>Peringatan!</strong> Stok produk hampir habis.
</x-alert>

<!-- Info alert -->
<x-alert type="info" message="Data berhasil disimpan ke database." />
```

### Props:
- `type` (optional): `success`, `error`, `warning`, `info`. Default: `info`
- `message` (optional): Pesan alert
- `dismissible` (optional): Boolean, default: `true`

---

## 4. Modal Form Component

**Lokasi:** `resources/views/components/modal-form.blade.php`

### Penggunaan:
```blade
<!-- Trigger button -->
<x-button variant="primary" x-on:click="$dispatch('open-modal-add-employee')">
    Tambah Karyawan
</x-button>

<!-- Modal -->
<x-modal-form name="add-employee" title="Tambah Karyawan Baru" :compact="true">
    <form action="{{ route('employees.store') }}" method="POST">
        @csrf
        
        <x-form-group label="Nama Lengkap" for="nama" required>
            <input type="text" id="nama" name="nama" 
                   class="w-full rounded-lg border-gray-300 dark:border-gray-600">
        </x-form-group>
        
        <div class="flex justify-end gap-3 mt-6">
            <x-button variant="outline" x-on:click="$dispatch('close-modal-add-employee')">
                Batal
            </x-button>
            <x-button variant="primary" type="submit">
                Simpan
            </x-button>
        </div>
    </form>
</x-modal-form>
```

### Props:
- `name` (required): Unique identifier untuk modal
- `title` (required): Judul modal
- `maxWidth` (optional): `sm`, `md`, `lg`, `xl`, `2xl`. Default: `md`
- `compact` (optional): Boolean untuk modal compact (28rem). Default: `false`

### Event:
- Buka: `$dispatch('open-modal-{name}')`
- Tutup: `$dispatch('close-modal-{name}')` atau `$dispatch('close-modal')`

---

## 5. Card Component

**Lokasi:** `resources/views/components/card.blade.php`

### Penggunaan:
```blade
<x-card title="Informasi Karyawan">
    <p>Konten card di sini...</p>
</x-card>

<!-- Tanpa title -->
<x-card>
    <div class="space-y-4">
        <p>Content...</p>
    </div>
</x-card>

<!-- Tanpa padding & shadow -->
<x-card :padding="false" :shadow="false">
    <img src="..." class="w-full">
</x-card>
```

### Props:
- `title` (optional): Judul card
- `padding` (optional): Boolean, default: `true`
- `shadow` (optional): Boolean, default: `true`

---

## 6. Table Components

### Table Wrapper

**Lokasi:** `resources/views/components/table/wrapper.blade.php`

```blade
<x-table-wrapper :responsive="true" minWidth="1200px">
    <thead>
        <tr>
            <x-table-th>No</x-table-th>
            <x-table-th sortable>Nama</x-table-th>
            <x-table-th align="center">Status</x-table-th>
            <x-table-th align="right">Gaji</x-table-th>
        </tr>
    </thead>
    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
        @foreach($employees as $employee)
        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
            <x-table-td>{{ $loop->iteration }}</x-table-td>
            <x-table-td>{{ $employee->nama }}</x-table-td>
            <x-table-td align="center">
                <x-badge type="success">Aktif</x-badge>
            </x-table-td>
            <x-table-td align="right">
                Rp {{ number_format($employee->gaji, 0, ',', '.') }}
            </x-table-td>
        </tr>
        @endforeach
    </tbody>
</x-table-wrapper>
```

### Props Table Wrapper:
- `responsive` (optional): Boolean, default: `true`
- `minWidth` (optional): Min width untuk scroll, default: `1000px`

### Props Table TH:
- `sortable` (optional): Boolean, menampilkan icon sort
- `align` (optional): `left`, `center`, `right`. Default: `left`

### Props Table TD:
- `align` (optional): `left`, `center`, `right`. Default: `left`

---

## 7. Badge Component

**Lokasi:** `resources/views/components/badge.blade.php`

### Penggunaan:
```blade
<x-badge type="success">Aktif</x-badge>
<x-badge type="error">Ditolak</x-badge>
<x-badge type="warning">Pending</x-badge>
<x-badge type="info">Diproses</x-badge>
<x-badge type="default">Draft</x-badge>

<!-- Dengan size -->
<x-badge type="success" size="sm">Kecil</x-badge>
<x-badge type="success" size="lg">Besar</x-badge>

<!-- Status PO -->
<x-badge type="accept">Accept</x-badge>
<x-badge type="pending">Pending</x-badge>
<x-badge type="reject">Reject</x-badge>
```

### Props:
- `type` (optional): `success`, `error`, `warning`, `info`, `default`, `pending`, `accept`, `reject`. Default: `default`
- `size` (optional): `sm`, `md`, `lg`. Default: `md`

---

## 8. Form Group Component

**Lokasi:** `resources/views/components/form/group.blade.php`

### Penggunaan:
```blade
<x-form-group label="Email" for="email" required :error="$errors->first('email')">
    <input type="email" id="email" name="email" 
           class="w-full rounded-lg border-gray-300 dark:border-gray-600">
</x-form-group>

<x-form-group label="Password" for="password" required 
              hint="Minimal 8 karakter">
    <input type="password" id="password" name="password">
</x-form-group>
```

### Props:
- `label` (optional): Label untuk field
- `for` (optional): ID input yang di-label
- `required` (optional): Boolean, menampilkan asterisk merah
- `error` (optional): Error message
- `hint` (optional): Helper text (tidak ditampilkan jika ada error)

---

## 9. Button Component

**Lokasi:** `resources/views/components/button.blade.php`

### Penggunaan:
```blade
<!-- Basic -->
<x-button variant="primary">Simpan</x-button>
<x-button variant="secondary">Batal</x-button>
<x-button variant="danger">Hapus</x-button>

<!-- Dengan icon -->
<x-button variant="success" icon="fas fa-check">
    Approve
</x-button>

<x-button variant="primary" icon="fas fa-arrow-right" iconPosition="right">
    Lanjutkan
</x-button>

<!-- Loading state -->
<x-button variant="primary" :loading="$isLoading">
    Proses
</x-button>

<!-- Size -->
<x-button variant="primary" size="sm">Kecil</x-button>
<x-button variant="primary" size="lg">Besar</x-button>

<!-- Submit button -->
<x-button type="submit" variant="primary">Submit Form</x-button>
```

### Props:
- `variant` (optional): `primary`, `secondary`, `success`, `danger`, `warning`, `info`, `outline`. Default: `primary`
- `size` (optional): `sm`, `md`, `lg`. Default: `md`
- `icon` (optional): Font Awesome icon class
- `iconPosition` (optional): `left`, `right`. Default: `left`
- `loading` (optional): Boolean untuk loading state
- `type` (optional): Button type. Default: `button`

---

## 10. Empty State Component

**Lokasi:** `resources/views/components/empty-state.blade.php`

### Penggunaan:
```blade
<!-- Basic -->
<x-empty-state />

<!-- Custom -->
<x-empty-state 
    icon="fas fa-users"
    title="Belum ada karyawan"
    description="Mulai tambahkan karyawan baru ke sistem."
    actionText="Tambah Karyawan"
    actionUrl="{{ route('employees.create') }}" />

<!-- Dengan custom action -->
<x-empty-state title="Tidak ada data">
    <x-button variant="primary" x-on:click="$dispatch('open-modal-add')">
        Tambah Data
    </x-button>
</x-empty-state>
```

### Props:
- `icon` (optional): Font Awesome icon. Default: `fas fa-inbox`
- `title` (optional): Judul. Default: `Tidak ada data`
- `description` (optional): Deskripsi
- `actionText` (optional): Teks tombol action
- `actionUrl` (optional): URL untuk tombol action

---

## 📝 Contoh Refactoring Halaman

### Sebelum (employees/index.blade.php):

```blade
<div class="space-y-8">
    <style>
        .modal-employee { max-width: 28rem !important; }
        html.dark .modal-employee { background-color: #0f172a !important; }
        <!-- 30+ baris CSS yang berulang -->
    </style>
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-xl p-6 shadow-sm">
            <div class="flex items-center">
                <!-- 15+ baris HTML berulang -->
            </div>
        </div>
        <!-- 2 card lagi dengan struktur yang sama -->
    </div>
    
    @if(session('success'))
    <div class="bg-green-50 dark:bg-green-900/20 border-l-4 border-green-500 p-4">
        <!-- 10+ baris alert HTML -->
    </div>
    @endif
</div>
```

### Sesudah (menggunakan components):

```blade
<div class="space-y-8">
    <!-- Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <x-stat-card label="Total Karyawan" :value="$totalKaryawan">
            <x-slot name="icon">
                <svg class="w-6 h-6 text-blue-600 dark:text-blue-400">...</svg>
            </x-slot>
        </x-stat-card>
        
        <x-stat-card label="Total Gaji" value="Rp {{ number_format($totalGaji, 0, ',', '.') }}">
            <x-slot name="icon">
                <svg class="w-6 h-6 text-green-600 dark:text-green-400">...</svg>
            </x-slot>
        </x-stat-card>
        
        <x-stat-card label="Rata-rata Gaji" value="Rp {{ number_format($rataRataGaji, 0, ',', '.') }}">
            <x-slot name="icon">
                <svg class="w-6 h-6 text-amber-600 dark:text-amber-400">...</svg>
            </x-slot>
        </x-stat-card>
    </div>
    
    <!-- Alert -->
    @if(session('success'))
        <x-alert type="success" :message="session('success')" />
    @endif
    
    <!-- Table -->
    <x-card>
        <x-table-wrapper>
            <thead>
                <tr>
                    <x-table-th>No</x-table-th>
                    <x-table-th>Nama</x-table-th>
                    <x-table-th align="center">Status</x-table-th>
                </tr>
            </thead>
            <tbody>
                @forelse($employees as $employee)
                <tr>
                    <x-table-td>{{ $loop->iteration }}</x-table-td>
                    <x-table-td>{{ $employee->nama }}</x-table-td>
                    <x-table-td align="center">
                        <x-badge type="success">Aktif</x-badge>
                    </x-table-td>
                </tr>
                @empty
                <tr>
                    <td colspan="3">
                        <x-empty-state 
                            title="Belum ada karyawan"
                            actionText="Tambah Karyawan"
                            actionUrl="{{ route('employees.create') }}" />
                    </td>
                </tr>
                @endforelse
            </tbody>
        </x-table-wrapper>
    </x-card>
</div>
```

---

## 🎯 Keuntungan Menggunakan Components

### 1. **Konsistensi UI**
- Semua halaman menggunakan styling yang sama
- Tidak ada perbedaan warna, spacing, atau ukuran

### 2. **Mudah di-Maintain**
- Update 1 component = update semua halaman
- Tidak perlu copy-paste kode

### 3. **Lebih Ringkas**
- 100+ baris HTML → 10-20 baris component
- Lebih mudah dibaca

### 4. **Dark Mode Otomatis**
- Component sudah include dark mode styling
- Tidak perlu tulis CSS dark mode manual

### 5. **Reusability**
- Component bisa digunakan di semua halaman
- DRY (Don't Repeat Yourself)

---

## 🚀 Migration Guide

### Langkah Refactoring Halaman:

1. **Identifikasi pattern yang berulang**
   - Alert messages
   - Statistics cards
   - Tables
   - Modals
   - Buttons

2. **Replace dengan component**
   ```blade
   <!-- Dari ini -->
   <div class="bg-green-50 border-l-4 border-green-500...">
   
   <!-- Jadi ini -->
   <x-alert type="success" :message="session('success')" />
   ```

3. **Test di browser**
   - Pastikan tampilan sama
   - Test dark mode
   - Test responsive

4. **Hapus CSS yang tidak terpakai**
   - CSS yang sudah di-handle component
   - Inline styles yang berulang

---

## 📦 Component Registration

Semua component otomatis ter-register di Laravel. Cukup gunakan dengan prefix `x-`:

```blade
<x-component-name />
<x-folder.component-name />
```

Contoh:
- `components/alert.blade.php` → `<x-alert />`
- `components/table/wrapper.blade.php` → `<x-table-wrapper />`
- `components/form/group.blade.php` → `<x-form-group />`

---

## 💡 Tips & Best Practices

1. **Gunakan slot untuk konten dinamis**
   ```blade
   <x-card>
       <p>Custom content here</p>
   </x-card>
   ```

2. **Named slots untuk multiple sections**
   ```blade
   <x-page-header title="Dashboard">
       <x-slot name="actions">
           <x-button>Action</x-button>
       </x-slot>
   </x-page-header>
   ```

3. **Props dengan default values**
   ```blade
   <x-alert type="success" /> <!-- Gunakan default dismissible=true -->
   <x-alert type="success" :dismissible="false" /> <!-- Override -->
   ```

4. **Combine components**
   ```blade
   <x-card title="Data Karyawan">
       <x-table-wrapper>
           <!-- Table content -->
       </x-table-wrapper>
   </x-card>
   ```

---

## 🔄 Next Steps (Optional)

1. **Buat lebih banyak components**:
   - Pagination component
   - Dropdown component
   - File upload component
   - Date picker component

2. **Class-based components** untuk logic kompleks
3. **Anonymous components** untuk component sederhana
4. **Component tests** dengan PHPUnit

---

**Happy Coding! 🚀**
