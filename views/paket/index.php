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
                        onclick="window.ProToast ? ProToast.warning('Perusahaan masih kosong. Silakan buat perusahaan terlebih dahulu.') : alert('Perusahaan masih kosong. Silakan buat perusahaan terlebih dahulu.'); window.location.href='<?= APP_URL ?>/perusahaan/create';"
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
<?php
$countAll = count($paket);
$countProses = 0;
$countCair = 0;
$countBelum = 0;
foreach ($paket as $p) {
    $st = strtolower($p['status'] ?? '');
    if ($st === 'cair') $countCair++;
    elseif ($st === 'proses') $countProses++;
    elseif ($st === 'belum') $countBelum++;
}
?>
<div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 p-3 mb-4">
    <!-- Top Row: Quick Status Pills -->
    <div class="flex items-center gap-1.5 overflow-x-auto pb-2.5 border-b border-slate-100 mb-3 text-xs scrollbar-none">
        <button type="button" onclick="setQuickStatusPill('')" id="pill_status_all"
                class="pill-status-btn px-3 py-1.5 rounded-xl font-bold transition-all bg-slate-900 text-white shadow-2xs flex items-center gap-1.5 shrink-0">
            <span>Semua Status</span>
            <span class="px-1.5 py-0.2 rounded-full text-[10px] bg-white/20"><?= $countAll ?></span>
        </button>
        <button type="button" onclick="setQuickStatusPill('proses')" id="pill_status_proses"
                class="pill-status-btn px-3 py-1.5 rounded-xl font-semibold transition-all bg-slate-100 hover:bg-slate-200 text-slate-700 flex items-center gap-1.5 shrink-0">
            <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
            <span>Proses</span>
            <span class="px-1.5 py-0.2 rounded-full text-[10px] bg-slate-200/80 text-slate-800"><?= $countProses ?></span>
        </button>
        <button type="button" onclick="setQuickStatusPill('cair')" id="pill_status_cair"
                class="pill-status-btn px-3 py-1.5 rounded-xl font-semibold transition-all bg-slate-100 hover:bg-slate-200 text-slate-700 flex items-center gap-1.5 shrink-0">
            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
            <span>Cair</span>
            <span class="px-1.5 py-0.2 rounded-full text-[10px] bg-slate-200/80 text-slate-800"><?= $countCair ?></span>
        </button>
        <button type="button" onclick="setQuickStatusPill('belum')" id="pill_status_belum"
                class="pill-status-btn px-3 py-1.5 rounded-xl font-semibold transition-all bg-slate-100 hover:bg-slate-200 text-slate-700 flex items-center gap-1.5 shrink-0">
            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
            <span>Belum</span>
            <span class="px-1.5 py-0.2 rounded-full text-[10px] bg-slate-200/80 text-slate-800"><?= $countBelum ?></span>
        </button>
    </div>

    <div class="flex flex-col md:flex-row items-center justify-between gap-3">
        <!-- Left: Search Box -->
        <div class="relative w-full md:w-72">
            <ion-icon name="search-outline" class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-base"></ion-icon>
            <input type="text" id="searchPaketInput" onkeyup="filterPaketTable()" 
                   placeholder="Cari paket, PJ, group..." 
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

            <!-- Filter Progres Barang -->
            <select id="filterProgresSelect" onchange="filterPaketTable()" 
                    class="px-2.5 py-1.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700 focus:outline-none focus:border-blue-500 transition-all cursor-pointer">
                <option value="">Semua Progres</option>
                <option value="belum">BELUM</option>
                <option value="belanja">BELANJA</option>
                <option value="distribusi">DISTRIBUSI</option>
                <option value="selesai">SELESAI</option>
            </select>

            <!-- Filter Status Pencairan -->
            <select id="filterStatusSelect" onchange="filterPaketTable()" 
                    class="px-2.5 py-1.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700 focus:outline-none focus:border-blue-500 transition-all cursor-pointer">
                <option value="">Semua Status</option>
                <option value="cair">CAIR</option>
                <option value="proses">PROSES</option>
                <option value="belum">BELUM</option>
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
            <thead class="bg-emerald-50/70 text-[10px] uppercase tracking-wider text-slate-700 font-bold border-b border-emerald-200/80">
                <tr>
                    <th class="py-2.5 px-3 w-16 text-center">KODE</th>
                    <th class="py-2.5 px-3 min-w-[190px]">Nama Paket</th>
                    <th class="py-2.5 px-3 text-right">Nilai Kontrak</th>
                    <th class="py-2.5 px-3 text-center">Penanggung Jawab</th>
                    <th class="py-2.5 px-3 text-center">Progres Barang</th>
                    <th class="py-2.5 px-3 text-right bg-emerald-100/40">DPP</th>
                    <th class="py-2.5 px-3 text-right">Potongan Pasal</th>
                    <th class="py-2.5 px-3 text-right">Admin</th>
                    <th class="py-2.5 px-3 text-right bg-amber-50/70 text-amber-900 font-extrabold">Total Pasal</th>
                    <th class="py-2.5 px-3 text-center">Status</th>
                    <th class="py-2.5 px-3 text-right w-16">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php if (empty($paket)): ?>
                    <tr id="emptyTableRow">
                        <td colspan="11" class="py-12 text-center text-slate-400">
                            <?php if (empty($activeCompany)): ?>
                                <div class="w-12 h-12 bg-amber-50 rounded-2xl flex items-center justify-center mx-auto mb-2 text-2xl text-amber-500">
                                    <ion-icon name="business-outline"></ion-icon>
                                </div>
                                <h4 class="text-xs font-bold text-slate-700 mb-0.5">Perusahaan Masih Kosong</h4>
                                <p class="text-[11px] text-slate-400 mb-3">Anda harus membuat profil perusahaan terlebih dahulu sebelum dapat menambahkan paket kegiatan.</p>
                                <a href="<?= APP_URL ?>/perusahaan/create" 
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
                    <?php foreach ($paket as $p): 
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
                    ?>
                        <tr class="hover:bg-slate-50/70 transition-colors group"
                            data-group-name="<?= htmlspecialchars(strtolower($p['nama_group'] ?? '')) ?>"
                            data-progres="<?= htmlspecialchars(strtolower($p['progres_barang'] ?? 'belum')) ?>"
                            data-status="<?= htmlspecialchars(strtolower($p['status'] ?? 'proses')) ?>">
                            
                            <!-- KODE (5-digit) -->
                            <td class="py-2.5 px-3 text-center">
                                <span class="px-2 py-0.5 font-mono font-bold text-[11px] bg-slate-100 text-slate-700 rounded-md tracking-wider">
                                    <?= htmlspecialchars($p['kode_paket'] ?: '#' . $p['id']) ?>
                                </span>
                            </td>

                            <!-- Nama Paket & Group Badge -->
                            <td class="py-2.5 px-3">
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    <a href="<?= APP_URL ?>/paket/detail/<?= $p['kode_paket'] ?: $p['id'] ?>" 
                                       class="font-bold text-slate-800 hover:text-blue-600 text-xs transition-colors">
                                        <?= htmlspecialchars($p['nama_paket']) ?>
                                    </a>
                                    <?php if (!empty($p['nama_group'])): ?>
                                        <a href="<?= APP_URL ?>/group/detail/<?= $p['group_id'] ?>" 
                                            class="inline-flex items-center gap-1 px-1.5 py-0.2 rounded bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-semibold text-[9px] border border-indigo-200 transition-colors"
                                            title="Group: <?= htmlspecialchars($p['nama_group']) ?>">
                                            <ion-icon name="folder-outline"></ion-icon>
                                            <span><?= htmlspecialchars($p['nama_group']) ?></span>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </td>

                            <!-- Nilai Kontrak (Pagu / Nego Acuan) -->
                            <td class="py-2.5 px-3 text-right whitespace-nowrap">
                                <?php if ($hasNego): ?>
                                    <div class="font-mono font-bold text-emerald-600 text-xs" title="Pagu Akhir (Hasil Negosiasi)">
                                        <?= formatRupiah($p['pagu_nego']) ?>
                                    </div>
                                    <div class="text-[10px] text-slate-400 line-through" title="Total Nilai Komponen Item">
                                        <?= formatRupiah($effectivePagu) ?>
                                    </div>
                                <?php else: ?>
                                    <div class="font-mono font-bold text-slate-800 text-xs" title="Total Nilai Komponen Item">
                                        <?= formatRupiah($effectivePagu) ?>
                                    </div>
                                <?php endif; ?>
                            </td>

                            <!-- Penanggung Jawab -->
                            <td class="py-2.5 px-3 text-center whitespace-nowrap">
                                <?= getPenanggungJawabBadge($p['penanggung_jawab'] ?? '') ?>
                            </td>

                            <!-- Progres Barang -->
                            <td class="py-2.5 px-3 text-center whitespace-nowrap">
                                <?= getProgresBarangBadge($p['progres_barang'] ?? 'Belum') ?>
                            </td>

                            <!-- DPP (-11% PPN) -->
                            <td class="py-2.5 px-3 text-right font-mono font-semibold text-slate-800 text-xs bg-emerald-50/20 whitespace-nowrap">
                                <?= formatRupiah($dpp) ?>
                            </td>

                            <!-- Potongan Pasal (Fleksibel) -->
                            <td class="py-2.5 px-3 text-right whitespace-nowrap">
                                <div class="font-mono text-slate-800 text-xs font-semibold"><?= formatRupiah($pasalVal) ?></div>
                                <div class="text-[10px] text-slate-400 font-medium"><?= (float)$persenPasal ?>%</div>
                            </td>

                            <!-- 2% / Admin (Fleksibel) -->
                            <td class="py-2.5 px-3 text-right whitespace-nowrap">
                                <?php if ($persenAdmin > 0): ?>
                                    <div class="font-mono text-slate-700 text-xs"><?= formatRupiah($adminVal) ?></div>
                                    <div class="text-[10px] text-slate-400 font-medium"><?= (float)$persenAdmin ?>%</div>
                                <?php else: ?>
                                    <span class="text-slate-400 text-xs font-medium" title="Tanpa potongan admin">-</span>
                                <?php endif; ?>
                            </td>

                            <!-- Total Pasal -->
                            <td class="py-2.5 px-3 text-right whitespace-nowrap bg-amber-50/40">
                                <div class="font-mono font-bold text-amber-800 text-xs">
                                    <?= formatRupiah($totalPasal) ?>
                                </div>
                                <div class="text-[10px] text-amber-700/80 font-bold"><?= (float)$totalPersen ?>%</div>
                            </td>

                            <!-- Status (Cair / Proses) -->
                            <td class="py-2.5 px-3 text-center whitespace-nowrap">
                                <?= getStatusBadge($p['status'] ?? 'Proses') ?>
                            </td>

                            <!-- Aksi -->
                            <td class="py-2.5 px-3 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-1">
                                    <!-- Detail Link -->
                                    <a href="<?= APP_URL ?>/paket/detail/<?= $p['kode_paket'] ?: $p['id'] ?>" 
                                       class="p-1.5 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors text-base"
                                       title="Lihat Detail & Rekap">
                                        <ion-icon name="eye-outline"></ion-icon>
                                    </a>

                                    <?php if ($role === 'admin' || $role === 'user'): ?>
                                        <!-- Skema Potongan Modal Trigger -->
                                        <button type="button" 
                                                onclick='openIndexSkemaPotonganModal(<?= json_encode($p, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)'
                                                class="p-1.5 text-slate-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-colors text-base"
                                                title="Atur Skema Potongan">
                                            <ion-icon name="options-outline"></ion-icon>
                                        </button>

                                        <!-- Edit Modal Trigger -->
                                        <button type="button" 
                                                onclick='openEditPaketModal(<?= json_encode($p, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)'
                                                class="p-1.5 text-slate-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-colors text-base"
                                                title="Edit Paket (Popup)">
                                            <ion-icon name="create-outline"></ion-icon>
                                        </button>

                                        <!-- Hapus via SweetAlert2 -->
                                        <a href="<?= APP_URL ?>/paket/delete/<?= $p['kode_paket'] ?: $p['id'] ?>" 
                                           data-confirm-delete="<?= htmlspecialchars($p['nama_paket']) ?>"
                                           data-type="Paket Kegiatan"
                                           class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors text-base"
                                           title="Hapus Paket">
                                            <ion-icon name="trash-outline"></ion-icon>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
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
    <div class="modal-card bg-white rounded-2xl shadow-2xl border border-slate-100 max-w-2xl w-full transform scale-95 opacity-0 transition-all duration-200 overflow-hidden max-h-[92vh] flex flex-col">
        <!-- Header -->
        <div class="px-6 py-4 bg-slate-50/80 border-b border-slate-100 flex items-center justify-between shrink-0">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-blue-600 text-white flex items-center justify-center text-lg shadow-xs">
                    <ion-icon name="cube-outline"></ion-icon>
                </div>
                <div>
                    <h3 class="font-bold text-slate-800 text-sm">Tambah Paket Kegiatan</h3>
                    <p class="text-[11px] text-slate-400">Input rincian paket, nilai kontrak, DPP, setor awal, dan pasal</p>
                </div>
            </div>
            <button type="button" data-modal-close="modalTambahPaket" class="p-1.5 text-slate-400 hover:text-slate-700 rounded-lg transition-colors">
                <ion-icon name="close-outline" class="text-xl"></ion-icon>
            </button>
        </div>

        <!-- Body Form -->
        <form method="POST" action="<?= APP_URL ?>/paket/store" class="p-6 overflow-y-auto space-y-4 flex-1">
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

            <!-- Perusahaan & Group Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Perusahaan Aktif</label>
                    <div class="flex items-center gap-2 px-3 py-2 bg-slate-100/90 border border-slate-200 rounded-xl text-xs">
                        <ion-icon name="business" class="text-blue-600 text-sm shrink-0"></ion-icon>
                        <span class="font-bold text-slate-800 truncate"><?= htmlspecialchars($activeCompany['nama'] ?? 'Belum Ada') ?></span>
                        <span class="text-[10px] text-slate-400 ml-auto shrink-0 font-medium">Terkunci</span>
                    </div>
                    <input type="hidden" name="perusahaan_id" value="<?= $activeCompany['id'] ?? '' ?>">
                </div>

                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="block text-xs font-semibold text-slate-700">Group Paket (Opsional)</label>
                        <button type="button" onclick="openQuickAddGroup('tambah_group_id')" class="text-[11px] text-blue-600 hover:underline font-semibold flex items-center gap-0.5">
                            <ion-icon name="add-circle-outline"></ion-icon> Group Baru
                        </button>
                    </div>
                    <select name="group_id" id="tambah_group_id" 
                            class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-blue-500 text-xs focus:bg-white transition-all cursor-pointer">
                        <option value="">-- Tanpa Group (Paket Mandiri) --</option>
                        <?php foreach ($groups as $grp): ?>
                            <option value="<?= $grp['id'] ?>"><?= htmlspecialchars($grp['nama_group']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- Financials Grid: Pagu Awal & Pagu Nego -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
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
                        Pagu Nego <span class="text-[10px] font-normal text-emerald-600 font-semibold">(Acuan Akhir jika ada)</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-400 font-semibold text-xs">Rp</span>
                        <input type="text" name="pagu_nego" id="tambah_pagu_nego" oninput="formatRupiahInput(this)"
                               class="w-full pl-8 pr-2.5 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-emerald-500 text-xs font-mono font-medium focus:bg-white transition-all text-emerald-700"
                               placeholder="Opsional">
                    </div>
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
<div id="modalEditPaket" class="app-modal fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 transition-all duration-200 opacity-0 pointer-events-none hidden">
    <div class="modal-card bg-white rounded-2xl shadow-2xl border border-slate-100 max-w-2xl w-full transform scale-95 opacity-0 transition-all duration-200 overflow-hidden max-h-[92vh] flex flex-col">
        <!-- Header -->
        <div class="px-6 py-4 bg-slate-50/80 border-b border-slate-100 flex items-center justify-between shrink-0">
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
        <form id="formEditPaket" method="POST" action="" class="p-6 overflow-y-auto space-y-4 flex-1">
            <?= csrfField() ?>
            <input type="hidden" name="jumlah_pemodal" id="edit_jumlah_pemodal" value="0">

            <!-- Nama Paket -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Paket Kegiatan <span class="text-red-500">*</span></label>
                <input type="text" id="edit_nama_paket" name="nama_paket" required 
                       class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-blue-500 text-xs focus:bg-white transition-all">
            </div>

            <!-- Perusahaan & Group Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Perusahaan</label>
                    <div class="flex items-center gap-2 px-3 py-2 bg-slate-100/90 border border-slate-200 rounded-xl text-xs">
                        <ion-icon name="business" class="text-blue-600 text-sm shrink-0"></ion-icon>
                        <span id="edit_perusahaan_nama" class="font-bold text-slate-800 truncate">-</span>
                        <span class="text-[10px] text-slate-400 ml-auto shrink-0 font-medium">Terkunci</span>
                    </div>
                    <input type="hidden" name="perusahaan_id" id="edit_perusahaan_id" value="">
                </div>

                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="block text-xs font-semibold text-slate-700">Group Paket (Opsional)</label>
                        <button type="button" onclick="openQuickAddGroup('edit_group_id')" class="text-[11px] text-blue-600 hover:underline font-semibold flex items-center gap-0.5">
                            <ion-icon name="add-circle-outline"></ion-icon> Group Baru
                        </button>
                    </div>
                    <select name="group_id" id="edit_group_id" 
                            class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-blue-500 text-xs focus:bg-white transition-all cursor-pointer">
                        <option value="">-- Tanpa Group (Paket Mandiri) --</option>
                        <?php foreach ($groups as $grp): ?>
                            <option value="<?= $grp['id'] ?>"><?= htmlspecialchars($grp['nama_group']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- Penanggung Jawab & Progres Barang Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Penanggung Jawab (PJ)</label>
                    <input type="text" list="list_pj" name="penanggung_jawab" id="edit_penanggung_jawab"
                           class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-blue-500 text-xs focus:bg-white transition-all uppercase tracking-wide"
                           placeholder="Contoh: RAHMAN, RIFKI, DARMAN, dll.">
                    <span class="text-[10px] text-slate-400 mt-0.5 block">Ketik nama atau pilih dari saran</span>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Progres Barang</label>
                    <select name="progres_barang" id="edit_progres_barang" 
                            class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-blue-500 text-xs focus:bg-white transition-all cursor-pointer font-semibold">
                        <option value="Belum" class="text-rose-700 font-bold">BELUM</option>
                        <option value="Belanja" class="text-blue-700 font-bold">BELANJA</option>
                        <option value="Distribusi" class="text-emerald-700 font-bold">DISTRIBUSI</option>
                        <option value="Selesai" class="text-teal-700 font-bold">SELESAI</option>
                    </select>
                </div>
            </div>

            <!-- Financials Grid: Pagu Awal, Pagu Nego, Status -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
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
                        Pagu Nego <span class="text-[10px] font-normal text-emerald-600 font-semibold">(Acuan Akhir jika ada)</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-400 font-semibold text-xs">Rp</span>
                        <input type="text" name="pagu_nego" id="edit_pagu_nego" oninput="formatRupiahInput(this)"
                               class="w-full pl-8 pr-2.5 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-emerald-500 text-xs font-mono font-medium focus:bg-white transition-all text-emerald-700">
                    </div>
                </div>

                <!-- Status Pencairan -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Status Kas</label>
                    <select id="edit_status" name="status" class="w-full px-2.5 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-blue-500 text-xs focus:bg-white transition-all cursor-pointer font-bold">
                        <option value="Proses" class="text-rose-700 font-bold">PROSES</option>
                        <option value="Cair" class="text-emerald-700 font-bold">CAIR</option>
                        <option value="Belum" class="text-slate-700 font-bold">BELUM</option>
                    </select>
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
<div id="modalSkemaPotongan" class="app-modal fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 transition-all duration-200 opacity-0 pointer-events-none hidden">
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

            <!-- Dropdown Pilih Skema -->
            <div>
                <select name="skema_id" id="potongan_skema_id" onchange="onSkemaSelectChange('potongan', this)"
                        class="w-full px-3 py-2 bg-white border border-slate-200 rounded-xl focus:outline-none focus:border-amber-500 text-xs font-semibold text-slate-700 transition-all cursor-pointer">
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

            <!-- Checkbox & Input Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                <!-- Box 1: Potongan Pasal -->
                <div id="potongan_card_pasal" class="p-2.5 rounded-xl border border-amber-200 bg-white shadow-2xs transition-all">
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="flex items-center gap-2 cursor-pointer select-none">
                            <input type="checkbox" name="ada_pasal" id="potongan_ada_pasal" value="1" checked onchange="togglePasalCheck('potongan')" class="w-4 h-4 rounded text-amber-600 focus:ring-0 cursor-pointer">
                            <span class="text-xs font-bold text-slate-800">Potongan Pasal</span>
                        </label>
                        <span id="potongan_badge_pasal" class="text-[10px] text-emerald-600 font-semibold">Aktif</span>
                    </div>
                    <div class="relative" id="potongan_pasal_wrap">
                        <input type="number" step="0.1" min="0" max="100" name="persen_pasal" id="potongan_persen_pasal" value="13" oninput="updateIndexPotonganCalc()"
                               class="w-full px-2.5 py-1.5 bg-slate-50 border border-slate-200 rounded-lg text-xs font-bold text-slate-800 focus:outline-none focus:border-amber-500 font-mono transition-all">
                        <span class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs font-bold">%</span>
                    </div>
                </div>

                <!-- Box 2: Biaya Admin Fee -->
                <div id="potongan_card_admin" class="p-2.5 rounded-xl border border-amber-200 bg-white shadow-2xs transition-all">
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="flex items-center gap-2 cursor-pointer select-none">
                            <input type="checkbox" name="ada_admin" id="potongan_ada_admin" value="1" checked onchange="toggleAdminCheck('potongan')" class="w-4 h-4 rounded text-amber-600 focus:ring-0 cursor-pointer">
                            <span class="text-xs font-bold text-slate-800">Biaya Admin (Fee)</span>
                        </label>
                        <span id="potongan_badge_admin" class="text-[10px] text-emerald-600 font-semibold">Aktif</span>
                    </div>
                    <div class="relative" id="potongan_admin_wrap">
                        <input type="number" step="0.1" min="0" max="100" name="persen_admin" id="potongan_persen_admin" value="2" oninput="updateIndexPotonganCalc()"
                               class="w-full px-2.5 py-1.5 bg-slate-50 border border-slate-200 rounded-lg text-xs font-bold text-slate-800 focus:outline-none focus:border-amber-500 font-mono transition-all">
                        <span class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs font-bold">%</span>
                    </div>
                </div>
            </div>

            <!-- Pilihan Dasar Potongan: Kontrak vs DPP -->
            <div class="p-2.5 rounded-xl border border-slate-200 bg-white/70">
                <label class="flex items-start gap-2 cursor-pointer select-none">
                    <input type="checkbox" name="tipe_potongan" id="potongan_tipe_potongan" value="kontrak" onchange="updateIndexPotonganCalc()" class="w-4 h-4 rounded text-amber-600 focus:ring-0 mt-0.5 cursor-pointer">
                    <div>
                        <span class="text-xs font-bold text-slate-800 block">Potongan Diambil dari Nilai Kontrak</span>
                        <span class="text-[10px] text-slate-500 block leading-tight">Tidak dicentang: potongan dihitung dari DPP (setelah kena pajak). Centang ini jika potongan dihitung dari Nilai Kontrak (sebelum pajak).</span>
                    </div>
                </label>
            </div>

            <div class="flex items-center justify-between pt-1">
                <button type="button" onclick="promptSaveCurrentAsSkema('potongan')" class="text-[11px] text-amber-600 hover:text-amber-700 font-semibold flex items-center gap-1 hover:underline">
                    <ion-icon name="bookmark-outline" class="text-xs"></ion-icon>
                    <span>Simpan nilai ini sebagai skema baru</span>
                </button>
                <span class="text-[10px] text-slate-400">Total Potongan: <strong id="potongan_lbl_sum_persen" class="text-amber-700 font-mono">15%</strong></span>
            </div>

            <!-- LIVE CALCULATION BREAKDOWN BOX -->
            <div class="p-3 bg-slate-50 rounded-xl border border-slate-200/80 space-y-2">
                <div class="flex items-center justify-between pb-2 border-b border-slate-200/60 text-xs">
                    <span class="font-bold text-slate-700 flex items-center gap-1.5">
                        <ion-icon name="calculator-outline" class="text-amber-600 text-sm"></ion-icon>
                        <span>Rincian Perhitungan DPP & Potongan</span>
                    </span>
                    <span class="text-[11px] text-slate-500">
                        Acuan Kontrak: <strong id="potongan_preview_acuan" class="font-mono text-slate-800">Rp 0</strong>
                    </span>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 pt-1 text-xs">
                    <div class="p-2 bg-white rounded-lg border border-slate-100 shadow-2xs">
                        <span class="block text-[10px] font-bold text-slate-400 uppercase">DPP (-11% PPN)</span>
                        <span class="font-mono font-bold text-slate-800 text-xs" id="potongan_preview_dpp">Rp 0</span>
                    </div>

                    <div class="p-2 bg-white rounded-lg border border-slate-100 shadow-2xs">
                        <span class="block text-[10px] font-bold text-slate-400 uppercase">Pasal (<span id="potongan_lbl_pasal">13</span>%)</span>
                        <span class="font-mono font-bold text-slate-800 text-xs" id="potongan_preview_pasal13">Rp 0</span>
                    </div>

                    <div class="p-2 bg-white rounded-lg border border-slate-100 shadow-2xs">
                        <span class="block text-[10px] font-bold text-slate-400 uppercase">Admin (<span id="potongan_lbl_admin">2</span>%)</span>
                        <span class="font-mono font-bold text-slate-800 text-xs" id="potongan_preview_admin2">Rp 0</span>
                    </div>

                    <div class="p-2 bg-amber-50/80 rounded-lg border border-amber-200/60 shadow-2xs">
                        <span class="block text-[10px] font-bold text-amber-800 uppercase">Total Pasal (<span id="potongan_lbl_total_pasal">15</span>%)</span>
                        <span class="font-mono font-bold text-amber-700 text-xs" id="potongan_preview_total_pasal">Rp 0</span>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2 shrink-0">
                <button type="button" data-modal-close="modalSkemaPotongan" 
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
<div id="modalQuickTambahSkema" class="app-modal fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 transition-all duration-200 opacity-0 pointer-events-none hidden">
    <div class="modal-card bg-white rounded-2xl shadow-2xl border border-slate-100 max-w-sm w-full transform scale-95 opacity-0 transition-all duration-200 overflow-hidden">
        <div class="px-5 py-3.5 bg-slate-50 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-bold text-slate-800 text-xs flex items-center gap-2">
                <ion-icon name="options-outline" class="text-blue-600 text-base"></ion-icon>
                <span>Buat Skema Potongan Baru</span>
            </h3>
            <button type="button" data-modal-close="modalQuickTambahSkema" class="p-1 text-slate-400 hover:text-slate-700 rounded-lg">
                <ion-icon name="close-outline" class="text-lg"></ion-icon>
            </button>
        </div>
        <form id="formQuickSkema" onsubmit="handleQuickTambahSkema(event)" class="p-5 space-y-3.5">
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Skema <span class="text-red-500">*</span></label>
                <input type="text" id="quick_nama_skema" required 
                       class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-blue-500 text-xs focus:bg-white"
                       placeholder="Misal: Skema 1, atau 20% Non-Admin">
            </div>

            <!-- Toggle Pasal -->
            <div class="p-2.5 rounded-xl border border-slate-200 bg-slate-50/50 space-y-2">
                <label class="flex items-center gap-2 cursor-pointer select-none">
                    <input type="checkbox" id="quick_ada_pasal" value="1" checked onchange="toggleQuickModalFields()" class="w-4 h-4 rounded text-blue-600 focus:ring-0 cursor-pointer">
                    <span class="text-xs font-bold text-slate-800">Aktifkan Potongan Pasal</span>
                </label>
                <div class="relative" id="quick_pasal_wrap">
                    <input type="number" step="0.1" min="0" max="100" id="quick_persen_pasal" value="13" oninput="updateQuickModalTotal()"
                           class="w-full px-2.5 py-1.5 bg-white border border-slate-200 rounded-lg text-xs font-bold text-slate-800 focus:outline-none focus:border-blue-500 font-mono transition-all">
                    <span class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs font-bold">%</span>
                </div>
            </div>

            <!-- Toggle Admin -->
            <div class="p-2.5 rounded-xl border border-slate-200 bg-slate-50/50 space-y-2">
                <label class="flex items-center gap-2 cursor-pointer select-none">
                    <input type="checkbox" id="quick_ada_admin" value="1" checked onchange="toggleQuickModalFields()" class="w-4 h-4 rounded text-blue-600 focus:ring-0 cursor-pointer">
                    <span class="text-xs font-bold text-slate-800">Aktifkan Biaya Admin (Fee)</span>
                </label>
                <div class="relative" id="quick_admin_wrap">
                    <input type="number" step="0.1" min="0" max="100" id="quick_persen_admin" value="2" oninput="updateQuickModalTotal()"
                           class="w-full px-2.5 py-1.5 bg-white border border-slate-200 rounded-lg text-xs font-bold text-slate-800 focus:outline-none focus:border-blue-500 font-mono transition-all">
                    <span class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs font-bold">%</span>
                </div>
            </div>

            <!-- Pilihan Dasar Potongan: Kontrak vs DPP -->
            <div class="p-2.5 rounded-xl border border-slate-200 bg-slate-50/50">
                <label class="flex items-start gap-2 cursor-pointer select-none">
                    <input type="checkbox" id="quick_tipe_potongan" value="kontrak" onchange="updateQuickModalTotal()" class="w-4 h-4 rounded text-blue-600 focus:ring-0 mt-0.5 cursor-pointer">
                    <div>
                        <span class="text-xs font-bold text-slate-800 block">Potongan Diambil dari Nilai Kontrak</span>
                        <span class="text-[11px] text-slate-500 block leading-tight">Tidak dicentang: potongan dihitung dari DPP (setelah kena pajak). Centang ini jika potongan dihitung dari Nilai Kontrak (sebelum pajak).</span>
                    </div>
                </label>
            </div>

            <div class="p-2 rounded-lg bg-blue-50 border border-blue-100 flex items-center justify-between text-xs">
                <span class="text-slate-600">Total Potongan Skema:</span>
                <strong id="quick_lbl_total_persen" class="font-mono text-blue-700 font-bold">15%</strong>
            </div>

            <div class="pt-2 border-t border-slate-100 flex justify-end gap-2">
                <button type="button" data-modal-close="modalQuickTambahSkema" class="px-3 py-1.5 rounded-lg border border-slate-200 text-slate-600 font-semibold hover:bg-slate-50 text-xs">
                    Batal
                </button>
                <button type="submit" id="btnSubmitQuickSkema" class="px-4 py-1.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg text-xs shadow-xs flex items-center gap-1.5">
                    <ion-icon name="checkmark-outline" class="text-sm"></ion-icon>
                    <span>Simpan & Gunakan</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================== -->
<!-- 4. JAVASCRIPT LOGIC                        -->
<!-- ========================================== -->
<script>
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
 * Buka Modal Edit Paket & Isi Otomatis
 */
function openEditPaketModal(p) {
    if (!p) return;

    const form = document.getElementById('formEditPaket');
    form.action = '<?= APP_URL ?>/paket/update/' + (p.kode_paket || p.id);

    document.getElementById('edit_nama_paket').value = p.nama_paket || '';
    document.getElementById('edit_perusahaan_id').value = p.perusahaan_id || '';
    const namaPerusahaan = p.perusahaan_nama || p.perusahaan || '-';
    const spanNama = document.getElementById('edit_perusahaan_nama');
    if (spanNama) spanNama.textContent = namaPerusahaan;
    document.getElementById('edit_group_id').value = p.group_id || '';
    document.getElementById('edit_penanggung_jawab').value = p.penanggung_jawab || '';
    document.getElementById('edit_progres_barang').value = p.progres_barang || 'Belum';
    document.getElementById('edit_status').value = p.status || 'Proses';
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
    const progresVal = document.getElementById('filterProgresSelect').value.toLowerCase();
    const statusVal = document.getElementById('filterStatusSelect').value.toLowerCase();
    
    const table = document.getElementById('paketTable');
    if (!table) return;
    const rows = table.querySelectorAll('tbody tr:not(#emptyTableRow)');

    let visibleCount = 0;
    rows.forEach(row => {
        const text = row.innerText.toLowerCase();
        const rowGroup = row.getAttribute('data-group-name') || '';
        const rowProgres = row.getAttribute('data-progres') || '';
        const rowStatus = row.getAttribute('data-status') || '';

        const matchSearch = text.includes(searchVal);
        
        let matchGroup = true;
        if (groupVal === '__none__') {
            matchGroup = (rowGroup === '');
        } else if (groupVal !== '') {
            matchGroup = rowGroup.includes(groupVal);
        }

        let matchProgres = true;
        if (progresVal !== '') {
            matchProgres = (rowProgres === progresVal);
        }

        let matchStatus = true;
        if (statusVal !== '') {
            matchStatus = (rowStatus === statusVal);
        }

        if (matchSearch && matchGroup && matchProgres && matchStatus) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    });
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
    document.getElementById('filterProgresSelect').value = '';
    document.getElementById('filterStatusSelect').value = '';
    
    document.querySelectorAll('.pill-status-btn').forEach(btn => {
        btn.className = 'pill-status-btn px-3 py-1.5 rounded-xl font-semibold transition-all bg-slate-100 hover:bg-slate-200 text-slate-700 flex items-center gap-1.5 shrink-0';
    });
    const allBtn = document.getElementById('pill_status_all');
    if (allBtn) allBtn.className = 'pill-status-btn px-3 py-1.5 rounded-xl font-bold transition-all bg-slate-900 text-white shadow-2xs flex items-center gap-1.5 shrink-0';

    filterPaketTable();
}
</script>
