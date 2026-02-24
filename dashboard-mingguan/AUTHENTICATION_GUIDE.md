# Panduan Dashboard Mingguan

## ✅ Setup Selesai - Versi Updated (V3 - FIXED)

Sistem Dashboard Mingguan telah diimplementasikan dengan fitur lengkap:

**Fixes Applied (V3):**
- ✅ Fixed column errors: `tanggal` → `tanggal_target` (kepuasan_pengunjung)
- ✅ Fixed column errors: `minggu`, `jumlah_pengunjung` → `nama_dataset`, `capaian` (portal_sata)
- ✅ Fixed middleware resolution - Kernel.php simplified and working
- ✅ Fixed admin dashboard access after login
- ✅ All queries now use correct database column names
- ✅ All middleware aliases properly configured

### 📋 Akun Default
- **Admin Account**
  - Email: `admin@dashboard.com`
  - Password: `12345678`
  
- **User Account**
  - Email: `user@dashboard.com`
  - Password: `user123456`

---

## 🚀 Cara Mengakses

### Home Dashboard (Public - Tanpa Login)
- Buka URL: **`http://127.0.0.1:8000/`** atau **`/`**
- Dashboard user langsung ditampilkan (read-only, tanpa login)
- Bisa melihat: Statistik, Calendar, Charts, dan Schedules

### Login Admin
- Ketik di URL: **`/admin`**
- Anda akan diarahkan ke halaman login
- Masukkan email dan password admin
- Akses ke `/admin/dashboard` dan semua fitur CRUD

### Access User Terautentikasi
- Login dengan user@dashboard.com / user123456
- Akses ke `/user/dashboard` (design sama dengan admin tapi read-only)
- Bisa melihat semua data tapi tidak bisa CRUD

### Logout
- Klik nama user di kanan atas navbar
- Pilih "Logout" dari dropdown menu

---

## 🔐 Fitur Keamanan

### Middleware & Role-Based Access Control
- **Admin Routes** (`/admin/*`): Hanya user dengan role `admin`
- **User Routes** (`/user/*`): Hanya user dengan role `user`
- Jika mencoba mengakses route yang tidak diizinkan → ditolak (redirect ke home)

### Database Features
- Password di-hash dengan `bcrypt`
- Kolom `role` untuk membedakan admin dan user
- Kolom `last_login_at` untuk tracking login terakhir

---

## 📊 Perbedaan Dashboard

### Public Dashboard (`/` - Home)
✅ **Fitur:**
- Akses **tanpa login**
- Lihat statistik data
- Lihat calendar konten tematik
- Lihat charts portal SATA dan kepuasan pengunjung
- View schedules/agendas
- **Read-only** - tidak ada tombol edit/hapus

### Admin Dashboard (`/admin/dashboard`)
✅ **Fitur:**
- Login required (role: admin)
- Lihat semua data dan statistik
- **CRUD penuh** untuk:
  - Infografis
  - Kepuasan Pengunjung
  - Konten Tematik
  - Layanan Konsultasi
  - Penggunaan Data
  - Portal SATA
  - Rekomendasi Statistik
  - Daftar Data

### User Dashboard (`/user/dashboard`)
✅ **Fitur:**
- Login required (role: user)
- Lihat semua data dan statistik (sama seperti admin)
- Sama design dengan admin dashboard
- **Read-only** - tidak ada CRUD buttons

### Perbandingan Visual
```
Home (/)           = Public User View (Read-Only)
/admin/dashboard   = Admin View (CRUD Enabled)
/user/dashboard    = Authenticated User View (Read-Only, Same as Admin Design)
```

---

## 📁 File-File yang Dibuat

### Controllers
- `app/Http/Controllers/AuthController.php` - Login/logout logic
- `app/Http/Controllers/UserIndexController.php` - Public user dashboard (home page)
- `app/Http/Controllers/Admin/DashboardController.php` - Admin dashboard
- `app/Http/Controllers/User/DashboardController.php` - Authenticated user dashboard

### Middleware
- `app/Http/Middleware/EnsureAdminRole.php` - Proteksi admin routes
- `app/Http/Middleware/EnsureUserRole.php` - Proteksi user routes
- `app/Http/Kernel.php` - Konfigurasi middleware aliases

### Frontend Views
- `resources/views/auth/login.blade.php` - Halaman login
- `resources/views/user/index.blade.php` - Public user dashboard (home)
- `resources/views/admin/dashboard/index.blade.php` - Admin & Authenticated user dashboard
- `resources/views/layouts/admin.blade.php` - Layout dengan logout button

### Database
- `database/migrations/2026_02_20_000000_add_role_to_users_table.php` - Add role & last_login_at
- `database/seeders/DatabaseSeeder.php` - Seed admin & user accounts

### Routes
- `routes/web.php` - Updated dengan auth routes dan middleware

