<!-- ========================================== -->
<!-- 6. TAB 6: ESTIMASI MARGIN PROFIT           -->
<!-- ========================================== -->
<div id="tabContent_profit" class="<?= ($activeTab === 'profit') ? '' : 'hidden ' ?>space-y-4">
    <?php
    $isProfit = ($nilaiBersihProfit > 0);
    $isLoss   = ($nilaiBersihProfit < 0);
    $isZero   = ($nilaiBersihProfit == 0);
    $itemsList = $itemsWithProfit ?? [];
    ?>

    <!-- TABLE CARD -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <!-- Top Bar Ringkas -->
        <div class="p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100">
            <div>
                <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider flex items-center gap-2">
                    <ion-icon name="calculator-outline" class="text-emerald-700 text-sm"></ion-icon>
                    <span>Estimasi Margin Profit Proyek</span>
                </h3>
                <p class="text-[11px] text-slate-400 mt-0.5">
                    Perhitungan Nilai Kontrak, Beban Potongan (<?= (float)$totalPersen ?>%), dan Modal Beli Barang (HPP).
                </p>
            </div>
            <div class="flex items-center gap-2 flex-wrap">
                <!-- Status Badge -->
                <?php if ($isLoss): ?>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200 shadow-2xs">
                        <ion-icon name="alert-circle" class="text-sm text-rose-600"></ion-icon>
                        <span>MINUS (<?= ($profitMarginPct > 0 ? '+' : '') . $profitMarginPct ?>%)</span>
                    </span>
                <?php elseif ($isProfit): ?>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 shadow-2xs">
                        <ion-icon name="checkmark-circle" class="text-sm text-emerald-600"></ion-icon>
                        <span>MENGUNTUNGKAN (+<?= $profitMarginPct ?>%)</span>
                    </span>
                <?php else: ?>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-700 border border-slate-200 shadow-2xs">
                        <ion-icon name="remove-circle" class="text-sm text-slate-500"></ion-icon>
                        <span>IMPAS (0%)</span>
                    </span>
                <?php endif; ?>

                <!-- Tombol Cetak / PDF -->
                <button type="button" onclick="window.print()" 
                        class="px-3 py-1.5 bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 rounded-xl text-xs font-semibold shadow-2xs transition-all flex items-center gap-1.5">
                    <ion-icon name="print-outline" class="text-sm text-slate-500"></ion-icon>
                    <span>Cetak</span>
                </button>
            </div>
        </div>

        <!-- Tabel Sesuai Format Referensi Excel User -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <!-- Header Dark Forest Green (#24543b) -->
                <thead>
                    <tr class="bg-[#24543b] text-white text-[11px] font-bold uppercase tracking-wider">
                        <th class="py-3 px-3 text-center w-10 border-b border-[#1b3f2c]">NO</th>
                        <th class="py-3 px-3 min-w-[200px] border-b border-[#1b3f2c]">KOMPONEN BARANG</th>
                        <th class="py-3 px-2.5 text-center min-w-[70px] border-b border-[#1b3f2c]">SATUAN</th>
                        <th class="py-3 px-2.5 text-center min-w-[60px] border-b border-[#1b3f2c]">JUMLAH</th>
                        <th class="py-3 px-3 text-right min-w-[130px] border-b border-[#1b3f2c]">HARGA SATUAN</th>
                        <th class="py-3 px-3 text-right min-w-[130px] border-b border-[#1b3f2c]">#</th>
                        <th class="py-3 px-3 text-right min-w-[130px] border-b border-[#1b3f2c]">HPP</th>
                        <th class="py-3 px-3 text-right min-w-[140px] border-b border-[#1b3f2c]">ESTIMASI UNTUNG</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    <?php if (empty($itemsList)): ?>
                        <tr>
                            <td colspan="8" class="py-8 text-center text-slate-400">
                                <ion-icon name="cube-outline" class="text-3xl text-slate-300 mb-1"></ion-icon>
                                <p class="text-xs font-semibold text-slate-600">Belum Ada Data Item / Spesifikasi</p>
                                <p class="text-[11px] text-slate-400">Tambahkan item kegiatan di Tab Item / Spesifikasi untuk memulai analisa.</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php 
                        $no = 1;
                        foreach ($itemsList as $it): 
                            $qty = (float)$it['jumlah'];
                            $hargaSatuan = (float)$it['harga_satuan'];
                            $totalKontrak = (float)$it['total_kontrak'];
                            $hpp = (float)$it['hpp'];
                            $untung = (float)$it['estimasi_untung'];
                            $hasDists = !empty($it['dists']);
                        ?>
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <!-- NO -->
                                <td class="py-2.5 px-3 text-center font-mono text-[11px] text-slate-400 font-semibold whitespace-nowrap align-middle">
                                    <?= $no++ ?>
                                </td>

                                <!-- KOMPONEN BARANG -->
                                <td class="py-2.5 px-3 align-middle">
                                    <div class="font-bold text-slate-800 text-xs">
                                        <?= htmlspecialchars($it['komponen']) ?>
                                    </div>
                                    <?php if (!empty($it['spesifikasi'])): ?>
                                        <div class="text-[11px] text-slate-400 font-normal truncate max-w-xs mt-0.5" title="<?= htmlspecialchars($it['spesifikasi']) ?>">
                                            <?= htmlspecialchars($it['spesifikasi']) ?>
                                        </div>
                                    <?php endif; ?>
                                    <?php if ($hasDists): ?>
                                        <button type="button" onclick="toggleProfitDetail(<?= $it['id'] ?>)" 
                                                class="inline-flex items-center gap-1 text-[10px] text-emerald-700 hover:text-emerald-800 font-semibold mt-1">
                                            <ion-icon name="chevron-down-outline" id="arrow_profit_<?= $it['id'] ?>" class="text-[10px] transition-transform"></ion-icon>
                                            <span>Rincian HPP (<?= count($it['dists']) ?> Lokasi)</span>
                                        </button>
                                    <?php endif; ?>
                                </td>

                                <!-- SATUAN -->
                                <td class="py-2.5 px-2.5 text-center whitespace-nowrap align-middle">
                                    <span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-700 font-semibold text-xs">
                                        <?= htmlspecialchars($it['satuan'] ?: 'Unit') ?>
                                    </span>
                                </td>

                                <!-- JUMLAH -->
                                <td class="py-2.5 px-2.5 text-center whitespace-nowrap font-bold text-slate-800 text-xs align-middle">
                                    <?= (float)$qty ?>
                                </td>

                                <!-- HARGA SATUAN -->
                                <td class="py-2.5 px-3 text-right whitespace-nowrap font-mono text-slate-700 align-middle">
                                    <?= formatRupiah($hargaSatuan) ?>
                                </td>

                                <!-- # (TOTAL KONTRAK ITEM) -->
                                <td class="py-2.5 px-3 text-right whitespace-nowrap font-mono font-bold text-slate-800 align-middle">
                                    <?= formatRupiah($totalKontrak) ?>
                                </td>

                                <!-- HPP (MODAL BELI BARANG) -->
                                <td class="py-2.5 px-3 text-right whitespace-nowrap font-mono font-bold align-middle">
                                    <?php if ($hpp > 0): ?>
                                        <span class="text-indigo-700"><?= formatRupiah($hpp) ?></span>
                                    <?php else: ?>
                                        <span class="text-slate-300 font-normal italic">-</span>
                                    <?php endif; ?>
                                </td>

                                <!-- ESTIMASI UNTUNG -->
                                <td class="py-2.5 px-3 text-right whitespace-nowrap font-mono font-extrabold align-middle">
                                    <?php if ($untung > 0): ?>
                                        <span class="text-emerald-700">+<?= formatRupiah($untung) ?></span>
                                    <?php elseif ($untung < 0): ?>
                                        <span class="text-rose-600"><?= formatRupiah($untung) ?></span>
                                    <?php else: ?>
                                        <span class="text-slate-400">0</span>
                                    <?php endif; ?>
                                </td>
                            </tr>

                            <!-- Sub-Row Rincian Distribusi (Collapsed by Default) -->
                            <?php if ($hasDists): ?>
                                <tr id="detail_profit_<?= $it['id'] ?>" class="hidden bg-slate-50/70 border-y border-slate-100">
                                    <td colspan="8" class="py-2.5 px-4 pl-12">
                                        <div class="text-[11px] font-bold text-slate-600 mb-1.5 flex items-center gap-1.5">
                                            <ion-icon name="storefront-outline" class="text-xs text-indigo-600"></ion-icon>
                                            <span>Rincian Pembelian Toko & Alokasi Tujuan:</span>
                                        </div>
                                        <div class="overflow-x-auto rounded-lg border border-slate-200 bg-white">
                                            <table class="w-full text-left text-[11px]">
                                                <thead class="bg-slate-50 text-[10px] text-slate-500 uppercase font-bold border-b border-slate-100">
                                                    <tr>
                                                        <th class="py-1.5 px-3">Tujuan Penerima</th>
                                                        <th class="py-1.5 px-3">Barang Pesanan Toko</th>
                                                        <th class="py-1.5 px-2.5 text-center">Satuan</th>
                                                        <th class="py-1.5 px-2.5 text-center">Qty</th>
                                                        <th class="py-1.5 px-3 text-right">Harga Beli Satuan</th>
                                                        <th class="py-1.5 px-3 text-right">Total HPP</th>
                                                        <th class="py-1.5 px-3 text-center">Status Barang</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="divide-y divide-slate-100">
                                                    <?php foreach ($it['dists'] as $d): 
                                                        $dQty = (float)$d['jumlah'];
                                                        $dHppSatuan = (float)($d['harga_satuan'] ?? 0);
                                                        $dSubtotal = $dQty * $dHppSatuan;
                                                    ?>
                                                        <tr>
                                                            <td class="py-1.5 px-3 font-medium text-slate-800">
                                                                <?= htmlspecialchars($d['nama_tujuan']) ?>
                                                            </td>
                                                            <td class="py-1.5 px-3 text-slate-600">
                                                                <?= !empty($d['nama_barang']) ? htmlspecialchars($d['nama_barang']) : '<span class="text-slate-400 italic">Sesuai komponen</span>' ?>
                                                            </td>
                                                            <td class="py-1.5 px-2.5 text-center">
                                                                <span class="px-1.5 py-0.5 rounded bg-slate-100 text-slate-700 font-medium text-xs">
                                                                    <?= htmlspecialchars($it['satuan'] ?: 'Unit') ?>
                                                                </span>
                                                            </td>
                                                            <td class="py-1.5 px-2.5 text-center font-bold text-slate-700 text-xs">
                                                                <?= $dQty ?>
                                                            </td>
                                                            <td class="py-1.5 px-3 text-right font-mono text-slate-600">
                                                                <?= $dHppSatuan > 0 ? formatRupiah($dHppSatuan) : '<span class="text-slate-400 italic">Belum diinput</span>' ?>
                                                            </td>
                                                            <td class="py-1.5 px-3 text-right font-mono font-bold text-indigo-700">
                                                                <?= $dSubtotal > 0 ? formatRupiah($dSubtotal) : '-' ?>
                                                            </td>
                                                            <td class="py-1.5 px-3 text-center">
                                                                <span class="inline-block px-1.5 py-0.5 rounded text-[10px] font-medium <?= (($d['status_barang'] ?? '') === 'Sudah Dipesan') ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-500' ?>">
                                                                    <?= htmlspecialchars($d['status_barang'] ?? 'Belum Dipesan') ?>
                                                                </span>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>

                <!-- FOOTER SUMMARY PERSIS DENGAN FORMAT PENGURANGAN DAN USER REFERENSI -->
                <tfoot class="border-t-2 border-slate-300">
                    <!-- Baris 1: Subtotal Item & Modal HPP -->
                    <tr class="bg-slate-50 text-slate-800 font-bold text-xs border-b border-slate-200">
                        <td colspan="3" class="py-2.5 px-3 text-right uppercase text-[11px] tracking-wider text-slate-600">
                            Subtotal Nilai Item & HPP:
                        </td>
                        <td class="py-2.5 px-2.5 text-center font-bold text-slate-800 whitespace-nowrap">
                            <?= $totalQtyAllItems ?>
                        </td>
                        <td class="py-2.5 px-3 text-right text-slate-400 font-mono">-</td>
                        <td class="py-2.5 px-3 text-right font-mono font-bold text-slate-800 whitespace-nowrap">
                            <?= formatRupiah($totalNilaiItemsAll) ?>
                        </td>
                        <td class="py-2.5 px-3 text-right font-mono font-extrabold text-indigo-700 whitespace-nowrap">
                            <?= formatRupiah($totalHppAllItems) ?>
                        </td>
                        <td class="py-2.5 px-3 text-right font-mono font-extrabold text-emerald-700 whitespace-nowrap">
                            <?= formatRupiah($totalNilaiItemsAll - $totalHppAllItems) ?>
                        </td>
                    </tr>

                    <!-- Baris 2: Beban Potongan Pajak & Admin (15%) -->
                    <tr class="bg-amber-50/40 text-amber-900 font-semibold text-xs border-b border-slate-200">
                        <td colspan="5" class="py-2 px-3 text-right text-[11px] uppercase tracking-wider text-amber-800">
                            Potongan (Pasal <?= (float)$persenPasal ?>% + Admin <?= (float)$persenAdmin ?>% dari <?= $basisLabel ?>):
                        </td>
                        <td class="py-2 px-3 text-right font-mono text-slate-400">-</td>
                        <td class="py-2 px-3 text-right font-mono text-slate-400">-</td>
                        <td class="py-2 px-3 text-right font-mono font-bold text-amber-700 whitespace-nowrap">
                            -<?= formatRupiah($totalPasalVal) ?>
                        </td>
                    </tr>

                    <!-- Baris 3: ESTIMASI UNTUNG BERSIH (NET PROFIT) -->
                    <tr class="<?= $isLoss ? 'bg-rose-50 text-rose-950' : ($isProfit ? 'bg-emerald-50 text-emerald-950' : 'bg-slate-100 text-slate-900') ?> text-xs font-bold border-b-2 border-slate-300">
                        <td colspan="3" class="py-3 px-3 text-right uppercase tracking-wider font-extrabold text-xs">
                            ESTIMASI UNTUNG BERSIH (NET PROFIT):
                        </td>
                        <td class="py-3 px-2.5 text-center font-extrabold text-xs">
                            <?= $totalQtyAllItems ?>
                        </td>
                        <td class="py-3 px-3 text-right text-slate-400 font-mono">-</td>
                        <td class="py-3 px-3 text-right font-mono font-extrabold text-xs whitespace-nowrap" title="Nilai Kontrak Acuan: <?= formatRupiah($acuanPagu) ?>">
                            <?= formatRupiah($acuanPagu) ?>
                        </td>
                        <td class="py-3 px-3 text-right font-mono font-extrabold text-xs whitespace-nowrap <?= $totalBeliBarang > 0 ? 'text-indigo-800' : 'text-slate-500' ?>">
                            <?= formatRupiah($totalBeliBarang) ?>
                        </td>
                        <td class="py-3 px-3 text-right whitespace-nowrap">
                            <div class="font-mono text-sm sm:text-base font-black <?= $isLoss ? 'text-rose-700' : ($isProfit ? 'text-emerald-700' : 'text-slate-700') ?>">
                                <?= ($nilaiBersihProfit > 0 ? '+' : '') . formatRupiah($nilaiBersihProfit) ?>
                            </div>
                            <div class="text-[10px] font-bold mt-0.5 <?= $isLoss ? 'text-rose-600' : ($isProfit ? 'text-emerald-700' : 'text-slate-500') ?>">
                                Margin: <?= ($profitMarginPct > 0 ? '+' : '') . $profitMarginPct ?>% &bull; <?= $isLoss ? 'MINUS' : ($isProfit ? 'MENGUNTUNGKAN' : 'IMPAS') ?>
                            </div>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<script>
function toggleProfitDetail(itemId) {
    const row = document.getElementById('detail_profit_' + itemId);
    const arrow = document.getElementById('arrow_profit_' + itemId);
    if (!row) return;

    if (row.classList.contains('hidden')) {
        row.classList.remove('hidden');
        if (arrow) arrow.classList.add('rotate-180');
    } else {
        row.classList.add('hidden');
        if (arrow) arrow.classList.remove('rotate-180');
    }
}
</script>
