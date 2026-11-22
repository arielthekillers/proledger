<?php
/**
 * Modal View: Pemesanan Toko / Supplier
 */
$role = $_SESSION['admin_role'] ?? 'user';
$pesanDone = 0;
$totalBeli = 0;
$totalQty = 0;
foreach ($distribusiList as $dist) {
    if (($dist['status_barang'] ?? '') === 'Sudah Dipesan') {
        $pesanDone++;
    }
    if ((float)($dist['harga_satuan'] ?? 0) > 0) {
        $totalBeli += ((float)$dist['harga_satuan'] * (float)$dist['jumlah']);
    }
    $totalQty += (float)$dist['jumlah'];
}
$totalDist = count($distribusiList);
$pctPesan = ($totalDist > 0) ? round(($pesanDone / $totalDist) * 100) : 0;
?>

<!-- Header Action Teleport Source -->
<div id="modalActionSource" class="hidden">
    <span class="text-xs font-bold px-2.5 py-1 rounded-lg bg-amber-50 text-amber-800 border border-amber-200 inline-flex items-center gap-1 shadow-2xs">
        <ion-icon name="bag-check-outline" class="text-amber-600"></ion-icon>
        <span>Progres: <?= $pesanDone ?>/<?= $totalDist ?> (<?= $pctPesan ?>%)</span>
    </span>
</div>

