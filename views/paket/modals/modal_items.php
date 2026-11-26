<?php
/**
 * Modal View: Komponen Item Barang
 */
$role = $_SESSION['admin_role'] ?? 'user';
$persenPasal = isset($paket['persen_pasal']) && $paket['persen_pasal'] !== '' ? (float)$paket['persen_pasal'] : 13.0;
$persenAdmin = isset($paket['persen_admin']) && $paket['persen_admin'] !== '' ? (float)$paket['persen_admin'] : 2.0;
$totalPotonganPersen = $persenPasal + $persenAdmin;
$limitFactor = max(0, 1 - ($totalPotonganPersen / 100));

$grandTotal = 0;
$grandLimit = 0;
$totalQty = 0;
$totalAllocated = 0;
foreach ($items as $it) {
    $rowTotal = (float)($it['total_harga'] ?? ((float)$it['jumlah'] * (float)$it['harga_satuan']));
    $grandTotal += $rowTotal;
    $grandLimit += round($rowTotal * $limitFactor);
    $totalQty += (float)$it['jumlah'];
    $totalAllocated += (float)($it['allocated_qty'] ?? 0);
}
?>

<!-- Header Action Teleport Source (Moved to Modal Header) -->
<div id="modalActionSource" class="hidden">
    <?php if ($role === 'admin' || $role === 'user'): ?>
        <button type="button" 
                onclick="openNativeTambahItemModal('<?= $paket['kode_paket'] ?: $paket['id'] ?>')"
                class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-semibold shadow-xs flex items-center gap-1.5 transition-all cursor-pointer">
            <ion-icon name="add-circle-outline" class="text-sm"></ion-icon>
            <span>Tambah Komponen</span>
        </button>
    <?php endif; ?>
</div>

