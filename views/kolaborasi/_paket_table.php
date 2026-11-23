<?php
/**
 * Shared Paket Table & Read-Only Modal for Kolaborasi & Public Monitoring
 * Tampilan identik dengan Daftar Paket (tanpa HPP, Profit, dan tombol input/edit/hapus).
 * Mengharapkan variabel: $pakets, $companyStats, $summary, $modalBaseUrl
 */
?>
<!-- ========================================== -->
<!-- TABEL MONITORING PAKET (IDENTIK PAKET)     -->
<!-- ========================================== -->
<div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 overflow-hidden">
    <div class="p-3.5 px-5 border-b border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-3">
        <div class="flex items-center gap-2">
            <ion-icon name="analytics-outline" class="text-blue-600 text-base"></ion-icon>
            <h3 class="font-bold text-slate-800 text-xs uppercase tracking-wider">
                Daftar Paket Kegiatan (<span id="kolabVisibleCount"><?= count($pakets) ?></span>)
            </h3>
            <span class="text-[10px] px-2 py-0.5 rounded-full bg-slate-100 text-slate-500 font-semibold">
                HPP &amp; Profit Privat
            </span>
        </div>

        <div class="flex items-center gap-2 flex-wrap">
            <div class="relative w-full sm:w-52">
                <ion-icon name="search-outline" class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></ion-icon>
                <input type="text" id="searchKolabPaket" onkeyup="filterKolabTable()"
                       placeholder="Cari paket, kode, tag..."
                       class="w-full pl-8 pr-3 py-1.5 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-blue-500 text-xs focus:bg-white">
            </div>

            <select id="filterKolabCompany" onchange="filterKolabTable()"
                    class="px-2.5 py-1.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700 focus:outline-none focus:border-blue-500 cursor-pointer">
                <option value="">Semua Perusahaan</option>
                <?php foreach ($companyStats as $cs): ?>
                    <option value="<?= htmlspecialchars(strtolower($cs['nama'])) ?>"><?= htmlspecialchars($cs['nama']) ?></option>
                <?php endforeach; ?>
            </select>

            <select id="filterKolabStatus" onchange="filterKolabTable()"
                    class="px-2.5 py-1.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700 focus:outline-none focus:border-blue-500 cursor-pointer">
                <option value="">Semua Pencairan</option>
                <option value="belum">Belum</option>
                <option value="proses">Proses</option>
                <option value="selesai">Cair / Selesai</option>
            </select>

            <button type="button" onclick="resetKolabFilter()"
                    class="p-1.5 text-slate-400 hover:text-slate-700 hover:bg-slate-100 rounded-xl border border-slate-200 cursor-pointer"
                    title="Reset Filter">
                <ion-icon name="refresh-outline" class="text-sm"></ion-icon>
            </button>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left text-xs" id="kolabPaketTable">
            <thead class="bg-slate-50/80 text-[10px] uppercase tracking-wider text-slate-500 font-bold border-b border-slate-200/80">
                <tr>
                    <th class="py-3 px-3 text-center">Kode</th>
                    <th class="py-3 px-3">Nama Paket</th>
                    <th class="py-3 px-3 text-right">Nilai Kontrak</th>
                    <th class="py-3 px-3 text-right">DPP</th>
                    <th class="py-3 px-3 text-right">Total Pasal</th>
                    <th class="py-3 px-3 text-right">Nilai Bersih</th>
                    <th class="py-3 px-2 text-center">Komponen</th>
                    <th class="py-3 px-2 text-center">Order</th>
                    <th class="py-3 px-2 text-center">Dist</th>
                    <th class="py-3 px-2 text-center">Dok</th>
                    <th class="py-3 px-2 text-center">BAST</th>
                    <th class="py-3 px-3 text-center">Pencairan</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php if (empty($pakets)): ?>
                    <tr>
                        <td colspan="12" class="py-12 text-center text-slate-400">
                            <p class="text-xs font-semibold text-slate-600 mb-1">Belum ada paket yang terhubung</p>
                            <p class="text-[11px] text-slate-400">Hubungkan Group Paket perusahaan ke ruang kolaborasi ini untuk mulai memantau.</p>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($pakets as $p):
                        $kodeRef = $p['kode_paket'] ?: $p['id'];
                        $namaJs = htmlspecialchars(addslashes($p['nama_paket']));
                        $tagsArr = !empty($p['tags']) ? array_filter(array_map('trim', explode(',', $p['tags']))) : [];
                    ?>
                        <tr class="hover:bg-slate-50/70 transition-colors kolab-paket-row"
                            data-search="<?= htmlspecialchars(strtolower(($p['nama_paket'] ?? '') . ' ' . ($p['kode_paket'] ?? '') . ' ' . ($p['tags'] ?? '') . ' ' . ($p['nama_group'] ?? '') . ' ' . ($p['perusahaan_nama'] ?? ''))) ?>"
                            data-company="<?= htmlspecialchars(strtolower($p['perusahaan_nama'])) ?>"
                            data-status="<?= htmlspecialchars($p['norm_status']) ?>"
                            data-kontrak="<?= $p['acuan'] ?>"
                            data-dpp="<?= $p['dpp'] ?>"
                            data-pasal="<?= $p['total_pasal'] ?>"
                            data-bersih="<?= $p['nilai_bersih'] ?>"
                            data-items="<?= $p['item_count'] ?>"
                            data-units="<?= $p['item_qty'] ?>">

                            <!-- KODE -->
                            <td class="py-2.5 px-3 text-center">
                                <span class="px-2 py-0.5 font-mono font-bold text-[11px] bg-slate-100 text-slate-700 rounded-md tracking-wider">
                                    <?= htmlspecialchars($p['kode_paket'] ?: '#' . $p['id']) ?>
                                </span>
                            </td>

                            <!-- NAMA PAKET + PERUSAHAAN + GROUP + TAGS -->
                            <td class="py-2.5 px-3 min-w-[230px]">
                                <button type="button"
                                        onclick="openKolabReadonlyModal('<?= $kodeRef ?>', 'info', '<?= $namaJs ?>')"
                                        class="text-left font-bold text-slate-800 hover:text-blue-600 text-xs transition-colors cursor-pointer block">
                                    <?= htmlspecialchars($p['nama_paket']) ?>
                                </button>
                                <div class="flex items-center gap-1 flex-wrap mt-1">
                                    <span class="px-1.5 py-0.2 rounded bg-amber-50 text-amber-700 border border-amber-200/80 text-[10px]">
                                        TA <?= (int)($p['tahun_anggaran'] ?? date('Y')) ?>
                                    </span>
                                    <span class="px-1.5 py-0.2 rounded bg-blue-50 text-blue-700 border border-blue-200/80 text-[10px] font-semibold">
                                        <?= htmlspecialchars($p['perusahaan_nama']) ?>
                                    </span>
                                    <span class="px-1.5 py-0.2 rounded bg-indigo-50 text-indigo-700 border border-indigo-200/80 text-[10px]">
                                        <?= htmlspecialchars($p['nama_group']) ?>
                                    </span>
                                    <?php foreach ($tagsArr as $tg): ?>
                                        <span class="px-1.5 py-0.2 rounded bg-slate-100 text-slate-600 text-[10px]">#<?= htmlspecialchars($tg) ?></span>
                                    <?php endforeach; ?>
                                </div>
                            </td>

                            <!-- NILAI KONTRAK -->
                            <td class="py-2.5 px-3 text-right whitespace-nowrap">
                                <div class="font-mono font-bold text-slate-900 text-xs leading-5">
                                    <?= formatRupiah($p['acuan']) ?>
                                </div>
                                <div class="mt-0.5 min-h-[16px] flex items-center justify-end">
                                    <?php if (abs($p['effective_pagu'] - $p['acuan']) > 0.5): ?>
                                        <span class="font-mono text-slate-400 line-through text-[10px]"><?= formatRupiah($p['effective_pagu']) ?></span>
                                    <?php else: ?>
                                        <span class="text-[10px] text-slate-400">Nilai Acuan</span>
                                    <?php endif; ?>
                                </div>
                            </td>

                            <!-- DPP -->
                            <td class="py-2.5 px-3 text-right whitespace-nowrap">
                                <div class="font-mono font-semibold text-slate-800 text-xs leading-5">
                                    <?= formatRupiah($p['dpp']) ?>
                                </div>
                                <div class="mt-0.5 min-h-[16px] flex items-center justify-end">
                                    <span class="inline-flex items-center px-1.5 py-0.2 rounded text-[9px] font-semibold bg-slate-100 text-slate-500 border border-slate-200/80">
                                        DPP -11%
                                    </span>
                                </div>
                            </td>

                            <!-- TOTAL PASAL -->
                            <td class="py-2.5 px-3 text-right whitespace-nowrap">
                                <div class="font-mono font-bold text-amber-700 text-xs leading-5">
                                    <?= formatRupiah($p['total_pasal']) ?>
                                </div>
                                <div class="mt-0.5 min-h-[16px] flex items-center justify-end">
                                    <span class="inline-flex items-center px-1.5 py-0.2 rounded text-[9px] font-bold bg-amber-50 text-amber-700 border border-amber-200/80">
                                        <?= $p['total_persen'] ?>%
                                    </span>
                                </div>
                            </td>

                            <!-- NILAI BERSIH (KLIK -> POPUP INFO & FINANSIAL READ-ONLY) -->
                            <td class="py-2.5 px-3 text-right whitespace-nowrap">
                                <button type="button"
                                        onclick="openKolabReadonlyModal('<?= $kodeRef ?>', 'info', '<?= $namaJs ?>')"
                                        class="text-right group/bersih hover:bg-emerald-50/80 px-2 py-1 -my-1 -mx-2 rounded-lg transition-all cursor-pointer inline-block"
                                        title="Klik untuk lihat Rincian Info & Finansial">
                                    <div class="font-mono font-bold text-emerald-700 text-xs leading-5">
                                        <?= formatRupiah($p['nilai_bersih']) ?>
                                    </div>
                                    <div class="mt-0.5 min-h-[16px] flex items-center justify-end">
                                        <span class="inline-flex items-center gap-0.5 px-1.5 py-0.2 rounded text-[9px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200/80">
                                            <ion-icon name="information-circle-outline" class="text-[10px]"></ion-icon> Info &amp; Finansial
                                        </span>
                                    </div>
                                </button>
                            </td>

                            <!-- KOMPONEN (KLIK -> POPUP ITEM READ-ONLY) -->
                            <td class="py-2.5 px-2 text-center whitespace-nowrap">
                                <button type="button"
                                        onclick="openKolabReadonlyModal('<?= $kodeRef ?>', 'items', '<?= $namaJs ?>')"
                                        class="group/item hover:bg-blue-50/80 px-2 py-1 -my-1 rounded-lg transition-all cursor-pointer inline-flex flex-col items-center"
                                        title="Klik untuk lihat Komponen Item Barang">
                                    <span class="font-normal text-slate-700 group-hover/item:text-blue-700 text-xs leading-5">
                                        <?= $p['item_count'] ?> Item
                                    </span>
                                    <span class="inline-flex items-center px-1.5 py-0.2 rounded text-[9px] font-semibold bg-slate-100 text-slate-600 border border-slate-200/80 mt-0.5">
                                        <?= number_format($p['item_qty'], 0, ',', '.') ?> Unit
                                    </span>
                                </button>
                            </td>

                            <!-- PROGRES CIRCLES (TOOLTIP + KLIK POPUP READ-ONLY) -->
                            <td class="py-2 px-2 text-center whitespace-nowrap">
                                <button type="button" onclick="openKolabReadonlyModal('<?= $kodeRef ?>', 'pemesanan', '<?= $namaJs ?>')" class="hover:scale-105 transition-transform inline-block cursor-pointer">
                                    <?= renderCircularProgress($p['pesan_done'] ?? 0, $p['total_dist'] ?? 0, 'pemesanan') ?>
                                </button>
                            </td>
                            <td class="py-2 px-2 text-center whitespace-nowrap">
                                <button type="button" onclick="openKolabReadonlyModal('<?= $kodeRef ?>', 'distribusi', '<?= $namaJs ?>')" class="hover:scale-105 transition-transform inline-block cursor-pointer">
                                    <?= renderCircularProgress($p['dist_done'] ?? 0, $p['total_dist'] ?? 0, 'distribusi') ?>
                                </button>
                            </td>
                            <td class="py-2 px-2 text-center whitespace-nowrap">
                                <button type="button" onclick="openKolabReadonlyModal('<?= $kodeRef ?>', 'dokumentasi', '<?= $namaJs ?>')" class="hover:scale-105 transition-transform inline-block cursor-pointer">
                                    <?= renderCircularProgress($p['dok_done'] ?? 0, $p['total_dist'] ?? 0, 'dokumentasi') ?>
                                </button>
                            </td>
                            <td class="py-2 px-2 text-center whitespace-nowrap">
                                <button type="button" onclick="openKolabReadonlyModal('<?= $kodeRef ?>', 'dokumen', '<?= $namaJs ?>')" class="hover:scale-105 transition-transform inline-block cursor-pointer">
                                    <?= renderCircularProgress($p['bast_done'] ?? 0, $p['total_bast'] ?? 0, 'bast') ?>
                                </button>
                            </td>

                            <!-- PENCAIRAN -->
                            <td class="py-2.5 px-3 text-center whitespace-nowrap">
                                <?= getStatusBadge($p['status']) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
            <?php if (!empty($pakets)): ?>
                <tfoot class="bg-slate-50/95 border-t-2 border-slate-200 font-bold text-xs">
                    <tr>
                        <td colspan="2" class="py-2.5 px-3 text-slate-700 uppercase text-[11px] tracking-wider">
                            Overall Total (<span id="kolabFootCount"><?= $summary['total_paket'] ?></span> Paket)
                        </td>
                        <td class="py-2.5 px-3 text-right font-mono text-slate-900 whitespace-nowrap" id="kolabFootKontrak">
                            <?= formatRupiah($summary['total_kontrak']) ?>
                        </td>
                        <td class="py-2.5 px-3 text-right font-mono text-slate-800 whitespace-nowrap" id="kolabFootDpp">
                            <?= formatRupiah($summary['total_dpp']) ?>
                        </td>
                        <td class="py-2.5 px-3 text-right font-mono text-amber-700 whitespace-nowrap" id="kolabFootPasal">
                            <?= formatRupiah($summary['total_pasal']) ?>
                        </td>
                        <td class="py-2.5 px-3 text-right font-mono text-emerald-700 whitespace-nowrap" id="kolabFootBersih">
                            <?= formatRupiah($summary['total_bersih']) ?>
                        </td>
                        <td class="py-2.5 px-2 text-center text-slate-600 text-[11px] whitespace-nowrap" id="kolabFootKomponen">
                            <div><?= $summary['total_items'] ?> Item</div>
                            <div class="text-[10px] font-normal text-slate-400"><?= number_format($summary['total_units'], 0, ',', '.') ?> Unit</div>
                        </td>
                        <td colspan="5" class="py-2.5 px-3"></td>
                    </tr>
                </tfoot>
            <?php endif; ?>
        </table>
    </div>
