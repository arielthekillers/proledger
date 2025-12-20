<!-- Header -->
<div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">Rekapitulasi Keuangan</h1>
        <p class="text-slate-500 mt-1">Ringkasan kesehatan finansial seluruh proyek</p>
    </div>
    <div class="flex gap-3">
        <a href="<?= APP_URL ?>/rekap/export/all" class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-xl transition-all shadow-lg shadow-emerald-600/30 hover:scale-[1.02] active:scale-[0.98]">
            <ion-icon name="download-outline" class="text-xl"></ion-icon>
            <span>Export Laporan</span>
        </a>
    </div>
</div>

<!-- Grand Total Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Total Modal -->
    <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm relative overflow-hidden group hover:shadow-md transition-all">
        <div class="absolute top-0 right-0 w-24 h-24 bg-blue-50 rounded-bl-full -mr-4 -mt-4 transition-transform group-hover:scale-110"></div>
        <div class="relative">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center text-xl">
                    <ion-icon name="wallet-outline"></ion-icon>
                </div>
                <span class="text-sm font-semibold text-slate-500">Total Modal Disetor</span>
            </div>
            <div class="text-2xl font-bold text-slate-800 mb-1"><?= formatRupiah($grand_total['total_modal']) ?></div>
            <div class="text-xs text-slate-500">Modal awal dari investor</div>
        </div>
    </div>

    <!-- Total Income -->
    <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm relative overflow-hidden group hover:shadow-md transition-all">
        <div class="absolute top-0 right-0 w-24 h-24 bg-emerald-50 rounded-bl-full -mr-4 -mt-4 transition-transform group-hover:scale-110"></div>
        <div class="relative">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center text-xl">
                    <ion-icon name="arrow-down-circle-outline"></ion-icon>
                </div>
                <span class="text-sm font-semibold text-slate-500">Total Pemasukan</span>
            </div>
            <div class="text-2xl font-bold text-slate-800 mb-1"><?= formatRupiah($grand_total['total_masuk']) ?></div>
            <div class="text-xs text-slate-500">Pendapatan seluruh proyek</div>
        </div>
    </div>

    <!-- Total Expense -->
    <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm relative overflow-hidden group hover:shadow-md transition-all">
        <div class="absolute top-0 right-0 w-24 h-24 bg-rose-50 rounded-bl-full -mr-4 -mt-4 transition-transform group-hover:scale-110"></div>
        <div class="relative">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-lg bg-rose-100 text-rose-600 flex items-center justify-center text-xl">
                    <ion-icon name="arrow-up-circle-outline"></ion-icon>
                </div>
                <span class="text-sm font-semibold text-slate-500">Total Pengeluaran</span>
            </div>
            <div class="text-2xl font-bold text-slate-800 mb-1"><?= formatRupiah($grand_total['total_keluar']) ?></div>
            <div class="text-xs text-slate-500">Biaya operasional & belanja</div>
        </div>
    </div>

    <!-- Profit/Loss -->
    <div class="bg-gradient-to-br from-slate-800 to-slate-900 rounded-2xl p-6 text-white shadow-lg relative overflow-hidden group hover:shadow-xl transition-all">
        <div class="absolute top-0 right-0 w-32 h-32 bg-white/5 rounded-full -mr-8 -mt-8 blur-2xl"></div>
        <div class="relative">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-lg bg-white/10 flex items-center justify-center text-xl backdrop-blur-sm">
                    <ion-icon name="trending-up-outline"></ion-icon>
                </div>
                <span class="text-sm font-semibold text-slate-300">Total Laba / Rugi</span>
            </div>
            <div class="text-2xl font-bold mb-1 <?= $grand_total['total_laba_rugi'] >= 0 ? 'text-emerald-400' : 'text-rose-400' ?>">
                <?= formatRupiah($grand_total['total_laba_rugi']) ?>
            </div>
            <div class="flex items-center gap-2 text-xs text-slate-400">
                <span>Net Profil Keseluruhan</span>
                <?php if ($grand_total['total_laba_rugi'] >= 0): ?>
                    <span class="px-1.5 py-0.5 rounded bg-emerald-500/20 text-emerald-300 font-bold">+Profit</span>
                <?php else: ?>
                    <span class="px-1.5 py-0.5 rounded bg-rose-500/20 text-rose-300 font-bold">-Loss</span>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="flex items-center gap-2 mb-6 overflow-x-auto pb-2">
    <a href="<?= APP_URL ?>/rekap" class="px-4 py-2 rounded-xl text-sm font-semibold transition-colors <?= !$filter_status ? 'bg-blue-600 text-white shadow-md shadow-blue-500/30' : 'bg-white text-slate-600 hover:bg-slate-50 border border-slate-200' ?>">
        Semua Paket
    </a>
    <a href="<?= APP_URL ?>/rekap?status=aktif" class="px-4 py-2 rounded-xl text-sm font-semibold transition-colors <?= $filter_status === 'aktif' ? 'bg-blue-600 text-white shadow-md shadow-blue-500/30' : 'bg-white text-slate-600 hover:bg-slate-50 border border-slate-200' ?>">
        Berjalan (Aktif)
    </a>
    <a href="<?= APP_URL ?>/rekap?status=selesai" class="px-4 py-2 rounded-xl text-sm font-semibold transition-colors <?= $filter_status === 'selesai' ? 'bg-blue-600 text-white shadow-md shadow-blue-500/30' : 'bg-white text-slate-600 hover:bg-slate-50 border border-slate-200' ?>">
        Selesai
    </a>
