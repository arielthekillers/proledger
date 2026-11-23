<?php
/**
 * ProLedger - Manajemen Tujuan Distribusi (Admin Only)
 */
$kategoriList = array_unique(array_filter(array_map(fn($x) => trim($x['kategori'] ?? ''), $lokasiList)));
sort($kategoriList);
?>

<!-- Header & Actions -->
<div class="mb-4 flex flex-wrap items-center justify-between gap-3">
    <div>
        <div class="flex items-center gap-2 mb-0.5 flex-wrap">
            <h1 class="text-lg md:text-xl font-extrabold text-slate-900 tracking-tight">Manajemen Tujuan Distribusi</h1>
            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                <?= count($lokasiList) ?> Lokasi
            </span>
        </div>
        <p class="text-xs text-slate-400">
            Master direktori sekolah, instansi, dan alamat penerima distribusi serta data pejabat penanda tangan BAST.
        </p>
    </div>

    <button type="button" data-modal-open="modalTambahTujuan"
            class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl text-xs shadow-sm shadow-blue-600/20 transition-all cursor-pointer">
        <ion-icon name="add-circle-outline" class="text-base"></ion-icon>
        <span>Tambah Tujuan</span>
    </button>
</div>

<!-- Table Card -->
<div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 overflow-hidden">
    <div class="p-3.5 px-5 border-b border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-3">
        <div class="relative w-full md:w-72">
            <ion-icon name="search-outline" class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-base"></ion-icon>
            <input type="text" id="searchTujuan" onkeyup="filterTujuanTable()"
                   placeholder="Cari nama sekolah, PIC, kab/kota..."
                   class="w-full pl-9 pr-3 py-1.5 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-blue-500 text-xs focus:bg-white">
        </div>

        <div class="flex items-center gap-2 flex-wrap">
            <select id="filterTujuanKategori" onchange="filterTujuanTable()"
                    class="px-2.5 py-1.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700 cursor-pointer">
                <option value="">Semua Kategori</option>
                <?php foreach ($kategoriList as $kat): ?>
                    <option value="<?= htmlspecialchars(strtolower($kat)) ?>"><?= htmlspecialchars($kat) ?></option>
                <?php endforeach; ?>
            </select>

            <select id="filterTujuanKab" onchange="filterTujuanTable()"
                    class="px-2.5 py-1.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700 cursor-pointer">
                <option value="">Semua Kab / Kota</option>
                <?php foreach ($kabupatenList as $kb): ?>
                    <option value="<?= htmlspecialchars(strtolower($kb['nama'])) ?>"><?= htmlspecialchars($kb['nama']) ?></option>
                <?php endforeach; ?>
            </select>

            <button type="button" onclick="resetTujuanFilter()"
                    class="p-1.5 text-slate-400 hover:text-slate-700 hover:bg-slate-100 rounded-xl border border-slate-200 cursor-pointer"
                    title="Reset Filter">
                <ion-icon name="refresh-outline" class="text-sm"></ion-icon>
            </button>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left text-xs">
            <thead class="bg-slate-50/80 text-[10px] uppercase tracking-wider text-slate-500 font-bold border-b border-slate-200/80">
                <tr>
                    <th class="py-3 px-4 w-12 text-center">No</th>
                    <th class="py-3 px-4">Nama Tujuan / Instansi</th>
                    <th class="py-3 px-3 text-center">Kategori</th>
                    <th class="py-3 px-3">Pejabat / PIC (BAST)</th>
                    <th class="py-3 px-3">Kab / Kota &amp; Alamat</th>
                    <th class="py-3 px-3">Kontak</th>
                    <th class="py-3 px-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php if (empty($lokasiList)): ?>
                    <tr>
                        <td colspan="7" class="py-12 text-center text-slate-400 text-xs">Belum ada data tujuan distribusi.</td>
                    </tr>
                <?php else: ?>
                    <?php $no = 1; foreach ($lokasiList as $l): ?>
                        <tr class="hover:bg-slate-50/70 transition-colors tujuan-row"
                            data-search="<?= htmlspecialchars(strtolower(($l['nama_lokasi'] ?? '') . ' ' . ($l['nama_pic'] ?? '') . ' ' . ($l['kab_kota'] ?? '') . ' ' . ($l['alamat_lengkap'] ?? ''))) ?>"
                            data-kategori="<?= htmlspecialchars(strtolower($l['kategori'] ?? '')) ?>"
                            data-kab="<?= htmlspecialchars(strtolower($l['kab_kota'] ?? '')) ?>">
                            <td class="py-2.5 px-4 text-center text-slate-400 font-mono text-[11px]"><?= $no++ ?></td>
                            <td class="py-2.5 px-4 font-bold text-slate-800"><?= htmlspecialchars($l['nama_lokasi']) ?></td>
                            <td class="py-2.5 px-3 text-center">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-indigo-50 text-indigo-700 border border-indigo-200">
                                    <?= htmlspecialchars($l['kategori'] ?: 'Sekolah') ?>
                                </span>
                            </td>
                            <td class="py-2.5 px-3">
                                <div class="font-semibold text-slate-800"><?= htmlspecialchars($l['nama_pic'] ?: '-') ?></div>
                                <div class="text-[10px] text-slate-400"><?= htmlspecialchars($l['jabatan_pic'] ?: '-') ?></div>
                            </td>
                            <td class="py-2.5 px-3 max-w-[240px]">
                                <div class="font-semibold text-slate-700"><?= htmlspecialchars($l['kab_kota'] ?: '-') ?></div>
                                <div class="text-[10px] text-slate-400 truncate" title="<?= htmlspecialchars($l['alamat_lengkap'] ?? '') ?>">
                                    <?= htmlspecialchars($l['alamat_lengkap'] ?: '-') ?>
                                </div>
                            </td>
                            <td class="py-2.5 px-3 text-slate-600 font-mono text-[11px]">
                                <?= htmlspecialchars($l['no_kontak'] ?: '-') ?>
                            </td>
                            <td class="py-2.5 px-4 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-1">
                                    <button type="button"
                                            onclick='openEditTujuanModal(<?= json_encode($l, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)'
                                            class="p-1.5 text-slate-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-colors text-base cursor-pointer"
                                            title="Edit Tujuan">
                                        <ion-icon name="create-outline"></ion-icon>
                                    </button>
                                    <a href="<?= APP_URL ?>/tujuan/delete/<?= $l['id'] ?>"
                                       data-confirm-delete="<?= htmlspecialchars($l['nama_lokasi']) ?>"
                                       data-type="Tujuan Distribusi"
                                       class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors text-base"
                                       title="Hapus Tujuan">
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

