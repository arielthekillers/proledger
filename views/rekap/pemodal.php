<div class="page-header">
    <div class="flex-between">
        <div>
            <h1 class="page-title"><?= htmlspecialchars($user['nama']) ?></h1>
            <p class="page-subtitle">Rekap portofolio investasi</p>
        </div>
        <div class="flex gap-2">
            <button onclick="window.print()" class="btn btn-secondary">🖨️ Print</button>
            <a href="<?= APP_URL ?>/rekap/export/pemodal/<?= $user['id'] ?>" class="btn btn-success">📥 Export CSV</a>
            <a href="<?= APP_URL ?>/pemodal/detail/<?= $user['id'] ?>" class="btn btn-outline">← Kembali</a>
        </div>
    </div>
</div>

<!-- Investor Info -->
<div class="card mb-3">
    <div class="grid grid-3">
        <div>
            <div class="stat-label">Email</div>
            <div style="font-size: 1.125rem; font-weight: 500;"><?= htmlspecialchars($user['email'] ?? '-') ?></div>
        </div>
        <div>
            <div class="stat-label">Telepon</div>
            <div style="font-size: 1.125rem; font-weight: 500;"><?= htmlspecialchars($user['telepon'] ?? '-') ?></div>
        </div>
        <div>
            <div class="stat-label">Jumlah Paket</div>
            <div style="font-size: 1.125rem; font-weight: 500;"><?= count($portfolio) ?> paket</div>
        </div>
    </div>
</div>

<!-- Portfolio Summary -->
<div class="grid grid-2 mb-4">
    <div class="stat-card accent">
        <div class="stat-label">Total Modal Disetor</div>
        <div class="stat-value"><?= formatRupiah($total_modal) ?></div>
        <div class="stat-change">Semua Paket</div>
    </div>
    
    <div class="stat-card <?= $total_estimated_laba_rugi >= 0 ? 'success' : 'danger' ?>">
        <div class="stat-label">Estimasi Laba/Rugi</div>
        <div class="stat-value"><?= formatRupiah($total_estimated_laba_rugi) ?></div>
        <div class="stat-change <?= $total_estimated_laba_rugi >= 0 ? 'positive' : 'negative' ?>">
            <?= $total_estimated_laba_rugi >= 0 ? '📈 Untung' : '📉 Rugi' ?>
        </div>
    </div>
</div>

