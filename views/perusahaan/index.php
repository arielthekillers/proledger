<!-- Header & Actions -->
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
    <div>
        <div class="flex items-center gap-2">
            <h1 class="text-lg md:text-xl font-bold text-slate-800">
                <?= ($role === 'admin') ? 'Daftar Semua Perusahaan' : 'Perusahaan Saya' ?>
            </h1>
            <span class="px-2 py-0.5 rounded-full bg-slate-200/80 text-slate-700 text-[10px] font-bold"><?= count($perusahaan) ?></span>
        </div>
        <p class="text-xs text-slate-500 mt-0.5">Kelola data mitra kerja, kontraktor, dan status perusahaan aktif</p>
    </div>
    
    <div class="flex items-center gap-2.5">
        <!-- Quick Search Filter -->
        <div class="relative w-48 sm:w-56">
            <span class="absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm">
                <ion-icon name="search-outline"></ion-icon>
            </span>
            <input type="text" id="searchCompanyInput" onkeyup="filterCompanyTable()" 
                   placeholder="Cari perusahaan..." 
                   class="w-full pl-8 pr-3 py-1.5 bg-white border border-slate-200 rounded-lg text-xs focus:outline-none focus:border-blue-500 transition-all">
        </div>

        <!-- Trigger Modal Tambah -->
        <button type="button" data-modal-open="modalTambahPerusahaan" 
                class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition-all shadow-xs text-xs hover:scale-[1.01] active:scale-[0.98] whitespace-nowrap">
            <ion-icon name="business-outline" class="text-base"></ion-icon>
            <span>+ Tambah Perusahaan</span>
        </button>
    </div>
</div>

