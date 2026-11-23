<?php
/**
 * ProLedger - Public Read-Only Monitoring View (Tanpa Login)
 * Ditujukan untuk Dinas / Stakeholder memantau progres paket kolaborasi.
 * STRICT PRIVACY: Tidak menampilkan HPP, belanja toko, profit, atau potongan internal.
 */

$renderRing = function (int $pct, string $label, string $strokeHex, string $textClass) {
    $r = 15;
    $c = round(2 * M_PI * $r, 2);
    $offset = round($c * (1 - max(0, min(100, $pct)) / 100), 2);
    $dur = max(0.35, round(($pct / 100) * 1.1, 2));
    ?>
    <div class="flex flex-col items-center">
        <div class="relative w-11 h-11 flex items-center justify-center">
            <svg class="w-11 h-11 -rotate-90" viewBox="0 0 38 38">
                <circle cx="19" cy="19" r="<?= $r ?>" fill="none" stroke="#e2e8f0" stroke-width="3.2"></circle>
                <circle cx="19" cy="19" r="<?= $r ?>" fill="none" stroke="<?= $strokeHex ?>" stroke-width="3.2"
                        stroke-linecap="round"
                        stroke-dasharray="<?= $c ?>"
                        stroke-dashoffset="<?= $c ?>"
                        data-ring-target="<?= $offset ?>"
                        style="transition: stroke-dashoffset <?= $dur ?>s cubic-bezier(0.22, 1, 0.36, 1);"></circle>
            </svg>
            <span class="absolute text-[9px] font-extrabold <?= $textClass ?>"><?= $pct ?>%</span>
        </div>
        <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider mt-0.5"><?= $label ?></span>
    </div>
    <?php
};