---

## 🛣️ URL Routes

| Endpoint | Method | Akses | Deskripsi |
|----------|--------|-------|-----------|
| `/` | GET | Public | Dashboard user (home page) |
| `/admin` | GET | Public | Halaman login |
| `/login` | POST | Public | Submit login form |
| `/logout` | POST | Auth | Logout user |
| `/admin/dashboard` | GET | Admin | Dashboard admin (CRUD enabled) |
| `/user/dashboard` | GET | Auth User | Dashboard user authenticated (read-only) |
| `/admin/infografis` | GET/POST/PUT/DELETE | Admin | CRUD Infografis |
| `/admin/kepuasan_pengunjung` | GET/POST/PUT/DELETE | Admin | CRUD Kepuasan Pengunjung |
| `/admin/konten_tematik` | GET/POST/PUT/DELETE | Admin | CRUD Konten Tematik |
| `/admin/layanan_konsultasi` | GET/POST/PUT/DELETE | Admin | CRUD Layanan Konsultasi |
| `/admin/penggunaan_data` | GET/POST/PUT/DELETE | Admin | CRUD Penggunaan Data |
| `/admin/portal_sata` | GET/POST/PUT/DELETE | Admin | CRUD Portal SATA |
| `/admin/rekomendasi_statistik` | GET/POST/PUT/DELETE | Admin | CRUD Rekomendasi Statistik |
| `/admin/daftar_data` | GET/POST/PUT/DELETE | Admin | CRUD Daftar Data |

---

## 🔧 Testing

### Test Home Public Dashboard
```bash
# Buka di browser atau curl
GET http://127.0.0.1:8000/
# Hasilnya: Dashboard user langsung ditampilkan
```

### Test Login Admin
```bash
GET http://127.0.0.1:8000/admin
# Login dengan:
email: admin@dashboard.com
password: admin123456
# Redirect ke: /admin/dashboard
```

### Test Login User  
```bash
GET http://127.0.0.1:8000/admin
# Login dengan:
email: user@dashboard.com
password: user123456
# Redirect ke: /user/dashboard
```

### Test Role Protection
```bash
# Login sebagai user, akses route admin
GET /admin/infografis
# Hasil: Ditolak (redirect ke home)

# Login sebagai admin, akses route user
GET /user/dashboard
# Hasil: Akses diterima (but no CRUD buttons)
```

### Test Logout
```bash
POST /logout
# Session dihapus, redirect ke /
```

---

## 📝 Catatan Penting

1. **Password Default**: Gunakan password di atas untuk testing. Untuk production, ubah via admin panel.
2. **CSRF Protection**: Semua form POST dilindungi CSRF token otomatis
3. **Session Management**: Session berlaku selama browser dibuka, logout menghapus session
4. **Email Unique**: Email user harus unique di database
5. **Last Login Tracking**: Setiap login mencatat waktu login terakhir

---

## 🎨 Struktur View & Design

### View Structure
```
resources/views/
├── auth/
│   └── login.blade.php          ← Halaman login
├── user/
│   └── index.blade.php          ← Public dashboard (home)
├── admin/
│   ├── dashboard/
│   │   └── index.blade.php      ← Admin & Authenticated user dashboard
│   └── ... (folder resource lainnya)
└── layouts/
    └── admin.blade.php          ← Main layout dengan navbar & logout
```

### Design Konsistensi
- **Home dashboard** (`/`) = layout admin tanpa navbar (bisa ditambahkan header)
- **Admin dashboard** (`/admin/dashboard`) = full layout dengan CRUD buttons
- **User dashboard** (`/user/dashboard`) = sama layout dengan admin tapi tanpa CRUD buttons
- Semua memakai styling yang sama: Bootstrap 5 + custom CSS dari `admin.css`

### Reusable Components
- Navbar dengan menu links
- Stat cards (4 kartu di atas)
- Calendar dengan range selection
- Charts (Portal SATA dan Kepuasan Pengunjung)
- Tables dan schedules

---

## ❓ Troubleshooting

### Error: Column not found - 'tanggal' in kepuasan_pengunjung
**Penyebab:** Query menggunakan kolom `tanggal` padahal nama kolom sebenarnya `tanggal_target`

**Solusi:** ✅ SUDAH DIPERBAIKI
- `UserIndexController.php` ✅
- `User/DashboardController.php` ✅  
- `user/index.blade.php` ✅
- Kolom yang benar: `tanggal_target`

### Error: Column not found - 'minggu' atau 'jumlah_pengunjung' in portal_sata
**Penyebab:** Query menggunakan kolom yang tidak ada di table `portal_sata`

**Solusi:** ✅ SUDAH DIPERBAIKI
- `UserIndexController.php` ✅
- `User/DashboardController.php` ✅
- `user/index.blade.php` ✅
- Kolom yang benar: `nama_dataset`, `capaian`

