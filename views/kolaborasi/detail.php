<?php
/**
 * ProLedger - Dashboard Monitoring Kolaborasi
 * Menampilkan progres gabungan lintas perusahaan & nilai kontrak.
 * STRICT PRIVACY: Tidak menampilkan HPP, belanja toko, profit, atau potongan internal.
 */

$shareUrl = APP_URL . '/monitoring/' . $kolab['share_token'];

$renderRing = function (int $pct, string $label, string $strokeHex, string $textClass) {
    $r = 15;
    $c = round(2 * M_PI * $r, 2);
    $offset = round($c * (1 - max(0, min(100, $pct)) / 100), 2);
    $dur = max(0.35, round(($pct / 100) * 1.1, 2));
    ?>
    <div class="flex flex-col items-center">
        <div class="relative w-11 h-11 flex items-center justify-center">
            <svg class="w-11 h-11 -rotate-90" viewBox="0 0 38 38">
                <circle cx="19" cy="19" r="<?= $r ?>" fill="none" stroke="#e2e8f0" stroke-width="3.2"></circle>
                <circle cx="19" cy="19" r="<?= $r ?>" fill="none" stroke="<?= $strokeHex ?>" stroke-width="3.2"
                        stroke-linecap="round"
                        stroke-dasharray="<?= $c ?>"
                        stroke-dashoffset="<?= $c ?>"
                        data-ring-target="<?= $offset ?>"
                        style="transition: stroke-dashoffset <?= $dur ?>s cubic-bezier(0.22, 1, 0.36, 1);"></circle>
            </svg>
            <span class="absolute text-[9px] font-extrabold <?= $textClass ?>"><?= $pct ?>%</span>
        </div>
        <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider mt-0.5"><?= $label ?></span>
    </div>
    <?php
};

$renderTableRing = function (int $pct, string $strokeHex, string $textClass) {
    $r = 12;
    $c = round(2 * M_PI * $r, 2);
    $offset = round($c * (1 - max(0, min(100, $pct)) / 100), 2);
    $dur = max(0.35, round(($pct / 100) * 1.1, 2));
    ?>
    <div class="relative w-8 h-8 inline-flex items-center justify-center">
        <svg class="w-8 h-8 -rotate-90" viewBox="0 0 32 32">
            <circle cx="16" cy="16" r="<?= $r ?>" fill="none" stroke="#e2e8f0" stroke-width="2.8"></circle>
            <circle cx="16" cy="16" r="<?= $r ?>" fill="none" stroke="<?= $strokeHex ?>" stroke-width="2.8"
                    stroke-linecap="round"
                    stroke-dasharray="<?= $c ?>"
                    stroke-dashoffset="<?= $c ?>"
                    data-ring-target="<?= $offset ?>"
                    style="transition: stroke-dashoffset <?= $dur ?>s cubic-bezier(0.22, 1, 0.36, 1);"></circle>
        </svg>
        <span class="absolute text-[8px] font-extrabold <?= $textClass ?>"><?= $pct ?>%</span>
    </div>
    <?php
};
?>

