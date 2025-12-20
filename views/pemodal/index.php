<!-- Header -->
<div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">Daftar Pemodal</h1>
        <p class="text-slate-500 mt-1">Kelola data investor dan mitra kerja</p>
    </div>
    <div class="flex gap-3">
        <a href="<?= APP_URL ?>/pemodal/create" class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl transition-all shadow-lg shadow-blue-600/30 hover:scale-[1.02] active:scale-[0.98]">
            <ion-icon name="add-circle-outline" class="text-xl"></ion-icon>
            <span>Tambah Pemodal</span>
        </a>
    </div>
</div>

<!-- Search & Filter -->
<div class="mb-6">
    <div class="relative max-w-md">
        <ion-icon name="search-outline" class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-lg"></ion-icon>
        <input type="text" id="searchInput" placeholder="Cari nama atau email..." 
               class="w-full pl-11 pr-4 py-3 bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-sm">
    </div>
</div>

<!-- Grid Layout -->
<?php if (empty($users)): ?>
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-12 text-center">
        <div class="w-20 h-20 bg-blue-50 rounded-full flex items-center justify-center mx-auto mb-4">
            <ion-icon name="people-outline" class="text-4xl text-blue-400"></ion-icon>
        </div>
        <h3 class="text-lg font-bold text-slate-800 mb-2">Belum ada Pemodal</h3>
        <p class="text-slate-500 mb-6 max-w-md mx-auto">Mulai tambahkan data pemodal untuk mengelola investasi dan pembagian profit.</p>
        <a href="<?= APP_URL ?>/pemodal/create" class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl transition-colors">
            Mulai Tambah
        </a>
    </div>
<?php else: ?>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6" id="pemodalGrid">
        <?php foreach ($users as $user): ?>
            <!-- User Card -->
            <div class="user-card bg-white rounded-2xl shadow-sm border border-slate-200 p-6 hover:shadow-md transition-all hover:-translate-y-1 group relative overflow-hidden">
                <!-- Decorative BG -->
                <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-slate-50 to-slate-100 rounded-bl-full -mr-4 -mt-4 transition-transform group-hover:scale-110"></div>
                
                <div class="relative">
                    <div class="flex items-start justify-between mb-4">
                        <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-blue-500 to-blue-600 text-white flex items-center justify-center text-xl font-bold shadow-lg shadow-blue-500/20">
                            <?= strtoupper(substr($user['nama'], 0, 1)) ?>
                        </div>
                        <div class="dropdown relative group/menu">
                            <button class="w-8 h-8 rounded-lg text-slate-400 hover:bg-slate-50 hover:text-slate-600 flex items-center justify-center transition-colors">
                                <ion-icon name="ellipsis-horizontal" class="text-xl"></ion-icon>
                            </button>
                            <!-- Dropdown Menu -->
                            <div class="absolute right-0 top-full mt-1 w-32 bg-white border border-slate-100 rounded-xl shadow-xl opacity-0 invisible group-hover/menu:opacity-100 group-hover/menu:visible transition-all z-10 transform origin-top-right">
                                <a href="<?= APP_URL ?>/pemodal/edit/<?= $user['id'] ?>" class="flex items-center gap-2 px-4 py-2.5 text-sm text-slate-600 hover:bg-slate-50 first:rounded-t-xl hover:text-blue-600">
                                    <ion-icon name="create-outline"></ion-icon> Edit
                                </a>
                                <form action="<?= APP_URL ?>/pemodal/delete/<?= $user['id'] ?>" method="POST" onsubmit="return confirm('Hapus pemodal ini?')">
                                    <?= csrfField() ?>
                                    <button type="submit" class="w-full flex items-center gap-2 px-4 py-2.5 text-sm text-rose-600 hover:bg-rose-50 last:rounded-b-xl">
                                        <ion-icon name="trash-outline"></ion-icon> Hapus
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <h3 class="text-lg font-bold text-slate-800 mb-1 truncate" title="<?= htmlspecialchars($user['nama']) ?>">
                        <?= htmlspecialchars($user['nama']) ?>
                    </h3>
                    
                    <div class="space-y-2 mb-6">
                        <div class="flex items-center gap-2 text-sm text-slate-500">
                            <ion-icon name="mail-outline" class="text-slate-400"></ion-icon>
                            <span class="truncate"><?= htmlspecialchars($user['email'] ?? '-') ?></span>
                        </div>
                        <div class="flex items-center gap-2 text-sm text-slate-500">
                            <ion-icon name="call-outline" class="text-slate-400"></ion-icon>
                            <span><?= htmlspecialchars($user['telepon'] ?? '-') ?></span>
                        </div>
                    </div>
                    
                    <a href="<?= APP_URL ?>/pemodal/detail/<?= $user['id'] ?>" class="flex items-center justify-center w-full py-2.5 bg-slate-50 hover:bg-blue-50 text-slate-600 hover:text-blue-600 font-semibold rounded-xl border border-slate-200 hover:border-blue-200 transition-all gap-2 group/btn">
                        <span>Lihat Detail</span>
                        <ion-icon name="arrow-forward-outline" class="transition-transform group-hover/btn:translate-x-1"></ion-icon>
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<script>
document.getElementById('searchInput').addEventListener('keyup', function() {
    let filter = this.value.toLowerCase();
    let cards = document.querySelectorAll('.user-card');
    
    cards.forEach(card => {
        let text = card.textContent.toLowerCase();
        if (text.includes(filter)) {
            card.style.display = '';
        } else {
            card.style.display = 'none';
        }
    });
});
</script>