<!-- Compact Table Card -->
<div class="bg-white rounded-xl border border-slate-200/80 shadow-xs overflow-hidden">
    <?php if (empty($perusahaan)): ?>
        <div class="py-12 text-center">
            <div class="w-12 h-12 bg-slate-50 text-slate-400 rounded-xl flex items-center justify-center mx-auto mb-2 text-2xl">
                <ion-icon name="business-outline"></ion-icon>
            </div>
            <h3 class="text-xs font-bold text-slate-800">Belum ada perusahaan terdaftar</h3>
            <p class="text-[11px] text-slate-400 mt-0.5 mb-3">Tambahkan perusahaan pertama Anda untuk mulai mengelola proyek.</p>
            <button type="button" data-modal-open="modalTambahPerusahaan" 
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-600 text-white text-xs font-semibold rounded-lg shadow-xs hover:bg-blue-700 transition-colors">
                <ion-icon name="add-outline" class="text-sm"></ion-icon>
                <span>Tambah Perusahaan Baru</span>
            </button>
        </div>
    <?php else: ?>
        <div class="overflow-x-auto">
            <table id="companyTable" class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50/75 border-b border-slate-200 text-[11px] uppercase font-bold text-slate-500 tracking-wider">
                    <tr>
                        <th class="py-2.5 px-4">Nama Perusahaan</th>
                        <th class="py-2.5 px-4">Kontak</th>
                        <th class="py-2.5 px-4">Alamat</th>
                        <th class="py-2.5 px-4 text-center">Total Paket</th>
                        <th class="py-2.5 px-4 text-center">Status Keaktifan</th>
                        <?php if ($role === 'admin'): ?>
                            <th class="py-2.5 px-4">Pemilik Akun</th>
                        <?php endif; ?>
                        <th class="py-2.5 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php foreach ($perusahaan as $p): ?>
                        <?php
                        $isActive = (($activeCompanyId ?? null) == $p['id']);
                        $pAvatarUrls = !empty($p['avatar']) ? Uploader::getAllUrls($p['avatar'], 'perusahaan', 'avatar') : null;
                        ?>
                        <tr class="hover:bg-slate-50/60 transition-colors <?= $isActive ? 'bg-blue-50/20' : '' ?>">
                            <!-- Nama Perusahaan -->
                            <td class="py-2.5 px-4">
                                <div class="flex items-center gap-2.5">
                                    <?php if ($pAvatarUrls && !empty($pAvatarUrls['small'])): ?>
                                        <img src="<?= htmlspecialchars($pAvatarUrls['small']) ?>" alt="<?= htmlspecialchars($p['nama']) ?>"
                                             class="w-8 h-8 rounded-lg object-cover border border-slate-200 bg-white shrink-0">
                                    <?php else: ?>
                                        <div class="w-8 h-8 rounded-lg <?= $isActive ? 'bg-blue-600 text-white shadow-xs' : 'bg-blue-50 text-blue-600' ?> font-bold flex items-center justify-center shrink-0 text-xs">
                                            <?= strtoupper(substr($p['nama'], 0, 1)) ?>
                                        </div>
                                    <?php endif; ?>
                                    <div class="truncate max-w-[200px]">
                                        <div class="font-bold text-slate-800 flex items-center gap-1.5">
                                            <a href="<?= APP_URL ?>/perusahaan/detail/<?= $p['id'] ?>" class="hover:text-blue-600 transition-colors truncate">
                                                <?= htmlspecialchars($p['nama']) ?>
                                            </a>
                                        </div>
                                        <div class="text-[10px] text-slate-400">ID: #<?= str_pad($p['id'], 3, '0', STR_PAD_LEFT) ?></div>
                                    </div>
                                </div>
                            </td>

                            <!-- Kontak -->
                            <td class="py-2.5 px-4">
                                <div class="space-y-0.5 text-[11px]">
                                    <?php if (!empty($p['email'])): ?>
                                        <div class="flex items-center gap-1 text-slate-700">
                                            <ion-icon name="mail-outline" class="text-slate-400 text-xs shrink-0"></ion-icon>
                                            <span class="truncate max-w-[170px]"><?= htmlspecialchars($p['email']) ?></span>
                                        </div>
                                    <?php endif; ?>
                                    <?php if (!empty($p['telepon'])): ?>
                                        <div class="flex items-center gap-1 text-slate-500 text-[10px]">
                                            <ion-icon name="call-outline" class="text-slate-400 text-xs shrink-0"></ion-icon>
                                            <span><?= htmlspecialchars($p['telepon']) ?></span>
                                        </div>
                                    <?php endif; ?>
                                    <?php if (empty($p['email']) && empty($p['telepon'])): ?>
                                        <span class="text-slate-400 italic text-[10px]">-</span>
                                    <?php endif; ?>
                                </div>
                            </td>

                            <!-- Alamat -->
                            <td class="py-2.5 px-4">
                                <span class="line-clamp-1 max-w-[200px] text-[11px] text-slate-600" title="<?= htmlspecialchars($p['alamat'] ?? '') ?>">
                                    <?= htmlspecialchars($p['alamat'] ?: '-') ?>
                                </span>
                            </td>

                            <!-- Total Paket -->
                            <td class="py-2.5 px-4 text-center">
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-slate-100 text-slate-700 font-semibold text-[11px]">
                                    <ion-icon name="briefcase-outline" class="text-xs"></ion-icon>
                                    <span><?= $p['jumlah_paket'] ?></span>
                                </span>
                            </td>

                            <!-- Status Keaktifan (Switch) -->
                            <td class="py-2.5 px-4 text-center">
                                <?php if ($isActive): ?>
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                        Sedang Aktif
                                    </span>
                                <?php else: ?>
                                    <a href="<?= APP_URL ?>/perusahaan/switch/<?= $p['id'] ?>" 
                                       class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-semibold bg-slate-100 hover:bg-blue-50 text-slate-600 hover:text-blue-600 transition-colors border border-slate-200/80">
                                        <ion-icon name="swap-horizontal-outline"></ion-icon>
                                        <span>Jadikan Aktif</span>
                                    </a>
                                <?php endif; ?>
                            </td>

                            <!-- Pemilik (Admin Only) -->
                            <?php if ($role === 'admin'): ?>
                                <td class="py-2.5 px-4 text-[11px] text-slate-600">
                                    <?= htmlspecialchars($p['owner_name'] ?: 'Admin') ?>
                                </td>
                            <?php endif; ?>

                            <!-- Aksi -->
                            <td class="py-2.5 px-4 text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <!-- Detail -->
                                    <a href="<?= APP_URL ?>/perusahaan/detail/<?= $p['id'] ?>" 
                                       class="p-1.5 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded transition-colors text-base"
                                       title="Lihat Detail">
                                        <ion-icon name="eye-outline"></ion-icon>
                                    </a>

                                    <!-- Edit Modal Trigger -->
                                    <button type="button" 
                                            onclick='openEditPerusahaanModal(<?= json_encode($p, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)'
                                            class="p-1.5 text-slate-400 hover:text-amber-600 hover:bg-amber-50 rounded transition-colors text-base"
                                            title="Edit (Popup)">
                                        <ion-icon name="create-outline"></ion-icon>
                                    </button>

                                    <!-- Delete SweetAlert Trigger -->
                                    <a href="<?= APP_URL ?>/perusahaan/delete/<?= $p['id'] ?>" 
                                       data-confirm-delete="<?= htmlspecialchars($p['nama']) ?>"
                                       data-type="Perusahaan"
                                       class="p-1.5 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded transition-colors text-base"
                                       title="Hapus">
                                        <ion-icon name="trash-outline"></ion-icon>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<!-- ========================================== -->
