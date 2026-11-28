<?php
/**
 * Modal View: Alokasi Distribusi Titik & Analisa Per Tujuan
 */
$role = $_SESSION['admin_role'] ?? 'user';
$totalDist = count($distribusiList);
$distDone = 0;
$totalQty = 0;
foreach ($distribusiList as $dist) {
    $s = trim($dist['status_distribusi'] ?? '');
    if ($s === 'Tiba di Tujuan' || $s === 'Tiba di Gudang') {
        $distDone++;
    }
    $totalQty += (float)$dist['jumlah'];
}
$pctDist = ($totalDist > 0) ? round(($distDone / $totalDist) * 100) : 0;

// Siapkan Analisa Distribusi Per Tujuan untuk Paket ini
$tujuanCards = [];
$analisaTotals = [
    'tujuan'         => 0,
    'items'          => 0,
    'terkirim'       => 0,
    'sedang_dikirim' => 0,
    'belum_dipesan'  => 0,
];
$kotaOptions = [];

foreach (($groupedDistribusi ?? []) as $grp) {
    $namaTujuan = trim((string)($grp['nama_tujuan'] ?? ''));
    if ($namaTujuan === '') continue;

    $kabKota = trim((string)($grp['kab_kota'] ?? ''));
    if ($kabKota !== '') {
        $kotaOptions[$kabKota] = true;
    }

    $cntTerkirim = 0;
    $cntSedang = 0;
    $cntBelum = 0;
    $cardItems = [];

    foreach (($grp['items'] ?? []) as $it) {
        $isOrdered = (($it['status_barang'] ?? '') === 'Sudah Dipesan');
        $sDist = trim((string)($it['status_distribusi'] ?? ''));
        if (!$isOrdered) {
            $bucket = 'belum_dipesan';
            $statusLabel = 'Belum Dipesan';
            $cntBelum++;
            $analisaTotals['belum_dipesan']++;
        } elseif ($sDist === 'Tiba di Tujuan' || $sDist === 'Tiba di Gudang') {
            $bucket = 'terkirim';
            $statusLabel = $sDist;
            $cntTerkirim++;
            $analisaTotals['terkirim']++;
        } else {
            $bucket = 'sedang_dikirim';
            $statusLabel = $sDist !== '' ? $sDist : 'Sudah Dipesan / Menunggu Kirim';
            $cntSedang++;
            $analisaTotals['sedang_dikirim']++;
        }

        $analisaTotals['items']++;
        $it['_bucket'] = $bucket;
        $it['_status_label'] = $statusLabel;
        $cardItems[] = $it;
    }

    $tujuanCards[] = [
        'nama_tujuan'   => $namaTujuan,
        'kab_kota'      => $kabKota,
        'nama_pic'      => trim((string)($grp['nama_pic'] ?? '')),
        'no_kontak'     => trim((string)($grp['no_kontak'] ?? '')),
        'total_qty'     => (float)($grp['total_qty'] ?? 0),
        'cnt_terkirim'  => $cntTerkirim,
        'cnt_sedang'    => $cntSedang,
        'cnt_belum'     => $cntBelum,
        'pending_count' => $cntSedang + $cntBelum,
        'items'         => $cardItems,
    ];
}

usort($tujuanCards, fn($a, $b) => strcasecmp($a['nama_tujuan'], $b['nama_tujuan']));
$analisaTotals['tujuan'] = count($tujuanCards);
$kotaList = array_keys($kotaOptions);
sort($kotaList);
?>

<!-- Header Action Teleport Source -->
<div id="modalActionSource" class="hidden">
    <div class="flex items-center gap-2">
        <span class="text-xs font-bold px-2.5 py-1 rounded-lg bg-indigo-50 text-indigo-700 border border-indigo-200 hidden sm:inline-flex items-center gap-1">
            <ion-icon name="navigate-outline"></ion-icon>
            <span>Monitoring Tiba: <?= $distDone ?>/<?= $totalDist ?> (<?= $pctDist ?>%)</span>
        </span>
    </div>
</div>

