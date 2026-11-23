<?php
/**
 * ProLedger - Dashboard Monitoring Kolaborasi
 * Menampilkan progres gabungan lintas perusahaan & nilai kontrak.
 * STRICT PRIVACY: Tidak menampilkan HPP, belanja toko, profit, atau potongan internal.
 */

$shareUrl = APP_URL . '/monitoring/' . $kolab['share_token'];

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

<!-- ========================================== -->
<!-- 1. TOP BAR & ACTIONS                       -->
<!-- ========================================== -->
<div class="mb-4 flex flex-wrap items-center justify-between gap-3">
    <div class="flex items-center gap-2 flex-wrap">
        <a href="<?= APP_URL ?>/kolaborasi"
           class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white hover:bg-slate-50 text-slate-700 font-semibold rounded-xl border border-slate-200 transition-all text-xs shadow-2xs">
            <ion-icon name="arrow-back-outline" class="text-sm"></ion-icon>
            <span>Kembali</span>
        </a>

        <button type="button" onclick="copyText('<?= htmlspecialchars($kolab['invite_code']) ?>', 'Kode undangan disalin!')"
                class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 border border-indigo-200 rounded-lg font-mono text-[11px] font-bold transition-colors cursor-pointer"
                title="Klik untuk menyalin kode undangan">
            <ion-icon name="key-outline" class="text-xs"></ion-icon>
            <span><?= htmlspecialchars($kolab['invite_code']) ?></span>
            <ion-icon name="copy-outline" class="text-[10px] opacity-70"></ion-icon>
        </button>
    </div>

    <div class="flex items-center gap-2 flex-wrap">
        <?php if (!empty($kolab['share_enabled'])): ?>
            <button type="button" onclick="copyText('<?= htmlspecialchars($shareUrl) ?>', 'Link monitoring Dinas disalin!')"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 rounded-xl text-xs font-semibold transition-colors cursor-pointer">
                <ion-icon name="link-outline" class="text-sm"></ion-icon>
                <span>Salin Link Dinas</span>
            </button>
            <a href="<?= htmlspecialchars($shareUrl) ?>" target="_blank"
               class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-white hover:bg-slate-50 text-slate-600 border border-slate-200 rounded-xl text-xs font-semibold transition-colors"
               title="Buka tampilan monitoring publik">
                <ion-icon name="open-outline" class="text-sm"></ion-icon>
            </a>
        <?php endif; ?>

        <button type="button" data-modal-open="modalKolabAnggotaGroup"
                class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-semibold shadow-xs transition-all cursor-pointer">
            <ion-icon name="git-network-outline" class="text-sm"></ion-icon>
            <span>Anggota & Share Group (<?= count($linkedGroups) ?>)</span>
        </button>

        <?php if ($isOwner): ?>
            <button type="button" data-modal-open="modalEditKolab"
                    class="p-1.5 bg-white hover:bg-amber-50 text-slate-600 hover:text-amber-600 border border-slate-200 rounded-xl transition-colors cursor-pointer"
                    title="Pengaturan Kolaborasi">
                <ion-icon name="settings-outline" class="text-base"></ion-icon>
            </button>
        <?php endif; ?>
    </div>
</div>

<!-- ========================================== -->
<!-- 2. HERO MONITORING SUMMARY CARD            -->
<!-- ========================================== -->
<div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 p-5 mb-4">
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-5">
        <div class="flex items-start gap-4 flex-1 min-w-0">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-indigo-500 to-blue-600 text-white flex items-center justify-center text-2xl shadow-sm shrink-0">
                <ion-icon name="people"></ion-icon>
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 flex-wrap mb-1">
                    <h1 class="text-lg md:text-xl font-extrabold text-slate-900 tracking-tight leading-tight">
                        <?= htmlspecialchars($kolab['nama']) ?>
                    </h1>
                    <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                        TA <?= (int)$kolab['tahun_anggaran'] ?>
                    </span>
                </div>
                <p class="text-xs text-slate-400 mb-2 flex items-center gap-2 flex-wrap">
                    <?php if (!empty($kolab['instansi'])): ?>
                        <span class="inline-flex items-center gap-1 text-slate-600 font-semibold">
                            <ion-icon name="business-outline"></ion-icon>
                            <?= htmlspecialchars($kolab['instansi']) ?>
                        </span>
                        <span class="text-slate-300">&bull;</span>
                    <?php endif; ?>
                    <span><?= $summary['total_perusahaan'] ?> Perusahaan</span>
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

        <!-- 4 Progress Rings Keseluruhan -->
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
                <?php if ($summary['paket_belum'] > 0): ?>
                    <span class="px-2 py-0.5 rounded-full font-semibold bg-slate-100 text-slate-600 border border-slate-200">
                        <?= $summary['paket_belum'] ?> Belum
                    </span>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Quick Prompt if User Hasn't Shared a Group Yet -->
