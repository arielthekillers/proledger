<?php
/**
 * ProLedger - Detail Group Paket Kegiatan & Roll-up Keuangan
 * Menampilkan rekapitulasi finansial gabungan dari semua paket anggota
 */

$totalAcuan = $stats['total_pagu_nego'] > 0 ? $stats['total_pagu_nego'] : $stats['total_pagu'];
?>

<!-- Header & Navigation -->
<div class="mb-5 flex flex-wrap items-center justify-between gap-3">
    <div class="flex items-center gap-2">
        <a href="<?= APP_URL ?>/group" 
           class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white hover:bg-slate-50 text-slate-700 font-semibold rounded-xl border border-slate-200 transition-all text-xs shadow-2xs hover:shadow-xs">
            <ion-icon name="arrow-back-outline" class="text-sm"></ion-icon>
            <span>Kembali ke Daftar Group</span>
        </a>

        <?php if (!empty($group['kode_group'])): ?>
            <span class="inline-flex items-center px-2 py-0.5 rounded-md font-mono text-[11px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-200">
                <?= htmlspecialchars($group['kode_group']) ?>
            </span>
        <?php endif; ?>
    </div>

    <div class="flex items-center gap-2">
        <a href="<?= APP_URL ?>/paket/create?group_id=<?= $group['id'] ?>" 
           class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-semibold shadow-xs transition-all hover:scale-[1.01] active:scale-[0.98]">
            <ion-icon name="add-circle-outline" class="text-base"></ion-icon>
            <span>Tambah Paket ke Group Ini</span>
        </a>
    </div>
</div>

<!-- Group Info Card -->
<div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 p-5 mb-5">
    <div class="flex items-start gap-4">
        <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-indigo-500 to-blue-600 text-white flex items-center justify-center text-2xl shadow-sm shrink-0">
            <ion-icon name="folder-open"></ion-icon>
        </div>
        <div class="flex-1 min-w-0">
            <h1 class="text-lg md:text-xl font-extrabold text-slate-900 tracking-tight leading-tight mb-1">
                <?= htmlspecialchars($group['nama_group']) ?>
            </h1>
            <p class="text-xs text-slate-400 mb-2 flex items-center gap-2 flex-wrap">
                <span>Perusahaan: <strong class="text-slate-600 font-semibold"><?= htmlspecialchars($group['perusahaan_nama'] ?: 'Umum / Semua') ?></strong></span>
                <span class="text-slate-300">&bull;</span>
                <span>Dibuat pada <?= date('d M Y', strtotime($group['created_at'])) ?></span>
            </p>
            <?php if (!empty($group['deskripsi'])): ?>
                <p class="text-xs text-slate-600 bg-slate-50 p-3 rounded-xl border border-slate-100 leading-relaxed">
                    <?= nl2br(htmlspecialchars($group['deskripsi'])) ?>
                </p>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- 4 Roll-up Financial Cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <!-- Card 1: Total Pagu Gabungan -->
    <div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 p-4">
        <div class="flex items-center justify-between mb-2">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Total Nilai Acuan Pagu</span>
            <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center text-sm">
                <ion-icon name="cash-outline"></ion-icon>
            </div>
        </div>
        <div class="text-lg font-extrabold text-slate-900 font-mono">
            <?= formatRupiah($totalAcuan) ?>
        </div>
        <div class="text-[10px] text-slate-400 mt-1">
            Dari <?= $stats['total_paket'] ?> paket kegiatan
        </div>
    </div>

    <!-- Card 2: Total DPP (-11%) -->
    <div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 p-4">
        <div class="flex items-center justify-between mb-2">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Total DPP (-11%)</span>
            <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center text-sm">
                <ion-icon name="calculator-outline"></ion-icon>
            </div>
        </div>
        <div class="text-lg font-extrabold text-slate-900 font-mono">
            <?= formatRupiah($stats['total_dpp']) ?>
        </div>
        <div class="text-[10px] text-slate-400 mt-1">
            Dasar Pengenaan Pajak
        </div>
    </div>

    <!-- Card 3: Total Potongan Pasal -->
    <div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 p-4">
        <div class="flex items-center justify-between mb-2">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Total Potongan Pasal</span>
            <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center text-sm">
                <ion-icon name="pie-chart-outline"></ion-icon>
            </div>
        </div>
        <div class="text-lg font-extrabold text-amber-700 font-mono">
            <?= formatRupiah($stats['total_pasal']) ?>
        </div>
        <div class="text-[10px] text-slate-400 mt-1">
            Akumulasi Pasal + Admin
        </div>
    </div>

    <!-- Card 4: Total Paket & Status -->
    <div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 p-4">
        <div class="flex items-center justify-between mb-2">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Jumlah Paket Terdaftar</span>
            <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center text-sm">
                <ion-icon name="layers-outline"></ion-icon>
            </div>
        </div>
        <div class="text-lg font-extrabold text-slate-900">
            <?= $stats['total_paket'] ?> <span class="text-xs font-semibold text-slate-400">Paket</span>
        </div>
        <div class="flex items-center gap-2 text-[10px] text-slate-500 mt-1">
            <span class="text-emerald-600 font-semibold"><?= $stats['paket_selesai'] ?> Selesai</span>
            <span>&bull;</span>
            <span class="text-blue-600 font-semibold"><?= $stats['paket_proses'] ?> Proses</span>
        </div>
    </div>
