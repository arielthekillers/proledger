<?php
/**
 * ProLedger - Modern Executive Dashboard
 * Menampilkan KPI finansial lengkap, grafik Chart.js (Komposisi Finansial & Alokasi Dana),
 * progres operasional 4 tahap, status kelengkapan branding perusahaan, paket terbaru,
 * ringkasan Group Paket, serta Kolaborasi aktif.
 */

$renderDashRing = function (int $pct, string $label, string $strokeHex, string $textClass) {
    $r = 16;
    $c = round(2 * M_PI * $r, 2);
    $offset = round($c * (1 - max(0, min(100, $pct)) / 100), 2);
    $dur = max(0.35, round(($pct / 100) * 1.1, 2));
    ?>
    <div class="flex flex-col items-center p-2.5 rounded-xl bg-slate-50/80 border border-slate-100">
        <div class="relative w-12 h-12 flex items-center justify-center">
            <svg class="w-12 h-12 -rotate-90" viewBox="0 0 40 40">
                <circle cx="20" cy="20" r="<?= $r ?>" fill="none" stroke="#e2e8f0" stroke-width="3.4"></circle>
                <circle cx="20" cy="20" r="<?= $r ?>" fill="none" stroke="<?= $strokeHex ?>" stroke-width="3.4"
                        stroke-linecap="round"
                        stroke-dasharray="<?= $c ?>"
                        stroke-dashoffset="<?= $c ?>"
                        data-ring-target="<?= $offset ?>"
                        style="transition: stroke-dashoffset <?= $dur ?>s cubic-bezier(0.22, 1, 0.36, 1);"></circle>
            </svg>
            <span class="absolute text-[10px] font-extrabold <?= $textClass ?>"><?= $pct ?>%</span>
        </div>
        <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider mt-1.5"><?= $label ?></span>
    </div>
    <?php
};

$brandChecks = [];
if (!empty($activeCompany)) {
    $brandChecks = [
        ['label' => 'Logo',     'ok' => !empty($activeCompany['logo'])],
        ['label' => 'Avatar',   'ok' => !empty($activeCompany['avatar'])],
        ['label' => 'Kop Surat','ok' => !empty($activeCompany['kopsurat'])],
        ['label' => 'TTD PNG',  'ok' => !empty($activeCompany['ttd'])],
        ['label' => 'Direktur', 'ok' => !empty($activeCompany['nama_direktur'])],
    ];
}
$brandDone = count(array_filter($brandChecks, fn($b) => $b['ok']));
?>

<!-- ========================================== -->
<!-- 1. TOP HEADER & COMPANY SWITCHER           -->
<!-- ========================================== -->
<div class="mb-4 flex flex-col lg:flex-row lg:items-center justify-between gap-3">
    <div>
        <div class="flex items-center gap-2 flex-wrap">
            <h1 class="text-lg md:text-xl font-extrabold text-slate-900 tracking-tight">Dashboard Overview</h1>
            <?php if (!empty($activeCompany)): ?>
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                    <span class="w-1.5 h-1.5 rounded-full bg-blue-500 animate-pulse"></span>
                    <span><?= htmlspecialchars($activeCompany['nama']) ?></span>
                </span>
            <?php else: ?>
                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                    Belum Ada Perusahaan
                </span>
            <?php endif; ?>
        </div>
        <p class="text-xs text-slate-400 mt-0.5">
            Ringkasan finansial, progres operasional, group program, dan kolaborasi perusahaan aktif.
        </p>
    </div>

    <div class="flex items-center gap-2 flex-wrap">
        <?php if (!empty($userCompanies) && count($userCompanies) > 1): ?>
            <div class="flex items-center gap-1 bg-white p-1 rounded-xl border border-slate-200/80 shadow-2xs overflow-x-auto max-w-full">
                <?php foreach ($userCompanies as $uc): ?>
                    <a href="<?= APP_URL ?>/perusahaan/switch/<?= $uc['id'] ?>"
                       class="px-2.5 py-1 rounded-lg text-[11px] font-semibold transition-all whitespace-nowrap <?= (($activeCompanyId ?? null) == $uc['id']) ? 'bg-blue-600 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100' ?>">
                        <?= htmlspecialchars($uc['nama']) ?>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <a href="<?= APP_URL ?>/paket"
           class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-semibold shadow-xs transition-all">
            <ion-icon name="cube-outline" class="text-sm"></ion-icon>
            <span>Kelola Paket</span>
        </a>
    </div>
