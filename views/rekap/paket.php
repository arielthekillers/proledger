<!-- Header & Back -->
<div class="mb-6 flex items-center justify-between">
    <?php 
    $backUrl = APP_URL . '/rekap';
    $backLabel = 'Kembali ke Rekap';
    if(isset($_GET['from']) && $_GET['from'] == 'paket') {
        $backUrl = APP_URL . '/paket/detail/' . $paket['id'];
        $backLabel = 'Kembali ke Paket';
    }
    ?>
    <a href="<?= $backUrl ?>" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-slate-200 rounded-xl text-slate-600 hover:bg-slate-50 hover:text-blue-600 transition-colors shadow-sm">
        <ion-icon name="arrow-back-outline"></ion-icon>
        <span><?= $backLabel ?></span>
    </a>
    <div class="flex gap-3">
        <a href="<?= APP_URL ?>/rekap/export/paket/<?= $paket['id'] ?>" class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 text-white rounded-xl hover:bg-emerald-700 transition-colors shadow-sm font-medium">
            <ion-icon name="download-outline"></ion-icon>
            <span>Export CSV</span>
        </a>
        <button onclick="window.print()" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-slate-200 rounded-xl text-slate-600 hover:bg-slate-50 transition-colors shadow-sm">
            <ion-icon name="print-outline"></ion-icon>
            <span>Print</span>
        </button>
    </div>
</div>

<!-- Title & Info -->
<div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 mb-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 mb-1"><?= htmlspecialchars($paket['nama_paket']) ?></h1>
            <div class="flex items-center gap-2 text-slate-500 text-sm">
                <span>Created at <?= formatTanggal($paket['created_at']) ?></span>
                <span>•</span>
                <?= getStatusBadge($paket['status']) ?>
            </div>
        </div>
        <div class="flex gap-4 md:gap-8">
            <div>
                <div class="text-xs uppercase tracking-wide text-slate-500 font-semibold mb-1">Nilai Pagu</div>
                <div class="text-lg font-bold text-slate-800"><?= formatRupiah($paket['pagu']) ?></div>
            </div>
            <div>
                <div class="text-xs uppercase tracking-wide text-slate-500 font-semibold mb-1">Nilai RAB</div>
                <div class="text-lg font-bold text-purple-600"><?= formatRupiah($paket['rab']) ?></div>
            </div>
        </div>
    </div>
</div>

<!-- Financial Stats Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Total Modal -->
    <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl p-6 text-white shadow-lg">
        <div class="flex items-center justify-between mb-4">
            <span class="text-blue-100 text-sm font-medium">Total Modal</span>
            <div class="w-8 h-8 rounded-lg bg-white/20 flex items-center justify-center">
                <ion-icon name="wallet-outline"></ion-icon>
            </div>
        </div>
        <div class="text-2xl font-bold mb-1"><?= formatRupiah($summary['total_modal']) ?></div>
        <div class="text-sm text-blue-100">Dari <?= $paket['jumlah_pemodal'] ?> investor</div>
    </div>
    
    <!-- Uang Masuk -->
    <div class="bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-2xl p-6 text-white shadow-lg">
        <div class="flex items-center justify-between mb-4">
            <span class="text-emerald-100 text-sm font-medium">Uang Masuk</span>
            <div class="w-8 h-8 rounded-lg bg-white/20 flex items-center justify-center">
                <ion-icon name="arrow-down-outline"></ion-icon>
            </div>
        </div>
        <div class="text-2xl font-bold mb-1"><?= formatRupiah($summary['total_masuk']) ?></div>
        <div class="text-sm text-emerald-100">Total pendapatan</div>
    </div>
    
    <!-- Uang Keluar -->
    <div class="bg-gradient-to-br from-rose-500 to-rose-600 rounded-2xl p-6 text-white shadow-lg">
        <div class="flex items-center justify-between mb-4">
            <span class="text-rose-100 text-sm font-medium">Uang Keluar</span>
            <div class="w-8 h-8 rounded-lg bg-white/20 flex items-center justify-center">
                <ion-icon name="arrow-up-outline"></ion-icon>
            </div>
        </div>
        <div class="text-2xl font-bold mb-1"><?= formatRupiah($summary['total_keluar']) ?></div>
        <div class="text-sm text-rose-100">Total belanja</div>
    </div>
    
    <!-- Laba/Rugi -->
    <div class="bg-gradient-to-br from-slate-700 to-slate-800 rounded-2xl p-6 text-white shadow-lg">
        <div class="flex items-center justify-between mb-4">
            <span class="text-slate-300 text-sm font-medium">Net Laba/Rugi</span>
            <div class="w-8 h-8 rounded-lg bg-white/20 flex items-center justify-center">
                <ion-icon name="<?= $summary['laba_rugi'] >= 0 ? 'trending-up-outline' : 'trending-down-outline' ?>"></ion-icon>
            </div>
        </div>
        <div class="text-2xl font-bold mb-1 <?= $summary['laba_rugi'] >= 0 ? 'text-emerald-400' : 'text-rose-400' ?>">
            <?= formatRupiah($summary['laba_rugi']) ?>
        </div>
        <div class="text-sm text-slate-400">Profitabilitas</div>
    </div>
</div>

<!-- Charts Section -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
        <h3 class="font-bold text-slate-800 mb-6 flex items-center gap-2">
            <ion-icon name="pie-chart-outline" class="text-blue-500"></ion-icon>
            Pengeluaran per Kategori
        </h3>
        <div class="aspect-square max-h-[300px] flex items-center justify-center mx-auto">
            <canvas id="categoryChart"></canvas>
        </div>
    </div>
    
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
        <h3 class="font-bold text-slate-800 mb-6 flex items-center gap-2">
            <ion-icon name="bar-chart-outline" class="text-blue-500"></ion-icon>
            Arus Kas Bulanan
        </h3>
        <div class="aspect-video max-h-[300px] w-full">
            <canvas id="incomeExpenseChart"></canvas>
        </div>
    </div>
