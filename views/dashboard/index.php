<!-- Compact Welcome Header & Company Indicator (Single Active Company) -->
<div class="mb-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
    <div>
        <div class="flex items-center gap-2">
            <h1 class="text-lg md:text-xl font-bold text-slate-800">Dashboard Overview</h1>
            <?php if (!empty($activeCompany)): ?>
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                    <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                    <span><?= htmlspecialchars($activeCompany['nama']) ?></span>
                </span>
            <?php else: ?>
                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                    Belum Ada Perusahaan
                </span>
            <?php endif; ?>
        </div>
        <p class="text-xs text-slate-500 mt-0.5">Ringkasan aktivitas dan nilai paket kegiatan untuk perusahaan aktif.</p>
    </div>

    <!-- Quick Company Switcher Pills -->
    <?php if (!empty($userCompanies) && count($userCompanies) > 1): ?>
        <div class="flex items-center gap-1.5 overflow-x-auto pb-1 sm:pb-0">
            <span class="text-[11px] font-semibold text-slate-400 mr-1 hidden md:inline">Switch Perusahaan:</span>
            <?php foreach ($userCompanies as $uc): ?>
                <a href="<?= APP_URL ?>/perusahaan/switch/<?= $uc['id'] ?>" 
                   class="px-2.5 py-1 rounded-lg text-xs font-semibold transition-all whitespace-nowrap <?= (($activeCompanyId ?? null) == $uc['id']) ? 'bg-blue-600 text-white shadow-xs' : 'bg-white text-slate-600 hover:bg-slate-50 border border-slate-200' ?>">
                    <?= htmlspecialchars($uc['nama']) ?>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<!-- Banner if user has 0 companies -->
<?php if (empty($userCompanies)): ?>
    <div class="mb-4 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-xl p-4 text-white shadow-sm flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-lg bg-white/20 flex items-center justify-center text-2xl shrink-0">
                <ion-icon name="business"></ion-icon>
            </div>
            <div>
                <h4 class="font-bold text-sm">Mulai dengan Menambahkan Perusahaan Anda</h4>
                <p class="text-xs text-blue-100">Daftarkan perusahaan Anda terlebih dahulu untuk mulai mengelola paket kegiatan dan group.</p>
            </div>
        </div>
        <a href="<?= APP_URL ?>/perusahaan?action=create" class="px-4 py-2 bg-white text-blue-700 hover:bg-blue-50 font-bold rounded-lg text-xs shadow transition-all whitespace-nowrap shrink-0">
            + Tambah Perusahaan
        </a>
    </div>
<?php endif; ?>

<!-- Compact Key Metrics Grid -->
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-3.5 mb-4">
    <!-- Total Paket -->
    <div class="bg-gradient-to-br from-blue-600 to-blue-700 rounded-xl p-4 text-white shadow-sm relative overflow-hidden group hover:shadow-md transition-all">
        <div class="absolute -right-2 -bottom-2 w-16 h-16 bg-white/10 rounded-full transition-transform group-hover:scale-125"></div>
        <div class="relative">
            <div class="flex items-center justify-between mb-2">
                <div class="w-8 h-8 rounded-lg bg-white/20 flex items-center justify-center text-lg backdrop-blur-xs">
                    <ion-icon name="cube-outline"></ion-icon>
                </div>
                <span class="bg-white/20 text-white text-[11px] font-bold px-2 py-0.5 rounded-full">
                    <?= $stats['paket_aktif'] ?> Aktif
                </span>
            </div>
            <div class="text-2xl font-bold leading-tight"><?= $stats['total_paket'] ?></div>
            <div class="text-blue-100 text-xs font-medium mt-0.5">Total Paket Kegiatan</div>
        </div>
    </div>

    <!-- Total Nilai Pagu Acuan -->
    <div class="bg-white rounded-xl p-4 border border-slate-200/80 shadow-xs relative overflow-hidden group hover:shadow-sm transition-all">
        <div class="flex items-center gap-2.5 mb-2">
            <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg shrink-0">
                <ion-icon name="cash-outline"></ion-icon>
            </div>
            <span class="text-xs font-semibold text-slate-500">Total Nilai Pagu / Kontrak</span>
        </div>
        <div class="text-xl font-bold text-slate-800 leading-tight"><?= formatRupiah($stats['total_pagu']) ?></div>
        <div class="flex items-center gap-1 text-[11px] text-emerald-600 font-medium mt-0.5">
            <ion-icon name="checkmark-circle-outline" class="text-xs"></ion-icon>
            <span>Acuan Perusahaan Aktif</span>
        </div>
    </div>

    <!-- Total Potongan Pasal -->
    <div class="bg-white rounded-xl p-4 border border-slate-200/80 shadow-xs relative overflow-hidden group hover:shadow-sm transition-all">
        <div class="flex items-center gap-2.5 mb-2">
            <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center text-lg shrink-0">
                <ion-icon name="calculator-outline"></ion-icon>
            </div>
            <span class="text-xs font-semibold text-slate-500">Total Potongan Pasal</span>
        </div>
        <div class="text-xl font-bold text-amber-700 leading-tight">
            <?= formatRupiah($stats['total_pasal']) ?>
        </div>
        <div class="text-[11px] text-slate-400 mt-0.5">
            <span>Pasal + Biaya Admin</span>
        </div>
    </div>

    <!-- Perusahaan Anda -->
    <div class="bg-gradient-to-br from-indigo-600 to-purple-600 rounded-xl p-4 text-white shadow-sm relative overflow-hidden group hover:shadow-md transition-all">
        <div class="absolute -right-2 -bottom-2 w-16 h-16 bg-white/10 rounded-full transition-transform group-hover:scale-125"></div>
        <div class="relative">
            <div class="flex items-center justify-between mb-2">
                <div class="w-8 h-8 rounded-lg bg-white/20 flex items-center justify-center text-lg backdrop-blur-xs">
                    <ion-icon name="business-outline"></ion-icon>
                </div>
            </div>
            <div class="text-2xl font-bold leading-tight"><?= $stats['total_perusahaan'] ?></div>
            <div class="text-purple-100 text-xs font-medium mt-0.5">Perusahaan Terdaftar</div>
        </div>
    </div>