<!-- 1. Langsung Tabel Komponen Item (Full Width, No Horizontal Scroll) -->
<div class="space-y-3">
    <div class="bg-white rounded-xl border border-slate-200/90 overflow-hidden shadow-2xs">
        <table class="w-full text-left text-xs table-fixed">
            <thead class="bg-slate-50 text-[11px] font-bold text-slate-500 uppercase tracking-wider border-b border-slate-200/80">
                <tr>
                    <th class="py-2.5 px-3 w-10 text-center">NO</th>
                    <th class="py-2.5 px-3 w-44">KOMPONEN BARANG</th>
                    <th class="py-2.5 px-3">SPESIFIKASI</th>
                    <th class="py-2.5 px-2 text-center w-20">QTY</th>
                    <th class="py-2.5 px-3 text-right w-28">HARGA SATUAN</th>
                    <th class="py-2.5 px-3 text-right w-32">TOTAL HARGA</th>
                    <th class="py-2.5 px-3 text-right w-32" title="Batas acuan belanja setelah dikurangi potongan <?= $totalPotonganPersen ?>%">LIMIT BELANJA (-<?= $totalPotonganPersen ?>%)</th>
                    <th class="py-2.5 px-2 text-center w-20">ALOKASI</th>
                    <?php if ($role === 'admin' || $role === 'user'): ?>
                        <th class="py-2.5 px-3 text-center w-24">AKSI</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php if (empty($items)): ?>
                    <tr>
                        <td colspan="9" class="py-8 text-center text-slate-400 text-xs">
                            <div class="w-10 h-10 bg-slate-50 rounded-2xl flex items-center justify-center mx-auto mb-2 text-xl text-slate-300">
                                <ion-icon name="cube-outline"></ion-icon>
                            </div>
                            <h4 class="text-xs font-bold text-slate-700 mb-0.5">Belum Ada Komponen Item</h4>
                            <p class="text-[11px] text-slate-400">Klik tombol "Tambah Komponen" pada header di atas untuk memasukkan rincian barang.</p>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php $no = 1; foreach ($items as $it): ?>
                        <?php 
                        $itNama = $it['komponen'] ?? $it['nama_barang'] ?? '-';
                        $itTotal = (float)($it['total_harga'] ?? ((float)$it['jumlah'] * (float)$it['harga_satuan']));
                        $itLimit = round($itTotal * $limitFactor);
                        $itLimitUnit = round((float)$it['harga_satuan'] * $limitFactor);
                        $itAlloc = (float)($it['allocated_qty'] ?? 0);
                        $itQty = (float)$it['jumlah'];
                        $isFull = ($itAlloc >= $itQty && $itQty > 0);
                        ?>
                        <tr class="hover:bg-slate-50/70 transition-colors" data-item-select-id="<?= $it['id'] ?>" data-item-select-name="<?= htmlspecialchars($itNama) ?>">
                            <td class="py-2.5 px-3 text-center text-slate-400 font-mono text-[11px]"><?= $no++ ?></td>
                            <td class="py-2.5 px-3 text-xs font-bold text-slate-800 break-words">
                                <?= htmlspecialchars($itNama) ?>
                            </td>
                            <td class="py-2.5 px-3 text-xs text-slate-500 break-words leading-relaxed" title="<?= htmlspecialchars($it['spesifikasi'] ?? '') ?>">
                                <?= htmlspecialchars($it['spesifikasi'] ?: '-') ?>
                            </td>
                            <td class="py-2.5 px-2 text-center whitespace-nowrap font-bold text-slate-800 text-xs">
                                <?= (float)$it['jumlah'] ?> <?= htmlspecialchars($it['satuan'] ?: 'Unit') ?>
                            </td>
                            <td class="py-2.5 px-3 text-right whitespace-nowrap text-xs font-mono text-slate-700">
                                Rp <?= number_format((float)$it['harga_satuan'], 0, ',', '.') ?>
                            </td>
                            <td class="py-2.5 px-3 text-right whitespace-nowrap text-xs font-mono font-bold text-slate-800">
                                Rp <?= number_format($itTotal, 0, ',', '.') ?>
                            </td>
                            <td class="py-2.5 px-3 text-right whitespace-nowrap text-xs font-mono">
                                <div class="font-bold text-emerald-700">Rp <?= number_format($itLimit, 0, ',', '.') ?></div>
                                <div class="text-[10px] text-slate-400">@ Rp <?= number_format($itLimitUnit, 0, ',', '.') ?></div>
                            </td>
                            <td class="py-2.5 px-2 text-center whitespace-nowrap">
                                <?php if ($role === 'admin' || $role === 'user'): ?>
                                    <button type="button"
                                            onclick="openNativeTambahDistribusiModal(<?= (int)$it['id'] ?>)"
                                            class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-bold transition-all cursor-pointer <?= $isFull ? 'bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-100' : 'bg-blue-50 text-blue-700 border border-blue-200 hover:bg-blue-100' ?>"
                                            title="<?= $itAlloc ?> dari <?= $itQty ?> telah dialokasikan &bull; Klik untuk tambah alokasi">
                                        <span><?= $itAlloc ?>/<?= $itQty ?></span>
                                        <?php if (!$isFull): ?>
                                            <ion-icon name="add-circle-outline" class="text-xs"></ion-icon>
                                        <?php endif; ?>
                                    </button>
                                <?php else: ?>
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-bold <?= $isFull ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-blue-50 text-blue-700 border border-blue-200' ?>" title="<?= $itAlloc ?> dari <?= $itQty ?> telah dialokasikan">
                                        <?= $itAlloc ?>/<?= $itQty ?>
                                    </span>
                                <?php endif; ?>
                            </td>
                            <?php if ($role === 'admin' || $role === 'user'): ?>
                                <td class="py-2.5 px-3 text-center whitespace-nowrap">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <button type="button" 
                                                onclick='openNativeEditItemModal(<?= json_encode($it, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)'
                                                class="p-1.5 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors text-xs cursor-pointer"
                                                title="Edit Item">
                                            <ion-icon name="create-outline"></ion-icon>
                                        </button>
                                        <button type="button" 
                                                onclick="deleteItemAjax('<?= $paket['kode_paket'] ?: $paket['id'] ?>', '<?= $it['id'] ?>', '<?= htmlspecialchars(addslashes($itNama)) ?>')"
                                                class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors text-xs cursor-pointer"
                                                title="Hapus Item">
                                            <ion-icon name="trash-outline"></ion-icon>
                                        </button>
                                    </div>
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
                        <span class="font-normal text-slate-500">Total Komponen:</span> <?= count($items) ?> Macam Item
                    </td>
                    <td class="py-2.5 px-2 text-center text-xs font-extrabold text-slate-900 whitespace-nowrap">
                        <?= number_format($totalQty, 0, ',', '.') ?> Unit
                    </td>
                    <td class="py-2.5 px-3 text-right text-xs text-slate-400 font-normal">
                        -
                    </td>
                    <td class="py-2.5 px-3 text-right text-xs font-mono font-extrabold text-blue-700 whitespace-nowrap">
                        Rp <?= number_format($grandTotal, 0, ',', '.') ?>
                    </td>
                    <td class="py-2.5 px-3 text-right text-xs font-mono font-extrabold text-emerald-700 whitespace-nowrap">
                        Rp <?= number_format($grandLimit, 0, ',', '.') ?>
                    </td>
                    <td class="py-2.5 px-2 text-center text-xs whitespace-nowrap">
                        <span class="text-[11px] font-bold text-slate-700"><?= $totalAllocated ?>/<?= $totalQty ?></span>
                    </td>
                    <?php if ($role === 'admin' || $role === 'user'): ?>
                        <td class="py-2.5 px-3"></td>
                    <?php endif; ?>
                </tr>
            </tfoot>
        </table>
    </div>

    <!-- 5. Informasi Tambahan & Kroscek DPP di Bawah Tabel -->
    <?php
    $hasNegoModal = (!empty($paket['pagu_nego']) && (float)$paket['pagu_nego'] > 0);
    $acuanModal = $hasNegoModal ? (float)$paket['pagu_nego'] : (float)($paket['pagu'] ?? 0);
    $targetDppModal = calculateDPP($acuanModal);
    $selisihDppModal = $grandTotal - $targetDppModal;
    $cocokDppModal = ($targetDppModal > 0 && abs($selisihDppModal) <= 100);
    ?>
    <div class="rounded-xl border px-3.5 py-2.5 flex flex-wrap items-center justify-between gap-2 text-xs <?= $cocokDppModal ? 'bg-emerald-50/70 border-emerald-200 text-emerald-800' : 'bg-rose-50/70 border-rose-200 text-rose-800' ?>">
        <div class="flex flex-wrap items-center gap-3">
            <span>Target DPP (-11%): <strong class="font-mono">Rp <?= number_format($targetDppModal, 0, ',', '.') ?></strong></span>
            <span class="text-slate-300">|</span>
            <span>Total Komponen: <strong class="font-mono">Rp <?= number_format($grandTotal, 0, ',', '.') ?></strong></span>
            <span class="text-slate-300">|</span>
            <span>Selisih:
                <strong class="font-mono">
                    <?= $cocokDppModal ? '✓ Cocok (Rp 0)' : (($selisihDppModal > 0 ? '+' : '-') . 'Rp ' . number_format(abs($selisihDppModal), 0, ',', '.')) ?>
                </strong>
            </span>
        </div>
        <span class="text-[11px] font-medium">Alokasi Distribusi: <strong><?= $totalAllocated ?></strong> dari <strong><?= $totalQty ?></strong> unit (<?= $totalQty > 0 ? round(($totalAllocated / $totalQty) * 100) : 0 ?>%)</span>
    </div>
</div>

<?php if ($role === 'admin' || $role === 'user'): ?>
    <?php require VIEW_PATH . '/paket/modals/_modal_tambah_alokasi.php'; ?>
<?php endif; ?>
