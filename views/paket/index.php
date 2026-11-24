<?php
/**
 * ProLedger - Daftar Paket Kegiatan
 * Compact Table dengan kalkulasi DPP (-11% PPN), Pasal 13%, Admin 2%, Total Pasal,
 * serta Pagu Nego (Pagu Akhir hasil negosiasi) dan Popup CRUD
 */
?>

<!-- ========================================== -->
<!-- 1. TOP HEADER & ACTION BUTTONS             -->
<!-- ========================================== -->
<div class="mb-4 flex flex-wrap items-center justify-between gap-3">
    <div>
        <div class="flex items-center gap-2 mb-0.5 flex-wrap">
            <h1 class="text-lg md:text-xl font-extrabold text-slate-900 tracking-tight">Daftar Paket Kegiatan</h1>
            <?php if (!empty($activeCompany)): ?>
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                    <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                    <span><?= htmlspecialchars($activeCompany['nama']) ?></span>
                </span>
            <?php else: ?>
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                    Perusahaan Belum Terdaftar
                </span>
            <?php endif; ?>
            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                <?= count($paket) ?> Paket
            </span>
        </div>
        <p class="text-xs text-slate-400">
            Kelola seluruh paket, pagu nego acuan, perhitungan DPP (-11%), serta potongan pasal & admin yang fleksibel.
        </p>
    </div>

    <div class="flex items-center gap-2">
        <!-- Kelola Group via Popup Modal -->
        <button type="button" 
                data-modal-open="modalManageGroup"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white hover:bg-slate-50 text-slate-700 font-semibold rounded-xl border border-slate-200 transition-all text-xs shadow-2xs hover:shadow-xs">
            <ion-icon name="folder-open-outline" class="text-indigo-600 text-sm"></ion-icon>
            <span>Kelola Group (<?= count($groups) ?>)</span>
        </button>

        <?php if ($role === 'admin' || $role === 'user'): ?>
            <?php if (empty($activeCompany)): ?>
                <!-- Tambah Paket Dinonaktifkan karena Perusahaan Masih Kosong -->
                <button type="button" 
                        onclick="window.ProToast ? ProToast.warning('Perusahaan masih kosong. Silakan buat perusahaan terlebih dahulu.') : alert('Perusahaan masih kosong. Silakan buat perusahaan terlebih dahulu.'); window.location.href='<?= APP_URL ?>/perusahaan?action=create';"
                        class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-500 font-semibold rounded-xl text-xs border border-slate-200 transition-all"
                        title="Perusahaan masih kosong">
                    <ion-icon name="add-circle-outline" class="text-base"></ion-icon>
                    <span>Tambah Paket</span>
                </button>
            <?php else: ?>
                <!-- Tambah Paket via Popup Modal -->
                <button type="button" 
                        data-modal-open="modalTambahPaket"
                        class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl text-xs shadow-sm shadow-blue-600/20 transition-all hover:scale-[1.01] active:scale-[0.98]">
                    <ion-icon name="add-circle-outline" class="text-base"></ion-icon>
                    <span>Tambah Paket</span>
                </button>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<!-- ========================================== -->
<!-- 2. COMPACT SEARCH & FILTER BAR             -->
<!-- ========================================== -->
<div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 p-3 mb-4">
    <div class="flex flex-col md:flex-row items-center justify-between gap-3">
        <!-- Left: Search Box -->
        <div class="relative w-full md:w-72">
            <ion-icon name="search-outline" class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-base"></ion-icon>
            <input type="text" id="searchPaketInput" onkeyup="filterPaketTable()" 
                   placeholder="Cari paket, PJ, group, tag..." 
                   class="w-full pl-9 pr-3 py-1.5 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-blue-500 text-xs focus:bg-white transition-all">
        </div>

        <!-- Right: Inline Filters -->
        <div class="flex items-center gap-2 w-full md:w-auto flex-wrap">
            <!-- Filter Group -->
            <select id="filterGroupSelect" onchange="filterPaketTable()" 
                    class="px-2.5 py-1.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700 focus:outline-none focus:border-blue-500 transition-all cursor-pointer">
                <option value="">Semua Group</option>
                <option value="__none__">Tanpa Group (Mandiri)</option>
                <?php foreach ($groups as $grp): ?>
                    <option value="<?= htmlspecialchars(strtolower($grp['nama_group'])) ?>">
                        <?= htmlspecialchars($grp['nama_group']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <!-- Filter Tahun Anggaran -->
            <select id="filterTahunSelect" onchange="filterPaketTable()" 
                    class="px-2.5 py-1.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700 focus:outline-none focus:border-blue-500 transition-all cursor-pointer">
                <option value="">Semua Tahun</option>
                <?php
                $curY = (int)date('Y');
                $yearsList = [$curY + 1, $curY, $curY - 1];
                foreach ($yearsList as $yr):
                ?>
                    <option value="<?= $yr ?>">TA <?= $yr ?></option>
                <?php endforeach; ?>
            </select>

            <!-- Filter Tag -->
            <?php $allTags = $allTags ?? []; ?>
            <select id="filterTagSelect" onchange="filterPaketTable()" 
                    class="px-2.5 py-1.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700 focus:outline-none focus:border-blue-500 transition-all cursor-pointer">
                <option value="">Semua Tag</option>
                <?php foreach ($allTags as $tg): ?>
                    <option value="<?= htmlspecialchars(strtolower($tg['tag'])) ?>">
                        #<?= htmlspecialchars($tg['tag']) ?> (<?= $tg['count'] ?>)
                    </option>
                <?php endforeach; ?>
            </select>

            <!-- Filter Status Pencairan -->
            <select id="filterStatusSelect" onchange="filterPaketTable()" 
                    class="px-2.5 py-1.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700 focus:outline-none focus:border-blue-500 transition-all cursor-pointer">
                <option value="">Semua Pencairan</option>
                <option value="belum">Belum</option>
                <option value="proses">Proses</option>
                <option value="selesai">Selesai</option>
            </select>

            <!-- Reset Button -->
            <button type="button" onclick="resetPaketFilters()" 
                    class="p-1.5 text-slate-400 hover:text-slate-700 hover:bg-slate-100 rounded-lg transition-colors" title="Reset Filter">
                <ion-icon name="refresh-outline" class="text-base"></ion-icon>
            </button>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- 3. COMPACT TABLE PAKET (PERSIS SPREADSHEET)-->
<!-- ========================================== -->
<div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-xs" id="paketTable">
            <thead class="bg-slate-50 text-[10px] uppercase tracking-wider text-slate-600 font-bold border-b border-slate-200">
                <tr>
                    <th class="py-2.5 px-3 w-16 text-center">KODE</th>
                    <th class="py-2.5 px-3 min-w-[200px]">Nama Paket</th>
                    <th class="py-2.5 px-3 text-right" title="Nilai Hasil Negosiasi (Acuan Utama) & Nilai Kontrak Awal">Nilai Kontrak</th>
                    <th class="py-2.5 px-3 text-right">DPP</th>
                    <th class="py-2.5 px-3 text-right" title="Klik angka untuk Pengaturan Potongan Pasal & Admin">Total Pasal</th>
                    <th class="py-2.5 px-3 text-right" title="Dana Bersih Setelah Potongan (Klik untuk Informasi & Finansial)">Nilai Bersih</th>
                    <th class="py-2.5 px-3 text-right" title="Total Belanja Toko / HPP (Klik untuk Kelola Pemesanan)">HPP</th>
                    <th class="py-2.5 px-3 text-right" title="Estimasi Profit Bersih Setelah Potongan & Belanja (Klik untuk Rincian Profit)">Profit</th>
                    <th class="py-2.5 px-2 text-center" title="Jumlah Macam Item Komponen & Total Unit (Klik untuk Kelola Item)">Komponen</th>
                    <th class="py-2.5 px-2 text-center w-16" title="Progres Pengiriman & Distribusi">Dist</th>
                    <th class="py-2.5 px-2 text-center w-16" title="Progres Pemesanan Barang (Supplier/Toko)">Order</th>
                    <th class="py-2.5 px-2 text-center w-16" title="Progres Foto Dokumentasi Titik Penerima">Dok</th>
                    <th class="py-2.5 px-2 text-center w-16" title="Progres Upload Berkas BAST Titik Penerima">BAST</th>
                    <th class="py-2.5 px-3 text-center">Pencairan</th>
                    <th class="py-2.5 px-3 text-right w-16">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php if (empty($paket)): ?>
                    <tr id="emptyTableRow">
                        <td colspan="15" class="py-12 text-center text-slate-400">
                            <?php if (empty($activeCompany)): ?>
                                <div class="w-12 h-12 bg-amber-50 rounded-2xl flex items-center justify-center mx-auto mb-2 text-2xl text-amber-500">
                                    <ion-icon name="business-outline"></ion-icon>
                                </div>
                                <h4 class="text-xs font-bold text-slate-700 mb-0.5">Perusahaan Masih Kosong</h4>
                                <p class="text-[11px] text-slate-400 mb-3">Anda harus membuat profil perusahaan terlebih dahulu sebelum dapat menambahkan paket kegiatan.</p>
                                <a href="<?= APP_URL ?>/perusahaan?action=create" 
                                   class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-semibold shadow-xs transition-all">
                                    <ion-icon name="add-circle-outline"></ion-icon>
                                    <span>Buat Perusahaan Sekarang</span>
                                </a>
                            <?php else: ?>
                                <div class="w-12 h-12 bg-slate-50 rounded-2xl flex items-center justify-center mx-auto mb-2 text-2xl text-slate-300">
                                    <ion-icon name="cube-outline"></ion-icon>
                                </div>
                                <h4 class="text-xs font-bold text-slate-700 mb-0.5">Belum Ada Paket Kegiatan untuk <?= htmlspecialchars($activeCompany['nama']) ?></h4>
                                <p class="text-[11px] text-slate-400 mb-3">Tambahkan paket kegiatan baru untuk memulai pencatatan proyek perusahaan ini.</p>
                                <?php if ($role === 'admin' || $role === 'user'): ?>
                                    <button type="button" 
                                            data-modal-open="modalTambahPaket"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-semibold shadow-xs transition-all">
                                        <ion-icon name="add-circle-outline"></ion-icon>
                                        <span>Tambah Paket Baru</span>
                                    </button>
                                <?php endif; ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php 
                    $sumKontrak = 0; $sumDpp = 0; $sumPasal = 0; $sumBersih = 0;
                    $sumHpp = 0; $sumProfit = 0; $sumItems = 0; $sumUnits = 0;
                    foreach ($paket as $p): 
                        $itemTotal = (float)($p['total_item_harga'] ?? 0);
                        $effectivePagu = ($itemTotal > 0) ? $itemTotal : (float)$p['pagu'];
                        $hasNego = (!empty($p['pagu_nego']) && (float)$p['pagu_nego'] > 0);
                        $acuan = $hasNego ? (float)$p['pagu_nego'] : $effectivePagu;
                        $tipePotongan = $p['tipe_potongan'] ?? 'dpp';
                        $dpp = calculateDPP($acuan);
                        $basis = ($tipePotongan === 'kontrak') ? $acuan : $dpp;
                        
                        $persenPasal = isset($p['persen_pasal']) && $p['persen_pasal'] !== '' ? (float)$p['persen_pasal'] : 13.0;
                        $persenAdmin = isset($p['persen_admin']) && $p['persen_admin'] !== '' ? (float)$p['persen_admin'] : 2.0;
                        $totalPersen = $persenPasal + $persenAdmin;

                        $pasalVal = calculatePasal($basis, $persenPasal);
                        $adminVal = calculateAdmin($basis, $persenAdmin);
                        $totalPasal = $pasalVal + $adminVal;
                        $nilaiBersih = $dpp - $totalPasal;
                        $totalBelanja = (float)($p['total_belanja'] ?? 0);
                        $estimasiProfit = $nilaiBersih - $totalBelanja;
                        $profitPct = ($acuan > 0) ? round(($estimasiProfit / $acuan) * 100, 1) : 0;
                        $jmlItem = (int)($p['total_item_count'] ?? 0);
                        $jmlUnit = (float)($p['total_item_qty'] ?? 0);

                        $sumKontrak += $acuan;
                        $sumDpp += $dpp;
                        $sumPasal += $totalPasal;
                        $sumBersih += $nilaiBersih;
                        $sumHpp += $totalBelanja;
                        $sumProfit += $estimasiProfit;
                        $sumItems += $jmlItem;
                        $sumUnits += $jmlUnit;

                        $rawSt = strtolower($p['status'] ?? 'proses');
                        $normStatus = ($rawSt === 'cair') ? 'selesai' : $rawSt;
                    ?>
                        <tr class="hover:bg-slate-50/70 transition-colors group"
                            data-group-name="<?= htmlspecialchars(strtolower($p['nama_group'] ?? '')) ?>"
                            data-tags="<?= htmlspecialchars(strtolower($p['tags'] ?? '')) ?>"
                            data-tahun="<?= htmlspecialchars((string)($p['tahun_anggaran'] ?? date('Y'))) ?>"
                            data-progres="<?= htmlspecialchars(strtolower($p['progres_barang'] ?? 'belum')) ?>"
                            data-status="<?= htmlspecialchars($normStatus) ?>"
                            data-kontrak="<?= $acuan ?>"
                            data-dpp="<?= $dpp ?>"
                            data-pasal="<?= $totalPasal ?>"
                            data-bersih="<?= $nilaiBersih ?>"
                            data-hpp="<?= $totalBelanja ?>"
                            data-profit="<?= $estimasiProfit ?>"
                            data-items="<?= $jmlItem ?>"
                            data-units="<?= $jmlUnit ?>">
                            
                            <!-- KODE (5-digit) -->
                            <td class="py-2.5 px-3 text-center">
                                <span class="px-2 py-0.5 font-mono font-bold text-[11px] bg-slate-100 text-slate-700 rounded-md tracking-wider">
                                    <?= htmlspecialchars($p['kode_paket'] ?: '#' . $p['id']) ?>
                                </span>
                            </td>

                            <!-- Nama Paket & Group Badge & Tags (Satu Baris Sebelahan) -->
                            <td class="py-2.5 px-3">
                                <div>
                                    <a href="javascript:void(0)" 
                                       onclick="openPaketModalTab('<?= $p['kode_paket'] ?: $p['id'] ?>', 'info', '<?= htmlspecialchars(addslashes($p['nama_paket'])) ?>')" 
                                       class="font-bold text-slate-800 hover:text-blue-600 text-xs transition-colors cursor-pointer"
                                       title="Klik untuk buka Informasi & Rincian Paket">
                                        <?= htmlspecialchars($p['nama_paket']) ?>
                                    </a>
                                </div>
                                <?php if (!empty($p['nama_group']) || !empty($p['tags']) || !empty($p['tahun_anggaran'])): ?>
                                    <div class="flex items-center gap-1.5 flex-wrap mt-1">
                                        <?php if (!empty($p['tahun_anggaran'])): ?>
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded-md bg-amber-50 text-amber-800 font-bold text-[9px] border border-amber-200" title="Tahun Anggaran <?= $p['tahun_anggaran'] ?>">
                                                TA <?= htmlspecialchars($p['tahun_anggaran']) ?>
                                            </span>
                                        <?php endif; ?>
                                        <?php if (!empty($p['nama_group'])): ?>
                                            <a href="<?= APP_URL ?>/group/detail/<?= $p['group_id'] ?>" 
                                                class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-md bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-semibold text-[9px] border border-indigo-200 transition-colors"
                                                title="Group: <?= htmlspecialchars($p['nama_group']) ?>">
                                                <ion-icon name="folder-outline"></ion-icon>
                                                <span><?= htmlspecialchars($p['nama_group']) ?></span>
                                            </a>
                                        <?php endif; ?>
                                        <?php if (!empty($p['tags'])): 
                                            $tagList = array_map('trim', explode(',', $p['tags']));
                                            foreach ($tagList as $tg): if ($tg === '') continue;
                                        ?>
                                            <button type="button" onclick="filterByTag('<?= htmlspecialchars(strtolower($tg)) ?>')" 
                                                    class="inline-flex items-center gap-0.5 px-1.5 py-0.5 rounded-md bg-slate-100 hover:bg-blue-50 hover:text-blue-600 text-slate-500 font-medium text-[9px] border border-slate-200/60 transition-colors"
                                                    title="Filter tag: #<?= htmlspecialchars($tg) ?>">
                                                <span>#<?= htmlspecialchars($tg) ?></span>
                                            </button>
                                        <?php endforeach; endif; ?>
                                    </div>
                                <?php endif; ?>
                            </td>

                            <!-- Nilai Kontrak (Pagu Nego di Atas, Pagu Awal Dicoret di Bawah) -->
                            <td class="py-2.5 px-3 text-right whitespace-nowrap">
                                <!-- Pagu Nego: Ditampilkan di Atas (Acuan Utama) -->
                                <div class="font-mono font-bold text-slate-800 text-xs leading-5" title="Nilai Hasil Negosiasi (Acuan Utama): <?= formatRupiah($acuan) ?>">
                                    <?= formatRupiah($acuan) ?>
                                </div>
                                <!-- Pagu Awal: Grey & Coretan di Tengah Text di Bawah -->
                                <div class="font-mono text-slate-400 line-through text-[11px] leading-4" title="Nilai Kontrak / Pagu Awal">
                                    <?= formatRupiah($effectivePagu) ?>
                                </div>
                            </td>

                            <!-- DPP (-11% PPN dari Nilai Nego) -->
                            <td class="py-2.5 px-3 text-right whitespace-nowrap">
                                <div class="font-mono font-bold text-slate-800 text-xs leading-5" title="DPP dihitung dari Nilai Nego / 1.11">
                                    <?= formatRupiah($dpp) ?>
                                </div>
                                <div class="mt-0.5 min-h-[16px] flex items-center justify-end">
                                    <span class="inline-flex items-center px-1.5 py-0.2 rounded text-[9px] font-bold bg-slate-100 text-slate-600 border border-slate-200" title="Dasar Pengenaan Pajak (-11% PPN dari Nego)">
                                        DPP -11%
                                    </span>
                                </div>
                            </td>

                            <!-- Total Pasal (Klik untuk Buka Popup Pengaturan Pasal) -->
                            <td class="py-2.5 px-3 text-right whitespace-nowrap">
                                <?php if ($role === 'admin' || $role === 'user'): ?>
                                    <button type="button"
                                            onclick='openIndexSkemaPotonganModal(<?= json_encode($p, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)'
                                            class="text-right group/pasal hover:bg-amber-50/80 px-2 py-1 -my-1 -mx-2 rounded-lg transition-all cursor-pointer inline-block"
                                            title="Klik untuk Pengaturan Potongan Pasal & Admin">
                                        <div class="font-mono font-bold text-slate-800 group-hover/pasal:text-amber-800 text-xs leading-5">
                                            <?= formatRupiah($totalPasal) ?>
                                        </div>
                                        <div class="mt-0.5 min-h-[16px] flex items-center justify-end">
                                            <span class="inline-flex items-center px-1.5 py-0.2 rounded text-[9px] font-bold bg-amber-100 text-amber-800 border border-amber-300/80">
                                                <?= (float)$totalPersen ?>%
                                            </span>
                                        </div>
                                    </button>
                                <?php else: ?>
                                    <div class="font-mono font-bold text-slate-800 text-xs leading-5">
                                        <?= formatRupiah($totalPasal) ?>
                                    </div>
                                    <div class="mt-0.5 min-h-[16px] flex items-center justify-end">
                                        <span class="inline-flex items-center px-1.5 py-0.2 rounded text-[9px] font-bold bg-amber-100 text-amber-800 border border-amber-300/80">
                                            <?= (float)$totalPersen ?>%
                                        </span>
                                    </div>
                                <?php endif; ?>
                            </td>

                            <!-- Nilai Bersih (Klik untuk Buka Popup Informasi & Finansial) -->
                            <td class="py-2.5 px-3 text-right whitespace-nowrap">
                                <button type="button"
                                        onclick="openPaketModalTab('<?= $p['kode_paket'] ?: $p['id'] ?>', 'info', '<?= htmlspecialchars(addslashes($p['nama_paket'])) ?>')"
                                        class="text-right group/bersih hover:bg-emerald-50/80 px-2 py-1 -my-1 -mx-2 rounded-lg transition-all cursor-pointer inline-block"
                                        title="Klik untuk buka Popup Informasi & Finansial Paket">
                                    <div class="font-mono font-bold text-emerald-700 group-hover/bersih:text-emerald-800 text-xs leading-5">
                                        <?= formatRupiah($nilaiBersih) ?>
                                    </div>
                                    <div class="mt-0.5 min-h-[16px] flex items-center justify-end">
                                        <span class="inline-flex items-center gap-0.5 px-1.5 py-0.2 rounded text-[9px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/80">
                                            <ion-icon name="wallet-outline" class="text-[10px]"></ion-icon>
                                            <span>Info & Finansial</span>
                                        </span>
                                    </div>
                                </button>
                            </td>

                            <!-- Belanja / HPP (Klik untuk Buka Popup Pemesanan) -->
                            <td class="py-2.5 px-3 text-right whitespace-nowrap">
                                <button type="button"
                                        onclick="openPaketModalTab('<?= $p['kode_paket'] ?: $p['id'] ?>', 'pemesanan', '<?= htmlspecialchars(addslashes($p['nama_paket'])) ?>')"
                                        class="text-right group/belanja hover:bg-amber-50/80 px-2 py-1 -my-1 -mx-2 rounded-lg transition-all cursor-pointer inline-block"
                                        title="Total Belanja Toko (HPP) • Klik untuk buka Pemesanan">
                                    <div class="font-mono font-bold text-slate-700 group-hover/belanja:text-amber-700 text-xs leading-5">
                                        <?= $totalBelanja > 0 ? formatRupiah($totalBelanja) : '-' ?>
                                    </div>
                                    <div class="mt-0.5 min-h-[16px] flex items-center justify-end">
                                        <span class="inline-flex items-center px-1.5 py-0.2 rounded text-[9px] font-semibold bg-slate-100 text-slate-500 border border-slate-200/80">
                                            HPP Toko
                                        </span>
                                    </div>
                                </button>
                            </td>

                            <!-- Estimasi Profit (Klik untuk Buka Popup Profit) -->
                            <td class="py-2.5 px-3 text-right whitespace-nowrap">
                                <?php $isProfitLoss = ($estimasiProfit < 0); ?>
                                <button type="button"
                                        onclick="openPaketModalTab('<?= $p['kode_paket'] ?: $p['id'] ?>', 'profit', '<?= htmlspecialchars(addslashes($p['nama_paket'])) ?>')"
                                        class="text-right group/profit hover:bg-emerald-50/80 px-2 py-1 -my-1 -mx-2 rounded-lg transition-all cursor-pointer inline-block"
                                        title="Estimasi Profit Bersih (Nilai Bersih - Belanja) • Klik untuk buka Rincian Profit">
                                    <div class="font-mono font-bold <?= $isProfitLoss ? 'text-rose-600' : 'text-emerald-700 group-hover/profit:text-emerald-800' ?> text-xs leading-5">
                                        <?= formatRupiah($estimasiProfit) ?>
                                    </div>
                                    <div class="mt-0.5 min-h-[16px] flex items-center justify-end">
                                        <span class="inline-flex items-center px-1.5 py-0.2 rounded text-[9px] font-bold font-mono border <?= $isProfitLoss ? 'bg-rose-50 text-rose-700 border-rose-200' : 'bg-emerald-50 text-emerald-700 border-emerald-200/80' ?>">
                                            <?= ($profitPct > 0 ? '+' : '') . $profitPct ?>%
                                        </span>
                                    </div>
                                </button>
                            </td>

                            <!-- Jumlah Item Komponen (Klik untuk Buka Popup Item) -->
                            <td class="py-2.5 px-2 text-center whitespace-nowrap">
                                <?php 
                                $jmlItem = (int)($p['total_item_count'] ?? 0);
                                $jmlUnit = (float)($p['total_item_qty'] ?? 0);
                                ?>
                                <button type="button"
                                        onclick="openPaketModalTab('<?= $p['kode_paket'] ?: $p['id'] ?>', 'items', '<?= htmlspecialchars(addslashes($p['nama_paket'])) ?>')"
                                        class="group/item hover:bg-blue-50/80 px-2 py-1 -my-1 rounded-lg transition-all cursor-pointer inline-flex flex-col items-center"
                                        title="Klik untuk buka Popup Komponen Item Barang">
                                    <span class="font-normal text-slate-700 group-hover/item:text-blue-700 text-xs leading-5">
                                        <?= $jmlItem ?> Item
                                    </span>
                                    <span class="inline-flex items-center px-1.5 py-0.2 rounded text-[9px] font-semibold bg-slate-100 text-slate-600 border border-slate-200/80 mt-0.5">
                                        <?= number_format($jmlUnit, 0, ',', '.') ?> Unit
                                    </span>
                                </button>
                            </td>

                            <!-- Progres Distribusi (Lingkaran - Klik untuk Buka Popup) -->
                            <td class="py-2 px-2 text-center whitespace-nowrap">
                                <button type="button" 
                                        onclick="openPaketModalTab('<?= $p['kode_paket'] ?: $p['id'] ?>', 'distribusi', '<?= htmlspecialchars(addslashes($p['nama_paket'])) ?>')" 
                                        class="hover:scale-105 transition-transform inline-block cursor-pointer">
                                    <?= renderCircularProgress($p['dist_done'] ?? 0, $p['total_dist'] ?? 0, 'distribusi') ?>
                                </button>
                            </td>

                            <!-- Progres Pemesanan (Lingkaran - Klik untuk Buka Popup) -->
                            <td class="py-2 px-2 text-center whitespace-nowrap">
                                <button type="button" 
                                        onclick="openPaketModalTab('<?= $p['kode_paket'] ?: $p['id'] ?>', 'pemesanan', '<?= htmlspecialchars(addslashes($p['nama_paket'])) ?>')" 
                                        class="hover:scale-105 transition-transform inline-block cursor-pointer">
                                    <?= renderCircularProgress($p['pesan_done'] ?? 0, $p['total_dist'] ?? 0, 'pemesanan') ?>
                                </button>
                            </td>

                            <!-- Progres Dokumentasi (Lingkaran - Klik untuk Buka Popup) -->
                            <td class="py-2 px-2 text-center whitespace-nowrap">
                                <button type="button" 
                                        onclick="openPaketModalTab('<?= $p['kode_paket'] ?: $p['id'] ?>', 'dokumentasi', '<?= htmlspecialchars(addslashes($p['nama_paket'])) ?>')" 
                                        class="hover:scale-105 transition-transform inline-block cursor-pointer">
                                    <?= renderCircularProgress($p['dok_done'] ?? 0, $p['total_dist'] ?? 0, 'dokumentasi') ?>
                                </button>
                            </td>

                            <!-- Progres BAST (Lingkaran - Klik untuk Buka Popup Berkas BAST) -->
                            <td class="py-2 px-2 text-center whitespace-nowrap">
                                <button type="button" 
                                        onclick="openPaketModalTab('<?= $p['kode_paket'] ?: $p['id'] ?>', 'dokumen', '<?= htmlspecialchars(addslashes($p['nama_paket'])) ?>')" 
                                        class="hover:scale-105 transition-transform inline-block cursor-pointer">
                                    <?= renderCircularProgress($p['bast_done'] ?? 0, $p['total_bast'] ?? 0, 'bast') ?>
                                </button>
                            </td>

                            <!-- Pencairan (Belum / Proses / Selesai) -->
                            <td class="py-2.5 px-3 text-center whitespace-nowrap">
                                <?php if ($role === 'admin' || $role === 'user'): ?>
                                    <button type="button" 
                                            onclick='openStatusPencairanModal(<?= json_encode([
                                                "id" => $p["id"],
                                                "kode_paket" => $p["kode_paket"],
                                                "nama_paket" => $p["nama_paket"],
                                                "status" => ucfirst($normStatus)
                                            ], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)'
                                            class="inline-flex items-center group/status hover:scale-105 transition-transform cursor-pointer"
                                            title="Klik untuk ubah status pencairan">
                                        <?= getStatusBadge($p['status'] ?? 'Proses') ?>
                                    </button>
                                <?php else: ?>
                                    <?= getStatusBadge($p['status'] ?? 'Proses') ?>
                                <?php endif; ?>
                            </td>

                            <!-- Aksi (Dropdown Titik Tiga ⋮) -->
                            <td class="py-2.5 px-3 text-right whitespace-nowrap">
                                <button type="button" 
                                        onclick='toggleActionMenu(event, <?= json_encode($p, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)'
                                        class="action-menu-trigger w-8 h-8 text-slate-500 hover:text-slate-800 hover:bg-slate-100 rounded-xl transition-all inline-flex items-center justify-center cursor-pointer text-lg shadow-2xs hover:shadow-xs active:scale-95"
                                        title="Menu Pilihan & Rincian Paket">
                                    <ion-icon name="ellipsis-vertical"></ion-icon>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
            <?php if (!empty($paket)): ?>
                <?php 
                $sumProfitPct = ($sumKontrak > 0) ? round(($sumProfit / $sumKontrak) * 100, 1) : 0;
                $isSumLoss = ($sumProfit < 0);
                ?>
                <tfoot id="paketTableFooter" class="bg-slate-50/95 font-bold text-slate-800 border-t-2 border-slate-200 text-xs">
                    <tr>
                        <td colspan="2" class="py-3 px-3 align-top">
                            <span class="text-slate-500 font-semibold uppercase text-[10px] tracking-wider">Overall Total</span>
                            <span class="ml-1.5 px-2 py-0.5 rounded-md bg-slate-200/80 text-slate-700 text-[10px] font-bold">
                                <span id="totalPaketCount"><?= count($paket) ?></span> Paket
                            </span>
                        </td>
                        <td class="py-3 px-3 align-top text-right whitespace-nowrap font-mono text-slate-900" id="totalKontrakVal">
                            <?= formatRupiah($sumKontrak) ?>
                        </td>
                        <td class="py-3 px-3 align-top text-right whitespace-nowrap font-mono text-slate-700" id="totalDppVal">
                            <?= formatRupiah($sumDpp) ?>
                        </td>
                        <td class="py-3 px-3 align-top text-right whitespace-nowrap font-mono text-amber-700" id="totalPasalVal">
                            <?= formatRupiah($sumPasal) ?>
                        </td>
                        <td class="py-3 px-3 align-top text-right whitespace-nowrap font-mono text-emerald-700" id="totalBersihVal">
                            <?= formatRupiah($sumBersih) ?>
                        </td>
                        <td class="py-3 px-3 align-top text-right whitespace-nowrap font-mono text-slate-700" id="totalHppVal">
                            <?= formatRupiah($sumHpp) ?>
                        </td>
                        <td class="py-3 px-3 align-top text-right whitespace-nowrap">
                            <div class="font-mono font-extrabold <?= $isSumLoss ? 'text-rose-600' : 'text-emerald-700' ?>" id="totalProfitVal">
                                <?= formatRupiah($sumProfit) ?>
                            </div>
                            <span id="totalProfitPct" class="inline-flex items-center px-1.5 py-0.2 rounded text-[9px] font-bold font-mono border mt-0.5 <?= $isSumLoss ? 'bg-rose-50 text-rose-700 border-rose-200' : 'bg-emerald-50 text-emerald-700 border-emerald-200/80' ?>">
                                <?= ($sumProfitPct > 0 ? '+' : '') . $sumProfitPct ?>%
                            </span>
                        </td>
                        <td class="py-3 px-2 align-top text-center whitespace-nowrap">
                            <div class="font-semibold text-slate-700" id="totalItemCount"><?= number_format($sumItems, 0, ',', '.') ?> Item</div>
                            <div class="text-[10px] font-normal text-slate-500 mt-0.5" id="totalUnitCount"><?= number_format($sumUnits, 0, ',', '.') ?> Unit</div>
                        </td>
                        <td colspan="6" class="py-3 px-3 align-top text-right text-[11px] font-normal text-slate-400">
                            -
                        </td>
                    </tr>
                </tfoot>
            <?php endif; ?>
        </table>
    </div>
