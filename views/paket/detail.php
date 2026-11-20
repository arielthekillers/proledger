<?php
$role = $_SESSION['admin_role'] ?? 'user';
$hasNego = (!empty($paket['pagu_nego']) && (float)$paket['pagu_nego'] > 0);
$acuan = $hasNego ? (float)$paket['pagu_nego'] : (float)$paket['pagu'];
$dpp = !empty($paket['dpp']) ? (float)$paket['dpp'] : calculateDPP($acuan);

$persenPasal = isset($paket['persen_pasal']) && $paket['persen_pasal'] !== '' ? (float)$paket['persen_pasal'] : 13.0;
$persenAdmin = isset($paket['persen_admin']) && $paket['persen_admin'] !== '' ? (float)$paket['persen_admin'] : 2.0;
$totalPersen = $persenPasal + $persenAdmin;

$tipePotongan = $paket['tipe_potongan'] ?? 'dpp';
$basisPotongan = ($tipePotongan === 'kontrak') ? $acuan : $dpp;
$basisLabel = ($tipePotongan === 'kontrak') ? 'Nilai Kontrak' : 'DPP';

$pasalVal = !empty($paket['pasal_13']) ? (float)$paket['pasal_13'] : calculatePasal($basisPotongan, $persenPasal);
$adminVal = isset($paket['admin_2']) ? (float)$paket['admin_2'] : calculateAdmin($basisPotongan, $persenAdmin);
$totalPasal = !empty($paket['total_pasal']) ? (float)$paket['total_pasal'] : ($pasalVal + $adminVal);

$items = $items ?? [];
$itemSummary = $itemSummary ?? ['total_item' => 0, 'total_qty' => 0, 'grand_total' => 0];
$distribusiList = $distribusiList ?? [];
$distribusiSummary = $distribusiSummary ?? ['total_baris' => 0, 'total_tujuan' => 0, 'total_qty_dialokasikan' => 0, 'total_diterima' => 0, 'total_dikirim' => 0, 'total_belum_kirim' => 0];
$groupedDistribusi = $groupedDistribusi ?? [];
$dokumenList = $dokumenList ?? [];
$lokasiList = $lokasiList ?? [];

$activeTab = $activeTab ?? ($_GET['tab'] ?? 'info');
$validTabs = ['info', 'items', 'distribusi', 'dokumen'];
if (!in_array($activeTab, $validTabs)) {
    $activeTab = 'info';
}
$groups = $groups ?? [];
$skemas = $skemas ?? [];

$backUrl = APP_URL . '/paket';
$backLabel = 'Kembali ke Paket';
if (isset($_GET['from']) && $_GET['from'] === 'perusahaan' && isset($_GET['source_id'])) {
    $backUrl = APP_URL . '/perusahaan/detail/' . $_GET['source_id'];
    $backLabel = 'Ke Perusahaan';
} elseif (isset($_GET['from']) && $_GET['from'] === 'group' && !empty($paket['group_id'])) {
    $backUrl = APP_URL . '/group/detail/' . $paket['group_id'];
    $backLabel = 'Ke Group';
}
?>

<!-- Master Lokasi Datalist untuk Autocomplete Cepat -->
<datalist id="list_master_lokasi">
    <?php foreach ($lokasiList as $lok): ?>
        <option value="<?= htmlspecialchars($lok['nama_lokasi']) ?>"
                data-id="<?= $lok['id'] ?>"
                data-kategori="<?= htmlspecialchars($lok['kategori']) ?>"
                data-pic="<?= htmlspecialchars($lok['nama_pic'] ?? '') ?>"
                data-kontak="<?= htmlspecialchars($lok['no_kontak'] ?? '') ?>"
                data-alamat="<?= htmlspecialchars($lok['alamat_lengkap'] ?? '') ?>"
                data-kabkota="<?= htmlspecialchars($lok['kab_kota'] ?? '') ?>">
            <?= htmlspecialchars($lok['nama_lokasi']) ?> (<?= htmlspecialchars($lok['kab_kota'] ?: $lok['kategori']) ?>)
        </option>
    <?php endforeach; ?>