<?php if (empty($myLinkedGroups)): ?>
    <div class="mb-4 bg-blue-50/80 border border-blue-200 rounded-2xl p-3.5 px-4 flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center gap-2.5 text-xs text-blue-800">
            <ion-icon name="information-circle-outline" class="text-lg text-blue-600 shrink-0"></ion-icon>
            <span>Anda belum menghubungkan Group Paket perusahaan Anda ke kolaborasi ini.</span>
        </div>
        <button type="button" data-modal-open="modalKolabAnggotaGroup"
                class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-semibold cursor-pointer">
            + Share Group Paket Saya
        </button>
    </div>
<?php endif; ?>

<!-- ========================================== -->
<!-- 3. REKAP PER PERUSAHAAN                    -->
<!-- ========================================== -->
<?php if (!empty($companyStats)): ?>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-3 mb-4">
        <?php foreach ($companyStats as $cs): ?>
            <button type="button" onclick="filterByCompany('<?= htmlspecialchars(strtolower($cs['nama']), ENT_QUOTES) ?>')"
                    class="text-left bg-white rounded-2xl shadow-xs border border-slate-200/80 p-3.5 hover:border-blue-300 hover:shadow-sm transition-all cursor-pointer">
                <div class="flex items-center justify-between gap-2 mb-1.5">
                    <span class="font-bold text-slate-800 text-xs truncate"><?= htmlspecialchars($cs['nama']) ?></span>
                    <span class="px-1.5 py-0.5 rounded-md bg-blue-50 text-blue-700 text-[10px] font-bold shrink-0">
                        <?= $cs['total_paket'] ?> Paket
                    </span>
                </div>
                <div class="font-mono font-bold text-slate-900 text-xs mb-2">
                    <?= formatRupiah($cs['total_kontrak']) ?>
                </div>
                <div class="flex items-center justify-between text-[10px] text-slate-400 mb-1">
                    <span>Progres Rata-rata</span>
                    <span class="font-bold text-slate-700"><?= $cs['avg_pct'] ?>%</span>
                </div>
                <div class="w-full h-1.5 bg-slate-100 rounded-full overflow-hidden mb-1.5">
                    <div class="h-full bg-gradient-to-r from-blue-500 to-indigo-600 rounded-full" style="width: <?= $cs['avg_pct'] ?>%"></div>
                </div>
                <div class="flex items-center justify-between text-[10px] text-slate-400">
                    <span>Order <?= $cs['order_pct'] ?>% &bull; Dist <?= $cs['dist_pct'] ?>%</span>
                    <span>BAST <?= $cs['bast_pct'] ?>%</span>
                </div>
            </button>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<!-- ========================================== -->
