<?php
/**
 * Modal View: Alokasi Distribusi Titik
 */
$role = $_SESSION['admin_role'] ?? 'user';
$totalDist = count($distribusiList);
$distDone = 0;
foreach ($distribusiList as $dist) {
    if (($dist['status_distribusi'] ?? '') === 'Tiba di Tujuan') {
        $distDone++;
    }
}
$pctDist = ($totalDist > 0) ? round(($distDone / $totalDist) * 100) : 0;
?>

<div class="space-y-4">
    <!-- Top Summary Banner & Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white p-4 rounded-xl border border-slate-200/90 shadow-2xs">
        <div>
            <h4 class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                <ion-icon name="navigate-outline" class="text-indigo-600 text-base"></ion-icon>
                <span>Monitoring Distribusi & Logistik Pengiriman</span>
            </h4>
            <p class="text-[11px] text-slate-400 mt-0.5">Tracking pergerakan pengiriman barang ke masing-masing titik tujuan/sekolah penerima.</p>
        </div>
        <div class="flex items-center gap-2 self-start sm:self-auto">
            <span class="text-xs font-bold px-3 py-1 rounded-lg bg-indigo-50 text-indigo-700 border border-indigo-200">
                Tiba: <?= $distDone ?> / <?= $totalDist ?> Alokasi (<?= $pctDist ?>%)
            </span>
            <?php if ($role === 'admin' || $role === 'user'): ?>
                <button type="button" 
                        onclick="openNativeTambahDistribusiModal('<?= $paket['kode_paket'] ?: $paket['id'] ?>')"
                        class="px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-semibold shadow-xs flex items-center gap-1.5 transition-all cursor-pointer">
                    <ion-icon name="add-circle-outline" class="text-sm"></ion-icon>
                    <span>Tambah Alokasi</span>
                </button>
            <?php endif; ?>
        </div>
    </div>

    <!-- Tabel Alokasi Distribusi -->
    <div class="bg-white rounded-xl border border-slate-200/90 overflow-hidden shadow-2xs">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-[11px] font-bold text-slate-500 uppercase tracking-wider border-b border-slate-200/80">
                    <tr>
                        <th class="py-2.5 px-3 w-8 text-center">NO</th>
                        <th class="py-2.5 px-3 min-w-[170px]">KOMPONEN BARANG</th>
                        <th class="py-2.5 px-3 min-w-[170px]">TITIK TUJUAN</th>
                        <th class="py-2.5 px-2.5 text-center min-w-[70px]">QTY</th>
                        <th class="py-2.5 px-3 text-center min-w-[140px]">STATUS DISTRIBUSI</th>
                        <th class="py-2.5 px-3 min-w-[130px]">PENGIRIMAN</th>
                        <?php if ($role === 'admin' || $role === 'user'): ?>
                            <th class="py-2.5 px-3 text-right min-w-[90px]">AKSI</th>
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
                                <p class="text-[11px] text-slate-400">Klik "Tambah Alokasi" di atas untuk menentukan tujuan penerima barang.</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php $no = 1; foreach ($distribusiList as $dist): ?>
                            <?php 
                            $sDist = $dist['status_distribusi'] ?? 'Belum Dikirim';
                            $badgeClass = 'bg-slate-100 text-slate-600 border-slate-200';
                            if ($sDist === 'Tiba di Tujuan') $badgeClass = 'bg-emerald-50 text-emerald-700 border-emerald-200 font-bold';
                            elseif ($sDist === 'Tiba di Gudang') $badgeClass = 'bg-blue-50 text-blue-700 border-blue-200';
                            elseif ($sDist === 'Tiba dirumah/kantor' || $sDist === 'Tiba di Rumah/Kantor') $badgeClass = 'bg-indigo-50 text-indigo-700 border-indigo-200';
                            elseif ($sDist === 'Dalam Pengiriman') $badgeClass = 'bg-amber-50 text-amber-800 border-amber-200';
                            ?>
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <td class="py-2 px-3 text-center text-slate-400 font-mono text-[11px]"><?= $no++ ?></td>
                                <td class="py-2 px-3 text-xs font-bold text-slate-800">
                                    <?= htmlspecialchars($dist['nama_barang'] ?? '-') ?>
                                </td>
                                <td class="py-2 px-3 text-xs font-semibold text-slate-800">
                                    <?= htmlspecialchars($dist['nama_tujuan'] ?? '-') ?>
                                    <div class="text-[10px] text-slate-400 font-normal"><?= htmlspecialchars($dist['kab_kota'] ?? '') ?></div>
                                </td>
                                <td class="py-2 px-2.5 text-center whitespace-nowrap font-bold text-slate-800 text-xs">
                                    <?= (float)$dist['jumlah'] ?> <?= htmlspecialchars($dist['satuan'] ?? 'Unit') ?>
                                </td>
                                <td class="py-2 px-3 text-center whitespace-nowrap">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md text-[11px] font-medium border <?= $badgeClass ?>">
                                        <?= htmlspecialchars($sDist) ?>
                                    </span>
                                </td>
                                <td class="py-2 px-3 text-xs text-slate-600">
                                    <div><?= htmlspecialchars($dist['ekspedisi'] ?: '-') ?></div>
                                    <div class="text-[10px] text-slate-400 font-mono"><?= htmlspecialchars($dist['no_resi'] ?: '-') ?></div>
                                </td>
                                <?php if ($role === 'admin' || $role === 'user'): ?>
                                    <td class="py-2 px-3 text-right whitespace-nowrap">
                                        <div class="flex items-center justify-end gap-1">
                                            <button type="button" 
                                                    onclick='openNativeUpdateStatusDistribusiModal(<?= json_encode($dist, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)'
                                                    class="px-2.5 py-1 text-xs font-semibold bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg transition-colors cursor-pointer inline-flex items-center gap-1">
                                                <ion-icon name="create-outline"></ion-icon>
                                                <span>Ubah</span>
                                            </button>
                                            <button type="button" 
                                                    onclick="deleteDistribusiAjax('<?= $paket['kode_paket'] ?: $paket['id'] ?>', '<?= $dist['id'] ?>')"
                                                    class="p-1 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-md transition-colors text-xs"
                                                    title="Hapus Alokasi">
                                                <ion-icon name="trash-outline"></ion-icon>
                                            </button>
                                        </div>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
