<!-- Header & Back -->
<div class="mb-6 flex items-center justify-between">
    <a href="<?= APP_URL ?>/pemodal" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-slate-200 rounded-xl text-slate-600 hover:bg-slate-50 hover:text-blue-600 transition-colors shadow-sm">
        <ion-icon name="arrow-back-outline"></ion-icon>
        <span>Kembali ke Daftar</span>
    </a>
    <div class="flex gap-3">
        <a href="<?= APP_URL ?>/pemodal/edit/<?= $user['id'] ?>" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-colors shadow-sm font-semibold">
            <ion-icon name="create-outline"></ion-icon>
            <span>Edit Profil</span>
        </a>
    </div>
</div>

<!-- Profile Hero -->
<div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 md:p-8 mb-8 relative overflow-hidden">
    <!-- Decorative BG -->
    <div class="absolute top-0 right-0 w-64 h-64 bg-gradient-to-br from-blue-50 to-blue-100 rounded-bl-full -mr-16 -mt-16 opacity-50"></div>
    
    <div class="relative flex flex-col md:flex-row items-center md:items-start gap-6 md:gap-8">
        <!-- Avatar -->
        <div class="w-24 h-24 md:w-32 md:h-32 rounded-3xl bg-gradient-to-br from-blue-600 to-blue-800 text-white flex items-center justify-center text-4xl md:text-5xl font-bold shadow-xl shadow-blue-600/20 shrink-0">
            <?= strtoupper(substr($user['nama'], 0, 1)) ?>
        </div>
        
        <!-- Info -->
        <div class="flex-1 text-center md:text-left space-y-2">
            <h1 class="text-2xl md:text-3xl font-bold text-slate-800"><?= htmlspecialchars($user['nama']) ?></h1>
            <div class="flex flex-wrap items-center justify-center md:justify-start gap-4 text-slate-500">
                <div class="flex items-center gap-2 bg-slate-50 px-3 py-1.5 rounded-lg border border-slate-100">
                    <ion-icon name="mail-outline" class="text-blue-500"></ion-icon>
                    <span><?= htmlspecialchars($user['email'] ?? 'Belum ada email') ?></span>
                </div>
                <div class="flex items-center gap-2 bg-slate-50 px-3 py-1.5 rounded-lg border border-slate-100">
                    <ion-icon name="call-outline" class="text-blue-500"></ion-icon>
                    <span><?= htmlspecialchars($user['telepon'] ?? 'Belum ada telepon') ?></span>
                </div>
                <!-- Login Access Status -->
                <div class="flex items-center gap-2 px-3 py-1.5 rounded-lg border <?= $login_access ? 'bg-emerald-50 border-emerald-100 text-emerald-700' : 'bg-slate-50 border-slate-100 text-slate-500' ?>">
                    <ion-icon name="<?= $login_access ? 'key' : 'lock-closed-outline' ?>"></ion-icon>
                    <span><?= $login_access ? 'Login Active' : 'No Login Access' ?></span>
                </div>
            </div>
            
            <!-- Generate Access Button -->
            <?php if (!$login_access && !empty($user['email'])): ?>
                <form action="<?= APP_URL ?>/pemodal/generateAccess/<?= $user['id'] ?>" method="POST" class="mt-4" onsubmit="return confirm('Generate login access for <?= htmlspecialchars($user['nama']) ?>?');">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                    <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-slate-800 text-white text-sm rounded-lg hover:bg-slate-900 transition-colors shadow-lg shadow-slate-800/20">
                        <ion-icon name="key-outline"></ion-icon>
                        Create Login Access
                    </button>
                </form>
            <?php endif; ?>
        </div>
        
        <!-- Quick Stat -->
        <div class="bg-gradient-to-br from-emerald-500 to-emerald-600 p-6 rounded-2xl text-white shadow-lg min-w-[200px] text-center md:text-right">
            <div class="text-emerald-100 text-sm font-medium mb-1">Total Investasi</div>
            <div class="text-2xl md:text-3xl font-bold"><?= formatRupiah($total_modal) ?></div>
            <div class="text-emerald-100 text-xs mt-2 bg-white/20 inline-block px-2 py-0.5 rounded-lg">
                <?= count($paket) ?> Paket Aktif
            </div>
        </div>
    </div>