</div>

<!-- ========================================== -->
<!-- MODAL READ-ONLY DETAIL PAKET (DYNAMIC)     -->
<!-- ========================================== -->
<div id="modalKolabReadonly" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 transition-all duration-200 opacity-0 pointer-events-none hidden">
    <div class="modal-card bg-white rounded-2xl shadow-2xl border border-slate-100 max-w-5xl w-full transform scale-95 opacity-0 transition-all duration-200 overflow-hidden max-h-[88vh] flex flex-col">
        <div class="px-5 py-3.5 bg-slate-50/80 border-b border-slate-100 flex items-center justify-between shrink-0">
            <div class="flex items-center gap-2.5 min-w-0">
                <div id="krModalIconWrap" class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-lg shrink-0">
                    <ion-icon id="krModalIcon" name="information-circle-outline"></ion-icon>
                </div>
                <div class="min-w-0">
                    <div class="flex items-center gap-1.5">
                        <h3 id="krModalTitle" class="font-bold text-slate-800 text-sm">Detail Paket</h3>
                        <span id="krModalKode" class="px-1.5 py-0.2 rounded bg-slate-200/70 text-slate-700 font-mono text-[10px] font-bold"></span>
                    </div>
                    <p id="krModalSubtitle" class="text-[11px] text-slate-400 truncate"></p>
                </div>
            </div>
            <button type="button" onclick="closeKolabReadonlyModal()" class="p-1.5 text-slate-400 hover:text-slate-700 rounded-lg cursor-pointer">
                <ion-icon name="close-outline" class="text-xl"></ion-icon>
            </button>
        </div>
        <div id="krModalBody" class="p-5 overflow-y-auto flex-1"></div>
    </div>
