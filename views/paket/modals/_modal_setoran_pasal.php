<!-- ========================================== -->
<!-- MODAL: KELOLA SETORAN PASAL PAKET          -->
<!-- ========================================== -->
<div id="modalSetoranPasal" class="app-modal fixed inset-0 z-[70] bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 transition-all duration-200 opacity-0 pointer-events-none hidden">
    <div class="modal-card bg-white rounded-2xl shadow-2xl border border-slate-100 max-w-lg w-full transform scale-95 opacity-0 transition-all duration-200 overflow-hidden max-h-[92vh] flex flex-col">
        <div class="px-5 py-3.5 bg-amber-50/70 border-b border-amber-100 flex items-center justify-between shrink-0">
            <div class="flex items-center gap-2.5 min-w-0">
                <div class="w-8 h-8 rounded-lg bg-amber-500 text-white flex items-center justify-center text-base shadow-xs shrink-0">
                    <ion-icon name="wallet-outline"></ion-icon>
                </div>
                <div class="min-w-0">
                    <h3 class="font-bold text-slate-800 text-sm">Setoran Pasal Paket</h3>
                    <p class="text-[11px] text-slate-500 truncate" id="setoranPasalSubtitle">-</p>
                </div>
            </div>
            <button type="button" onclick="window.ProModal.close('modalSetoranPasal')" class="p-1.5 text-slate-400 hover:text-slate-700 rounded-lg cursor-pointer">
                <ion-icon name="close-outline" class="text-xl"></ion-icon>
            </button>
        </div>

        <div class="overflow-y-auto p-5 space-y-4">
            <!-- Summary Cards -->
            <div class="grid grid-cols-3 gap-2.5">
                <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-200/80">
                    <div class="text-[10px] font-semibold text-slate-400 uppercase">Kewajiban Pasal</div>
                    <div id="spKewajiban" class="font-mono font-bold text-xs text-slate-800 mt-0.5">Rp 0</div>
                </div>
                <div class="p-2.5 rounded-xl bg-emerald-50/70 border border-emerald-200/80">
                    <div class="text-[10px] font-semibold text-emerald-700 uppercase">Sudah Disetor</div>
                    <div id="spTerbayar" class="font-mono font-bold text-xs text-emerald-700 mt-0.5">Rp 0</div>
                </div>
                <div class="p-2.5 rounded-xl bg-amber-50/70 border border-amber-200/80">
                    <div class="text-[10px] font-semibold text-amber-700 uppercase">Sisa Kurang</div>
                    <div id="spSisa" class="font-mono font-bold text-xs text-amber-800 mt-0.5">Rp 0</div>
                </div>
            </div>

            <!-- Progress Bar -->
            <div>
                <div class="flex items-center justify-between text-[11px] mb-1">
                    <span class="font-semibold text-slate-600">Progres Pelunasan Pasal</span>
                    <span id="spPersenLabel" class="font-mono font-extrabold text-amber-700">0%</span>
                </div>
                <div class="w-full h-2 bg-slate-100 rounded-full overflow-hidden">
                    <div id="spProgressBar" class="h-full bg-amber-500 rounded-full transition-all duration-300" style="width: 0%"></div>
                </div>
            </div>

            <!-- Form Tambah Setoran (hanya jika punya izin) -->
            <form id="formTambahSetoranPasal" onsubmit="submitSetoranPasal(event)" class="hidden p-3.5 bg-slate-50/80 rounded-xl border border-slate-200/80 space-y-2.5">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-700">+ Catat Setoran Baru</span>
                    <button type="button" id="btnLunasCepatPasal" onclick="fillLunasCepatPasal()"
                            class="px-2 py-0.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 rounded-md text-[10px] font-bold cursor-pointer">
                        Isi Lunas Sisa
                    </button>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                    <div>
                        <label class="block text-[10px] font-semibold text-slate-600 mb-1">Tanggal Setor</label>
                        <input type="date" name="tanggal_setor" id="spInputTanggal" value="<?= date('Y-m-d') ?>" required
                               class="w-full px-2.5 py-1.5 bg-white border border-slate-200 rounded-lg text-xs focus:outline-none focus:border-amber-500">
                    </div>
                    <div>
                        <label class="block text-[10px] font-semibold text-slate-600 mb-1">Nominal (Rp)</label>
                        <input type="text" name="nominal" id="spInputNominal" oninput="formatRupiahInput(this)" placeholder="0" required
                               class="w-full px-2.5 py-1.5 bg-white border border-slate-200 rounded-lg text-xs font-mono font-bold focus:outline-none focus:border-amber-500">
                    </div>
                </div>
                <div>
                    <label class="block text-[10px] font-semibold text-slate-600 mb-1">Catatan (Opsional)</label>
                    <input type="text" name="catatan" id="spInputCatatan" placeholder="Misal: Transfer tahap 1 ke koordinator..."
                           class="w-full px-2.5 py-1.5 bg-white border border-slate-200 rounded-lg text-xs focus:outline-none focus:border-amber-500">
                </div>
                <div class="flex justify-end">
                    <button type="submit" id="btnSubmitSetoranPasal"
                            class="px-4 py-1.5 bg-amber-600 hover:bg-amber-700 text-white font-semibold rounded-lg text-xs shadow-xs cursor-pointer">
                        Simpan Setoran
                    </button>
                </div>
            </form>

            <!-- Riwayat Setoran -->
            <div>
                <div class="text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-2">Riwayat Setoran</div>
                <div id="spHistoryList" class="space-y-1.5 max-h-52 overflow-y-auto">
                    <div class="text-xs text-slate-400 italic text-center py-3">Memuat...</div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let currentSpKode = null;
