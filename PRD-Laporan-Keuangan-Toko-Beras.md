# Product Requirements Document (PRD)
# Sistem Laporan Keuangan Toko Beras

**Versi:** 1.1 (revisi: penggunaan personal/single-user, Laravel 12)
**Tanggal:** 20 Juli 2026
**Tech Stack:** Laravel 12 + Tailwind CSS + MySQL

---

## 1. Latar Belakang

Pemilik toko beras saat ini mencatat keuangan secara manual (modal, pembelian, penjualan) sehingga sulit mengetahui laba/rugi secara real-time dan tidak ada rekap mingguan/bulanan yang rapi. Sistem ini dibangun untuk mendigitalisasi pencatatan tersebut sekaligus menyediakan dashboard otomatis.

## 2. Tujuan Produk

1. Mencatat modal awal usaha.
2. Mencatat setiap transaksi pembelian stok beras.
3. Mencatat setiap transaksi penjualan.
4. Menghitung laba/rugi secara otomatis dari data pembelian & penjualan.
5. Menyajikan dashboard laporan keuangan mingguan dan bulanan.

## 3. Target Pengguna

**Revisi:** Aplikasi ini untuk **penggunaan pribadi (personal use)** — hanya 1 akun (Owner) yang mengelola semuanya. Tidak ada halaman registrasi publik dan tidak ada fitur multi-user di MVP ini.

| Role | Deskripsi | Hak Akses |
|---|---|---|
| Owner (single user) | Pemilik toko, satu-satunya pengguna sistem | Full akses ke semua fitur |

Kolom `role` tetap disiapkan di tabel `users` (default `owner`) agar tidak perlu migrasi ulang jika suatu saat ingin dikembangkan menjadi multi-user, namun **tidak diimplementasikan logic-nya di MVP ini**.

## 4. Ruang Lingkup (Scope)

### In Scope (MVP)
- Autentikasi single-user (login/logout only, **tanpa halaman register publik**)
- CRUD Modal (input modal awal & modal tambahan)
- CRUD Pembelian (stok masuk)
- CRUD Penjualan (stok keluar)
- Perhitungan otomatis: Laba Kotor, Laba Bersih, Total Modal Berjalan
- Dashboard ringkasan mingguan & bulanan (grafik + angka)
- Filter laporan berdasarkan rentang tanggal
- Export laporan (PDF/Excel) — *nice to have, bisa fase 2*

### Out of Scope (fase berikutnya)
- Manajemen stok per jenis beras secara detail (varian, satuan karung/kg otomatis konversi)
- Multi-cabang toko
- Multi-user / manajemen staff & role
- Hutang piutang (kasbon pelanggan/supplier)
- Notifikasi stok menipis

## 5. Rincian Fitur (User Stories)

### 5.1 Input Modal
**Sebagai** owner, **saya ingin** mencatat modal awal dan modal tambahan, **agar** saya tahu total dana yang sudah saya suntikkan ke usaha.

- Form input: jumlah modal, tanggal, keterangan (mis. "Modal awal", "Tambahan modal Juli")
- Riwayat modal ditampilkan dalam tabel, urut tanggal terbaru
- Total modal = SUM seluruh entri modal

### 5.2 Input Pembelian
**Sebagai** owner, **saya ingin** mencatat setiap pembelian stok beras, **agar** biaya pokok penjualan (HPP) tercatat rapi.

- Form input: nama barang, jumlah (qty), satuan, harga per satuan, total otomatis (qty × harga satuan), tanggal, supplier (opsional), catatan
- List pembelian bisa difilter per tanggal/minggu/bulan
- Total pembelian per periode dihitung otomatis

### 5.3 Input Hasil Penjualan
**Sebagai** owner, **saya ingin** mencatat setiap penjualan, **agar** pemasukan toko tercatat rapi.

- Form input: nama barang, jumlah (qty), satuan, harga per satuan, total otomatis, tanggal, pembeli (opsional), catatan
- List penjualan bisa difilter per tanggal/minggu/bulan
- Total penjualan per periode dihitung otomatis

