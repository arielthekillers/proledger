<?php
/**
 * ProLedger - Detail Group Paket Kegiatan & Roll-up Keuangan
 * UI Modern terintegrasi: Hero Group Card + Progres Keseluruhan + 6 KPI Finansial
 * + Filter & Tabel Paket Lengkap + Overall Total Dinamis + Popup Tambah Paket & Edit Group
 */

$renderMiniRing = function (int $pct, string $label, string $strokeHex, string $textClass) {
    $r = 15;
    $c = round(2 * M_PI * $r, 2);
    $offset = round($c * (1 - max(0, min(100, $pct)) / 100), 2);
    $dur = max(0.35, round(($pct / 100) * 1.1, 2));
    ?>
    <div class="flex flex-col items-center">
        <div class="relative w-10 h-10 flex items-center justify-center">
            <svg class="w-10 h-10 -rotate-90" viewBox="0 0 38 38">
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
<!-- 1. TOP BAR NAVIGATION & QUICK ACTIONS     -->
<!-- ========================================== -->
<div class="mb-4 flex flex-wrap items-center justify-between gap-3">
    <div class="flex items-center gap-2 flex-wrap">
        <a href="<?= APP_URL ?>/group" 
           class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white hover:bg-slate-50 text-slate-700 font-semibold rounded-xl border border-slate-200 transition-all text-xs shadow-2xs hover:shadow-xs">
            <ion-icon name="arrow-back-outline" class="text-sm"></ion-icon>
            <span>Kembali ke Daftar Group</span>
        </a>

        <?php if (!empty($group['kode_group'])): ?>
            <span class="inline-flex items-center px-2.5 py-1 rounded-lg font-mono text-[11px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-200">
                <?= htmlspecialchars($group['kode_group']) ?>
            </span>
        <?php endif; ?>
    </div>

    <div class="flex items-center gap-2">
        <?php if ($role === 'admin' || $role === 'user'): ?>
            <button type="button"
                    onclick='openEditGroupModal(<?= json_encode($group, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)'
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white hover:bg-amber-50 text-slate-700 hover:text-amber-700 font-semibold rounded-xl border border-slate-200 hover:border-amber-200 transition-all text-xs shadow-2xs cursor-pointer">
                <ion-icon name="create-outline" class="text-sm"></ion-icon>
                <span>Edit Group</span>
            </button>

            <button type="button"
                    data-modal-open="modalTambahPaketGroup"
                    class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-semibold shadow-sm shadow-blue-600/20 transition-all hover:scale-[1.01] active:scale-[0.98] cursor-pointer">
                <ion-icon name="add-circle-outline" class="text-base"></ion-icon>
                <span>Tambah Paket ke Group Ini</span>
            </button>
        <?php endif; ?>
    </div>
</div>

<!-- ========================================== -->
<!-- 2. HERO GROUP HEADER & OVERALL PROGRESS   -->
<!-- ========================================== -->
<div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 p-5 mb-4">
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-5">
        <div class="flex items-start gap-4 flex-1 min-w-0">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-indigo-500 to-blue-600 text-white flex items-center justify-center text-2xl shadow-sm shrink-0">
                <ion-icon name="folder-open"></ion-icon>
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 flex-wrap mb-1">
                    <h1 class="text-lg md:text-xl font-extrabold text-slate-900 tracking-tight leading-tight">
                        <?= htmlspecialchars($group['nama_group']) ?>
                    </h1>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                        <?= $stats['total_paket'] ?> Paket
                    </span>
                </div>
                <p class="text-xs text-slate-400 mb-2 flex items-center gap-2 flex-wrap">
                    <span class="inline-flex items-center gap-1">
                        <ion-icon name="business-outline"></ion-icon>
                        <strong class="text-slate-600 font-semibold"><?= htmlspecialchars($group['perusahaan_nama'] ?: 'Umum / Semua') ?></strong>
                    </span>
                    <span class="text-slate-300">&bull;</span>
                    <span class="inline-flex items-center gap-1">
                        <ion-icon name="calendar-outline"></ion-icon>
                        <span>Dibuat <?= date('d M Y', strtotime($group['created_at'])) ?></span>
                    </span>
                </p>
                <?php if (!empty($group['deskripsi'])): ?>
                    <p class="text-xs text-slate-600 bg-slate-50/90 px-3 py-2 rounded-xl border border-slate-100 leading-relaxed">
                        <?= nl2br(htmlspecialchars($group['deskripsi'])) ?>
                    </p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Right: Progress Rings & Pencairan Status -->
        <div class="flex flex-col sm:flex-row lg:flex-col items-start sm:items-center lg:items-end justify-between gap-3 pt-4 lg:pt-0 border-t lg:border-t-0 lg:border-l border-slate-100 lg:pl-6 shrink-0">
            <div class="flex items-center gap-3.5">
                <?php $renderMiniRing($stats['order_pct'], 'Order', '#f59e0b', 'text-amber-600'); ?>
                <?php $renderMiniRing($stats['dist_pct'], 'Dist', '#6366f1', 'text-indigo-600'); ?>
                <?php $renderMiniRing($stats['dok_pct'], 'Dok', '#10b981', 'text-emerald-600'); ?>
                <?php $renderMiniRing($stats['bast_pct'], 'BAST', '#a855f7', 'text-purple-600'); ?>
            </div>
            <div class="flex items-center gap-1.5 flex-wrap text-[11px]">
                <span class="px-2 py-0.5 rounded-full font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                    <?= $stats['paket_selesai'] ?> Cair/Selesai
                </span>
                <span class="px-2 py-0.5 rounded-full font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                    <?= $stats['paket_proses'] ?> Proses
                </span>
                <?php if ($stats['paket_belum'] > 0): ?>
                    <span class="px-2 py-0.5 rounded-full font-semibold bg-slate-100 text-slate-600 border border-slate-200">
                        <?= $stats['paket_belum'] ?> Belum
                    </span>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- 3. 6-CARD FINANCIAL ROLL-UP KPI STRIP     -->
<!-- ========================================== -->
<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3 mb-4">
    <!-- 1. Nilai Kontrak -->
    <div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 p-3.5">
        <div class="flex items-center justify-between mb-1.5">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Nilai Kontrak</span>
            <div class="w-7 h-7 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center text-xs">
                <ion-icon name="cash-outline"></ion-icon>
            </div>
        </div>
        <div class="text-sm md:text-base font-extrabold text-slate-900 font-mono truncate" title="<?= formatRupiah($stats['total_acuan']) ?>">
            <?= formatRupiah($stats['total_acuan']) ?>
        </div>
        <div class="text-[10px] text-slate-400 mt-1">
            <?= $stats['total_paket'] ?> Paket &bull; <?= $stats['total_items'] ?> Item
        </div>
    </div>

    <!-- 2. Total DPP -->
    <div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 p-3.5">
        <div class="flex items-center justify-between mb-1.5">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Total DPP (-11%)</span>
            <div class="w-7 h-7 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center text-xs">
                <ion-icon name="calculator-outline"></ion-icon>
            </div>
        </div>
        <div class="text-sm md:text-base font-extrabold text-slate-800 font-mono truncate" title="<?= formatRupiah($stats['total_dpp']) ?>">
            <?= formatRupiah($stats['total_dpp']) ?>
        </div>
        <div class="text-[10px] text-slate-400 mt-1">Dasar Pengenaan Pajak</div>
    </div>

    <!-- 3. Total Pasal -->
    <div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 p-3.5">
        <div class="flex items-center justify-between mb-1.5">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Total Pasal</span>
            <div class="w-7 h-7 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center text-xs">
                <ion-icon name="pie-chart-outline"></ion-icon>
            </div>
        </div>
        <div class="text-sm md:text-base font-extrabold text-amber-700 font-mono truncate" title="<?= formatRupiah($stats['total_pasal']) ?>">
            <?= formatRupiah($stats['total_pasal']) ?>
        </div>
        <div class="text-[10px] text-slate-400 mt-1">Potongan Pajak + Admin</div>
    </div>

    <!-- 4. Nilai Bersih -->
    <div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 p-3.5">
        <div class="flex items-center justify-between mb-1.5">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Nilai Bersih</span>
            <div class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center text-xs">
                <ion-icon name="wallet-outline"></ion-icon>
            </div>
        </div>
        <div class="text-sm md:text-base font-extrabold text-emerald-700 font-mono truncate" title="<?= formatRupiah($stats['total_bersih']) ?>">
            <?= formatRupiah($stats['total_bersih']) ?>
        </div>
        <div class="text-[10px] text-slate-400 mt-1">Kontrak - Potongan</div>
    </div>

    <!-- 5. Total HPP -->
    <div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 p-3.5">
        <div class="flex items-center justify-between mb-1.5">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Total HPP</span>
            <div class="w-7 h-7 rounded-lg bg-slate-100 text-slate-600 flex items-center justify-center text-xs">
                <ion-icon name="cart-outline"></ion-icon>
            </div>
        </div>
        <div class="text-sm md:text-base font-extrabold text-slate-800 font-mono truncate" title="<?= formatRupiah($stats['total_hpp']) ?>">
            <?= formatRupiah($stats['total_hpp']) ?>
        </div>
        <div class="text-[10px] text-slate-400 mt-1">Realisasi Belanja Toko</div>
    </div>

    <!-- 6. Estimasi Profit -->
    <div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 p-3.5">
        <div class="flex items-center justify-between mb-1.5">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Estimasi Profit</span>
            <span class="px-1.5 py-0.5 rounded-md text-[10px] font-extrabold <?= $stats['total_profit'] >= 0 ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200' ?>">
                <?= ($stats['profit_pct'] >= 0 ? '+' : '') . $stats['profit_pct'] ?>%
            </span>
        </div>
        <div class="text-sm md:text-base font-extrabold font-mono truncate <?= $stats['total_profit'] >= 0 ? 'text-emerald-700' : 'text-rose-600' ?>" title="<?= formatRupiah($stats['total_profit']) ?>">
            <?= formatRupiah($stats['total_profit']) ?>
        </div>
        <div class="text-[10px] text-slate-400 mt-1">Bersih - HPP</div>
    </div>
</div>

<!-- ========================================== -->
<!-- 4. FILTER BAR & MEMBER PACKAGES TABLE     -->
<!-- ========================================== -->
<div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 overflow-hidden">
    <div class="p-3.5 px-5 border-b border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-3">
        <div class="flex items-center gap-2">
            <ion-icon name="cube-outline" class="text-blue-600 text-base"></ion-icon>
            <h3 class="font-bold text-slate-800 text-xs uppercase tracking-wider">
                Daftar Paket Anggota (<span id="visiblePaketCount"><?= count($paketList) ?></span>)
            </h3>
        </div>

        <!-- Live Search & Filters -->
        <div class="flex items-center gap-2 flex-wrap">
            <div class="relative w-full sm:w-56">
                <ion-icon name="search-outline" class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></ion-icon>
                <input type="text" id="searchGroupPaket" onkeyup="filterGroupPaketTable()"
                       placeholder="Cari paket, kode, tag..."
                       class="w-full pl-8 pr-3 py-1.5 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-blue-500 text-xs focus:bg-white transition-all">
            </div>

            <select id="filterGroupPaketTahun" onchange="filterGroupPaketTable()"
                    class="px-2.5 py-1.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700 focus:outline-none focus:border-blue-500 cursor-pointer">
                <option value="">Semua Tahun</option>
                <?php
                $years = array_unique(array_map(fn($x) => (int)($x['tahun_anggaran'] ?? date('Y')), $paketList));
                rsort($years);
                foreach ($years as $yr): ?>
                    <option value="<?= $yr ?>">TA <?= $yr ?></option>
                <?php endforeach; ?>
            </select>

            <select id="filterGroupPaketStatus" onchange="filterGroupPaketTable()"
                    class="px-2.5 py-1.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700 focus:outline-none focus:border-blue-500 cursor-pointer">
                <option value="">Semua Pencairan</option>
                <option value="belum">Belum</option>
                <option value="proses">Proses</option>
                <option value="selesai">Selesai / Cair</option>
            </select>

            <button type="button" onclick="resetGroupPaketFilter()"
                    class="p-1.5 text-slate-400 hover:text-slate-700 hover:bg-slate-100 rounded-xl border border-slate-200 transition-colors cursor-pointer"
                    title="Reset Filter">
                <ion-icon name="refresh-outline" class="text-sm"></ion-icon>
            </button>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left text-xs" id="groupPaketTable">
            <thead class="bg-slate-50/80 text-[10px] uppercase tracking-wider text-slate-500 font-bold border-b border-slate-200/80">
                <tr>
                    <th class="py-3 px-3 text-center">Kode</th>
                    <th class="py-3 px-3">Nama Paket</th>
                    <th class="py-3 px-3 text-right">Nilai Kontrak</th>
                    <th class="py-3 px-3 text-right">DPP</th>
                    <th class="py-3 px-3 text-right">Total Pasal</th>
                    <th class="py-3 px-3 text-right">Nilai Bersih</th>
                    <th class="py-3 px-3 text-right">HPP</th>
                    <th class="py-3 px-3 text-right">Profit</th>
                    <th class="py-3 px-3 text-center">Komponen</th>
                    <th class="py-3 px-2 text-center">Dist</th>
                    <th class="py-3 px-2 text-center">Order</th>
                    <th class="py-3 px-2 text-center">Dok</th>
                    <th class="py-3 px-2 text-center">BAST</th>
                    <th class="py-3 px-3 text-center">Pencairan</th>
                    <th class="py-3 px-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php if (empty($paketList)): ?>
                    <tr>
                        <td colspan="15" class="py-12 text-center text-slate-400">
                            <div class="w-12 h-12 bg-slate-50 rounded-2xl flex items-center justify-center mx-auto mb-2 text-2xl text-slate-300">
                                <ion-icon name="cube-outline"></ion-icon>
                            </div>
                            <h4 class="text-xs font-bold text-slate-700 mb-0.5">Belum Ada Paket Kegiatan</h4>
                            <p class="text-[11px] text-slate-400 mb-3">Tambahkan paket kegiatan yang berada di bawah naungan group ini.</p>
                            <?php if ($role === 'admin' || $role === 'user'): ?>
                                <button type="button" data-modal-open="modalTambahPaketGroup"
                                        class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-semibold shadow-xs transition-all cursor-pointer">
                                    <ion-icon name="add-circle-outline"></ion-icon>
                                    <span>Tambah Paket Baru</span>
                                </button>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($paketList as $p):
                        $m = PaketGroup::calculatePaketMetrics($p);
                        $showDiffBase = (abs($m['effective_pagu'] - $m['acuan']) > 0.5);
                        $tagsArr = !empty($p['tags']) ? array_filter(array_map('trim', explode(',', $p['tags']))) : [];
                    ?>
                        <tr class="hover:bg-slate-50/70 transition-colors group-paket-row"
                            data-search="<?= htmlspecialchars(strtolower(($p['nama_paket'] ?? '') . ' ' . ($p['kode_paket'] ?? '') . ' ' . ($p['tags'] ?? ''))) ?>"
                            data-tahun="<?= htmlspecialchars((string)($p['tahun_anggaran'] ?? date('Y'))) ?>"
                            data-status="<?= htmlspecialchars($m['norm_status']) ?>"
                            data-kontrak="<?= $m['acuan'] ?>"
                            data-dpp="<?= $m['dpp'] ?>"
                            data-pasal="<?= $m['total_pasal'] ?>"
                            data-bersih="<?= $m['nilai_bersih'] ?>"
                            data-hpp="<?= $m['hpp'] ?>"
                            data-profit="<?= $m['profit'] ?>"
                            data-items="<?= $m['item_count'] ?>"
                            data-units="<?= $m['item_qty'] ?>">

                            <!-- Kode -->
                            <td class="py-2.5 px-3 text-center">
                                <span class="px-2 py-0.5 font-mono font-bold text-[11px] bg-slate-100 text-slate-700 rounded-md tracking-wider">
                                    <?= htmlspecialchars($p['kode_paket'] ?: '#' . $p['id']) ?>
                                </span>
                            </td>

                            <!-- Nama Paket & Badges -->
                            <td class="py-2.5 px-3 min-w-[200px]">
                                <a href="<?= APP_URL ?>/paket/detail/<?= $p['kode_paket'] ?: $p['id'] ?>"
                                   class="font-bold text-slate-800 hover:text-blue-600 text-xs transition-colors block">
                                    <?= htmlspecialchars($p['nama_paket']) ?>
                                </a>
                                <div class="flex items-center gap-1 flex-wrap mt-1">
                                    <span class="px-1.5 py-0.2 rounded bg-amber-50 text-amber-700 border border-amber-200/80 text-[10px]">
                                        TA <?= htmlspecialchars((string)($p['tahun_anggaran'] ?? date('Y'))) ?>
                                    </span>
                                    <?php foreach ($tagsArr as $tg): ?>
                                        <span class="px-1.5 py-0.2 rounded bg-slate-100 text-slate-600 text-[10px]">
                                            #<?= htmlspecialchars($tg) ?>
                                        </span>
                                    <?php endforeach; ?>
                                </div>
                            </td>

                            <!-- Nilai Kontrak -->
                            <td class="py-2.5 px-3 text-right whitespace-nowrap align-top">
                                <div class="font-mono font-bold text-slate-900 text-xs">
                                    <?= formatRupiah($m['acuan']) ?>
                                </div>
                                <?php if ($showDiffBase): ?>
                                    <div class="font-mono text-slate-400 line-through text-[10px] mt-0.5" title="Nilai Kontrak Awal">
                                        <?= formatRupiah($m['effective_pagu']) ?>
                                    </div>
                                <?php endif; ?>
                            </td>

                            <!-- DPP -->
                            <td class="py-2.5 px-3 text-right whitespace-nowrap align-top">
                                <div class="font-mono font-semibold text-slate-800 text-xs">
                                    <?= formatRupiah($m['dpp']) ?>
                                </div>
                                <div class="text-[10px] text-slate-400 mt-0.5">DPP -11%</div>
                            </td>

                            <!-- Total Pasal -->
                            <td class="py-2.5 px-3 text-right whitespace-nowrap align-top">
                                <div class="font-mono font-bold text-amber-700 text-xs">
                                    <?= formatRupiah($m['total_pasal']) ?>
                                </div>
                                <div class="text-[10px] text-amber-600/80 mt-0.5"><?= $m['total_persen'] ?>%</div>
                            </td>

                            <!-- Nilai Bersih -->
                            <td class="py-2.5 px-3 text-right whitespace-nowrap align-top">
                                <div class="font-mono font-bold text-emerald-700 text-xs">
                                    <?= formatRupiah($m['nilai_bersih']) ?>
                                </div>
                            </td>

                            <!-- HPP -->
                            <td class="py-2.5 px-3 text-right whitespace-nowrap align-top">
                                <div class="font-mono font-semibold text-slate-700 text-xs">
                                    <?= formatRupiah($m['hpp']) ?>
                                </div>
                                <div class="text-[10px] text-slate-400 mt-0.5">HPP Toko</div>
                            </td>

                            <!-- Profit -->
                            <td class="py-2.5 px-3 text-right whitespace-nowrap align-top">
                                <div class="font-mono font-bold text-xs <?= $m['profit'] >= 0 ? 'text-emerald-700' : 'text-rose-600' ?>">
                                    <?= formatRupiah($m['profit']) ?>
                                </div>
                                <span class="inline-block mt-0.5 px-1.5 py-0.2 rounded text-[10px] font-bold <?= $m['profit'] >= 0 ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200' ?>">
                                    <?= ($m['profit_pct'] >= 0 ? '+' : '') . $m['profit_pct'] ?>%
                                </span>
                            </td>

                            <!-- Komponen -->
                            <td class="py-2.5 px-3 text-center whitespace-nowrap align-top">
                                <div class="font-bold text-slate-800 text-xs"><?= $m['item_count'] ?> Item</div>
                                <div class="text-[10px] text-slate-400 mt-0.5"><?= rtrim(rtrim(number_format($m['item_qty'], 2, ',', '.'), '0'), ',') ?> Unit</div>
                            </td>

                            <!-- Progress Rings -->
                            <td class="py-2 px-2 text-center whitespace-nowrap"><?= renderCircularProgress($p['dist_done'] ?? 0, $p['total_dist'] ?? 0, 'distribusi') ?></td>
                            <td class="py-2 px-2 text-center whitespace-nowrap"><?= renderCircularProgress($p['pesan_done'] ?? 0, $p['total_dist'] ?? 0, 'pemesanan') ?></td>
                            <td class="py-2 px-2 text-center whitespace-nowrap"><?= renderCircularProgress($p['dok_done'] ?? 0, $p['total_dist'] ?? 0, 'dokumentasi') ?></td>
                            <td class="py-2 px-2 text-center whitespace-nowrap"><?= renderCircularProgress($p['bast_done'] ?? 0, $p['total_bast'] ?? 0, 'bast') ?></td>

                            <!-- Pencairan -->
                            <td class="py-2.5 px-3 text-center whitespace-nowrap">
                                <?= getStatusBadge($p['status']) ?>
                            </td>

                            <!-- Aksi -->
                            <td class="py-2.5 px-3 text-right">
                                <a href="<?= APP_URL ?>/paket/detail/<?= $p['kode_paket'] ?: $p['id'] ?>"
                                   class="p-1.5 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors text-base inline-block"
                                   title="Lihat Detail Paket">
                                    <ion-icon name="arrow-forward-circle-outline"></ion-icon>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>

            <?php if (!empty($paketList)): ?>
                <tfoot class="bg-slate-50/95 border-t-2 border-slate-200 font-bold text-xs">
                    <tr>
                        <td colspan="2" class="py-3 px-3 text-slate-700 uppercase text-[11px] tracking-wider">
                            Overall Total (<span id="footPaketCount"><?= $stats['total_paket'] ?></span> Paket)
                        </td>
                        <td class="py-3 px-3 text-right font-mono text-slate-900 whitespace-nowrap" id="footTotalKontrak">
                            <?= formatRupiah($stats['total_acuan']) ?>
                        </td>
                        <td class="py-3 px-3 text-right font-mono text-slate-700 whitespace-nowrap" id="footTotalDpp">
                            <?= formatRupiah($stats['total_dpp']) ?>
                        </td>
                        <td class="py-3 px-3 text-right font-mono text-amber-700 whitespace-nowrap" id="footTotalPasal">
                            <?= formatRupiah($stats['total_pasal']) ?>
                        </td>
                        <td class="py-3 px-3 text-right font-mono text-emerald-700 whitespace-nowrap" id="footTotalBersih">
                            <?= formatRupiah($stats['total_bersih']) ?>
                        </td>
                        <td class="py-3 px-3 text-right font-mono text-slate-700 whitespace-nowrap" id="footTotalHpp">
                            <?= formatRupiah($stats['total_hpp']) ?>
                        </td>
                        <td class="py-3 px-3 text-right font-mono whitespace-nowrap <?= $stats['total_profit'] >= 0 ? 'text-emerald-700' : 'text-rose-600' ?>" id="footTotalProfit">
                            <?= formatRupiah($stats['total_profit']) ?>
                        </td>
                        <td class="py-3 px-3 text-center whitespace-nowrap text-slate-600 text-[11px]" id="footTotalKomponen">
                            <?= $stats['total_items'] ?> Item / <?= rtrim(rtrim(number_format($stats['total_units'], 2, ',', '.'), '0'), ',') ?> Unit
                        </td>
                        <td colspan="6" class="py-3 px-3"></td>
                    </tr>
                </tfoot>
            <?php endif; ?>
        </table>
    </div>
</div>

<!-- ========================================== -->
<!-- 5. POPUP MODAL: TAMBAH PAKET KE GROUP INI  -->
<!-- ========================================== -->
<div id="modalTambahPaketGroup" class="app-modal fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 transition-all duration-200 opacity-0 pointer-events-none hidden">
    <div class="modal-card bg-white rounded-2xl shadow-2xl border border-slate-100 max-w-lg w-full transform scale-95 opacity-0 transition-all duration-200 overflow-hidden">
        <div class="px-5 py-4 bg-slate-50/80 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-blue-600 text-white flex items-center justify-center text-lg shadow-xs">
                    <ion-icon name="cube-outline"></ion-icon>
                </div>
                <div>
                    <h3 class="font-bold text-slate-800 text-sm">Tambah Paket ke Group</h3>
                    <p class="text-[11px] text-slate-400">Group: <?= htmlspecialchars($group['nama_group']) ?></p>
                </div>
            </div>
            <button type="button" data-modal-close="modalTambahPaketGroup" class="p-1.5 text-slate-400 hover:text-slate-700 rounded-lg transition-colors cursor-pointer">
                <ion-icon name="close-outline" class="text-xl"></ion-icon>
            </button>
        </div>

        <form method="POST" action="<?= APP_URL ?>/paket/store" class="p-5 space-y-3.5">
            <?= csrfField() ?>
            <input type="hidden" name="group_id" value="<?= $group['id'] ?>">
            <input type="hidden" name="redirect_to" value="/group/detail/<?= $group['id'] ?>">

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Paket Kegiatan <span class="text-red-500">*</span></label>
                <input type="text" name="nama_paket" required
                       class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-blue-500 text-xs focus:bg-white transition-all"
                       placeholder="Contoh: Pengadaan Alat Laboratorium IPA">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Nilai Kontrak / Pagu Awal <span class="text-red-500">*</span></label>
                    <input type="text" name="pagu" id="gp_pagu" required
                           oninput="formatRupiahInput(this); syncGpNego(this.value);"
                           class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-blue-500 text-xs font-mono focus:bg-white transition-all"
                           placeholder="Rp 0">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Pagu Negosiasi (Acuan) <span class="text-red-500">*</span></label>
                    <input type="text" name="pagu_nego" id="gp_pagu_nego" required
                           oninput="this.dataset.edited='1'; formatRupiahInput(this);"
                           class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-blue-500 text-xs font-mono focus:bg-white transition-all"
                           placeholder="Rp 0">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Tahun Anggaran</label>
                    <input type="number" name="tahun_anggaran" value="<?= date('Y') ?>" min="2020" max="2035"
                           class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-blue-500 text-xs focus:bg-white transition-all">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Status Pencairan</label>
                    <select name="status" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-blue-500 text-xs focus:bg-white transition-all">
                        <option value="Proses" selected>Proses</option>
                        <option value="Belum">Belum</option>
                        <option value="Cair">Cair / Selesai</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Tags (Opsional)</label>
                    <input type="text" name="tags"
                           class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-blue-500 text-xs focus:bg-white transition-all"
                           placeholder="SMA, DAK">
                </div>
            </div>

            <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                <button type="button" data-modal-close="modalTambahPaketGroup"
                        class="px-3.5 py-2 rounded-xl border border-slate-200 text-slate-600 font-semibold hover:bg-slate-50 text-xs transition-colors cursor-pointer">
                    Batal
                </button>
                <button type="submit"
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl text-xs shadow-xs transition-all flex items-center gap-1.5 cursor-pointer">
                    <ion-icon name="checkmark-outline" class="text-sm"></ion-icon>
                    <span>Simpan Paket</span>
                </button>
            </div>
        </form>
    </div>
</div>

<?php require __DIR__ . '/_modals.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function () {
    requestAnimationFrame(() => {
        document.querySelectorAll('circle[data-ring-target]').forEach(el => {
            el.style.strokeDashoffset = el.getAttribute('data-ring-target');
        });
    });
});

