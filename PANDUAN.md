# Cara Apply Update — Puskesmas Sancta Vita

## File yang Perlu Diganti / Ditambahkan

Semua file di ZIP ini tinggal dicopy ke project `puskesmass` kamu.

```
update_puskesmas/
├── .env                                          → ganti puskesmass/.env
├── routes/
│   └── web.php                                   → ganti puskesmass/routes/web.php
├── app/Http/Controllers/Auth/
│   └── LoginController.php                       → ganti file yang sama
├── resources/views/
│   ├── landing/
│   │   └── index.blade.php                       → BUAT FOLDER BARU & COPY
│   ├── auth/
│   │   └── login.blade.php                       → ganti file yang sama
│   └── layouts/
│       └── app.blade.php                         → ganti file yang sama
```

---

## Perubahan yang Dibuat

### 1. Alur Baru (Sesuai Permintaan)
```
Scan QR → localhost:8000/
              ↓
      [Halaman Pilihan]
       ↙           ↘
  Pasien           Admin
  /daftar          /login
     ↓                ↓
  Form daftar    Dashboard Admin
```

### 2. QR Code
QR Code diarahkan ke: `http://localhost:8000/`
Generate QR Code dari URL tersebut di: https://qr.io atau https://qrcodemonkey.com

### 3. Lemot → Kencang
- Layout sekarang pakai **CDN Bootstrap langsung** → tidak perlu `npm run build`
- Hapus dependency Vite yang tidak perlu untuk development
- Session driver tetap `file` (lebih ringan dari database)

---

## Langkah Apply

### 1. Copy file .env
```
Ganti isi puskesmass/.env dengan file .env dari ZIP ini
```

### 2. Copy semua views
```
update_puskesmas/resources/views/ → puskesmass/resources/views/
```

### 3. Copy controllers dan routes
```
update_puskesmas/routes/web.php → puskesmass/routes/web.php
update_puskesmas/app/Http/Controllers/Auth/LoginController.php → puskesmass/app/...
```

### 4. Clear cache (wajib!)
```bash
php artisan optimize:clear
```

### 5. Jalankan lagi
```bash
php artisan serve
```

Buka `http://localhost:8000` → muncul halaman pilihan Admin / Pasien.

---

## Login Admin
```
Email    : admin@sanctavita.com
Password : password
```

## URL Penting
| Halaman        | URL                              |
|----------------|----------------------------------|
| Landing / QR   | http://localhost:8000/           |
| Daftar Pasien  | http://localhost:8000/daftar     |
| Login Admin    | http://localhost:8000/login      |
| Dashboard Admin| http://localhost:8000/admin/dashboard |
| Display TV     | http://localhost:8000/display    |