### 5.4 Hitung Laba Otomatis
**Sebagai** owner, **saya ingin** sistem menghitung laba otomatis, **agar** saya tidak perlu hitung manual.

**Formula:**
```
Laba (Profit) = Total Penjualan (periode) − Total Pembelian (periode)
Modal Berjalan = Total Modal Masuk + Akumulasi Laba − Akumulasi Rugi
```

- Laba dihitung real-time setiap ada transaksi baru (via query agregasi, bukan disimpan statis) supaya selalu akurat
- Ditampilkan per hari, minggu, dan bulan
- Indikator visual: hijau (laba/untung), merah (rugi)

### 5.5 Dashboard Laporan Mingguan & Bulanan
**Sebagai** owner, **saya ingin** melihat ringkasan keuangan mingguan dan bulanan dalam satu halaman, **agar** saya bisa mengambil keputusan bisnis dengan cepat.

Isi dashboard:
- Card ringkasan: Total Modal, Total Pembelian, Total Penjualan, Laba/Rugi (periode berjalan)
- Grafik tren (line/bar chart) penjualan vs pembelian per minggu (untuk view bulanan) dan per hari (untuk view mingguan)
- Tabel transaksi terbaru (pembelian & penjualan)
- Toggle/filter: Mingguan | Bulanan | Custom date range

## 6. Alur Proses Bisnis (Flow)

```
1. Owner login
2. Input modal awal (sekali di awal, bisa tambah modal kapan saja)
3. Setiap ada belanja stok  -> input transaksi Pembelian
4. Setiap ada penjualan     -> input transaksi Penjualan
5. Sistem otomatis menghitung Laba = Penjualan - Pembelian
6. Owner buka Dashboard untuk lihat rekap mingguan/bulanan
```

## 7. Kebutuhan Non-Fungsional

| Aspek | Kebutuhan |
|---|---|
| Framework | Laravel 12.x (mendukung PHP 8.2+) |
| Styling | Vanilla CSS / Tailwind CSS (Breeze default) + Alpine.js untuk interaktivitas ringan |
| Database | **SQLite** (Sangat direkomendasikan untuk penggunaan pribadi karena portable, zero-configuration, dan mudah di-backup) ATAU **MySQL 8.x** sebagai alternatif |
| Auth | Laravel Breeze (Blade/Livewire stack), disesuaikan untuk single-user — tanpa register publik |
| Chart | Chart.js atau ApexCharts (via CDN atau npm) untuk visualisasi dashboard |
| Responsif | Mobile-friendly (dioptimalkan untuk browser handphone) |
| Keamanan | Proteksi route ketat, login rate limiting, session security, & opsi 2FA (TOTP) |
| Performa | Indexing pada kolom tanggal dan `user_id` untuk query laporan cepat |

---

## 8. Rancangan Database

### 8.1 Entity Relationship (ringkas)

```
users (1) ────< capitals (modal)
users (1) ────< purchases (pembelian)
users (1) ────< purchases >──── products
users (1) ────< sales (penjualan)
users (1) ────< sales >──── products
products (1) ──< purchases
products (1) ──< sales
```

> Catatan: tabel `products` ditambahkan sebagai penyempurnaan agar nama barang konsisten & bisa dipakai untuk laporan per jenis beras di masa depan. Jika ingin benar-benar minimal sesuai MVP awal, kolom `item_name` bisa langsung ditulis manual di tabel purchases/sales tanpa tabel products.

### 8.2 Struktur Tabel

#### `users`
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint, PK | |
| name | varchar(100) | |
| email | varchar(100), unique | |
| password | varchar(255) | |
| role | enum('owner','staff') default 'owner' | Tetap disiapkan untuk skalabilitas masa depan |
| created_at, updated_at | timestamp | |