<!-- 4. TABEL MONITORING SEMUA PAKET            -->
<!-- ========================================== -->
<div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 overflow-hidden">
    <div class="p-3.5 px-5 border-b border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-3">
        <div class="flex items-center gap-2">
            <ion-icon name="analytics-outline" class="text-blue-600 text-base"></ion-icon>
            <h3 class="font-bold text-slate-800 text-xs uppercase tracking-wider">
                Monitoring Progres Paket (<span id="kolabVisibleCount"><?= count($pakets) ?></span>)
            </h3>
            <span class="text-[10px] px-2 py-0.5 rounded-full bg-slate-100 text-slate-500 font-semibold">
                HPP & Profit Privat
            </span>
        </div>

        <div class="flex items-center gap-2 flex-wrap">
            <div class="relative w-full sm:w-52">
                <ion-icon name="search-outline" class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></ion-icon>
                <input type="text" id="searchKolabPaket" onkeyup="filterKolabTable()"
                       placeholder="Cari paket, kode, tag..."
                       class="w-full pl-8 pr-3 py-1.5 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-blue-500 text-xs focus:bg-white">
            </div>

            <select id="filterKolabCompany" onchange="filterKolabTable()"
                    class="px-2.5 py-1.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700 focus:outline-none focus:border-blue-500 cursor-pointer">
                <option value="">Semua Perusahaan</option>
                <?php foreach ($companyStats as $cs): ?>
                    <option value="<?= htmlspecialchars(strtolower($cs['nama'])) ?>"><?= htmlspecialchars($cs['nama']) ?></option>
                <?php endforeach; ?>
            </select>

            <select id="filterKolabStatus" onchange="filterKolabTable()"
                    class="px-2.5 py-1.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700 focus:outline-none focus:border-blue-500 cursor-pointer">
                <option value="">Semua Pencairan</option>
                <option value="belum">Belum</option>
                <option value="proses">Proses</option>
                <option value="selesai">Cair / Selesai</option>
            </select>

            <button type="button" onclick="resetKolabFilter()"
                    class="p-1.5 text-slate-400 hover:text-slate-700 hover:bg-slate-100 rounded-xl border border-slate-200 cursor-pointer"
                    title="Reset Filter">
                <ion-icon name="refresh-outline" class="text-sm"></ion-icon>
            </button>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left text-xs" id="kolabPaketTable">
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
                <?php if (empty($pakets)): ?>
                    <tr>
                        <td colspan="10" class="py-12 text-center text-slate-400">
                            <p class="text-xs font-semibold text-slate-600 mb-1">Belum ada paket yang terhubung</p>
                            <p class="text-[11px] text-slate-400">Klik tombol <strong>Anggota & Share Group</strong> untuk menghubungkan Group Paket perusahaan.</p>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($pakets as $p):
                        $tagsArr = !empty($p['tags']) ? array_filter(array_map('trim', explode(',', $p['tags']))) : [];
                    ?>
                        <tr class="hover:bg-slate-50/70 transition-colors kolab-paket-row"
                            data-search="<?= htmlspecialchars(strtolower(($p['nama_paket'] ?? '') . ' ' . ($p['kode_paket'] ?? '') . ' ' . ($p['tags'] ?? '') . ' ' . ($p['nama_group'] ?? ''))) ?>"
                            data-company="<?= htmlspecialchars(strtolower($p['perusahaan_nama'])) ?>"
                            data-status="<?= htmlspecialchars($p['norm_status']) ?>"
                            data-kontrak="<?= $p['acuan'] ?>"
                            data-items="<?= $p['item_count'] ?>"
                            data-units="<?= $p['item_qty'] ?>">
                            <td class="py-3 px-3 text-center">
                                <span class="px-2 py-0.5 font-mono font-bold text-[11px] bg-slate-100 text-slate-700 rounded-md">
                                    <?= htmlspecialchars($p['kode_paket'] ?: '#' . $p['id']) ?>
                                </span>
                            </td>
                            <td class="py-3 px-3 min-w-[220px]">
                                <div class="font-bold text-slate-800 text-xs"><?= htmlspecialchars($p['nama_paket']) ?></div>
                                <div class="flex items-center gap-1 flex-wrap mt-1">
                                    <span class="px-1.5 py-0.5 rounded bg-amber-50 text-amber-700 border border-amber-200/80 text-[10px]">
                                        TA <?= (int)($p['tahun_anggaran'] ?? date('Y')) ?>
                                    </span>
                                    <span class="px-1.5 py-0.5 rounded bg-indigo-50 text-indigo-700 text-[10px]">
                                        <?= htmlspecialchars($p['nama_group']) ?>
                                    </span>
                                    <?php foreach ($tagsArr as $tg): ?>
                                        <span class="px-1.5 py-0.5 rounded bg-slate-100 text-slate-600 text-[10px]">#<?= htmlspecialchars($tg) ?></span>
                                    <?php endforeach; ?>
                                </div>
                            </td>
                            <td class="py-3 px-3 whitespace-nowrap">
                                <span class="inline-flex items-center gap-1.5 font-semibold text-slate-700 text-xs">
                                    <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                                    <?= htmlspecialchars($p['perusahaan_nama']) ?>
                                </span>
                            </td>
                            <td class="py-3 px-3 text-right font-mono font-bold text-slate-900 whitespace-nowrap">
                                <?= formatRupiah($p['acuan']) ?>
                            </td>
                            <td class="py-3 px-3 text-center whitespace-nowrap">
                                <div class="font-bold text-slate-800 text-xs"><?= $p['item_count'] ?> Item</div>
                                <div class="text-[10px] text-slate-400"><?= rtrim(rtrim(number_format($p['item_qty'], 2, ',', '.'), '0'), ',') ?> Unit</div>
                            </td>
                            <td class="py-3 px-2 text-center"><?php $renderTableRing($p['order_pct'], '#f59e0b', 'text-amber-600'); ?></td>
                            <td class="py-3 px-2 text-center"><?php $renderTableRing($p['dist_pct'], '#6366f1', 'text-indigo-600'); ?></td>
                            <td class="py-3 px-2 text-center"><?php $renderTableRing($p['dok_pct'], '#10b981', 'text-emerald-600'); ?></td>
                            <td class="py-3 px-2 text-center"><?php $renderTableRing($p['bast_pct'], '#a855f7', 'text-purple-600'); ?></td>
                            <td class="py-3 px-3 text-center whitespace-nowrap">
                                <?= getStatusBadge($p['status']) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
            <?php if (!empty($pakets)): ?>
                <tfoot class="bg-slate-50/95 border-t-2 border-slate-200 font-bold text-xs">
                    <tr>
                        <td colspan="3" class="py-3 px-3 text-slate-700 uppercase text-[11px] tracking-wider">
                            Overall Total (<span id="kolabFootCount"><?= $summary['total_paket'] ?></span> Paket)
                        </td>
                        <td class="py-3 px-3 text-right font-mono text-slate-900 whitespace-nowrap" id="kolabFootKontrak">
                            <?= formatRupiah($summary['total_kontrak']) ?>
                        </td>
                        <td class="py-3 px-3 text-center text-slate-600 text-[11px] whitespace-nowrap" id="kolabFootKomponen">
                            <?= $summary['total_items'] ?> Item / <?= rtrim(rtrim(number_format($summary['total_units'], 2, ',', '.'), '0'), ',') ?> Unit
                        </td>
                        <td colspan="5" class="py-3 px-3"></td>
                    </tr>
                </tfoot>
            <?php endif; ?>
        </table>
    </div>
