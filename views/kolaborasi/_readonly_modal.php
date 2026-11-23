<?php
/**
 * Shared Read-Only Modal Fragment for Kolaborasi & Public Monitoring
 * Menampilkan detail Info & Finansial (Kontrak/DPP/Pasal/Bersih), Komponen Item,
 * Progres Pemesanan, Alokasi Distribusi, Foto Dokumentasi, dan Dokumen/BAST — TANPA edit/tambah/HPP/profit.
 */
$paket      = $modalData['paket'];
$items      = $modalData['items'];
$distribusi = $modalData['distribusi'];
$fotos      = $modalData['fotos'];
$dokumens   = $modalData['dokumens'];
$fin        = $modalData['finansial'];
?>

<?php if ($module === 'info'): ?>
    <div class="space-y-4">
        <div class="bg-slate-50 rounded-xl p-4 border border-slate-200/80 grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
            <div>
                <span class="text-[10px] font-bold text-slate-400 uppercase block">Perusahaan Pelaksana</span>
                <span class="font-bold text-slate-800"><?= htmlspecialchars($paket['perusahaan_nama'] ?: '-') ?></span>
            </div>
            <div>
                <span class="text-[10px] font-bold text-slate-400 uppercase block">Group / Program</span>
                <span class="font-semibold text-indigo-700"><?= htmlspecialchars($paket['nama_group'] ?: '-') ?></span>
            </div>
            <div>
                <span class="text-[10px] font-bold text-slate-400 uppercase block">Nomor Surat Pesanan</span>
                <span class="font-mono text-slate-700"><?= htmlspecialchars($paket['nomor_sp'] ?: '-') ?></span>
            </div>
            <div>
                <span class="text-[10px] font-bold text-slate-400 uppercase block">Kabupaten / Kota & Tahun</span>
                <span class="text-slate-700"><?= htmlspecialchars($paket['kab_kota'] ?: '-') ?> &bull; TA <?= (int)$paket['tahun_anggaran'] ?></span>
            </div>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            <div class="p-3 rounded-xl bg-white border border-slate-200">
                <span class="text-[10px] font-bold text-slate-400 uppercase block">Nilai Kontrak</span>
                <span class="font-mono font-extrabold text-slate-900 text-sm"><?= formatRupiah($fin['acuan']) ?></span>
            </div>
            <div class="p-3 rounded-xl bg-white border border-slate-200">
                <span class="text-[10px] font-bold text-slate-400 uppercase block">DPP (-11%)</span>
                <span class="font-mono font-bold text-slate-800 text-sm"><?= formatRupiah($fin['dpp']) ?></span>
            </div>
            <div class="p-3 rounded-xl bg-amber-50/60 border border-amber-200">
                <span class="text-[10px] font-bold text-amber-600 uppercase block">Total Pasal (<?= $fin['persen_pasal'] + $fin['persen_admin'] ?>%)</span>
                <span class="font-mono font-bold text-amber-700 text-sm"><?= formatRupiah($fin['total_pasal']) ?></span>
            </div>
            <div class="p-3 rounded-xl bg-emerald-50/60 border border-emerald-200">
                <span class="text-[10px] font-bold text-emerald-600 uppercase block">Nilai Bersih</span>
                <span class="font-mono font-extrabold text-emerald-700 text-sm"><?= formatRupiah($fin['nilai_bersih']) ?></span>
            </div>
        </div>
    </div>

<?php elseif ($module === 'items'): ?>
    <?php if (empty($items)): ?>
        <div class="py-10 text-center text-xs text-slate-400">Belum ada komponen item barang.</div>
    <?php else: ?>
        <div class="overflow-x-auto border border-slate-200 rounded-xl">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-[10px] uppercase text-slate-500 font-bold border-b border-slate-200">
                    <tr>
                        <th class="py-2.5 px-3">No</th>
                        <th class="py-2.5 px-3">Nama Item Barang</th>
                        <th class="py-2.5 px-3">Merk / Spesifikasi</th>
                        <th class="py-2.5 px-3 text-center">Jumlah</th>
                        <th class="py-2.5 px-3 text-right">Harga Satuan</th>
                        <th class="py-2.5 px-3 text-right">Total Kontrak</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php foreach ($items as $idx => $it): ?>
                        <tr>
                            <td class="py-2 px-3 text-slate-400 font-mono"><?= $idx + 1 ?></td>
                            <td class="py-2 px-3 font-semibold text-slate-800"><?= htmlspecialchars($it['nama_item']) ?></td>
                            <td class="py-2 px-3 text-slate-500"><?= htmlspecialchars(trim(($it['merk'] ?? '') . ' ' . ($it['spesifikasi'] ?? '')) ?: '-') ?></td>
                            <td class="py-2 px-3 text-center font-semibold"><?= rtrim(rtrim(number_format((float)$it['jumlah'], 2, ',', '.'), '0'), ',') ?> <?= htmlspecialchars($it['satuan'] ?? 'Unit') ?></td>
                            <td class="py-2 px-3 text-right font-mono text-slate-700"><?= formatRupiah($it['harga_satuan'] ?? 0) ?></td>
                            <td class="py-2 px-3 text-right font-mono font-bold text-slate-900"><?= formatRupiah($it['total_harga'] ?? 0) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

