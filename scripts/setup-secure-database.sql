-- ========================================
-- SETUP SECURE DATABASE USER
-- PT. CAM JAYA ABADI - Manajemen Perusahaan
-- ========================================

-- 1. Set password untuk root user (GANTI PASSWORD_DISINI dengan password kuat)
ALTER USER 'root'@'localhost' IDENTIFIED BY 'Root_P@ssw0rd_2025!';

-- 2. Buat user database khusus untuk aplikasi (JANGAN gunakan root)
CREATE USER IF NOT EXISTS 'cam_user'@'localhost' IDENTIFIED BY 'CamJaya_2025_Secure!';

-- 3. Berikan privileges hanya untuk database manajemen_perusahaan
GRANT ALL PRIVILEGES ON manajemen_perusahaan.* TO 'cam_user'@'localhost';

-- 4. Flush privileges
FLUSH PRIVILEGES;

-- 5. Verifikasi user yang dibuat
SELECT User, Host FROM mysql.user WHERE User IN ('root', 'cam_user');

-- ========================================
-- CATATAN PENTING:
-- ========================================
-- 1. GANTI password di atas dengan password yang lebih kuat
-- 2. Password harus minimal 16 karakter
-- 3. Kombinasi huruf besar, kecil, angka, dan simbol
-- 4. Jangan gunakan password yang mudah ditebak
-- 5. Simpan password di tempat yang aman (password manager)
--
-- Contoh password kuat:
-- - CamJaya@2025#Secure!Prod
-- - P@ssw0rd_Manajemen_2025!
-- - Secure#Database$2025!
-- ========================================

-- Setelah menjalankan script ini, update file .env dengan:
-- DB_USERNAME=cam_user
-- DB_PASSWORD=CamJaya_2025_Secure!
