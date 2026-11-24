<?php
/**
 * Modal View: Dokumentasi Pengiriman & Serah Terima Barang
 */
$role = $_SESSION['admin_role'] ?? 'user';
$generalDocs = $generalDokumentasi ?? [];
$globalDocs = [];
$itemDocMap = [];
foreach ($generalDocs as $gd) {
    if (empty($gd['item_id'])) {
        $globalDocs[] = $gd;
    } else {
        $itemDocMap[(int)$gd['item_id']][] = $gd;
    }
}
$totalDist = count($distribusiList);
$isDokSelesai = !empty($paket['is_dok_selesai']);
$itemsWithDocs = 0;
$totalQty = 0;
foreach ($distribusiList as $dist) {
    $photos = $dokumentasiMap[$dist['id']] ?? [];
    if (count($photos) > 0) $itemsWithDocs++;
    $totalQty += (float)$dist['jumlah'];
}
if ($isDokSelesai) {
    $itemsWithDocs = $totalDist;
}
$totalFotoAll = (int)($totalDokumentasiFoto ?? 0);
$pctDok = $isDokSelesai ? 100 : (($totalDist > 0) ? round(($itemsWithDocs / $totalDist) * 100) : 0);
$paketKode = $paket['kode_paket'] ?: $paket['id'];
?>

<!-- Header Action Teleport Source -->
<div id="modalActionSource" class="hidden">
    <div class="flex items-center gap-2">
        <?php if ($role === 'admin' || $role === 'user'): ?>
            <button type="button" onclick="openModalDokUmum()"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-teal-600 hover:bg-teal-700 text-white rounded-xl text-xs font-semibold shadow-xs transition-all cursor-pointer">
                <ion-icon name="add-circle-outline" class="text-sm"></ion-icon>
                <span>Dokumentasi Umum</span>
            </button>
        <?php endif; ?>
        <span class="text-xs font-bold px-2.5 py-1 rounded-lg bg-teal-50 text-teal-700 border border-teal-200 hidden sm:inline-flex items-center gap-1">
            <ion-icon name="images-outline"></ion-icon>
            <span>Total: <?= $totalFotoAll ?> Foto</span>
        </span>
    </div>
</div>

