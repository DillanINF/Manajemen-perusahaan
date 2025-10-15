  # 🚀 CHECKLIST DEPLOYMENT & KEAMANAN
## Aplikasi Manajemen Perusahaan - PT. CAM JAYA ABADI

---

## ⚠️ WAJIB DILAKUKAN SEBELUM HOSTING

### 1. **GENERATE APP_KEY BARU** ⚠️ KRITIS
```bash
php artisan key:generate
```
**Alasan:** APP_KEY saat ini sangat lemah dan berbahaya untuk production.

---

### 2. **KONFIGURASI .env PRODUCTION**

Buat file `.env.production` atau edit `.env` dengan konfigurasi berikut:

```env
# === APLIKASI ===
APP_NAME="Manajemen Perusahaan"
APP_ENV=production
APP_KEY=base64:XXXXX  # Generate dengan: php artisan key:generate
APP_DEBUG=false       # ⚠️ WAJIB false di production
APP_URL=https://yourdomain.com

# === DATABASE ===
DB_CONNECTION=mysql
DB_HOST=127.0.0.1     # atau IP database server
DB_PORT=3306
DB_DATABASE=manajemen_perusahaan
DB_USERNAME=db_user   # ⚠️ JANGAN gunakan root
DB_PASSWORD=STRONG_PASSWORD_HERE  # ⚠️ WAJIB password kuat

# === SESSION ===
SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=true  # ⚠️ Ubah ke true untuk keamanan
SESSION_PATH=/
SESSION_DOMAIN=yourdomain.com
SESSION_SECURE_COOKIE=true  # ⚠️ Tambahkan ini untuk HTTPS

# === CACHE ===
CACHE_STORE=database
CACHE_PREFIX=cam_

# === QUEUE ===
QUEUE_CONNECTION=database

# === MAIL (untuk OTP & notifikasi) ===
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com  # sesuaikan provider
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="${APP_NAME}"

# === LOGGING ===
LOG_CHANNEL=daily
LOG_LEVEL=error  # production: error, development: debug

# === SECURITY ===
BCRYPT_ROUNDS=12
```

---

### 3. **DATABASE SECURITY** ⚠️ KRITIS

#### A. Buat User Database Khusus (JANGAN gunakan root)
```sql
-- Login ke MySQL sebagai root
CREATE USER 'cam_user'@'localhost' IDENTIFIED BY 'PASSWORD_KUAT_DISINI';
GRANT ALL PRIVILEGES ON manajemen_perusahaan.* TO 'cam_user'@'localhost';
FLUSH PRIVILEGES;
```

#### B. Set Password Root MySQL
```sql
ALTER USER 'root'@'localhost' IDENTIFIED BY 'PASSWORD_ROOT_KUAT';
```

#### C. Backup Database Sebelum Deploy
```bash
# Backup database
mysqldump -u root -p manajemen_perusahaan > backup_$(date +%Y%m%d).sql

# Restore jika diperlukan
mysql -u root -p manajemen_perusahaan < backup_YYYYMMDD.sql
```

---

### 4. **FILE PERMISSIONS** ⚠️ PENTING

```bash
# Set permission untuk storage dan cache
chmod -R 775 storage
chmod -R 775 bootstrap/cache

# Set ownership (sesuaikan dengan user web server)
chown -R www-data:www-data storage
chown -R www-data:www-data bootstrap/cache

# Protect .env file
chmod 600 .env
```

---

### 5. **OPTIMASI LARAVEL**

```bash
# Clear semua cache
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Cache untuk production
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Optimize autoloader
composer install --optimize-autoloader --no-dev
```

---

### 6. **KEAMANAN WEB SERVER**

#### A. Nginx Configuration (Recommended)
```nginx
server {
    listen 80;
    server_name yourdomain.com;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    server_name yourdomain.com;
    root /path/to/login-app/public;

    # SSL Certificate
    ssl_certificate /path/to/cert.pem;
    ssl_certificate_key /path/to/key.pem;

    # Security Headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header Referrer-Policy "no-referrer-when-downgrade" always;
    add_header Content-Security-Policy "default-src 'self' http: https: data: blob: 'unsafe-inline'" always;

    index index.php;

    charset utf-8;

    # Deny access to sensitive files
    location ~ /\.(?!well-known).* {
        deny all;
    }

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    # Deny access to .env and other sensitive files
    location ~ /\.(env|git|htaccess) {
        deny all;
    }
}
```

#### B. Apache Configuration (.htaccess sudah ada di Laravel)
Pastikan mod_rewrite enabled:
```bash
sudo a2enmod rewrite
sudo systemctl restart apache2
```

---

### 7. **SSL/TLS CERTIFICATE** ⚠️ WAJIB

```bash
# Install Certbot (Let's Encrypt - GRATIS)
sudo apt install certbot python3-certbot-nginx

# Generate SSL Certificate
sudo certbot --nginx -d yourdomain.com -d www.yourdomain.com

# Auto-renewal
sudo certbot renew --dry-run
```

---

### 8. **FIREWALL & SECURITY**

