# 📋 Panduan Sinkronisasi Data Invoice ke Tabel Invoices

## 🎯 Ringkasan Perubahan

Sistem telah diubah agar **data invoice yang diinput di form Data Invoice sekarang masuk ke tabel `invoices`**, bukan lagi ke tabel `pos`.

### ✅ Yang Sudah Dilakukan:

1. **Struktur Tabel Invoices** - Disesuaikan dengan kebutuhan form
2. **Model Invoice** - Fillable diupdate
3. **Auto-Sinkronisasi** - POController otomatis menyimpan ke tabel invoices saat store/update
4. **Backward Compatibility** - JatuhTempoController tetap bisa baca data lama dari POS

---

## 📝 Langkah-Langkah Implementasi

### 1. Jalankan SQL untuk Menyesuaikan Struktur Tabel Invoices

Buka **phpMyAdmin** dan jalankan file SQL ini:

```
database/migrations/fix_invoices_table_structure.sql
```

**Apa yang dilakukan:**
- Menghapus kolom yang tidak perlu (alamat_1, alamat_2, tanggal_jatuh_tempo, dll)
- Menambahkan kolom `po_number` untuk mencocokkan dengan no urut invoice
- Menyesuaikan tipe data kolom
- Menambahkan index untuk performa

### 2. Backfill Data Existing (Opsional tapi Direkomendasikan)

Jalankan SQL ini untuk memindahkan data historis dari `pos` ke `invoices`:

```
database/migrations/backfill_pos_to_invoices.sql
```

**Apa yang dilakukan:**
- Menyalin semua data dari tabel `pos` yang valid ke tabel `invoices`
- Menggunakan `ON DUPLICATE KEY UPDATE` untuk menghindari duplikasi
- Skip data draft

### 3. Clear Cache Laravel

Jalankan di terminal:

```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

---

## 🚀 Cara Kerja Sistem Baru

### Input Data Invoice (Form PO)

Ketika Anda menyimpan data di **Form Input PO** (dari menu Data Invoice):

1. Data disimpan ke tabel `pos` (seperti biasa)
2. **OTOMATIS** juga disimpan ke tabel `invoices` melalui method `syncToInvoices()`
3. Data di tabel `invoices` di-aggregate berdasarkan `po_number` (no urut invoice)

### Halaman Jatuh Tempo

Ketika Anda membuka halaman **Jatuh Tempo**:

1. Sistem **prioritas baca dari tabel `invoices`** berdasarkan `no_invoice` atau `no_po`
2. Jika tidak ada di `invoices`, fallback ke tabel `pos` (untuk data lama)
3. Total ditampilkan dari `grand_total` jika ada, jika tidak dari `total`

---

## 🔍 Verifikasi

### Cek Data di Tabel Invoices

Buka phpMyAdmin dan jalankan:

```sql
SELECT 
    no_invoice,
    no_po,
    customer,
    tanggal_invoice,
    grand_total,
    status
FROM invoices
ORDER BY id DESC
LIMIT 10;
```

### Cek Sinkronisasi

1. **Input data baru** di form Data Invoice
2. **Cek di phpMyAdmin** tabel `invoices` - harus ada data baru dengan `po_number` yang sama
3. **Buka halaman Jatuh Tempo** - total tagihan harus sesuai dengan grand_total di tabel `invoices`

---

## 📊 Struktur Tabel Invoices (Final)

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| id | bigint | Primary key |
| no_invoice | varchar | Nomor invoice (dari po_number) |
| no_po | varchar | Nomor PO |
| customer | varchar | Nama customer |
| tanggal_invoice | date | Tanggal invoice |
| po_number | int | Nomor urut invoice (KEY untuk sinkronisasi) |
| qty | int | Total quantity |
| qty_jenis | enum | PCS/SET |
| total | decimal | Total per item |
| grand_total | decimal | **Total keseluruhan** |
| status | enum | Draft/Pending/Accept/Reject |
| created_at | timestamp | |
| updated_at | timestamp | |

---

## ⚙️ File yang Diubah

1. **app/Models/Invoice.php** - Fillable & casts
2. **app/Http/Controllers/POController.php** - Ditambahkan `syncToInvoices()` dan `mapPOStatusToInvoiceStatus()`
3. **app/Http/Controllers/JatuhTempoController.php** - `getTotalPembayaranFromInvoice()` dengan fallback ke POS
4. **database/migrations/fix_invoices_table_structure.sql** - SQL untuk struktur tabel
5. **database/migrations/backfill_pos_to_invoices.sql** - SQL untuk backfill data

---

## 🛠️ Troubleshooting

### Total Tagihan Masih Rp 0?

1. Pastikan SQL `fix_invoices_table_structure.sql` sudah dijalankan
2. Jalankan `backfill_pos_to_invoices.sql` untuk data historis
3. Clear cache: `php artisan cache:clear && php artisan config:clear`
4. Cek di phpMyAdmin apakah tabel `invoices` sudah terisi

### Data Invoice Tidak Muncul?

1. Cek di tabel `invoices` apakah `po_number` ter-set dengan benar
2. Cek di tabel `jatuh_tempos` apakah `no_invoice` cocok dengan `po_number` di invoices
3. Cek log Laravel: `storage/logs/laravel.log` untuk error sinkronisasi

### Error Saat Input Data Invoice Baru?

1. Cek constraint tabel `invoices` apakah sudah sesuai
2. Cek fillable di `app/Models/Invoice.php`
3. Cek log error di `storage/logs/laravel.log`

---

## 📞 Catatan Penting

- ✅ **Data lama tetap aman** - masih tersimpan di tabel `pos`
- ✅ **Data baru otomatis sinkron** - ke tabel `invoices` saat save/update
- ✅ **Backward compatible** - Jatuh Tempo tetap bisa baca data lama dari POS
- ✅ **No breaking changes** - Semua fitur existing tetap berfungsi

---

Dibuat: {{ date('Y-m-d') }}