<!-- 1. Langsung Tabel Dokumentasi (Full Width, No Horizontal Scroll) -->
<div class="space-y-3">
    <?php if ($role === 'admin' || $role === 'user'): ?>
        <label class="flex items-center justify-between gap-3 p-3 rounded-xl border <?= $isDokSelesai ? 'bg-emerald-50/70 border-emerald-200' : 'bg-slate-50/70 border-slate-200' ?> cursor-pointer select-none transition-colors">
            <div class="flex items-center gap-2.5">
                <input type="checkbox" id="chkDokSelesai100" <?= $isDokSelesai ? 'checked' : '' ?>
                       onchange="window.toggleDokSelesai(this)"
                       class="w-4 h-4 rounded text-emerald-600 focus:ring-0 cursor-pointer shrink-0">
                <div>
                    <span class="text-xs font-bold <?= $isDokSelesai ? 'text-emerald-800' : 'text-slate-800' ?> block">Nyatakan Dokumentasi Selesai (100%)</span>
                    <span class="text-[10px] <?= $isDokSelesai ? 'text-emerald-700' : 'text-slate-500' ?> block">Centang jika dokumentasi umum sudah mewakili seluruh item meskipun foto tiap lokasi belum diisi.</span>
                </div>
            </div>
            <?php if ($isDokSelesai): ?>
                <span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-bold shrink-0">100% Selesai</span>
            <?php endif; ?>
        </label>
    <?php endif; ?>
    <?php if (!empty($generalDocs)): ?>
        <div class="bg-teal-50/50 rounded-xl border border-teal-200/80 p-3 space-y-2" id="generalDokStrip">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-teal-800 uppercase tracking-wider flex items-center gap-1.5">
                    <ion-icon name="albums-outline" class="text-sm text-teal-600"></ion-icon>
                    <span>Dokumentasi Umum / Per Komponen (<?= count($generalDocs) ?> Foto)</span>
                </span>
                <span class="text-[10px] text-teal-700">Mewakili item distribusi yang belum memiliki foto khusus</span>
            </div>
            <div class="flex flex-wrap gap-2">
                <?php
                $genUrls = array_map(fn($g) => APP_URL . '/' . ltrim($g['file_path'], '/'), $generalDocs);
                $genUrlsJson = htmlspecialchars(json_encode($genUrls), ENT_QUOTES, 'UTF-8');
                foreach ($generalDocs as $gIdx => $gd):
                    $gThumb = !empty($gd['thumb_path']) ? (APP_URL . '/' . ltrim($gd['thumb_path'], '/')) : (APP_URL . '/' . ltrim($gd['file_path'], '/'));
                    $gLabel = !empty($gd['nama_komponen']) ? $gd['nama_komponen'] : 'Semua Komponen';
                ?>
                    <div class="relative group bg-white rounded-lg border border-teal-200/80 p-1.5 flex items-center gap-2 shadow-2xs" id="gen_dok_card_<?= (int)$gd['id'] ?>">
                        <img src="<?= htmlspecialchars($gThumb) ?>" alt="Foto Umum"
                             onclick="openProPhotoViewer(<?= $genUrlsJson ?>, <?= $gIdx ?>, 'Dokumentasi Umum')"
                             class="w-10 h-10 rounded-md object-cover cursor-pointer hover:opacity-90 shrink-0">
                        <div class="min-w-0 pr-5">
                            <div class="text-[10px] font-bold text-teal-800 truncate max-w-[130px]" title="<?= htmlspecialchars($gLabel) ?>"><?= htmlspecialchars($gLabel) ?></div>
                            <div class="text-[9px] text-slate-400 truncate max-w-[130px]"><?= htmlspecialchars($gd['keterangan'] ?: ($gd['nama_file_asli'] ?? 'Foto')) ?></div>
                        </div>
                        <?php if ($role === 'admin' || $role === 'user'): ?>
                            <button type="button" onclick="deleteGeneralDokPhoto(<?= (int)$gd['id'] ?>)"
                                    class="absolute top-1 right-1 p-1 text-slate-400 hover:text-rose-600 rounded cursor-pointer" title="Hapus foto umum">
                                <ion-icon name="trash-outline" class="text-xs"></ion-icon>
                            </button>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
    <div class="bg-white rounded-xl border border-slate-200/90 overflow-hidden shadow-2xs">
        <table class="w-full text-left text-xs table-fixed">
            <thead class="bg-slate-50 text-[11px] font-bold text-slate-500 uppercase tracking-wider border-b border-slate-200/80">
                <tr>
                    <th class="py-2.5 px-3 w-10 text-center">NO</th>
                    <th class="py-2.5 px-3 w-40">KOMPONEN BARANG</th>
                    <th class="py-2.5 px-3">TUJUAN PENERIMA</th>
                    <th class="py-2.5 px-2 text-center w-20">QTY</th>
                    <th class="py-2.5 px-2 text-center w-32">STATUS</th>
                    <th class="py-2.5 px-3 w-36">FOTO DOKUMENTASI</th>
                    <?php if ($role === 'admin' || $role === 'user'): ?>
                        <th class="py-2.5 px-3 text-center w-28">AKSI</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php if (empty($distribusiList)): ?>
                    <tr>
                        <td colspan="7" class="py-8 text-center text-slate-400 text-xs">
                            <div class="w-10 h-10 bg-slate-50 rounded-2xl flex items-center justify-center mx-auto mb-2 text-xl text-slate-300">
                                <ion-icon name="camera-outline"></ion-icon>
                            </div>
                            <h4 class="text-xs font-bold text-slate-700 mb-0.5">Belum Ada Alokasi Barang</h4>
                            <p class="text-[11px] text-slate-400">Tambahkan alokasi distribusi terlebih dahulu pada modul Distribusi.</p>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php $no = 1; foreach ($distribusiList as $dist): ?>
                        <?php 
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

                        $namaKomp = $dist['nama_komponen'] ?? $dist['nama_barang'] ?? '-';
                        $qtyText = (float)$dist['jumlah'] . ' ' . ($dist['satuan'] ?: 'Unit');
                        ?>
                        <tr class="hover:bg-slate-50/70 transition-colors" id="row_dok_<?= $dist['id'] ?>">
                            <td class="py-2.5 px-3 text-center text-slate-400 font-mono text-[11px]"><?= $no++ ?></td>
                            <td class="py-2.5 px-3 text-xs font-bold text-slate-800 break-words">
                                <?= htmlspecialchars($namaKomp) ?>
                            </td>
                            <td class="py-2.5 px-3 text-xs text-slate-700 font-normal">
                                <div class="truncate" title="<?= htmlspecialchars($dist['nama_tujuan'] ?? '-') ?>">
                                    <?= htmlspecialchars($dist['nama_tujuan'] ?? '-') ?>
                                </div>
                            </td>
                            <td class="py-2.5 px-2 text-center whitespace-nowrap font-bold text-slate-800 text-xs">
                                <?= htmlspecialchars($qtyText) ?>
                            </td>
                            <td class="py-2.5 px-2 text-center whitespace-nowrap">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] border <?= $badgeClass ?>">
                                    <?= htmlspecialchars($statusLabel) ?>
                                </span>
                            </td>
                            <td class="py-2.5 px-3 whitespace-nowrap" id="cell_dok_photos_<?= $dist['id'] ?>">
                                <?php
                                $itemRepDocs = $itemDocMap[(int)($dist['item_id'] ?? 0)] ?? [];
                                $repDocs = !empty($itemRepDocs) ? $itemRepDocs : $globalDocs;
                                $repLabel = !empty($itemRepDocs) ? 'Diwakili Komponen' : 'Diwakili Umum';
                                ?>
                                <?php if ($photoCount === 0 && !empty($repDocs)): ?>
                                    <?php
                                    $repUrls = array_map(fn($ph) => APP_URL . '/' . ltrim($ph['file_path'], '/'), $repDocs);
                                    $repJson = htmlspecialchars(json_encode($repUrls), ENT_QUOTES, 'UTF-8');
                                    $repCap = htmlspecialchars(addslashes($repLabel . ' - ' . $namaKomp));
                                    ?>
                                    <button type="button"
                                            onclick="openProPhotoViewer(<?= $repJson ?>, 0, '<?= $repCap ?>')"
                                            class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-semibold bg-teal-50/70 hover:bg-teal-100 text-teal-700 border border-teal-200/80 cursor-pointer"
                                            title="Klik untuk melihat foto perwakilan (<?= count($repDocs) ?> foto)">
                                        <ion-icon name="albums-outline" class="text-xs"></ion-icon>
                                        <span><?= $repLabel ?></span>
                                    </button>
                                <?php elseif ($photoCount === 0): ?>
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-medium bg-slate-100 text-slate-400 border border-slate-200/70">
                                        <ion-icon name="image-outline" class="text-xs"></ion-icon>
                                        <span>Belum Ada</span>
                                    </span>
                                <?php else: ?>
                                    <div class="flex items-center gap-1.5">
                                        <?php
                                        $photoUrls = array_map(fn($ph) => APP_URL . '/' . ltrim($ph['file_path'], '/'), $photos);
                                        $photosJson = htmlspecialchars(json_encode($photoUrls), ENT_QUOTES, 'UTF-8');
                                        $cap = htmlspecialchars(addslashes(($dist['nama_tujuan'] ?? '') . ' - ' . $namaKomp));
                                        ?>
                                        <div class="flex items-center -space-x-1.5">
                                            <?php foreach (array_slice($photos, 0, 3) as $pIdx => $p): ?>
                                                <?php 
                                                $thumb = !empty($p['thumb_path']) ? (APP_URL . '/' . ltrim($p['thumb_path'], '/')) : (APP_URL . '/' . ltrim($p['file_path'], '/'));
                                                ?>
                                                <img src="<?= htmlspecialchars($thumb) ?>" 
                                                     alt="Foto" 
                                                     onclick="openProPhotoViewer(<?= $photosJson ?>, <?= $pIdx ?>, '<?= $cap ?>')"
                                                     class="w-7 h-7 rounded-lg object-cover border-2 border-white shadow-2xs hover:scale-110 hover:z-10 transition-transform cursor-pointer"
                                                     title="Klik untuk memperbesar">
                                            <?php endforeach; ?>
                                        </div>
                                        <button type="button"
                                                onclick="openProPhotoViewer(<?= $photosJson ?>, 0, '<?= $cap ?>')"
                                                class="inline-flex items-center px-1.5 py-0.5 rounded text-[11px] font-bold bg-teal-50 hover:bg-teal-100 text-teal-700 border border-teal-200/80 cursor-pointer">
                                            <?= $photoCount ?> Foto
                                        </button>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <?php if ($role === 'admin' || $role === 'user'): ?>
                                <td class="py-2.5 px-3 text-center whitespace-nowrap">
                                    <button type="button" 
                                            onclick="openModalDokumentasi(<?= (int)$dist['id'] ?>, '<?= htmlspecialchars(addslashes($dist['nama_tujuan'] ?? '-')) ?>', '<?= htmlspecialchars(addslashes($namaKomp)) ?>', '<?= htmlspecialchars(addslashes($qtyText)) ?>')"
                                            class="inline-flex items-center gap-1 px-2.5 py-1 bg-teal-50 hover:bg-teal-100 text-teal-700 border border-teal-200 rounded-lg text-[11px] font-semibold transition-all shadow-2xs cursor-pointer">
                                        <ion-icon name="cloud-upload-outline" class="text-xs"></ion-icon>
                                        <span><?= $photoCount > 0 ? 'Kelola' : 'Upload' ?></span>
                                    </button>
                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
            <!-- 2. Ringkasan Info Menyatu di Kolom Bawah Tabel (tfoot) -->
            <tfoot class="bg-slate-50/90 font-bold text-slate-700 border-t-2 border-slate-200">
                <tr>
                    <td class="py-2.5 px-3 text-center text-slate-400 font-mono text-[11px]">-</td>
                    <td colspan="2" class="py-2.5 px-3 text-xs text-slate-800">
                        <span class="font-normal text-slate-500">Total Alokasi:</span> <?= $totalDist ?> Titik Pengiriman
                    </td>
                    <td class="py-2.5 px-2 text-center text-xs font-extrabold text-slate-900 whitespace-nowrap">
                        <?= number_format($totalQty, 0, ',', '.') ?> Unit
                    </td>
                    <td class="py-2.5 px-2 text-center text-xs whitespace-nowrap">
                        <span class="text-[11px] font-bold text-teal-700"><?= $itemsWithDocs ?>/<?= $totalDist ?> Ada Foto</span>
                    </td>
                    <td class="py-2.5 px-3 text-xs text-teal-800 whitespace-nowrap">
                        <?= $totalFotoAll ?> Berkas Foto
                    </td>
                    <?php if ($role === 'admin' || $role === 'user'): ?>
                        <td class="py-2.5 px-3"></td>
                    <?php endif; ?>
                </tr>
            </tfoot>
        </table>
    </div>

    <!-- 5. Informasi Tambahan di Bawah Tabel -->
    <div class="flex items-center justify-between text-[11px] text-slate-500 px-1">
        <div class="flex items-center gap-1.5">
            <ion-icon name="information-circle-outline" class="text-teal-500 text-sm"></ion-icon>
            <span>Klik tombol <strong>"Upload"</strong> atau <strong>"Kelola"</strong> di kolom Aksi untuk mengunggah atau melihat galeri foto per titik tujuan.</span>
        </div>
        <span class="font-medium text-slate-600">Realisasi Dokumentasi: <strong><?= $itemsWithDocs ?></strong> dari <strong><?= $totalDist ?></strong> lokasi (<?= $pctDok ?>%)</span>
    </div>