</datalist>

<!-- ========================================== -->
<!-- 1. PAGE HEADER                             -->
<!-- ========================================== -->
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
    <div class="min-w-0">
        <div class="flex items-center gap-2 flex-wrap">
            <h1 class="text-lg md:text-xl font-bold text-slate-800 leading-snug">
                <?= htmlspecialchars($paket['nama_paket']) ?>
            </h1>
            <span class="px-2 py-0.5 rounded-md bg-blue-50 text-blue-700 border border-blue-200/80 font-mono font-bold text-[11px] tracking-wide shrink-0">
                #<?= htmlspecialchars($paket['kode_paket'] ?: $paket['id']) ?>
            </span>
        </div>
        <div class="flex items-center gap-2 mt-1 text-xs text-slate-500 flex-wrap">
            <?php if (!empty($paket['perusahaan'])): ?>
                <a href="<?= APP_URL ?>/perusahaan/detail/<?= $paket['perusahaan_id'] ?>" class="text-slate-600 font-medium inline-flex items-center gap-1 hover:text-blue-600 transition-colors">
                    <ion-icon name="business-outline" class="text-slate-400 text-xs"></ion-icon>
                    <span><?= htmlspecialchars($paket['perusahaan']) ?></span>
                </a>
            <?php endif; ?>

            <?php if (!empty($paket['nama_group'])): ?>
                <span class="text-slate-300">&bull;</span>
                <a href="<?= APP_URL ?>/group/detail/<?= $paket['group_id'] ?>" class="text-slate-600 hover:text-indigo-600 inline-flex items-center gap-1 transition-colors">
                    <ion-icon name="folder-outline" class="text-slate-400 text-xs"></ion-icon>
                    <span>Group: <?= htmlspecialchars($paket['nama_group']) ?></span>
                </a>
            <?php endif; ?>

            <?php if (!empty($paket['nomor_sp'])): ?>
                <span class="text-slate-300">&bull;</span>
                <span class="text-slate-500 font-mono text-[11px] inline-flex items-center gap-1" title="Nomor Surat Pesanan / SPK">
                    <span>No. SP: <?= htmlspecialchars($paket['nomor_sp']) ?></span>
                </span>
            <?php endif; ?>

            <?php if (!empty($paket['kab_kota'])): ?>
                <span class="text-slate-300">&bull;</span>
                <span class="text-slate-500 inline-flex items-center gap-1">
                    <ion-icon name="location-outline" class="text-slate-400 text-xs"></ion-icon>
                    <span><?= htmlspecialchars($paket['kab_kota']) ?></span>
                </span>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="flex items-center gap-2 shrink-0">
        <a href="<?= $backUrl ?>" class="px-3 py-1.5 bg-white border border-slate-200 text-slate-600 font-semibold rounded-xl hover:bg-slate-50 transition-colors shadow-2xs flex items-center gap-1.5 text-xs">
            <ion-icon name="arrow-back-outline"></ion-icon>
            <span><?= $backLabel ?></span>
        </a>

        <?php if ($role === 'admin' || $role === 'user'): ?>
            <button type="button" onclick="openDetailEditPaketModal()" 
                    class="px-3.5 py-1.5 bg-slate-800 hover:bg-slate-900 text-white font-semibold rounded-xl transition-all shadow-xs flex items-center gap-1.5 text-xs">
                <ion-icon name="create-outline" class="text-sm"></ion-icon>
                <span>Edit Paket</span>
            </button>
        <?php endif; ?>
    </div>
</div>

