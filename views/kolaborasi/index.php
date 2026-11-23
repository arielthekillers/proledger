<?php
/**
 * ProLedger - Daftar Ruang Kolaborasi Paket Bersama
 */
?>

<!-- Header & Actions -->
<div class="mb-5 flex flex-wrap items-center justify-between gap-3">
    <div>
        <div class="flex items-center gap-2 mb-0.5 flex-wrap">
            <h1 class="text-lg md:text-xl font-extrabold text-slate-900 tracking-tight">Kolaborasi Paket Bersama</h1>
            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-indigo-50 text-indigo-700 border border-indigo-200">
                <?= count($joinedKolabs) ?> Ruang Aktif
            </span>
        </div>
        <p class="text-xs text-slate-400">
            Pantau progres paket lintas perusahaan secara bersama-sama dan bagikan link monitoring read-only.
        </p>
    </div>

    <div class="flex items-center gap-2">
        <button type="button"
                data-modal-open="modalJoinKolab"
                class="inline-flex items-center gap-1.5 px-3 py-2 bg-white hover:bg-slate-50 text-slate-700 font-semibold rounded-xl border border-slate-200 transition-all text-xs shadow-2xs cursor-pointer">
            <ion-icon name="enter-outline" class="text-sm text-indigo-600"></ion-icon>
            <span>Gabung via Kode</span>
        </button>

        <button type="button"
                data-modal-open="modalBuatKolab"
                class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl text-xs shadow-sm shadow-blue-600/20 transition-all hover:scale-[1.01] active:scale-[0.98] cursor-pointer">
            <ion-icon name="add-circle-outline" class="text-base"></ion-icon>
            <span>Buat Kolaborasi</span>
        </button>
    </div>
</div>

<!-- Pending Invitations -->
<?php if (!empty($pendingInvites)): ?>
    <div class="mb-5 space-y-2.5">
        <h2 class="text-xs font-bold text-amber-700 uppercase tracking-wider flex items-center gap-1.5">
            <ion-icon name="mail-unread-outline" class="text-sm"></ion-icon>
            <span>Undangan Kolaborasi Masuk (<?= count($pendingInvites) ?>)</span>
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <?php foreach ($pendingInvites as $inv): ?>
                <div class="bg-amber-50/70 border border-amber-200 rounded-2xl p-4 flex items-center justify-between gap-3">
                    <div class="min-w-0">
                        <div class="font-bold text-slate-900 text-sm truncate"><?= htmlspecialchars($inv['nama']) ?></div>
                        <div class="text-[11px] text-slate-500 mt-0.5">
                            Diundang oleh <strong><?= htmlspecialchars($inv['owner_name'] ?? 'Koordinator') ?></strong>
                            <?php if (!empty($inv['instansi'])): ?>
                                &bull; <?= htmlspecialchars($inv['instansi']) ?>
                            <?php endif; ?>
                            &bull; TA <?= (int)$inv['tahun_anggaran'] ?>
                        </div>
                    </div>
                    <div class="flex items-center gap-1.5 shrink-0">
                        <a href="<?= APP_URL ?>/kolaborasi/respond/<?= $inv['id'] ?>/accept"
                           class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-semibold transition-colors">
                            Terima
                        </a>
                        <a href="<?= APP_URL ?>/kolaborasi/respond/<?= $inv['id'] ?>/decline"
                           class="px-3 py-1.5 bg-white hover:bg-slate-50 text-slate-600 border border-slate-200 rounded-xl text-xs font-semibold transition-colors">
                            Tolak
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>

