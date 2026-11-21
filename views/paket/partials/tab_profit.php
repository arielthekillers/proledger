<!-- ========================================== -->
<!-- 6. TAB 6: ESTIMASI MARGIN PROFIT           -->
<!-- ========================================== -->
<div id="tabContent_profit" class="<?= ($activeTab === 'profit') ? '' : 'hidden ' ?>space-y-4">
    <?php
    $isProfit = ($nilaiBersihProfit > 0);
    $isLoss   = ($nilaiBersihProfit < 0);
    $isZero   = ($nilaiBersihProfit == 0);
    $persenPasal = isset($paket['persen_pasal']) && $paket['persen_pasal'] !== '' ? (float)$paket['persen_pasal'] : 13.0;
    $persenAdmin = isset($paket['persen_admin']) && $paket['persen_admin'] !== '' ? (float)$paket['persen_admin'] : 2.0;
    $totalPersen = $persenPasal + $persenAdmin;
    $basisLabel  = (($paket['tipe_potongan'] ?? 'dpp') === 'kontrak') ? 'Nilai Kontrak' : 'DPP';
    ?>

    <!-- TABLE CARD -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <!-- Top Bar Ringkas -->
        <div class="p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100">
            <div>
                <h3 class="text-base font-bold text-slate-800 flex items-center gap-2">
                    <ion-icon name="calculator-outline" class="text-emerald-700 text-lg"></ion-icon>
                    <span>Estimasi Margin Profit Proyek</span>
                </h3>
                <p class="text-xs text-slate-400 mt-0.5">
                    Perhitungan Nilai Kontrak, Beban Potongan (<?= (float)$totalPersen ?>%), dan Modal Beli Barang (HPP) per alokasi tujuan.
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

                <!-- Tombol Atur Skema Potongan -->
                <?php if ($role === 'admin' || $role === 'user'): ?>
                    <button type="button" onclick="openModalSkemaPotongan()" 
                            class="px-3 py-1.5 bg-amber-50 hover:bg-amber-100 text-amber-800 border border-amber-200/80 rounded-xl text-xs font-semibold shadow-2xs transition-all flex items-center gap-1.5">
                        <ion-icon name="options-outline" class="text-sm text-amber-600"></ion-icon>
                        <span>Atur Skema Potongan</span>
                    </button>
                <?php endif; ?>

                <!-- Tombol Cetak / PDF -->
                <button type="button" onclick="window.print()" 
                        class="px-3 py-1.5 bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 rounded-xl text-xs font-semibold shadow-2xs transition-all flex items-center gap-1.5">
                    <ion-icon name="print-outline" class="text-sm text-slate-500"></ion-icon>
                    <span>Cetak</span>
                </button>
            </div>
        </div>

        <!-- Tabel Sesuai Format Tab Pemesanan (Flat, Tidak Di-group) -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs" id="profitTable">
                <thead class="bg-slate-50 text-[10px] uppercase font-bold text-slate-500 border-b border-slate-200/90 tracking-wider">
                    <tr>
                        <th class="py-2.5 px-3 w-8 text-center">NO</th>
                        <th class="py-2.5 px-3 min-w-[150px]">KOMPONEN</th>
                        <th class="py-2.5 px-3 min-w-[170px]">TUJUAN PENERIMA</th>
                        <th class="py-2.5 px-2.5 text-center min-w-[70px]">SATUAN</th>
                        <th class="py-2.5 px-2.5 text-center min-w-[60px]">JUMLAH</th>
                        <th class="py-2.5 px-3 text-right min-w-[120px]">HARGA SATUAN</th>
                        <th class="py-2.5 px-3 text-right min-w-[130px]">#</th>
                        <th class="py-2.5 px-3 text-right min-w-[130px]">HPP</th>
                        <th class="py-2.5 px-3 text-right min-w-[140px]">ESTIMASI UNTUNG</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php if (empty($distribusiList)): ?>
                        <tr>
                            <td colspan="9" class="py-8 text-center text-slate-400">
                                <div class="w-10 h-10 bg-slate-50 rounded-2xl flex items-center justify-center mx-auto mb-2 text-xl text-slate-300">
                                    <ion-icon name="calculator-outline"></ion-icon>
                                </div>
                                <h4 class="text-xs font-bold text-slate-700 mb-0.5">Belum Ada Data Alokasi</h4>
                                <p class="text-[11px] text-slate-400 mb-3">Tentukan alokasi tujuan terlebih dahulu melalui tab <strong>Item / Spesifikasi</strong>.</p>
                                <button type="button" onclick="switchDetailTab('items')" 
                                        class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-semibold shadow-xs transition-all">
                                    <ion-icon name="list-outline"></ion-icon>
                                    <span>Buka Tab Item / Spesifikasi</span>
                                </button>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php 
                        $no = 1;
                        $totalNilaiKontrakDist = 0;
                        $totalHppDist = 0;
                        foreach ($distribusiList as $dist): 
                            $qty = (float)$dist['jumlah'];
                            $itemHargaSatuan = (float)($dist['item_harga_satuan'] ?? 0);
                            $totalKontrakRow = $qty * $itemHargaSatuan;
                            $hargaBeliSatuan = (float)($dist['harga_satuan'] ?? 0);
                            $hppRow = $qty * $hargaBeliSatuan;
                            $untungRow = $totalKontrakRow - $hppRow;

                            $totalNilaiKontrakDist += $totalKontrakRow;
                            $totalHppDist += $hppRow;
                        ?>
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <!-- No -->
                                <td class="py-2 px-3 text-center font-mono text-[11px] text-slate-400 font-semibold whitespace-nowrap">
                                    <?= $no++ ?>
                                </td>

                                <!-- Komponen -->
                                <td class="py-2 px-3 whitespace-nowrap text-xs font-bold text-slate-800">
                                    <?= htmlspecialchars($dist['nama_komponen'] ?: '-') ?>
                                </td>

                                <!-- Tujuan Penerima -->
                                <td class="py-2 px-3 whitespace-nowrap text-xs font-semibold text-slate-800">
                                    <?= htmlspecialchars($dist['nama_tujuan']) ?>
                                </td>

                                <!-- Satuan -->
                                <td class="py-2 px-2.5 text-center whitespace-nowrap">
                                    <span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-700 font-semibold text-xs">
                                        <?= htmlspecialchars($dist['satuan'] ?: 'Unit') ?>
                                    </span>
                                </td>

                                <!-- Jumlah -->
                                <td class="py-2 px-2.5 text-center whitespace-nowrap font-bold text-slate-800 text-xs">
                                    <?= (float)$qty ?>
                                </td>

                                <!-- Harga Satuan Kontrak -->
                                <td class="py-2 px-3 text-right whitespace-nowrap font-mono text-xs text-slate-700">
                                    <?= $itemHargaSatuan > 0 ? formatRupiah($itemHargaSatuan) : '-' ?>
                                </td>

                                <!-- # (Total Kontrak Row) -->
                                <td class="py-2 px-3 text-right whitespace-nowrap font-mono font-bold text-xs text-slate-800">
                                    <?= $totalKontrakRow > 0 ? formatRupiah($totalKontrakRow) : '-' ?>
                                </td>

                                <!-- HPP Row -->
                                <td class="py-2 px-3 text-right whitespace-nowrap font-mono font-bold text-xs">
                                    <?php if ($hppRow > 0): ?>
                                        <span class="text-indigo-700"><?= formatRupiah($hppRow) ?></span>
                                    <?php else: ?>
                                        <span class="text-slate-300 font-normal italic">-</span>
                                    <?php endif; ?>
                                </td>

                                <!-- Estimasi Untung Row -->
                                <td class="py-2 px-3 text-right whitespace-nowrap font-mono font-extrabold text-xs">
                                    <?php if ($untungRow > 0): ?>
                                        <span class="text-emerald-700">+<?= formatRupiah($untungRow) ?></span>
                                    <?php elseif ($untungRow < 0): ?>
                                        <span class="text-rose-600"><?= formatRupiah($untungRow) ?></span>
                                    <?php else: ?>
                                        <span class="text-slate-400">0</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>

                <!-- Footer Summary (Sesuai Referensi & Tab Pemesanan) -->
                <?php if (!empty($distribusiList)): ?>
                    <tfoot class="border-t-2 border-slate-300">
                        <!-- Baris 1: Subtotal Item & HPP -->
                        <tr class="bg-slate-50 text-slate-800 font-bold text-xs border-b border-slate-200">
                            <td colspan="4" class="py-2.5 px-3 text-right uppercase text-[10px] tracking-wider text-slate-500 font-bold">
                                Subtotal Nilai Komponen & HPP:
                            </td>
                            <td class="py-2.5 px-2.5 text-center font-bold text-slate-800 text-xs whitespace-nowrap">
                                <?= (float)($distribusiSummary['total_qty_dialokasikan'] ?? 0) ?>
                            </td>
                            <td class="py-2.5 px-3 text-right font-mono text-xs text-slate-400">-</td>
                            <td class="py-2.5 px-3 text-right font-mono font-bold text-slate-800 text-xs whitespace-nowrap">
                                <?= formatRupiah($totalNilaiKontrakDist) ?>
                            </td>
                            <td class="py-2.5 px-3 text-right font-mono font-extrabold text-indigo-700 text-xs whitespace-nowrap">
                                <?= formatRupiah($totalHppDist) ?>
                            </td>
                            <td class="py-2.5 px-3 text-right font-mono font-extrabold text-emerald-700 text-xs whitespace-nowrap">
                                <?= formatRupiah($totalNilaiKontrakDist - $totalHppDist) ?>
                            </td>
                        </tr>

                        <!-- Baris 2: Beban Potongan Pajak & Admin -->
                        <tr class="bg-amber-50/40 text-amber-900 font-semibold text-xs border-b border-slate-200">
                            <td colspan="6" class="py-2 px-3 text-right text-[10px] uppercase tracking-wider text-amber-800 font-bold">
                                Potongan (Pasal <?= (float)$persenPasal ?>% + Admin <?= (float)$persenAdmin ?>% dari <?= $basisLabel ?>):
                            </td>
                            <td class="py-2 px-3 text-right font-mono text-slate-400 text-xs">-</td>
                            <td class="py-2 px-3 text-right font-mono text-slate-400 text-xs">-</td>
                            <td class="py-2 px-3 text-right font-mono font-bold text-amber-700 text-xs whitespace-nowrap">
                                -<?= formatRupiah($totalPasalVal) ?>
                            </td>
                        </tr>

                        <!-- Baris 3: ESTIMASI UNTUNG BERSIH (NET PROFIT) -->
                        <tr class="<?= $isLoss ? 'bg-rose-50 text-rose-950' : ($isProfit ? 'bg-emerald-50 text-emerald-950' : 'bg-slate-100 text-slate-900') ?> text-xs font-bold border-b-2 border-slate-300">
                            <td colspan="4" class="py-2.5 px-3 text-right uppercase tracking-wider font-extrabold text-xs">
                                ESTIMASI UNTUNG BERSIH (NET PROFIT):
                            </td>
                            <td class="py-2.5 px-2.5 text-center font-extrabold text-xs">
                                <?= (float)($distribusiSummary['total_qty_dialokasikan'] ?? 0) ?>
                            </td>
                            <td class="py-2.5 px-3 text-right text-slate-400 font-mono text-xs">-</td>
                            <td class="py-2.5 px-3 text-right font-mono font-extrabold text-xs whitespace-nowrap" title="Nilai Nego (Acuan Utama): <?= formatRupiah($acuanPagu) ?>">
                                <div class="text-xs"><?= formatRupiah($acuanPagu) ?></div>
                                <div class="text-[9px] font-normal text-emerald-700">Acuan Nego</div>
                            </td>
                            <td class="py-2.5 px-3 text-right font-mono font-extrabold text-xs whitespace-nowrap <?= $totalBeliBarang > 0 ? 'text-indigo-800' : 'text-slate-500' ?>">
                                <?= formatRupiah($totalBeliBarang) ?>
                            </td>
                            <td class="py-2.5 px-3 text-right whitespace-nowrap">
                                <div class="font-mono text-sm sm:text-base font-black <?= $isLoss ? 'text-rose-700' : ($isProfit ? 'text-emerald-700' : 'text-slate-700') ?>">
                                    <?= ($nilaiBersihProfit > 0 ? '+' : '') . formatRupiah($nilaiBersihProfit) ?>
                                </div>
                                <div class="text-[10px] font-bold mt-0.5 <?= $isLoss ? 'text-rose-600' : ($isProfit ? 'text-emerald-700' : 'text-slate-500') ?>">
                                    Margin: <?= ($profitMarginPct > 0 ? '+' : '') . $profitMarginPct ?>% &bull; <?= $isLoss ? 'MINUS' : ($isProfit ? 'MENGUNTUNGKAN' : 'IMPAS') ?>
                                </div>
                            </td>
                        </tr>
                    </tfoot>
                <?php endif; ?>
            </table>
        </div>
    </div>
</div>