$renderTableRing = function (int $pct, string $strokeHex, string $textClass) {
    $r = 12;
    $c = round(2 * M_PI * $r, 2);
    $offset = round($c * (1 - max(0, min(100, $pct)) / 100), 2);
    $dur = max(0.35, round(($pct / 100) * 1.1, 2));
    ?>
    <div class="relative w-8 h-8 inline-flex items-center justify-center">
        <svg class="w-8 h-8 -rotate-90" viewBox="0 0 32 32">
            <circle cx="16" cy="16" r="<?= $r ?>" fill="none" stroke="#e2e8f0" stroke-width="2.8"></circle>
            <circle cx="16" cy="16" r="<?= $r ?>" fill="none" stroke="<?= $strokeHex ?>" stroke-width="2.8"
                    stroke-linecap="round"
                    stroke-dasharray="<?= $c ?>"
                    stroke-dashoffset="<?= $c ?>"
                    data-ring-target="<?= $offset ?>"
                    style="transition: stroke-dashoffset <?= $dur ?>s cubic-bezier(0.22, 1, 0.36, 1);"></circle>
        </svg>
        <span class="absolute text-[8px] font-extrabold <?= $textClass ?>"><?= $pct ?>%</span>
    </div>
    <?php
};
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title) ?> - SimPaket Monitoring</title>
    <link rel="icon" type="image/svg+xml" href="<?= APP_URL ?>/favicon.svg">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="bg-slate-100 text-slate-800 antialiased text-sm min-h-screen">
    <!-- Top Public Bar -->
    <header class="bg-white/95 backdrop-blur-md border-b border-slate-200 sticky top-0 z-30">
        <div class="max-w-7xl mx-auto px-4 md:px-6 h-14 flex items-center justify-between gap-3">
            <div class="flex items-center gap-2.5">
                <img src="<?= APP_URL ?>/favicon.svg" alt="SimPaket" class="w-8 h-8 rounded-lg shadow-xs">
                <div>
                    <span class="font-bold text-base text-slate-800">Sim<span class="text-blue-600">Paket</span></span>
                    <span class="ml-1.5 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                        Monitoring Publik (Read-Only)
                    </span>
                </div>
            </div>
            <div class="text-[11px] text-slate-400 hidden sm:block">
                Diperbarui otomatis &bull; <?= date('d M Y H:i') ?>
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 md:px-6 py-6">
        <!-- Hero Card -->
        <div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 p-5 mb-4">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-5">
                <div class="flex items-start gap-4 flex-1 min-w-0">
                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-indigo-500 to-blue-600 text-white flex items-center justify-center text-2xl shadow-sm shrink-0">
                        <ion-icon name="analytics"></ion-icon>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 flex-wrap mb-1">
                            <h1 class="text-lg md:text-xl font-extrabold text-slate-900 tracking-tight">
                                <?= htmlspecialchars($kolab['nama']) ?>
                            </h1>
                            <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                TA <?= (int)$kolab['tahun_anggaran'] ?>
                            </span>
                        </div>
                        <p class="text-xs text-slate-400 mb-2 flex items-center gap-2 flex-wrap">
                            <?php if (!empty($kolab['instansi'])): ?>
                                <span class="text-slate-600 font-semibold"><?= htmlspecialchars($kolab['instansi']) ?></span>
                                <span class="text-slate-300">&bull;</span>
                            <?php endif; ?>
                            <span><?= $summary['total_perusahaan'] ?> Perusahaan Pelaksana</span>
                            <span class="text-slate-300">&bull;</span>
                            <span><?= $summary['total_paket'] ?> Paket</span>
                            <span class="text-slate-300">&bull;</span>
                            <span class="font-mono font-bold text-slate-700"><?= formatRupiah($summary['total_kontrak']) ?></span>
                        </p>
                        <?php if (!empty($kolab['deskripsi'])): ?>
                            <p class="text-xs text-slate-600 bg-slate-50 px-3 py-2 rounded-xl border border-slate-100 leading-relaxed">
                                <?= nl2br(htmlspecialchars($kolab['deskripsi'])) ?>
                            </p>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row lg:flex-col items-start sm:items-center lg:items-end justify-between gap-3 pt-4 lg:pt-0 border-t lg:border-t-0 lg:border-l border-slate-100 lg:pl-6 shrink-0">
                    <div class="flex items-center gap-3.5">
                        <?php $renderRing($summary['order_pct'], 'Order', '#f59e0b', 'text-amber-600'); ?>
                        <?php $renderRing($summary['dist_pct'], 'Distribusi', '#6366f1', 'text-indigo-600'); ?>
                        <?php $renderRing($summary['dok_pct'], 'Dokumen', '#10b981', 'text-emerald-600'); ?>
                        <?php $renderRing($summary['bast_pct'], 'BAST', '#a855f7', 'text-purple-600'); ?>
                    </div>
                    <div class="flex items-center gap-1.5 flex-wrap text-[11px]">
                        <span class="px-2 py-0.5 rounded-full font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                            <?= $summary['paket_selesai'] ?> Cair/Selesai
                        </span>
                        <span class="px-2 py-0.5 rounded-full font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                            <?= $summary['paket_proses'] ?> Proses
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Per-Company Cards -->
        <?php if (!empty($companyStats)): ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-3 mb-4">
                <?php foreach ($companyStats as $cs): ?>
                    <button type="button" onclick="filterPublicCompany('<?= htmlspecialchars(strtolower($cs['nama']), ENT_QUOTES) ?>')"
                            class="text-left bg-white rounded-2xl shadow-xs border border-slate-200/80 p-3.5 hover:border-blue-300 transition-all cursor-pointer">
                        <div class="flex items-center justify-between gap-2 mb-1.5">
                            <span class="font-bold text-slate-800 text-xs truncate"><?= htmlspecialchars($cs['nama']) ?></span>
                            <span class="px-1.5 py-0.5 rounded-md bg-blue-50 text-blue-700 text-[10px] font-bold shrink-0">
                                <?= $cs['total_paket'] ?> Paket
                            </span>
                        </div>
                        <div class="font-mono font-bold text-slate-900 text-xs mb-2"><?= formatRupiah($cs['total_kontrak']) ?></div>
                        <div class="w-full h-1.5 bg-slate-100 rounded-full overflow-hidden mb-1.5">
                            <div class="h-full bg-gradient-to-r from-blue-500 to-indigo-600 rounded-full" style="width: <?= $cs['avg_pct'] ?>%"></div>
                        </div>
                        <div class="flex items-center justify-between text-[10px] text-slate-400">
                            <span>Progres <?= $cs['avg_pct'] ?>%</span>
                            <span>BAST <?= $cs['bast_pct'] ?>%</span>
                        </div>
                    </button>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <!-- Table -->
        <div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 overflow-hidden">
            <div class="p-3.5 px-5 border-b border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-3">
                <h3 class="font-bold text-slate-800 text-xs uppercase tracking-wider">
                    Daftar Paket Kegiatan (<span id="pubVisibleCount"><?= count($pakets) ?></span>)
                </h3>
                <div class="flex items-center gap-2 flex-wrap">
                    <input type="text" id="pubSearch" onkeyup="filterPublicTable()" placeholder="Cari paket atau kode..."
                           class="px-3 py-1.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-blue-500">
                    <select id="pubCompany" onchange="filterPublicTable()"
                            class="px-2.5 py-1.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700 cursor-pointer">
                        <option value="">Semua Perusahaan</option>
                        <?php foreach ($companyStats as $cs): ?>
                            <option value="<?= htmlspecialchars(strtolower($cs['nama'])) ?>"><?= htmlspecialchars($cs['nama']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50/80 text-[10px] uppercase tracking-wider text-slate-500 font-bold border-b border-slate-200/80">
                        <tr>
                            <th class="py-3 px-3 text-center">Kode</th>
                            <th class="py-3 px-3">Nama Paket</th>
                            <th class="py-3 px-3">Perusahaan Pelaksana</th>
                            <th class="py-3 px-3 text-right">Nilai Kontrak</th>
                            <th class="py-3 px-3 text-center">Komponen</th>
                            <th class="py-3 px-2 text-center">Order</th>
                            <th class="py-3 px-2 text-center">Dist</th>
                            <th class="py-3 px-2 text-center">Dok</th>
                            <th class="py-3 px-2 text-center">BAST</th>
                            <th class="py-3 px-3 text-center">Pencairan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php foreach ($pakets as $p): ?>
                            <tr class="hover:bg-slate-50/70 pub-row"
                                data-search="<?= htmlspecialchars(strtolower(($p['nama_paket'] ?? '') . ' ' . ($p['kode_paket'] ?? ''))) ?>"
                                data-company="<?= htmlspecialchars(strtolower($p['perusahaan_nama'])) ?>"
                                data-kontrak="<?= $p['acuan'] ?>">
                                <td class="py-2.5 px-3 text-center">
                                    <span class="px-2 py-0.5 font-mono font-bold text-[11px] bg-slate-100 text-slate-700 rounded-md">
                                        <?= htmlspecialchars($p['kode_paket'] ?: '#' . $p['id']) ?>
                                    </span>
                                </td>
                                <td class="py-2.5 px-3 min-w-[220px]">
                                    <div class="font-bold text-slate-800 text-xs"><?= htmlspecialchars($p['nama_paket']) ?></div>
                                    <div class="text-[10px] text-slate-400 mt-0.5">TA <?= (int)($p['tahun_anggaran'] ?? date('Y')) ?></div>
                                </td>
                                <td class="py-2.5 px-3 whitespace-nowrap font-semibold text-slate-700"><?= htmlspecialchars($p['perusahaan_nama']) ?></td>
                                <td class="py-2.5 px-3 text-right font-mono font-bold text-slate-900 whitespace-nowrap"><?= formatRupiah($p['acuan']) ?></td>
                                <td class="py-2.5 px-3 text-center whitespace-nowrap">
                                    <div class="font-bold text-slate-800 text-xs"><?= $p['item_count'] ?> Item</div>
                                    <div class="text-[10px] text-slate-400"><?= rtrim(rtrim(number_format($p['item_qty'], 2, ',', '.'), '0'), ',') ?> Unit</div>
                                </td>
                                <td class="py-2 px-2 text-center whitespace-nowrap"><?= renderCircularProgress($p['pesan_done'] ?? 0, $p['total_dist'] ?? 0, 'pemesanan') ?></td>
                                <td class="py-2 px-2 text-center whitespace-nowrap"><?= renderCircularProgress($p['dist_done'] ?? 0, $p['total_dist'] ?? 0, 'distribusi') ?></td>
                                <td class="py-2 px-2 text-center whitespace-nowrap"><?= renderCircularProgress($p['dok_done'] ?? 0, $p['total_dist'] ?? 0, 'dokumentasi') ?></td>
                                <td class="py-2 px-2 text-center whitespace-nowrap"><?= renderCircularProgress($p['bast_done'] ?? 0, $p['total_bast'] ?? 0, 'bast') ?></td>
                                <td class="py-2.5 px-3 text-center whitespace-nowrap"><?= getStatusBadge($p['status']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <?php if (!empty($pakets)): ?>
                        <tfoot class="bg-slate-50/95 border-t-2 border-slate-200 font-bold text-xs">
                            <tr>
                                <td colspan="3" class="py-3 px-3 text-slate-700 uppercase text-[11px]">
                                    Overall Total (<span id="pubFootCount"><?= $summary['total_paket'] ?></span> Paket)
                                </td>
                                <td class="py-3 px-3 text-right font-mono text-slate-900 whitespace-nowrap" id="pubFootKontrak">
                                    <?= formatRupiah($summary['total_kontrak']) ?>
                                </td>
                                <td colspan="6"></td>
                            </tr>
                        </tfoot>
                    <?php endif; ?>
                </table>
            </div>
        </div>
    </main>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        requestAnimationFrame(() => {
            document.querySelectorAll('circle[data-ring-target]').forEach(el => {
                el.style.strokeDashoffset = el.getAttribute('data-ring-target');
            });
        });
    });
    function filterPublicCompany(comp) {
        const sel = document.getElementById('pubCompany');
        if (!sel) return;
        sel.value = (sel.value === comp) ? '' : comp;
        filterPublicTable();
    }
    function filterPublicTable() {
        const q = (document.getElementById('pubSearch')?.value || '').toLowerCase().trim();
        const comp = document.getElementById('pubCompany')?.value || '';
        let vis = 0, sKontrak = 0;
        document.querySelectorAll('.pub-row').forEach(row => {
            const show = (!q || (row.dataset.search || '').includes(q)) && (!comp || (row.dataset.company || '') === comp);
            row.style.display = show ? '' : 'none';
            if (show) {
                vis++;
                sKontrak += parseFloat(row.dataset.kontrak || 0);
            }
        });
        document.getElementById('pubVisibleCount').textContent = vis;
        const fc = document.getElementById('pubFootCount');
        if (fc) fc.textContent = vis;
        const fk = document.getElementById('pubFootKontrak');
        if (fk) fk.textContent = 'Rp ' + Math.round(sKontrak).toLocaleString('id-ID');
    }
    </script>
</body>
</html>
