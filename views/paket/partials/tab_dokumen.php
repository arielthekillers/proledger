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
            <div class="flex items-center gap-2 flex-wrap">
                <span class="text-xs font-semibold px-2.5 py-1.5 rounded-xl bg-amber-50 text-amber-700 border border-amber-200/80">
                    Total: <?= count($groupedDistribusi ?? []) ?> Titik Penerima
                </span>
                <?php if (!empty($groupedDistribusi)): ?>
                    <a href="<?= APP_URL ?>/paket/cetakBast/<?= $paket['kode_paket'] ?: $paket['id'] ?>?all=1" 
                       target="_blank"
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-amber-600 hover:bg-amber-700 text-white rounded-xl text-xs font-semibold shadow-xs transition-all hover:scale-[1.01]"
                       title="Cetak BAST untuk seluruh titik penerima sekaligus dalam satu dokumen">
                        <ion-icon name="print-outline" class="text-sm"></ion-icon>
                        <span>Cetak Semua BAST</span>
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs" id="bastTable">
                <thead class="bg-slate-50 text-[10px] uppercase font-bold text-slate-500 border-b border-slate-200/90 tracking-wider">
                    <tr>
                        <th class="py-2 px-3 w-8 text-center">NO</th>
                        <th class="py-2 px-3 min-w-[180px]">LOKASI TUJUAN & PENANGGUNG JAWAB</th>
                        <th class="py-2 px-3 min-w-[220px]">ITEM KOMPONEN DIALOKASIKAN</th>
                        <th class="py-2 px-2.5 text-center min-w-[70px]">TOTAL QTY</th>
                        <th class="py-2 px-3 text-center min-w-[140px]">STATUS PENERIMAAN</th>
                        <th class="py-2 px-3 text-right min-w-[210px]">AKSI BAST</th>
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
                            <?php $uploadedBast = $uploadedBastByTujuan[$grp['nama_tujuan']] ?? null; ?>
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <!-- No -->
                                <td class="py-2 px-3 text-center font-mono text-[11px] text-slate-400 font-semibold">
                                    <?= $grpNo++ ?>
                                </td>

                                <!-- Lokasi Tujuan & PIC -->
                                <td class="py-2 px-3">
                                    <div class="font-bold text-slate-800 text-xs flex items-center gap-1.5">
                                        <span><?= htmlspecialchars($grp['nama_tujuan']) ?></span>
                                        <span class="px-1.5 py-0.2 rounded text-[9px] font-semibold bg-indigo-50 text-indigo-700 border border-indigo-200/80">
                                            <?= htmlspecialchars($grp['kategori'] ?: 'Sekolah') ?>
                                        </span>
                                    </div>
                                    <div class="text-[11px] text-slate-600 mt-0.5">
                                        <span class="font-medium text-slate-700"><?= htmlspecialchars($grp['nama_pic'] ?: '-') ?></span>
                                        <span class="text-slate-400 text-[10px]">(<?= htmlspecialchars($grp['jabatan_pic'] ?: 'Kepala Sekolah') ?>)</span>
                                    </div>
                                    <?php if (!empty($grp['alamat']) || !empty($grp['kab_kota'])): ?>
                                        <div class="text-[10px] text-slate-400 truncate max-w-sm mt-0.5" title="<?= htmlspecialchars($grp['alamat'] ?: $grp['kab_kota']) ?>">
                                            <?= htmlspecialchars($grp['alamat'] ?: $grp['kab_kota']) ?>
                                        </div>
                                    <?php endif; ?>
                                </td>

                                <!-- Daftar Item Komponen -->
                                <td class="py-2 px-3">
                                    <div class="space-y-1">
                                        <?php foreach ($grp['items'] as $gItem): ?>
                                            <div class="flex items-center justify-between gap-2 text-[11px] bg-slate-50 px-2 py-0.5 rounded border border-slate-100">
                                                <span class="font-medium text-slate-700 truncate"><?= htmlspecialchars($gItem['nama_komponen']) ?></span>
                                                <span class="font-mono font-bold text-slate-800 shrink-0"><?= (float)$gItem['jumlah'] ?> <?= htmlspecialchars($gItem['satuan']) ?></span>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </td>

                                <!-- Total Qty -->
                                <td class="py-2 px-2.5 text-center font-mono font-bold text-slate-900 text-xs bg-amber-50/20 whitespace-nowrap">
                                    <?= (float)$grp['total_qty'] ?> Unit
                                </td>

                                <!-- Status Kesiapan BAST -->
                                <td class="py-2 px-3 text-center whitespace-nowrap">
                                    <?php if ($grp['is_bast_ready']): ?>
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            <ion-icon name="checkmark-circle" class="text-xs"></ion-icon>
                                            <span>Lengkap di Tujuan</span>
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                            <ion-icon name="time-outline" class="text-xs"></ion-icon>
                                            <span><?= $grp['status_count']['diterima'] ?>/<?= $grp['status_count']['total'] ?> Tiba di Tujuan</span>
                                        </span>
                                    <?php endif; ?>
                                </td>

                                <!-- Aksi Cetak BAST & Upload BAST -->
                                <td class="py-2 px-3 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-1.5 flex-wrap">
                                        <!-- Tombol Cetak BAST -->
                                        <a href="<?= APP_URL ?>/paket/cetakBast/<?= $paket['kode_paket'] ?: $paket['id'] ?>?tujuan=<?= urlencode($grp['nama_tujuan']) ?>" 
                                           target="_blank"
                                           class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold <?= $grp['is_bast_ready'] ? 'bg-amber-600 hover:bg-amber-700 text-white shadow-xs' : 'bg-slate-100 text-slate-500 hover:bg-slate-200' ?> transition-all"
                                           title="<?= $grp['is_bast_ready'] ? 'Cetak Lembar Resmi BAST' : 'Pratinjau Draft BAST (Barang belum lengkap diterima)' ?>">
                                            <ion-icon name="print-outline"></ion-icon>
                                            <span>Cetak BAST</span>
                                        </a>

                                        <!-- Tombol Upload BAST -->
                                        <?php if ($role === 'admin' || $role === 'user'): ?>
                                            <button type="button" 
                                                    onclick="openUploadBastSekolah('<?= htmlspecialchars(addslashes($grp['nama_tujuan'])) ?>')"
                                                    class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold bg-indigo-50 text-indigo-700 hover:bg-indigo-100 border border-indigo-200/80 transition-colors"
                                                    title="Upload scan/foto berkas BAST yang sudah ditandatangani">
                                                <ion-icon name="cloud-upload-outline"></ion-icon>
                                                <span>Upload BAST</span>
                                            </button>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Berkas BAST yang Sudah Terupload -->
                                    <?php if ($uploadedBast): ?>
                                        <div class="flex items-center justify-end gap-1.5 mt-1.5">
                                            <a href="<?= APP_URL ?>/paket/downloadDokumen/<?= $paket['kode_paket'] ?: $paket['id'] ?>/<?= $uploadedBast['id'] ?>?view=1" 
                                               target="_blank"
                                               class="inline-flex items-center gap-1 text-[10px] font-bold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 px-2 py-0.5 rounded border border-emerald-200 transition-colors"
                                               title="Buka / Preview Berkas: <?= htmlspecialchars($uploadedBast['nama_file_asli']) ?>">
                                                <ion-icon name="document-attach-outline"></ion-icon>
                                                <span>BAST Terupload (<?= strtoupper($uploadedBast['file_ext'] ?: 'FILE') ?>)</span>
                                            </a>
                                            <a href="<?= APP_URL ?>/paket/downloadDokumen/<?= $paket['kode_paket'] ?: $paket['id'] ?>/<?= $uploadedBast['id'] ?>" 
                                               class="p-0.5 text-slate-400 hover:text-emerald-700 rounded transition-colors text-xs"
                                               title="Unduh Berkas BAST">
                                                <ion-icon name="download-outline"></ion-icon>
                                            </a>
                                            <?php if ($role === 'admin' || $role === 'user'): ?>
                                                <a href="<?= APP_URL ?>/paket/deleteDokumen/<?= $paket['kode_paket'] ?: $paket['id'] ?>/<?= $uploadedBast['id'] ?>" 
                                                   data-confirm-delete="<?= htmlspecialchars($uploadedBast['nama_dokumen']) ?>"
                                                   data-type="Dokumen BAST"
                                                   class="p-0.5 text-slate-400 hover:text-rose-600 rounded transition-colors text-xs"
                                                   title="Hapus Berkas BAST">
                                                    <ion-icon name="trash-outline"></ion-icon>
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