</div>

<!-- Modal Distribution -->
<div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden mb-8">
    <div class="p-6 border-b border-slate-100">
        <h3 class="font-bold text-slate-800">Distribusi Modal & Kepemilikan</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-slate-50/50 border-b border-slate-200">
                    <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase">Nama Pemodal</th>
                    <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase text-right">Modal Pasal</th>
                    <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase text-right">Modal Belanja</th>
                    <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase text-right">Total Modal</th>
                    <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase text-right">% Saham</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($modal_detail as $m): ?>
                    <?php $ownership = ($m['total_modal'] / ($summary['total_modal'] ?: 1)) * 100; ?>
                    <tr class="border-b border-slate-50 hover:bg-slate-50/50 transition-colors">
                        <td class="px-6 py-4 font-semibold text-slate-800"><?= htmlspecialchars($m['nama']) ?></td>
                        <td class="px-6 py-4 text-right text-slate-600 font-mono"><?= formatRupiah($m['modal_pasal']) ?></td>
                        <td class="px-6 py-4 text-right text-slate-600 font-mono"><?= formatRupiah($m['modal_belanja']) ?></td>
                        <td class="px-6 py-4 text-right text-slate-800 font-mono font-bold"><?= formatRupiah($m['total_modal']) ?></td>
                        <td class="px-6 py-4 text-right">
                            <span class="bg-blue-100 text-blue-700 px-2 py-1 rounded text-xs font-bold"><?= number_format($ownership, 2) ?>%</span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot class="bg-slate-50 font-bold border-t border-slate-200">
                <tr>
                    <td class="px-6 py-4 text-slate-800">TOTAL</td>
                    <td class="px-6 py-4 text-right text-slate-800 font-mono"><?= formatRupiah(array_sum(array_column($modal_detail, 'modal_pasal'))) ?></td>
                    <td class="px-6 py-4 text-right text-slate-800 font-mono"><?= formatRupiah(array_sum(array_column($modal_detail, 'modal_belanja'))) ?></td>
                    <td class="px-6 py-4 text-right text-blue-600 font-mono text-lg"><?= formatRupiah($summary['total_modal']) ?></td>
                    <td class="px-6 py-4 text-right">100%</td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<!-- Transaction History -->
<div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
    <div class="p-6 border-b border-slate-100 flex items-center justify-between">
        <h3 class="font-bold text-slate-800">Riwayat Pembukuan</h3>
        <span class="text-sm text-slate-500"><?= count($transaksi) ?> transaksi</span>
    </div>
    
    <?php if (empty($transaksi)): ?>
        <div class="text-center py-12">
            <p class="text-slate-400">Belum ada transaksi</p>
        </div>
    <?php else: ?>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-slate-50/50 border-b border-slate-200">
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase">Tanggal</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase">Tipe</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase">Kategori</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase text-right">Jumlah</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase">Keterangan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php foreach ($transaksi as $t): ?>
                        <tr class="hover:bg-slate-50/50">
                            <td class="px-6 py-3 text-slate-600 font-mono text-sm"><?= formatTanggal($t['tanggal']) ?></td>
                            <td class="px-6 py-3"><?= getTipeBadge($t['tipe']) ?></td>
                            <td class="px-6 py-3 text-slate-700 text-sm"><?= htmlspecialchars($t['kategori'] ?? '-') ?></td>
                            <td class="px-6 py-3 text-right font-mono font-bold <?= $t['tipe'] === 'masuk' ? 'text-emerald-600' : 'text-rose-600' ?>">
                                <?= formatRupiah($t['jumlah']) ?>
                            </td>
                            <td class="px-6 py-3 text-slate-500 text-sm"><?= htmlspecialchars($t['keterangan'] ?? '-') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<!-- Charts Script -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Category Pie Chart
const catCtx = document.getElementById('categoryChart');
if (catCtx) {
    const catData = <?= json_encode($transaksi_by_kategori ?: []) ?>;
    if (catData.length > 0) {
        new Chart(catCtx, {
            type: 'doughnut',
            data: {
                labels: catData.map(d => d.kategori),
                datasets: [{
                    data: catData.map(d => d.total),
                    backgroundColor: ['#ef4444', '#f59e0b', '#10b981', '#3b82f6', '#8b5cf6', '#ec4899'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', labels: { boxWidth: 12, padding: 20 } }
                },
                cutout: '70%'
            }
        });
    } else {
        catCtx.parentElement.innerHTML = '<p class="text-center text-slate-400 text-sm">Belum ada data</p>';
    }
}

// Income/Expense Bar Chart
const cashCtx = document.getElementById('incomeExpenseChart');
if (cashCtx) {
    const cashData = <?= json_encode($transaksi_by_month ?: []) ?>;
    if (cashData.length > 0) {
        new Chart(cashCtx, {
            type: 'bar',
            data: {
                labels: cashData.map(d => d.month),
                datasets: [
                    { label: 'Masuk', data: cashData.map(d => d.masuk), backgroundColor: '#10b981', borderRadius: 6 },
                    { label: 'Keluar', data: cashData.map(d => d.keluar), backgroundColor: '#f43f5e', borderRadius: 6 }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: { beginAtZero: true, grid: { borderDash: [2, 4] } },
                    x: { grid: { display: false } }
                },
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });
    } else {
        cashCtx.parentElement.innerHTML = '<p class="text-center text-slate-400 text-sm">Belum ada data</p>';
    }
}
</script>
