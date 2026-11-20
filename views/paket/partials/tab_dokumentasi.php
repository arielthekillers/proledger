<!-- ========================================== -->
<!-- 5A. TAB 4: DOKUMENTASI PENGIRIMAN & BARANG -->
<!-- ========================================== -->
<div id="tabContent_dokumentasi" class="<?= ($activeTab === 'dokumentasi') ? '' : 'hidden ' ?>space-y-4">
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <!-- Header Card -->
        <div class="p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100">
            <div>
                <h3 class="text-base font-bold text-slate-800 flex items-center gap-2">
                    <ion-icon name="camera-outline" class="text-teal-600 text-lg"></ion-icon>
                    <span>Dokumentasi Pengiriman & Serah Terima Barang</span>
                </h3>
                <p class="text-xs text-slate-400 mt-0.5">Upload dan kelola foto fisik barang, proses pengiriman, dan serah terima per alokasi tujuan penerima.</p>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-xs font-semibold px-2.5 py-1 rounded-lg bg-teal-50 text-teal-700 border border-teal-200/80 flex items-center gap-1.5">
                    <ion-icon name="images-outline" class="text-sm"></ion-icon>
                    <span>Total Foto: <strong id="headerTotalFotoBadge"><?= (int)($totalDokumentasiFoto ?? 0) ?></strong></span>
                </span>
            </div>
        </div>

        <!-- Tabel Dokumentasi -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs" id="dokumentasiTable">
                <thead class="bg-slate-50 text-[10px] uppercase font-bold text-slate-500 border-b border-slate-200/90 tracking-wider">
                    <tr>
                        <th class="py-2.5 px-3 w-8 text-center">NO</th>
                        <th class="py-2.5 px-3 min-w-[150px]">KOMPONEN BARANG</th>
                        <th class="py-2.5 px-3 min-w-[170px]">TUJUAN PENERIMA</th>
                        <th class="py-2.5 px-2.5 text-center min-w-[70px]">SATUAN</th>
                        <th class="py-2.5 px-2.5 text-center min-w-[60px]">JUMLAH</th>
                        <th class="py-2.5 px-3 min-w-[130px]">STATUS DISTRIBUSI</th>
                        <th class="py-2.5 px-3 min-w-[180px]">FOTO DOKUMENTASI</th>
                        <th class="py-2.5 px-2.5 text-right w-28">AKSI</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php if (empty($distribusiList)): ?>
                        <tr>
                            <td colspan="8" class="py-8 text-center text-slate-400">
                                <div class="w-10 h-10 bg-slate-50 rounded-2xl flex items-center justify-center mx-auto mb-2 text-xl text-slate-300">
                                    <ion-icon name="camera-outline"></ion-icon>
                                </div>
                                <h4 class="text-xs font-bold text-slate-700 mb-0.5">Belum Ada Alokasi Barang</h4>
                                <p class="text-[11px] text-slate-400 mb-3">Tentukan alokasi tujuan dan barang terlebih dahulu di tab <strong>Item / Spesifikasi</strong> atau <strong>Distribusi</strong>.</p>
                                <button type="button" onclick="switchDetailTab('items')" 
                                        class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-semibold shadow-xs transition-all">
                                    <ion-icon name="list-outline"></ion-icon>
                                    <span>Buka Tab Item / Spesifikasi</span>
                                </button>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php 
                        $no = 1; 
                        $itemsWithDocs = 0;
                        foreach ($distribusiList as $dist): 
                            $photos = $dokumentasiMap[$dist['id']] ?? [];
                            $photoCount = count($photos);
                            if ($photoCount > 0) $itemsWithDocs++;
                            $sDist = $dist['status_distribusi'] ?? '';
                            $isOrdered = (($dist['status_barang'] ?? '') === 'Sudah Dipesan');
                        ?>
                            <tr class="hover:bg-slate-50/70 transition-colors" id="row_dok_<?= $dist['id'] ?>">
                                <!-- No -->
                                <td class="py-2 px-3 text-center font-mono text-[11px] text-slate-400 font-semibold whitespace-nowrap">
                                    <?= $no++ ?>
                                </td>

                                <!-- Komponen Barang -->
                                <td class="py-2 px-3 whitespace-nowrap text-xs font-bold text-slate-800">
                                    <?= htmlspecialchars($dist['nama_komponen'] ?: '-') ?>
                                </td>

                                <!-- Tujuan Penerima -->
                                <td class="py-2 px-3 whitespace-nowrap text-xs text-slate-700">
                                    <div class="font-semibold text-slate-800"><?= htmlspecialchars($dist['nama_tujuan']) ?></div>
                                    <?php if (!empty($dist['kab_kota'])): ?>
                                        <div class="text-[10px] text-slate-400 font-normal"><?= htmlspecialchars($dist['kab_kota']) ?></div>
                                    <?php endif; ?>
                                </td>

                                <!-- Satuan -->
                                <td class="py-2 px-2.5 text-center whitespace-nowrap">
                                    <span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-700 font-semibold text-xs">
                                        <?= htmlspecialchars($dist['satuan'] ?: 'Unit') ?>
                                    </span>
                                </td>

                                <!-- Jumlah -->
                                <td class="py-2 px-2.5 text-center whitespace-nowrap font-bold text-slate-800 text-xs">
                                    <?= (float)$dist['jumlah'] ?>
                                </td>

                                <!-- Status Distribusi -->
                                <td class="py-2 px-3 whitespace-nowrap">
                                    <?php if (!$isOrdered): ?>
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-md text-xs font-medium leading-normal bg-slate-100 text-slate-400 border border-slate-200/80">
                                            <ion-icon name="lock-closed-outline" class="text-xs"></ion-icon>
                                            <span>Belum Dipesan</span>
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-md text-xs font-medium leading-normal <?= ($sDist === 'Tiba di Tujuan') ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : (($sDist === 'Tiba di Gudang') ? 'bg-amber-50 text-amber-700 border border-amber-200' : 'bg-blue-50 text-blue-700 border border-blue-200') ?>">
                                            <?php if ($sDist === 'Tiba di Tujuan'): ?>
                                                <ion-icon name="checkmark-done-circle" class="text-xs text-emerald-600 shrink-0"></ion-icon>
                                            <?php elseif ($sDist === 'Tiba di Gudang'): ?>
                                                <ion-icon name="business-outline" class="text-xs shrink-0 text-amber-600"></ion-icon>
                                            <?php else: ?>
                                                <ion-icon name="home-outline" class="text-xs shrink-0 text-blue-600"></ion-icon>
                                            <?php endif; ?>
                                            <span><?= htmlspecialchars($sDist ?: 'Dalam Pengiriman') ?></span>
                                        </span>
                                    <?php endif; ?>
                                </td>

                                <!-- Foto Dokumentasi Strip -->
                                <td class="py-2 px-3 whitespace-nowrap" id="cell_dok_photos_<?= $dist['id'] ?>">
                                    <?php if ($photoCount === 0): ?>
                                        <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-md text-xs font-medium bg-slate-100 text-slate-400 border border-slate-200/70">
                                            <ion-icon name="image-outline" class="text-xs"></ion-icon>
                                            <span>Belum Ada Foto</span>
                                        </span>
                                    <?php else: ?>
                                        <div class="flex items-center gap-1.5">
                                            <div class="flex items-center -space-x-1.5">
                                                <?php foreach (array_slice($photos, 0, 3) as $p): ?>
                                                    <?php 
                                                     $thumb = !empty($p['thumb_path']) ? (APP_URL . '/' . ltrim($p['thumb_path'], '/')) : (APP_URL . '/' . ltrim($p['file_path'], '/'));
                                                    $full = APP_URL . '/' . ltrim($p['file_path'], '/');
                                                    ?>
                                                    <img src="<?= htmlspecialchars($thumb) ?>" 
                                                         alt="Foto" 
                                                         onclick="openPhotoLightbox('<?= htmlspecialchars($full) ?>', '<?= htmlspecialchars(addslashes($dist['nama_tujuan'] . ' - ' . ($dist['nama_komponen'] ?: 'Barang'))) ?>')"
                                                         class="w-7 h-7 rounded-lg object-cover border-2 border-white shadow-2xs hover:scale-110 hover:z-10 transition-transform cursor-pointer"
                                                         title="Klik untuk memperbesar">
                                                <?php endforeach; ?>
                                            </div>
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[11px] font-bold bg-teal-50 text-teal-700 border border-teal-200/80">
                                                <?= $photoCount ?> Foto
                                            </span>
                                        </div>
                                    <?php endif; ?>
                                </td>

                                <!-- Aksi Upload / Galeri -->
                                <td class="py-2 px-2.5 text-right whitespace-nowrap">
                                    <button type="button" 
                                            onclick="openModalDokumentasi(<?= (int)$dist['id'] ?>, '<?= htmlspecialchars(addslashes($dist['nama_tujuan'])) ?>', '<?= htmlspecialchars(addslashes($dist['nama_komponen'] ?: '-')) ?>', '<?= (float)$dist['jumlah'] ?> <?= htmlspecialchars(addslashes($dist['satuan'] ?: 'Unit')) ?>')"
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-teal-50 hover:bg-teal-100 text-teal-700 border border-teal-200 rounded-lg text-xs font-semibold transition-all hover:scale-[1.02] shadow-2xs cursor-pointer group"
                                            title="Upload dan lihat galeri dokumentasi">
                                        <ion-icon name="cloud-upload-outline" class="text-xs group-hover:scale-110 transition-transform"></ion-icon>
                                        <span><?= $photoCount > 0 ? 'Kelola Foto' : 'Upload Foto' ?></span>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>

                <!-- Footer Summary Row -->
                <?php if (!empty($distribusiList)): ?>
                    <tfoot class="bg-slate-50/90 font-bold text-slate-700 border-t-2 border-slate-200 text-xs">
                        <tr>
                            <td colspan="4" class="py-2.5 px-3 text-right uppercase text-[10px] tracking-wider text-slate-500">
                                Total:
                            </td>
                            <td class="py-2.5 px-2.5 text-center font-bold text-slate-800 text-xs whitespace-nowrap">
                                <?= (float)($distribusiSummary['total_qty_dialokasikan'] ?? 0) ?>
                            </td>
                            <td colspan="3" class="py-2.5 px-3 text-slate-500 text-[10px] font-normal">
                                <span class="font-bold text-teal-700"><?= $itemsWithDocs ?></span> dari <?= count($distribusiList) ?> titik alokasi telah memiliki foto dokumentasi (Total: <span class="font-bold text-teal-700" id="footerTotalFotoCount"><?= (int)($totalDokumentasiFoto ?? 0) ?></span> foto)
                            </td>
                        </tr>
                    </tfoot>
                <?php endif; ?>
            </table>
        </div>
    </div>
</div>