</div>

<!-- ============================================================== -->
<!-- SUB-MODAL: UPLOAD & GALERI DOKUMENTASI PER TUJUAN              -->
<!-- ============================================================== -->
<div id="modalDokumentasi" class="app-modal fixed inset-0 z-[70] bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 transition-all duration-200 opacity-0 pointer-events-none hidden">
    <div class="modal-card bg-white rounded-2xl shadow-2xl border border-slate-100 max-w-2xl w-full transform scale-95 opacity-0 transition-all duration-200 overflow-hidden max-h-[92vh] flex flex-col">
        <!-- Header -->
        <div class="px-5 py-3.5 bg-slate-50 border-b border-slate-100 flex items-center justify-between shrink-0">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-teal-600 text-white flex items-center justify-center text-lg shadow-xs">
                    <ion-icon name="camera-outline"></ion-icon>
                </div>
                <div>
                    <h3 class="font-bold text-slate-800 text-sm flex items-center gap-1.5">
                        <span>Dokumentasi Foto</span>
                        <span class="text-slate-400 font-normal">|</span>
                        <span id="modalDokTujuan" class="text-teal-700 font-semibold">Nama Tujuan</span>
                    </h3>
                    <p class="text-[11px] text-slate-500 mt-0.5" id="modalDokBarang">Barang: -</p>
                </div>
            </div>
            <button type="button" onclick="window.ProModal.close('modalDokumentasi')" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-lg hover:bg-slate-100 transition-colors cursor-pointer">
                <ion-icon name="close-outline" class="text-xl"></ion-icon>
            </button>
        </div>

        <div class="overflow-y-auto p-5 space-y-5">
            <!-- SECTION 1: UPLOAD FOTO BARU -->
            <div class="bg-slate-50/70 p-4 rounded-xl border border-slate-200/90 space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-700 uppercase tracking-wider flex items-center gap-1.5">
                        <ion-icon name="cloud-upload-outline" class="text-teal-600 text-sm"></ion-icon>
                        <span>Upload Foto Baru (Bisa Lebih Dari 1)</span>
                    </span>
                    <span class="text-[10px] text-slate-400">JPG, PNG, WebP (Otomatis Di-compress)</span>
                </div>

                <!-- Dropzone / File Picker Area -->
                <div id="dokDropzone" 
                     onclick="document.getElementById('dokFileInput').click()"
                     ondragover="event.preventDefault(); this.classList.add('border-teal-500', 'bg-teal-50/50')"
                     ondragleave="this.classList.remove('border-teal-500', 'bg-teal-50/50')"
                     ondrop="window.handleDokDrop(event)"
                     class="border-2 border-dashed border-slate-300 hover:border-teal-500 hover:bg-teal-50/30 rounded-xl p-4 text-center cursor-pointer transition-all">
                    <input type="file" id="dokFileInput" multiple accept="image/jpeg,image/png,image/webp,image/jpg,.heic,.heif,image/heic,image/heif" class="hidden" onchange="window.handleDokFileSelect(this.files)">
                    <div class="w-10 h-10 bg-teal-50 rounded-full flex items-center justify-center text-teal-600 text-xl mx-auto mb-2 shadow-2xs">
                        <ion-icon name="images-outline"></ion-icon>
                    </div>
                    <p class="text-xs font-semibold text-slate-700">
                        Klik untuk memilih foto atau seret foto ke sini
                    </p>
                    <p class="text-[11px] text-slate-400 mt-0.5">
                        Bisa pilih banyak foto sekaligus dari HP atau Komputer
                    </p>
                </div>

                <!-- Queue Selected Files Preview -->
                <div id="dokSelectedQueue" class="hidden space-y-2">
                    <div class="flex items-center justify-between text-xs text-slate-600 font-semibold">
                        <span>Foto yang akan diupload (<span id="dokSelectedCount">0</span> foto):</span>
                        <button type="button" onclick="window.clearDokSelectedQueue()" class="text-rose-500 hover:underline text-[11px] cursor-pointer">Batal Semua</button>
                    </div>
                    <div id="dokQueueList" class="grid grid-cols-2 sm:grid-cols-4 gap-2 max-h-40 overflow-y-auto p-1 bg-white rounded-lg border border-slate-200"></div>
                </div>

                <!-- Keterangan Opsional -->
                <div>
                    <input type="text" id="dokKeteranganInput" placeholder="Keterangan / Catatan foto dokumentasi (opsional)..."
                           class="w-full px-3 py-2 bg-white border border-slate-200 rounded-xl text-xs focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 transition-all">
                </div>

                <!-- Progress Bar Container -->
                <div id="dokProgressContainer" class="hidden space-y-1.5 pt-1">
                    <div class="flex items-center justify-between text-xs">
                        <span id="dokProgressStatus" class="font-semibold text-teal-700 flex items-center gap-1.5">
                            <span id="dokProgressStatusText">Mengompres & Mengunggah...</span>
                        </span>
                        <span id="dokProgressPercent" class="font-mono font-bold text-teal-800 text-xs">0%</span>
                    </div>
                    <div class="w-full bg-slate-200 rounded-full h-2.5 overflow-hidden">
                        <div id="dokProgressBar" class="bg-teal-600 h-2.5 rounded-full transition-all duration-150" style="width: 0%"></div>
                    </div>
                </div>

                <!-- Action Button -->
                <div class="flex justify-end pt-1">
                    <button type="button" id="btnUploadDokumentasi" onclick="window.startUploadDokumentasi()" disabled
                            class="px-4 py-2 bg-slate-200 text-slate-400 font-semibold rounded-xl text-xs shadow-xs transition-all cursor-not-allowed flex items-center gap-1.5">
                        <ion-icon name="cloud-upload-outline" class="text-sm"></ion-icon>
                        <span id="btnUploadDokumentasiText">Mulai Upload Foto</span>
                    </button>
                </div>
            </div>

            <!-- SECTION 2: GALERI FOTO TERSIMPAN -->
            <div class="space-y-3">
                <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                    <h4 class="text-xs font-bold text-slate-800 uppercase tracking-wider flex items-center gap-1.5">
                        <ion-icon name="images-outline" class="text-teal-600 text-sm"></ion-icon>
                        <span>Galeri Foto Tersimpan (<span id="dokGalleryCount">0</span>)</span>
                    </h4>
                    <span class="text-[11px] text-slate-400">Klik foto untuk memperbesar</span>
                </div>

                <div id="dokGalleryLoading" class="py-6 text-center text-slate-400 text-xs">
                    <span>Memuat galeri foto...</span>
                </div>

                <div id="dokGalleryEmpty" class="hidden py-6 text-center text-slate-400 bg-slate-50/50 rounded-xl border border-dashed border-slate-200">
                    <ion-icon name="image-outline" class="text-2xl text-slate-300 mb-1"></ion-icon>
                    <p class="text-xs font-medium text-slate-600">Belum ada foto dokumentasi tersimpan</p>
                    <p class="text-[11px] text-slate-400">Gunakan form di atas untuk mengupload foto barang atau serah terima.</p>
                </div>

                <div id="dokGalleryGrid" class="hidden grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3"></div>
            </div>
        </div>

        <!-- Footer -->
        <div class="px-5 py-3 bg-slate-50 border-t border-slate-100 flex items-center justify-end shrink-0">
            <button type="button" onclick="window.ProModal.close('modalDokumentasi')"
                    class="px-4 py-2 bg-white border border-slate-200 text-slate-700 hover:bg-slate-100 rounded-xl text-xs font-semibold transition-colors cursor-pointer">
                Tutup
            </button>
        </div>
    </div>
