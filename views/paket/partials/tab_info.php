<!-- ========================================== -->
<!-- 3. TAB 1: INFORMASI PAKET                  -->
<!-- ========================================== -->
<div id="tabContent_info" class="space-y-6">
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 md:p-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-12">
            <!-- Left Column: Financial & Tax Calculation -->
            <div>
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider flex items-center gap-2">
                        <ion-icon name="calculator-outline" class="text-base text-blue-600"></ion-icon>
                        <span>Rincian Anggaran & Nilai Kontrak</span>
                    </h3>
                    <span class="text-[11px] text-slate-400 font-mono">Kode: #<?= htmlspecialchars($paket['kode_paket'] ?: $paket['id']) ?></span>
                </div>
                
                <!-- Pagu & Nego Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/80">
                        <div class="text-xs text-slate-500 mb-1">Nilai Pagu Awal</div>
                        <div class="font-bold text-slate-800 text-lg font-mono"><?= formatRupiah($paket['pagu']) ?></div>
                    </div>
                    <div class="p-4 rounded-xl <?= $hasNego ? 'bg-emerald-50/70 border-emerald-200' : 'bg-slate-50 border-slate-200/80' ?>">
                        <div class="text-xs <?= $hasNego ? 'text-emerald-700 font-bold' : 'text-slate-500' ?> mb-1">
                            Pagu Akhir (Hasil Nego) <?= $hasNego ? '<span class="text-[10px] bg-emerald-100 text-emerald-800 px-1.5 py-0.5 rounded ml-1 font-semibold">Acuan Aktif</span>' : '' ?>
                        </div>
                        <div class="font-bold <?= $hasNego ? 'text-emerald-700' : 'text-slate-400' ?> text-lg font-mono">
                            <?= $hasNego ? formatRupiah($paket['pagu_nego']) : 'Tidak ada negosiasi' ?>
                        </div>
                    </div>
                </div>

                <!-- Breakdown Perpajakan & Pasal Grid -->
                <div class="p-5 rounded-2xl bg-gradient-to-br from-slate-50 to-blue-50/30 border border-slate-200/80 space-y-3.5">
                    <div class="flex items-center justify-between text-xs pb-2 border-b border-slate-200/60">
                        <span class="text-slate-600 font-medium">Nilai Acuan Kontrak:</span>
                        <span class="font-bold text-slate-800 font-mono text-sm"><?= formatRupiah($acuan) ?></span>
                    </div>
                    <div class="flex items-center justify-between text-xs pb-2 border-b border-slate-200/60">
                        <div>
                            <span class="text-slate-600 font-medium">DPP (Dasar Pengenaan Pajak)</span>
                            <span class="text-[10px] text-slate-400 block">Setelah dikurangi PPN 11% (Nilai / 1.11)</span>
                        </div>
                        <span class="font-bold text-slate-800 font-mono text-sm"><?= formatRupiah($dpp) ?></span>
                    </div>
                    <div class="grid grid-cols-2 gap-3 pt-1">
                        <div class="p-3 rounded-xl bg-white border border-slate-100 shadow-2xs">
                            <div class="text-[11px] text-slate-500 font-medium">Potongan Pasal (<?= (float)$persenPasal ?>%)</div>
                            <div class="font-bold text-slate-800 font-mono text-sm mt-0.5"><?= formatRupiah($pasalVal) ?></div>
                        </div>
                        <div class="p-3 rounded-xl bg-white border border-slate-100 shadow-2xs">
                            <div class="text-[11px] text-slate-500 font-medium">Admin (<?= (float)$persenAdmin ?>%)</div>
                            <div class="font-bold text-slate-800 font-mono text-sm mt-0.5">
                                <?= ($persenAdmin > 0) ? formatRupiah($adminVal) : '<span class="text-slate-400">-</span>' ?>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center justify-between text-xs pt-3 border-t border-slate-200/60">
                        <span class="text-amber-900 font-bold">Total Pasal (<?= (float)$totalPersen ?>% dari DPP):</span>
                        <span class="font-extrabold text-amber-700 font-mono text-lg"><?= formatRupiah($totalPasal) ?></span>
                    </div>
                </div>
            </div>

            <!-- Right Column: Project & Execution Metadata -->
            <div class="space-y-6">
                <div>
                    <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4 flex items-center gap-2">
                        <ion-icon name="information-circle-outline" class="text-base text-blue-600"></ion-icon>
                        <span>Informasi Pelaksana & Status</span>
                    </h3>

                    <!-- Perusahaan Card -->
                    <?php if(!empty($paket['perusahaan'])): ?>
                        <a href="<?= APP_URL ?>/perusahaan/detail/<?= $paket['perusahaan_id'] ?>" class="block p-4 rounded-xl bg-slate-50 border border-slate-200/80 hover:bg-blue-50 hover:border-blue-200 transition-all group mb-4">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-lg bg-blue-600 text-white flex items-center justify-center font-bold text-lg shadow-md shadow-blue-600/20">
                                    <?= strtoupper(substr($paket['perusahaan'], 0, 2)) ?>
                                </div>
                                <div>
                                    <div class="font-bold text-slate-800 group-hover:text-blue-700 transition-colors"><?= htmlspecialchars($paket['perusahaan']) ?></div>
                                    <div class="text-xs text-slate-500 group-hover:text-blue-500 flex items-center gap-1">
                                        <span>Lihat Profil Perusahaan</span>
                                        <ion-icon name="arrow-forward"></ion-icon>
                                    </div>
                                </div>
                            </div>
                        </a>
                    <?php endif; ?>

                    <!-- Status, Progres, PJ & Tanggal Grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 p-5 rounded-2xl bg-slate-50 border border-slate-200/80">
                        <div>
                            <div class="text-xs font-semibold text-slate-500 uppercase mb-1.5">Penanggung Jawab</div>
                            <div><?= getPenanggungJawabBadge($paket['penanggung_jawab'] ?? '') ?></div>
                        </div>
                        <div>
                            <div class="text-xs font-semibold text-slate-500 uppercase mb-1.5">Progres Barang</div>
                            <div><?= getProgresBarangBadge($paket['progres_barang'] ?? 'Belum') ?></div>
                        </div>
                        <div>
                            <div class="text-xs font-semibold text-slate-500 uppercase mb-1.5">Status Kas</div>
                            <div><?= getStatusBadge($paket['status'] ?? 'Proses') ?></div>
                        </div>
                        <div>
                            <div class="text-xs font-semibold text-slate-500 uppercase mb-1.5">Tanggal Dibuat</div>
                            <div class="font-medium text-slate-800 text-xs mt-1"><?= formatTanggal($paket['created_at']) ?></div>
                        </div>
                    </div>
                </div>

                <!-- Group Paket Info -->
                <div class="p-4 rounded-xl bg-indigo-50/50 border border-indigo-100 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-lg bg-indigo-100 text-indigo-700 flex items-center justify-center text-lg">
                            <ion-icon name="folder-open-outline"></ion-icon>
                        </div>
                        <div>
                            <div class="text-[11px] font-bold text-indigo-500 uppercase">Group Paket</div>
                            <div class="text-sm font-bold text-slate-800">
                                <?= !empty($paket['nama_group']) ? htmlspecialchars($paket['nama_group']) : 'Paket Mandiri (Tanpa Group)' ?>
                            </div>
                        </div>
                    </div>
                    <?php if (!empty($paket['group_id'])): ?>
                        <a href="<?= APP_URL ?>/group/detail/<?= $paket['group_id'] ?>" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 flex items-center gap-1">
                            <span>Buka Group</span>
                            <ion-icon name="arrow-forward"></ion-icon>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Ringkasan Item & Distribusi Pengadaan (Summary Terpusat) -->
    <?php
    $totalReqQty = (float)($itemSummary['total_qty'] ?? 0);
    $totalDistQty = (float)($distribusiSummary['total_qty_dialokasikan'] ?? 0);
    $isComplete = ($totalDistQty >= $totalReqQty && $totalReqQty > 0);
    $pctDist = ($totalReqQty > 0) ? min(100, round(($totalDistQty / $totalReqQty) * 100)) : 100;
    ?>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Ringkasan Item & Komponen -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-4">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-lg shadow-2xs">
                        <ion-icon name="list-outline"></ion-icon>
                    </div>
                    <div>
                        <h4 class="font-bold text-slate-800 text-sm">Ringkasan Item & Spesifikasi</h4>
                        <p class="text-[11px] text-slate-400">Total volume dan nilai realisasi barang</p>
                    </div>
                </div>
                <button type="button" onclick="switchDetailTab('items')" class="text-xs font-semibold text-blue-600 hover:text-blue-800 flex items-center gap-1 transition-colors">
                    <span>Ke Tab Item</span>
                    <ion-icon name="arrow-forward"></ion-icon>
                </button>
            </div>

            <div class="grid grid-cols-3 gap-3">
                <div class="p-3 bg-slate-50 rounded-xl border border-slate-200/70">
                    <span class="block text-[10px] font-semibold text-slate-500 uppercase tracking-wider">Total Item</span>
                    <span class="font-bold text-slate-800 text-base mt-0.5 block"><?= (int)$itemSummary['total_item'] ?> <span class="text-[11px] font-normal text-slate-400">Jenis</span></span>
                </div>
                <div class="p-3 bg-slate-50 rounded-xl border border-slate-200/70">
                    <span class="block text-[10px] font-semibold text-slate-500 uppercase tracking-wider">Total Kuantitas</span>
                    <span class="font-bold text-slate-800 text-base mt-0.5 block"><?= (float)$itemSummary['total_qty'] ?> <span class="text-[11px] font-normal text-slate-400">Unit</span></span>
                </div>
                <div class="p-3 bg-blue-50/80 rounded-xl border border-blue-200/70">
                    <span class="block text-[10px] font-bold text-blue-700 uppercase tracking-wider">Total Nilai Item</span>
                    <span class="font-extrabold text-blue-700 font-mono text-xs sm:text-sm mt-0.5 block truncate" title="<?= formatRupiah($itemSummary['grand_total']) ?>">
                        <?= formatRupiah($itemSummary['grand_total']) ?>
                    </span>
                </div>
            </div>
        </div>

        <!-- Ringkasan Alokasi & Distribusi -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-4">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-lg shadow-2xs">
                        <ion-icon name="navigate-outline"></ion-icon>
                    </div>
                    <div>
                        <h4 class="font-bold text-slate-800 text-sm">Ringkasan Alokasi Distribusi</h4>
                        <p class="text-[11px] text-slate-400">Penyaluran ke lokasi penerima</p>
                    </div>
                </div>
                <button type="button" onclick="switchDetailTab('distribusi')" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 flex items-center gap-1 transition-colors">
                    <span>Ke Tab Distribusi</span>
                    <ion-icon name="arrow-forward"></ion-icon>
                </button>
            </div>

            <div class="grid grid-cols-3 gap-3">
                <div class="p-3 bg-slate-50 rounded-xl border border-slate-200/70">
                    <span class="block text-[10px] font-semibold text-slate-500 uppercase tracking-wider">Titik Penerima</span>
                    <span class="font-bold text-slate-800 text-base mt-0.5 block"><?= (int)($distribusiSummary['total_tujuan'] ?? 0) ?> <span class="text-[11px] font-normal text-slate-400">Lokasi</span></span>
                </div>
                <div class="p-3 bg-slate-50 rounded-xl border border-slate-200/70">
                    <span class="block text-[10px] font-semibold text-slate-500 uppercase tracking-wider">Teralokasi</span>
                    <span class="font-bold text-slate-800 text-base mt-0.5 block">
                        <?= (float)($distribusiSummary['total_qty_dialokasikan'] ?? 0) ?><span class="text-[11px] font-normal text-slate-400">/<?= (float)$itemSummary['total_qty'] ?></span>
                    </span>
                </div>
                <div class="p-3 <?= $isComplete ? 'bg-emerald-50/80 border-emerald-200/70' : 'bg-amber-50/80 border-amber-200/70' ?> rounded-xl border">
                    <span class="block text-[10px] font-bold <?= $isComplete ? 'text-emerald-700' : 'text-amber-700' ?> uppercase tracking-wider">Status Alokasi</span>
                    <div class="flex items-center gap-1 mt-1">
                        <span class="px-1.5 py-0.5 rounded text-[10px] font-bold <?= $isComplete ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' ?>">
                            <?= $pctDist ?>% <?= $isComplete ? 'Lengkap' : 'Sebagian' ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
