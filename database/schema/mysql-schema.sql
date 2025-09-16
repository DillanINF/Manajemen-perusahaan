/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
DROP TABLE IF EXISTS `annual_summaries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `annual_summaries` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `year` smallint unsigned NOT NULL,
  `revenue_net_total` bigint NOT NULL DEFAULT '0',
  `expense_salary_total` bigint NOT NULL DEFAULT '0',
  `expense_other_total` bigint NOT NULL DEFAULT '0',
  `expense_total` bigint NOT NULL DEFAULT '0',
  `employee_count` int unsigned NOT NULL DEFAULT '0',
  `invoice_count` int unsigned NOT NULL DEFAULT '0',
  `barang_masuk_qty` bigint NOT NULL DEFAULT '0',
  `barang_keluar_qty` bigint NOT NULL DEFAULT '0',
  `meta` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `annual_summaries_year_unique` (`year`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `barang_keluars`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `barang_keluars` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tanggal` date NOT NULL,
  `produk_id` bigint unsigned DEFAULT NULL,
  `qty` int NOT NULL DEFAULT '0',
  `qty_jenis` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'PCS',
  `harga` decimal(15,2) NOT NULL DEFAULT '0.00',
  `total` decimal(15,2) NOT NULL DEFAULT '0.00',
  `keterangan` text COLLATE utf8mb4_unicode_ci,
  `user_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `barang_keluars_user_id_foreign` (`user_id`),
  CONSTRAINT `barang_keluars_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `barang_masuks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `barang_masuks` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tanggal` date NOT NULL,
  `produk_id` bigint unsigned DEFAULT NULL,
  `qty` int NOT NULL DEFAULT '0',
  `qty_jenis` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'PCS',
  `harga` decimal(15,2) NOT NULL DEFAULT '0.00',
  `total` decimal(15,2) NOT NULL DEFAULT '0.00',
  `keterangan` text COLLATE utf8mb4_unicode_ci,
  `user_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `barang_masuks_user_id_foreign` (`user_id`),
  CONSTRAINT `barang_masuks_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `customers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `customers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address_1` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address_2` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_terms_days` int NOT NULL DEFAULT '30',
  `delivery_note_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `invoice_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `nama_customer` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `code_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `employees`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `employees` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nama_karyawan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `no_telepon` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `alamat` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `posisi` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `departemen` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `gaji_pokok` decimal(15,2) NOT NULL,
  `tunjangan` decimal(15,2) NOT NULL DEFAULT '0.00',
  `status` enum('aktif','tidak_aktif') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'aktif',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `foto` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `tanggal_masuk` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `employees_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `expenses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `expenses` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tanggal` date NOT NULL,
  `jenis` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deskripsi` text COLLATE utf8mb4_unicode_ci,
  `description` text COLLATE utf8mb4_unicode_ci,
  `kategori` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `keterangan` text COLLATE utf8mb4_unicode_ci,
  `jumlah` decimal(15,2) NOT NULL DEFAULT '0.00',
  `amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `user_id` bigint unsigned DEFAULT NULL,
  `status` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'approved',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `invoices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `invoices` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `no_invoice` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `no_po` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `alamat_1` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `alamat_2` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tanggal_invoice` date NOT NULL,
  `tanggal_jatuh_tempo` date NOT NULL,
  `produk_id` bigint unsigned NOT NULL,
  `qty` int NOT NULL,
  `qty_jenis` enum('PCS','SET') COLLATE utf8mb4_unicode_ci NOT NULL,
  `harga` int NOT NULL,
  `total` int NOT NULL,
  `pajak` int DEFAULT NULL,
  `grand_total` int NOT NULL,
  `status` enum('Draft','Sent','Paid','Overdue') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Draft',
  `keterangan` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `invoices_no_invoice_unique` (`no_invoice`),
  KEY `invoices_produk_id_foreign` (`produk_id`),
  CONSTRAINT `invoices_produk_id_foreign` FOREIGN KEY (`produk_id`) REFERENCES `produks` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `jatuh_tempos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jatuh_tempos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `no_invoice` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `no_po` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal_invoice` date NOT NULL,
  `tanggal_jatuh_tempo` date NOT NULL,
  `jumlah_tagihan` bigint NOT NULL,
  `jumlah_terbayar` bigint NOT NULL DEFAULT '0',
  `sisa_tagihan` bigint NOT NULL,
  `status_pembayaran` enum('Belum Bayar','Sebagian','Lunas') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Belum Bayar',
  `status_approval` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Pending',
  `hari_terlambat` int NOT NULL DEFAULT '0',
  `denda` bigint DEFAULT NULL,
  `catatan` text COLLATE utf8mb4_unicode_ci,
  `reminder_sent` tinyint(1) NOT NULL DEFAULT '0',
  `last_reminder_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `jatuh_tempos_tanggal_jatuh_tempo_index` (`tanggal_jatuh_tempo`),
  KEY `jatuh_tempos_status_approval_index` (`status_approval`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `kendaraans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `kendaraans` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nama_kendaraan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `no_polisi` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `jenis` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `nama` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `otp_codes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `otp_codes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `otp_code` varchar(6) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expires_at` timestamp NOT NULL,
  `is_used` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `otp_codes_email_otp_code_index` (`email`,`otp_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `pengirim`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pengirim` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nama` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kendaraan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `no_polisi` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `po_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `po_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `po_id` bigint unsigned NOT NULL,
  `produk_id` bigint unsigned DEFAULT NULL,
  `qty` int NOT NULL DEFAULT '0',
  `qty_jenis` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'PCS',
  `harga` decimal(15,2) NOT NULL DEFAULT '0.00',
  `total` decimal(15,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `po_items_po_id_foreign` (`po_id`),
  KEY `po_items_produk_id_foreign` (`produk_id`),
  CONSTRAINT `po_items_po_id_foreign` FOREIGN KEY (`po_id`) REFERENCES `pos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `po_items_produk_id_foreign` FOREIGN KEY (`produk_id`) REFERENCES `produks` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `pos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `no_surat_jalan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `no_po` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `no_invoice` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `customer_id` bigint unsigned DEFAULT NULL,
  `po_number` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `customer` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `pengirim` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status_approval` enum('Pending','Accept') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Pending',
  `alamat_1` text COLLATE utf8mb4_unicode_ci,
  `alamat_2` text COLLATE utf8mb4_unicode_ci,
  `tanggal_po` date NOT NULL,
  `produk_id` bigint unsigned DEFAULT NULL,
  `qty` int NOT NULL,
  `qty_jenis` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `harga` int NOT NULL,
  `total` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `kendaraan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kendaraan_id` bigint unsigned DEFAULT NULL,
  `no_polisi` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pos_produk_id_foreign` (`produk_id`),
  KEY `pos_kendaraan_id_index` (`kendaraan_id`),
  KEY `pos_customer_id_foreign` (`customer_id`),
  CONSTRAINT `pos_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL,
  CONSTRAINT `pos_kendaraan_id_foreign` FOREIGN KEY (`kendaraan_id`) REFERENCES `kendaraans` (`id`) ON DELETE SET NULL,
  CONSTRAINT `pos_produk_id_foreign` FOREIGN KEY (`produk_id`) REFERENCES `produks` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `produks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `produks` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `kode_produk` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_produk` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `harga` decimal(15,2) NOT NULL DEFAULT '0.00',
  `harga_pcs` decimal(15,2) NOT NULL DEFAULT '0.00',
  `harga_set` decimal(15,2) NOT NULL DEFAULT '0.00',
  `satuan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deskripsi` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `produks_kode_produk_unique` (`kode_produk`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `salaries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `salaries` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` bigint unsigned NOT NULL,
  `bulan` tinyint unsigned NOT NULL,
  `tahun` smallint unsigned NOT NULL,
  `gaji_pokok` decimal(15,2) NOT NULL DEFAULT '0.00',
  `tunjangan` decimal(15,2) NOT NULL DEFAULT '0.00',
  `bonus` decimal(15,2) NOT NULL DEFAULT '0.00',
  `lembur` decimal(15,2) NOT NULL DEFAULT '0.00',
  `potongan_pajak` decimal(15,2) NOT NULL DEFAULT '0.00',
  `potongan_bpjs` decimal(15,2) NOT NULL DEFAULT '0.00',
  `potongan_lain` decimal(15,2) NOT NULL DEFAULT '0.00',
  `potongan` decimal(15,2) NOT NULL DEFAULT '0.00',
  `total_gaji` decimal(15,2) NOT NULL DEFAULT '0.00',
  `status_pembayaran` enum('belum_dibayar','dibayar') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'belum_dibayar',
  `tanggal_bayar` date DEFAULT NULL,
  `keterangan` text COLLATE utf8mb4_unicode_ci,
  `status` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'paid',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `salaries_employee_id_bulan_tahun_index` (`employee_id`,`bulan`,`tahun`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `settings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` text COLLATE utf8mb4_unicode_ci,
  `next_invoice_number` int NOT NULL DEFAULT '1000',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `settings_key_unique` (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `sisa_po_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sisa_po_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `no_po` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `produk_id` bigint unsigned NOT NULL,
  `qty_diminta` int NOT NULL,
  `qty_tersedia` int NOT NULL,
  `qty_sisa` int NOT NULL,
  `qty_jenis` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'PCS',
  `harga` decimal(15,2) NOT NULL DEFAULT '0.00',
  `total_sisa` decimal(15,2) NOT NULL DEFAULT '0.00',
  `customer` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tanggal_po` date NOT NULL,
  `status` enum('pending','fulfilled','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `keterangan` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sisa_po_items_no_po_status_index` (`no_po`,`status`),
  KEY `sisa_po_items_produk_id_status_index` (`produk_id`,`status`),
  KEY `sisa_po_items_tanggal_po_index` (`tanggal_po`),
  CONSTRAINT `sisa_po_items_produk_id_foreign` FOREIGN KEY (`produk_id`) REFERENCES `produks` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `surat_jalans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `surat_jalans` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `no_surat_jalan` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `no_po` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `alamat` text COLLATE utf8mb4_unicode_ci,
  `tanggal_po` date NOT NULL,
  `produk_id` bigint unsigned NOT NULL,
  `qty` int NOT NULL,
  `qty_jenis` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `harga` bigint NOT NULL,
  `total` bigint NOT NULL,
  `kendaraan` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `no_polisi` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `driver` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `keterangan` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `surat_jalans_produk_id_foreign` (`produk_id`),
  KEY `surat_jalans_tanggal_po_index` (`tanggal_po`),
  KEY `surat_jalans_customer_index` (`customer`),
  KEY `surat_jalans_no_po_index` (`no_po`),
  CONSTRAINT `surat_jalans_produk_id_foreign` FOREIGN KEY (`produk_id`) REFERENCES `produks` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `tanda_terimas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tanda_terimas` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `no_tanda_terima` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `no_po` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `no_surat_jalan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `customer` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `alamat_1` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `alamat_2` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tanggal_terima` date NOT NULL,
  `produk_id` bigint unsigned NOT NULL,
  `qty_dikirim` int NOT NULL,
  `qty_diterima` int NOT NULL,
  `qty_jenis` enum('PCS','SET') COLLATE utf8mb4_unicode_ci NOT NULL,
  `kondisi_barang` enum('Baik','Rusak','Sebagian Rusak') COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('Lengkap','Sebagian','Pending') COLLATE utf8mb4_unicode_ci NOT NULL,
  `penerima_nama` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `penerima_jabatan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `catatan` text COLLATE utf8mb4_unicode_ci,
  `foto_bukti` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tanda_terimas_no_tanda_terima_unique` (`no_tanda_terima`),
  KEY `tanda_terimas_produk_id_foreign` (`produk_id`),
  CONSTRAINT `tanda_terimas_produk_id_foreign` FOREIGN KEY (`produk_id`) REFERENCES `produks` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `profile_photo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_admin` tinyint(1) NOT NULL DEFAULT '0',
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (1,'0001_01_01_000000_create_users_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (2,'0001_01_01_000001_create_cache_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (3,'0001_01_01_000002_create_jobs_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (4,'2024_09_08_134233_add_gaji_pokok_to_employees',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (5,'2024_09_08_135039_add_customer_to_pos',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (6,'2025_09_11_110930_add_po_number_to_pos_table',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (7,'2025_08_14_083508_create_employees_table',3);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (8,'2025_08_13_155730_create_customers_table',4);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (9,'2025_01_04_141800_add_payment_terms_to_customers',5);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (10,'2025_08_05_084802_create_pos_table',5);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (11,'2025_08_05_094847_add_kendaraan_nopol_to_pos_table',5);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (12,'2025_08_05_162459_create_produks_table',5);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (13,'2025_08_06_124858_add_qty_jenis_to_pos_table',5);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (14,'2025_08_07_084402_create_surat_jalan_table',5);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (15,'2025_08_11_023535_remove_produk_from_pos_table',5);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (16,'2025_08_11_032145_add_produk_id_to_pos_table',5);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (17,'2025_08_12_085845_add_harga_columns_to_produks_table',5);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (18,'2025_09_11_113200_ensure_customers_table_exists',6);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (19,'2025_09_11_112500_create_kendaraans_table',7);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (20,'2025_09_11_022007_create_pos_table',8);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (21,'2025_08_16_065612_create_pengirim_table',9);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (22,'2025_09_11_113500_ensure_pengirim_table_exists',10);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (23,'2025_09_11_113800_ensure_po_items_table_exists',11);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (24,'2025_09_11_114100_ensure_salaries_table_exists',12);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (25,'2025_09_11_114300_ensure_expenses_table_exists',13);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (26,'2025_09_11_114500_add_amount_to_expenses_table',14);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (27,'2025_09_11_114700_ensure_barang_masuks_table_exists',15);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (28,'2025_09_11_114900_ensure_barang_keluars_table_exists',16);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (29,'2025_09_11_115100_add_missing_columns_to_pos_table',17);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (30,'2025_09_11_115800_ensure_po_number_in_pos',18);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (31,'2025_08_12_093337_add_no_polisi_to_kendaraans_table',19);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (32,'2025_08_14_061934_add_alamat_columns_to_pos_table',19);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (33,'2025_08_14_075941_create_invoices_table',19);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (34,'2025_08_14_075941_create_kendaraans_table',19);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (35,'2025_08_14_075942_create_tanda_terimas_table',19);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (36,'2025_08_14_075943_create_jatuh_tempos_table',19);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (37,'2025_08_14_075944_remove_duplicate_alamat_columns_from_pos_table',19);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (38,'2025_08_14_083509_create_salaries_table',19);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (39,'2025_08_14_091700_simplify_employees_table',19);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (40,'2025_08_15_041152_clean_invalid_kendaraan_data_in_pos_table',19);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (41,'2025_08_15_041153_add_kendaraan_id_to_pos_table',19);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (42,'2025_08_15_041154_modify_kendaraan_column_in_pos_table',19);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (43,'2025_08_16_064119_add_pengirim_column_to_pos_table',19);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (44,'2025_08_19_000001_create_po_items_table',19);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (45,'2025_08_19_000002_add_customer_id_to_pos_table',19);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (46,'2025_08_26_000000_add_is_admin_to_users_table',19);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (47,'2025_08_26_114500_add_no_invoice_to_pos_table',19);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (48,'2025_08_26_153100_add_invoice_number_to_customers_table',19);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (49,'2025_08_27_013000_create_expenses_table',19);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (50,'2025_08_29_000001_create_barang_masuks_table',19);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (51,'2025_08_29_000002_create_barang_keluars_table',19);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (52,'2025_09_03_000000_create_annual_summaries_table',19);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (53,'2025_09_03_084607_add_profile_photo_to_users_table',19);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (54,'2025_09_04_013335_add_status_approval_to_jatuh_tempos',19);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (55,'2025_09_08_013012_create_otp_codes_table',19);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (56,'2025_09_08_135534_add_total_gaji_to_salaries_table',19);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (57,'2025_09_08_143600_add_qty_to_barang_masuks_table',19);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (58,'2025_09_08_143700_add_qty_to_barang_keluars_table',19);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (59,'2025_09_08_143900_add_missing_customer_columns',19);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (60,'2025_09_08_144050_add_missing_columns_to_produks_table',19);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (61,'2025_09_08_144900_add_nama_kendaraan_to_kendaraans_table',19);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (62,'2025_09_08_145100_add_missing_employee_columns',19);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (63,'2025_09_08_145600_add_status_pembayaran_to_salaries_table',19);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (64,'2025_09_08_145700_add_missing_columns_to_salaries_table',19);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (65,'2025_09_08_152600_add_jenis_to_expenses_table',19);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (66,'2025_09_08_152800_add_deskripsi_to_expenses_table',19);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (67,'2025_09_08_152900_add_user_id_to_expenses_table',19);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (68,'2025_09_08_153300_add_legacy_description_to_expenses_table',19);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (69,'2025_09_08_153700_add_no_surat_jalan_to_pos_table',19);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (70,'2025_09_08_153900_add_missing_columns_to_pos_table',19);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (71,'2025_09_08_154000_add_po_number_to_pos_table',19);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (72,'2025_09_08_154100_alter_po_number_nullable_in_pos_table',19);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (73,'2025_09_08_154300_add_missing_columns_to_po_items_table',19);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (74,'2025_09_11_090400_add_next_invoice_number_to_settings_table',20);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (75,'2025_09_11_141600_fix_missing_columns',20);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (76,'2025_09_11_143100_add_name_to_produks_table',21);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (77,'2025_09_12_083800_add_user_id_to_barang_tables_if_missing',22);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (78,'2025_09_12_000001_add_code_number_to_customers_table',23);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (79,'2025_09_12_000002_add_kendaraan_no_polisi_to_pengirim_table',24);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (80,'2025_09_13_115131_create_sisa_po_items_table',25);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (81,'2025_09_16_020330_update_jatuh_tempos_unique_index',26);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (82,'2025_09_16_023000_drop_all_unique_indexes_on_jatuh_tempos',27);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (83,'2025_09_16_154200_add_status_approval_to_pos_table',28);
