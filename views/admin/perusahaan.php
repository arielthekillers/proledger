<?php require_once VIEW_PATH . '/layout/header.php'; ?>

<div class="max-w-7xl mx-auto space-y-4">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h1 class="text-xl font-bold text-slate-800">Direktori Perusahaan</h1>
            <p class="text-xs text-slate-500 mt-0.5">Daftar seluruh perusahaan terdaftar beserta pemilik, jumlah paket, dan akses mode support</p>
        </div>
        <form method="GET" action="<?= APP_URL ?>/admin/perusahaan" class="flex items-center gap-2">
            <input type="text" name="search" value="<?= htmlspecialchars($search) ?>"
                   placeholder="Cari perusahaan, direktur, pemilik..."
                   class="px-3 py-1.5 bg-white border border-slate-200 rounded-xl text-xs w-64 focus:outline-none focus:border-blue-500">
            <button type="submit" class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-semibold cursor-pointer">Cari</button>
        </form>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200/80 overflow-hidden shadow-2xs">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-[10px] uppercase tracking-wider text-slate-500 font-bold border-b border-slate-200">
                    <tr>
                        <th class="py-3 px-4">Perusahaan</th>
                        <th class="py-3 px-4">Direktur / Kontak</th>
                        <th class="py-3 px-4">Pemilik Akun</th>
                        <th class="py-3 px-4 text-center">Paket</th>
                        <th class="py-3 px-4 text-right">Total Kontrak</th>
                        <th class="py-3 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php if (empty($companies)): ?>
                        <tr><td colspan="6" class="py-10 text-center text-slate-400">Belum ada data perusahaan.</td></tr>
                    <?php else: ?>
                        <?php foreach ($companies as $c): ?>
                            <tr class="hover:bg-slate-50/70">
                                <td class="py-3 px-4">
                                    <div class="font-bold text-slate-800"><?= htmlspecialchars($c['nama']) ?></div>
                                    <?php if (!empty($c['alamat'])): ?>
                                        <div class="text-[11px] text-slate-400 truncate max-w-xs"><?= htmlspecialchars($c['alamat']) ?></div>
                                    <?php endif; ?>
                                </td>
                                <td class="py-3 px-4">
                                    <div class="text-slate-700 font-medium"><?= htmlspecialchars($c['nama_direktur'] ?: '-') ?></div>
                                    <div class="text-[11px] text-slate-400"><?= htmlspecialchars($c['telepon'] ?: ($c['email'] ?: '-')) ?></div>
                                </td>
                                <td class="py-3 px-4">
                                    <div class="font-semibold text-slate-700"><?= htmlspecialchars($c['owner_name'] ?: '-') ?></div>
                                    <div class="text-[11px] text-slate-400"><?= htmlspecialchars($c['owner_email'] ?: '-') ?></div>
                                </td>
                                <td class="py-3 px-4 text-center">
                                    <a href="<?= APP_URL ?>/admin/paket?perusahaan_id=<?= (int)$c['id'] ?>"
                                       class="inline-flex items-center px-2 py-0.5 rounded-full bg-blue-50 text-blue-700 font-bold text-[11px] hover:bg-blue-100">
                                        <?= (int)$c['total_paket'] ?> Paket
                                    </a>
                                </td>
                                <td class="py-3 px-4 text-right font-mono font-bold text-slate-800">
                                    <?= formatRupiah($c['total_kontrak']) ?>
                                </td>
                                <td class="py-3 px-4 text-center whitespace-nowrap">
                                    <div class="inline-flex items-center gap-1.5">
                                        <a href="<?= APP_URL ?>/admin/paket?perusahaan_id=<?= (int)$c['id'] ?>"
                                           class="px-2.5 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-[11px] font-semibold">
                                            Lihat Paket
                                        </a>
                                        <?php if (!empty($c['user_id'])): ?>
                                            <form method="POST" action="<?= APP_URL ?>/admin/impersonate/<?= (int)$c['user_id'] ?>" class="inline"
                                                  onsubmit="return confirm('Masuk ke mode support sebagai <?= htmlspecialchars(addslashes($c['owner_name'] ?? 'User')) ?>?')">
                                                <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
                                                <input type="hidden" name="perusahaan_id" value="<?= (int)$c['id'] ?>">
                                                <button type="submit" class="px-2.5 py-1 bg-amber-50 hover:bg-amber-100 text-amber-800 border border-amber-200 rounded-lg text-[11px] font-semibold cursor-pointer">
                                                    Login As
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once VIEW_PATH . '/layout/footer.php'; ?>
