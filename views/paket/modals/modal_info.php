<?php
/**
 * Modal View: Informasi & Finansial Paket
 */
$hasNego = (!empty($paket['pagu_nego']) && (float)$paket['pagu_nego'] > 0);
$acuan = $hasNego ? (float)$paket['pagu_nego'] : (float)$paket['pagu'];
$dpp = !empty($paket['dpp']) ? (float)$paket['dpp'] : calculateDPP($acuan);
$tipePotongan = $paket['tipe_potongan'] ?? 'dpp';
$basisPotongan = ($tipePotongan === 'kontrak') ? $acuan : $dpp;
$basisLabel = ($tipePotongan === 'kontrak') ? 'Nilai Kontrak' : 'DPP (Pagu/1.11)';

$persenPasal = isset($paket['persen_pasal']) && $paket['persen_pasal'] !== '' ? (float)$paket['persen_pasal'] : 13.0;
$persenAdmin = isset($paket['persen_admin']) && $paket['persen_admin'] !== '' ? (float)$paket['persen_admin'] : 2.0;
$totalPersen = $persenPasal + $persenAdmin;

$pasalVal = !empty($paket['pasal_13']) ? (float)$paket['pasal_13'] : calculatePasal($basisPotongan, $persenPasal);
$adminVal = isset($paket['admin_2']) ? (float)$paket['admin_2'] : calculateAdmin($basisPotongan, $persenAdmin);
$totalPasal = !empty($paket['total_pasal']) ? (float)$paket['total_pasal'] : ($pasalVal + $adminVal);
$danaBersih = $dpp - $totalPasal;

$tagList = [];
if (!empty($paket['tags'])) {
    $tagList = array_filter(array_map('trim', explode(',', $paket['tags'])));
}
?>

<!-- Header Action Teleport Source -->
<div id="modalActionSource" class="hidden">
    <div class="flex items-center gap-2">
        <button type="button" 
                onclick='openEditPaketModal(<?= json_encode($paket, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)' 
                class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-semibold shadow-xs flex items-center gap-1.5 transition-all cursor-pointer">
            <ion-icon name="create-outline" class="text-sm"></ion-icon>
            <span>Edit Paket</span>
        </button>
        <button type="button" 
                onclick='openIndexSkemaPotonganModal(<?= json_encode($paket, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)' 
                class="px-3 py-1.5 bg-amber-500 hover:bg-amber-600 text-white rounded-xl text-xs font-semibold shadow-xs flex items-center gap-1.5 transition-all cursor-pointer">
            <ion-icon name="options-outline" class="text-sm"></ion-icon>
            <span>Ubah Skema Potongan</span>
        </button>
    </div>
</div>

