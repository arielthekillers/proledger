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

<?php elseif ($module === 'dokumentasi'):
    $dokumentasiMap = $modalData['dokumentasiMap'] ?? [];
?>
    <div class="bg-white rounded-xl border border-slate-200/90 overflow-hidden shadow-2xs">
        <table class="w-full text-left text-xs">
            <thead class="bg-slate-50 text-[11px] font-bold text-slate-500 uppercase tracking-wider border-b border-slate-200/80">
                <tr>
                    <th class="py-2.5 px-3 w-10 text-center">NO</th>
                    <th class="py-2.5 px-3">KOMPONEN BARANG</th>
                    <th class="py-2.5 px-3">TUJUAN PENERIMA</th>
                    <th class="py-2.5 px-2 text-center w-24">QTY</th>
                    <th class="py-2.5 px-2 text-center w-32">STATUS</th>
                    <th class="py-2.5 px-3 w-40">FOTO DOKUMENTASI</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php if (empty($distribusi)): ?>
                    <tr>
                        <td colspan="6" class="py-8 text-center text-slate-400 text-xs">Belum ada alokasi barang.</td>
                    </tr>
                <?php else: ?>
                    <?php $no = 1; foreach ($distribusi as $dist):
                        $photos = $dokumentasiMap[$dist['id']] ?? [];
                        $photoCount = count($photos);
                        $isOrdered = (($dist['status_barang'] ?? '') === 'Sudah Dipesan');
                        $sDist = trim($dist['status_distribusi'] ?? '');
                        $badgeClass = 'bg-slate-100 text-slate-500 border-slate-200';
                        $statusLabel = 'Belum Dikirim';
                        if (!$isOrdered) {
                            $statusLabel = 'Belum Dipesan';
                        } elseif ($sDist === 'Tiba di Tujuan') {
                            $statusLabel = 'Tiba di Tujuan';
                            $badgeClass = 'bg-emerald-50 text-emerald-700 border-emerald-200 font-bold';
                        } elseif ($sDist === 'Tiba di Gudang') {
                            $statusLabel = 'Tiba di Gudang';
                            $badgeClass = 'bg-blue-50 text-blue-700 border-blue-200 font-bold';
                        } elseif ($sDist === 'Tiba di Rumah' || stripos($sDist, 'rumah') !== false || stripos($sDist, 'kantor') !== false) {
                            $statusLabel = 'Tiba di Rumah';
                            $badgeClass = 'bg-amber-50 text-amber-800 border-amber-200 font-semibold';
                        }
                        $qtyText = rtrim(rtrim(number_format((float)$dist['jumlah'], 2, ',', '.'), '0'), ',') . ' ' . ($dist['satuan'] ?: 'Unit');
                    ?>
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="py-2.5 px-3 text-center text-slate-400 font-mono text-[11px]"><?= $no++ ?></td>
                            <td class="py-2.5 px-3 text-xs font-bold text-slate-800"><?= htmlspecialchars($dist['nama_komponen']) ?></td>
                            <td class="py-2.5 px-3 text-xs text-slate-700"><?= htmlspecialchars($dist['nama_tujuan']) ?></td>
                            <td class="py-2.5 px-2 text-center whitespace-nowrap font-bold text-slate-800 text-xs"><?= htmlspecialchars($qtyText) ?></td>
                            <td class="py-2.5 px-2 text-center whitespace-nowrap">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] border <?= $badgeClass ?>">
                                    <?= htmlspecialchars($statusLabel) ?>
                                </span>
                            </td>
                            <td class="py-2.5 px-3 whitespace-nowrap">
                                <?php if ($photoCount === 0): ?>
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-medium bg-slate-100 text-slate-400 border border-slate-200/70">
                                        <ion-icon name="image-outline" class="text-xs"></ion-icon>
                                        <span>Belum Ada</span>
                                    </span>
                                <?php else:
                                    $photoUrls = array_map(function ($p) {
                                        return APP_URL . '/' . ltrim($p['file_path'], '/');
                                    }, $photos);
                                    $photosJson = htmlspecialchars(json_encode($photoUrls), ENT_QUOTES, 'UTF-8');
                                    $caption = htmlspecialchars(($dist['nama_tujuan'] ?? '') . ' - ' . $dist['nama_komponen'], ENT_QUOTES, 'UTF-8');
                                ?>
                                    <div class="flex items-center gap-1.5">
                                        <div class="flex items-center -space-x-1.5">
                                            <?php foreach (array_slice($photos, 0, 3) as $pIdx => $p):
                                                $thumb = !empty($p['thumb_path']) ? (APP_URL . '/' . ltrim($p['thumb_path'], '/')) : (APP_URL . '/' . ltrim($p['file_path'], '/'));
                                            ?>
                                                <button type="button"
                                                        onclick="openKolabPhotoViewer(<?= $photosJson ?>, <?= $pIdx ?>, '<?= $caption ?>')"
                                                        title="Klik untuk melihat foto"
                                                        class="cursor-pointer focus:outline-none">
                                                    <img src="<?= htmlspecialchars($thumb) ?>" alt="Foto"
                                                         class="w-7 h-7 rounded-lg object-cover border-2 border-white shadow-2xs hover:scale-110 transition-transform">
                                                </button>
                                            <?php endforeach; ?>
                                        </div>
                                        <button type="button"
                                                onclick="openKolabPhotoViewer(<?= $photosJson ?>, 0, '<?= $caption ?>')"
                                                class="inline-flex items-center px-1.5 py-0.5 rounded text-[11px] font-bold bg-teal-50 hover:bg-teal-100 text-teal-700 border border-teal-200/80 cursor-pointer transition-colors">
                                            <?= $photoCount ?> Foto
                                        </button>
                                    </div>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

