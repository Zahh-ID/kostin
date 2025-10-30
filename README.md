# KostIn Backend API

KostIn adalah backend Laravel 11 untuk marketplace kos dengan fitur pengelolaan properti, kontrak, invoice ber-QRIS (Midtrans), audit log, scheduler, email, dan dokumentasi Swagger.

## Teknologi
- PHP 8.2 + Laravel 11
- MySQL 8 (InnoDB, utf8mb4_unicode_ci)
- L5-Swagger untuk dokumentasi API di `/api/docs`
- Guzzle HTTP Client & Midtrans Core API v2 (QRIS)
- Queue driver database, scheduler Laravel, email mailer SMTP/log
- CI/CD GitHub Actions + deploy via SSH/rsync

## Persiapan Lokal
1. **MySQL 8**
   - Buat database `kostin` dengan charset `utf8mb4` dan collation `utf8mb4_unicode_ci`:
     ```sql
     CREATE DATABASE kostin CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
     ```
2. **Instalasi**
   ```bash
   composer install
   cp .env.example .env
   php artisan key:generate
   php artisan migrate --seed
   npm install
   npm run build
   php artisan serve
   ```
3. **Konfigurasi**
   - Set kredensial database di `.env`
   - Jalankan queue worker: `php artisan queue:work`
   - Scheduler (dev): `php artisan schedule:work`

## Dokumentasi API
- Generate dokumen: `php artisan l5-swagger:generate`
- Akses UI Swagger: [http://localhost:8000/api/docs](http://localhost:8000/api/docs)
- Versi schema: `SWAGGER_VERSION` pada `.env`

## Akun Demo
| Role  | Email                | Password |
|-------|----------------------|----------|
| Admin | `admin@example.com`  | `password` |
| Owner | `owner@example.com`  | `password` |
| Tenant| `tenant@example.com` | `password` |

Seeder membuat contoh properti, tipe kamar, dua kamar (101, 102), kontrak aktif, dan invoice bulan berjalan.

## Midtrans
- Konfigurasi `.env`:
  ```env
  MIDTRANS_IS_PRODUCTION=false
  MIDTRANS_SERVER_KEY=SB-Mid-server-xxxxxxxx
  MIDTRANS_CLIENT_KEY=SB-Mid-client-xxxxxxxx
  ```
- Endpoint notifikasi: `POST /api/v1/payments/midtrans/webhook`
- Payload QRIS tersimpan di kolom `invoices.qris_payload`

## Email
- Mailable `InvoiceGeneratedMail` dikirim saat command `invoices:generate`
- Atur SMTP lokal (mis. Mailhog di `localhost:1025`) sesuai `.env`

## Scheduler & Queue
- Command `invoices:generate` dijadwalkan harian (`app/Console/Kernel.php`)
- Contoh cron di server:
  ```cron
  * * * * * cd /var/www/kostin && php artisan schedule:run >> /dev/null 2>&1
  ```
- Supervisor queue worker: lihat `deploy/supervisor/kostin-queue.conf`

## CI GitHub Actions
- Workflow: `.github/workflows/ci.yml`
- Service MySQL 8 dengan kredensial `kostin/secret`
- Menggunakan file `.env.ci` (disalin ke `.env` saat build)
- Langkah: composer install → key generate → migrate → `php artisan test`

## Deploy GitHub Actions
- Workflow: `.github/workflows/deploy.yml`
- Secrets yang diperlukan:
  - `SSH_HOST`, `SSH_USER`, `SSH_PORT`, `SSH_KEY`
  - `DEPLOY_PATH` (mis. `/var/www/kostin`)
- Deploy via rsync, jalankan `scripts/post-deploy.sh` di server:
  ```bash
  #!/usr/bin/env bash
  set -e
  cd $DEPLOY_PATH
  php artisan down || true
  composer install --no-dev --prefer-dist --optimize-autoloader
  php artisan migrate --force
  php artisan optimize
  php artisan queue:restart
  php artisan l5-swagger:generate
  php artisan up
  ```
- Pastikan `scripts/post-deploy.sh` bernilai executable (`chmod +x`).

## Konfigurasi Server
- **Nginx**: `deploy/nginx/kostin.conf` (root `public`, PHP-FPM 8.2)
- **Supervisor**: `deploy/supervisor/kostin-queue.conf`
- **Cron**: lihat bagian scheduler

## Testing
- Unit relasi model & endpoint `GET /api/v1/properties`
- Jalankan: `php artisan test`

Selamat berkontribusi! Dokumentasi tambahan tersedia di Swagger dan komentar kode.
# kostin
