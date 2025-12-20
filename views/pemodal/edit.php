<!-- Header & Back -->
<div class="mb-6 flex items-center justify-between">
    <a href="<?= APP_URL ?>/pemodal" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-slate-200 rounded-xl text-slate-600 hover:bg-slate-50 hover:text-blue-600 transition-colors shadow-sm">
        <ion-icon name="arrow-back-outline"></ion-icon>
        <span>Kembali</span>
    </a>
</div>

<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-6 border-b border-slate-100 bg-slate-50/50">
            <h1 class="text-xl font-bold text-slate-800">Edit Data Pemodal</h1>
            <p class="text-slate-500 text-sm mt-1">Perbarui informasi kontak dan profil pemodal</p>
        </div>
        
        <form method="POST" action="<?= APP_URL ?>/pemodal/update/<?= $user['id'] ?>" class="p-6 md:p-8 space-y-6">
            <?= csrfField() ?>
            
            <!-- Nama -->
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Nama Lengkap <span class="text-red-500">*</span></label>
                <div class="relative">
                    <ion-icon name="person-outline" class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-lg"></ion-icon>
                    <input type="text" name="nama" class="w-full pl-11 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-colors" 
                           required placeholder="Nama lengkap sesuai KTP" value="<?= htmlspecialchars($user['nama']) ?>">
                </div>
            </div>
            
            <!-- Contact Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Email -->
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Alamat Email</label>
                    <div class="relative">
                        <ion-icon name="mail-outline" class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-lg"></ion-icon>
                        <input type="email" name="email" class="w-full pl-11 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-colors" 
                               placeholder="contoh@email.com" value="<?= htmlspecialchars($user['email'] ?? '') ?>">
                    </div>
                </div>
                
                <!-- Phone -->
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Nomor Telepon / WA</label>
                    <div class="relative">
                        <ion-icon name="call-outline" class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-lg"></ion-icon>
                        <input type="text" name="telepon" class="w-full pl-11 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-colors" 
                               placeholder="0812..." value="<?= htmlspecialchars($user['telepon'] ?? '') ?>">
                    </div>
                </div>
            </div>
            
            <div class="pt-4 flex items-center gap-4">
                <button type="submit" class="flex-1 py-3.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-lg shadow-blue-600/30 transition-all hover:scale-[1.02] active:scale-[0.98] flex items-center justify-center gap-2">
                    <ion-icon name="save-outline" class="text-xl"></ion-icon>
                    <span>Update Data</span>
                </button>
                <a href="<?= APP_URL ?>/pemodal" class="px-6 py-3.5 bg-white border border-slate-200 text-slate-600 font-semibold rounded-xl hover:bg-slate-50 transition-colors">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