<?php elseif ($module === 'pemesanan'): ?>
    <?php if (empty($distribusi)): ?>
        <div class="py-10 text-center text-xs text-slate-400">Belum ada data alokasi pemesanan barang.</div>
    <?php else: ?>
        <div class="overflow-x-auto border border-slate-200 rounded-xl">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-[10px] uppercase text-slate-500 font-bold border-b border-slate-200">
                    <tr>
                        <th class="py-2.5 px-3">Tujuan / Instansi</th>
                        <th class="py-2.5 px-3">Item Barang</th>
                        <th class="py-2.5 px-3 text-center">Jumlah</th>
                        <th class="py-2.5 px-3 text-center">Status Pemesanan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php foreach ($distribusi as $d):
                        $isOrdered = ($d['status_barang'] === 'Sudah Dipesan');
                    ?>
                        <tr>
                            <td class="py-2 px-3 font-semibold text-slate-800"><?= htmlspecialchars($d['nama_tujuan']) ?></td>
                            <td class="py-2 px-3 text-slate-600"><?= htmlspecialchars($d['nama_item']) ?></td>
                            <td class="py-2 px-3 text-center font-semibold"><?= rtrim(rtrim(number_format($d['jumlah'], 2, ',', '.'), '0'), ',') ?> <?= htmlspecialchars($d['satuan']) ?></td>
                            <td class="py-2 px-3 text-center">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold <?= $isOrdered ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-amber-50 text-amber-700 border border-amber-200' ?>">
                                    <?= htmlspecialchars($d['status_barang']) ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

<?php elseif ($module === 'distribusi'): ?>
    <?php if (empty($distribusi)): ?>
        <div class="py-10 text-center text-xs text-slate-400">Belum ada alokasi titik distribusi.</div>
    <?php else: ?>
        <div class="overflow-x-auto border border-slate-200 rounded-xl">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-[10px] uppercase text-slate-500 font-bold border-b border-slate-200">
                    <tr>
                        <th class="py-2.5 px-3">Tujuan Distribusi</th>
                        <th class="py-2.5 px-3">Item Barang</th>
                        <th class="py-2.5 px-3 text-center">Qty</th>
                        <th class="py-2.5 px-3 text-center">Status Pengiriman</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php foreach ($distribusi as $d):
                        $arrived = in_array($d['status_distribusi'], ['Tiba di Tujuan', 'Tiba di Gudang'], true);
                    ?>
                        <tr>
                            <td class="py-2 px-3">
                                <div class="font-semibold text-slate-800"><?= htmlspecialchars($d['nama_tujuan']) ?></div>
                                <?php if (!empty($d['kabupaten']) || !empty($d['alamat_tujuan'])): ?>
                                    <div class="text-[10px] text-slate-400"><?= htmlspecialchars(trim($d['alamat_tujuan'] . ' ' . $d['kabupaten'])) ?></div>
                                <?php endif; ?>
                            </td>
                            <td class="py-2 px-3 text-slate-600"><?= htmlspecialchars($d['nama_item']) ?></td>
                            <td class="py-2 px-3 text-center font-semibold"><?= rtrim(rtrim(number_format($d['jumlah'], 2, ',', '.'), '0'), ',') ?> <?= htmlspecialchars($d['satuan']) ?></td>
                            <td class="py-2 px-3 text-center">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold <?= $arrived ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-indigo-50 text-indigo-700 border border-indigo-200' ?>">
                                    <?= htmlspecialchars($d['status_distribusi']) ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

<?php elseif ($module === 'dokumentasi'): ?>
    <?php if (empty($fotos)): ?>
        <div class="py-10 text-center text-xs text-slate-400">Belum ada foto dokumentasi distribusi yang diunggah.</div>
    <?php else: ?>
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
            <?php foreach ($fotos as $f):
                $fotoUrl = APP_URL . '/uploads/dokumentasi/' . rawurlencode($f['nama_file'] ?? $f['file_path'] ?? '');
            ?>
                <a href="<?= $fotoUrl ?>" target="_blank" class="group block rounded-xl overflow-hidden border border-slate-200 bg-slate-50">
                    <div class="h-32 bg-slate-100 overflow-hidden">
                        <img src="<?= $fotoUrl ?>" alt="Dokumentasi" class="w-full h-full object-cover group-hover:scale-105 transition-transform">
                    </div>
                    <div class="p-2 text-[10px] text-slate-600 truncate font-semibold">
                        <?= htmlspecialchars($f['nama_tujuan'] ?? $f['keterangan'] ?? 'Foto Dokumentasi') ?>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

<?php elseif ($module === 'dokumen'): ?>
    <?php if (empty($dokumens)): ?>
        <div class="py-10 text-center text-xs text-slate-400">Belum ada berkas dokumen / BAST yang diunggah.</div>
    <?php else: ?>
        <div class="space-y-2">
            <?php foreach ($dokumens as $doc):
                $docUrl = APP_URL . '/uploads/dokumen/' . rawurlencode($doc['nama_file'] ?? '');
            ?>
                <div class="flex items-center justify-between gap-3 p-3 rounded-xl border border-slate-200 bg-slate-50/70">
                    <div class="min-w-0">
                        <div class="font-bold text-slate-800 text-xs truncate"><?= htmlspecialchars($doc['nama_dokumen'] ?: $doc['nama_file_asli']) ?></div>
                        <div class="text-[10px] text-slate-400"><?= htmlspecialchars($doc['nama_file_asli'] ?? '') ?></div>
                    </div>
                    <?php if (!empty($doc['nama_file'])): ?>
                        <a href="<?= $docUrl ?>" target="_blank"
                           class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-[11px] font-semibold shrink-0">
                            Lihat Berkas
                        </a>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
<?php endif; ?>