#### `products` (opsional, rekomendasi)
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint, PK | |
| name | varchar(100) | mis. "Beras IR64", "Beras Pandan Wangi" |
| unit | varchar(20) | mis. "kg", "karung" |
| created_at, updated_at | timestamp | |

#### `capitals` (modal)
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint, PK | |
| user_id | bigint, FK → users.id | |
| amount | decimal(15,2) | jumlah modal |
| type | enum('awal','tambahan') default 'awal' | |
| description | varchar(255) nullable | |
| date | date | tanggal modal masuk |
| created_at, updated_at | timestamp | |

#### `purchases` (pembelian)
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint, PK | |
| user_id | bigint, FK → users.id | |
| product_id | bigint, FK → products.id, nullable | |
| item_name | varchar(150) | fallback jika tanpa tabel products |
| quantity | decimal(10,2) | |
| unit | varchar(20) | |
| price_per_unit | decimal(15,2) | |
| total_price | decimal(15,2) | quantity × price_per_unit (disimpan agar query cepat) |
| supplier | varchar(100) nullable | |
| purchase_date | date | |
| notes | text nullable | |
| created_at, updated_at | timestamp | |

#### `sales` (penjualan)
| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint, PK | |
| user_id | bigint, FK → users.id | |
| product_id | bigint, FK → products.id, nullable | |
| item_name | varchar(150) | fallback jika tanpa tabel products |
| quantity | decimal(10,2) | |
| unit | varchar(20) | |
| price_per_unit | decimal(15,2) | |
| total_price | decimal(15,2) | quantity × price_per_unit |
| buyer | varchar(100) nullable | |
| sale_date | date | |
| notes | text nullable | |
| created_at, updated_at | timestamp | |

### 8.3 Query Kunci (contoh logika laporan)

**Total Modal:**
```sql
SELECT SUM(amount) AS total_modal FROM capitals WHERE user_id = ?;
```

**Total Pembelian per periode:**
```sql
SELECT SUM(total_price) AS total_pembelian
FROM purchases
WHERE user_id = ? AND purchase_date BETWEEN ? AND ?;
```

**Total Penjualan per periode:**
```sql
SELECT SUM(total_price) AS total_penjualan
FROM sales
WHERE user_id = ? AND sale_date BETWEEN ? AND ?;
```

**Laba periode:**
```
laba = total_penjualan - total_pembelian
```

**Rekap mingguan (contoh, group per minggu dalam sebulan):**
```sql
SELECT YEARWEEK(sale_date, 1) AS minggu, SUM(total_price) AS total_penjualan
FROM sales
WHERE user_id = ?
GROUP BY minggu
ORDER BY minggu DESC;
```

### 8.4 Indexing yang Disarankan
- `purchases`: index pada `purchase_date`, `user_id`
- `sales`: index pada `sale_date`, `user_id`
- `capitals`: index pada `date`, `user_id`

---

## 9. Rancangan Halaman (Information Architecture)

1. **Login** (Halaman Autentikasi Tunggal)
2. **Dashboard** (home setelah login) — ringkasan modal, pembelian, penjualan, laba (mingguan/bulanan), grafik
3. **Modal** — list + form tambah modal
4. **Pembelian** — list + form tambah/edit pembelian, filter tanggal
5. **Penjualan** — list + form tambah/edit penjualan, filter tanggal
6. **Laporan** — halaman detail laporan dengan filter custom range + export (fase 2)
7. **Pengaturan Profil & Keamanan** (ubah password, aktifkan 2FA)

---

## 10. Roadmap Pengembangan

| Fase | Fitur |
|---|---|
| Fase 1 (MVP) | Auth (Breeze, No Register), CRUD Modal, CRUD Pembelian, CRUD Penjualan, Hitung laba otomatis, Dashboard mingguan/bulanan, SQLite backup |
| Fase 2 | Export laporan PDF/Excel, Manajemen produk/jenis beras, Grafik lebih detail, Autentikasi Dua Faktor (2FA) |
| Fase 3 | Multi-user (staff) jika dibutuhkan, Hutang-piutang, Notifikasi stok |