<div class="space-y-4">
    <!-- 1. Ringkasan Informasi Paket (5 Card Terurut di Atas Tabel) -->
    <div class="grid grid-cols-2 sm:grid-cols-5 gap-3">
        <!-- 1. Tahun Anggaran -->
        <div class="p-3 bg-white rounded-xl border border-slate-200/90 shadow-2xs">
            <div class="text-[10px] text-slate-400 uppercase font-semibold">Tahun Anggaran</div>
            <div class="text-xs font-bold text-slate-800 mt-1 flex items-center gap-1.5">
                <ion-icon name="calendar-outline" class="text-amber-500 text-sm shrink-0"></ion-icon>
                <span>TA <?= htmlspecialchars($paket['tahun_anggaran'] ?? date('Y')) ?></span>
            </div>
        </div>

        <!-- 2. Group Paket -->
        <div class="p-3 bg-white rounded-xl border border-slate-200/90 shadow-2xs">
            <div class="text-[10px] text-slate-400 uppercase font-semibold">Group Paket</div>
            <div class="text-xs font-bold text-slate-800 mt-1 flex items-center gap-1.5 truncate">
                <ion-icon name="folder-open-outline" class="text-indigo-500 text-sm shrink-0"></ion-icon>
                <span class="truncate" title="<?= htmlspecialchars($paket['nama_group'] ?: 'Mandiri (Non-Group)') ?>">
                    <?= htmlspecialchars(!empty($paket['nama_group']) ? $paket['nama_group'] : 'Mandiri') ?>
                </span>
            </div>
        </div>

        <!-- 3. Kota / Wilayah -->
        <div class="p-3 bg-white rounded-xl border border-slate-200/90 shadow-2xs">
            <div class="text-[10px] text-slate-400 uppercase font-semibold">Kota / Wilayah</div>
            <div class="text-xs font-bold text-slate-800 mt-1 flex items-center gap-1.5 truncate">
                <ion-icon name="location-outline" class="text-blue-500 text-sm shrink-0"></ion-icon>
                <span class="truncate" title="<?= htmlspecialchars($paket['kab_kota'] ?: 'Belum ditentukan') ?>">
                    <?= htmlspecialchars(!empty($paket['kab_kota']) ? $paket['kab_kota'] : '-') ?>
                </span>
            </div>
        </div>

        <!-- 4. Tags -->
        <div class="p-3 bg-white rounded-xl border border-slate-200/90 shadow-2xs">
            <div class="text-[10px] text-slate-400 uppercase font-semibold">Tags</div>
            <div class="mt-1 flex items-center gap-1 flex-wrap">
                <?php if (!empty($tagList)): ?>
                    <?php foreach (array_slice($tagList, 0, 3) as $tg): ?>
                        <span class="px-1.5 py-0.2 rounded bg-slate-100 text-slate-700 text-[10px] font-semibold border border-slate-200/80">#<?= htmlspecialchars($tg) ?></span>
                    <?php endforeach; ?>
                <?php else: ?>
                    <span class="text-xs text-slate-400">-</span>
                <?php endif; ?>
            </div>
        </div>

        <!-- 5. Status Pencairan -->
        <div class="p-3 bg-white rounded-xl border border-slate-200/90 shadow-2xs col-span-2 sm:col-span-1">
            <div class="text-[10px] text-slate-400 uppercase font-semibold">Status Pencairan</div>
            <div class="text-xs font-bold text-slate-800 mt-1 flex items-center gap-1.5">
                <ion-icon name="wallet-outline" class="text-emerald-600 text-sm shrink-0"></ion-icon>
                <span><?= htmlspecialchars($paket['status'] ?? 'Proses') ?></span>
            </div>
        </div>
    </div>

    <!-- 2. Tabel Rincian Finansial & Potongan (Full Width, No Wrap Pos Keuangan) -->
    <div class="bg-white rounded-xl border border-slate-200/90 overflow-hidden shadow-2xs">
        <table class="w-full text-left text-xs table-fixed">
            <thead class="bg-slate-50 text-[11px] font-bold text-slate-500 uppercase tracking-wider border-b border-slate-200/80">
                <tr>
                    <th class="py-2.5 px-3 w-10 text-center">NO</th>
                    <th class="py-2.5 px-3 w-60">POS KEUANGAN</th>
                    <th class="py-2.5 px-3 w-40">BASIS / TARIF</th>
                    <th class="py-2.5 px-3 text-right w-40">NILAI NOMINAL</th>
                    <th class="py-2.5 px-3">CATATAN / FORMULA</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <tr class="hover:bg-slate-50/70 transition-colors">
                    <td class="py-2.5 px-3 text-center text-slate-400 font-mono text-[11px]">1</td>
                    <td class="py-2.5 px-3 font-semibold text-slate-800 whitespace-nowrap">Nilai Kontrak (Pagu Awal)</td>
                    <td class="py-2.5 px-3 text-slate-500">Pagu Asli</td>
                    <td class="py-2.5 px-3 text-right font-mono font-bold <?= $hasNego ? 'text-slate-400 line-through' : 'text-slate-800' ?>">
                        Rp <?= number_format((float)$paket['pagu'], 0, ',', '.') ?>
                    </td>
                    <td class="py-2.5 px-3 text-slate-400 text-[11px]">
                        <div class="truncate cursor-default pro-dark-tooltip"
                             data-tooltip-title="Nilai Kontrak (Pagu Awal)"
                             data-tooltip-body="Nilai pagu proyek yang tertera pada kontrak kerja awal sebelum negosiasi.">
                            Nilai pagu proyek yang tertera pada kontrak kerja awal
                        </div>
                    </td>
                </tr>

                <tr class="hover:bg-slate-50/70 transition-colors bg-blue-50/30">
                    <td class="py-2.5 px-3 text-center text-blue-500 font-mono text-[11px]">2</td>
                    <td class="py-2.5 px-3 font-bold text-blue-800 whitespace-nowrap">Pagu Nego (Acuan Pokok)</td>
                    <td class="py-2.5 px-3 text-blue-600 font-medium"><?= $hasNego ? 'Hasil Negosiasi' : 'Sama dg Kontrak' ?></td>
                    <td class="py-2.5 px-3 text-right font-mono font-extrabold text-blue-700">
                        Rp <?= number_format($acuan, 0, ',', '.') ?>
                    </td>
                    <td class="py-2.5 px-3 text-blue-500 text-[11px]">
                        <div class="truncate cursor-default pro-dark-tooltip"
                             data-tooltip-title="Pagu Nego (Acuan Pokok)"
                             data-tooltip-body="Angka acuan utama perhitungan DPP, potongan pajak, dan nilai bersih paket.">
                            Angka acuan utama perhitungan DPP dan potongan pajak
                        </div>
                    </td>
                </tr>

                <tr class="hover:bg-slate-50/70 transition-colors">
                    <td class="py-2.5 px-3 text-center text-slate-400 font-mono text-[11px]">3</td>
                    <td class="py-2.5 px-3 font-semibold text-slate-800 whitespace-nowrap">Dasar Pengenaan Pajak (DPP)</td>
                    <td class="py-2.5 px-3 text-slate-500">Acuan / 1.11</td>
                    <td class="py-2.5 px-3 text-right font-mono font-bold text-slate-700">
                        Rp <?= number_format($dpp, 0, ',', '.') ?>
                    </td>
                    <td class="py-2.5 px-3 text-slate-400 text-[11px]">
                        <div class="truncate cursor-default pro-dark-tooltip"
                             data-tooltip-title="Formula Resmi DPP"
                             data-tooltip-body="Dasar Pengenaan Pajak dihitung dari Pagu Acuan dibagi 1.11 (-11% PPN).">
                            Formula resmi DPP (Pagu Acuan dibagi 1.11)
                        </div>
                    </td>
                </tr>

                <tr class="hover:bg-slate-50/70 transition-colors">
                    <td class="py-2.5 px-3 text-center text-slate-400 font-mono text-[11px]">4</td>
                    <td class="py-2.5 px-3 font-semibold text-slate-800 whitespace-nowrap">Potongan Pasal</td>
                    <td class="py-2.5 px-3 text-slate-600"><?= $persenPasal ?>% dari <?= $basisLabel ?></td>
                    <td class="py-2.5 px-3 text-right font-mono font-semibold text-rose-600">
                        - Rp <?= number_format($pasalVal, 0, ',', '.') ?>
                    </td>
                    <td class="py-2.5 px-3 text-slate-400 text-[11px]">
                        <div class="truncate cursor-default pro-dark-tooltip"
                             data-tooltip-title="Potongan Pasal (<?= $persenPasal ?>%)"
                             data-tooltip-body="Kewajiban pajak & pasal dihitung <?= $persenPasal ?>% dari basis <?= htmlspecialchars($basisLabel) ?>.">
                            PPh & kewajiban pasal (Basis: <?= htmlspecialchars($basisLabel) ?>)
                        </div>
                    </td>
                </tr>

                <tr class="hover:bg-slate-50/70 transition-colors">
                    <td class="py-2.5 px-3 text-center text-slate-400 font-mono text-[11px]">5</td>
                    <td class="py-2.5 px-3 font-semibold text-slate-800 whitespace-nowrap">Biaya Administrasi</td>
                    <td class="py-2.5 px-3 text-slate-600"><?= $persenAdmin ?>% dari <?= $basisLabel ?></td>
                    <td class="py-2.5 px-3 text-right font-mono font-semibold text-rose-600">
                        - Rp <?= number_format($adminVal, 0, ',', '.') ?>
                    </td>
                    <td class="py-2.5 px-3 text-slate-400 text-[11px]">
                        <div class="truncate cursor-default pro-dark-tooltip"
                             data-tooltip-title="Biaya Administrasi (<?= $persenAdmin ?>%)"
                             data-tooltip-body="Biaya administrasi operasional proyek sebesar <?= $persenAdmin ?>% dari basis <?= htmlspecialchars($basisLabel) ?>.">
                            Biaya administrasi operasional proyek
                        </div>
                    </td>
                </tr>
            </tbody>
            <!-- Ringkasan Info Menyatu di Kolom Bawah Tabel (tfoot) -->
            <tfoot class="bg-emerald-50/80 font-bold text-emerald-900 border-t-2 border-emerald-200">
                <tr>
                    <td class="py-3 px-3 text-center text-emerald-600 font-mono text-[11px]">✓</td>
                    <td class="py-3 px-3 text-xs uppercase tracking-wider text-emerald-800 whitespace-nowrap">
                        Sisa Dana Bersih Paket
                    </td>
                    <td class="py-3 px-3"></td>
                    <td class="py-3 px-3 text-right text-sm font-mono font-extrabold text-emerald-700 whitespace-nowrap">
                        Rp <?= number_format($danaBersih, 0, ',', '.') ?>
                    </td>
                    <td class="py-3 px-3 text-[11px] text-emerald-600 font-normal">
                        <div class="truncate cursor-default pro-dark-tooltip"
                             data-tooltip-title="Sisa Dana Bersih Paket"
                             data-tooltip-body="Dana bersih setelah dikurangi total potongan <?= $totalPersen ?>% yang siap dialokasikan untuk belanja modal / HPP barang.">
                            Dana bersih siap dialokasikan untuk belanja modal / HPP
                        </div>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
