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
?>

<div class="space-y-4">
    <!-- Top Summary Banner -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white p-4 rounded-xl border border-slate-200/90 shadow-2xs">
        <div>
            <h4 class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                <ion-icon name="ribbon-outline" class="text-amber-600 text-base"></ion-icon>
                <span>Penerbitan & Upload Berita Acara Serah Terima (BAST)</span>
            </h4>
            <p class="text-[11px] text-slate-400 mt-0.5">Surat BAST diterbitkan 1 berkas per sekolah/tujuan gabungan dari semua item komponen yang diterima di lokasi.</p>
        </div>
        <span class="text-xs font-bold px-3 py-1 rounded-lg bg-amber-50 text-amber-700 border border-amber-200 shrink-0 self-start sm:self-auto">
            Total: <?= count($groupedDistribusi ?? []) ?> Titik Penerima
        </span>
    </div>

    <!-- Tabel BAST per Sekolah -->
    <div class="bg-white rounded-xl border border-slate-200/90 overflow-hidden shadow-2xs">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-[11px] font-bold text-slate-500 uppercase tracking-wider border-b border-slate-200/80">
                    <tr>
                        <th class="py-2.5 px-3 w-8 text-center">NO</th>
                        <th class="py-2.5 px-3 min-w-[170px]">KOMPONEN</th>
                        <th class="py-2.5 px-3 min-w-[160px]">TUJUAN PENERIMA</th>
                        <th class="py-2.5 px-2.5 text-center min-w-[80px]">TOTAL QTY</th>
                        <th class="py-2.5 px-3 text-center min-w-[130px]">STATUS PENERIMAAN</th>
                        <th class="py-2.5 px-3 text-right min-w-[180px]">AKSI BAST</th>
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
                            ?>
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <td class="py-2 px-3 text-center text-slate-400 font-mono text-[11px]"><?= $grpNo++ ?></td>
                                <td class="py-2 px-3 text-xs text-slate-700">
                                    <div class="font-bold text-slate-800"><?= htmlspecialchars($grp['item_names_summary'] ?? '-') ?></div>
                                    <div class="text-[10px] text-slate-400"><?= (int)$grp['item_count'] ?> macam komponen</div>
                                </td>
                                <td class="py-2 px-3 whitespace-nowrap text-xs font-semibold text-slate-800">
                                    <?= htmlspecialchars($grp['nama_tujuan']) ?>
                                </td>
                                <td class="py-2 px-2.5 text-center whitespace-nowrap font-bold text-slate-800 text-xs">
                                    <?= (float)$grp['total_qty'] ?> Unit
                                </td>
                                <td class="py-2 px-3 text-center whitespace-nowrap">
                                    <?php if ($grp['is_bast_ready']): ?>
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            <ion-icon name="checkmark-circle" class="text-xs text-emerald-600"></ion-icon>
                                            <span>Siap BAST</span>
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-medium bg-amber-50 text-amber-800 border border-amber-200"
                                              title="<?= $grp['status_count']['diterima'] ?> dari <?= $grp['status_count']['total'] ?> barang telah tiba di gudang/tujuan">
                                            <ion-icon name="time-outline" class="text-xs text-amber-600"></ion-icon>
                                            <span><?= $grp['status_count']['diterima'] ?>/<?= $grp['status_count']['total'] ?> Tiba</span>
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-2 px-3 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <?php if ($uploadedBast): ?>
                                            <a href="<?= APP_URL ?>/paket/downloadDokumen/<?= $paket['kode_paket'] ?: $paket['id'] ?>/<?= $uploadedBast['id'] ?>?view=1" 
                                               target="_blank"
                                               class="inline-flex items-center gap-1 text-[11px] font-semibold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 px-2 py-0.5 rounded-md border border-emerald-200 transition-colors shadow-2xs"
                                               title="Buka / Preview Berkas: <?= htmlspecialchars($uploadedBast['nama_file_asli']) ?>">
                                                <ion-icon name="document-attach-outline" class="text-xs"></ion-icon>
                                                <span>Lihat (<?= strtoupper($uploadedBast['file_ext'] ?: 'PDF') ?>)</span>
                                            </a>
                                            <?php if ($role === 'admin' || $role === 'user'): ?>
                                                <button type="button" 
                                                        onclick="deleteDokumenAjax('<?= $paket['kode_paket'] ?: $paket['id'] ?>', '<?= $uploadedBast['id'] ?>', '<?= htmlspecialchars(addslashes($uploadedBast['nama_dokumen'])) ?>')"
                                                        class="p-1 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-md transition-colors text-xs inline-flex items-center justify-center cursor-pointer"
                                                        title="Hapus Berkas BAST">
                                                    <ion-icon name="trash-outline"></ion-icon>
                                                </button>
                                            <?php endif; ?>
                                        <?php endif; ?>

                                        <?php if ($grp['is_bast_ready']): ?>
                                            <a href="<?= APP_URL ?>/paket/cetakBast/<?= $paket['kode_paket'] ?: $paket['id'] ?>?tujuan=<?= urlencode($grp['nama_tujuan']) ?>" 
                                               target="_blank"
                                               class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md text-[11px] font-semibold bg-amber-600 hover:bg-amber-700 text-white shadow-2xs transition-all"
                                               title="Cetak Berkas BAST">
                                                <ion-icon name="print-outline" class="text-xs"></ion-icon>
                                                <span>Cetak</span>
                                            </a>
                                        <?php else: ?>
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-medium bg-slate-100 text-slate-400 border border-slate-200 cursor-not-allowed select-none opacity-80"
                                                  title="Semua barang harus tiba di gudang atau tujuan terlebih dahulu untuk cetak BAST">
                                                <ion-icon name="lock-closed-outline" class="text-xs"></ion-icon>
                                                <span>Cetak</span>
                                            </span>
                                        <?php endif; ?>

                                        <?php if ($role === 'admin' || $role === 'user'): ?>
                                            <?php if ($grp['is_bast_ready']): ?>
                                                <button type="button" 
                                                        onclick="openNativeUploadBastModal('<?= $paket['kode_paket'] ?: $paket['id'] ?>', '<?= htmlspecialchars(addslashes($grp['nama_tujuan'])) ?>')"
                                                        class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md text-[11px] font-semibold bg-indigo-50 text-indigo-700 hover:bg-indigo-100 border border-indigo-200 shadow-2xs transition-all cursor-pointer">
                                                    <ion-icon name="cloud-upload-outline" class="text-xs"></ion-icon>
                                                    <span><?= $uploadedBast ? 'Ganti' : 'Upload' ?></span>
                                                </button>
                                            <?php else: ?>
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-medium bg-slate-100 text-slate-400 border border-slate-200 cursor-not-allowed select-none opacity-80"
                                                      title="Semua barang harus tiba di gudang atau tujuan terlebih dahulu untuk upload BAST">
                                                    <ion-icon name="lock-closed-outline" class="text-xs"></ion-icon>
                                                    <span>Upload</span>
                                                </span>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
