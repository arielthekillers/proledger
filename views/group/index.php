<?php
/**
 * ProLedger - Daftar Group Paket Kegiatan
 * Menampilkan kelompok payung paket kegiatan dengan roll-up finansial & popup CRUD
 */
?>

<!-- Header & Actions -->
<div class="mb-5 flex flex-wrap items-center justify-between gap-3">
    <div>
        <div class="flex items-center gap-2 mb-0.5">
            <h1 class="text-lg md:text-xl font-extrabold text-slate-900 tracking-tight">Group Paket Kegiatan</h1>
            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-blue-50 text-blue-700 border border-blue-200">
                <?= count($groups) ?> Group
            </span>
        </div>
        <p class="text-xs text-slate-400">
            Kelompokkan paket kegiatan ke dalam program payung. Paket independen tetap dapat berjalan mandiri tanpa group.
        </p>
    </div>

    <div class="flex items-center gap-2">
        <a href="<?= APP_URL ?>/paket" 
           class="inline-flex items-center gap-1.5 px-3 py-2 bg-white hover:bg-slate-50 text-slate-700 font-semibold rounded-xl border border-slate-200 transition-all text-xs shadow-2xs hover:shadow-xs">
            <ion-icon name="cube-outline" class="text-sm"></ion-icon>
            <span>Semua Paket</span>
        </a>

        <?php if ($role === 'admin' || $role === 'user'): ?>
            <button type="button" 
                    data-modal-open="modalTambahGroup" 
                    class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl text-xs shadow-sm shadow-blue-600/20 transition-all hover:scale-[1.01] active:scale-[0.98]">
                <ion-icon name="add-circle-outline" class="text-base"></ion-icon>
                <span>Tambah Group</span>
            </button>
        <?php endif; ?>
    </div>
</div>

<!-- Search & Filter Bar -->
<div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 p-3 mb-4">
    <div class="flex flex-col sm:flex-row items-center justify-between gap-3">
        <div class="relative w-full sm:w-80">
            <ion-icon name="search-outline" class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-base"></ion-icon>
            <input type="text" id="searchGroupInput" onkeyup="filterGroupTable()" 
                   placeholder="Cari nama group, kode, atau dinas..." 
                   class="w-full pl-9 pr-3 py-1.5 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-blue-500 text-xs focus:bg-white transition-all">
        </div>

        <div class="text-[11px] text-slate-400 text-right w-full sm:w-auto">
            <span>Menampilkan data group berdasarkan perusahaan aktif</span>
        </div>
    </div>
</div>

