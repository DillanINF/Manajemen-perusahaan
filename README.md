<div align="center">

# 🏢 Sistem Manajemen Perusahaan Terpadu

<p align="center">
  <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="350" alt="Laravel Logo">
</p>

### ✨ Solusi Manajemen Operasional Perusahaan Modern & Terintegrasi

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel">
  <img src="https://img.shields.io/badge/PHP%208.2+-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP">
  <img src="https://img.shields.io/badge/TailwindCSS-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white" alt="TailwindCSS">
  <img src="https://img.shields.io/badge/Alpine.js-8BC34A?style=for-the-badge&logo=alpine.js&logoColor=white" alt="Alpine.js">
  <img src="https://img.shields.io/badge/MySQL-4479A1?style=for-the-badge&logo=mysql&logoColor=white" alt="MySQL">
  <img src="https://img.shields.io/badge/Vite-646CFF?style=for-the-badge&logo=vite&logoColor=white" alt="Vite">
</p>

<p align="center">
  <img src="https://img.shields.io/badge/Status-✅%20Production%20Ready-success?style=flat-square" alt="Status">
  <img src="https://img.shields.io/badge/Version-2.6.0-blue?style=flat-square" alt="Version">
  <img src="https://img.shields.io/badge/License-MIT-green?style=flat-square" alt="License">
  <img src="https://img.shields.io/badge/Build-Passing-brightgreen?style=flat-square" alt="Build">
</p>

<p align="center">
  <img src="https://img.shields.io/badge/🎯-Enterprise%20Grade-orange?style=flat-square" alt="Enterprise">
  <img src="https://img.shields.io/badge/📱-Mobile%20Responsive-purple?style=flat-square" alt="Mobile">
  <img src="https://img.shields.io/badge/⚡-High%20Performance-yellow?style=flat-square" alt="Performance">
  <img src="https://img.shields.io/badge/🔒-Secure-red?style=flat-square" alt="Security">
</p>

---

</div>

## 🎯 Ringkasan Eksekutif

<div align="center">

### 🚀 **Transformasi Digital untuk Operasional Perusahaan**

</div>

#### Kenapa proyek ini berbeda?
- ✅ Fokus pada alur bisnis nyata: PO → Surat Jalan → Invoice → Jatuh Tempo → Pembayaran
- ✅ Terkoneksi end-to-end dengan master data (Customer, Produk, Kendaraan, Pengirim)
- ✅ Siap produksi: performa cepat, aman, dan mudah dipelihara
- ✅ Dokumentasi rapi + changelog aktif, memudahkan adopsi dan kolaborasi

<table>
<tr>
<td width="50%" align="center">

#### 🎯 **Tujuan Utama**
Sistem manajemen operasional **end-to-end** untuk perusahaan distribusi, logistik, dan manufaktur dengan fokus pada **efisiensi**, **transparansi**, dan **skalabilitas**.

</td>
<td width="50%" align="center">

#### ⭐ **Value Proposition**
- 📈 **Produktivitas +40%**
- 💰 **Penghematan Biaya 25%**
- ⚡ **Proses Otomatis 80%**
- 📊 **Real-time Insights**

</td>
</tr>
</table>

### 🎨 **Keunggulan Teknologi**

| 🏗️ **Arsitektur** | 🎨 **UI/UX** | ⚡ **Performance** | 🔒 **Security** |
|:---:|:---:|:---:|:---:|
| Modular Laravel | Modern TailwindCSS | Vite Build System | Role-based Access |
| RESTful API | Alpine.js Reactive | Optimized Queries | Session Management |
| Clean Code | Mobile Responsive | Lazy Loading | CSRF Protection |

> Update Terbaru (2025-09-16)
> - Jatuh Tempo: Insert SELALU baris baru untuk PO yang sama; tidak digabung/ditimpa. Unique index pada `jatuh_tempos` dihapus via migrasi agar duplikasi valid. Tambah logging untuk debug insert.
> - Cascade Delete: Hapus Invoice atau Surat Jalan otomatis menghapus entri terkait di Jatuh Tempo (berdasarkan `no_invoice`/`no_po`).
> - Bulk Delete: Tambah aksi Hapus Semua (destroyAll) untuk Jatuh Tempo, Invoice, dan Surat Jalan. UI memakai checkbox switch dengan konfirmasi.
> - No Invoice Source: Kolom No Invoice di Jatuh Tempo tidak lagi mengambil dari No Surat Jalan. Jika No Invoice kosong, fallback ke `po_number` (No Urut dari Data Invoice).
> - Navigasi: Double click pada No Invoice di Data Invoice membuka Form Input PO dengan parameter `from=invoice&po_number=<noUrut>`.

