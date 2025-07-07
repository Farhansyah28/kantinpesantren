# Import Data Ustadz/Ustadzah

Fitur import data ustadz/ustadzah telah berhasil ditambahkan ke sistem kantin pesantren.

## Fitur yang Tersedia

### 1. Import CSV

- Upload file CSV untuk mengimpor data ustadz/ustadzah secara massal
- Validasi format dan data sebelum import
- Laporan hasil import (berhasil/gagal)
- Log aktivitas import

### 2. Download Template

- Template CSV dengan format yang benar
- Contoh data untuk referensi
- Encoding UTF-8 dengan BOM

### 3. Export CSV

- Export semua data ustadz/ustadzah ke file CSV
- Format yang konsisten dengan template

## Format CSV

### Struktur Kolom

```
Nama Lengkap;Nomor Telepon
```

### Contoh Data

```
Nama Lengkap;Nomor Telepon
Ust. Ahmad;081234567890
Usth. Fatimah;081234567891
Ust. Muhammad;+62-812-3456-7892
```

## Aturan Validasi

### Nama Lengkap

- Minimal 3 karakter, maksimal 100 karakter
- Hanya huruf, spasi, dan titik diperbolehkan
- Harus unik (tidak boleh duplikat)

### Nomor Telepon

- Minimal 10 digit, maksimal 15 digit
- Format yang diperbolehkan: angka, +, -, spasi, tanda kurung
- Contoh: `081234567890`, `+62-812-3456-7892`

## Cara Penggunaan

### 1. Akses Halaman Import

- Login sebagai admin atau keuangan
- Buka menu "Ustadz/Ustadzah"
- Klik tombol "Import CSV"

### 2. Download Template

- Klik tombol "Download Template"
- Simpan file template
- Isi data sesuai format

### 3. Upload File

- Klik "Pilih File CSV"
- Pilih file yang sudah disiapkan
- Klik "Import Data"

### 4. Review Hasil

- Sistem akan menampilkan hasil import
- Jika ada error, detail error akan ditampilkan
- Data yang berhasil akan langsung tersimpan

## File yang Dibuat/Dimodifikasi

### Controller

- `application/controllers/Ustadz.php`
  - Method `import()` - Proses upload dan import CSV
  - Method `download_template()` - Download template CSV
  - Method `export_csv()` - Export data ke CSV

### View

- `application/views/ustadz/import.php` - Halaman import
- `application/views/ustadz/index.php` - Tambah tombol import/export

### Template

- `uploads/ustadz/ustadz_template.csv` - Template CSV

### Direktori

- `uploads/ustadz/` - Direktori untuk file upload sementara

## Keamanan

### Validasi File

- Hanya file .csv yang diperbolehkan
- Maksimal ukuran file 5MB
- Validasi MIME type
- Sanitasi nama file

### Validasi Data

- Validasi format data per baris
- Cek duplikat nama
- Validasi panjang dan format input
- Error handling yang detail

### Log Aktivitas

- Semua aktivitas import dicatat
- Log sukses dan gagal
- Tracking per baris data

## Troubleshooting

### Error Umum

1. **File tidak valid**: Pastikan file berformat .csv
2. **Ukuran file terlalu besar**: Kompres atau split file
3. **Format data salah**: Gunakan template yang disediakan
4. **Nama duplikat**: Cek data yang sudah ada

### Tips

- Selalu gunakan template yang disediakan
- Periksa data sebelum upload
- Backup data sebelum import massal
- Test dengan data kecil terlebih dahulu

## Dependensi

- CodeIgniter 3.x
- PHP 7.4+
- MySQL/MariaDB
- Bootstrap 4
- FontAwesome 5

## Support

Untuk bantuan teknis, silakan hubungi administrator sistem.
