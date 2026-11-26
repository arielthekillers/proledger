<?php
$totKewajiban = (float)($summary['total_pasal'] ?? 0);
$totSetor     = (float)($summary['total_setor_pasal'] ?? 0);
$totSisa      = max(0, $totKewajiban - $totSetor);
$totPct       = $totKewajiban > 0 ? min(100, round(($totSetor / $totKewajiban) * 100, 1)) : 0;

$totPasalUtama = (float)($summary['total_pasal_utama'] ?? 0);
$totSetorUtama = (float)($summary['total_setor_utama'] ?? 0);
$totSisaUtama  = max(0, $totPasalUtama - $totSetorUtama);

$totAdminFee   = (float)($summary['total_admin_fee'] ?? 0);
$totSetorAdmin = (float)($summary['total_setor_admin'] ?? 0);
$totSisaAdmin  = max(0, $totAdminFee - $totSetorAdmin);

$pasalByRate = $summary['pasal_by_rate'] ?? [];
$adminByRate = $summary['admin_by_rate'] ?? [];
uksort($pasalByRate, fn($a, $b) => (float)$b <=> (float)$a);
uksort($adminByRate, fn($a, $b) => (float)$b <=> (float)$a);
?>

<style>
@media print {
    header, .no-print { display: none !important; }
    body { background: #fff !important; }
    .print-card { box-shadow: none !important; border-color: #cbd5e1 !important; break-inside: avoid; }
}
</style>

<div class="max-w-7xl mx-auto space-y-5">
    <!-- Top Bar -->
    <div class="flex flex-wrap items-center justify-between gap-3 no-print">
        <div class="flex items-center gap-2.5">
            <a href="<?= APP_URL ?>/kolaborasi/detail/<?= (int)$kolab['id'] ?>"
               class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white hover:bg-slate-50 text-slate-700 font-semibold rounded-xl border border-slate-200 text-xs shadow-2xs">
                <ion-icon name="arrow-back-outline" class="text-sm"></ion-icon>
                <span>Kembali ke Kolaborasi</span>
            </a>
            <div>
                <h1 class="text-lg font-bold text-slate-800">Laporan Keuangan Koordinator</h1>
                <p class="text-[11px] text-slate-500"><?= htmlspecialchars($kolab['nama']) ?> &bull; <?= htmlspecialchars($kolab['instansi'] ?: '-') ?> &bull; TA <?= htmlspecialchars($kolab['tahun_anggaran'] ?? date('Y')) ?></p>
            </div>
        </div>
        <button type="button" onclick="window.print()"
                class="inline-flex items-center gap-1.5 px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white rounded-xl text-xs font-semibold shadow-xs cursor-pointer">
            <ion-icon name="print-outline" class="text-sm"></ion-icon>
            <span>Cetak / Simpan PDF</span>
        </button>
    </div>

    <!-- Print Header -->
    <div class="hidden print:block border-b-2 border-slate-800 pb-3 mb-4">
        <h1 class="text-lg font-bold text-slate-900">LAPORAN KEUANGAN KOORDINATOR KOLABORASI</h1>
        <p class="text-xs text-slate-600"><?= htmlspecialchars($kolab['nama']) ?> &bull; <?= htmlspecialchars($kolab['instansi'] ?: '-') ?> &bull; TA <?= htmlspecialchars($kolab['tahun_anggaran'] ?? date('Y')) ?> &bull; Dicetak: <?= date('d/m/Y H:i') ?></p>
    </div>

    <!-- 4 KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5">
        <div class="print-card bg-white rounded-2xl border border-slate-200/80 p-4 shadow-2xs">
            <div class="text-[11px] font-semibold text-slate-500 uppercase">Total Kontrak &amp; DPP</div>
            <div class="text-lg font-bold font-mono text-slate-800 mt-1"><?= formatRupiah($summary['total_kontrak'] ?? 0) ?></div>
            <div class="text-[11px] text-slate-500 mt-1">DPP: <span class="font-mono font-semibold"><?= formatRupiah($summary['total_dpp'] ?? 0) ?></span> (<?= (int)$summary['total_paket'] ?> Paket)</div>
        </div>

        <div class="print-card bg-amber-50/50 rounded-2xl border border-amber-200/80 p-4 shadow-2xs">
            <div class="text-[11px] font-bold text-amber-800 uppercase">Total Potongan Pasal (Utama)</div>
            <div class="text-lg font-bold font-mono text-amber-900 mt-1"><?= formatRupiah($totPasalUtama) ?></div>
            <div class="text-[11px] text-amber-700 mt-1 flex items-center justify-between">
                <span>Tersetor: <strong class="font-mono"><?= formatRupiah($totSetorUtama) ?></strong></span>
                <span>Sisa: <strong class="font-mono"><?= formatRupiah($totSisaUtama) ?></strong></span>
            </div>
        </div>

        <div class="print-card bg-indigo-50/50 rounded-2xl border border-indigo-200/80 p-4 shadow-2xs">
            <div class="text-[11px] font-bold text-indigo-800 uppercase">Total Potongan Admin</div>
            <div class="text-lg font-bold font-mono text-indigo-900 mt-1"><?= formatRupiah($totAdminFee) ?></div>
            <div class="text-[11px] text-indigo-700 mt-1 flex items-center justify-between">
                <span>Tersetor: <strong class="font-mono"><?= formatRupiah($totSetorAdmin) ?></strong></span>
                <span>Sisa: <strong class="font-mono"><?= formatRupiah($totSisaAdmin) ?></strong></span>
            </div>
        </div>

        <div class="print-card bg-emerald-50/50 rounded-2xl border border-emerald-200/80 p-4 shadow-2xs">
            <div class="text-[11px] font-bold text-emerald-800 uppercase">Setoran Masuk ke Koordinator</div>
            <div class="text-lg font-bold font-mono text-emerald-800 mt-1"><?= formatRupiah($totSetor) ?> <span class="text-xs font-sans">(<?= $totPct ?>%)</span></div>
            <div class="text-[11px] text-emerald-700 mt-1">
                Sisa Tagihan: <strong class="font-mono text-rose-600"><?= formatRupiah($totSisa) ?></strong>
            </div>
        </div>
    </div>

    <!-- Pemisahan Berdasarkan Tarif Persentase -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <!-- Breakdown Pasal -->
        <div class="print-card bg-white rounded-2xl border border-slate-200/80 overflow-hidden shadow-2xs">
            <div class="px-4 py-3 bg-amber-50/60 border-b border-amber-100 flex items-center justify-between">
                <div>
                    <h2 class="text-xs font-bold text-amber-900 uppercase tracking-wider">Rincian Bagian Pasal (Per Tarif)</h2>
                    <p class="text-[11px] text-amber-700">Dipisahkan per persentase agar jelas jumlah yang disetor ke tujuan pasal</p>
                </div>
            </div>
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-[10px] uppercase text-slate-500 font-bold border-b border-slate-200">
                    <tr>
                        <th class="py-2.5 px-4">Tarif Pasal</th>
                        <th class="py-2.5 px-3 text-center">Paket</th>
                        <th class="py-2.5 px-3 text-right">Total Tagihan</th>
                        <th class="py-2.5 px-3 text-right">Tersetor (Prop.)</th>
                        <th class="py-2.5 px-4 text-right">Sisa</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php foreach ($pasalByRate as $pr):
                        $sisaPr = max(0, $pr['tagihan'] - $pr['tersetor']);
                    ?>
                        <tr>
                            <td class="py-2.5 px-4 font-bold text-amber-800">Pasal <?= (float)$pr['rate'] ?>%</td>
                            <td class="py-2.5 px-3 text-center text-slate-600"><?= (int)$pr['paket_count'] ?></td>
                            <td class="py-2.5 px-3 text-right font-mono font-bold text-slate-800"><?= formatRupiah($pr['tagihan']) ?></td>
                            <td class="py-2.5 px-3 text-right font-mono text-emerald-700 font-semibold"><?= formatRupiah($pr['tersetor']) ?></td>
                            <td class="py-2.5 px-4 text-right font-mono text-rose-600 font-semibold"><?= formatRupiah($sisaPr) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot class="bg-slate-50 font-bold border-t border-slate-200 text-xs">
                    <tr>
                        <td colspan="2" class="py-2.5 px-4 text-slate-700">TOTAL BAGIAN PASAL</td>
                        <td class="py-2.5 px-3 text-right font-mono text-amber-900"><?= formatRupiah($totPasalUtama) ?></td>
                        <td class="py-2.5 px-3 text-right font-mono text-emerald-700"><?= formatRupiah($totSetorUtama) ?></td>
                        <td class="py-2.5 px-4 text-right font-mono text-rose-600"><?= formatRupiah($totSisaUtama) ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Breakdown Admin -->
        <div class="print-card bg-white rounded-2xl border border-slate-200/80 overflow-hidden shadow-2xs">
            <div class="px-4 py-3 bg-indigo-50/60 border-b border-indigo-100 flex items-center justify-between">
                <div>
                    <h2 class="text-xs font-bold text-indigo-900 uppercase tracking-wider">Rincian Bagian Admin (Per Tarif)</h2>
                    <p class="text-[11px] text-indigo-700">Dipisahkan per persentase untuk keperluan tujuan biaya admin</p>
                </div>
            </div>
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-[10px] uppercase text-slate-500 font-bold border-b border-slate-200">
                    <tr>
                        <th class="py-2.5 px-4">Tarif Admin</th>
                        <th class="py-2.5 px-3 text-center">Paket</th>
                        <th class="py-2.5 px-3 text-right">Total Tagihan</th>
                        <th class="py-2.5 px-3 text-right">Tersetor (Prop.)</th>
                        <th class="py-2.5 px-4 text-right">Sisa</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php foreach ($adminByRate as $ar):
                        $sisaAr = max(0, $ar['tagihan'] - $ar['tersetor']);
                    ?>
                        <tr>
                            <td class="py-2.5 px-4 font-bold text-indigo-800">Admin <?= (float)$ar['rate'] ?>%</td>
                            <td class="py-2.5 px-3 text-center text-slate-600"><?= (int)$ar['paket_count'] ?></td>
                            <td class="py-2.5 px-3 text-right font-mono font-bold text-slate-800"><?= formatRupiah($ar['tagihan']) ?></td>
                            <td class="py-2.5 px-3 text-right font-mono text-emerald-700 font-semibold"><?= formatRupiah($ar['tersetor']) ?></td>
                            <td class="py-2.5 px-4 text-right font-mono text-rose-600 font-semibold"><?= formatRupiah($sisaAr) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot class="bg-slate-50 font-bold border-t border-slate-200 text-xs">
                    <tr>
                        <td colspan="2" class="py-2.5 px-4 text-slate-700">TOTAL BAGIAN ADMIN</td>
                        <td class="py-2.5 px-3 text-right font-mono text-indigo-900"><?= formatRupiah($totAdminFee) ?></td>
                        <td class="py-2.5 px-3 text-right font-mono text-emerald-700"><?= formatRupiah($totSetorAdmin) ?></td>
                        <td class="py-2.5 px-4 text-right font-mono text-rose-600"><?= formatRupiah($totSisaAdmin) ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- Rekap Kewajiban & Setoran Per Perusahaan -->
    <div class="print-card bg-white rounded-2xl border border-slate-200/80 overflow-hidden shadow-2xs">
        <div class="px-4 py-3 border-b border-slate-100">
            <h2 class="text-sm font-bold text-slate-800">Rekap Kewajiban &amp; Setoran Per Perusahaan</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-[10px] uppercase text-slate-500 font-bold border-b border-slate-200">
                    <tr>
                        <th class="py-2.5 px-4">Perusahaan</th>
                        <th class="py-2.5 px-3 text-center">Paket</th>
                        <th class="py-2.5 px-3 text-right">Nilai Kontrak</th>
                        <th class="py-2.5 px-3 text-right">Bagian Pasal</th>
                        <th class="py-2.5 px-3 text-right">Bagian Admin</th>
                        <th class="py-2.5 px-3 text-right">Total Kewajiban</th>
                        <th class="py-2.5 px-3 text-right">Sudah Disetor</th>
                        <th class="py-2.5 px-4 text-right">Sisa Tagihan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php foreach ($companyStats as $cs):
                        $csKewajiban = (float)($cs['total_kewajiban'] ?? 0);
                        $csSetor     = (float)($cs['total_setor'] ?? 0);
                        $csSisa      = max(0, $csKewajiban - $csSetor);
                    ?>
                        <tr class="hover:bg-slate-50/70">
                            <td class="py-2.5 px-4 font-bold text-slate-800"><?= htmlspecialchars($cs['nama']) ?></td>
                            <td class="py-2.5 px-3 text-center text-slate-600"><?= (int)$cs['total_paket'] ?></td>
                            <td class="py-2.5 px-3 text-right font-mono text-slate-700"><?= formatRupiah($cs['total_kontrak']) ?></td>
                            <td class="py-2.5 px-3 text-right font-mono text-amber-800"><?= formatRupiah($cs['total_pasal'] ?? 0) ?></td>
                            <td class="py-2.5 px-3 text-right font-mono text-indigo-800"><?= formatRupiah($cs['total_admin'] ?? 0) ?></td>
                            <td class="py-2.5 px-3 text-right font-mono font-bold text-slate-900"><?= formatRupiah($csKewajiban) ?></td>
                            <td class="py-2.5 px-3 text-right font-mono font-semibold text-emerald-700"><?= formatRupiah($csSetor) ?></td>
                            <td class="py-2.5 px-4 text-right font-mono font-bold <?= $csSisa > 0 ? 'text-rose-600' : 'text-emerald-600' ?>">
                                <?= $csSisa > 0 ? formatRupiah($csSisa) : 'Lunas' ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Rincian Per Paket -->
    <div class="print-card bg-white rounded-2xl border border-slate-200/80 overflow-hidden shadow-2xs">
        <div class="px-4 py-3 border-b border-slate-100">
            <h2 class="text-sm font-bold text-slate-800">Rincian Potongan &amp; Setoran Per Paket (<?= count($pakets) ?> Paket)</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-[10px] uppercase text-slate-500 font-bold border-b border-slate-200">
                    <tr>
                        <th class="py-2.5 px-3 text-center">Kode</th>
                        <th class="py-2.5 px-3">Nama Paket &amp; Perusahaan</th>
                        <th class="py-2.5 px-3 text-center">Tarif</th>
                        <th class="py-2.5 px-3 text-right">Bagian Pasal</th>
                        <th class="py-2.5 px-3 text-right">Bagian Admin</th>
                        <th class="py-2.5 px-3 text-right">Total Kewajiban</th>
                        <th class="py-2.5 px-3 text-right">Tersetor</th>
                        <th class="py-2.5 px-3 text-right">Sisa</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php foreach ($pakets as $pk):
                        $pkKew = (float)$pk['total_pasal'];
                        $pkSetor = (float)$pk['total_setor_pasal'];
                        $pkSisa = max(0, $pkKew - $pkSetor);
                    ?>
                        <tr class="hover:bg-slate-50/70">
                            <td class="py-2 px-3 text-center font-mono font-bold text-slate-700"><?= htmlspecialchars($pk['kode_paket'] ?: ('#' . $pk['id'])) ?></td>
                            <td class="py-2 px-3">
                                <div class="font-semibold text-slate-800"><?= htmlspecialchars($pk['nama_paket']) ?></div>
                                <div class="text-[10px] text-slate-400"><?= htmlspecialchars($pk['perusahaan_nama']) ?></div>
                            </td>
                            <td class="py-2 px-3 text-center whitespace-nowrap">
                                <span class="px-1.5 py-0.2 rounded bg-amber-50 text-amber-800 text-[10px] font-bold"><?= (float)$pk['persen_pasal'] ?>%</span>
                                +
                                <span class="px-1.5 py-0.2 rounded bg-indigo-50 text-indigo-800 text-[10px] font-bold"><?= (float)$pk['persen_admin'] ?>%</span>
                            </td>
                            <td class="py-2 px-3 text-right font-mono text-amber-800"><?= formatRupiah($pk['pasal_val']) ?></td>
                            <td class="py-2 px-3 text-right font-mono text-indigo-800"><?= formatRupiah($pk['admin_val']) ?></td>
                            <td class="py-2 px-3 text-right font-mono font-bold text-slate-900"><?= formatRupiah($pkKew) ?></td>
                            <td class="py-2 px-3 text-right font-mono text-emerald-700 font-semibold"><?= formatRupiah($pkSetor) ?></td>
                            <td class="py-2 px-3 text-right font-mono font-bold <?= $pkSisa > 0 ? 'text-rose-600' : 'text-emerald-600' ?>">
                                <?= $pkSisa > 0 ? formatRupiah($pkSisa) : 'Lunas' ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