</div>

<script>
// Teleport sub-modal ke document.body agar tidak terpotong oleh transform/overflow parent modal
(function() {
    document.querySelectorAll('body > #modalDokumentasi').forEach(function(oldEl) { oldEl.remove(); });
    var el = document.getElementById('modalDokumentasi');
    if (el) document.body.appendChild(el);
})();

window.currentDokDistId = null;
window.dokSelectedFiles = [];
window.isUploadingDok = false;

window.openModalDokumentasi = function(distId, namaTujuan, namaBarang, qty) {
    window.currentDokDistId = distId;

    const elTujuan = document.getElementById('modalDokTujuan');
    const elBarang = document.getElementById('modalDokBarang');
    if (elTujuan) elTujuan.textContent = namaTujuan;
    if (elBarang) elBarang.textContent = 'Barang: ' + namaBarang + ' (' + qty + ')';

    window.clearDokSelectedQueue();
    const ketInput = document.getElementById('dokKeteranganInput');
    if (ketInput) ketInput.value = '';

    window.ProModal.open('modalDokumentasi');
    window.loadDokumentasiPhotos(distId);
};

window.loadDokumentasiPhotos = function(distId) {
    const loadingEl = document.getElementById('dokGalleryLoading');
    const emptyEl = document.getElementById('dokGalleryEmpty');
    const gridEl = document.getElementById('dokGalleryGrid');
    const countEl = document.getElementById('dokGalleryCount');

    if (loadingEl) loadingEl.classList.remove('hidden');
    if (emptyEl) emptyEl.classList.add('hidden');
    if (gridEl) {
        gridEl.classList.add('hidden');
        gridEl.innerHTML = '';
    }

    fetch('<?= APP_URL ?>/paket/getDokumentasiList/<?= $paketKode ?>/' + distId, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(r => r.json())
    .then(res => {
        if (loadingEl) loadingEl.classList.add('hidden');
        if (res.success) {
            const photos = res.photos || [];
            if (countEl) countEl.textContent = photos.length;

            if (photos.length === 0) {
                if (emptyEl) emptyEl.classList.remove('hidden');
            } else if (gridEl) {
                gridEl.classList.remove('hidden');
                photos.forEach(p => {
                    const card = document.createElement('div');
                    card.className = 'group relative bg-slate-50 rounded-xl border border-slate-200 overflow-hidden shadow-2xs hover:shadow-md transition-all flex flex-col';
                    card.id = 'photo_card_' + p.id;

                    const imgUrl = p.url;
                    const thumbUrl = p.thumb_url || p.url;
                    const caption = (p.keterangan ? (p.keterangan + ' - ') : '') + (p.formatted_date || '');

                    card.innerHTML = `
                        <div class="relative aspect-square w-full overflow-hidden bg-slate-100 cursor-pointer" onclick="openPhotoPreview('${imgUrl}', '${escapeDokHtml(caption)}')">
                            <img src="${thumbUrl}" alt="Dokumentasi" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-200" loading="lazy">
                            <div class="absolute inset-0 bg-black/30 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center text-white text-lg">
                                <ion-icon name="search-outline"></ion-icon>
                            </div>
                        </div>
                        <div class="p-2 flex items-center justify-between gap-1 text-[10px] text-slate-500 bg-white border-t border-slate-100">
                            <div class="truncate">
                                <span class="font-semibold text-slate-700 block truncate" title="${escapeDokHtml(p.nama_file_asli)}">${escapeDokHtml(p.nama_file_asli)}</span>
                                <span class="text-[9px] text-slate-400 block">${p.formatted_size} &bull; ${p.formatted_date}</span>
                            </div>
                            <button type="button" onclick="window.deleteDokumentasiFoto(${p.id}, ${distId})" class="text-slate-400 hover:text-rose-600 p-1 rounded-md hover:bg-rose-50 transition-colors shrink-0 cursor-pointer" title="Hapus Foto">
                                <ion-icon name="trash-outline" class="text-sm"></ion-icon>
                            </button>
                        </div>
                    `;
                    gridEl.appendChild(card);
                });
            }
        } else {
            if (emptyEl) emptyEl.classList.remove('hidden');
            if (window.ProToast) window.ProToast.error(res.message || 'Gagal memuat galeri foto.');
        }
    })
    .catch(() => {
        if (loadingEl) loadingEl.classList.add('hidden');
        if (emptyEl) emptyEl.classList.remove('hidden');
    });
};

window.handleDokDrop = function(e) {
    e.preventDefault();
    const dropzone = document.getElementById('dokDropzone');
    if (dropzone) dropzone.classList.remove('border-teal-500', 'bg-teal-50/50');
    if (e.dataTransfer && e.dataTransfer.files) {
        window.handleDokFileSelect(e.dataTransfer.files);
    }
};

window.handleDokFileSelect = function(files) {
    if (!files || files.length === 0) return;
    for (let i = 0; i < files.length; i++) {
        const f = files[i];
        const isHeic = /\.(heic|heif)$/i.test(f.name || '');
        if ((f.type && f.type.startsWith('image/')) || isHeic) {
            window.dokSelectedFiles.push(f);
        } else if (window.ProToast) {
            window.ProToast.warning('File ' + f.name + ' bukan gambar yang didukung.');
        }
    }
    window.renderDokQueuePreview();
};

window.renderDokQueuePreview = function() {
    const queueContainer = document.getElementById('dokSelectedQueue');
    const queueList = document.getElementById('dokQueueList');
    const countEl = document.getElementById('dokSelectedCount');
    const uploadBtn = document.getElementById('btnUploadDokumentasi');

    if (!queueContainer || !queueList) return;

    if (window.dokSelectedFiles.length === 0) {
        queueContainer.classList.add('hidden');
        queueList.innerHTML = '';
        if (uploadBtn) {
            uploadBtn.disabled = true;
            uploadBtn.className = 'px-4 py-2 bg-slate-200 text-slate-400 font-semibold rounded-xl text-xs shadow-xs transition-all cursor-not-allowed flex items-center gap-1.5';
        }
        return;
    }

    queueContainer.classList.remove('hidden');
    if (countEl) countEl.textContent = window.dokSelectedFiles.length;
    queueList.innerHTML = '';

    window.dokSelectedFiles.forEach((file, index) => {
        const item = document.createElement('div');
        item.className = 'relative flex items-center gap-2 p-1.5 bg-slate-50 border border-slate-200 rounded-lg overflow-hidden group';

        const img = document.createElement('img');
        img.className = 'w-10 h-10 rounded-md object-cover border border-slate-200 shrink-0';
        img.src = URL.createObjectURL(file);

        const info = document.createElement('div');
        info.className = 'truncate text-[10px] leading-tight flex-1';
        const sizeKb = Math.round(file.size / 1024);
        info.innerHTML = `<span class="font-bold text-slate-700 block truncate">${escapeDokHtml(file.name)}</span><span class="text-slate-400 font-mono">${sizeKb} KB</span>`;

        const delBtn = document.createElement('button');
        delBtn.type = 'button';
        delBtn.className = 'text-slate-400 hover:text-rose-600 p-1 rounded transition-colors cursor-pointer';
        delBtn.title = 'Hapus dari antrean';
        delBtn.innerHTML = '<ion-icon name="close-circle" class="text-base"></ion-icon>';
        delBtn.onclick = () => window.removeDokQueueItem(index);

        item.appendChild(img);
        item.appendChild(info);
        item.appendChild(delBtn);
        queueList.appendChild(item);
    });

    if (uploadBtn) {
        uploadBtn.disabled = false;
        uploadBtn.className = 'px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white font-semibold rounded-xl text-xs shadow-xs transition-all cursor-pointer flex items-center gap-1.5 hover:scale-[1.02]';
    }
};

window.removeDokQueueItem = function(index) {
    if (index >= 0 && index < window.dokSelectedFiles.length) {
        window.dokSelectedFiles.splice(index, 1);
        window.renderDokQueuePreview();
    }
};

window.clearDokSelectedQueue = function() {
    window.dokSelectedFiles = [];
    const input = document.getElementById('dokFileInput');
    if (input) input.value = '';
    window.renderDokQueuePreview();

    const progressContainer = document.getElementById('dokProgressContainer');
    if (progressContainer) progressContainer.classList.add('hidden');
};

window.ensureHeicConverter = function() {
    if (window.HeicTo && typeof window.HeicTo.heicTo === 'function') {
        return Promise.resolve({ type: 'heic-to', fn: window.HeicTo.heicTo });
    }
    if (typeof window.heicTo === 'function') {
        return Promise.resolve({ type: 'heic-to', fn: window.heicTo });
    }
    const loadScript = (src) => new Promise((resolve, reject) => {
        const s = document.createElement('script');
        s.src = src;
        s.onload = () => resolve(true);
        s.onerror = () => reject(new Error('Gagal memuat ' + src));
        document.head.appendChild(s);
    });
    return loadScript('https://cdn.jsdelivr.net/npm/heic-to@1.6.7/dist/iife/heic-to.js')
        .catch(() => loadScript('https://unpkg.com/heic-to@1.6.7/dist/iife/heic-to.js'))
        .then(() => {
            if (window.HeicTo && typeof window.HeicTo.heicTo === 'function') {
                return { type: 'heic-to', fn: window.HeicTo.heicTo };
            }
            if (typeof window.heicTo === 'function') {
                return { type: 'heic-to', fn: window.heicTo };
            }
            throw new Error('HeicTo global unavailable');
        })
        .catch(() => loadScript('https://cdn.jsdelivr.net/npm/heic2any@0.0.4/dist/heic2any.min.js')
            .then(() => ({ type: 'heic2any', fn: window.heic2any })));
};

window.compressImageToBlob = async function(file, maxWidth = 1600, quality = 0.82) {
    let sourceFile = file;
    const isHeic = /\.(heic|heif)$/i.test(file.name) || /image\/(heic|heif)/i.test(file.type || '');
    if (isHeic) {
        try {
            const conv = await window.ensureHeicConverter();
            let blob;
            if (conv.type === 'heic-to') {
                blob = await conv.fn({ blob: file, type: 'image/jpeg', quality: 0.9 });
            } else {
                const res = await conv.fn({ blob: file, toType: 'image/jpeg', quality: 0.9 });
                blob = Array.isArray(res) ? res[0] : res;
            }
            const jpgName = file.name.replace(/\.(heic|heif)$/i, '.jpg');
            sourceFile = new File([blob], jpgName, { type: 'image/jpeg' });
        } catch (err) {
            console.warn('HEIC client convert failed, fallback to server:', err);
        }
    }

    return new Promise((resolve) => {
        if (!sourceFile.type.match(/image\/(jpeg|jpg|png|webp)/i)) {
            return resolve(sourceFile);
        }
        const reader = new FileReader();
        reader.onload = function(e) {
            const img = new Image();
            img.onload = function() {
                let w = img.width;
                let h = img.height;
                if (w > maxWidth || h > maxWidth) {
                    if (w > h) {
                        h = Math.round((h * maxWidth) / w);
                        w = maxWidth;
                    } else {
                        w = Math.round((w * maxWidth) / h);
                        h = maxWidth;
                    }
                }
                const canvas = document.createElement('canvas');
                canvas.width = w;
                canvas.height = h;
                const ctx = canvas.getContext('2d');
                ctx.drawImage(img, 0, 0, w, h);
                canvas.toBlob((blob) => {
                    if (blob && blob.size < sourceFile.size) {
                        resolve(new File([blob], sourceFile.name.replace(/\.[^/.]+$/, "") + ".jpg", {
                            type: 'image/jpeg',
                            lastModified: Date.now()
                        }));
                    } else {
                        resolve(sourceFile);
                    }
                }, 'image/jpeg', quality);
            };
            img.onerror = () => resolve(sourceFile);
            img.src = e.target.result;
        };
        reader.onerror = () => resolve(sourceFile);
        reader.readAsDataURL(sourceFile);
    });
};

window.startUploadDokumentasi = async function() {
    if (window.dokSelectedFiles.length === 0 || !window.currentDokDistId || window.isUploadingDok) return;
    window.isUploadingDok = true;

    const progressContainer = document.getElementById('dokProgressContainer');
    const progressBar = document.getElementById('dokProgressBar');
    const progressPercent = document.getElementById('dokProgressPercent');
    const progressStatus = document.getElementById('dokProgressStatusText');
    const uploadBtn = document.getElementById('btnUploadDokumentasi');
    const uploadBtnText = document.getElementById('btnUploadDokumentasiText');

    if (progressContainer) progressContainer.classList.remove('hidden');
    if (uploadBtn) {
        uploadBtn.disabled = true;
        uploadBtn.classList.add('opacity-75');
    }
    if (uploadBtnText) uploadBtnText.textContent = 'Memproses...';

    if (progressStatus) progressStatus.textContent = 'Mengompres gambar (' + window.dokSelectedFiles.length + ' foto)...';
    if (progressBar) progressBar.style.width = '15%';
    if (progressPercent) progressPercent.textContent = '15%';

    const formData = new FormData();
    formData.append('is_ajax', '1');
    formData.append('distribusi_id', window.currentDokDistId);
    const ketVal = (document.getElementById('dokKeteranganInput')?.value || '').trim();
    if (ketVal) formData.append('keterangan', ketVal);

    for (let i = 0; i < window.dokSelectedFiles.length; i++) {
        const comp = await window.compressImageToBlob(window.dokSelectedFiles[i], 1600, 0.82);
        formData.append('fotos[]', comp, comp.name || window.dokSelectedFiles[i].name);
    }

    if (progressStatus) progressStatus.textContent = 'Mengunggah ke server...';
    if (progressBar) progressBar.style.width = '30%';
    if (progressPercent) progressPercent.textContent = '30%';

    const xhr = new XMLHttpRequest();
    xhr.open('POST', '<?= APP_URL ?>/paket/uploadDokumentasi/<?= $paketKode ?>', true);
    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

    xhr.upload.onprogress = function(e) {
        if (e.lengthComputable) {
            const pct = 30 + Math.round((e.loaded / e.total) * 65);
            if (progressBar) progressBar.style.width = pct + '%';
            if (progressPercent) progressPercent.textContent = pct + '%';
        }
    };

    xhr.onload = function() {
        window.isUploadingDok = false;
        if (progressBar) progressBar.style.width = '100%';
        if (progressPercent) progressPercent.textContent = '100%';
        if (progressStatus) progressStatus.textContent = 'Selesai!';

        setTimeout(() => {
            if (progressContainer) progressContainer.classList.add('hidden');
            if (uploadBtn) {
                uploadBtn.disabled = false;
                uploadBtn.classList.remove('opacity-75');
            }
            if (uploadBtnText) uploadBtnText.textContent = 'Mulai Upload Foto';
        }, 600);

        try {
            const res = JSON.parse(xhr.responseText);
            if (res.success) {
                window.clearDokSelectedQueue();
                const ketInput = document.getElementById('dokKeteranganInput');
                if (ketInput) ketInput.value = '';
                window.loadDokumentasiPhotos(window.currentDokDistId);
                if (window.ProToast) window.ProToast.success(res.message || 'Foto dokumentasi berhasil diupload!');
                if (typeof refreshCurrentNativeModal === 'function') {
                    // Refresh background table silently without closing sub-modal
                    fetch('<?= APP_URL ?>/paket/modalView/<?= $paketKode ?>/dokumentasi')
                        .then(r => r.text())
                        .then(html => {
                            const temp = document.createElement('div');
                            temp.innerHTML = html;
                            const newTable = temp.querySelector('table');
                            const curTable = document.querySelector('#modalModuleBody table');
                            if (newTable && curTable) curTable.innerHTML = newTable.innerHTML;
                            const newAction = temp.querySelector('#modalActionSource');
                            const actionContainer = document.getElementById('modalModuleHeaderAction');
                            if (newAction && actionContainer) actionContainer.innerHTML = newAction.innerHTML;
                        });
                }
            } else if (window.ProToast) {
                window.ProToast.error(res.message || 'Gagal mengupload foto.');
            }
        } catch (err) {
            if (window.ProToast) window.ProToast.error('Format respon server tidak valid.');
        }
    };

    xhr.onerror = function() {
        window.isUploadingDok = false;
        if (progressContainer) progressContainer.classList.add('hidden');
        if (uploadBtn) {
            uploadBtn.disabled = false;
            uploadBtn.classList.remove('opacity-75');
        }
        if (uploadBtnText) uploadBtnText.textContent = 'Mulai Upload Foto';
        if (window.ProToast) window.ProToast.error('Koneksi terputus saat mengupload foto.');
    };

    xhr.send(formData);
};

window.deleteDokumentasiFoto = function(fotoId, distId) {
    if (!confirm('Yakin ingin menghapus foto dokumentasi ini? File yang dihapus tidak dapat dikembalikan.')) {
        return;
    }

    const card = document.getElementById('photo_card_' + fotoId);
    if (card) card.style.opacity = '0.4';

    fetch('<?= APP_URL ?>/paket/deleteDokumentasi/<?= $paketKode ?>/' + fotoId, {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            if (card) card.remove();
            window.loadDokumentasiPhotos(distId);
            if (window.ProToast) window.ProToast.success('Foto dokumentasi berhasil dihapus.');
            fetch('<?= APP_URL ?>/paket/modalView/<?= $paketKode ?>/dokumentasi')
                .then(r => r.text())
                .then(html => {
                    const temp = document.createElement('div');
                    temp.innerHTML = html;
                    const newTable = temp.querySelector('table');
                    const curTable = document.querySelector('#modalModuleBody table');
                    if (newTable && curTable) curTable.innerHTML = newTable.innerHTML;
                    const newAction = temp.querySelector('#modalActionSource');
                    const actionContainer = document.getElementById('modalModuleHeaderAction');
                    if (newAction && actionContainer) actionContainer.innerHTML = newAction.innerHTML;
                });
        } else {
            if (card) card.style.opacity = '1';
            if (window.ProToast) window.ProToast.error(res.message || 'Gagal menghapus foto.');
        }
    })
    .catch(() => {
        if (card) card.style.opacity = '1';
        if (window.ProToast) window.ProToast.error('Koneksi terputus saat menghapus foto.');
    });
};