<!-- Groups Table Card -->
<div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-xs" id="groupTable">
            <thead class="bg-slate-50/80 text-[11px] uppercase tracking-wider text-slate-500 font-bold border-b border-slate-200/80">
                <tr>
                    <th class="py-3 px-4">Nama Group / Program</th>
                    <th class="py-3 px-4">Perusahaan</th>
                    <th class="py-3 px-4 text-center">Jumlah Paket</th>
                    <th class="py-3 px-4 text-right">Total Pagu Acuan</th>
                    <th class="py-3 px-4 text-right">Total DPP (-11%)</th>
                    <th class="py-3 px-4 text-right">Total Potongan Pasal</th>
                    <th class="py-3 px-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php if (empty($groups)): ?>
                    <tr>
                        <td colspan="7" class="py-12 text-center text-slate-400">
                            <div class="w-14 h-14 bg-slate-50 rounded-2xl flex items-center justify-center mx-auto mb-3 text-slate-300 text-3xl">
                                <ion-icon name="folder-open-outline"></ion-icon>
                            </div>
                            <h4 class="text-sm font-bold text-slate-700 mb-1">Belum Ada Group Paket</h4>
                            <p class="text-xs text-slate-400 max-w-sm mx-auto mb-4">
                                Buat group untuk mengelompokkan paket kegiatan di bawah kontrak atau program yang sama (misal: Dinas Pendidikan 2025).
                            </p>
                            <?php if ($role === 'admin' || $role === 'user'): ?>
                                <button type="button" 
                                        data-modal-open="modalTambahGroup" 
                                        class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl text-xs shadow-xs transition-all">
                                    <ion-icon name="add-circle-outline"></ion-icon>
                                    <span>Buat Group Pertama</span>
                                </button>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($groups as $g): 
                        $sisaPagu = $g['total_pagu'] - $g['total_realisasi'];
                    ?>
                        <tr class="hover:bg-slate-50/60 transition-colors group">
                            <!-- Nama Group & Kode -->
                            <td class="py-3 px-4">
                                <div class="flex items-start gap-2.5">
                                    <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center text-base shrink-0 mt-0.5">
                                        <ion-icon name="folder-outline"></ion-icon>
                                    </div>
                                    <div class="min-w-0">
                                        <a href="<?= APP_URL ?>/group/detail/<?= $g['id'] ?>" class="font-bold text-slate-800 hover:text-blue-600 text-xs transition-colors block truncate">
                                            <?= htmlspecialchars($g['nama_group']) ?>
                                        </a>
                                        <div class="flex items-center gap-2 mt-0.5">
                                            <?php if (!empty($g['kode_group'])): ?>
                                                <span class="inline-block px-1.5 py-0.2 rounded bg-slate-100 text-slate-600 font-mono text-[10px] font-semibold">
                                                    <?= htmlspecialchars($g['kode_group']) ?>
                                                </span>
                                            <?php endif; ?>
                                            <?php if (!empty($g['deskripsi'])): ?>
                                                <span class="text-[10px] text-slate-400 truncate max-w-[200px]" title="<?= htmlspecialchars($g['deskripsi']) ?>">
                                                    <?= htmlspecialchars($g['deskripsi']) ?>
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Perusahaan -->
                            <td class="py-3 px-4 text-xs text-slate-600">
                                <?= htmlspecialchars($g['perusahaan_nama'] ?: 'Umum / Semua') ?>
                            </td>

                            <!-- Jumlah Paket -->
                            <td class="py-3 px-4 text-center">
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full font-semibold text-[11px] <?= $g['total_paket'] > 0 ? 'bg-blue-50 text-blue-700 border border-blue-200' : 'bg-slate-100 text-slate-500' ?>">
                                    <ion-icon name="cube-outline" class="text-xs"></ion-icon>
                                    <span><?= $g['total_paket'] ?> Paket</span>
                                </span>
                            </td>

                            <!-- Total Pagu Acuan -->
                            <td class="py-3 px-4 text-right">
                                <div class="font-mono font-bold text-slate-800 text-xs">
                                    <?= formatRupiah($g['total_pagu_nego'] > 0 ? $g['total_pagu_nego'] : $g['total_pagu']) ?>
                                </div>
                            </td>

                            <!-- Total DPP -->
                            <td class="py-3 px-4 text-right">
                                <div class="font-mono font-medium text-slate-600 text-xs">
                                    <?= formatRupiah($g['total_dpp']) ?>
                                </div>
                            </td>

                            <!-- Total Potongan Pasal -->
                            <td class="py-3 px-4 text-right">
                                <div class="font-mono font-bold text-xs text-amber-700">
                                    <?= formatRupiah($g['total_pasal']) ?>
                                </div>
                            </td>

                            <!-- Aksi -->
                            <td class="py-3 px-4 text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <!-- Detail -->
                                    <a href="<?= APP_URL ?>/group/detail/<?= $g['id'] ?>" 
                                       class="p-1.5 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors text-base"
                                       title="Lihat Detail & Rekapitulasi">
                                        <ion-icon name="eye-outline"></ion-icon>
                                    </a>

                                    <?php if ($role === 'admin' || $role === 'user'): ?>
                                        <!-- Edit Modal Trigger -->
                                        <button type="button" 
                                                onclick='openEditGroupModal(<?= json_encode($g, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)'
                                                class="p-1.5 text-slate-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-colors text-base"
                                                title="Edit Group">
                                            <ion-icon name="create-outline"></ion-icon>
                                        </button>

                                        <!-- Hapus via SweetAlert2 -->
                                        <a href="<?= APP_URL ?>/group/delete/<?= $g['id'] ?>" 
                                           data-confirm-delete="<?= htmlspecialchars($g['nama_group']) ?>"
                                           data-type="Group Paket"
                                           class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors text-base"
                                           title="Hapus Group">
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