</div>

<!-- ========================================== -->
<!-- 5. MODAL: ANGGOTA & SHARE GROUP            -->
<!-- ========================================== -->
<div id="modalKolabAnggotaGroup" class="app-modal fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 transition-all duration-200 opacity-0 pointer-events-none hidden">
    <div class="modal-card bg-white rounded-2xl shadow-2xl border border-slate-100 max-w-2xl w-full transform scale-95 opacity-0 transition-all duration-200 overflow-hidden max-h-[90vh] flex flex-col">
        <div class="px-5 py-4 bg-slate-50/80 border-b border-slate-100 flex items-center justify-between shrink-0">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-blue-600 text-white flex items-center justify-center text-lg">
                    <ion-icon name="git-network-outline"></ion-icon>
                </div>
                <div>
                    <h3 class="font-bold text-slate-800 text-sm">Anggota & Share Group Paket</h3>
                    <p class="text-[11px] text-slate-400">Undang rekan perusahaan & pilih Group Paket untuk ditampilkan</p>
                </div>
            </div>
            <button type="button" data-modal-close="modalKolabAnggotaGroup" class="p-1.5 text-slate-400 hover:text-slate-700 rounded-lg cursor-pointer">
                <ion-icon name="close-outline" class="text-xl"></ion-icon>
            </button>
        </div>

        <div class="p-5 space-y-5 overflow-y-auto">
            <!-- Section A: Share My Group -->
            <div class="bg-blue-50/60 border border-blue-200/80 rounded-xl p-4">
                <h4 class="text-xs font-bold text-slate-800 mb-2 flex items-center gap-1.5">
                    <ion-icon name="folder-open-outline" class="text-blue-600"></ion-icon>
                    <span>Hubungkan Group Paket Perusahaan Saya</span>
                </h4>
                <?php if (!empty($myAvailableGroups)): ?>
                    <form method="POST" action="<?= APP_URL ?>/kolaborasi/linkGroup/<?= $kolab['id'] ?>" class="flex flex-col sm:flex-row gap-2">
                        <?= csrfField() ?>
                        <select name="group_id" required class="flex-1 px-3 py-2 bg-white border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-blue-500">
                            <option value="">-- Pilih Group Paket --</option>
                            <?php foreach ($myAvailableGroups as $ag): ?>
                                <option value="<?= $ag['id'] ?>">
                                    <?= htmlspecialchars($ag['nama_group']) ?> (<?= htmlspecialchars($ag['perusahaan_nama'] ?: 'Umum') ?> &bull; <?= (int)$ag['total_paket'] ?> Paket)
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-semibold shrink-0 cursor-pointer">
                            + Share Group
                        </button>
                    </form>
                <?php else: ?>
                    <p class="text-[11px] text-slate-500">Semua Group Paket milik Anda sudah terhubung atau belum ada Group Paket yang dibuat.</p>
                <?php endif; ?>
            </div>

            <!-- Section B: Linked Groups List -->
            <div>
                <h4 class="text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    Group Paket Terhubung (<?= count($linkedGroups) ?>)
                </h4>
                <?php if (empty($linkedGroups)): ?>
                    <p class="text-xs text-slate-400 italic">Belum ada group yang dihubungkan.</p>
                <?php else: ?>
                    <div class="space-y-2">
                        <?php foreach ($linkedGroups as $lg): ?>
                            <div class="flex items-center justify-between gap-3 p-2.5 bg-slate-50 rounded-xl border border-slate-200/70">
                                <div class="min-w-0">
                                    <div class="font-bold text-slate-800 text-xs truncate"><?= htmlspecialchars($lg['nama_group']) ?></div>
                                    <div class="text-[10px] text-slate-500">
                                        <?= htmlspecialchars($lg['perusahaan_nama'] ?: 'Umum') ?> &bull; Oleh <?= htmlspecialchars($lg['user_name']) ?>
                                    </div>
                                </div>
                                <?php if ($isOwner || (int)$lg['user_id'] === $userId): ?>
                                    <a href="<?= APP_URL ?>/kolaborasi/unlinkGroup/<?= $kolab['id'] ?>/<?= $lg['group_id'] ?>"
                                       class="px-2.5 py-1 text-[11px] font-semibold text-rose-600 hover:bg-rose-50 rounded-lg border border-rose-200 shrink-0">
                                        Lepas
                                    </a>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Section C: Invite Member (Owner Only) -->
            <?php if ($isOwner): ?>
                <div class="pt-4 border-t border-slate-100">
                    <h4 class="text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Undang Anggota via Email</h4>
                    <form method="POST" action="<?= APP_URL ?>/kolaborasi/invite/<?= $kolab['id'] ?>" class="flex flex-col sm:flex-row gap-2 mb-3">
                        <?= csrfField() ?>
                        <input type="email" name="email" required
                               placeholder="email.rekan@perusahaan.com"
                               class="flex-1 px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-indigo-500 focus:bg-white">
                        <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-semibold shrink-0 cursor-pointer">
                            Kirim Undangan
                        </button>
                    </form>
                </div>
            <?php endif; ?>

            <!-- Section D: Member List -->
            <div>
                <h4 class="text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    Daftar Anggota (<?= count($members) ?>)
                </h4>
                <div class="space-y-1.5">
                    <?php foreach ($members as $m): ?>
                        <div class="flex items-center justify-between gap-2 p-2.5 rounded-xl border border-slate-100 bg-white">
                            <div class="min-w-0">
                                <div class="font-semibold text-slate-800 text-xs flex items-center gap-1.5">
                                    <span><?= htmlspecialchars($m['name']) ?></span>
                                    <span class="px-1.5 py-0.2 rounded text-[9px] font-bold <?= $m['role'] === 'owner' ? 'bg-indigo-50 text-indigo-700' : 'bg-slate-100 text-slate-600' ?>">
                                        <?= $m['role'] === 'owner' ? 'Koordinator' : 'Anggota' ?>
                                    </span>
                                </div>
                                <div class="text-[10px] text-slate-400"><?= htmlspecialchars($m['email']) ?> &bull; <?= (int)$m['linked_groups_count'] ?> Group</div>
                            </div>
                            <div class="flex items-center gap-2 shrink-0">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold <?= $m['status'] === 'joined' ? 'bg-emerald-50 text-emerald-700' : ($m['status'] === 'pending' ? 'bg-amber-50 text-amber-700' : 'bg-rose-50 text-rose-700') ?>">
                                    <?= $m['status'] === 'joined' ? 'Bergabung' : ($m['status'] === 'pending' ? 'Pending' : 'Ditolak') ?>
                                </span>
                                <?php if ($isOwner && $m['role'] !== 'owner'): ?>
                                    <a href="<?= APP_URL ?>/kolaborasi/removeMember/<?= $kolab['id'] ?>/<?= $m['user_id'] ?>"
                                       class="p-1 text-slate-400 hover:text-rose-600 rounded-lg" title="Keluarkan anggota">
                                        <ion-icon name="trash-outline"></ion-icon>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- 6. MODAL: PENGATURAN KOLABORASI (OWNER)    -->
