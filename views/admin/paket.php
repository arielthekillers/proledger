<?php require_once VIEW_PATH . '/layout/header.php'; ?>

<div class="max-w-7xl mx-auto space-y-4">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-3">
        <div>
            <h1 class="text-xl font-bold text-slate-800">Pencarian Paket Global</h1>
            <p class="text-xs text-slate-500 mt-0.5">Telusuri paket kegiatan lintas seluruh perusahaan berdasarkan kode, nama, tag, atau status (maks 150 hasil terbaru)</p>
        </div>
    </div>

    <form method="GET" action="<?= APP_URL ?>/admin/paket" class="bg-white rounded-2xl border border-slate-200/80 p-3 flex flex-wrap items-center gap-2.5 shadow-2xs">
        <input type="text" name="search" value="<?= htmlspecialchars($search) ?>"
               placeholder="Cari kode paket, nama paket, tag, atau pemilik..."
               class="px-3 py-1.5 bg-slate-50 border border-slate-200 rounded-xl text-xs flex-1 min-w-[220px] focus:outline-none focus:border-blue-500">
        <select name="perusahaan_id" class="px-3 py-1.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700">
            <option value="0">Semua Perusahaan</option>
            <?php foreach ($companies as $comp): ?>
                <option value="<?= (int)$comp['id'] ?>" <?= $perusahaanId === (int)$comp['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($comp['nama']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <select name="status" class="px-3 py-1.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700">
            <option value="">Semua Status</option>
            <option value="belum" <?= $status === 'belum' ? 'selected' : '' ?>>Belum</option>
            <option value="proses" <?= $status === 'proses' ? 'selected' : '' ?>>Proses</option>
            <option value="selesai" <?= $status === 'selesai' ? 'selected' : '' ?>>Cair / Selesai</option>
        </select>
        <button type="submit" class="px-4 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-semibold cursor-pointer">Filter</button>
        <a href="<?= APP_URL ?>/admin/paket" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs font-semibold">Reset</a>
    </form>

    <div class="bg-white rounded-2xl border border-slate-200/80 overflow-hidden shadow-2xs">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-[10px] uppercase tracking-wider text-slate-500 font-bold border-b border-slate-200">
                    <tr>
                        <th class="py-3 px-4">Kode</th>
                        <th class="py-3 px-4">Nama Paket</th>
                        <th class="py-3 px-4">Perusahaan &amp; Pemilik</th>
                        <th class="py-3 px-4 text-right">Nilai Kontrak</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-center">Support</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php if (empty($pakets)): ?>
                        <tr><td colspan="6" class="py-10 text-center text-slate-400">Tidak ada paket yang cocok dengan filter.</td></tr>
                    <?php else: ?>
                        <?php foreach ($pakets as $pk): ?>
                            <tr class="hover:bg-slate-50/70">
                                <td class="py-3 px-4 font-mono font-bold text-slate-700 whitespace-nowrap">
                                    <?= htmlspecialchars($pk['kode_paket'] ?: ('#' . $pk['id'])) ?>
                                </td>
                                <td class="py-3 px-4">
                                    <div class="font-bold text-slate-800"><?= htmlspecialchars($pk['nama_paket']) ?></div>
                                    <div class="text-[11px] text-slate-400 flex flex-wrap items-center gap-1.5 mt-0.5">
                                        <?php if (!empty($pk['tahun_anggaran'])): ?>
                                            <span class="px-1.5 py-0.2 bg-amber-50 text-amber-700 rounded border border-amber-200/70 text-[10px] font-semibold">TA <?= htmlspecialchars($pk['tahun_anggaran']) ?></span>
                                        <?php endif; ?>
                                        <?php if (!empty($pk['nama_group'])): ?>
                                            <span class="px-1.5 py-0.2 bg-emerald-50 text-emerald-700 rounded text-[10px] font-semibold"><?= htmlspecialchars($pk['nama_group']) ?></span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="py-3 px-4">
                                    <div class="font-semibold text-slate-700"><?= htmlspecialchars($pk['perusahaan_nama'] ?: ($pk['perusahaan'] ?: '-')) ?></div>
                                    <div class="text-[11px] text-slate-400"><?= htmlspecialchars($pk['owner_name'] ?: '-') ?></div>
                                </td>
                                <td class="py-3 px-4 text-right font-mono font-bold text-slate-800 whitespace-nowrap">
                                    <?= formatRupiah($pk['nilai_kontrak']) ?>
                                </td>
                                <td class="py-3 px-4 text-center whitespace-nowrap">
                                    <?= getStatusBadge($pk['status']) ?>
                                </td>
                                <td class="py-3 px-4 text-center whitespace-nowrap">
                                    <?php if (!empty($pk['owner_user_id'])): ?>
                                        <form method="POST" action="<?= APP_URL ?>/admin/impersonate/<?= (int)$pk['owner_user_id'] ?>" class="inline">
                                            <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
                                            <input type="hidden" name="perusahaan_id" value="<?= (int)$pk['perusahaan_id'] ?>">
                                            <button type="submit" class="px-2.5 py-1 bg-amber-50 hover:bg-amber-100 text-amber-800 border border-amber-200 rounded-lg text-[11px] font-semibold cursor-pointer">
                                                Login As Pemilik
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <span class="text-slate-300">-</span>
                                    <?php endif; ?>
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