<!-- ========================================== -->
<!-- 1. TOP BAR & ACTIONS                       -->
<!-- ========================================== -->
<div class="mb-4 flex flex-wrap items-center justify-between gap-3">
    <div class="flex items-center gap-2 flex-wrap">
        <a href="<?= APP_URL ?>/kolaborasi"
           class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white hover:bg-slate-50 text-slate-700 font-semibold rounded-xl border border-slate-200 transition-all text-xs shadow-2xs">
            <ion-icon name="arrow-back-outline" class="text-sm"></ion-icon>
            <span>Kembali</span>
        </a>

        <button type="button" onclick="copyText('<?= htmlspecialchars($kolab['invite_code']) ?>', 'Kode undangan disalin!')"
                class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 border border-indigo-200 rounded-lg font-mono text-[11px] font-bold transition-colors cursor-pointer"
                title="Klik untuk menyalin kode undangan">
            <ion-icon name="key-outline" class="text-xs"></ion-icon>
            <span><?= htmlspecialchars($kolab['invite_code']) ?></span>
            <ion-icon name="copy-outline" class="text-[10px] opacity-70"></ion-icon>
        </button>
    </div>

    <div class="flex items-center gap-2 flex-wrap">
        <?php if (!empty($kolab['share_enabled'])): ?>
            <button type="button" onclick="copyText('<?= htmlspecialchars($shareUrl) ?>', 'Link monitoring disalin!')"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 rounded-xl text-xs font-semibold transition-colors cursor-pointer">
                <ion-icon name="link-outline" class="text-sm"></ion-icon>
                <span>Share Link</span>
            </button>
            <a href="<?= htmlspecialchars($shareUrl) ?>" target="_blank"
               class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-white hover:bg-slate-50 text-slate-600 border border-slate-200 rounded-xl text-xs font-semibold transition-colors"
               title="Buka tampilan monitoring publik">
                <ion-icon name="open-outline" class="text-sm"></ion-icon>
            </a>
        <?php endif; ?>

        <button type="button" data-modal-open="modalKolabAnggotaGroup"
                class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-semibold shadow-xs transition-all cursor-pointer">
            <ion-icon name="git-network-outline" class="text-sm"></ion-icon>
            <span>Anggota & Share Group (<?= count($linkedGroups) ?>)</span>
        </button>

        <?php if ($isOwner): ?>
            <button type="button" data-modal-open="modalEditKolab"
                    class="p-1.5 bg-white hover:bg-amber-50 text-slate-600 hover:text-amber-600 border border-slate-200 rounded-xl transition-colors cursor-pointer"
                    title="Pengaturan Kolaborasi">
                <ion-icon name="settings-outline" class="text-base"></ion-icon>
            </button>
        <?php endif; ?>
    </div>
</div>

<!-- ========================================== -->
<!-- 2. HERO MONITORING SUMMARY CARD (COMPACT)  -->
<!-- ========================================== -->
<div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 px-4 py-3 mb-3.5">
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3">
        <div class="flex items-center gap-3 flex-1 min-w-0">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500 to-blue-600 text-white flex items-center justify-center text-xl shadow-xs shrink-0">
                <ion-icon name="people"></ion-icon>
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 flex-wrap">
                    <h1 class="text-base md:text-lg font-extrabold text-slate-900 tracking-tight leading-tight">
                        <?= htmlspecialchars($kolab['nama']) ?>
                    </h1>
                    <span class="px-2 py-0.2 rounded-full text-[10px] font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                        TA <?= (int)$kolab['tahun_anggaran'] ?>
                    </span>
                </div>
                <p class="text-[11px] text-slate-400 mt-0.5 flex items-center gap-1.5 flex-wrap">
                    <?php if (!empty($kolab['instansi'])): ?>
                        <span class="inline-flex items-center gap-1 text-slate-600 font-semibold">
                            <ion-icon name="business-outline"></ion-icon>
                            <?= htmlspecialchars($kolab['instansi']) ?>
                        </span>
                        <span class="text-slate-300">&bull;</span>
                    <?php endif; ?>
                    <span><?= $summary['total_perusahaan'] ?> Perusahaan</span>
                    <span class="text-slate-300">&bull;</span>
                    <span><?= $summary['total_paket'] ?> Paket</span>
                    <span class="text-slate-300">&bull;</span>
                    <span class="font-mono font-bold text-slate-700"><?= formatRupiah($summary['total_kontrak']) ?></span>
                </p>
                <?php if (!empty($kolab['deskripsi'])): ?>
                    <p class="text-[11px] text-slate-500 mt-1 truncate" title="<?= htmlspecialchars($kolab['deskripsi']) ?>">
                        <?= htmlspecialchars($kolab['deskripsi']) ?>
                    </p>
                <?php endif; ?>
            </div>
        </div>

        <!-- 4 Progress Rings + Status Inline -->
        <div class="flex flex-wrap items-center gap-4 pt-2 lg:pt-0 border-t lg:border-t-0 lg:border-l border-slate-100 lg:pl-5 shrink-0">
            <div class="flex items-center gap-3">
                <?php $renderRing($summary['order_pct'], 'Order', '#f59e0b', 'text-amber-600'); ?>
                <?php $renderRing($summary['dist_pct'], 'Dist', '#6366f1', 'text-indigo-600'); ?>
                <?php $renderRing($summary['dok_pct'], 'Dok', '#10b981', 'text-emerald-600'); ?>
                <?php $renderRing($summary['bast_pct'], 'BAST', '#a855f7', 'text-purple-600'); ?>
            </div>
            <div class="flex items-center gap-1.5 text-[10px]">
                <span class="px-2 py-0.5 rounded-full font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                    <?= $summary['paket_selesai'] ?> Cair
                </span>
                <span class="px-2 py-0.5 rounded-full font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                    <?= $summary['paket_proses'] ?> Proses
                </span>
                <?php if ($summary['paket_belum'] > 0): ?>
                    <span class="px-2 py-0.5 rounded-full font-semibold bg-slate-100 text-slate-600 border border-slate-200">
                        <?= $summary['paket_belum'] ?> Belum
                    </span>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Quick Prompt if User Hasn't Shared a Group Yet -->
