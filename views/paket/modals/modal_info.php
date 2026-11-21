<?php
/**
 * Modal View: Informasi & Finansial Paket
 */
$hasNego = (!empty($paket['pagu_nego']) && (float)$paket['pagu_nego'] > 0);
$acuan = $hasNego ? (float)$paket['pagu_nego'] : (float)$paket['pagu'];
$dpp = !empty($paket['dpp']) ? (float)$paket['dpp'] : calculateDPP($acuan);
$tipePotongan = $paket['tipe_potongan'] ?? 'dpp';
$basisPotongan = ($tipePotongan === 'kontrak') ? $acuan : $dpp;
$basisLabel = ($tipePotongan === 'kontrak') ? 'Nilai Kontrak' : 'DPP';

$persenPasal = isset($paket['persen_pasal']) && $paket['persen_pasal'] !== '' ? (float)$paket['persen_pasal'] : 13.0;
$persenAdmin = isset($paket['persen_admin']) && $paket['persen_admin'] !== '' ? (float)$paket['persen_admin'] : 2.0;
$totalPersen = $persenPasal + $persenAdmin;

$pasalVal = !empty($paket['pasal_13']) ? (float)$paket['pasal_13'] : calculatePasal($basisPotongan, $persenPasal);
$adminVal = isset($paket['admin_2']) ? (float)$paket['admin_2'] : calculateAdmin($basisPotongan, $persenAdmin);
$totalPasal = !empty($paket['total_pasal']) ? (float)$paket['total_pasal'] : ($pasalVal + $adminVal);
$danaBersih = $acuan - $totalPasal;
?>