</div>

<!-- ========================================== -->
<!-- POPUP IMAGE VIEWER / LIGHTBOX              -->
<!-- ========================================== -->
<div id="kolabPhotoViewer" class="fixed inset-0 z-[70] bg-slate-950/90 backdrop-blur-sm flex items-center justify-center p-4 hidden">
    <div class="relative max-w-4xl w-full flex flex-col items-center">
        <!-- Top bar -->
        <div class="w-full flex items-center justify-between text-white mb-3 px-1">
            <div class="min-w-0">
                <div id="kpvCaption" class="text-xs sm:text-sm font-bold truncate"></div>
                <div id="kpvCounter" class="text-[11px] text-slate-300 font-mono"></div>
            </div>
            <button type="button" onclick="closeKolabPhotoViewer()"
                    class="w-9 h-9 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center text-xl transition-colors cursor-pointer shrink-0">
                <ion-icon name="close-outline"></ion-icon>
            </button>
        </div>

        <!-- Image stage -->
        <div class="relative w-full flex items-center justify-center bg-slate-900/60 rounded-2xl border border-white/10 p-2 min-h-[260px] max-h-[78vh]">
            <button type="button" id="kpvPrevBtn" onclick="stepKolabPhoto(-1)"
                    class="absolute left-3 z-10 w-10 h-10 rounded-full bg-black/50 hover:bg-black/80 text-white flex items-center justify-center text-xl transition-colors cursor-pointer hidden">
                <ion-icon name="chevron-back-outline"></ion-icon>
            </button>

            <img id="kpvImage" src="" alt="Preview Foto" class="max-w-full max-h-[74vh] object-contain rounded-xl shadow-2xl">

            <button type="button" id="kpvNextBtn" onclick="stepKolabPhoto(1)"
                    class="absolute right-3 z-10 w-10 h-10 rounded-full bg-black/50 hover:bg-black/80 text-white flex items-center justify-center text-xl transition-colors cursor-pointer hidden">
                <ion-icon name="chevron-forward-outline"></ion-icon>
            </button>
        </div>
    </div>
