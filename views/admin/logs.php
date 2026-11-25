<?php require_once VIEW_PATH . '/layout/header.php'; ?>

<div class="max-w-7xl mx-auto space-y-4">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h1 class="text-xl font-bold text-slate-800">Log Aktivitas Sistem</h1>
            <p class="text-xs text-slate-500 mt-0.5">Rekam jejak login, persetujuan user, perubahan sensitif, setoran pasal, dan aksi administrator (maks 150 terbaru)</p>
        </div>
        <form method="POST" action="<?= APP_URL ?>/admin/clearOldLogs" onsubmit="return confirm('Hapus log aktivitas yang lebih lama dari 90 hari?')">
            <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
            <button type="submit" class="px-3 py-1.5 bg-white border border-slate-200 hover:border-rose-300 hover:text-rose-700 text-slate-600 rounded-xl text-xs font-semibold cursor-pointer">
                Bersihkan Log &gt; 90 Hari
            </button>
        </form>
    </div>

    <form method="GET" action="<?= APP_URL ?>/admin/logs" class="bg-white rounded-2xl border border-slate-200/80 p-3 flex flex-wrap items-center gap-2.5 shadow-2xs">
        <input type="text" name="search" value="<?= htmlspecialchars($filter['search'] ?? '') ?>"
               placeholder="Cari user, deskripsi, target ID, IP..."
               class="px-3 py-1.5 bg-slate-50 border border-slate-200 rounded-xl text-xs flex-1 min-w-[220px] focus:outline-none focus:border-blue-500">
        <select name="module" class="px-3 py-1.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700">
            <option value="">Semua Modul</option>
            <?php foreach ($modules as $mod): ?>
                <option value="<?= htmlspecialchars($mod) ?>" <?= ($filter['module'] ?? '') === $mod ? 'selected' : '' ?>>
                    <?= htmlspecialchars(strtoupper($mod)) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <button type="submit" class="px-4 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-semibold cursor-pointer">Filter</button>
        <a href="<?= APP_URL ?>/admin/logs" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs font-semibold">Reset</a>
    </form>

    <div class="bg-white rounded-2xl border border-slate-200/80 overflow-hidden shadow-2xs">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-[10px] uppercase tracking-wider text-slate-500 font-bold border-b border-slate-200">
                    <tr>
                        <th class="py-3 px-4">Waktu</th>
                        <th class="py-3 px-4">Pengguna</th>
                        <th class="py-3 px-4">Modul / Aksi</th>
                        <th class="py-3 px-4">Deskripsi</th>
                        <th class="py-3 px-4">IP</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php if (empty($logs)): ?>
                        <tr><td colspan="5" class="py-10 text-center text-slate-400">Belum ada log aktivitas.</td></tr>
                    <?php else: ?>
                        <?php foreach ($logs as $lg): ?>
                            <tr class="hover:bg-slate-50/70">
                                <td class="py-2.5 px-4 text-[11px] text-slate-500 whitespace-nowrap font-mono">
                                    <?= date('d/m/Y H:i:s', strtotime($lg['created_at'])) ?>
                                </td>
                                <td class="py-2.5 px-4">
                                    <div class="font-semibold text-slate-800"><?= htmlspecialchars($lg['user_name'] ?: '-') ?></div>
                                    <div class="text-[10px] text-slate-400 uppercase"><?= htmlspecialchars($lg['user_role'] ?: '-') ?></div>
                                </td>
                                <td class="py-2.5 px-4 whitespace-nowrap">
                                    <span class="px-1.5 py-0.5 rounded bg-blue-50 text-blue-700 text-[10px] font-bold uppercase"><?= htmlspecialchars($lg['module']) ?></span>
                                    <span class="ml-1 text-[11px] font-mono text-slate-600"><?= htmlspecialchars($lg['action']) ?></span>
                                </td>
                                <td class="py-2.5 px-4 text-slate-700">
                                    <?= htmlspecialchars($lg['description']) ?>
                                    <?php if (!empty($lg['target_id'])): ?>
                                        <span class="ml-1 text-[10px] font-mono text-slate-400">#<?= htmlspecialchars($lg['target_id']) ?></span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-2.5 px-4 text-[11px] font-mono text-slate-400 whitespace-nowrap">
                                    <?= htmlspecialchars($lg['ip_address'] ?: '-') ?>
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