```bash
# UFW Firewall (Ubuntu/Debian)
sudo ufw allow 22/tcp    # SSH
sudo ufw allow 80/tcp    # HTTP
sudo ufw allow 443/tcp   # HTTPS
sudo ufw enable

# Fail2Ban (proteksi brute force)
sudo apt install fail2ban
sudo systemctl enable fail2ban
sudo systemctl start fail2ban
```

---

### 9. **MONITORING & LOGGING**

#### A. Setup Log Rotation
```bash
# Edit /etc/logrotate.d/laravel
/path/to/login-app/storage/logs/*.log {
    daily
    missingok
    rotate 14
    compress
    delaycompress
    notifempty
    create 0640 www-data www-data
    sharedscripts
}
```

#### B. Monitor Disk Space
```bash
# Cek disk space
df -h

# Cek folder size
du -sh storage/logs/
```

---

### 10. **BACKUP STRATEGY** ⚠️ PENTING

#### A. Database Backup (Daily)
```bash
#!/bin/bash
# /usr/local/bin/backup-db.sh

DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/backup/database"
DB_NAME="manajemen_perusahaan"
DB_USER="cam_user"
DB_PASS="YOUR_PASSWORD"

mkdir -p $BACKUP_DIR
mysqldump -u $DB_USER -p$DB_PASS $DB_NAME | gzip > $BACKUP_DIR/backup_$DATE.sql.gz

# Keep only last 30 days
find $BACKUP_DIR -name "backup_*.sql.gz" -mtime +30 -delete
```

#### B. Cron Job untuk Auto Backup
```bash
# Edit crontab
crontab -e

# Tambahkan:
0 2 * * * /usr/local/bin/backup-db.sh
```

---

### 11. **UPDATE DEPENDENCIES**

```bash
# Update composer packages
composer update

# Check security vulnerabilities
composer audit

# Update specific packages
composer update laravel/framework
composer update phpoffice/phpspreadsheet
```

---

### 12. **TESTING SEBELUM GO LIVE**

- [ ] Test login/logout functionality
- [ ] Test semua CRUD operations
- [ ] Test file upload (jika ada)
- [ ] Test export Excel/PDF
- [ ] Test email OTP
- [ ] Test admin vs user permissions
- [ ] Test di berbagai browser (Chrome, Firefox, Safari, Edge)
- [ ] Test di mobile devices
- [ ] Load testing (jika traffic tinggi)

---

## 🔒 CHECKLIST KEAMANAN FINAL

- [ ] APP_DEBUG=false
- [ ] APP_KEY sudah di-generate ulang
- [ ] Database password kuat (min 16 karakter, kombinasi huruf/angka/simbol)
- [ ] User database bukan root
- [ ] SSL/TLS certificate terpasang
- [ ] HTTPS redirect aktif
- [ ] File permissions benar (775 storage, 600 .env)
- [ ] .env tidak ter-commit ke Git
- [ ] SESSION_ENCRYPT=true
- [ ] Security headers terpasang
- [ ] Firewall aktif
- [ ] Backup otomatis berjalan
- [ ] Log rotation aktif
- [ ] Fail2Ban terpasang (opsional tapi recommended)

---

## 📞 SUPPORT & MAINTENANCE

### Monitoring Checklist (Weekly)
- [ ] Cek error logs: `tail -f storage/logs/laravel.log`
- [ ] Cek disk space: `df -h`
- [ ] Cek database size: `SELECT table_schema, SUM(data_length + index_length) / 1024 / 1024 AS "Size (MB)" FROM information_schema.TABLES GROUP BY table_schema;`
- [ ] Test backup restore
- [ ] Update dependencies (monthly)

### Emergency Contacts
- Hosting Provider Support: [PHONE/EMAIL]
- Database Admin: [PHONE/EMAIL]
- Developer: [PHONE/EMAIL]

---

## 🎯 DEPLOYMENT STEPS SUMMARY

1. **Persiapan Server**
   - Install PHP 8.2+, MySQL 8.0+, Nginx/Apache
   - Install Composer
   - Setup SSL Certificate

2. **Upload Aplikasi**
   - Clone/Upload source code
   - `composer install --optimize-autoloader --no-dev`
   - Copy `.env.example` ke `.env` dan edit konfigurasi

3. **Database Setup**
   - Buat database MySQL
   - Buat user database khusus
   - Import database atau jalankan migrations
   - `php artisan migrate --force`

4. **Konfigurasi**
   - `php artisan key:generate`
   - `php artisan config:cache`
   - `php artisan route:cache`
   - `php artisan view:cache`
   - Set file permissions

5. **Testing**
   - Test semua fitur
   - Test keamanan
   - Test performance

6. **Go Live**
   - Point domain ke server
   - Monitor logs
   - Setup backup otomatis

---

**Dibuat:** 2025-01-15  
**Untuk:** PT. CAM JAYA ABADI - Sistem Manajemen Perusahaan  
**Tech Stack:** Laravel 12, MySQL 8, PHP 8.2+