</div>

<!-- Datalist untuk Penanggung Jawab Sesuai Spreadsheet -->
<datalist id="list_pj">
    <option value="RAHMAN">
    <option value="RIFKI">
    <option value="DARMAN">
    <option value="ASHARI">
    <option value="SULIS">
    <option value="RIZAL">
</datalist>

<!-- ========================================== -->
<!-- MODAL 1: TAMBAH PAKET KEGIATAN (POPUP)     -->
<!-- ========================================== -->
<div id="modalTambahPaket" class="app-modal fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 transition-all duration-200 opacity-0 pointer-events-none hidden">
    <div class="modal-card bg-white rounded-2xl shadow-2xl border border-slate-100 max-w-lg w-full transform scale-95 opacity-0 transition-all duration-200 overflow-hidden max-h-[92vh] flex flex-col">
        <!-- Header -->
        <div class="px-5 py-3.5 bg-slate-50/80 border-b border-slate-100 flex items-center justify-between shrink-0">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-blue-600 text-white flex items-center justify-center text-lg shadow-xs">
                    <ion-icon name="cube-outline"></ion-icon>
                </div>
                <div>
                    <h3 class="font-bold text-slate-800 text-sm">Tambah Paket Kegiatan</h3>
                    <p class="text-[11px] text-slate-400">Input rincian paket, nilai kontrak, DPP, dan negosiasi</p>
                </div>
            </div>
            <button type="button" data-modal-close="modalTambahPaket" class="p-1.5 text-slate-400 hover:text-slate-700 rounded-lg transition-colors">
                <ion-icon name="close-outline" class="text-xl"></ion-icon>
            </button>
        </div>

        <!-- Body Form -->
        <form method="POST" action="<?= APP_URL ?>/paket/store" class="p-5 overflow-y-auto space-y-3.5 flex-1">
            <?= csrfField() ?>
            <!-- Nilai default saat paket baru dibuat (dikelola lebih lanjut di Edit Paket) -->
            <input type="hidden" name="penanggung_jawab" value="">
            <input type="hidden" name="progres_barang" value="Belum">
            <input type="hidden" name="status" value="Proses">
            <input type="hidden" name="jumlah_pemodal" id="tambah_jumlah_pemodal" value="0">

            <!-- Nama Paket -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Paket Kegiatan <span class="text-red-500">*</span></label>
                <input type="text" name="nama_paket" required 
                       class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-blue-500 text-xs focus:bg-white transition-all"
                       placeholder="Contoh: Belanja Bahan-Isi Tabung Pemadam Kebakaran">
            </div>

            <!-- Nomor Surat Pesanan -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">
                    Nomor Surat Pesanan (SPK) <span class="text-[10px] text-slate-400 font-normal">(Opsional, tampil di BAST)</span>
                </label>
                <input type="text" name="nomor_sp"
                       class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-blue-500 text-xs font-mono focus:bg-white transition-all"
                       placeholder="Contoh: EP-01M1ZS80DH32WZR9NZ8JCWCA44">
            </div>

            <!-- Tags & Kota/Kabupaten Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">
                        Tags Paket <span class="text-[10px] text-slate-400 font-normal">(Opsional)</span>
                    </label>
                    <div class="relative">
                        <ion-icon name="pricetags-outline" class="absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></ion-icon>
                        <input type="text" name="tags" placeholder="Contoh: Disdik, APBD-P, SMA"
                               class="w-full pl-8 pr-2.5 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-blue-500 text-xs focus:bg-white transition-all">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Kota / Kabupaten</label>
                    <select name="kab_kota" id="tambah_kab_kota"
                            class="w-full px-2.5 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-blue-500 text-xs focus:bg-white transition-all cursor-pointer">
                        <option value="">-- Semua / Pilih Kota --</option>
                        <?php if (!empty($kabupatenList)): ?>
                            <?php foreach ($kabupatenList as $kab): ?>
                                <option value="<?= htmlspecialchars($kab['nama']) ?>"><?= htmlspecialchars($kab['nama']) ?></option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
            </div>

            <!-- Perusahaan, Group & Tahun Anggaran Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Perusahaan</label>
                    <div class="flex items-center gap-1.5 px-2.5 py-2 bg-slate-100/90 border border-slate-200 rounded-xl text-xs">
                        <ion-icon name="business" class="text-blue-600 text-sm shrink-0"></ion-icon>
                        <span class="font-bold text-slate-800 truncate"><?= htmlspecialchars($activeCompany['nama'] ?? 'Belum Ada') ?></span>
                    </div>
                    <input type="hidden" name="perusahaan_id" value="<?= $activeCompany['id'] ?? '' ?>">
                </div>

                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="block text-xs font-semibold text-slate-700">Group Paket</label>
                    </div>
                    <select name="group_id" id="tambah_group_id" 
                            class="w-full px-2.5 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-blue-500 text-xs focus:bg-white transition-all cursor-pointer">
                        <option value="">-- Mandiri --</option>
                        <?php foreach ($groups as $grp): ?>
                            <option value="<?= $grp['id'] ?>"><?= htmlspecialchars($grp['nama_group']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Tahun Anggaran</label>
                    <select name="tahun_anggaran" id="tambah_tahun_anggaran" 
                            class="w-full px-2.5 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-blue-500 text-xs focus:bg-white transition-all cursor-pointer font-bold text-slate-800">
                        <?php
                        $curY = (int)date('Y');
                        $optYears = [$curY - 1, $curY, $curY + 1];
                        foreach ($optYears as $y):
                        ?>
                            <option value="<?= $y ?>" <?= ($y === $curY) ? 'selected' : '' ?>><?= $y ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- Financials Grid: Pagu Awal & Pagu Nego (Wajib) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <!-- Pagu Awal -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Nilai Kontrak / Pagu Awal <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <span class="absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-400 font-semibold text-xs">Rp</span>
                        <input type="text" name="pagu" id="tambah_pagu" required oninput="formatRupiahInput(this)"
                               class="w-full pl-8 pr-2.5 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-blue-500 text-xs font-mono font-medium focus:bg-white transition-all"
                               placeholder="15.584.400">
                    </div>
                </div>

                <!-- Pagu Akhir / Nego -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">
                        Pagu Nego (Hasil Negosiasi) <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-400 font-semibold text-xs">Rp</span>
                        <input type="text" name="pagu_nego" id="tambah_pagu_nego" required oninput="formatRupiahInput(this)"
                               class="w-full pl-8 pr-2.5 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-emerald-500 text-xs font-mono font-medium focus:bg-white transition-all text-emerald-700 font-bold"
                               placeholder="14.850.000">
                    </div>
                    <p class="text-[10px] text-emerald-600 font-medium mt-1 flex items-center gap-1">
                        <ion-icon name="checkmark-circle-outline"></ion-icon>
                        <span>Acuan utama DPP & potongan pasal</span>
                    </p>
                </div>
            </div>

            <!-- Footer -->
            <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2 shrink-0">
                <button type="button" data-modal-close="modalTambahPaket" 
                        class="px-3.5 py-2 rounded-xl border border-slate-200 text-slate-600 font-semibold hover:bg-slate-50 text-xs transition-colors">
                    Batal
                </button>
                <button type="submit" 
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl text-xs shadow-xs transition-all flex items-center gap-1.5">
                    <ion-icon name="checkmark-outline" class="text-sm"></ion-icon>
                    <span>Simpan Paket</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================== -->
<!-- MODAL 2: EDIT PAKET KEGIATAN (POPUP)       -->
<!-- ========================================== -->
<div id="modalEditPaket" class="app-modal fixed inset-0 z-[70] bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 transition-all duration-200 opacity-0 pointer-events-none hidden">
    <div class="modal-card bg-white rounded-2xl shadow-2xl border border-slate-100 max-w-lg w-full transform scale-95 opacity-0 transition-all duration-200 overflow-hidden max-h-[92vh] flex flex-col">
        <!-- Header -->
        <div class="px-5 py-3.5 bg-slate-50/80 border-b border-slate-100 flex items-center justify-between shrink-0">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-amber-500 text-white flex items-center justify-center text-lg shadow-xs">
                    <ion-icon name="create-outline"></ion-icon>
                </div>
                <div>
                    <h3 class="font-bold text-slate-800 text-sm">Edit Paket Kegiatan</h3>
                    <p class="text-[11px] text-slate-400" id="editPaketSubtitle">Perbarui rincian paket kegiatan</p>
                </div>
            </div>
            <button type="button" data-modal-close="modalEditPaket" class="p-1.5 text-slate-400 hover:text-slate-700 rounded-lg transition-colors">
                <ion-icon name="close-outline" class="text-xl"></ion-icon>
            </button>
        </div>

        <!-- Body Form -->
        <form id="formEditPaket" method="POST" action="" class="p-5 overflow-y-auto space-y-3.5 flex-1">
            <?= csrfField() ?>
            <input type="hidden" name="jumlah_pemodal" id="edit_jumlah_pemodal" value="0">
            <input type="hidden" name="redirect_to" value="/paket">

            <!-- Nama Paket -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Paket Kegiatan <span class="text-red-500">*</span></label>
                <input type="text" id="edit_nama_paket" name="nama_paket" required 
                       class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-blue-500 text-xs focus:bg-white transition-all">
            </div>

            <!-- Nomor Surat Pesanan -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">
                    Nomor Surat Pesanan (SPK) <span class="text-[10px] text-slate-400 font-normal">(Opsional, tampil di BAST)</span>
                </label>
                <input type="text" id="edit_nomor_sp" name="nomor_sp"
                       class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-blue-500 text-xs font-mono focus:bg-white transition-all"
                       placeholder="Contoh: EP-01M1ZS80DH32WZR9NZ8JCWCA44">
            </div>

            <!-- Tags & Kota/Kabupaten Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">
                        Tags Paket <span class="text-[10px] text-slate-400 font-normal">(Opsional)</span>
                    </label>
                    <div class="relative">
                        <ion-icon name="pricetags-outline" class="absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></ion-icon>
                        <input type="text" name="tags" id="edit_tags" placeholder="Contoh: Disdik, APBD-P, SMA"
                               class="w-full pl-8 pr-2.5 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-blue-500 text-xs focus:bg-white transition-all">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Kota / Kabupaten</label>
                    <select name="kab_kota" id="edit_kab_kota"
                            class="w-full px-2.5 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-blue-500 text-xs focus:bg-white transition-all cursor-pointer">
                        <option value="">-- Semua / Pilih Kota --</option>
                        <?php if (!empty($kabupatenList)): ?>
                            <?php foreach ($kabupatenList as $kab): ?>
                                <option value="<?= htmlspecialchars($kab['nama']) ?>"><?= htmlspecialchars($kab['nama']) ?></option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
            </div>

            <!-- Perusahaan, Group & Tahun Anggaran Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Perusahaan</label>
                    <div class="flex items-center gap-1.5 px-2.5 py-2 bg-slate-100/90 border border-slate-200 rounded-xl text-xs">
                        <ion-icon name="business" class="text-blue-600 text-sm shrink-0"></ion-icon>
                        <span id="edit_perusahaan_nama" class="font-bold text-slate-800 truncate">-</span>
                    </div>
                    <input type="hidden" name="perusahaan_id" id="edit_perusahaan_id" value="">
                </div>

                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="block text-xs font-semibold text-slate-700">Group Paket</label>
                    </div>
                    <select name="group_id" id="edit_group_id" 
                            class="w-full px-2.5 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-blue-500 text-xs focus:bg-white transition-all cursor-pointer">
                        <option value="">-- Mandiri --</option>
                        <?php foreach ($groups as $grp): ?>
                            <option value="<?= $grp['id'] ?>"><?= htmlspecialchars($grp['nama_group']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Tahun Anggaran</label>
                    <select name="tahun_anggaran" id="edit_tahun_anggaran" 
                            class="w-full px-2.5 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-blue-500 text-xs focus:bg-white transition-all cursor-pointer font-bold text-slate-800">
                        <?php
                        $curY = (int)date('Y');
                        $optYears = [$curY - 1, $curY, $curY + 1];
                        foreach ($optYears as $y):
                        ?>
                            <option value="<?= $y ?>"><?= $y ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- Financials Grid: Pagu Awal & Pagu Nego (Wajib) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <!-- Pagu Awal -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Nilai Kontrak / Pagu Awal <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <span class="absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-400 font-semibold text-xs">Rp</span>
                        <input type="text" name="pagu" id="edit_pagu" required oninput="formatRupiahInput(this)"
                               class="w-full pl-8 pr-2.5 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-blue-500 text-xs font-mono font-medium focus:bg-white transition-all">
                    </div>
                </div>

                <!-- Pagu Akhir / Nego -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">
                        Pagu Nego (Hasil Negosiasi) <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-400 font-semibold text-xs">Rp</span>
                        <input type="text" name="pagu_nego" id="edit_pagu_nego" required oninput="formatRupiahInput(this)"
                               class="w-full pl-8 pr-2.5 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-emerald-500 text-xs font-mono font-medium focus:bg-white transition-all text-emerald-700 font-bold"
                               placeholder="14.850.000">
                    </div>
                    <p class="text-[10px] text-emerald-600 font-medium mt-1 flex items-center gap-1">
                        <ion-icon name="checkmark-circle-outline"></ion-icon>
                        <span>Acuan utama DPP & potongan pasal</span>
                    </p>
                </div>
            </div>

            <!-- Footer -->
            <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2 shrink-0">
                <button type="button" data-modal-close="modalEditPaket" 
                        class="px-3.5 py-2 rounded-xl border border-slate-200 text-slate-600 font-semibold hover:bg-slate-50 text-xs transition-colors">
                    Batal
                </button>
                <button type="submit" 
                        class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white font-semibold rounded-xl text-xs shadow-xs transition-all flex items-center gap-1.5">
                    <ion-icon name="checkmark-outline" class="text-sm"></ion-icon>
                    <span>Simpan Perubahan</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================== -->
<!-- MODAL: SKEMA POTONGAN (POPUP MANDIRI INDEX)-->
<!-- ========================================== -->
<div id="modalSkemaPotongan" class="app-modal fixed inset-0 z-[70] bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 transition-all duration-200 opacity-0 pointer-events-none hidden">
    <div class="modal-card bg-white rounded-2xl shadow-2xl border border-slate-100 max-w-xl w-full transform scale-95 opacity-0 transition-all duration-200 overflow-hidden max-h-[92vh] flex flex-col">
        <div class="px-5 py-3.5 bg-slate-50 border-b border-slate-100 flex items-center justify-between shrink-0">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-amber-500 text-white flex items-center justify-center text-base shadow-xs">
                    <ion-icon name="options-outline"></ion-icon>
                </div>
                <div>
                    <h3 class="font-bold text-slate-800 text-xs">Skema Potongan</h3>
                    <p class="text-[10px] text-slate-400" id="potongan_paket_subtitle">Atur potongan pasal, admin fee, dan dasar perhitungan pajak/DPP</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <button type="button" onclick="openQuickAddSkema('potongan')" class="text-xs text-amber-600 hover:text-amber-700 font-semibold flex items-center gap-1 hover:underline">
                    <ion-icon name="add-circle-outline"></ion-icon>
                    <span>Skema Baru</span>
                </button>
                <button type="button" data-modal-close="modalSkemaPotongan" class="p-1 text-slate-400 hover:text-slate-700 rounded-lg">
                    <ion-icon name="close-outline" class="text-lg"></ion-icon>
                </button>
            </div>
        </div>

        <form id="formSkemaPotongan" action="" method="POST" class="overflow-y-auto p-5 space-y-4">
            <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
            <input type="hidden" name="redirect_to" value="index">
            <input type="hidden" id="potongan_acuan_hidden" value="0">

            <!-- Preset Skema -->
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label for="potongan_skema_id" class="text-xs font-semibold text-slate-700">Pilih Preset Skema</label>
                    <span class="text-[11px] text-slate-400">Total Potongan: <strong id="potongan_lbl_sum_persen" class="text-amber-600 font-mono">15%</strong></span>
                </div>
                <select name="skema_id" id="potongan_skema_id" onchange="onSkemaSelectChange('potongan', this)"
                        class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:bg-white focus:border-amber-500 text-xs font-semibold text-slate-700 transition-all cursor-pointer">
                    <option value="">-- Kustom / Atur Sendiri --</option>
                    <?php if (!empty($skemas)): ?>
                        <?php foreach ($skemas as $sk): ?>
                            <option value="<?= $sk['id'] ?>" 
                                    data-ada-pasal="<?= $sk['ada_pasal'] ?>" 
                                    data-pasal="<?= (float)$sk['persen_pasal'] ?>" 
                                    data-ada-admin="<?= $sk['ada_admin'] ?>" 
                                    data-admin="<?= (float)$sk['persen_admin'] ?>"
                                    data-tipe="<?= htmlspecialchars($sk['tipe_potongan'] ?? 'dpp') ?>">
                                <?= htmlspecialchars($sk['nama_skema']) ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>

            <!-- Tarif Pasal & Admin (Clean, tanpa box berlapis) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-1">
                <div id="potongan_card_pasal" class="transition-opacity">
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="flex items-center gap-2 cursor-pointer select-none">
                            <input type="checkbox" name="ada_pasal" id="potongan_ada_pasal" value="1" checked onchange="togglePasalCheck('potongan')" class="w-4 h-4 rounded text-amber-600 focus:ring-0 cursor-pointer">
                            <span class="text-xs font-semibold text-slate-700">Potongan Pasal</span>
                        </label>
                        <span id="potongan_badge_pasal" class="text-[10px] text-emerald-600 font-semibold">Aktif</span>
                    </div>
                    <div class="relative" id="potongan_pasal_wrap">
                        <input type="number" step="0.1" min="0" max="100" name="persen_pasal" id="potongan_persen_pasal" value="13" oninput="updateIndexPotonganCalc()"
                               class="w-full pl-3 pr-8 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-800 focus:outline-none focus:bg-white focus:border-amber-500 font-mono transition-all [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none">
                        <span class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs font-bold pointer-events-none">%</span>
                    </div>
                </div>

                <div id="potongan_card_admin" class="transition-opacity">
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="flex items-center gap-2 cursor-pointer select-none">
                            <input type="checkbox" name="ada_admin" id="potongan_ada_admin" value="1" checked onchange="toggleAdminCheck('potongan')" class="w-4 h-4 rounded text-amber-600 focus:ring-0 cursor-pointer">
                            <span class="text-xs font-semibold text-slate-700">Biaya Admin (Fee)</span>
                        </label>
                        <span id="potongan_badge_admin" class="text-[10px] text-emerald-600 font-semibold">Aktif</span>
                    </div>
                    <div class="relative" id="potongan_admin_wrap">
                        <input type="number" step="0.1" min="0" max="100" name="persen_admin" id="potongan_persen_admin" value="2" oninput="updateIndexPotonganCalc()"
                               class="w-full pl-3 pr-8 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-800 focus:outline-none focus:bg-white focus:border-amber-500 font-mono transition-all [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none">
                        <span class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs font-bold pointer-events-none">%</span>
                    </div>
                </div>
            </div>

            <!-- Basis Perhitungan & Simpan Skema -->
            <div class="pt-3 border-t border-slate-100 space-y-2.5">
                <label class="flex items-start gap-2.5 cursor-pointer select-none">
                    <input type="checkbox" name="tipe_potongan" id="potongan_tipe_potongan" value="kontrak" onchange="updateIndexPotonganCalc()" class="w-4 h-4 rounded text-amber-600 focus:ring-0 mt-0.5 cursor-pointer">
                    <div>
                        <span class="text-xs font-semibold text-slate-700 block">Hitung Potongan dari Pagu Negosiasi</span>
                        <span class="text-[11px] text-slate-400 block leading-snug">Jika tidak dicentang, potongan dihitung dari DPP (setelah PPN 11%).</span>
                    </div>
                </label>

                <button type="button" onclick="promptSaveCurrentAsSkema('potongan')" class="text-[11px] text-amber-600 hover:text-amber-700 font-semibold inline-flex items-center gap-1 hover:underline cursor-pointer">
                    <ion-icon name="bookmark-outline" class="text-xs"></ion-icon>
                    <span>Simpan nilai ini sebagai skema baru</span>
                </button>
            </div>

            <!-- Live Preview Ringkas (1 strip bersih, tanpa kotak di dalam kotak) -->
            <div class="px-3.5 py-3 bg-slate-50 rounded-xl space-y-2.5">
                <div class="flex items-center justify-between text-[11px] text-slate-500 pb-2 border-b border-slate-200/70">
                    <span class="font-semibold text-slate-700">Simulasi Potongan</span>
                    <span>Acuan: <strong id="potongan_preview_acuan" class="font-mono text-slate-800">Rp 0</strong></span>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs">
                    <div>
                        <span class="block text-[10px] font-medium text-slate-400 uppercase">DPP (-11%)</span>
                        <span class="font-mono font-bold text-slate-700 text-xs" id="potongan_preview_dpp">Rp 0</span>
                    </div>
                    <div>
                        <span class="block text-[10px] font-medium text-slate-400 uppercase">Pasal (<span id="potongan_lbl_pasal">13</span>%)</span>
                        <span class="font-mono font-bold text-slate-700 text-xs" id="potongan_preview_pasal13">Rp 0</span>
                    </div>
                    <div>
                        <span class="block text-[10px] font-medium text-slate-400 uppercase">Admin (<span id="potongan_lbl_admin">2</span>%)</span>
                        <span class="font-mono font-bold text-slate-700 text-xs" id="potongan_preview_admin2">Rp 0</span>
                    </div>
                    <div>
                        <span class="block text-[10px] font-bold text-amber-700 uppercase">Total (<span id="potongan_lbl_total_pasal">15</span>%)</span>
                        <span class="font-mono font-extrabold text-amber-700 text-xs" id="potongan_preview_total_pasal">Rp 0</span>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2 shrink-0">
                <button type="button" data-modal-close="modalSkemaPotongan" 
                        class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 font-semibold hover:bg-slate-50 text-xs transition-colors cursor-pointer">
                    Batal
                </button>
                <button type="submit" 
                        class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white font-semibold rounded-xl text-xs shadow-xs transition-all flex items-center gap-1.5 cursor-pointer">
                    <ion-icon name="checkmark-outline" class="text-sm"></ion-icon>
                    <span>Simpan Perubahan</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================== -->
<!-- MODAL: UBAH STATUS PENCAIRAN (POPUP)       -->
<!-- ========================================== -->
<div id="modalStatusPencairan" class="app-modal fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 transition-all duration-200 opacity-0 pointer-events-none hidden">
    <div class="modal-card bg-white rounded-2xl shadow-2xl border border-slate-100 max-w-sm w-full transform scale-95 opacity-0 transition-all duration-200 overflow-hidden">
        <!-- Header -->
        <div class="px-5 py-3.5 bg-slate-50 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-emerald-600 text-white flex items-center justify-center text-base shadow-xs">
                    <ion-icon name="wallet-outline"></ion-icon>
                </div>
                <div>
                    <h3 class="font-bold text-slate-800 text-xs">Ubah Status Pencairan</h3>
                    <p class="text-[10px] text-slate-400 truncate max-w-[200px]" id="status_pencairan_subtitle">-</p>
                </div>
            </div>
            <button type="button" data-modal-close="modalStatusPencairan" class="p-1 text-slate-400 hover:text-slate-700 rounded-lg">
                <ion-icon name="close-outline" class="text-lg"></ion-icon>
            </button>
        </div>

        <!-- Form -->
        <form id="formStatusPencairan" action="" method="POST" class="p-5 space-y-3">
            <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
            <input type="hidden" name="redirect_to" value="index">

            <div class="space-y-2">
                <!-- Option 1: Belum -->
                <label class="flex items-center justify-between p-3 rounded-xl border border-slate-200 hover:border-slate-300 hover:bg-slate-50/60 cursor-pointer transition-all has-[:checked]:border-slate-600 has-[:checked]:bg-slate-50/80">
                    <div class="flex items-center gap-2.5">
                        <input type="radio" name="status" value="Belum" id="status_opt_belum" class="w-4 h-4 text-slate-700 focus:ring-0 cursor-pointer">
                        <div>
                            <div class="text-xs font-bold text-slate-800">Belum Cair</div>
                            <div class="text-[10px] text-slate-400">Berkas/termin belum diajukan</div>
                        </div>
                    </div>
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-slate-600 text-white shadow-2xs">BELUM</span>
                </label>

                <!-- Option 2: Proses -->
                <label class="flex items-center justify-between p-3 rounded-xl border border-slate-200 hover:border-slate-300 hover:bg-slate-50/60 cursor-pointer transition-all has-[:checked]:border-rose-600 has-[:checked]:bg-rose-50/30">
                    <div class="flex items-center gap-2.5">
                        <input type="radio" name="status" value="Proses" id="status_opt_proses" class="w-4 h-4 text-rose-700 focus:ring-0 cursor-pointer">
                        <div>
                            <div class="text-xs font-bold text-slate-800">Sedang Proses</div>
                            <div class="text-[10px] text-slate-400">Pengajuan berkas dalam proses</div>
                        </div>
                    </div>
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-rose-700 text-white shadow-2xs">PROSES</span>
                </label>

                <!-- Option 3: Selesai -->
                <label class="flex items-center justify-between p-3 rounded-xl border border-slate-200 hover:border-slate-300 hover:bg-slate-50/60 cursor-pointer transition-all has-[:checked]:border-emerald-600 has-[:checked]:bg-emerald-50/30">
                    <div class="flex items-center gap-2.5">
                        <input type="radio" name="status" value="Selesai" id="status_opt_selesai" class="w-4 h-4 text-emerald-600 focus:ring-0 cursor-pointer">
                        <div>
                            <div class="text-xs font-bold text-slate-800">Selesai</div>
                            <div class="text-[10px] text-slate-400">Dana pencairan telah selesai/cair</div>
                        </div>
                    </div>
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-600 text-white shadow-2xs">SELESAI</span>
                </label>
            </div>

            <!-- Footer -->
            <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2 shrink-0">
                <button type="button" data-modal-close="modalStatusPencairan" 
                        class="px-3.5 py-2 rounded-xl border border-slate-200 text-slate-600 font-semibold hover:bg-slate-50 text-xs transition-colors">
                    Batal
                </button>
                <button type="submit" 
                        class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-xl text-xs shadow-xs transition-all flex items-center gap-1.5">
                    <ion-icon name="checkmark-outline" class="text-sm"></ion-icon>
                    <span>Simpan Perubahan</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================== -->
<!-- MODAL 3: KELOLA GROUP PAKET (POPUP)        -->
<!-- ========================================== -->
<div id="modalManageGroup" class="app-modal fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 transition-all duration-200 opacity-0 pointer-events-none hidden">
    <div class="modal-card bg-white rounded-2xl shadow-2xl border border-slate-100 max-w-2xl w-full transform scale-95 opacity-0 transition-all duration-200 overflow-hidden max-h-[85vh] flex flex-col">
        <!-- Header -->
        <div class="px-6 py-4 bg-slate-50/80 border-b border-slate-100 flex items-center justify-between shrink-0">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-indigo-600 text-white flex items-center justify-center text-lg shadow-xs">
                    <ion-icon name="folder-open-outline"></ion-icon>
                </div>
                <div>
                    <h3 class="font-bold text-slate-800 text-sm">Kelola Group Paket</h3>
                    <p class="text-[11px] text-slate-400">Wadah pengelompokan program atau kontrak payung</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" onclick="openQuickAddGroup()" 
                        class="px-3 py-1 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 rounded-lg text-xs font-semibold flex items-center gap-1 border border-indigo-200 transition-colors">
                    <ion-icon name="add-circle-outline"></ion-icon>
                    <span>Tambah Group</span>
                </button>
                <button type="button" data-modal-close="modalManageGroup" class="p-1.5 text-slate-400 hover:text-slate-700 rounded-lg transition-colors">
                    <ion-icon name="close-outline" class="text-xl"></ion-icon>
                </button>
            </div>
        </div>

        <!-- Content Table -->
        <div class="p-6 overflow-y-auto flex-1">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs" id="groupPopupTable">
                    <thead class="bg-slate-50 text-[10px] uppercase font-bold text-slate-500 border-b border-slate-100">
                        <tr>
                            <th class="py-2 px-3">Nama Group</th>
                            <th class="py-2 px-3">Kode</th>
                            <th class="py-2 px-3 text-center">Paket</th>
                            <th class="py-2 px-3 text-right">Total Pagu</th>
                            <th class="py-2 px-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100" id="groupPopupTableBody">
                        <?php if (empty($groups)): ?>
                            <tr>
                                <td colspan="5" class="py-8 text-center text-slate-400">
                                    <p class="text-xs font-medium">Belum ada group terdaftar.</p>
                                    <button type="button" onclick="openQuickAddGroup()" class="mt-2 text-xs text-blue-600 font-semibold hover:underline">
                                        + Buat Group Baru Sekarang
                                    </button>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($groups as $g): ?>
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="py-2 px-3 font-semibold text-slate-800">
                                        <a href="<?= APP_URL ?>/group/detail/<?= $g['id'] ?>" class="hover:text-blue-600">
                                            <?= htmlspecialchars($g['nama_group']) ?>
                                        </a>
                                    </td>
                                    <td class="py-2 px-3 font-mono text-slate-500 text-[10px]">
                                        <?= htmlspecialchars($g['kode_group'] ?: '-') ?>
                                    </td>
                                    <td class="py-2 px-3 text-center">
                                        <span class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-700 font-semibold text-[10px]">
                                            <?= $g['total_paket'] ?>
                                        </span>
                                    </td>
                                    <td class="py-2 px-3 text-right font-mono font-semibold text-slate-700">
                                        <?= formatRupiah($g['total_pagu']) ?>
                                    </td>
                                    <td class="py-2 px-3 text-right">
                                        <div class="flex items-center justify-end gap-1">
                                            <a href="<?= APP_URL ?>/group/detail/<?= $g['id'] ?>" class="p-1 text-slate-400 hover:text-blue-600" title="Buka Detail">
                                                <ion-icon name="open-outline"></ion-icon>
                                            </a>
                                            <a href="<?= APP_URL ?>/group/delete/<?= $g['id'] ?>" 
                                               data-confirm-delete="<?= htmlspecialchars($g['nama_group']) ?>"
                                               data-type="Group Paket"
                                               class="p-1 text-slate-400 hover:text-rose-600" title="Hapus Group">
                                                <ion-icon name="trash-outline"></ion-icon>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="px-6 py-3 bg-slate-50/50 border-t border-slate-100 flex items-center justify-between text-xs text-slate-400">
            <span>Daftar group terkait perusahaan aktif</span>
            <a href="<?= APP_URL ?>/group" class="text-blue-600 hover:underline font-semibold flex items-center gap-1">
                <span>Halaman Group Lengkap</span>
                <ion-icon name="arrow-forward-outline"></ion-icon>
            </a>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- MODAL 4: QUICK TAMBAH GROUP (AJAX)         -->
<!-- ========================================== -->
<div id="modalQuickTambahGroup" class="app-modal fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 transition-all duration-200 opacity-0 pointer-events-none hidden">
    <div class="modal-card bg-white rounded-2xl shadow-2xl border border-slate-100 max-w-sm w-full transform scale-95 opacity-0 transition-all duration-200 overflow-hidden">
        <div class="px-5 py-3.5 bg-slate-50 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-bold text-slate-800 text-xs flex items-center gap-2">
                <ion-icon name="folder-open-outline" class="text-blue-600 text-base"></ion-icon>
                <span>Buat Group Baru Cepat</span>
            </h3>
            <button type="button" data-modal-close="modalQuickTambahGroup" class="p-1 text-slate-400 hover:text-slate-700 rounded-lg">
                <ion-icon name="close-outline" class="text-lg"></ion-icon>
            </button>
        </div>
        <form id="formQuickGroup" onsubmit="handleQuickTambahGroup(event)" class="p-5 space-y-3">
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Group / Program <span class="text-red-500">*</span></label>
                <input type="text" id="quick_nama_group" required 
                       class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-blue-500 text-xs focus:bg-white"
                       placeholder="Contoh: Dinas Pendidikan 2025">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Kode Group (Opsional)</label>
                <input type="text" id="quick_kode_group" 
                       class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-blue-500 text-xs focus:bg-white"
                       placeholder="Contoh: GRP-DIKNAS-2025">
            </div>
            <div class="pt-2 border-t border-slate-100 flex justify-end gap-2">
                <button type="button" data-modal-close="modalQuickTambahGroup" class="px-3 py-1.5 rounded-lg border border-slate-200 text-slate-600 font-semibold hover:bg-slate-50 text-xs">
                    Batal
                </button>
                <button type="submit" id="btnSubmitQuickGroup" class="px-4 py-1.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg text-xs shadow-xs">
                    Simpan & Pilih Group
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================== -->
<!-- MODAL 5: QUICK TAMBAH SKEMA (AJAX)         -->
<!-- ========================================== -->
<div id="modalQuickTambahSkema" class="app-modal fixed inset-0 z-[80] bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 transition-all duration-200 opacity-0 pointer-events-none hidden">
    <div class="modal-card bg-white rounded-2xl shadow-2xl border border-slate-100 max-w-md w-full transform scale-95 opacity-0 transition-all duration-200 overflow-hidden">
        <div class="px-5 py-3.5 bg-slate-50 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-bold text-slate-800 text-xs flex items-center gap-2">
                <ion-icon name="options-outline" class="text-blue-600 text-base"></ion-icon>
                <span>Buat Skema Potongan Baru</span>
            </h3>
            <button type="button" data-modal-close="modalQuickTambahSkema" class="p-1 text-slate-400 hover:text-slate-700 rounded-lg cursor-pointer">
                <ion-icon name="close-outline" class="text-lg"></ion-icon>
            </button>
        </div>
        <form id="formQuickSkema" onsubmit="handleQuickTambahSkema(event)" class="p-5 space-y-4">
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label for="quick_nama_skema" class="text-xs font-semibold text-slate-700">Nama Skema <span class="text-red-500">*</span></label>
                    <span class="text-[11px] text-slate-400">Total: <strong id="quick_lbl_total_persen" class="font-mono text-blue-600">15%</strong></span>
                </div>
                <input type="text" id="quick_nama_skema" required 
                       class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-blue-500 text-xs focus:bg-white transition-all"
                       placeholder="Misal: Skema 1, atau 20% Non-Admin">
            </div>

            <!-- Pasal & Admin Side-by-Side (Clean, tanpa box berlapis) -->
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="flex items-center gap-2 cursor-pointer select-none mb-1.5">
                        <input type="checkbox" id="quick_ada_pasal" value="1" checked onchange="toggleQuickModalFields()" class="w-4 h-4 rounded text-blue-600 focus:ring-0 cursor-pointer">
                        <span class="text-xs font-semibold text-slate-700">Potongan Pasal</span>
                    </label>
                    <div class="relative" id="quick_pasal_wrap">
                        <input type="number" step="0.1" min="0" max="100" id="quick_persen_pasal" value="13" oninput="updateQuickModalTotal()"
                               class="w-full pl-3 pr-8 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-800 focus:outline-none focus:bg-white focus:border-blue-500 font-mono transition-all [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none">
                        <span class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs font-bold pointer-events-none">%</span>
                    </div>
                </div>

                <div>
                    <label class="flex items-center gap-2 cursor-pointer select-none mb-1.5">
                        <input type="checkbox" id="quick_ada_admin" value="1" checked onchange="toggleQuickModalFields()" class="w-4 h-4 rounded text-blue-600 focus:ring-0 cursor-pointer">
                        <span class="text-xs font-semibold text-slate-700">Biaya Admin</span>
                    </label>
                    <div class="relative" id="quick_admin_wrap">
                        <input type="number" step="0.1" min="0" max="100" id="quick_persen_admin" value="2" oninput="updateQuickModalTotal()"
                               class="w-full pl-3 pr-8 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-800 focus:outline-none focus:bg-white focus:border-blue-500 font-mono transition-all [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none">
                        <span class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs font-bold pointer-events-none">%</span>
                    </div>
                </div>
            </div>

            <!-- Pilihan Dasar Potongan -->
            <div class="pt-3 border-t border-slate-100">
                <label class="flex items-start gap-2.5 cursor-pointer select-none">
                    <input type="checkbox" id="quick_tipe_potongan" value="kontrak" onchange="updateQuickModalTotal()" class="w-4 h-4 rounded text-blue-600 focus:ring-0 mt-0.5 cursor-pointer">
                    <div>
                        <span class="text-xs font-semibold text-slate-700 block">Hitung Potongan dari Pagu Negosiasi</span>
                        <span class="text-[11px] text-slate-400 block leading-snug">Jika tidak dicentang, potongan dihitung dari DPP (setelah PPN 11%).</span>
                    </div>
                </label>
            </div>

            <div class="pt-3 border-t border-slate-100 flex justify-end gap-2">
                <button type="button" data-modal-close="modalQuickTambahSkema" class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 font-semibold hover:bg-slate-50 text-xs cursor-pointer">
                    Batal
                </button>
                <button type="submit" id="btnSubmitQuickSkema" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl text-xs shadow-xs flex items-center gap-1.5 cursor-pointer">
                    <ion-icon name="checkmark-outline" class="text-sm"></ion-icon>
                    <span>Simpan & Gunakan</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ============================================================== -->
<!-- FLOATING ACTION DROPDOWN MENU (TITIK 3 ⋮)                       -->
<!-- ============================================================== -->
<div id="globalActionDropdown" class="fixed z-50 hidden bg-white rounded-2xl shadow-xl border border-slate-200/90 py-1.5 w-64 text-xs font-medium text-slate-700 divide-y divide-slate-100 transition-all select-none">
    <!-- Header: Info Singkat Paket -->
    <div class="px-3.5 py-2.5 bg-slate-50/80">
        <div class="flex items-center justify-between gap-1.5 mb-1">
            <span id="actionMenuKode" class="font-mono font-bold text-[10px] text-blue-600 bg-blue-50 px-1.5 py-0.2 rounded border border-blue-200/60">#KODE</span>
            <span class="text-[9px] uppercase tracking-wider font-extrabold text-slate-400">PILIHAN MENU</span>
        </div>
        <div id="actionMenuNama" class="text-xs font-bold text-slate-800 truncate" title="">Nama Paket</div>
    </div>

    <!-- Group 1: Modul Detail (Buka Popup) -->
    <div class="py-1">
        <div class="px-3.5 py-1 text-[9px] uppercase tracking-wider font-bold text-slate-400">Rincian Modul (Popup)</div>
        <button type="button" onclick="triggerMenuTab('info')" class="w-full px-3.5 py-1.5 text-left flex items-center gap-2 hover:bg-blue-50 hover:text-blue-600 transition-colors cursor-pointer">
            <ion-icon name="information-circle-outline" class="text-sm text-blue-500"></ion-icon>
            <span>Informasi & Pajak</span>
        </button>
        <button type="button" onclick="triggerMenuTab('items')" class="w-full px-3.5 py-1.5 text-left flex items-center gap-2 hover:bg-blue-50 hover:text-blue-600 transition-colors cursor-pointer">
            <ion-icon name="list-outline" class="text-sm text-blue-500"></ion-icon>
            <span>Komponen Item Barang</span>
        </button>
        <button type="button" onclick="triggerMenuTab('distribusi')" class="w-full px-3.5 py-1.5 text-left flex items-center gap-2 hover:bg-indigo-50 hover:text-indigo-600 transition-colors cursor-pointer">
            <ion-icon name="navigate-outline" class="text-sm text-indigo-500"></ion-icon>
            <span>Alokasi Distribusi Titik</span>
        </button>
        <button type="button" onclick="triggerMenuTab('pemesanan')" class="w-full px-3.5 py-1.5 text-left flex items-center gap-2 hover:bg-amber-50 hover:text-amber-700 transition-colors cursor-pointer">
            <ion-icon name="bag-check-outline" class="text-sm text-amber-500"></ion-icon>
            <span>Progres Pemesanan Toko</span>
        </button>
        <button type="button" onclick="triggerMenuTab('dokumentasi')" class="w-full px-3.5 py-1.5 text-left flex items-center gap-2 hover:bg-teal-50 hover:text-teal-600 transition-colors cursor-pointer">
            <ion-icon name="camera-outline" class="text-sm text-teal-500"></ion-icon>
            <span>Foto Dokumentasi</span>
        </button>
        <button type="button" onclick="triggerMenuTab('dokumen')" class="w-full px-3.5 py-1.5 text-left flex items-center gap-2 hover:bg-emerald-50 hover:text-emerald-600 transition-colors cursor-pointer">
            <ion-icon name="document-text-outline" class="text-sm text-emerald-500"></ion-icon>
            <span>Berkas Dokumen BAST</span>
        </button>
        <button type="button" onclick="triggerMenuTab('profit')" class="w-full px-3.5 py-1.5 text-left flex items-center gap-2 hover:bg-emerald-50 hover:text-emerald-700 transition-colors cursor-pointer">
            <ion-icon name="trending-up-outline" class="text-sm text-emerald-600"></ion-icon>
            <span>Estimasi Margin Profit</span>
        </button>
    </div>

    <!-- Group 2: Pengelolaan & Aksi Cepat -->
    <div class="py-1">
        <div class="px-3.5 py-1 text-[9px] uppercase tracking-wider font-bold text-slate-400">Pengelolaan Paket</div>
        <?php if ($role === 'admin' || $role === 'user'): ?>
            <button type="button" onclick="triggerMenuEditPaket()" class="w-full px-3.5 py-1.5 text-left flex items-center gap-2 hover:bg-amber-50 hover:text-amber-700 transition-colors cursor-pointer">
                <ion-icon name="create-outline" class="text-sm text-amber-500"></ion-icon>
                <span>Edit Data Paket</span>
            </button>
            <button type="button" onclick="triggerMenuSkemaPotongan()" class="w-full px-3.5 py-1.5 text-left flex items-center gap-2 hover:bg-amber-50 hover:text-amber-700 transition-colors cursor-pointer">
                <ion-icon name="options-outline" class="text-sm text-amber-500"></ion-icon>
                <span>Atur Skema Potongan</span>
            </button>
            <button type="button" onclick="triggerMenuHapusPaket()" class="w-full px-3.5 py-1.5 text-left flex items-center gap-2 hover:bg-rose-50 text-rose-600 transition-colors cursor-pointer">
                <ion-icon name="trash-outline" class="text-sm text-rose-500"></ion-icon>
                <span>Hapus Paket Kegiatan</span>
            </button>
        <?php endif; ?>
    </div>
</div>

<!-- ============================================================== -->
<!-- DYNAMIC NATIVE AJAX MODAL (BEST PRACTICE / NO IFRAME)          -->
<!-- ============================================================== -->
<div id="modalModuleDynamic" class="app-modal fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-5 transition-all duration-200 opacity-0 pointer-events-none hidden">
    <div id="modalModuleCard" class="modal-card bg-white rounded-2xl shadow-2xl border border-slate-200 w-full max-w-6xl transform scale-95 opacity-0 transition-all duration-200 overflow-hidden max-h-[90vh] flex flex-col">
        <!-- Header -->
        <div class="px-5 py-3.5 bg-slate-50/90 border-b border-slate-200 flex items-center justify-between shrink-0">
            <div class="flex items-center gap-2.5 min-w-0">
                <div id="modalModuleIconWrap" class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-lg shrink-0">
                    <ion-icon name="layers-outline" id="modalModuleIcon"></ion-icon>
                </div>
                <div class="min-w-0">
                    <div class="flex items-center gap-2">
                        <span id="modalModuleSubtitle" class="text-sm font-bold text-slate-800 truncate max-w-sm sm:max-w-xl">Nama Paket</span>
                        <span id="modalModuleKode" class="font-mono font-bold text-[10px] bg-slate-100 text-slate-700 px-1.5 py-0.2 rounded border border-slate-200 shrink-0">#KODE</span>
                    </div>
                    <p id="modalModuleTitle" class="text-[11px] font-medium text-slate-400">Detail Paket</p>
                </div>
            </div>

            <!-- Right Actions in Modal Header -->
            <div class="flex items-center gap-2.5 shrink-0">
                <div id="modalModuleHeaderAction" class="flex items-center gap-2"></div>
                <button type="button" onclick="closeModuleModal()"
                        class="w-8 h-8 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-500 hover:text-slate-800 flex items-center justify-center transition-colors cursor-pointer"
                        title="Tutup Popup (Esc)">
                    <ion-icon name="close-outline" class="text-lg"></ion-icon>
                </button>
            </div>
        </div>

        <!-- Scrollable Modal Body (Pure Native HTML) - Auto-fit height up to 90vh -->
        <div id="modalModuleBody" class="relative w-full overflow-y-auto max-h-[calc(90vh-65px)] bg-slate-50/60 p-4 sm:p-5">
            <!-- Dynamic Content / Skeleton injected here -->
        </div>
    </div>
</div>

<!-- ============================================================== -->
<!-- NATIVE SUB-MODAL: UPLOAD BAST                                  -->
<!-- ============================================================== -->
<div id="modalNativeUploadBast" class="app-modal fixed inset-0 z-[60] bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 transition-all duration-200 opacity-0 pointer-events-none hidden">
    <div class="modal-card bg-white rounded-2xl shadow-2xl border border-slate-100 max-w-lg w-full transform scale-95 opacity-0 transition-all duration-200 overflow-hidden max-h-[92vh] flex flex-col">
        <div class="px-5 py-3.5 bg-slate-50 border-b border-slate-100 flex items-center justify-between shrink-0">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-amber-600 text-white flex items-center justify-center text-lg shadow-xs">
                    <ion-icon name="cloud-upload-outline"></ion-icon>
                </div>
                <div>
                    <h3 class="font-bold text-slate-800 text-sm" id="nativeUploadBastTitle">Upload Berkas BAST</h3>
                    <p class="text-[11px] text-slate-400">Upload lembar scan/foto BAST yang telah ditandatangani.</p>
                </div>
            </div>
            <button type="button" onclick="window.ProModal.close('modalNativeUploadBast')" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg">
                <ion-icon name="close-outline" class="text-xl"></ion-icon>
            </button>
        </div>

        <form id="formNativeUploadBast" action="" method="POST" enctype="multipart/form-data" class="overflow-y-auto p-5 space-y-4">
            <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
            <input type="hidden" name="jenis_dokumen" value="BAST">
            <input type="hidden" name="redirect_to" id="nativeUploadBastRedirect" value="">

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">
                    Nama Dokumen BAST <span class="text-rose-500">*</span>
                </label>
                <input type="text" name="nama_dokumen" id="nativeUploadBastNama" required
                       placeholder="Misal: BAST SMAN 2 Bontang"
                       class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:border-amber-500 transition-all font-semibold">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">
                    Pilih File Hasil Scan / Foto BAST <span class="text-rose-500">*</span>
                </label>
                <input type="file" name="file_dokumen" required
                       accept=".pdf,.jpg,.jpeg,.png,.webp"
                       class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:border-amber-500 transition-all file:mr-3 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-amber-100 file:text-amber-800 hover:file:bg-amber-200 cursor-pointer">
                <p class="text-[10px] text-slate-400 mt-1">Format: PDF, JPG, PNG, WEBP. Maksimal 25MB.</p>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Catatan (Opsional)</label>
                <textarea name="keterangan" rows="2" placeholder="Catatan berkas BAST..."
                          class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:border-amber-500 transition-all resize-none"></textarea>
            </div>

            <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2.5">
                <button type="button" onclick="window.ProModal.close('modalNativeUploadBast')"
                        class="px-4 py-2 border border-slate-200 text-slate-600 hover:bg-slate-50 rounded-xl text-xs font-semibold transition-colors">
                    Batal
                </button>
                <button type="submit"
                        class="px-5 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-xl text-xs font-semibold shadow-xs transition-all flex items-center gap-1.5 cursor-pointer">
                    <ion-icon name="cloud-upload-outline"></ion-icon>
                    <span>Upload BAST</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ============================================================== -->
<!-- NATIVE SUB-MODAL: TAMBAH / EDIT ITEM KOMPONEN                  -->
<!-- ============================================================== -->
<div id="modalNativeItem" class="app-modal fixed inset-0 z-[60] bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 transition-all duration-200 opacity-0 pointer-events-none hidden">
    <div class="modal-card bg-white rounded-2xl shadow-2xl border border-slate-100 max-w-lg w-full transform scale-95 opacity-0 transition-all duration-200 overflow-hidden max-h-[92vh] flex flex-col">
        <div class="px-5 py-3.5 bg-slate-50 border-b border-slate-100 flex items-center justify-between shrink-0">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-blue-600 text-white flex items-center justify-center text-lg shadow-xs">
                    <ion-icon name="cube-outline"></ion-icon>
                </div>
                <div>
                    <h3 class="font-bold text-slate-800 text-sm" id="nativeItemModalTitle">Tambah Komponen Barang</h3>
                    <p class="text-[11px] text-slate-400">Rincian spesifikasi teknis dan pagu satuan item</p>
                </div>
            </div>
            <button type="button" onclick="window.ProModal.close('modalNativeItem')" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg">
                <ion-icon name="close-outline" class="text-xl"></ion-icon>
            </button>
        </div>

        <form id="formNativeItem" action="" method="POST" class="overflow-y-auto p-5 space-y-4">
            <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
            <input type="hidden" name="redirect_to" id="nativeItemRedirect" value="">

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Nama Komponen / Barang <span class="text-rose-500">*</span></label>
                <input type="text" name="komponen" id="nativeItemKomponen" required placeholder="Contoh: Tenda Lipat 3x3m"
                       class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:border-blue-500 transition-all font-semibold">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Spesifikasi Lengkap</label>
                <textarea name="spesifikasi" id="nativeItemSpesifikasi" rows="2" placeholder="Ukuran, bahan, tipe, atau spesifikasi teknis..."
                          class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:border-blue-500 transition-all resize-none"></textarea>
            </div>

            <div class="grid grid-cols-3 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Satuan</label>
                    <input type="text" name="satuan" id="nativeItemSatuan" value="Unit" placeholder="Unit, Pcs..."
                           class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:border-blue-500 transition-all">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Jumlah (Qty) <span class="text-rose-500">*</span></label>
                    <input type="number" name="jumlah" id="nativeItemJumlah" value="1" min="1" step="any" required
                           class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:border-blue-500 transition-all font-semibold">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Harga Satuan (Rp)</label>
                    <input type="text" name="harga_satuan" id="nativeItemHargaSatuan" value="0"
                           class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:border-blue-500 transition-all font-mono font-semibold">
                </div>
            </div>

            <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2.5">
                <button type="button" onclick="window.ProModal.close('modalNativeItem')"
                        class="px-4 py-2 border border-slate-200 text-slate-600 hover:bg-slate-50 rounded-xl text-xs font-semibold transition-colors">
                    Batal
                </button>
                <button type="submit"
                        class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-semibold shadow-xs transition-all flex items-center gap-1.5 cursor-pointer">
                    <ion-icon name="save-outline"></ion-icon>
                    <span id="nativeItemSubmitBtn">Simpan Komponen</span>
                </button>
            </div>
        </form>
    </div>
</div>



<!-- ============================================================== -->
<!-- NATIVE SUB-MODAL: UPDATE STATUS PEMESANAN BARANG               -->
<!-- ============================================================== -->
<div id="modalNativeStatusBarang" class="app-modal fixed inset-0 z-[60] bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 transition-all duration-200 opacity-0 pointer-events-none hidden">
    <div class="modal-card bg-white rounded-2xl shadow-2xl border border-slate-100 max-w-md w-full transform scale-95 opacity-0 transition-all duration-200 overflow-hidden max-h-[92vh] flex flex-col">
        <div class="px-5 py-3.5 bg-slate-50 border-b border-slate-100 flex items-center justify-between shrink-0">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-emerald-600 text-white flex items-center justify-center text-base shadow-xs">
                    <ion-icon name="bag-check-outline"></ion-icon>
                </div>
                <div>
                    <h3 class="font-bold text-slate-800 text-xs">Update Status Pemesanan</h3>
                    <p class="text-[10px] text-slate-400" id="nativeStatusBarangSub">-</p>
                </div>
            </div>
            <button type="button" onclick="window.ProModal.close('modalNativeStatusBarang')" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg">
                <ion-icon name="close-outline" class="text-lg"></ion-icon>
            </button>
        </div>

        <form id="formNativeStatusBarang" onsubmit="submitNativeStatusBarang(event)" class="overflow-y-auto p-5 space-y-3.5">
            <input type="hidden" id="nativeStatusBarangDistId" name="dist_id">

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Status Pemesanan <span class="text-rose-500">*</span></label>
                <div class="grid grid-cols-2 gap-2">
                    <label class="flex items-center gap-2 p-2.5 rounded-xl border border-slate-200 cursor-pointer bg-white hover:bg-slate-50 transition-colors">
                        <input type="radio" name="status_barang" value="Belum Dipesan" id="radioStatusBelumPesan" onchange="toggleNativeStatusBarangInputs()">
                        <span class="text-xs font-semibold text-slate-700">Belum Dipesan</span>
                    </label>
                    <label class="flex items-center gap-2 p-2.5 rounded-xl border border-slate-200 cursor-pointer bg-white hover:bg-emerald-50/50 transition-colors">
                        <input type="radio" name="status_barang" value="Sudah Dipesan" id="radioStatusSudahPesan" onchange="toggleNativeStatusBarangInputs()">
                        <span class="text-xs font-semibold text-emerald-800">Sudah Dipesan</span>
                    </label>
                </div>
            </div>

            <div id="sectionNativeDetailPesanan" class="space-y-3 pt-2 border-t border-slate-100 hidden">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Barang Dibeli / Toko</label>
                    <input type="text" name="nama_barang" id="nativeStatusBarangNama" placeholder="Contoh: Asus Official Store / Tokopedia"
                           class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:border-emerald-500 transition-all font-semibold">
                </div>
                <div class="grid grid-cols-2 gap-2.5">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Ekspedisi</label>
                        <input type="text" name="ekspedisi" id="nativeStatusBarangEkspedisi" placeholder="JNE / SiCepat..."
                               class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:border-emerald-500 transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">No. Resi</label>
                        <input type="text" name="no_resi" id="nativeStatusBarangResi" placeholder="No Resi..."
                               class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:border-emerald-500 transition-all font-mono">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-2.5">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Harga Beli Satuan (Rp)</label>
                        <input type="text" name="harga_satuan" id="nativeStatusBarangHarga" placeholder="0" oninput="formatRupiahInput(this)"
                               class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:border-emerald-500 transition-all font-mono font-semibold">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Ongkos Kirim (Rp)</label>
                        <input type="text" name="ongkos_kirim" id="nativeStatusBarangOngkir" placeholder="0" oninput="formatRupiahInput(this)"
                               class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:border-emerald-500 transition-all font-mono font-semibold">
                    </div>
                </div>
            </div>

            <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                <button type="button" onclick="window.ProModal.close('modalNativeStatusBarang')"
                        class="px-4 py-2 border border-slate-200 text-slate-600 hover:bg-slate-50 rounded-xl text-xs font-semibold">Batal</button>
                <button type="submit" id="btnSubmitNativeStatusBarang"
                        class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-semibold transition-all">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<!-- ============================================================== -->
<!-- NATIVE SUB-MODAL: KELOLA RINCIAN ONGKOS KIRIM                  -->
<!-- ============================================================== -->
<div id="modalNativeKelolaOngkir" class="app-modal fixed inset-0 z-[60] bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 transition-all duration-200 opacity-0 pointer-events-none hidden">
    <div class="modal-card bg-white rounded-2xl shadow-2xl border border-slate-100 max-w-md w-full transform scale-95 opacity-0 transition-all duration-200 overflow-hidden flex flex-col">
        <div class="px-5 py-3.5 bg-amber-50/70 border-b border-amber-100 flex items-center justify-between shrink-0">
            <div class="flex items-center gap-2 min-w-0">
                <div class="w-8 h-8 rounded-lg bg-amber-500 text-white flex items-center justify-center text-base shadow-xs shrink-0">
                    <ion-icon name="cash-outline"></ion-icon>
                </div>
                <div class="min-w-0">
                    <h3 class="font-bold text-slate-800 text-xs">Rincian Ongkos Kirim</h3>
                    <p class="text-[10px] text-slate-500 truncate" id="nativeOngkirSub">-</p>
                </div>
            </div>
            <button type="button" onclick="window.ProModal.close('modalNativeKelolaOngkir')" class="p-1 text-slate-400 hover:text-slate-700 rounded-lg cursor-pointer">
                <ion-icon name="close-outline" class="text-lg"></ion-icon>
            </button>
        </div>
        <form id="formNativeKelolaOngkir" onsubmit="submitNativeKelolaOngkir(event)" class="p-5 space-y-3.5">
            <input type="hidden" id="nativeOngkirDistId" value="">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-700">Daftar Ongkos Kirim</span>
                <button type="button" onclick="addNativeOngkirRow()" class="inline-flex items-center gap-1 px-2.5 py-1 bg-amber-50 hover:bg-amber-100 text-amber-700 border border-amber-200 rounded-lg text-[11px] font-semibold cursor-pointer">
                    <ion-icon name="add-outline" class="text-sm"></ion-icon>
                    <span>Tambah Ongkir</span>
                </button>
            </div>
            <div id="nativeOngkirRowsContainer" class="space-y-2 max-h-60 overflow-y-auto pr-1"></div>
            <div class="p-3 bg-slate-50 rounded-xl border border-slate-200 flex items-center justify-between">
                <span class="text-xs font-bold text-slate-600">Total Ongkir (Masuk HPP)</span>
                <span id="nativeOngkirTotal" class="font-mono font-extrabold text-sm text-amber-700">Rp 0</span>
            </div>
            <div class="pt-2 border-t border-slate-100 flex items-center justify-end gap-2">
                <button type="button" onclick="window.ProModal.close('modalNativeKelolaOngkir')" class="px-4 py-2 border border-slate-200 text-slate-600 hover:bg-slate-50 rounded-xl text-xs font-semibold cursor-pointer">Batal</button>
                <button type="submit" id="btnSubmitNativeOngkir" class="px-5 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-xl text-xs font-semibold transition-all cursor-pointer">Simpan Ongkir</button>
            </div>
        </form>
    </div>
</div>

<!-- ============================================================== -->
<!-- NATIVE SUB-MODAL: UPDATE STATUS DISTRIBUSI / PENGIRIMAN        -->
<!-- ============================================================== -->
<div id="modalNativeStatusDistribusi" class="app-modal fixed inset-0 z-[60] bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 transition-all duration-200 opacity-0 pointer-events-none hidden">
    <div class="modal-card bg-white rounded-2xl shadow-2xl border border-slate-100 max-w-sm w-full transform scale-95 opacity-0 transition-all duration-200 overflow-hidden flex flex-col">
        <div class="px-5 py-3.5 bg-slate-50 border-b border-slate-100 flex items-center justify-between shrink-0">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-indigo-600 text-white flex items-center justify-center text-base shadow-xs">
                    <ion-icon name="paper-plane-outline"></ion-icon>
                </div>
                <div>
                    <h3 class="font-bold text-slate-800 text-xs">Update Status Pengiriman</h3>
                    <p class="text-[10px] text-slate-400" id="nativeStatusDistSub">-</p>
                </div>
            </div>
            <button type="button" onclick="window.ProModal.close('modalNativeStatusDistribusi')" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg cursor-pointer">
                <ion-icon name="close-outline" class="text-lg"></ion-icon>
            </button>
        </div>

        <form id="formNativeStatusDistribusi" onsubmit="submitNativeStatusDistribusi(event)" class="p-5 space-y-4">
            <input type="hidden" id="nativeStatusDistId" name="dist_id">

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Status Pengiriman <span class="text-rose-500">*</span></label>
                <select name="status_distribusi" id="nativeStatusDistSelect" required
                        class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:border-indigo-500 transition-all font-semibold text-slate-800">
                    <option value="Tiba di Rumah">Tiba di Rumah (Transit)</option>
                    <option value="Tiba di Gudang">Tiba di Gudang</option>
                    <option value="Tiba di Tujuan">Tiba di Tujuan</option>
                </select>
                <p class="text-[10px] text-slate-400 mt-1.5">Tiba di Gudang & Tiba di Tujuan menandakan barang sudah sampai.</p>
            </div>

            <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                <button type="button" onclick="window.ProModal.close('modalNativeStatusDistribusi')"
                        class="px-4 py-2 border border-slate-200 text-slate-600 hover:bg-slate-50 rounded-xl text-xs font-semibold cursor-pointer">Batal</button>
                <button type="submit" id="btnSubmitNativeStatusDist"
                        class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-semibold transition-all shadow-xs cursor-pointer">Simpan Status</button>
            </div>
        </form>
    </div>
</div>

<!-- ============================================================== -->
<!-- NATIVE SUB-MODAL: UPLOAD FOTO DOKUMENTASI                      -->
<!-- ============================================================== -->
<div id="modalNativeUploadFoto" class="app-modal fixed inset-0 z-[60] bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 transition-all duration-200 opacity-0 pointer-events-none hidden">
    <div class="modal-card bg-white rounded-2xl shadow-2xl border border-slate-100 max-w-lg w-full transform scale-95 opacity-0 transition-all duration-200 overflow-hidden max-h-[92vh] flex flex-col">
        <div class="px-5 py-3.5 bg-slate-50 border-b border-slate-100 flex items-center justify-between shrink-0">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-teal-600 text-white flex items-center justify-center text-lg shadow-xs">
                    <ion-icon name="camera-outline"></ion-icon>
                </div>
                <div>
                    <h3 class="font-bold text-slate-800 text-sm">Upload Foto Dokumentasi</h3>
                    <p class="text-[11px] text-slate-400">Bukti serah terima / kondisi fisik barang di lokasi</p>
                </div>
            </div>
            <button type="button" onclick="window.ProModal.close('modalNativeUploadFoto')" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg">
                <ion-icon name="close-outline" class="text-xl"></ion-icon>
            </button>
        </div>

        <form id="formNativeUploadFoto" action="" method="POST" enctype="multipart/form-data" class="overflow-y-auto p-5 space-y-4">
            <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
            <input type="hidden" name="redirect_to" id="nativeUploadFotoRedirect" value="">

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">Pilih Foto Dokumentasi <span class="text-rose-500">*</span></label>
                <input type="file" name="fotos[]" multiple required accept="image/*"
                       class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:border-teal-500 transition-all file:mr-3 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-teal-100 file:text-teal-800 hover:file:bg-teal-200 cursor-pointer">
                <p class="text-[10px] text-slate-400 mt-1">Dapat memilih lebih dari 1 file gambar sekaligus (JPG, PNG, WEBP). Maks 15MB/foto.</p>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Keterangan Foto</label>
                <input type="text" name="keterangan" id="nativeUploadFotoKeterangan" placeholder="Contoh: Serah terima barang kepada Kepala Sekolah..."
                       class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:border-teal-500 transition-all">
            </div>

            <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2.5">
                <button type="button" onclick="window.ProModal.close('modalNativeUploadFoto')"
                        class="px-4 py-2 border border-slate-200 text-slate-600 hover:bg-slate-50 rounded-xl text-xs font-semibold transition-colors">
                    Batal
                </button>
                <button type="submit"
                        class="px-5 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-xl text-xs font-semibold shadow-xs transition-all flex items-center gap-1.5 cursor-pointer">
                    <ion-icon name="cloud-upload-outline"></ion-icon>
                    <span>Upload Foto</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================== -->
<!-- 4. JAVASCRIPT LOGIC (NATIVE MODALS ENGINE) -->
<!-- ========================================== -->
<script>
// ==========================================
// ACTION DROPDOWN & NATIVE AJAX MODAL LOGIC
// ==========================================
let currentPaketData = null;
let activeMenuBtn = null;
let currentOpenKode = null;
let currentOpenModule = null;

function toggleActionMenu(event, paketJson) {
    event.stopPropagation();
    const btn = event.currentTarget;
    const menu = document.getElementById('globalActionDropdown');
    if (!menu) return;

    if (activeMenuBtn === btn && !menu.classList.contains('hidden')) {
        closeActionMenu();
        return;
    }

    activeMenuBtn = btn;
    currentPaketData = paketJson;

    // Header info
    const kodeEl = document.getElementById('actionMenuKode');
    const namaEl = document.getElementById('actionMenuNama');

    if (kodeEl) kodeEl.textContent = '#' + (paketJson.kode_paket || paketJson.id);
    if (namaEl) {
        namaEl.textContent = paketJson.nama_paket;
        namaEl.title = paketJson.nama_paket;
    }

    // Display & positioning
    menu.classList.remove('hidden');

    const rect = btn.getBoundingClientRect();
    const menuWidth = 256;
    let left = rect.right - menuWidth;
    let top = rect.bottom + 6;

    const menuHeight = menu.offsetHeight || 390;
    if (top + menuHeight > window.innerHeight) {
        top = Math.max(10, rect.top - menuHeight - 6);
    }
    if (left < 10) left = 10;

    menu.style.top = top + 'px';
    menu.style.left = left + 'px';
}

function closeActionMenu() {
    const menu = document.getElementById('globalActionDropdown');
    if (menu) menu.classList.add('hidden');
    activeMenuBtn = null;
}

document.addEventListener('click', function(e) {
    const menu = document.getElementById('globalActionDropdown');
    if (menu && !menu.classList.contains('hidden') && !menu.contains(e.target) && !e.target.closest('.action-menu-trigger')) {
        closeActionMenu();
    }
});
window.addEventListener('scroll', closeActionMenu, true);

/**
 * Open Native Modal with AJAX Content (Best Practice)
 */
function openPaketModalTab(idOrKode, module, namaPaket) {
    openPaketNativeModal(idOrKode, module, namaPaket);
}

function openPaketNativeModal(idOrKode, module, namaPaket) {
    closeActionMenu();
    currentOpenKode = idOrKode;
    currentOpenModule = module || 'info';

    const tabConfig = {
        'info': { name: 'Informasi & Finansial', icon: 'information-circle-outline', bg: 'bg-blue-50 text-blue-600' },
        'items': { name: 'Komponen Item Barang', icon: 'list-outline', bg: 'bg-blue-50 text-blue-600' },
        'pemesanan': { name: 'Progres Pemesanan Toko', icon: 'bag-check-outline', bg: 'bg-amber-50 text-amber-700' },
        'distribusi': { name: 'Alokasi Distribusi Titik', icon: 'navigate-outline', bg: 'bg-indigo-50 text-indigo-600' },
        'dokumentasi': { name: 'Foto Dokumentasi', icon: 'camera-outline', bg: 'bg-teal-50 text-teal-600' },
        'dokumen': { name: 'Berkas Dokumen BAST', icon: 'ribbon-outline', bg: 'bg-amber-50 text-amber-700' },
        'profit': { name: 'Estimasi Margin Profit', icon: 'trending-up-outline', bg: 'bg-emerald-50 text-emerald-700' }
    };

    const cfg = tabConfig[module] || { name: 'Detail Paket', icon: 'layers-outline', bg: 'bg-blue-50 text-blue-600' };

    const titleEl = document.getElementById('modalModuleTitle');
    const kodeEl = document.getElementById('modalModuleKode');
    const subEl = document.getElementById('modalModuleSubtitle');
    const iconEl = document.getElementById('modalModuleIcon');
    const iconWrap = document.getElementById('modalModuleIconWrap');
    const bodyEl = document.getElementById('modalModuleBody');

    if (titleEl) titleEl.textContent = cfg.name;
    if (kodeEl) kodeEl.textContent = '#' + idOrKode;
    if (subEl) subEl.textContent = namaPaket || '';
    if (iconEl) iconEl.setAttribute('name', cfg.icon);
    if (iconWrap) iconWrap.className = 'w-8 h-8 rounded-xl flex items-center justify-center text-lg shrink-0 ' + cfg.bg;

    const headerActionEl = document.getElementById('modalModuleHeaderAction');
    if (headerActionEl) headerActionEl.innerHTML = '';

    // Show clean skeleton loader
    if (bodyEl) {
        bodyEl.innerHTML = `
            <div class="animate-pulse space-y-4 py-2">
                <div class="h-44 bg-slate-200/80 rounded-xl"></div>
            </div>
        `;
    }

    // Open modal immediately
    window.ProModal.open('modalModuleDynamic');

    // Fetch pure HTML fragment via AJAX
    fetch('<?= APP_URL ?>/paket/modalView/' + encodeURIComponent(idOrKode) + '/' + encodeURIComponent(module))
        .then(response => {
            if (!response.ok) throw new Error('Gagal memuat data modul (' + response.status + ')');
            return response.text();
        })
        .then(html => {
            if (bodyEl) {
                bodyEl.innerHTML = html;
                // Teleport action button to modal header if present
                const actionSource = bodyEl.querySelector('#modalActionSource');
                const actionContainer = document.getElementById('modalModuleHeaderAction');
                if (actionContainer) {
                    actionContainer.innerHTML = actionSource ? actionSource.innerHTML : '';
                }

                // Execute scripts inside injected HTML
                const scripts = bodyEl.querySelectorAll('script');
                scripts.forEach(oldScript => {
                    const newScript = document.createElement('script');
                    Array.from(oldScript.attributes).forEach(attr => newScript.setAttribute(attr.name, attr.value));
                    newScript.text = oldScript.innerHTML;
                    oldScript.parentNode.replaceChild(newScript, oldScript);
                });
            }
        })
        .catch(err => {
            if (bodyEl) {
                bodyEl.innerHTML = `
                    <div class="p-8 text-center text-slate-500">
                        <div class="w-12 h-12 bg-rose-50 text-rose-500 rounded-full flex items-center justify-center mx-auto mb-3 text-2xl">
                            <ion-icon name="alert-circle-outline"></ion-icon>
                        </div>
                        <h4 class="font-bold text-slate-800 text-sm mb-1">Gagal Memuat Konten</h4>
                        <p class="text-xs text-slate-400 mb-3">${err.message}</p>
                        <button type="button" onclick="openPaketNativeModal('${idOrKode}', '${module}', '${namaPaket || ''}')" class="px-3 py-1.5 bg-slate-800 text-white rounded-lg text-xs font-semibold cursor-pointer">Coba Lagi</button>
                    </div>
                `;
            }
        });
}

function closeModuleModal() {
    window.ProModal.close('modalModuleDynamic');
    const headerActionEl = document.getElementById('modalModuleHeaderAction');
    if (headerActionEl) headerActionEl.innerHTML = '';
    currentOpenKode = null;
    currentOpenModule = null;
}

function refreshCurrentNativeModal() {
    if (currentOpenKode && currentOpenModule) {
        const sub = document.getElementById('modalModuleSubtitle')?.textContent || '';
        openPaketNativeModal(currentOpenKode, currentOpenModule, sub);
    }
}

/**
 * Native Tambah Distribusi Modal Trigger Fallback
 */
window.openNativeTambahDistribusiModal = function(kode) {
    if (typeof window.doOpenNativeTambahDistribusiModal === 'function') {
        window.doOpenNativeTambahDistribusiModal(kode);
    } else {
        window.ProModal.open('modalNativeDistribusi');
    }
};

/**
 * Native Upload BAST Modal Trigger
 */
function openNativeUploadBastModal(kode, namaTujuan) {
    const form = document.getElementById('formNativeUploadBast');
    if (form) {
        form.action = '<?= APP_URL ?>/paket/storeDokumen/' + encodeURIComponent(kode);
        document.getElementById('nativeUploadBastRedirect').value = '/paket?open_modal=' + encodeURIComponent(kode) + '&tab=dokumen';
        document.getElementById('nativeUploadBastNama').value = namaTujuan ? ('BAST - ' + namaTujuan) : 'BAST Umum Paket';
        const titleEl = document.getElementById('nativeUploadBastTitle');
        if (titleEl) titleEl.textContent = namaTujuan ? ('Upload BAST: ' + namaTujuan) : 'Upload BAST Umum Paket';
    }
    window.ProModal.open('modalNativeUploadBast');
}

/**
 * Native Tambah & Edit Item Modal Trigger
 */
function openNativeTambahItemModal(kode) {
    const targetKode = kode || currentOpenKode;
    const form = document.getElementById('formNativeItem');
    if (form) {
        form.action = '<?= APP_URL ?>/paket/storeItem/' + encodeURIComponent(targetKode);
        document.getElementById('nativeItemRedirect').value = '/paket?open_modal=' + encodeURIComponent(targetKode) + '&tab=items';
        document.getElementById('nativeItemModalTitle').textContent = 'Tambah Komponen Barang';
        document.getElementById('nativeItemSubmitBtn').textContent = 'Simpan Komponen';
        document.getElementById('nativeItemKomponen').value = '';
        document.getElementById('nativeItemSpesifikasi').value = '';
        document.getElementById('nativeItemSatuan').value = 'Unit';
        document.getElementById('nativeItemJumlah').value = '1';
        document.getElementById('nativeItemHargaSatuan').value = '0';
    }
    window.ProModal.open('modalNativeItem');
}

function openNativeEditItemModal(it) {
    if (!it) return;
    const form = document.getElementById('formNativeItem');
    const targetKode = currentOpenKode || it.kode_paket || it.paket_id;
    if (form) {
        form.action = '<?= APP_URL ?>/paket/updateItem/' + encodeURIComponent(targetKode) + '/' + encodeURIComponent(it.id);
        document.getElementById('nativeItemRedirect').value = '/paket?open_modal=' + encodeURIComponent(targetKode) + '&tab=items';
        document.getElementById('nativeItemModalTitle').textContent = 'Edit Komponen Barang';
        document.getElementById('nativeItemSubmitBtn').textContent = 'Perbarui Komponen';
        document.getElementById('nativeItemKomponen').value = it.komponen || it.nama_barang || '';
        document.getElementById('nativeItemSpesifikasi').value = it.spesifikasi || '';
        document.getElementById('nativeItemSatuan').value = it.satuan || 'Unit';
        document.getElementById('nativeItemJumlah').value = it.jumlah || 1;
        document.getElementById('nativeItemHargaSatuan').value = it.harga_satuan ? new Intl.NumberFormat('id-ID').format(it.harga_satuan) : '0';
    }
    window.ProModal.open('modalNativeItem');
}



/**
 * Native Update Status Barang Modal Trigger & AJAX Submit
 */
function openNativeUpdateStatusBarangModal(dist) {
    if (!dist) return;
    document.getElementById('nativeStatusBarangDistId').value = dist.id;
    const sub = document.getElementById('nativeStatusBarangSub');
    if (sub) sub.textContent = (dist.nama_tujuan || '-') + ' (' + (dist.nama_komponen || dist.komponen || '-') + ')';

    const isSudah = (dist.status_barang === 'Sudah Dipesan');
    document.getElementById('radioStatusSudahPesan').checked = isSudah;
    document.getElementById('radioStatusBelumPesan').checked = !isSudah;

    document.getElementById('nativeStatusBarangNama').value = dist.nama_barang || '';
    document.getElementById('nativeStatusBarangEkspedisi').value = dist.ekspedisi || '';
    document.getElementById('nativeStatusBarangResi').value = dist.no_resi || '';
    document.getElementById('nativeStatusBarangHarga').value = dist.harga_satuan ? new Intl.NumberFormat('id-ID').format(dist.harga_satuan) : '';
    const ongkirEl = document.getElementById('nativeStatusBarangOngkir');
    if (ongkirEl) ongkirEl.value = dist.ongkos_kirim ? new Intl.NumberFormat('id-ID').format(dist.ongkos_kirim) : '';

    toggleNativeStatusBarangInputs();
    window.ProModal.open('modalNativeStatusBarang');
}

function openNativeOngkirModal(dist) {
    if (!dist) return;
    document.getElementById('nativeOngkirDistId').value = dist.id;
    const sub = document.getElementById('nativeOngkirSub');
    if (sub) sub.textContent = (dist.nama_tujuan || '-') + ' (' + (dist.nama_komponen || dist.komponen || '-') + ')';

    const container = document.getElementById('nativeOngkirRowsContainer');
    container.innerHTML = '';
    let items = [];
    if (dist.ongkir_items) {
        try {
            const parsed = JSON.parse(dist.ongkir_items);
            if (Array.isArray(parsed)) items = parsed;
        } catch (e) {}
    }
    if (items.length === 0 && parseFloat(dist.ongkos_kirim) > 0) {
        items = [{ label: 'Ongkir 1', nominal: parseFloat(dist.ongkos_kirim) }];
    }
    if (items.length === 0) {
        addNativeOngkirRow('Ongkir 1', '');
    } else {
        items.forEach(it => addNativeOngkirRow(it.label || '', it.nominal || ''));
    }
    recalcNativeOngkirTotal();
    window.ProModal.open('modalNativeKelolaOngkir');
}

function addNativeOngkirRow(label = '', nominal = '') {
    const container = document.getElementById('nativeOngkirRowsContainer');
    const idx = container.children.length + 1;
    const row = document.createElement('div');
    row.className = 'flex items-center gap-2 native-ongkir-row';
    const formattedNom = nominal ? parseInt(nominal, 10).toLocaleString('id-ID') : '';
    const safeLabel = String(label || ('Ongkir ' + idx)).replace(/"/g, '&quot;');
    row.innerHTML = `
        <input type="text" name="labels[]" value="${safeLabel}" placeholder="Keterangan (mis: Ongkir ${idx})"
               class="flex-1 px-2.5 py-1.5 bg-slate-50 border border-slate-200 rounded-lg text-xs focus:outline-none focus:border-amber-500 focus:bg-white">
        <div class="relative w-36 shrink-0">
            <span class="absolute left-2.5 top-1.5 text-[11px] font-bold text-slate-400">Rp</span>
            <input type="text" name="nominals[]" value="${formattedNom}" placeholder="0"
                   oninput="formatRupiahInput(this); recalcNativeOngkirTotal();"
                   class="w-full pl-8 pr-2.5 py-1.5 bg-slate-50 border border-slate-200 rounded-lg text-xs font-mono font-bold text-right focus:outline-none focus:border-amber-500 focus:bg-white">
        </div>
        <button type="button" onclick="this.closest('.native-ongkir-row').remove(); recalcNativeOngkirTotal();"
                class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg cursor-pointer shrink-0" title="Hapus">
            <ion-icon name="trash-outline" class="text-sm"></ion-icon>
        </button>
    `;
    container.appendChild(row);
    recalcNativeOngkirTotal();
}

function recalcNativeOngkirTotal() {
    let total = 0;
    document.querySelectorAll('#nativeOngkirRowsContainer input[name="nominals[]"]').forEach(inp => {
        const num = parseInt(String(inp.value).replace(/[^0-9]/g, ''), 10) || 0;
        total += num;
    });
    const el = document.getElementById('nativeOngkirTotal');
    if (el) el.textContent = 'Rp ' + total.toLocaleString('id-ID');
}

function submitNativeKelolaOngkir(e) {
    e.preventDefault();
    const distId = document.getElementById('nativeOngkirDistId').value;
    const formData = new FormData(document.getElementById('formNativeKelolaOngkir'));
    const kode = currentOpenKode;
    if (!kode || !distId) return;

    const btn = document.getElementById('btnSubmitNativeOngkir');
    if (btn) { btn.disabled = true; btn.textContent = 'Menyimpan...'; }

    fetch('<?= APP_URL ?>/paket/updateOngkir/' + encodeURIComponent(kode) + '/' + encodeURIComponent(distId), {
        method: 'POST',
        body: formData,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(data => {
        if (btn) { btn.disabled = false; btn.textContent = 'Simpan Ongkir'; }
        if (data.success) {
            window.ProModal.close('modalNativeKelolaOngkir');
            if (window.ProToast) window.ProToast.success(data.message || 'Ongkos kirim berhasil disimpan!');
            refreshCurrentNativeModal();
        } else {
            if (window.ProToast) window.ProToast.error(data.message || 'Gagal menyimpan ongkos kirim.');
        }
    })
    .catch(() => {
        if (btn) { btn.disabled = false; btn.textContent = 'Simpan Ongkir'; }
        window.ProModal.close('modalNativeKelolaOngkir');
        refreshCurrentNativeModal();
    });
}

function toggleNativeStatusBarangInputs() {
    const isSudah = document.getElementById('radioStatusSudahPesan')?.checked;
    const sec = document.getElementById('sectionNativeDetailPesanan');
    if (sec) {
        if (isSudah) sec.classList.remove('hidden');
        else sec.classList.add('hidden');
    }
}

function submitNativeStatusBarang(e) {
    e.preventDefault();
    const distId = document.getElementById('nativeStatusBarangDistId').value;
    const form = document.getElementById('formNativeStatusBarang');
    const formData = new FormData(form);
    const kode = currentOpenKode;
    if (!kode || !distId) return;

    const btn = document.getElementById('btnSubmitNativeStatusBarang');
    if (btn) {
        btn.disabled = true;
        btn.textContent = 'Menyimpan...';
    }

    fetch('<?= APP_URL ?>/paket/updateStatusBarang/' + encodeURIComponent(kode) + '/' + encodeURIComponent(distId), {
        method: 'POST',
        body: formData,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(data => {
        if (btn) { btn.disabled = false; btn.textContent = 'Simpan Perubahan'; }
        if (data.success) {
            window.ProModal.close('modalNativeStatusBarang');
            if (window.ProToast) window.ProToast.success(data.message || 'Status pesanan berhasil disimpan!');
            refreshCurrentNativeModal();
        } else {
            if (window.ProToast) window.ProToast.error(data.message || 'Gagal menyimpan status pesanan.');
        }
    })
    .catch(() => {
        if (btn) { btn.disabled = false; btn.textContent = 'Simpan Perubahan'; }
        window.ProModal.close('modalNativeStatusBarang');
        refreshCurrentNativeModal();
    });
}

/**
 * Native Update Status Pengiriman / Distribusi Modal Trigger & AJAX Submit
 */
function openNativeUpdateStatusDistribusiModal(dist) {
    if (!dist) return;
    document.getElementById('nativeStatusDistId').value = dist.id;
    const sub = document.getElementById('nativeStatusDistSub');
    if (sub) sub.textContent = (dist.nama_tujuan || '-') + ' (' + (dist.nama_komponen || dist.komponen || dist.nama_barang || '-') + ')';

    const sel = document.getElementById('nativeStatusDistSelect');
    if (sel) {
        let val = (dist.status_distribusi || '').trim();
        if (val.toLowerCase().includes('gudang')) {
            sel.value = 'Tiba di Gudang';
        } else if (val.toLowerCase().includes('tujuan')) {
            sel.value = 'Tiba di Tujuan';
        } else {
            sel.value = 'Tiba di Rumah';
        }
    }

    window.ProModal.open('modalNativeStatusDistribusi');
}

function submitNativeStatusDistribusi(e) {
    e.preventDefault();
    const distId = document.getElementById('nativeStatusDistId').value;
    const form = document.getElementById('formNativeStatusDistribusi');
    const formData = new FormData(form);
    const kode = currentOpenKode;
    if (!kode || !distId) return;

    const btn = document.getElementById('btnSubmitNativeStatusDist');
    if (btn) {
        btn.disabled = true;
        btn.textContent = 'Menyimpan...';
    }

    fetch('<?= APP_URL ?>/paket/updateStatusDistribusi/' + encodeURIComponent(kode) + '/' + encodeURIComponent(distId), {
        method: 'POST',
        body: formData,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(data => {
        if (btn) { btn.disabled = false; btn.textContent = 'Simpan Status'; }
        if (data.success) {
            window.ProModal.close('modalNativeStatusDistribusi');
            if (window.ProToast) window.ProToast.success(data.message || 'Status distribusi berhasil disimpan!');
            refreshCurrentNativeModal();
        } else {
            if (window.ProToast) window.ProToast.error(data.message || 'Gagal menyimpan status distribusi.');
        }
    })
    .catch(() => {
        if (btn) { btn.disabled = false; btn.textContent = 'Simpan Status'; }
        window.ProModal.close('modalNativeStatusDistribusi');
        refreshCurrentNativeModal();
    });
}

/**
 * Native Upload Foto Dokumentasi Modal Trigger
 */
function openNativeUploadFotoModal(kode) {
    const targetKode = kode || currentOpenKode;
    const form = document.getElementById('formNativeUploadFoto');
    if (form) {
        form.action = '<?= APP_URL ?>/paket/storeDokumentasi/' + encodeURIComponent(targetKode);
        document.getElementById('nativeUploadFotoRedirect').value = '<?= APP_URL ?>/paket?open_modal=' + encodeURIComponent(targetKode) + '&tab=dokumentasi';
        document.getElementById('nativeUploadFotoKeterangan').value = '';
    }
    window.ProModal.open('modalNativeUploadFoto');
}

/**
 * Delete Dokumen BAST via AJAX
 */
function deleteDokumenAjax(kode, docId, namaDoc) {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'Hapus Berkas BAST?',
            html: `Apakah Anda yakin ingin menghapus <strong>${namaDoc || 'dokumen ini'}</strong>?`,
            icon: 'warning',
            iconColor: '#f43f5e',
            showCancelButton: true,
            confirmButtonColor: '#e11d48',
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('<?= APP_URL ?>/paket/deleteDokumen/' + kode + '/' + docId + '?ajax=1')
                    .then(r => r.json())
                    .then(data => {
                        if (data.success) {
                            if (window.ProToast) window.ProToast.success(data.message || 'Dokumen berhasil dihapus.');
                            refreshCurrentNativeModal();
                        } else {
                            if (window.ProToast) window.ProToast.error(data.message || 'Gagal menghapus dokumen.');
                        }
                    })
                    .catch(() => {
                        window.location.reload();
                    });
            }
        });
    } else {
        if (confirm('Hapus berkas BAST?')) {
            window.location.href = '<?= APP_URL ?>/paket/deleteDokumen/' + kode + '/' + docId;
        }
    }
}

/**
 * Delete Item Komponen via AJAX
 */
function deleteItemAjax(kode, itemId, namaItem) {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'Hapus Item Barang?',
            html: `Hapus <strong>${namaItem || 'komponen ini'}</strong> dari paket?`,
            icon: 'warning',
            iconColor: '#f43f5e',
            showCancelButton: true,
            confirmButtonColor: '#e11d48',
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('<?= APP_URL ?>/paket/deleteItem/' + kode + '/' + itemId + '?ajax=1')
                    .then(r => r.json())
                    .then(data => {
                        if (data.success) {
                            if (window.ProToast) window.ProToast.success(data.message || 'Item berhasil dihapus.');
                            refreshCurrentNativeModal();
                        } else {
                            if (window.ProToast) window.ProToast.error(data.message || 'Gagal menghapus item.');
                        }
                    })
                    .catch(() => {
                        window.location.reload();
                    });
            }
        });
    } else {
        if (confirm('Hapus item?')) {
            window.location.href = '<?= APP_URL ?>/paket/deleteItem/' + kode + '/' + itemId;
        }
    }
}

/**
 * Delete Alokasi Distribusi via AJAX
 */
function deleteDistribusiAjax(kode, distId) {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'Hapus Alokasi Distribusi?',
            text: 'Data alokasi tujuan dan dokumentasinya akan dihapus.',
            icon: 'warning',
            iconColor: '#f43f5e',
            showCancelButton: true,
            confirmButtonColor: '#e11d48',
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('<?= APP_URL ?>/paket/deleteDistribusi/' + kode + '/' + distId + '?ajax=1')
                    .then(r => r.json())
                    .then(data => {
                        if (data.success) {
                            if (window.ProToast) window.ProToast.success(data.message || 'Alokasi berhasil dihapus.');
                            refreshCurrentNativeModal();
                        } else {
                            if (window.ProToast) window.ProToast.error(data.message || 'Gagal menghapus alokasi.');
                        }
                    })
                    .catch(() => {
                        window.location.reload();
                    });
            }
        });
    } else {
        if (confirm('Hapus alokasi distribusi?')) {
            window.location.href = '<?= APP_URL ?>/paket/deleteDistribusi/' + kode + '/' + distId;
        }
    }
}

