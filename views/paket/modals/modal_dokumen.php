<?php
/**
 * Modal View: Berkas Dokumen BAST
 */
$uploadedBastByTujuan = [];
foreach ($dokumenList as $doc) {
    foreach ($groupedDistribusi as $tujuanKey => $grpVal) {
        if (stripos($doc['nama_dokumen'], $tujuanKey) !== false || stripos($doc['nama_file_asli'], $tujuanKey) !== false) {
            $uploadedBastByTujuan[$tujuanKey] = $doc;
            break;
        }
    }
}
$role = $_SESSION['admin_role'] ?? 'user';

$totalTitik = count($groupedDistribusi ?? []);
$totalQty = 0;
$totalUploaded = count($uploadedBastByTujuan);
$totalSiap = 0;
foreach (($groupedDistribusi ?? []) as $grp) {
    $totalQty += (float)($grp['total_qty'] ?? 0);
    if (!empty($grp['is_bast_ready'])) $totalSiap++;
}
?>

<!-- Header Action Teleport Source -->
<div id="modalActionSource" class="hidden">
    <span class="text-xs font-bold px-2.5 py-1 rounded-lg bg-amber-50 text-amber-800 border border-amber-200 inline-flex items-center gap-1 shadow-2xs">
        <ion-icon name="ribbon-outline" class="text-amber-600"></ion-icon>
        <span>Total: <?= $totalTitik ?> Titik (<?= $totalUploaded ?> BAST Diupload)</span>
    </span>
</div>

