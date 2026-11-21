<?php
/**
 * Modal View: Estimasi Margin Profit
 */
$isLoss = ($nilaiBersihProfit < 0);
?>

<div class="space-y-4">
    <!-- Top Summary Banner -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <!-- Margin % -->
        <div class="p-4 rounded-xl border <?= $isLoss ? 'bg-rose-50/70 border-rose-200' : 'bg-emerald-50/70 border-emerald-200' ?> shadow-2xs">
            <div class="text-[10px] uppercase font-bold <?= $isLoss ? 'text-rose-600' : 'text-emerald-700' ?> mb-1">Estimasi Margin Profit</div>
            <div class="text-xl font-extrabold <?= $isLoss ? 'text-rose-700' : 'text-emerald-800' ?> font-mono">
                <?= ($profitMarginPct > 0 ? '+' : '') . $profitMarginPct ?>%
            </div>
            <div class="text-[10px] text-slate-400 mt-0.5">Persentase dari Pagu Acuan</div>
        </div>

        <!-- Nilai Bersih Profit -->
        <div class="p-4 rounded-xl border <?= $isLoss ? 'bg-rose-50/70 border-rose-200' : 'bg-emerald-50/70 border-emerald-200' ?> shadow-2xs">
            <div class="text-[10px] uppercase font-bold <?= $isLoss ? 'text-rose-600' : 'text-emerald-700' ?> mb-1">Untung Bersih (Estimasi)</div>
            <div class="text-sm font-extrabold <?= $isLoss ? 'text-rose-700' : 'text-emerald-800' ?> font-mono">
                Rp <?= number_format($nilaiBersihProfit, 0, ',', '.') ?>
            </div>
            <div class="text-[10px] text-slate-400 mt-0.5">Setelah dikurangi pasal & HPP</div>
        </div>

        <!-- Total Pendapatan Kontrak Bersih -->
        <div class="bg-white p-4 rounded-xl border border-slate-200/90 shadow-2xs">
            <div class="text-[10px] uppercase font-bold text-slate-400 mb-1">Pendapatan Bersih Kontrak</div>
            <div class="text-sm font-extrabold text-slate-800 font-mono">
                Rp <?= number_format($acuanPagu - $totalPasalVal, 0, ',', '.') ?>
            </div>
            <div class="text-[10px] text-slate-400 mt-0.5">Pagu Nego - Total Potongan</div>
        </div>

        <!-- Total HPP Belanja Toko -->
        <div class="bg-white p-4 rounded-xl border border-slate-200/90 shadow-2xs">
            <div class="text-[10px] uppercase font-bold text-slate-400 mb-1">Total Belanja Barang (HPP)</div>
            <div class="text-sm font-extrabold text-slate-800 font-mono">
                Rp <?= number_format($totalBeliBarang, 0, ',', '.') ?>
            </div>
            <div class="text-[10px] text-slate-400 mt-0.5">Total harga beli di toko</div>
        </div>
    </div>

    <!-- Breakdown Tabel per Item Komponen -->
    <div class="bg-white rounded-xl border border-slate-200/90 overflow-hidden shadow-2xs">
        <div class="p-3.5 bg-slate-50/80 border-b border-slate-100 flex items-center justify-between">
            <h4 class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                <ion-icon name="analytics-outline" class="text-emerald-600 text-base"></ion-icon>
                <span>Kontribusi Margin per Item Komponen</span>
            </h4>
            <span class="text-[11px] text-slate-400">ROI: <strong><?= ($totalBeliBarang > 0) ? $roiPct . '%' : '-' ?></strong></span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-[11px] font-bold text-slate-500 uppercase tracking-wider border-b border-slate-200/80">
                    <tr>
                        <th class="py-2 px-3 w-8 text-center">NO</th>
                        <th class="py-2 px-3 min-w-[170px]">KOMPONEN BARANG</th>
                        <th class="py-2 px-2.5 text-center min-w-[70px]">QTY</th>
                        <th class="py-2 px-3 text-right min-w-[120px]">NILAI KONTRAK</th>
                        <th class="py-2 px-3 text-right min-w-[120px]">TOTAL HPP</th>
                        <th class="py-2 px-3 text-right min-w-[120px]">ESTIMASI UNTUNG</th>
                        <th class="py-2 px-3 text-center min-w-[90px]">MARGIN</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php if (empty($itemsWithProfit)): ?>
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-400 text-xs">
                                Belum ada komponen barang.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php $no = 1; foreach ($itemsWithProfit as $it): ?>
                            <?php 
                            $itUntung = (float)($it['estimasi_untung'] ?? 0);
                            $itMargin = (float)($it['margin_pct'] ?? 0);
                            $isItemLoss = ($itUntung < 0);
                            ?>
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <td class="py-2 px-3 text-center text-slate-400 font-mono text-[11px]"><?= $no++ ?></td>
                                <td class="py-2 px-3 text-xs font-bold text-slate-800">
                                    <?= htmlspecialchars($it['komponen'] ?? $it['nama_barang'] ?? '-') ?>
                                </td>
                                <td class="py-2 px-2.5 text-center whitespace-nowrap font-bold text-slate-800 text-xs">
                                    <?= (float)$it['jumlah'] ?> <?= htmlspecialchars($it['satuan'] ?: 'Unit') ?>
                                </td>
                                <td class="py-2 px-3 text-right whitespace-nowrap text-xs font-mono text-slate-700">
                                    Rp <?= number_format((float)$it['total_kontrak'], 0, ',', '.') ?>
                                </td>
                                <td class="py-2 px-3 text-right whitespace-nowrap text-xs font-mono text-slate-700">
                                    Rp <?= number_format((float)$it['hpp'], 0, ',', '.') ?>
                                </td>
                                <td class="py-2 px-3 text-right whitespace-nowrap text-xs font-mono font-bold <?= $isItemLoss ? 'text-rose-600' : 'text-emerald-700' ?>">
                                    Rp <?= number_format($itUntung, 0, ',', '.') ?>
                                </td>
                                <td class="py-2 px-3 text-center whitespace-nowrap">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-bold font-mono <?= $isItemLoss ? 'bg-rose-50 text-rose-700 border border-rose-200' : 'bg-emerald-50 text-emerald-700 border border-emerald-200' ?>">
                                        <?= ($itMargin > 0 ? '+' : '') . $itMargin ?>%
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