<div class="space-y-3">
    <!-- View Switcher: Semua Baris vs Analisa Per Tujuan -->
    <div class="flex flex-wrap items-center justify-between gap-2">
        <div class="inline-flex items-center p-1 bg-slate-100 rounded-xl border border-slate-200/70">
            <button type="button" id="btnSwitchAllRows" onclick="switchPaketDistView('rows')"
                    class="px-3 py-1.5 rounded-lg text-xs font-bold bg-white text-indigo-700 shadow-2xs cursor-pointer transition-all inline-flex items-center gap-1.5">
                <ion-icon name="list-outline"></ion-icon>
                <span>Semua Baris (<?= $totalDist ?>)</span>
            </button>
            <button type="button" id="btnSwitchPerTujuan" onclick="switchPaketDistView('tujuan')"
                    class="px-3 py-1.5 rounded-lg text-xs font-semibold text-slate-600 hover:text-slate-800 cursor-pointer transition-all inline-flex items-center gap-1.5">
                <ion-icon name="location-outline"></ion-icon>
                <span>Analisa Per Tujuan (<?= $analisaTotals['tujuan'] ?>)</span>
            </button>
        </div>
        <span class="text-[11px] text-slate-500">
            Total Alokasi: <strong class="text-slate-800"><?= number_format($totalQty, 0, ',', '.') ?> Unit</strong>
        </span>
    </div>

    <!-- ========================================== -->
    <!-- TAB 1: ANALISA PER TUJUAN -->
    <!-- ========================================== -->
    <div id="paketDistViewTujuan" class="hidden space-y-2.5">
        <!-- Cards Per Tujuan -->
        <div class="space-y-2.5">
            <?php if (empty($tujuanCards)): ?>
                <div class="bg-white rounded-xl border border-slate-200/80 py-8 text-center text-slate-400 text-xs">
                    <div class="w-10 h-10 bg-slate-50 rounded-2xl flex items-center justify-center mx-auto mb-2 text-xl text-slate-300">
                        <ion-icon name="navigate-outline"></ion-icon>
                    </div>
                    <h4 class="text-xs font-bold text-slate-700 mb-0.5">Belum Ada Alokasi Distribusi</h4>
                    <p class="text-[11px] text-slate-400">Tambahkan alokasi barang melalui tab <strong>Item Barang</strong> (kolom Alokasi).</p>
                </div>
            <?php else: ?>
                <?php foreach ($tujuanCards as $idx => $tc):
                    $searchBlob = strtolower($tc['nama_tujuan'] . ' ' . $tc['kab_kota'] . ' ' . implode(' ', array_map(fn($x) => ($x['nama_barang'] ?? $x['nama_komponen'] ?? '') . ' ' . ($x['no_resi'] ?? ''), $tc['items'])));
                ?>
                    <div class="pkt-tujuan-card bg-white rounded-xl border border-slate-200/90 overflow-hidden shadow-2xs"
                         data-search="<?= htmlspecialchars($searchBlob) ?>"
                         data-kota="<?= htmlspecialchars(strtolower($tc['kab_kota'])) ?>"
                         data-pending="<?= (int)$tc['pending_count'] ?>"
                         data-sedang="<?= (int)$tc['cnt_sedang'] ?>"
                         data-belum="<?= (int)$tc['cnt_belum'] ?>"
                         data-terkirim="<?= (int)$tc['cnt_terkirim'] ?>">

                        <div class="px-3.5 py-2.5 bg-slate-50/80 border-b border-slate-100 flex flex-wrap items-center justify-between gap-2 cursor-pointer"
                             onclick="this.nextElementSibling.classList.toggle('hidden')">
                            <div class="min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="font-bold text-slate-800 text-xs sm:text-sm"><?= htmlspecialchars($tc['nama_tujuan']) ?></span>
                                    <?php if ($tc['kab_kota'] !== ''): ?>
                                        <span class="px-2 py-0.5 rounded-md bg-white border border-slate-200 text-slate-600 text-[10px] font-semibold">
                                            <?= htmlspecialchars($tc['kab_kota']) ?>
                                        </span>
                                    <?php endif; ?>
                                    <span class="px-2 py-0.5 rounded-md bg-indigo-50 text-indigo-700 border border-indigo-200 text-[10px] font-bold">
                                        <?= count($tc['items']) ?> Item &bull; <?= (float)$tc['total_qty'] ?> Unit
                                    </span>
                                </div>
                            </div>

                            <div class="flex items-center gap-1.5 flex-wrap text-[10px]">
                                <?php if ($tc['cnt_terkirim'] > 0): ?>
                                    <span class="px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 font-bold">
                                        ✓ <?= $tc['cnt_terkirim'] ?> Terkirim
                                    </span>
                                <?php endif; ?>
                                <?php if ($tc['cnt_sedang'] > 0): ?>
                                    <span class="px-2 py-0.5 rounded-full bg-amber-50 text-amber-800 border border-amber-200 font-bold">
                                        <?= $tc['cnt_sedang'] ?> Sedang Dikirim
                                    </span>
                                <?php endif; ?>
                                <?php if ($tc['cnt_belum'] > 0): ?>
                                    <span class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-600 border border-slate-200 font-bold">
                                        <?= $tc['cnt_belum'] ?> Belum Dipesan
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="overflow-x-auto <?= ($tc['pending_count'] === 0 && $idx > 2) ? 'hidden' : '' ?>">
                            <table class="w-full text-left text-xs table-fixed">
                                <thead class="bg-white text-[10px] uppercase text-slate-400 font-bold border-b border-slate-100">
                                    <tr>
                                        <th class="py-2 px-3 w-10 text-center">No</th>
                                        <th class="py-2 px-3">Item Barang</th>
                                        <th class="py-2 px-2 text-center w-20">Qty</th>
                                        <th class="py-2 px-2 text-center w-52">Status</th>
                                        <th class="py-2 px-3 w-36">Ekspedisi / Resi</th>
                                        <?php if ($role === 'admin' || $role === 'user'): ?>
                                            <th class="py-2 px-3 text-center w-14">Aksi</th>
                                        <?php endif; ?>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    <?php $subNo = 1; foreach ($tc['items'] as $it):
                                        $bCls = $it['_bucket'] === 'terkirim'
                                            ? 'bg-emerald-50 text-emerald-700 border-emerald-200 font-bold'
                                            : ($it['_bucket'] === 'sedang_dikirim' ? 'bg-amber-50 text-amber-800 border-amber-200 font-semibold' : 'bg-slate-100 text-slate-600 border-slate-200');
                                    ?>
                                        <tr class="pkt-dist-item-row hover:bg-slate-50/70" data-bucket="<?= $it['_bucket'] ?>">
                                            <td class="py-2 px-3 text-center text-slate-400 font-mono text-[11px]"><?= $subNo++ ?></td>
                                            <td class="py-2 px-3 font-semibold text-slate-800">
                                                <?= htmlspecialchars($it['nama_barang'] ?? $it['nama_komponen'] ?? '-') ?>
                                            </td>
                                            <td class="py-2 px-2 text-center font-bold whitespace-nowrap">
                                                <?= (float)$it['jumlah'] ?> <?= htmlspecialchars($it['satuan'] ?? 'Unit') ?>
                                            </td>
                                            <td class="py-2 px-2 text-center whitespace-nowrap">
                                                <?php if ($role === 'admin' || $role === 'user'): ?>
                                                    <button type="button"
                                                            onclick='openNativeUpdateStatusDistribusiModal(<?= json_encode($it, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)'
                                                            class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] border cursor-pointer hover:scale-102 transition-all <?= $bCls ?>"
                                                            title="Klik untuk mengubah status pengiriman">
                                                        <span><?= htmlspecialchars($it['_status_label']) ?></span>
                                                        <ion-icon name="create-outline" class="text-[10px] opacity-60"></ion-icon>
                                                    </button>
                                                <?php else: ?>
                                                    <span class="px-2 py-0.5 rounded-md text-[10px] border <?= $bCls ?>">
                                                        <?= htmlspecialchars($it['_status_label']) ?>
                                                    </span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="py-2 px-3 text-[11px] text-slate-500 whitespace-nowrap">
                                                <?php if (!empty($it['ekspedisi']) || !empty($it['no_resi'])): ?>
                                                    <span class="font-medium text-slate-700"><?= htmlspecialchars($it['ekspedisi'] ?: 'Kurir') ?></span>
                                                    <?= !empty($it['no_resi']) ? ' &bull; <span class="font-mono text-[10px]">' . htmlspecialchars($it['no_resi']) . '</span>' : '' ?>
                                                <?php else: ?>
                                                    <span class="text-slate-400 italic">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <?php if ($role === 'admin' || $role === 'user'): ?>
                                                <td class="py-2 px-3 text-center whitespace-nowrap">
                                                    <button type="button"
                                                            onclick="deleteDistribusiAjax('<?= $paket['kode_paket'] ?: $paket['id'] ?>', '<?= $it['id'] ?>')"
                                                            class="p-1 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors text-xs cursor-pointer"
                                                            title="Hapus Alokasi">
                                                        <ion-icon name="trash-outline" class="text-sm"></ion-icon>
                                                    </button>
                                                </td>
                                            <?php endif; ?>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- TAB 2: SEMUA BARIS (TABEL FLAT) -->
    <!-- ========================================== -->
    <div id="paketDistViewRows" class="space-y-3">
        <div class="bg-white rounded-xl border border-slate-200/90 overflow-hidden shadow-2xs">
        <table class="w-full text-left text-xs table-fixed">
            <thead class="bg-slate-50 text-[11px] font-bold text-slate-500 uppercase tracking-wider border-b border-slate-200/80">
                <tr>
                    <th class="py-2.5 px-3 w-10 text-center">NO</th>
                    <th class="py-2.5 px-3 w-44">KOMPONEN BARANG</th>
                    <th class="py-2.5 px-3">TITIK TUJUAN</th>
                    <th class="py-2.5 px-2 text-center w-16">QTY</th>
                    <th class="py-2.5 px-2 text-center w-36">STATUS DISTRIBUSI</th>
                    <th class="py-2.5 px-3 w-36">PENGIRIMAN & RESI</th>
                    <?php if ($role === 'admin' || $role === 'user'): ?>
                        <th class="py-2.5 px-3 text-center w-12">AKSI</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php if (empty($distribusiList)): ?>
                    <tr>
                        <td colspan="7" class="py-8 text-center text-slate-400 text-xs">
                            <div class="w-10 h-10 bg-slate-50 rounded-2xl flex items-center justify-center mx-auto mb-2 text-xl text-slate-300">
                                <ion-icon name="navigate-outline"></ion-icon>
                            </div>
                            <h4 class="text-xs font-bold text-slate-700 mb-0.5">Belum Ada Alokasi Distribusi</h4>
                            <p class="text-[11px] text-slate-400">Tambahkan alokasi barang melalui popup <strong>Komponen Item Barang</strong> (kolom Alokasi).</p>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php $no = 1; foreach ($distribusiList as $dist): ?>
                        <?php 
                        $sDist = trim($dist['status_distribusi'] ?? '');
                        if (empty($sDist)) {
                            $sDist = 'Belum Dikirim';
                            $badgeClass = 'bg-slate-100 text-slate-600 border-slate-200';
                        } elseif ($sDist === 'Tiba di Tujuan') {
                            $badgeClass = 'bg-emerald-50 text-emerald-700 border-emerald-200 font-bold';
                        } elseif ($sDist === 'Tiba di Gudang') {
                            $badgeClass = 'bg-blue-50 text-blue-700 border-blue-200 font-bold';
                        } elseif ($sDist === 'Tiba di Rumah' || stripos($sDist, 'rumah') !== false || stripos($sDist, 'kantor') !== false) {
                            $sDist = 'Tiba di Rumah';
                            $badgeClass = 'bg-amber-50 text-amber-800 border-amber-200 font-semibold';
                        } else {
                            $badgeClass = 'bg-slate-100 text-slate-600 border-slate-200';
                        }
                        ?>
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="py-2.5 px-3 text-center text-slate-400 font-mono text-[11px]"><?= $no++ ?></td>
                            <td class="py-2.5 px-3 text-xs font-bold text-slate-800 break-words">
                                <?= htmlspecialchars($dist['nama_barang'] ?? $dist['nama_komponen'] ?? '-') ?>
                            </td>
                            <td class="py-2.5 px-3 text-xs text-slate-700 font-normal">
                                <div class="truncate" title="<?= htmlspecialchars($dist['nama_tujuan'] ?? '-') ?>">
                                    <?= htmlspecialchars($dist['nama_tujuan'] ?? '-') ?>
                                </div>
                            </td>
                            <td class="py-2.5 px-2 text-center whitespace-nowrap font-bold text-slate-800 text-xs">
                                <?= (float)$dist['jumlah'] ?> <?= htmlspecialchars($dist['satuan'] ?? 'Unit') ?>
                            </td>
                            <td class="py-2.5 px-2 text-center whitespace-nowrap">
                                <?php if ($role === 'admin' || $role === 'user'): ?>
                                    <button type="button" 
                                            onclick='openNativeUpdateStatusDistribusiModal(<?= json_encode($dist, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)'
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[11px] font-semibold border transition-all cursor-pointer shadow-2xs hover:scale-102 <?= $badgeClass ?>"
                                            title="Klik untuk mengubah status pengiriman">
                                        <span><?= htmlspecialchars($sDist) ?></span>
                                        <ion-icon name="create-outline" class="text-[10px] opacity-60"></ion-icon>
                                    </button>
                                <?php else: ?>
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-medium border <?= $badgeClass ?>">
                                        <?= htmlspecialchars($sDist) ?>
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="py-2.5 px-3 text-xs text-slate-600 break-words">
                                <?php if (!empty($dist['ekspedisi']) || !empty($dist['no_resi'])): ?>
                                    <div class="font-medium text-slate-800"><?= htmlspecialchars($dist['ekspedisi'] ?: 'Kurir') ?></div>
                                    <div class="text-[10px] text-slate-400 font-mono"><?= htmlspecialchars($dist['no_resi'] ?: '-') ?></div>
                                <?php else: ?>
                                    <span class="text-slate-400 italic text-[11px]">-</span>
                                <?php endif; ?>
                            </td>
                            <?php if ($role === 'admin' || $role === 'user'): ?>
                                <td class="py-2.5 px-3 text-center whitespace-nowrap">
                                    <button type="button" 
                                            onclick="deleteDistribusiAjax('<?= $paket['kode_paket'] ?: $paket['id'] ?>', '<?= $dist['id'] ?>')"
                                            class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors text-xs cursor-pointer"
                                            title="Hapus Alokasi">
                                        <ion-icon name="trash-outline" class="text-sm"></ion-icon>
                                    </button>
                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
            <!-- 2. Ringkasan Info Menyatu di Kolom Bawah Tabel (tfoot) -->
            <tfoot class="bg-slate-50/90 font-bold text-slate-700 border-t-2 border-slate-200">
                <tr>
                    <td class="py-2.5 px-3 text-center text-slate-400 font-mono text-[11px]">-</td>
                    <td colspan="2" class="py-2.5 px-3 text-xs text-slate-800">
                        <span class="font-normal text-slate-500">Total Alokasi:</span> <?= $totalDist ?> Titik Pengiriman
                    </td>
                    <td class="py-2.5 px-2 text-center text-xs font-extrabold text-slate-900 whitespace-nowrap">
                        <?= number_format($totalQty, 0, ',', '.') ?> Unit
                    </td>
                    <td class="py-2.5 px-2 text-center text-xs whitespace-nowrap">
                        <span class="text-[11px] font-bold text-emerald-700"><?= $distDone ?>/<?= $totalDist ?> Tiba</span>
                    </td>
                    <td class="py-2.5 px-3 text-xs text-slate-400 font-normal">
                        -
                    </td>
                    <?php if ($role === 'admin' || $role === 'user'): ?>
                        <td class="py-2.5 px-3"></td>
                    <?php endif; ?>
                </tr>
            </tfoot>
        </table>
    </div>

    <!-- 5. Informasi Tambahan di Bawah Tabel -->
    <div class="flex items-center justify-between text-[11px] text-slate-500 px-1">
        <div class="flex items-center gap-1.5">
            <ion-icon name="information-circle-outline" class="text-indigo-500 text-sm"></ion-icon>
            <span>Status <strong>"Tiba di Gudang"</strong> & <strong>"Tiba di Tujuan"</strong> menandakan barang sudah sampai & siap diterbitkan BAST. Status <strong>"Tiba di Rumah"</strong> adalah transit.</span>
        </div>
        <span class="font-medium text-slate-600">Realisasi Distribusi: <strong><?= $distDone ?></strong> dari <strong><?= $totalDist ?></strong> lokasi (<?= $pctDist ?>%)</span>
    </div>
    </div>
