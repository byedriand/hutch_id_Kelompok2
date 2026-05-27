# 🚀 Panduan Deployment - HutchID Backend ke Server Production

## Prasyarat
- VPS/Hosting dengan PHP 8.1+, Composer, MySQL, Nginx/Apache
- Domain yang sudah diarahkan ke server (contoh: `api.hutchprestige.com`)

---

## Langkah-Langkah Deployment

### 1. Upload Kode ke Server
```bash
# Clone dari GitHub ke server
git clone https://github.com/byedriand/hutch_id_Kelompok2.git /var/www/hutchid
cd /var/www/hutchid/hutch_id_Website_OrderFlow
```

### 2. Install Dependencies
```bash
composer install --no-dev --optimize-autoloader
```

### 3. Konfigurasi Environment
```bash
# Copy template production
cp .env.production .env

# Generate app key
php artisan key:generate

# Edit password database & URL sesuai server Anda
nano .env
```

### 4. Setup Database
```bash
# Buat database MySQL
mysql -u root -p -e "CREATE DATABASE hutch_id_prod;"

# Jalankan migrasi dan seeder
php artisan migrate:fresh --seed --force
```

### 5. Optimasi untuk Production
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan storage:link
```

### 6. Set Permissions
```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data /var/www/hutchid
```

### 7. Update URL di Flutter App
Buka file `lib/config/app_config.dart` dan ubah:
```dart
static const Env _env = Env.production; // ← ganti dari development
```
Ubah `baseUrl` production menjadi URL server Anda:
```dart
case Env.production:
  return 'https://api.hutchprestige.com/api'; // ← ganti domain Anda
```

---

## Verifikasi Deployment
```bash
# Test endpoint login
curl -X POST https://api.hutchprestige.com/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@hutchprestige.com","password":"password123"}'
```

Respon yang diharapkan:
```json
{"token": "...", "user": {"nama": "Administrator", "role": "Administrator"}}
```

---

## Nginx Config (Contoh)
```nginx
server {
    listen 80;
    server_name api.hutchprestige.com;
    root /var/www/hutchid/hutch_id_Website_OrderFlow/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;
    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```