<!-- 1. Langsung Tabel Pemesanan (Full Width, No Horizontal Scroll) -->
<div class="space-y-3">
    <div class="bg-white rounded-xl border border-slate-200/90 overflow-visible shadow-2xs">
        <table class="w-full text-left text-xs table-fixed">
            <thead class="bg-slate-50 text-[11px] font-bold text-slate-500 uppercase tracking-wider border-b border-slate-200/80">
                <tr>
                    <th class="py-2.5 px-3 w-10 text-center rounded-tl-xl">NO</th>
                    <th class="py-2.5 px-3 w-40">KOMPONEN BARANG</th>
                    <th class="py-2.5 px-3">TUJUAN PENERIMA</th>
                    <th class="py-2.5 px-2 text-center w-16">QTY</th>
                    <th class="py-2.5 px-3 w-32">PESANAN TOKO</th>
                    <th class="py-2.5 px-3 text-right w-28">HARGA BELI</th>
                    <th class="py-2.5 px-3 text-right w-32">TOTAL BELI</th>
                    <th class="py-2.5 px-3 text-center w-44 rounded-tr-xl">STATUS PESANAN</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php if (empty($distribusiList)): ?>
                    <tr>
                        <td colspan="8" class="py-8 text-center text-slate-400 text-xs">
                            <div class="w-10 h-10 bg-slate-50 rounded-2xl flex items-center justify-center mx-auto mb-2 text-xl text-slate-300">
                                <ion-icon name="bag-outline"></ion-icon>
                            </div>
                            <h4 class="text-xs font-bold text-slate-700 mb-0.5">Belum Ada Daftar Pesanan</h4>
                            <p class="text-[11px] text-slate-400">Tambahkan alokasi distribusi terlebih dahulu pada modul Distribusi.</p>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php $no = 1; foreach ($distribusiList as $dist): ?>
                        <?php 
                        $isPesan = (($dist['status_barang'] ?? '') === 'Sudah Dipesan');
                        $hSatuan = (float)($dist['harga_satuan'] ?? 0);
                        $subTotalBeli = $hSatuan * (float)$dist['jumlah'];
                        $namaKomp = $dist['nama_komponen'] ?? $dist['komponen'] ?? '-';
                        ?>
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="py-2.5 px-3 text-center text-slate-400 font-mono text-[11px]"><?= $no++ ?></td>
                            <td class="py-2.5 px-3 text-xs font-bold text-slate-800 break-words">
                                <?= htmlspecialchars($namaKomp) ?>
                            </td>
                            <td class="py-2.5 px-3 text-xs text-slate-700 font-normal">
                                <div class="truncate" title="<?= htmlspecialchars($dist['nama_tujuan'] ?? '-') ?>">
                                    <?= htmlspecialchars($dist['nama_tujuan'] ?? '-') ?>
                                </div>
                            </td>
                            <td class="py-2.5 px-2 text-center whitespace-nowrap font-bold text-slate-800 text-xs">
                                <?= (float)$dist['jumlah'] ?> <?= htmlspecialchars($dist['satuan'] ?? 'Unit') ?>
                            </td>
                            <td class="py-2.5 px-3 text-xs text-slate-700">
                                <?php if (!empty($dist['nama_barang'])): ?>
                                    <?php $hasShipping = !empty($dist['ekspedisi']) || !empty($dist['no_resi']); ?>
                                    <div class="relative group/toko inline-flex items-center gap-1 max-w-full cursor-default">
                                        <span class="font-medium text-slate-800 truncate"><?= htmlspecialchars($dist['nama_barang']) ?></span>
                                        <?php if ($hasShipping): ?>
                                            <ion-icon name="information-circle-outline" class="text-blue-500 text-xs shrink-0"></ion-icon>
                                            <div class="hidden group-hover/toko:block absolute left-0 bottom-full mb-1.5 z-40 w-56 p-2.5 bg-slate-800 text-white rounded-xl shadow-xl text-[11px] leading-relaxed pointer-events-none">
                                                <div class="font-bold text-white border-b border-slate-700 pb-1 mb-1 flex items-center gap-1.5">
                                                    <ion-icon name="storefront-outline" class="text-blue-400"></ion-icon>
                                                    <span class="truncate"><?= htmlspecialchars($dist['nama_barang']) ?></span>
                                                </div>
                                                <?php if (!empty($dist['ekspedisi'])): ?>
                                                    <div class="flex items-center justify-between gap-2 text-slate-300">
                                                        <span>Ekspedisi:</span>
                                                        <span class="font-semibold text-white"><?= htmlspecialchars($dist['ekspedisi']) ?></span>
                                                    </div>
                                                <?php endif; ?>
                                                <?php if (!empty($dist['no_resi'])): ?>
                                                    <div class="flex items-center justify-between gap-2 text-slate-300">
                                                        <span>No. Resi:</span>
                                                        <span class="font-mono text-emerald-300"><?= htmlspecialchars($dist['no_resi']) ?></span>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                <?php else: ?>
                                    <span class="text-slate-400 italic text-[11px]">-</span>
                                <?php endif; ?>
                            </td>
                            <td class="py-2.5 px-3 text-right whitespace-nowrap text-xs font-mono text-slate-700">
                                <?= $hSatuan > 0 ? 'Rp ' . number_format($hSatuan, 0, ',', '.') : '-' ?>
                            </td>
                            <td class="py-2.5 px-3 text-right whitespace-nowrap text-xs font-mono font-bold text-slate-800">
                                <?= $subTotalBeli > 0 ? 'Rp ' . number_format($subTotalBeli, 0, ',', '.') : '-' ?>
                            </td>
                            <td class="py-2.5 px-3 text-center whitespace-nowrap">
                                <?php if ($role === 'admin' || $role === 'user'): ?>
                                    <button type="button" 
                                            onclick='openNativeUpdateStatusBarangModal(<?= json_encode($dist, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)'
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold border transition-all cursor-pointer shadow-2xs hover:scale-102 <?= $isPesan ? 'bg-emerald-50 text-emerald-700 border-emerald-200 hover:bg-emerald-100 hover:border-emerald-300' : 'bg-amber-50 text-amber-800 border-amber-200 hover:bg-amber-100 hover:border-amber-300' ?>"
                                            title="Klik untuk mengubah status pesanan & rincian toko">
                                        <ion-icon name="<?= $isPesan ? 'checkmark-circle' : 'time-outline' ?>" class="text-xs"></ion-icon>
                                        <span><?= $isPesan ? 'Sudah Dipesan' : 'Belum Dipesan' ?></span>
                                        <ion-icon name="create-outline" class="text-[11px] opacity-60"></ion-icon>
                                    </button>
                                <?php else: ?>
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md text-[11px] font-medium border <?= $isPesan ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-amber-50 text-amber-800 border-amber-200' ?>">
                                        <ion-icon name="<?= $isPesan ? 'checkmark-circle' : 'time-outline' ?>" class="text-xs"></ion-icon>
                                        <span><?= $isPesan ? 'Sudah Dipesan' : 'Belum Dipesan' ?></span>
                                    </span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
            <!-- 2. Ringkasan Info Menyatu di Kolom Bawah Tabel (tfoot) -->
            <tfoot class="bg-slate-50/90 font-bold text-slate-700 border-t-2 border-slate-200">
                <tr>
                    <td class="py-2.5 px-3 text-center text-slate-400 font-mono text-[11px]">-</td>
                    <td colspan="2" class="py-2.5 px-3 text-xs text-slate-800">
                        <span class="font-normal text-slate-500">Total Alokasi:</span> <?= $totalDist ?> Titik Pesanan
                    </td>
                    <td class="py-2.5 px-2 text-center text-xs font-extrabold text-slate-900 whitespace-nowrap">
                        <?= number_format($totalQty, 0, ',', '.') ?> Unit
                    </td>
                    <td colspan="2" class="py-2.5 px-3 text-right text-xs text-slate-500 font-normal">
                        Total Belanja (HPP):
                    </td>
                    <td class="py-2.5 px-3 text-right text-xs font-mono font-extrabold text-slate-900 whitespace-nowrap">
                        Rp <?= number_format($totalBeli, 0, ',', '.') ?>
                    </td>
                    <td class="py-2.5 px-3 text-center text-xs whitespace-nowrap">
                        <span class="text-[11px] font-bold text-emerald-700"><?= $pesanDone ?>/<?= $totalDist ?> Selesai</span>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>

    <!-- 5. Informasi Tambahan di Bawah Tabel -->
    <div class="flex items-center justify-between text-[11px] text-slate-500 px-1">
        <div class="flex items-center gap-1.5">
            <ion-icon name="information-circle-outline" class="text-amber-500 text-sm"></ion-icon>
            <span>Klik tombol pada kolom <strong>Status Pesanan</strong> untuk mengupdate status, toko, no. resi, dan harga beli.</span>
        </div>
        <span class="font-medium text-slate-600">Realisasi Pesanan: <strong><?= $pesanDone ?></strong> dari <strong><?= $totalDist ?></strong> barang (<?= $pctPesan ?>%)</span>
    </div>
</div>