</div>

<script>
const KOLAB_MODAL_BASE = <?= json_encode($modalBaseUrl) ?>;
let kpvPhotos = [];
let kpvIndex = 0;

function openKolabPhotoViewer(photos, startIdx, caption) {
    if (!Array.isArray(photos) || photos.length === 0) return;
    kpvPhotos = photos;
    kpvIndex = Math.max(0, Math.min(startIdx || 0, photos.length - 1));
    document.getElementById('kpvCaption').textContent = caption || 'Foto Dokumentasi';
    renderKolabPhoto();
    document.getElementById('kolabPhotoViewer').classList.remove('hidden');
}

function renderKolabPhoto() {
    document.getElementById('kpvImage').src = kpvPhotos[kpvIndex];
    document.getElementById('kpvCounter').textContent = (kpvIndex + 1) + ' / ' + kpvPhotos.length;
    const multi = kpvPhotos.length > 1;
    document.getElementById('kpvPrevBtn').classList.toggle('hidden', !multi);
    document.getElementById('kpvNextBtn').classList.toggle('hidden', !multi);
}

function stepKolabPhoto(dir) {
    if (kpvPhotos.length <= 1) return;
    kpvIndex = (kpvIndex + dir + kpvPhotos.length) % kpvPhotos.length;
    renderKolabPhoto();
}