<!-- ========================================== -->
<?php if ($isOwner): ?>
<div id="modalEditKolab" class="app-modal fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 transition-all duration-200 opacity-0 pointer-events-none hidden">
    <div class="modal-card bg-white rounded-2xl shadow-2xl border border-slate-100 max-w-md w-full transform scale-95 opacity-0 transition-all duration-200 overflow-hidden">
        <div class="px-5 py-4 bg-slate-50/80 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-bold text-slate-800 text-sm">Pengaturan Ruang Kolaborasi</h3>
            <button type="button" data-modal-close="modalEditKolab" class="p-1.5 text-slate-400 hover:text-slate-700 rounded-lg cursor-pointer">
                <ion-icon name="close-outline" class="text-xl"></ion-icon>
            </button>
        </div>

        <form method="POST" action="<?= APP_URL ?>/kolaborasi/update/<?= $kolab['id'] ?>" class="p-5 space-y-3.5">
            <?= csrfField() ?>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Kolaborasi <span class="text-red-500">*</span></label>
                <input type="text" name="nama" required value="<?= htmlspecialchars($kolab['nama']) ?>"
                       class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-blue-500">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Instansi / Dinas</label>
                    <input type="text" name="instansi" value="<?= htmlspecialchars($kolab['instansi'] ?? '') ?>"
                           class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Tahun Anggaran</label>
                    <input type="number" name="tahun_anggaran" value="<?= (int)$kolab['tahun_anggaran'] ?>"
                           class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-blue-500">
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Keterangan</label>
                <textarea name="deskripsi" rows="2" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-blue-500"><?= htmlspecialchars($kolab['deskripsi'] ?? '') ?></textarea>
            </div>

            <!-- Public Read-Only Link Controls -->
            <div class="p-3 bg-slate-50 rounded-xl border border-slate-200 space-y-2">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-700">Link Monitoring Dinas (Read-Only)</span>
                    <a href="<?= APP_URL ?>/kolaborasi/toggleShare/<?= $kolab['id'] ?>"
                       class="px-2 py-0.5 rounded-full text-[10px] font-bold <?= $kolab['share_enabled'] ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-200 text-slate-600' ?>">
                        <?= $kolab['share_enabled'] ? 'Aktif (Klik Nonaktifkan)' : 'Nonaktif (Klik Aktifkan)' ?>
                    </a>
                </div>
                <div class="flex items-center gap-1.5">
                    <input type="text" readonly value="<?= htmlspecialchars($shareUrl) ?>"
                           class="flex-1 px-2.5 py-1.5 bg-white border border-slate-200 rounded-lg text-[10px] font-mono text-slate-600">
                    <a href="<?= APP_URL ?>/kolaborasi/regenerateShare/<?= $kolab['id'] ?>"
                       class="px-2.5 py-1.5 bg-white hover:bg-slate-100 border border-slate-200 rounded-lg text-[10px] font-semibold text-slate-700 shrink-0"
                       title="Buat token link baru">
                        Reset Link
                    </a>
                </div>
            </div>

            <div class="pt-3 border-t border-slate-100 flex items-center justify-between gap-2">
                <a href="<?= APP_URL ?>/kolaborasi/delete/<?= $kolab['id'] ?>"
                   data-confirm-delete="<?= htmlspecialchars($kolab['nama']) ?>"
                   data-type="Ruang Kolaborasi"
                   class="px-3 py-2 text-rose-600 hover:bg-rose-50 rounded-xl text-xs font-semibold">
                    Hapus Ruang
                </a>
                <div class="flex items-center gap-2">
                    <button type="button" data-modal-close="modalEditKolab"
                            class="px-3.5 py-2 rounded-xl border border-slate-200 text-slate-600 font-semibold text-xs cursor-pointer">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl text-xs cursor-pointer">
                        Simpan
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function () {
    requestAnimationFrame(() => {
        document.querySelectorAll('circle[data-ring-target]').forEach(el => {
            el.style.strokeDashoffset = el.getAttribute('data-ring-target');
        });
    });
});

