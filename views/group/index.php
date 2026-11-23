<?php
/**
 * ProLedger - Daftar Group Paket Kegiatan
 * UI Modern dengan Summary KPI, Tabel Roll-up Finansial Lengkap (Kontrak, Bersih, HPP, Profit),
 * Progres Rata-rata, Overall Total Dinamis, dan Popup CRUD
 */

$allPaket = 0; $allKontrak = 0; $allBersih = 0; $allHpp = 0; $allProfit = 0;
$allCair = 0; $allProses = 0; $allBelum = 0;
foreach ($groups as $grp) {
    $allPaket  += (int)($grp['total_paket'] ?? 0);
    $allKontrak += (float)($grp['total_acuan'] ?? 0);
    $allBersih += (float)($grp['total_bersih'] ?? 0);
    $allHpp    += (float)($grp['total_hpp'] ?? 0);
    $allProfit += (float)($grp['total_profit'] ?? 0);
    $allCair   += (int)($grp['total_selesai'] ?? 0);
    $allProses += (int)($grp['total_proses'] ?? 0);
    $allBelum  += (int)($grp['total_belum'] ?? 0);
}
$allProfitPct = $allKontrak > 0 ? round(($allProfit / $allKontrak) * 100, 1) : 0;
?>

<!-- ========================================== -->
<!-- 1. HEADER & ACTIONS                        -->
<!-- ========================================== -->
<div class="mb-4 flex flex-wrap items-center justify-between gap-3">
    <div>
        <div class="flex items-center gap-2 mb-0.5 flex-wrap">
            <h1 class="text-lg md:text-xl font-extrabold text-slate-900 tracking-tight">Group Paket Kegiatan</h1>
            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                <?= count($groups) ?> Group
            </span>
        </div>
        <p class="text-xs text-slate-400">
            Kelompokkan paket kegiatan ke dalam program payung dan pantau roll-up kontrak, bersih, HPP, hingga profit.
        </p>
    </div>

    <div class="flex items-center gap-2">
        <a href="<?= APP_URL ?>/paket" 
           class="inline-flex items-center gap-1.5 px-3 py-2 bg-white hover:bg-slate-50 text-slate-700 font-semibold rounded-xl border border-slate-200 transition-all text-xs shadow-2xs hover:shadow-xs">
            <ion-icon name="cube-outline" class="text-sm"></ion-icon>
            <span>Semua Paket</span>
        </a>

        <?php if ($role === 'admin' || $role === 'user'): ?>
            <button type="button" 
                    data-modal-open="modalTambahGroup" 
                    class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl text-xs shadow-sm shadow-blue-600/20 transition-all hover:scale-[1.01] active:scale-[0.98] cursor-pointer">
                <ion-icon name="add-circle-outline" class="text-base"></ion-icon>
                <span>Tambah Group</span>
            </button>
        <?php endif; ?>
    </div>
</div>

<!-- ========================================== -->
<!-- 2. 4 SUMMARY KPI CARDS                     -->
<!-- ========================================== -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-4">
    <div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 p-4">
        <div class="flex items-center justify-between mb-1.5">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Total Group</span>
            <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center text-sm">
                <ion-icon name="folder-open-outline"></ion-icon>
            </div>
        </div>
        <div class="text-lg font-extrabold text-slate-900">
            <?= count($groups) ?> <span class="text-xs font-semibold text-slate-400">Program</span>
        </div>
        <div class="text-[10px] text-slate-400 mt-1">Berdasarkan perusahaan aktif</div>
    </div>

    <div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 p-4">
        <div class="flex items-center justify-between mb-1.5">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Paket Tergabung</span>
            <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center text-sm">
                <ion-icon name="layers-outline"></ion-icon>
            </div>
        </div>
        <div class="text-lg font-extrabold text-slate-900">
            <?= $allPaket ?> <span class="text-xs font-semibold text-slate-400">Paket</span>
        </div>
        <div class="flex items-center gap-1.5 text-[10px] mt-1">
            <span class="text-emerald-600 font-semibold"><?= $allCair ?> Cair</span>
            <span class="text-slate-300">&bull;</span>
            <span class="text-rose-600 font-semibold"><?= $allProses ?> Proses</span>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 p-4">
        <div class="flex items-center justify-between mb-1.5">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Total Nilai Kontrak</span>
            <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center text-sm">
                <ion-icon name="cash-outline"></ion-icon>
            </div>
        </div>
        <div class="text-lg font-extrabold text-slate-900 font-mono truncate" title="<?= formatRupiah($allKontrak) ?>">
            <?= formatRupiah($allKontrak) ?>
        </div>
        <div class="text-[10px] text-slate-400 mt-1">Bersih: <?= formatRupiah($allBersih) ?></div>
    </div>

    <div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 p-4">
        <div class="flex items-center justify-between mb-1.5">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Total Estimasi Profit</span>
            <span class="px-1.5 py-0.5 rounded-md text-[10px] font-extrabold <?= $allProfit >= 0 ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200' ?>">
                <?= ($allProfitPct >= 0 ? '+' : '') . $allProfitPct ?>%
            </span>
        </div>
        <div class="text-lg font-extrabold font-mono truncate <?= $allProfit >= 0 ? 'text-emerald-700' : 'text-rose-600' ?>" title="<?= formatRupiah($allProfit) ?>">
            <?= formatRupiah($allProfit) ?>
        </div>
        <div class="text-[10px] text-slate-400 mt-1">HPP: <?= formatRupiah($allHpp) ?></div>
    </div>
