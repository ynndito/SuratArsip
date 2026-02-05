# Panduan Instalasi Sistem Arsip Dokumen

## Persyaratan
- XAMPP (PHP 7.4+ dan MySQL)
- Web browser modern

## Langkah Instalasi

### 1. Setup Database

1. Buka phpMyAdmin di browser: `http://localhost/phpmyadmin`
2. Klik tab "SQL"
3. Copy dan paste seluruh isi file `database.sql`
4. Klik "Go" untuk menjalankan query
5. Database `arsip_surat` dan tabel `surat` akan dibuat otomatis

### 2. Konfigurasi Koneksi Database

Edit file `config/koneksi.php` jika diperlukan:
- `$host`: default `localhost`
- `$username`: default `root`
- `$password`: default kosong (sesuaikan dengan setting MySQL Anda)
- `$database`: default `arsip_surat`

### 3. Setup Folder Uploads

Folder `uploads/` sudah dibuat otomatis. Pastikan folder ini memiliki permission write (biasanya sudah otomatis di Windows).

Jika folder belum ada, buat manual:
- Buat folder baru dengan nama `uploads` di root project
- Pastikan folder memiliki permission write

### 4. Akses Aplikasi

Buka browser dan akses:
```
http://localhost/SuratArsip/index.php
```

## Struktur File

```
SuratArsip/
├── config/
│   └── koneksi.php          # Koneksi database
├── uploads/                  # Folder untuk menyimpan file upload
├── index.php                 # Halaman utama (form input + daftar surat)
├── upload.php                # Proses upload dan simpan data
├── detail.php                # Halaman detail surat
├── download.php              # Proses download file
├── database.sql              # Script SQL untuk setup database
└── index.html                # File HTML original (tidak digunakan)

```

## Fitur yang Tersedia

1. **Input Dokumen**
   - Nomor surat
   - Nama surat
   - Tanggal surat
   - Upload file (PDF, JPG, PNG, maksimal 10MB)

2. **Daftar Dokumen**
   - Tampilkan semua surat yang tersimpan
   - Pencarian berdasarkan nama surat
   - Filter berdasarkan rentang tanggal

3. **Detail Dokumen**
   - Informasi lengkap surat
   - Preview file (PDF menggunakan iframe, gambar menggunakan img)
   - Tombol download file

4. **Keamanan**
   - Validasi tipe file (hanya PDF, JPG, PNG)
   - Validasi MIME type
   - Validasi ukuran file (maksimal 10MB)
   - Sanitasi input untuk mencegah SQL injection
   - Nama file unik untuk mencegah overwrite

## Catatan Penting

- Pastikan folder `uploads/` memiliki permission write
- File yang diupload akan disimpan dengan nama unik (timestamp + uniqid)
- Database menggunakan charset UTF-8 untuk mendukung karakter Indonesia
- Semua input sudah di-sanitize untuk keamanan

## Troubleshooting

### Error: "Koneksi gagal"
- Pastikan MySQL service di XAMPP sudah running
- Cek username dan password di `config/koneksi.php`

### Error: "File tidak bisa diupload"
- Pastikan folder `uploads/` ada dan memiliki permission write
- Cek ukuran file tidak melebihi 10MB

### Error: "Tipe file tidak diizinkan"
- Pastikan file yang diupload adalah PDF, JPG, atau PNG
- Cek ekstensi file (case sensitive)