<!-- Portfolio Details -->
<div class="card mb-4">
    <div class="card-header">
        <h2 class="card-title">Detail Portofolio</h2>
    </div>
    
    <?php if (empty($portfolio)): ?>
        <div class="text-center" style="padding: 3rem;">
            <p style="font-size: 3rem; margin-bottom: 1rem;">📊</p>
            <h3 style="color: var(--gray);">Belum ada investasi</h3>
            <p class="text-gray">Pemodal ini belum terdaftar di paket manapun</p>
        </div>
    <?php else: ?>
        <div class="grid grid-1 gap-3" style="padding: var(--spacing-md);">
            <?php foreach ($portfolio as $item): ?>
                <?php 
                    $p = $item['paket'];
                    $m = $item['modal'];
                    $s = $item['summary'];
                    $labaRugi = $item['estimated_laba_rugi'];
                ?>
                <div class="card card-glass">
                    <div class="flex-between mb-2">
                        <div>
                            <h3 style="margin-bottom: 0.5rem;"><?= htmlspecialchars($p['nama_paket']) ?></h3>
                            <p class="text-gray" style="font-size: 0.875rem;">
                                <?= $p['jumlah_pemodal'] ?> pemodal • <?= getStatusBadge($p['status']) ?>
                            </p>
                        </div>
                        <a href="<?= APP_URL ?>/paket/detail/<?= $p['id'] ?>" class="btn btn-sm btn-primary">Detail</a>
                    </div>
                    
                    <div class="grid grid-4 gap-2 mt-3">
                        <div>
                            <div class="stat-label" style="font-size: 0.75rem;">Modal Pasal</div>
                            <div style="font-size: 1rem; font-weight: 600;"><?= formatRupiah($m['modal_pasal']) ?></div>
                        </div>
                        <div>
                            <div class="stat-label" style="font-size: 0.75rem;">Modal Belanja</div>
                            <div style="font-size: 1rem; font-weight: 600;"><?= formatRupiah($m['modal_belanja']) ?></div>
                        </div>
                        <div>
                            <div class="stat-label" style="font-size: 0.75rem;">Total Modal</div>
                            <div style="font-size: 1rem; font-weight: 600; color: var(--accent);"><?= formatRupiah($m['total_modal']) ?></div>
                        </div>
                        <div>
                            <div class="stat-label" style="font-size: 0.75rem;">Est. Laba/Rugi</div>
                            <div style="font-size: 1rem; font-weight: 600; color: <?= $labaRugi >= 0 ? 'var(--success)' : 'var(--danger)' ?>;">
                                <?= formatRupiah($labaRugi) ?>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Package Financial Summary -->
                    <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid var(--light);">
                        <div class="grid grid-4 gap-2">
                            <div>
                                <div class="text-gray" style="font-size: 0.75rem;">Total Modal Paket</div>
                                <div style="font-size: 0.875rem; font-weight: 500;"><?= formatRupiah($s['total_modal']) ?></div>
                            </div>
                            <div>
                                <div class="text-gray" style="font-size: 0.75rem;">Uang Masuk</div>
                                <div style="font-size: 0.875rem; font-weight: 500; color: var(--success);"><?= formatRupiah($s['total_masuk']) ?></div>
                            </div>
                            <div>
                                <div class="text-gray" style="font-size: 0.75rem;">Uang Keluar</div>
                                <div style="font-size: 0.875rem; font-weight: 500; color: var(--danger);"><?= formatRupiah($s['total_keluar']) ?></div>
                            </div>
                            <div>
                                <div class="text-gray" style="font-size: 0.75rem;">Saldo Paket</div>
                                <div style="font-size: 0.875rem; font-weight: 500;"><?= formatRupiah($s['saldo']) ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<!-- Summary Table -->
<?php if (!empty($portfolio)): ?>
<div class="card">
    <div class="card-header">
        <h2 class="card-title">Ringkasan Investasi</h2>
    </div>
    
    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th>Nama Paket</th>
                    <th>Modal Pasal</th>
                    <th>Modal Belanja</th>
                    <th>Total Modal</th>
                    <th>Est. Laba/Rugi</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($portfolio as $item): ?>
                    <?php 
                        $p = $item['paket'];
                        $m = $item['modal'];
                        $labaRugi = $item['estimated_laba_rugi'];
                    ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($p['nama_paket']) ?></strong></td>
                        <td><?= formatRupiah($m['modal_pasal']) ?></td>
                        <td><?= formatRupiah($m['modal_belanja']) ?></td>
                        <td><strong><?= formatRupiah($m['total_modal']) ?></strong></td>
                        <td style="color: <?= $labaRugi >= 0 ? 'var(--success)' : 'var(--danger)' ?>;">
                            <strong><?= formatRupiah($labaRugi) ?></strong>
                        </td>
                        <td><?= getStatusBadge($p['status']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr style="background: var(--light); font-weight: bold;">
                    <td>TOTAL</td>
                    <td><?= formatRupiah(array_sum(array_column(array_column($portfolio, 'modal'), 'modal_pasal'))) ?></td>
                    <td><?= formatRupiah(array_sum(array_column(array_column($portfolio, 'modal'), 'modal_belanja'))) ?></td>
                    <td><?= formatRupiah($total_modal) ?></td>
                    <td style="color: <?= $total_estimated_laba_rugi >= 0 ? 'var(--success)' : 'var(--danger)' ?>;">
                        <?= formatRupiah($total_estimated_laba_rugi) ?>
                    </td>
                    <td>-</td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
<?php endif; ?>
