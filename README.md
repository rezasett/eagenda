# E-Agenda — Sistem Administrasi Surat Masuk/Keluar

Aplikasi Laravel + Livewire tanpa NPM untuk tugas pengujian perangkat lunak. Scope aplikasi dibatasi pada administrasi surat masuk dan surat keluar.

## Fitur

- Login sederhana tanpa Breeze/Fortify.
- Role pengguna:
  - `staff`: input, edit, hapus, lihat, download surat.
  - `pimpinan`: lihat dan download surat saja.
- CRUD surat masuk/keluar.
- Upload berkas digital surat: PDF, JPG, JPEG, PNG, DOC, DOCX.
- Validasi testable untuk EP dan BVA:
  - Tanggal surat wajib format `DD-MM-YYYY`.
  - File maksimal 2 MB.
  - Nomor surat minimal 1 karakter, maksimal 50 karakter.
  - Perihal maksimal 100 karakter.
  - Email pengirim valid jika diisi.
  - Kategori wajib dari dropdown.
  - Pencarian menolak pola script/injection dasar.
- Filter berdasarkan tipe surat, kategori, tanggal, dan bulan ini.
- Download file surat melalui route terproteksi.
- Test plan EP, BVA, Black Box, bug, dan ISO 25010 tersedia di `docs/TEST_PLAN.md`.

## Cara Install di Project Laravel Baru

> Paket ini adalah file aplikasi yang ditempel ke project Laravel kosong. Folder `vendor` tidak disertakan.

```bash
composer create-project laravel/laravel e-agenda
cd e-agenda
composer require livewire/livewire
```

Salin seluruh folder/file dari paket ini ke root project Laravel. Timpa file jika diminta.

Set `.env`:

```env
APP_NAME="E-Agenda"
DB_CONNECTION=sqlite
```

Buat database SQLite jika belum ada:

```bash
php -r "file_exists('database/database.sqlite') || touch('database/database.sqlite');"
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

Akses:

```text
http://127.0.0.1:8000
```

## Akun Demo

| Role | Email | Password |
|---|---|---|
| Staff Administrasi | staff@kantor.com | password |
| Pimpinan | pimpinan@kantor.com | password |

## Struktur File Penting

```text
app/Livewire/LoginPage.php
app/Livewire/DashboardPage.php
app/Livewire/LetterIndex.php
app/Models/Letter.php
app/Models/User.php
app/Http/Controllers/LetterDownloadController.php
app/Providers/AppServiceProvider.php
database/migrations/2026_07_06_000001_add_role_to_users_table.php
database/migrations/2026_07_06_000002_create_letters_table.php
database/seeders/DatabaseSeeder.php
resources/views/layouts/app.blade.php
resources/views/livewire/login-page.blade.php
resources/views/livewire/dashboard-page.blade.php
resources/views/livewire/letter-index.blade.php
routes/web.php
public/css/eagenda.css
docs/TEST_PLAN.md
```

## Menjalankan Test

```bash
php artisan test
```

## Catatan Tanpa NPM

Aplikasi tidak menggunakan Vite, npm, Tailwind build, atau Breeze. Styling menggunakan CSS lokal di `public/css/eagenda.css`. Livewire asset dimuat melalui `@livewireStyles` dan `@livewireScripts`.
