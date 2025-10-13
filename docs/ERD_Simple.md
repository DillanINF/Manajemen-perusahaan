# Entity Relationship Diagram (ERD)
## Sistem Manajemen Perusahaan PT. CAM JAYA ABADI

### Diagram Relasi Database

```mermaid
erDiagram
    USERS ||--o{ BARANG_MASUKS : creates
    USERS ||--o{ BARANG_KELUARS : creates
    USERS ||--o{ EXPENSES : records
    
    EMPLOYEES ||--o{ SALARIES : receives
    
    CUSTOMERS ||--o{ POS : orders
    
    PRODUKS ||--o{ POS : "used in"
    PRODUKS ||--o{ PO_ITEMS : contains
    PRODUKS ||--o{ INVOICES : "billed in"
    PRODUKS ||--o{ BARANG_MASUKS : "stock in"
    PRODUKS ||--o{ BARANG_KELUARS : "stock out"
    
    KENDARAANS ||--o{ POS : transports
    
    PENGIRIM ||--o{ POS : sends
    
    POS ||--o{ PO_ITEMS : "has details"
    
    USERS {
        int id PK
        string name
        string email
        string password
        boolean is_admin
    }
    
    EMPLOYEES {
        int id PK
        string nama_karyawan
        string email
        string posisi
        decimal gaji_pokok
        string status
    }
    
    CUSTOMERS {
        int id PK
        string name
        string email
        string phone
        string address
        int payment_terms_days
    }
    
    PRODUKS {
        int id PK
        string kode_produk
        string nama_produk
        decimal harga
        string satuan
    }
    
    KENDARAANS {
        int id PK
        string nama
        string no_polisi
        string jenis_kendaraan
    }
    
    PENGIRIM {
        int id PK
        string nama
        string kendaraan
        string no_polisi
    }
    
    POS {
        int id PK
        date tanggal_po
        int customer_id FK
        int produk_id FK
        int kendaraan FK
        string pengirim FK
        string no_po
        int qty
        decimal total
    }
    
    PO_ITEMS {
        int id PK
        int po_id FK
        int produk_id FK
        int qty
        decimal total
    }
    
    INVOICES {
        int id PK
        string no_invoice
        string customer
        date tanggal_invoice
        int produk_id FK
        decimal grand_total
    }
    
    SALARIES {
        int id PK
        int employee_id FK
        string bulan
        int tahun
        decimal total_gaji
        string status_pembayaran
    }
    
    BARANG_MASUKS {
        int id PK
        int produk_id FK
        int user_id FK
        int qty
        date tanggal
    }
    
    BARANG_KELUARS {
        int id PK
        int produk_id FK
        int user_id FK
        int qty
        date tanggal
    }
    
    EXPENSES {
        int id PK
        int user_id FK
        date tanggal
        string jenis
        decimal amount
    }
```

---

### Penjelasan Singkat

**Tabel Utama:**
- **Users** - User login sistem (admin & user biasa)
- **Employees** - Data karyawan perusahaan
- **Customers** - Data customer/pelanggan
- **Produks** - Master data produk
- **Kendaraans** - Data kendaraan untuk pengiriman
- **Pengirim** - Data pengirim barang

**Tabel Transaksi:**
- **POS** - Purchase Orders (pesanan)
- **PO_Items** - Detail item per PO
- **Invoices** - Faktur/invoice
- **Salaries** - Gaji karyawan
- **Barang Masuks** - Stok masuk
- **Barang Keluars** - Stok keluar
- **Expenses** - Pengeluaran perusahaan

**Relasi Utama:**
- 1 Customer → banyak PO
- 1 PO → banyak PO Items
- 1 Produk → dipakai di banyak transaksi
- 1 Employee → terima banyak Salary records
- Users mencatat Barang Masuk/Keluar dan Expenses