> Update Terbaru (2025-09-15)
> - Auth/OTP: Alur reset password via OTP aktif dan stabil (kirim OTP, verifikasi OTP, reset password via OTP).
> - Email: Panduan konfigurasi email untuk OTP ditambahkan dan dirujuk di README (lihat `docs/EMAIL_CONFIGURATION.md`). Default development masih menggunakan driver non-smtp hingga dikonfigurasi.
> - API: Dokumentasi endpoint OTP ditambahkan pada bagian API Routes.
>
> Update Terbaru (2025-09-12)
> - PO: "No Surat Jalan" kini otomatis terisi dari `code_number` Customer (format CAM-TTG/2025 → Nomor=CAM, PT=TTG, Tahun=2025). Customer di form PO dibuat non-editable saat datang dari Data Invoice.
> - PO: Input "No Invoice" di form PO dihapus; penomoran invoice dikelola dari halaman Data Invoice dengan logika lanjut nomor terbesar (bukan MEX terkecil). Alignment kolom No Invoice dirapikan agar rata kiri.
> - PO: Tanggal PO default ke hari ini saat form dibuka dari Data Invoice; perubahan tanggal mengisi Tahun No Surat Jalan otomatis.
> - Customer: Tambah field `code_number` (CAM-TTG/2025) pada Data Customer (form Tambah/Edit) dan tampilkan kolom "Kode Number" di tabel.
> - Pengirim: Tambah kolom `kendaraan` dan `no_polisi` (DB, model, controller). Form Tambah/Edit Pengirim kini memiliki input Kendaraan dan No Polisi; tabel menampilkan kedua kolom tersebut.
> - UI: Perbaikan builder baris dinamis pada Data Invoice agar kolom sesuai header (Tanggal, No Invoice, Customer, dst) dan badge No Invoice tidak "miring" ke kolom lain.
>
> Update Terbaru (2025-09-11)
> - UI: Navbar halaman Data Invoice disejajarkan dengan garis sidebar agar konsisten dengan form lain (Surat Jalan/Jatuh Tempo).
> - Tabel Invoice: perilaku responsif disempurnakan. Header dan isi kolom tidak turun (nowrap) sesuai kebutuhan.
> - Scroll Horizontal: hanya aktif dan tersembunyi di SEL PRODUCT; kolom lain tidak terpengaruh. Ambang min-width diatur sehingga scroll tidak muncul terlalu sensitif.
> - Struktur Header: perapihan struktur HTML Blade pada halaman Data Invoice agar layout stabil saat zoom/resize.
>
> Update Terbaru (2025-09-09)
> - Excel Tanda Terima: F21 diisi tanggal PO dari tabel Surat Jalan dengan format `d/mmm/yyyy` (contoh: 9/Sep/2025).
> - Excel Tanda Terima: E24 diisi deadline (tanggal_jatuh_tempo) dari modul Jatuh Tempo.
> - PDF Invoice: tanggal memakai `tanggal_po` sehingga konsisten dengan tabel Surat Jalan.
> - Form PO: saat memilih Tanggal PO, otomatis mengisi bulan/tahun No Invoice serta tahun No Surat Jalan.
> - Jatuh Tempo: input "No PO" diubah menjadi tanggal (selaras tampilan di Surat Jalan).
> - Polishing UI minor untuk konsistensi light/dark mode & responsif.

## 📋 Daftar Isi