<!-- ========================================== -->
<!-- MODAL 1: TAMBAH GROUP (POPUP)              -->
<!-- ========================================== -->
<div id="modalTambahGroup" class="app-modal fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 transition-all duration-200 opacity-0 pointer-events-none hidden">
    <div class="modal-card bg-white rounded-2xl shadow-2xl border border-slate-100 max-w-md w-full transform scale-95 opacity-0 transition-all duration-200 overflow-hidden">
        <div class="px-5 py-4 bg-slate-50/80 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-blue-600 text-white flex items-center justify-center text-lg shadow-xs">
                    <ion-icon name="folder-open-outline"></ion-icon>
                </div>
                <div>
                    <h3 class="font-bold text-slate-800 text-sm">Tambah Group Paket</h3>
                    <p class="text-[11px] text-slate-400">Buat wadah program untuk mengelompokkan paket</p>
                </div>
            </div>
            <button type="button" data-modal-close="modalTambahGroup" class="p-1.5 text-slate-400 hover:text-slate-700 rounded-lg transition-colors">
                <ion-icon name="close-outline" class="text-xl"></ion-icon>
            </button>
        </div>

        <form method="POST" action="<?= APP_URL ?>/group/store" class="p-5 space-y-3.5">
            <?= csrfField() ?>

            <!-- Perusahaan Pemilik -->
            <?php if (!empty($perusahaanList) && count($perusahaanList) > 1): ?>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Perusahaan</label>
                    <select name="perusahaan_id" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-blue-500 text-xs focus:bg-white transition-all">
                        <option value="">-- Umum / Semua Perusahaan --</option>
                        <?php foreach ($perusahaanList as $cp): ?>
                            <option value="<?= $cp['id'] ?>" <?= ($activeCompId == $cp['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cp['nama']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            <?php else: ?>
                <input type="hidden" name="perusahaan_id" value="<?= $activeCompId ?? ($perusahaanList[0]['id'] ?? '') ?>">
            <?php endif; ?>

            <!-- Nama Group -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Group / Program <span class="text-red-500">*</span></label>
                <input type="text" name="nama_group" required 
                       class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-blue-500 text-xs focus:bg-white transition-all"
                       placeholder="Contoh: Dinas Pendidikan 2025">
            </div>

            <!-- Kode Group -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Kode Group (Opsional)</label>
                <input type="text" name="kode_group" 
                       class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-blue-500 text-xs focus:bg-white transition-all"
                       placeholder="Contoh: GRP-DIKNAS-2025">
            </div>

            <!-- Deskripsi -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Deskripsi / Ruang Lingkup</label>
                <textarea name="deskripsi" rows="2" 
                          class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-blue-500 text-xs focus:bg-white transition-all"
                          placeholder="Catatan mengenai kontrak payung atau dinas terkait..."></textarea>
            </div>

            <!-- Footer -->
            <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                <button type="button" data-modal-close="modalTambahGroup" 
                        class="px-3.5 py-1.5 rounded-xl border border-slate-200 text-slate-600 font-semibold hover:bg-slate-50 text-xs transition-colors">
                    Batal
                </button>
                <button type="submit" 
                        class="px-4 py-1.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl text-xs shadow-xs transition-all flex items-center gap-1.5">
                    <ion-icon name="checkmark-outline" class="text-sm"></ion-icon>
                    <span>Simpan Group</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================== -->
<!-- MODAL 2: EDIT GROUP (POPUP)                -->
<!-- ========================================== -->
<div id="modalEditGroup" class="app-modal fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 transition-all duration-200 opacity-0 pointer-events-none hidden">
    <div class="modal-card bg-white rounded-2xl shadow-2xl border border-slate-100 max-w-md w-full transform scale-95 opacity-0 transition-all duration-200 overflow-hidden">
        <div class="px-5 py-4 bg-slate-50/80 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-amber-500 text-white flex items-center justify-center text-lg shadow-xs">
                    <ion-icon name="create-outline"></ion-icon>
                </div>
                <div>
                    <h3 class="font-bold text-slate-800 text-sm">Edit Group Paket</h3>
                    <p class="text-[11px] text-slate-400" id="editGroupSubtitle">Perbarui rincian data group</p>
                </div>
            </div>
            <button type="button" data-modal-close="modalEditGroup" class="p-1.5 text-slate-400 hover:text-slate-700 rounded-lg transition-colors">
                <ion-icon name="close-outline" class="text-xl"></ion-icon>
            </button>
        </div>

        <form id="formEditGroup" method="POST" action="" class="p-5 space-y-3.5">
            <?= csrfField() ?>

            <!-- Perusahaan Pemilik -->
            <?php if (!empty($perusahaanList) && count($perusahaanList) > 1): ?>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Perusahaan</label>
                    <select id="edit_group_perusahaan_id" name="perusahaan_id" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-blue-500 text-xs focus:bg-white transition-all">
                        <option value="">-- Umum / Semua Perusahaan --</option>
                        <?php foreach ($perusahaanList as $cp): ?>
                            <option value="<?= $cp['id'] ?>"><?= htmlspecialchars($cp['nama']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            <?php endif; ?>

            <!-- Nama Group -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Group / Program <span class="text-red-500">*</span></label>
                <input type="text" id="edit_group_nama" name="nama_group" required 
                       class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-blue-500 text-xs focus:bg-white transition-all">
            </div>

            <!-- Kode Group -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Kode Group (Opsional)</label>
                <input type="text" id="edit_group_kode" name="kode_group" 
                       class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-blue-500 text-xs focus:bg-white transition-all">
            </div>

            <!-- Deskripsi -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Deskripsi / Ruang Lingkup</label>
                <textarea id="edit_group_deskripsi" name="deskripsi" rows="2" 
                          class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-blue-500 text-xs focus:bg-white transition-all"></textarea>
            </div>

            <!-- Footer -->
            <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                <button type="button" data-modal-close="modalEditGroup" 
                        class="px-3.5 py-1.5 rounded-xl border border-slate-200 text-slate-600 font-semibold hover:bg-slate-50 text-xs transition-colors">
                    Batal
                </button>
                <button type="submit" 
                        class="px-4 py-1.5 bg-amber-500 hover:bg-amber-600 text-white font-semibold rounded-xl text-xs shadow-xs transition-all flex items-center gap-1.5">
                    <ion-icon name="checkmark-outline" class="text-sm"></ion-icon>
                    <span>Simpan Perubahan</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
/**
 * Filter tabel group secara instan
 */
function filterGroupTable() {
    const input = document.getElementById("searchGroupInput");
    const filter = input.value.toLowerCase();
    const table = document.getElementById("groupTable");
    if (!table) return;
    const tr = table.getElementsByTagName("tr");

    for (let i = 1; i < tr.length; i++) {
        const textContent = tr[i].textContent || tr[i].innerText;
        if (textContent.toLowerCase().indexOf(filter) > -1) {
            tr[i].style.display = "";
        } else {
            tr[i].style.display = "none";
        }
    }
}

/**
 * Buka modal edit group
 */
function openEditGroupModal(group) {
    if (!group) return;

    const form = document.getElementById('formEditGroup');
    form.action = '<?= APP_URL ?>/group/update/' + group.id;

    document.getElementById('edit_group_nama').value = group.nama_group || '';
    document.getElementById('edit_group_kode').value = group.kode_group || '';
    document.getElementById('edit_group_deskripsi').value = group.deskripsi || '';
    document.getElementById('editGroupSubtitle').textContent = 'Mengubah: ' + (group.nama_group || '');

    const pSelect = document.getElementById('edit_group_perusahaan_id');
    if (pSelect) {
        pSelect.value = group.perusahaan_id || '';
    }

    window.ProModal.open('modalEditGroup');
}
</script>
