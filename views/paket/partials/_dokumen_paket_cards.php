<?php
/**
 * Shared Partial: Dokumen Paket (Faktur Pajak, Surat Pesanan, BAST Paket)
 * Dipakai bersama di modal_info.php dan modal_dokumen.php sehingga
 * upload BAST Paket di kedua tempat merujuk ke data paket_dokumen yang sama.
 */
$paketKode = $paket['kode_paket'] ?: $paket['id'];
$role = $_SESSION['admin_role'] ?? '';
$canEditDokPaket = !empty($_SESSION['admin_logged_in']) && empty($isReadOnly) && empty($isPublicView) && ($role === 'admin' || $role === 'user');
$redirectTab = $dokumenPaketRedirectTab ?? 'info';

$tujuanKeys = array_keys($groupedDistribusi ?? []);
$isPerTujuanBast = function ($doc) use ($tujuanKeys) {
    $jenis = strtoupper(trim((string)($doc['jenis_dokumen'] ?? '')));
    if ($jenis !== '' && $jenis !== 'BAST') {
        return false;
    }
    foreach ($tujuanKeys as $tk) {
        if ($tk !== '' && (stripos((string)$doc['nama_dokumen'], $tk) !== false || stripos((string)$doc['nama_file_asli'], $tk) !== false)) {
            return true;
        }
    }
    return false;
};

$docsFakturPajak = [];
$docsSuratPesanan = [];
$docsBastPaket = [];

foreach (($dokumenList ?? []) as $doc) {
    $jenis = strtoupper(trim((string)($doc['jenis_dokumen'] ?? '')));
    if ($jenis === 'FAKTUR_PAJAK') {
        $docsFakturPajak[] = $doc;
    } elseif ($jenis === 'SURAT_PESANAN') {
        $docsSuratPesanan[] = $doc;
    } elseif ($jenis === 'BAST_PAKET' || ($jenis === 'BAST' && !$isPerTujuanBast($doc))) {
        $docsBastPaket[] = $doc;
    }
}

$dokPaketSlots = [
    [
        'jenis' => 'FAKTUR_PAJAK',
        'label' => 'Faktur Pajak',
        'icon'  => 'receipt-outline',
        'color' => 'blue',
        'docs'  => $docsFakturPajak,
        'empty' => 'Belum ada Faktur Pajak',
    ],
    [
        'jenis' => 'SURAT_PESANAN',
        'label' => 'Surat Pesanan',
        'icon'  => 'document-text-outline',
        'color' => 'indigo',
        'docs'  => $docsSuratPesanan,
        'empty' => 'Belum ada Surat Pesanan',
    ],
    [
        'jenis' => 'BAST',
        'label' => 'BAST Paket',
        'icon'  => 'ribbon-outline',
        'color' => 'amber',
        'docs'  => $docsBastPaket,
        'empty' => 'Belum ada BAST Paket',
    ],
];
?>

<div class="bg-white rounded-xl border border-slate-200/90 p-3.5 shadow-2xs space-y-3">
    <div class="flex items-center justify-between gap-2">
        <div class="flex items-center gap-2">
            <span class="w-6 h-6 rounded-lg bg-slate-100 text-slate-700 flex items-center justify-center text-sm">
                <ion-icon name="folder-open-outline"></ion-icon>
            </span>
            <div>
                <h4 class="text-xs font-bold text-slate-800">Dokumen Paket (Gambar / PDF)</h4>
                <p class="text-[10px] text-slate-400">Faktur Pajak, Surat Pesanan, dan BAST Paket (tersinkron dengan Popup BAST)</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-2.5">
        <?php foreach ($dokPaketSlots as $slot):
            $c = $slot['color'];
        ?>
            <div class="rounded-xl border border-slate-200/80 bg-slate-50/50 p-3 flex flex-col justify-between gap-2.5">
                <div class="flex items-center justify-between gap-2">
                    <span class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-800">
                        <ion-icon name="<?= $slot['icon'] ?>" class="text-<?= $c ?>-600 text-sm"></ion-icon>
                        <span><?= $slot['label'] ?></span>
                    </span>
                    <?php if ($canEditDokPaket): ?>
                        <button type="button"
                                onclick="openNativeUploadDokumenPaketModal('<?= $paketKode ?>', '<?= $slot['jenis'] ?>', '<?= $slot['label'] ?>', '<?= $redirectTab ?>')"
                                class="inline-flex items-center gap-1 px-2 py-1 rounded-lg text-[10px] font-semibold bg-<?= $c ?>-600 hover:bg-<?= $c ?>-700 text-white shadow-2xs transition-colors cursor-pointer">
                            <ion-icon name="cloud-upload-outline" class="text-xs"></ion-icon>
                            <span>Upload</span>
                        </button>
                    <?php endif; ?>
                </div>

                <?php if (empty($slot['docs'])): ?>
                    <div class="text-[11px] text-slate-400 italic py-1"><?= $slot['empty'] ?></div>
                <?php else: ?>
                    <div class="space-y-1.5">
                        <?php foreach ($slot['docs'] as $dItem): ?>
                            <div class="bg-white rounded-lg border border-slate-200/80 px-2.5 py-1.5 flex items-center justify-between gap-2">
                                <a href="<?= APP_URL ?>/paket/downloadDokumen/<?= $paketKode ?>/<?= $dItem['id'] ?>?view=1"
                                   target="_blank"
                                   class="inline-flex items-center gap-1.5 text-[11px] font-semibold text-<?= $c ?>-700 hover:underline min-w-0 truncate"
                                   title="<?= htmlspecialchars($dItem['nama_file_asli']) ?>">
                                    <ion-icon name="document-attach-outline" class="shrink-0"></ion-icon>
                                    <span class="truncate"><?= htmlspecialchars($dItem['nama_dokumen']) ?></span>
                                </a>
                                <div class="flex items-center gap-1 shrink-0">
                                    <span class="text-[9px] font-bold px-1 py-0.2 rounded bg-slate-100 text-slate-500 uppercase">
                                        <?= htmlspecialchars($dItem['file_ext'] ?: 'FILE') ?>
                                    </span>
                                    <?php if ($canEditDokPaket): ?>
                                        <button type="button"
                                                onclick="deleteDokumenAjax('<?= $paketKode ?>', '<?= $dItem['id'] ?>', '<?= htmlspecialchars(addslashes($dItem['nama_dokumen'])) ?>', '<?= $redirectTab ?>')"
                                                class="p-0.5 text-slate-400 hover:text-rose-600 rounded cursor-pointer"
                                                title="Hapus <?= $slot['label'] ?>">
                                            <ion-icon name="trash-outline" class="text-xs"></ion-icon>
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
</div>