<!-- Joined Collaboration Rooms -->
<?php if (empty($joinedKolabs)): ?>
    <div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 p-12 text-center">
        <div class="w-14 h-14 bg-indigo-50 text-indigo-500 rounded-2xl flex items-center justify-center mx-auto mb-3 text-3xl">
            <ion-icon name="people-outline"></ion-icon>
        </div>
        <h3 class="text-sm font-bold text-slate-800 mb-1">Belum Ada Ruang Kolaborasi</h3>
        <p class="text-xs text-slate-400 max-w-md mx-auto mb-5 leading-relaxed">
            Buat ruang kolaborasi untuk memantau paket kegiatan bersama beberapa perusahaan sekaligus (misal: 5 perusahaan × 10 paket Dinas Pendidikan), atau gabung menggunakan kode undangan dari rekan Anda.
        </p>
        <div class="flex items-center justify-center gap-2">
            <button type="button" data-modal-open="modalJoinKolab"
                    class="px-3.5 py-2 bg-white hover:bg-slate-50 text-slate-700 border border-slate-200 rounded-xl text-xs font-semibold cursor-pointer">
                Gabung via Kode
            </button>
            <button type="button" data-modal-open="modalBuatKolab"
                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-semibold shadow-xs cursor-pointer">
                + Buat Kolaborasi Pertama
            </button>
        </div>
    </div>
<?php else: ?>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <?php foreach ($joinedKolabs as $k): ?>
            <div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 p-5 flex flex-col justify-between hover:border-blue-300 hover:shadow-sm transition-all">
                <div>
                    <div class="flex items-start justify-between gap-2 mb-3">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500 to-blue-600 text-white flex items-center justify-center text-lg shrink-0 shadow-xs">
                                <ion-icon name="people"></ion-icon>
                            </div>
                            <div class="min-w-0">
                                <a href="<?= APP_URL ?>/kolaborasi/detail/<?= $k['id'] ?>"
                                   class="font-bold text-slate-900 hover:text-blue-600 text-sm block truncate transition-colors">
                                    <?= htmlspecialchars($k['nama']) ?>
                                </a>
                                <div class="text-[11px] text-slate-400 truncate">
                                    <?= htmlspecialchars($k['instansi'] ?: 'Monitoring Bersama') ?> &bull; TA <?= (int)$k['tahun_anggaran'] ?>
                                </div>
                            </div>
                        </div>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold shrink-0 <?= $k['my_role'] === 'owner' ? 'bg-indigo-50 text-indigo-700 border border-indigo-200' : 'bg-slate-100 text-slate-600' ?>">
                            <?= $k['my_role'] === 'owner' ? 'Koordinator' : 'Anggota' ?>
                        </span>
                    </div>

                    <?php if (!empty($k['deskripsi'])): ?>
                        <p class="text-xs text-slate-500 line-clamp-2 mb-3 leading-relaxed">
                            <?= htmlspecialchars($k['deskripsi']) ?>
                        </p>
                    <?php endif; ?>

                    <div class="grid grid-cols-3 gap-2 p-2.5 bg-slate-50 rounded-xl border border-slate-100 text-center mb-4">
                        <div>
                            <div class="text-xs font-extrabold text-slate-800"><?= (int)$k['total_anggota'] ?></div>
                            <div class="text-[10px] text-slate-400">Anggota</div>
                        </div>
                        <div class="border-x border-slate-200/80">
                            <div class="text-xs font-extrabold text-indigo-600"><?= (int)$k['total_groups'] ?></div>
                            <div class="text-[10px] text-slate-400">Group</div>
                        </div>
                        <div>
                            <div class="text-xs font-extrabold text-blue-600"><?= (int)$k['total_paket'] ?></div>
                            <div class="text-[10px] text-slate-400">Paket</div>
                        </div>
                    </div>
                </div>

                <div class="pt-3 border-t border-slate-100 flex items-center justify-between gap-2">
                    <span class="font-mono text-[10px] font-bold text-slate-500 bg-slate-100 px-2 py-1 rounded-md">
                        <?= htmlspecialchars($k['invite_code']) ?>
                    </span>
                    <a href="<?= APP_URL ?>/kolaborasi/detail/<?= $k['id'] ?>"
                       class="inline-flex items-center gap-1 px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-semibold transition-colors">
                        <span>Buka Dashboard</span>
                        <ion-icon name="arrow-forward-outline" class="text-xs"></ion-icon>
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<!-- MODAL: BUAT KOLABORASI BARU -->
<div id="modalBuatKolab" class="app-modal fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 transition-all duration-200 opacity-0 pointer-events-none hidden">
    <div class="modal-card bg-white rounded-2xl shadow-2xl border border-slate-100 max-w-md w-full transform scale-95 opacity-0 transition-all duration-200 overflow-hidden">
        <div class="px-5 py-4 bg-slate-50/80 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-blue-600 text-white flex items-center justify-center text-lg shadow-xs">
                    <ion-icon name="people-outline"></ion-icon>
                </div>
                <div>
                    <h3 class="font-bold text-slate-800 text-sm">Buat Ruang Kolaborasi</h3>
                    <p class="text-[11px] text-slate-400">Monitoring paket lintas perusahaan & Dinas</p>
                </div>
            </div>
            <button type="button" data-modal-close="modalBuatKolab" class="p-1.5 text-slate-400 hover:text-slate-700 rounded-lg cursor-pointer">
                <ion-icon name="close-outline" class="text-xl"></ion-icon>
            </button>
        </div>

        <form method="POST" action="<?= APP_URL ?>/kolaborasi/store" class="p-5 space-y-3.5">
            <?= csrfField() ?>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Kolaborasi / Program <span class="text-red-500">*</span></label>
                <input type="text" name="nama" required
                       class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-blue-500 text-xs focus:bg-white transition-all"
                       placeholder="Contoh: Paket Dinas Pendidikan Provinsi - Bontang">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Instansi / Dinas Pemberi</label>
                    <input type="text" name="instansi"
                           class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-blue-500 text-xs focus:bg-white transition-all"
                           placeholder="Dinas Pendidikan Prov.">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Tahun Anggaran</label>
                    <input type="number" name="tahun_anggaran" value="<?= date('Y') ?>" min="2020" max="2035"
                           class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-blue-500 text-xs focus:bg-white transition-all">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Keterangan</label>
                <textarea name="deskripsi" rows="2"
                          class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-blue-500 text-xs focus:bg-white transition-all"
                          placeholder="Catatan singkat ruang lingkup kolaborasi..."></textarea>
            </div>

            <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                <button type="button" data-modal-close="modalBuatKolab"
                        class="px-3.5 py-2 rounded-xl border border-slate-200 text-slate-600 font-semibold hover:bg-slate-50 text-xs cursor-pointer">
                    Batal
                </button>
                <button type="submit"
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl text-xs shadow-xs flex items-center gap-1.5 cursor-pointer">
                    <ion-icon name="checkmark-outline" class="text-sm"></ion-icon>
                    <span>Buat Kolaborasi</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL: GABUNG VIA KODE -->
