<!-- Page Header -->
<div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
    <div>
        <div class="flex items-center gap-2 text-slate-500 mb-1">
            <ion-icon name="cube-outline"></ion-icon>
            <span class="text-xs font-bold uppercase tracking-wider">Detail Paket Kegiatan</span>
        </div>
        <h1 class="text-2xl md:text-3xl font-bold text-slate-800"><?= htmlspecialchars($paket['nama_paket']) ?></h1>
        <?php if (!empty($paket['perusahaan'])): ?>
            <a href="<?= APP_URL ?>/perusahaan/detail/<?= $paket['perusahaan_id'] ?>" class="text-slate-500 font-medium mt-1 inline-flex items-center gap-2 hover:text-blue-600 transition-colors">
                <ion-icon name="business-outline"></ion-icon>
                <?= htmlspecialchars($paket['perusahaan']) ?>
                <ion-icon name="open-outline" class="text-xs"></ion-icon>
            </a>
        <?php endif; ?>
    </div>
    
    <div class="flex items-center gap-3">
        <?php 
        $backUrl = APP_URL . '/paket';
        $backLabel = 'Kembali';
        
        if (isset($_GET['from']) && $_GET['from'] === 'pemodal' && isset($_GET['pemodal_id'])) {
            $backUrl = APP_URL . '/pemodal/detail/' . $_GET['pemodal_id'];
            $backLabel = 'Ke Pemodal';
        } elseif (isset($_GET['from']) && $_GET['from'] === 'perusahaan' && isset($_GET['source_id'])) {
            $backUrl = APP_URL . '/perusahaan/detail/' . $_GET['source_id'];
            $backLabel = 'Ke Perusahaan';
        }
        ?>
        <a href="<?= $backUrl ?>" class="px-4 py-2 bg-white border border-slate-200 text-slate-600 font-semibold rounded-xl hover:bg-slate-50 transition-colors shadow-sm flex items-center gap-2">
            <ion-icon name="arrow-back-outline"></ion-icon>
            <span><?= $backLabel ?></span>
        </a>
        <a href="<?= APP_URL ?>/rekap/paket/<?= $paket['id'] ?>?from=paket" class="px-5 py-2 bg-emerald-600 text-white font-semibold rounded-xl hover:bg-emerald-700 transition-colors shadow-lg shadow-emerald-600/20 flex items-center gap-2">
            <ion-icon name="pie-chart-outline"></ion-icon>
            <span>Lihat Rekap</span>
        </a>
        <?php if(($_SESSION['admin_role'] ?? 'admin') === 'admin'): ?>
        <a href="<?= APP_URL ?>/paket/edit/<?= $paket['id'] ?>" class="px-5 py-2 bg-slate-800 text-white font-semibold rounded-xl hover:bg-slate-900 transition-colors shadow-lg shadow-slate-800/20 flex items-center gap-2">
            <ion-icon name="create-outline"></ion-icon>
            <span>Edit Paket</span>
        </a>
        <?php endif; ?>
    </div>
</div>

