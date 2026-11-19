<div class="max-w-xl mx-auto">
    <!-- Header & Back -->
    <div class="mb-4 flex items-center justify-between">
        <div>
            <h1 class="text-lg md:text-xl font-bold text-slate-800">Edit Perusahaan</h1>
            <p class="text-xs text-slate-500">Perbarui data profil perusahaan</p>
        </div>
        <a href="<?= APP_URL ?>/perusahaan" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-lg transition-colors text-xs">
            <ion-icon name="arrow-back-outline" class="text-sm"></ion-icon>
            <span>Kembali</span>
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-xs border border-slate-200/80 p-5">
        <form method="POST" action="<?= APP_URL ?>/perusahaan/update/<?= $perusahaan['id'] ?>" class="space-y-3.5">
            <?= csrfField() ?>
            
            <!-- Nama -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Perusahaan / CV / PT <span class="text-red-500">*</span></label>
                <div class="relative">
                    <ion-icon name="business-outline" class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-base"></ion-icon>
                    <input type="text" name="nama" value="<?= htmlspecialchars($perusahaan['nama']) ?>"
                           class="w-full pl-9 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:border-blue-500 text-xs focus:bg-white transition-all" 
                           required>
                </div>
            </div>
            
            <!-- Contact Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                <!-- Email -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Email Perusahaan</label>
                    <div class="relative">
                        <ion-icon name="mail-outline" class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-base"></ion-icon>
                        <input type="email" name="email" value="<?= htmlspecialchars($perusahaan['email'] ?? '') ?>"
                               class="w-full pl-9 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:border-blue-500 text-xs focus:bg-white transition-all">
                    </div>
                </div>
                
                <!-- Phone -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Nomor Telepon / WhatsApp</label>
                    <div class="relative">
                        <ion-icon name="call-outline" class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-base"></ion-icon>
                        <input type="text" name="telepon" value="<?= htmlspecialchars($perusahaan['telepon'] ?? '') ?>"
                               class="w-full pl-9 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:border-blue-500 text-xs focus:bg-white transition-all">
                    </div>
                </div>
            </div>
            
            <!-- Alamat -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Alamat Kantor</label>
                <textarea name="alamat" rows="2" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:border-blue-500 text-xs focus:bg-white transition-all"><?= htmlspecialchars($perusahaan['alamat'] ?? '') ?></textarea>
            </div>
            
            <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                <a href="<?= APP_URL ?>/perusahaan" class="px-3.5 py-1.5 rounded-lg border border-slate-200 text-slate-600 font-semibold hover:bg-slate-50 text-xs transition-colors">
                    Batal
                </a>
                <button type="submit" class="px-4 py-1.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg text-xs shadow-xs transition-all flex items-center gap-1.5">
                    <ion-icon name="save-outline" class="text-sm"></ion-icon>
                    <span>Simpan Perubahan</span>
                </button>
            </div>
        </form>
    </div>
</div>