/**
 * Delete Foto Dokumentasi via AJAX
 */
function deleteFotoDokumenAjax(kode, fotoId) {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'Hapus Foto Dokumentasi?',
            text: 'Foto bukti serah terima akan dihapus permanen.',
            icon: 'warning',
            iconColor: '#f43f5e',
            showCancelButton: true,
            confirmButtonColor: '#e11d48',
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('<?= APP_URL ?>/paket/deleteDokumentasi/' + kode + '/' + fotoId, {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    body: new URLSearchParams({ is_ajax: '1' })
                })
                    .then(r => r.json())
                    .then(data => {
                        if (data.success) {
                            if (window.ProToast) window.ProToast.success(data.message || 'Foto berhasil dihapus.');
                            refreshCurrentNativeModal();
                        } else {
                            if (window.ProToast) window.ProToast.error(data.message || 'Gagal menghapus foto.');
                        }
                    })
                    .catch(() => {
                        window.location.reload();
                    });
            }
        });
    } else {
        if (confirm('Hapus foto?')) {
            window.location.href = '<?= APP_URL ?>/paket/deleteDokumentasi/' + kode + '/' + fotoId;
        }
    }
}

/**
 * Photo Lightbox Preview (delegates to global openProPhotoViewer in footer.php)
 */
function openPhotoPreview(url, caption) {
    if (typeof openProPhotoViewer === 'function') {
        openProPhotoViewer(url, 0, caption);
    } else {
        window.open(url, '_blank');
    }
}