</div>

<!-- Main Table -->
<div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
    <?php if (empty($paket)): ?>
        <div class="p-12 text-center">
            <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4">
                <ion-icon name="document-text-outline" class="text-4xl text-slate-400"></ion-icon>
            </div>
            <h3 class="text-lg font-bold text-slate-800 mb-2">Tidak ada data paket</h3>
            <p class="text-slate-500">Belum ada paket kegiatan yang sesuai dengan filter ini.</p>
        </div>
    <?php else: ?>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/50 border-b border-slate-200">
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Paket Kegiatan</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-right">Modal</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-right">Masuk</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-right">Keluar</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-right">Saldo</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-right">Laba/Rugi</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">Status</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php foreach ($paket as $p): ?>
                        <tr class="hover:bg-slate-50/50 transition-colors group">
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-800 group-hover:text-blue-600 transition-colors"><?= htmlspecialchars($p['nama_paket']) ?></div>
                                <div class="text-xs text-slate-500 mt-1"><?= $p['jumlah_pemodal'] ?> Pemodal</div>
                            </td>
                            <td class="px-6 py-4 text-right font-mono text-sm text-slate-600">
                                <?= formatRupiah($p['total_modal']) ?>
                            </td>
                            <td class="px-6 py-4 text-right font-mono text-sm text-emerald-600">
                                <?= formatRupiah($p['total_masuk']) ?>
                            </td>
                            <td class="px-6 py-4 text-right font-mono text-sm text-rose-600">
                                <?= formatRupiah($p['total_keluar']) ?>
                            </td>
                            <td class="px-6 py-4 text-right font-mono text-sm font-bold text-slate-700">
                                <?= formatRupiah($p['saldo']) ?>
                            </td>
                            <td class="px-6 py-4 text-right font-mono text-sm font-bold <?= $p['laba_rugi'] >= 0 ? 'text-emerald-600' : 'text-rose-600' ?>">
                                <?= formatRupiah($p['laba_rugi']) ?>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <?= getStatusBadge($p['status']) ?>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <a href="<?= APP_URL ?>/rekap/paket/<?= $p['id'] ?>" class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-white border border-slate-200 text-slate-400 hover:text-blue-600 hover:border-blue-200 transition-colors" title="Lihat Detail Rekap">
                                    <ion-icon name="stats-chart-outline"></ion-icon>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
