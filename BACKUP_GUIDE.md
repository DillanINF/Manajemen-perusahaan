# 📦 Panduan Backup & Restore Database
## Untuk Pemula - Bahasa Sederhana

---

## 🤔 Apa itu Backup?

**Backup = Fotocopy/Salinan Data**

Bayangkan database seperti **buku catatan perusahaan** yang berisi:
- Data invoice
- Data customer
- Data karyawan
- Data PO
- Semua data penting

**Backup = Fotocopy buku catatan ini** dan simpan di tempat aman.

**Kenapa perlu backup?**
- ✅ Jika buku asli hilang → masih ada fotocopy
- ✅ Jika buku asli rusak → bisa pakai fotocopy
- ✅ Jika ada kesalahan → bisa kembali ke versi lama

---

## 🤔 Apa itu Restore?

**Restore = Kembalikan Data dari Fotocopy**

Jika buku asli rusak/hilang:
1. Ambil fotocopy terakhir
2. Ganti buku asli dengan fotocopy
3. Data kembali seperti saat difotocopy

**⚠️ PENTING:** 
- Data setelah fotocopy akan hilang
- Contoh: Fotocopy kemarin → data hari ini hilang

---

## 🚀 Fitur yang Tersedia

### 1. **Backup Database (Fotocopy Data)**
- Buat salinan database otomatis
- Simpan dengan nama tanggal (contoh: backup_2025-10-08.sql)
- Hapus salinan lama otomatis (simpan 30 hari terakhir)
- Lokasi file: `storage/app/backups/`

### 2. **Restore Database (Kembalikan Data)**
- Kembalikan database dari salinan
- Pilih salinan mana yang mau dipakai
- Otomatis backup dulu sebelum restore (aman!)

---

## 📝 Cara Menggunakan (Langkah Mudah)

### **1️⃣ BACKUP DATABASE (Buat Salinan)**

**Kapan digunakan?**
- Setiap hari (otomatis via Task Scheduler)
- Sebelum update aplikasi
- Sebelum hapus data penting
- Kapan saja mau aman

**Cara pakai:**
```bash
# Buka PowerShell/CMD di folder project
cd "C:\Users\DILAN\login-app - Copy - Copy"

# Jalankan backup
php artisan backup:database
```

**Hasil:**
```
🔄 Mulai backup...
💾 Backup database: manajemen_perusahaan
✅ Backup berhasil!
📦 File: backup_2025-10-08_142530.sql
📊 Ukuran: 2.45 MB
📍 Lokasi: storage\app\backups\

🗑️  Hapus backup lama (simpan 30 hari terakhir)...
✅ Tidak ada backup lama
📦 Total backup: 1 file
```

**Artinya:**
- ✅ Data sudah di-fotocopy
- ✅ Disimpan dengan nama tanggal
- ✅ Backup lama (>30 hari) otomatis dihapus

---

### **2️⃣ LIHAT DAFTAR BACKUP**

**Cara pakai:**
```bash
php artisan backup:restore --list
```

**Hasil:**
```
📦 Daftar Backup:

+------------------------------+---------------------+---------+----------+
| Nama File                    | Dibuat              | Ukuran  | Umur     |
+------------------------------+---------------------+---------+----------+
| backup_2025-10-08_142530.sql | 2025-10-08 14:25:30 | 2.45 MB | hari ini |
| backup_2025-10-07_020000.sql | 2025-10-07 02:00:00 | 2.40 MB | kemarin  |
| backup_2025-10-06_020000.sql | 2025-10-06 02:00:00 | 2.38 MB | 2 hari   |
+------------------------------+---------------------+---------+----------+

Total: 3 backup
```

**Artinya:**
- Ada 3 salinan data
- Bisa pilih mana saja untuk restore
- Backup terbaru paling atas

---

### **3️⃣ RESTORE DATABASE (Kembalikan Data)**

**⚠️ HATI-HATI! Gunakan hanya jika:**
- Database rusak/error
- Data penting terhapus
- Update aplikasi gagal
- Darurat/emergency

**Cara pakai:**
```bash
php artisan backup:restore
```

**Langkah-langkah:**

**Step 1: Pilih Backup**
```
📦 Pilih backup yang mau dikembalikan:

  [0] backup_2025-10-08_142530.sql (hari ini) - 2.45 MB
  [1] backup_2025-10-07_020000.sql (kemarin) - 2.40 MB
  [2] backup_2025-10-06_020000.sql (2 hari lalu) - 2.38 MB

Pilih nomor: 1  ← Ketik 1 lalu Enter (pilih backup kemarin)
```

