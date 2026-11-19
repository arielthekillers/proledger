<!-- Header & Back -->
<div class="mb-6 flex items-center justify-between max-w-4xl mx-auto">
    <div>
        <div class="flex items-center gap-2 text-slate-500 mb-0.5">
            <ion-icon name="cube-outline"></ion-icon>
            <span class="text-xs font-bold uppercase tracking-wider">Paket Kegiatan</span>
        </div>
        <h1 class="text-2xl font-bold text-slate-800">Tambah Paket Kegiatan</h1>
    </div>

    <a href="<?= APP_URL ?>/paket" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-slate-200 rounded-xl text-slate-600 hover:bg-slate-50 hover:text-blue-600 transition-colors shadow-sm text-xs font-semibold">
        <ion-icon name="arrow-back-outline"></ion-icon>
        <span>Kembali ke Daftar</span>
    </a>
</div>

<form method="POST" action="<?= APP_URL ?>/paket/store" id="paketForm" class="max-w-4xl mx-auto space-y-6">
    <?= csrfField() ?>
    <!-- Nilai default (Penanggung Jawab, Progres, Status, & Investor dikelola di Edit Paket) -->
    <input type="hidden" name="penanggung_jawab" value="">
    <input type="hidden" name="progres_barang" value="Belum">
    <input type="hidden" name="status" value="Proses">
    <input type="hidden" name="jumlah_pemodal" id="jumlah_pemodal" value="0">
    
    <!-- 1. Basic Info Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
        <!-- Nama Paket -->
        <div class="mb-6">
            <label class="block text-sm font-bold text-slate-700 mb-2">Nama Paket Kegiatan <span class="text-red-500">*</span></label>
            <input type="text" name="nama_paket" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-colors text-sm" 
                   required placeholder="Contoh: Belanja Bahan-Isi Tabung Pemadam Kebakaran" value="<?= old('nama_paket') ?>">
        </div>
        
        <!-- Perusahaan & Group Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Perusahaan Aktif</label>
                <div class="flex items-center gap-3 px-4 py-3 bg-slate-100 border border-slate-200 rounded-xl text-sm">
                    <ion-icon name="business" class="text-blue-600 text-xl shrink-0"></ion-icon>
                    <span class="font-bold text-slate-800"><?= htmlspecialchars($activeCompany['nama'] ?? 'Belum Ada Perusahaan') ?></span>
                    <span class="text-xs text-slate-400 ml-auto font-medium">Terkunci ke Perusahaan Aktif</span>
                </div>
                <input type="hidden" name="perusahaan_id" id="paket_perusahaan_id" value="<?= $activeCompany['id'] ?? '' ?>">
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
                    <select name="group_id" id="select_group_id" class="w-full pl-11 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-colors appearance-none text-slate-700 text-sm">
                        <option value="">-- Tanpa Group (Paket Mandiri) --</option>
                        <?php if(isset($groups)): foreach($groups as $grp): ?>
                            <option value="<?= $grp['id'] ?>" <?= (($_GET['group_id'] ?? '') == $grp['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($grp['nama_group']) ?>
                            </option>
                        <?php endforeach; endif; ?>
                    </select>
                    <ion-icon name="chevron-down-outline" class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"></ion-icon>
                </div>
                <p class="text-[11px] text-slate-400 mt-1">Kosongkan jika paket ini berdiri sendiri</p>
            </div>
        </div>
    </div>
    
    <!-- 2. Financials Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
        <h3 class="text-base font-bold text-slate-800 mb-6 flex items-center gap-2">
            <ion-icon name="cash-outline" class="text-blue-500"></ion-icon>
            <span>Rincian Nilai Kontrak & Potongan</span>
        </h3>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Nilai Kontrak / Pagu Awal <span class="text-red-500">*</span></label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 font-semibold text-xs">Rp</span>
                    <input type="text" name="pagu" id="pagu" class="rupiah-input w-full pl-9 pr-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-colors font-mono font-medium text-slate-800 text-sm" 
                           required placeholder="0" value="<?= old('pagu') ?>">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Pagu Nego <span class="text-emerald-600 font-normal">(Acuan Akhir jika ada)</span></label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 font-semibold text-xs">Rp</span>
                    <input type="text" name="pagu_nego" id="pagu_nego" class="rupiah-input w-full pl-9 pr-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-colors font-mono font-medium text-emerald-700 text-sm" 
                           placeholder="Opsional" value="<?= old('pagu_nego') ?>">
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
                <button type="button" onclick="setCreatePreset(13, 2, this)" class="create-preset-btn px-3 py-1.5 text-xs font-semibold rounded-lg border bg-blue-600 text-white border-blue-600 shadow-2xs transition-all">
                    13% + 2% Admin (Total 15%)
                </button>
                <button type="button" onclick="setCreatePreset(20, 0, this)" class="create-preset-btn px-3 py-1.5 text-xs font-semibold rounded-lg border bg-white text-slate-600 border-slate-200 hover:border-slate-300 transition-all">
                    20% (Tanpa Admin)
                </button>
                <button type="button" onclick="setCreatePreset(15, 0, this)" class="create-preset-btn px-3 py-1.5 text-xs font-semibold rounded-lg border bg-white text-slate-600 border-slate-200 hover:border-slate-300 transition-all">
                    15% (Tanpa Admin)
                </button>
            </div>

            <div class="grid grid-cols-2 gap-4 pt-1">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Potongan Pasal (%)</label>
                    <div class="relative">
                        <input type="number" step="0.1" min="0" max="100" name="persen_pasal" id="persen_pasal" value="13" oninput="updateCalculations()"
                               class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-sm font-bold text-slate-800 focus:outline-none focus:border-blue-500 transition-all">
                        <span class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs font-bold">%</span>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Potongan Admin (%)</label>
                    <div class="relative">
                        <input type="number" step="0.1" min="0" max="100" name="persen_admin" id="persen_admin" value="2" oninput="updateCalculations()"
                               class="w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-sm font-bold text-slate-800 focus:outline-none focus:border-blue-500 transition-all">
                        <span class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs font-bold">%</span>
                    </div>
                </div>
            </div>

            <!-- Pilihan Dasar Potongan: Kontrak vs DPP -->
            <div class="p-2.5 rounded-xl border border-slate-200 bg-white/70">
                <label class="flex items-start gap-2 cursor-pointer select-none">
                    <input type="checkbox" name="tipe_potongan" id="tipe_potongan" value="kontrak" onchange="updateCreateCalculations()" class="w-4 h-4 rounded text-blue-600 focus:ring-0 mt-0.5 cursor-pointer">
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
                <div class="p-2.5 bg-white rounded-lg border border-slate-100 shadow-2xs">
                    <span class="text-[10px] text-slate-400 font-bold block uppercase">DPP (-11% PPN)</span>
                    <span class="font-bold font-mono text-slate-800 text-xs" id="previewDPP">Rp 0</span>
                </div>
                <div class="p-2.5 bg-white rounded-lg border border-slate-100 shadow-2xs">
                    <span class="text-[10px] text-slate-400 font-bold block uppercase">Pasal (<span id="lbl_pasal">13</span>%)</span>
                    <span class="font-bold font-mono text-slate-800 text-xs" id="previewPasal13">Rp 0</span>
                </div>
                <div class="p-2.5 bg-white rounded-lg border border-slate-100 shadow-2xs">
                    <span class="text-[10px] text-slate-400 font-bold block uppercase">Admin (<span id="lbl_admin">2</span>%)</span>
                    <span class="font-bold font-mono text-slate-800 text-xs" id="previewAdmin2">Rp 0</span>
                </div>
                <div class="p-2.5 bg-amber-50 rounded-lg border border-amber-200 shadow-2xs">
                    <span class="text-[10px] text-amber-800 font-bold block uppercase">Total Pasal (<span id="lbl_total_pasal">15</span>%)</span>
                    <span class="font-bold font-mono text-amber-800 text-xs" id="previewTotalPasal">Rp 0</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Submit Action -->
    <div class="flex items-center justify-end gap-3 pt-2">
        <a href="<?= APP_URL ?>/paket" class="px-5 py-2.5 border border-slate-200 text-slate-600 font-semibold rounded-xl hover:bg-slate-50 transition-colors text-sm">
            Batal
        </a>
        <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-md shadow-blue-600/20 transition-all hover:scale-[1.01] active:scale-[0.99] flex items-center gap-2 text-sm">
            <ion-icon name="checkmark-outline" class="text-lg"></ion-icon>
            <span>Simpan Paket Kegiatan</span>
        </button>
    </div>
</form>

<script>
function setCreatePreset(pasal, admin, btn) {
    document.getElementById('persen_pasal').value = pasal;
    document.getElementById('persen_admin').value = admin;
    document.querySelectorAll('.create-preset-btn').forEach(b => {
        b.className = 'create-preset-btn px-3 py-1.5 text-xs font-semibold rounded-lg border bg-white text-slate-600 border-slate-200 hover:border-slate-300 transition-all';
    });
    if (btn) {
        btn.className = 'create-preset-btn px-3 py-1.5 text-xs font-semibold rounded-lg border bg-blue-600 text-white border-blue-600 shadow-2xs transition-all';
    }
    window.updateCreateCalculations();
}

document.addEventListener('DOMContentLoaded', function() {
    // Format helpers
    const formatRupiah = (num) => 'Rp ' + new Intl.NumberFormat('id-ID').format(Math.floor(num));
    const parseRupiah = (str) => parseInt(str.replace(/[^0-9]/g, '') || 0);

    window.updateCreateCalculations = function() {
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
    };
    
    // Auto format Rupiah
    document.querySelectorAll('.rupiah-input').forEach(input => {
        input.addEventListener('input', function(e) {
            let val = this.value.replace(/[^0-9]/g, '');
            if (val) {
                this.value = parseInt(val, 10).toLocaleString('id-ID');
            } else {
                this.value = '';
            }
            window.updateCreateCalculations();
        });
    });

    window.updateCreateCalculations();
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
