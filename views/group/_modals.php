<!-- ========================================== -->
<!-- SHARED MODALS: TAMBAH & EDIT GROUP (POPUP) -->
<!-- ========================================== -->
<div id="modalTambahGroup" class="app-modal fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 transition-all duration-200 opacity-0 pointer-events-none hidden">
    <div class="modal-card bg-white rounded-2xl shadow-2xl border border-slate-100 max-w-md w-full transform scale-95 opacity-0 transition-all duration-200 overflow-hidden">
        <div class="px-5 py-4 bg-slate-50/80 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-blue-600 text-white flex items-center justify-center text-lg shadow-xs">
                    <ion-icon name="folder-open-outline"></ion-icon>
                </div>
                <div>
                    <h3 class="font-bold text-slate-800 text-sm">Tambah Group Paket</h3>
                    <p class="text-[11px] text-slate-400">Buat wadah program untuk mengelompokkan paket</p>
                </div>
            </div>
            <button type="button" data-modal-close="modalTambahGroup" class="p-1.5 text-slate-400 hover:text-slate-700 rounded-lg transition-colors cursor-pointer">
                <ion-icon name="close-outline" class="text-xl"></ion-icon>
            </button>
        </div>

        <form method="POST" action="<?= APP_URL ?>/group/store" class="p-5 space-y-3.5">
            <?= csrfField() ?>

            <?php if (!empty($perusahaanList) && count($perusahaanList) > 1): ?>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Perusahaan</label>
                    <select name="perusahaan_id" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-blue-500 text-xs focus:bg-white transition-all">
                        <option value="">-- Umum / Semua Perusahaan --</option>
                        <?php foreach ($perusahaanList as $cp): ?>
                            <option value="<?= $cp['id'] ?>" <?= (!empty($activeCompId) && $activeCompId == $cp['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cp['nama']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            <?php else: ?>
                <input type="hidden" name="perusahaan_id" value="<?= $activeCompId ?? ($perusahaanList[0]['id'] ?? '') ?>">
            <?php endif; ?>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Group / Program <span class="text-red-500">*</span></label>
                <input type="text" name="nama_group" required 
                       class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-blue-500 text-xs focus:bg-white transition-all"
                       placeholder="Contoh: Dinas Pendidikan 2025">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Kode Group (Opsional)</label>
                <input type="text" name="kode_group" 
                       class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-blue-500 text-xs focus:bg-white transition-all font-mono"
                       placeholder="Contoh: GRP-DIKNAS-2025">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Deskripsi / Ruang Lingkup</label>
                <textarea name="deskripsi" rows="2" 
                          class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-blue-500 text-xs focus:bg-white transition-all"
                          placeholder="Catatan mengenai kontrak payung atau dinas terkait..."></textarea>
            </div>

            <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                <button type="button" data-modal-close="modalTambahGroup" 
                        class="px-3.5 py-2 rounded-xl border border-slate-200 text-slate-600 font-semibold hover:bg-slate-50 text-xs transition-colors cursor-pointer">
                    Batal
                </button>
                <button type="submit" 
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl text-xs shadow-xs transition-all flex items-center gap-1.5 cursor-pointer">
                    <ion-icon name="checkmark-outline" class="text-sm"></ion-icon>
                    <span>Simpan Group</span>
                </button>
            </div>
        </form>
    </div>
</div>

<div id="modalEditGroup" class="app-modal fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 transition-all duration-200 opacity-0 pointer-events-none hidden">
    <div class="modal-card bg-white rounded-2xl shadow-2xl border border-slate-100 max-w-md w-full transform scale-95 opacity-0 transition-all duration-200 overflow-hidden">
        <div class="px-5 py-4 bg-slate-50/80 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-amber-500 text-white flex items-center justify-center text-lg shadow-xs">
                    <ion-icon name="create-outline"></ion-icon>
                </div>
                <div>
                    <h3 class="font-bold text-slate-800 text-sm">Edit Group Paket</h3>
                    <p class="text-[11px] text-slate-400" id="editGroupSubtitle">Perbarui rincian data group</p>
                </div>
            </div>
            <button type="button" data-modal-close="modalEditGroup" class="p-1.5 text-slate-400 hover:text-slate-700 rounded-lg transition-colors cursor-pointer">
                <ion-icon name="close-outline" class="text-xl"></ion-icon>
            </button>
        </div>

        <form id="formEditGroup" method="POST" action="" class="p-5 space-y-3.5">
            <?= csrfField() ?>

            <?php if (!empty($perusahaanList) && count($perusahaanList) > 1): ?>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Perusahaan</label>
                    <select id="edit_group_perusahaan_id" name="perusahaan_id" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-blue-500 text-xs focus:bg-white transition-all">
                        <option value="">-- Umum / Semua Perusahaan --</option>
                        <?php foreach ($perusahaanList as $cp): ?>
                            <option value="<?= $cp['id'] ?>"><?= htmlspecialchars($cp['nama']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            <?php endif; ?>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Group / Program <span class="text-red-500">*</span></label>
                <input type="text" id="edit_group_nama" name="nama_group" required 
                       class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-blue-500 text-xs focus:bg-white transition-all">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Kode Group (Opsional)</label>
                <input type="text" id="edit_group_kode" name="kode_group" 
                       class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-blue-500 text-xs focus:bg-white transition-all font-mono">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Deskripsi / Ruang Lingkup</label>
                <textarea id="edit_group_deskripsi" name="deskripsi" rows="2" 
                          class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-blue-500 text-xs focus:bg-white transition-all"></textarea>
            </div>

            <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                <button type="button" data-modal-close="modalEditGroup" 
                        class="px-3.5 py-2 rounded-xl border border-slate-200 text-slate-600 font-semibold hover:bg-slate-50 text-xs transition-colors cursor-pointer">
                    Batal
                </button>
                <button type="submit" 
                        class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white font-semibold rounded-xl text-xs shadow-xs transition-all flex items-center gap-1.5 cursor-pointer">
                    <ion-icon name="checkmark-outline" class="text-sm"></ion-icon>
                    <span>Simpan Perubahan</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openEditGroupModal(group) {
    if (!group) return;

    const form = document.getElementById('formEditGroup');
    if (form) form.action = '<?= APP_URL ?>/group/update/' + group.id;

    document.getElementById('edit_group_nama').value = group.nama_group || '';
    document.getElementById('edit_group_kode').value = group.kode_group || '';
    document.getElementById('edit_group_deskripsi').value = group.deskripsi || '';
    document.getElementById('editGroupSubtitle').textContent = 'Mengubah: ' + (group.nama_group || '');

    const sel = document.getElementById('edit_group_perusahaan_id');
    if (sel) {
        sel.value = group.perusahaan_id || '';
    }

    window.ProModal.open('modalEditGroup');
}
</script>