</div>

<!-- Member Packages Table -->
<div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 overflow-hidden">
    <div class="p-4 px-5 border-b border-slate-100 flex items-center justify-between">
        <div>
            <h3 class="font-bold text-slate-800 text-xs uppercase tracking-wider flex items-center gap-2">
                <ion-icon name="cube-outline" class="text-blue-600 text-base"></ion-icon>
                <span>Paket Kegiatan di Bawah Group Ini (<?= count($paketList) ?>)</span>
            </h3>
        </div>
        <a href="<?= APP_URL ?>/paket/create?group_id=<?= $group['id'] ?>" class="text-xs text-blue-600 hover:text-blue-700 font-semibold flex items-center gap-1">
            <ion-icon name="add-outline"></ion-icon>
            <span>Tambah Paket</span>
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left text-xs">
            <thead class="bg-slate-50/80 text-[11px] uppercase tracking-wider text-slate-500 font-bold border-b border-slate-200/80">
                <tr>
                    <th class="py-3 px-5">Nama Paket Kegiatan</th>
                    <th class="py-3 px-4 text-center">Penanggung Jawab</th>
                    <th class="py-3 px-4 text-right">Nilai Acuan</th>
                    <th class="py-3 px-4 text-right">DPP (-11%)</th>
                    <th class="py-3 px-4 text-right">Total Pasal</th>
                    <th class="py-3 px-4 text-center">Status</th>
                    <th class="py-3 px-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php if (empty($paketList)): ?>
                    <tr>
                        <td colspan="7" class="py-10 text-center text-slate-400">
                            <div class="w-12 h-12 bg-slate-50 rounded-xl flex items-center justify-center mx-auto mb-2 text-2xl text-slate-300">
                                <ion-icon name="cube-outline"></ion-icon>
                            </div>
                            <h4 class="text-xs font-bold text-slate-700 mb-0.5">Belum Ada Paket Kegiatan</h4>
                            <p class="text-[11px] text-slate-400 mb-3">Tambahkan paket kegiatan yang berada di bawah naungan group ini.</p>
                            <a href="<?= APP_URL ?>/paket/create?group_id=<?= $group['id'] ?>" 
                               class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-semibold shadow-xs transition-all">
                                <ion-icon name="add-circle-outline"></ion-icon>
                                <span>Tambah Paket Baru</span>
                            </a>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($paketList as $p): 
                        $hasNego = (!empty($p['pagu_nego']) && (float)$p['pagu_nego'] > 0);
                        $acuan = $hasNego ? (float)$p['pagu_nego'] : (float)$p['pagu'];
                    ?>
                        <tr class="hover:bg-slate-50/60 transition-colors group">
                            <td class="py-3 px-5">
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    <span class="px-1.5 py-0.2 rounded bg-slate-100 font-mono font-bold text-[10px] text-slate-600">
                                        <?= htmlspecialchars($p['kode_paket'] ?: '#' . $p['id']) ?>
                                    </span>
                                    <a href="<?= APP_URL ?>/paket/detail/<?= $p['kode_paket'] ?: $p['id'] ?>" class="font-bold text-slate-800 hover:text-blue-600 transition-colors">
                                        <?= htmlspecialchars($p['nama_paket']) ?>
                                    </a>
                                </div>
                                <div class="text-[10px] text-slate-400 mt-0.5">
                                    Perusahaan: <?= htmlspecialchars($p['perusahaan_nama'] ?: '-') ?>
                                </div>
                            </td>
                            <td class="py-3 px-4 text-center">
                                <?= getPenanggungJawabBadge($p['penanggung_jawab'] ?? '') ?>
                            </td>
                            <td class="py-3 px-4 text-right font-mono font-bold text-slate-800">
                                <?= formatRupiah($acuan) ?>
                                <?php if ($hasNego): ?>
                                    <span class="block text-[10px] text-emerald-600 font-semibold">Nego</span>
                                <?php endif; ?>
                            </td>
                            <td class="py-3 px-4 text-right font-mono text-slate-600">
                                <?= !empty($p['dpp']) ? formatRupiah($p['dpp']) : '-' ?>
                            </td>
                            <td class="py-3 px-4 text-right font-mono font-bold text-amber-700">
                                <?= !empty($p['total_pasal']) ? formatRupiah($p['total_pasal']) : '-' ?>
                            </td>
                            <td class="py-3 px-4 text-center">
                                <?= getStatusBadge($p['status']) ?>
                            </td>
                            <td class="py-3 px-4 text-right">
                                <a href="<?= APP_URL ?>/paket/detail/<?= $p['kode_paket'] ?: $p['id'] ?>" class="p-1.5 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors text-base inline-block" title="Lihat Detail Paket">
                                    <ion-icon name="arrow-forward-circle-outline"></ion-icon>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
