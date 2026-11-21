<!-- ========================================== -->
<!-- 3. TAB 1: INFORMASI PAKET (EXECUTIVE SUMMARY)-->
<!-- ========================================== -->
<div id="tabContent_info" class="<?= ($activeTab === 'info') ? '' : 'hidden ' ?>space-y-4">

    <!-- 1. TOP WORKFLOW PROGRESS STRIP (Rekapitulasi 5 Tahap Utama) -->
    <?php
    $itP    = $tabProgress['items'] ?? ['done' => 0, 'total' => 0];
    $pesanP = $tabProgress['pemesanan'] ?? ['done' => 0, 'total' => 0];
    $distP  = $tabProgress['distribusi'] ?? ['done' => 0, 'total' => 0];
    $dokP   = $tabProgress['dokumentasi'] ?? ['done' => 0, 'total' => 0];
    $bastP  = $tabProgress['dokumen'] ?? ['done' => 0, 'total' => 0];

    // Persentase keseluruhan
    $totalSteps = max(1, ($itP['total'] + $pesanP['total'] + $distP['total'] + $dokP['total'] + $bastP['total']));
    $doneSteps  = ($itP['done'] + $pesanP['done'] + $distP['done'] + $dokP['done'] + $bastP['done']);
    $overallProgress = min(100, round(($doneSteps / $totalSteps) * 100));
    ?>
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-4 sm:p-5">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-3.5 pb-3 border-b border-slate-100">
            <div>
                <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider flex items-center gap-2">
                    <ion-icon name="git-branch-outline" class="text-blue-600 text-sm"></ion-icon>
                    <span>Status & Alur Pengerjaan Paket</span>
                </h3>
                <p class="text-[11px] text-slate-400 mt-0.5">Ringkasan progres terintegrasi dari alokasi barang, pemesanan, distribusi, dokumentasi, hingga BAST.</p>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-xs font-semibold text-slate-500">Progres Keseluruhan:</span>
                <span class="px-2.5 py-1 rounded-lg text-xs font-bold <?= ($overallProgress >= 100) ? 'bg-emerald-100 text-emerald-800' : 'bg-blue-50 text-blue-700 border border-blue-200/60' ?>">
                    <?= $overallProgress ?>%
                </span>
            </div>
        </div>

        <!-- Flow Pipeline 5 Langkah -->
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-2.5 sm:gap-3">
            <!-- Step 1: Item / Spesifikasi -->
            <button type="button" onclick="switchDetailTab('items')" 
                    class="p-2.5 rounded-xl border text-left transition-all hover:scale-[1.01] cursor-pointer <?= ($itP['total'] > 0 && $itP['done'] >= $itP['total']) ? 'bg-emerald-50/50 border-emerald-200 hover:bg-emerald-50' : 'bg-slate-50/70 border-slate-200/80 hover:bg-slate-100' ?>">
                <div class="flex items-center justify-between mb-1">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500 flex items-center gap-1">
                        <ion-icon name="list-outline" class="text-xs"></ion-icon>
                        <span>1. Alokasi Item</span>
                    </span>
                    <span class="text-[10px] font-bold px-1.5 py-0.2 rounded font-mono <?= ($itP['total'] > 0 && $itP['done'] >= $itP['total']) ? 'bg-emerald-200 text-emerald-800' : 'bg-slate-200 text-slate-700' ?>">
                        <?= $itP['done'] ?>/<?= $itP['total'] ?>
                    </span>
                </div>
                <div class="text-xs font-bold text-slate-800 truncate"><?= (float)($itemSummary['total_qty'] ?? 0) ?> Unit Barang</div>
                <div class="text-[10px] text-slate-400 mt-0.5"><?= (int)($itemSummary['total_item'] ?? 0) ?> Jenis Komponen</div>
            </button>

            <!-- Step 2: Pemesanan Barang -->
            <button type="button" onclick="switchDetailTab('pemesanan')" 
                    class="p-2.5 rounded-xl border text-left transition-all hover:scale-[1.01] cursor-pointer <?= ($pesanP['total'] > 0 && $pesanP['done'] >= $pesanP['total']) ? 'bg-emerald-50/50 border-emerald-200 hover:bg-emerald-50' : 'bg-slate-50/70 border-slate-200/80 hover:bg-slate-100' ?>">
                <div class="flex items-center justify-between mb-1">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500 flex items-center gap-1">
                        <ion-icon name="bag-check-outline" class="text-xs"></ion-icon>
                        <span>2. Pemesanan</span>
                    </span>
                    <span class="text-[10px] font-bold px-1.5 py-0.2 rounded font-mono <?= ($pesanP['total'] > 0 && $pesanP['done'] >= $pesanP['total']) ? 'bg-emerald-200 text-emerald-800' : 'bg-amber-100 text-amber-800' ?>">
                        <?= $pesanP['done'] ?>/<?= $pesanP['total'] ?>
                    </span>
                </div>
                <div class="text-xs font-bold text-slate-800 truncate"><?= $pesanP['done'] ?> Selesai Dipesan</div>
                <div class="text-[10px] text-slate-400 mt-0.5"><?= max(0, $pesanP['total'] - $pesanP['done']) ?> Belum Dipesan</div>
            </button>

            <!-- Step 3: Distribusi -->
            <button type="button" onclick="switchDetailTab('distribusi')" 
                    class="p-2.5 rounded-xl border text-left transition-all hover:scale-[1.01] cursor-pointer <?= ($distP['total'] > 0 && $distP['done'] >= $distP['total']) ? 'bg-emerald-50/50 border-emerald-200 hover:bg-emerald-50' : 'bg-slate-50/70 border-slate-200/80 hover:bg-slate-100' ?>">
                <div class="flex items-center justify-between mb-1">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500 flex items-center gap-1">
                        <ion-icon name="navigate-outline" class="text-xs"></ion-icon>
                        <span>3. Distribusi</span>
                    </span>
                    <span class="text-[10px] font-bold px-1.5 py-0.2 rounded font-mono <?= ($distP['total'] > 0 && $distP['done'] >= $distP['total']) ? 'bg-emerald-200 text-emerald-800' : 'bg-indigo-100 text-indigo-700' ?>">
                        <?= $distP['done'] ?>/<?= $distP['total'] ?>
                    </span>
                </div>
                <div class="text-xs font-bold text-slate-800 truncate"><?= $statusDistBreakdown['tiba_tujuan'] ?> Lokasi Tiba</div>
                <div class="text-[10px] text-slate-400 mt-0.5"><?= $statusDistBreakdown['tiba_gudang'] ?> di Gudang &bull; <?= $statusDistBreakdown['tiba_kantor'] ?> di Kantor</div>
            </button>

            <!-- Step 4: Dokumentasi -->
            <button type="button" onclick="switchDetailTab('dokumentasi')" 
                    class="p-2.5 rounded-xl border text-left transition-all hover:scale-[1.01] cursor-pointer <?= ($dokP['total'] > 0 && $dokP['done'] >= $dokP['total']) ? 'bg-emerald-50/50 border-emerald-200 hover:bg-emerald-50' : 'bg-slate-50/70 border-slate-200/80 hover:bg-slate-100' ?>">
                <div class="flex items-center justify-between mb-1">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500 flex items-center gap-1">
                        <ion-icon name="camera-outline" class="text-xs"></ion-icon>
                        <span>4. Dokumentasi</span>
                    </span>
                    <span class="text-[10px] font-bold px-1.5 py-0.2 rounded font-mono <?= ($dokP['total'] > 0 && $dokP['done'] >= $dokP['total']) ? 'bg-emerald-200 text-emerald-800' : 'bg-teal-100 text-teal-800' ?>">
                        <?= $dokP['done'] ?>/<?= $dokP['total'] ?>
                    </span>
                </div>
                <div class="text-xs font-bold text-slate-800 truncate"><?= (int)($totalDokumentasiFoto ?? 0) ?> Foto Terunggah</div>
                <div class="text-[10px] text-slate-400 mt-0.5"><?= $dokP['done'] ?> dari <?= $dokP['total'] ?> Titik Lengkap</div>
            </button>

            <!-- Step 5: Dokumen BAST -->
            <button type="button" onclick="switchDetailTab('dokumen')" 
                    class="p-2.5 rounded-xl border text-left transition-all hover:scale-[1.01] cursor-pointer <?= ($bastP['total'] > 0 && $bastP['done'] >= $bastP['total']) ? 'bg-emerald-50/50 border-emerald-200 hover:bg-emerald-50' : 'bg-slate-50/70 border-slate-200/80 hover:bg-slate-100' ?>">
                <div class="flex items-center justify-between mb-1">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500 flex items-center gap-1">
                        <ion-icon name="document-text-outline" class="text-xs"></ion-icon>
                        <span>5. Berkas BAST</span>
                    </span>
                    <span class="text-[10px] font-bold px-1.5 py-0.2 rounded font-mono <?= ($bastP['total'] > 0 && $bastP['done'] >= $bastP['total']) ? 'bg-emerald-200 text-emerald-800' : 'bg-amber-100 text-amber-800' ?>">
                        <?= $bastP['done'] ?>/<?= $bastP['total'] ?>
                    </span>
                </div>
                <div class="text-xs font-bold text-slate-800 truncate"><?= $bastP['done'] ?> BAST Diupload</div>
                <div class="text-[10px] text-slate-400 mt-0.5"><?= max(0, $bastP['total'] - $bastP['done']) ?> Berkas Menunggu</div>
            </button>
        </div>
    </div>

    <!-- 2. GRAFIK VISUALISASI INTERAKTIF (Chart.js) -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <!-- Grafik 1: Status Distribusi & Pengiriman (Donut Chart) -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-4 sm:p-5 flex flex-col justify-between">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div>
                    <h4 class="text-xs font-bold text-slate-800 uppercase tracking-wider flex items-center gap-1.5">
                        <ion-icon name="pie-chart-outline" class="text-indigo-600 text-sm"></ion-icon>
                        <span>Monitoring Logistik Distribusi</span>
                    </h4>
                    <p class="text-[11px] text-slate-400 mt-0.5">Sebaran status titik alokasi pengiriman barang.</p>
                </div>
                <span class="text-[11px] font-semibold text-slate-500 font-mono"><?= count($distribusiList) ?> Titik Total</span>
            </div>

            <!-- Canvas & Legend -->
            <div class="flex flex-col sm:flex-row items-center justify-center gap-6 py-4 my-auto">
                <div class="relative w-36 h-36 shrink-0 flex items-center justify-center">
                    <canvas id="chartStatusDistribusi" width="144" height="144"></canvas>
                    <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                        <span class="text-base font-extrabold text-slate-800 font-mono" id="chartDistPercent">
                            <?= (count($distribusiList) > 0) ? round(($statusDistBreakdown['tiba_tujuan'] / count($distribusiList)) * 100) : 0 ?>%
                        </span>
                        <span class="text-[9px] text-slate-400 font-medium uppercase tracking-tight">Sampai</span>
                    </div>
                </div>

                <!-- Clean Legend -->
                <div class="space-y-1.5 text-xs w-full sm:w-auto">
                    <div class="flex items-center justify-between sm:justify-start gap-3">
                        <span class="flex items-center gap-2 text-slate-600">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 shrink-0"></span>
                            <span>Tiba di Tujuan</span>
                        </span>
                        <strong class="font-mono text-slate-800"><?= $statusDistBreakdown['tiba_tujuan'] ?></strong>
                    </div>
                    <div class="flex items-center justify-between sm:justify-start gap-3">
                        <span class="flex items-center gap-2 text-slate-600">
                            <span class="w-2.5 h-2.5 rounded-full bg-amber-500 shrink-0"></span>
                            <span>Tiba di Gudang Transit</span>
                        </span>
                        <strong class="font-mono text-slate-800"><?= $statusDistBreakdown['tiba_gudang'] ?></strong>
                    </div>
                    <div class="flex items-center justify-between sm:justify-start gap-3">
                        <span class="flex items-center gap-2 text-slate-600">
                            <span class="w-2.5 h-2.5 rounded-full bg-blue-500 shrink-0"></span>
                            <span>Tiba di Rumah/Kantor</span>
                        </span>
                        <strong class="font-mono text-slate-800"><?= $statusDistBreakdown['tiba_kantor'] ?></strong>
                    </div>
                    <div class="flex items-center justify-between sm:justify-start gap-3">
                        <span class="flex items-center gap-2 text-slate-600">
                            <span class="w-2.5 h-2.5 rounded-full bg-slate-300 shrink-0"></span>
                            <span>Belum Dipesan / OTW</span>
                        </span>
                        <strong class="font-mono text-slate-800"><?= ($statusDistBreakdown['belum_dipesan'] + $statusDistBreakdown['dalam_pengiriman']) ?></strong>
                    </div>
                </div>
            </div>

            <div class="pt-2 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-400">
                <span>Rute tujuan tercatat: <?= (int)($distribusiSummary['total_tujuan'] ?? 0) ?> sekolah</span>
                <button type="button" onclick="switchDetailTab('distribusi')" class="text-blue-600 hover:underline font-semibold">Kelola Rute &rarr;</button>
            </div>
        </div>

        <!-- Grafik 2: Finansial & Estimasi Keuntungan (Bar / Donut Chart) -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-4 sm:p-5 flex flex-col justify-between">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div>
                    <h4 class="text-xs font-bold text-slate-800 uppercase tracking-wider flex items-center gap-1.5">
                        <ion-icon name="wallet-outline" class="text-emerald-600 text-sm"></ion-icon>
                        <span>Finansial & Estimasi Margin Profit</span>
                    </h4>
                    <p class="text-[11px] text-slate-400 mt-0.5">Komposisi nilai kontrak, modal barang, dan estimasi keuntungan bersih.</p>
                </div>
                <span class="text-[11px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-200">
                    Margin: <?= $profitMarginPct ?>%
                </span>
            </div>

            <!-- Canvas & Legend -->
            <div class="flex flex-col sm:flex-row items-center justify-center gap-6 py-4 my-auto">
                <div class="relative w-36 h-36 shrink-0 flex items-center justify-center">
                    <canvas id="chartFinancial" width="144" height="144"></canvas>
                    <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                        <span class="text-xs font-bold text-slate-400 font-mono">Pagu</span>
                        <span class="text-[11px] font-extrabold text-slate-700 font-mono truncate max-w-[100px] text-center" title="<?= formatRupiah($acuan) ?>">
                            <?= formatRupiah($acuan) ?>
                        </span>
                    </div>
                </div>

                <!-- Clean Legend -->
                <div class="space-y-1.5 text-xs w-full sm:w-auto">
                    <div class="flex items-center justify-between sm:justify-start gap-3">
                        <span class="flex items-center gap-2 text-slate-600">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-600 shrink-0"></span>
                            <span>Est. Keuntungan Bersih</span>
                        </span>
                        <strong class="font-mono text-emerald-700 font-bold"><?= formatRupiah($estimasiProfit) ?></strong>
                    </div>
                    <div class="flex items-center justify-between sm:justify-start gap-3">
                        <span class="flex items-center gap-2 text-slate-600">
                            <span class="w-2.5 h-2.5 rounded-full bg-indigo-500 shrink-0"></span>
                            <span>Modal Beli Barang</span>
                        </span>
                        <strong class="font-mono text-slate-800"><?= formatRupiah($totalBeliBarang) ?></strong>
                    </div>
                    <div class="flex items-center justify-between sm:justify-start gap-3">
                        <span class="flex items-center gap-2 text-slate-600">
                            <span class="w-2.5 h-2.5 rounded-full bg-amber-500 shrink-0"></span>
                            <span>Beban Pajak & Potongan</span>
                        </span>
                        <strong class="font-mono text-slate-800"><?= formatRupiah($totalPasal) ?></strong>
                    </div>
                </div>
            </div>

            <div class="pt-2 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-400">
                <span>Nilai DPP: <?= formatRupiah($dpp) ?></span>
                <span class="font-medium text-slate-600">Potongan: <?= (float)$totalPersen ?>%</span>
            </div>
        </div>
    </div>

    <!-- 3. DETAIL METRIK COMPACT (Tabel Tanpa Kotak-kotak Bertumpuk) -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="grid grid-cols-1 lg:grid-cols-2 divide-y lg:divide-y-0 lg:divide-x divide-slate-100">
            <!-- Kolom Kiri: Rincian Finansial & Pajak -->
            <div class="p-4 sm:p-5 space-y-3">
                <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                    <h4 class="text-xs font-bold text-slate-800 uppercase tracking-wider flex items-center gap-1.5">
                        <ion-icon name="calculator-outline" class="text-blue-600 text-sm"></ion-icon>
                        <span>Rincian Finansial & Perpajakan</span>
                    </h4>
                    <div class="flex items-center gap-2">
                        <span class="text-[11px] text-slate-400 font-mono">Kode: #<?= htmlspecialchars($paket['kode_paket'] ?: $paket['id']) ?></span>
                        <?php if ($role === 'admin' || $role === 'user'): ?>
                            <button type="button" onclick="openModalSkemaPotongan()" class="text-[11px] font-semibold text-amber-600 hover:text-amber-700 hover:underline flex items-center gap-1">
                                <ion-icon name="options-outline"></ion-icon>
                                <span>Atur Skema</span>
                            </button>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="divide-y divide-slate-100 text-xs">
                    <div class="py-2 flex items-center justify-between">
                        <span class="text-slate-500">Nilai Pagu Awal Kontrak</span>
                        <span class="font-mono font-semibold text-slate-400 line-through" title="Pagu awal sebelum negosiasi"><?= formatRupiah($paket['pagu']) ?></span>
                    </div>

                    <div class="py-2.5 flex items-center justify-between bg-emerald-50/60 -mx-4 px-4 sm:-mx-5 sm:px-5 border-y border-emerald-100">
                        <div>
                            <span class="text-emerald-900 font-bold flex items-center gap-1.5">
                                <ion-icon name="checkmark-circle" class="text-emerald-600 text-sm"></ion-icon>
                                <span>Nilai Hasil Negosiasi (Pagu Nego)</span>
                                <span class="text-[9px] bg-emerald-200 text-emerald-800 px-1.5 py-0.2 rounded font-extrabold uppercase">Acuan Utama</span>
                            </span>
                            <span class="text-[10px] text-emerald-700 block mt-0.5">Seluruh perhitungan DPP, potongan & laba bersandar pada nilai ini</span>
                        </div>
                        <span class="font-mono font-black text-emerald-800 text-sm"><?= formatRupiah($paket['pagu_nego'] ?: $acuan) ?></span>
                    </div>

                    <div class="py-2 flex items-center justify-between">
                        <div>
                            <span class="text-slate-600">DPP (Dasar Pengenaan Pajak)</span>
                            <span class="text-[10px] text-slate-400 block">Dihitung dari Nilai Nego / 1.11 (dikurangi PPN 11%)</span>
                        </div>
                        <span class="font-mono font-bold text-slate-800"><?= formatRupiah($dpp) ?></span>
                    </div>

                    <div class="py-2 flex items-center justify-between">
                        <span class="text-slate-500">Potongan Pasal (<?= (float)$persenPasal ?>% <?= $basisLabel ?>)</span>
                        <span class="font-mono font-semibold text-slate-700"><?= formatRupiah($pasalVal) ?></span>
                    </div>

                    <?php if ($persenAdmin > 0): ?>
                        <div class="py-2 flex items-center justify-between">
                            <span class="text-slate-500">Biaya Administrasi (<?= (float)$persenAdmin ?>% <?= $basisLabel ?>)</span>
                            <span class="font-mono font-semibold text-slate-700"><?= formatRupiah($adminVal) ?></span>
                        </div>
                    <?php endif; ?>

                    <div class="py-2.5 flex items-center justify-between bg-amber-50/50 -mx-4 px-4 sm:-mx-5 sm:px-5">
                        <span class="font-bold text-amber-900">Total Potongan (<?= (float)$totalPersen ?>% dari <?= $basisLabel ?>)</span>
                        <span class="font-mono font-extrabold text-amber-700 text-sm"><?= formatRupiah($totalPasal) ?></span>
                    </div>
                </div>
            </div>

            <!-- Kolom Kanan: Rekapitulasi Operasional & Metadata Pelaksana -->
            <div class="p-4 sm:p-5 space-y-3">
                <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                    <h4 class="text-xs font-bold text-slate-800 uppercase tracking-wider flex items-center gap-1.5">
                        <ion-icon name="briefcase-outline" class="text-slate-600 text-sm"></ion-icon>
                        <span>Informasi Pelaksana & Rekap 4 Tab</span>
                    </h4>
                    <span class="text-[11px] text-slate-400">Status Terkini</span>
                </div>

                <div class="divide-y divide-slate-100 text-xs">
                    <!-- Perusahaan & Group -->
                    <div class="py-2 flex items-center justify-between">
                        <span class="text-slate-500">Perusahaan Pelaksana</span>
                        <?php if (!empty($paket['perusahaan'])): ?>
                            <a href="<?= APP_URL ?>/perusahaan/detail/<?= $paket['perusahaan_id'] ?>" class="font-semibold text-blue-600 hover:underline inline-flex items-center gap-1">
                                <span><?= htmlspecialchars($paket['perusahaan']) ?></span>
                                <ion-icon name="open-outline" class="text-[10px]"></ion-icon>
                            </a>
                        <?php else: ?>
                            <span class="text-slate-400">-</span>
                        <?php endif; ?>
                    </div>

                    <div class="py-2 flex items-center justify-between">
                        <span class="text-slate-500">Group Paket</span>
                        <span class="font-semibold text-slate-800">
                            <?= !empty($paket['nama_group']) ? htmlspecialchars($paket['nama_group']) : 'Paket Mandiri' ?>
                        </span>
                    </div>

                    <div class="py-2 flex items-center justify-between">
                        <span class="text-slate-500">Tahun Anggaran</span>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-bold bg-amber-50 text-amber-800 border border-amber-200">
                            TA <?= htmlspecialchars($paket['tahun_anggaran'] ?? date('Y')) ?>
                        </span>
                    </div>

                    <div class="py-2 flex items-center justify-between">
                        <span class="text-slate-500">Tags Paket</span>
                        <div class="flex flex-wrap items-center gap-1.5 justify-end">
                            <?php if (!empty($paket['tags'])): ?>
                                <?php 
                                $tList = array_filter(array_map('trim', explode(',', $paket['tags'])));
                                foreach ($tList as $tg): 
                                ?>
                                    <a href="<?= APP_URL ?>/paket?tag=<?= urlencode($tg) ?>" class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-semibold bg-blue-50 text-blue-700 hover:bg-blue-100 transition-colors">
                                        #<?= htmlspecialchars($tg) ?>
                                    </a>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <span class="text-slate-400 italic text-[11px]">Belum ada tags</span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Shortcut Rekap Tab Item -->
                    <div class="py-2 flex items-center justify-between">
                        <span class="text-slate-500">Total Komponen</span>
                        <div class="flex items-center gap-2">
                            <span class="font-mono font-bold text-slate-800"><?= (int)$itemSummary['total_item'] ?> Item (<?= (float)$itemSummary['total_qty'] ?> Unit)</span>
                            <button type="button" onclick="switchDetailTab('items')" class="text-blue-600 hover:text-blue-800 text-[11px] font-semibold flex items-center">
                                <span>Lihat</span>
                                <ion-icon name="chevron-forward-outline"></ion-icon>
                            </button>
                        </div>
                    </div>

                    <!-- Shortcut Rekap Tab Pemesanan -->
                    <div class="py-2 flex items-center justify-between">
                        <span class="text-slate-500">Pemesanan Toko / Supplier</span>
                        <div class="flex items-center gap-2">
                            <span class="font-mono font-bold text-slate-800"><?= $pesanP['done'] ?>/<?= $pesanP['total'] ?> Dipesan</span>
                            <button type="button" onclick="switchDetailTab('pemesanan')" class="text-blue-600 hover:text-blue-800 text-[11px] font-semibold flex items-center">
                                <span>Lihat</span>
                                <ion-icon name="chevron-forward-outline"></ion-icon>
                            </button>
                        </div>
                    </div>

                    <!-- Shortcut Rekap Tab Distribusi -->
                    <div class="py-2 flex items-center justify-between">
                        <span class="text-slate-500">Penyaluran Distribusi</span>
                        <div class="flex items-center gap-2">
                            <span class="font-mono font-bold text-slate-800"><?= $statusDistBreakdown['tiba_tujuan'] ?>/<?= count($distribusiList) ?> Tiba di Tujuan</span>
                            <button type="button" onclick="switchDetailTab('distribusi')" class="text-blue-600 hover:text-blue-800 text-[11px] font-semibold flex items-center">
                                <span>Lihat</span>
                                <ion-icon name="chevron-forward-outline"></ion-icon>
                            </button>
                        </div>
                    </div>

                    <!-- Shortcut Rekap Tab Dokumentasi -->
                    <div class="py-2 flex items-center justify-between">
                        <span class="text-slate-500">Foto Dokumentasi</span>
                        <div class="flex items-center gap-2">
                            <span class="font-mono font-bold text-slate-800"><?= $dokP['done'] ?>/<?= $dokP['total'] ?> Lokasi (<?= (int)$totalDokumentasiFoto ?> Foto)</span>
                            <button type="button" onclick="switchDetailTab('dokumentasi')" class="text-blue-600 hover:text-blue-800 text-[11px] font-semibold flex items-center">
                                <span>Lihat</span>
                                <ion-icon name="chevron-forward-outline"></ion-icon>
                            </button>
                        </div>
                    </div>

                    <!-- Shortcut Rekap Tab BAST -->
                    <div class="py-2 flex items-center justify-between">
                        <span class="text-slate-500">Berkas BAST Terbit</span>
                        <div class="flex items-center gap-2">
                            <span class="font-mono font-bold text-slate-800"><?= $bastP['done'] ?>/<?= $bastP['total'] ?> Berkas Terupload</span>
                            <button type="button" onclick="switchDetailTab('dokumen')" class="text-blue-600 hover:text-blue-800 text-[11px] font-semibold flex items-center">
                                <span>Lihat</span>
                                <ion-icon name="chevron-forward-outline"></ion-icon>
                            </button>
                        </div>
                    </div>

                    <!-- Status Pencairan & Tanggal -->
                    <div class="py-2 flex items-center justify-between">
                        <span class="text-slate-500">Status Pencairan</span>
                        <div class="flex items-center gap-2">
                            <button type="button" 
                                    onclick='openStatusPencairanModal(<?= json_encode([
                                        "id" => $paket["id"],
                                        "kode_paket" => $paket["kode_paket"],
                                        "nama_paket" => $paket["nama_paket"],
                                        "status" => $paket["status"] ?? "Proses"
                                    ], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)'
                                    class="inline-flex items-center gap-1 hover:scale-105 transition-transform cursor-pointer"
                                    title="Klik untuk ubah status pencairan">
                                <?= getStatusBadge($paket['status'] ?? 'Proses') ?>
                            </button>
                            <span class="text-slate-400 text-[11px]">&bull; <?= formatTanggal($paket['created_at']) ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- SCRIPT INIT CHART.JS -->
