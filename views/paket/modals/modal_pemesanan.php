<?php
/**
 * Modal View: Pemesanan Toko / Supplier
 */
$role = $_SESSION['admin_role'] ?? 'user';
$pesanDone = 0;
$totalBeli = 0;
foreach ($distribusiList as $dist) {
    if (($dist['status_barang'] ?? '') === 'Sudah Dipesan') {
        $pesanDone++;
    }
    if ((float)($dist['harga_satuan'] ?? 0) > 0) {
        $totalBeli += ((float)$dist['harga_satuan'] * (float)$dist['jumlah']);
    }
}
$totalDist = count($distribusiList);
$pctPesan = ($totalDist > 0) ? round(($pesanDone / $totalDist) * 100) : 0;
?>

<div class="space-y-4">
    <!-- Top Summary Banner -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white p-4 rounded-xl border border-slate-200/90 shadow-2xs">
        <div>
            <h4 class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                <ion-icon name="bag-check-outline" class="text-amber-600 text-base"></ion-icon>
                <span>Progres Pemesanan ke Toko / Supplier</span>
            </h4>
            <p class="text-[11px] text-slate-400 mt-0.5">Tracking status pembelian barang di toko/supplier pihak ketiga.</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="text-xs font-bold px-3 py-1 rounded-lg bg-amber-50 text-amber-800 border border-amber-200">
                Pemesanan: <?= $pesanDone ?> / <?= $totalDist ?> Barang (<?= $pctPesan ?>%)
            </span>
            <span class="text-xs font-bold px-3 py-1 rounded-lg bg-slate-100 text-slate-700 border border-slate-200">
                Total Beli: Rp <?= number_format($totalBeli, 0, ',', '.') ?>
            </span>
        </div>
    </div>

    <!-- Tabel Pemesanan -->
    <div class="bg-white rounded-xl border border-slate-200/90 overflow-hidden shadow-2xs">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-[11px] font-bold text-slate-500 uppercase tracking-wider border-b border-slate-200/80">
                    <tr>
                        <th class="py-2.5 px-3 w-8 text-center">NO</th>
                        <th class="py-2.5 px-3 min-w-[170px]">KOMPONEN BARANG</th>
                        <th class="py-2.5 px-3 min-w-[170px]">TUJUAN PENERIMA</th>
                        <th class="py-2.5 px-2.5 text-center min-w-[70px]">QTY</th>
                        <th class="py-2.5 px-3 min-w-[160px]">PESANAN TOKO</th>
                        <th class="py-2.5 px-3 text-right min-w-[110px]">HARGA BELI</th>
                        <th class="py-2.5 px-3 text-right min-w-[120px]">TOTAL BELI</th>
                        <th class="py-2.5 px-3 text-center min-w-[130px]">STATUS</th>
                        <?php if ($role === 'admin' || $role === 'user'): ?>
                            <th class="py-2.5 px-3 text-right min-w-[80px]">AKSI</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php if (empty($distribusiList)): ?>
                        <tr>
                            <td colspan="9" class="py-8 text-center text-slate-400 text-xs">
                                <div class="w-10 h-10 bg-slate-50 rounded-2xl flex items-center justify-center mx-auto mb-2 text-xl text-slate-300">
                                    <ion-icon name="bag-outline"></ion-icon>
                                </div>
                                <h4 class="text-xs font-bold text-slate-700 mb-0.5">Belum Ada Daftar Barang</h4>
                                <p class="text-[11px] text-slate-400">Tambahkan komponen item dan alokasi terlebih dahulu.</p>
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
                                <td class="py-2 px-3 text-center text-slate-400 font-mono text-[11px]"><?= $no++ ?></td>
                                <td class="py-2 px-3 text-xs font-bold text-slate-800">
                                    <?= htmlspecialchars($namaKomp) ?>
                                </td>
                                <td class="py-2 px-3 whitespace-nowrap text-xs font-semibold text-slate-800">
                                    <?= htmlspecialchars($dist['nama_tujuan'] ?? '-') ?>
                                </td>
                                <td class="py-2 px-2.5 text-center whitespace-nowrap font-bold text-slate-800 text-xs">
                                    <?= (float)$dist['jumlah'] ?> <?= htmlspecialchars($dist['satuan'] ?? 'Unit') ?>
                                </td>
                                <td class="py-2 px-3 text-xs text-slate-700">
                                    <?php if (!empty($dist['nama_barang'])): ?>
                                        <div class="font-medium text-slate-800"><?= htmlspecialchars($dist['nama_barang']) ?></div>
                                        <?php if (!empty($dist['ekspedisi'])): ?>
                                            <div class="text-[10px] text-slate-400 mt-0.5"><?= htmlspecialchars($dist['ekspedisi']) ?></div>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="text-slate-400 italic text-[11px]">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-2 px-3 text-right whitespace-nowrap text-xs font-mono text-slate-700">
                                    <?= $hSatuan > 0 ? 'Rp ' . number_format($hSatuan, 0, ',', '.') : '-' ?>
                                </td>
                                <td class="py-2 px-3 text-right whitespace-nowrap text-xs font-mono font-bold text-slate-800">
                                    <?= $subTotalBeli > 0 ? 'Rp ' . number_format($subTotalBeli, 0, ',', '.') : '-' ?>
                                </td>
                                <td class="py-2 px-3 text-center whitespace-nowrap">
                                    <?php if ($isPesan): ?>
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            <ion-icon name="checkmark-circle" class="text-xs text-emerald-600"></ion-icon>
                                            <span>Sudah Dipesan</span>
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md text-[11px] font-medium bg-amber-50 text-amber-800 border border-amber-200">
                                            <ion-icon name="time-outline" class="text-xs text-amber-600"></ion-icon>
                                            <span>Belum Dipesan</span>
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <?php if ($role === 'admin' || $role === 'user'): ?>
                                    <td class="py-2 px-3 text-right whitespace-nowrap">
                                        <button type="button" 
                                                onclick='openNativeUpdateStatusBarangModal(<?= json_encode($dist, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)'
                                                class="px-2.5 py-1 text-xs font-semibold bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg transition-colors cursor-pointer inline-flex items-center gap-1">
                                            <ion-icon name="create-outline"></ion-icon>
                                            <span>Ubah</span>
                                        </button>
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