function copyText(txt, msg) {
    navigator.clipboard.writeText(txt).then(() => {
        if (window.ProToast) ProToast.success(msg);
        else alert(msg);
    });
}

function filterByCompany(companyLower) {
    const sel = document.getElementById('filterKolabCompany');
    if (!sel) return;
    sel.value = (sel.value === companyLower) ? '' : companyLower;
    filterKolabTable();
}

function formatRpKolab(num) {
    const n = Math.round(num || 0);
    return (n < 0 ? '-Rp ' : 'Rp ') + Math.abs(n).toLocaleString('id-ID');
}

function filterKolabTable() {
    const q = (document.getElementById('searchKolabPaket')?.value || '').toLowerCase().trim();
    const comp = document.getElementById('filterKolabCompany')?.value || '';
    const status = document.getElementById('filterKolabStatus')?.value || '';

    const rows = document.querySelectorAll('.kolab-paket-row');
    let vis = 0, sKontrak = 0, sItems = 0, sUnits = 0;

    rows.forEach(row => {
        const show = (!q || (row.dataset.search || '').includes(q))
                  && (!comp || (row.dataset.company || '') === comp)
                  && (!status || (row.dataset.status || '') === status);
        row.style.display = show ? '' : 'none';
        if (show) {
            vis++;
            sKontrak += parseFloat(row.dataset.kontrak || 0);
            sItems   += parseInt(row.dataset.items || 0, 10);
            sUnits   += parseFloat(row.dataset.units || 0);
        }
    });

    const setTxt = (id, t) => { const el = document.getElementById(id); if (el) el.textContent = t; };
    setTxt('kolabVisibleCount', vis);
    setTxt('kolabFootCount', vis);
    setTxt('kolabFootKontrak', formatRpKolab(sKontrak));
    setTxt('kolabFootKomponen', sItems + ' Item / ' + sUnits.toLocaleString('id-ID') + ' Unit');
}

function resetKolabFilter() {
    const s = document.getElementById('searchKolabPaket');
    const c = document.getElementById('filterKolabCompany');
    const st = document.getElementById('filterKolabStatus');
    if (s) s.value = '';
    if (c) c.value = '';
    if (st) st.value = '';
    filterKolabTable();
}
</script>
