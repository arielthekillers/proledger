<!-- ============================================================== -->
<!-- SUB-MODAL: TAMBAH ALOKASI DISTRIBUSI                           -->
<!-- ============================================================== -->
<div id="modalNativeDistribusi" class="app-modal fixed inset-0 z-[70] bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 transition-all duration-200 opacity-0 pointer-events-none hidden">
    <div class="modal-card bg-white rounded-2xl shadow-2xl border border-slate-100 max-w-2xl w-full transform scale-95 opacity-0 transition-all duration-200 overflow-hidden max-h-[92vh] flex flex-col">
        <!-- Header -->
        <div class="px-5 py-3.5 bg-slate-50 border-b border-slate-100 flex items-center justify-between shrink-0">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-indigo-600 text-white flex items-center justify-center text-lg shadow-xs">
                    <ion-icon name="navigate-outline"></ion-icon>
                </div>
                <div>
                    <h3 class="font-bold text-slate-800 text-sm">Tambah Alokasi Distribusi</h3>
                    <p class="text-[11px] text-slate-400">Pilih tujuan penerima dan tentukan jumlah barang</p>
                </div>
            </div>
            <button type="button" onclick="window.ProModal.close('modalNativeDistribusi')" class="p-1.5 text-slate-400 hover:text-slate-700 rounded-lg cursor-pointer">
                <ion-icon name="close-outline" class="text-xl"></ion-icon>
            </button>
        </div>

        <!-- Form Body -->
        <form id="formNativeDistribusi" onsubmit="submitNativeDistribusiForm(event)" class="p-5 overflow-y-auto space-y-4 flex-1">
            <input type="hidden" name="lokasi_id" id="tambah_dist_lokasi_id" value="" required>
            <input type="hidden" name="nama_tujuan" id="tambah_dist_nama_tujuan" value="">
            <input type="hidden" name="item_id" id="tambah_dist_item_id_hidden" value="">

            <!-- Dropdown Pilihan Item Komponen -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Pilih Komponen Barang <span class="text-rose-500">*</span></label>
                <select id="native_dist_item_select" onchange="onDistribusiItemSelectChange(this.value)" required
                        class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-indigo-500 text-xs font-semibold text-slate-800 focus:bg-white transition-all cursor-pointer">
                    <option value="">-- Pilih Komponen Barang --</option>
                    <?php foreach ($items as $it): ?>
                        <?php 
                        $rem = (float)$it['remaining_qty']; 
                        $tot = (float)$it['jumlah'];
                        $sat = $it['satuan'] ?: 'Unit';
                        ?>
                        <option value="<?= $it['id'] ?>" <?= ($rem <= 0) ? 'disabled class="text-slate-400 bg-slate-50"' : '' ?>>
                            <?= htmlspecialchars($it['komponen']) ?> (Total: <?= $tot ?> <?= htmlspecialchars($sat) ?><?= ($rem > 0) ? ', Sisa: ' . $rem . ' ' . htmlspecialchars($sat) : ' - Penuh' ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Card Ringkas Info Kuota Item & Input Jumlah -->
            <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-center p-3.5 rounded-xl border border-indigo-100 bg-indigo-50/50">
                <!-- Info Item & Sisa Kuota -->
                <div class="sm:col-span-7">
                    <div class="flex items-center gap-2 mb-1.5">
                        <span class="w-6 h-6 rounded-md bg-indigo-600 text-white flex items-center justify-center text-xs font-bold shadow-2xs">
                            <ion-icon name="cube-outline"></ion-icon>
                        </span>
                        <div class="min-w-0">
                            <span class="font-bold text-slate-800 text-xs block leading-tight truncate" id="dist_card_item_name">Silakan pilih item di atas</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-2.5 text-xs text-slate-600 bg-white/80 px-2.5 py-1.5 rounded-lg border border-indigo-100/80 w-fit">
                        <div>Total: <strong class="text-slate-800 font-mono" id="dist_card_total">0</strong></div>
                        <span class="text-slate-300">&bull;</span>
                        <div>Teralokasi: <strong class="text-blue-700 font-mono" id="dist_card_allocated">0</strong></div>
                        <span class="text-slate-300">&bull;</span>
                        <div>Sisa: <strong class="text-indigo-700 font-mono font-bold" id="dist_card_remaining">0</strong> <span class="dist_satuan_text">Unit</span></div>
                    </div>
                </div>

                <!-- Input Jumlah Alokasi -->
                <div class="sm:col-span-5 bg-white p-2.5 rounded-xl border border-slate-200/90 shadow-2xs">
                    <label class="block text-xs font-bold text-slate-700 mb-1">Jumlah Alokasi <span class="text-rose-500">*</span></label>
                    <div class="flex rounded-xl overflow-hidden border border-slate-200 focus-within:border-indigo-500 focus-within:ring-2 focus-within:ring-indigo-100 bg-white transition-all">
                        <input type="number" step="1" min="1" name="jumlah" id="tambah_dist_jumlah" value="1" required 
                               oninput="validateDistribusiQty()" disabled
                               class="w-full px-3 py-1.5 text-center text-sm font-mono font-bold text-slate-800 bg-transparent focus:outline-none [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none">
                        <div class="px-3 py-1.5 bg-slate-100 border-l border-slate-200 text-xs font-semibold text-slate-600 flex items-center justify-center shrink-0 min-w-14">
                            <span class="dist_satuan_text">Unit</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Error Banner jika kuota habis / jumlah melebihi kuota -->
            <div id="tambah_dist_qty_error" class="p-2.5 rounded-xl bg-rose-50 border border-rose-200 text-xs font-semibold text-rose-700 hidden flex items-center gap-1.5">
                <ion-icon name="alert-circle" class="text-base text-rose-600 shrink-0"></ion-icon>
                <span id="tambah_dist_qty_error_msg"></span>
            </div>

            <!-- TABEL PILIHAN TUJUAN DARI DATABASE MASTER -->
            <div class="space-y-2">
                <div class="flex items-center justify-between">
                    <div>
                        <label class="block text-xs font-bold text-slate-700">
                            Pilih Tujuan Penerima <span class="text-rose-500">*</span>
                        </label>
                        <span class="text-[11px] text-slate-400">
                            Wilayah: <strong class="text-slate-600"><?= htmlspecialchars($paket['kab_kota'] ?: 'Semua Wilayah') ?></strong>
                            &bull; <span id="tujuan_count_label"><?= count($lokasiList) ?> tujuan</span>
                        </span>
                    </div>
                    <button type="button" onclick="openQuickTambahTujuanModal()" 
                            class="px-2.5 py-1 rounded-lg bg-indigo-50 border border-indigo-200 text-indigo-700 hover:bg-indigo-100 text-xs font-bold flex items-center gap-1 transition-colors cursor-pointer">
                        <ion-icon name="add-circle" class="text-sm"></ion-icon>
                        <span>+ Tambah Tujuan</span>
                    </button>
                </div>

                <!-- Input Pencarian Cepat -->
                <div class="relative">
                    <input type="text" id="search_tujuan_table" 
                           oninput="filterTujuanTable(this.value)"
                           placeholder="Ketik nama tujuan atau kota untuk mencari..." 
                           class="w-full pl-8 pr-3 py-1.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:border-indigo-500 focus:bg-white transition-all">
                    <ion-icon name="search-outline" class="absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></ion-icon>
                </div>

                <!-- Scrollable Table -->
                <div class="border border-slate-200 rounded-xl overflow-hidden max-h-48 overflow-y-auto bg-white shadow-2xs">
                    <table class="w-full text-left text-xs" id="tableListTujuan">
                        <thead class="bg-slate-100 text-[10px] uppercase font-bold text-slate-600 sticky top-0 z-10 border-b border-slate-200">
                            <tr>
                                <th class="py-2.5 px-3 w-12 text-center">PILIH</th>
                                <th class="py-2.5 px-3">NAMA TUJUAN / INSTANSI</th>
                                <th class="py-2.5 px-3 w-40">KOTA / KABUPATEN</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100" id="tbodyListTujuan">
                            <?php if (empty($lokasiList)): ?>
                                <tr id="emptyTujuanRow">
                                    <td colspan="3" class="py-6 text-center text-slate-400 text-xs">
                                        Belum ada data tujuan di wilayah <?= htmlspecialchars($paket['kab_kota'] ?: '-') ?>.<br>
                                        Klik <strong class="text-indigo-600 cursor-pointer" onclick="openQuickTambahTujuanModal()">+ Tambah Tujuan</strong> untuk menambahkan.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($lokasiList as $lok): ?>
                                    <tr class="tujuan-row hover:bg-indigo-50/60 cursor-pointer transition-colors" 
                                        data-id="<?= $lok['id'] ?>"
                                        data-search="<?= strtolower(htmlspecialchars($lok['nama_lokasi'] . ' ' . ($lok['kab_kota'] ?? ''))) ?>"
                                        onclick="selectTujuanRow(this, <?= $lok['id'] ?>, '<?= htmlspecialchars(addslashes($lok['nama_lokasi'])) ?>', '<?= htmlspecialchars(addslashes($lok['kab_kota'] ?? '')) ?>')">
                                        <td class="py-2.5 px-3 text-center">
                                            <input type="radio" name="selected_lokasi_radio" value="<?= $lok['id'] ?>" 
                                                   class="tujuan-radio w-4 h-4 text-indigo-600 border-slate-300 focus:ring-indigo-500 cursor-pointer"
                                                   onclick="event.stopPropagation(); selectTujuanRow(this.closest('tr'), <?= $lok['id'] ?>, '<?= htmlspecialchars(addslashes($lok['nama_lokasi'])) ?>', '<?= htmlspecialchars(addslashes($lok['kab_kota'] ?? '')) ?>')">
                                        </td>
                                        <td class="py-2.5 px-3 font-semibold text-slate-800">
                                            <?= htmlspecialchars($lok['nama_lokasi']) ?>
                                        </td>
                                        <td class="py-2.5 px-3 text-slate-600 whitespace-nowrap">
                                            <span class="px-2 py-0.5 rounded bg-slate-100 text-slate-700 text-[11px] font-medium">
                                                <?= htmlspecialchars($lok['kab_kota'] ?: '-') ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Indikator Tujuan Terpilih -->
                <div id="tujuan_selected_indicator" class="p-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs flex items-center justify-between transition-all">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-slate-300" id="indicator_dot"></span>
                        <span class="text-slate-500" id="indicator_text">Belum ada tujuan yang dipilih. Silakan klik salah satu baris di tabel.</span>
                    </div>
                </div>
            </div>

            <!-- Footer Action Buttons -->
            <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2 shrink-0">
                <button type="button" onclick="window.ProModal.close('modalNativeDistribusi')"
                        class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 font-semibold hover:bg-slate-50 text-xs transition-colors cursor-pointer">
                    Batal
                </button>
                <button type="submit" id="btnSubmitDistribusi"
                        class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl text-xs shadow-xs transition-all flex items-center gap-1.5 opacity-40 cursor-not-allowed pointer-events-none cursor-pointer" disabled>
                    <ion-icon name="checkmark-outline" class="text-sm"></ion-icon>
                    <span>Simpan Alokasi</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ============================================================== -->
<!-- SUB-MODAL: QUICK TAMBAH TUJUAN BARU                            -->
<!-- ============================================================== -->
<div id="modalQuickTambahTujuan" class="app-modal fixed inset-0 z-[80] bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 transition-all duration-200 opacity-0 pointer-events-none hidden">
    <div class="modal-card bg-white rounded-2xl shadow-2xl border border-slate-100 max-w-md w-full transform scale-95 opacity-0 transition-all duration-200 overflow-hidden">
        <div class="px-5 py-3.5 bg-slate-50 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-bold text-slate-800 text-xs flex items-center gap-2">
                <ion-icon name="location" class="text-indigo-600 text-base"></ion-icon>
                <span>Tambah Tujuan Baru Cepat</span>
            </h3>
            <button type="button" onclick="closeQuickTambahTujuanModal()" class="p-1 text-slate-400 hover:text-slate-700 rounded-lg cursor-pointer">
                <ion-icon name="close-outline" class="text-lg"></ion-icon>
            </button>
        </div>
        <form id="formQuickTujuan" onsubmit="handleQuickTambahTujuan(event)" class="p-5 space-y-3.5">
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">
                    Nama Tujuan / Instansi <span class="text-rose-500">*</span>
                </label>
                <input type="text" id="quick_nama_lokasi" required 
                       class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-indigo-500 text-xs focus:bg-white transition-all font-medium"
                       placeholder="Contoh: SMAN 2 Bontang / Kantor Kelurahan...">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">
                    Kota / Kabupaten <span class="text-rose-500">*</span>
                </label>
                <select id="quick_kabupaten" required
                        class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-indigo-500 text-xs focus:bg-white transition-all cursor-pointer font-medium">
                    <option value="">-- Pilih Kota / Kabupaten --</option>
                    <?php if (!empty($kabupatenList)): ?>
                        <?php foreach ($kabupatenList as $kab): ?>
                            <option value="<?= htmlspecialchars($kab['nama']) ?>" <?= (($paket['kab_kota'] ?? '') === $kab['nama']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($kab['nama']) ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
                <p class="text-[10px] text-slate-400 mt-1">Terhubung langsung dengan master data kota/kabupaten.</p>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">
                        Nama Pejabat / Penerima
                    </label>
                    <input type="text" id="quick_nama_pic" 
                           class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-indigo-500 text-xs focus:bg-white transition-all font-medium"
                           placeholder="Nama Kepala Sekolah / Pejabat...">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">
                        Jabatan
                    </label>
                    <input type="text" id="quick_jabatan_pic" 
                           class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-indigo-500 text-xs focus:bg-white transition-all font-medium"
                           placeholder="Contoh: Kepala Sekolah / PPK">
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">
                    Alamat Lengkap
                </label>
                <textarea id="quick_alamat_lokasi" rows="2" 
                          class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-indigo-500 text-xs focus:bg-white transition-all resize-none"
                          placeholder="Alamat lengkap lokasi penerima (untuk BAST)..."></textarea>
            </div>
            <div class="pt-2 border-t border-slate-100 flex justify-end gap-2">
                <button type="button" onclick="closeQuickTambahTujuanModal()" class="px-3.5 py-1.5 rounded-lg border border-slate-200 text-slate-600 font-semibold hover:bg-slate-50 text-xs transition-colors cursor-pointer">
                    Batal
                </button>
                <button type="submit" id="btnSubmitQuickTujuan" class="px-4 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg text-xs shadow-xs transition-colors flex items-center gap-1.5 cursor-pointer">
                    <ion-icon name="checkmark-outline" class="text-sm"></ion-icon>
                    <span>Simpan & Pilih Tujuan</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Teleport sub-modals ke document.body agar tidak terpotong oleh transform/overflow parent modal
(function() {
    ['modalNativeDistribusi', 'modalQuickTambahTujuan'].forEach(function(id) {
        document.querySelectorAll('body > #' + id).forEach(function(oldEl) { oldEl.remove(); });
        var el = document.getElementById(id);
        if (el) document.body.appendChild(el);
    });
})();

window.paketDistribusiItems = <?= json_encode(array_values($items), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
window.currentDistribusiItem = null;

window.doOpenNativeTambahDistribusiModal = function(preselectedItemId) {
    window.resetTujuanTableSelection();
    const sel = document.getElementById('native_dist_item_select');
    if (sel) {
        sel.value = '';
        if (preselectedItemId) {
            const targetItem = (window.paketDistribusiItems || []).find(it => String(it.id) === String(preselectedItemId));
            if (targetItem && (parseFloat(targetItem.remaining_qty) || 0) > 0) {
                sel.value = targetItem.id;
                window.onDistribusiItemSelectChange(targetItem.id);
            } else {
                window.onDistribusiItemSelectChange(preselectedItemId);
            }
        } else {
            const firstAvailable = (window.paketDistribusiItems || []).find(it => (parseFloat(it.remaining_qty) || 0) > 0);
            if (firstAvailable) {
                sel.value = firstAvailable.id;
                window.onDistribusiItemSelectChange(firstAvailable.id);
            } else {
                window.onDistribusiItemSelectChange('');
            }
        }
    }
    window.ProModal.open('modalNativeDistribusi');
};
window.openNativeTambahDistribusiModal = window.doOpenNativeTambahDistribusiModal;

window.onDistribusiItemSelectChange = function(itemId) {
    document.getElementById('tambah_dist_item_id_hidden').value = itemId || '';
    if (!itemId) {
        window.updateDistribusiQuotaCard(null);
        return;
    }
    const item = (window.paketDistribusiItems || []).find(it => it.id == itemId);
    window.updateDistribusiQuotaCard(item || null);
};

window.updateDistribusiQuotaCard = function(item) {
    window.currentDistribusiItem = item;
    const inputJumlah = document.getElementById('tambah_dist_jumlah');
    const btnSubmit = document.getElementById('btnSubmitDistribusi');
    const errBox = document.getElementById('tambah_dist_qty_error');
    const errMsg = document.getElementById('tambah_dist_qty_error_msg');

    if (!item) {
        document.getElementById('dist_card_item_name').textContent = 'Silakan pilih item di atas';
        document.getElementById('dist_card_total').textContent = '0';
        document.getElementById('dist_card_allocated').textContent = '0';
        document.getElementById('dist_card_remaining').textContent = '0';
        document.querySelectorAll('.dist_satuan_text').forEach(el => el.textContent = 'Unit');
        inputJumlah.disabled = true;
        inputJumlah.value = 1;
        btnSubmit.disabled = true;
        btnSubmit.classList.add('opacity-40', 'cursor-not-allowed', 'pointer-events-none');
        errBox.classList.add('hidden');
        return;
    }

    const totalQty = parseFloat(item.jumlah) || 0;
    const allocatedQty = parseFloat(item.allocated_qty) || 0;
    const remainingQty = Math.max(0, totalQty - allocatedQty);
    const satuan = item.satuan || 'Unit';

    document.getElementById('dist_card_item_name').textContent = item.komponen || 'Item Barang';
    document.getElementById('dist_card_total').textContent = totalQty;
    document.getElementById('dist_card_allocated').textContent = allocatedQty;
    document.getElementById('dist_card_remaining').textContent = remainingQty;

    document.querySelectorAll('.dist_satuan_text').forEach(el => {
        el.textContent = satuan;
    });

    if (remainingQty > 0) {
        inputJumlah.disabled = false;
        inputJumlah.step = "1";
        inputJumlah.min = "1";
        inputJumlah.max = remainingQty;
        inputJumlah.value = Math.floor(remainingQty);
        errBox.classList.add('hidden');
    } else {
        inputJumlah.value = 0;
        inputJumlah.disabled = true;
        inputJumlah.max = 0;
        btnSubmit.disabled = true;
        btnSubmit.classList.add('opacity-40', 'cursor-not-allowed', 'pointer-events-none');
        errMsg.textContent = 'Seluruh kuota item ini (' + totalQty + ' ' + satuan + ') sudah dialokasikan ke penerima. Tombol simpan dinonaktifkan.';
        errBox.classList.remove('hidden');
    }

    window.validateDistribusiQty();
};

window.validateDistribusiQty = function() {
    if (!window.currentDistribusiItem) return;

    const inputJumlah = document.getElementById('tambah_dist_jumlah');
    const btnSubmit = document.getElementById('btnSubmitDistribusi');
    const errBox = document.getElementById('tambah_dist_qty_error');
    const errMsg = document.getElementById('tambah_dist_qty_error_msg');
    const lokasiId = document.getElementById('tambah_dist_lokasi_id').value;

    const totalQty = parseFloat(window.currentDistribusiItem.jumlah) || 0;
    const allocatedQty = parseFloat(window.currentDistribusiItem.allocated_qty) || 0;
    const remainingQty = Math.max(0, totalQty - allocatedQty);
    const satuan = window.currentDistribusiItem.satuan || 'Unit';
    const val = parseFloat(inputJumlah.value);

    if (remainingQty <= 0) {
        btnSubmit.disabled = true;
        btnSubmit.classList.add('opacity-40', 'cursor-not-allowed', 'pointer-events-none');
        errMsg.textContent = 'Kuota item ini sudah habis (0 ' + satuan + ' tersisa).';
        errBox.classList.remove('hidden');
        return;
    }

    if (isNaN(val) || val <= 0) {
        btnSubmit.disabled = true;
        btnSubmit.classList.add('opacity-40', 'cursor-not-allowed', 'pointer-events-none');
        errMsg.textContent = 'Jumlah alokasi harus berupa bilangan bulat minimal 1.';
        errBox.classList.remove('hidden');
        return;
    }

    if (val > remainingQty) {
        btnSubmit.disabled = true;
        btnSubmit.classList.add('opacity-40', 'cursor-not-allowed', 'pointer-events-none');
        errMsg.textContent = 'Jumlah alokasi (' + val + ' ' + satuan + ') melebihi sisa kuota yang tersedia (' + remainingQty + ' ' + satuan + ')!';
        errBox.classList.remove('hidden');
        return;
    }

    errBox.classList.add('hidden');

    if (!lokasiId) {
        btnSubmit.disabled = true;
        btnSubmit.classList.add('opacity-40', 'cursor-not-allowed', 'pointer-events-none');
        return;
    }

    btnSubmit.disabled = false;
    btnSubmit.classList.remove('opacity-40', 'cursor-not-allowed', 'pointer-events-none');
};

window.selectTujuanRow = function(tr, id, nama, kota) {
    document.querySelectorAll('#tbodyListTujuan tr').forEach(r => {
        r.classList.remove('bg-indigo-50/90');
        const rad = r.querySelector('.tujuan-radio');
        if (rad) rad.checked = false;
    });

    if (tr) {
        tr.classList.add('bg-indigo-50/90');
        const radio = tr.querySelector('.tujuan-radio');
        if (radio) radio.checked = true;
    }

    document.getElementById('tambah_dist_lokasi_id').value = id;
    document.getElementById('tambah_dist_nama_tujuan').value = nama;

    const ind = document.getElementById('tujuan_selected_indicator');
    const dot = document.getElementById('indicator_dot');
    const text = document.getElementById('indicator_text');

    if (ind) ind.className = 'p-2.5 rounded-xl bg-emerald-50 border border-emerald-200 text-xs flex items-center justify-between transition-all';
    if (dot) dot.className = 'w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse';
    if (text) text.innerHTML = '<strong class="text-emerald-800">' + escapeHtml(nama) + '</strong> <span class="text-emerald-600">(' + escapeHtml(kota || '-') + ') terpilih</span>';

    window.validateDistribusiQty();
};

window.resetTujuanTableSelection = function() {
    document.getElementById('tambah_dist_lokasi_id').value = '';
    document.getElementById('tambah_dist_nama_tujuan').value = '';

    const ind = document.getElementById('tujuan_selected_indicator');
    const dot = document.getElementById('indicator_dot');
    const text = document.getElementById('indicator_text');

    if (ind) ind.className = 'p-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs flex items-center justify-between transition-all';
    if (dot) dot.className = 'w-2.5 h-2.5 rounded-full bg-slate-300';
    if (text) text.textContent = 'Belum ada tujuan yang dipilih. Silakan klik salah satu baris di tabel.';

    document.querySelectorAll('#tbodyListTujuan tr').forEach(r => {
        r.classList.remove('bg-indigo-50/90');
        const rad = r.querySelector('.tujuan-radio');
        if (rad) rad.checked = false;
    });

    const searchInput = document.getElementById('search_tujuan_table');
    if (searchInput) {
        searchInput.value = '';
        window.filterTujuanTable('');
    }
};

window.filterTujuanTable = function(query) {
    const q = (query || '').toLowerCase().trim();
    const rows = document.querySelectorAll('#tbodyListTujuan .tujuan-row');
    rows.forEach(r => {
        const search = r.getAttribute('data-search') || '';
        if (!q || search.includes(q)) {
            r.classList.remove('hidden');
        } else {
            r.classList.add('hidden');
        }
    });
};

window.openQuickTambahTujuanModal = function() {
    const inputNama = document.getElementById('quick_nama_lokasi');
    if (inputNama) inputNama.value = '';
    const selKab = document.getElementById('quick_kabupaten');
    const defaultKab = <?= json_encode($paket['kab_kota'] ?? '') ?>;
    if (selKab && defaultKab) {
        selKab.value = defaultKab;
    }
    window.ProModal.open('modalQuickTambahTujuan');
    setTimeout(() => {
        if (inputNama) inputNama.focus();
    }, 150);
};

window.closeQuickTambahTujuanModal = function() {
    window.ProModal.close('modalQuickTambahTujuan');
};

window.handleQuickTambahTujuan = function(e) {
    e.preventDefault();
    const btn = document.getElementById('btnSubmitQuickTujuan');
    const nama = document.getElementById('quick_nama_lokasi').value.trim();
    const kab = document.getElementById('quick_kabupaten').value.trim();
    const namaPic = (document.getElementById('quick_nama_pic')?.value || '').trim();
    const jabatanPic = (document.getElementById('quick_jabatan_pic')?.value || '').trim();
    const alamatLokasi = (document.getElementById('quick_alamat_lokasi')?.value || '').trim();
    if (!nama) return;

    btn.disabled = true;
    btn.textContent = 'Menyimpan...';

    const formData = new FormData();
    formData.append('nama_lokasi', nama);
    formData.append('kab_kota', kab);
    if (namaPic) formData.append('nama_pic', namaPic);
    if (jabatanPic) formData.append('jabatan_pic', jabatanPic);
    if (alamatLokasi) formData.append('alamat_lengkap', alamatLokasi);

    fetch('<?= APP_URL ?>/paket/storeLokasiQuick/<?= $paket['kode_paket'] ?: $paket['id'] ?>', {
        method: 'POST',
        body: formData,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(res => res.json())
    .then(data => {
        btn.disabled = false;
        btn.innerHTML = '<ion-icon name="checkmark-outline" class="text-sm"></ion-icon> <span>Simpan & Pilih Tujuan</span>';

        if (data.success && (data.lokasi || data.data)) {
            const lok = data.lokasi || data.data;
            const tbody = document.getElementById('tbodyListTujuan');
            if (tbody) {
                const emptyRow = document.getElementById('emptyTujuanRow');
                if (emptyRow) emptyRow.remove();

                const tr = document.createElement('tr');
                tr.className = 'tujuan-row hover:bg-indigo-50/60 cursor-pointer transition-colors';
                tr.setAttribute('data-id', lok.id);
                tr.setAttribute('data-search', ((lok.nama_lokasi || '') + ' ' + (lok.kab_kota || '')).toLowerCase());
                tr.onclick = function() { window.selectTujuanRow(this, lok.id, lok.nama_lokasi, lok.kab_kota); };
                tr.innerHTML = `
                    <td class="py-2.5 px-3 text-center">
                        <input type="radio" name="selected_lokasi_radio" value="${lok.id}" 
                               class="tujuan-radio w-4 h-4 text-indigo-600 border-slate-300 focus:ring-indigo-500 cursor-pointer">
                    </td>
                    <td class="py-2.5 px-3 font-semibold text-slate-800">
                        ${escapeHtml(lok.nama_lokasi)}
                    </td>
                    <td class="py-2.5 px-3 text-slate-600 whitespace-nowrap">
                        <span class="px-2 py-0.5 rounded bg-slate-100 text-slate-700 text-[11px] font-medium">
                            ${escapeHtml(lok.kab_kota || '-')}
                        </span>
                    </td>
                `;
                tbody.insertBefore(tr, tbody.firstChild);
                window.selectTujuanRow(tr, lok.id, lok.nama_lokasi, lok.kab_kota);

                const countLabel = document.getElementById('tujuan_count_label');
                if (countLabel) {
                    const totalRows = tbody.querySelectorAll('.tujuan-row').length;
                    countLabel.textContent = totalRows + ' tujuan';
                }
            }

            window.closeQuickTambahTujuanModal();
            if (window.ProToast) window.ProToast.success(data.message || 'Tujuan berhasil ditambahkan!');
            document.getElementById('formQuickTujuan').reset();
        } else {
            if (window.ProToast) window.ProToast.error(data.message || 'Gagal menambahkan tujuan.');
        }
    })
    .catch(() => {
        btn.disabled = false;
        btn.innerHTML = '<ion-icon name="checkmark-outline" class="text-sm"></ion-icon> <span>Simpan & Pilih Tujuan</span>';
        if (window.ProToast) window.ProToast.error('Koneksi terputus saat menyimpan tujuan.');
    });
};

window.submitNativeDistribusiForm = function(e) {
    e.preventDefault();
    const btn = document.getElementById('btnSubmitDistribusi');
    const form = document.getElementById('formNativeDistribusi');
    const formData = new FormData(form);

    if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<span>Menyimpan Alokasi...</span>';
    }

    fetch('<?= APP_URL ?>/paket/storeDistribusi/<?= $paket['kode_paket'] ?: $paket['id'] ?>', {
        method: 'POST',
        body: formData,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(data => {
        if (btn) {
            btn.disabled = false;
            btn.innerHTML = '<ion-icon name="checkmark-outline" class="text-sm"></ion-icon> <span>Simpan Alokasi</span>';
        }
        if (data.success) {
            window.ProModal.close('modalNativeDistribusi');
            if (window.ProToast) window.ProToast.success(data.message || 'Alokasi distribusi berhasil disimpan!');
            refreshCurrentNativeModal();
        } else {
            if (window.ProToast) window.ProToast.error(data.message || 'Gagal menyimpan alokasi distribusi.');
        }
    })
    .catch(() => {
        if (btn) {
            btn.disabled = false;
            btn.innerHTML = '<ion-icon name="checkmark-outline" class="text-sm"></ion-icon> <span>Simpan Alokasi</span>';
        }
        window.ProModal.close('modalNativeDistribusi');
        refreshCurrentNativeModal();
    });
};

function escapeHtml(text) {
    if (!text) return '';
    return String(text).replace(/[&<>"']/g, function(m) {
        return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[m];
    });
}
window.escapeHtml = escapeHtml;
</script>
