# Data Flow Diagram (DFD) Level 0
## Sistem Manajemen Perusahaan PT. CAM JAYA ABADI

### Context Diagram (DFD Level 0)

```mermaid
graph TD
    subgraph External["Entitas Eksternal"]
        A[Admin/Manager]
        B[Staff Keuangan]
        C[Staff Gudang]
        D[Customer]
        E[Karyawan]
    end
    
    subgraph System["Sistem Manajemen Perusahaan"]
        SYS((0<br/>Sistem<br/>Manajemen<br/>Perusahaan))
    end
    
    %% Admin/Manager
    A -->|Data Login| SYS
    A -->|Data Master| SYS
    SYS -->|Laporan Dashboard| A
    SYS -->|Laporan Keuangan| A
    
    %% Staff Keuangan
    B -->|Data Invoice| SYS
    B -->|Data Pembayaran| SYS
    B -->|Data Pengeluaran| SYS
    SYS -->|Laporan Invoice| B
    SYS -->|Laporan Jatuh Tempo| B
    
    %% Staff Gudang
    C -->|Data Barang Masuk| SYS
    C -->|Data Barang Keluar| SYS
    SYS -->|Laporan Stok| C
    
    %% Customer
    D -->|Purchase Order| SYS
    SYS -->|Surat Jalan| D
    SYS -->|Invoice| D
    
    %% Karyawan
    SYS -->|Slip Gaji| E
    B -->|Data Kehadiran| SYS
    
    style SYS fill:#3498db,stroke:#2c3e50,stroke-width:3px,color:#fff
    style A fill:#e74c3c,stroke:#c0392b,stroke-width:2px,color:#fff
    style B fill:#e74c3c,stroke:#c0392b,stroke-width:2px,color:#fff
    style C fill:#e74c3c,stroke:#c0392b,stroke-width:2px,color:#fff
    style D fill:#e74c3c,stroke:#c0392b,stroke-width:2px,color:#fff
    style E fill:#e74c3c,stroke:#c0392b,stroke-width:2px,color:#fff
```

---

## Penjelasan Entitas Eksternal

### 1. **Admin/Manager**
   - **Input:** Data login, data master (produk, kendaraan, customer, karyawan)
   - **Output:** Laporan dashboard, laporan keuangan lengkap
   - **Fungsi:** Mengelola sistem dan melihat semua laporan

### 2. **Staff Keuangan**
   - **Input:** Data invoice, data pembayaran, data pengeluaran, data kehadiran karyawan
   - **Output:** Laporan invoice, laporan jatuh tempo, slip gaji
   - **Fungsi:** Mengelola keuangan dan penggajian

### 3. **Staff Gudang**
   - **Input:** Data barang masuk, data barang keluar
   - **Output:** Laporan stok produk
   - **Fungsi:** Mengelola inventori/stok barang

### 4. **Customer**
   - **Input:** Purchase Order (PO)
   - **Output:** Surat jalan, invoice
   - **Fungsi:** Melakukan pemesanan dan menerima dokumen

### 5. **Karyawan**
   - **Input:** -
   - **Output:** Slip gaji bulanan
   - **Fungsi:** Menerima informasi gaji

---

## Aliran Data Utama

| Dari | Ke | Data | Keterangan |
|------|-----|------|------------|
| Admin | Sistem | Data Master | Input produk, kendaraan, customer, karyawan |
| Staff Keuangan | Sistem | Data Invoice | Input tagihan customer |
| Staff Keuangan | Sistem | Data Pengeluaran | Input expenses perusahaan |
| Staff Keuangan | Sistem | Data Kehadiran | Input kehadiran untuk gaji |
| Staff Gudang | Sistem | Data Barang Masuk | Input stok masuk |
| Staff Gudang | Sistem | Data Barang Keluar | Input stok keluar |
| Customer | Sistem | Purchase Order | Order dari customer |
| Sistem | Admin | Laporan Dashboard | Grafik dan statistik |
| Sistem | Staff Keuangan | Laporan Invoice | Daftar invoice dan jatuh tempo |
| Sistem | Staff Gudang | Laporan Stok | Laporan stok produk |
| Sistem | Customer | Surat Jalan & Invoice | Dokumen pengiriman dan tagihan |
| Sistem | Karyawan | Slip Gaji | Rincian gaji bulanan |

---

**Status:** ✅ DFD Level 0 (Context Diagram) selesai  
**Selanjutnya:** DFD Level 1 (detail per proses)
