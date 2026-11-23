<?php
/**
 * ProLedger - Template Cetak BAST (Berita Acara Serah Terima Pekerjaan)
 * Format standar formal pengadaan barang pemerintah / sekolah
 * Mendukung cetak tunggal per tujuan atau cetak gabungan (bulk multi-page)
 */

// Helper convert tanggal ke format Indonesia: "Jumat, 6 November 2026"
function tanggalIndonesia($tgl) {
    if (empty($tgl)) $tgl = date('Y-m-d');
    $hariArr = [
        'Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa',
        'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'
    ];
    $bulanArr = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni',
        7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
    ];
    
    $timestamp = strtotime($tgl);
    $hari = $hariArr[date('l', $timestamp)] ?? date('l', $timestamp);
    $tglNum = date('j', $timestamp);
    $bulan = $bulanArr[(int)date('n', $timestamp)] ?? date('F', $timestamp);
    $tahun = date('Y', $timestamp);
    
    return "{$hari}, {$tglNum} {$bulan} {$tahun}";
}

$isBulk = !empty($isBulk);
$tglBast = !empty($bastDate) ? $bastDate : date('Y-m-d');
$hariTgl = tanggalIndonesia($tglBast);

require_once HELPER_PATH . '/Uploader.php';
$kopUrl = null;
if (!empty($perusahaan['kopsurat'])) {
    $kopUrls = Uploader::getAllUrls($perusahaan['kopsurat'], 'perusahaan', 'kopsurat');
    $kopUrl = $kopUrls['large'] ?? ($kopUrls['medium'] ?? ($kopUrls['small'] ?? null));
}