// Auto open modal on load if URL params has open_modal
document.addEventListener('DOMContentLoaded', function() {
    const params = new URLSearchParams(window.location.search);
    const openKode = params.get('open_modal');
    const openTab = params.get('tab') || 'info';
    if (openKode) {
        openPaketNativeModal(openKode, openTab);
        const cleanUrl = new URL(window.location.href);
        cleanUrl.searchParams.delete('open_modal');
        cleanUrl.searchParams.delete('tab');
        window.history.replaceState({}, document.title, cleanUrl.toString());
    }
});

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeModuleModal();
        closeActionMenu();
    }
});

function triggerMenuTab(tab) {
    if (!currentPaketData) return;
    openPaketModalTab(
        currentPaketData.kode_paket || currentPaketData.id,
        tab,
        currentPaketData.nama_paket
    );
}

function triggerMenuEditPaket() {
    if (!currentPaketData) return;
    const pkg = currentPaketData;
    closeActionMenu();
    openEditPaketModal(pkg);
}

function triggerMenuSkemaPotongan() {
    if (!currentPaketData) return;
    const pkg = currentPaketData;
    closeActionMenu();
    openIndexSkemaPotonganModal(pkg);
}

function triggerMenuHapusPaket() {
    if (!currentPaketData) return;
    const pkg = currentPaketData;
    closeActionMenu();
    const deleteUrl = '<?= APP_URL ?>/paket/delete/' + (pkg.kode_paket || pkg.id);
    if (typeof confirmAction === 'function') {
        confirmAction(deleteUrl, {
            title: 'Hapus Paket Kegiatan?',
            text: 'Paket "' + pkg.nama_paket + '" beserta seluruh alokasi barang akan dihapus permanen.',
            icon: 'warning',
            confirmText: 'Ya, Hapus Paket',
            confirmColor: '#e11d48'
        });
    } else {
        if (confirm('Hapus paket "' + pkg.nama_paket + '"?')) {
            window.location.href = deleteUrl;
        }
    }
}