<!-- MODAL 1: TAMBAH PERUSAHAAN (POPUP)        -->
<!-- ========================================== -->
<div id="modalTambahPerusahaan" class="app-modal app-modal-auto-create fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 transition-all duration-200 opacity-0 pointer-events-none hidden">
    <div class="modal-card bg-white rounded-2xl shadow-2xl border border-slate-100 max-w-lg w-full transform scale-95 opacity-0 transition-all duration-200 overflow-hidden">
        <!-- Modal Header -->
        <div class="px-6 py-4 bg-slate-50/75 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-blue-600 text-white flex items-center justify-center text-lg shadow-sm">
                    <ion-icon name="business"></ion-icon>
                </div>
                <div>
                    <h3 class="font-bold text-slate-800 text-sm">Tambah Perusahaan Baru</h3>
                    <p class="text-[11px] text-slate-400">Daftarkan profil perusahaan / CV mitra kerja</p>
                </div>
            </div>
            <button type="button" data-modal-close="modalTambahPerusahaan" class="p-1.5 text-slate-400 hover:text-slate-700 rounded-lg transition-colors">
                <ion-icon name="close-outline" class="text-xl"></ion-icon>
            </button>
        </div>

        <!-- Modal Form -->
        <form method="POST" action="<?= APP_URL ?>/perusahaan/store" class="p-6 space-y-4">
            <?= csrfField() ?>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Perusahaan / CV / PT <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <ion-icon name="business-outline" class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-base"></ion-icon>
                        <input type="text" name="nama" required 
                               class="w-full pl-9 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-blue-500 text-xs focus:bg-white transition-all"
                               placeholder="Contoh: CV. Bangun Jaya Sentosa">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Direktur / Pimpinan</label>
                    <div class="relative">
                        <ion-icon name="person-outline" class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-base"></ion-icon>
                        <input type="text" name="nama_direktur" 
                               class="w-full pl-9 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-blue-500 text-xs focus:bg-white transition-all"
                               placeholder="Untuk tanda tangan BAST">
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Email Perusahaan</label>
                    <div class="relative">
                        <ion-icon name="mail-outline" class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-base"></ion-icon>
                        <input type="email" name="email" 
                               class="w-full pl-9 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-blue-500 text-xs focus:bg-white transition-all"
                               placeholder="kontak@perusahaan.com">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Nomor Telepon / WA</label>
                    <div class="relative">
                        <ion-icon name="call-outline" class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-base"></ion-icon>
                        <input type="text" name="telepon" 
                               class="w-full pl-9 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-blue-500 text-xs focus:bg-white transition-all"
                               placeholder="081234567890">
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Website Perusahaan</label>
                <div class="relative">
                    <ion-icon name="globe-outline" class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-base"></ion-icon>
                    <input type="url" name="website" 
                           class="w-full pl-9 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-blue-500 text-xs focus:bg-white transition-all"
                           placeholder="https://perusahaan.co.id">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Alamat Domisili Kantor</label>
                <textarea name="alamat" rows="2" 
                          class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-blue-500 text-xs focus:bg-white transition-all"
                          placeholder="Jalan, No, Kota, Provinsi..."></textarea>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Keterangan / Deskripsi Singkat</label>
                <textarea name="keterangan" rows="2" 
                          class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-blue-500 text-xs focus:bg-white transition-all"
                          placeholder="Bidang usaha, spesialisasi, atau catatan perusahaan..."></textarea>
            </div>

            <!-- Modal Footer -->
            <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                <button type="button" data-modal-close="modalTambahPerusahaan" 
                        class="px-3.5 py-2 rounded-xl border border-slate-200 text-slate-600 font-semibold hover:bg-slate-50 text-xs transition-colors">
                    Batal
                </button>
                <button type="submit" 
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl text-xs shadow-sm transition-all flex items-center gap-1.5 hover:scale-[1.01] active:scale-[0.98]">
                    <ion-icon name="checkmark-outline" class="text-sm"></ion-icon>
                    <span>Simpan Perusahaan</span>
                </button>
            </div>
        </form>
    </div>
</div>

<?php require __DIR__ . '/_modal_edit.php'; ?>

<script>
/**
 * Quick Filter Perusahaan Table
 */
function filterCompanyTable() {
    const input = document.getElementById("searchCompanyInput");
    const filter = input.value.toLowerCase();
    const table = document.getElementById("companyTable");
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
</script>
