<!-- ========================================== -->
<!-- 3. TAB PEMESANAN BARANG                    -->
<!-- ========================================== -->
<div id="tabContent_pemesanan" class="<?= ($activeTab === 'pemesanan') ? '' : 'hidden ' ?>space-y-4">
    <!-- Pemesanan Table Card (Ringkas, Bersih, Langsung Tabel) -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100">
            <div>
                <h3 class="text-base font-bold text-slate-800 flex items-center gap-2">
                    <ion-icon name="bag-check-outline" class="text-emerald-600 text-lg"></ion-icon>
                    <span>Pemesanan Barang (Supplier / Toko)</span>
                </h3>
                <p class="text-xs text-slate-400 mt-0.5">Tracking status pemesanan ke toko/vendor, nama barang belanja, harga beli modal (HPP), dan bukti resi pembelian.</p>
            </div>
            <?php if (!empty($distribusiList)): ?>
                <div class="flex items-center gap-2">
                    <a href="<?= APP_URL ?>/paket/exportDistribusi/<?= $paket['kode_paket'] ?: $paket['id'] ?>"
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200/90 rounded-xl text-xs font-semibold shadow-2xs transition-all hover:scale-[1.01]"
                       title="Download rekap pemesanan dalam format Excel / CSV">
                        <ion-icon name="download-outline" class="text-sm"></ion-icon>
                        <span>Export Excel</span>
                    </a>
                </div>
            <?php endif; ?>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs" id="pemesananTable">
                <thead class="bg-slate-50 text-[10px] uppercase font-bold text-slate-500 border-b border-slate-200/90 tracking-wider">
                    <tr>
                        <th class="py-2.5 px-3 w-8 text-center">NO</th>
                        <th class="py-2.5 px-3 min-w-[150px]">KOMPONEN</th>
                        <th class="py-2.5 px-3 min-w-[160px]">TUJUAN PENERIMA</th>
                        <th class="py-2.5 px-2.5 text-center min-w-[70px]">SATUAN</th>
                        <th class="py-2.5 px-2.5 text-center min-w-[60px]">JUMLAH</th>
                        <th class="py-2.5 px-3 text-right min-w-[120px]">HARGA SATUAN</th>
                        <th class="py-2.5 px-3 min-w-[170px]">NAMA PESANAN (TOKO)</th>
                        <th class="py-2.5 px-3 text-right min-w-[120px]">HARGA BELI SATUAN</th>
                        <th class="py-2.5 px-3 text-right min-w-[100px]">ONGKOS KIRIM</th>
                        <th class="py-2.5 px-3 text-right min-w-[130px]">TOTAL MODAL BELI</th>
                        <th class="py-2.5 px-3 min-w-[130px]">STATUS PESANAN</th>
                        <th class="py-2.5 px-2.5 text-right w-12">AKSI</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php if (empty($distribusiList)): ?>
                        <tr>
                            <td colspan="12" class="py-8 text-center text-slate-400">
                                <div class="w-10 h-10 bg-slate-50 rounded-2xl flex items-center justify-center mx-auto mb-2 text-xl text-slate-300">
                                    <ion-icon name="bag-handle-outline"></ion-icon>
                                </div>
                                <h4 class="text-xs font-bold text-slate-700 mb-0.5">Belum Ada Data Alokasi Barang</h4>
                                <p class="text-[11px] text-slate-400 mb-3">Tentukan alokasi tujuan barang terlebih dahulu melalui tab <strong>Item / Spesifikasi</strong>.</p>
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
                        $totalPesananSudah = 0;
                        $totalNilaiKontrakDist = 0;
                        $totalOngkirDist = 0;
                        foreach ($distribusiList as $dist): 
                            $isOrdered = (($dist['status_barang'] ?? '') === 'Sudah Dipesan');
                            if ($isOrdered) $totalPesananSudah++;
                            $qty = (float)$dist['jumlah'];
                            $itemHargaSatuan = (float)($dist['item_harga_satuan'] ?? 0);
                            $totalNilaiKontrakDist += ($qty * $itemHargaSatuan);
                            $hargaSatuan = (float)($dist['harga_satuan'] ?? 0);
                            $ongkirVal = (float)($dist['ongkos_kirim'] ?? 0);
                            $totalOngkirDist += $ongkirVal;
                            $subtotalBeli = ($qty * $hargaSatuan) + $ongkirVal;
                        ?>
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <!-- No -->
                                <td class="py-2 px-3 text-center font-mono text-[11px] text-slate-400 font-semibold whitespace-nowrap">
                                    <?= $no++ ?>
                                </td>

                                <!-- Komponen Barang -->
                                <td class="py-2 px-3 whitespace-nowrap text-xs font-bold text-slate-800">
                                    <?= htmlspecialchars($dist['nama_komponen'] ?: '-') ?>
                                </td>

                                <!-- Tujuan Penerima -->
                                <td class="py-2 px-3 whitespace-nowrap text-xs font-semibold text-slate-800">
                                    <?= htmlspecialchars($dist['nama_tujuan']) ?>
                                </td>

                                <!-- Satuan -->
                                <td class="py-2 px-2.5 text-center whitespace-nowrap">
                                    <span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-700 font-semibold text-xs">
                                        <?= htmlspecialchars($dist['satuan'] ?: 'Unit') ?>
                                    </span>
                                </td>

                                <!-- Jumlah -->
                                <td class="py-2 px-2.5 text-center whitespace-nowrap font-bold text-slate-800 text-xs">
                                    <?= (float)$qty ?>
                                </td>

                                <!-- HARGA SATUAN (KONTRAK) -->
                                <td class="py-2 px-3 text-right whitespace-nowrap font-mono text-xs font-semibold text-slate-800">
                                    <?php if ($itemHargaSatuan > 0): ?>
                                        <?= formatRupiah($itemHargaSatuan) ?>
                                    <?php else: ?>
                                        <span class="text-slate-300 font-normal">-</span>
                                    <?php endif; ?>
                                </td>

                                <!-- Nama Barang Pesanan Toko -->
                                <td class="py-2 px-3 text-xs">
                                    <?php if (!empty($dist['nama_barang'])): ?>
                                        <div class="font-medium text-slate-800"><?= htmlspecialchars($dist['nama_barang']) ?></div>
                                        <?php if (!empty($dist['ekspedisi']) || !empty($dist['no_resi'])): ?>
                                            <div class="text-[10px] text-slate-400 flex items-center gap-1 mt-0.5">
                                                <ion-icon name="paper-plane-outline" class="text-[10px] text-slate-400"></ion-icon>
                                                <span><?= htmlspecialchars($dist['ekspedisi'] ?: 'Kurir') ?></span>
                                                <?php if (!empty($dist['no_resi'])): ?>
                                                    <span class="font-mono text-slate-600 font-medium">(<?= htmlspecialchars($dist['no_resi']) ?>)</span>
                                                <?php endif; ?>
                                            </div>
                                        <?php endif; ?>
                                        <?php if (!empty($dist['link_pembelian'])): ?>
                                            <a href="<?= htmlspecialchars($dist['link_pembelian']) ?>" target="_blank" rel="noopener noreferrer" class="text-[10px] text-blue-600 hover:underline flex items-center gap-0.5 mt-0.5">
                                                <ion-icon name="link-outline" class="text-[10px]"></ion-icon>
                                                <span>Link Toko</span>
                                            </a>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="text-slate-400 italic text-[11px]">Belum diinput</span>
                                    <?php endif; ?>
                                </td>

                                <!-- Harga Beli Satuan Toko -->
                                <td class="py-2 px-3 text-right whitespace-nowrap font-mono text-xs">
                                    <?php if ($hargaSatuan > 0): ?>
                                        <span class="font-semibold text-indigo-700"><?= formatRupiah($hargaSatuan) ?></span>
                                    <?php else: ?>
                                        <span class="text-slate-400 italic text-[11px]">Rp 0</span>
                                    <?php endif; ?>
                                </td>

                                <!-- Ongkos Kirim -->
                                <td class="py-2 px-3 text-right whitespace-nowrap font-mono text-xs">
                                    <?php if ($ongkirVal > 0): ?>
                                        <span class="font-semibold text-amber-700"><?= formatRupiah($ongkirVal) ?></span>
                                    <?php else: ?>
                                        <span class="text-slate-300 font-normal">-</span>
                                    <?php endif; ?>
                                </td>

                                <!-- Total Modal Beli (HPP) -->
                                <td class="py-2 px-3 text-right whitespace-nowrap font-mono text-xs font-bold">
                                    <?php if ($subtotalBeli > 0): ?>
                                        <span class="text-indigo-700"><?= formatRupiah($subtotalBeli) ?></span>
                                    <?php else: ?>
                                        <span class="text-slate-300 font-normal">-</span>
                                    <?php endif; ?>
                                </td>

                                <!-- STATUS BARANG (Sudah Dipesan vs Belum Dipesan) -->
                                <td class="py-2 px-3 whitespace-nowrap">
                                    <?php if ($role === 'admin' || $role === 'user'): ?>
                                        <button type="button" 
                                                onclick='openModalUpdateStatusBarang(<?= json_encode($dist, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)'
                                                class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-medium leading-normal transition-all hover:scale-[1.02] shadow-2xs cursor-pointer group <?= $isOrdered ? 'bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-100' : 'bg-slate-100 text-slate-600 border border-slate-200 hover:bg-slate-200' ?>"
                                                title="Klik untuk ubah status barang & data pemesanan">
                                            <?php if ($isOrdered): ?>
                                                <ion-icon name="bag-check-outline" class="text-xs shrink-0 text-emerald-600"></ion-icon>
                                                <span>Sudah dipesan</span>
                                            <?php else: ?>
                                                <ion-icon name="time-outline" class="text-xs shrink-0 text-slate-500"></ion-icon>
                                                <span>Belum Dipesan</span>
                                            <?php endif; ?>
                                            <ion-icon name="create-outline" class="text-[10px] opacity-40 group-hover:opacity-100 transition-opacity ml-0.5"></ion-icon>
                                        </button>
                                    <?php else: ?>
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-medium leading-normal <?= $isOrdered ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-600' ?>">
                                            <span><?= $isOrdered ? 'Sudah dipesan' : 'Belum Dipesan' ?></span>
                                        </span>
                                    <?php endif; ?>
                                </td>

                                <!-- Aksi -->
                                <td class="py-2 px-2.5 text-right whitespace-nowrap">
                                    <?php if ($role === 'admin' || $role === 'user'): ?>
                                        <button type="button" 
                                                onclick='openModalUpdateStatusBarang(<?= json_encode($dist, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)'
                                                class="p-1.5 text-slate-400 hover:text-emerald-700 hover:bg-emerald-50 rounded-lg transition-colors cursor-pointer"
                                                title="Update Pemesanan Toko">
                                            <ion-icon name="create-outline" class="text-base"></ion-icon>
                                        </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>

                <!-- Footer Summary Pemesanan -->
                <?php if (!empty($distribusiList)): ?>
                    <tfoot class="bg-slate-50/90 font-bold text-slate-700 border-t-2 border-slate-200 text-xs">
                        <tr>
                            <td colspan="4" class="py-2.5 px-3 text-right uppercase text-[10px] tracking-wider text-slate-500">
                                Total:
                            </td>
                            <td class="py-2.5 px-2.5 text-center font-bold text-slate-800 text-xs whitespace-nowrap">
                                <?= (float)($distribusiSummary['total_qty_dialokasikan'] ?? 0) ?>
                            </td>
                            <td class="py-2.5 px-3 text-right font-mono text-xs font-bold text-slate-700 whitespace-nowrap">
                                <?= formatRupiah($totalNilaiKontrakDist) ?>
                            </td>
                            <td class="py-2.5 px-3 text-right text-slate-400 text-[10px] uppercase font-semibold">
                                Total Modal Beli:
                            </td>
                            <td class="py-2.5 px-3 text-right font-mono text-xs font-semibold text-slate-400">-</td>
                            <td class="py-2.5 px-3 text-right font-mono text-xs font-semibold text-amber-700 whitespace-nowrap">
                                <?= $totalOngkirDist > 0 ? formatRupiah($totalOngkirDist) : '-' ?>
                            </td>
                            <td class="py-2.5 px-3 text-right font-mono font-extrabold text-indigo-700 text-xs whitespace-nowrap">
                                <?= formatRupiah($totalBeliBarang) ?>
                            </td>
                            <td colspan="2" class="py-2.5 px-3 text-center whitespace-nowrap">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold <?= ($totalPesananSudah >= count($distribusiList)) ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' ?>">
                                    <?= $totalPesananSudah ?> / <?= count($distribusiList) ?> Selesai Dipesan
                                </span>
                            </td>
                        </tr>
                    </tfoot>
                <?php endif; ?>
            </table>
        </div>
    </div>
</div>