let targetGroupSelectId = null;
let targetSkemaPrefix = 'tambah';

function openQuickAddGroup(selectId = null) {
    targetGroupSelectId = selectId;
    window.ProModal.open('modalQuickTambahGroup');
}

/**
 * Handle Quick Tambah Group via AJAX
 */
function handleQuickTambahGroup(e) {
    e.preventDefault();
    const btn = document.getElementById('btnSubmitQuickGroup');
    const nama = document.getElementById('quick_nama_group').value.trim();
    const kode = document.getElementById('quick_kode_group').value.trim();
    if (!nama) return;

    btn.disabled = true;
    btn.innerHTML = '<svg class="animate-spin -ml-0.5 mr-1.5 h-3.5 w-3.5 inline-block text-current" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> <span>Menyimpan...</span>';

    const formData = new FormData();
    formData.append('nama_group', nama);
    formData.append('kode_group', kode);

    fetch('<?= APP_URL ?>/group/ajaxStore', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        btn.disabled = false;
        btn.innerHTML = '<span>Simpan & Pilih Group</span>';

        if (data.success && data.group) {
            // Update both select dropdowns (tambah & edit)
            ['tambah_group_id', 'edit_group_id', 'filterGroupSelect'].forEach(id => {
                const sel = document.getElementById(id);
                if (sel) {
                    const opt = document.createElement('option');
                    opt.value = (id === 'filterGroupSelect') ? data.group.nama_group.toLowerCase() : data.group.id;
                    opt.textContent = data.group.nama_group;
                    sel.appendChild(opt);
                }
            });

            if (targetGroupSelectId) {
                const targetSel = document.getElementById(targetGroupSelectId);
                if (targetSel) targetSel.value = data.group.id;
            }

            window.ProModal.close('modalQuickTambahGroup');
            window.ProToast.success(data.message || 'Group berhasil dibuat!');
            document.getElementById('formQuickGroup').reset();
        } else {
            window.ProToast.error(data.message || 'Gagal membuat group.');
        }
    })
    .catch(err => {
        btn.disabled = false;
        btn.innerHTML = '<span>Simpan & Pilih Group</span>';
        window.ProToast.error('Koneksi terputus saat menyimpan group.');
    });
}