</div>

<!-- Tabs Container -->
<div class="bg-white rounded-2xl shadow-sm border border-slate-200">
    <!-- Header -->
    <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
        <h3 class="text-lg font-bold text-slate-800 flex items-center gap-2">
            <ion-icon name="briefcase-outline" class="text-blue-500"></ion-icon>
            Portofolio Investasi
        </h3>
    </div>

    <!-- Table -->
    <?php if (empty($modal_detail)): ?>
        <div class="p-12 text-center">
            <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4">
                <ion-icon name="folder-open-outline" class="text-3xl text-slate-400"></ion-icon>
            </div>
            <h3 class="text-lg font-bold text-slate-800 mb-1">Belum Ada Investasi</h3>
            <p class="text-slate-500">Pemodal ini belum tergabung dalam paket kegiatan apapun.</p>
        </div>
    <?php else: ?>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/50 border-b border-slate-200">
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Nama Paket</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-right">Modal Pasal</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-right">Modal Belanja</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-right">Total Investasi</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-right">Est. Keuntungan</th>
                        <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php foreach ($modal_detail as $md): ?>
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-6 py-4">
                                <!-- Since modal_detail might not have packet name directly if relying only on modal table, 
                                     we need to cross reference or assume the model joins it. 
                                     Checking ModalDetail model, likely fetches packet name. -->
                                <div class="font-semibold text-slate-800"><?= htmlspecialchars($md['nama_paket'] ?? 'Paket #' . $md['paket_id']) ?></div>
                                <div class="text-xs text-slate-500 mt-0.5">Joined recently</div>
                            </td>
                            <td class="px-6 py-4 text-right text-slate-600 font-medium">
                                <?= formatRupiah($md['modal_pasal']) ?>
                            </td>
                            <td class="px-6 py-4 text-right text-slate-600 font-medium">
                                <?= formatRupiah($md['modal_belanja']) ?>
                            </td>
                            <td class="px-6 py-4 text-right font-bold text-emerald-600">
                                <?= formatRupiah($md['total_modal']) ?>
                            </td>
                            <td class="px-6 py-4 text-right font-bold text-blue-500">
                                <?php 
                                    $pagu = $md['pagu'] ?? 0;
                                    $jml_pemodal = $md['jumlah_pemodal'] > 0 ? $md['jumlah_pemodal'] : 1;
                                    $total_modal_person = $md['total_modal'];
                                    $est_profit = ($pagu / $jml_pemodal) - $total_modal_person;
                                    echo formatRupiah($est_profit);
                                ?>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <a href="<?= APP_URL ?>/paket/detail/<?= $md['paket_id'] ?>?from=pemodal&pemodal_id=<?= $user['id'] ?>" class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-white border border-slate-200 text-slate-400 hover:text-blue-600 hover:border-blue-200 transition-colors">
                                    <ion-icon name="arrow-forward"></ion-icon>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot class="bg-slate-50 border-t border-slate-200">
                    <tr>
                        <td class="px-6 py-4 font-bold text-slate-800">TOTAL PORTFOLIO</td>
                        <td class="px-6 py-4 text-right font-bold text-slate-800"><?= formatRupiah(array_sum(array_column($modal_detail, 'modal_pasal'))) ?></td>
                        <td class="px-6 py-4 text-right font-bold text-slate-800"><?= formatRupiah(array_sum(array_column($modal_detail, 'modal_belanja'))) ?></td>
                        <td class="px-6 py-4 text-right font-bold text-emerald-600 text-lg"><?= formatRupiah($total_modal) ?></td>
                        <td></td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    <?php endif; ?>
</div>