<div class="space-y-5">
    <!-- Grid Kartu Finansial -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <!-- Pagu Awal -->
        <div class="bg-white p-3.5 rounded-xl border border-slate-200/90 shadow-2xs">
            <div class="text-[10px] uppercase font-bold text-slate-400 mb-1">Nilai Kontrak / Awal</div>
            <div class="text-sm font-extrabold text-slate-500 <?= $hasNego ? 'line-through' : '' ?>">
                Rp <?= number_format((float)$paket['pagu'], 0, ',', '.') ?>
            </div>
        </div>

        <!-- Pagu Nego -->
        <div class="bg-blue-50/60 p-3.5 rounded-xl border border-blue-200/80 shadow-2xs">
            <div class="text-[10px] uppercase font-bold text-blue-600 mb-1">Pagu Nego (Acuan)</div>
            <div class="text-sm font-extrabold text-blue-700">
                <?= $hasNego ? 'Rp ' . number_format((float)$paket['pagu_nego'], 0, ',', '.') : '<span class="text-xs text-slate-400 font-normal">Sama dg Kontrak</span>' ?>
            </div>
        </div>

        <!-- DPP -->
        <div class="bg-white p-3.5 rounded-xl border border-slate-200/90 shadow-2xs">
            <div class="text-[10px] uppercase font-bold text-slate-400 mb-1">DPP (Acuan / 1.11)</div>
            <div class="text-sm font-extrabold text-slate-800">
                Rp <?= number_format($dpp, 0, ',', '.') ?>
            </div>
        </div>

        <!-- Dana Bersih -->
        <div class="bg-emerald-50/70 p-3.5 rounded-xl border border-emerald-200/80 shadow-2xs">
            <div class="text-[10px] uppercase font-bold text-emerald-700 mb-1">Sisa Dana Bersih</div>
            <div class="text-sm font-extrabold text-emerald-700">
                Rp <?= number_format($danaBersih, 0, ',', '.') ?>
            </div>
        </div>
    </div>

    <!-- Skema Potongan Breakdown Card -->
    <div class="bg-white rounded-xl border border-slate-200/90 p-4 shadow-2xs space-y-3">
        <div class="flex items-center justify-between border-b border-slate-100 pb-2.5">
            <div class="flex items-center gap-2">
                <ion-icon name="calculator-outline" class="text-amber-600 text-base"></ion-icon>
                <h4 class="text-xs font-bold text-slate-800">Rincian Potongan & Pajak</h4>
                <span class="text-[10px] font-semibold px-2 py-0.5 rounded-md bg-amber-50 text-amber-800 border border-amber-200">Basis: <?= $basisLabel ?></span>
            </div>
            <button type="button" onclick="triggerMenuSkemaPotongan()" class="text-xs font-semibold text-amber-600 hover:text-amber-700 inline-flex items-center gap-1">
                <ion-icon name="options-outline"></ion-icon>
                <span>Ubah Skema</span>
            </button>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-1">
            <div class="p-3 rounded-lg bg-slate-50 border border-slate-200/70">
                <div class="text-[10px] text-slate-400 font-semibold mb-0.5">Potongan Pasal (<?= $persenPasal ?>%)</div>
                <div class="text-xs font-bold text-slate-800">Rp <?= number_format($pasalVal, 0, ',', '.') ?></div>
            </div>
            <div class="p-3 rounded-lg bg-slate-50 border border-slate-200/70">
                <div class="text-[10px] text-slate-400 font-semibold mb-0.5">Biaya Admin (<?= $persenAdmin ?>%)</div>
                <div class="text-xs font-bold text-slate-800">Rp <?= number_format($adminVal, 0, ',', '.') ?></div>
            </div>
            <div class="p-3 rounded-lg bg-amber-50/60 border border-amber-200/70">
                <div class="text-[10px] text-amber-700 font-bold mb-0.5">Total Potongan (<?= $totalPersen ?>%)</div>
                <div class="text-xs font-extrabold text-amber-800">Rp <?= number_format($totalPasal, 0, ',', '.') ?></div>
            </div>
        </div>
    </div>

    <!-- Metadata & Informasi Administrasi -->
    <div class="bg-white rounded-xl border border-slate-200/90 p-4 shadow-2xs">
        <h4 class="text-xs font-bold text-slate-800 border-b border-slate-100 pb-2.5 mb-3 flex items-center gap-2">
            <ion-icon name="information-circle-outline" class="text-blue-600 text-base"></ion-icon>
            <span>Informasi Administrasi Paket</span>
        </h4>

        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 text-xs">
            <div>
                <span class="text-[10px] uppercase font-semibold text-slate-400 block mb-0.5">Perusahaan Pelaksana</span>
                <span class="font-bold text-slate-800"><?= htmlspecialchars($paket['perusahaan'] ?: ($paket['perusahaan_nama'] ?? '-')) ?></span>
            </div>
            <div>
                <span class="text-[10px] uppercase font-semibold text-slate-400 block mb-0.5">Group Paket Kegiatan</span>
                <span class="font-bold text-slate-800"><?= htmlspecialchars($paket['nama_group'] ?: 'Paket Mandiri') ?></span>
            </div>
            <div>
                <span class="text-[10px] uppercase font-semibold text-slate-400 block mb-0.5">Tahun Anggaran</span>
                <span class="font-bold text-amber-700">TA <?= htmlspecialchars((string)($paket['tahun_anggaran'] ?: date('Y'))) ?></span>
            </div>
            <div>
                <span class="text-[10px] uppercase font-semibold text-slate-400 block mb-0.5">Penanggung Jawab (PJ)</span>
                <span class="font-bold text-slate-800"><?= htmlspecialchars($paket['penanggung_jawab'] ?: '-') ?></span>
            </div>
            <div>
                <span class="text-[10px] uppercase font-semibold text-slate-400 block mb-0.5">Status Pencairan</span>
                <span class="inline-block mt-0.5"><?= getStatusBadge($paket['status'] ?? 'Proses') ?></span>
            </div>
            <div>
                <span class="text-[10px] uppercase font-semibold text-slate-400 block mb-0.5">Tags Pengelompokan</span>
                <div class="flex items-center gap-1 flex-wrap mt-0.5">
                    <?php if (!empty($paket['tags'])): ?>
                        <?php foreach (explode(',', $paket['tags']) as $tg): $tg = trim($tg); if (!$tg) continue; ?>
                            <span class="px-2 py-0.5 bg-slate-100 text-slate-600 rounded text-[10px] font-medium">#<?= htmlspecialchars($tg) ?></span>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <span class="text-slate-400">-</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