let currentSpSisa = 0;
let currentSpCanEdit = false;
let currentSpDirty = false;

function fmtSpRupiah(n) {
    return 'Rp ' + Math.round(n || 0).toLocaleString('id-ID');
}

function openSetoranPasalModal(kode, namaPaket) {
    currentSpKode = kode;
    currentSpDirty = false;
    const sub = document.getElementById('setoranPasalSubtitle');
    if (sub) sub.textContent = (namaPaket || '') + ' [#' + kode + ']';
    window.ProModal.open('modalSetoranPasal');
    loadSetoranPasalData();
}

function loadSetoranPasalData() {
    if (!currentSpKode) return;
    fetch('<?= APP_URL ?>/paket/getSetoranPasal/' + encodeURIComponent(currentSpKode), {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(res => {
        if (!res.success) {
            if (window.ProToast) window.ProToast.error(res.message || 'Gagal memuat data setoran.');
            return;
        }
        currentSpSisa = parseFloat(res.sisa) || 0;
        currentSpCanEdit = !!res.can_edit;

        document.getElementById('spKewajiban').textContent = fmtSpRupiah(res.total_pasal);
        document.getElementById('spTerbayar').textContent = fmtSpRupiah(res.total_setor);
        document.getElementById('spSisa').textContent = fmtSpRupiah(res.sisa);

        const pct = Math.min(100, Math.max(0, parseInt(res.persen, 10) || 0));
        const pctLbl = document.getElementById('spPersenLabel');
        const bar = document.getElementById('spProgressBar');
        if (pctLbl) {
            pctLbl.textContent = pct + '%' + (pct >= 100 ? ' (Lunas)' : '');
            pctLbl.className = 'font-mono font-extrabold ' + (pct >= 100 ? 'text-emerald-700' : 'text-amber-700');
        }
        if (bar) {
            bar.style.width = pct + '%';
            bar.className = 'h-full rounded-full transition-all duration-300 ' + (pct >= 100 ? 'bg-emerald-500' : 'bg-amber-500');
        }

        const form = document.getElementById('formTambahSetoranPasal');
        if (form) {
            if (currentSpCanEdit) form.classList.remove('hidden');
            else form.classList.add('hidden');
        }
        const btnLunas = document.getElementById('btnLunasCepatPasal');
        if (btnLunas) {
            if (currentSpSisa > 0) btnLunas.classList.remove('hidden');
            else btnLunas.classList.add('hidden');
        }

        const list = document.getElementById('spHistoryList');
        if (list) {
            if (!res.items || res.items.length === 0) {
                list.innerHTML = '<div class="text-xs text-slate-400 italic text-center py-3">Belum ada riwayat setoran pasal.</div>';
            } else {
                list.innerHTML = res.items.map(it => {
                    const delBtn = currentSpCanEdit
                        ? `<button type="button" onclick="deleteSetoranPasal(${it.id})" class="p-1 text-slate-400 hover:text-rose-600 rounded cursor-pointer" title="Hapus"><ion-icon name="trash-outline" class="text-xs"></ion-icon></button>`
                        : '';
                    const noteHtml = it.catatan ? `<div class="text-[10px] text-slate-500">${String(it.catatan).replace(/</g,'&lt;')}</div>` : '';
                    const byHtml = it.creator_name ? ` &bull; ${String(it.creator_name).replace(/</g,'&lt;')}` : '';
                    return `
                        <div class="flex items-center justify-between gap-2 p-2.5 bg-white rounded-xl border border-slate-200/80">
                            <div class="min-w-0">
                                <div class="font-mono font-bold text-xs text-emerald-700">${fmtSpRupiah(it.nominal)}</div>
                                <div class="text-[10px] text-slate-400">${it.tanggal_setor}${byHtml}</div>
                                ${noteHtml}
                            </div>
                            ${delBtn}
                        </div>
                    `;
                }).join('');
            }
        }
    });
}

function fillLunasCepatPasal() {
    const inp = document.getElementById('spInputNominal');
    if (inp && currentSpSisa > 0) {
        inp.value = Math.round(currentSpSisa).toLocaleString('id-ID');
    }
}

function submitSetoranPasal(e) {
    e.preventDefault();
    if (!currentSpKode) return;
    const form = document.getElementById('formTambahSetoranPasal');
    const fd = new FormData(form);
    const btn = document.getElementById('btnSubmitSetoranPasal');
    if (btn) { btn.disabled = true; btn.textContent = 'Menyimpan...'; }

    fetch('<?= APP_URL ?>/paket/storeSetoranPasal/' + encodeURIComponent(currentSpKode), {
        method: 'POST',
        body: fd,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(res => {
        if (btn) { btn.disabled = false; btn.textContent = 'Simpan Setoran'; }
        if (res.success) {
            currentSpDirty = true;
            document.getElementById('spInputNominal').value = '';
            document.getElementById('spInputCatatan').value = '';
            if (window.ProToast) window.ProToast.success(res.message || 'Setoran dicatat.');
            loadSetoranPasalData();
        } else {
            if (window.ProToast) window.ProToast.error(res.message || 'Gagal menyimpan setoran.');
        }
    })
    .catch(() => {
        if (btn) { btn.disabled = false; btn.textContent = 'Simpan Setoran'; }
    });
}

function deleteSetoranPasal(id) {
    if (!currentSpKode || !confirm('Hapus catatan setoran pasal ini?')) return;
    fetch('<?= APP_URL ?>/paket/deleteSetoranPasal/' + encodeURIComponent(currentSpKode) + '/' + id, {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            currentSpDirty = true;
            if (window.ProToast) window.ProToast.success(res.message || 'Setoran dihapus.');
            loadSetoranPasalData();
        } else {
            if (window.ProToast) window.ProToast.error(res.message || 'Gagal menghapus setoran.');
        }
    });
}

document.addEventListener('click', function(e) {
    const modal = document.getElementById('modalSetoranPasal');
    if (!modal || !currentSpDirty) return;
    if (e.target === modal || e.target.closest('[onclick*="modalSetoranPasal"]')) {
        if (e.target.closest('[onclick*="openSetoranPasalModal"]')) return;
        setTimeout(() => window.location.reload(), 150);
    }
});
</script>
