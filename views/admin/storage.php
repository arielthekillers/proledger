<?php require_once VIEW_PATH . '/layout/header.php'; ?>

<div class="max-w-7xl mx-auto space-y-5">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h1 class="text-xl font-bold text-slate-800">Monitor Storage Upload</h1>
            <p class="text-xs text-slate-500 mt-0.5">Pemakaian disk folder <code class="bg-slate-100 px-1.5 py-0.5 rounded">public/uploads</code> dan pembersihan file tidak terpakai</p>
        </div>
        <a href="<?= APP_URL ?>/admin/storage?scan=1"
           class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-semibold shadow-xs">
            <ion-icon name="scan-outline" class="text-base"></ion-icon>
            Pindai File Yatim (Orphan)
        </a>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white rounded-2xl border border-slate-200/80 p-4 shadow-2xs">
            <div class="text-[11px] font-semibold text-slate-500 uppercase">Total Pemakaian Storage</div>
            <div class="text-2xl font-bold text-slate-800 mt-1"><?= formatBytes($storageStats['total_bytes']) ?></div>
            <div class="text-[11px] text-slate-400 mt-1"><?= number_format($storageStats['total_files']) ?> file tersimpan</div>
        </div>
        <?php foreach ($storageStats['folders'] as $f): ?>
            <div class="bg-white rounded-2xl border border-slate-200/80 p-4 shadow-2xs">
                <div class="text-[11px] font-semibold text-slate-500 uppercase">Folder /<?= htmlspecialchars($f['name']) ?></div>
                <div class="text-xl font-bold text-slate-800 mt-1"><?= formatBytes($f['bytes']) ?></div>
                <div class="text-[11px] text-slate-400 mt-1"><?= number_format($f['files']) ?> file</div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Orphan Scan Result -->
    <?php if ($orphans !== null): ?>
        <div class="bg-white rounded-2xl border border-slate-200/80 overflow-hidden shadow-2xs">
            <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-bold text-slate-800">Hasil Pemindaian File Yatim (<?= count($orphans) ?> File)</h2>
                    <p class="text-[11px] text-slate-400">File di disk yang tidak lagi dirujuk oleh data perusahaan, dokumentasi, maupun dokumen paket</p>
                </div>
            </div>
            <?php if (empty($orphans)): ?>
                <div class="p-8 text-center text-xs text-emerald-600 font-semibold">
                    Storage bersih! Tidak ditemukan file yatim di folder uploads.
                </div>
            <?php else: ?>
                <form method="POST" action="<?= APP_URL ?>/admin/cleanOrphans" onsubmit="return confirm('Yakin ingin menghapus permanen file terpilih?')">
                    <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
                    <div class="p-3 bg-slate-50 border-b border-slate-100 flex items-center justify-between">
                        <label class="inline-flex items-center gap-2 text-xs font-semibold text-slate-700 cursor-pointer">
                            <input type="checkbox" onclick="document.querySelectorAll('.orphan-chk').forEach(c => c.checked = this.checked)">
                            Pilih Semua
                        </label>
                        <button type="submit" class="px-3 py-1.5 bg-rose-600 hover:bg-rose-700 text-white rounded-lg text-xs font-semibold cursor-pointer">
                            Hapus File Terpilih
                        </button>
                    </div>
                    <div class="divide-y divide-slate-100 max-h-96 overflow-y-auto">
                        <?php foreach ($orphans as $o): ?>
                            <label class="px-4 py-2.5 flex items-center justify-between gap-3 text-xs hover:bg-slate-50 cursor-pointer">
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <input type="checkbox" name="files[]" value="<?= htmlspecialchars($o['rel_path']) ?>" class="orphan-chk">
                                    <span class="font-mono text-slate-700 truncate"><?= htmlspecialchars($o['rel_path']) ?></span>
                                </div>
                                <span class="text-[11px] text-slate-400 whitespace-nowrap shrink-0">
                                    <?= formatBytes($o['size']) ?> &bull; <?= date('d/m/Y H:i', $o['mtime']) ?>
                                </span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<?php require_once VIEW_PATH . '/layout/footer.php'; ?>