</div>

<!-- Banner if user has 0 companies -->
<?php if (empty($userCompanies)): ?>
    <div class="mb-4 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-2xl p-4 text-white shadow-sm flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center text-2xl shrink-0">
                <ion-icon name="business"></ion-icon>
            </div>
            <div>
                <h4 class="font-bold text-sm">Mulai dengan Menambahkan Perusahaan Anda</h4>
                <p class="text-xs text-blue-100">Daftarkan perusahaan Anda terlebih dahulu untuk mengelola paket kegiatan, group, dan kolaborasi.</p>
            </div>
        </div>
        <a href="<?= APP_URL ?>/perusahaan" class="px-4 py-2 bg-white text-blue-700 hover:bg-blue-50 font-bold rounded-xl text-xs shadow transition-all whitespace-nowrap shrink-0">
            + Kelola Perusahaan
        </a>
    </div>
<?php endif; ?>

<!-- Pending Kolaborasi Invitation Banner -->
<?php if (!empty($pendingInvites)): ?>
    <div class="mb-4 bg-amber-50 border border-amber-200 rounded-2xl px-4 py-2.5 flex flex-wrap items-center justify-between gap-2">
        <div class="flex items-center gap-2 text-xs text-amber-800">
            <ion-icon name="mail-unread-outline" class="text-base text-amber-600 shrink-0"></ion-icon>
            <span>Anda memiliki <strong><?= count($pendingInvites) ?> undangan kolaborasi</strong> yang menunggu konfirmasi.</span>
        </div>
        <a href="<?= APP_URL ?>/kolaborasi" class="px-3 py-1 bg-amber-500 hover:bg-amber-600 text-white rounded-lg text-[11px] font-semibold transition-colors">
            Lihat Undangan
        </a>
    </div>
<?php endif; ?>

<!-- ========================================== -->
<!-- 2. 6-CARD EXECUTIVE KPI STRIP              -->
<!-- ========================================== -->
<div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-6 gap-3 mb-4">
    <!-- 1. Total Paket -->
    <div class="bg-gradient-to-br from-blue-600 to-indigo-700 rounded-2xl p-4 text-white shadow-sm relative overflow-hidden">
        <div class="absolute -right-3 -bottom-3 w-16 h-16 bg-white/10 rounded-full"></div>
        <div class="flex items-center justify-between mb-1.5">
            <span class="text-[10px] font-bold uppercase tracking-wider text-blue-100">Total Paket</span>
            <ion-icon name="cube-outline" class="text-lg text-blue-100"></ion-icon>
        </div>
        <div class="text-xl font-extrabold leading-tight"><?= $stats['total_paket'] ?> <span class="text-xs font-medium text-blue-100">Paket</span></div>
        <div class="text-[10px] text-blue-100 mt-1">
            <?= $stats['paket_selesai'] ?> Cair &bull; <?= $stats['paket_proses'] ?> Proses
        </div>
    </div>

    <!-- 2. Nilai Kontrak -->
    <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-xs">
        <div class="flex items-center justify-between mb-1.5">
            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Nilai Kontrak</span>
            <div class="w-7 h-7 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center text-xs">
                <ion-icon name="cash-outline"></ion-icon>
            </div>
        </div>
        <div class="text-sm md:text-base font-extrabold text-slate-900 font-mono truncate" title="<?= formatRupiah($stats['total_acuan']) ?>">
            <?= formatRupiah($stats['total_acuan']) ?>
        </div>
        <div class="text-[10px] text-slate-400 mt-1">
            <?= $stats['total_items'] ?> Item &bull; <?= number_format($stats['total_units'], 0, ',', '.') ?> Unit
        </div>
    </div>

    <!-- 3. Nilai Bersih -->
    <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-xs">
        <div class="flex items-center justify-between mb-1.5">
            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Nilai Bersih</span>
            <div class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center text-xs">
                <ion-icon name="wallet-outline"></ion-icon>
            </div>
        </div>
        <div class="text-sm md:text-base font-extrabold text-emerald-700 font-mono truncate" title="<?= formatRupiah($stats['total_bersih']) ?>">
            <?= formatRupiah($stats['total_bersih']) ?>
        </div>
        <div class="text-[10px] text-slate-400 mt-1">Pasal: <?= formatRupiah($stats['total_pasal']) ?></div>
    </div>

    <!-- 4. Total HPP -->
    <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-xs">
        <div class="flex items-center justify-between mb-1.5">
            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Total HPP</span>
            <div class="w-7 h-7 rounded-lg bg-slate-100 text-slate-600 flex items-center justify-center text-xs">
                <ion-icon name="cart-outline"></ion-icon>
            </div>
        </div>
        <div class="text-sm md:text-base font-extrabold text-slate-800 font-mono truncate" title="<?= formatRupiah($stats['total_hpp']) ?>">
            <?= formatRupiah($stats['total_hpp']) ?>
        </div>
        <div class="text-[10px] text-slate-400 mt-1">Realisasi Belanja Toko</div>
    </div>

    <!-- 5. Estimasi Profit -->
    <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-xs">
        <div class="flex items-center justify-between mb-1.5">
            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Estimasi Profit</span>
            <span class="px-1.5 py-0.5 rounded-md text-[10px] font-extrabold <?= $stats['total_profit'] >= 0 ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200' ?>">
                <?= ($stats['profit_pct'] >= 0 ? '+' : '') . $stats['profit_pct'] ?>%
            </span>
        </div>
        <div class="text-sm md:text-base font-extrabold font-mono truncate <?= $stats['total_profit'] >= 0 ? 'text-emerald-700' : 'text-rose-600' ?>" title="<?= formatRupiah($stats['total_profit']) ?>">
            <?= formatRupiah($stats['total_profit']) ?>
        </div>
        <div class="text-[10px] text-slate-400 mt-1">Bersih - HPP</div>
    </div>

    <!-- 6. Group & Kolaborasi -->
    <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-xs">
        <div class="flex items-center justify-between mb-1.5">
            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Program &amp; Tim</span>
            <div class="w-7 h-7 rounded-lg bg-purple-50 text-purple-600 flex items-center justify-center text-xs">
                <ion-icon name="people-outline"></ion-icon>
            </div>
        </div>
        <div class="text-sm md:text-base font-extrabold text-slate-900">
            <?= $total_groups ?> <span class="text-xs font-semibold text-slate-400">Group</span> &bull; <?= $total_kolab ?> <span class="text-xs font-semibold text-slate-400">Kolab</span>
        </div>
        <div class="text-[10px] text-slate-400 mt-1"><?= $stats['total_perusahaan'] ?> Perusahaan Terdaftar</div>
    </div>