function closeKolabPhotoViewer() {
    document.getElementById('kolabPhotoViewer')?.classList.add('hidden');
    const img = document.getElementById('kpvImage');
    if (img) img.src = '';
}

document.getElementById('kolabPhotoViewer')?.addEventListener('click', function (e) {
    if (e.target === this) closeKolabPhotoViewer();
});

document.addEventListener('keydown', function (e) {
    const viewer = document.getElementById('kolabPhotoViewer');
    if (!viewer || viewer.classList.contains('hidden')) return;
    if (e.key === 'Escape') closeKolabPhotoViewer();
    else if (e.key === 'ArrowLeft') stepKolabPhoto(-1);
    else if (e.key === 'ArrowRight') stepKolabPhoto(1);
});

function openKolabReadonlyModal(idOrKode, mod, namaPaket) {
    const cfgMap = {
        'info':        { name: 'Informasi & Finansial', icon: 'information-circle-outline', bg: 'bg-blue-50 text-blue-600' },
        'items':       { name: 'Komponen Item Barang', icon: 'list-outline', bg: 'bg-blue-50 text-blue-600' },
        'pemesanan':   { name: 'Progres Pemesanan', icon: 'bag-check-outline', bg: 'bg-amber-50 text-amber-700' },
        'distribusi':  { name: 'Alokasi Distribusi Titik', icon: 'navigate-outline', bg: 'bg-indigo-50 text-indigo-600' },
        'dokumentasi': { name: 'Foto Dokumentasi', icon: 'camera-outline', bg: 'bg-teal-50 text-teal-600' },
        'dokumen':     { name: 'Berkas Dokumen BAST', icon: 'ribbon-outline', bg: 'bg-amber-50 text-amber-700' }
    };
    const cfg = cfgMap[mod] || cfgMap['info'];

    document.getElementById('krModalTitle').textContent = cfg.name;
    document.getElementById('krModalKode').textContent = '#' + idOrKode;
    document.getElementById('krModalSubtitle').textContent = namaPaket || '';
    document.getElementById('krModalIcon').setAttribute('name', cfg.icon);
    document.getElementById('krModalIconWrap').className = 'w-8 h-8 rounded-xl flex items-center justify-center text-lg shrink-0 ' + cfg.bg;

    const bodyEl = document.getElementById('krModalBody');
    bodyEl.innerHTML = '<div class="animate-pulse h-36 bg-slate-100 rounded-xl"></div>';

    const modal = document.getElementById('modalKolabReadonly');
    modal.classList.remove('hidden', 'opacity-0', 'pointer-events-none');
    const card = modal.querySelector('.modal-card');
    if (card) {
        requestAnimationFrame(() => {
            card.classList.remove('scale-95', 'opacity-0');
        });
    }

    fetch(KOLAB_MODAL_BASE + '/' + encodeURIComponent(idOrKode) + '/' + encodeURIComponent(mod))
        .then(r => r.text())
        .then(html => { bodyEl.innerHTML = html; })
        .catch(() => { bodyEl.innerHTML = '<div class="text-center text-xs text-rose-600 py-8">Gagal memuat data.</div>'; });
}