<div id="modalJoinKolab" class="app-modal fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 transition-all duration-200 opacity-0 pointer-events-none hidden">
    <div class="modal-card bg-white rounded-2xl shadow-2xl border border-slate-100 max-w-sm w-full transform scale-95 opacity-0 transition-all duration-200 overflow-hidden">
        <div class="px-5 py-4 bg-slate-50/80 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-indigo-600 text-white flex items-center justify-center text-lg shadow-xs">
                    <ion-icon name="enter-outline"></ion-icon>
                </div>
                <div>
                    <h3 class="font-bold text-slate-800 text-sm">Gabung Kolaborasi</h3>
                    <p class="text-[11px] text-slate-400">Masukkan kode undangan dari koordinator</p>
                </div>
            </div>
            <button type="button" data-modal-close="modalJoinKolab" class="p-1.5 text-slate-400 hover:text-slate-700 rounded-lg cursor-pointer">
                <ion-icon name="close-outline" class="text-xl"></ion-icon>
            </button>
        </div>

        <form method="POST" action="<?= APP_URL ?>/kolaborasi/join" class="p-5 space-y-3.5">
            <?= csrfField() ?>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Kode Undangan <span class="text-red-500">*</span></label>
                <input type="text" name="invite_code" required
                       class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-indigo-500 text-sm font-mono uppercase tracking-wider text-center focus:bg-white transition-all"
                       placeholder="KOLAB-XXXXX">
            </div>

            <div class="pt-2 flex items-center justify-end gap-2">
                <button type="button" data-modal-close="modalJoinKolab"
                        class="px-3.5 py-2 rounded-xl border border-slate-200 text-slate-600 font-semibold hover:bg-slate-50 text-xs cursor-pointer">
                    Batal
                </button>
                <button type="submit"
                        class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl text-xs shadow-xs cursor-pointer">
                    Gabung Sekarang
                </button>
            </div>
        </form>
    </div>
</div>
