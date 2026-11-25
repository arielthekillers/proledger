<?php require_once VIEW_PATH . '/layout/header.php'; ?>

<div class="max-w-7xl mx-auto space-y-5">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h1 class="text-xl font-bold text-slate-800 tracking-tight">Dashboard Administrator</h1>
            <p class="text-xs text-slate-500 mt-0.5">Ringkasan operasional lintas pengguna, perusahaan, paket, kolaborasi, dan sistem</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <a href="<?= APP_URL ?>/admin/paket" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white border border-slate-200 hover:border-blue-300 text-slate-700 rounded-xl text-xs font-semibold transition-all shadow-2xs">
                <ion-icon name="search-outline" class="text-sm text-blue-600"></ion-icon>
                Cari Paket Global
            </a>
            <a href="<?= APP_URL ?>/admin/settings" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-semibold transition-all shadow-xs">
                <ion-icon name="megaphone-outline" class="text-sm"></ion-icon>
                Pengumuman &amp; Pengaturan
            </a>
        </div>
    </div>

    <!-- KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3.5">
        <a href="<?= APP_URL ?>/user" class="bg-white rounded-2xl border border-slate-200/80 p-4 shadow-2xs hover:border-blue-300 transition-all">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Pengguna</span>
                <span class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center"><ion-icon name="people-outline" class="text-lg"></ion-icon></span>
            </div>
            <div class="text-2xl font-bold text-slate-800"><?= number_format($stats['users_total']) ?></div>
            <div class="mt-1.5 flex items-center gap-2 text-[11px]">
                <span class="text-emerald-600 font-semibold"><?= $stats['users_active'] ?> Aktif</span>
                <?php if ($stats['users_pending'] > 0): ?>
                    <span class="px-1.5 py-0.2 bg-amber-100 text-amber-800 rounded-full font-bold"><?= $stats['users_pending'] ?> Pending</span>
                <?php else: ?>
                    <span class="text-slate-400">0 Pending</span>
                <?php endif; ?>
            </div>
        </a>

        <a href="<?= APP_URL ?>/admin/perusahaan" class="bg-white rounded-2xl border border-slate-200/80 p-4 shadow-2xs hover:border-indigo-300 transition-all">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Perusahaan</span>
                <span class="w-8 h-8 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center"><ion-icon name="business-outline" class="text-lg"></ion-icon></span>
            </div>
            <div class="text-2xl font-bold text-slate-800"><?= number_format($stats['perusahaan_total']) ?></div>
            <div class="mt-1.5 text-[11px] text-slate-500">
                <?= $stats['group_total'] ?> Group &bull; <?= $stats['delegasi_total'] ?> Delegasi
            </div>
        </a>

        <a href="<?= APP_URL ?>/admin/paket" class="bg-white rounded-2xl border border-slate-200/80 p-4 shadow-2xs hover:border-emerald-300 transition-all">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Total Paket</span>
                <span class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center"><ion-icon name="cube-outline" class="text-lg"></ion-icon></span>
            </div>
            <div class="text-2xl font-bold text-slate-800"><?= number_format($stats['paket_total']) ?></div>
            <div class="mt-1.5 flex items-center gap-2 text-[11px]">
                <span class="text-emerald-600 font-semibold"><?= $stats['paket_selesai'] ?> Cair</span>
                <span class="text-rose-600 font-semibold"><?= $stats['paket_proses'] ?> Proses</span>
                <span class="text-slate-400"><?= $stats['paket_belum'] ?> Belum</span>
            </div>
        </a>

        <div class="bg-white rounded-2xl border border-slate-200/80 p-4 shadow-2xs">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Nilai Kontrak</span>
                <span class="w-8 h-8 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center"><ion-icon name="cash-outline" class="text-lg"></ion-icon></span>
            </div>
            <div class="text-lg font-bold font-mono text-slate-800 truncate" title="<?= formatRupiah($stats['total_kontrak']) ?>">
                <?= formatRupiah($stats['total_kontrak']) ?>
            </div>
            <div class="mt-1.5 text-[11px] text-slate-500 truncate">
                Setor Pasal: <span class="font-semibold text-amber-700"><?= formatRupiah($stats['total_setor_pasal']) ?></span>
            </div>
        </div>

        <a href="<?= APP_URL ?>/admin/kolaborasi" class="bg-white rounded-2xl border border-slate-200/80 p-4 shadow-2xs hover:border-purple-300 transition-all">
            <div class="flex items-center justify-between mb-2">
                <span class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider">Kolaborasi &amp; Storage</span>
                <span class="w-8 h-8 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center"><ion-icon name="layers-outline" class="text-lg"></ion-icon></span>
            </div>
            <div class="text-2xl font-bold text-slate-800"><?= number_format($stats['kolab_total']) ?> <span class="text-xs font-normal text-slate-400">Ruang</span></div>
            <div class="mt-1.5 text-[11px] text-slate-500">
                <span class="text-emerald-600 font-semibold"><?= $stats['kolab_public'] ?> Link Publik</span> &bull; <?= formatBytes($storageStats['total_bytes']) ?>
            </div>
        </a>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200/80 p-4 shadow-2xs">
            <div class="flex items-center justify-between mb-3">
                <div>
                    <h2 class="text-sm font-bold text-slate-800">Tren Paket Baru (6 Bulan Terakhir)</h2>
                    <p class="text-[11px] text-slate-400">Jumlah paket kegiatan yang dibuat per bulan di seluruh perusahaan</p>
                </div>
            </div>
            <div class="h-56">
                <canvas id="adminTrendChart"></canvas>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-200/80 p-4 shadow-2xs flex flex-col">
            <h2 class="text-sm font-bold text-slate-800 mb-1">Status Pencairan Paket</h2>
            <p class="text-[11px] text-slate-400 mb-3">Distribusi status seluruh paket di sistem</p>
            <div class="h-44 flex-1 flex items-center justify-center">
                <canvas id="adminStatusChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Pending Approvals & Recent Activity Logs -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <!-- Pending Approvals -->
        <div class="bg-white rounded-2xl border border-slate-200/80 overflow-hidden shadow-2xs">
            <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full <?= !empty($pendingUsers) ? 'bg-amber-500 animate-pulse' : 'bg-emerald-500' ?>"></span>
                    <h2 class="text-sm font-bold text-slate-800">Persetujuan Pengguna Baru</h2>
                </div>
                <a href="<?= APP_URL ?>/user?tab=pending" class="text-[11px] font-semibold text-blue-600 hover:underline">Lihat Semua</a>
            </div>
            <?php if (empty($pendingUsers)): ?>
                <div class="p-8 text-center text-xs text-slate-400">
                    Tidak ada permohonan pendaftaran user yang menunggu persetujuan.
                </div>
            <?php else: ?>
                <div class="divide-y divide-slate-100">
                    <?php foreach ($pendingUsers as $pu): ?>
                        <div class="px-4 py-3 flex items-center justify-between gap-3">
                            <div class="min-w-0">
                                <div class="font-semibold text-slate-800 text-xs truncate"><?= htmlspecialchars($pu['name']) ?></div>
                                <div class="text-[11px] text-slate-500 truncate"><?= htmlspecialchars($pu['email']) ?> &bull; <?= date('d M Y H:i', strtotime($pu['created_at'])) ?></div>
                            </div>
                            <div class="flex items-center gap-1.5 shrink-0">
                                <form action="<?= APP_URL ?>/user/approve/<?= $pu['id'] ?>" method="POST" class="inline">
                                    <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
                                    <button type="submit" class="px-2.5 py-1 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-[11px] font-semibold cursor-pointer">Setujui</button>
                                </form>
                                <form action="<?= APP_URL ?>/user/reject/<?= $pu['id'] ?>" method="POST" class="inline">
                                    <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
                                    <button type="submit" class="px-2.5 py-1 bg-slate-100 hover:bg-rose-100 text-slate-600 hover:text-rose-700 rounded-lg text-[11px] font-semibold cursor-pointer">Tolak</button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Recent Activity Logs -->
        <div class="bg-white rounded-2xl border border-slate-200/80 overflow-hidden shadow-2xs">
            <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between">
                <h2 class="text-sm font-bold text-slate-800">Aktivitas Sistem Terbaru</h2>
                <a href="<?= APP_URL ?>/admin/logs" class="text-[11px] font-semibold text-blue-600 hover:underline">Buka Audit Log</a>
            </div>
            <?php if (empty($recentLogs)): ?>
                <div class="p-8 text-center text-xs text-slate-400">
                    Belum ada log aktivitas yang tercatat.
                </div>
            <?php else: ?>
                <div class="divide-y divide-slate-100 max-h-72 overflow-y-auto">
                    <?php foreach ($recentLogs as $lg): ?>
                        <div class="px-4 py-2.5 flex items-start justify-between gap-3 text-xs">
                            <div class="min-w-0">
                                <div class="flex items-center gap-1.5">
                                    <span class="px-1.5 py-0.2 rounded bg-slate-100 text-slate-700 text-[10px] font-bold uppercase"><?= htmlspecialchars($lg['module']) ?></span>
                                    <span class="font-semibold text-slate-800 truncate"><?= htmlspecialchars($lg['user_name'] ?? 'System') ?></span>
                                </div>
                                <div class="text-[11px] text-slate-500 mt-0.5 truncate"><?= htmlspecialchars($lg['description']) ?></div>
                            </div>
                            <span class="text-[10px] text-slate-400 whitespace-nowrap shrink-0"><?= date('d/m H:i', strtotime($lg['created_at'])) ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const trendCtx = document.getElementById('adminTrendChart');
    if (trendCtx) {
        new Chart(trendCtx, {
            type: 'bar',
            data: {
                labels: <?= json_encode($stats['monthly_labels']) ?>,
                datasets: [{
                    label: 'Paket Baru',
                    data: <?= json_encode($stats['monthly_paket']) ?>,
                    backgroundColor: 'rgba(37, 99, 235, 0.85)',
                    borderRadius: 8,
                    maxBarThickness: 36
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, ticks: { precision: 0, font: { size: 11 } }, grid: { color: '#f1f5f9' } },
                    x: { ticks: { font: { size: 11 } }, grid: { display: false } }
                }
            }
        });
    }

    const statusCtx = document.getElementById('adminStatusChart');
    if (statusCtx) {
        new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: ['Cair / Selesai', 'Proses', 'Belum'],
                datasets: [{
                    data: [<?= (int)$stats['paket_selesai'] ?>, <?= (int)$stats['paket_proses'] ?>, <?= (int)$stats['paket_belum'] ?>],
                    backgroundColor: ['#10b981', '#f43f5e', '#94a3b8'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '66%',
                plugins: {
                    legend: { position: 'bottom', labels: { boxWidth: 10, font: { size: 11 } } }
                }
            }
        });
    }
});
</script>

<?php require_once VIEW_PATH . '/layout/footer.php'; ?>
