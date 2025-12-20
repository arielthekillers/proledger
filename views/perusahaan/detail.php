<!-- Header & Back -->
<div class="mb-6 flex items-center justify-between">
    <a href="<?= APP_URL ?>/perusahaan" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-slate-200 rounded-xl text-slate-600 hover:bg-slate-50 hover:text-blue-600 transition-colors shadow-sm text-sm font-medium">
        <ion-icon name="arrow-back-outline"></ion-icon>
        <span>Kembali ke Daftar</span>
    </a>
    <div class="flex gap-3">
        <a href="<?= APP_URL ?>/perusahaan/edit/<?= $perusahaan['id'] ?>" class="inline-flex items-center gap-2 px-4 py-2 bg-slate-900 text-white rounded-xl hover:bg-slate-800 transition-colors shadow-lg shadow-slate-900/20 text-sm font-semibold">
            <ion-icon name="create-outline"></ion-icon>
            <span>Edit Data Perusahaan</span>
        </a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
    <!-- Main Profile Card -->
    <div class="lg:col-span-2">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden h-full">
            <div class="p-8">
                <div class="flex items-start gap-6">
                    <!-- Modern Logo Placeholder -->
                    <div class="w-20 h-20 rounded-2xl bg-gradient-to-br from-blue-600 to-indigo-700 text-white shadow-lg shadow-blue-600/20 flex items-center justify-center text-3xl font-bold shrink-0">
                        <?= strtoupper(substr($perusahaan['nama'], 0, 2)) ?>
                    </div>
                    
                    <div class="flex-1">
                        <div class="flex items-start justify-between">
                            <div>
                                <h1 class="text-2xl font-bold text-slate-900 leading-tight mb-1">
                                    <?= htmlspecialchars($perusahaan['nama']) ?>
                                </h1>
                                <p class="text-slate-500 text-sm mb-4">Terdaftar sejak <?= date('d M Y', strtotime($perusahaan['created_at'])) ?></p>
                                
                                <!-- Login Status -->
                                <div class="flex items-center gap-2 text-sm mb-4">
                                    <?php if ($login_access): ?>
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 font-medium text-xs border border-emerald-100">
                                            <ion-icon name="shield-checkmark"></ion-icon>
                                            Login Active
                                        </span>
                                    <?php else: ?>
                                        <?php if (!empty($perusahaan['email'])): ?>
                                            <form action="<?= APP_URL ?>/perusahaan/generateAccess/<?= $perusahaan['id'] ?>" method="POST" onsubmit="return confirm('Generate login access for <?= htmlspecialchars($perusahaan['nama']) ?>?');">
                                                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                                                <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-slate-100 text-slate-600 hover:bg-slate-800 hover:text-white transition-all text-xs font-medium border border-slate-200 hover:border-slate-800">
                                                    <ion-icon name="key-outline"></ion-icon>
                                                    Enable Login Access
                                                </button>
                                            </form>
                                        <?php else: ?>
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-slate-50 text-slate-400 font-medium text-xs border border-slate-100" title="Email required">
                                                <ion-icon name="lock-closed"></ion-icon>
                                                Email Missing
                                            </span>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Stats Grid -->
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                            <div class="p-4 bg-slate-50 rounded-xl border border-slate-100">
                                <span class="block text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1">Total Paket</span>
                                <span class="text-xl font-bold text-slate-800"><?= count($paket) ?></span>
                            </div>
                            <div class="p-4 bg-slate-50 rounded-xl border border-slate-100 col-span-2 sm:col-span-2">
                                <span class="block text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1">Total Nilai Pagu</span>
                                <span class="text-xl font-bold text-slate-800 font-mono">
                                    <?php 
                                    $totalPagu = array_reduce($paket, function($carry, $item) {
                                        return $carry + $item['pagu'];
                                    }, 0);
                                    echo formatRupiah($totalPagu);
                                    ?>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Contact Info Card -->
    <div class="lg:col-span-1">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 h-full">
            <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wide mb-4 flex items-center gap-2">
                <ion-icon name="information-circle-outline" class="text-lg text-blue-600"></ion-icon>
                Kontak & Alamat
            </h3>
            
            <div class="space-y-5">
                <div class="flex gap-4 group">
                    <div class="w-10 h-10 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform">
                        <ion-icon name="mail-outline" class="text-xl"></ion-icon>
                    </div>
                    <div>
                        <span class="block text-xs font-medium text-slate-500 mb-0.5">Email Perusahaan</span>
                        <div class="text-slate-800 font-medium break-all">
                            <?php if($perusahaan['email']): ?>
                                <a href="mailto:<?= htmlspecialchars($perusahaan['email']) ?>" class="hover:text-blue-600 hover:underline">
                                    <?= htmlspecialchars($perusahaan['email']) ?>
                                </a>
                            <?php else: ?>
                                <span class="text-slate-400 italic">Tidak ada email</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="flex gap-4 group">
                    <div class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform">
                        <ion-icon name="call-outline" class="text-xl"></ion-icon>
                    </div>
                    <div>
                        <span class="block text-xs font-medium text-slate-500 mb-0.5">Nomor Telepon</span>
                        <div class="text-slate-800 font-medium">
                            <?php if($perusahaan['telepon']): ?>
                                <a href="tel:<?= htmlspecialchars($perusahaan['telepon']) ?>" class="hover:text-blue-600 hover:underline">
                                    <?= htmlspecialchars($perusahaan['telepon']) ?>
                                </a>
                            <?php else: ?>
                                <span class="text-slate-400 italic">Tidak ada telepon</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="flex gap-4 group">
                    <div class="w-10 h-10 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform">
                        <ion-icon name="location-outline" class="text-xl"></ion-icon>
                    </div>
                    <div>
                        <span class="block text-xs font-medium text-slate-500 mb-0.5">Alamat Kantor</span>
                        <div class="text-slate-800 font-medium leading-relaxed">
                            <?php if($perusahaan['alamat']): ?>
                                <?= nl2br(htmlspecialchars($perusahaan['alamat'])) ?>
                            <?php else: ?>
                                <span class="text-slate-400 italic">Alamat belum diisi</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Projects List -->