</div>

<!-- ========================================== -->
<!-- 3. GROUPS TABLE CARD                       -->
<!-- ========================================== -->
<div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 overflow-hidden">
    <div class="p-3.5 px-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div class="relative w-full sm:w-80">
            <ion-icon name="search-outline" class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-base"></ion-icon>
            <input type="text" id="searchGroupInput" onkeyup="filterGroupTable()" 
                   placeholder="Cari nama group, kode, atau perusahaan..." 
                   class="w-full pl-9 pr-3 py-1.5 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-blue-500 text-xs focus:bg-white transition-all">
        </div>
        <div class="text-[11px] text-slate-400">
            Menampilkan <strong class="text-slate-600" id="visibleGroupCount"><?= count($groups) ?></strong> group
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left text-xs" id="groupTable">
            <thead class="bg-slate-50/80 text-[10px] uppercase tracking-wider text-slate-500 font-bold border-b border-slate-200/80">
                <tr>
                    <th class="py-3 px-4">Nama Group / Program</th>
                    <th class="py-3 px-3">Perusahaan</th>
                    <th class="py-3 px-3 text-center">Paket</th>
                    <th class="py-3 px-3 text-right">Nilai Kontrak</th>
                    <th class="py-3 px-3 text-right">Nilai Bersih</th>
                    <th class="py-3 px-3 text-right">HPP</th>
                    <th class="py-3 px-3 text-right">Estimasi Profit</th>
                    <th class="py-3 px-3 text-center">Progres Rata-rata</th>
                    <th class="py-3 px-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php if (empty($groups)): ?>
                    <tr>
                        <td colspan="9" class="py-12 text-center text-slate-400">
                            <div class="w-14 h-14 bg-slate-50 rounded-2xl flex items-center justify-center mx-auto mb-3 text-slate-300 text-3xl">
                                <ion-icon name="folder-open-outline"></ion-icon>
                            </div>
                            <h4 class="text-sm font-bold text-slate-700 mb-1">Belum Ada Group Paket</h4>
                            <p class="text-xs text-slate-400 max-w-sm mx-auto mb-4">
                                Buat group untuk mengelompokkan paket kegiatan di bawah kontrak atau program yang sama (misal: Dinas Pendidikan 2025).
                            </p>
                            <?php if ($role === 'admin' || $role === 'user'): ?>
                                <button type="button" 
                                        data-modal-open="modalTambahGroup" 
                                        class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl text-xs shadow-xs transition-all cursor-pointer">
                                    <ion-icon name="add-circle-outline"></ion-icon>
                                    <span>Buat Group Pertama</span>
                                </button>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($groups as $g):
                        $avgProg = (int)round((($g['order_pct'] ?? 0) + ($g['dist_pct'] ?? 0) + ($g['dok_pct'] ?? 0) + ($g['bast_pct'] ?? 0)) / 4);
                    ?>
                        <tr class="hover:bg-slate-50/70 transition-colors group-row"
                            data-search="<?= htmlspecialchars(strtolower(($g['nama_group'] ?? '') . ' ' . ($g['kode_group'] ?? '') . ' ' . ($g['deskripsi'] ?? '') . ' ' . ($g['perusahaan_nama'] ?? ''))) ?>"
                            data-paket="<?= (int)($g['total_paket'] ?? 0) ?>"
                            data-kontrak="<?= (float)($g['total_acuan'] ?? 0) ?>"
                            data-bersih="<?= (float)($g['total_bersih'] ?? 0) ?>"
                            data-hpp="<?= (float)($g['total_hpp'] ?? 0) ?>"
                            data-profit="<?= (float)($g['total_profit'] ?? 0) ?>">

                            <!-- Nama Group & Kode -->
                            <td class="py-3 px-4 min-w-[220px]">
                                <div class="flex items-start gap-2.5">
                                    <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-indigo-50 to-blue-50 text-indigo-600 border border-indigo-100 flex items-center justify-center text-base shrink-0 mt-0.5">
                                        <ion-icon name="folder-outline"></ion-icon>
                                    </div>
                                    <div class="min-w-0">
                                        <a href="<?= APP_URL ?>/group/detail/<?= $g['id'] ?>" class="font-bold text-slate-800 hover:text-blue-600 text-xs transition-colors block truncate">
                                            <?= htmlspecialchars($g['nama_group']) ?>
                                        </a>
                                        <div class="flex items-center gap-1.5 mt-0.5 flex-wrap">
                                            <?php if (!empty($g['kode_group'])): ?>
                                                <span class="inline-block px-1.5 py-0.2 rounded bg-slate-100 text-slate-600 font-mono text-[10px] font-semibold">
                                                    <?= htmlspecialchars($g['kode_group']) ?>
                                                </span>
                                            <?php endif; ?>
                                            <?php if (!empty($g['deskripsi'])): ?>
                                                <span class="text-[10px] text-slate-400 truncate max-w-[200px]" title="<?= htmlspecialchars($g['deskripsi']) ?>">
                                                    <?= htmlspecialchars($g['deskripsi']) ?>
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Perusahaan -->
                            <td class="py-3 px-3 text-xs text-slate-600 whitespace-nowrap">
                                <?= htmlspecialchars($g['perusahaan_nama'] ?: 'Umum / Semua') ?>
                            </td>

                            <!-- Paket & Status -->
                            <td class="py-3 px-3 text-center whitespace-nowrap">
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full font-semibold text-[11px] <?= $g['total_paket'] > 0 ? 'bg-blue-50 text-blue-700 border border-blue-200' : 'bg-slate-100 text-slate-500' ?>">
                                    <span><?= $g['total_paket'] ?> Paket</span>
                                </span>
                                <?php if ($g['total_paket'] > 0): ?>
                                    <div class="text-[10px] text-slate-400 mt-0.5">
                                        <span class="text-emerald-600 font-semibold"><?= $g['total_selesai'] ?></span> Cair &bull;
                                        <span class="text-rose-600 font-semibold"><?= $g['total_proses'] ?></span> Proses
                                    </div>
                                <?php endif; ?>
                            </td>

                            <!-- Nilai Kontrak -->
                            <td class="py-3 px-3 text-right whitespace-nowrap font-mono font-bold text-slate-900 text-xs">
                                <?= formatRupiah($g['total_acuan'] ?? 0) ?>
                            </td>

                            <!-- Nilai Bersih -->
                            <td class="py-3 px-3 text-right whitespace-nowrap font-mono font-bold text-emerald-700 text-xs">
                                <?= formatRupiah($g['total_bersih'] ?? 0) ?>
                            </td>

                            <!-- HPP -->
                            <td class="py-3 px-3 text-right whitespace-nowrap font-mono font-semibold text-slate-700 text-xs">
                                <?= formatRupiah($g['total_hpp'] ?? 0) ?>
                            </td>

                            <!-- Profit -->
                            <td class="py-3 px-3 text-right whitespace-nowrap">
                                <div class="font-mono font-bold text-xs <?= ($g['total_profit'] ?? 0) >= 0 ? 'text-emerald-700' : 'text-rose-600' ?>">
                                    <?= formatRupiah($g['total_profit'] ?? 0) ?>
                                </div>
                                <span class="inline-block mt-0.5 px-1.5 py-0.2 rounded text-[10px] font-bold <?= ($g['total_profit'] ?? 0) >= 0 ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200' ?>">
                                    <?= (($g['profit_pct'] ?? 0) >= 0 ? '+' : '') . ($g['profit_pct'] ?? 0) ?>%
                                </span>
                            </td>

                            <!-- Progres Rata-rata -->
                            <td class="py-3 px-3 text-center whitespace-nowrap">
                                <div class="w-24 mx-auto">
                                    <div class="flex items-center justify-between text-[10px] mb-1">
                                        <span class="text-slate-400 font-medium">Rata-rata</span>
                                        <span class="font-bold text-slate-700"><?= $avgProg ?>%</span>
                                    </div>
                                    <div class="w-full h-1.5 bg-slate-100 rounded-full overflow-hidden">
                                        <div class="h-full bg-gradient-to-r from-blue-500 to-indigo-600 rounded-full" style="width: <?= $avgProg ?>%"></div>
                                    </div>
                                </div>
                            </td>

                            <!-- Aksi -->
                            <td class="py-3 px-4 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="<?= APP_URL ?>/group/detail/<?= $g['id'] ?>" 
                                       class="p-1.5 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors text-base"
                                       title="Lihat Detail & Rekapitulasi">
                                        <ion-icon name="eye-outline"></ion-icon>
                                    </a>

                                    <?php if ($role === 'admin' || $role === 'user'): ?>
                                        <button type="button" 
                                                onclick='openEditGroupModal(<?= json_encode($g, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)'
                                                class="p-1.5 text-slate-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-colors text-base cursor-pointer"
                                                title="Edit Group">
                                            <ion-icon name="create-outline"></ion-icon>
                                        </button>

                                        <a href="<?= APP_URL ?>/group/delete/<?= $g['id'] ?>" 
                                           data-confirm-delete="<?= htmlspecialchars($g['nama_group']) ?>"
                                           data-type="Group Paket"
                                           class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors text-base"
                                           title="Hapus Group">
                                            <ion-icon name="trash-outline"></ion-icon>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>

            <?php if (!empty($groups)): ?>
                <tfoot class="bg-slate-50/95 border-t-2 border-slate-200 font-bold text-xs">
                    <tr>
                        <td colspan="2" class="py-3 px-4 text-slate-700 uppercase text-[11px] tracking-wider">
                            Overall Total (<span id="footGroupCount"><?= count($groups) ?></span> Group)
                        </td>
                        <td class="py-3 px-3 text-center text-slate-700" id="footGroupPaket"><?= $allPaket ?> Paket</td>
                        <td class="py-3 px-3 text-right font-mono text-slate-900 whitespace-nowrap" id="footGroupKontrak"><?= formatRupiah($allKontrak) ?></td>
                        <td class="py-3 px-3 text-right font-mono text-emerald-700 whitespace-nowrap" id="footGroupBersih"><?= formatRupiah($allBersih) ?></td>
                        <td class="py-3 px-3 text-right font-mono text-slate-700 whitespace-nowrap" id="footGroupHpp"><?= formatRupiah($allHpp) ?></td>
                        <td class="py-3 px-3 text-right font-mono whitespace-nowrap <?= $allProfit >= 0 ? 'text-emerald-700' : 'text-rose-600' ?>" id="footGroupProfit"><?= formatRupiah($allProfit) ?></td>
                        <td colspan="2" class="py-3 px-4"></td>
                    </tr>
                </tfoot>
            <?php endif; ?>
        </table>
    </div>
