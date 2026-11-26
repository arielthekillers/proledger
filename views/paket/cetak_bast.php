<?php
/**
 * ProLedger - Template Cetak BAST (Berita Acara Serah Terima Pekerjaan)
 * Format standar formal pengadaan barang pemerintah / sekolah
 * Mendukung cetak tunggal per tujuan atau cetak gabungan (bulk multi-page)
 */

if (!class_exists('Uploader')) {
    require_once __DIR__ . '/../../helpers/Uploader.php';
}

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
$kopPdfUrl = null;
if (!empty($perusahaan['kopsurat'])) {
    if (preg_match('/\.pdf$/i', $perusahaan['kopsurat'])) {
        $kopPdfUrl = APP_URL . '/uploads/perusahaan/kopsurat/' . $perusahaan['kopsurat'];
    } else {
        $kopUrls = Uploader::getAllUrls($perusahaan['kopsurat'], 'perusahaan', 'kopsurat');
        $kopUrl = $kopUrls['large'] ?? ($kopUrls['medium'] ?? ($kopUrls['small'] ?? null));
    }
}
$hasKopBg = (!empty($kopUrl) || !empty($kopPdfUrl));

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
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            .page-sheet {
                box-shadow: none !important;
                border: none !important;
                margin: 0 !important;
                width: 210mm !important;
                min-height: 297mm !important;
                max-width: 210mm !important;
                page-break-after: always;
                break-after: page;
            }
            .page-sheet:last-of-type {
                page-break-after: auto;
                break-after: auto;
            }
            @page {
                size: A4 portrait;
                margin: 0;
            }
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            color: #111827;
            background-color: #f3f4f6;
        }
        .page-sheet {
            width: 210mm;
            min-height: 297mm;
            position: relative;
            box-sizing: border-box;
            overflow: hidden;
        }
        .kop-bg-full {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            z-index: 0;
            pointer-events: none;
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
            <!-- Main Sheet (A4 Proportion 210mm x 297mm) -->
            <div class="page-sheet mx-auto bg-white shadow-lg border border-slate-200 rounded-sm text-body"
                 style="<?= $hasKopBg ? 'padding: 44mm 18mm 20mm 18mm;' : 'padding: 15mm 18mm 20mm 18mm;' ?>">
                
                <!-- 1. KOP SURAT A4 FULL BACKGROUND -->
                <?php if (!empty($kopPdfUrl)): ?>
                    <img src="" alt="Kop Surat A4" class="kop-bg-full kop-pdf-bg-img hidden">
                <?php elseif (!empty($kopUrl)): ?>
                    <img src="<?= $kopUrl ?>" alt="Kop Surat A4" class="kop-bg-full">
                <?php endif; ?>

                <div class="relative z-10">
                <?php if (!$hasKopBg): ?>
                    <div class="border-b-2 border-black pb-3 mb-6 text-center">
                        <h1 class="font-bold text-lg md:text-xl uppercase tracking-wider text-black">
                            <?= htmlspecialchars($perusahaan['nama'] ?? '-') ?>
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
                <div class="mb-2 pl-4">
                    <table class="w-full text-left leading-tight" style="border-collapse: collapse;">
                        <tr>
                            <td class="w-20 align-top py-0 font-normal">Nama</td>
                            <td class="w-3 align-top py-0 text-center">:</td>
                            <td class="align-top py-0 font-bold">&nbsp;</td>
                        </tr>
                        <tr>
                            <td class="align-top py-0 font-normal">Jabatan</td>
                            <td class="align-top py-0 text-center">:</td>
                            <td class="align-top py-0">&nbsp;</td>
                        </tr>
                        <tr>
                            <td class="align-top py-0 font-normal">Instansi</td>
                            <td class="align-top py-0 text-center">:</td>
                            <td class="align-top py-0 font-semibold"><?= htmlspecialchars($curTujuan['nama_tujuan']) ?></td>
                        </tr>
                        <tr>
                            <td class="align-top py-0 font-normal">Alamat</td>
                            <td class="align-top py-0 text-center">:</td>
                            <td class="align-top py-0">
                                <?= htmlspecialchars($curTujuan['alamat'] ?: '-') ?>
                                <?= !empty($curTujuan['kab_kota']) ? ', ' . htmlspecialchars($curTujuan['kab_kota']) : '' ?>
                            </td>
                        </tr>
                    </table>
                </div>

                <p class="mb-2.5">
                    Selanjutnya, disebut <strong>PIHAK PERTAMA</strong>
                </p>

                <!-- 5. PIHAK KEDUA (PELAKSANA / PERUSAHAAN REKANAN) -->
                <div class="mb-2 pl-4">
                    <table class="w-full text-left leading-tight" style="border-collapse: collapse;">
                        <tr>
                            <td class="w-20 align-top py-0 font-normal">Nama</td>
                            <td class="w-3 align-top py-0 text-center">:</td>
                            <td class="align-top py-0 font-bold">
                                <?= htmlspecialchars($perusahaan['nama_direktur'] ?: ($perusahaan['penanggung_jawab'] ?: '-')) ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="align-top py-0 font-normal">Jabatan</td>
                            <td class="align-top py-0 text-center">:</td>
                            <td class="align-top py-0"><?= htmlspecialchars($perusahaan['jabatan_direktur'] ?: 'Direktur') ?></td>
                        </tr>
                        <tr>
                            <td class="align-top py-0 font-normal">Instansi</td>
                            <td class="align-top py-0 text-center">:</td>
                            <td class="align-top py-0 font-semibold"><?= htmlspecialchars($perusahaan['nama']) ?></td>
                        </tr>
                    </table>
                </div>

                <p class="mb-3.5">
                    Selanjutnya disebut <strong>PIHAK KEDUA</strong>
                </p>

                <!-- 6. DASAR PEKERJAAN & SURAT PESANAN -->
                <p class="mb-3 text-justify leading-relaxed">
                    Berdasarkan <strong>Surat Pesanan Nomor : <?= htmlspecialchars($paket['nomor_sp'] ?: ($paket['kode_paket'] ?: '-')) ?></strong> (<strong><?= htmlspecialchars($paket['nama_paket']) ?></strong>) PIHAK KEDUA telah menyelesaikan pekerjaan :
                </p>

                <!-- 7. TABEL BARANG YANG DISERAHKAN KE SEKOLAH / LOKASI INI -->
                <div class="mb-4 overflow-hidden">
                    <table class="w-full table-fixed border-collapse border border-black text-xs">
                        <thead>
                            <tr class="bg-slate-100 text-center font-bold">
                                <th class="border border-black py-1 px-2 w-10 text-center">NO.</th>
                                <th class="border border-black py-1 px-2.5 text-left">ITEM BARANG</th>
                                <th class="border border-black py-1 px-2 text-center w-32">MERK</th>
                                <th class="border border-black py-1 px-2 text-center w-24">JUMLAH</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($curItems)): ?>
                                <tr>
                                    <td colspan="4" class="border border-black py-2.5 text-center text-slate-500 italic">Tidak ada rincian barang.</td>
                                </tr>
                            <?php else: ?>
                                <?php $no = 1; foreach ($curItems as $it): ?>
                                    <tr>
                                        <td class="border border-black py-1 px-2 text-center align-top"><?= $no++ ?>.</td>
                                        <td class="border border-black py-1 px-2.5 font-semibold align-top break-words leading-snug"><?= htmlspecialchars($it['nama_komponen']) ?></td>
                                        <td class="border border-black py-1 px-2 text-center align-top break-words leading-snug"><?= htmlspecialchars(!empty($it['nama_barang']) ? $it['nama_barang'] : '-') ?></td>
                                        <td class="border border-black py-1 px-2 text-center align-top font-bold whitespace-nowrap">
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
                <div class="grid grid-cols-2 gap-8 text-center mt-8">
                    <!-- Pihak Pertama (Kosong untuk diisi manual pakai pena) -->
                    <div class="relative flex flex-col items-center">
                        <p class="font-bold uppercase tracking-wider text-xs md:text-sm">PIHAK PERTAMA</p>
                        <div class="h-24 w-full shrink-0"></div>
                        <p class="font-bold text-sm md:text-base h-6 min-w-[180px] border-b border-slate-800 inline-block">&nbsp;</p>
                        <p class="text-xs text-slate-600 h-5 mt-0.5">&nbsp;</p>
                    </div>

                    <!-- Pihak Kedua -->
                    <div class="relative flex flex-col items-center">
                        <p class="font-bold uppercase tracking-wider text-xs md:text-sm">PIHAK KEDUA</p>
                        <div class="relative h-24 w-full shrink-0">
                            <!-- Stamp / TTD Digital overlaps di atas teks PIHAK KEDUA & nama/jabatan tanpa menggeser jarak -->
                            <?php
                            $ttdUrls = !empty($perusahaan['ttd']) ? Uploader::getAllUrls($perusahaan['ttd'], 'perusahaan', 'ttd') : null;
                            $ttdSrc = $ttdUrls['medium'] ?? (!empty($perusahaan['stempel_ttd']) ? (APP_URL . '/' . ltrim($perusahaan['stempel_ttd'], '/')) : null);
                            $ttdScale = max(30, min(250, (int)($perusahaan['ttd_scale'] ?? 100)));
                            $ttdHeightPx = round(56 * ($ttdScale / 100));
                            $pihakKeduaNama = trim((string)($perusahaan['nama_direktur'] ?: ($perusahaan['penanggung_jawab'] ?? '')));
                            $pihakKeduaJabatan = trim((string)($perusahaan['jabatan_direktur'] ?: 'Direktur'));
                            ?>
                            <?php if ($ttdSrc): ?>
                                <img src="<?= htmlspecialchars($ttdSrc) ?>"
                                     alt="Tanda Tangan"
                                     style="height: <?= $ttdHeightPx ?>px; width: auto; max-width: none;"
                                     class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 object-contain pointer-events-none z-20">
                            <?php endif; ?>
                        </div>

                        <p class="font-bold text-sm md:text-base h-6 min-w-[180px] border-b border-slate-800 inline-block relative z-10">
                            <?= $pihakKeduaNama !== '' ? htmlspecialchars($pihakKeduaNama) : '&nbsp;' ?>
                        </p>
                        <p class="text-xs text-slate-600 h-5 mt-0.5 relative z-10">
                            <?= $pihakKeduaJabatan !== '' ? htmlspecialchars($pihakKeduaJabatan) : '&nbsp;' ?>
                        </p>
                    </div>
                </div>

                </div><!-- /.relative.z-10 -->
            </div>
        <?php endforeach; ?>
    </div>

    <?php if (!empty($kopPdfUrl)): ?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
    <script>
        (async function() {
            try {
                pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
                const pdf = await pdfjsLib.getDocument('<?= htmlspecialchars($kopPdfUrl, ENT_QUOTES, 'UTF-8') ?>').promise;
                const page = await pdf.getPage(1);
                const viewport = page.getViewport({ scale: 3.5 });
                const canvas = document.createElement('canvas');
                canvas.width = viewport.width;
                canvas.height = viewport.height;
                await page.render({ canvasContext: canvas.getContext('2d'), viewport }).promise;
                const dataUrl = canvas.toDataURL('image/png');
                document.querySelectorAll('.kop-pdf-bg-img').forEach(img => {
                    img.src = dataUrl;
                    img.classList.remove('hidden');
                });
            } catch (e) {
                console.error('Gagal merender PDF kop surat:', e);
            }
        })();
    </script>
    <?php endif; ?>
</body>
</html>
