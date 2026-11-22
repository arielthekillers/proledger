<?php
/**
 * Modal View: Komponen Item Barang
 */
$role = $_SESSION['admin_role'] ?? 'user';
$grandTotal = 0;
$totalQty = 0;
$totalAllocated = 0;
foreach ($items as $it) {
    $grandTotal += (float)($it['total_harga'] ?? ((float)$it['jumlah'] * (float)$it['harga_satuan']));
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
                    <th class="py-2.5 px-2 text-center w-20">ALOKASI</th>
                    <?php if ($role === 'admin' || $role === 'user'): ?>
                        <th class="py-2.5 px-3 text-center w-24">AKSI</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php if (empty($items)): ?>
                    <tr>
                        <td colspan="8" class="py-8 text-center text-slate-400 text-xs">
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
                            <td class="py-2.5 px-2 text-center whitespace-nowrap">
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-bold <?= $isFull ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-blue-50 text-blue-700 border border-blue-200' ?>" title="<?= $itAlloc ?> dari <?= $itQty ?> telah dialokasikan">
                                    <?= $itAlloc ?>/<?= $itQty ?>
                                </span>
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

    <!-- 5. Informasi Tambahan di Bawah Tabel -->
    <div class="flex items-center justify-between text-[11px] text-slate-500 px-1">
        <div class="flex items-center gap-1.5">
            <ion-icon name="information-circle-outline" class="text-blue-500 text-sm"></ion-icon>
            <span>Total Nilai Komponen otomatis menyinkronkan Nilai Pagu Kontrak paket ini.</span>
        </div>
        <span class="font-medium text-slate-600">Alokasi Distribusi: <strong><?= $totalAllocated ?></strong> dari <strong><?= $totalQty ?></strong> unit (<?= $totalQty > 0 ? round(($totalAllocated / $totalQty) * 100) : 0 ?>%)</span>
    </div>
</div>
