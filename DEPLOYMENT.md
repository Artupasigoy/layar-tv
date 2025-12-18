# 📺 Panduan Deploy Layar TV - Digital Signage

Panduan lengkap untuk deploy aplikasi Layar TV di **Server Lokal** (Windows/Linux) dan **VPS** (Ubuntu/Debian).

---

## 📋 Daftar Isi

1. [Prasyarat & Kebutuhan Sistem](#-prasyarat--kebutuhan-sistem)
2. [Deploy di Server Lokal Windows](#-deploy-di-server-lokal-windows)
3. [Deploy di VPS Ubuntu/Debian](#-deploy-di-vps-ubuntudebian)
4. [Konfigurasi Nginx (Production)](#-konfigurasi-nginx-production)
5. [Setup Supervisor untuk Queue Worker](#-setup-supervisor-untuk-queue-worker)
6. [SSL/HTTPS dengan Let's Encrypt](#-sslhttps-dengan-lets-encrypt)
7. [Troubleshooting](#-troubleshooting)

---

## 🔧 Prasyarat & Kebutuhan Sistem

### Minimum Requirements
| Komponen | Spesifikasi |
|----------|-------------|
| RAM | 1 GB (2 GB recommended) |
| Storage | 10 GB (tergantung jumlah media) |
| PHP | 8.2 atau lebih baru |
| Database | MySQL 8.0 / MariaDB 10.6+ / SQLite |
| FFmpeg | Diperlukan untuk encode video |

### Software yang Dibutuhkan
- **PHP 8.2+** dengan extensions: `gd`, `mbstring`, `openssl`, `pdo`, `tokenizer`, `xml`, `ctype`, `json`, `bcmath`, `fileinfo`
- **Composer** (PHP package manager)
- **Node.js 18+** dan **npm** (untuk build assets)
- **FFmpeg** (untuk processing video)
- **MySQL/MariaDB** atau **SQLite** (database)
- **Git** (untuk clone repository)

---

## 🖥️ Deploy di Server Lokal Windows

### Langkah 1: Install Laragon (Cara Paling Mudah)

1. Download **Laragon Full** dari: https://laragon.org/download/
2. Install Laragon (default setting)
3. Laragon sudah include: PHP, MySQL, Apache/Nginx, Git, Node.js

### Langkah 2: Install FFmpeg

1. Download FFmpeg dari: https://www.gyan.dev/ffmpeg/builds/
2. Pilih **ffmpeg-release-essentials.zip**
3. Extract ke folder, contoh: `C:\laragon\bin\ffmpeg`
4. Tambahkan ke PATH:
   - Buka **System Properties** → **Environment Variables**
   - Edit **Path** → Add `C:\laragon\bin\ffmpeg\bin`
5. Test: buka CMD baru, ketik `ffmpeg -version`

### Langkah 3: Clone & Setup Project

```powershell
# Buka terminal di folder www Laragon
cd C:\laragon\www

# Clone repository
git clone https://github.com/Artupasigoy/layar-tv.git
cd layar-tv

# Install PHP dependencies
composer install --optimize-autoloader

# Install Node dependencies & build assets
npm install
npm run build

# Copy environment file
copy .env.example .env

# Generate application key
php artisan key:generate
```

### Langkah 4: Konfigurasi Database

Edit file `.env`:

```env
# Untuk MySQL (recommended)
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=layar_tv
DB_USERNAME=root
DB_PASSWORD=

# ATAU untuk SQLite (lebih simple)
DB_CONNECTION=sqlite
# Hapus/comment DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD
```

Jika pakai SQLite, buat file database:
```powershell
New-Item database\database.sqlite -ItemType File
```

Jika pakai MySQL, buat database dulu di phpMyAdmin atau terminal:
```sql
CREATE DATABASE layar_tv CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### Langkah 5: Migrasi Database & Setup Awal

```powershell
# Jalankan migrasi database
php artisan migrate

# Buat symbolic link untuk storage
php artisan storage:link

# Bersihkan cache
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

### Langkah 6: Buat User Admin

```powershell
php artisan tinker
```

Di dalam Tinker, jalankan:
```php
\App\Models\User::create([
    'name' => 'Admin',
    'email' => 'admin@example.com',
    'password' => bcrypt('password123'),
    'email_verified_at' => now(),
]);
exit
```

### Langkah 7: Jalankan Aplikasi

**Terminal 1 - Web Server:**
```powershell
php artisan serve --host=0.0.0.0 --port=8000
```

**Terminal 2 - Queue Worker (WAJIB untuk processing media):**
```powershell
# Pastikan PATH FFmpeg sudah benar
$env:Path += ';C:\laragon\bin\ffmpeg\bin'
php artisan queue:work --tries=3 --timeout=300
```

### Langkah 8: Akses Aplikasi

- **Admin Panel:** http://localhost:8000/dashboard
- **Signage Display:** http://localhost:8000/display
- **Dari perangkat lain di jaringan yang sama:** http://[IP-KOMPUTER]:8000/display

Untuk mengetahui IP komputer:
```powershell
ipconfig
# Cari IPv4 Address, contoh: 192.168.1.100
```

---

## 🐧 Deploy di VPS Ubuntu/Debian

### Langkah 1: Update Sistem & Install Dependencies

```bash
# Login ke VPS via SSH
ssh root@your-vps-ip

# Update sistem
sudo apt update && sudo apt upgrade -y

# Install dependencies dasar
sudo apt install -y curl git unzip software-properties-common
```

### Langkah 2: Install PHP 8.2

```bash
# Tambah repository PHP
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update

# Install PHP dan extensions yang diperlukan
sudo apt install -y php8.2 php8.2-fpm php8.2-cli php8.2-common \
    php8.2-mysql php8.2-sqlite3 php8.2-mbstring php8.2-xml \
    php8.2-curl php8.2-gd php8.2-bcmath php8.2-zip php8.2-tokenizer \
    php8.2-fileinfo php8.2-intl

# Verifikasi instalasi
php -v
```

### Langkah 3: Install Composer

```bash
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
composer --version
```

### Langkah 4: Install Node.js 18+

```bash
# Install Node.js via NodeSource
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt install -y nodejs

# Verifikasi
node -v
npm -v
```

### Langkah 5: Install FFmpeg

```bash
sudo apt install -y ffmpeg

# Verifikasi
ffmpeg -version
```

### Langkah 6: Install MySQL/MariaDB

```bash
# Install MariaDB
sudo apt install -y mariadb-server mariadb-client

# Amankan instalasi MySQL
sudo mysql_secure_installation
# Ikuti instruksi: set root password, remove anonymous users, dll

# Login ke MySQL dan buat database
sudo mysql -u root -p
```

Di dalam MySQL:
```sql
CREATE DATABASE layar_tv CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'layartv'@'localhost' IDENTIFIED BY 'password_aman_kamu';
GRANT ALL PRIVILEGES ON layar_tv.* TO 'layartv'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### Langkah 7: Install Nginx

```bash
sudo apt install -y nginx
sudo systemctl enable nginx
sudo systemctl start nginx
```

### Langkah 8: Clone & Setup Project

```bash
# Buat folder untuk aplikasi
sudo mkdir -p /var/www/layar-tv
sudo chown -R $USER:$USER /var/www/layar-tv

# Clone repository
cd /var/www
git clone https://github.com/Artupasigoy/layar-tv.git layar-tv
cd layar-tv

# Install PHP dependencies
composer install --optimize-autoloader --no-dev

# Install Node dependencies & build assets
npm install
npm run build

# Copy dan edit environment file
cp .env.example .env
nano .env
```

### Langkah 9: Edit File .env

```env
APP_NAME="Layar TV"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=http://your-domain.com

LOG_CHANNEL=stack
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=layar_tv
DB_USERNAME=layartv
DB_PASSWORD=password_aman_kamu

SESSION_DRIVER=database
QUEUE_CONNECTION=database

# Jika pakai Redis (optional, lebih cepat)
# SESSION_DRIVER=redis
# QUEUE_CONNECTION=redis
```

Simpan file: `Ctrl+X`, lalu `Y`, lalu `Enter`

### Langkah 10: Finalisasi Setup Laravel

```bash
# Generate application key
php artisan key:generate

# Jalankan migrasi
php artisan migrate --force

# Buat storage link
php artisan storage:link

# Optimasi untuk production
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Set permissions yang benar
sudo chown -R www-data:www-data /var/www/layar-tv
sudo chmod -R 755 /var/www/layar-tv
sudo chmod -R 775 /var/www/layar-tv/storage
sudo chmod -R 775 /var/www/layar-tv/bootstrap/cache
```

### Langkah 11: Buat User Admin

```bash
php artisan tinker
```

```php
\App\Models\User::create([
    'name' => 'Admin',
    'email' => 'admin@example.com',
    'password' => bcrypt('password_aman_kamu'),
    'email_verified_at' => now(),
]);
exit
```

---

## 🌐 Konfigurasi Nginx (Production)

### Buat Config Nginx

```bash
sudo nano /etc/nginx/sites-available/layar-tv
```

Paste konfigurasi berikut:

```nginx
server {
    listen 80;
    listen [::]:80;
    
    server_name your-domain.com www.your-domain.com;
    # Atau gunakan IP: server_name 123.45.67.89;
    
    root /var/www/layar-tv/public;
    index index.php;

    # Logging
    access_log /var/log/nginx/layar-tv-access.log;
    error_log /var/log/nginx/layar-tv-error.log;

    # Max upload size (sesuaikan dengan kebutuhan)
    client_max_body_size 500M;

    # Gzip compression
    gzip on;
    gzip_types text/plain text/css application/json application/javascript text/xml application/xml;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    # PHP-FPM
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        
        # Timeout untuk processing video besar
        fastcgi_read_timeout 300;
    }

    # Cache static assets
    location ~* \.(jpg|jpeg|png|gif|ico|css|js|woff|woff2|ttf|svg|webp|mp4|webm)$ {
        expires 30d;
        add_header Cache-Control "public, immutable";
    }

    # Deny access to sensitive files
    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

### Aktifkan Site & Restart Nginx

```bash
# Buat symbolic link
sudo ln -s /etc/nginx/sites-available/layar-tv /etc/nginx/sites-enabled/

# Hapus default site (optional)
sudo rm /etc/nginx/sites-enabled/default

# Test konfigurasi
sudo nginx -t

# Restart Nginx
sudo systemctl restart nginx
```

### Konfigurasi PHP-FPM untuk Upload Besar

```bash
sudo nano /etc/php/8.2/fpm/php.ini
```

Cari dan ubah nilai berikut:
```ini
upload_max_filesize = 500M
post_max_size = 500M
max_execution_time = 300
memory_limit = 256M
```

Restart PHP-FPM:
```bash
sudo systemctl restart php8.2-fpm
```

---

## 👷 Setup Supervisor untuk Queue Worker

Queue Worker WAJIB berjalan terus agar processing media berfungsi!

### Install Supervisor

```bash
sudo apt install -y supervisor
```

### Buat Config Supervisor

```bash
sudo nano /etc/supervisor/conf.d/layar-tv-worker.conf
```

Paste konfigurasi:

```ini
[program:layar-tv-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/layar-tv/artisan queue:work --sleep=3 --tries=3 --timeout=300 --max-jobs=100 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/layar-tv/storage/logs/worker.log
stopwaitsecs=3600
```

### Aktifkan Supervisor

```bash
# Reload konfigurasi
sudo supervisorctl reread
sudo supervisorctl update

# Start worker
sudo supervisorctl start layar-tv-worker:*

# Cek status
sudo supervisorctl status
```

### Perintah Maintenance Supervisor

```bash
# Restart worker (setelah update code)
sudo supervisorctl restart layar-tv-worker:*

# Stop worker
sudo supervisorctl stop layar-tv-worker:*

# Lihat log
tail -f /var/www/layar-tv/storage/logs/worker.log
```

---

## 🔒 SSL/HTTPS dengan Let's Encrypt

### Install Certbot

```bash
sudo apt install -y certbot python3-certbot-nginx
```

### Generate SSL Certificate

```bash
sudo certbot --nginx -d your-domain.com -d www.your-domain.com
```

Ikuti instruksi:
1. Masukkan email untuk notifikasi
2. Agree to Terms of Service
3. Pilih redirect HTTP ke HTTPS (recommended)

### Auto-Renewal

Certbot otomatis menambahkan cron job. Untuk test:
```bash
sudo certbot renew --dry-run
```

### Update APP_URL di .env

```bash
nano /var/www/layar-tv/.env
```

Ubah:
```env
APP_URL=https://your-domain.com
```

Clear cache:
```bash
php artisan config:cache
```

---

## 🔄 Update Aplikasi (Setelah Ada Perubahan Code)

### Di Server Lokal Windows

```powershell
cd C:\laragon\www\layar-tv

# Pull perubahan terbaru
git pull origin main

# Update dependencies
composer install --optimize-autoloader
npm install
npm run build

# Jalankan migrasi (jika ada)
php artisan migrate

# Clear cache
php artisan config:clear
php artisan cache:clear
php artisan view:clear

# Restart queue worker (jika running)
# Ctrl+C di terminal queue worker, lalu jalankan ulang
```

### Di VPS

```bash
cd /var/www/layar-tv

# Pull perubahan terbaru
git pull origin main

# Update dependencies
composer install --optimize-autoloader --no-dev
npm install
npm run build

# Jalankan migrasi
php artisan migrate --force

# Optimasi ulang
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Restart queue worker
sudo supervisorctl restart layar-tv-worker:*
```

---

## 🛠️ Troubleshooting

### ❌ Error: "Permission denied" di storage

```bash
sudo chown -R www-data:www-data /var/www/layar-tv/storage
sudo chmod -R 775 /var/www/layar-tv/storage
```

### ❌ Error: Video tidak terproses

1. Cek FFmpeg terinstall:
   ```bash
   ffmpeg -version
   ```
2. Cek queue worker berjalan:
   ```bash
   sudo supervisorctl status
   ```
3. Cek log error:
   ```bash
   tail -f /var/www/layar-tv/storage/logs/laravel.log
   tail -f /var/www/layar-tv/storage/logs/worker.log
   ```

### ❌ Error: "413 Request Entity Too Large"

Edit Nginx config, tambah/ubah:
```nginx
client_max_body_size 500M;
```

Lalu restart: `sudo systemctl restart nginx`

### ❌ Error: "504 Gateway Timeout"

Edit Nginx config, tambah di blok `location ~ \.php$`:
```nginx
fastcgi_read_timeout 300;
```

Lalu restart: `sudo systemctl restart nginx`

### ❌ Halaman blank / 500 error

```bash
# Cek log
tail -100 /var/www/layar-tv/storage/logs/laravel.log

# Cek permissions
sudo chown -R www-data:www-data /var/www/layar-tv
sudo chmod -R 755 /var/www/layar-tv
sudo chmod -R 775 /var/www/layar-tv/storage
sudo chmod -R 775 /var/www/layar-tv/bootstrap/cache

# Clear all cache
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
```

### ❌ Signage tidak update setelah upload media

1. Pastikan queue worker berjalan
2. Cek status processing di admin panel

### ❌ Logo/gambar tidak muncul

```bash
# Cek storage link
ls -la /var/www/layar-tv/public/storage

# Jika tidak ada, buat ulang
php artisan storage:link
```

---

## 📌 Checklist Deploy

### Server Lokal Windows
- [ ] Laragon terinstall
- [ ] FFmpeg terinstall dan ada di PATH
- [ ] Project di-clone
- [ ] `composer install` sukses
- [ ] `npm install && npm run build` sukses
- [ ] `.env` dikonfigurasi
- [ ] Database dibuat
- [ ] `php artisan migrate` sukses
- [ ] `php artisan storage:link` sukses
- [ ] User admin dibuat
- [ ] Web server berjalan
- [ ] Queue worker berjalan
- [ ] Bisa login ke admin
- [ ] Bisa upload dan processing media
- [ ] Signage display berfungsi

### VPS Production
- [ ] PHP 8.2 terinstall dengan semua extensions
- [ ] Composer terinstall
- [ ] Node.js 18+ terinstall
- [ ] FFmpeg terinstall
- [ ] MySQL/MariaDB terinstall
- [ ] Nginx terinstall
- [ ] Project di-clone ke `/var/www/layar-tv`
- [ ] Dependencies terinstall
- [ ] Assets ter-build
- [ ] `.env` dikonfigurasi untuk production
- [ ] Database dibuat dan dikonfigurasi
- [ ] Migrasi sukses
- [ ] Storage link dibuat
- [ ] Permissions benar (www-data)
- [ ] Nginx dikonfigurasi
- [ ] PHP-FPM dikonfigurasi untuk upload besar
- [ ] Supervisor dikonfigurasi untuk queue worker
- [ ] SSL/HTTPS dikonfigurasi (jika pakai domain)
- [ ] User admin dibuat
- [ ] Semua fitur berfungsi

---

## 💡 Tips Production

1. **Gunakan `APP_DEBUG=false`** di production untuk keamanan
2. **Backup database secara rutin** dengan cron job
3. **Monitor disk space** karena media bisa memakan banyak storage
4. **Gunakan CDN** jika banyak perangkat mengakses signage
5. **Setup monitoring** dengan tools seperti Laravel Telescope atau Sentry

---

**Selamat! Aplikasi Layar TV siap digunakan! 🎉**

Jika ada pertanyaan atau masalah, cek bagian [Troubleshooting](#-troubleshooting) atau buka issue di GitHub.