/**
 * Format string rupiah ke angka murni
 */
function parseRupiahNumber(str) {
    if (!str) return 0;
    return parseFloat(str.toString().replace(/[^0-9]/g, '')) || 0;
}

/**
 * Format angka ke format Rupiah standar
 */
function formatRupiahString(num) {
    return 'Rp ' + Math.round(num).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}

/**
 * Auto-format input saat mengetik
 */
function formatRupiahInput(input) {
    let val = input.value.replace(/[^0-9]/g, '');
    if (val) {
        input.value = parseInt(val, 10).toLocaleString('id-ID');
    } else {
        input.value = '';
    }
}

/**
 * Toggle Checkbox Potongan Pasal
 */
function togglePasalCheck(prefix) {
    const chk = document.getElementById(prefix + '_ada_pasal');
    const input = document.getElementById(prefix + '_persen_pasal');
    const card = document.getElementById(prefix + '_card_pasal');
    const badge = document.getElementById(prefix + '_badge_pasal');

    if (chk && chk.checked) {
        if (input) {
            input.disabled = false;
            input.classList.remove('opacity-40', 'bg-slate-100');
            input.classList.add('bg-white');
            if (!input.value || parseFloat(input.value) === 0) {
                input.value = 13;
            }
        }
        if (card) card.classList.remove('opacity-60');
        if (badge) {
            badge.textContent = 'Aktif';
            badge.className = 'text-[10px] text-emerald-600 font-semibold';
        }
    } else {
        if (input) {
            input.disabled = true;
            input.classList.add('opacity-40', 'bg-slate-100');
            input.classList.remove('bg-white');
        }
        if (card) card.classList.add('opacity-60');
        if (badge) {
            badge.textContent = 'Nonaktif';
            badge.className = 'text-[10px] text-slate-400 font-medium';
        }
    }

    if (prefix === 'potongan') updateIndexPotonganCalc();
}

