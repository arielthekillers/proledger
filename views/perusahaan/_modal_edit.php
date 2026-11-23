<!-- ========================================== -->
<!-- SHARED MODAL: EDIT PROFIL PERUSAHAAN       -->
<!-- ========================================== -->
<div id="modalEditPerusahaan" class="app-modal fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 transition-all duration-200 opacity-0 pointer-events-none hidden">
    <div class="modal-card bg-white rounded-2xl shadow-2xl border border-slate-100 max-w-lg w-full transform scale-95 opacity-0 transition-all duration-200 overflow-hidden">
        <!-- Modal Header -->
        <div class="px-6 py-4 bg-slate-50/75 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-amber-500 text-white flex items-center justify-center text-lg shadow-sm">
                    <ion-icon name="create"></ion-icon>
                </div>
                <div>
                    <h3 class="font-bold text-slate-800 text-sm">Edit Data Perusahaan</h3>
                    <p class="text-[11px] text-slate-400" id="editPerusahaanSubtitle">Perbarui rincian data perusahaan</p>
                </div>
            </div>
            <button type="button" data-modal-close="modalEditPerusahaan" class="p-1.5 text-slate-400 hover:text-slate-700 rounded-lg transition-colors cursor-pointer">
                <ion-icon name="close-outline" class="text-xl"></ion-icon>
            </button>
        </div>

        <!-- Modal Form -->
        <form id="formEditPerusahaan" method="POST" action="" class="p-6 space-y-4">
            <?= csrfField() ?>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Perusahaan / CV / PT <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <ion-icon name="business-outline" class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-base"></ion-icon>
                        <input type="text" id="edit_nama" name="nama" required 
                               class="w-full pl-9 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-blue-500 text-xs focus:bg-white transition-all">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Direktur / Pimpinan</label>
                    <div class="relative">
                        <ion-icon name="person-outline" class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-base"></ion-icon>
                        <input type="text" id="edit_nama_direktur" name="nama_direktur" 
                               class="w-full pl-9 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-blue-500 text-xs focus:bg-white transition-all"
                               placeholder="Untuk tanda tangan BAST">
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Email Perusahaan</label>
                    <div class="relative">
                        <ion-icon name="mail-outline" class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-base"></ion-icon>
                        <input type="email" id="edit_email" name="email" 
                               class="w-full pl-9 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-blue-500 text-xs focus:bg-white transition-all">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Nomor Telepon / WA</label>
                    <div class="relative">
                        <ion-icon name="call-outline" class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-base"></ion-icon>
                        <input type="text" id="edit_telepon" name="telepon" 
                               class="w-full pl-9 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-blue-500 text-xs focus:bg-white transition-all">
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Website Perusahaan</label>
                <div class="relative">
                    <ion-icon name="globe-outline" class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-base"></ion-icon>
                    <input type="url" id="edit_website" name="website" 
                           class="w-full pl-9 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-blue-500 text-xs focus:bg-white transition-all"
                           placeholder="https://perusahaan.co.id">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Alamat Domisili Kantor</label>
                <textarea id="edit_alamat" name="alamat" rows="2" 
                          class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-blue-500 text-xs focus:bg-white transition-all"></textarea>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Keterangan / Deskripsi Singkat</label>
                <textarea id="edit_keterangan" name="keterangan" rows="2" 
                          class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-blue-500 text-xs focus:bg-white transition-all"></textarea>
            </div>

            <!-- Modal Footer -->
            <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                <button type="button" data-modal-close="modalEditPerusahaan" 
                        class="px-3.5 py-2 rounded-xl border border-slate-200 text-slate-600 font-semibold hover:bg-slate-50 text-xs transition-colors cursor-pointer">
                    Batal
                </button>
                <button type="submit" 
                        class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white font-semibold rounded-xl text-xs shadow-sm transition-all flex items-center gap-1.5 cursor-pointer">
                    <ion-icon name="checkmark-outline" class="text-sm"></ion-icon>
                    <span>Simpan Perubahan</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openEditPerusahaanModal(company) {
    if (!company) return;

    const form = document.getElementById('formEditPerusahaan');
    if (form) form.action = '<?= APP_URL ?>/perusahaan/update/' + company.id;

    document.getElementById('edit_nama').value = company.nama || '';
    document.getElementById('edit_nama_direktur').value = company.nama_direktur || '';
    document.getElementById('edit_email').value = company.email || '';
    document.getElementById('edit_telepon').value = company.telepon || '';
    document.getElementById('edit_website').value = company.website || '';
    document.getElementById('edit_alamat').value = company.alamat || '';
    document.getElementById('edit_keterangan').value = company.keterangan || '';
    document.getElementById('editPerusahaanSubtitle').textContent = 'Mengubah: ' + (company.nama || '');

    window.ProModal.open('modalEditPerusahaan');
}
</script>
