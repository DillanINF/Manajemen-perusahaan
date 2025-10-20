-- ============================================
-- HAPUS TABEL INVOICES (Tidak Digunakan)
-- Jalankan di phpMyAdmin setelah memastikan
-- semua kode sudah dibersihkan
-- ============================================

USE manajemen_perusahaan;

-- Cek apakah tabel invoices ada
SELECT 
    'Tabel invoices ditemukan' as status,
    COUNT(*) as jumlah_records
FROM invoices;

-- HAPUS tabel invoices
-- PERINGATAN: Ini tidak bisa di-undo!
DROP TABLE IF EXISTS invoices;

-- Verifikasi tabel sudah terhapus
SHOW TABLES LIKE 'invoices';
-- Jika kosong berarti sudah terhapus

-- ============================================
-- CATATAN:
-- - Data invoice sebenarnya ada di tabel POS
-- - Form "Data Invoice" menyimpan ke tabel POS
-- - Tabel invoices hanya legacy yang tidak terpakai
-- ============================================