<!-- MODAL TAMBAH TUJUAN -->
<div id="modalTambahTujuan" class="app-modal fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 transition-all duration-200 opacity-0 pointer-events-none hidden">
    <div class="modal-card bg-white rounded-2xl shadow-2xl border border-slate-100 max-w-lg w-full transform scale-95 opacity-0 transition-all duration-200 overflow-hidden">
        <div class="px-5 py-4 bg-slate-50/80 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-bold text-slate-800 text-sm">Tambah Tujuan Distribusi</h3>
            <button type="button" data-modal-close="modalTambahTujuan" class="p-1.5 text-slate-400 hover:text-slate-700 rounded-lg cursor-pointer">
                <ion-icon name="close-outline" class="text-xl"></ion-icon>
            </button>
        </div>

        <form method="POST" action="<?= APP_URL ?>/tujuan/store" class="p-5 space-y-3.5">
            <?= csrfField() ?>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Tujuan / Instansi <span class="text-red-500">*</span></label>
                    <input type="text" name="nama_lokasi" required placeholder="Contoh: SMAN 1 BONTANG"
                           class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-blue-500 focus:bg-white">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Kategori</label>
                    <select name="kategori" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-blue-500">
                        <option value="Sekolah">Sekolah</option>
                        <option value="Kantor Dinas">Kantor Dinas</option>
                        <option value="Puskesmas">Puskesmas</option>
                        <option value="Instansi">Instansi</option>
                        <option value="Lainnya">Lainnya</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Pejabat / PIC</label>
                    <input type="text" name="nama_pic" placeholder="Nama penerima BAST"
                           class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-blue-500 focus:bg-white">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Jabatan</label>
                    <input type="text" name="jabatan_pic" value="Kepala Sekolah"
                           class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-blue-500 focus:bg-white">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Kabupaten / Kota</label>
                    <select name="kab_kota" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-blue-500">
                        <option value="">-- Pilih Kab / Kota --</option>
                        <?php foreach ($kabupatenList as $kb): ?>
                            <option value="<?= htmlspecialchars($kb['nama']) ?>"><?= htmlspecialchars($kb['nama']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">No. Kontak / WA</label>
                    <input type="text" name="no_kontak" placeholder="08xxxxxxxxxx"
                           class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-blue-500 focus:bg-white">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Alamat Lengkap</label>
                <textarea name="alamat_lengkap" rows="2" placeholder="Alamat jalan, kelurahan, kecamatan..."
                          class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-blue-500 focus:bg-white"></textarea>
            </div>

            <div class="pt-3 border-t border-slate-100 flex justify-end gap-2">
                <button type="button" data-modal-close="modalTambahTujuan" class="px-3.5 py-2 rounded-xl border border-slate-200 text-slate-600 font-semibold text-xs cursor-pointer">Batal</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-semibold text-xs cursor-pointer">Simpan Tujuan</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL EDIT TUJUAN -->
<div id="modalEditTujuan" class="app-modal fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 transition-all duration-200 opacity-0 pointer-events-none hidden">
    <div class="modal-card bg-white rounded-2xl shadow-2xl border border-slate-100 max-w-lg w-full transform scale-95 opacity-0 transition-all duration-200 overflow-hidden">
        <div class="px-5 py-4 bg-slate-50/80 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-bold text-slate-800 text-sm">Edit Tujuan Distribusi</h3>
            <button type="button" data-modal-close="modalEditTujuan" class="p-1.5 text-slate-400 hover:text-slate-700 rounded-lg cursor-pointer">
                <ion-icon name="close-outline" class="text-xl"></ion-icon>
            </button>
        </div>

        <form id="formEditTujuan" method="POST" action="" class="p-5 space-y-3.5">
            <?= csrfField() ?>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Tujuan / Instansi <span class="text-red-500">*</span></label>
                    <input type="text" id="et_nama_lokasi" name="nama_lokasi" required
                           class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-blue-500 focus:bg-white">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Kategori</label>
                    <select id="et_kategori" name="kategori" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-blue-500">
                        <option value="Sekolah">Sekolah</option>
                        <option value="Kantor Dinas">Kantor Dinas</option>
                        <option value="Puskesmas">Puskesmas</option>
                        <option value="Instansi">Instansi</option>
                        <option value="Lainnya">Lainnya</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Pejabat / PIC</label>
                    <input type="text" id="et_nama_pic" name="nama_pic"
                           class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-blue-500 focus:bg-white">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Jabatan</label>
                    <input type="text" id="et_jabatan_pic" name="jabatan_pic"
                           class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-blue-500 focus:bg-white">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Kabupaten / Kota</label>
                    <select id="et_kab_kota" name="kab_kota" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-blue-500">
                        <option value="">-- Pilih Kab / Kota --</option>
                        <?php foreach ($kabupatenList as $kb): ?>
                            <option value="<?= htmlspecialchars($kb['nama']) ?>"><?= htmlspecialchars($kb['nama']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">No. Kontak / WA</label>
                    <input type="text" id="et_no_kontak" name="no_kontak"
                           class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-blue-500 focus:bg-white">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Alamat Lengkap</label>
                <textarea id="et_alamat_lengkap" name="alamat_lengkap" rows="2"
                          class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-blue-500 focus:bg-white"></textarea>
            </div>

            <div class="pt-3 border-t border-slate-100 flex justify-end gap-2">
                <button type="button" data-modal-close="modalEditTujuan" class="px-3.5 py-2 rounded-xl border border-slate-200 text-slate-600 font-semibold text-xs cursor-pointer">Batal</button>
                <button type="submit" class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-xl font-semibold text-xs cursor-pointer">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script>
function openEditTujuanModal(item) {
    if (!item) return;
    document.getElementById('formEditTujuan').action = '<?= APP_URL ?>/tujuan/update/' + item.id;
    document.getElementById('et_nama_lokasi').value = item.nama_lokasi || '';
    document.getElementById('et_kategori').value = item.kategori || 'Sekolah';
    document.getElementById('et_nama_pic').value = item.nama_pic || '';
    document.getElementById('et_jabatan_pic').value = item.jabatan_pic || '';
    document.getElementById('et_kab_kota').value = item.kab_kota || '';
    document.getElementById('et_no_kontak').value = item.no_kontak || '';
    document.getElementById('et_alamat_lengkap').value = item.alamat_lengkap || '';
    window.ProModal.open('modalEditTujuan');
}

function filterTujuanTable() {
    const q = (document.getElementById('searchTujuan')?.value || '').toLowerCase().trim();
    const kat = document.getElementById('filterTujuanKategori')?.value || '';
    const kab = document.getElementById('filterTujuanKab')?.value || '';

    document.querySelectorAll('.tujuan-row').forEach(row => {
        const show = (!q || (row.dataset.search || '').includes(q))
                  && (!kat || (row.dataset.kategori || '') === kat)
                  && (!kab || (row.dataset.kab || '') === kab);
        row.style.display = show ? '' : 'none';
    });
}

function resetTujuanFilter() {
    document.getElementById('searchTujuan').value = '';
    document.getElementById('filterTujuanKategori').value = '';
    document.getElementById('filterTujuanKab').value = '';
    filterTujuanTable();
}
</script>