<!-- ========================================== -->
<!-- 2. TAB NAVIGATION (4 TABS)                 -->
<!-- ========================================== -->
<script>
(function() {
    var validTabs = ['info', 'items', 'distribusi', 'dokumen'];
    var hash = (window.location.hash || '').replace('#', '');
    var urlParams = new URLSearchParams(window.location.search);
    var tabParam = urlParams.get('tab');
    var active = (validTabs.indexOf(tabParam) !== -1) ? tabParam : ((validTabs.indexOf(hash) !== -1) ? hash : '<?= $activeTab ?>');
    if (active && validTabs.indexOf(active) !== -1) {
        var css = '';
        validTabs.forEach(function(t) {
            css += '#tabContent_' + t + ' { display: ' + (t === active ? 'block' : 'none') + ' !important; } ';
        });
        document.write('<style id="tab-preload-style">' + css + '</style>');
    }
})();
</script>

<div class="mb-5 border-b border-slate-200/90 bg-white px-2 rounded-t-xl">
    <nav class="flex space-x-2" aria-label="Tabs">
        <!-- Tab 1: Informasi Paket -->
        <button type="button" onclick="switchDetailTab('info')" id="tabBtn_info"
                class="tab-btn py-3 px-4 text-xs <?= ($activeTab === 'info') ? 'font-bold border-b-2 border-blue-600 text-blue-600' : 'font-semibold border-b-2 border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300' ?> flex items-center gap-2 transition-all">
            <ion-icon name="information-circle-outline" class="text-base"></ion-icon>
            <span>Informasi Paket</span>
        </button>

        <!-- Tab 2: Item / Spesifikasi -->
        <?php $itP = $tabProgress['items'] ?? ['done' => 0, 'total' => count($items)]; ?>
        <?php $itDone = ($itP['total'] > 0 && $itP['done'] >= $itP['total']); ?>
        <button type="button" onclick="switchDetailTab('items')" id="tabBtn_items"
                class="tab-btn py-3 px-4 text-xs <?= ($activeTab === 'items') ? 'font-bold border-b-2 border-blue-600 text-blue-600' : 'font-semibold border-b-2 border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300' ?> flex items-center gap-2 transition-all">
            <ion-icon name="list-outline" class="text-base"></ion-icon>
            <span>Item / Spesifikasi</span>
            <span id="tabBadge_itemsCount" class="px-2 py-0.5 rounded-full text-[10px] font-bold <?= $itDone ? 'bg-emerald-100 text-emerald-700' : 'bg-blue-100 text-blue-700' ?>" title="<?= $itP['done'] ?> dari <?= $itP['total'] ?> item telah lengkap dialokasikan">
                <?= $itP['done'] ?>/<?= $itP['total'] ?>
            </span>
        </button>

        <!-- Tab 3: Distribusi -->
        <?php $distP = $tabProgress['distribusi'] ?? ['done' => 0, 'total' => count($distribusiList)]; ?>
        <?php $distDone = ($distP['total'] > 0 && $distP['done'] >= $distP['total']); ?>
        <button type="button" onclick="switchDetailTab('distribusi')" id="tabBtn_distribusi"
                class="tab-btn py-3 px-4 text-xs <?= ($activeTab === 'distribusi') ? 'font-bold border-b-2 border-blue-600 text-blue-600' : 'font-semibold border-b-2 border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300' ?> flex items-center gap-2 transition-all">
            <ion-icon name="navigate-outline" class="text-base"></ion-icon>
            <span>Distribusi</span>
            <span id="tabBadge_distribusiCount" class="px-2 py-0.5 rounded-full text-[10px] font-bold <?= $distDone ? 'bg-emerald-100 text-emerald-700' : 'bg-indigo-100 text-indigo-700' ?>" title="<?= $distP['done'] ?> dari <?= $distP['total'] ?> alokasi telah tiba di tujuan">
                <?= $distP['done'] ?>/<?= $distP['total'] ?>
            </span>
        </button>

        <!-- Tab 4: Dokumentasi -->
        <?php $dokP = $tabProgress['dokumentasi'] ?? ['done' => 0, 'total' => count($distribusiList)]; ?>
        <?php $dokDone = ($dokP['total'] > 0 && $dokP['done'] >= $dokP['total']); ?>
        <button type="button" onclick="switchDetailTab('dokumentasi')" id="tabBtn_dokumentasi"
                class="tab-btn py-3 px-4 text-xs <?= ($activeTab === 'dokumentasi') ? 'font-bold border-b-2 border-blue-600 text-blue-600' : 'font-semibold border-b-2 border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300' ?> flex items-center gap-2 transition-all">
            <ion-icon name="camera-outline" class="text-base"></ion-icon>
            <span>Dokumentasi</span>
            <span id="tabBadge_dokumentasiCount" class="px-2 py-0.5 rounded-full text-[10px] font-bold <?= $dokDone ? 'bg-emerald-100 text-emerald-700' : 'bg-teal-100 text-teal-800' ?>" title="<?= $dokP['done'] ?> dari <?= $dokP['total'] ?> alokasi telah memiliki foto dokumentasi">
                <?= $dokP['done'] ?>/<?= $dokP['total'] ?>
            </span>
        </button>

        <!-- Tab 5: Dokumen BAST -->
        <?php $bastP = $tabProgress['dokumen'] ?? ['done' => 0, 'total' => count($groupedDistribusi ?? [])]; ?>
        <?php $bastDone = ($bastP['total'] > 0 && $bastP['done'] >= $bastP['total']); ?>
        <button type="button" onclick="switchDetailTab('dokumen')" id="tabBtn_dokumen"
                class="tab-btn py-3 px-4 text-xs <?= ($activeTab === 'dokumen') ? 'font-bold border-b-2 border-blue-600 text-blue-600' : 'font-semibold border-b-2 border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300' ?> flex items-center gap-2 transition-all">
            <ion-icon name="document-text-outline" class="text-base"></ion-icon>
            <span>Dokumen BAST</span>
            <span id="tabBadge_dokumenCount" class="px-2 py-0.5 rounded-full text-[10px] font-bold <?= $bastDone ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-800' ?>" title="<?= $bastP['done'] ?> dari <?= $bastP['total'] ?> berkas BAST telah diupload">
                <?= $bastP['done'] ?>/<?= $bastP['total'] ?>
            </span>
        </button>
    </nav>
