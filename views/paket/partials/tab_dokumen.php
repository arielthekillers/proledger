<!-- ========================================== -->
<!-- 5B. TAB 4: DOKUMEN & BAST                  -->
<!-- ========================================== -->
<div id="tabContent_dokumen" class="<?= ($activeTab === 'dokumen') ? '' : 'hidden ' ?>space-y-4">
    <?php
    // Lookup BAST terupload per tujuan
    $uploadedBastByTujuan = [];
    foreach ($dokumenList as $doc) {
        foreach ($groupedDistribusi as $tujuanKey => $grpVal) {
            if (stripos($doc['nama_dokumen'], $tujuanKey) !== false || stripos($doc['nama_file_asli'], $tujuanKey) !== false) {
                $uploadedBastByTujuan[$tujuanKey] = $doc;
                break;
            }
        }
    }
    ?>

    <!-- Tabel BAST per Sekolah / Lokasi Tujuan -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100">
            <div>
                <h3 class="text-base font-bold text-slate-800 flex items-center gap-2">
                    <ion-icon name="ribbon-outline" class="text-amber-600 text-lg"></ion-icon>
                    <span>Penerbitan & Upload Berita Acara Serah Terima (BAST)</span>
                </h3>
                <p class="text-xs text-slate-400 mt-0.5">Surat BAST diterbitkan 1 berkas per sekolah/tujuan gabungan dari semua item komponen yang diterima di lokasi.</p>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-xs font-semibold px-2.5 py-1 rounded-lg bg-amber-50 text-amber-700 border border-amber-200/80">
                    Total: <?= count($groupedDistribusi ?? []) ?> Titik Penerima
                </span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs" id="bastTable">
                <thead class="bg-slate-50/80 text-[11px] font-bold text-slate-500 uppercase tracking-wider border-b border-slate-200/80">
                    <tr>
                        <th class="py-2 px-3 w-8 text-center">NO</th>
                        <th class="py-2 px-3 min-w-[170px]">TUJUAN PENERIMA</th>
                        <th class="py-2 px-3 min-w-[180px]">ITEM KOMPONEN</th>
                        <th class="py-2 px-2.5 text-center min-w-[70px]">TOTAL QTY</th>
                        <th class="py-2 px-3 text-center min-w-[140px]">STATUS PENERIMAAN</th>
                        <th class="py-2 px-3 text-right min-w-[200px]">AKSI BAST</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php if (empty($groupedDistribusi)): ?>
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-400 text-xs">
                                <div class="w-10 h-10 bg-slate-50 rounded-2xl flex items-center justify-center mx-auto mb-2 text-xl text-slate-300">
                                    <ion-icon name="document-text-outline"></ion-icon>
                                </div>
                                <h4 class="text-xs font-bold text-slate-700 mb-0.5">Belum Ada Data BAST</h4>
                                <p class="text-[11px] text-slate-400">Tentukan alokasi distribusi barang terlebih dahulu di Tab Distribusi.</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php $grpNo = 1; foreach ($groupedDistribusi as $grp): ?>
                            <?php 
                            $uploadedBast = $uploadedBastByTujuan[$grp['nama_tujuan']] ?? null; 
                            $itemStrs = [];
                            foreach ($grp['items'] as $gItem) {
                                $itemStrs[] = htmlspecialchars($gItem['nama_komponen']) . ' (' . (float)$gItem['jumlah'] . ' ' . htmlspecialchars($gItem['satuan']) . ')';
                            }
                            $itemsText = implode(', ', $itemStrs);
                            ?>
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <!-- No -->
                                <td class="py-1.5 px-3 text-center font-mono text-[11px] text-slate-400 font-semibold whitespace-nowrap">
                                    <?= $grpNo++ ?>
                                </td>

                                <!-- Tujuan Penerima (Hanya nama sekolah / tujuan, tanpa info kepsek & alamat) -->
                                <td class="py-1.5 px-3 whitespace-nowrap text-xs font-semibold text-slate-800">
                                    <?= htmlspecialchars($grp['nama_tujuan']) ?>
                                </td>

                                <!-- Daftar Item Komponen -->
                                <td class="py-1.5 px-3 whitespace-nowrap text-xs text-slate-700 max-w-xs truncate" title="<?= $itemsText ?>">
                                    <?= $itemsText ?>
                                </td>

                                <!-- Total Qty -->
                                <td class="py-1.5 px-2.5 text-center whitespace-nowrap font-bold text-slate-800 text-xs">
                                    <?= (float)$grp['total_qty'] ?> Unit
                                </td>

                                <!-- Status Penerimaan (Semua barang harus Tiba di Gudang atau Tiba di Tujuan) -->
                                <td class="py-1.5 px-3 text-center whitespace-nowrap">
                                    <?php if ($grp['is_bast_ready']): ?>
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-md text-xs font-medium leading-normal bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            <ion-icon name="checkmark-circle" class="text-xs text-emerald-600 shrink-0"></ion-icon>
                                            <span>Siap BAST</span>
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-md text-xs font-medium leading-normal bg-amber-50 text-amber-700 border border-amber-200"
                                              title="<?= $grp['status_count']['diterima'] ?> dari <?= $grp['status_count']['total'] ?> barang telah tiba di gudang/tujuan">
                                            <ion-icon name="time-outline" class="text-xs text-amber-600 shrink-0"></ion-icon>
                                            <span><?= $grp['status_count']['diterima'] ?>/<?= $grp['status_count']['total'] ?> Barang Tiba</span>
                                        </span>
                                    <?php endif; ?>
                                </td>

                                <!-- Aksi BAST: Cetak & Upload hanya bisa jika semua barang tiba di gudang/tujuan -->
                                <td class="py-1.5 px-3 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <!-- Berkas BAST yang Sudah Terupload -->
                                        <?php if ($uploadedBast): ?>
                                            <a href="<?= APP_URL ?>/paket/downloadDokumen/<?= $paket['kode_paket'] ?: $paket['id'] ?>/<?= $uploadedBast['id'] ?>?view=1" 
                                               target="_blank"
                                               class="inline-flex items-center gap-1 text-[11px] font-semibold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 px-2 py-0.5 rounded-md border border-emerald-200 transition-colors shadow-2xs"
                                               title="Buka / Preview Berkas: <?= htmlspecialchars($uploadedBast['nama_file_asli']) ?>">
                                                <ion-icon name="document-attach-outline" class="text-xs"></ion-icon>
                                                <span>Lihat BAST (<?= strtoupper($uploadedBast['file_ext'] ?: 'FILE') ?>)</span>
                                            </a>
                                            <?php if ($role === 'admin' || $role === 'user'): ?>
                                                <a href="<?= APP_URL ?>/paket/deleteDokumen/<?= $paket['kode_paket'] ?: $paket['id'] ?>/<?= $uploadedBast['id'] ?>" 
                                                   data-confirm-delete="<?= htmlspecialchars($uploadedBast['nama_dokumen']) ?>"
                                                   data-type="Dokumen BAST"
                                                   class="p-1 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-md transition-colors text-xs inline-flex items-center justify-center"
                                                   title="Hapus Berkas BAST">
                                                    <ion-icon name="trash-outline"></ion-icon>
                                                </a>
                                            <?php endif; ?>
                                        <?php endif; ?>

                                        <!-- Tombol Cetak BAST -->
                                        <?php if ($grp['is_bast_ready']): ?>
                                            <a href="<?= APP_URL ?>/paket/cetakBast/<?= $paket['kode_paket'] ?: $paket['id'] ?>?tujuan=<?= urlencode($grp['nama_tujuan']) ?>" 
                                               target="_blank"
                                               class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md text-xs font-medium leading-normal bg-amber-600 hover:bg-amber-700 text-white shadow-2xs transition-all hover:scale-[1.02]"
                                               title="Cetak Berkas BAST">
                                                <ion-icon name="print-outline" class="text-xs"></ion-icon>
                                                <span>Cetak BAST</span>
                                            </a>
                                        <?php else: ?>
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md text-xs font-medium leading-normal bg-slate-100 text-slate-400 border border-slate-200/80 cursor-not-allowed select-none opacity-80"
                                                  title="Semua barang harus tiba di gudang atau tujuan terlebih dahulu untuk cetak BAST">
                                                <ion-icon name="lock-closed-outline" class="text-xs"></ion-icon>
                                                <span>Cetak BAST</span>
                                            </span>
                                        <?php endif; ?>

                                        <!-- Tombol Upload BAST -->
                                        <?php if ($role === 'admin' || $role === 'user'): ?>
                                            <?php if ($grp['is_bast_ready']): ?>
                                                <button type="button" 
                                                        onclick="openUploadBastSekolah('<?= htmlspecialchars(addslashes($grp['nama_tujuan'])) ?>')"
                                                        class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md text-xs font-medium leading-normal bg-indigo-50 text-indigo-700 hover:bg-indigo-100 border border-indigo-200/80 shadow-2xs transition-all hover:scale-[1.02]"
                                                        title="<?= $uploadedBast ? 'Ganti File BAST' : 'Upload Berkas BAST yang sudah ditandatangani' ?>">
                                                    <ion-icon name="cloud-upload-outline" class="text-xs"></ion-icon>
                                                    <span><?= $uploadedBast ? 'Ganti BAST' : 'Upload BAST' ?></span>
                                                </button>
                                            <?php else: ?>
                                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md text-xs font-medium leading-normal bg-slate-100 text-slate-400 border border-slate-200/80 cursor-not-allowed select-none opacity-80"
                                                      title="Semua barang harus tiba di gudang atau tujuan terlebih dahulu untuk upload BAST">
                                                    <ion-icon name="lock-closed-outline" class="text-xs"></ion-icon>
                                                    <span>Upload BAST</span>
                                                </span>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>

                <!-- Footer Summary Row -->
                <?php if (!empty($groupedDistribusi)): ?>
                    <tfoot class="bg-slate-50/90 font-bold text-slate-700 border-t-2 border-slate-200">
                        <tr>
                            <td colspan="3" class="py-2 px-3 text-right uppercase text-[10px] tracking-wider text-slate-500">
                                Ringkasan Dokumen BAST:
                            </td>
                            <td class="py-2 px-2.5 text-center font-bold text-slate-800 text-xs whitespace-nowrap">
                                <?= (float)($distribusiSummary['total_qty_dialokasikan'] ?? 0) ?> Unit
                            </td>
                            <td colspan="2" class="py-2 px-3 text-slate-500 text-[10px] font-normal">
                                <span class="font-bold text-amber-700"><?= count($uploadedBastByTujuan) ?></span> dari <?= count($groupedDistribusi) ?> berkas BAST telah diupload (<?= (int)(count($groupedDistribusi) - count($uploadedBastByTujuan)) ?> pending)
                            </td>
                        </tr>
                    </tfoot>
                <?php endif; ?>
            </table>
        </div>
    </div>
</div>