</div>

<!-- Recent Activity & Quick Actions -->
<div class="grid grid-cols-1 xl:grid-cols-3 gap-3.5">
    <!-- Main Table (2 Cols) -->
    <div class="xl:col-span-2">
        <div class="bg-white rounded-xl shadow-xs border border-slate-200/80 overflow-hidden">
            <div class="p-3.5 px-5 border-b border-slate-100 flex items-center justify-between">
                <h3 class="font-bold text-slate-800 text-sm flex items-center gap-2">
                    <ion-icon name="time-outline" class="text-blue-500 text-base"></ion-icon>
                    Paket Terbaru <?= !empty($activeCompany) ? '(' . htmlspecialchars($activeCompany['nama']) . ')' : '' ?>
                </h3>
                <a href="<?= APP_URL ?>/paket" class="text-xs font-semibold text-blue-600 hover:text-blue-700 hover:underline">Lihat Semua</a>
            </div>
            
            <?php if (empty($recent_paket)): ?>
                <div class="p-8 text-center">
                    <div class="w-12 h-12 bg-slate-50 text-slate-400 rounded-xl flex items-center justify-center mx-auto mb-2 text-2xl">
                        <ion-icon name="cube-outline"></ion-icon>
                    </div>
                    <p class="text-xs text-slate-500 font-medium">Belum ada paket kegiatan pada perusahaan ini</p>
                    <a href="<?= APP_URL ?>/paket" class="mt-3 inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-600 text-white rounded-lg text-xs font-semibold hover:bg-blue-700 transition-colors">
                        <ion-icon name="add-circle-outline"></ion-icon>
                        Tambah Paket Sekarang
                    </a>
                </div>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50/75 uppercase text-slate-500 font-bold border-b border-slate-100">
                            <tr>
                                <th class="px-5 py-3">Paket Kegiatan</th>
                                <th class="px-5 py-3 text-right">Nilai Acuan</th>
                                <th class="px-5 py-3 text-right">Total Pasal</th>
                                <th class="px-5 py-3 text-center">Status</th>
                                <th class="px-5 py-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <?php foreach ($recent_paket as $paket): 
                                $hasNego = (!empty($paket['pagu_nego']) && (float)$paket['pagu_nego'] > 0);
                                $acuan = $hasNego ? (float)$paket['pagu_nego'] : (float)$paket['pagu'];
                            ?>
                                <tr class="hover:bg-slate-50/50 transition-colors group">
                                    <td class="px-5 py-3">
                                        <div class="flex items-center gap-1.5 flex-wrap">
                                            <span class="px-1.5 py-0.2 rounded bg-slate-100 font-mono font-bold text-[10px] text-slate-600">
                                                <?= htmlspecialchars($paket['kode_paket'] ?: '#' . $paket['id']) ?>
                                            </span>
                                            <a href="<?= APP_URL ?>/paket/detail/<?= $paket['kode_paket'] ?: $paket['id'] ?>" class="font-bold text-slate-800 hover:text-blue-600 transition-colors">
                                                <?= htmlspecialchars($paket['nama_paket']) ?>
                                            </a>
                                        </div>
                                        <?php if (!empty($paket['nama_group'])): ?>
                                            <div class="text-[10px] text-indigo-600 font-medium mt-0.5 flex items-center gap-1">
                                                <ion-icon name="folder-outline"></ion-icon>
                                                <?= htmlspecialchars($paket['nama_group']) ?>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-5 py-3 text-right font-mono font-medium text-slate-700">
                                        <?= formatRupiah($acuan) ?>
                                        <?php if ($hasNego): ?>
                                            <span class="block text-[10px] text-emerald-600 font-bold">Nego</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-5 py-3 text-right font-mono font-bold text-amber-700">
                                        <?= !empty($paket['total_pasal']) ? formatRupiah($paket['total_pasal']) : '-' ?>
                                    </td>
                                    <td class="px-5 py-3 text-center">
                                        <?= getStatusBadge($paket['status']) ?>
                                    </td>
                                    <td class="px-5 py-3 text-right">
                                        <a href="<?= APP_URL ?>/paket/detail/<?= $paket['kode_paket'] ?: $paket['id'] ?>" class="text-slate-400 hover:text-blue-600 transition-colors text-lg inline-block" title="Lihat Detail">
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
    <div class="space-y-3.5">
        <!-- Quick Actions Card -->
        <div class="bg-slate-900 rounded-xl p-4 text-white shadow-sm">
            <h3 class="font-bold text-sm mb-3 flex items-center gap-2">
                <ion-icon name="flash-outline" class="text-amber-400 text-base"></ion-icon>
                Aksi Cepat
            </h3>
            
            <div class="space-y-2">
                <a href="<?= APP_URL ?>/paket" class="flex items-center gap-2.5 p-2.5 bg-blue-600 hover:bg-blue-700 rounded-lg transition-all text-xs font-semibold text-white shadow-xs">
                    <ion-icon name="cube-outline" class="text-base"></ion-icon>
                    <span>Kelola Paket Kegiatan</span>
                </a>
                <a href="<?= APP_URL ?>/group" class="flex items-center gap-2.5 p-2.5 bg-white/10 hover:bg-white/20 rounded-lg transition-all text-xs font-semibold">
                    <ion-icon name="folder-outline" class="text-indigo-400 text-base"></ion-icon>
                    <span>Kelola Group Paket</span>
                </a>
                <a href="<?= APP_URL ?>/perusahaan" class="flex items-center gap-2.5 p-2.5 bg-white/10 hover:bg-white/20 rounded-lg transition-all text-xs font-semibold">
                    <ion-icon name="business-outline" class="text-amber-400 text-base"></ion-icon>
                    <span>Kelola Perusahaan</span>
                </a>
            </div>
        </div>
        
        <!-- System Info -->
        <div class="bg-white rounded-xl shadow-xs border border-slate-200/80 p-4">
            <h3 class="font-bold text-slate-800 mb-3 text-xs uppercase tracking-wider flex items-center justify-between">
                <span>Info Perusahaan Aktif</span>
                <span class="bg-emerald-50 text-emerald-700 text-[10px] px-1.5 py-0.5 rounded-full font-bold">Online</span>
            </h3>
            <div class="space-y-2 text-xs">
                <div class="flex items-center justify-between py-1 border-b border-slate-100">
                    <span class="text-slate-400">Nama Perusahaan</span>
                    <span class="font-semibold text-blue-600 truncate max-w-[140px]">
                        <?= htmlspecialchars($_SESSION['active_perusahaan_nama'] ?? 'Belum Dipilih') ?>
                    </span>
                </div>
                <div class="flex items-center justify-between py-1 border-b border-slate-100">
                    <span class="text-slate-400">Total Paket</span>
                    <span class="font-bold text-slate-700"><?= $stats['total_paket'] ?> Paket</span>
                </div>
                <div class="flex items-center justify-between py-1">
                    <span class="text-slate-400">Server Time</span>
                    <span class="font-mono text-slate-700"><?= date('H:i:s') ?></span>
                </div>
            </div>
        </div>
    </div>
</div>
