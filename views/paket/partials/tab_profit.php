<!-- ========================================== -->
<!-- 6. TAB 6: ESTIMASI MARGIN PROFIT           -->
<!-- ========================================== -->
<div id="tabContent_profit" class="<?= ($activeTab === 'profit') ? '' : 'hidden ' ?>space-y-4">
    <?php
    $isProfit = ($nilaiBersihProfit > 0);
    $isLoss   = ($nilaiBersihProfit < 0);
    $isZero   = ($nilaiBersihProfit == 0);
    $penerimaanSetelahPajak = $acuanPagu - $totalPasalVal;
    ?>

    <!-- 1. EXECUTIVE STATUS BANNER: MENGUNTUNGKAN VS MINUS -->
    <div class="rounded-2xl p-5 sm:p-6 border transition-all <?= $isLoss ? 'bg-gradient-to-br from-rose-50 via-white to-rose-50/40 border-rose-200 shadow-xs' : ($isProfit ? 'bg-gradient-to-br from-emerald-50 via-white to-teal-50/40 border-emerald-200 shadow-xs' : 'bg-slate-50 border-slate-200') ?>">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 mb-2 flex-wrap">
                    <?php if ($isLoss): ?>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-rose-600 text-white shadow-xs">
                            <ion-icon name="alert-circle" class="text-sm"></ion-icon>
                            <span>PROYEK MINUS (DEFISIT / RUGI)</span>
                        </span>
                    <?php elseif ($isProfit): ?>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-600 text-white shadow-xs">
                            <ion-icon name="checkmark-circle" class="text-sm"></ion-icon>
                            <span>PROYEK MENGUNTUNGKAN (PROFIT)</span>
                        </span>
                    <?php else: ?>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-slate-600 text-white shadow-xs">
                            <ion-icon name="remove-circle" class="text-sm"></ion-icon>
                            <span>IMPAS (BREAK-EVEN)</span>
                        </span>
                    <?php endif; ?>

                    <span class="text-xs text-slate-500 font-medium">Analisa Margin Keuntungan Proyek</span>
                </div>

                <h3 class="text-2xl sm:text-3xl font-extrabold font-mono tracking-tight flex items-baseline gap-2 <?= $isLoss ? 'text-rose-700' : ($isProfit ? 'text-emerald-700' : 'text-slate-700') ?>">
                    <span><?= ($nilaiBersihProfit > 0 ? '+' : '') . formatRupiah($nilaiBersihProfit) ?></span>
                    <span class="text-sm sm:text-base font-bold font-sans opacity-80">
                        (<?= ($profitMarginPct > 0 ? '+' : '') . $profitMarginPct ?>% Margin)
                    </span>
                </h3>

                <p class="text-xs text-slate-500 mt-1 max-w-2xl leading-relaxed">
                    <?php if ($isLoss): ?>
                        <strong class="text-rose-700">Perhatian:</strong> Akumulasi modal beli barang di toko dan potongan pajak melebihi nilai kontrak proyek sebesar <strong class="text-rose-700"><?= formatRupiah(abs($nilaiBersihProfit)) ?></strong>. Disarankan mengevaluasi ulang harga beli toko atau negosiasi ulang.
                    <?php elseif ($isProfit): ?>
                        Estimasi laba bersih setelah dikurangi seluruh potongan pajak/pasal (<?= (float)$totalPersen ?>%) dan akumulasi modal belanja riil di toko. Return on Investment (ROI): <strong class="text-emerald-700 font-bold"><?= ($roiPct > 0 ? '+' : '') . $roiPct ?>%</strong>.
                    <?php else: ?>
                        Total pendapatan kontrak sama persis dengan total modal belanja barang dan potongan pajak.
                    <?php endif; ?>
                </p>

                <?php if ($itemsUnpricedCount > 0): ?>
                    <div class="inline-flex items-center gap-1.5 mt-2.5 px-2.5 py-1 bg-amber-50 border border-amber-200/80 rounded-lg text-[11px] text-amber-800 font-medium">
                        <ion-icon name="information-circle-outline" class="text-xs shrink-0 text-amber-600"></ion-icon>
                        <span>Ada <strong><?= $itemsUnpricedCount ?></strong> barang yang harga belinya belum diisi di tab Distribusi. Estimasi akan semakin akurat saat semua harga beli terinput.</span>
                    </div>
                <?php endif; ?>
            </div>

            <div class="flex items-center gap-2 self-start md:self-center shrink-0">
                <button type="button" onclick="window.print()" 
                        class="px-3.5 py-2 bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 rounded-xl text-xs font-semibold shadow-2xs transition-all flex items-center gap-1.5">
                    <ion-icon name="print-outline" class="text-sm"></ion-icon>
                    <span>Cetak Analisa</span>
                </button>
            </div>
        </div>
    </div>

    <!-- 2. EMPAT KARTU ALIRAN FINANSIAL (KPI GRID) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5">
        <!-- Kartu 1: Nilai Kontrak -->
        <div class="p-4 bg-white rounded-2xl border border-slate-200 shadow-2xs space-y-1">
            <div class="flex items-center justify-between text-slate-400">
                <span class="text-[10px] uppercase font-bold tracking-wider">1. Nilai Kontrak</span>
                <ion-icon name="document-text-outline" class="text-sm text-blue-600"></ion-icon>
            </div>
            <div class="text-base sm:text-lg font-bold font-mono text-slate-800">
                <?= formatRupiah($acuanPagu) ?>
            </div>
            <p class="text-[10px] text-slate-400">
                <?= $hasNego ? 'Berdasarkan Nilai Pagu Hasil Nego' : 'Berdasarkan Nilai Pagu Awal Kontrak' ?>
            </p>
        </div>

        <!-- Kartu 2: Potongan Pajak & Admin -->
        <div class="p-4 bg-white rounded-2xl border border-slate-200 shadow-2xs space-y-1">
            <div class="flex items-center justify-between text-slate-400">
                <span class="text-[10px] uppercase font-bold tracking-wider">2. Beban Potongan (<?= (float)$totalPersen ?>%)</span>
                <ion-icon name="receipt-outline" class="text-sm text-amber-600"></ion-icon>
            </div>
            <div class="text-base sm:text-lg font-bold font-mono text-amber-700">
                -<?= formatRupiah($totalPasalVal) ?>
            </div>
            <p class="text-[10px] text-slate-400">
                Pasal (<?= (float)$persenPasal ?>%) + Admin (<?= (float)$persenAdmin ?>%) dari <?= $basisLabel ?>
            </p>
        </div>

        <!-- Kartu 3: Modal Beli Barang Toko -->
        <div class="p-4 bg-white rounded-2xl border border-slate-200 shadow-2xs space-y-1">
            <div class="flex items-center justify-between text-slate-400">
                <span class="text-[10px] uppercase font-bold tracking-wider">3. Modal Beli Barang</span>
                <ion-icon name="cart-outline" class="text-sm text-indigo-600"></ion-icon>
            </div>
            <div class="text-base sm:text-lg font-bold font-mono text-indigo-700">
                -<?= formatRupiah($totalBeliBarang) ?>
            </div>
            <p class="text-[10px] text-slate-400">
                <?= $itemsPricedCount ?> dari <?= count($distribusiList) ?> alokasi barang telah terisi harga
            </p>
        </div>

        <!-- Kartu 4: Estimasi Keuntungan Bersih -->
        <div class="p-4 rounded-2xl border shadow-2xs space-y-1 <?= $isLoss ? 'bg-rose-50/60 border-rose-200' : ($isProfit ? 'bg-emerald-50/60 border-emerald-200' : 'bg-slate-50 border-slate-200') ?>">
            <div class="flex items-center justify-between <?= $isLoss ? 'text-rose-500' : ($isProfit ? 'text-emerald-600' : 'text-slate-400') ?>">
                <span class="text-[10px] uppercase font-bold tracking-wider">4. Keuntungan Bersih</span>
                <ion-icon name="<?= $isLoss ? 'trending-down-outline' : 'trending-up-outline' ?>" class="text-sm"></ion-icon>
            </div>
            <div class="text-base sm:text-lg font-black font-mono <?= $isLoss ? 'text-rose-700' : ($isProfit ? 'text-emerald-700' : 'text-slate-700') ?>">
                <?= ($nilaiBersihProfit > 0 ? '+' : '') . formatRupiah($nilaiBersihProfit) ?>
            </div>
            <p class="text-[10px] font-semibold <?= $isLoss ? 'text-rose-600' : ($isProfit ? 'text-emerald-700' : 'text-slate-500') ?>">
                Margin Profit: <?= ($profitMarginPct > 0 ? '+' : '') . $profitMarginPct ?>%
            </p>
        </div>
    </div>

    <!-- 3. GRAFIK ANALISA PROFITABILITAS (Chart.js) -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <!-- Grafik 1: Perbandingan Aliran Dana (Bar Chart Horizontal) -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-4 sm:p-5 flex flex-col justify-between">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div>
                    <h4 class="text-xs font-bold text-slate-800 uppercase tracking-wider flex items-center gap-1.5">
                        <ion-icon name="bar-chart-outline" class="text-emerald-600 text-sm"></ion-icon>
                        <span>Aliran Finansial & Komparasi Dana</span>
                    </h4>
                    <p class="text-[11px] text-slate-400 mt-0.5">Perbandingan nominal Nilai Kontrak vs Potongan vs Modal Beli vs Net Profit.</p>
                </div>
            </div>

            <div class="py-3 my-auto w-full">
                <canvas id="chartProfitBar" height="180"></canvas>
            </div>

            <div class="pt-2 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-400">
                <span>Penerimaan Setelah Potongan: <strong class="text-slate-700 font-mono"><?= formatRupiah($penerimaanSetelahPajak) ?></strong></span>
                <span class="font-medium text-slate-500">Basis: <?= $basisLabel ?></span>
            </div>
        </div>

        <!-- Grafik 2: Komposisi Penggunaan Dana Kontrak (Donut Chart) -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-4 sm:p-5 flex flex-col justify-between">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div>
                    <h4 class="text-xs font-bold text-slate-800 uppercase tracking-wider flex items-center gap-1.5">
                        <ion-icon name="pie-chart-outline" class="text-indigo-600 text-sm"></ion-icon>
                        <span>Proporsi Alokasi Nilai Kontrak</span>
                    </h4>
                    <p class="text-[11px] text-slate-400 mt-0.5">Persentase porsi modal beli toko, beban pajak, dan sisa laba bersih.</p>
                </div>
                <span class="text-[11px] font-bold font-mono px-2 py-0.5 rounded <?= $isLoss ? 'bg-rose-50 text-rose-700 border border-rose-200' : 'bg-emerald-50 text-emerald-700 border border-emerald-200' ?>">
                    Profit: <?= $profitMarginPct ?>%
                </span>
            </div>

            <div class="flex flex-col sm:flex-row items-center justify-center gap-6 py-3 my-auto">
                <div class="relative w-36 h-36 shrink-0 flex items-center justify-center">
                    <canvas id="chartProfitDonut" width="144" height="144"></canvas>
                    <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                        <span class="text-xs font-extrabold font-mono <?= $isLoss ? 'text-rose-600' : 'text-emerald-700' ?>">
                            <?= ($profitMarginPct > 0 ? '+' : '') . $profitMarginPct ?>%
                        </span>
                        <span class="text-[9px] text-slate-400 font-medium uppercase tracking-tight">Margin</span>
                    </div>
                </div>

                <div class="space-y-2 text-xs w-full sm:w-auto">
                    <div class="flex items-center justify-between sm:justify-start gap-3">
                        <span class="flex items-center gap-2 text-slate-600">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-600 shrink-0"></span>
                            <span>Keuntungan Bersih (Profit)</span>
                        </span>
                        <strong class="font-mono text-emerald-700 font-bold"><?= formatRupiah(max(0, $nilaiBersihProfit)) ?></strong>
                    </div>
                    <div class="flex items-center justify-between sm:justify-start gap-3">
                        <span class="flex items-center gap-2 text-slate-600">
                            <span class="w-2.5 h-2.5 rounded-full bg-indigo-500 shrink-0"></span>
                            <span>Modal Belanja Barang Toko</span>
                        </span>
                        <strong class="font-mono text-slate-800"><?= formatRupiah($totalBeliBarang) ?></strong>
                    </div>
                    <div class="flex items-center justify-between sm:justify-start gap-3">
                        <span class="flex items-center gap-2 text-slate-600">
                            <span class="w-2.5 h-2.5 rounded-full bg-amber-500 shrink-0"></span>
                            <span>Beban Pajak & Potongan</span>
                        </span>
                        <strong class="font-mono text-slate-800"><?= formatRupiah($totalPasalVal) ?></strong>
                    </div>
                </div>
            </div>

            <div class="pt-2 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-400">
                <span>Total Nilai Kontrak: <strong class="text-slate-700 font-mono"><?= formatRupiah($acuanPagu) ?></strong></span>
                <span class="text-slate-500">ROI: <?= ($roiPct > 0 ? '+' : '') . $roiPct ?>%</span>
            </div>
        </div>
    </div>

    <!-- 4. TABEL RINCIAN PEMBELIAN BARANG PER ALOKASI (Compact & Detail) -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100">
            <div>
                <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider flex items-center gap-2">
                    <ion-icon name="receipt-outline" class="text-indigo-600 text-sm"></ion-icon>
                    <span>Rincian Modal Pembelian Barang per Lokasi Penerima</span>
                </h3>
                <p class="text-[11px] text-slate-400 mt-0.5">Daftar biaya beli barang di toko pesanan yang menjadi pengurang langsung keuntungan proyek.</p>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" onclick="switchDetailTab('distribusi')" 
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-700 border border-blue-200/90 rounded-xl text-xs font-semibold shadow-2xs transition-all">
                    <ion-icon name="create-outline" class="text-sm"></ion-icon>
                    <span>Update Status & Harga di Tab Distribusi</span>
                </button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs" id="profitDetailTable">
                <thead class="bg-slate-50 text-[10px] uppercase font-bold text-slate-500 border-b border-slate-200/90 tracking-wider">
                    <tr>
                        <th class="py-2 px-3 w-8 text-center">NO</th>
                        <th class="py-2 px-3 min-w-[170px]">TUJUAN PENERIMA</th>
                        <th class="py-2 px-3 min-w-[140px]">KOMPONEN BARANG</th>
                        <th class="py-2 px-3 min-w-[170px]">NAMA BARANG PESANAN (TOKO)</th>
                        <th class="py-2 px-2.5 text-center min-w-[70px]">JUMLAH</th>
                        <th class="py-2 px-3 text-right min-w-[120px]">HARGA BELI SATUAN</th>
                        <th class="py-2 px-3 text-right min-w-[130px]">TOTAL MODAL BELI</th>
                        <th class="py-2 px-3 text-center min-w-[110px]">STATUS</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php if (empty($distribusiList)): ?>
                        <tr>
                            <td colspan="8" class="py-8 text-center text-slate-400">
                                <ion-icon name="cart-outline" class="text-3xl text-slate-300 mb-1"></ion-icon>
                                <p class="text-xs font-semibold text-slate-600">Belum Ada Data Alokasi Barang</p>
                                <p class="text-[11px] text-slate-400">Tentukan alokasi barang terlebih dahulu di Tab Item / Spesifikasi.</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php 
                        $no = 1;
                        $runningTotalBeli = 0;
                        foreach ($distribusiList as $dist): 
                            $qty = (float)$dist['jumlah'];
                            $hargaSatuan = (float)($dist['harga_satuan'] ?? 0);
                            $subtotalBeli = $qty * $hargaSatuan;
                            $runningTotalBeli += $subtotalBeli;
                            $isOrdered = (($dist['status_barang'] ?? '') === 'Sudah Dipesan');
                        ?>
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <!-- No -->
                                <td class="py-1.5 px-3 text-center font-mono text-[11px] text-slate-400 font-semibold whitespace-nowrap">
                                    <?= $no++ ?>
                                </td>

                                <!-- Tujuan Penerima -->
                                <td class="py-1.5 px-3 whitespace-nowrap text-xs font-semibold text-slate-800">
                                    <?= htmlspecialchars($dist['nama_tujuan']) ?>
                                </td>

                                <!-- Komponen Paket -->
                                <td class="py-1.5 px-3 whitespace-nowrap text-xs text-slate-700">
                                    <?= htmlspecialchars($dist['nama_komponen'] ?: '-') ?>
                                </td>

                                <!-- Nama Barang Pesanan Toko -->
                                <td class="py-1.5 px-3 whitespace-nowrap text-xs">
                                    <?php if (!empty($dist['nama_barang'])): ?>
                                        <span class="font-medium text-slate-800"><?= htmlspecialchars($dist['nama_barang']) ?></span>
                                    <?php else: ?>
                                        <span class="text-slate-400 italic text-[11px]">Belum diinput</span>
                                    <?php endif; ?>
                                </td>

                                <!-- Jumlah -->
                                <td class="py-1.5 px-2.5 text-center whitespace-nowrap bg-indigo-50/20 font-mono font-bold text-slate-900 text-xs">
                                    <?= $qty ?> <span class="text-[10px] text-slate-500 font-medium"><?= htmlspecialchars($dist['satuan'] ?: 'Unit') ?></span>
                                </td>

                                <!-- Harga Beli Satuan -->
                                <td class="py-1.5 px-3 text-right whitespace-nowrap font-mono text-xs">
                                    <?php if ($hargaSatuan > 0): ?>
                                        <span class="font-semibold text-slate-700"><?= formatRupiah($hargaSatuan) ?></span>
                                    <?php else: ?>
                                        <span class="text-slate-400 italic text-[11px]">Rp 0</span>
                                    <?php endif; ?>
                                </td>

                                <!-- Total Modal Beli -->
                                <td class="py-1.5 px-3 text-right whitespace-nowrap font-mono text-xs font-bold bg-slate-50/50">
                                    <?php if ($subtotalBeli > 0): ?>
                                        <span class="text-indigo-700"><?= formatRupiah($subtotalBeli) ?></span>
                                    <?php else: ?>
                                        <span class="text-slate-400">-</span>
                                    <?php endif; ?>
                                </td>

                                <!-- Status Pemesanan -->
                                <td class="py-1.5 px-3 text-center whitespace-nowrap">
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-medium <?= $isOrdered ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-500 border border-slate-200' ?>">
                                        <ion-icon name="<?= $isOrdered ? 'checkmark-circle' : 'time-outline' ?>" class="text-xs"></ion-icon>
                                        <span><?= $isOrdered ? 'Sudah Dipesan' : 'Belum Dipesan' ?></span>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>

                <!-- Footer Summary Row -->
                <?php if (!empty($distribusiList)): ?>
                    <tfoot class="bg-slate-50/90 font-bold text-slate-700 border-t-2 border-slate-200 text-xs">
                        <tr>
                            <td colspan="4" class="py-2 px-3 text-right uppercase text-[10px] tracking-wider text-slate-500">
                                Total Modal Pembelian Barang:
                            </td>
                            <td class="py-2 px-2.5 text-center font-mono text-xs text-indigo-700 bg-indigo-50/60 font-extrabold">
                                <?= (float)($distribusiSummary['total_qty_dialokasikan'] ?? 0) ?> Unit
                            </td>
                            <td class="py-2 px-3 text-right text-slate-400 text-[10px] uppercase font-semibold">
                                Total Belanja:
                            </td>
                            <td class="py-2 px-3 text-right font-mono font-extrabold text-indigo-700 bg-indigo-50/60 text-xs">
                                <?= formatRupiah($runningTotalBeli) ?>
                            </td>
                            <td class="py-2 px-3 text-center">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold <?= $isLoss ? 'bg-rose-100 text-rose-800' : 'bg-emerald-100 text-emerald-800' ?>">
                                    Margin: <?= ($profitMarginPct > 0 ? '+' : '') . $profitMarginPct ?>%
                                </span>
                            </td>
                        </tr>
                    </tfoot>
                <?php endif; ?>
            </table>
        </div>
    </div>
