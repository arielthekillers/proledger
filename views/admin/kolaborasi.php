<?php require_once VIEW_PATH . '/layout/header.php'; ?>

<div class="max-w-7xl mx-auto space-y-4">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h1 class="text-xl font-bold text-slate-800">Pengawasan Ruang Kolaborasi &amp; Link Publik</h1>
            <p class="text-xs text-slate-500 mt-0.5">Pantau semua ruang kolaborasi dan nonaktifkan paksa link monitoring publik jika diperlukan</p>
        </div>
        <form method="GET" action="<?= APP_URL ?>/admin/kolaborasi" class="flex items-center gap-2">
            <input type="text" name="search" value="<?= htmlspecialchars($search) ?>"
                   placeholder="Cari nama ruang, instansi, koordinator..."
                   class="px-3 py-1.5 bg-white border border-slate-200 rounded-xl text-xs w-64 focus:outline-none focus:border-blue-500">
            <button type="submit" class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-semibold cursor-pointer">Cari</button>
        </form>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200/80 overflow-hidden shadow-2xs">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-[10px] uppercase tracking-wider text-slate-500 font-bold border-b border-slate-200">
                    <tr>
                        <th class="py-3 px-4">Ruang Kolaborasi</th>
                        <th class="py-3 px-4">Koordinator</th>
                        <th class="py-3 px-4 text-center">Anggota / Group</th>
                        <th class="py-3 px-4 text-center">Link Monitoring Publik</th>
                        <th class="py-3 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php if (empty($kolabs)): ?>
                        <tr><td colspan="5" class="py-10 text-center text-slate-400">Belum ada ruang kolaborasi.</td></tr>
                    <?php else: ?>
                        <?php foreach ($kolabs as $k):
                            $pubOn = !empty($k['share_enabled']);
                            $pubUrl = !empty($k['share_token']) ? APP_URL . '/monitoring/' . rawurlencode($k['share_token']) : '';
                        ?>
                            <tr class="hover:bg-slate-50/70">
                                <td class="py-3 px-4">
                                    <div class="font-bold text-slate-800"><?= htmlspecialchars($k['nama']) ?></div>
                                    <div class="text-[11px] text-slate-400"><?= htmlspecialchars($k['instansi'] ?: '-') ?> &bull; TA <?= htmlspecialchars($k['tahun_anggaran'] ?: '-') ?></div>
                                </td>
                                <td class="py-3 px-4">
                                    <div class="font-semibold text-slate-700"><?= htmlspecialchars($k['owner_name'] ?: '-') ?></div>
                                    <div class="text-[11px] text-slate-400"><?= htmlspecialchars($k['owner_email'] ?: '-') ?></div>
                                </td>
                                <td class="py-3 px-4 text-center">
                                    <span class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-700 text-[11px] font-semibold">
                                        <?= (int)$k['member_count'] ?> Anggota &bull; <?= (int)$k['group_count'] ?> Group
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-center">
                                    <?php if ($pubOn && $pubUrl): ?>
                                        <div class="inline-flex items-center gap-1.5">
                                            <span class="px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 text-[10px] font-bold">Aktif</span>
                                            <a href="<?= $pubUrl ?>" target="_blank" class="text-blue-600 hover:underline text-[11px] font-semibold">Buka</a>
                                        </div>
                                    <?php else: ?>
                                        <span class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-500 text-[10px] font-bold">Nonaktif</span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-3 px-4 text-center whitespace-nowrap">
                                    <form method="POST" action="<?= APP_URL ?>/admin/toggleKolabShare/<?= (int)$k['id'] ?>" class="inline">
                                        <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
                                        <input type="hidden" name="enable" value="<?= $pubOn ? 0 : 1 ?>">
                                        <?php if ($pubOn): ?>
                                            <button type="submit" class="px-2.5 py-1 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 rounded-lg text-[11px] font-semibold cursor-pointer">
                                                Matikan Link Publik
                                            </button>
                                        <?php else: ?>
                                            <button type="submit" class="px-2.5 py-1 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 rounded-lg text-[11px] font-semibold cursor-pointer">
                                                Aktifkan Link Publik
                                            </button>
                                        <?php endif; ?>
                                    </form>
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