</div>
<script>
(function() {
    var validTabs = ['info', 'items', 'distribusi', 'dokumentasi', 'dokumen'];
    var hash = (window.location.hash || '').replace('#', '');
    var urlParams = new URLSearchParams(window.location.search);
    var tabParam = urlParams.get('tab');
    var active = (validTabs.indexOf(tabParam) !== -1) ? tabParam : ((validTabs.indexOf(hash) !== -1) ? hash : '<?= $activeTab ?>');
    if (active && validTabs.indexOf(active) !== -1) {
        validTabs.forEach(function(t) {
            var btn = document.getElementById('tabBtn_' + t);
            if (btn) {
                if (t === active) {
                    btn.className = 'tab-btn py-3 px-4 text-xs font-bold border-b-2 border-blue-600 text-blue-600 flex items-center gap-2 transition-all';
                } else {
                    btn.className = 'tab-btn py-3 px-4 text-xs font-semibold border-b-2 border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300 flex items-center gap-2 transition-all';
                }
            }
        });
    }
})();
</script>

<!-- ========================================== -->
<!-- 3. TAB CONTENT PARTIALS                     -->
<!-- ========================================== -->
<?php require __DIR__ . '/partials/tab_info.php'; ?>
<?php require __DIR__ . '/partials/tab_items.php'; ?>
<?php require __DIR__ . '/partials/tab_distribusi.php'; ?>
<?php require __DIR__ . '/partials/tab_dokumentasi.php'; ?>
<?php require __DIR__ . '/partials/tab_dokumen.php'; ?>

<!-- ========================================== -->
<!-- 4. MODALS & POPUPS                         -->
<!-- ========================================== -->
<?php require __DIR__ . '/partials/modals.php'; ?>

<!-- ========================================== -->
<!-- 5. CLIENT-SIDE SCRIPTS                     -->
<!-- ========================================== -->
<?php require __DIR__ . '/partials/scripts.php'; ?>