<?php if (empty($myLinkedGroups)): ?>
    <div class="mb-4 bg-blue-50/80 border border-blue-200 rounded-2xl p-3.5 px-4 flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center gap-2.5 text-xs text-blue-800">
            <ion-icon name="information-circle-outline" class="text-lg text-blue-600 shrink-0"></ion-icon>
            <span>Anda belum menghubungkan Group Paket perusahaan Anda ke kolaborasi ini.</span>
        </div>
        <button type="button" data-modal-open="modalKolabAnggotaGroup"
                class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-semibold cursor-pointer">
            + Share Group Paket Saya
        </button>
    </div>
<?php endif; ?>

<!-- ========================================== -->
<!-- 3. TABEL MONITORING SEMUA PAKET            -->
<!-- ========================================== -->
<?php
$modalBaseUrl = APP_URL . '/kolaborasi/modalView/' . $kolab['id'];
require __DIR__ . '/_paket_table.php';
?>

<!-- ========================================== -->
<!-- 4. REKAP PER PERUSAHAAN                    -->
<!-- ========================================== -->
<?php if (!empty($companyStats)): ?>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-3 mt-4">
        <?php foreach ($companyStats as $cs): ?>
            <button type="button" onclick="filterByCompany('<?= htmlspecialchars(strtolower($cs['nama']), ENT_QUOTES) ?>')"
                    class="text-left bg-white rounded-2xl shadow-xs border border-slate-200/80 p-3.5 hover:border-blue-300 hover:shadow-sm transition-all cursor-pointer">
                <div class="flex items-center justify-between gap-2 mb-1.5">
                    <span class="font-bold text-slate-800 text-xs truncate"><?= htmlspecialchars($cs['nama']) ?></span>
                    <span class="px-1.5 py-0.5 rounded-md bg-blue-50 text-blue-700 text-[10px] font-bold shrink-0">
                        <?= $cs['total_paket'] ?> Paket
                    </span>
                </div>
                <div class="font-mono font-bold text-slate-900 text-xs mb-2">
                    <?= formatRupiah($cs['total_kontrak']) ?>
                </div>
                <div class="flex items-center justify-between text-[10px] text-slate-400 mb-1">
                    <span>Progres Rata-rata</span>
                    <span class="font-bold text-slate-700"><?= $cs['avg_pct'] ?>%</span>
                </div>
                <div class="w-full h-1.5 bg-slate-100 rounded-full overflow-hidden mb-1.5">
                    <div class="h-full bg-gradient-to-r from-blue-500 to-indigo-600 rounded-full" style="width: <?= $cs['avg_pct'] ?>%"></div>
                </div>
                <div class="flex items-center justify-between text-[10px] text-slate-400">
                    <span>Order <?= $cs['order_pct'] ?>% &bull; Dist <?= $cs['dist_pct'] ?>%</span>
                    <span>BAST <?= $cs['bast_pct'] ?>%</span>
                </div>
            </button>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<!-- ========================================== -->