</div>

<script>
window.paketDistBucket = 'all';

function switchPaketDistView(mode) {
    const viewTujuan = document.getElementById('paketDistViewTujuan');
    const viewRows = document.getElementById('paketDistViewRows');
    const btnTujuan = document.getElementById('btnSwitchPerTujuan');
    const btnRows = document.getElementById('btnSwitchAllRows');
    if (!viewTujuan || !viewRows) return;

    if (mode === 'rows') {
        viewTujuan.classList.add('hidden');
        viewRows.classList.remove('hidden');
        btnRows.className = 'px-3 py-1.5 rounded-lg text-xs font-bold bg-white text-indigo-700 shadow-2xs cursor-pointer transition-all inline-flex items-center gap-1.5';
        btnTujuan.className = 'px-3 py-1.5 rounded-lg text-xs font-semibold text-slate-600 hover:text-slate-800 cursor-pointer transition-all inline-flex items-center gap-1.5';
    } else {
        viewRows.classList.add('hidden');
        viewTujuan.classList.remove('hidden');
        btnTujuan.className = 'px-3 py-1.5 rounded-lg text-xs font-bold bg-white text-indigo-700 shadow-2xs cursor-pointer transition-all inline-flex items-center gap-1.5';
        btnRows.className = 'px-3 py-1.5 rounded-lg text-xs font-semibold text-slate-600 hover:text-slate-800 cursor-pointer transition-all inline-flex items-center gap-1.5';
    }
}
</script>