<?php elseif ($module === 'dokumen'):
    $groupedDistribusi = $modalData['groupedDistribusi'] ?? [];
    $uploadedBastByTujuan = [];
    foreach ($dokumens as $doc) {
        foreach ($groupedDistribusi as $tujuanKey => $grpVal) {
            if (stripos($doc['nama_dokumen'], $tujuanKey) !== false || stripos($doc['nama_file_asli'], $tujuanKey) !== false) {
                $uploadedBastByTujuan[$tujuanKey] = $doc;
                break;
            }
        }
    }
?>
    <div class="bg-white rounded-xl border border-slate-200/90 overflow-hidden shadow-2xs">
        <table class="w-full text-left text-xs">
            <thead class="bg-slate-50 text-[11px] font-bold text-slate-500 uppercase tracking-wider border-b border-slate-200/80">
                <tr>
                    <th class="py-2.5 px-3 w-10 text-center">NO</th>
                    <th class="py-2.5 px-3">KOMPONEN DIKIRIM</th>
                    <th class="py-2.5 px-3">TUJUAN PENERIMA</th>
                    <th class="py-2.5 px-2 text-center w-24">TOTAL QTY</th>
                    <th class="py-2.5 px-2 text-center w-32">STATUS PENERIMAAN</th>
                    <th class="py-2.5 px-3 text-right w-36">BERKAS BAST</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php if (empty($groupedDistribusi)): ?>
                    <tr>
                        <td colspan="6" class="py-8 text-center text-slate-400 text-xs">Belum ada data distribusi.</td>
                    </tr>
                <?php else: ?>
                    <?php $grpNo = 1; foreach ($groupedDistribusi as $grp):
                        $uploadedBast = $uploadedBastByTujuan[$grp['nama_tujuan']] ?? null;
                        $itemStrs = [];
                        foreach (($grp['items'] ?? []) as $gItem) {
                            $itemStrs[] = htmlspecialchars($gItem['nama_komponen'] ?? $gItem['komponen'] ?? '-') . ' (' . (float)($gItem['jumlah'] ?? 0) . ' ' . htmlspecialchars($gItem['satuan'] ?? 'Unit') . ')';
                        }
                        $itemsSummary = !empty($itemStrs) ? implode(', ', $itemStrs) : '-';
                        $itemCount = count($grp['items'] ?? []);
                    ?>
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="py-2.5 px-3 text-center text-slate-400 font-mono text-[11px]"><?= $grpNo++ ?></td>
                            <td class="py-2.5 px-3 text-xs text-slate-700">
                                <div class="font-bold text-slate-800"><?= $itemsSummary ?></div>
                                <div class="text-[10px] text-slate-400"><?= $itemCount ?> macam komponen</div>
                            </td>
                            <td class="py-2.5 px-3 text-xs text-slate-700"><?= htmlspecialchars($grp['nama_tujuan']) ?></td>
                            <td class="py-2.5 px-2 text-center whitespace-nowrap font-bold text-slate-800 text-xs">
                                <?= (float)($grp['total_qty'] ?? 0) ?> Unit
                            </td>
                            <td class="py-2.5 px-2 text-center whitespace-nowrap">
                                <?php if (!empty($grp['is_bast_ready'])): ?>
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <ion-icon name="checkmark-circle" class="text-xs text-emerald-600"></ion-icon>
                                        <span>Siap BAST</span>
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-medium bg-amber-50 text-amber-800 border border-amber-200">
                                        <ion-icon name="time-outline" class="text-xs text-amber-600"></ion-icon>
                                        <span><?= ($grp['status_count']['diterima'] ?? 0) ?>/<?= ($grp['status_count']['total'] ?? 0) ?> Tiba</span>
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="py-2.5 px-3 text-right whitespace-nowrap">
                                <?php if ($uploadedBast && !empty($uploadedBast['file_path'])): ?>
                                    <a href="<?= APP_URL . '/' . ltrim($uploadedBast['file_path'], '/') ?>" target="_blank"
                                       class="inline-flex items-center gap-1 text-[11px] font-semibold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 px-2.5 py-1 rounded-md border border-emerald-200 transition-colors">
                                        <ion-icon name="open-outline"></ion-icon>
                                        <span>Lihat BAST</span>
                                    </a>
                                <?php else: ?>
                                    <span class="text-[11px] text-slate-400 italic">Belum Diupload</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