</div>

<?php require __DIR__ . '/_modals.php'; ?>

<script>
function formatRpGroup(num) {
    const n = Math.round(num || 0);
    return (n < 0 ? '-Rp ' : 'Rp ') + Math.abs(n).toLocaleString('id-ID');
}

function filterGroupTable() {
    const q = (document.getElementById('searchGroupInput')?.value || '').toLowerCase().trim();
    const rows = document.querySelectorAll('.group-row');
    let vis = 0, sPaket = 0, sKontrak = 0, sBersih = 0, sHpp = 0, sProfit = 0;

    rows.forEach(row => {
        const show = !q || (row.dataset.search || '').includes(q);
        row.style.display = show ? '' : 'none';
        if (show) {
            vis++;
            sPaket   += parseInt(row.dataset.paket || 0, 10);
            sKontrak += parseFloat(row.dataset.kontrak || 0);
            sBersih  += parseFloat(row.dataset.bersih || 0);
            sHpp     += parseFloat(row.dataset.hpp || 0);
            sProfit  += parseFloat(row.dataset.profit || 0);
        }
    });

    const setTxt = (id, txt) => { const el = document.getElementById(id); if (el) el.textContent = txt; };
    setTxt('visibleGroupCount', vis);
    setTxt('footGroupCount', vis);
    setTxt('footGroupPaket', sPaket + ' Paket');
    setTxt('footGroupKontrak', formatRpGroup(sKontrak));
    setTxt('footGroupBersih', formatRpGroup(sBersih));
    setTxt('footGroupHpp', formatRpGroup(sHpp));

    const pEl = document.getElementById('footGroupProfit');
    if (pEl) {
        pEl.textContent = formatRpGroup(sProfit);
        pEl.className = 'py-3 px-3 text-right font-mono whitespace-nowrap ' + (sProfit >= 0 ? 'text-emerald-700' : 'text-rose-600');
    }
}
</script>
