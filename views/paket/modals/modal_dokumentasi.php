<?php
/**
 * Modal View: Foto Dokumentasi
 */
$role = $_SESSION['admin_role'] ?? 'user';
$allPhotos = [];
foreach ($dokumentasiMap as $distId => $photos) {
    foreach ($photos as $ph) {
        $allPhotos[] = $ph;
    }
}
?>

<div class="space-y-4">
    <!-- Top Summary Banner & Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white p-4 rounded-xl border border-slate-200/90 shadow-2xs">
        <div>
            <h4 class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                <ion-icon name="camera-outline" class="text-teal-600 text-base"></ion-icon>
                <span>Foto Dokumentasi Penyerahan Barang</span>
            </h4>
            <p class="text-[11px] text-slate-400 mt-0.5">Bukti foto barang telah diterima oleh PIC/pihak sekolah di lokasi.</p>
        </div>
        <div class="flex items-center gap-2 self-start sm:self-auto">
            <span class="text-xs font-bold px-3 py-1 rounded-lg bg-teal-50 text-teal-700 border border-teal-200">
                Total: <?= count($allPhotos) ?> Foto
            </span>
            <?php if ($role === 'admin' || $role === 'user'): ?>
                <button type="button" 
                        onclick="openNativeUploadFotoModal('<?= $paket['kode_paket'] ?: $paket['id'] ?>')"
                        class="px-3 py-1.5 bg-teal-600 hover:bg-teal-700 text-white rounded-xl text-xs font-semibold shadow-xs flex items-center gap-1.5 transition-all cursor-pointer">
                    <ion-icon name="cloud-upload-outline" class="text-sm"></ion-icon>
                    <span>Upload Foto</span>
                </button>
            <?php endif; ?>
        </div>
    </div>

    <!-- Gallery Grid -->
    <?php if (empty($allPhotos)): ?>
        <div class="bg-white rounded-xl border border-slate-200/90 p-10 text-center shadow-2xs">
            <div class="w-12 h-12 bg-slate-50 rounded-2xl flex items-center justify-center mx-auto mb-3 text-2xl text-slate-300">
                <ion-icon name="images-outline"></ion-icon>
            </div>
            <h4 class="text-xs font-bold text-slate-700 mb-0.5">Belum Ada Foto Dokumentasi</h4>
            <p class="text-[11px] text-slate-400 max-w-sm mx-auto mb-4">Upload foto bukti serah terima atau kondisi barang di sekolah penerima.</p>
            <?php if ($role === 'admin' || $role === 'user'): ?>
                <button type="button" 
                        onclick="openNativeUploadFotoModal('<?= $paket['kode_paket'] ?: $paket['id'] ?>')"
                        class="px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-xl text-xs font-semibold shadow-xs inline-flex items-center gap-1.5 transition-all cursor-pointer">
                    <ion-icon name="cloud-upload-outline" class="text-sm"></ion-icon>
                    <span>Upload Foto Sekarang</span>
                </button>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
            <?php foreach ($allPhotos as $ph): ?>
                <?php 
                $imgUrl = APP_URL . '/' . ltrim($ph['file_path'], '/');
                ?>
                <div class="group bg-white rounded-xl border border-slate-200/90 overflow-hidden shadow-2xs hover:shadow-md transition-all flex flex-col">
                    <div class="relative aspect-4/3 bg-slate-100 overflow-hidden cursor-pointer" onclick="openPhotoPreview('<?= $imgUrl ?>', '<?= htmlspecialchars(addslashes($ph['keterangan'] ?: ($ph['nama_tujuan'] ?? 'Foto Dokumentasi'))) ?>')">
                        <img src="<?= $imgUrl ?>" alt="Dokumentasi" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                        <div class="absolute inset-0 bg-black/30 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center text-white text-xl">
                            <ion-icon name="search-outline"></ion-icon>
                        </div>
                    </div>
                    <div class="p-2.5 flex-1 flex flex-col justify-between">
                        <div>
                            <div class="text-xs font-bold text-slate-800 truncate" title="<?= htmlspecialchars($ph['nama_tujuan'] ?? '') ?>">
                                <?= htmlspecialchars($ph['nama_tujuan'] ?: 'Dokumentasi') ?>
                            </div>
                            <?php if (!empty($ph['keterangan'])): ?>
                                <p class="text-[10px] text-slate-400 truncate mt-0.5" title="<?= htmlspecialchars($ph['keterangan']) ?>">
                                    <?= htmlspecialchars($ph['keterangan']) ?>
                                </p>
                            <?php endif; ?>
                        </div>
                        <?php if ($role === 'admin' || $role === 'user'): ?>
                            <div class="mt-2 pt-2 border-t border-slate-100 flex items-center justify-between text-[10px]">
                                <span class="text-slate-400"><?= !empty($ph['created_at']) ? date('d/m/Y', strtotime($ph['created_at'])) : '' ?></span>
                                <button type="button" 
                                        onclick="deleteFotoDokumenAjax('<?= $paket['kode_paket'] ?: $paket['id'] ?>', '<?= $ph['id'] ?>')"
                                        class="text-rose-500 hover:text-rose-700 font-semibold inline-flex items-center gap-0.5 cursor-pointer">
                                    <ion-icon name="trash-outline"></ion-icon>
                                    <span>Hapus</span>
                                </button>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