<!-- 5. MODAL: ANGGOTA & SHARE GROUP            -->
<!-- ========================================== -->
<div id="modalKolabAnggotaGroup" class="app-modal fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 transition-all duration-200 opacity-0 pointer-events-none hidden">
    <div class="modal-card bg-white rounded-2xl shadow-2xl border border-slate-100 max-w-2xl w-full transform scale-95 opacity-0 transition-all duration-200 overflow-hidden max-h-[90vh] flex flex-col">
        <div class="px-5 py-4 bg-slate-50/80 border-b border-slate-100 flex items-center justify-between shrink-0">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-blue-600 text-white flex items-center justify-center text-lg">
                    <ion-icon name="git-network-outline"></ion-icon>
                </div>
                <div>
                    <h3 class="font-bold text-slate-800 text-sm">Anggota & Share Group Paket</h3>
                    <p class="text-[11px] text-slate-400">Undang rekan perusahaan & pilih Group Paket untuk ditampilkan</p>
                </div>
            </div>
            <button type="button" data-modal-close="modalKolabAnggotaGroup" class="p-1.5 text-slate-400 hover:text-slate-700 rounded-lg cursor-pointer">
                <ion-icon name="close-outline" class="text-xl"></ion-icon>
            </button>
        </div>

        <div class="p-5 space-y-5 overflow-y-auto">
            <!-- Section A: Share My Group -->
            <div class="bg-blue-50/60 border border-blue-200/80 rounded-xl p-4">
                <h4 class="text-xs font-bold text-slate-800 mb-2 flex items-center gap-1.5">
                    <ion-icon name="folder-open-outline" class="text-blue-600"></ion-icon>
                    <span>Hubungkan Group Paket Perusahaan Saya</span>
                </h4>
                <?php if (!empty($myAvailableGroups)):
                    $groupsByCompany = [];
                    foreach ($myAvailableGroups as $ag) {
                        $compLabel = $ag['perusahaan_nama'] ?: 'Umum / Semua Perusahaan';
                        $groupsByCompany[$compLabel][] = $ag;
                    }
                    ksort($groupsByCompany, SORT_NATURAL | SORT_FLAG_CASE);
                ?>
                    <form method="POST" action="<?= APP_URL ?>/kolaborasi/linkGroup/<?= $kolab['id'] ?>" class="space-y-3">
                        <?= csrfField() ?>
                        <div class="max-h-56 overflow-y-auto space-y-2.5 pr-1">
                            <?php foreach ($groupsByCompany as $compLabel => $cGroups): ?>
                                <div class="bg-white rounded-xl border border-slate-200/80 overflow-hidden">
                                    <div class="px-3 py-1.5 bg-slate-50 border-b border-slate-100 flex items-center justify-between">
                                        <span class="text-[11px] font-bold text-slate-700 flex items-center gap-1.5">
                                            <ion-icon name="business-outline" class="text-blue-600"></ion-icon>
                                            <?= htmlspecialchars($compLabel) ?>
                                        </span>
                                        <span class="text-[10px] text-slate-400"><?= count($cGroups) ?> Group</span>
                                    </div>
                                    <div class="divide-y divide-slate-100">
                                        <?php foreach ($cGroups as $ag): ?>
                                            <label class="flex items-center justify-between gap-3 px-3 py-2 hover:bg-blue-50/40 cursor-pointer transition-colors">
                                                <div class="flex items-center gap-2.5 min-w-0">
                                                    <input type="checkbox" name="group_ids[]" value="<?= $ag['id'] ?>"
                                                           class="w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500 cursor-pointer shrink-0">
                                                    <div class="min-w-0">
                                                        <div class="text-xs font-semibold text-slate-800 truncate">
                                                            <?= htmlspecialchars($ag['nama_group']) ?>
                                                        </div>
                                                        <?php if (!empty($ag['kode_group'])): ?>
                                                            <div class="text-[10px] font-mono text-slate-400"><?= htmlspecialchars($ag['kode_group']) ?></div>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                                <span class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-600 text-[10px] font-semibold shrink-0">
                                                    <?= (int)$ag['total_paket'] ?> Paket
                                                </span>
                                            </label>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="flex justify-end">
                            <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-semibold cursor-pointer inline-flex items-center gap-1.5 shadow-xs">
                                <ion-icon name="checkmark-circle-outline" class="text-sm"></ion-icon>
                                <span>+ Share Group Terpilih</span>
                            </button>
                        </div>
                    </form>
                <?php else: ?>
                    <p class="text-[11px] text-slate-500">Semua Group Paket milik Anda sudah terhubung atau belum ada Group Paket yang dibuat.</p>
                <?php endif; ?>
            </div>

            <!-- Section B: Linked Groups List -->
            <div>
                <h4 class="text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    Group Paket Terhubung (<?= count($linkedGroups) ?>)
                </h4>
                <?php if (empty($linkedGroups)): ?>
                    <p class="text-xs text-slate-400 italic">Belum ada group yang dihubungkan.</p>
                <?php else: ?>
                    <div class="space-y-2">
                        <?php foreach ($linkedGroups as $lg): ?>
                            <div class="flex items-center justify-between gap-3 p-2.5 bg-slate-50 rounded-xl border border-slate-200/70">
                                <div class="min-w-0">
                                    <div class="font-bold text-slate-800 text-xs truncate"><?= htmlspecialchars($lg['nama_group']) ?></div>
                                    <div class="text-[10px] text-slate-500">
                                        <?= htmlspecialchars($lg['perusahaan_nama'] ?: 'Umum') ?> &bull; Oleh <?= htmlspecialchars($lg['user_name']) ?>
                                    </div>
                                </div>
                                <?php if ($isOwner || (int)$lg['user_id'] === $userId): ?>
                                    <a href="<?= APP_URL ?>/kolaborasi/unlinkGroup/<?= $kolab['id'] ?>/<?= $lg['group_id'] ?>"
                                       class="px-2.5 py-1 text-[11px] font-semibold text-rose-600 hover:bg-rose-50 rounded-lg border border-rose-200 shrink-0">
                                        Lepas
                                    </a>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Section C: Invite Member (Owner Only) -->
            <?php if ($isOwner): ?>
                <div class="pt-4 border-t border-slate-100">
                    <h4 class="text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Undang Anggota via Email</h4>
                    <form method="POST" action="<?= APP_URL ?>/kolaborasi/invite/<?= $kolab['id'] ?>" class="flex flex-col sm:flex-row gap-2 mb-3">
                        <?= csrfField() ?>
                        <input type="email" name="email" required
                               placeholder="email.rekan@perusahaan.com"
                               class="flex-1 px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-indigo-500 focus:bg-white">
                        <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-semibold shrink-0 cursor-pointer">
                            Kirim Undangan
                        </button>
                    </form>
                </div>
            <?php endif; ?>

            <!-- Section D: Member List -->
            <div>
                <h4 class="text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    Daftar Anggota (<?= count($members) ?>)
                </h4>
                <div class="space-y-1.5">
                    <?php foreach ($members as $m): ?>
                        <div class="flex items-center justify-between gap-2 p-2.5 rounded-xl border border-slate-100 bg-white">
                            <div class="min-w-0">
                                <div class="font-semibold text-slate-800 text-xs flex items-center gap-1.5">
                                    <span><?= htmlspecialchars($m['name']) ?></span>
                                    <span class="px-1.5 py-0.2 rounded text-[9px] font-bold <?= $m['role'] === 'owner' ? 'bg-indigo-50 text-indigo-700' : 'bg-slate-100 text-slate-600' ?>">
                                        <?= $m['role'] === 'owner' ? 'Koordinator' : 'Anggota' ?>
                                    </span>
                                </div>
                                <div class="text-[10px] text-slate-400"><?= htmlspecialchars($m['email']) ?> &bull; <?= (int)$m['linked_groups_count'] ?> Group</div>
                            </div>
                            <div class="flex items-center gap-2 shrink-0">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold <?= $m['status'] === 'joined' ? 'bg-emerald-50 text-emerald-700' : ($m['status'] === 'pending' ? 'bg-amber-50 text-amber-700' : 'bg-rose-50 text-rose-700') ?>">
                                    <?= $m['status'] === 'joined' ? 'Bergabung' : ($m['status'] === 'pending' ? 'Pending' : 'Ditolak') ?>
                                </span>
                                <?php if ($isOwner && $m['role'] !== 'owner'): ?>
                                    <a href="<?= APP_URL ?>/kolaborasi/removeMember/<?= $kolab['id'] ?>/<?= $m['user_id'] ?>"
                                       class="p-1 text-slate-400 hover:text-rose-600 rounded-lg" title="Keluarkan anggota">
                                        <ion-icon name="trash-outline"></ion-icon>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- 6. MODAL: PENGATURAN KOLABORASI (OWNER)    -->
