<!-- ========================================== -->
<!-- 5. TAB 3: DISTRIBUSI BARANG                -->
<!-- ========================================== -->
<div id="tabContent_distribusi" class="hidden space-y-4">
    <!-- Distribusi Table Card (Ringkas, Bersih, Langsung Tabel) -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100">
            <div>
                <h3 class="text-base font-bold text-slate-800 flex items-center gap-2">
                    <ion-icon name="navigate-outline" class="text-indigo-600 text-lg"></ion-icon>
                    <span>Monitoring Distribusi & Pengiriman</span>
                </h3>
                <p class="text-xs text-slate-400 mt-0.5">Tracking rute pengiriman, status pemesanan, dan konfirmasi penerimaan barang di lokasi.</p>
            </div>
            <?php if (!empty($distribusiList)): ?>
                <div class="flex items-center gap-2">
                    <a href="<?= APP_URL ?>/paket/exportDistribusi/<?= $paket['kode_paket'] ?: $paket['id'] ?>"
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200/90 rounded-xl text-xs font-semibold shadow-2xs transition-all hover:scale-[1.01]"
                       title="Download rekap distribusi dalam format Excel / CSV">
                        <ion-icon name="download-outline" class="text-sm"></ion-icon>
                        <span>Export Excel / CSV</span>
                    </a>
                </div>
            <?php endif; ?>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs" id="distribusiTable">
                <thead class="bg-slate-50 text-[10px] uppercase font-bold text-slate-500 border-b border-slate-200/90 tracking-wider">
                    <tr>
                        <th class="py-2 px-3 w-8 text-center">NO</th>
                        <th class="py-2 px-3 min-w-[170px]">TUJUAN PENERIMA</th>
                        <th class="py-2 px-3 min-w-[150px]">KOMPONEN BARANG</th>
                        <th class="py-2 px-2.5 text-center min-w-[70px]">JUMLAH</th>
                        <th class="py-2 px-3 min-w-[140px]">RUTE PENGIRIMAN</th>
                        <th class="py-2 px-3 min-w-[150px]">STATUS PEMESANAN</th>
                        <th class="py-2 px-3 min-w-[150px]">STATUS PENERIMAAN</th>
                        <th class="py-2 px-2.5 text-right w-12">AKSI</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php if (empty($distribusiList)): ?>
                        <tr>
                            <td colspan="8" class="py-8 text-center text-slate-400">
                                <div class="w-10 h-10 bg-slate-50 rounded-2xl flex items-center justify-center mx-auto mb-2 text-xl text-slate-300">
                                    <ion-icon name="navigate-outline"></ion-icon>
                                </div>
                                <h4 class="text-xs font-bold text-slate-700 mb-0.5">Belum Ada Data Distribusi</h4>
                                <p class="text-[11px] text-slate-400 mb-3">Alokasi tujuan dan kuantitas barang ditentukan langsung melalui tab <strong>Item / Spesifikasi</strong>.</p>
                                <button type="button" onclick="switchDetailTab('items')" 
                                        class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-semibold shadow-xs transition-all">
                                    <ion-icon name="list-outline"></ion-icon>
                                    <span>Buka Tab Item / Spesifikasi</span>
                                </button>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php $no = 1; foreach ($distribusiList as $dist): ?>
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <!-- No -->
                                <td class="py-1.5 px-3 text-center font-mono text-[11px] text-slate-400 font-semibold">
                                    <?= $no++ ?>
                                </td>

                                <!-- Tujuan Penerima (Bersih tanpa info kategori / kab-kota / PIC yang berulang) -->
                                <td class="py-2.5 px-3">
                                    <span class="font-bold text-slate-800 text-xs block">
                                        <?= htmlspecialchars($dist['nama_tujuan']) ?>
                                    </span>
                                </td>

                                <!-- Komponen Barang (Bersih tanpa deskripsi teks spesifikasi panjang) -->
                                <td class="py-2.5 px-3">
                                    <span class="font-semibold text-slate-800 text-xs block">
                                        <?= htmlspecialchars($dist['nama_komponen'] ?: '-') ?>
                                    </span>
                                </td>

                                <!-- Satuan & Jumlah -->
                                <td class="py-2.5 px-2.5 text-center whitespace-nowrap bg-indigo-50/20">
                                    <span class="font-mono font-bold text-slate-900 text-xs">
                                        <?= (float)$dist['jumlah'] ?>
                                    </span>
                                    <span class="text-[10px] text-slate-500 font-medium ml-0.5">
                                        <?= htmlspecialchars($dist['satuan'] ?: 'Unit') ?>
                                    </span>
                                </td>

                                <!-- Rute Pengiriman (Interaktif - Klik untuk Popup) -->
                                <td class="py-2.5 px-3 whitespace-nowrap">
                                    <?php $rute = $dist['rute_pengiriman'] ?? 'kantor'; ?>
                                    <?php if ($role === 'admin' || $role === 'user'): ?>
                                        <button type="button" 
                                                onclick='openModalUpdateRute(<?= json_encode($dist, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)'
                                                class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold transition-all hover:scale-[1.02] shadow-2xs cursor-pointer group <?= ($rute === 'kantor') ? 'bg-blue-50 text-blue-700 border border-blue-200/90 hover:bg-blue-100' : (($rute === 'gudang') ? 'bg-amber-50 text-amber-700 border border-amber-200/90 hover:bg-amber-100' : 'bg-emerald-50 text-emerald-700 border border-emerald-200/90 hover:bg-emerald-100') ?>"
                                                title="Klik untuk mengubah rute pengiriman">
                                            <?php if ($rute === 'kantor'): ?>
                                                <ion-icon name="home-outline" class="text-sm shrink-0"></ion-icon>
                                                <span>Ke Rumah / Kantor</span>
                                            <?php elseif ($rute === 'gudang'): ?>
                                                <ion-icon name="business-outline" class="text-sm shrink-0"></ion-icon>
                                                <span>Ke Gudang Transit</span>
                                            <?php else: ?>
                                                <ion-icon name="paper-plane-outline" class="text-sm shrink-0"></ion-icon>
                                                <span>Langsung ke Tujuan</span>
                                            <?php endif; ?>
                                            <ion-icon name="chevron-down-outline" class="text-[10px] opacity-40 group-hover:opacity-100 transition-opacity ml-0.5"></ion-icon>
                                        </button>
                                    <?php else: ?>
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold <?= ($rute === 'kantor') ? 'bg-blue-50 text-blue-700 border border-blue-200/90' : (($rute === 'gudang') ? 'bg-amber-50 text-amber-700 border border-amber-200/90' : 'bg-emerald-50 text-emerald-700 border border-emerald-200/90') ?>">
                                            <span><?= ($rute === 'kantor') ? 'Ke Rumah / Kantor' : (($rute === 'gudang') ? 'Ke Gudang Transit' : 'Langsung ke Tujuan') ?></span>
                                        </span>
                                    <?php endif; ?>
                                </td>

                                <!-- Status Pemesanan Logistik (Interaktif - Klik untuk Popup) -->
                                <td class="py-2 px-3 whitespace-nowrap">
                                    <?php $spm = $dist['status_pemesanan'] ?? 'Belum Dipesan'; ?>
                                    <?php if ($role === 'admin' || $role === 'user'): ?>
                                        <button type="button" 
                                                onclick='openModalUpdatePemesanan(<?= json_encode($dist, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)'
                                                class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold transition-all hover:scale-[1.02] shadow-2xs cursor-pointer group <?= ($spm === 'Tiba di Transit') ? 'bg-indigo-50 text-indigo-700 border border-indigo-200 hover:bg-indigo-100' : (($spm === 'Sudah Dipesan') ? 'bg-sky-50 text-sky-700 border border-sky-200 hover:bg-sky-100' : 'bg-slate-100 text-slate-600 border border-slate-200 hover:bg-slate-200') ?>"
                                                title="Klik untuk update status pemesanan & resi">
                                            <?php if ($spm === 'Tiba di Transit'): ?>
                                                <ion-icon name="cube-outline" class="text-sm shrink-0"></ion-icon>
                                                <span>Tiba di Transit</span>
                                            <?php elseif ($spm === 'Sudah Dipesan'): ?>
                                                <ion-icon name="bag-check-outline" class="text-sm shrink-0"></ion-icon>
                                                <span>Sudah Dipesan</span>
                                            <?php else: ?>
                                                <ion-icon name="time-outline" class="text-sm shrink-0"></ion-icon>
                                                <span>Belum Dipesan</span>
                                            <?php endif; ?>
                                            <ion-icon name="create-outline" class="text-[10px] opacity-40 group-hover:opacity-100 transition-opacity ml-0.5"></ion-icon>
                                        </button>
                                    <?php else: ?>
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold <?= ($spm === 'Tiba di Transit') ? 'bg-indigo-50 text-indigo-700' : (($spm === 'Sudah Dipesan') ? 'bg-sky-50 text-sky-700' : 'bg-slate-100 text-slate-600') ?>">
                                            <span><?= htmlspecialchars($spm) ?></span>
                                        </span>
                                    <?php endif; ?>

                                    <?php if (!empty($dist['kurir_ekspedisi']) || !empty($dist['no_resi'])): ?>
                                        <div class="text-[10px] text-slate-400 font-mono mt-0.5 truncate max-w-[150px]" title="<?= htmlspecialchars(($dist['kurir_ekspedisi'] ? $dist['kurir_ekspedisi'] . ': ' : '') . $dist['no_resi']) ?>">
                                            <?= htmlspecialchars(($dist['kurir_ekspedisi'] ? $dist['kurir_ekspedisi'] . ' ' : '') . ($dist['no_resi'] ?: '')) ?>
                                        </div>
                                    <?php endif; ?>
                                </td>

                                <!-- Status Penerimaan di Lokasi (Interaktif - Klik untuk Popup) -->
                                <td class="py-2 px-3 whitespace-nowrap">
                                    <?php 
                                    $spn = $dist['status_penerimaan'] ?? (($dist['status'] === 'Diterima') ? 'Diterima di Tujuan' : 'Menunggu Barang');
                                    ?>
                                    <?php if ($role === 'admin' || $role === 'user'): ?>
                                        <button type="button" 
                                                onclick='openModalUpdatePenerimaan(<?= json_encode($dist, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)'
                                                class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold transition-all hover:scale-[1.02] shadow-2xs cursor-pointer group <?= ($spn === 'Diterima di Tujuan') ? 'bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-100' : (($spn === 'Dalam Pengantaran ke Tujuan') ? 'bg-blue-50 text-blue-700 border border-blue-200 hover:bg-blue-100' : (($spn === 'Siap Disalurkan / Diambil') ? 'bg-amber-50 text-amber-700 border border-amber-200 hover:bg-amber-100' : 'bg-slate-100 text-slate-600 border border-slate-200 hover:bg-slate-200')) ?>"
                                                title="Klik untuk update status penerimaan & BAST">
                                            <?php if ($spn === 'Diterima di Tujuan'): ?>
                                                <ion-icon name="checkmark-done-circle" class="text-sm text-emerald-600 shrink-0"></ion-icon>
                                                <span>Diterima di Tujuan</span>
                                            <?php elseif ($spn === 'Dalam Pengantaran ke Tujuan'): ?>
                                                <ion-icon name="paper-plane-outline" class="text-sm shrink-0"></ion-icon>
                                                <span>Dalam Pengantaran</span>
                                            <?php elseif ($spn === 'Siap Disalurkan / Diambil'): ?>
                                                <ion-icon name="cube-outline" class="text-sm shrink-0"></ion-icon>
                                                <span>Siap Salur / Ambil</span>
                                            <?php else: ?>
                                                <ion-icon name="hourglass-outline" class="text-sm shrink-0"></ion-icon>
                                                <span>Menunggu Barang</span>
                                            <?php endif; ?>
                                            <ion-icon name="create-outline" class="text-[10px] opacity-40 group-hover:opacity-100 transition-opacity ml-0.5"></ion-icon>
                                        </button>
                                    <?php else: ?>
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold <?= ($spn === 'Diterima di Tujuan') ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-600' ?>">
                                            <span><?= htmlspecialchars($spn) ?></span>
                                        </span>
                                    <?php endif; ?>

                                    <?php if (!empty($dist['tanggal_terima']) && $spn === 'Diterima di Tujuan'): ?>
                                        <div class="text-[10px] text-emerald-600 font-mono mt-0.5">
                                            Tiba: <?= date('d/m/Y', strtotime($dist['tanggal_terima'])) ?>
                                        </div>
                                    <?php endif; ?>
                                </td>

                                <!-- Aksi (Hanya Tombol Hapus - Fitur Edit Distribusi Dihapus) -->
                                <td class="py-2 px-2.5 text-right whitespace-nowrap">
                                    <?php if ($role === 'admin' || $role === 'user'): ?>
                                        <div class="flex items-center justify-end">
                                            <a href="<?= APP_URL ?>/paket/deleteDistribusi/<?= $paket['kode_paket'] ?: $paket['id'] ?>/<?= $dist['id'] ?>" 
                                               data-confirm-delete="<?= htmlspecialchars($dist['nama_tujuan'] . ' (' . $dist['nama_komponen'] . ')') ?>"
                                               data-type="Alokasi Distribusi"
                                               class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors text-sm inline-flex items-center justify-center"
                                               title="Hapus Alokasi">
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

                <!-- Footer Summary Row -->
                <?php if (!empty($distribusiList)): ?>
                    <tfoot class="bg-slate-50/90 font-bold text-slate-700 border-t-2 border-slate-200">
                        <tr>
                            <td colspan="3" class="py-2 px-3 text-right uppercase text-[10px] tracking-wider text-slate-500">
                                Total Barang Dialokasikan:
                            </td>
                            <td class="py-2 px-2.5 text-center font-mono text-xs text-indigo-700 bg-indigo-50/60 font-extrabold">
                                <?= (float)($distribusiSummary['total_qty_dialokasikan'] ?? 0) ?> Unit
                            </td>
                            <td colspan="4" class="py-2 px-3 text-slate-400 text-[10px] font-normal">
                                (Tersebar ke <?= (int)($distribusiSummary['total_tujuan'] ?? 0) ?> titik penerima)
                            </td>
                        </tr>
                    </tfoot>
                <?php endif; ?>
            </table>
        </div>
    </div>
</div>