/**
 * Toggle Checkbox Biaya Admin
 */
function toggleAdminCheck(prefix) {
    const chk = document.getElementById(prefix + '_ada_admin');
    const input = document.getElementById(prefix + '_persen_admin');
    const card = document.getElementById(prefix + '_card_admin');
    const badge = document.getElementById(prefix + '_badge_admin');

    if (chk && chk.checked) {
        if (input) {
            input.disabled = false;
            input.classList.remove('opacity-40', 'bg-slate-100');
            input.classList.add('bg-white');
            if (!input.value || parseFloat(input.value) === 0) {
                input.value = 2;
            }
        }
        if (card) card.classList.remove('opacity-60');
        if (badge) {
            badge.textContent = 'Aktif';
            badge.className = 'text-[10px] text-emerald-600 font-semibold';
        }
    } else {
        if (input) {
            input.disabled = true;
            input.classList.add('opacity-40', 'bg-slate-100');
            input.classList.remove('bg-white');
        }
        if (card) card.classList.add('opacity-60');
        if (badge) {
            badge.textContent = 'Nonaktif';
            badge.className = 'text-[10px] text-slate-400 font-medium';
        }
    }

    if (prefix === 'potongan') updateIndexPotonganCalc();
}

/**
 * Event Listener saat Skema Dipilih dari Dropdown
 */
function onSkemaSelectChange(prefix, sel) {
    const opt = sel.options[sel.selectedIndex];
    if (!opt || !opt.value) return;

    const adaPasal = opt.getAttribute('data-ada-pasal') == '1';
    const pasalVal = parseFloat(opt.getAttribute('data-pasal')) || 0;
    const adaAdmin = opt.getAttribute('data-ada-admin') == '1';
    const adminVal = parseFloat(opt.getAttribute('data-admin')) || 0;

    const chkPasal = document.getElementById(prefix + '_ada_pasal');
    const inputPasal = document.getElementById(prefix + '_persen_pasal');
    if (chkPasal) chkPasal.checked = adaPasal;
    if (inputPasal) inputPasal.value = pasalVal;
    togglePasalCheck(prefix);

    const chkAdmin = document.getElementById(prefix + '_ada_admin');
    const inputAdmin = document.getElementById(prefix + '_persen_admin');
    if (chkAdmin) chkAdmin.checked = adaAdmin;
    if (inputAdmin) inputAdmin.value = adminVal;
    toggleAdminCheck(prefix);

    const tipePotongan = opt.getAttribute('data-tipe') || 'dpp';
    const chkTipe = document.getElementById(prefix + '_tipe_potongan');
    if (chkTipe) {
        chkTipe.checked = (tipePotongan === 'kontrak');
    }

    if (prefix === 'potongan') updateIndexPotonganCalc();
}

/**
 * Buka Modal Quick Tambah Skema
 */
function openQuickAddSkema(prefix = 'potongan') {
    targetSkemaPrefix = prefix;
    const chkPasal = document.getElementById(prefix + '_ada_pasal');
    const valPasal = document.getElementById(prefix + '_persen_pasal');
    const chkAdmin = document.getElementById(prefix + '_ada_admin');
    const valAdmin = document.getElementById(prefix + '_persen_admin');

    document.getElementById('quick_ada_pasal').checked = chkPasal ? chkPasal.checked : true;
    document.getElementById('quick_persen_pasal').value = valPasal ? valPasal.value : 13;
    document.getElementById('quick_ada_admin').checked = chkAdmin ? chkAdmin.checked : true;
    document.getElementById('quick_persen_admin').value = valAdmin ? valAdmin.value : 2;
    document.getElementById('quick_nama_skema').value = '';

    const chkTipe = document.getElementById(prefix + '_tipe_potongan');
    const quickTipe = document.getElementById('quick_tipe_potongan');
    if (quickTipe) quickTipe.checked = chkTipe ? chkTipe.checked : false;

    toggleQuickModalFields();
    updateQuickModalTotal();
    window.ProModal.open('modalQuickTambahSkema');
}

function promptSaveCurrentAsSkema(prefix = 'potongan') {
    openQuickAddSkema(prefix);
}

function toggleQuickModalFields() {
    const chkP = document.getElementById('quick_ada_pasal');
    const inP = document.getElementById('quick_persen_pasal');
    if (inP) {
        inP.disabled = !chkP.checked;
        inP.classList.toggle('opacity-40', !chkP.checked);
    }

    const chkA = document.getElementById('quick_ada_admin');
    const inA = document.getElementById('quick_persen_admin');
    if (inA) {
        inA.disabled = !chkA.checked;
        inA.classList.toggle('opacity-40', !chkA.checked);
    }

    updateQuickModalTotal();
}

