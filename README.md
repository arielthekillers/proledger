# PaketFlow 📦🚀
**Sistem Terintegrasi Manajemen Paket Kegiatan, Pengadaan, Logistik Distribusi & BAST**

**PaketFlow** adalah platform web modern, ringan, dan aman untuk mengelola siklus hidup paket pekerjaan pengadaan barang/jasa secara menyeluruh. Dari kontrak kerja (SPK), simulasi potongan pajak (DPP -11% PPN, Pasal & Fee), spesifikasi item, alokasi distribusi barang ke sekolah/instansi penerima, hingga pencetakan Berita Acara Serah Terima (BAST) otomatis.

---

## ✨ Fitur Unggulan

### 1. 💼 Manajemen Paket Pekerjaan
- **Kontrak & Anggaran**: Pengelolaan nomor SP/SPK, Pagu awal, DPP (-11% PPN), dan kalkulasi otomatis potongan pasal (13%) & fee admin (2%).
- **Multi Perusahaan & Branding**: Pengaturan logo, stempel, dan kop surat resmi per perusahaan pelaksana.
- **Grouping Program**: Pengelompokan paket berdasarkan dinas atau program (misal: Dinas Pendidikan, Dinas Kesehatan).
- **Status Tracking**: Filter status interaktif (*Semua, Proses, Cair, Belum*) dengan hitungan langsung.

### 2. 📋 Item & Spesifikasi Pengadaan
- **Katalog Spesifikasi**: Input rincian nama item, spesifikasi teknis, volume, harga satuan, dan total pagu item.
- **Perhitungan Alokasi Dinamis**: Sistem otomatis menghitung kuota teralokasi dan sisa unit barang yang belum didistribusikan.

### 3. 🚚 Logistik & Alokasi Distribusi
- **Alokasi Cepat**: Alokasikan unit barang langsung dari daftar spesifikasi ke lokasi tujuan penerima tanpa pusing memilih ulang item.
- **Master Tujuan Global**: Terhubung dengan master wilayah kabupaten/kota, sekolah, dan instansi dinas dengan opsi penambahan instansi cepat.
- **Tracking Logistik 3-Tahap**:
  - **Rute Pengiriman**: Status pengiriman (Langsung ke Lokasi / Ke Rumah/Kantor).
  - **Status Pemesanan**: Pelacakan status order barang.
  - **Status Penerimaan**: Konfirmasi fisik penerimaan barang di titik tujuan.
- **Export CSV**: Unduh rekapitulasi data distribusi lengkap siap pakai di Excel.

### 4. 📄 Dokumen & BAST Otomatis
- **Cetak BAST Satuan**: Format resmi Berita Acara Serah Terima lengkap dengan nomor kontrak, tanggal, data PIC penerima, kop surat perusahaan, dan tanda tangan.
- **Cetak BAST Massal**: Cetak seluruh BAST untuk semua titik distribusi dalam 1 dokumen PDF multi-halaman dengan pemisahan halaman otomatis (A4).
- **Arsip Dokumen**: Upload dan kelola berkas lampiran (PDF/gambar) dengan proteksi folder upload.

---

## 🛠️ Arsitektur & Teknologi
- **Backend**: Native PHP 8.2+ (MVC Router Ringan & Cepat)
- **Database**: MariaDB 11 / MySQL 8.0 (PDO Singleton, Prepared Statements, Cascade Deletion)
- **Styling**: Tailwind CSS v3 (Terkolpilasi Offline di `public/css/app.min.css`)
- **Icons**: Ionicons 7
- **Deployment**: Dockerfile & `docker-compose.yml` siap rilis di **Coolify**, VPS, atau Docker Host.

---

## 🚀 Panduan Instalasi Cepat (Lokal)

1. **Clone Repository**:
   ```bash
   git clone https://github.com/arielthekillers/proledger.git paketflow
   cd paketflow
   ```

2. **Setup Environment**:
   Salin file `.env.example` menjadi `.env`:
   ```bash
   cp .env.example .env
   ```
   Sesuaikan konfigurasi database `DB_HOST`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`.

3. **Import Database**:
   Import file skema bersih ke MySQL Anda:
   ```bash
   mysql -u root -p paketflow < database/proledger_production.sql
   ```

4. **Jalankan Server**:
   ```bash
   php -S localhost:8000 -t public
   ```
   Buka di browser: `http://localhost:8000`

---

## 🐳 Deployment di Coolify
1. Buat **New Resource** → **Application** → Hubungkan repo ini.
2. Coolify akan otomatis mendeteksi `Dockerfile`.
3. Set volume persisten:
   ```text
   paketflow_uploads:/var/www/html/public/uploads
   ```
4. Tambahkan Environment Variables dari `.env.example`.
5. Klik **Deploy**!

---

&copy; 2026 **PaketFlow** by [Sintesa Corp](https://sintesacorp.id).