<script>
let chartDistInstance = null;
let chartFinInstance = null;

document.addEventListener('DOMContentLoaded', function() {
    initTabInfoCharts();
});

function initTabInfoCharts() {
    if (typeof Chart === 'undefined') return;

    // 1. Chart Donut Status Distribusi
    const ctxDist = document.getElementById('chartStatusDistribusi');
    if (ctxDist) {
        if (chartDistInstance) {
            chartDistInstance.destroy();
            chartDistInstance = null;
        }

        const dataDist = [
            <?= (int)$statusDistBreakdown['tiba_tujuan'] ?>,
            <?= (int)$statusDistBreakdown['tiba_gudang'] ?>,
            <?= (int)$statusDistBreakdown['tiba_kantor'] ?>,
            <?= (int)($statusDistBreakdown['belum_dipesan'] + $statusDistBreakdown['dalam_pengiriman']) ?>
        ];
        const isAllZero = dataDist.every(v => v === 0);

        chartDistInstance = new Chart(ctxDist, {
            type: 'doughnut',
            data: {
                labels: ['Tiba di Tujuan', 'Tiba di Gudang', 'Tiba di Rumah/Kantor', 'Belum Dipesan / OTW'],
                datasets: [{
                    data: isAllZero ? [0, 0, 0, 1] : dataDist,
                    backgroundColor: isAllZero ? ['#E2E8F0', '#E2E8F0', '#E2E8F0', '#CBD5E1'] : ['#10B981', '#F59E0B', '#3B82F6', '#CBD5E1'],
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
                        enabled: !isAllZero,
                        callbacks: {
                            label: function(c) {
                                return ' ' + c.label + ': ' + c.raw + ' titik';
                            }
                        }
                    }
                }
            }
        });
    }

    // 2. Chart Donut Finansial & Profit
    const ctxFin = document.getElementById('chartFinancial');
    if (ctxFin) {
        if (chartFinInstance) {
            chartFinInstance.destroy();
            chartFinInstance = null;
        }

        const estProfit = <?= (float)$estimasiProfit ?>;
        const totalBeli = <?= (float)$totalBeliBarang ?>;
        const totalPajak = <?= (float)$totalPasal ?>;

        chartFinInstance = new Chart(ctxFin, {
            type: 'doughnut',
            data: {
                labels: ['Est. Keuntungan Bersih', 'Modal Beli Barang', 'Beban Pajak & Potongan'],
                datasets: [{
                    data: [estProfit, totalBeli, totalPajak],
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