function updateQuickModalTotal() {
    const chkP = document.getElementById('quick_ada_pasal');
    const pVal = chkP.checked ? (parseFloat(document.getElementById('quick_persen_pasal').value) || 0) : 0;
    const chkA = document.getElementById('quick_ada_admin');
    const aVal = chkA.checked ? (parseFloat(document.getElementById('quick_persen_admin').value) || 0) : 0;
    const total = Math.round((pVal + aVal) * 100) / 100;
    document.getElementById('quick_lbl_total_persen').textContent = total + '%';
}

/**
 * Handle Simpan Skema Baru via AJAX
 */
function handleQuickTambahSkema(e) {
    e.preventDefault();
    const btn = document.getElementById('btnSubmitQuickSkema');
    const nama = document.getElementById('quick_nama_skema').value.trim();
    if (!nama) return;

    const chkP = document.getElementById('quick_ada_pasal').checked ? 1 : 0;
    const valP = parseFloat(document.getElementById('quick_persen_pasal').value) || 0;
    const chkA = document.getElementById('quick_ada_admin').checked ? 1 : 0;
    const valA = parseFloat(document.getElementById('quick_persen_admin').value) || 0;
    const chkK = document.getElementById('quick_tipe_potongan');
    const valTipe = (chkK && chkK.checked) ? 'kontrak' : 'dpp';

    btn.disabled = true;
    btn.innerHTML = '<svg class="animate-spin -ml-0.5 mr-1.5 h-3.5 w-3.5 inline-block text-current" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> <span>Menyimpan...</span>';

    const formData = new FormData();
    formData.append('nama_skema', nama);
    formData.append('ada_pasal', chkP);
    formData.append('persen_pasal', valP);
    formData.append('ada_admin', chkA);
    formData.append('persen_admin', valA);
    formData.append('tipe_potongan', valTipe);

    fetch('<?= APP_URL ?>/paket/ajaxStoreSkema', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        btn.disabled = false;
        btn.innerHTML = '<ion-icon name="checkmark-outline" class="text-sm"></ion-icon> <span>Simpan & Gunakan</span>';

        if (data.success && data.skema) {
            const sk = data.skema;
            ['potongan_skema_id', 'tambah_skema_id', 'edit_skema_id'].forEach(id => {
                const sel = document.getElementById(id);
                if (sel) {
                    const opt = document.createElement('option');
                    opt.value = sk.id;
                    opt.textContent = sk.nama_skema;
                    opt.setAttribute('data-ada-pasal', sk.ada_pasal);
                    opt.setAttribute('data-pasal', sk.persen_pasal);
                    opt.setAttribute('data-ada-admin', sk.ada_admin);
                    opt.setAttribute('data-admin', sk.persen_admin);
                    opt.setAttribute('data-tipe', sk.tipe_potongan || 'dpp');
                    sel.appendChild(opt);
                }
            });

            const targetSel = document.getElementById(targetSkemaPrefix + '_skema_id');
            if (targetSel) {
                targetSel.value = sk.id;
                onSkemaSelectChange(targetSkemaPrefix, targetSel);
            }

            window.ProModal.close('modalQuickTambahSkema');
            window.ProToast.success(data.message || 'Skema berhasil disimpan!');
            document.getElementById('formQuickSkema').reset();
        } else {
            window.ProToast.error(data.message || 'Gagal menyimpan skema.');
        }
    })
    .catch(err => {
        btn.disabled = false;
        btn.innerHTML = '<ion-icon name="checkmark-outline" class="text-sm"></ion-icon> <span>Simpan & Gunakan</span>';
        window.ProToast.error('Koneksi terputus saat menyimpan skema.');
    });
}

/**
 * Perhitungan Live Standalone Modal Skema Potongan (Index)
 */
function updateIndexPotonganCalc() {
    const hiddenAcuan = document.getElementById('potongan_acuan_hidden');
    const acuan = hiddenAcuan ? (parseFloat(hiddenAcuan.value) || 0) : 0;
    
    const chkPasal = document.getElementById('potongan_ada_pasal');
    const adaPasal = chkPasal ? chkPasal.checked : true;
    const inPasal = document.getElementById('potongan_persen_pasal');
    const persenPasal = (adaPasal && inPasal) ? (parseFloat(inPasal.value) || 0) : 0;
    
    const chkAdmin = document.getElementById('potongan_ada_admin');
    const adaAdmin = chkAdmin ? chkAdmin.checked : true;
    const inAdmin = document.getElementById('potongan_persen_admin');
    const persenAdmin = (adaAdmin && inAdmin) ? (parseFloat(inAdmin.value) || 0) : 0;

    const dpp = acuan > 0 ? Math.round(acuan / 1.11) : 0;
    const chkKontrak = document.getElementById('potongan_tipe_potongan');
    const isKontrak = chkKontrak ? chkKontrak.checked : false;
    const basis = isKontrak ? acuan : dpp;

    const pasal = Math.round(basis * (persenPasal / 100));
    const admin = Math.round(basis * (persenAdmin / 100));
    const totalPasal = pasal + admin;
    const totalPersen = Math.round((persenPasal + persenAdmin) * 100) / 100;

    const elAcuan = document.getElementById('potongan_preview_acuan');
    if (elAcuan) elAcuan.textContent = formatRupiahString(acuan);
    const elDpp = document.getElementById('potongan_preview_dpp');
    if (elDpp) elDpp.textContent = formatRupiahString(dpp);
    const elPasal = document.getElementById('potongan_preview_pasal13');
    if (elPasal) elPasal.textContent = formatRupiahString(pasal);
    const elAdmin = document.getElementById('potongan_preview_admin2');
    if (elAdmin) elAdmin.textContent = formatRupiahString(admin);
    const elTotal = document.getElementById('potongan_preview_total_pasal');
    if (elTotal) elTotal.textContent = formatRupiahString(totalPasal);

    const elLblP = document.getElementById('potongan_lbl_pasal');
    if (elLblP) elLblP.textContent = persenPasal;
    const elLblA = document.getElementById('potongan_lbl_admin');
    if (elLblA) elLblA.textContent = persenAdmin;
    const elLblTot = document.getElementById('potongan_lbl_total_pasal');
    if (elLblTot) elLblTot.textContent = totalPersen;

    const lblSum = document.getElementById('potongan_lbl_sum_persen');
    if (lblSum) lblSum.textContent = totalPersen + '%';
}

/**
 * Buka Modal Skema Potongan Mandiri (Index)
 */
function openIndexSkemaPotonganModal(p) {
    if (!p) return;
    const form = document.getElementById('formSkemaPotongan');
    form.action = '<?= APP_URL ?>/paket/updatePotongan/' + (p.kode_paket || p.id);
    
    const subtitle = document.getElementById('potongan_paket_subtitle');
    if (subtitle) {
        subtitle.textContent = (p.nama_paket || '') + (p.kode_paket ? ' [' + p.kode_paket + ']' : '');
    }

    const itemTotal = parseFloat(p.total_item_harga) || 0;
    const pagu = itemTotal > 0 ? itemTotal : (parseFloat(p.pagu) || 0);
    const paguNego = (p.pagu_nego && parseFloat(p.pagu_nego) > 0) ? parseFloat(p.pagu_nego) : 0;
    const acuan = paguNego > 0 ? paguNego : pagu;
    const hiddenAcuan = document.getElementById('potongan_acuan_hidden');
    if (hiddenAcuan) hiddenAcuan.value = acuan;

    const selSkema = document.getElementById('potongan_skema_id');
    if (selSkema) selSkema.value = p.skema_id || '';

    const persenPasal = (p.persen_pasal !== undefined && p.persen_pasal !== null && p.persen_pasal !== '') ? parseFloat(p.persen_pasal) : 13;
    const persenAdmin = (p.persen_admin !== undefined && p.persen_admin !== null && p.persen_admin !== '') ? parseFloat(p.persen_admin) : 2;

    const chkPasal = document.getElementById('potongan_ada_pasal');
    if (chkPasal) chkPasal.checked = (persenPasal > 0);
    const inPasal = document.getElementById('potongan_persen_pasal');
    if (inPasal) inPasal.value = persenPasal > 0 ? persenPasal : 13;
    togglePasalCheck('potongan');

    const chkAdmin = document.getElementById('potongan_ada_admin');
    if (chkAdmin) chkAdmin.checked = (persenAdmin > 0);
    const inAdmin = document.getElementById('potongan_persen_admin');
    if (inAdmin) inAdmin.value = persenAdmin > 0 ? persenAdmin : 2;
    toggleAdminCheck('potongan');

    const chkTipe = document.getElementById('potongan_tipe_potongan');
    if (chkTipe) chkTipe.checked = (p.tipe_potongan === 'kontrak');

    updateIndexPotonganCalc();
    window.ProModal.open('modalSkemaPotongan');
}

/**
 * Buka Modal Ubah Status Pencairan (Popup Mandiri)
 */
function openStatusPencairanModal(p) {
    if (!p) return;
    const form = document.getElementById('formStatusPencairan');
    form.action = '<?= APP_URL ?>/paket/updateStatus/' + (p.kode_paket || p.id);

    const subtitle = document.getElementById('status_pencairan_subtitle');
    if (subtitle) {
        subtitle.textContent = (p.nama_paket || '') + (p.kode_paket ? ' [' + p.kode_paket + ']' : '');
    }

    const st = (p.status || 'Proses').toLowerCase();
    const optBelum = document.getElementById('status_opt_belum');
    const optProses = document.getElementById('status_opt_proses');
    const optSelesai = document.getElementById('status_opt_selesai');

    if (st === 'belum' && optBelum) {
        optBelum.checked = true;
    } else if ((st === 'selesai' || st === 'cair') && optSelesai) {
        optSelesai.checked = true;
    } else if (optProses) {
        optProses.checked = true;
    }

    window.ProModal.open('modalStatusPencairan');
}

/**
 * Buka Modal Edit Paket & Isi Otomatis
 */
function openEditPaketModal(p) {
    if (!p) return;

    const form = document.getElementById('formEditPaket');
    form.action = '<?= APP_URL ?>/paket/update/' + (p.kode_paket || p.id);

    document.getElementById('edit_nama_paket').value = p.nama_paket || '';
    if (document.getElementById('edit_nomor_sp')) {
        document.getElementById('edit_nomor_sp').value = p.nomor_sp || '';
    }
    if (document.getElementById('edit_tags')) {
        document.getElementById('edit_tags').value = p.tags || '';
    }
    if (document.getElementById('edit_kab_kota')) {
        document.getElementById('edit_kab_kota').value = p.kab_kota || '';
    }
    document.getElementById('edit_perusahaan_id').value = p.perusahaan_id || '';
    const namaPerusahaan = p.perusahaan_nama || p.perusahaan || '-';
    const spanNama = document.getElementById('edit_perusahaan_nama');
    if (spanNama) spanNama.textContent = namaPerusahaan;
    document.getElementById('edit_group_id').value = p.group_id || '';
    if (document.getElementById('edit_tahun_anggaran')) {
        document.getElementById('edit_tahun_anggaran').value = p.tahun_anggaran || new Date().getFullYear();
    }
    if (document.getElementById('edit_penanggung_jawab')) document.getElementById('edit_penanggung_jawab').value = p.penanggung_jawab || '';
    if (document.getElementById('edit_progres_barang')) document.getElementById('edit_progres_barang').value = p.progres_barang || 'Belum';
    if (document.getElementById('edit_status')) document.getElementById('edit_status').value = p.status || 'Proses';
    document.getElementById('editPaketSubtitle').textContent = 'Mengubah: ' + (p.nama_paket || '') + (p.kode_paket ? ' [' + p.kode_paket + ']' : '');

    const itemTotal = parseFloat(p.total_item_harga) || 0;
    const effectivePagu = itemTotal > 0 ? itemTotal : (parseFloat(p.pagu) || 0);
    const paguInput = document.getElementById('edit_pagu');
    paguInput.value = effectivePagu ? Math.round(effectivePagu).toLocaleString('id-ID') : '';

    const negoInput = document.getElementById('edit_pagu_nego');
    negoInput.value = (p.pagu_nego && p.pagu_nego > 0) ? parseInt(p.pagu_nego, 10).toLocaleString('id-ID') : '';

    window.ProModal.open('modalEditPaket');
}

/**
 * Quick Filter Paket Table
 */
function filterPaketTable() {
    const searchVal = document.getElementById('searchPaketInput').value.toLowerCase();
    const groupVal = document.getElementById('filterGroupSelect').value.toLowerCase();
    const tahunVal = document.getElementById('filterTahunSelect') ? document.getElementById('filterTahunSelect').value : '';
    const tagVal = document.getElementById('filterTagSelect') ? document.getElementById('filterTagSelect').value.toLowerCase() : '';
    const statusVal = document.getElementById('filterStatusSelect').value.toLowerCase();
    
    const table = document.getElementById('paketTable');
    if (!table) return;
    const rows = table.querySelectorAll('tbody tr:not(#emptyTableRow)');

    let visibleCount = 0;
    let sumKontrak = 0, sumDpp = 0, sumPasal = 0, sumBersih = 0;
    let sumHpp = 0, sumProfit = 0, sumItems = 0, sumUnits = 0;

    rows.forEach(row => {
        const text = row.innerText.toLowerCase();
        const rowGroup = row.getAttribute('data-group-name') || '';
        const rowTahun = row.getAttribute('data-tahun') || '';
        const rowTags = row.getAttribute('data-tags') || '';
        const rowStatus = row.getAttribute('data-status') || '';

        const matchSearch = text.includes(searchVal);
        
        let matchGroup = true;
        if (groupVal === '__none__') {
            matchGroup = (rowGroup === '');
        } else if (groupVal !== '') {
            matchGroup = rowGroup.includes(groupVal);
        }

        let matchTahun = true;
        if (tahunVal !== '') {
            matchTahun = (rowTahun === tahunVal);
        }

        let matchTag = true;
        if (tagVal !== '') {
            matchTag = rowTags.includes(tagVal);
        }

        let matchStatus = true;
        if (statusVal !== '') {
            matchStatus = (rowStatus === statusVal);
        }

        if (matchSearch && matchGroup && matchTahun && matchTag && matchStatus) {
            row.style.display = '';
            visibleCount++;
            sumKontrak += parseFloat(row.getAttribute('data-kontrak') || '0');
            sumDpp     += parseFloat(row.getAttribute('data-dpp') || '0');
            sumPasal   += parseFloat(row.getAttribute('data-pasal') || '0');
            sumBersih  += parseFloat(row.getAttribute('data-bersih') || '0');
            sumHpp     += parseFloat(row.getAttribute('data-hpp') || '0');
            sumProfit  += parseFloat(row.getAttribute('data-profit') || '0');
            sumItems   += parseInt(row.getAttribute('data-items') || '0', 10);
            sumUnits   += parseFloat(row.getAttribute('data-units') || '0');
        } else {
            row.style.display = 'none';
        }
    });

    const footer = document.getElementById('paketTableFooter');
    if (footer) {
        const fmtRp = (n) => {
            const neg = n < 0;
            const absVal = Math.round(Math.abs(n)).toLocaleString('id-ID');
            return (neg ? '-Rp ' : 'Rp ') + absVal;
        };
        const setEl = (id, val) => { const el = document.getElementById(id); if (el) el.textContent = val; };
        setEl('totalPaketCount', visibleCount);
        setEl('totalKontrakVal', fmtRp(sumKontrak));
        setEl('totalDppVal', fmtRp(sumDpp));
        setEl('totalPasalVal', fmtRp(sumPasal));
        setEl('totalBersihVal', fmtRp(sumBersih));
        setEl('totalHppVal', fmtRp(sumHpp));
        setEl('totalProfitVal', fmtRp(sumProfit));
        setEl('totalItemCount', sumItems.toLocaleString('id-ID') + ' Item');
        setEl('totalUnitCount', Math.round(sumUnits).toLocaleString('id-ID') + ' Unit');

        const profitEl = document.getElementById('totalProfitVal');
        const pctEl = document.getElementById('totalProfitPct');
        const pct = sumKontrak > 0 ? Math.round((sumProfit / sumKontrak) * 1000) / 10 : 0;
        const isLoss = sumProfit < 0;
        if (profitEl) {
            profitEl.className = 'font-mono font-extrabold ' + (isLoss ? 'text-rose-600' : 'text-emerald-700');
        }
        if (pctEl) {
            pctEl.textContent = (pct > 0 ? '+' : '') + pct + '%';
            pctEl.className = 'inline-flex items-center px-1.5 py-0.2 rounded text-[9px] font-bold font-mono border mt-0.5 ' +
                (isLoss ? 'bg-rose-50 text-rose-700 border-rose-200' : 'bg-emerald-50 text-emerald-700 border-emerald-200/80');
        }
    }
}

function filterByTag(tag) {
    const sel = document.getElementById('filterTagSelect');
    if (sel) {
        sel.value = tag.toLowerCase();
    }
    filterPaketTable();
}

function setQuickStatusPill(statusVal) {
    const sel = document.getElementById('filterStatusSelect');
    if (sel) sel.value = statusVal;

    document.querySelectorAll('.pill-status-btn').forEach(btn => {
        btn.className = 'pill-status-btn px-3 py-1.5 rounded-xl font-semibold transition-all bg-slate-100 hover:bg-slate-200 text-slate-700 flex items-center gap-1.5 shrink-0';
    });

    const activeId = statusVal ? 'pill_status_' + statusVal : 'pill_status_all';
    const activeBtn = document.getElementById(activeId);
    if (activeBtn) {
        activeBtn.className = 'pill-status-btn px-3 py-1.5 rounded-xl font-bold transition-all bg-slate-900 text-white shadow-2xs flex items-center gap-1.5 shrink-0';
    }

    filterPaketTable();
}

function resetPaketFilters() {
    document.getElementById('searchPaketInput').value = '';
    document.getElementById('filterGroupSelect').value = '';
    if (document.getElementById('filterTahunSelect')) document.getElementById('filterTahunSelect').value = '';
    if (document.getElementById('filterTagSelect')) document.getElementById('filterTagSelect').value = '';
    document.getElementById('filterStatusSelect').value = '';
    
    document.querySelectorAll('.pill-status-btn').forEach(btn => {
        btn.className = 'pill-status-btn px-3 py-1.5 rounded-xl font-semibold transition-all bg-slate-100 hover:bg-slate-200 text-slate-700 flex items-center gap-1.5 shrink-0';
    });
    const allBtn = document.getElementById('pill_status_all');
    if (allBtn) allBtn.className = 'pill-status-btn px-3 py-1.5 rounded-xl font-bold transition-all bg-slate-900 text-white shadow-2xs flex items-center gap-1.5 shrink-0';

    filterPaketTable();
}
</script>