<!-- ========================================== -->
<?php if ($isOwner): ?>
<div id="modalEditKolab" class="app-modal fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 transition-all duration-200 opacity-0 pointer-events-none hidden">
    <div class="modal-card bg-white rounded-2xl shadow-2xl border border-slate-100 max-w-md w-full transform scale-95 opacity-0 transition-all duration-200 overflow-hidden">
        <div class="px-5 py-4 bg-slate-50/80 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-bold text-slate-800 text-sm">Pengaturan Ruang Kolaborasi</h3>
            <button type="button" data-modal-close="modalEditKolab" class="p-1.5 text-slate-400 hover:text-slate-700 rounded-lg cursor-pointer">
                <ion-icon name="close-outline" class="text-xl"></ion-icon>
            </button>
        </div>

        <form method="POST" action="<?= APP_URL ?>/kolaborasi/update/<?= $kolab['id'] ?>" class="p-5 space-y-3.5">
            <?= csrfField() ?>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Kolaborasi <span class="text-red-500">*</span></label>
                <input type="text" name="nama" required value="<?= htmlspecialchars($kolab['nama']) ?>"
                       class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-blue-500">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Instansi / Dinas</label>
                    <input type="text" name="instansi" value="<?= htmlspecialchars($kolab['instansi'] ?? '') ?>"
                           class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Tahun Anggaran</label>
                    <input type="number" name="tahun_anggaran" value="<?= (int)$kolab['tahun_anggaran'] ?>"
                           class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-blue-500">
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Keterangan</label>
                <textarea name="deskripsi" rows="2" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-blue-500"><?= htmlspecialchars($kolab['deskripsi'] ?? '') ?></textarea>
            </div>

            <!-- Public Read-Only Link Controls -->
            <div class="p-3 bg-slate-50 rounded-xl border border-slate-200 space-y-2">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-700">Link Monitoring (Read-Only)</span>
                    <a href="<?= APP_URL ?>/kolaborasi/toggleShare/<?= $kolab['id'] ?>"
                       class="px-2 py-0.5 rounded-full text-[10px] font-bold <?= $kolab['share_enabled'] ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-200 text-slate-600' ?>">
                        <?= $kolab['share_enabled'] ? 'Aktif (Klik Nonaktifkan)' : 'Nonaktif (Klik Aktifkan)' ?>
                    </a>
                </div>
                <div class="flex items-center gap-1.5">
                    <input type="text" readonly value="<?= htmlspecialchars($shareUrl) ?>"
                           class="flex-1 px-2.5 py-1.5 bg-white border border-slate-200 rounded-lg text-[10px] font-mono text-slate-600">
                    <a href="<?= APP_URL ?>/kolaborasi/regenerateShare/<?= $kolab['id'] ?>"
                       class="px-2.5 py-1.5 bg-white hover:bg-slate-100 border border-slate-200 rounded-lg text-[10px] font-semibold text-slate-700 shrink-0"
                       title="Buat token link baru">
                        Reset Link
                    </a>
                </div>
            </div>

            <div class="pt-3 border-t border-slate-100 flex items-center justify-between gap-2">
                <a href="<?= APP_URL ?>/kolaborasi/delete/<?= $kolab['id'] ?>"
                   data-confirm-delete="<?= htmlspecialchars($kolab['nama']) ?>"
                   data-type="Ruang Kolaborasi"
                   class="px-3 py-2 text-rose-600 hover:bg-rose-50 rounded-xl text-xs font-semibold">
                    Hapus Ruang
                </a>
                <div class="flex items-center gap-2">
                    <button type="button" data-modal-close="modalEditKolab"
                            class="px-3.5 py-2 rounded-xl border border-slate-200 text-slate-600 font-semibold text-xs cursor-pointer">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl text-xs cursor-pointer">
                        Simpan
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function () {
    requestAnimationFrame(() => {
        document.querySelectorAll('circle[data-ring-target]').forEach(el => {
            el.style.strokeDashoffset = el.getAttribute('data-ring-target');
        });
    });
});

