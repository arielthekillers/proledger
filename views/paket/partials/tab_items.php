<!-- ========================================== -->
<!-- 4. TAB 2: ITEM / SPESIFIKASI               -->
<!-- ========================================== -->
<div id="tabContent_items" class="hidden space-y-4">
    <!-- Items Table Card (Langsung Tabel) -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100">
            <div>
                <h3 class="text-base font-bold text-slate-800 flex items-center gap-2">
                    <ion-icon name="list-outline" class="text-blue-600 text-lg"></ion-icon>
                    <span>Daftar Item & Spesifikasi Paket</span>
                </h3>
                <p class="text-xs text-slate-400 mt-0.5">Rincian spesifikasi barang, volume, harga satuan, dan tautan katalog acuan/pembanding</p>
            </div>

            <?php if ($role === 'admin' || $role === 'user'): ?>
                <button type="button" onclick="openTambahItemModal()" 
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl text-xs shadow-xs transition-all flex items-center gap-1.5 shrink-0 self-start sm:self-auto">
                    <ion-icon name="add-circle-outline" class="text-base"></ion-icon>
                    <span>Tambah Item Baru</span>
                </button>
            <?php endif; ?>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs" id="itemsTable">
                <thead class="bg-slate-50 text-[10px] uppercase font-bold text-slate-500 border-b border-slate-200/90 tracking-wider">
                    <tr>
                        <th class="py-2 px-3 w-10 text-center">NO</th>
                        <th class="py-2 px-3 min-w-[150px]">KOMPONEN</th>
                        <th class="py-2 px-3 min-w-[220px]">SPESIFIKASI</th>
                        <th class="py-2 px-2.5 text-center">SATUAN</th>
                        <th class="py-2 px-2.5 text-center">JUMLAH</th>
                        <th class="py-2 px-3 text-right">HARGA SATUAN</th>
                        <th class="py-2 px-3 text-right">TOTAL</th>
                        <th class="py-2 px-3 text-center min-w-[160px]">LINK KATALOG & PEMBANDING</th>
                        <th class="py-2 px-3 text-center min-w-[120px]">DISTRIBUSI</th>
                        <th class="py-2 px-3 text-right min-w-[70px]">AKSI</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php if (empty($items)): ?>
                        <tr>
                            <td colspan="10" class="py-8 text-center text-slate-400">
                                <div class="w-10 h-10 bg-slate-50 rounded-2xl flex items-center justify-center mx-auto mb-2 text-xl text-slate-300">
                                    <ion-icon name="cube-outline"></ion-icon>
                                </div>
                                <h4 class="text-xs font-bold text-slate-700 mb-0.5">Belum Ada Item / Spesifikasi</h4>
                                <p class="text-[11px] text-slate-400 mb-3">Tambahkan item pengadaan barang ke paket ini untuk mencatat spesifikasi dan tautan harga pembanding.</p>
                                <?php if ($role === 'admin' || $role === 'user'): ?>
                                    <button type="button" onclick="openTambahItemModal()" 
                                            class="inline-flex items-center gap-1.5 px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-semibold shadow-xs transition-all">
                                        <ion-icon name="add-circle-outline"></ion-icon>
                                        <span>Tambah Item Sekarang</span>
                                    </button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php $no = 1; foreach ($items as $item): ?>
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <!-- No -->
                                <td class="py-1.5 px-3 text-center font-mono text-[11px] text-slate-400 font-semibold">
                                    <?= $no++ ?>
                                </td>

                                <!-- Komponen -->
                                <td class="py-1.5 px-3 font-bold text-slate-800 text-xs">
                                    <?= htmlspecialchars($item['komponen']) ?>
                                </td>

                                <!-- Spesifikasi -->
                                <td class="py-1.5 px-3 text-slate-600 text-[11px] leading-relaxed max-w-xs">
                                    <?= nl2br(htmlspecialchars($item['spesifikasi'] ?: '-')) ?>
                                </td>

                                <!-- Satuan -->
                                <td class="py-1.5 px-2.5 text-center whitespace-nowrap">
                                    <span class="px-1.5 py-0.5 rounded bg-slate-100 text-slate-700 font-semibold text-[10px]">
                                        <?= htmlspecialchars($item['satuan'] ?: 'Unit') ?>
                                    </span>
                                </td>

                                <!-- Jumlah -->
                                <td class="py-1.5 px-2.5 text-center font-mono font-bold text-slate-800 text-xs whitespace-nowrap">
                                    <?= (float)$item['jumlah'] ?>
                                </td>

                                <!-- Harga Satuan -->
                                <td class="py-1.5 px-3 text-right font-mono text-slate-700 text-xs whitespace-nowrap">
                                    <?= formatRupiah($item['harga_satuan']) ?>
                                </td>

                                <!-- Total Harga -->
                                <td class="py-1.5 px-3 text-right font-mono font-bold text-slate-900 text-xs bg-slate-50/50 whitespace-nowrap">
                                    <?= formatRupiah($item['total_harga']) ?>
                                </td>

                                <!-- Link Acuan & Pembanding -->
                                <td class="py-1.5 px-3 text-center whitespace-nowrap">
                                    <div class="flex items-center justify-center gap-1 flex-wrap">
                                        <!-- Link Asli -->
                                        <?php if (!empty($item['link_asli'])): ?>
                                            <a href="<?= htmlspecialchars($item['link_asli']) ?>" target="_blank" rel="noopener noreferrer"
                                               class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded bg-blue-50 text-blue-700 border border-blue-200/80 hover:bg-blue-100 text-[10px] font-semibold transition-colors"
                                               title="Buka Link Asli: <?= htmlspecialchars($item['link_asli']) ?>">
                                                <ion-icon name="link-outline"></ion-icon>
                                                <span>Asli</span>
                                            </a>
                                        <?php else: ?>
                                            <span class="text-slate-300 text-[11px]">-</span>
                                        <?php endif; ?>

                                        <!-- Link Pembanding 1 -->
                                        <?php if (!empty($item['link_pembanding_1'])): ?>
                                            <a href="<?= htmlspecialchars($item['link_pembanding_1']) ?>" target="_blank" rel="noopener noreferrer"
                                               class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded bg-emerald-50 text-emerald-700 border border-emerald-200/80 hover:bg-emerald-100 text-[10px] font-semibold transition-colors"
                                               title="Pembanding 1: <?= htmlspecialchars($item['link_pembanding_1']) ?>">
                                                <ion-icon name="git-compare-outline"></ion-icon>
                                                <span>P1</span>
                                            </a>
                                        <?php endif; ?>

                                        <!-- Link Pembanding 2 -->
                                        <?php if (!empty($item['link_pembanding_2'])): ?>
                                            <a href="<?= htmlspecialchars($item['link_pembanding_2']) ?>" target="_blank" rel="noopener noreferrer"
                                               class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded bg-purple-50 text-purple-700 border border-purple-200/80 hover:bg-purple-100 text-[10px] font-semibold transition-colors"
                                               title="Pembanding 2: <?= htmlspecialchars($item['link_pembanding_2']) ?>">
                                                <ion-icon name="git-compare-outline"></ion-icon>
                                                <span>P2</span>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </td>

                                <!-- Distribusi -->
                                <td class="py-1.5 px-3 text-center whitespace-nowrap">
                                    <?php 
                                        $tujuanCount = (int)($item['tujuan_count'] ?? 0);
                                        if ($tujuanCount === 0 && !empty($item['existing_dists'])) {
                                            $tujuanCount = count($item['existing_dists']);
                                        }
                                    ?>
                                    <?php if ($role === 'admin' || $role === 'user'): ?>
                                        <button type="button" 
                                                onclick='openDistribusiPerItemModal(<?= json_encode($item, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)'
                                                class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold transition-all shadow-2xs <?= $item['is_fully_allocated'] ? 'bg-slate-50 border border-slate-200 text-slate-700 hover:bg-slate-100' : ($tujuanCount > 0 ? 'bg-indigo-50 border border-indigo-200 text-indigo-700 hover:bg-indigo-100' : 'bg-indigo-600 hover:bg-indigo-700 text-white shadow-xs') ?>"
                                                title="<?= $item['is_fully_allocated'] ? 'Item sudah dialokasikan penuh (' . $tujuanCount . ' Tujuan)' : 'Alokasikan distribusi item (Sisa kuota: ' . (float)$item['remaining_qty'] . ' ' . htmlspecialchars($item['satuan'] ?: 'Unit') . ')' ?>">
                                            <?php if ($item['is_fully_allocated']): ?>
                                                <ion-icon name="checkmark-circle" class="text-sm text-emerald-600 shrink-0"></ion-icon>
                                                <span><?= $tujuanCount ?> Tujuan</span>
                                            <?php elseif ($tujuanCount > 0): ?>
                                                <ion-icon name="navigate" class="text-xs text-indigo-600 shrink-0"></ion-icon>
                                                <span><?= $tujuanCount ?> Tujuan</span>
                                                <span class="bg-indigo-600 text-white text-[10px] px-1.5 py-0.2 rounded font-mono font-bold" title="Sisa kuota: <?= (float)$item['remaining_qty'] ?> <?= htmlspecialchars($item['satuan'] ?: 'Unit') ?>"><?= (float)$item['remaining_qty'] ?></span>
                                            <?php else: ?>
                                                <ion-icon name="navigate" class="text-xs"></ion-icon>
                                                <span>Distribusi</span>
                                                <span class="bg-indigo-700/80 text-white text-[10px] px-1.5 py-0.2 rounded font-mono font-bold"><?= (float)$item['remaining_qty'] ?></span>
                                            <?php endif; ?>
                                        </button>
                                    <?php else: ?>
                                        <?php if ($item['is_fully_allocated']): ?>
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold bg-slate-50 border border-slate-200 text-slate-700">
                                                <ion-icon name="checkmark-circle" class="text-sm text-emerald-600"></ion-icon>
                                                <span><?= $tujuanCount ?> Tujuan</span>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-slate-500 text-xs font-medium"><?= $tujuanCount > 0 ? $tujuanCount . ' Tujuan' : '-' ?></span>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </td>

                                <!-- Aksi (Edit & Hapus) -->
                                <td class="py-1.5 px-3 text-right whitespace-nowrap">
                                    <?php if ($role === 'admin' || $role === 'user'): ?>
                                        <div class="flex items-center justify-end gap-1">
                                            <!-- Edit Item Trigger -->
                                            <button type="button" 
                                                    onclick='openEditItemModal(<?= json_encode($item, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)'
                                                    class="p-1 text-slate-400 hover:text-amber-600 hover:bg-amber-50 rounded-md transition-colors text-sm"
                                                    title="Edit Item">
                                                <ion-icon name="create-outline"></ion-icon>
                                            </button>

                                            <!-- Hapus Item -->
                                            <a href="<?= APP_URL ?>/paket/deleteItem/<?= $paket['kode_paket'] ?: $paket['id'] ?>/<?= $item['id'] ?>" 
                                               data-confirm-delete="<?= htmlspecialchars($item['komponen']) ?>"
                                               data-type="Item Paket"
                                               class="p-1 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-md transition-colors text-sm"
                                               title="Hapus Item">
                                                <ion-icon name="trash-outline"></ion-icon>
                                            </a>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-slate-300">-</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>

                <!-- Table Footer Summary Row -->
                <?php if (!empty($items)): ?>
                    <tfoot class="bg-slate-50/90 font-bold text-slate-700 border-t-2 border-slate-200">
                        <tr>
                            <td colspan="4" class="py-2 px-3 text-right uppercase text-[10px] tracking-wider text-slate-500">
                                Total:
                            </td>
                            <td class="py-2 px-2.5 text-center font-mono text-xs text-slate-900">
                                <?= (float)$itemSummary['total_qty'] ?>
                            </td>
                            <td class="py-2 px-3 text-right text-[10px] text-slate-400 font-normal">
                                Subtotal Realisasi
                            </td>
                            <td class="py-2 px-3 text-right font-mono text-xs sm:text-sm text-blue-700 bg-blue-50/50">
                                <?= formatRupiah($itemSummary['grand_total']) ?>
                            </td>
                            <td colspan="3" class="py-2 px-3 text-slate-400 text-[10px] font-normal">
                                (<?= count($items) ?> item terdaftar)
                            </td>
                        </tr>
                    </tfoot>
                <?php endif; ?>
            </table>
        </div>
    </div>
</div>