---

## 11. Best Practice Autentikasi & Keamanan (Personal Use, Laravel 12)

Karena aplikasi ini **untuk dipakai sendiri** dan menyimpan data keuangan pribadi, pendekatannya dibuat **sederhana namun tetap aman** sesuai standar keamanan web modern.

### 11.1 Starter Kit: Laravel Breeze
Gunakan **Laravel Breeze** (stack Blade atau Livewire) untuk mempercepat scaffolding login, logout, reset password, dan manajemen profil bawaan.
```bash
composer require laravel/breeze --dev
php artisan breeze:install blade
npm install && npm run build
```

### 11.2 Nonaktifkan Registrasi Publik
Untuk mencegah user tidak dikenal mendaftar ke aplikasi:
1. Buka `routes/auth.php` yang dihasilkan oleh Breeze.
2. Hapus atau beri komentar pada route `register` (baik `GET` maupun `POST`).
3. Hapus link menuju halaman register ("Register") pada tampilan login (`resources/views/auth/login.blade.php`).

### 11.3 Pembuatan Akun Owner yang Aman (Artisan Command)
Jangan menuliskan password plain-text dalam database seeder atau file migrasi yang bisa ter-commit ke repositori Git. Gunakan perintah Artisan kustom untuk membuat akun owner secara interaktif.

Buat command baru:
```bash
php artisan make:command CreateOwnerCommand
```

Implementasi di `app/Console/Commands/CreateOwnerCommand.php`:
```php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Laravel\Prompts\Prompt; // Laravel 12 menggunakan Laravel Prompts secara native

class CreateOwnerCommand extends Command
{
    protected $signature = 'app:create-owner';
    protected $description = 'Membuat akun owner pertama untuk aplikasi Laporan Keuangan';

    public function handle(): int
    {
        $name = \Laravel\Prompts\text('Nama Owner:', default: 'Owner Toko');
        $email = \Laravel\Prompts\text(
            label: 'Email Owner:',
            required: true,
            validate: fn (string $value) => !filter_var($value, FILTER_VALIDATE_EMAIL) ? 'Format email tidak valid.' : null
        );
        
        if (User::where('email', $email)->exists()) {
            $this->error('User dengan email tersebut sudah ada.');
            return self::FAILURE;
        }

        $password = \Laravel\Prompts\password('Password (minimal 8 karakter):', required: true);

        User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'role' => 'owner',
        ]);

        $this->info('Akun owner berhasil dibuat! Silakan masuk melalui halaman login.');
        return self::SUCCESS;
    }
}
```

### 11.4 Proteksi Route & Middleware di Laravel 12
Bungkus seluruh route laporan keuangan dan dashboard dalam middleware `auth`. Pada Laravel 12, konfigurasi routing dan global middleware dipusatkan pada `bootstrap/app.php` (tanpa `Http/Kernel.php` tradisional).

Contoh proteksi route di `routes/web.php`:
```php
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CapitalController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\SaleController;

Route::redirect('/', '/login');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('capitals', CapitalController::class);
    Route::resource('purchases', PurchaseController::class);
    Route::resource('sales', SaleController::class);
});
```

### 11.5 Rate Limiting & Proteksi Brute-Force
Pastikan controller login tetap menggunakan custom request dari Breeze (`App\Http\Requests\Auth\LoginRequest`) yang menyertakan built-in rate limiting (`RateLimiter::ensureIsNotRateLimited()`). Ini mencegah peretas menebak password melalui brute-force attack.