</div>

<!-- ========================================== -->
<!-- 3. CHARTS & OPERATIONAL PROGRESS ROW       -->
<!-- ========================================== -->
<div class="grid grid-cols-1 lg:grid-cols-12 gap-4 mb-4">
    <!-- Chart 1: Komposisi Finansial per Paket -->
    <div class="lg:col-span-5 bg-white rounded-2xl border border-slate-200/80 shadow-xs p-4 flex flex-col">
        <div class="flex items-center justify-between mb-3">
            <div>
                <h3 class="font-bold text-slate-800 text-xs uppercase tracking-wider flex items-center gap-1.5">
                    <ion-icon name="bar-chart-outline" class="text-blue-600 text-sm"></ion-icon>
                    <span>Komposisi Finansial Paket</span>
                </h3>
                <p class="text-[11px] text-slate-400">Perbandingan Kontrak, Bersih, HPP, &amp; Profit (6 Paket Terbaru)</p>
            </div>
        </div>
        <div class="relative flex-1 min-h-[220px]">
            <?php if (empty($chartData['labels'])): ?>
                <div class="h-full flex items-center justify-center text-xs text-slate-400">Belum ada data paket untuk ditampilkan.</div>
            <?php else: ?>
                <canvas id="dashFinanceChart"></canvas>
            <?php endif; ?>
        </div>
    </div>

    <!-- Chart 2: Proporsi Dana & Status Pencairan -->
    <div class="lg:col-span-4 bg-white rounded-2xl border border-slate-200/80 shadow-xs p-4 flex flex-col">
        <div class="flex items-center justify-between mb-3">
            <div>
                <h3 class="font-bold text-slate-800 text-xs uppercase tracking-wider flex items-center gap-1.5">
                    <ion-icon name="pie-chart-outline" class="text-emerald-600 text-sm"></ion-icon>
                    <span>Alokasi Nilai Kontrak</span>
                </h3>
                <p class="text-[11px] text-slate-400">Proporsi Pajak/Pasal, HPP Belanja, &amp; Profit Bersih</p>
            </div>
            <div class="flex items-center gap-1 text-[10px]">
                <span class="px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 font-bold border border-emerald-200"><?= $stats['paket_selesai'] ?> Cair</span>
                <span class="px-2 py-0.5 rounded-full bg-rose-50 text-rose-700 font-bold border border-rose-200"><?= $stats['paket_proses'] ?> Proses</span>
            </div>
        </div>
        <div class="relative flex-1 min-h-[220px] flex items-center justify-center">
            <?php if ($stats['total_acuan'] <= 0): ?>
                <div class="text-xs text-slate-400">Belum ada nilai kontrak.</div>
            <?php else: ?>
                <canvas id="dashAllocationChart"></canvas>
            <?php endif; ?>
        </div>
    </div>

    <!-- Panel 3: Progres Operasional & Kelengkapan Perusahaan -->
    <div class="lg:col-span-3 bg-white rounded-2xl border border-slate-200/80 shadow-xs p-4 flex flex-col justify-between gap-3">
        <div>
            <h3 class="font-bold text-slate-800 text-xs uppercase tracking-wider flex items-center gap-1.5 mb-0.5">
                <ion-icon name="pulse-outline" class="text-indigo-600 text-sm"></ion-icon>
                <span>Progres Operasional</span>
            </h3>
            <p class="text-[11px] text-slate-400 mb-3">Rata-rata penyelesaian 4 tahap</p>

            <div class="grid grid-cols-2 gap-2.5">
                <?php $renderDashRing($stats['order_pct'], 'Order', '#f59e0b', 'text-amber-600'); ?>
                <?php $renderDashRing($stats['dist_pct'], 'Distribusi', '#6366f1', 'text-indigo-600'); ?>
                <?php $renderDashRing($stats['dok_pct'], 'Dokumentasi', '#10b981', 'text-emerald-600'); ?>
                <?php $renderDashRing($stats['bast_pct'], 'BAST', '#a855f7', 'text-purple-600'); ?>
            </div>
        </div>

        <?php if (!empty($activeCompany)): ?>
            <div class="pt-3 border-t border-slate-100">
                <div class="flex items-center justify-between text-[11px] mb-1.5">
                    <span class="font-bold text-slate-700">Kelengkapan Perusahaan</span>
                    <a href="<?= APP_URL ?>/perusahaan/detail/<?= $activeCompany['id'] ?>" class="text-blue-600 hover:underline font-semibold">
                        <?= $brandDone ?>/<?= count($brandChecks) ?> &rarr;
                    </a>
                </div>
                <div class="flex items-center gap-1 flex-wrap">
                    <?php foreach ($brandChecks as $bc): ?>
                        <span class="px-1.5 py-0.5 rounded text-[10px] font-semibold <?= $bc['ok'] ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-400' ?>">
                            <?= $bc['ok'] ? '✓ ' : '' ?><?= $bc['label'] ?>
                        </span>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- ========================================== -->
