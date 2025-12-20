<!-- Page Layout -->
<div class="flex flex-col lg:flex-row gap-6 items-start relative">
    
    <!-- Sidebar Filters (Collapsible) -->
    <div id="filter-sidebar" class="hidden w-full lg:w-80 flex-shrink-0 transition-all duration-300 ease-in-out">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 sticky top-24">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-bold text-slate-800 flex items-center gap-2">
                    <ion-icon name="filter-circle-outline" class="text-blue-600 text-xl"></ion-icon>
                    Filter & Pencarian
                </h3>
                <button onclick="toggleFilter()" class="lg:hidden text-slate-400 hover:text-slate-600">
                    <ion-icon name="close-outline" class="text-xl"></ion-icon>
                </button>
            </div>
            
            <form action="<?= APP_URL ?>/paket" method="GET" class="space-y-5">
                
                <!-- Search -->
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Cari Nama Paket</label>
                    <div class="relative">
                        <ion-icon name="search-outline" class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></ion-icon>
                        <input type="text" name="search" value="<?= htmlspecialchars($filters['search'] ?? '') ?>" 
                               class="w-full pl-9 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all text-sm"
                               placeholder="Contoh: Paket Jalan...">
                    </div>
                </div>
                
                <!-- Status -->
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Status Proyek</label>
                    <div class="space-y-2">
                        <?php 
                        $statuses = ['Kontrak', 'Pemesanan', 'Distribusi', 'Selesai'];
                        $currentStatus = $filters['status'] ?? '';
                        ?>
                        <label class="flex items-center gap-2 p-2 rounded-lg border cursor-pointer transition-all <?= $currentStatus === '' ? 'bg-blue-50 border-blue-200 text-blue-700' : 'bg-white border-slate-200 text-slate-600 hover:bg-slate-50' ?>">
                            <input type="radio" name="status" value="" class="hidden" <?= $currentStatus === '' ? 'checked' : '' ?> onchange="this.form.submit()">
                            <div class="w-4 h-4 rounded-full border flex items-center justify-center <?= $currentStatus === '' ? 'border-blue-600' : 'border-slate-300' ?>">
                                <?php if($currentStatus === ''): ?><div class="w-2 h-2 rounded-full bg-blue-600"></div><?php endif; ?>
                            </div>
                            <span class="text-sm font-medium">Semua Status</span>
                        </label>
                        
                        <?php foreach($statuses as $s): ?>
                            <label class="flex items-center gap-2 p-2 rounded-lg border cursor-pointer transition-all <?= $currentStatus === $s ? 'bg-blue-50 border-blue-200 text-blue-700' : 'bg-white border-slate-200 text-slate-600 hover:bg-slate-50' ?>">
                                <input type="radio" name="status" value="<?= $s ?>" class="hidden" <?= $currentStatus === $s ? 'checked' : '' ?> onchange="this.form.submit()">
                                <div class="w-4 h-4 rounded-full border flex items-center justify-center <?= $currentStatus === $s ? 'border-blue-600' : 'border-slate-300' ?>">
                                    <?php if($currentStatus === $s): ?><div class="w-2 h-2 rounded-full bg-blue-600"></div><?php endif; ?>
                                </div>
                                <span class="text-sm font-medium"><?= $s ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <!-- Range Pagu -->
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Range Pagu (Nilai Kontrak)</label>
                    <div class="grid grid-cols-2 gap-2 mb-2">
                        <div>
                            <span class="text-[10px] text-slate-400 uppercase">Min (Juta)</span>
                            <select name="min_pagu" class="w-full p-2 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:outline-none focus:border-blue-500 transition-colors cursor-pointer">
                                <option value="">Min</option>
                                <?php for($i=0; $i<=500; $i+=50): ?>
                                    <option value="<?= $i * 1000000 ?>" <?= ($filters['min_pagu'] ?? '') !== '' && ($filters['min_pagu'] == $i * 1000000) ? 'selected' : '' ?>>
                                        <?= $i == 0 ? '0' : $i . ' Jt' ?>
                                    </option>
                                <?php endfor; ?>
                                <option value="500000001" <?= ($filters['min_pagu'] ?? '') == '500000001' ? 'selected' : '' ?>> > 500 Jt </option>
                            </select>
                        </div>
                        <div>
                            <span class="text-[10px] text-slate-400 uppercase">Max (Juta)</span>
                            <select name="max_pagu" class="w-full p-2 bg-slate-50 border border-slate-200 rounded-lg text-sm focus:outline-none focus:border-blue-500 transition-colors cursor-pointer">
                                <option value="">Max</option>
                                <?php for($i=50; $i<=500; $i+=50): ?>
                                    <option value="<?= $i * 1000000 ?>" <?= ($filters['max_pagu'] ?? '') !== '' && ($filters['max_pagu'] == $i * 1000000) ? 'selected' : '' ?>>
                                        <?= $i . ' Jt' ?>
                                    </option>
                                <?php endfor; ?>
                                <option value="" <?= isset($filters['max_pagu']) && $filters['max_pagu'] === '' ? 'selected' : '' ?>> > 500 Jt </option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="flex gap-2">
                    <button type="submit" class="flex-1 py-2.5 bg-slate-800 text-white font-medium rounded-xl hover:bg-slate-900 transition-colors shadow-lg shadow-slate-800/20 text-sm">
                        Terapkan
                    </button>
                    <?php if(!empty($filters['search']) || !empty($filters['status']) || !empty($filters['min_pagu'])): ?>
                        <a href="<?= APP_URL ?>/paket" class="px-3 py-2.5 bg-rose-50 text-rose-600 font-medium rounded-xl hover:bg-rose-100 transition-colors text-sm flex items-center justify-center">
                            <ion-icon name="refresh-outline" class="text-lg"></ion-icon>
                        </a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Main Content -->
    <div class="flex-1 w-full min-w-0">
        <!-- Header Action -->
        <div class="flex flex-col md:flex-row md:items-center justify-between mb-6 gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Daftar Paket Kegiatan</h1>
                <div class="flex items-center gap-2 mt-1">
                    <p class="text-slate-500 text-sm">
                        Menampilkan <strong><?= count($paket) ?></strong> paket 
                        <?php if(!empty($filters['status'])) echo "status <strong>" . htmlspecialchars($filters['status']) . "</strong>"; ?>
                    </p>
                    
                    <!-- Filter Toggle Button -->
                    <button onclick="toggleFilter()" id="btn-filter-toggle" class="inline-flex items-center gap-1 px-3 py-1 rounded-lg bg-slate-100 text-slate-600 text-xs font-semibold hover:bg-blue-50 hover:text-blue-600 transition-colors ml-2 border border-transparent hover:border-blue-200">
                        <ion-icon name="filter" class="text-sm"></ion-icon>
                        <span id="filter-text">Filter</span>
                        <span class="w-2 h-2 rounded-full bg-blue-500 ml-1 <?= !empty($filters) && array_filter($filters) ? '' : 'hidden' ?>"></span>
                    </button>
                </div>
            </div>
            
            <div class="flex items-center gap-3">
                <!-- View Toggle Buttons -->
                <div class="bg-white border border-slate-200 rounded-xl p-1 flex items-center shadow-sm">
                    <button onclick="switchView('list')" id="btn-list" class="p-2 rounded-lg text-slate-500 hover:text-slate-800 transition-all">
                        <ion-icon name="list-outline" class="text-xl"></ion-icon>
                    </button>
                    <div class="w-px h-4 bg-slate-200 mx-1"></div>
                    <button onclick="switchView('card')" id="btn-card" class="p-2 rounded-lg text-slate-500 hover:text-slate-800 transition-all">
                        <ion-icon name="grid-outline" class="text-xl"></ion-icon>
                    </button>
                </div>
                
                <?php if(($_SESSION['admin_role'] ?? 'admin') === 'admin'): ?>
                <a href="<?= APP_URL ?>/paket/create" class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl transition-all shadow-lg shadow-blue-600/30 hover:scale-[1.02] active:scale-[0.98]">
                    <ion-icon name="add-circle-outline" class="text-xl"></ion-icon>
                    <span class="hidden md:inline">Paket Baru</span>
                </a>
                <?php endif; ?>
            </div>
            
            <!-- Mobile FAB -->
            <?php if(($_SESSION['admin_role'] ?? 'admin') === 'admin'): ?>
            <a href="<?= APP_URL ?>/paket/create" class="md:hidden fixed bottom-6 right-6 w-14 h-14 bg-blue-600 text-white rounded-full shadow-xl flex items-center justify-center text-3xl z-50 hover:scale-110 transition-transform">
                <ion-icon name="add"></ion-icon>
            </a>
            <?php endif; ?>
        </div>
        
        <?php if (empty($paket)): ?>
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-12 text-center">
                <div class="w-24 h-24 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4">
                    <ion-icon name="search-outline" class="text-4xl text-slate-400"></ion-icon>
                </div>
                <h3 class="text-lg font-bold text-slate-800 mb-2">Tidak ditemukan paket</h3>
                <p class="text-slate-500 mb-6">Coba sesuaikan filter atau kata kunci pencarian Anda.</p>
                <a href="<?= APP_URL ?>/paket" class="inline-flex items-center gap-2 px-6 py-2 bg-slate-100 text-slate-600 font-medium rounded-lg hover:bg-slate-200 transition-colors">
                    Reset Filter
                </a>
            </div>
        <?php else: ?>
            
            <!-- List View (Default) -->
            <div id="view-list" class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-slate-50/50 text-[10px] uppercase text-slate-400 font-bold border-b border-slate-100 tracking-wider">
                            <tr>
                                <th class="px-6 py-4">Paket Kegiatan</th>
                                <th class="px-6 py-4 text-center">Status</th>
                                <th class="px-6 py-4 text-right">Nilai Pagu</th>
                                <th class="px-6 py-4 text-right">Nilai RAB</th>
                                <th class="px-6 py-4 text-center">Pemodal</th>
                                <th class="px-6 py-4 w-20"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-sm">
                            <?php foreach ($paket as $p): ?>
                                <tr class="hover:bg-slate-50/50 transition-colors group cursor-pointer" onclick="window.location='<?= APP_URL ?>/paket/detail/<?= $p['id'] ?>'">
                                    <td class="px-6 py-4 align-top">
                                        <div class="font-bold text-slate-800 text-base mb-1 hover:text-blue-600 transition-colors"><?= htmlspecialchars($p['nama_paket']) ?></div>
                                        <div class="text-xs text-slate-500 flex items-center gap-1.5">
                                            <ion-icon name="business-outline"></ion-icon>
                                            <?= htmlspecialchars($p['perusahaan'] ?? '-') ?>
                                            <span class="mx-1">&bull;</span>
                                            <span class="font-mono"><?= date('d M Y', strtotime($p['created_at'])) ?></span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 align-top text-center">
                                        <?= getStatusBadge($p['status']) ?>
                                    </td>
                                    <td class="px-6 py-4 align-top text-right">
                                        <span class="font-bold text-slate-700 font-mono"><?= formatRupiah($p['pagu']) ?></span>
                                    </td>
                                    <td class="px-6 py-4 align-top text-right">
                                        <span class="font-bold text-indigo-600 font-mono"><?= formatRupiah($p['rab']) ?></span>
                                    </td>
                                    <td class="px-6 py-4 align-top text-center">
                                        <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-slate-100 text-slate-600 text-xs font-semibold">
                                            <ion-icon name="people"></ion-icon>
                                            <?= $p['jumlah_pemodal'] ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 align-top text-center">
                                        <div class="w-8 h-8 rounded-full bg-white border border-slate-200 text-slate-400 flex items-center justify-center group-hover:border-blue-200 group-hover:text-blue-600 transition-colors">
                                            <ion-icon name="chevron-forward"></ion-icon>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Card View (Hidden by default) -->
            <div id="view-card" class="hidden grid grid-cols-1 md:grid-cols-2 xl:grid-cols-2 gap-4">
                <?php foreach ($paket as $p): ?>
                    <a href="<?= APP_URL ?>/paket/detail/<?= $p['id'] ?>" class="group block bg-white rounded-2xl border border-slate-200 p-5 hover:border-blue-400 hover:shadow-md transition-all relative overflow-hidden">
                        <!-- Left Border Status Color -->
                        <div class="absolute left-0 top-0 bottom-0 w-1 
                            <?php
                            switch($p['status']) {
                                case 'Selesai': echo 'bg-emerald-500'; break;
                                case 'Kontrak': echo 'bg-blue-500'; break;
                                case 'Distribusi': echo 'bg-purple-500'; break;
                                case 'Pemesanan': echo 'bg-amber-500'; break;
                                default: echo 'bg-slate-300';
                            }
                            ?>
                        "></div>
                    
                        <div class="flex justify-between items-start mb-3 pl-2">
                            <div>
                                <h3 class="font-bold text-slate-800 text-lg group-hover:text-blue-600 transition-colors line-clamp-1" title="<?= htmlspecialchars($p['nama_paket'] ?? '') ?>">
                                    <?= htmlspecialchars($p['nama_paket'] ?? '') ?>
                                </h3>
                                <div class="text-xs text-slate-500 mt-1 flex items-center gap-1">
                                    <ion-icon name="business-outline"></ion-icon>
                                    <?= htmlspecialchars($p['perusahaan'] ?? '-') ?>
                                </div>
                            </div>
                            <div class="flex-shrink-0">
                                <?= getStatusBadge($p['status']) ?>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4 mb-4 pl-2">
                            <div class="bg-slate-50 p-3 rounded-xl">
                                <span class="text-[10px] uppercase font-bold text-slate-500 tracking-wider">Nilai Pagu</span>
                                <div class="text-slate-800 font-bold font-mono">
                                    <?= formatRupiah($p['pagu']) ?>
                                </div>
                            </div>
                            <div class="bg-indigo-50 p-3 rounded-xl">
                                <span class="text-[10px] uppercase font-bold text-indigo-500 tracking-wider">Estimasi RAB</span>
                                <div class="text-indigo-700 font-bold font-mono">
                                    <?= formatRupiah($p['rab']) ?>
                                </div>
                            </div>
                        </div>
                        
                        <div class="flex items-center justify-between text-sm text-slate-500 pl-2 border-t border-slate-100 pt-3">
                            <div class="flex items-center gap-3">
                                <span class="flex items-center gap-1" title="Jumlah Pemodal">
                                    <ion-icon name="people" class="text-blue-500"></ion-icon>
                                    <?= $p['jumlah_pemodal'] ?>
                                </span>
                                <span class="flex items-center gap-1" title="Tanggal Dibuat">
                                    <ion-icon name="calendar-outline"></ion-icon>
                                    <?= date('d M Y', strtotime($p['created_at'])) ?>
                                </span>
                            </div>
                            <div class="text-blue-600 font-semibold text-xs flex items-center gap-1 group-hover:gap-2 transition-all">
                                Lihat Detail <ion-icon name="arrow-forward"></ion-icon>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
            
        <?php endif; ?>
    </div>
</div>

<script>
    function switchView(mode) {
        // Save to localStorage
        localStorage.setItem('paketMode', mode);
        
        const listBtn = document.getElementById('btn-list');
        const cardBtn = document.getElementById('btn-card');
        const listView = document.getElementById('view-list');
        const cardView = document.getElementById('view-card');
        
        // Reset styles
        listBtn.classList.remove('bg-slate-100', 'text-blue-600');
        cardBtn.classList.remove('bg-slate-100', 'text-blue-600');
        listView.classList.add('hidden');
        cardView.classList.add('hidden');
        
        // Apply active style
        if (mode === 'list') {
            listBtn.classList.add('bg-slate-100', 'text-blue-600');
            listView.classList.remove('hidden');
        } else {
            cardBtn.classList.add('bg-slate-100', 'text-blue-600');
            cardView.classList.remove('hidden');
        }
    }
    
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

    // Initialize View
    document.addEventListener('DOMContentLoaded', () => {
        const savedMode = localStorage.getItem('paketMode') || 'list';
        switchView(savedMode);
    });
</script>
