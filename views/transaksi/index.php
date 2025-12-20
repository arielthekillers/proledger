<!-- Page Layout -->
<div class="flex flex-col lg:flex-row gap-6 items-start relative">
    
    <!-- Sidebar Filters (Collapsible) -->
    <div id="filter-sidebar" class="hidden w-full lg:w-80 flex-shrink-0 transition-all duration-300 ease-in-out">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 sticky top-24">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-bold text-slate-800 flex items-center gap-2">
                    <ion-icon name="filter-circle-outline" class="text-blue-600 text-xl"></ion-icon>
                    Filter Transaksi
                </h3>
                <button onclick="toggleFilter()" class="lg:hidden text-slate-400 hover:text-slate-600">
                    <ion-icon name="close-outline" class="text-xl"></ion-icon>
                </button>
            </div>
            
            <form action="<?= APP_URL ?>/transaksi" method="GET" class="space-y-5">
                
                <!-- Filter Paket -->
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Paket Kegiatan</label>
                    <div class="relative">
                        <ion-icon name="cube-outline" class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></ion-icon>
                        <select name="paket_id" class="w-full pl-9 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 appearance-none text-sm transition-all">
                            <option value="">Semua Paket Kegiatan</option>
                            <?php foreach($all_paket as $p): ?>
                                <option value="<?= $p['id'] ?>" <?= ($filters['paket_id'] ?? '') == $p['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($p['nama_paket']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <ion-icon name="chevron-down-outline" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"></ion-icon>
                    </div>
                </div>

                <!-- Date Range -->
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Tanggal Transaksi</label>
                    <div class="space-y-2">
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-[10px] font-bold text-slate-400 uppercase">Dari</span>
                            <input type="date" name="start_date" value="<?= $filters['start_date'] ?? '' ?>" 
                                   class="w-full pl-10 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:outline-none focus:border-blue-500">
                        </div>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-[10px] font-bold text-slate-400 uppercase">Smp</span>
                            <input type="date" name="end_date" value="<?= $filters['end_date'] ?? '' ?>" 
                                   class="w-full pl-10 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:outline-none focus:border-blue-500">
                        </div>
                    </div>
                </div>

                <!-- Filter Tipe -->
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Tipe Transaksi</label>
                    <div class="grid grid-cols-2 gap-2">
                        <label class="cursor-pointer">
                            <input type="radio" name="tipe" value="masuk" class="peer sr-only" <?= ($filters['tipe'] ?? '') === 'masuk' ? 'checked' : '' ?>>
                            <div class="px-3 py-2 rounded-lg border border-slate-200 bg-white text-slate-600 text-center text-sm font-medium peer-checked:bg-emerald-50 peer-checked:border-emerald-200 peer-checked:text-emerald-700 transition-all hover:bg-slate-50">
                                Masuk
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="tipe" value="keluar" class="peer sr-only" <?= ($filters['tipe'] ?? '') === 'keluar' ? 'checked' : '' ?>>
                            <div class="px-3 py-2 rounded-lg border border-slate-200 bg-white text-slate-600 text-center text-sm font-medium peer-checked:bg-rose-50 peer-checked:border-rose-200 peer-checked:text-rose-700 transition-all hover:bg-slate-50">
                                Keluar
                            </div>
                        </label>
                    </div>
                </div>
                
                <!-- Filter Kategori -->
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Kategori</label>
                    <select name="kategori" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:border-blue-500 text-sm appearance-none cursor-pointer">
                        <option value="">Semua Kategori</option>
                        <optgroup label="Pemasukan">
                            <option value="Pasal" <?= ($filters['kategori'] ?? '') === 'Pasal' ? 'selected' : '' ?>>Pasal</option>
                            <option value="Modal Belanja" <?= ($filters['kategori'] ?? '') === 'Modal Belanja' ? 'selected' : '' ?>>Modal Belanja</option>
                            <option value="Administrasi" <?= ($filters['kategori'] ?? '') === 'Administrasi' ? 'selected' : '' ?>>Administrasi</option>
                            <option value="Lainnya" <?= ($filters['kategori'] ?? '') === 'Lainnya' ? 'selected' : '' ?>>Lainnya</option>
                        </optgroup>
                        <optgroup label="Pengeluaran">
                            <option value="Pasal" <?= ($filters['kategori'] ?? '') === 'Pasal' ? 'selected' : '' ?>>Pasal</option>
                            <option value="Modal Belanja" <?= ($filters['kategori'] ?? '') === 'Modal Belanja' ? 'selected' : '' ?>>Modal Belanja</option>
                            <option value="Pengeluaran" <?= ($filters['kategori'] ?? '') === 'Pengeluaran' ? 'selected' : '' ?>>Pengeluaran Umum</option>
                            <option value="Administrasi" <?= ($filters['kategori'] ?? '') === 'Administrasi' ? 'selected' : '' ?>>Administrasi</option>
                        </optgroup>
                    </select>
                </div>

                <div class="flex gap-2">
                    <button type="submit" class="flex-1 py-2.5 bg-slate-800 text-white font-medium rounded-xl hover:bg-slate-900 transition-colors shadow-lg shadow-slate-800/20 text-sm">
                        Terapkan
                    </button>
                    <?php if(!empty($filters['tipe']) || !empty($filters['kategori']) || !empty($filters['paket_id']) || !empty($filters['start_date']) || !empty($filters['end_date'])): ?>
                        <a href="<?= APP_URL ?>/transaksi" class="px-3 py-2.5 bg-rose-50 text-rose-600 font-medium rounded-xl hover:bg-rose-100 transition-colors text-sm flex items-center justify-center">
                            <ion-icon name="refresh-outline" class="text-lg"></ion-icon>
                        </a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>

    <!-- Main Content -->
    <div class="flex-1 w-full min-w-0">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Riwayat Transaksi</h1>
                <div class="flex items-center gap-2 mt-1">
                    <p class="text-slate-500 text-sm">
                        Menampilkan <strong><?= count($transaksi) ?></strong> data
                    </p>
                    
                    <!-- Filter Toggle Button -->
                    <button onclick="toggleFilter()" id="btn-filter-toggle" class="inline-flex items-center gap-1 px-3 py-1 rounded-lg bg-slate-100 text-slate-600 text-xs font-semibold hover:bg-blue-50 hover:text-blue-600 transition-colors ml-2 border border-transparent hover:border-blue-200">
                        <ion-icon name="filter" class="text-sm"></ion-icon>
                        <span id="filter-text">Filter</span>
                        <span class="w-2 h-2 rounded-full bg-blue-500 ml-1 <?= !empty($filters) && (array_filter($filters)) ? '' : 'hidden' ?>"></span>
                    </button>
                </div>
            </div>
            
            <div class="flex items-center catch-gap">
                <?php if(($_SESSION['admin_role'] ?? 'admin') === 'admin'): ?>
                <a href="<?= APP_URL ?>/transaksi/create" class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl transition-all shadow-lg shadow-blue-600/30 hover:scale-[1.02] active:scale-[0.98]">
                    <ion-icon name="add-circle-outline" class="text-xl"></ion-icon>
                    <span class="hidden md:inline">Catat Transaksi</span>
                    <span class="md:hidden">Baru</span>
                </a>
                <?php endif; ?>
            </div>
            
            <!-- Mobile FAB -->
            <?php if(($_SESSION['admin_role'] ?? 'admin') === 'admin'): ?>
            <a href="<?= APP_URL ?>/transaksi/create" class="md:hidden fixed bottom-6 right-6 w-14 h-14 bg-blue-600 text-white rounded-full shadow-xl flex items-center justify-center text-3xl z-50 hover:scale-110 transition-transform">
                <ion-icon name="add"></ion-icon>
            </a>
            <?php endif; ?>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <?php if(empty($transaksi)): ?>
                <div class="p-12 text-center">
                    <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4">
                        <ion-icon name="receipt-outline" class="text-3xl text-slate-400"></ion-icon>
                    </div>
                    <p class="text-slate-500 font-medium">Belum ada data transaksi</p>
                    <p class="text-sm text-slate-400 mt-1">Silakan sesuaikan filter atau tambah transaksi baru</p>
                    <?php if(!empty($filters)): ?>
                    <div class="mt-4">
                        <a href="<?= APP_URL ?>/transaksi" class="text-blue-600 font-medium hover:underline">Reset Filter</a>
                    </div>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-slate-50/50 text-[10px] uppercase text-slate-400 font-bold border-b border-slate-100 tracking-wider">
                            <tr>
                                <th class="px-6 py-4 w-32">Tanggal</th>
                                <th class="px-6 py-4">Kategori / Paket</th>
                                <th class="px-6 py-4 text-center w-32">Tipe</th>
                                <th class="px-6 py-4">Keterangan</th>
                                <th class="px-6 py-4 text-right w-40">Jumlah</th>
                                <th class="px-6 py-4 w-20"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-sm">
                            <?php 
                            $total_masuk = 0;
                            $total_keluar = 0;
                            foreach ($transaksi as $t): 
                                if ($t['tipe'] == 'masuk') $total_masuk += $t['jumlah'];
                                else $total_keluar += $t['jumlah'];
                            ?>
                                <tr class="hover:bg-slate-50/50 transition-colors group relative">
                                    <td class="px-6 py-4 whitespace-nowrap align-top">
                                        <div class="font-mono text-slate-600 font-medium"><?= date('d/m/Y', strtotime($t['tanggal'])) ?></div>
                                    </td>
                                    <td class="px-6 py-4 align-top">
                                        <div class="font-bold text-slate-800 text-base mb-1"><?= htmlspecialchars($t['kategori'] ?? '-') ?></div>
                                        <div class="text-[10px] text-slate-400 font-bold uppercase tracking-wide flex items-center gap-1">
                                            <ion-icon name="cube-outline"></ion-icon>
                                            <span class="line-clamp-1"><?= htmlspecialchars($t['nama_paket']) ?></span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-center align-top">
                                        <?php if($t['tipe'] == 'masuk'): ?>
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-emerald-600 bg-emerald-50 border border-emerald-100 text-[10px] font-bold uppercase tracking-wide">
                                                Masuk
                                            </span>
                                        <?php else: ?>
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-rose-600 bg-rose-50 border border-rose-100 text-[10px] font-bold uppercase tracking-wide">
                                                Keluar
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 align-top">
                                        <div class="text-slate-600 text-sm leading-relaxed"><?= htmlspecialchars($t['keterangan'] ?? '-') ?></div>
                                    </td>
                                    <td class="px-6 py-4 text-right align-top">
                                        <span class="font-mono font-bold <?= $t['tipe'] == 'masuk' ? 'text-emerald-600' : 'text-slate-700' ?>">
                                            <?= formatRupiah($t['jumlah']) ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right align-top">
                                        <?php if(($_SESSION['admin_role'] ?? 'admin') === 'admin'): ?>
                                        <div class="flex items-center justify-end gap-1 opacity-0 group-hover:opacity-100 transition-opacity absolute right-4 top-4">
                                            <a href="<?= APP_URL ?>/transaksi/edit/<?= $t['id'] ?>" class="p-1.5 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded transition-colors" title="Edit">
                                                <ion-icon name="create" class="text-lg"></ion-icon>
                                            </a>
                                            <form action="<?= APP_URL ?>/transaksi/delete/<?= $t['id'] ?>" method="POST" onsubmit="return confirm('Hapus transaksi ini?')">
                                                <?= csrfField() ?>
                                                <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded transition-colors" title="Hapus">
                                                    <ion-icon name="trash" class="text-lg"></ion-icon>
                                                </button>
                                            </form>
                                        </div>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot class="bg-slate-50 border-t border-slate-200">
                            <tr>
                                <td colspan="4" class="px-6 py-3 font-bold text-slate-500 text-right uppercase text-[10px] tracking-wider">Total Mutasi (Halaman Ini)</td>
                                <td class="px-6 py-3 text-right font-mono text-slate-800 font-bold whitespace-nowrap text-xs">
                                    <div class="text-emerald-600 mb-0.5">+ <?= formatRupiah($total_masuk) ?></div>
                                    <div class="text-rose-600">- <?= formatRupiah($total_keluar) ?></div>
                                </td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    function toggleFilter() {
        const sidebar = document.getElementById('filter-sidebar');
        const btn = document.getElementById('btn-filter-toggle');
        
        // Toggle hidden class
        if (sidebar.classList.contains('hidden')) {
            sidebar.classList.remove('hidden');
            // Update button style to active
            btn.classList.add('bg-blue-50', 'text-blue-600', 'border-blue-200');
            btn.classList.remove('bg-slate-100', 'text-slate-600', 'border-transparent');
        } else {
            sidebar.classList.add('hidden');
            // Update button style to inactive
            btn.classList.remove('bg-blue-50', 'text-blue-600', 'border-blue-200');
            btn.classList.add('bg-slate-100', 'text-slate-600', 'border-transparent');
        }
    }
</script>
