<?php $pemodal_ids = []; ?>

<!-- Header & Back -->
<div class="mb-6 flex items-center justify-between">
    <div class="flex items-center gap-2.5">
        <h1 class="text-xl font-bold text-slate-800">Edit Paket Kegiatan</h1>
        <span class="px-2.5 py-0.5 rounded-lg bg-blue-100 text-blue-800 font-mono font-bold text-xs tracking-wider">
            #<?= htmlspecialchars($paket['kode_paket'] ?: $paket['id']) ?>
        </span>
    </div>

    <a href="<?= APP_URL ?>/paket/detail/<?= $paket['kode_paket'] ?: $paket['id'] ?>" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-slate-200 rounded-xl text-slate-600 hover:bg-slate-50 hover:text-blue-600 transition-colors shadow-sm text-xs font-semibold">
        <ion-icon name="arrow-back-outline"></ion-icon>
        <span>Kembali ke Detail</span>
    </a>
</div>

<form method="POST" action="<?= APP_URL ?>/paket/update/<?= $paket['kode_paket'] ?: $paket['id'] ?>" id="paketForm">
    <?= csrfField() ?>
    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Form (2 Columns) -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- Basic Info Card -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                <!-- Nama Paket -->
                <div class="mb-6">
                    <label class="block text-sm font-bold text-slate-700 mb-2">Nama Paket Kegiatan <span class="text-red-500">*</span></label>
                    <input type="text" name="nama_paket" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-colors" 
                           required placeholder="Contoh: Pembangunan Jalan Raya Km 10-15" value="<?= htmlspecialchars($paket['nama_paket']) ?>">
                </div>
                
                <!-- Perusahaan, Group, & Status Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Perusahaan</label>
                        <div class="flex items-center gap-3 px-4 py-3 bg-slate-100 border border-slate-200 rounded-xl text-sm">
                            <ion-icon name="business" class="text-blue-600 text-xl shrink-0"></ion-icon>
                            <span class="font-bold text-slate-800"><?= htmlspecialchars($paket['perusahaan'] ?? $paket['perusahaan_nama'] ?? '-') ?></span>
                            <span class="text-xs text-slate-400 ml-auto font-medium">Terkunci</span>
                        </div>
                        <input type="hidden" name="perusahaan_id" id="paket_perusahaan_id" value="<?= $paket['perusahaan_id'] ?? '' ?>">
                    </div>

                    <!-- Group Paket (Opsional) -->
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label class="block text-sm font-bold text-slate-700">Group Paket (Opsional)</label>
                            <button type="button" data-modal-open="modalQuickTambahGroup" class="text-xs text-blue-600 hover:text-blue-700 font-semibold flex items-center gap-1 hover:underline">
                                <ion-icon name="add-circle-outline"></ion-icon>
                                <span>Group Baru</span>
                            </button>
                        </div>
                        <div class="relative">
                            <ion-icon name="folder-outline" class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-lg"></ion-icon>
                            <select name="group_id" id="select_group_id" class="w-full pl-11 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-colors appearance-none text-slate-700">
                                <option value="">-- Tanpa Group (Paket Independen) --</option>
                                <?php if(isset($groups)): foreach($groups as $grp): ?>
                                    <option value="<?= $grp['id'] ?>" <?= ($paket['group_id'] ?? '') == $grp['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($grp['nama_group']) ?>
                                    </option>
                                <?php endforeach; endif; ?>
                            </select>
                            <ion-icon name="chevron-down-outline" class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"></ion-icon>
                        </div>
                        <p class="text-[11px] text-slate-400 mt-1">Kelompokkan paket ke program payung (misal: Dinas Pendidikan 2025)</p>
                    </div>
                </div>

                <!-- Penanggung Jawab & Progres Barang Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Penanggung Jawab (PJ)</label>
                        <input type="text" list="list_pj" name="penanggung_jawab" value="<?= htmlspecialchars($paket['penanggung_jawab'] ?? '') ?>"
                               class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-colors uppercase tracking-wide font-medium" 
                               placeholder="Contoh: RAHMAN, RIFKI, DARMAN, dll.">
                        <datalist id="list_pj">
                            <option value="RAHMAN">
                            <option value="RIFKI">
                            <option value="DARMAN">
                            <option value="ASHARI">
                            <option value="SULIS">
                            <option value="RIZAL">
                        </datalist>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Progres Barang</label>
                        <div class="relative">
                            <select name="progres_barang" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-colors appearance-none font-bold">
                                <option value="Belum" <?= ($paket['progres_barang'] ?? '') === 'Belum' ? 'selected' : '' ?>>BELUM</option>
                                <option value="Belanja" <?= ($paket['progres_barang'] ?? '') === 'Belanja' ? 'selected' : '' ?>>BELANJA</option>
                                <option value="Distribusi" <?= ($paket['progres_barang'] ?? '') === 'Distribusi' ? 'selected' : '' ?>>DISTRIBUSI</option>
                                <option value="Selesai" <?= ($paket['progres_barang'] ?? '') === 'Selesai' ? 'selected' : '' ?>>SELESAI</option>
                            </select>
                            <ion-icon name="chevron-down-outline" class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"></ion-icon>
                        </div>
                    </div>
                </div>

                <!-- Status Kas -->
                <div class="mb-2">
                    <label class="block text-sm font-bold text-slate-700 mb-2">Status Kas / Pencairan</label>
                    <div class="relative">
                        <ion-icon name="flag-outline" class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-lg"></ion-icon>
                        <select name="status" class="w-full pl-11 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-colors appearance-none font-bold">
                            <option value="Proses" <?= in_array($paket['status'] ?? '', ['Proses', 'Kontrak']) ? 'selected' : '' ?>>PROSES</option>
                            <option value="Cair" <?= in_array($paket['status'] ?? '', ['Cair', 'Selesai']) ? 'selected' : '' ?>>CAIR</option>
                            <option value="Belum" <?= ($paket['status'] ?? '') === 'Belum' ? 'selected' : '' ?>>BELUM</option>
                        </select>
                        <ion-icon name="chevron-down-outline" class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"></ion-icon>
                    </div>
                </div>
            </div>
            
            <!-- Financials Card -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                <h3 class="text-lg font-bold text-slate-800 mb-6 flex items-center gap-2">
                    <ion-icon name="cash-outline" class="text-blue-500"></ion-icon>
                    <span>Rincian Nilai Kontrak & Potongan</span>
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Nilai Kontrak / Pagu Awal <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 font-semibold text-xs">Rp</span>
                            <input type="text" name="pagu" id="pagu" class="rupiah-input w-full pl-9 pr-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-colors font-mono font-medium text-slate-800 text-sm" 
                                   required placeholder="0" value="<?= number_format($paket['pagu'], 0, ',', '.') ?>">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Pagu Nego <span class="text-emerald-600 font-normal">(Acuan Akhir jika ada)</span></label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 font-semibold text-xs">Rp</span>
                            <input type="text" name="pagu_nego" id="pagu_nego" class="rupiah-input w-full pl-9 pr-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-colors font-mono font-medium text-emerald-700 text-sm" 
                                   placeholder="Opsional" value="<?= (!empty($paket['pagu_nego']) && (float)$paket['pagu_nego'] > 0) ? number_format($paket['pagu_nego'], 0, ',', '.') : '' ?>">
                        </div>
                    </div>
                </div>

                <!-- Skema Fleksibel Potongan Pasal & Admin -->
                <div class="p-4 bg-slate-50/80 rounded-xl border border-slate-200/80 mb-6 space-y-3">
                    <div class="flex items-center justify-between">
                        <label class="text-xs font-bold text-slate-700 flex items-center gap-1.5">
                            <ion-icon name="options-outline" class="text-blue-600 text-base"></ion-icon>
                            <span>Skema Potongan Pasal & Admin</span>
                        </label>
                        <span class="text-xs text-slate-400">Pilih skema cepat atau atur persentase manual</span>
                    </div>

                    <div class="flex items-center gap-2 flex-wrap">
                        <button type="button" onclick="setEditPresetStandalone(13, 2, this)" class="edit-preset-btn px-3 py-1.5 text-xs font-semibold rounded-lg border bg-blue-600 text-white border-blue-600 shadow-2xs transition-all">
                            13% + 2% Admin (Total 15%)
                        </button>
                        <button type="button" onclick="setEditPresetStandalone(20, 0, this)" class="edit-preset-btn px-3 py-1.5 text-xs font-semibold rounded-lg border bg-white text-slate-600 border-slate-200 hover:border-slate-300 transition-all">
                            20% (Tanpa Admin)
                        </button>
                        <button type="button" onclick="setEditPresetStandalone(15, 0, this)" class="edit-preset-btn px-3 py-1.5 text-xs font-semibold rounded-lg border bg-white text-slate-600 border-slate-200 hover:border-slate-300 transition-all">
                            15% (Tanpa Admin)
                        </button>
                    </div>

                    <div class="grid grid-cols-2 gap-4 pt-1">
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1">Potongan Pasal (%)</label>
                            <div class="relative">
                                <input type="number" step="0.1" min="0" max="100" name="persen_pasal" id="persen_pasal" value="<?= (float)($paket['persen_pasal'] ?? 13) ?>" oninput="updateEditCalculations()"
                                       class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-sm font-bold text-slate-800 focus:outline-none focus:border-blue-500 transition-all">
                                <span class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs font-bold">%</span>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1">Potongan Admin (%)</label>
                            <div class="relative">
                                <input type="number" step="0.1" min="0" max="100" name="persen_admin" id="persen_admin" value="<?= (float)($paket['persen_admin'] ?? 2) ?>" oninput="updateEditCalculations()"
                                       class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-sm font-bold text-slate-800 focus:outline-none focus:border-blue-500 transition-all">
                                <span class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs font-bold">%</span>
                            </div>
                        </div>
                    </div>

                    <!-- Pilihan Dasar Potongan: Kontrak vs DPP -->
                    <div class="p-2.5 rounded-xl border border-slate-200 bg-white/70">
                        <label class="flex items-start gap-2 cursor-pointer select-none">
                            <input type="checkbox" name="tipe_potongan" id="tipe_potongan" value="kontrak" <?= (($paket['tipe_potongan'] ?? 'dpp') === 'kontrak') ? 'checked' : '' ?> onchange="updateEditCalculations()" class="w-4 h-4 rounded text-blue-600 focus:ring-0 mt-0.5 cursor-pointer">
                            <div>
                                <span class="text-xs font-bold text-slate-800 block">Potongan Diambil dari Nilai Kontrak</span>
                                <span class="text-[10px] text-slate-500 block leading-tight">Tidak dicentang: potongan dihitung dari DPP (setelah kena pajak). Centang ini jika potongan dihitung dari Nilai Kontrak (sebelum pajak).</span>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Live Calculation Preview -->
                <div class="space-y-3 bg-slate-50 rounded-xl p-4 border border-slate-100">
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 text-xs">
                        <div class="p-2 bg-white rounded-lg border border-slate-100">
                            <span class="text-[10px] text-slate-400 font-bold block uppercase">DPP (-11% PPN)</span>
                            <span class="font-bold font-mono text-slate-800 text-xs" id="previewDPP">Rp 0</span>
                        </div>
                        <div class="p-2 bg-white rounded-lg border border-slate-100">
                            <span class="text-[10px] text-slate-400 font-bold block uppercase">Pasal (<span id="lbl_pasal">13</span>%)</span>
                            <span class="font-bold font-mono text-slate-800 text-xs" id="previewPasal13">Rp 0</span>
                        </div>
                        <div class="p-2 bg-white rounded-lg border border-slate-100">
                            <span class="text-[10px] text-slate-400 font-bold block uppercase">Admin (<span id="lbl_admin">2</span>%)</span>
                            <span class="font-bold font-mono text-slate-800 text-xs" id="previewAdmin2">Rp 0</span>
                        </div>
                        <div class="p-2 bg-amber-50 rounded-lg border border-amber-200">
                            <span class="text-[10px] text-amber-800 font-bold block uppercase">Total Pasal (<span id="lbl_total_pasal">15</span>%)</span>
                            <span class="font-bold font-mono text-amber-800 text-xs" id="previewTotalPasal">Rp 0</span>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        
        <!-- Sidebar Selection (1 Column) -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Submit Button -->
            <button type="submit" class="w-full py-4 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-lg shadow-blue-600/30 transition-all hover:scale-[1.02] active:scale-[0.98] flex items-center justify-center gap-2">
                <ion-icon name="save-outline" class="text-xl"></ion-icon>
                <span>Update Paket Kegiatan</span>
            </button>
        </div>
            <button type="submit" class="w-full py-4 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-lg shadow-blue-600/30 transition-all hover:scale-[1.02] active:scale-[0.98] flex items-center justify-center gap-2">
                <ion-icon name="save-outline" class="text-xl"></ion-icon>
                <span>Update Paket Kegiatan</span>
            </button>
            
            

        </div>
    </div>
</form>

<script>
function setEditPresetStandalone(pasal, admin, btn) {
    document.getElementById('persen_pasal').value = pasal;
    document.getElementById('persen_admin').value = admin;
    document.querySelectorAll('.edit-preset-btn').forEach(b => {
        b.className = 'edit-preset-btn px-3 py-1.5 text-xs font-semibold rounded-lg border bg-white text-slate-600 border-slate-200 hover:border-slate-300 transition-all';
    });
    if (btn) {
        btn.className = 'edit-preset-btn px-3 py-1.5 text-xs font-semibold rounded-lg border bg-blue-600 text-white border-blue-600 shadow-2xs transition-all';
    }
    window.updateEditCalculations();
}

document.addEventListener('DOMContentLoaded', function() {
    const checkboxes = document.querySelectorAll('.pemodal-checkbox');
    const jumlahInput = document.getElementById('jumlah_pemodal');
    const countBadge = document.getElementById('countBadge');
    
    // Format helpers
    const formatRupiah = (num) => 'Rp ' + new Intl.NumberFormat('id-ID').format(Math.floor(num));
    const parseRupiah = (str) => parseInt(str.replace(/[^0-9]/g, '') || 0);

    window.updateEditCalculations = function() {
        const count = document.querySelectorAll('.pemodal-checkbox:checked').length;
        jumlahInput.value = count;
        countBadge.textContent = count;
        document.getElementById('previewCount').textContent = count;
        
        const pagu = parseRupiah(document.getElementById('pagu').value);
        const paguNego = parseRupiah(document.getElementById('pagu_nego').value);
        const persenPasal = parseFloat(document.getElementById('persen_pasal').value) || 0;
        const persenAdmin = parseFloat(document.getElementById('persen_admin').value) || 0;
        
        const acuan = (paguNego > 0) ? paguNego : pagu;
        const dpp = acuan > 0 ? Math.round(acuan / 1.11) : 0;
        const chkKontrak = document.getElementById('tipe_potongan');
        const isKontrak = chkKontrak ? chkKontrak.checked : false;
        const basis = isKontrak ? acuan : dpp;

        const pasal = Math.round(basis * (persenPasal / 100));
        const admin = Math.round(basis * (persenAdmin / 100));
        const totalPasal = pasal + admin;
        const totalPersen = Math.round((persenPasal + persenAdmin) * 100) / 100;
        
        document.getElementById('previewDPP').textContent = formatRupiah(dpp);
        document.getElementById('previewPasal13').textContent = formatRupiah(pasal);
        document.getElementById('previewAdmin2').textContent = formatRupiah(admin);
        document.getElementById('previewTotalPasal').textContent = formatRupiah(totalPasal);

        document.getElementById('lbl_pasal').textContent = persenPasal;
        document.getElementById('lbl_admin').textContent = persenAdmin;
        document.getElementById('lbl_total_pasal').textContent = totalPersen;

        if (count > 0 && totalPasal > 0) {
            const totalPerOrang = Math.round(totalPasal / count);
            document.getElementById('previewTotalPerOrang').textContent = formatRupiah(totalPerOrang);
        } else {
            document.getElementById('previewTotalPerOrang').textContent = 'Rp 0';
        }
    };
    
    // Listeners
    checkboxes.forEach(cb => cb.addEventListener('change', window.updateEditCalculations));
    document.getElementById('pagu').addEventListener('input', window.updateEditCalculations);
    document.getElementById('pagu_nego').addEventListener('input', window.updateEditCalculations);
    
    // Auto format Rupiah
    document.querySelectorAll('.rupiah-input').forEach(input => {
        input.addEventListener('input', function(e) {
            let val = this.value.replace(/[^0-9]/g, '');
            if (val) {
                this.value = parseInt(val, 10).toLocaleString('id-ID');
            } else {
                this.value = '';
            }
            window.updateEditCalculations();
        });
    });
    
    // Initial
    window.updateEditCalculations();
});

/**
 * Handle Quick Tambah Group via AJAX
 */
function handleQuickTambahGroup(e) {
    e.preventDefault();
    const btn = document.getElementById('btnSubmitQuickGroup');
    const nama = document.getElementById('quick_nama_group').value.trim();
    const kode = document.getElementById('quick_kode_group').value.trim();
    const pSelect = document.getElementById('paket_perusahaan_id');
    const perusahaanId = pSelect ? pSelect.value : '';

    if (!nama) return;

    btn.disabled = true;
    btn.innerHTML = '<ion-icon name="sync-outline" class="animate-spin text-sm"></ion-icon> <span>Menyimpan...</span>';

    const formData = new FormData();
    formData.append('nama_group', nama);
    formData.append('kode_group', kode);
    formData.append('perusahaan_id', perusahaanId);

    fetch('<?= APP_URL ?>/group/ajaxStore', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        btn.disabled = false;
        btn.innerHTML = '<span>Simpan & Pilih Group</span>';

        if (data.success && data.group) {
            // Append option to select_group_id and select it
            const select = document.getElementById('select_group_id');
            const opt = document.createElement('option');
            opt.value = data.group.id;
            opt.textContent = data.group.nama_group;
            opt.selected = true;
            select.appendChild(opt);

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
</script>

<!-- Quick Tambah Group Modal -->
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
