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
$danaBersih = $acuan - $totalPasal;
?>

<!-- Header Action Teleport Source -->
<div id="modalActionSource" class="hidden">
    <button type="button" 
            onclick='openIndexSkemaPotonganModal(<?= json_encode($paket, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)' 
            class="px-3 py-1.5 bg-amber-500 hover:bg-amber-600 text-white rounded-xl text-xs font-semibold shadow-xs flex items-center gap-1.5 transition-all cursor-pointer">
        <ion-icon name="options-outline" class="text-sm"></ion-icon>
        <span>Ubah Skema Potongan</span>
    </button>
</div>

<div class="space-y-4">
    <!-- 1. Langsung Tabel Rincian Finansial & Potongan (Full Width, No Horizontal Scroll) -->
    <div class="bg-white rounded-xl border border-slate-200/90 overflow-hidden shadow-2xs">
        <table class="w-full text-left text-xs table-fixed">
            <thead class="bg-slate-50 text-[11px] font-bold text-slate-500 uppercase tracking-wider border-b border-slate-200/80">
                <tr>
                    <th class="py-2.5 px-3 w-10 text-center">NO</th>
                    <th class="py-2.5 px-3 w-48">POS KEUANGAN</th>
                    <th class="py-2.5 px-3 w-40">BASIS / TARIF</th>
                    <th class="py-2.5 px-3 text-right w-44">NILAI NOMINAL</th>
                    <th class="py-2.5 px-3">CATATAN / FORMULA</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <tr class="hover:bg-slate-50/70 transition-colors">
                    <td class="py-2.5 px-3 text-center text-slate-400 font-mono text-[11px]">1</td>
                    <td class="py-2.5 px-3 font-semibold text-slate-800">Nilai Kontrak (Pagu Awal)</td>
                    <td class="py-2.5 px-3 text-slate-500">Pagu Asli</td>
                    <td class="py-2.5 px-3 text-right font-mono font-bold <?= $hasNego ? 'text-slate-400 line-through' : 'text-slate-800' ?>">
                        Rp <?= number_format((float)$paket['pagu'], 0, ',', '.') ?>
                    </td>
                    <td class="py-2.5 px-3 text-slate-400 text-[11px]">Nilai pagu proyek yang tertera pada kontrak kerja</td>
                </tr>

                <tr class="hover:bg-slate-50/70 transition-colors bg-blue-50/30">
                    <td class="py-2.5 px-3 text-center text-blue-500 font-mono text-[11px]">2</td>
                    <td class="py-2.5 px-3 font-bold text-blue-800">Pagu Nego (Acuan Pokok)</td>
                    <td class="py-2.5 px-3 text-blue-600 font-medium"><?= $hasNego ? 'Hasil Negosiasi' : 'Sama dg Kontrak' ?></td>
                    <td class="py-2.5 px-3 text-right font-mono font-extrabold text-blue-700">
                        Rp <?= number_format($acuan, 0, ',', '.') ?>
                    </td>
                    <td class="py-2.5 px-3 text-blue-500 text-[11px]">Angka acuan utama perhitungan DPP dan potongan pajak</td>
                </tr>

                <tr class="hover:bg-slate-50/70 transition-colors">
                    <td class="py-2.5 px-3 text-center text-slate-400 font-mono text-[11px]">3</td>
                    <td class="py-2.5 px-3 font-semibold text-slate-800">Dasar Pengenaan Pajak (DPP)</td>
                    <td class="py-2.5 px-3 text-slate-500">Acuan / 1.11</td>
                    <td class="py-2.5 px-3 text-right font-mono font-bold text-slate-700">
                        Rp <?= number_format($dpp, 0, ',', '.') ?>
                    </td>
                    <td class="py-2.5 px-3 text-slate-400 text-[11px]">Formula resmi DPP (Pagu Acuan dibagi 1.11)</td>
                </tr>

                <tr class="hover:bg-slate-50/70 transition-colors">
                    <td class="py-2.5 px-3 text-center text-slate-400 font-mono text-[11px]">4</td>
                    <td class="py-2.5 px-3 font-semibold text-slate-800">Potongan Pasal</td>
                    <td class="py-2.5 px-3 text-slate-600"><?= $persenPasal ?>% dari <?= $basisLabel ?></td>
                    <td class="py-2.5 px-3 text-right font-mono font-semibold text-rose-600">
                        - Rp <?= number_format($pasalVal, 0, ',', '.') ?>
                    </td>
                    <td class="py-2.5 px-3 text-slate-400 text-[11px]">PPh & kewajiban pasal (Basis: <?= $basisLabel ?>)</td>
                </tr>

                <tr class="hover:bg-slate-50/70 transition-colors">
                    <td class="py-2.5 px-3 text-center text-slate-400 font-mono text-[11px]">5</td>
                    <td class="py-2.5 px-3 font-semibold text-slate-800">Biaya Administrasi</td>
                    <td class="py-2.5 px-3 text-slate-600"><?= $persenAdmin ?>% dari <?= $basisLabel ?></td>
                    <td class="py-2.5 px-3 text-right font-mono font-semibold text-rose-600">
                        - Rp <?= number_format($adminVal, 0, ',', '.') ?>
                    </td>
                    <td class="py-2.5 px-3 text-slate-400 text-[11px]">Biaya administrasi operasional proyek</td>
                </tr>
            </tbody>
            <!-- 2. Ringkasan Info Menyatu di Kolom Bawah Tabel (tfoot) -->
            <tfoot class="bg-emerald-50/80 font-bold text-emerald-900 border-t-2 border-emerald-200">
                <tr>
                    <td class="py-3 px-3 text-center text-emerald-600 font-mono text-[11px]">✓</td>
                    <td class="py-3 px-3 text-xs uppercase tracking-wider text-emerald-800">
                        Sisa Dana Bersih Paket
                    </td>
                    <td class="py-3 px-3 text-xs text-emerald-700 font-medium">
                        Pagu Acuan - Total Potongan (<?= $totalPersen ?>%)
                    </td>
                    <td class="py-3 px-3 text-right text-sm font-mono font-extrabold text-emerald-700 whitespace-nowrap">
                        Rp <?= number_format($danaBersih, 0, ',', '.') ?>
                    </td>
                    <td class="py-3 px-3 text-[11px] text-emerald-600 font-normal">
                        Dana bersih yang siap dialokasikan untuk belanja modal / HPP barang
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>

    <!-- 5. Informasi Tambahan di Bawah Tabel (Metadata Paket) -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <div class="p-3 bg-white rounded-xl border border-slate-200/90 shadow-2xs">
            <div class="text-[10px] text-slate-400 uppercase font-semibold">Tahun Anggaran</div>
            <div class="text-xs font-bold text-slate-800 mt-0.5">TA <?= htmlspecialchars($paket['tahun_anggaran'] ?? date('Y')) ?></div>
        </div>
        <div class="p-3 bg-white rounded-xl border border-slate-200/90 shadow-2xs">
            <div class="text-[10px] text-slate-400 uppercase font-semibold">Status Pencairan</div>
            <div class="text-xs font-bold text-slate-800 mt-0.5"><?= htmlspecialchars($paket['status'] ?? 'Proses') ?></div>
        </div>
        <div class="p-3 bg-white rounded-xl border border-slate-200/90 shadow-2xs">
            <div class="text-[10px] text-slate-400 uppercase font-semibold">Basis Perhitungan Potongan</div>
            <div class="text-xs font-bold text-slate-800 mt-0.5"><?= $basisLabel ?></div>
        </div>
        <div class="p-3 bg-white rounded-xl border border-slate-200/90 shadow-2xs">
            <div class="text-[10px] text-slate-400 uppercase font-semibold">Total Potongan (Pasal + Admin)</div>
            <div class="text-xs font-bold text-rose-600 mt-0.5">Rp <?= number_format($totalPasal, 0, ',', '.') ?> (<?= $totalPersen ?>%)</div>
        </div>
    </div>
</div>