function closeKolabReadonlyModal() {
    const modal = document.getElementById('modalKolabReadonly');
    if (!modal) return;
    modal.classList.add('opacity-0', 'pointer-events-none', 'hidden');
}

document.getElementById('modalKolabReadonly')?.addEventListener('click', function (e) {
    if (e.target === this) closeKolabReadonlyModal();
});

function filterByCompany(companyLower) {
    const sel = document.getElementById('filterKolabCompany');
    if (!sel) return;
    sel.value = (sel.value === companyLower) ? '' : companyLower;
    filterKolabTable();
}

function formatRpKolab(num) {
    const n = Math.round(num || 0);
    return (n < 0 ? '-Rp ' : 'Rp ') + Math.abs(n).toLocaleString('id-ID');
}

function filterKolabTable() {
    const q = (document.getElementById('searchKolabPaket')?.value || '').toLowerCase().trim();
    const comp = document.getElementById('filterKolabCompany')?.value || '';
    const status = document.getElementById('filterKolabStatus')?.value || '';

    const rows = document.querySelectorAll('.kolab-paket-row');
    let vis = 0, sKontrak = 0, sDpp = 0, sPasal = 0, sBersih = 0, sItems = 0, sUnits = 0;

    rows.forEach(row => {
        const show = (!q || (row.dataset.search || '').includes(q))
                  && (!comp || (row.dataset.company || '') === comp)
                  && (!status || (row.dataset.status || '') === status);
        row.style.display = show ? '' : 'none';
        if (show) {
            vis++;
            sKontrak += parseFloat(row.dataset.kontrak || 0);
            sDpp     += parseFloat(row.dataset.dpp || 0);
            sPasal   += parseFloat(row.dataset.pasal || 0);
            sBersih  += parseFloat(row.dataset.bersih || 0);
            sItems   += parseInt(row.dataset.items || 0, 10);
            sUnits   += parseFloat(row.dataset.units || 0);
        }
    });

    const setTxt = (id, t) => { const el = document.getElementById(id); if (el) el.textContent = t; };
    setTxt('kolabVisibleCount', vis);
    setTxt('kolabFootCount', vis);
    setTxt('kolabFootKontrak', formatRpKolab(sKontrak));
    setTxt('kolabFootDpp', formatRpKolab(sDpp));
    setTxt('kolabFootPasal', formatRpKolab(sPasal));
    setTxt('kolabFootBersih', formatRpKolab(sBersih));
    const komEl = document.getElementById('kolabFootKomponen');
    if (komEl) {
        komEl.innerHTML = '<div>' + sItems + ' Item</div><div class="text-[10px] font-normal text-slate-400">' + Math.round(sUnits).toLocaleString('id-ID') + ' Unit</div>';
    }
}

function resetKolabFilter() {
    const s = document.getElementById('searchKolabPaket');
    const c = document.getElementById('filterKolabCompany');
    const st = document.getElementById('filterKolabStatus');
    if (s) s.value = '';
    if (c) c.value = '';
    if (st) st.value = '';
    filterKolabTable();
}
</script>