<!-- Financial Summary Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Total Modal -->
    <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm relative overflow-hidden group">
        <div class="absolute right-0 top-0 p-4 opacity-5 group-hover:opacity-10 transition-opacity">
            <ion-icon name="wallet" class="text-8xl text-blue-600"></ion-icon>
        </div>
        <div class="relative z-10">
            <div class="flex items-center gap-2 mb-2">
                <div class="p-1.5 bg-blue-50 text-blue-600 rounded-lg">
                    <ion-icon name="wallet-outline"></ion-icon>
                </div>
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Modal</span>
            </div>
            <div class="text-2xl font-bold text-slate-800 mb-1"><?= formatRupiah($summary['total_modal']) ?></div>
            <div class="text-xs font-medium text-slate-400 flex items-center gap-1">
                <ion-icon name="people-outline"></ion-icon>
                <?= $paket['jumlah_pemodal'] ?> Investor Terdaftar
            </div>
        </div>
    </div>
    
    <!-- Uang Masuk -->
    <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm relative overflow-hidden group">
        <div class="absolute right-0 top-0 p-4 opacity-5 group-hover:opacity-10 transition-opacity">
            <ion-icon name="arrow-down-circle" class="text-8xl text-emerald-600"></ion-icon>
        </div>
        <div class="relative z-10">
            <div class="flex items-center gap-2 mb-2">
                <div class="p-1.5 bg-emerald-50 text-emerald-600 rounded-lg">
                    <ion-icon name="arrow-down-outline"></ion-icon>
                </div>
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Masuk</span>
            </div>
            <div class="text-2xl font-bold text-emerald-600 mb-1"><?= formatRupiah($summary['total_masuk']) ?></div>
            <div class="text-xs font-medium text-slate-400">Dari Investor</div>
        </div>
    </div>
    
    <!-- Uang Keluar -->
    <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm relative overflow-hidden group">
        <div class="absolute right-0 top-0 p-4 opacity-5 group-hover:opacity-10 transition-opacity">
            <ion-icon name="arrow-up-circle" class="text-8xl text-rose-600"></ion-icon>
        </div>
        <div class="relative z-10">
            <div class="flex items-center gap-2 mb-2">
                <div class="p-1.5 bg-rose-50 text-rose-600 rounded-lg">
                    <ion-icon name="arrow-up-outline"></ion-icon>
                </div>
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Keluar</span>
            </div>
            <div class="text-2xl font-bold text-rose-600 mb-1"><?= formatRupiah($summary['total_keluar']) ?></div>
            <div class="text-xs font-medium text-slate-400">Belanja & Operasional</div>
        </div>
    </div>
    
    <!-- Sisa/Saldo -->
    <div class="bg-gradient-to-br from-slate-800 to-slate-900 rounded-2xl p-6 text-white shadow-lg relative overflow-hidden">
        <div class="absolute right-0 top-0 p-4 opacity-10">
            <ion-icon name="podium" class="text-8xl text-white"></ion-icon>
        </div>
        <div class="relative z-10">
            <div class="flex items-center gap-2 mb-2">
                <div class="p-1.5 bg-white/10 rounded-lg backdrop-blur-sm">
                    <ion-icon name="stats-chart-outline"></ion-icon>
                </div>
                <span class="text-xs font-bold text-slate-300 uppercase tracking-wider">Saldo Akhir</span>
            </div>
            <div class="text-2xl font-bold mb-1"><?= formatRupiah($summary['laba_rugi']) ?></div>
            <?php if($summary['laba_rugi'] >= 0): ?>
                <div class="text-xs font-medium text-emerald-400 flex items-center gap-1">
                    <ion-icon name="trending-up"></ion-icon> Surplus
                </div>
            <?php else: ?>
                <div class="text-xs font-medium text-rose-400 flex items-center gap-1">
                    <ion-icon name="alert-circle"></ion-icon> Defisit
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Tabs & Content -->
<div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden min-h-[500px]">
    <!-- Tab Navigation -->
    <div class="flex border-b border-slate-100 px-6 pt-4 gap-6 overflow-x-auto">
        <button onclick="switchTab('info', this)" class="tab-btn active pb-4 text-sm font-bold text-slate-500 border-b-2 border-transparent hover:text-slate-800 transition-all flex items-center gap-2 whitespace-nowrap" id="default-tab">
            <ion-icon name="information-circle-outline" class="text-lg"></ion-icon>
            Informasi Paket
        </button>
        <button onclick="switchTab('modal', this)" class="tab-btn pb-4 text-sm font-bold text-slate-500 border-b-2 border-transparent hover:text-slate-800 transition-all flex items-center gap-2 whitespace-nowrap">
            <ion-icon name="wallet-outline" class="text-lg"></ion-icon>
            Detail Modal
        </button>
        <button onclick="switchTab('transaksi', this)" class="tab-btn pb-4 text-sm font-bold text-slate-500 border-b-2 border-transparent hover:text-slate-800 transition-all flex items-center gap-2 whitespace-nowrap">
            <ion-icon name="receipt-outline" class="text-lg"></ion-icon>
            Riwayat Transaksi
        </button>
    </div>

    <!-- Tab Content: Info -->
    <div id="tab-info" class="p-6 md:p-8 tab-content">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
            <!-- Left: Details -->
            <div class="space-y-8">
                <div>
                    <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4">Detail Utama</h3>
                    <div class="grid grid-cols-2 gap-6">
                        <div class="p-4 rounded-xl bg-slate-50 border border-slate-100">
                            <div class="text-xs text-slate-500 mb-1">Nilai Pagu</div>
                            <div class="font-bold text-slate-800 text-lg"><?= formatRupiah($paket['pagu']) ?></div>
                        </div>
                        <div class="p-4 rounded-xl bg-slate-50 border border-slate-100">
                            <div class="text-xs text-slate-500 mb-1">Nilai RAB</div>
                            <div class="font-bold text-slate-800 text-lg"><?= formatRupiah($paket['rab']) ?></div>
                        </div>
                    </div>
                </div>
                
                <?php if(!empty($paket['perusahaan'])): ?>
                <div>
                     <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4">Perusahaan Pelaksana</h3>
                     <a href="<?= APP_URL ?>/perusahaan/detail/<?= $paket['perusahaan_id'] ?>" class="block p-4 rounded-xl bg-slate-50 border border-slate-100 hover:bg-blue-50 hover:border-blue-200 transition-all group">
                         <div class="flex items-center gap-4">
                             <div class="w-12 h-12 rounded-lg bg-blue-600 text-white flex items-center justify-center font-bold text-lg shadow-lg shadow-blue-600/20">
                                 <?= strtoupper(substr($paket['perusahaan'], 0, 2)) ?>
                             </div>
                             <div>
                                 <div class="font-bold text-slate-800 group-hover:text-blue-700 transition-colors"><?= htmlspecialchars($paket['perusahaan']) ?></div>
                                 <div class="text-xs text-slate-500 group-hover:text-blue-500 flex items-center gap-1">
                                     Lihat Profil Perusahaan <ion-icon name="arrow-forward"></ion-icon>
                                 </div>
                             </div>
                         </div>
                     </a>
                </div>
                <?php endif; ?>
                
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <div class="text-xs font-semibold text-slate-500 uppercase mb-1">Status Proyek</div>
                        <?= getStatusBadge($paket['status']) ?>
                    </div>
                    <div>
                        <div class="text-xs font-semibold text-slate-500 uppercase mb-1">Tanggal Dibuat</div>
                        <div class="font-medium text-slate-800"><?= formatTanggal($paket['created_at']) ?></div>
                    </div>
                </div>
            </div>
            
            <!-- Right: Investors -->
            <div>
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4">Investor Terdaftar</h3>
                <div class="grid grid-cols-1 gap-3">
                    <?php foreach ($paket['pemodal'] as $p): ?>
                        <div class="flex items-center gap-4 p-3 rounded-xl hover:bg-slate-50 border border-transparent hover:border-slate-100 transition-all group cursor-default">
                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 text-white flex items-center justify-center font-bold shadow-sm shadow-blue-500/30">
                                <?= strtoupper(substr($p['nama'], 0, 1)) ?>
                            </div>
                            <div class="flex-1">
                                <a href="<?= APP_URL ?>/pemodal/detail/<?= $p['id'] ?>?from=paket&paket_id=<?= $paket['id'] ?>" class="font-bold text-slate-800 hover:text-blue-600 hover:underline"><?= htmlspecialchars($p['nama']) ?></a>
                                <div class="text-xs text-slate-500"><?= htmlspecialchars($p['email'] ?? '-') ?></div>
                            </div>
                            <?php if(isset($p['no_hp'])): ?>
                                <a href="https://wa.me/<?= preg_replace('/^0/', '62', preg_replace('/[^0-9]/', '', $p['no_hp'])) ?>" target="_blank" class="p-2 text-slate-400 hover:text-emerald-500 transition-colors opacity-0 group-hover:opacity-100">
                                    <ion-icon name="logo-whatsapp" class="text-xl"></ion-icon>
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Tab Content: Modal -->
    <div id="tab-modal" class="hidden tab-content">
        <div class="relative">
            <table class="w-full text-left">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-4 text-[10px] font-bold text-slate-500 uppercase tracking-wider">Investor</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-slate-500 uppercase tracking-wider text-right">Modal Pasal (13%)</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-slate-500 uppercase tracking-wider text-right">Modal Belanja</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-slate-800 uppercase tracking-wider text-right">Total Kontribusi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    <?php foreach ($modal_detail as $md): ?>
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-800">
                                    <a href="<?= APP_URL ?>/pemodal/detail/<?= $md['user_id'] ?>?from=paket&paket_id=<?= $paket['id'] ?>" class="hover:text-blue-600 hover:underline">
                                        <?= htmlspecialchars($md['nama']) ?>
                                    </a>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right font-mono text-slate-600 text-sm">
                                <?= formatRupiah($md['modal_pasal']) ?>
                            </td>
                            <td class="px-6 py-4 text-right font-mono text-slate-600 text-sm">
                                <?= formatRupiah($md['modal_belanja']) ?>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <span class="font-bold text-blue-600 font-mono"><?= formatRupiah($md['total_modal']) ?></span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot class="bg-slate-50 border-t border-slate-200">
                    <tr>
                        <td class="px-6 py-4 font-bold text-slate-800 text-xs uppercase">Total Akumulasi</td>
                        <td class="px-6 py-4 text-right font-bold text-slate-800 font-mono text-sm"><?= formatRupiah(array_sum(array_column($modal_detail, 'modal_pasal'))) ?></td>
                        <td class="px-6 py-4 text-right font-bold text-slate-800 font-mono text-sm"><?= formatRupiah(array_sum(array_column($modal_detail, 'modal_belanja'))) ?></td>
                        <td class="px-6 py-4 text-right font-bold text-blue-700 font-mono text-base"><?= formatRupiah($summary['total_modal']) ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- Tab Content: Transaksi -->
    <div id="tab-transaksi" class="hidden tab-content">
        <!-- Action Bar -->
        <div class="px-8 py-6 flex items-center justify-between border-b border-slate-100 bg-slate-50/30">
            <h4 class="font-bold text-slate-700">Riwayat Mutasi Dana</h4>
            <?php if(($_SESSION['admin_role'] ?? 'admin') === 'admin'): ?>
            <a href="<?= APP_URL ?>/transaksi/create/<?= $paket['id'] ?>" class="px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition-colors shadow-lg shadow-blue-600/20 flex items-center gap-2">
                <ion-icon name="add-circle"></ion-icon>
                <span>Catat Transaksi</span>
            </a>
            <?php endif; ?>
        </div>

        <?php if (empty($transaksi)): ?>
            <div class="text-center py-20">
                <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4 text-slate-300">
                    <ion-icon name="receipt" class="text-3xl"></ion-icon>
                </div>
                <p class="text-slate-500 font-medium">Belum ada transaksi</p>
                <p class="text-xs text-slate-400 mt-1">Mulai catat pemasukan atau pengeluaran untuk paket ini.</p>
            </div>
        <?php else: ?>
            <table class="w-full text-left">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-4 text-[10px] font-bold text-slate-500 uppercase tracking-wider w-32">Tanggal</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-slate-500 uppercase tracking-wider w-24 text-center">Tipe</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-slate-500 uppercase tracking-wider">Kategori / Keterangan</th>
                        <th class="px-6 py-4 text-[10px] font-bold text-slate-500 uppercase tracking-wider text-right w-40">Nominal</th>
                        <th class="px-6 py-4 w-20"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    <?php foreach ($transaksi as $t): ?>
                        <tr class="group hover:bg-slate-50/80 transition-colors">
                            <td class="px-6 py-4 align-top">
                                <div class="font-mono text-slate-600 text-sm"><?= formatTanggal($t['tanggal']) ?></div>
                            </td>
                            <td class="px-6 py-4 align-top text-center">
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
                                <div class="font-bold text-slate-800 text-sm mb-0.5"><?= htmlspecialchars($t['kategori'] ?? '-') ?></div>
                                <div class="text-xs text-slate-500"><?= htmlspecialchars($t['keterangan'] ?? '') ?></div>
                            </td>
                            <td class="px-6 py-4 align-top text-right">
                                <span class="font-mono font-bold text-sm <?= $t['tipe'] == 'masuk' ? 'text-emerald-600' : 'text-slate-800' ?>">
                                    <?= formatRupiah($t['jumlah']) ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 align-top text-right">
                                <?php if(($_SESSION['admin_role'] ?? 'admin') === 'admin'): ?>
                                <div class="flex items-center justify-end gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <a href="<?= APP_URL ?>/transaksi/edit/<?= $t['id'] ?>" class="p-1.5 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded transition-colors" title="Edit">
                                        <ion-icon name="create" class="text-lg"></ion-icon>
                                    </a>
                                    <form method="POST" action="<?= APP_URL ?>/transaksi/delete/<?= $t['id'] ?>" style="display: inline;" onsubmit="return confirm('Hapus transaksi ini?')">
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
            </table>
        <?php endif; ?>
    </div>
</div>

<script>
    function switchTab(tabId, btnElement) {
        // Hide all contents
        document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
        // Show selected
        document.getElementById('tab-' + tabId).classList.remove('hidden');
        
        // Reset buttons
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.classList.remove('text-blue-600', 'border-blue-600', 'active');
            btn.classList.add('text-slate-500', 'border-transparent');
        });
        
        // Activate button
        btnElement.classList.remove('text-slate-500', 'border-transparent');
        btnElement.classList.add('text-blue-600', 'border-blue-600', 'active');
    }

    // Auto-init first tab if needed, or check hash
    document.addEventListener('DOMContentLoaded', () => {
        const hash = window.location.hash.replace('#', '');
        if(hash && document.getElementById('tab-' + hash)) {
            const btn = Array.from(document.querySelectorAll('.tab-btn')).find(b => b.onclick.toString().includes(hash));
            if(btn) btn.click();
        } else {
            // Default is already set by HTML classes, but let's ensure
            document.getElementById('default-tab').click();
        }
    });
</script>