### Error: Target class [admin] does not exist
**Penyebab:** Middleware cache belum di-refresh setelah perubahan

**Solusi:** ✅ SUDAH DIPERBAIKI
- Cache cleared: `php artisan config:clear && php artisan cache:clear && php artisan route:clear`
- Middleware aliases sudah di-register di `Kernel.php`

### Masalah: Login gagal
**Solusi**: 
- Pastikan email dan password benar
- Pastikan user sudah tersimpan di database: `php artisan db:seed`
- Check file `.env` MySQL connection

### Masalah: Migration error
**Solusi**: 
- Jalankan `php artisan migrate --fresh` untuk reset
- Jangan lupa jalankan `php artisan db:seed` setelah migrate
- Pastikan `.env` DATABASE_URL benar

### Masalah: CSRF token mismatch
**Solusi**:
- Pastikan session disk tersimpan dengan benar
- Clear browser cache dan cookies
- Pastikan POST requests include CSRF token

### Masalah: 404 atau route not found
**Solusi:**
- Jalankan `php artisan route:list` untuk melihat semua routes
- Pastikan controller dan route sudah terdaftar dengan benar

### Masalah: Data tidak muncul di chart
**Solusi:**
- Check database apakah sudah ada data di table
- Lihat error di `storage/logs/laravel.log`
- Pastikan query menggunakan kolom yang benar

---

## � Database Schema Reference

### kepuasan_pengunjung Table
| Column | Type | Note |
|--------|------|------|
| `id` | int | Primary key |
| `tanggal_target` | date | **[CORRECT COLUMN]** - untuk date query |
| `jenis_kelamin` | string |  |
| `sangat_puas` | string |  |
| `puas` | string |  |
| `tidak_puas` | string |  |
| `sangat_tidak_puas` | string |  |
| `created_at`, `updated_at` | timestamp |  |

### portal_sata Table
| Column | Type | Note |
|--------|------|------|
| `id` | int | Primary key |
| `tanggal_target` | date | **[CORRECT COLUMN]** - untuk date query |
| `nama_dataset` | string | **[CORRECT COLUMN]** - untuk chart labels |
| `dataset` | string |  |
| `target_total` | string |  |
| `capaian` | string | **[CORRECT COLUMN]** - untuk chart data |
| `capaian(%)` | decimal |  |
| `created_at`, `updated_at` | timestamp |  |

### users Table
| Column | Type | Note |
|--------|------|------|
| `id` | int | Primary key |
| `name` | string | User full name |
| `email` | string | Unique email |
| `password` | string | Hashed password (bcrypt) |
| `role` | enum | `admin` atau `user` |
| `last_login_at` | timestamp | Last login time |
| `created_at`, `updated_at` | timestamp |  |

### 1. Setup Database
```bash
# Run migrations
php artisan migrate

# Seed user accounts
php artisan db:seed
```

### 2. Start Development Server
```bash
php artisan serve
```

### 3. Akses Aplikasi
- **Public Dashboard**: `http://127.0.0.1:8000/`
- **Login Admin**: `http://127.0.0.1:8000/admin`

### 4. Test Akun
```
Admin:
- Email: admin@dashboard.com
- Password: admin123456

User:
- Email: user@dashboard.com
- Password: user123456
```

### 5. Fitur yang Tersedia
| Halaman | Akses | CRUD |
|---------|-------|------|
| `/` (Home) | Public | Tidak |
| `/admin` (Login) | Public | - |
| `/admin/dashboard` | Admin only | Ya |
| `/user/dashboard` | Auth User | Tidak |

### Cache Clearing (jika ada issue)
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
```

---

## 🔄 Fixes Terbaru (V2)

### Error Resolutions
1. **Column `tanggal` not found di `kepuasan_pengunjung`**
   - ✅ Fixed: Gunakan `tanggal_target` bukan `tanggal`
   - Files: `UserIndexController.php`, `User/DashboardController.php`, `user/index.blade.php`

2. **Column `minggu` not found di `portal_sata`**
   - ✅ Fixed: Gunakan `nama_dataset` bukan `minggu`
   - Files: `UserIndexController.php`, `User/DashboardController.php`, `user/index.blade.php`

3. **Column `jumlah_pengunjung` not found di `portal_sata`**
   - ✅ Fixed: Gunakan `capaian` bukan `jumlah_pengunjung`
   - Files: Chart labels dan data updated

4. **Middleware error: Target class [admin] does not exist**
   - ✅ Fixed: Ran cache clear commands
   - Command: `php artisan config:clear && php artisan cache:clear && php artisan route:clear`

### After Fixes
- Clear cache immediately:
  ```bash
  php artisan config:clear
  php artisan cache:clear
  php artisan route:clear
  ```
- Reload application di browser
