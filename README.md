<div align="center">

# Velorium Accounting

**Sistem akuntansi berbasis Laravel untuk pencatatan keuangan akrual, konsinyasi, dan pelaporan keuangan sesuai standar akuntansi Indonesia.**

![Laravel](https://img.shields.io/badge/Laravel-11-FF2D20?style=flat&logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.2%2B-777BB4?style=flat&logo=php&logoColor=white)
![License](https://img.shields.io/badge/license-Unlicensed-lightgrey?style=flat)
![Status](https://img.shields.io/badge/status-in%20development-yellow?style=flat)

</div>

---

## Tentang Velorium Accounting

Velorium Accounting adalah sistem akuntansi double-entry yang dibangun dari nol di atas Laravel, dirancang untuk mendukung alur bisnis yang lebih kompleks dari sekadar pencatatan kas masuk-keluar: konsinyasi, multi cabang, penyusutan aset tetap, hingga rekonsiliasi bank. Setiap modul mengikuti alur dan rumus akuntansi yang berlaku di Indonesia, dengan **SAK EMKM** sebagai acuan utama.

## Daftar Isi

- [Fitur](#fitur)
- [Standar Akuntansi](#standar-akuntansi)
- [Tech Stack](#tech-stack)
- [Instalasi](#instalasi)
- [Struktur Project](#struktur-project)
- [Roadmap](#roadmap)
- [Kontribusi](#kontribusi)
- [Lisensi](#lisensi)

## Fitur

| Modul | Deskripsi |
|---|---|
| **Chart of Account** | COA berjenjang level 1/2/3 dengan validasi jurnal berimbang otomatis |
| **Buku Besar** | Laporan mutasi & saldo berjalan per akun, Neraca Saldo |
| **Kas & Bank** | Pencatatan kas/bank terpusat dengan Rekonsiliasi Bank (sisi buku vs sisi bank) |
| **Penjualan & Pembelian** | Transaksi reguler dengan retur serta diskon dagang dan diskon tunai |
| **Piutang** | Penagihan, pembayaran bertahap, cadangan kerugian piutang (metode aging) |
| **Kewajiban** | Hutang usaha & hutang berbunga, jadwal pembayaran cicilan |
| **Aset Tetap** | Penyusutan otomatis bulanan (garis lurus & saldo menurun ganda) |
| **Persediaan** | Penilaian stok FIFO / Rata-rata Tertimbang |
| **Konsinyasi** | Pengiriman barang titipan, laporan penjualan dari consignee, retur, komisi |
| **Perpajakan** | PPN Keluaran/Masukan dan PPh Final dengan tarif sebagai parameter |
| **Jurnal Penyesuaian** | Otomatis untuk biaya dibayar dimuka, pendapatan diterima dimuka, beban akrual |
| **Tutup Buku** | Jurnal penutup akhir periode dan jurnal pembalik awal periode |
| **Kontrol & Audit** | Alur persetujuan jurnal berjenjang (draft → approved → posted) dan audit trail |
| **Multi Cabang** | Laporan per cabang maupun konsolidasi, stok per gudang |
| **Laporan Keuangan** | Neraca, Laba Rugi, Perubahan Modal, Arus Kas, CALK |
| **Analisis Rasio** | Dashboard likuiditas, profitabilitas, solvabilitas, dan aktivitas |

## Standar Akuntansi

Seluruh alur dan rumus di sistem ini mengacu pada:

- **SAK EMKM** — Standar Akuntansi Keuangan Entitas Mikro, Kecil, dan Menengah
- **PSAK 16** — Aset Tetap, untuk perhitungan penyusutan
- **PSAK 14** — Persediaan, untuk metode penilaian stok (FIFO & Rata-rata Tertimbang)

## Tech Stack

| Layer | Teknologi |
|---|---|
| Framework | Laravel 11 |
| View | Blade, Tailwind CSS, Alpine.js |
| Database | MySQL / MariaDB |
| Autentikasi | Native Laravel Authentication dengan role-based access control berbasis Middleware |
| Export | Laravel Excel, DomPDF |

## Instalasi

### Prasyarat

- PHP 8.2 atau lebih baru
- Composer
- MySQL / MariaDB

### Langkah

```bash
git clone https://github.com/<username>/velorium-accounting.git
cd velorium-accounting
composer install
cp .env.example .env
php artisan key:generate
```

Sesuaikan koneksi database pada `.env`, lalu:

```bash
php artisan migrate --seed
php artisan serve
```

Aplikasi dapat diakses di `http://127.0.0.1:8000`.

## Struktur Project

```
app/
  Models/            Model tiap modul (COA, jurnal, kas, piutang, kewajiban, aset tetap, konsinyasi, dst.)
  Services/          Logika bisnis dan pencatatan jurnal otomatis, terpusat lewat JournalService
  Console/Commands/  Proses terjadwal (penyusutan, jurnal penyesuaian, tutup buku)
  Http/Controllers/  Controller per modul
  Http/Middleware/   Middleware otorisasi berbasis role
resources/views/     Blade views per modul dan komponen UI reusable
routes/web.php       Definisi route, dikelompokkan per modul dan middleware
```

## Kontribusi

Repository ini masih dalam tahap pengembangan aktif. Pull request dan masukan lewat Issues sangat terbuka.