function escapeDokHtml(str) {
    if (!str) return '';
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}
window.escapeDokHtml = escapeDokHtml;
</script>

<!-- ============================================================== -->
<!-- SUB-MODAL: UPLOAD DOKUMENTASI UMUM / PER KOMPONEN              -->
<!-- ============================================================== -->
<div id="modalDokUmum" class="app-modal fixed inset-0 z-[75] bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 transition-all duration-200 opacity-0 pointer-events-none hidden">
    <div class="modal-card bg-white rounded-2xl shadow-2xl border border-slate-100 max-w-md w-full transform scale-95 opacity-0 transition-all duration-200 overflow-hidden flex flex-col">
        <div class="px-5 py-3.5 bg-teal-50/70 border-b border-teal-100 flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-teal-600 text-white flex items-center justify-center text-base shadow-xs">
                    <ion-icon name="albums-outline"></ion-icon>
                </div>
                <div>
                    <h3 class="font-bold text-slate-800 text-sm">Upload Dokumentasi Umum</h3>
                    <p class="text-[11px] text-slate-500">1 foto mewakili komponen paket tanpa input per lokasi</p>
                </div>
            </div>
            <button type="button" onclick="window.ProModal.close('modalDokUmum')" class="p-1 text-slate-400 hover:text-slate-700 rounded-lg cursor-pointer">
                <ion-icon name="close-outline" class="text-lg"></ion-icon>
            </button>
        </div>

        <form id="formDokUmum" onsubmit="submitDokUmum(event)" class="p-5 space-y-3.5">
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Komponen Paket yang Diwakili</label>
                <select id="dokUmumItemId" name="item_id" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-teal-500 focus:bg-white">
                    <option value="0">Semua Komponen (Umum Paket)</option>
                    <?php foreach (($items ?? []) as $it): ?>
                        <option value="<?= (int)$it['id'] ?>"><?= htmlspecialchars($it['komponen']) ?> (<?= (float)$it['jumlah'] ?> <?= htmlspecialchars($it['satuan'] ?: 'Unit') ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Pilih Foto Dokumentasi <span class="text-red-500">*</span></label>
                <input type="file" id="dokUmumFiles" accept="image/*,.heic,.heif,image/heic,image/heif" multiple required
                       class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-teal-600 file:text-white hover:file:bg-teal-700 cursor-pointer">
                <span class="text-[10px] text-slate-400 mt-1 block">Bisa pilih 1 atau beberapa foto sekaligus (otomatis dikompres).</span>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Keterangan (Opsional)</label>
                <input type="text" id="dokUmumKeterangan" placeholder="Misal: Foto barang siap kirim / serah terima..."
                       class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-teal-500 focus:bg-white">
            </div>

            <div class="pt-2 border-t border-slate-100 flex justify-end gap-2">
                <button type="button" onclick="window.ProModal.close('modalDokUmum')" class="px-3.5 py-1.5 rounded-lg border border-slate-200 text-slate-600 font-semibold text-xs cursor-pointer">Batal</button>
                <button type="submit" id="btnSubmitDokUmum" class="px-4 py-1.5 bg-teal-600 hover:bg-teal-700 text-white font-semibold rounded-lg text-xs shadow-xs cursor-pointer">Upload Foto</button>
            </div>
        </form>
    </div>
</div>

<script>
(function() {
    // Teleport sub-modals ke document.body agar backdrop fixed inset-0 penuh layar (tidak terpotong border modal parent)
    ['modalDokumentasi', 'modalDokUmum'].forEach(id => {
        const list = document.querySelectorAll('#' + id);
        if (list.length > 1) {
            for (let i = 0; i < list.length - 1; i++) list[i].remove();
        }
        const el = document.getElementById(id);
        if (el && el.parentElement !== document.body) {
            document.body.appendChild(el);
        }
    });
})();

window.openModalDokUmum = function() {
    const f = document.getElementById('formDokUmum');
    if (f) f.reset();
    window.ProModal.open('modalDokUmum');
};

window.submitDokUmum = async function(e) {
    e.preventDefault();
    const fileInput = document.getElementById('dokUmumFiles');
    if (!fileInput || !fileInput.files || fileInput.files.length === 0) return;

    const btn = document.getElementById('btnSubmitDokUmum');
    if (btn) { btn.disabled = true; btn.textContent = 'Mengunggah...'; }

    const formData = new FormData();
    formData.append('is_ajax', '1');
    formData.append('is_umum', '1');
    formData.append('item_id', document.getElementById('dokUmumItemId')?.value || '0');
    const ket = (document.getElementById('dokUmumKeterangan')?.value || '').trim();
    if (ket) formData.append('keterangan', ket);

    for (let i = 0; i < fileInput.files.length; i++) {
        const comp = await window.compressImageToBlob(fileInput.files[i], 1600, 0.82);
        formData.append('fotos[]', comp, comp.name || fileInput.files[i].name);
    }

    fetch('<?= APP_URL ?>/paket/uploadDokumentasi/<?= $paketKode ?>', {
        method: 'POST',
        body: formData,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(data => {
        if (btn) { btn.disabled = false; btn.textContent = 'Upload Foto'; }
        if (data.success) {
            window.ProModal.close('modalDokUmum');
            if (window.ProToast) window.ProToast.success(data.message || 'Dokumentasi umum berhasil diunggah!');
            if (typeof refreshCurrentNativeModal === 'function') {
                refreshCurrentNativeModal();
            } else {
                window.location.reload();
            }
        } else {
            if (window.ProToast) window.ProToast.error(data.message || 'Gagal mengunggah foto.');
        }
    })
    .catch(() => {
        if (btn) { btn.disabled = false; btn.textContent = 'Upload Foto'; }
        if (window.ProToast) window.ProToast.error('Gagal terhubung ke server.');
    });
};

window.toggleDokSelesai = function(cb) {
    const fd = new FormData();
    fd.append('is_dok_selesai', cb && cb.checked ? '1' : '0');
    fetch('<?= APP_URL ?>/paket/toggleDokSelesai/<?= $paketKode ?>', {
        method: 'POST',
        body: fd,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            if (window.ProToast) window.ProToast.success(data.message);
            if (typeof refreshCurrentNativeModal === 'function') {
                refreshCurrentNativeModal();
            } else {
                window.location.reload();
            }
        } else {
            if (cb) cb.checked = !cb.checked;
            if (window.ProToast) window.ProToast.error(data.message || 'Gagal memperbarui status.');
        }
    })
    .catch(() => {
        if (cb) cb.checked = !cb.checked;
        if (window.ProToast) window.ProToast.error('Koneksi terputus.');
    });
};

window.deleteGeneralDokPhoto = function(docId) {
    if (!confirm('Hapus foto dokumentasi umum ini?')) return;
    fetch('<?= APP_URL ?>/paket/deleteDokumentasi/<?= $paketKode ?>/' + docId, {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
    })
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            if (window.ProToast) window.ProToast.success('Foto dokumentasi umum berhasil dihapus.');
            if (typeof refreshCurrentNativeModal === 'function') {
                refreshCurrentNativeModal();
            } else {
                window.location.reload();
            }
        } else {
            if (window.ProToast) window.ProToast.error(res.message || 'Gagal menghapus foto.');
        }
    })
    .catch(() => {
        if (window.ProToast) window.ProToast.error('Koneksi terputus saat menghapus foto.');
    });
};
</script>