function copyText(txt, msg) {
    navigator.clipboard.writeText(txt).then(() => {
        if (window.ProToast) ProToast.success(msg);
        else alert(msg);
    });
}

function filterByCompany(companyLower) {
    const sel = document.getElementById('filterKolabCompany');
    if (!sel) return;
    sel.value = (sel.value === companyLower) ? '' : companyLower;
    filterKolabTable();
}

function formatRpKolab(num) {
    const n = Math.round(num || 0);
    return (n < 0 ? '-Rp ' : 'Rp ') + Math.abs(n).toLocaleString('id-ID');
}

function filterKolabTable() {
    const q = (document.getElementById('searchKolabPaket')?.value || '').toLowerCase().trim();
    const comp = document.getElementById('filterKolabCompany')?.value || '';
    const status = document.getElementById('filterKolabStatus')?.value || '';

    const rows = document.querySelectorAll('.kolab-paket-row');
    let vis = 0, sKontrak = 0, sItems = 0, sUnits = 0;

    rows.forEach(row => {
        const show = (!q || (row.dataset.search || '').includes(q))
                  && (!comp || (row.dataset.company || '') === comp)
                  && (!status || (row.dataset.status || '') === status);
        row.style.display = show ? '' : 'none';
        if (show) {
            vis++;
            sKontrak += parseFloat(row.dataset.kontrak || 0);
            sItems   += parseInt(row.dataset.items || 0, 10);
            sUnits   += parseFloat(row.dataset.units || 0);
        }
    });

    const setTxt = (id, t) => { const el = document.getElementById(id); if (el) el.textContent = t; };
    setTxt('kolabVisibleCount', vis);
    setTxt('kolabFootCount', vis);
    setTxt('kolabFootKontrak', formatRpKolab(sKontrak));
    setTxt('kolabFootKomponen', sItems + ' Item / ' + sUnits.toLocaleString('id-ID') + ' Unit');
}

function resetKolabFilter() {
    const s = document.getElementById('searchKolabPaket');
    const c = document.getElementById('filterKolabCompany');
    const st = document.getElementById('filterKolabStatus');
    if (s) s.value = '';
    if (c) c.value = '';
    if (st) st.value = '';
    filterKolabTable();
}
</script>
