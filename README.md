# Manajemen Perusahaan

Aplikasi manajemen operasional end-to-end: PO  Surat Jalan  Invoice  Jatuh Tempo  Pembayaran.

- Teknologi: Laravel 11, PHP 8.2+, Blade, TailwindCSS, Alpine.js, MySQL, Vite
- Status: Production-ready

## Fitur Utama
- Purchase Order, Surat Jalan, Invoice, Jatuh Tempo
- Master Data: Customer, Produk, Pengirim, Kendaraan
- Export: Excel Surat Jalan, Tanda Terima, Invoice PDF
- Salary: Template Excel ke HTML interaktif (auto-calc, responsive)

## Quick Start
`ash
# Clone & masuk folder
git clone https://github.com/DillanINF/Manajemen-perusahaan.git
cd Manajemen-perusahaan

# Install
composer install
npm install

# Env & key
cp .env.example .env
php artisan key:generate

# Database
php artisan migrate
# php artisan db:seed  (opsional)

# Jalankan
npm run dev
php artisan serve
`

## .env Contoh (ringkas)
`ini
APP_NAME="Manajemen Perusahaan"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=manajemen_perusahaan
DB_USERNAME=root
DB_PASSWORD=
`

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

## Lisensi
MIT