<!-- 1. Langsung Tabel BAST (Full Width, No Horizontal Scroll) -->
<div class="space-y-3">
    <div class="bg-white rounded-xl border border-slate-200/90 overflow-hidden shadow-2xs">
        <table class="w-full text-left text-xs table-fixed">
            <thead class="bg-slate-50 text-[11px] font-bold text-slate-500 uppercase tracking-wider border-b border-slate-200/80">
                <tr>
                    <th class="py-2.5 px-3 w-10 text-center">NO</th>
                    <th class="py-2.5 px-3 w-48">KOMPONEN DIKIRIM</th>
                    <th class="py-2.5 px-3 w-44">TUJUAN PENERIMA</th>
                    <th class="py-2.5 px-2 text-center w-20">TOTAL QTY</th>
                    <th class="py-2.5 px-2 text-center w-32">STATUS PENERIMAAN</th>
                    <th class="py-2.5 px-3 text-right">AKSI BAST</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php if (empty($groupedDistribusi)): ?>
                    <tr>
                        <td colspan="6" class="py-8 text-center text-slate-400 text-xs">
                            <div class="w-10 h-10 bg-slate-50 rounded-2xl flex items-center justify-center mx-auto mb-2 text-xl text-slate-300">
                                <ion-icon name="document-text-outline"></ion-icon>
                            </div>
                            <h4 class="text-xs font-bold text-slate-700 mb-0.5">Belum Ada Data Distribusi</h4>
                            <p class="text-[11px] text-slate-400">Tentukan alokasi distribusi barang terlebih dahulu di Menu Distribusi.</p>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php $grpNo = 1; foreach ($groupedDistribusi as $grp): ?>
                        <?php 
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
                            <td class="py-2.5 px-3 text-xs text-slate-700 break-words">
                                <div class="font-bold text-slate-800" title="<?= $itemsSummary ?>"><?= $itemsSummary ?></div>
                                <div class="text-[10px] text-slate-400"><?= $itemCount ?> macam komponen</div>
                            </td>
                            <td class="py-2.5 px-3 text-xs font-semibold text-slate-800 break-words">
                                <?= htmlspecialchars($grp['nama_tujuan']) ?>
                            </td>
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
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-medium bg-amber-50 text-amber-800 border border-amber-200"
                                          title="<?= ($grp['status_count']['diterima'] ?? 0) ?> dari <?= ($grp['status_count']['total'] ?? 0) ?> barang telah tiba di gudang/tujuan">
                                        <ion-icon name="time-outline" class="text-xs text-amber-600"></ion-icon>
                                        <span><?= ($grp['status_count']['diterima'] ?? 0) ?>/<?= ($grp['status_count']['total'] ?? 0) ?> Tiba</span>
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="py-2.5 px-3 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-1.5 flex-wrap">
                                    <?php if ($uploadedBast): ?>
                                        <a href="<?= APP_URL ?>/paket/downloadDokumen/<?= $paket['kode_paket'] ?: $paket['id'] ?>/<?= $uploadedBast['id'] ?>?view=1" 
                                           target="_blank"
                                           class="inline-flex items-center gap-1 text-[11px] font-semibold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 px-2 py-0.5 rounded-md border border-emerald-200 transition-colors shadow-2xs"
                                           title="Buka / Preview Berkas: <?= htmlspecialchars($uploadedBast['nama_file_asli']) ?>">
                                            <ion-icon name="open-outline"></ion-icon>
                                            <span>Lihat BAST</span>
                                        </a>

                                        <?php if ($role === 'admin' || $role === 'user'): ?>
                                            <button type="button" 
                                                    onclick="deleteDokumenAjax('<?= $paket['kode_paket'] ?: $paket['id'] ?>', '<?= $uploadedBast['id'] ?>', '<?= htmlspecialchars(addslashes($uploadedBast['nama_dokumen'])) ?>')"
                                                    class="p-1 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-md transition-colors text-xs cursor-pointer"
                                                    title="Hapus Berkas BAST">
                                                <ion-icon name="trash-outline"></ion-icon>
                                            </button>
                                        <?php endif; ?>
                                    <?php endif; ?>

                                    <?php if (!empty($grp['is_bast_ready'])): ?>
                                        <a href="<?= APP_URL ?>/paket/cetakBast/<?= $paket['kode_paket'] ?: $paket['id'] ?>?tujuan=<?= urlencode($grp['nama_tujuan']) ?>" 
                                           target="_blank"
                                           class="inline-flex items-center gap-1 text-[11px] font-semibold text-white bg-amber-600 hover:bg-amber-700 px-2 py-0.5 rounded-md transition-colors shadow-2xs">
                                            <ion-icon name="print-outline"></ion-icon>
                                            <span>Cetak BAST</span>
                                        </a>
                                    <?php else: ?>
                                        <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-slate-400 bg-slate-100 px-2 py-0.5 rounded-md border border-slate-200 cursor-not-allowed"
                                              title="Semua barang harus berstatus 'Tiba di Tujuan' untuk dapat mencetak surat BAST">
                                            <ion-icon name="lock-closed-outline"></ion-icon>
                                            <span>Cetak BAST</span>
                                        </span>
                                    <?php endif; ?>

                                    <?php if ($role === 'admin' || $role === 'user'): ?>
                                        <?php if ($uploadedBast): ?>
                                            <button type="button" 
                                                    onclick="openNativeUploadBastModal('<?= $paket['kode_paket'] ?: $paket['id'] ?>', '<?= htmlspecialchars(addslashes($grp['nama_tujuan'])) ?>')"
                                                    class="inline-flex items-center gap-1 text-[11px] font-semibold text-indigo-700 bg-indigo-50 hover:bg-indigo-100 px-2 py-0.5 rounded-md border border-indigo-200 transition-colors shadow-2xs cursor-pointer"
                                                    title="Ganti Berkas Scan BAST">
                                                <ion-icon name="cloud-upload-outline"></ion-icon>
                                                <span>Ganti BAST</span>
                                            </button>
                                        <?php else: ?>
                                            <?php if (!empty($grp['is_bast_ready'])): ?>
                                                <button type="button" 
                                                        onclick="openNativeUploadBastModal('<?= $paket['kode_paket'] ?: $paket['id'] ?>', '<?= htmlspecialchars(addslashes($grp['nama_tujuan'])) ?>')"
                                                        class="inline-flex items-center gap-1 text-[11px] font-semibold text-blue-700 bg-blue-50 hover:bg-blue-100 px-2 py-0.5 rounded-md border border-blue-200 transition-colors shadow-2xs cursor-pointer">
                                                    <ion-icon name="cloud-upload-outline"></ion-icon>
                                                    <span>Upload BAST</span>
                                                </button>
                                            <?php else: ?>
                                                <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-slate-400 bg-slate-100 px-2 py-0.5 rounded-md border border-slate-200 cursor-not-allowed"
                                                      title="Menunggu barang tiba di lokasi sebelum dapat upload BAST">
                                                    <ion-icon name="lock-closed-outline"></ion-icon>
                                                    <span>Upload BAST</span>
                                                </span>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
            <!-- 2. Ringkasan Info Menyatu di Kolom Bawah Tabel (tfoot) -->
            <tfoot class="bg-slate-50/90 font-bold text-slate-700 border-t-2 border-slate-200">
                <tr>
                    <td class="py-2.5 px-3 text-center text-slate-400 font-mono text-[11px]">-</td>
                    <td colspan="2" class="py-2.5 px-3 text-xs text-slate-800">
                        <span class="font-normal text-slate-500">Total Berkas:</span> <?= $totalTitik ?> Titik Penerima BAST
                    </td>
                    <td class="py-2.5 px-2 text-center text-xs font-extrabold text-slate-900 whitespace-nowrap">
                        <?= number_format($totalQty, 0, ',', '.') ?> Unit
                    </td>
                    <td class="py-2.5 px-2 text-center text-xs whitespace-nowrap">
                        <span class="text-[11px] font-bold text-emerald-700"><?= $totalSiap ?>/<?= $totalTitik ?> Siap</span>
                    </td>
                    <td class="py-2.5 px-3 text-right text-xs whitespace-nowrap">
                        <span class="text-[11px] font-bold text-indigo-700"><?= $totalUploaded ?>/<?= $totalTitik ?> Terupload (<?= $totalTitik - $totalUploaded ?> Pending)</span>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>

    <!-- 5. Informasi Tambahan di Bawah Tabel -->
    <div class="flex items-center justify-between text-[11px] text-slate-500 px-1">
        <div class="flex items-center gap-1.5">
            <ion-icon name="information-circle-outline" class="text-amber-500 text-sm"></ion-icon>
            <span>Surat BAST diterbitkan 1 berkas per sekolah/tujuan gabungan dari semua item komponen yang diterima di lokasi.</span>
        </div>
        <span class="font-medium text-slate-600">Dokumen Lengkap: <strong><?= $totalUploaded ?></strong> dari <strong><?= $totalTitik ?></strong> titik</span>
    </div>
</div>