<!-- 4. RECENT PAKETS & GROUP / KOLABORASI      -->
<!-- ========================================== -->
<div class="grid grid-cols-1 lg:grid-cols-12 gap-4">
    <!-- Left: Recent Pakets -->
    <div class="lg:col-span-8 bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="px-5 py-3.5 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-bold text-slate-800 text-xs uppercase tracking-wider flex items-center gap-1.5">
                <ion-icon name="time-outline" class="text-blue-600 text-base"></ion-icon>
                <span>Paket Kegiatan Terbaru</span>
            </h3>
            <a href="<?= APP_URL ?>/paket" class="text-xs font-semibold text-blue-600 hover:text-blue-700">Lihat Semua &rarr;</a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50/80 text-[10px] uppercase tracking-wider text-slate-500 font-bold border-b border-slate-200/80">
                    <tr>
                        <th class="py-2.5 px-4">Paket Kegiatan</th>
                        <th class="py-2.5 px-3 text-right">Nilai Kontrak</th>
                        <th class="py-2.5 px-3 text-right">Est. Profit</th>
                        <th class="py-2.5 px-3 text-center">Progres</th>
                        <th class="py-2.5 px-4 text-center">Pembayaran</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php if (empty($recent_paket)): ?>
                        <tr>
                            <td colspan="5" class="py-10 text-center text-slate-400 text-xs">
                                Belum ada paket kegiatan untuk perusahaan aktif.
                                <a href="<?= APP_URL ?>/paket" class="text-blue-600 font-semibold hover:underline ml-1">Tambah Paket</a>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($recent_paket as $rp):
                            $m = $rp['_m'];
                            $kodeRef = $rp['kode_paket'] ?: $rp['id'];
                            $avgP = (int)round(($m['order_pct'] + $m['dist_pct'] + $m['dok_pct'] + $m['bast_pct']) / 4);
                        ?>
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <td class="py-2.5 px-4">
                                    <div class="flex items-center gap-2">
                                        <span class="px-1.5 py-0.5 rounded bg-slate-100 font-mono font-bold text-[10px] text-slate-600 shrink-0">
                                            <?= htmlspecialchars($kodeRef) ?>
                                        </span>
                                        <a href="<?= APP_URL ?>/paket?open_modal=<?= urlencode((string)$kodeRef) ?>&tab=info"
                                           class="font-bold text-slate-800 hover:text-blue-600 transition-colors truncate max-w-[230px]">
                                            <?= htmlspecialchars($rp['nama_paket']) ?>
                                        </a>
                                    </div>
                                    <div class="flex items-center gap-1.5 mt-0.5 text-[10px] text-slate-400">
                                        <span>TA <?= (int)($rp['tahun_anggaran'] ?? date('Y')) ?></span>
                                        <?php if (!empty($rp['nama_group'])): ?>
                                            <span>&bull;</span>
                                            <span class="text-indigo-600 font-medium"><?= htmlspecialchars($rp['nama_group']) ?></span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="py-2.5 px-3 text-right font-mono font-bold text-slate-800 whitespace-nowrap">
                                    <?= formatRupiah($m['acuan']) ?>
                                </td>
                                <td class="py-2.5 px-3 text-right whitespace-nowrap">
                                    <div class="font-mono font-bold <?= $m['profit'] >= 0 ? 'text-emerald-700' : 'text-rose-600' ?>">
                                        <?= formatRupiah($m['profit']) ?>
                                    </div>
                                    <div class="text-[10px] text-slate-400"><?= ($m['profit_pct'] >= 0 ? '+' : '') . $m['profit_pct'] ?>%</div>
                                </td>
                                <td class="py-2.5 px-3 text-center whitespace-nowrap">
                                    <div class="w-20 mx-auto">
                                        <div class="text-[10px] font-bold text-slate-600 mb-0.5"><?= $avgP ?>%</div>
                                        <div class="w-full h-1.5 bg-slate-100 rounded-full overflow-hidden">
                                            <div class="h-full bg-gradient-to-r from-blue-500 to-indigo-600 rounded-full" style="width: <?= $avgP ?>%"></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-2.5 px-4 text-center whitespace-nowrap">
                                    <?= getStatusBadge($rp['status'] ?? 'Proses') ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Right: Group Paket & Kolaborasi -->
    <div class="lg:col-span-4 space-y-4">
        <!-- Group Paket -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-4">
            <div class="flex items-center justify-between mb-3">
                <h3 class="font-bold text-slate-800 text-xs uppercase tracking-wider flex items-center gap-1.5">
                    <ion-icon name="folder-open-outline" class="text-indigo-600 text-sm"></ion-icon>
                    <span>Group Paket (<?= $total_groups ?>)</span>
                </h3>
                <a href="<?= APP_URL ?>/group" class="text-[11px] font-semibold text-blue-600 hover:underline">Kelola &rarr;</a>
            </div>
            <?php if (empty($groups)): ?>
                <p class="text-xs text-slate-400 py-3 text-center">Belum ada group paket untuk perusahaan ini.</p>
            <?php else: ?>
                <div class="space-y-2">
                    <?php foreach ($groups as $g): ?>
                        <a href="<?= APP_URL ?>/group/detail/<?= $g['id'] ?>"
                           class="flex items-center justify-between gap-2 p-2.5 rounded-xl bg-slate-50/80 hover:bg-blue-50/60 border border-slate-100 transition-colors">
                            <div class="min-w-0">
                                <div class="font-bold text-slate-800 text-xs truncate"><?= htmlspecialchars($g['nama_group']) ?></div>
                                <div class="text-[10px] text-slate-400"><?= (int)$g['total_paket'] ?> Paket</div>
                            </div>
                            <div class="text-right shrink-0">
                                <div class="font-mono font-bold text-slate-800 text-[11px]"><?= formatRupiah($g['total_acuan'] ?? 0) ?></div>
                                <div class="font-mono text-[10px] text-emerald-600 font-semibold">Profit <?= formatRupiah($g['total_profit'] ?? 0) ?></div>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Kolaborasi Aktif -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-4">
            <div class="flex items-center justify-between mb-3">
                <h3 class="font-bold text-slate-800 text-xs uppercase tracking-wider flex items-center gap-1.5">
                    <ion-icon name="people-outline" class="text-purple-600 text-sm"></ion-icon>
                    <span>Kolaborasi Aktif (<?= $total_kolab ?>)</span>
                </h3>
                <a href="<?= APP_URL ?>/kolaborasi" class="text-[11px] font-semibold text-blue-600 hover:underline">Semua &rarr;</a>
            </div>
            <?php if (empty($joinedKolabs)): ?>
                <p class="text-xs text-slate-400 py-3 text-center">Belum tergabung dalam ruang kolaborasi.</p>
            <?php else: ?>
                <div class="space-y-2">
                    <?php foreach ($joinedKolabs as $k): ?>
                        <a href="<?= APP_URL ?>/kolaborasi/detail/<?= $k['id'] ?>"
                           class="flex items-center justify-between gap-2 p-2.5 rounded-xl bg-slate-50/80 hover:bg-indigo-50/60 border border-slate-100 transition-colors">
                            <div class="min-w-0">
                                <div class="font-bold text-slate-800 text-xs truncate"><?= htmlspecialchars($k['nama']) ?></div>
                                <div class="text-[10px] text-slate-400"><?= htmlspecialchars($k['instansi'] ?: 'Monitoring Bersama') ?> &bull; TA <?= (int)$k['tahun_anggaran'] ?></div>
                            </div>
                            <span class="px-2 py-0.5 rounded-full bg-indigo-50 text-indigo-700 text-[10px] font-bold shrink-0">
                                <?= (int)$k['total_paket'] ?> Paket
                            </span>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Animate Operational Progress Rings
    requestAnimationFrame(() => {
        document.querySelectorAll('circle[data-ring-target]').forEach(el => {
            el.style.strokeDashoffset = el.getAttribute('data-ring-target');
        });
    });

    if (typeof Chart === 'undefined') return;
    const chartData = <?= json_encode($chartData) ?>;
    const fmtRp = (v) => 'Rp ' + Math.round(v).toLocaleString('id-ID');

    // 1. Bar Chart: Komposisi Finansial Paket
    const finCtx = document.getElementById('dashFinanceChart');
    if (finCtx && chartData.labels.length > 0) {
        new Chart(finCtx, {
            type: 'bar',
            data: {
                labels: chartData.labels,
                datasets: [
                    { label: 'Kontrak', data: chartData.kontrak, backgroundColor: '#3b82f6', borderRadius: 6 },
                    { label: 'Bersih',  data: chartData.bersih,  backgroundColor: '#10b981', borderRadius: 6 },
                    { label: 'HPP',     data: chartData.hpp,     backgroundColor: '#64748b', borderRadius: 6 },
                    { label: 'Profit',  data: chartData.profit,  backgroundColor: '#f59e0b', borderRadius: 6 }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', labels: { boxWidth: 10, font: { size: 11 } } },
                    tooltip: { callbacks: { label: (ctx) => ctx.dataset.label + ': ' + fmtRp(ctx.raw) } }
                },
                scales: {
                    x: { ticks: { font: { size: 10 } }, grid: { display: false } },
                    y: {
                        ticks: {
                            font: { size: 10 },
                            callback: (v) => v >= 1000000 ? (v / 1000000).toFixed(0) + ' Jt' : v.toLocaleString('id-ID')
                        },
                        grid: { color: '#f1f5f9' }
                    }
                }
            }
        });
    }

    // 2. Doughnut Chart: Alokasi Nilai Kontrak
    const allocCtx = document.getElementById('dashAllocationChart');
    if (allocCtx) {
        const p = chartData.proporsi;
        new Chart(allocCtx, {
            type: 'doughnut',
            data: {
                labels: ['Potongan Pasal', 'HPP Belanja Toko', 'Estimasi Profit'],
                datasets: [{
                    data: [p.pasal, p.hpp, p.profit],
                    backgroundColor: ['#f59e0b', '#64748b', '#10b981'],
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '64%',
                plugins: {
                    legend: { position: 'bottom', labels: { boxWidth: 10, font: { size: 11 } } },
                    tooltip: { callbacks: { label: (ctx) => ctx.label + ': ' + fmtRp(ctx.raw) } }
                }
            }
        });
    }
});
</script>
