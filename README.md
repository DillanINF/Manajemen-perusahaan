# Manajemen Perusahaan

Aplikasi manajemen operasional end-to-end: PO  Surat Jalan  Invoice  Jatuh Tempo  Pembayaran.

- Teknologi: Laravel 11, PHP 8.2+, Blade, TailwindCSS, Alpine.js, MySQL, Vite
- Status: Production-ready

## Fitur Utama
- Master Data: Customer, Produk, Pengirim, Kendaraan
- Export: Excel Surat Jalan, Tanda Terima, Invoice PDF
- Salary: Template Excel ke HTML interaktif (auto-calc, responsive)

## Quick Start
```bash
# Clone & masuk folder
git clone https://github.com/DillanINF/Manajemen-perusahaan.git
cd Manajemen-perusahaan

# Install dependencies
composer install
npm install

# Salin env & generate app key
cp .env.example .env
php artisan key:generate

# Siapkan database
php artisan migrate
# php artisan db:seed   # opsional

# Jalankan (dua terminal terpisah)
npm run dev
php artisan serve
```
## Modul & Rute (ringkas)
- Dashboard: /dashboard
- PO: /po
- Surat Jalan: /suratjalan
- Invoice: /invoice
- Jatuh Tempo: /jatuh-tempo
- Master Data: /customer, /produk, /pengirim, /kendaraan
- Export: via halaman Surat Jalan (Excel/Tanda Terima)

## Catatan Teknis
- Build frontend: Vite (npm run dev/build)
- Export file pruning: folder exports otomatis menyimpan 20 file terbaru
- Keamanan: CSRF, session, standar Laravel

## Prasyarat
- PHP 8.2+
- Composer 2+
- Node.js 18+ dan npm 9+
- MySQL 8+ (atau sesuaikan `.env`)

## Perintah Penting
```bash
# Development
npm run dev            # Vite dev server
php artisan serve      # Laravel server

# Production build
npm run build

# Migrasi / rollback
php artisan migrate
php artisan migrate:rollback

# Cache optimisasi
php artisan optimize
php artisan config:cache && php artisan route:cache && php artisan view:cache
```

## Modul Salary (Template Excel)
- File template `GAJI.xlsx` ditempatkan di salah satu path berikut:
  - `storage/app/template/GAJI.xlsx`
  - `storage/template/GAJI.xlsx`
- Sistem merender Excel menjadi HTML di halaman `salary_index` dengan penyesuaian:
  - Auto-fit tinggi konten dalam iframe.
  - Cropping area kosong bawah/kanan agar tidak menyisakan ruang.
  - Penyembunyian elemen gambar/branding bila diperlukan.

## Struktur Utama (ringkas)
- `app/Http/Controllers/` — logika bisnis modul (PO, Surat Jalan, Invoice, Salary, dll)
- `resources/views/` — Blade templates (dashboard, modul-modul, layout)
- `routes/web.php` — definisi rute aplikasi
- `public/` — aset publik

## Deployment Singkat
1) Set `APP_ENV=production`, `APP_DEBUG=false` di `.env`.
2) Jalankan build front-end: `npm run build`.
3) Jalankan optimisasi: `php artisan optimize`.
4) Pastikan permission storage/logs dan storage/framework writeable.

## Lisensi
MIT