<div class="bg-white rounded-2xl shadow-sm border border-slate-200">
    <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
        <h3 class="text-lg font-bold text-slate-800 flex items-center gap-2">
            <ion-icon name="briefcase-outline" class="text-blue-500"></ion-icon>
            Riwayat Paket Pekerjaan
        </h3>
        <!-- Filter/Search could go here -->
    </div>

    <?php if(empty($paket)): ?>
        <div class="p-12 text-center">
            <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4">
                <ion-icon name="folder-open-outline" class="text-3xl text-slate-400"></ion-icon>
            </div>
            <h4 class="text-slate-900 font-medium mb-1">Belum Ada Paket</h4>
            <p class="text-slate-500 text-sm">Perusahaan ini belum memiliki riwayat paket kegiatan.</p>
        </div>
    <?php else: ?>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-slate-50/50 text-[10px] uppercase text-slate-500 font-bold border-b border-slate-100 tracking-wider">
                    <tr>
                        <th class="px-6 py-4">Nama Paket Kegiatan</th>
                        <th class="px-6 py-4 text-center">Status</th>
                        <th class="px-6 py-4 text-right">Nilai Pagu</th>
                        <th class="px-6 py-4 text-center">Pemodal</th>
                        <th class="px-6 py-4 text-right">Tanggal Register</th>
                        <th class="px-6 py-4"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    <?php foreach ($paket as $p): ?>
                        <tr class="hover:bg-slate-50/80 transition-colors group cursor-pointer" onclick="window.location='<?= APP_URL ?>/paket/detail/<?= $p['id'] ?>?from=perusahaan&source_id=<?= $perusahaan['id'] ?>'">
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-800 group-hover:text-blue-600 transition-colors"><?= htmlspecialchars($p['nama_paket']) ?></div>
                                <div class="text-xs text-slate-500 mt-0.5">ID: #<?= str_pad($p['id'], 5, '0', STR_PAD_LEFT) ?></div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <?= getStatusBadge($p['status']) ?>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="font-mono font-bold text-slate-700"><?= formatRupiah($p['pagu']) ?></div>
                                <div class="text-[10px] text-slate-400 mt-0.5">RAB: <?= formatRupiah($p['rab']) ?></div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="inline-flex -space-x-2 overflow-hidden">
                                     <!-- Avatar stack placeholder or count -->
                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-slate-100 ring-2 ring-white text-xs font-bold text-slate-600" title="<?= $p['jumlah_pemodal'] ?> Investor">
                                        <?= $p['jumlah_pemodal'] ?>
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right text-slate-500 text-xs font-medium">
                                <?= date('d M Y', strtotime($p['created_at'])) ?>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-300 group-hover:bg-blue-50 group-hover:text-blue-600 transition-all">
                                    <ion-icon name="chevron-forward"></ion-icon>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
