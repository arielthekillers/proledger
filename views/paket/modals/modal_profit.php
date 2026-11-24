<?php
/**
 * Modal View: Estimasi Margin Profit
 */
$isLoss = ($nilaiBersihProfit < 0);
$totalQty = 0;
$totalKontrakSum = 0;
$totalHppSum = 0;
$totalUntungSum = 0;
foreach ($itemsWithProfit as $it) {
    $totalQty += (float)$it['jumlah'];
    $totalKontrakSum += (float)($it['total_kontrak'] ?? 0);
    $totalHppSum += (float)($it['hpp'] ?? 0);
    $totalUntungSum += (float)($it['estimasi_untung'] ?? 0);
}
?>

<!-- Header Action Teleport Source -->
<div id="modalActionSource" class="hidden">
    <span class="text-xs font-bold px-2.5 py-1 rounded-lg <?= $isLoss ? 'bg-rose-50 text-rose-700 border border-rose-200' : 'bg-emerald-50 text-emerald-800 border border-emerald-200' ?> inline-flex items-center gap-1 shadow-2xs font-mono">
        <ion-icon name="trending-up-outline"></ion-icon>
        <span>Margin: <?= ($profitMarginPct > 0 ? '+' : '') . $profitMarginPct ?>%</span>
    </span>
</div>

