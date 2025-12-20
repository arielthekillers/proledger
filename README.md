# ProLedger 🚀
**Sistem Manajemen Keuangan Proyek Kolaboratif**

ProLedger adalah aplikasi berbasis web yang tangguh, ringan, dan aman untuk mengelola catatan keuangan proyek kolaboratif yang melibatkan banyak pemangku kepentingan (Investor/Pemodal dan Perusahaan). Aplikasi ini memberikan transparansi waktu nyata (real-time) mengenai dana proyek, pengeluaran, dan pembagian keuntungan.

![Pratinjau Dashboard ProLedger](https://via.placeholder.com/800x400?text=Preview+Dashboard+ProLedger)

## ✨ Fitur Utama

### 1. 📊 Dashboard Interaktif
- **Statistik Real-time**: Lihat total proyek aktif, investor terdaftar, total modal, dan laba/rugi bersih dalam sekilas.
- **Ringkasan Arus Kas**: Visualisasi ringkas total pemasukan vs pengeluaran.
- **Aksi Cepat**: Akses satu klik untuk membuat proyek, transaksi, atau investasi baru (Khusus Admin).

### 2. 💼 Manajemen Paket Kegiatan
- **Data Terpusat**: Kelola semua detail proyek termasuk Pagu (Plafon Anggaran), RAB, dan Status.
- **Perhitungan Otomatis**: Secara otomatis menghitung "Modal Pasal" (13% dari Pagu) dan "Modal Belanja".
- **Pelacakan Status**: Pantau siklus hidup proyek (Draft, Berjalan, Selesai).

### 3. 👥 Investasi Multi-Stakeholder
- **Manajemen Investor**: Daftar dan kelola "Pemodal" (Investor).
- **Modal Gabungan**: Mendukung banyak investor untuk satu proyek.
- **Perhitungan Bagi Hasil**: Sistem menghitung persentase kepemilikan setiap investor berdasarkan kontribusi mereka secara otomatis.

### 4. 💸 Pencatatan Keuangan
- **Transaksi Terperinci**: Catat setiap pemasukan dan pengeluaran dengan presisi.
- **Kategori**: Terorganisir berdasarkan "Pasal", "Modal Belanja", "Operasional", dll.
- **Transparansi**: Setiap transaksi ditautkan ke proyek tertentu dan memiliki stempel waktu.

### 5. 🛡️ Hak Akses Berbasis Peran (RBAC)
- **Admin**: Akses penuh ke semua fitur (Buat/Edit/Hapus).
- **Perusahaan**: Akses lihat-saja (view-only) untuk proyek yang ditugaskan kepada mereka.
- **Pemodal (Investor)**: Akses lihat-saja yang dibatasi secara ketat hanya untuk portofolio investasi dan laporan mereka sendiri.

### 6. 📈 Pelaporan (Rekap Keuangan)
- **Rekap Proyek**: Periksa kesehatan keuangan per proyek secara detail.
- **Laporan Investor**: Laporan personal yang menampilkan modal individu vs nilai saat ini.
- **Analisis Laba/Rugi**: Perhitungan otomatis saldo saat ini dan performa keuangan.

---

## 🛠️ Teknologi yang Digunakan
- **Backend**: Native PHP 8.2+ (Arsitektur MVC Kustom yang Ringan)
- **Database**: MySQL 8.0
- **Frontend**: HTML5, Tailwind CSS (UI Modern)
- **Ikon**: Ionicons
- **Keamanan**: 
    - Proteksi CSRF
    - Hashing Password (Bcrypt)
    - Otentikasi Berbasis Sesi
    - Konfigurasi Variabel Lingkungan (.env)

---

## 🚀 Instalasi & Pengaturan

### Prasyarat
- PHP >= 8.0
- MySQL
- Web Server (Apache/Nginx/IIS)

### Langkah-langkah

1.  **Clone Repository**
    ```bash
    git clone https://github.com/username-anda/proledger.git
    cd proledger
    ```

2.  **Konfigurasi Environment**
    Buat file `.env` dari contoh yang tersedia:
    ```bash
    cp .env.example .env
    ```
    Buka file `.env` dan sesuaikan konfigurasi database Anda:
    ```ini
    APP_ENV=local
    DB_DATABASE=proledger
    DB_USERNAME=root
    DB_PASSWORD=password_anda
    ```

3.  **Siapkan Database**
    - Buat database MySQL baru dengan nama `proledger`.
    - Import skema database dari `database/proledger.sql` (jika tersedia) atau jalankan migrasi.

4.  **Jalankan Aplikasi**
    Jika menggunakan server bawaan PHP:
    ```bash
    php -S localhost:8000 -t public
    ```
    Akses aplikasi di `http://localhost:8000`.

---

## 🔒 Catatan Keamanan
Proyek ini menggunakan sistem otentikasi kustom. Pastikan `APP_ENV` diatur ke `production` saat deploy ke server live untuk menonaktifkan tampilan debug dan pelaporan error.

---

## 📄 Lisensi
Privat / Proprietary.
