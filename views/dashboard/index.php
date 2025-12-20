<!-- Welcome Header -->
<div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">Dashboard Overview</h1>
        <p class="text-slate-500 mt-1">Selamat datang kembali! Berikut ringkasan aktivitas proyek Anda.</p>
    </div>
    <div class="flex gap-3">
        <!-- Buttons removed as they are redundant with Aksi Cepat -->
    </div>
</div>

<!-- Key Metrics Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 mb-8">
    <!-- Active Projects -->
    <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl p-6 text-white shadow-lg relative overflow-hidden group hover:shadow-xl transition-all">
        <div class="absolute top-0 right-0 w-24 h-24 bg-white/10 rounded-bl-full -mr-4 -mt-4 transition-transform group-hover:scale-110"></div>
        <div class="relative">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-xl bg-white/20 flex items-center justify-center text-2xl backdrop-blur-sm">
                    <ion-icon name="cube-outline"></ion-icon>
                </div>
                <span class="bg-blue-400/30 text-blue-50 text-xs font-bold px-2 py-1 rounded-lg border border-blue-400/30">
                    <?= $stats['paket_aktif'] ?> Aktif
                </span>
            </div>
            <div class="text-3xl font-bold mb-1"><?= $stats['total_paket'] ?></div>
            <div class="text-blue-100 text-sm font-medium">Total Paket Kegiatan</div>
        </div>
    </div>

    <!-- Total Investors -->
    <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl p-6 text-white shadow-lg relative overflow-hidden group hover:shadow-xl transition-all">
        <div class="absolute top-0 right-0 w-24 h-24 bg-white/10 rounded-bl-full -mr-4 -mt-4 transition-transform group-hover:scale-110"></div>
        <div class="relative">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-xl bg-white/20 flex items-center justify-center text-2xl backdrop-blur-sm">
                    <ion-icon name="people-outline"></ion-icon>
                </div>
            </div>
            <div class="text-3xl font-bold mb-1"><?= $stats['total_pemodal'] ?></div>
            <div class="text-purple-100 text-sm font-medium">Investor Terdaftar</div>
        </div>
    </div>

    <!-- Total Capital -->
    <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm relative overflow-hidden group hover:shadow-md transition-all">
        <div class="absolute top-0 right-0 w-24 h-24 bg-emerald-50 rounded-bl-full -mr-4 -mt-4 transition-transform group-hover:scale-110"></div>
        <div class="relative">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center text-xl">
                    <ion-icon name="wallet-outline"></ion-icon>
                </div>
                <span class="text-sm font-semibold text-slate-500">Total Modal Disetor</span>
            </div>
            <div class="text-2xl font-bold text-slate-800 mb-1"><?= formatRupiah($stats['total_modal']) ?></div>
            <div class="flex items-center gap-1 text-xs text-emerald-600 font-semibold">
                <ion-icon name="trending-up"></ion-icon>
                <span>Financial Backbone</span>
            </div>
        </div>
    </div>

    <!-- Net Profit -->
    <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm relative overflow-hidden group hover:shadow-md transition-all">
        <div class="absolute top-0 right-0 w-24 h-24 bg-<?= $stats['total_laba_rugi'] >= 0 ? 'emerald' : 'rose' ?>-50 rounded-bl-full -mr-4 -mt-4 transition-transform group-hover:scale-110"></div>
        <div class="relative">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-lg bg-<?= $stats['total_laba_rugi'] >= 0 ? 'emerald' : 'rose' ?>-100 text-<?= $stats['total_laba_rugi'] >= 0 ? 'emerald' : 'rose' ?>-600 flex items-center justify-center text-xl">
                    <ion-icon name="<?= $stats['total_laba_rugi'] >= 0 ? 'trophy-outline' : 'warning-outline' ?>"></ion-icon>
                </div>
                <span class="text-sm font-semibold text-slate-500">Net Profit / Loss</span>
            </div>
            <div class="text-2xl font-bold <?= $stats['total_laba_rugi'] >= 0 ? 'text-emerald-600' : 'text-rose-600' ?> mb-1">
                <?= formatRupiah($stats['total_laba_rugi']) ?>
            </div>
            <div class="flex items-center gap-1 text-xs text-slate-400">
                <span>Akumulasi Laba Rugi</span>
            </div>
        </div>
    </div>
</div>

<!-- Cash Flow Summary -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    <!-- Income -->
    <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm flex items-center justify-between group hover:border-emerald-200 transition-colors">
        <div>
            <div class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Total Pemasukan</div>
            <div class="text-xl font-bold text-emerald-600"><?= formatRupiah($stats['total_masuk']) ?></div>
        </div>
        <div class="w-12 h-12 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">
            <ion-icon name="arrow-down-circle"></ion-icon>
        </div>
    </div>
    
    <!-- Expense -->
    <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm flex items-center justify-between group hover:border-rose-200 transition-colors">
        <div>
            <div class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Total Pengeluaran</div>
            <div class="text-xl font-bold text-rose-600"><?= formatRupiah($stats['total_keluar']) ?></div>
        </div>
        <div class="w-12 h-12 rounded-full bg-rose-50 text-rose-600 flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">
            <ion-icon name="arrow-up-circle"></ion-icon>
        </div>
    </div>
    
    <!-- Balance -->
    <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm flex items-center justify-between group hover:border-blue-200 transition-colors">
        <div>
            <div class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Saldo Kas</div>
            <div class="text-xl font-bold text-blue-600"><?= formatRupiah($stats['total_saldo']) ?></div>
        </div>
        <div class="w-12 h-12 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">
            <ion-icon name="briefcase"></ion-icon>
        </div>
    </div>