<div class="space-y-4">
    <!-- 1. Langsung Tabel Kontribusi Margin per Komponen (Full Width, No Horizontal Scroll) -->
    <div class="bg-white rounded-xl border border-slate-200/90 overflow-hidden shadow-2xs">
        <table class="w-full text-left text-xs table-fixed">
            <thead class="bg-slate-50 text-[11px] font-bold text-slate-500 uppercase tracking-wider border-b border-slate-200/80">
                <tr>
                    <th class="py-2.5 px-3 w-10 text-center">NO</th>
                    <th class="py-2.5 px-3 w-56">KOMPONEN BARANG</th>
                    <th class="py-2.5 px-2 text-center w-24">QTY</th>
                    <th class="py-2.5 px-3 text-right w-36">NILAI KONTRAK</th>
                    <th class="py-2.5 px-3 text-right w-36">BIAYA BELI (HPP)</th>
                    <th class="py-2.5 px-3 text-right w-36">ESTIMASI UNTUNG</th>
                    <th class="py-2.5 px-2 text-center w-24">MARGIN</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php if (empty($itemsWithProfit)): ?>
                    <tr>
                        <td colspan="7" class="py-8 text-center text-slate-400 text-xs">
                            <div class="w-10 h-10 bg-slate-50 rounded-2xl flex items-center justify-center mx-auto mb-2 text-xl text-slate-300">
                                <ion-icon name="analytics-outline"></ion-icon>
                            </div>
                            <h4 class="text-xs font-bold text-slate-700 mb-0.5">Belum Ada Komponen Barang</h4>
                            <p class="text-[11px] text-slate-400">Tambahkan komponen barang pada modul Item.</p>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php $no = 1; foreach ($itemsWithProfit as $it): ?>
                        <?php 
                        $itUntung = (float)($it['estimasi_untung'] ?? 0);
                        $itMargin = (float)($it['margin_pct'] ?? 0);
                        $isItemLoss = ($itUntung < 0);
                        $namaKomp = $it['komponen'] ?? $it['nama_barang'] ?? '-';
                        ?>
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="py-2.5 px-3 text-center text-slate-400 font-mono text-[11px]"><?= $no++ ?></td>
                            <td class="py-2.5 px-3 text-xs font-bold text-slate-800 break-words">
                                <?= htmlspecialchars($namaKomp) ?>
                            </td>
                            <td class="py-2.5 px-2 text-center whitespace-nowrap font-bold text-slate-800 text-xs">
                                <?= (float)$it['jumlah'] ?> <?= htmlspecialchars($it['satuan'] ?: 'Unit') ?>
                            </td>
                            <td class="py-2.5 px-3 text-right whitespace-nowrap text-xs font-mono text-slate-700">
                                Rp <?= number_format((float)$it['total_kontrak'], 0, ',', '.') ?>
                            </td>
                            <td class="py-2.5 px-3 text-right whitespace-nowrap text-xs font-mono text-slate-700">
                                Rp <?= number_format((float)$it['hpp'], 0, ',', '.') ?>
                            </td>
                            <td class="py-2.5 px-3 text-right whitespace-nowrap text-xs font-mono font-bold <?= $isItemLoss ? 'text-rose-600' : 'text-emerald-700' ?>">
                                Rp <?= number_format($itUntung, 0, ',', '.') ?>
                            </td>
                            <td class="py-2.5 px-2 text-center whitespace-nowrap">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-bold font-mono <?= $isItemLoss ? 'bg-rose-50 text-rose-700 border border-rose-200' : 'bg-emerald-50 text-emerald-700 border border-emerald-200' ?>">
                                    <?= ($itMargin > 0 ? '+' : '') . $itMargin ?>%
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
            <!-- 2. Ringkasan Info Menyatu di Kolom Bawah Tabel (tfoot) -->
            <tfoot class="bg-slate-50/90 font-bold text-slate-700 border-t-2 border-slate-200">
                <tr>
                    <td class="py-2.5 px-3 text-center text-slate-400 font-mono text-[11px]">-</td>
                    <td class="py-2.5 px-3 text-xs text-slate-800">
                        <span class="font-normal text-slate-500">Total Komponen:</span> <?= count($itemsWithProfit) ?> Item
                    </td>
                    <td class="py-2.5 px-2 text-center text-xs font-extrabold text-slate-900 whitespace-nowrap">
                        <?= number_format($totalQty, 0, ',', '.') ?> Unit
                    </td>
                    <td class="py-2.5 px-3 text-right text-xs font-mono font-bold text-slate-800 whitespace-nowrap">
                        Rp <?= number_format($totalKontrakSum, 0, ',', '.') ?>
                    </td>
                    <td class="py-2.5 px-3 text-right text-xs font-mono font-bold text-slate-800 whitespace-nowrap">
                        Rp <?= number_format($totalHppSum, 0, ',', '.') ?>
                    </td>
                    <td class="py-2.5 px-3 text-right text-xs font-mono font-extrabold <?= $isLoss ? 'text-rose-600' : 'text-emerald-700' ?> whitespace-nowrap">
                        Rp <?= number_format($totalUntungSum, 0, ',', '.') ?>
                    </td>
                    <td class="py-2.5 px-2 text-center text-xs font-mono font-bold <?= $isLoss ? 'text-rose-700' : 'text-emerald-700' ?> whitespace-nowrap">
                        <?= ($profitMarginPct > 0 ? '+' : '') . $profitMarginPct ?>%
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>

    <!-- 5. Informasi Tambahan Ringkasan Finansial di Bawah Tabel -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 pt-1">
        <!-- Margin % -->
        <div class="p-3.5 rounded-xl border <?= $isLoss ? 'bg-rose-50/70 border-rose-200' : 'bg-emerald-50/70 border-emerald-200' ?> shadow-2xs">
            <div class="text-[10px] uppercase font-bold <?= $isLoss ? 'text-rose-600' : 'text-emerald-700' ?> mb-0.5">Estimasi Margin Profit</div>
            <div class="text-lg font-extrabold <?= $isLoss ? 'text-rose-700' : 'text-emerald-800' ?> font-mono">
                <?= ($profitMarginPct > 0 ? '+' : '') . $profitMarginPct ?>%
            </div>
            <div class="text-[10px] text-slate-400 mt-0.5">Persentase dari Pagu Acuan</div>
        </div>

        <!-- Nilai Bersih Profit -->
        <div class="p-3.5 rounded-xl border <?= $isLoss ? 'bg-rose-50/70 border-rose-200' : 'bg-emerald-50/70 border-emerald-200' ?> shadow-2xs">
            <div class="text-[10px] uppercase font-bold <?= $isLoss ? 'text-rose-600' : 'text-emerald-700' ?> mb-0.5">Untung Bersih Riil</div>
            <div class="text-sm font-extrabold <?= $isLoss ? 'text-rose-700' : 'text-emerald-800' ?> font-mono">
                Rp <?= number_format($nilaiBersihProfit, 0, ',', '.') ?>
            </div>
            <div class="text-[10px] text-slate-400 mt-0.5">Setelah dikurangi pasal & HPP</div>
        </div>

        <!-- Total Pendapatan Kontrak Bersih -->
        <div class="bg-white p-3.5 rounded-xl border border-slate-200/90 shadow-2xs">
            <div class="text-[10px] uppercase font-bold text-slate-400 mb-0.5">Dana Bersih Kontrak</div>
            <div class="text-sm font-extrabold text-slate-800 font-mono">
                Rp <?= number_format(($dppNilai ?? calculateDPP($acuanPagu)) - $totalPasalVal, 0, ',', '.') ?>
            </div>
            <div class="text-[10px] text-slate-400 mt-0.5">DPP - Potongan Pasal</div>
        </div>

        <!-- Total HPP Belanja Toko -->
        <div class="bg-white p-3.5 rounded-xl border border-slate-200/90 shadow-2xs">
            <div class="text-[10px] uppercase font-bold text-slate-400 mb-0.5">Total Belanja Barang (HPP)</div>
            <div class="text-sm font-extrabold text-slate-800 font-mono">
                Rp <?= number_format($totalBeliBarang, 0, ',', '.') ?>
            </div>
            <div class="text-[10px] text-slate-400 mt-0.5">ROI: <strong><?= ($totalBeliBarang > 0) ? $roiPct . '%' : '-' ?></strong></div>
        </div>
    </div>
</div>
