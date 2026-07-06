# Test Plan — Sistem Administrasi Surat Masuk dan Surat Keluar (E-Agenda)

## 1. Analisis Sistem

| Komponen | Penjelasan |
|---|---|
| Nama aplikasi | Sistem Administrasi Surat Masuk dan Surat Keluar (E-Agenda) |
| Tujuan | Mengelola, mencatat, menyimpan arsip digital, dan memudahkan pencarian surat masuk/keluar. |
| Scope | Administrasi satu entitas data, yaitu surat masuk dan surat keluar. |
| Aktor 1 | Staf Administrasi/Inputer. Dapat login, input, edit, hapus, lihat, filter, dan download data surat. |
| Aktor 2 | Kepala Bagian/Pimpinan. Dapat login, melihat daftar surat, melakukan filter, dan download berkas. Tidak dapat input, edit, atau hapus. |
| Batas ukuran file | Maksimal 2 MB. |
| Format file | PDF, JPG, JPEG, PNG, DOC, DOCX. |
| Format tanggal input form | DD-MM-YYYY. |
| Batas perihal | Maksimal 100 karakter. |

## 2. Fitur Utama

1. Login pengguna berdasarkan role.
2. Input surat masuk dan surat keluar.
3. Upload berkas digital surat.
4. Edit dan hapus surat oleh staf administrasi.
5. Download berkas surat oleh semua role yang login.
6. Pencarian berdasarkan nomor surat, pengirim, penerima, dan perihal.
7. Filter berdasarkan jenis surat, kategori, rentang tanggal, dan surat bulan ini.
8. Pembatasan hak akses pimpinan agar tidak dapat mengubah atau menghapus data.

---

## 3. Test Case Equivalence Partitioning (EP)

| ID | Area Uji | Kelas Valid | Kelas Invalid | Langkah Uji | Expected Result |
|---|---|---|---|---|---|
| EP-01 | Input tanggal surat | `06-07-2026` | `kemarin` | Isi tanggal dengan format valid dan invalid lalu klik Simpan. | Format valid diterima; teks sembarang ditolak dengan pesan error tanggal harus DD-MM-YYYY. |
| EP-02 | Format upload berkas | `.pdf`, `.jpg`, `.docx` | `.exe`, `.mp3` | Upload file valid dan invalid. | File valid tersimpan; file invalid ditolak. |
| EP-03 | Email pengirim | `admin@kantor.com` | `admin#kantor` | Isi email valid dan invalid. | Email valid diterima; email tanpa format email ditolak. |
| EP-04 | Kategori surat | Memilih `Umum/Keuangan/Hukum/Internal/Eksternal/Rahasia` | Kategori dikosongkan | Simpan data dengan kategori dipilih dan dikosongkan. | Kategori valid diterima; kategori kosong ditolak. |
| EP-05 | Form pencarian | Kata kunci normal, misalnya `undangan` | `<script>alert(1)</script>` atau `select * from letters` | Masukkan kata kunci normal dan pola script/injection. | Kata kunci normal menampilkan hasil; pola script/injection ditolak/ditampilkan error. |

---

## 4. Test Case Boundary Value Analysis (BVA)

| ID | Area Uji | Nilai Batas | Langkah Uji | Expected Result |
|---|---|---|---|---|
| BVA-01 | Ukuran file maksimal | File tepat 2 MB | Upload file 2 MB. | File diterima jika formatnya valid. |
| BVA-02 | Ukuran file melebihi batas | File 2,1 MB | Upload file 2,1 MB. | File ditolak dengan pesan maksimal 2 MB. |
| BVA-03 | Nomor surat minimal | 1 karakter | Isi nomor surat `A`. | Data diterima jika field lain valid. |
| BVA-04 | Nomor surat kosong | 0 karakter | Kosongkan nomor surat. | Data ditolak dengan pesan nomor surat wajib diisi. |
| BVA-05 | Perihal maksimal | 100 karakter dan 101 karakter | Isi perihal 100 karakter lalu 101 karakter. | 100 karakter diterima; 101 karakter ditolak. |

---

## 5. Test Case Black Box Testing

| ID | Fungsi | Langkah Uji | Expected Result |
|---|---|---|---|
| BB-01 | Tombol Simpan | Login sebagai staff, isi seluruh form valid, klik Simpan. | Data surat berhasil masuk ke tabel database dan muncul di daftar surat. |
| BB-02 | Tombol Hapus Data | Login sebagai staff, klik Hapus pada salah satu data. | Muncul pop-up konfirmasi browser; setelah disetujui, data terhapus. |
| BB-03 | Download Berkas | Klik Download pada data surat yang memiliki file. | File terunduh dengan nama file asli. |
| BB-04 | Hak akses pimpinan | Login sebagai pimpinan, buka Data Surat. | Pimpinan hanya melihat tombol Download; tombol Input/Edit/Hapus tidak tersedia. |
| BB-05 | Filter bulan ini | Pilih filter `Surat Bulan Ini`. | Sistem hanya menampilkan surat dengan tanggal pada bulan berjalan. |

---

## 6. Potensi Bug dan Evaluasi

### Potensi Bug

Bug yang sering muncul pada sistem administrasi surat adalah error saat user mengunggah file dengan nama sangat panjang atau karakter unik, misalnya:

```text
surat_rahasia_&_penting_%v2.pdf
```

Mitigasi pada aplikasi ini:

1. File disimpan oleh sistem menggunakan path hasil generate, bukan nama asli mentah dari user.
2. Nama asli file disanitasi sebelum disimpan ke database.
3. Panjang nama asli file dibatasi maksimal 150 karakter.
4. Format dan ukuran file divalidasi sebelum masuk database.

### Evaluasi ISO 25010

| Karakteristik | Fokus Evaluasi | Implementasi pada Aplikasi |
|---|---|---|
| Functional Suitability | Kesesuaian fitur dengan kebutuhan pencatatan surat. | CRUD surat, upload/download, filter, dan dashboard. |
| Security | Kerahasiaan data administrasi dan pembatasan akses. | Login, role staff/pimpinan, gate `manage-letters`, route download terproteksi. |
| Maintainability | Kemudahan perawatan struktur aplikasi. | Pemisahan Model, Livewire Component, Controller, Migration, dan Seeder. |
| Reliability | Sistem tetap berjalan saat input tidak sesuai. | Validasi tanggal, email, kategori, file, panjang karakter, dan pencarian. |
| Usability | Kemudahan digunakan oleh staf/pimpinan. | Form sederhana, pesan error Bahasa Indonesia, filter jelas, tombol aksi terlihat. |
| Portability | Kemudahan dijalankan di environment lokal. | Tanpa npm, cukup Composer, Laravel, Livewire, dan SQLite/MySQL. |

---

## 7. Mapping Requirement ke File Kode

| Requirement | File Implementasi |
|---|---|
| Login role staff/pimpinan | `app/Livewire/LoginPage.php`, `database/seeders/DatabaseSeeder.php` |
| Hak akses role | `app/Providers/AppServiceProvider.php`, `resources/views/livewire/letter-index.blade.php` |
| Input/edit/hapus surat | `app/Livewire/LetterIndex.php` |
| Upload dan validasi file | `app/Livewire/LetterIndex.php` |
| Download file | `app/Http/Controllers/LetterDownloadController.php` |
| Filter surat | `app/Livewire/LetterIndex.php` |
| Struktur database | `database/migrations/2026_07_06_000002_create_letters_table.php` |
| Tampilan tanpa npm | `resources/views/layouts/app.blade.php`, `public/css/eagenda.css` |