</div>

<!-- Recent Activity & Quick Actions -->
<div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
    <!-- Main Table (2 Cols) -->
    <div class="xl:col-span-2 space-y-6">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                <h3 class="font-bold text-slate-800 flex items-center gap-2">
                    <ion-icon name="time-outline" class="text-blue-500"></ion-icon>
                    Aktivitas Terbaru
                </h3>
                <a href="<?= APP_URL ?>/paket" class="text-sm font-semibold text-blue-600 hover:text-blue-700 hover:underline">Lihat Semua</a>
            </div>
            
            <?php if (empty($recent_paket)): ?>
                <div class="p-12 text-center">
                    <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4">
                        <ion-icon name="cube-outline" class="text-3xl text-slate-400"></ion-icon>
                    </div>
                    <p class="text-slate-500 font-medium">Belum ada paket kegiatan</p>
                </div>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-slate-50/50 text-xs uppercase text-slate-500 font-bold border-b border-slate-100">
                            <tr>
                                <th class="px-6 py-4">Paket Kegiatan</th>
                                <th class="px-6 py-4 text-right">Nilai Pagu</th>
                                <th class="px-6 py-4 text-center">Status</th>
                                <th class="px-6 py-4"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <?php foreach ($recent_paket as $paket): ?>
                                <tr class="hover:bg-slate-50/50 transition-colors group">
                                    <td class="px-6 py-4">
                                        <div class="font-semibold text-slate-800 group-hover:text-blue-600 transition-colors"><?= htmlspecialchars($paket['nama_paket']) ?></div>
                                        <div class="text-xs text-slate-500 mt-1 flex items-center gap-1">
                                            <ion-icon name="people-outline"></ion-icon>
                                            <?= $paket['jumlah_pemodal'] ?> Investor
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-right font-mono text-sm text-slate-700">
                                        <?= formatRupiah($paket['pagu']) ?>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <?= getStatusBadge($paket['status']) ?>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <a href="<?= APP_URL ?>/paket/detail/<?= $paket['id'] ?>" class="text-slate-400 hover:text-blue-600 transition-colors text-xl">
                                            <ion-icon name="arrow-forward-circle-outline"></ion-icon>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Sidebar Shortcuts (1 Col) -->
    <div class="space-y-6">
        <!-- Quick Actions Card -->
        <div class="bg-gradient-to-br from-slate-800 to-slate-900 rounded-2xl p-6 text-white shadow-lg">
            <h3 class="font-bold text-lg mb-4 flex items-center gap-2">
                <ion-icon name="flash-outline" class="text-yellow-400"></ion-icon>
                Aksi Cepat
            </h3>
            <div class="grid grid-cols-2 gap-3">
                <?php if(($_SESSION['admin_role'] ?? 'admin') === 'admin'): ?>
                    <a href="<?= APP_URL ?>/paket/create" class="flex flex-col items-center justify-center p-4 bg-white/10 hover:bg-white/20 rounded-xl transition-all border border-white/5 group">
                        <ion-icon name="add-circle" class="text-2xl mb-2 text-emerald-400 group-hover:scale-110 transition-transform"></ion-icon>
                        <span class="text-xs font-semibold">Paket Baru</span>
                    </a>
                    <a href="<?= APP_URL ?>/pemodal/create" class="flex flex-col items-center justify-center p-4 bg-white/10 hover:bg-white/20 rounded-xl transition-all border border-white/5 group">
                        <ion-icon name="person-add" class="text-2xl mb-2 text-blue-400 group-hover:scale-110 transition-transform"></ion-icon>
                        <span class="text-xs font-semibold">Add Investor</span>
                    </a>
                    <a href="<?= APP_URL ?>/transaksi/create" class="flex flex-col items-center justify-center p-4 bg-white/10 hover:bg-white/20 rounded-xl transition-all border border-white/5 group">
                        <ion-icon name="receipt" class="text-2xl mb-2 text-purple-400 group-hover:scale-110 transition-transform"></ion-icon>
                        <span class="text-xs font-semibold">Add Transaksi</span>
                    </a>
                    <a href="<?= APP_URL ?>/rekap" class="flex flex-col items-center justify-center p-4 bg-white/10 hover:bg-white/20 rounded-xl transition-all border border-white/5 group">
                        <ion-icon name="pie-chart" class="text-2xl mb-2 text-rose-400 group-hover:scale-110 transition-transform"></ion-icon>
                        <span class="text-xs font-semibold">Laporan</span>
                    </a>
                <?php else: ?>
                    <div class="col-span-2 text-center text-sm text-slate-400 py-4 italic">
                        Menu cepat hanya untuk Admin.
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- System Info -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
            <h3 class="font-bold text-slate-800 mb-4 text-sm uppercase tracking-wide">System Status</h3>
            <div class="space-y-4">
                <div class="flex items-center justify-between text-sm">
                    <span class="text-slate-500">Server Time</span>
                    <span class="font-mono text-slate-700"><?= date('H:i:s') ?></span>
                </div>
                <div class="flex items-center justify-between text-sm">
                    <span class="text-slate-500">Mode</span>
                    <span class="bg-emerald-100 text-emerald-700 text-xs px-2 py-0.5 rounded font-bold">Production</span>
                </div>
                <div class="flex items-center justify-between text-sm">
                    <span class="text-slate-500">Version</span>
                    <span class="text-slate-700 font-semibold">v1.2.0</span>
                </div>
            </div>
        </div>
    </div>
</div>