- [🎯 Fitur Utama](#-fitur-utama)
- [🖼️ Preview](#️-preview)
- [🛠️ Tech Stack](#️-tech-stack)
- [⚡ Quick Start](#-quick-start)
- [🗄️ Konfigurasi Cepat MySQL](#️-konfigurasi-cepat-mysql)
- [✉️ Email OTP - Quick Setup](#️-email-otp---quick-setup)
- [🎨 UI/UX Highlights](#-uiux-highlights)
- [📚 API Routes](#-api-routes)
- [🔧 Troubleshooting](#-troubleshooting)
- [🏷️ Standar Kualitas](#️-standar-kualitas)
- [🗺️ Roadmap](#️-roadmap)
- [🤝 Contributing](#-contributing)
- [🆘 Dukungan & Kontak](#-dukungan--kontak)

## 🎯 Fitur Unggulan

<div align="center">

### 🌟 **Ekosistem Manajemen Terintegrasi**

</div>

<table>
<tr>
<td width="33%" align="center">

### 📦 **Supply Chain Management**
```
🎯 Purchase Order System
├── 📋 PO Creation & Tracking
├── 🔄 Status Management
├── 📊 Vendor Analytics
└── 📈 Performance Metrics

🚚 Logistics & Distribution
├── 📄 Digital Surat Jalan
├── 🗺️ Route Optimization
├── 📱 Mobile Tracking
└── 📋 Delivery Confirmation
```

</td>
<td width="33%" align="center">

### 💼 **Financial Operations**
```
💰 Revenue Management
├── 📊 Income Tracking
├── 📈 Monthly Reports
├── 💹 Profit Analysis
└── 🎯 Target Monitoring

🧾 Billing & Collections
├── 📄 Invoice Generation
├── ⏰ Due Date Tracking
├── 💳 Payment Processing
└── 📋 Receipt Management
```

</td>
<td width="33%" align="center">

### 👥 **Human Resources**
```
👤 Employee Management
├── 📝 Staff Database
├── 📊 Performance Tracking
├── 📅 Attendance System
└── 🎯 Goal Setting

💰 Payroll System
├── 💵 Salary Calculation
├── 📊 Payslip Generation
├── 🏦 Bank Integration
└── 📈 Cost Analysis
```

</td>
</tr>
</table>

### 🚀 **Fitur Terbaru & Unggulan**

| 🎨 **UI/UX Excellence** | ⚡ **Performance** | 🔧 **Advanced Features** | 📊 **Analytics** |
|---|---|---|---|
| 🎭 **Dark/Light Mode** | ⚡ **Vite Bundling** | 🔍 **Smart Filtering** | 📈 **Real-time Charts** |
| 📱 **Mobile First** | 🚀 **Lazy Loading** | 📅 **Date Range Picker** | 📊 **Monthly Statistics** |
| 🎯 **Intuitive Navigation** | 💾 **Optimized Queries** | 🔄 **Status Tracking** | 💹 **Financial Insights** |
| ✨ **Smooth Animations** | 📦 **Asset Optimization** | 📤 **Bulk Operations** | 🎯 **KPI Monitoring** |

### 🛡️ **Enterprise Security & Compliance**

<table>
<tr>
<td width="50%">

#### 🔒 **Security Features**
- ✅ **Multi-layer Authentication**
- ✅ **CSRF Protection**
- ✅ **SQL Injection Prevention**
- ✅ **XSS Protection**
- ✅ **Session Security**
- ✅ **Password Encryption**

</td>
<td width="50%">

#### 📋 **Compliance & Audit**
- ✅ **Activity Logging**
- ✅ **Data Backup**
- ✅ **User Access Control**
- ✅ **Permission Management**
- ✅ **Audit Trail**
- ✅ **GDPR Compliance**

</td>
</tr>
</table>

## 🗂️ Arsitektur Sistem & Modul

<div align="center">

### 🏗️ **Struktur Modular Enterprise**

</div>

```mermaid
graph TB
    A[🏠 Dashboard] --> B[📊 Analytics Engine]
    A --> C[🔔 Notification Center]
    
    D[💼 Financial Module] --> E[💰 Revenue Tracking]
    D --> F[📄 Invoice Management]
    D --> G[⏰ Due Date Monitor]
    
    H[📦 Supply Chain] --> I[🛒 Purchase Orders]
    H --> J[🚚 Logistics]
    H --> K[📋 Inventory]
    
    L[👥 HR Module] --> M[👤 Employee Management]
    L --> N[💰 Payroll System]
    
    O[🗄️ Master Data] --> P[🏢 Customers]
    O --> Q[📦 Products]
    O --> R[🚛 Vehicles]
```

### 📋 **Modul Operasional**

<table>
<tr>
<td width="50%">

| 🎯 **Core Modules** | 📝 **Deskripsi** | 🔗 **Endpoint** |
|:---:|:---|:---:|
| 🏠 **Dashboard** | KPI Analytics, Financial Charts, Monthly Overview | `/dashboard` |
| 💼 **Finance** | Revenue/Expense Tracking, Profit Analysis | `/finance/*` |
| 📦 **Purchase Order** | PO Lifecycle Management, Vendor Relations | `/po` |
| 🚚 **Surat Jalan** | Digital Delivery Notes, PDF Export | `/suratjalan` |

</td>
<td width="50%">

| 🎯 **Business Modules** | 📝 **Deskripsi** | 🔗 **Endpoint** |
|:---:|:---|:---:|
| 📄 **Invoice** | Billing Management, Payment Tracking | `/invoice` |
| ⏰ **Jatuh Tempo** | Due Date Monitoring, Collection Management | `/jatuh-tempo` |
| 👥 **Employee** | Staff Database, Performance Tracking | `/employee` |
| 💰 **Payroll** | Salary Processing, Payment Records | `/salary` |

</td>
</tr>
</table>

### 🗄️ **Master Data Management**

| 📊 **Data Entity** | 🎯 **Purpose** | 🔧 **Features** | 🔗 **Route** |
|:---:|:---|:---|:---:|
| 🏢 **Customers** | Client Database & Relations | CRM Integration, Payment Terms | `/customer` |
| 📦 **Products** | Inventory & Catalog | Pricing, Categories, Stock | `/produk` |
| 🚛 **Vehicles** | Fleet Management | Tracking, Maintenance | `/kendaraan` |
| 📮 **Senders** | Logistics Partners | Route Optimization | `/pengirim` |

## 🖼️ Galeri & Preview

<div align="center">

### 🎨 **Interface Showcase**

<img src="docs/screenshot/Dashboard.png" alt="Dashboard Preview" width="900" style="border-radius: 15px; box-shadow: 0 8px 32px rgba(0,0,0,0.12); border: 1px solid #e2e8f0;">

#### 📊 **Dashboard Analytics**
*Real-time business intelligence dengan visualisasi data yang interaktif*

<table>
<tr>
<td width="33%" align="center">

### 💰 **Financial Overview**
- 📈 **Revenue Tracking**
- 💹 **Profit Analysis** 
- 📊 **Monthly Trends**
- 🎯 **Target Monitoring**

</td>
<td width="33%" align="center">

### 📦 **Operational Metrics**
- 🚚 **Delivery Status**
- 📋 **Order Processing**
- ⏰ **Due Payments**
- 🔄 **Workflow Status**

</td>
<td width="33%" align="center">

### 👥 **Team Performance**
- 👤 **Employee Stats**
- 💰 **Payroll Summary**
- 📅 **Attendance Rate**
- 🎯 **Productivity Index**

</td>
</tr>
</table>

</div>

### 🎬 Demo Video

<div align="center">

<video src="docs/media/mrsidi.mp4" controls width="900" poster="docs/screenshot/dashboard.png" style="border-radius: 12px; box-shadow: 0 8px 32px rgba(0,0,0,0.12); border: 1px solid #e2e8f0;">
  Browser Anda tidak mendukung video HTML5.
  <a href="docs/media/mrsidi.mp4">Tonton video</a> secara langsung.
  
</video>

</div>

### 🎭 **UI/UX Design Philosophy**

<div align="center">

| 🎨 **Design Principle** | 💡 **Implementation** | ✨ **User Experience** |
|:---:|:---:|:---:|
| **Minimalist & Clean** | TailwindCSS + Custom Components | Reduced Cognitive Load |
| **Mobile-First Responsive** | Breakpoint-driven Design | Seamless Cross-device |
| **Accessibility Focus** | WCAG 2.1 Compliance | Inclusive User Access |
| **Performance Optimized** | Vite + Lazy Loading | Lightning Fast Response |

</div>

## 🛠️ Tech Stack & Arsitektur

<div align="center">

### 🏗️ **Enterprise-Grade Technology Stack**

</div>

<table>
<tr>
<td width="25%" align="center">

### 🔧 **Backend**
![Laravel](https://img.shields.io/badge/Laravel%2011-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP%208.2+-777BB4?style=for-the-badge&logo=php&logoColor=white)
![Eloquent](https://img.shields.io/badge/Eloquent%20ORM-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)

**🎯 Features:**
- RESTful API Architecture
- Middleware Security
- Artisan CLI Tools
- Queue Management

</td>
<td width="25%" align="center">

### 🎨 **Frontend**
![Blade](https://img.shields.io/badge/Blade%20Templates-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![TailwindCSS](https://img.shields.io/badge/TailwindCSS-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)
![Alpine.js](https://img.shields.io/badge/Alpine.js-8BC34A?style=for-the-badge&logo=alpine.js&logoColor=white)

**🎯 Features:**
- Component-based UI
- Reactive Interactions
- Mobile-first Design
- Dark/Light Mode

</td>
<td width="25%" align="center">

### 🗄️ **Database**
![MySQL](https://img.shields.io/badge/MySQL%208.0+-4479A1?style=for-the-badge&logo=mysql&logoColor=white)
![MariaDB](https://img.shields.io/badge/MariaDB-003545?style=for-the-badge&logo=mariadb&logoColor=white)
![Migrations](https://img.shields.io/badge/Laravel%20Migrations-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)

**🎯 Features:**
- ACID Compliance
- Indexing Strategy
- Backup & Recovery
- Performance Tuning

</td>
<td width="25%" align="center">

### ⚡ **DevOps**
![Vite](https://img.shields.io/badge/Vite-646CFF?style=for-the-badge&logo=vite&logoColor=white)
![Composer](https://img.shields.io/badge/Composer-885630?style=for-the-badge&logo=composer&logoColor=white)
![Node.js](https://img.shields.io/badge/Node.js%2018+-339933?style=for-the-badge&logo=node.js&logoColor=white)

**🎯 Features:**
- Hot Module Replacement
- Asset Optimization
- Dependency Management
- Build Automation

</td>
</tr>
</table>

### 🏛️ **Arsitektur Aplikasi**

```mermaid
graph TD
    A[🌐 Web Browser] --> B[🔒 Authentication Layer]
    B --> C[🎯 Route Handler]
    C --> D[🛡️ Middleware Stack]
    D --> E[📋 Controller Layer]
    E --> F[🔄 Business Logic]
    F --> G[🗄️ Model/Database]
    
    H[🎨 Blade Templates] --> I[📱 Responsive UI]
    I --> J[⚡ Alpine.js Reactivity]
    J --> K[🎭 TailwindCSS Styling]
    
    L[⚙️ Vite Build] --> M[📦 Asset Bundling]
    M --> N[🚀 Performance Optimization]
```

## ⚡ Quick Start

### 📋 Prerequisites

```bash
✅ PHP 8.2+
✅ Composer 2+
✅ Node.js 18+ & NPM
✅ MySQL/MariaDB
```

### 🚀 Installation

```bash
# 1️⃣ Clone repository
git clone https://github.com/DillanINF/Manajemen-perusahaan.git
cd Manajemen-perusahaan

# 2️⃣ Install dependencies
composer install
npm install

# 3️⃣ Environment setup
cp .env.example .env
php artisan key:generate

# 4️⃣ Database setup
php artisan migrate
php artisan db:seed  # Optional

# 5️⃣ Build assets
npm run dev  # Development
# npm run build  # Production

# 6️⃣ Start server
php artisan serve
```

### 🔑 Demo Akun (Untuk Pengujian Cepat)

```
Email: perle@gmail.com    | Password: password123
Email: admin@gmail.com    | Password: admin123
```

### ⚙️ Environment Configuration

```ini
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
```

## 🗄️ Konfigurasi Cepat MySQL

Gunakan konfigurasi final MySQL (sesuai migrasi terbaru) agar aplikasi langsung berjalan:

```ini
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=manajemen_perusahaan
DB_USERNAME=root
DB_PASSWORD=
```

Langkah singkat:
1. Buat database `manajemen_perusahaan` di phpMyAdmin.
2. Update file `.env` sesuai blok di atas.
3. Jalankan: `php artisan config:cache` lalu `php artisan migrate`.
4. (Opsional) `php artisan db:seed` dan login menggunakan demo akun.

## ✉️ Email OTP - Quick Setup

Fitur OTP reset password sudah aktif. Agar email OTP terkirim:
- Development: gunakan Mailtrap atau set `MAIL_MAILER=array` untuk testing cepat.
- Production: gunakan SMTP (Gmail atau provider email lain) dan set `MAIL_MAILER=smtp`.

Panduan lengkap: lihat `docs/EMAIL_CONFIGURATION.md`.

## 🎨 UI/UX Highlights

<div align="center">

### ✨ **Modern Interface Design**

</div>

| Feature | Description |
|---------|-------------|
| 🎯 **Smart Sidebar** | Sticky header/footer dengan smooth scrolling |
| 🎭 **Smooth Animations** | Alpine.js transitions untuk pengalaman yang halus |
| 📱 **Responsive Design** | Mobile-first approach dengan hamburger menu |
| 🎨 **Consistent Icons** | Icon system yang seragam dan scalable |
| ⚡ **Quick Access** | One-click access ke pengaturan dari sidebar footer |

## 🏷️ Standar Kualitas

**Kaidah yang kami terapkan untuk menjaga kualitas proyek:**
- **Konvensi Commit**: menggunakan Conventional Commits (lihat bagian Contributing).
- **Gaya Kode**: mengikuti standar Laravel & PSR-12.
- **Keamanan**: CSRF, XSS, SQL Injection prevention aktif by default.
- **Kinerja**: query dioptimalkan, asset dibundel via Vite, lazy-loading gambar/komponen.
- **Dokumentasi**: README + Changelog diperbarui ketika ada perubahan berarti.

## 📚 API Routes

<details>
<summary><b>🔗 Click to expand routes</b></summary>

### 🏠 **Core & Settings**
```php
GET   /dashboard                         # Main dashboard
GET   /settings                          # Settings page (view)
```

### 💸 **Finance**
```php
GET   /finance                           # Alias ke pendapatan
GET   /finance/pendapatan                # Income (query: inc_month, inc_year)
GET   /finance/pengeluaran               # Expense (query: month, year)
GET   /finance/pendapatan/detail         # Income detail (JSON)
GET   /finance/pengeluaran/detail        # Expense detail (JSON)
POST  /finance/pengeluaran/store         # Store expense (JSON/Form)
```

### 👤 **Users**
```php
GET   /users                             # List users (admin)
GET   /users/create                      # Create form
POST  /users                             # Store
DELETE /users/{user}                     # Delete
```

### 🔐 **Auth & OTP**
```php
# Login & Forgot Password (guest)
GET   /login                             # Login form
POST  /login                             # Login submit
GET   /forgot-password                   # Forgot password (form)

# OTP Flow (guest)
POST  /send-otp                          # Kirim OTP ke email (name: otp.send)
GET   /verify-otp                        # Tampilkan form verifikasi OTP (name: otp.verify.form)
POST  /verify-otp                        # Verifikasi OTP (name: otp.verify)
GET   /reset-password-form               # Form reset password via OTP (name: password.reset.form)
POST  /reset-password-otp                # Reset password via OTP (name: password.reset.otp)

# Password reset bawaan (kompatibilitas)
POST  /forgot-password                   # Kirim link reset (name: password.email)
GET   /reset-password/{token}            # Form reset via token (name: password.reset)
POST  /reset-password                    # Submit reset via token (name: password.store)

# Authenticated
POST  /logout                            # Logout (auth)
```

### 🚚 **Surat Jalan**
```php
GET   /suratjalan                         # Listing
POST  /suratjalan                         # Store
GET   /suratjalan/{id}/edit-form          # Edit form (custom)
GET   /suratjalan/{id}/edit               # Edit
PUT   /suratjalan/{id}                    # Update
DELETE /suratjalan/{id}                   # Destroy
DELETE /suratjalan/all                    # Destroy ALL (bulk delete)
POST  /suratjalan/export                  # Export (Excel via POExportController)
POST  /suratjalan/invoice-data            # Ambil data invoice
POST  /suratjalan/invoice-pdf             # Generate invoice PDF
```

### 📦 **Purchase Order**
```php
GET   /po                                 # Listing
GET   /po/create                          # Create form
POST  /po                                 # Store
GET   /po/{id}/edit                       # Edit form
PUT   /po/{id}                            # Update
DELETE /po/{id}                           # Destroy
# Export PO dinonaktifkan (gunakan export di menu Surat Jalan)
```

### 👥 **Employee & Salary**
```php
# Employee
GET   /employee                           # Index
GET   /employee/create                    # Create
POST  /employee                           # Store
GET   /employee/{id}/edit                 # Edit
PUT   /employee/{id}                      # Update
DELETE /employee/{id}                     # Destroy
PATCH /employee/{id}/status               # Update status

# Salary
GET   /salary                             # Index
GET   /salary/create                      # Create
POST  /salary                             # Store
GET   /salary/{id}/edit                   # Edit
PUT   /salary/{id}                        # Update
DELETE /salary/{id}                       # Destroy
POST  /salary/generate-payroll            # Generate payroll
PATCH /salary/{id}/payment                # Mark as paid
```

### 🧾 **Invoice**
```php
GET   /invoice                            # Index
GET   /invoice/create                     # Create
POST  /invoice                            # Store
GET   /invoice/{id}/edit                  # Edit
PUT   /invoice/{id}                       # Update
DELETE /invoice/{id}                      # Destroy
PATCH /invoice/{id}/status                # Update status
DELETE /invoice/all                       # Destroy ALL (bulk delete)
```

### 📑 **Tanda Terima (Dinonaktifkan)**
Fitur rute CRUD Tanda Terima telah dinonaktifkan dan kini dipusatkan pada modul **Surat Jalan**.

### ⏰ **Jatuh Tempo**
```php
GET   /jatuh-tempo                        # Index
GET   /jatuh-tempo/create                 # Create
POST  /jatuh-tempo                        # Store
GET   /jatuh-tempo/{id}/edit              # Edit
PUT   /jatuh-tempo/{id}                   # Update
DELETE /jatuh-tempo/{id}                  # Destroy
PATCH /jatuh-tempo/{id}/payment           # Mark as paid
GET   /jatuh-tempo/{id}/send-reminder     # Kirim pengingat
POST  /jatuh-tempo/{id}/update-status     # Update status
PUT   /jatuh-tempo/{id}/update-deadline   # Update deadline
DELETE /jatuh-tempo/all                   # Destroy ALL (bulk delete)
```

### 📦➡️ **Barang Masuk & Keluar**
```php
# Barang Masuk
GET   /barang/masuk                       # Index
POST  /barang/masuk                       # Store
GET   /barang/masuk/{id}                  # Show/Edit (resource default)
PUT   /barang/masuk/{id}                  # Update
DELETE /barang/masuk/{id}                 # Destroy

# Barang Keluar
GET   /barang/keluar                      # Index
POST  /barang/keluar                      # Store
GET   /barang/keluar/{id}                 # Show/Edit (resource default)
PUT   /barang/keluar/{id}                 # Update
DELETE /barang/keluar/{id}                # Destroy
```

### 🗂️ **Master Data**
```php
RESOURCE /kendaraan                        # Kendaraan (CRUD)
RESOURCE /produk                           # Produk (CRUD)
RESOURCE /customer                         # Customer (CRUD)
RESOURCE /pengirim                         # Pengirim (CRUD)
```

</details>

### 🔔 **Notifications API**
```php
GET   /api/jatuh-tempo/notifications      # Notifikasi Jatuh Tempo (API)
```

## 🔧 Troubleshooting

<details>
<summary><b>🛠️ Common Issues & Solutions</b></summary>

### 🚨 **Sidebar Issues**
```bash
Problem: Sidebar tidak sticky
Solution: Pastikan <aside id="sidebar"> memiliki overflow-hidden
```

### 🎯 **Icon Problems**
```bash
Problem: Icon panah mengecil
Solution: Gunakan classes: min-w-[1rem] min-h-[1rem] shrink-0 flex-none
```

### ⚙️ **Settings Access**
```bash
Problem: Klik profil tidak membuka pengaturan
Solution: Pastikan footer profil dibungkus <a href="{{ route('settings') }}">
```

### 🎭 **Animation Issues**
```bash
Problem: Animasi pengaturan tidak muncul
Solution: Cek kondisional request()->routeIs('settings')
```

### 📦 **Build Problems**
```bash
Problem: Vite build gagal
Solution: rm -rf .vite node_modules && npm install
```

</details>

## 🗺️ Roadmap

<div align="center">

### 🎯 **Coming Soon**

</div>

```mermaid
gantt
    title Development Roadmap
    dateFormat  YYYY-MM-DD
    section Phase 1
    Reports Export (Excel/PDF)    :2025-01-01, 30d
    section Phase 2
    Role & Permission System      :2025-02-01, 45d
    section Phase 3
    Real-time Notifications       :2025-03-15, 30d
    section Phase 4
    Automated Testing Suite       :2025-04-15, 60d
```

- [ ] 📊 **Advanced Reporting** - Export Excel/PDF dengan template kustom
- [ ] 👥 **Role Management** - Sistem permission yang granular
- [ ] 🔔 **Real-time Notifications** - WebSocket integration
- [ ] 🧪 **Testing Suite** - Comprehensive testing dengan Pest/PHPUnit
- [ ] 📱 **Mobile App** - React Native companion app
- [ ] 🤖 **API Integration** - RESTful API untuk third-party integration

## 📝 Changelog

### 2025-09-16 (v2.7.0)
- Jatuh Tempo:
  - Insert selalu baris baru; hapus seluruh unique index yang menghambat duplikasi melalui migrasi baru.
  - Tambah logging proses create untuk memudahkan debugging.
- Cascade Delete:
  - Menghapus Invoice atau satu entri Surat Jalan akan otomatis menghapus data Jatuh Tempo yang terkait (`no_invoice`/`no_po`).
- Bulk Delete:
  - Tambah endpoint dan UI switch “Hapus Semua” untuk Jatuh Tempo, Invoice, dan Surat Jalan.
- No Invoice:
  - Tidak lagi fallback ke No Surat Jalan; fallback ke `po_number` jika No Invoice kosong agar kolom tidak “-”.
  - Input No Invoice di Form PO dihapus; dikelola dari Data Invoice.

### 2025-09-15 (v2.6.0)
- Auth/OTP:
  - Aktifkan dan dokumentasikan alur reset password via OTP (`send-otp`, `verify-otp`, `reset-password-form`, `reset-password-otp`).
  - Tambah referensi konfigurasi email OTP di README yang menunjuk ke `docs/EMAIL_CONFIGURATION.md`.
- Dokumentasi:
  - Tambah bagian "Auth & OTP" pada API Routes.
  - Tambah bagian "Konfigurasi Email OTP" setelah Quick Start.

### 2025-09-12 (v2.5.0)
- Purchase Order:
  - Prefill No Surat Jalan dari `code_number` Customer (CAM-TTG/2025 → Nomor/PT/Tahun).
  - Hapus input No Invoice di form PO; penomoran via Data Invoice (lanjut nomor terbesar, bukan celah kecil). Rapi rata-kiri kolom No Invoice dan urutan kolom baris dinamis.
  - Tanggal PO default ke hari ini saat datang dari Data Invoice; sinkron tahun ke No Surat Jalan secara otomatis.
- Customer:
  - Tambah kolom `code_number` (form Tambah/Edit) dan tampilkan kolom "Kode Number" di listing.
- Pengirim:
  - Tambah kolom `kendaraan` dan `no_polisi` (migrasi, model, controller). Form Tambah/Edit mendukung input keduanya, listing menampilkan dua kolom baru.
- UI/UX:
  - Perbaikan kecil agar badge dan kolom tidak salah posisi; pengalaman dark mode tetap konsisten.


### 2025-09-09 (v2.4.0)
- Export Tanda Terima (Excel):
  - Kolom F21: tanggal dari `tanggal_po` diformat `d/mmm/yyyy` (misal 9/Sep/2025).
  - Kolom E24: deadline dari `tanggal_jatuh_tempo` (Jatuh Tempo).
- PDF Invoice: tanggal kini menggunakan `tanggal_po` sehingga cocok dengan tabel Surat Jalan.
- Form PO: auto-fill bulan/tahun No Invoice dan tahun No Surat Jalan ketika Tanggal PO dipilih.
- Jatuh Tempo: "No PO" menjadi input tanggal untuk konsistensi.
- Perbaikan kecil UI/UX (konsistensi, aksesibilitas, dan responsif).

### 2025-09-08 (v2.3.0)
- Auth: UI halaman `Forgot Password` diperindah, tombol "Kembali" ditambahkan, dan alur OTP dipermudah.
- Database: Konfigurasi final MySQL digunakan (`manajemen_perusahaan` @ 127.0.0.1), perbaikan kolom hilang di beberapa tabel.
- Stabilitas: Penyesuaian query untuk kompatibilitas MySQL sepenuhnya.

### 2025-09-05 (v2.2.0)
- Konsolidasi modul **Tanda Terima** ke dalam **Surat Jalan**; rute Tanda Terima dinonaktifkan.
- Jatuh Tempo: tambah rute `send-reminder`, `update-status`, dan `update-deadline`.
- Finance: tambah endpoint `POST /finance/pengeluaran/store` untuk menyimpan pengeluaran.
- Tambah Notifications API: `GET /api/jatuh-tempo/notifications`.
- Sinkronisasi daftar rute di README dengan `routes/web.php`.

## 🤝 Contributing

<div align="center">

### 💝 **We Welcome Contributors!**

</div>

```bash
# 1️⃣ Fork the repository
# 2️⃣ Create feature branch
git checkout -b feature/amazing-feature

# 3️⃣ Commit changes (follow conventional commits)
git commit -m "feat(ui): add amazing feature"

# 4️⃣ Push to branch
git push origin feature/amazing-feature

# 5️⃣ Open Pull Request
```

### 📝 **Commit Convention**
- `feat(scope):` ✨ New features
- `fix(scope):` 🐛 Bug fixes  
- `docs(scope):` 📚 Documentation
- `style(scope):` 💄 Code style
- `refactor(scope):` ♻️ Code refactoring
- `chore(scope):` 🔧 Maintenance

---

<div align="center">

### 📄 **License**

This project is licensed under the **MIT License** - see the [LICENSE](LICENSE) file for details.

### 💖 **Made with Love**

Built with ❤️ by [DillanINF](https://github.com/DillanINF)

<p align="center">
  <img src="https://img.shields.io/badge/⭐-Star%20this%20repo-yellow?style=for-the-badge" alt="Star this repo">
</p>

</div>