function formatRupiahInput(el) {
    let val = el.value.replace(/[^0-9]/g, '');
    el.value = val ? 'Rp ' + parseInt(val, 10).toLocaleString('id-ID') : '';
}

function syncGpNego(val) {
    const nego = document.getElementById('gp_pagu_nego');
    if (nego && !nego.dataset.edited) {
        nego.value = val;
    }
}

function formatRpNum(num) {
    const n = Math.round(num || 0);
    const prefix = n < 0 ? '-Rp ' : 'Rp ';
    return prefix + Math.abs(n).toLocaleString('id-ID');
}

function filterGroupPaketTable() {
    const q = (document.getElementById('searchGroupPaket')?.value || '').toLowerCase().trim();
    const tahun = document.getElementById('filterGroupPaketTahun')?.value || '';
    const status = document.getElementById('filterGroupPaketStatus')?.value || '';

    const rows = document.querySelectorAll('.group-paket-row');
    let visible = 0;
    let sKontrak = 0, sDpp = 0, sPasal = 0, sBersih = 0, sHpp = 0, sProfit = 0, sItems = 0, sUnits = 0;

    rows.forEach(row => {
        const matchQ = !q || (row.dataset.search || '').includes(q);
        const matchT = !tahun || (row.dataset.tahun || '') === tahun;
        const matchS = !status || (row.dataset.status || '') === status;

        const show = matchQ && matchT && matchS;
        row.style.display = show ? '' : 'none';

        if (show) {
            visible++;
            sKontrak += parseFloat(row.dataset.kontrak || 0);
            sDpp     += parseFloat(row.dataset.dpp || 0);
            sPasal   += parseFloat(row.dataset.pasal || 0);
            sBersih  += parseFloat(row.dataset.bersih || 0);
            sHpp     += parseFloat(row.dataset.hpp || 0);
            sProfit  += parseFloat(row.dataset.profit || 0);
            sItems   += parseInt(row.dataset.items || 0, 10);
            sUnits   += parseFloat(row.dataset.units || 0);
        }
    });

    const cntEl = document.getElementById('visiblePaketCount');
    if (cntEl) cntEl.textContent = visible;

    const footCnt = document.getElementById('footPaketCount');
    if (footCnt) footCnt.textContent = visible;

    const setTxt = (id, txt) => { const el = document.getElementById(id); if (el) el.textContent = txt; };
    setTxt('footTotalKontrak', formatRpNum(sKontrak));
    setTxt('footTotalDpp', formatRpNum(sDpp));
    setTxt('footTotalPasal', formatRpNum(sPasal));
    setTxt('footTotalBersih', formatRpNum(sBersih));
    setTxt('footTotalHpp', formatRpNum(sHpp));

    const profitEl = document.getElementById('footTotalProfit');
    if (profitEl) {
        profitEl.textContent = formatRpNum(sProfit);
        profitEl.className = 'py-3 px-3 text-right font-mono whitespace-nowrap ' + (sProfit >= 0 ? 'text-emerald-700' : 'text-rose-600');
    }
    setTxt('footTotalKomponen', sItems + ' Item / ' + sUnits.toLocaleString('id-ID') + ' Unit');
}

function resetGroupPaketFilter() {
    const s = document.getElementById('searchGroupPaket');
    const t = document.getElementById('filterGroupPaketTahun');
    const st = document.getElementById('filterGroupPaketStatus');
    if (s) s.value = '';
    if (t) t.value = '';
    if (st) st.value = '';
    filterGroupPaketTable();
}
</script>