</div>

<!-- SCRIPT INIT CHART UNTUK TAB PROFIT -->
<script>
let chartProfitBarInstance = null;
let chartProfitDonutInstance = null;

function initTabProfitCharts() {
    if (typeof Chart === 'undefined') return;

    // 1. Chart Bar Aliran Finansial
    const ctxBar = document.getElementById('chartProfitBar');
    if (ctxBar) {
        if (chartProfitBarInstance) {
            chartProfitBarInstance.destroy();
            chartProfitBarInstance = null;
        }

        const pagu = <?= (float)$acuanPagu ?>;
        const pajak = <?= (float)$totalPasalVal ?>;
        const beli = <?= (float)$totalBeliBarang ?>;
        const profit = <?= (float)$nilaiBersihProfit ?>;

        chartProfitBarInstance = new Chart(ctxBar, {
            type: 'bar',
            data: {
                labels: ['Nilai Kontrak', 'Potongan Pajak', 'Modal Belanja', 'Estimasi Net Profit'],
                datasets: [{
                    data: [pagu, pajak, beli, profit],
                    backgroundColor: [
                        '#3B82F6', // Blue for Kontrak
                        '#F59E0B', // Amber for Pajak
                        '#6366F1', // Indigo for Beli Barang
                        profit >= 0 ? '#10B981' : '#E11D48' // Emerald if Profit, Rose if Minus
                    ],
                    borderRadius: 8,
                    maxBarThickness: 36
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(c) {
                                return ' ' + c.label + ': Rp ' + Number(c.raw).toLocaleString('id-ID');
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(v) {
                                if (Math.abs(v) >= 1000000) return (v / 1000000) + ' Jt';
                                return v;
                            },
                            font: { size: 10 }
                        },
                        grid: { color: '#F1F5F9' }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 10, weight: 'bold' } }
                    }
                }
            }
        });
    }

    // 2. Chart Donut Proporsi Alokasi Dana Kontrak
    const ctxDonut = document.getElementById('chartProfitDonut');
    if (ctxDonut) {
        if (chartProfitDonutInstance) {
            chartProfitDonutInstance.destroy();
            chartProfitDonutInstance = null;
        }

        const estProfitSafe = Math.max(0, <?= (float)$nilaiBersihProfit ?>);
        const totalBeli = <?= (float)$totalBeliBarang ?>;
        const totalPajak = <?= (float)$totalPasalVal ?>;

        chartProfitDonutInstance = new Chart(ctxDonut, {
            type: 'doughnut',
            data: {
                labels: ['Keuntungan Bersih (Profit)', 'Modal Beli Barang Toko', 'Beban Pajak & Potongan'],
                datasets: [{
                    data: [estProfitSafe, totalBeli, totalPajak],
                    backgroundColor: ['#059669', '#6366F1', '#F59E0B'],
                    borderWidth: 2,
                    borderColor: '#ffffff',
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: false,
                maintainAspectRatio: false,
                cutout: '72%',
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(c) {
                                return ' ' + c.label + ': Rp ' + Number(c.raw).toLocaleString('id-ID');
                            }
                        }
                    }
                }
            }
        });
    }
}
</script>