### 11.6 Pengaturan Sesi & Cookie di File `.env`
Gunakan driver sesi `database` atau `file`. Jika menggunakan SQLite, driver sesi `file` sudah sangat memadai dan cepat. Sesuaikan pengaturan `.env` berikut:
```env
SESSION_DRIVER=file
SESSION_LIFETIME=120           # Durasi sesi aktif (menit)
SESSION_EXPIRE_ON_CLOSE=true  # Sesi berakhir saat browser ditutup (opsional untuk keamanan tambahan)
SESSION_ENCRYPT=true          # Mengenkripsi data sesi
SESSION_PATH=/
SESSION_DOMAIN=null
SESSION_SECURE_COOKIE=true    # Wajib TRUE jika dideploy menggunakan HTTPS
SESSION_HTTP_ONLY=true        # Mencegah akses XSS ke cookie sesi
SESSION_SAME_SITE=lax
```

### 11.7 Pemaksaan HTTPS (Enforce HTTPS)
Jika aplikasi di-deploy pada VPS atau server lokal yang terhubung ke jaringan publik, paksa penggunaan HTTPS untuk mengenkripsi traffic data keuangan. Pada Laravel 12, Anda dapat melakukannya di `app/Providers/AppServiceProvider.php`:
```php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }
    }
}
```

### 11.8 Two-Factor Authentication (2FA) - Opsi Rekomendasi
Untuk proteksi ekstra pada data keuangan pribadi, disarankan mengintegrasikan 2FA berbasis TOTP (Google Authenticator) di Fase 2 menggunakan package seperti `pragmarx/google2fa-laravel` atau Laravel Fortify. Pengguna harus memasukkan kode 6 digit dari ponsel mereka setiap kali login.

### 11.9 Strategi Backup Data Keuangan (Sangat Penting)
Karena ini merupakan sistem laporan keuangan pribadi, kehilangan data adalah risiko terbesar.
1. **Jika Menggunakan SQLite**: Backup harian dapat dilakukan dengan sangat mudah menggunakan cron job sederhana atau Laravel Scheduler yang menyalin file database (mis. `database/database.sqlite`) ke folder eksternal, cloud storage (Google Drive, Dropbox, AWS S3), atau mengirimkannya secara aman ke email/Telegram Bot pribadi.
2. **Menggunakan Package**: Instal package `spatie/laravel-backup` untuk mempermudah backup terjadwal database beserta file assets penting langsung ke cloud storage pilihan.
   ```bash
   composer require spatie/laravel-backup
   ```
   Atur schedule backup di `routes/console.php` (gaya baru Laravel 12 untuk task scheduling):
   ```php
   use Illuminate\Support\Facades\Schedule;

   Schedule::command('backup:run')->daily()->at('01:00');
   ```

### 11.10 Ringkasan Perubahan dari Rancangan Awal
| Aspek | Rancangan Awal | Direvisi Menjadi (Best Practice) |
|---|---|---|
| **Database** | MySQL | **SQLite** (Direkomendasikan untuk kemudahan backup & portabilitas personal) |
| **Registrasi** | Registrasi Publik Aktif | **Registrasi Dinonaktifkan**, registrasi diganti command Artisan kustom |
| **Pembuatan Akun** | Database Seeder (Plain Password) | **Command Artisan Kustom** (`php artisan app:create-owner`) |
| **Penyimpanan Sesi** | Database | **File / Database** (Session Encryption diaktifkan) |
| **Task Scheduler** | `app/Console/Kernel.php` (Laravel < 11) | **`routes/console.php`** (Laravel 12 standar) |
| **Backup** | Manual / MySQL Dump | **Laravel Scheduler + Spatie Backup (Otomatis)** |

---

## 12. Metrik Keberhasilan (Success Metrics)

- Owner dapat melihat laba harian/mingguan/bulanan tanpa hitung manual.
- Waktu input transaksi < 30 detik per transaksi.
- Data laporan akurat 100% terhadap pencatatan manual (diverifikasi saat fase testing).
- Proses backup database berjalan sukses sekali setiap hari.

---

*Dokumen ini dapat disesuaikan lebih lanjut sesuai kebutuhan spesifik toko (misal jika ingin per jenis beras, satuan karung vs kg dengan konversi otomatis, dll).*
