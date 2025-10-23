-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Oct 23, 2025 at 09:09 AM
-- Server version: 8.0.30
-- PHP Version: 8.3.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `manajemen_perusahaan`
--

-- --------------------------------------------------------

--
-- Table structure for table `annual_summaries`
--

CREATE TABLE `annual_summaries` (
  `id` bigint UNSIGNED NOT NULL,
  `year` smallint UNSIGNED NOT NULL,
  `revenue_net_total` bigint NOT NULL DEFAULT '0',
  `expense_salary_total` bigint NOT NULL DEFAULT '0',
  `expense_other_total` bigint NOT NULL DEFAULT '0',
  `expense_total` bigint NOT NULL DEFAULT '0',
  `employee_count` int UNSIGNED NOT NULL DEFAULT '0',
  `invoice_count` int UNSIGNED NOT NULL DEFAULT '0',
  `barang_masuk_qty` bigint NOT NULL DEFAULT '0',
  `barang_keluar_qty` bigint NOT NULL DEFAULT '0',
  `meta` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `barang_keluars`
--

CREATE TABLE `barang_keluars` (
  `id` bigint UNSIGNED NOT NULL,
  `tanggal` date NOT NULL,
  `produk_id` bigint UNSIGNED DEFAULT NULL,
  `qty` int NOT NULL DEFAULT '0',
  `qty_jenis` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'PCS',
  `keterangan` text COLLATE utf8mb4_unicode_ci,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `barang_keluars`
--

INSERT INTO `barang_keluars` (`id`, `tanggal`, `produk_id`, `qty`, `qty_jenis`, `keterangan`, `user_id`, `created_at`, `updated_at`) VALUES
(87, '2025-09-17', 7, 4, 'PCS', 'Auto Keluar dari PO 2445', 5, '2025-09-16 23:39:27', '2025-09-16 23:39:27'),
(95, '2025-09-17', 7, 4, 'PCS', 'Auto Keluar dari PO 4364', 5, '2025-09-16 23:58:55', '2025-09-16 23:58:55'),
(97, '2025-09-17', 7, 8, 'PCS', 'Auto Keluar dari PO 423', 5, '2025-09-17 00:06:12', '2025-09-17 00:06:12'),
(147, '2025-06-25', 1, 32, 'PCS', 'Auto Keluar dari PO 0812345', 5, '2025-10-06 23:24:28', '2025-10-06 23:24:28'),
(150, '2025-05-06', 7, 4, 'PCS', 'Auto Keluar dari PO 453', 5, '2025-10-07 02:40:33', '2025-10-07 02:40:33'),
(152, '2025-06-08', 1, 13, 'PCS', 'Auto Keluar dari PO 2324', 5, '2025-10-07 20:30:13', '2025-10-07 20:30:13'),
(153, '2025-10-08', 7, 32, 'PCS', 'Auto Keluar dari PO 0812345', 5, '2025-10-07 20:35:27', '2025-10-07 20:35:27'),
(154, '2025-10-08', 8, 32, 'PCS', 'Auto Keluar dari PO 0812345', 5, '2025-10-07 20:35:27', '2025-10-07 20:35:27'),
(155, '2025-04-08', 7, 23, 'PCS', 'Auto Keluar dari PO 23', 5, '2025-10-07 20:45:44', '2025-10-07 20:45:44'),
(157, '2025-09-13', 7, 3, 'PCS', 'Auto Keluar dari PO 93428', 5, '2025-10-13 00:06:31', '2025-10-13 00:06:31'),
(158, '2025-05-13', 7, 3, 'PCS', 'Auto Keluar dari PO 3424', 5, '2025-10-13 01:35:25', '2025-10-13 01:35:25'),
(164, '2025-10-16', 10, 23, 'PCS', 'Auto Keluar dari PO PO.48932', 5, '2025-10-15 23:57:52', '2025-10-15 23:57:52'),
(165, '2025-10-16', 10, 32, 'PCS', 'Auto Keluar dari PO PO.484354', 5, '2025-10-16 00:14:35', '2025-10-16 00:14:35'),
(169, '2025-10-17', 10, 3, 'PCS', 'Auto Keluar dari PO PO.43234', 5, '2025-10-16 20:55:58', '2025-10-16 20:55:58'),
(170, '2025-10-18', 10, 62, 'PCS', 'Auto Keluar dari PO PO.432423', 5, '2025-10-18 02:41:14', '2025-10-18 02:41:14'),
(171, '2025-10-20', 11, 34, 'PCS', 'Auto Keluar dari PO PO.5694234', 5, '2025-10-19 19:45:20', '2025-10-19 19:45:20'),
(174, '2025-10-20', 11, 3, 'PCS', 'Auto Keluar dari PO PO.432432', 5, '2025-10-19 19:56:38', '2025-10-19 19:56:38'),
(175, '2025-10-20', 11, 3, 'PCS', 'Auto Keluar dari PO PO.8347534', 5, '2025-10-19 21:08:29', '2025-10-19 21:08:29'),
(198, '2025-10-23', 17, 23, 'PCS', 'Auto Keluar dari PO po.342', 5, '2025-10-22 19:18:48', '2025-10-22 19:18:48'),
(199, '2025-06-23', 17, 3, 'PCS', 'Auto Keluar dari PO po.432432', 5, '2025-10-22 23:11:55', '2025-10-22 23:11:55'),
(200, '2025-04-23', 17, 2, 'PCS', 'Auto Keluar dari PO 2345324', 5, '2025-10-22 23:20:40', '2025-10-22 23:20:40');

-- --------------------------------------------------------

--
-- Table structure for table `barang_masuks`
--

CREATE TABLE `barang_masuks` (
  `id` bigint UNSIGNED NOT NULL,
  `tanggal` date NOT NULL,
  `produk_id` bigint UNSIGNED DEFAULT NULL,
  `qty` int NOT NULL DEFAULT '0',
  `qty_jenis` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'PCS',
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `barang_masuks`
--

INSERT INTO `barang_masuks` (`id`, `tanggal`, `produk_id`, `qty`, `qty_jenis`, `user_id`, `created_at`, `updated_at`) VALUES
(13, '2025-10-16', 10, 123, 'PCS', 5, '2025-10-15 20:21:42', '2025-10-15 20:21:42'),
(14, '2025-10-16', 11, 300, 'PCS', 5, '2025-10-15 23:31:34', '2025-10-15 23:31:34'),
(15, '2025-10-21', 12, 323, 'PCS', 5, '2025-10-20 18:44:35', '2025-10-20 18:44:35'),
(16, '2025-10-21', 10, 233, 'PCS', 5, '2025-10-20 18:58:48', '2025-10-20 18:58:48'),
(17, '2025-10-22', 17, 344, 'PCS', 9, '2025-10-22 02:37:01', '2025-10-22 02:37:01');

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address_1` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address_2` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_terms_days` int NOT NULL DEFAULT '30',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `code_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id`, `name`, `email`, `address_1`, `address_2`, `payment_terms_days`, `created_at`, `updated_at`, `code_number`) VALUES
(55, 'dayat', 'dilaninf6@gmail.com', 'JLN.DAYAT', 'KAB.DAYAT', 30, '2025-10-22 02:35:33', '2025-10-22 02:35:33', '32-DYT/2025');

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

CREATE TABLE `employees` (
  `id` bigint UNSIGNED NOT NULL,
  `nama_karyawan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `no_telepon` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `alamat` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `posisi` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `excel_sheet_edits`
--

CREATE TABLE `excel_sheet_edits` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `sheet_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `period_year` smallint UNSIGNED DEFAULT NULL,
  `period_month` tinyint UNSIGNED DEFAULT NULL,
  `cells` json NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `expenses`
--

CREATE TABLE `expenses` (
  `id` bigint UNSIGNED NOT NULL,
  `tanggal` date NOT NULL,
  `jenis` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deskripsi` text COLLATE utf8mb4_unicode_ci,
  `amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `status` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'approved',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `expenses`
--

INSERT INTO `expenses` (`id`, `tanggal`, `jenis`, `deskripsi`, `amount`, `user_id`, `status`, `created_at`, `updated_at`) VALUES
(9, '2025-10-22', 'Transportasi', 'dayat', '200000.00', 5, 'approved', '2025-10-21 20:56:23', '2025-10-21 20:56:23');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jatuh_tempos`
--

CREATE TABLE `jatuh_tempos` (
  `id` bigint UNSIGNED NOT NULL,
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
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `jatuh_tempos`
--

INSERT INTO `jatuh_tempos` (`id`, `no_invoice`, `no_po`, `customer`, `tanggal_invoice`, `tanggal_jatuh_tempo`, `jumlah_tagihan`, `jumlah_terbayar`, `sisa_tagihan`, `status_pembayaran`, `status_approval`, `created_at`, `updated_at`) VALUES
(129, '1', 'po.342', 'dayat', '2025-08-23', '2025-09-23', 4600000, 0, 4600000, 'Lunas', 'Pending', '2025-10-22 19:39:51', '2025-10-22 20:44:47'),
(131, '2', 'po.432432', 'dayat', '2025-06-23', '2025-07-23', 600000, 0, 600000, 'Belum Bayar', 'Pending', '2025-10-22 23:12:50', '2025-10-22 23:18:31'),
(132, '3', '2345324', 'dayat', '2025-04-23', '2025-05-23', 400000, 0, 400000, 'Belum Bayar', 'Pending', '2025-10-22 23:20:47', '2025-10-22 23:20:47');

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint UNSIGNED NOT NULL,
  `reserved_at` int UNSIGNED DEFAULT NULL,
  `available_at` int UNSIGNED NOT NULL,
  `created_at` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

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
  `finished_at` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2024_09_08_134233_add_gaji_pokok_to_employees', 1),
(5, '2024_09_08_135039_add_customer_to_pos', 1),
(6, '2025_09_11_110930_add_po_number_to_pos_table', 2),
(7, '2025_08_14_083508_create_employees_table', 3),
(8, '2025_08_13_155730_create_customers_table', 4),
(9, '2025_01_04_141800_add_payment_terms_to_customers', 5),
(10, '2025_08_05_084802_create_pos_table', 5),
(11, '2025_08_05_094847_add_kendaraan_nopol_to_pos_table', 5),
(12, '2025_08_05_162459_create_produks_table', 5),
(13, '2025_08_06_124858_add_qty_jenis_to_pos_table', 5),
(14, '2025_08_07_084402_create_surat_jalan_table', 5),
(15, '2025_08_11_023535_remove_produk_from_pos_table', 5),
(16, '2025_08_11_032145_add_produk_id_to_pos_table', 5),
(17, '2025_08_12_085845_add_harga_columns_to_produks_table', 5),
(18, '2025_09_11_113200_ensure_customers_table_exists', 6),
(19, '2025_09_11_112500_create_kendaraans_table', 7),
(20, '2025_09_11_022007_create_pos_table', 8),
(21, '2025_08_16_065612_create_pengirim_table', 9),
(22, '2025_09_11_113500_ensure_pengirim_table_exists', 10),
(23, '2025_09_11_113800_ensure_po_items_table_exists', 11),
(24, '2025_09_11_114100_ensure_salaries_table_exists', 12),
(25, '2025_09_11_114300_ensure_expenses_table_exists', 13),
(26, '2025_09_11_114500_add_amount_to_expenses_table', 14),
(27, '2025_09_11_114700_ensure_barang_masuks_table_exists', 15),
(28, '2025_09_11_114900_ensure_barang_keluars_table_exists', 16),
(29, '2025_09_11_115100_add_missing_columns_to_pos_table', 17),
(30, '2025_09_11_115800_ensure_po_number_in_pos', 18),
(31, '2025_08_12_093337_add_no_polisi_to_kendaraans_table', 19),
(32, '2025_08_14_061934_add_alamat_columns_to_pos_table', 19),
(33, '2025_08_14_075941_create_invoices_table', 19),
(34, '2025_08_14_075941_create_kendaraans_table', 19),
(35, '2025_08_14_075942_create_tanda_terimas_table', 19),
(36, '2025_08_14_075943_create_jatuh_tempos_table', 19),
(37, '2025_08_14_075944_remove_duplicate_alamat_columns_from_pos_table', 19),
(38, '2025_08_14_083509_create_salaries_table', 19),
(39, '2025_08_14_091700_simplify_employees_table', 19),
(40, '2025_08_15_041152_clean_invalid_kendaraan_data_in_pos_table', 19),
(41, '2025_08_15_041153_add_kendaraan_id_to_pos_table', 19),
(42, '2025_08_15_041154_modify_kendaraan_column_in_pos_table', 19),
(43, '2025_08_16_064119_add_pengirim_column_to_pos_table', 19),
(44, '2025_08_19_000001_create_po_items_table', 19),
(45, '2025_08_19_000002_add_customer_id_to_pos_table', 19),
(46, '2025_08_26_000000_add_is_admin_to_users_table', 19),
(47, '2025_08_26_114500_add_no_invoice_to_pos_table', 19),
(48, '2025_08_26_153100_add_invoice_number_to_customers_table', 19),
(49, '2025_08_27_013000_create_expenses_table', 19),
(50, '2025_08_29_000001_create_barang_masuks_table', 19),
(51, '2025_08_29_000002_create_barang_keluars_table', 19),
(52, '2025_09_03_000000_create_annual_summaries_table', 19),
(53, '2025_09_03_084607_add_profile_photo_to_users_table', 19),
(54, '2025_09_04_013335_add_status_approval_to_jatuh_tempos', 19),
(55, '2025_09_08_013012_create_otp_codes_table', 19),
(56, '2025_09_08_135534_add_total_gaji_to_salaries_table', 19),
(57, '2025_09_08_143600_add_qty_to_barang_masuks_table', 19),
(58, '2025_09_08_143700_add_qty_to_barang_keluars_table', 19),
(59, '2025_09_08_143900_add_missing_customer_columns', 19),
(60, '2025_09_08_144050_add_missing_columns_to_produks_table', 19),
(61, '2025_09_08_144900_add_nama_kendaraan_to_kendaraans_table', 19),
(62, '2025_09_08_145100_add_missing_employee_columns', 19),
(63, '2025_09_08_145600_add_status_pembayaran_to_salaries_table', 19),
(64, '2025_09_08_145700_add_missing_columns_to_salaries_table', 19),
(65, '2025_09_08_152600_add_jenis_to_expenses_table', 19),
(66, '2025_09_08_152800_add_deskripsi_to_expenses_table', 19),
(67, '2025_09_08_152900_add_user_id_to_expenses_table', 19),
(68, '2025_09_08_153300_add_legacy_description_to_expenses_table', 19),
(69, '2025_09_08_153700_add_no_surat_jalan_to_pos_table', 19),
(70, '2025_09_08_153900_add_missing_columns_to_pos_table', 19),
(71, '2025_09_08_154000_add_po_number_to_pos_table', 19),
(72, '2025_09_08_154100_alter_po_number_nullable_in_pos_table', 19),
(73, '2025_09_08_154300_add_missing_columns_to_po_items_table', 19),
(74, '2025_09_11_090400_add_next_invoice_number_to_settings_table', 20),
(75, '2025_09_11_141600_fix_missing_columns', 20),
(76, '2025_09_11_143100_add_name_to_produks_table', 21),
(77, '2025_09_12_083800_add_user_id_to_barang_tables_if_missing', 22),
(78, '2025_09_12_000001_add_code_number_to_customers_table', 23),
(79, '2025_09_12_000002_add_kendaraan_no_polisi_to_pengirim_table', 24),
(80, '2025_09_13_115131_create_sisa_po_items_table', 25),
(81, '2025_09_16_020330_update_jatuh_tempos_unique_index', 26),
(82, '2025_09_16_023000_drop_all_unique_indexes_on_jatuh_tempos', 27),
(83, '2025_09_16_154200_add_status_approval_to_pos_table', 28),
(84, '2025_01_16_160200_add_status_approval_to_pos_table', 29),
(85, '2025_09_18_150700_add_jenis_gaji_to_salaries_table', 29),
(90, '2025_09_23_140600_create_excel_sheet_edits_table', 30),
(91, '2025_10_07_080712_add_email_sent_at_to_jatuh_tempos_table', 30),
(92, '2025_10_07_091200_make_employee_id_nullable_in_salaries_table', 30),
(94, '2025_10_07_131700_add_email_to_customers_table', 31),
(95, '2025_10_22_020500_add_performance_indexes', 32);

-- --------------------------------------------------------

--
-- Table structure for table `otp_codes`
--

CREATE TABLE `otp_codes` (
  `id` bigint UNSIGNED NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `otp_code` varchar(6) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expires_at` timestamp NOT NULL,
  `is_used` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `otp_codes`
--

INSERT INTO `otp_codes` (`id`, `email`, `otp_code`, `expires_at`, `is_used`, `created_at`, `updated_at`) VALUES
(7, 'dilaninf6@gmail.com', '537447', '2025-10-22 01:05:01', 1, '2025-10-22 00:55:01', '2025-10-22 00:55:17');

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pengirim`
--

CREATE TABLE `pengirim` (
  `id` bigint UNSIGNED NOT NULL,
  `nama` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kendaraan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `no_polisi` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pengirim`
--

INSERT INTO `pengirim` (`id`, `nama`, `kendaraan`, `no_polisi`, `created_at`, `updated_at`) VALUES
(16, 'DAYAT', 'DAYAT', 'D 4 YAT', '2025-10-22 02:36:01', '2025-10-22 02:36:01');

-- --------------------------------------------------------

--
-- Table structure for table `pos`
--

CREATE TABLE `pos` (
  `id` bigint UNSIGNED NOT NULL,
  `no_surat_jalan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `no_po` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `no_invoice` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `customer` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `pengirim` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status_approval` enum('Pending','Accept') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Pending',
  `alamat_1` text COLLATE utf8mb4_unicode_ci,
  `alamat_2` text COLLATE utf8mb4_unicode_ci,
  `tanggal_po` date NOT NULL,
  `produk_id` bigint UNSIGNED DEFAULT NULL,
  `qty` int NOT NULL,
  `qty_jenis` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `harga` int NOT NULL,
  `total` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `kendaraan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `no_polisi` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pos`
--

INSERT INTO `pos` (`id`, `no_surat_jalan`, `no_po`, `no_invoice`, `customer`, `pengirim`, `status_approval`, `alamat_1`, `alamat_2`, `tanggal_po`, `produk_id`, `qty`, `qty_jenis`, `harga`, `total`, `created_at`, `updated_at`, `kendaraan`, `no_polisi`) VALUES
(424, '3245/32-DYT/2025', 'po.342', '1', 'dayat', 'DAYAT', 'Accept', 'JLN.DAYAT', 'KAB.DAYAT', '2025-08-23', 17, 23, 'PCS', 200000, 4600000, '2025-10-22 19:18:32', '2025-10-22 19:39:51', 'DAYAT', 'D 4 YAT'),
(425, '289344/32-DYT/2025', 'po.432432', '2', 'dayat', 'DAYAT', 'Accept', 'JLN.DAYAT', 'KAB.DAYAT', '2025-06-23', 17, 3, 'PCS', 200000, 600000, '2025-10-22 19:30:29', '2025-10-22 23:12:50', 'DAYAT', 'D 4 YAT'),
(426, '3425/32-DYT/2025', '2345324', '3', 'dayat', 'DAYAT', 'Accept', 'JLN.DAYAT', 'KAB.DAYAT', '2025-04-23', 17, 2, 'PCS', 200000, 400000, '2025-10-22 23:20:21', '2025-10-22 23:20:47', 'DAYAT', 'D 4 YAT');

-- --------------------------------------------------------

--
-- Table structure for table `po_items`
--

CREATE TABLE `po_items` (
  `id` bigint UNSIGNED NOT NULL,
  `po_id` bigint UNSIGNED NOT NULL,
  `produk_id` bigint UNSIGNED DEFAULT NULL,
  `qty` int NOT NULL DEFAULT '0',
  `qty_jenis` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'PCS',
  `harga` decimal(15,2) NOT NULL DEFAULT '0.00',
  `total` decimal(15,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `po_items`
--

INSERT INTO `po_items` (`id`, `po_id`, `produk_id`, `qty`, `qty_jenis`, `harga`, `total`, `created_at`, `updated_at`) VALUES
(211, 424, 17, 23, 'PCS', '200000.00', '4600000.00', '2025-10-22 19:18:48', '2025-10-22 19:18:48'),
(212, 425, 17, 3, 'PCS', '200000.00', '600000.00', '2025-10-22 23:11:55', '2025-10-22 23:11:55'),
(213, 426, 17, 2, 'PCS', '200000.00', '400000.00', '2025-10-22 23:20:40', '2025-10-22 23:20:40');

-- --------------------------------------------------------

--
-- Table structure for table `produks`
--

CREATE TABLE `produks` (
  `id` bigint UNSIGNED NOT NULL,
  `kode_produk` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_produk` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `harga` decimal(15,2) NOT NULL DEFAULT '0.00',
  `harga_pcs` decimal(15,2) NOT NULL DEFAULT '0.00',
  `harga_set` decimal(15,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `produks`
--

INSERT INTO `produks` (`id`, `kode_produk`, `nama_produk`, `name`, `harga`, `harga_pcs`, `harga_set`, `created_at`, `updated_at`) VALUES
(17, 'PRD0001', 'DAYAT', 'DAYAT', '200000.00', '200000.00', '0.00', '2025-10-22 02:35:48', '2025-10-22 02:35:48');

-- --------------------------------------------------------

--
-- Table structure for table `salaries`
--

CREATE TABLE `salaries` (
  `id` bigint UNSIGNED NOT NULL,
  `bulan` tinyint UNSIGNED NOT NULL,
  `tahun` smallint UNSIGNED NOT NULL,
  `gaji_pokok` decimal(15,2) NOT NULL DEFAULT '0.00',
  `potongan` decimal(15,2) NOT NULL DEFAULT '0.00',
  `total_gaji` decimal(15,2) NOT NULL DEFAULT '0.00',
  `status_pembayaran` enum('belum_dibayar','dibayar') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'belum_dibayar',
  `status` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'paid',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `salaries`
--

INSERT INTO `salaries` (`id`, `bulan`, `tahun`, `gaji_pokok`, `potongan`, `total_gaji`, `status_pembayaran`, `status`, `created_at`, `updated_at`) VALUES
(5, 10, 2025, '2300000.00', '0.00', '2300000.00', 'dibayar', 'paid', '2025-10-23 01:14:06', '2025-10-23 01:14:06');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('RmMrr5ZY7qUzZqZdGkYf1A1iKXls7Z5Z3TFjXZ17', 5, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0', 'ZXlKcGRpSTZJbmx1TjB0blpHVmtZV1oyWlV0V1RHSm9TbEl5V0hjOVBTSXNJblpoYkhWbElqb2lia0pRVGl0T1dEbE1aMGxxU2t4WmJYTXZTMFZ0WldwVWVVMUlNbFJWV2xWdlMwUXlUWEV3WWtoVGJHTnRZVVpETHpKSFVYSkROM05CYWt0V09HUjRiMVZ3VkdsRGVIaG1TVTlsY1ZGd1QyMWlVWGQ0UTNsdFZrNXJNV1l6ZHpRMWNVZzFOemR1VWs5TFRITlhZMUpVUVhGUFRHNUVNM2tyY21KdVRUaHdOMG96VjJJMEwwMXZWSHBtV2tsU1VscHZTbXd2ZEV4MmQweDZTV1I2YVcxM09WcFJVakF3YkZwRGF6SjJWbVJZYXpOQllrbHZRamh6VmtSSGRIWmtWalIzY1ZWNmVWUlJhRmRsS3pKeGRWWmhOalpKY1ZKeWEwUkhZMWhWUWpJNFRpOUNlV2xEVVZKRmJXdzVTazFOVDNGbGRFcE9SV3RCWTNCaU1WTndkRU13VjIxbFJYWm9UbkpZYmpnM01WaFlkVFZtZDNjMVNFOWlXRWRSYTBSeFdTOW9TbkF5U2t0Q2JtWlhVa0pMYlhjMVZpOUdRa0V6Y1ZGbmFrbEdSVE5NWldaQ01FVnFZMUJqTjNWelNVRndUMk4wTlZoelRVUllObXhtWWxkak1tVTNUVXR1UlhneFZtRlZjVXRVUjI1VFVEQlZkRUZPV1hGbE1uTkJWbGQwVlcwNVVUWklXWFIyWmxnNGREaGlZMVo2T1hOVlRVMVRVWFJHZDBNelZqZGhOMDF6ZVc5bFVrZDBUVGRxYkRseVdub3dhRFJFVm1sVmRHVlBaRWhFVDBWaVN5SXNJbTFoWXlJNklqZGlORFJrT1RoaVltUTNZV0pqTWpZNVpEVXpPVFkwWm1FM05qaGlOMkppWmpZMU1HRXdabVppTW1NeFlqVXlZMlUyWWprNVltWXpNV00xTnpVd01HWWlMQ0owWVdjaU9pSWlmUT09', 1761210563);

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` bigint UNSIGNED NOT NULL,
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` text COLLATE utf8mb4_unicode_ci,
  `next_invoice_number` int NOT NULL DEFAULT '1000',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `key`, `value`, `next_invoice_number`, `created_at`, `updated_at`) VALUES
(1, 'next_invoice_number', '3001', 1000, '2025-09-11 00:27:24', '2025-10-23 01:19:06');

-- --------------------------------------------------------

--
-- Table structure for table `sisa_po_items`
--

CREATE TABLE `sisa_po_items` (
  `id` bigint UNSIGNED NOT NULL,
  `no_po` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `produk_id` bigint UNSIGNED NOT NULL,
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
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_admin` tinyint(1) NOT NULL DEFAULT '0',
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `is_admin`, `remember_token`, `created_at`, `updated_at`) VALUES
(5, 'Super Admin', 'roidkubro86@gmail.com', '2025-09-16 06:51:40', '$2y$12$fOjjwYmryAY5wT52nWPpFOA5SViPgEqfW5TCtUR4uzH1z8DNk/xmC', 1, NULL, '2025-09-16 06:51:40', '2025-09-16 06:51:40'),
(9, 'DILAN', 'dilaninf6@gmail.com', NULL, '$2y$12$4uuYSjG2HuhSNAoB3jOu4OvAA7rKg.Q5rFv.AXEl9q7dyljLf64LG', 0, NULL, '2025-10-22 00:40:20', '2025-10-22 00:55:28');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `annual_summaries`
--
ALTER TABLE `annual_summaries`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `annual_summaries_year_unique` (`year`);

--
-- Indexes for table `barang_keluars`
--
ALTER TABLE `barang_keluars`
  ADD PRIMARY KEY (`id`),
  ADD KEY `barang_keluars_user_id_foreign` (`user_id`),
  ADD KEY `barang_keluars_tanggal_index` (`tanggal`),
  ADD KEY `barang_keluars_produk_id_index` (`produk_id`);

--
-- Indexes for table `barang_masuks`
--
ALTER TABLE `barang_masuks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `barang_masuks_user_id_foreign` (`user_id`),
  ADD KEY `barang_masuks_tanggal_index` (`tanggal`),
  ADD KEY `barang_masuks_produk_id_index` (`produk_id`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `customers_code_number_unique` (`code_number`);

--
-- Indexes for table `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `excel_sheet_edits`
--
ALTER TABLE `excel_sheet_edits`
  ADD PRIMARY KEY (`id`),
  ADD KEY `excel_sheet_edits_user_id_index` (`user_id`),
  ADD KEY `excel_sheet_edits_period_year_index` (`period_year`),
  ADD KEY `excel_sheet_edits_period_month_index` (`period_month`);

--
-- Indexes for table `expenses`
--
ALTER TABLE `expenses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `expenses_tanggal_index` (`tanggal`),
  ADD KEY `expenses_user_id_index` (`user_id`),
  ADD KEY `idx_expenses_tanggal` (`tanggal`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `jatuh_tempos`
--
ALTER TABLE `jatuh_tempos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jatuh_tempos_tanggal_jatuh_tempo_index` (`tanggal_jatuh_tempo`),
  ADD KEY `jatuh_tempos_status_approval_index` (`status_approval`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `otp_codes`
--
ALTER TABLE `otp_codes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `otp_codes_email_otp_code_index` (`email`,`otp_code`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `pengirim`
--
ALTER TABLE `pengirim`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pos`
--
ALTER TABLE `pos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pos_produk_id_foreign` (`produk_id`),
  ADD KEY `pos_tanggal_po_index` (`tanggal_po`),
  ADD KEY `pos_customer_index` (`customer`),
  ADD KEY `pos_no_invoice_index` (`no_invoice`);

--
-- Indexes for table `po_items`
--
ALTER TABLE `po_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `po_items_po_id_index` (`po_id`),
  ADD KEY `po_items_produk_id_index` (`produk_id`);

--
-- Indexes for table `produks`
--
ALTER TABLE `produks`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `produks_kode_produk_unique` (`kode_produk`);

--
-- Indexes for table `salaries`
--
ALTER TABLE `salaries`
  ADD PRIMARY KEY (`id`),
  ADD KEY `salaries_employee_id_bulan_tahun_index` (`bulan`,`tahun`),
  ADD KEY `salaries_bulan_tahun_index` (`bulan`,`tahun`),
  ADD KEY `idx_salaries_tahun_bulan` (`tahun`,`bulan`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `settings_key_unique` (`key`);

--
-- Indexes for table `sisa_po_items`
--
ALTER TABLE `sisa_po_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sisa_po_items_no_po_status_index` (`no_po`,`status`),
  ADD KEY `sisa_po_items_produk_id_status_index` (`produk_id`,`status`),
  ADD KEY `sisa_po_items_tanggal_po_index` (`tanggal_po`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `annual_summaries`
--
ALTER TABLE `annual_summaries`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `barang_keluars`
--
ALTER TABLE `barang_keluars`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=201;

--
-- AUTO_INCREMENT for table `barang_masuks`
--
ALTER TABLE `barang_masuks`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;

--
-- AUTO_INCREMENT for table `employees`
--
ALTER TABLE `employees`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `excel_sheet_edits`
--
ALTER TABLE `excel_sheet_edits`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `expenses`
--
ALTER TABLE `expenses`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jatuh_tempos`
--
ALTER TABLE `jatuh_tempos`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=133;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=96;

--
-- AUTO_INCREMENT for table `otp_codes`
--
ALTER TABLE `otp_codes`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `pengirim`
--
ALTER TABLE `pengirim`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `pos`
--
ALTER TABLE `pos`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=428;

--
-- AUTO_INCREMENT for table `po_items`
--
ALTER TABLE `po_items`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=214;

--
-- AUTO_INCREMENT for table `produks`
--
ALTER TABLE `produks`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `salaries`
--
ALTER TABLE `salaries`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `sisa_po_items`
--
ALTER TABLE `sisa_po_items`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `barang_keluars`
--
ALTER TABLE `barang_keluars`
  ADD CONSTRAINT `barang_keluars_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `barang_masuks`
--
ALTER TABLE `barang_masuks`
  ADD CONSTRAINT `barang_masuks_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `pos`
--
ALTER TABLE `pos`
  ADD CONSTRAINT `pos_produk_id_foreign` FOREIGN KEY (`produk_id`) REFERENCES `produks` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `po_items`
--
ALTER TABLE `po_items`
  ADD CONSTRAINT `po_items_po_id_foreign` FOREIGN KEY (`po_id`) REFERENCES `pos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `po_items_produk_id_foreign` FOREIGN KEY (`produk_id`) REFERENCES `produks` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `sisa_po_items`
--
ALTER TABLE `sisa_po_items`
  ADD CONSTRAINT `sisa_po_items_produk_id_foreign` FOREIGN KEY (`produk_id`) REFERENCES `produks` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