**Step 2: Konfirmasi**
```
⚠️  PERINGATAN: Database sekarang akan diganti!
   Database sekarang akan di-backup dulu untuk jaga-jaga.

Lanjutkan? (yes/no): yes  ← Ketik yes lalu Enter
```

**Step 3: Proses Restore**
```
💾 Backup database sekarang dulu...
✅ Backup selesai!

🔄 Kembalikan data dari: backup_2025-10-07_020000.sql
✅ Restore berhasil!

💡 Silakan cek aplikasi, pastikan data sudah kembali normal.
```

**Artinya:**
- ✅ Database dikembalikan ke kondisi kemarin
- ✅ Data kemarin sudah muncul lagi
- ⚠️ Data hari ini (setelah backup kemarin) hilang

---

### **📌 Contoh Kasus Nyata**

**Kasus: Tidak Sengaja Hapus Invoice Penting**

**Kronologi:**
- Kemarin jam 2 pagi: Backup otomatis (Invoice #001 masih ada)
- Hari ini jam 10 pagi: Tidak sengaja hapus Invoice #001
- Hari ini jam 11 pagi: Baru sadar Invoice #001 hilang 😱

**Solusi:**
```bash
# 1. Jalankan restore
php artisan backup:restore

# 2. Pilih backup kemarin
Pilih nomor: 1 (backup kemarin jam 2 pagi)

# 3. Konfirmasi
Lanjutkan? yes

# 4. Hasil:
✅ Invoice #001 muncul lagi!
⚠️ Tapi data dari kemarin jam 2 pagi - hari ini jam 11 pagi hilang
   (Data 1 hari 9 jam hilang)
```

**Tips:**
- Semakin sering backup → semakin sedikit data hilang saat restore
- Backup harian jam 2 pagi → maksimal kehilangan 1 hari data

---

## ⏰ Setup Backup Otomatis (Setiap Hari)

**Tujuan:** Backup otomatis setiap hari jam 2 pagi (tanpa manual)

### **Langkah-Langkah:**

**1. Buka Task Scheduler**
```
Cara 1:
- Tekan tombol Windows + R
- Ketik: taskschd.msc
- Enter

Cara 2:
- Klik Start
- Ketik: Task Scheduler
- Klik aplikasi Task Scheduler
```

**2. Buat Task Baru**
```
- Klik "Create Basic Task" (di panel kanan)
- Name: Backup Database Manajemen Perusahaan
- Description: Backup otomatis setiap hari jam 2 pagi
- Klik Next
```

**3. Atur Jadwal (Trigger)**
```
- When do you want the task to start?
  Pilih: Daily (Harian)
  Klik Next

- Start: 02:00:00 (jam 2 pagi)
- Recur every: 1 days (setiap 1 hari)
- Klik Next
```

**4. Atur Perintah (Action)**
```
- What action do you want the task to perform?
  Pilih: Start a program (Jalankan program)
  Klik Next

- Program/script: 
  Ketik: C:\xampp\php\php.exe
  (Sesuaikan dengan lokasi PHP Anda)

- Add arguments:
  Ketik: artisan backup:database

- Start in:
  Ketik: C:\Users\DILAN\login-app - Copy - Copy
  (Folder project Anda)

- Klik Next
```

**5. Selesai**
```
- Centang: "Open the Properties dialog for this task when I click Finish"
- Klik Finish
```

**6. Setting Tambahan (Penting!)**
```
Di jendela Properties yang muncul:

Tab "General":
- Centang: "Run whether user is logged on or not"
  (Jalan meskipun tidak login)

Tab "Settings":
- Centang: "Run task as soon as possible after a scheduled start is missed"
  (Jalan jika terlewat, misal komputer mati)

Klik OK
```

**7. Test Backup Otomatis**
```
- Klik kanan task yang baru dibuat
- Klik "Run"
- Tunggu beberapa detik
- Cek folder: storage\app\backups\
- Harus ada file backup baru
```

---

### **❓ Troubleshooting**

**Task tidak jalan?**
1. Cek path PHP benar: `C:\xampp\php\php.exe`
2. Cek folder project benar: `C:\Users\DILAN\login-app - Copy - Copy`
3. Cek "Last Run Result" di Task Scheduler (harus 0x0 = sukses)

**Cara cek path PHP:**
```bash
# Buka PowerShell/CMD
where php

# Hasilnya misal: C:\xampp\php\php.exe
# Gunakan path ini di Task Scheduler
```

---

## 🆘 Jika Ada Masalah

### **1. Error: mysqldump not found**

**Artinya:** Program backup MySQL tidak ditemukan

**Solusi:**
```bash
# Cek lokasi mysqldump
where mysqldump

# Jika tidak ada, tambahkan ke PATH:
# 1. Buka System Properties
# 2. Environment Variables
# 3. Edit PATH
# 4. Tambahkan: C:\xampp\mysql\bin
# 5. OK → Restart PowerShell
```

---

### **2. Error: Access denied**

**Artinya:** Username/password database salah

**Solusi:**
```
1. Buka file .env
2. Cek:
   DB_USERNAME=root
   DB_PASSWORD=
3. Pastikan sama dengan MySQL Anda
4. Jalankan: php artisan config:clear
5. Coba backup lagi
```

---

### **3. Backup file kosong (0 bytes)**

**Artinya:** Backup gagal, file kosong

**Solusi:**
```bash
# 1. Cek koneksi database
php artisan tinker
>>> DB::connection()->getPdo();

# 2. Clear cache
php artisan config:clear

# 3. Coba backup lagi
php artisan backup:database
```

---

### **4. Database Corrupt/Rusak**

**Langkah Darurat:**
```
1. JANGAN PANIK! Data bisa dikembalikan
2. Jangan tutup aplikasi/server
3. Jalankan: php artisan backup:restore
4. Pilih backup terakhir yang bagus
5. Ketik: yes
6. Tunggu selesai
7. Test aplikasi
```

---

### **5. Semua Backup Corrupt**

**Solusi Alternatif:**
```
1. Cek backup manual di Google Drive/OneDrive
2. Atau restore via phpMyAdmin:
   - Buka phpMyAdmin
   - Pilih database manajemen_perusahaan
   - Klik Import
   - Pilih file backup .sql
   - Klik Go
3. Jika tidak ada backup sama sekali:
   - Hubungi IT support
   - Atau mulai dari awal (data hilang)
```

---

## 💡 Tips & Best Practice

### **✅ LAKUKAN:**
1. **Backup rutin** - Setiap hari otomatis (via Task Scheduler)
2. **Backup manual** - Sebelum update/hapus data penting
3. **Test restore** - 1x per bulan untuk memastikan backup berfungsi
4. **Upload ke cloud** - Simpan backup penting di Google Drive/OneDrive
5. **Cek backup** - Sesekali cek folder backup, pastikan ada file baru

### **❌ JANGAN:**
1. **Jangan hapus semua backup** - Minimal simpan 7 hari terakhir
2. **Jangan restore sembarangan** - Hanya saat darurat
3. **Jangan lupa test** - Setelah restore, test aplikasi dulu
4. **Jangan simpan di 1 tempat** - Backup juga ke cloud/external HDD
5. **Jangan abaikan error** - Jika backup error, segera perbaiki

---

## 📋 Checklist Keamanan Data

**Harian:**
- [ ] Backup otomatis jalan (cek Task Scheduler)
- [ ] Ada file backup baru di folder `storage/app/backups/`

**Mingguan:**
- [ ] Upload backup penting ke Google Drive/OneDrive
- [ ] Cek ukuran backup (tidak terlalu besar/kecil)

**Bulanan:**
- [ ] Test restore (pastikan backup bisa dikembalikan)
- [ ] Hapus backup sangat lama (>60 hari) jika perlu space

**Sebelum Update Besar:**
- [ ] Backup manual: `php artisan backup:database`
- [ ] Download file backup ke komputer lokal
- [ ] Baru update aplikasi

---

## 📞 Bantuan & Support

**Jika masih ada masalah:**

1. **Cek Log Error:**
   ```
   Lokasi: storage/logs/laravel.log
   Cari error terakhir
   ```

2. **Clear Cache:**
   ```bash
   php artisan config:clear
   php artisan cache:clear
   ```

3. **Hubungi Developer:**
   - Kirim screenshot error
   - Kirim file log (jika ada)
   - Jelaskan langkah yang sudah dicoba

---

## 📝 Ringkasan Perintah

```bash
# Backup database
php artisan backup:database

# Lihat daftar backup
php artisan backup:restore --list

# Restore database
php artisan backup:restore

# Backup dengan simpan 60 hari
php artisan backup:database --keep-days=60

# Cek path PHP (untuk Task Scheduler)
where php
```

---

**📅 Dibuat:** 8 Oktober 2025  
**📌 Versi:** 1.0  
**👤 Untuk:** Manajemen Perusahaan  
**🔒 Status:** Private & Aman
