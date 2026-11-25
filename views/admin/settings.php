<?php require_once VIEW_PATH . '/layout/header.php'; ?>

<div class="max-w-4xl mx-auto space-y-5">
    <div>
        <h1 class="text-xl font-bold text-slate-800">Pengaturan Global &amp; Pengumuman</h1>
        <p class="text-xs text-slate-500 mt-0.5">Kelola banner pengumuman untuk seluruh pengguna dan nilai referensi default aplikasi</p>
    </div>

    <form method="POST" action="<?= APP_URL ?>/admin/saveSettings" class="space-y-5">
        <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">

        <!-- Banner Pengumuman -->
        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-2xs space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div>
                    <h2 class="text-sm font-bold text-slate-800">Banner Pengumuman Pengguna</h2>
                    <p class="text-[11px] text-slate-400">Tampil di bagian atas halaman seluruh user saat diaktifkan</p>
                </div>
                <label class="inline-flex items-center gap-2 text-xs font-semibold text-slate-700 cursor-pointer">
                    <input type="checkbox" name="announcement_enabled" value="1" <?= ($settings['announcement_enabled'] ?? '0') === '1' ? 'checked' : '' ?>
                           class="w-4 h-4 rounded border-slate-300 text-blue-600">
                    Aktifkan Banner
                </label>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div>
                    <label class="block text-[11px] font-semibold text-slate-600 mb-1">Tipe Warna</label>
                    <select name="announcement_type" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs">
                        <?php foreach (['info' => 'Info (Biru)', 'warning' => 'Peringatan (Kuning)', 'success' => 'Sukses (Hijau)', 'danger' => 'Penting (Merah)'] as $k => $lbl): ?>
                            <option value="<?= $k ?>" <?= ($settings['announcement_type'] ?? 'info') === $k ? 'selected' : '' ?>><?= $lbl ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-[11px] font-semibold text-slate-600 mb-1">Judul Pengumuman</label>
                    <input type="text" name="announcement_title" value="<?= htmlspecialchars($settings['announcement_title'] ?? '') ?>"
                           placeholder="Misal: Jadwal Pemeliharaan Sistem / Fitur Baru"
                           class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs">
                </div>
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-slate-600 mb-1">Isi Pesan Pengumuman</label>
                <textarea name="announcement_message" rows="3" placeholder="Tulis pesan pengumuman yang akan dilihat oleh seluruh user..."
                          class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs"><?= htmlspecialchars($settings['announcement_message'] ?? '') ?></textarea>
            </div>
        </div>

        <!-- Referensi Default -->
        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-2xs space-y-4">
            <div class="border-b border-slate-100 pb-3">
                <h2 class="text-sm font-bold text-slate-800">Referensi Default Sistem</h2>
                <p class="text-[11px] text-slate-400">Nilai acuan global untuk form baru</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div>
                    <label class="block text-[11px] font-semibold text-slate-600 mb-1">Default PPN (%)</label>
                    <input type="number" step="0.1" name="default_ppn_persen" value="<?= htmlspecialchars($settings['default_ppn_persen'] ?? '11') ?>"
                           class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs">
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-slate-600 mb-1">Default Tahun Anggaran</label>
                    <input type="text" name="default_tahun_anggaran" value="<?= htmlspecialchars($settings['default_tahun_anggaran'] ?? '2026') ?>"
                           class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs">
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-slate-600 mb-1">Daftar Tahun Anggaran</label>
                    <input type="text" name="tahun_anggaran_list" value="<?= htmlspecialchars($settings['tahun_anggaran_list'] ?? '2024,2025,2026,2027') ?>"
                           placeholder="2024,2025,2026,2027"
                           class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs">
                </div>
            </div>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-semibold shadow-xs cursor-pointer">
                Simpan Pengaturan
            </button>
        </div>
    </form>
</div>

<?php require_once VIEW_PATH . '/layout/footer.php'; ?>