// Menyiapkan daftar data BAST yang akan dicetak
$bastSheets = [];
if ($isBulk && !empty($groupedList)) {
    foreach ($groupedList as $grp) {
        $bastSheets[] = [
            'tujuan' => [
                'nama_tujuan' => $grp['nama_tujuan'],
                'kategori'    => $grp['kategori'],
                'nama_pic'    => $grp['nama_pic'] ?: 'Kepala Sekolah',
                'jabatan_pic' => $grp['jabatan_pic'] ?: 'Kepala Sekolah',
                'no_kontak'   => $grp['no_kontak'] ?: '',
                'alamat'      => $grp['alamat'] ?: '',
                'kab_kota'    => $grp['kab_kota'] ?? ''
            ],
            'items' => $grp['items'] ?? []
        ];
    }
} else {
    $bastSheets[] = [
        'tujuan' => $tujuan ?? [],
        'items'  => $items ?? []
    ];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?= $isBulk ? 'BAST Gabungan (' . count($bastSheets) . ' Titik)' : 'BAST - ' . htmlspecialchars($tujuan['nama_tujuan'] ?? 'Penerima') ?> - <?= htmlspecialchars($paket['nama_paket'] ?? '') ?>
    </title>
    <link rel="stylesheet" href="<?= APP_URL ?>/css/app.min.css">
    <?php if (defined('APP_ENV') && APP_ENV === 'local'): ?>
        <script src="https://cdn.tailwindcss.com"></script>
    <?php endif; ?>
    <style>
        @media print {
            .no-print { display: none !important; }
            body { 
                background: white !important; 
                padding: 0 !important;
                margin: 0 !important;
                color: black !important;
            }
            .page-sheet {
                box-shadow: none !important;
                border: none !important;
                margin: 0 !important;
                padding: 0 !important;
                width: 100% !important;
                max-width: 100% !important;
                page-break-after: always;
                break-after: page;
            }
            .page-sheet:last-of-type {
                page-break-after: auto;
                break-after: auto;
            }
            @page {
                size: A4 portrait;
                margin: 6mm 15mm 12mm 15mm;
            }
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            color: #111827;
            background-color: #f3f4f6;
        }
        .text-body {
            font-size: 11pt;
            line-height: 1.5;
        }
    </style>
</head>
<body class="py-6 px-4">

    <!-- Top Action Toolbar (No Print) -->
    <div class="no-print max-w-4xl mx-auto mb-4 flex items-center justify-between bg-white p-4 rounded-xl shadow-sm border border-slate-200">
        <div class="flex items-center gap-3">
            <a href="<?= APP_URL ?>/paket/detail/<?= $paket['kode_paket'] ?: $paket['id'] ?>#dokumen" 
               class="px-3.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-lg text-xs flex items-center gap-1.5 transition-colors">
                <span>&larr; Kembali ke Paket</span>
            </a>
            <span class="text-xs text-slate-500 font-sans">
                <?php if ($isBulk): ?>
                    BAST Gabungan: <strong class="text-slate-800"><?= count($bastSheets) ?> Titik Penerima</strong>
                <?php else: ?>
                    BAST untuk: <strong class="text-slate-800"><?= htmlspecialchars($tujuan['nama_tujuan'] ?? '') ?></strong>
                <?php endif; ?>
            </span>
        </div>
        <div class="flex items-center gap-2">
            <button type="button" onclick="window.print()" 
                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg text-xs shadow flex items-center gap-2 transition-all cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                <span>Cetak / Simpan PDF <?= $isBulk ? '(' . count($bastSheets) . ' Halaman)' : '' ?></span>
            </button>
        </div>
    </div>

    <!-- Container Semua Lembar BAST -->
    <div class="space-y-6">
        <?php foreach ($bastSheets as $index => $sheet): ?>
            <?php 
                $curTujuan = $sheet['tujuan'];
                $curItems = $sheet['items'];
            ?>
            <!-- Main Sheet (A4 Proportion) -->
            <div class="page-sheet max-w-4xl mx-auto bg-white pt-5 px-10 pb-10 md:pt-6 md:px-14 md:pb-12 shadow-lg border border-slate-200 rounded-sm text-body">
                
                <!-- 1. KOP SURAT PERUSAHAAN REKANAN -->
                <?php if (!empty($kopUrl)): ?>
                    <div class="mb-4 text-center">
                        <img src="<?= $kopUrl ?>" alt="Kop Surat" class="max-h-32 mx-auto w-full object-contain">
                    </div>
                <?php else: ?>
                    <div class="border-b-2 border-black pb-3 mb-6 text-center">
                        <h1 class="font-bold text-lg md:text-xl uppercase tracking-wider text-black">
                            <?= htmlspecialchars($perusahaan['nama'] ?? 'CV. KARSA KREASI BERDIKARI') ?>
                        </h1>
                        <?php if (!empty($perusahaan['alamat'])): ?>
                            <p class="text-xs mt-0.5 text-black"><?= htmlspecialchars($perusahaan['alamat']) ?></p>
                        <?php endif; ?>
                        <?php if (!empty($perusahaan['telepon']) || !empty($perusahaan['email'])): ?>
                            <p class="text-xs text-black">
                                <?= !empty($perusahaan['telepon']) ? 'Telp: ' . htmlspecialchars($perusahaan['telepon']) : '' ?>
                                <?= (!empty($perusahaan['telepon']) && !empty($perusahaan['email'])) ? ' | ' : '' ?>
                                <?= !empty($perusahaan['email']) ? 'Email: ' . htmlspecialchars($perusahaan['email']) : '' ?>
                            </p>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <!-- 2. JUDUL BAST -->
                <div class="text-center mb-6">
                    <h2 class="font-bold text-base md:text-lg uppercase underline tracking-wide">
                        BERITA ACARA SERAH TERIMA PEKERJAAN
                    </h2>
                </div>

                <!-- 3. KALIMAT PEMBUKA -->
                <p class="mb-4 text-justify leading-relaxed">
                    Pada hari ini <strong><?= $hariTgl ?></strong> bertempat di <strong><?= htmlspecialchars($curTujuan['nama_tujuan']) ?></strong>, telah dilaksanakan serah terima pekerjaan, antara :
                </p>

                <!-- 4. PIHAK PERTAMA (PENERIMA / SEKOLAH / INSTANSI) -->
                <div class="mb-4 pl-4 space-y-1">
                    <table class="w-full text-left" style="border-collapse: collapse;">
                        <tr>
                            <td class="w-24 align-top py-0.5 font-normal">Nama</td>
                            <td class="w-4 align-top py-0.5 text-center">:</td>
                            <td class="align-top py-0.5 font-bold"><?= htmlspecialchars($curTujuan['nama_pic'] ?: 'Kepala Sekolah') ?></td>
                        </tr>
                        <tr>
                            <td class="align-top py-0.5 font-normal">Jabatan</td>
                            <td class="align-top py-0.5 text-center">:</td>
                            <td class="align-top py-0.5"><?= htmlspecialchars($curTujuan['jabatan_pic'] ?: 'Kepala Sekolah') ?></td>
                        </tr>
                        <tr>
                            <td class="align-top py-0.5 font-normal">Instansi</td>
                            <td class="align-top py-0.5 text-center">:</td>
                            <td class="align-top py-0.5 font-semibold"><?= htmlspecialchars($curTujuan['nama_tujuan']) ?></td>
                        </tr>
                        <tr>
                            <td class="align-top py-0.5 font-normal">Alamat</td>
                            <td class="align-top py-0.5 text-center">:</td>
                            <td class="align-top py-0.5">
                                <?= htmlspecialchars($curTujuan['alamat'] ?: '-') ?>
                                <?= !empty($curTujuan['kab_kota']) ? ', ' . htmlspecialchars($curTujuan['kab_kota']) : '' ?>
                            </td>
                        </tr>
                    </table>
                </div>

                <p class="mb-4">
                    Selanjutnya, disebut <strong>PIHAK PERTAMA</strong>
                </p>

                <!-- 5. PIHAK KEDUA (PELAKSANA / PERUSAHAAN REKANAN) -->
                <div class="mb-4 pl-4 space-y-1">
                    <table class="w-full text-left" style="border-collapse: collapse;">
                        <tr>
                            <td class="w-24 align-top py-0.5 font-normal">Nama</td>
                            <td class="w-4 align-top py-0.5 text-center">:</td>
                            <td class="align-top py-0.5 font-bold">
                                <?= htmlspecialchars($perusahaan['nama_direktur'] ?: ($perusahaan['penanggung_jawab'] ?? 'Muhammad Rifki')) ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="align-top py-0.5 font-normal">Jabatan</td>
                            <td class="align-top py-0.5 text-center">:</td>
                            <td class="align-top py-0.5"><?= htmlspecialchars($perusahaan['jabatan_direktur'] ?: 'Direktur') ?></td>
                        </tr>
                        <tr>
                            <td class="align-top py-0.5 font-normal">Instansi</td>
                            <td class="align-top py-0.5 text-center">:</td>
                            <td class="align-top py-0.5 font-semibold"><?= htmlspecialchars($perusahaan['nama']) ?></td>
                        </tr>
                    </table>
                </div>

                <p class="mb-5">
                    Selanjutnya disebut <strong>PIHAK KEDUA</strong>
                </p>

                <!-- 6. DASAR PEKERJAAN & SURAT PESANAN -->
                <p class="mb-4 text-justify leading-relaxed">
                    Berdasarkan <strong>Surat Pesanan Nomor : <?= htmlspecialchars($paket['nomor_sp'] ?: ($paket['kode_paket'] ?: '-')) ?></strong> (<strong><?= htmlspecialchars($paket['nama_paket']) ?></strong>) PIHAK KEDUA telah menyelesaikan pekerjaan :
                </p>

                <!-- 7. TABEL BARANG YANG DISERAHKAN KE SEKOLAH / LOKASI INI -->
                <div class="mb-5 overflow-hidden">
                    <table class="w-full border-collapse border border-black text-xs md:text-sm">
                        <thead>
                            <tr class="bg-slate-100 text-center font-bold">
                                <th class="border border-black py-1.5 px-2 w-10 text-center">NO.</th>
                                <th class="border border-black py-1.5 px-3 text-left">ITEM BARANG</th>
                                <th class="border border-black py-1.5 px-3 text-center w-20">MERK</th>
                                <th class="border border-black py-1.5 px-3 text-left">SPESIFIKASI</th>
                                <th class="border border-black py-1.5 px-3 text-center w-24">JUMLAH</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($curItems)): ?>
                                <tr>
                                    <td colspan="5" class="border border-black py-3 text-center text-slate-500 italic">Tidak ada rincian barang.</td>
                                </tr>
                            <?php else: ?>
                                <?php $no = 1; foreach ($curItems as $it): ?>
                                    <tr>
                                        <td class="border border-black py-1.5 px-2 text-center align-top"><?= $no++ ?>.</td>
                                        <td class="border border-black py-1.5 px-3 font-semibold align-top"><?= htmlspecialchars($it['nama_komponen']) ?></td>
                                        <td class="border border-black py-1.5 px-3 text-center align-top">-</td>
                                        <td class="border border-black py-1.5 px-3 align-top leading-relaxed"><?= nl2br(htmlspecialchars($it['spesifikasi'] ?: '-')) ?></td>
                                        <td class="border border-black py-1.5 px-3 text-center align-top font-bold whitespace-nowrap">
                                            <?= (float)$it['jumlah'] ?> <?= htmlspecialchars($it['satuan'] ?: 'Unit') ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- 8. PENUTUP -->
                <p class="mb-2 text-justify leading-relaxed">
                    Selanjutnya pihak kedua menyerahkan seluruh hasil pekerjaan beserta dokumen atau kelengkapan pendukung kepada PIHAK PERTAMA.
                </p>
                <p class="mb-8 text-justify leading-relaxed">
                    Demikian Berita Acara Serah Terima Pekerjaan ini dibuat dengan sebenar - benarnya untuk dapat dipergunakan sebagaimana mestinya.
                </p>

                <!-- 9. TANDA TANGAN KEDUA PIHAK -->
                <div class="grid grid-cols-2 gap-8 text-center mt-6">
                    <!-- Pihak Pertama -->
                    <div>
                        <p class="font-bold mb-16 uppercase tracking-wider text-xs md:text-sm">PIHAK PERTAMA</p>
                        <p class="font-bold underline text-sm md:text-base">
                            <?= htmlspecialchars($curTujuan['nama_pic'] ?: 'Kepala Sekolah') ?>
                        </p>
                        <p class="text-xs text-slate-600">
                            <?= htmlspecialchars($curTujuan['jabatan_pic'] ?: 'Kepala Sekolah') ?>
                        </p>
                    </div>

                    <!-- Pihak Kedua -->
                    <div class="relative">
                        <p class="font-bold mb-16 uppercase tracking-wider text-xs md:text-sm">PIHAK KEDUA</p>
                        
                        <!-- Stamp / TTD Digital jika ada -->
                        <?php if (!empty($perusahaan['stempel_ttd'])): ?>
                            <img src="<?= APP_URL . '/' . ltrim($perusahaan['stempel_ttd'], '/') ?>" 
                                 alt="Stempel & TTD" 
                                 class="absolute -top-4 left-1/2 transform -translate-x-1/2 max-h-24 opacity-90 pointer-events-none">
                        <?php endif; ?>

                        <p class="font-bold underline text-sm md:text-base">
                            <?= htmlspecialchars($perusahaan['nama_direktur'] ?: ($perusahaan['penanggung_jawab'] ?? 'Muhammad Rifki')) ?>
                        </p>
                        <p class="text-xs text-slate-600">
                            <?= htmlspecialchars($perusahaan['jabatan_direktur'] ?: 'Direktur') ?>
                        </p>
                    </div>
                </div>

            </div>
        <?php endforeach; ?>
    </div>

</body>
</html>
