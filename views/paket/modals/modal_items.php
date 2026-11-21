<?php
/**
 * Modal View: Komponen Item Barang
 */
$role = $_SESSION['admin_role'] ?? 'user';
$grandTotal = 0;
$totalQty = 0;
foreach ($items as $it) {
    $grandTotal += (float)($it['total_harga'] ?? ((float)$it['jumlah'] * (float)$it['harga_satuan']));
    $totalQty += (float)$it['jumlah'];
}
?>

<div class="space-y-4">
    <!-- Top Summary Banner & Action -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white p-4 rounded-xl border border-slate-200/90 shadow-2xs">
        <div class="flex items-center gap-3 flex-wrap">
            <span class="text-xs font-bold px-3 py-1 rounded-lg bg-blue-50 text-blue-700 border border-blue-200">
                <?= count($items) ?> Macam Item
            </span>
            <span class="text-xs font-bold px-3 py-1 rounded-lg bg-slate-100 text-slate-700 border border-slate-200">
                Total Qty: <?= number_format($totalQty, 0, ',', '.') ?>
            </span>
            <span class="text-xs font-bold px-3 py-1 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200">
                Total Nilai: Rp <?= number_format($grandTotal, 0, ',', '.') ?>
            </span>
        </div>

        <?php if ($role === 'admin' || $role === 'user'): ?>
            <button type="button" 
                    onclick="openNativeTambahItemModal('<?= $paket['kode_paket'] ?: $paket['id'] ?>')"
                    class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-semibold shadow-xs flex items-center gap-1.5 transition-all self-start sm:self-auto cursor-pointer">
                <ion-icon name="add-circle-outline" class="text-sm"></ion-icon>
                <span>Tambah Komponen</span>
            </button>
        <?php endif; ?>
    </div>

    <!-- Tabel Komponen Item -->
    <div class="bg-white rounded-xl border border-slate-200/90 overflow-hidden shadow-2xs">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-[11px] font-bold text-slate-500 uppercase tracking-wider border-b border-slate-200/80">
                    <tr>
                        <th class="py-2.5 px-3 w-8 text-center">NO</th>
                        <th class="py-2.5 px-3 min-w-[180px]">KOMPONEN BARANG</th>
                        <th class="py-2.5 px-3 min-w-[160px]">SPESIFIKASI</th>
                        <th class="py-2.5 px-2.5 text-center min-w-[70px]">QTY</th>
                        <th class="py-2.5 px-3 text-right min-w-[110px]">HARGA SATUAN</th>
                        <th class="py-2.5 px-3 text-right min-w-[120px]">TOTAL HARGA</th>
                        <th class="py-2.5 px-3 text-center min-w-[100px]">ALOKASI</th>
                        <?php if ($role === 'admin' || $role === 'user'): ?>
                            <th class="py-2.5 px-3 text-right min-w-[80px]">AKSI</th>
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
                                <p class="text-[11px] text-slate-400">Klik tombol "Tambah Komponen" di atas untuk memasukkan rincian barang.</p>
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
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <td class="py-2 px-3 text-center text-slate-400 font-mono text-[11px]"><?= $no++ ?></td>
                                <td class="py-2 px-3 whitespace-nowrap text-xs font-bold text-slate-800">
                                    <?= htmlspecialchars($itNama) ?>
                                </td>
                                <td class="py-2 px-3 text-xs text-slate-500 max-w-xs truncate" title="<?= htmlspecialchars($it['spesifikasi'] ?? '') ?>">
                                    <?= htmlspecialchars($it['spesifikasi'] ?: '-') ?>
                                </td>
                                <td class="py-2 px-2.5 text-center whitespace-nowrap font-bold text-slate-800 text-xs">
                                    <?= (float)$it['jumlah'] ?> <?= htmlspecialchars($it['satuan'] ?: 'Unit') ?>
                                </td>
                                <td class="py-2 px-3 text-right whitespace-nowrap text-xs font-mono text-slate-700">
                                    Rp <?= number_format((float)$it['harga_satuan'], 0, ',', '.') ?>
                                </td>
                                <td class="py-2 px-3 text-right whitespace-nowrap text-xs font-mono font-bold text-slate-800">
                                    Rp <?= number_format($itTotal, 0, ',', '.') ?>
                                </td>
                                <td class="py-2 px-3 text-center whitespace-nowrap">
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[11px] font-bold <?= $isFull ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-blue-50 text-blue-700 border border-blue-200' ?>" title="<?= $itAlloc ?> dari <?= $itQty ?> telah dialokasikan">
                                        <?= $itAlloc ?>/<?= $itQty ?>
                                    </span>
                                </td>
                                <?php if ($role === 'admin' || $role === 'user'): ?>
                                    <td class="py-2 px-3 text-right whitespace-nowrap">
                                        <div class="flex items-center justify-end gap-1">
                                            <button type="button" 
                                                    onclick='openNativeEditItemModal(<?= json_encode($it, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)'
                                                    class="p-1 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-md transition-colors text-xs"
                                                    title="Edit Item">
                                                <ion-icon name="create-outline"></ion-icon>
                                            </button>
                                            <button type="button" 
                                                    onclick="deleteItemAjax('<?= $paket['kode_paket'] ?: $paket['id'] ?>', '<?= $it['id'] ?>', '<?= htmlspecialchars(addslashes($itNama)) ?>')"
                                                    class="p-1 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-md transition-colors text-xs"
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
            </table>
        </div>
    </div>
</div>
