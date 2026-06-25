# Puskesmas Sancta Vita — Setup Guide

## Struktur File yang Disediakan

```
puskesmas/
├── database/migrations/          ← Copy ke database/migrations/
├── app/Models/                   ← Copy ke app/Models/
├── app/Http/Controllers/         ← Copy ke app/Http/Controllers/
├── app/Http/Middleware/          ← Copy ke app/Http/Middleware/
├── app/Events/                   ← Copy ke app/Events/
├── routes/web.php                ← Replace routes/web.php
├── resources/views/              ← Copy ke resources/views/
├── .env.example                  ← Referensi konfigurasi
└── SETUP.md                      ← File ini
```

---

## Langkah Setup

### 1. Buat project Laravel baru
```bash
composer create-project laravel/laravel puskesmas
cd puskesmas
```

### 2. Install dependencies tambahan
```bash
# Barcode generator
composer require milon/barcode

# Laravel Echo + Pusher (untuk Soketi)
composer require pusher/pusher-php-server

# NPM packages
npm install laravel-echo pusher-js
```

### 3. Copy semua file dari folder ini ke project Laravel

### 4. Setup .env
```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env`:
```
DB_DATABASE=puskesmas
DB_USERNAME=root
DB_PASSWORD=your_password
```

### 5. Buat database & jalankan migration
```bash
mysql -u root -p -e "CREATE DATABASE puskesmas;"
php artisan migrate
```
Migration terakhir sudah include seed data awal (admin + poli + dokter).

### 6. Setup Soketi (realtime)
```bash
# Install Soketi global
npm install -g @soketi/soketi

# Buat file soketi.json
cat > soketi.json << 'EOF'
{
  "debug": false,
  "host": "0.0.0.0",
  "port": 6001,
  "appManager.driver": "array",
  "appManager.array.apps": [
    {
      "id": "puskesmas-app",
      "key": "puskesmas-key",
      "secret": "puskesmas-secret",
      "webhooks": []
    }
  ]
}
EOF

# Jalankan Soketi
soketi start --config=soketi.json
```

### 7. Setup Laravel Echo di resources/js/bootstrap.js
Tambahkan di bawah import axios:
```js
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    wsHost: import.meta.env.VITE_PUSHER_HOST,
    wsPort: import.meta.env.VITE_PUSHER_PORT,
    wssPort: import.meta.env.VITE_PUSHER_PORT,
    forceTLS: false,
    enabledTransports: ['ws', 'wss'],
    disableStats: true,
});
```

### 8. Update config/broadcasting.php
```php
'pusher' => [
    'driver' => 'pusher',
    'key'    => env('PUSHER_APP_KEY'),
    'secret' => env('PUSHER_APP_SECRET'),
    'app_id' => env('PUSHER_APP_ID'),
    'options' => [
        'cluster' => env('PUSHER_APP_CLUSTER'),
        'host'    => env('PUSHER_HOST', '127.0.0.1'),
        'port'    => env('PUSHER_PORT', 6001),
        'scheme'  => env('PUSHER_SCHEME', 'http'),
        'useTLS'  => false,
    ],
],
```

### 9. Tambahkan middleware di bootstrap/app.php (Laravel 12)
```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'admin' => \App\Http\Middleware\AdminMiddleware::class,
    ]);
})
```

### 10. Build assets & jalankan server
```bash
npm run build
php artisan serve
```

---

## Login Admin

| Field    | Value                  |
|----------|------------------------|
| Email    | admin@sanctavita.com   |
| Password | password               |

---

## URL Penting

| Halaman             | URL                        |
|---------------------|----------------------------|
| Daftar Pasien       | http://localhost:8000/      |
| Dashboard Admin     | http://localhost:8000/admin/dashboard |
| Scan Barcode        | http://localhost:8000/admin/antrian/scan |
| Display TV          | http://localhost:8000/display |
| Login Admin         | http://localhost:8000/login |

---

## Flow Penggunaan

```
Pasien buka /
  → Isi form (nama, usia, keluhan, pilih poli, dokter)
  → Dapat tiket + barcode

Petugas buka /admin/antrian/scan
  → Scan barcode pasien
  → Klik "Panggil" → Display TV update otomatis

Display /display
  → Terpasang di TV lobby
  → Update realtime via Soketi
```
