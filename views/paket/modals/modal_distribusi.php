<?php
/**
 * Modal View: Alokasi Distribusi Titik
 */
$role = $_SESSION['admin_role'] ?? 'user';
$totalDist = count($distribusiList);
$distDone = 0;
$totalQty = 0;
foreach ($distribusiList as $dist) {
    if (($dist['status_distribusi'] ?? '') === 'Tiba di Tujuan') {
        $distDone++;
    }
    $totalQty += (float)$dist['jumlah'];
}
$pctDist = ($totalDist > 0) ? round(($distDone / $totalDist) * 100) : 0;
?>

<!-- Header Action Teleport Source -->
<div id="modalActionSource" class="hidden">
    <div class="flex items-center gap-2">
        <span class="text-xs font-bold px-2.5 py-1 rounded-lg bg-indigo-50 text-indigo-700 border border-indigo-200 hidden sm:inline-flex items-center gap-1">
            <ion-icon name="navigate-outline"></ion-icon>
            <span>Tiba: <?= $distDone ?>/<?= $totalDist ?> (<?= $pctDist ?>%)</span>
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

<!-- 1. Langsung Tabel Distribusi (Full Width, No Horizontal Scroll) -->
<div class="space-y-3">
    <div class="bg-white rounded-xl border border-slate-200/90 overflow-hidden shadow-2xs">
        <table class="w-full text-left text-xs table-fixed">
            <thead class="bg-slate-50 text-[11px] font-bold text-slate-500 uppercase tracking-wider border-b border-slate-200/80">
                <tr>
                    <th class="py-2.5 px-3 w-10 text-center">NO</th>
                    <th class="py-2.5 px-3 w-40">KOMPONEN BARANG</th>
                    <th class="py-2.5 px-3 w-44">TITIK TUJUAN</th>
                    <th class="py-2.5 px-2 text-center w-16">QTY</th>
                    <th class="py-2.5 px-2 text-center w-36">STATUS DISTRIBUSI</th>
                    <th class="py-2.5 px-3">PENGIRIMAN & RESI</th>
                    <?php if ($role === 'admin' || $role === 'user'): ?>
                        <th class="py-2.5 px-3 text-center w-32">AKSI</th>
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
                            <p class="text-[11px] text-slate-400">Klik "Tambah Alokasi" pada header di atas untuk menentukan tujuan penerima barang.</p>
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
                        elseif ($sDist === 'Dalam Pengiriman' || $sDist === 'Dalam Perjalanan') $badgeClass = 'bg-amber-50 text-amber-800 border-amber-200';
                        ?>
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="py-2.5 px-3 text-center text-slate-400 font-mono text-[11px]"><?= $no++ ?></td>
                            <td class="py-2.5 px-3 text-xs font-bold text-slate-800 break-words">
                                <?= htmlspecialchars($dist['nama_barang'] ?? $dist['nama_komponen'] ?? '-') ?>
                            </td>
                            <td class="py-2.5 px-3 text-xs font-semibold text-slate-800 break-words">
                                <?= htmlspecialchars($dist['nama_tujuan'] ?? '-') ?>
                                <?php if (!empty($dist['kab_kota'])): ?>
                                    <div class="text-[10px] text-slate-400 font-normal"><?= htmlspecialchars($dist['kab_kota']) ?></div>
                                <?php endif; ?>
                            </td>
                            <td class="py-2.5 px-2 text-center whitespace-nowrap font-bold text-slate-800 text-xs">
                                <?= (float)$dist['jumlah'] ?> <?= htmlspecialchars($dist['satuan'] ?? 'Unit') ?>
                            </td>
                            <td class="py-2.5 px-2 text-center whitespace-nowrap">
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-medium border <?= $badgeClass ?>">
                                    <?= htmlspecialchars($sDist) ?>
                                </span>
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
                                    <div class="flex items-center justify-center gap-1.5">
                                        <button type="button" 
                                                onclick='openNativeUpdateStatusDistribusiModal(<?= json_encode($dist, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)'
                                                class="px-2.5 py-1 text-xs font-semibold bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg transition-colors cursor-pointer inline-flex items-center gap-1 shadow-2xs">
                                            <ion-icon name="create-outline"></ion-icon>
                                            <span>Ubah</span>
                                        </button>
                                        <button type="button" 
                                                onclick="deleteDistribusiAjax('<?= $paket['kode_paket'] ?: $paket['id'] ?>', '<?= $dist['id'] ?>')"
                                                class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors text-xs cursor-pointer"
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
            <span>Alokasi yang telah berstatus <strong>"Tiba di Tujuan"</strong> siap diterbitkan berkas Berita Acara Serah Terima (BAST).</span>
        </div>
        <span class="font-medium text-slate-600">Realisasi Distribusi: <strong><?= $distDone ?></strong> dari <strong><?= $totalDist ?></strong> lokasi (<?= $pctDist ?>%)</span>
    </div>
</div>
