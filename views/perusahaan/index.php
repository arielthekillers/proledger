<!-- Header -->
<div class="flex items-center justify-between mb-8">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">Daftar Perusahaan</h1>
        <div class="flex items-center gap-2 mt-1">
            <p class="text-slate-500 text-sm">Mitra kerja dan kontraktor</p>
            <span class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-600 text-xs font-bold"><?= count($perusahaan) ?></span>
        </div>
    </div>
    <a href="<?= APP_URL ?>/perusahaan/create" class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl transition-all shadow-lg shadow-blue-600/30 hover:scale-[1.02] active:scale-[0.98]">
        <ion-icon name="business-outline" class="text-xl"></ion-icon>
        <span>Tambah Perusahaan</span>
    </a>
</div>

<!-- Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <?php foreach ($perusahaan as $p): ?>
        <a href="<?= APP_URL ?>/perusahaan/detail/<?= $p['id'] ?>" class="group block bg-white rounded-2xl border border-slate-200 p-6 hover:border-blue-400 hover:shadow-md transition-all relative overflow-hidden">
            <!-- Top Gradient -->
            <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-blue-500 to-indigo-500 opacity-0 group-hover:opacity-100 transition-opacity"></div>
            
            <div class="flex items-start justify-between mb-4">
                <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-2xl group-hover:bg-blue-600 group-hover:text-white transition-colors">
                    <span class="font-bold"><?= strtoupper(substr($p['nama'], 0, 1)) ?></span>
                </div>
                <div class="flex items-center gap-1">
                    <span class="px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-700 text-xs font-bold flex items-center gap-1">
                        <ion-icon name="briefcase"></ion-icon>
                        <?= $p['jumlah_paket'] ?> Paket
                    </span>
                </div>
            </div>
            
            <h3 class="text-lg font-bold text-slate-800 mb-2 group-hover:text-blue-600 transition-colors line-clamp-1"><?= htmlspecialchars($p['nama']) ?></h3>
            
            <div class="space-y-2 text-sm text-slate-500">
                <?php if($p['telepon']): ?>
                    <div class="flex items-center gap-2">
                        <ion-icon name="call-outline" class="text-slate-400"></ion-icon>
                        <span><?= htmlspecialchars($p['telepon']) ?></span>
                    </div>
                <?php endif; ?>
                <?php if($p['email']): ?>
                    <div class="flex items-center gap-2">
                        <ion-icon name="mail-outline" class="text-slate-400"></ion-icon>
                        <span class="line-clamp-1"><?= htmlspecialchars($p['email']) ?></span>
                    </div>
                <?php endif; ?>
                <?php if($p['alamat']): ?>
                    <div class="flex items-start gap-2">
                        <ion-icon name="location-outline" class="text-slate-400 mt-0.5"></ion-icon>
                        <span class="line-clamp-2 text-xs"><?= htmlspecialchars($p['alamat']) ?></span>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="mt-4 pt-4 border-t border-slate-100 flex justify-end text-blue-600 text-xs font-semibold">
                <span class="flex items-center gap-1 group-hover:gap-2 transition-all">Lihat Detail <ion-icon name="arrow-forward"></ion-icon></span>
            </div>
        </a>
    <?php endforeach; ?>
    
    <!-- Empty State -->
    <?php if (empty($perusahaan)): ?>
        <div class="col-span-full py-12 text-center bg-white rounded-2xl border border-dashed border-slate-300">
            <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4">
                <ion-icon name="business-outline" class="text-3xl text-slate-400"></ion-icon>
            </div>
            <h3 class="text-lg font-bold text-slate-800">Belum ada perusahaan</h3>
            <p class="text-slate-500 mt-1 mb-4">Tambahkan perusahaan mitra pertama Anda</p>
            <a href="<?= APP_URL ?>/perusahaan/create" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-50 text-blue-600 font-semibold rounded-lg hover:bg-blue-100 transition-colors">
                <ion-icon name="add"></ion-icon> Tambah Baru
            </a>
        </div>
    <?php endif; ?>
</div>
