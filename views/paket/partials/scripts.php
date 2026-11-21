<!-- ========================================== -->
<!-- 6. JAVASCRIPT LOGIC                        -->
<!-- ========================================== -->
<script>
const SPINNER_SVG = '<svg class="animate-spin -ml-0.5 mr-1.5 h-3.5 w-3.5 inline-block text-current" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';

/**
 * Tab Switcher (info, items, distribusi, dokumentasi, dokumen)
 */
function switchDetailTab(tabName) {
    const validTabs = ['info', 'items', 'pemesanan', 'distribusi', 'dokumentasi', 'dokumen', 'profit'];
    if (!validTabs.includes(tabName)) tabName = 'info';

    // Hapus style preload jika ada agar kontrol display kembali normal
    const preload = document.getElementById('tab-preload-style');
    if (preload) preload.remove();

    validTabs.forEach(t => {
        const el = document.getElementById('tabContent_' + t);
        const btn = document.getElementById('tabBtn_' + t);
        if (t === tabName) {
            if (el) el.classList.remove('hidden');
            if (btn) {
                btn.className = (t === 'profit') 
                    ? 'tab-btn py-3 px-4 text-xs font-bold border-b-2 border-emerald-600 text-emerald-600 flex items-center gap-2 transition-all'
                    : 'tab-btn py-3 px-4 text-xs font-bold border-b-2 border-blue-600 text-blue-600 flex items-center gap-2 transition-all';
            }
        } else {
            if (el) el.classList.add('hidden');
            if (btn) {
                btn.className = 'tab-btn py-3 px-4 text-xs font-semibold border-b-2 border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300 flex items-center gap-2 transition-all';
            }
        }
    });

    if (tabName === 'info' && typeof initTabInfoCharts === 'function') {
        setTimeout(initTabInfoCharts, 50);
    }
    if (tabName === 'profit' && typeof initTabProfitCharts === 'function') {
        setTimeout(initTabProfitCharts, 50);
    }

    if (window.history && window.history.replaceState) {
        const u = new URL(window.location.href);
        u.searchParams.set('tab', tabName);
        u.hash = tabName;
        window.history.replaceState(null, '', u.toString());
    } else {
        window.location.hash = tabName;
    }
}

// Check initial hash on load
window.addEventListener('DOMContentLoaded', () => {
    const urlParams = new URLSearchParams(window.location.search);
    const tabParam = urlParams.get('tab');
    const h = (window.location.hash || '').replace('#', '');
    const validTabs = ['info', 'items', 'pemesanan', 'distribusi', 'dokumentasi', 'dokumen', 'profit'];
    const target = (validTabs.includes(tabParam)) ? tabParam : (validTabs.includes(h) ? h : '');
    if (target) {
        switchDetailTab(target);
    }
});

function openUploadDokumenModal() {
    openUploadBastSekolah('');
}

function openUploadBastSekolah(namaTujuan) {
    const modal = document.getElementById('modalUploadDokumen');
    if (!modal) return;

    const modalTitle = document.getElementById('modalUploadTitle');
    if (modalTitle) {
        modalTitle.textContent = namaTujuan ? ('Upload BAST - ' + namaTujuan) : 'Upload Berkas BAST';
    }

    const modalSub = document.getElementById('modalUploadSubtitle');
    if (modalSub) {
        modalSub.textContent = namaTujuan ? ('Upload scan/foto berkas BAST yang telah ditandatangani untuk ' + namaTujuan) : 'Upload scan/foto lembar BAST yang telah ditandatangani.';
    }

    const namaDocInput = document.getElementById('upload_nama_dokumen');
    if (namaDocInput) {
        namaDocInput.value = namaTujuan ? ('BAST - ' + namaTujuan) : '';
    }

    const ketInput = document.getElementById('upload_keterangan');
    if (ketInput) {
        ketInput.value = namaTujuan ? ('Berita Acara Serah Terima ditandatangani oleh pihak ' + namaTujuan) : '';
    }

    window.ProModal.open('modalUploadDokumen');
}

function toggleCustomJenis(val) {
    const wrap = document.getElementById('wrapper_custom_jenis');
    const input = document.getElementById('upload_jenis_dokumen_custom');
    if (val === 'Lainnya') {
        if (wrap) wrap.classList.remove('hidden');
        if (input) {
            input.required = true;
            input.focus();
        }
    } else {
        if (wrap) wrap.classList.add('hidden');
        if (input) {
            input.required = false;
        }
    }
}

/**
 * Format string rupiah ke angka murni
 */
function parseRupiahNumber(str) {
    if (!str) return 0;
    return parseFloat(str.toString().replace(/[^0-9]/g, '')) || 0;
}

function formatRupiahString(num) {
    return 'Rp ' + Math.round(num).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}

function formatRupiahInput(input) {
    let val = input.value.replace(/[^0-9]/g, '');
    if (val) {
        input.value = parseInt(val, 10).toLocaleString('id-ID');
    } else {
        input.value = '';
    }
}

/**
 * Live Calculation Total Harga Item
 */
function calcItemTotal(prefix) {
    const qtyInput = document.getElementById(prefix + '_item_jumlah');
    const priceInput = document.getElementById(prefix + '_item_harga');
    const previewEl = document.getElementById(prefix + '_item_preview_total');

    const qty = parseFloat(qtyInput.value) || 0;
    const price = parseRupiahNumber(priceInput.value);
    const total = Math.round(qty * price);

    if (previewEl) {
        previewEl.textContent = formatRupiahString(total);
    }
}

/**
 * Satuan Custom Toggle
 */
function toggleCustomSatuan(selectEl, customInputId) {
    const customInput = document.getElementById(customInputId);
    if (!customInput) return;
    if (selectEl.value === 'CUSTOM') {
        customInput.classList.remove('hidden');
        customInput.focus();
        customInput.required = true;
    } else {
        customInput.classList.add('hidden');
        customInput.required = false;
        customInput.value = '';
    }
}

/**
 * Buka Modal Tambah Item
 */
function openTambahItemModal() {
    document.getElementById('tambah_item_komponen').value = '';
    document.getElementById('tambah_item_spesifikasi').value = '';
    
    const selSatuan = document.getElementById('tambah_item_satuan');
    if (selSatuan) {
        selSatuan.value = 'Unit';
        toggleCustomSatuan(selSatuan, 'tambah_item_satuan_custom');
    }

    document.getElementById('tambah_item_jumlah').value = '1';
    document.getElementById('tambah_item_harga').value = '';
    document.getElementById('tambah_item_preview_total').textContent = 'Rp 0';

    window.ProModal.open('modalTambahItem');
}

/**
 * Buka Modal Edit Item
 */
function openEditItemModal(item) {
    if (!item) return;

    const form = document.getElementById('formEditItem');
    form.action = '<?= APP_URL ?>/paket/updateItem/<?= $paket['kode_paket'] ?: $paket['id'] ?>/' + item.id;

    document.getElementById('edit_item_komponen').value = item.komponen || '';
    document.getElementById('edit_item_spesifikasi').value = item.spesifikasi || '';

    // Set Satuan Select & Custom input
    const selSatuan = document.getElementById('edit_item_satuan');
    const customInput = document.getElementById('edit_item_satuan_custom');
    const valSat = (item.satuan || 'Unit').trim();
    let found = false;

    if (selSatuan) {
        for (let i = 0; i < selSatuan.options.length; i++) {
            if (selSatuan.options[i].value.toLowerCase() === valSat.toLowerCase()) {
                selSatuan.selectedIndex = i;
                found = true;
                break;
            }
        }
        if (!found) {
            selSatuan.value = 'CUSTOM';
            if (customInput) {
                customInput.value = valSat;
                customInput.classList.remove('hidden');
            }
        } else {
            if (customInput) {
                customInput.value = '';
                customInput.classList.add('hidden');
            }
        }
    }

    document.getElementById('edit_item_jumlah').value = parseFloat(item.jumlah) || 1;

    const harga = parseInt(item.harga_satuan, 10) || 0;
    document.getElementById('edit_item_harga').value = harga > 0 ? harga.toLocaleString('id-ID') : '';

    document.getElementById('editItemSubtitle').textContent = 'Mengubah: ' + (item.komponen || '');

    calcItemTotal('edit');
    window.ProModal.open('modalEditItem');
}

/**
 * Autocomplete Lokasi Tujuan dari Master Lokasi
 */
function onSelectLokasiTujuan(input, prefix) {
    const val = input.value.trim().toUpperCase();
    if (!val) return;

    const datalist = document.getElementById('list_master_lokasi');
    if (!datalist) return;

    const options = datalist.querySelectorAll('option');
    for (let opt of options) {
        if (opt.value.toUpperCase() === val) {
            const kat = opt.getAttribute('data-kategori');
            const pic = opt.getAttribute('data-pic');
            const kontak = opt.getAttribute('data-kontak');
            const alamat = opt.getAttribute('data-alamat');
            const kabkota = opt.getAttribute('data-kabkota');
            const lokId = opt.getAttribute('data-id');

            if (kat) document.getElementById(prefix + '_dist_kategori').value = kat;
            if (pic) document.getElementById(prefix + '_dist_pic').value = pic;
            if (kontak) document.getElementById(prefix + '_dist_kontak').value = kontak;
            if (alamat) document.getElementById(prefix + '_dist_alamat').value = alamat;
            if (kabkota) document.getElementById(prefix + '_dist_kabkota').value = kabkota;
            if (lokId) {
                const hiddenId = document.getElementById(prefix + '_dist_lokasi_id');
                if (hiddenId) hiddenId.value = lokId;
            }
            break;
        }
    }
}

// Master data items dari PHP
window.paketItemsData = <?= json_encode($items, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?> || [];

let currentDistribusiItem = null;

/**
 * Pilih baris tujuan dari tabel master di modal
 */
function selectTujuanRow(rowEl, id, namaLokasi, kabKota) {
    if (!rowEl) return;

    // Bersihkan highlight dan radio di semua baris
    document.querySelectorAll('#tbodyListTujuan tr').forEach(r => {
        r.classList.remove('bg-indigo-50/90', 'border-indigo-300');
        const rad = r.querySelector('.tujuan-radio');
        if (rad) rad.checked = false;
    });

    // Beri highlight dan check radio pada baris terpilih
    rowEl.classList.add('bg-indigo-50/90');
    const radio = rowEl.querySelector('.tujuan-radio');
    if (radio) radio.checked = true;

    // Set hidden inputs
    document.getElementById('tambah_dist_lokasi_id').value = id;
    document.getElementById('tambah_dist_nama_tujuan').value = namaLokasi;

    // Update bar indikator tujuan terpilih
    const ind = document.getElementById('tujuan_selected_indicator');
    const dot = document.getElementById('indicator_dot');
    const text = document.getElementById('indicator_text');

    ind.className = 'p-2.5 rounded-xl bg-emerald-50 border border-emerald-200 text-xs flex items-center justify-between transition-all';
    dot.className = 'w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse';
    text.innerHTML = 'Tujuan Terpilih: <strong class="text-slate-800 font-bold">' + namaLokasi + '</strong> <span class="text-slate-500">(' + (kabKota || '-') + ')</span>';

    // Cek validasi kuota & aktifkan tombol jika valid
    validateDistribusiQty();
}

/**
 * Filter tabel tujuan berdasarkan pencarian
 */
function filterTujuanTable(query) {
    const q = (query || '').toLowerCase().trim();
    const rows = document.querySelectorAll('#tbodyListTujuan tr');
    let visibleCount = 0;

    rows.forEach(r => {
        const text = r.getAttribute('data-search') || '';
        if (!q || text.includes(q)) {
            r.style.display = '';
            visibleCount++;
        } else {
            r.style.display = 'none';
        }
    });

    const countLabel = document.getElementById('tujuan_count_label');
    if (countLabel) {
        countLabel.textContent = visibleCount + ' tujuan ditemukan';
    }
}

/**
 * Helper Escape HTML untuk JS Templating
 */
function escapeHtml(text) {
    if (!text) return '';
    return String(text).replace(/[&<>"']/g, function(m) {
        return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[m];
    });
}

/**
 * Quick Tambah Tujuan Modal Handlers (AJAX)
 */
function openQuickTambahTujuanModal() {
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
}

function closeQuickTambahTujuanModal() {
    window.ProModal.close('modalQuickTambahTujuan');
}

function handleQuickTambahTujuan(e) {
    e.preventDefault();
    const btn = document.getElementById('btnSubmitQuickTujuan');
    const nama = document.getElementById('quick_nama_lokasi').value.trim();
    const kab = document.getElementById('quick_kabupaten').value.trim();
    if (!nama) return;

    btn.disabled = true;
    btn.innerHTML = SPINNER_SVG + ' <span>Menyimpan...</span>';

    const formData = new FormData();
    formData.append('nama_lokasi', nama);
    formData.append('kab_kota', kab);

    fetch('<?= APP_URL ?>/paket/storeLokasiQuick/<?= $paket['kode_paket'] ?: $paket['id'] ?>', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        btn.disabled = false;
        btn.innerHTML = '<ion-icon name="checkmark-outline" class="text-sm"></ion-icon> <span>Simpan & Pilih Tujuan</span>';

        if (data.success && (data.lokasi || data.data)) {
            const lok = data.lokasi || data.data;
            const tbody = document.getElementById('tbodyListTujuan');
            if (tbody) {
                const tr = document.createElement('tr');
                tr.className = 'tujuan-row hover:bg-indigo-50/60 cursor-pointer transition-colors';
                tr.setAttribute('data-id', lok.id);
                tr.setAttribute('data-search', ((lok.nama_lokasi || '') + ' ' + (lok.kab_kota || '')).toLowerCase());
                tr.onclick = function() { selectTujuanRow(this, lok.id, lok.nama_lokasi, lok.kab_kota); };
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
                selectTujuanRow(tr, lok.id, lok.nama_lokasi, lok.kab_kota);

                const countLabel = document.getElementById('tujuan_count_label');
                if (countLabel) {
                    const totalRows = tbody.querySelectorAll('tr').length;
                    countLabel.textContent = totalRows + ' tujuan';
                }
            }

            closeQuickTambahTujuanModal();
            window.ProToast.success(data.message || 'Tujuan berhasil ditambahkan!');
            document.getElementById('formQuickTujuan').reset();
        } else {
            window.ProToast.error(data.message || 'Gagal menambahkan tujuan.');
        }
    })
    .catch(err => {
        btn.disabled = false;
        btn.innerHTML = '<ion-icon name="checkmark-outline" class="text-sm"></ion-icon> <span>Simpan & Pilih Tujuan</span>';
        window.ProToast.error('Koneksi terputus saat menyimpan tujuan.');
    });
}

/**
 * Update UI kartu kalkulasi kuota item di modal
 */
function updateDistribusiQuotaCard(item) {
    currentDistribusiItem = item;
    const inputJumlah = document.getElementById('tambah_dist_jumlah');
    const btnSubmit = document.getElementById('btnSubmitDistribusi');
    const errBox = document.getElementById('tambah_dist_qty_error');
    const errMsg = document.getElementById('tambah_dist_qty_error_msg');

    if (!item) {
        document.getElementById('dist_card_item_name').textContent = 'Pilih item barang terlebih dahulu';
        document.getElementById('dist_card_total').textContent = '0';
        document.getElementById('dist_card_allocated').textContent = '0';
        document.getElementById('dist_card_remaining').textContent = '0';
        inputJumlah.value = 1;
        inputJumlah.disabled = true;
        btnSubmit.disabled = true;
        btnSubmit.classList.add('opacity-40', 'cursor-not-allowed', 'pointer-events-none');
        return;
    }

    const totalQty = parseFloat(item.jumlah) || 0;
    const allocatedQty = parseFloat(item.allocated_qty) || 0;
    const remainingQty = Math.max(0, totalQty - allocatedQty);
    const satuan = item.satuan || 'Unit';

    // Update labels and text
    document.getElementById('dist_card_item_name').textContent = item.komponen;
    document.getElementById('dist_card_total').textContent = totalQty;
    document.getElementById('dist_card_allocated').textContent = allocatedQty;
    document.getElementById('dist_card_remaining').textContent = remainingQty;

    document.querySelectorAll('.dist_satuan_text').forEach(el => {
        el.textContent = satuan;
    });

    if (remainingQty > 0) {
        // Input & submit handling
        inputJumlah.disabled = false;
        inputJumlah.step = "1";
        inputJumlah.min = "1";
        inputJumlah.max = remainingQty;
        inputJumlah.value = Math.floor(remainingQty); // Otomatis terisi bulat sisa kuota, dan bisa dikurangi
        errBox.classList.add('hidden');
    } else {
        // Kuota habis -> tombol simpan ga bisa diklik!
        inputJumlah.value = 0;
        inputJumlah.disabled = true;
        inputJumlah.max = 0;
        btnSubmit.disabled = true;
        btnSubmit.classList.add('opacity-40', 'cursor-not-allowed', 'pointer-events-none');
        errMsg.textContent = 'Seluruh kuota item ini (' + totalQty + ' ' + satuan + ') sudah dialokasikan ke penerima. Tombol simpan dinonaktifkan.';
        errBox.classList.remove('hidden');
    }

    // Jalankan validasi untuk cek apakah tujuan sudah terpilih
    validateDistribusiQty();
}

/**
 * Validasi realtime jumlah alokasi saat user mengetik
 */
function validateDistribusiQty() {
    if (!currentDistribusiItem) return;

    const inputJumlah = document.getElementById('tambah_dist_jumlah');
    const btnSubmit = document.getElementById('btnSubmitDistribusi');
    const errBox = document.getElementById('tambah_dist_qty_error');
    const errMsg = document.getElementById('tambah_dist_qty_error_msg');
    const lokasiId = document.getElementById('tambah_dist_lokasi_id').value;

    const totalQty = parseFloat(currentDistribusiItem.jumlah) || 0;
    const allocatedQty = parseFloat(currentDistribusiItem.allocated_qty) || 0;
    const remainingQty = Math.max(0, totalQty - allocatedQty);
    const satuan = currentDistribusiItem.satuan || 'Unit';
    const val = parseFloat(inputJumlah.value);

    if (remainingQty <= 0) {
        btnSubmit.disabled = true;
        btnSubmit.classList.add('opacity-40', 'cursor-not-allowed', 'pointer-events-none');
        errMsg.textContent = 'Kuota item ini sudah habis (0 ' + satuan + ' tersisa). Tombol simpan dinonaktifkan.';
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

    // Input jumlah valid!
    errBox.classList.add('hidden');

    // Cek apakah tujuan di tabel sudah dipilih
    if (!lokasiId) {
        btnSubmit.disabled = true;
        btnSubmit.classList.add('opacity-40', 'cursor-not-allowed', 'pointer-events-none');
        return;
    }

    // Semua valid -> tombol simpan aktif!
    btnSubmit.disabled = false;
    btnSubmit.classList.remove('opacity-40', 'cursor-not-allowed', 'pointer-events-none');
}

/**
 * Handle perubahan pilihan dropdown item di modal tambah distribusi umum
 */
function onDistribusiItemSelectChange(itemId) {
    const hiddenId = document.getElementById('tambah_dist_item_id_hidden');
    hiddenId.value = itemId || '';
    if (!itemId) {
        updateDistribusiQuotaCard(null);
        return;
    }
    const item = window.paketItemsData.find(it => it.id == itemId);
    updateDistribusiQuotaCard(item || null);
}

/**
 * Reset pilihan tujuan di tabel
 */
function resetTujuanTableSelection() {
    document.getElementById('tambah_dist_lokasi_id').value = '';
    document.getElementById('tambah_dist_nama_tujuan').value = '';

    const ind = document.getElementById('tujuan_selected_indicator');
    const dot = document.getElementById('indicator_dot');
    const text = document.getElementById('indicator_text');

    if (ind) ind.className = 'p-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs flex items-center justify-between transition-all';
    if (dot) dot.className = 'w-2.5 h-2.5 rounded-full bg-slate-300';
    if (text) text.textContent = 'Belum ada tujuan yang dipilih. Silakan klik salah satu baris di tabel.';

    // Bersihkan radio & highlight
    document.querySelectorAll('#tbodyListTujuan tr').forEach(r => {
        r.classList.remove('bg-indigo-50/90');
        const rad = r.querySelector('.tujuan-radio');
        if (rad) rad.checked = false;
    });

    const searchInput = document.getElementById('search_tujuan_table');
    if (searchInput) {
        searchInput.value = '';
        filterTujuanTable('');
    }
}

/**
 * Buka Modal Distribusi langsung per Item dari tabel Item / Spesifikasi (Tab 2)
 */
function openDistribusiPerItemModal(item) {
    if (!item) return;

    // Header modal - bersih tanpa mengulang nama barang
    document.getElementById('tambah_dist_modal_title').textContent = 'Tambah Alokasi Distribusi';
    document.getElementById('tambah_dist_modal_subtitle').textContent = 'Pilih tujuan penerima dan tentukan jumlah barang';

    // Set hidden item_id & redirect tab ke items
    document.getElementById('tambah_dist_item_id_hidden').value = item.id;
    document.getElementById('tambah_dist_redirect_tab').value = 'items';

    // Sembunyikan select dropdown item (tidak perlu select lagi)
    document.getElementById('tambah_dist_item_dropdown_wrapper').classList.add('hidden');

    // Reset pilihan tabel tujuan
    resetTujuanTableSelection();

    // Update kalkulasi kuota card
    updateDistribusiQuotaCard(item);

    window.ProModal.open('modalTambahDistribusi');
}
/**
 * Update Status Barang Pop Up (Belum Dipesan vs Sudah Dipesan)
 */
function openModalUpdateStatusBarang(dist) {
    if (!dist) return;
    document.getElementById('status_barang_dist_id').value = dist.id;
    document.getElementById('status_barang_modal_tujuan').textContent = dist.nama_tujuan || '-';
    
    const qtyText = (parseFloat(dist.jumlah) || 1) + ' ' + (dist.satuan || 'Unit');
    document.getElementById('status_barang_modal_item').textContent = (dist.nama_komponen || '-') + ' (' + qtyText + ')';

    const itemHargaKontrak = (dist.item_harga_satuan && parseFloat(dist.item_harga_satuan) > 0) 
        ? formatRupiahString(parseFloat(dist.item_harga_satuan)) 
        : '-';
    const elHargaKontrak = document.getElementById('status_barang_modal_harga_kontrak');
    if (elHargaKontrak) elHargaKontrak.textContent = itemHargaKontrak;

    const isSudah = (dist.status_barang === 'Sudah Dipesan');
    const optBelum = document.getElementById('opt_status_belum_pesan');
    const optSudah = document.getElementById('opt_status_sudah_pesan');

    if (isSudah) {
        if (optSudah) optSudah.checked = true;
    } else {
        if (optBelum) optBelum.checked = true;
    }

    // Kosongkan jika belum pernah diinput detail barangnya oleh user
    document.getElementById('status_barang_nama').value = dist.nama_barang || '';
    
    const hargaVal = (dist.harga_satuan !== null && dist.harga_satuan !== undefined && dist.harga_satuan !== '') 
        ? dist.harga_satuan 
        : '';
    const hargaInput = document.getElementById('status_barang_harga');
    if (hargaInput) {
        hargaInput.value = hargaVal ? parseInt(hargaVal, 10).toLocaleString('id-ID') : '';
    }

    document.getElementById('status_barang_ekspedisi').value = dist.ekspedisi || '';
    document.getElementById('status_barang_resi').value = dist.no_resi || '';
    document.getElementById('status_barang_link').value = dist.link_pembelian || '';

    toggleStatusBarangInputs();

    window.ProModal.open('modalUpdateStatusBarang');
}

function toggleStatusBarangInputs() {
    const optSudah = document.getElementById('opt_status_sudah_pesan');
    const section = document.getElementById('section_detail_pesanan');
    const namaInput = document.getElementById('status_barang_nama');

    if (optSudah && optSudah.checked) {
        if (section) section.classList.remove('hidden');
        if (namaInput) namaInput.required = true;
    } else {
        if (section) section.classList.add('hidden');
        if (namaInput) namaInput.required = false;
    }
}

function submitUpdateStatusBarang(e) {
    e.preventDefault();
    const distId = document.getElementById('status_barang_dist_id').value;
    const form = document.getElementById('formUpdateStatusBarang');
    const formData = new FormData(form);
    const btn = document.getElementById('btnSubmitStatusBarang');

    if (btn) {
        btn.disabled = true;
        btn.innerHTML = SPINNER_SVG + ' <span>Menyimpan...</span>';
    }

    fetch('<?= APP_URL ?>/paket/updateStatusBarang/<?= $paket['kode_paket'] ?: $paket['id'] ?>/' + distId, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            window.ProToast?.success ? window.ProToast.success(data.message || 'Status barang berhasil disimpan!') : window.ProToast?.show?.('success', data.message || 'Status barang berhasil disimpan!');
            window.ProModal.close('modalUpdateStatusBarang');
            if (window.history && window.history.replaceState) {
                const targetUrl = new URL(window.location.href);
                targetUrl.searchParams.set('tab', 'pemesanan');
                targetUrl.hash = 'pemesanan';
                window.history.replaceState(null, '', targetUrl.toString());
            }
            setTimeout(() => {
                window.location.reload();
            }, 300);
        } else {
            alert(data.message || 'Gagal menyimpan status barang.');
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = '<ion-icon name="checkmark-outline" class="text-sm"></ion-icon><span>Simpan Status Barang</span>';
            }
        }
    })
    .catch(err => {
        console.error(err);
        form.action = '<?= APP_URL ?>/paket/updateStatusBarang/<?= $paket['kode_paket'] ?: $paket['id'] ?>/' + distId + '?tab=pemesanan#pemesanan';
        form.method = 'POST';
        form.submit();
    });
}

/**
 * Update Status Distribusi Pop Up (Tiba dirumah/kantor, Tiba di Gudang, Tiba di Tujuan)
 */
function openModalUpdateStatusDistribusi(dist) {
    if (!dist) return;

    if (dist.status_barang !== 'Sudah Dipesan') {
        window.ProToast?.show?.('Pesan barang terlebih dahulu sebelum mengatur status distribusi!', 'warning');
        return;
    }

    document.getElementById('status_distribusi_dist_id').value = dist.id;
    document.getElementById('status_distribusi_modal_tujuan').textContent = dist.nama_tujuan || '-';
    
    const qtyText = (parseFloat(dist.jumlah) || 1) + ' ' + (dist.satuan || 'Unit');
    document.getElementById('status_distribusi_modal_item').textContent = (dist.nama_komponen || '-') + ' (' + qtyText + ')';

    const sDist = dist.status_distribusi || 'Tiba dirumah/kantor';
    const optKantor = document.getElementById('dist_opt_kantor');
    const optGudang = document.getElementById('dist_opt_gudang');
    const optTujuan = document.getElementById('dist_opt_tujuan');

    if (optKantor) optKantor.checked = (sDist === 'Tiba dirumah/kantor' || sDist === 'Tiba di Rumah/Kantor' || sDist === 'kantor');
    if (optGudang) optGudang.checked = (sDist === 'Tiba di Gudang' || sDist === 'gudang');
    if (optTujuan) optTujuan.checked = (sDist === 'Tiba di Tujuan');

    window.ProModal.open('modalUpdateStatusDistribusi');
}

function submitUpdateStatusDistribusi(e) {
    e.preventDefault();
    const distId = document.getElementById('status_distribusi_dist_id').value;
    const form = document.getElementById('formUpdateStatusDistribusi');
    const formData = new FormData(form);
    const btn = document.getElementById('btnSubmitStatusDistribusi');

    if (btn) {
        btn.disabled = true;
        btn.innerHTML = SPINNER_SVG + ' <span>Menyimpan...</span>';
    }

    fetch('<?= APP_URL ?>/paket/updateStatusDistribusi/<?= $paket['kode_paket'] ?: $paket['id'] ?>/' + distId, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            window.ProToast?.success ? window.ProToast.success(data.message || 'Status distribusi berhasil disimpan!') : window.ProToast?.show?.('success', data.message || 'Status distribusi berhasil disimpan!');
            window.ProModal.close('modalUpdateStatusDistribusi');
            if (window.history && window.history.replaceState) {
                const targetUrl = new URL(window.location.href);
                targetUrl.searchParams.set('tab', 'distribusi');
                targetUrl.hash = 'distribusi';
                window.history.replaceState(null, '', targetUrl.toString());
            }
            setTimeout(() => {
                window.location.reload();
            }, 300);
        } else {
            alert(data.message || 'Gagal menyimpan status distribusi.');
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = '<ion-icon name="checkmark-outline" class="text-sm"></ion-icon><span>Simpan Status</span>';
            }
        }
    })
    .catch(err => {
        console.error(err);
        form.action = '<?= APP_URL ?>/paket/updateStatusDistribusi/<?= $paket['kode_paket'] ?: $paket['id'] ?>/' + distId + '?tab=distribusi#distribusi';
        form.method = 'POST';
        form.submit();
    });
}

/**
 * Buka Modal Edit Paket Pop Up di Halaman Detail
 */
function openDetailEditPaketModal() {
    window.ProModal.open('modalEditPaket');
}

/**
 * Buka Modal Skema Potongan Mandiri
 */
function openModalSkemaPotongan() {
    updatePotonganCalc();
    window.ProModal.open('modalSkemaPotongan');
}

/**
 * Logika Skema Potongan (Checkboxes, Dropdown, Perhitungan Live)
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
            if (!input.value || parseFloat(input.value) === 0) input.value = 13;
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
    if (prefix === 'potongan') updatePotonganCalc();
    else updateEditCalc();
}

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
            if (!input.value || parseFloat(input.value) === 0) input.value = 2;
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
    if (prefix === 'potongan') updatePotonganCalc();
    else updateEditCalc();
}

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
    if (prefix === 'potongan') updatePotonganCalc();
    else updateEditCalc();
}

/**
 * Perhitungan Live Standalone Modal Skema Potongan
 */
function updatePotonganCalc() {
    const acuan = <?= (float)$acuan ?>;
    const chkPasal = document.getElementById('potongan_ada_pasal');
    const adaPasal = chkPasal ? chkPasal.checked : true;
    const persenPasal = adaPasal ? (parseFloat(document.getElementById('potongan_persen_pasal').value) || 0) : 0;

    const chkAdmin = document.getElementById('potongan_ada_admin');
    const adaAdmin = chkAdmin ? chkAdmin.checked : true;
    const persenAdmin = adaAdmin ? (parseFloat(document.getElementById('potongan_persen_admin').value) || 0) : 0;

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

function updateEditCalc() {
    const paguInput = document.getElementById('edit_pagu');
    if (!paguInput) return;
    const pagu = parseRupiahNumber(paguInput.value);
    const negoInput = document.getElementById('edit_pagu_nego');
    const paguNego = negoInput ? parseRupiahNumber(negoInput.value) : 0;
    
    const chkPasal = document.getElementById('edit_ada_pasal');
    const adaPasal = chkPasal ? chkPasal.checked : true;
    const inPasal = document.getElementById('edit_persen_pasal');
    const persenPasal = (adaPasal && inPasal) ? (parseFloat(inPasal.value) || 0) : 0;
    
    const chkAdmin = document.getElementById('edit_ada_admin');
    const adaAdmin = chkAdmin ? chkAdmin.checked : true;
    const inAdmin = document.getElementById('edit_persen_admin');
    const persenAdmin = (adaAdmin && inAdmin) ? (parseFloat(inAdmin.value) || 0) : 0;

    const acuan = (paguNego > 0) ? paguNego : pagu;
    const dpp = acuan > 0 ? Math.round(acuan / 1.11) : 0;
    const chkKontrak = document.getElementById('edit_tipe_potongan');
    const isKontrak = chkKontrak ? chkKontrak.checked : false;
    const basis = isKontrak ? acuan : dpp;

    const pasal = Math.round(basis * (persenPasal / 100));
    const admin = Math.round(basis * (persenAdmin / 100));
    const totalPasal = pasal + admin;
    const totalPersen = Math.round((persenPasal + persenAdmin) * 100) / 100;

    const elAcuan = document.getElementById('edit_preview_acuan');
    if (elAcuan) elAcuan.textContent = formatRupiahString(acuan);
    const elDpp = document.getElementById('edit_preview_dpp');
    if (elDpp) elDpp.textContent = formatRupiahString(dpp);
    const elPasal = document.getElementById('edit_preview_pasal13');
    if (elPasal) elPasal.textContent = formatRupiahString(pasal);
    const elAdmin = document.getElementById('edit_preview_admin2');
    if (elAdmin) elAdmin.textContent = formatRupiahString(admin);
    const elTotal = document.getElementById('edit_preview_total_pasal');
    if (elTotal) elTotal.textContent = formatRupiahString(totalPasal);

    const elLblP = document.getElementById('edit_lbl_pasal');
    if (elLblP) elLblP.textContent = persenPasal;
    const elLblA = document.getElementById('edit_lbl_admin');
    if (elLblA) elLblA.textContent = persenAdmin;
    const elLblTot = document.getElementById('edit_lbl_total_pasal');
    if (elLblTot) elLblTot.textContent = totalPersen;

    const lblSum = document.getElementById('edit_lbl_sum_persen');
    if (lblSum) lblSum.textContent = totalPersen + '%';
}

/**
 * Quick Tambah Group & Skema Helpers
 */
let targetGroupSelectId = null;
let targetSkemaPrefix = 'potongan';

function openQuickAddGroup(selectId = null) {
    targetGroupSelectId = selectId;
    window.ProModal.open('modalQuickTambahGroup');
}

function handleQuickTambahGroup(e) {
    e.preventDefault();
    const btn = document.getElementById('btnSubmitQuickGroup');
    const nama = document.getElementById('quick_nama_group').value.trim();
    const kode = document.getElementById('quick_kode_group').value.trim();
    if (!nama) return;

    btn.disabled = true;
    btn.innerHTML = SPINNER_SVG + ' <span>Menyimpan...</span>';

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
            const sel = document.getElementById('edit_group_id');
            if (sel) {
                const opt = document.createElement('option');
                opt.value = data.group.id;
                opt.textContent = data.group.nama_group;
                sel.appendChild(opt);
                sel.value = data.group.id;
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
    btn.innerHTML = SPINNER_SVG + ' <span>Menyimpan...</span>';

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
            ['potongan_skema_id', 'edit_skema_id'].forEach(id => {
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
                    sel.value = sk.id;
                }
            });

            const activeSel = document.getElementById(targetSkemaPrefix + '_skema_id');
            if (activeSel) {
                onSkemaSelectChange(targetSkemaPrefix, activeSel);
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

// ==========================================
// LOGIKA TAB DOKUMENTASI (UPLOAD & GALERI)
// ==========================================
let currentDokDistId = null;
let dokSelectedFiles = [];
let isUploadingDok = false;

/**
 * Buka modal dokumentasi foto untuk alokasi distribusi tertentu
 */
function openModalDokumentasi(distId, namaTujuan, namaBarang, qty) {
    currentDokDistId = distId;

    const elTujuan = document.getElementById('modalDokTujuan');
    const elBarang = document.getElementById('modalDokBarang');
    if (elTujuan) elTujuan.textContent = namaTujuan;
    if (elBarang) elBarang.textContent = 'Barang: ' + namaBarang + ' (' + qty + ')';

    clearDokSelectedQueue();
    const ketInput = document.getElementById('dokKeteranganInput');
    if (ketInput) ketInput.value = '';

    window.ProModal.open('modalDokumentasi');
    loadDokumentasiPhotos(distId);
}

/**
 * Muat daftar foto dokumentasi dari server (AJAX)
 */
function loadDokumentasiPhotos(distId) {
    const loadingEl = document.getElementById('dokGalleryLoading');
    const emptyEl = document.getElementById('dokGalleryEmpty');
    const gridEl = document.getElementById('dokGalleryGrid');
    const countEl = document.getElementById('dokGalleryCount');

    if (loadingEl) loadingEl.classList.remove('hidden');
    if (emptyEl) emptyEl.classList.add('hidden');
    if (gridEl) {
        gridEl.classList.add('hidden');
        gridEl.innerHTML = '';
    }

    fetch('<?= APP_URL ?>/paket/getDokumentasiList/<?= $paket['kode_paket'] ?: $paket['id'] ?>/' + distId, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(r => r.json())
    .then(res => {
        if (loadingEl) loadingEl.classList.add('hidden');

        if (res.success) {
            const photos = res.photos || [];
            if (countEl) countEl.textContent = photos.length;

            if (photos.length === 0) {
                if (emptyEl) emptyEl.classList.remove('hidden');
            } else {
                if (gridEl) {
                    gridEl.classList.remove('hidden');
                    photos.forEach(p => {
                        const card = document.createElement('div');
                        card.className = 'group relative bg-slate-50 rounded-xl border border-slate-200 overflow-hidden shadow-2xs hover:shadow-md transition-all flex flex-col';
                        card.id = 'photo_card_' + p.id;

                        const imgUrl = p.url;
                        const thumbUrl = p.thumb_url || p.url;
                        const caption = (p.keterangan ? (p.keterangan + ' - ') : '') + (p.formatted_date || '');

                        card.innerHTML = `
                            <div class="relative aspect-square w-full overflow-hidden bg-slate-100 cursor-pointer" onclick="openPhotoLightbox('${imgUrl}', '${escapeHtml(caption)}')">
                                <img src="${thumbUrl}" alt="Dokumentasi" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-200" loading="lazy">
                                <div class="absolute inset-0 bg-black/30 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center text-white text-lg">
                                    <ion-icon name="search-outline"></ion-icon>
                                </div>
                            </div>
                            <div class="p-2 flex items-center justify-between gap-1 text-[10px] text-slate-500 bg-white border-t border-slate-100">
                                <div class="truncate">
                                    <span class="font-semibold text-slate-700 block truncate" title="${escapeHtml(p.nama_file_asli)}">${escapeHtml(p.nama_file_asli)}</span>
                                    <span class="text-[9px] text-slate-400 block">${p.formatted_size} &bull; ${p.formatted_date}</span>
                                </div>
                                <button type="button" onclick="deleteDokumentasiFoto(${p.id}, ${distId})" class="text-slate-400 hover:text-rose-600 p-1 rounded-md hover:bg-rose-50 transition-colors shrink-0" title="Hapus Foto">
                                    <ion-icon name="trash-outline" class="text-sm"></ion-icon>
                                </button>
                            </div>
                        `;
                        gridEl.appendChild(card);
                    });
                }
            }
        } else {
            if (emptyEl) emptyEl.classList.remove('hidden');
            window.ProToast.error(res.message || 'Gagal memuat galeri foto.');
        }
    })
    .catch(() => {
        if (loadingEl) loadingEl.classList.add('hidden');
        if (emptyEl) emptyEl.classList.remove('hidden');
        window.ProToast.error('Koneksi terputus saat memuat galeri.');
    });
}

function escapeHtml(str) {
    if (!str) return '';
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

/**
 * Handle Drag & Drop di Dropzone Foto
 */
function handleDokDrop(e) {
    e.preventDefault();
    const dropzone = document.getElementById('dokDropzone');
    if (dropzone) dropzone.classList.remove('border-teal-500', 'bg-teal-50/50');
    if (e.dataTransfer && e.dataTransfer.files) {
        handleDokFileSelect(e.dataTransfer.files);
    }
}

/**
 * Handle penambahan file baru ke antrean upload
 */
function handleDokFileSelect(files) {
    if (!files || files.length === 0) return;

    for (let i = 0; i < files.length; i++) {
        const f = files[i];
        if (f.type.startsWith('image/')) {
            dokSelectedFiles.push(f);
        } else {
            window.ProToast.warning('File ' + f.name + ' bukan gambar yang didukung.');
        }
    }

    renderDokQueuePreview();
}

/**
 * Render thumbnail preview untuk foto yang dipilih sebelum upload
 */
function renderDokQueuePreview() {
    const queueContainer = document.getElementById('dokSelectedQueue');
    const queueList = document.getElementById('dokQueueList');
    const countEl = document.getElementById('dokSelectedCount');
    const uploadBtn = document.getElementById('btnUploadDokumentasi');

    if (!queueContainer || !queueList) return;

    if (dokSelectedFiles.length === 0) {
        queueContainer.classList.add('hidden');
        queueList.innerHTML = '';
        if (uploadBtn) {
            uploadBtn.disabled = true;
            uploadBtn.className = 'px-4 py-2 bg-slate-200 text-slate-400 font-semibold rounded-xl text-xs shadow-xs transition-all cursor-not-allowed flex items-center gap-1.5';
        }
        return;
    }

    queueContainer.classList.remove('hidden');
    if (countEl) countEl.textContent = dokSelectedFiles.length;
    queueList.innerHTML = '';

    dokSelectedFiles.forEach((file, index) => {
        const item = document.createElement('div');
        item.className = 'relative flex items-center gap-2 p-1.5 bg-slate-50 border border-slate-200 rounded-lg overflow-hidden group';

        const img = document.createElement('img');
        img.className = 'w-10 h-10 rounded-md object-cover border border-slate-200 shrink-0';
        img.src = URL.createObjectURL(file);

        const info = document.createElement('div');
        info.className = 'truncate text-[10px] leading-tight flex-1';
        const sizeKb = Math.round(file.size / 1024);
        info.innerHTML = `<span class="font-bold text-slate-700 block truncate">${escapeHtml(file.name)}</span><span class="text-slate-400 font-mono">${sizeKb} KB</span>`;

        const delBtn = document.createElement('button');
        delBtn.type = 'button';
        delBtn.className = 'text-slate-400 hover:text-rose-600 p-1 rounded transition-colors';
        delBtn.title = 'Hapus dari antrean';
        delBtn.innerHTML = '<ion-icon name="close-circle" class="text-base"></ion-icon>';
        delBtn.onclick = () => removeDokQueueItem(index);

        item.appendChild(img);
        item.appendChild(info);
        item.appendChild(delBtn);
        queueList.appendChild(item);
    });

    if (uploadBtn) {
        uploadBtn.disabled = false;
        uploadBtn.className = 'px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white font-semibold rounded-xl text-xs shadow-xs transition-all cursor-pointer flex items-center gap-1.5 hover:scale-[1.02]';
    }
}

/**
 * Hapus satu item dari antrean upload
 */
function removeDokQueueItem(index) {
    if (index >= 0 && index < dokSelectedFiles.length) {
        dokSelectedFiles.splice(index, 1);
        renderDokQueuePreview();
    }
}

/**
 * Bersihkan seluruh antrean upload
 */
function clearDokSelectedQueue() {
    dokSelectedFiles = [];
    const input = document.getElementById('dokFileInput');
    if (input) input.value = '';
    renderDokQueuePreview();

    const progressContainer = document.getElementById('dokProgressContainer');
    if (progressContainer) progressContainer.classList.add('hidden');
}

/**
 * Client-Side Image Compression using HTML5 Canvas
 * Mengompres gambar jika resolusi > maxWidth atau ukuran besar
 */
function compressImageToBlob(file, maxWidth = 1600, quality = 0.82) {
    return new Promise((resolve) => {
        if (!file.type.match(/image\/(jpeg|jpg|png|webp)/i)) {
            return resolve(file); // fallback
        }

        const reader = new FileReader();
        reader.onload = function(e) {
            const img = new Image();
            img.onload = function() {
                let w = img.width;
                let h = img.height;

                if (w > maxWidth || h > maxWidth) {
                    if (w > h) {
                        h = Math.round((h * maxWidth) / w);
                        w = maxWidth;
                    } else {
                        w = Math.round((w * maxWidth) / h);
                        h = maxWidth;
                    }
                }

                const canvas = document.createElement('canvas');
                canvas.width = w;
                canvas.height = h;
                const ctx = canvas.getContext('2d');
                ctx.drawImage(img, 0, 0, w, h);

                canvas.toBlob((blob) => {
                    if (blob && blob.size < file.size) {
                        const compFile = new File([blob], file.name.replace(/\.[^/.]+$/, "") + ".jpg", {
                            type: 'image/jpeg',
                            lastModified: Date.now()
                        });
                        resolve(compFile);
                    } else {
                        resolve(file);
                    }
                }, 'image/jpeg', quality);
            };
            img.onerror = () => resolve(file);
            img.src = e.target.result;
        };
        reader.onerror = () => resolve(file);
        reader.readAsDataURL(file);
    });
}

/**
 * Eksekusi Upload Foto Dokumentasi dengan Progress Bar Real-time
 */
async function startUploadDokumentasi() {
    if (dokSelectedFiles.length === 0 || !currentDokDistId || isUploadingDok) return;
    isUploadingDok = true;

    const progressContainer = document.getElementById('dokProgressContainer');
    const progressBar = document.getElementById('dokProgressBar');
    const progressPercent = document.getElementById('dokProgressPercent');
    const progressStatus = document.getElementById('dokProgressStatusText');
    const uploadBtn = document.getElementById('btnUploadDokumentasi');
    const uploadBtnText = document.getElementById('btnUploadDokumentasiText');

    if (progressContainer) progressContainer.classList.remove('hidden');
    if (uploadBtn) {
        uploadBtn.disabled = true;
        uploadBtn.classList.add('opacity-75');
    }
    if (uploadBtnText) uploadBtnText.textContent = 'Memproses...';

    // 1. Fase kompresi client-side
    if (progressStatus) progressStatus.textContent = 'Mengompres gambar (' + dokSelectedFiles.length + ' foto)...';
    if (progressBar) progressBar.style.width = '15%';
    if (progressPercent) progressPercent.textContent = '15%';

    const formData = new FormData();
    formData.append('is_ajax', '1');
    formData.append('distribusi_id', currentDokDistId);
    const ketVal = (document.getElementById('dokKeteranganInput')?.value || '').trim();
    if (ketVal) formData.append('keterangan', ketVal);

    for (let i = 0; i < dokSelectedFiles.length; i++) {
        const comp = await compressImageToBlob(dokSelectedFiles[i], 1600, 0.82);
        formData.append('fotos[]', comp, dokSelectedFiles[i].name);
    }

    if (progressStatus) progressStatus.textContent = 'Mengunggah ke server...';
    if (progressBar) progressBar.style.width = '30%';
    if (progressPercent) progressPercent.textContent = '30%';

    // 2. Upload via AJAX XMLHttpRequest
    const xhr = new XMLHttpRequest();
    xhr.open('POST', '<?= APP_URL ?>/paket/uploadDokumentasi/<?= $paket['kode_paket'] ?: $paket['id'] ?>', true);
    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

    xhr.upload.onprogress = function(e) {
        if (e.lengthComputable) {
            const pct = 30 + Math.round((e.loaded / e.total) * 65); // 30% -> 95%
            if (progressBar) progressBar.style.width = pct + '%';
            if (progressPercent) progressPercent.textContent = pct + '%';
        }
    };

    xhr.onload = function() {
        isUploadingDok = false;
        if (progressBar) progressBar.style.width = '100%';
        if (progressPercent) progressPercent.textContent = '100%';
        if (progressStatus) progressStatus.textContent = 'Selesai!';

        setTimeout(() => {
            if (progressContainer) progressContainer.classList.add('hidden');
            if (uploadBtn) {
                uploadBtn.disabled = false;
                uploadBtn.classList.remove('opacity-75');
            }
            if (uploadBtnText) uploadBtnText.textContent = 'Mulai Upload Foto';
        }, 600);

        try {
            const res = JSON.parse(xhr.responseText);
            if (res.success) {
                clearDokSelectedQueue();
                const ketInput = document.getElementById('dokKeteranganInput');
                if (ketInput) ketInput.value = '';
                loadDokumentasiPhotos(currentDokDistId);
                refreshDokTableRow(currentDokDistId);
                window.ProToast.success(res.message || 'Foto dokumentasi berhasil diupload!');
            } else {
                window.ProToast.error(res.message || 'Gagal mengupload foto.');
            }
        } catch (err) {
            window.ProToast.error('Format respon server tidak valid.');
        }
    };

    xhr.onerror = function() {
        isUploadingDok = false;
        if (progressContainer) progressContainer.classList.add('hidden');
        if (uploadBtn) {
            uploadBtn.disabled = false;
            uploadBtn.classList.remove('opacity-75');
        }
        if (uploadBtnText) uploadBtnText.textContent = 'Mulai Upload Foto';
        window.ProToast.error('Koneksi terputus saat mengupload foto.');
    };

    xhr.send(formData);
}

/**
 * Hapus satu foto dokumentasi
 */
function deleteDokumentasiFoto(fotoId, distId) {
    if (!confirm('Yakin ingin menghapus foto dokumentasi ini? File yang dihapus tidak dapat dikembalikan.')) {
        return;
    }

    const card = document.getElementById('photo_card_' + fotoId);
    if (card) card.style.opacity = '0.4';

    fetch('<?= APP_URL ?>/paket/deleteDokumentasi/<?= $paket['kode_paket'] ?: $paket['id'] ?>/' + fotoId, {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            if (card) card.remove();
            loadDokumentasiPhotos(distId);
            refreshDokTableRow(distId);
            window.ProToast.success('Foto dokumentasi berhasil dihapus.');
        } else {
            if (card) card.style.opacity = '1';
            window.ProToast.error(res.message || 'Gagal menghapus foto.');
        }
    })
    .catch(() => {
        if (card) card.style.opacity = '1';
        window.ProToast.error('Koneksi terputus saat menghapus foto.');
    });
}

/**
 * Update cell tabel dokumentasi secara dinamis setelah upload/delete
 */
function refreshDokTableRow(distId) {
    fetch('<?= APP_URL ?>/paket/getDokumentasiList/<?= $paket['kode_paket'] ?: $paket['id'] ?>/' + distId, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            const cell = document.getElementById('cell_dok_photos_' + distId);
            const photos = res.photos || [];

            if (cell) {
                if (photos.length === 0) {
                    cell.innerHTML = `
                        <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-md text-xs font-medium bg-slate-100 text-slate-400 border border-slate-200/70">
                            <ion-icon name="image-outline" class="text-xs"></ion-icon>
                            <span>Belum Ada Foto</span>
                        </span>
                    `;
                } else {
                    const previewSlice = photos.slice(0, 3);
                    let imgsHtml = '<div class="flex items-center -space-x-1.5">';
                    previewSlice.forEach(p => {
                        const thumb = p.thumb_url || p.url;
                        const caption = (res.distribusi?.nama_tujuan || '') + ' - ' + (res.distribusi?.nama_komponen || 'Barang');
                        imgsHtml += `
                            <img src="${thumb}" alt="Foto" 
                                 onclick="openPhotoLightbox('${p.url}', '${escapeHtml(caption)}')"
                                 class="w-7 h-7 rounded-lg object-cover border-2 border-white shadow-2xs hover:scale-110 hover:z-10 transition-transform cursor-pointer"
                                 title="Klik untuk memperbesar">
                        `;
                    });
                    imgsHtml += '</div>';

                    cell.innerHTML = `
                        <div class="flex items-center gap-1.5">
                            ${imgsHtml}
                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[11px] font-bold bg-teal-50 text-teal-700 border border-teal-200/80">
                                ${photos.length} Foto
                            </span>
                        </div>
                    `;
                }
            }

            // Update badge total
            updateDokumentasiTotalBadge();
        }
    })
    .catch(() => {});
}

/**
 * Hitung dan perbarui angka badge dokumentasi di header tab
 */
function updateDokumentasiTotalBadge() {
    let totalPhotos = 0;
    let documentedRows = 0;
    const rows = document.querySelectorAll('#dokumentasiTable tbody tr[id^="row_dok_"]');
    const totalRows = rows.length;

    rows.forEach(r => {
        const badge = r.querySelector('[id^="cell_dok_photos_"] span.bg-teal-50');
        if (badge) {
            const num = parseInt(badge.textContent) || 0;
            totalPhotos += num;
            if (num > 0) documentedRows++;
        }
    });

    const headerBadge = document.getElementById('headerTotalFotoBadge');
    if (headerBadge) headerBadge.textContent = totalPhotos;

    const tabBadge = document.getElementById('tabBadge_dokumentasiCount');
    if (tabBadge) {
        tabBadge.textContent = documentedRows + '/' + totalRows;
        tabBadge.title = documentedRows + ' dari ' + totalRows + ' alokasi telah memiliki foto dokumentasi';
        if (totalRows > 0 && documentedRows >= totalRows) {
            tabBadge.className = 'px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-700';
        } else {
            tabBadge.className = 'px-2 py-0.5 rounded-full text-[10px] font-bold bg-teal-100 text-teal-800';
        }
    }

    const footerTotal = document.getElementById('footerTotalFotoCount');
    if (footerTotal) footerTotal.textContent = totalPhotos;
}

/**
 * Lightbox Full Image Preview
 */
function openPhotoLightbox(url, caption) {
    const modal = document.getElementById('modalPhotoLightbox');
    const img = document.getElementById('lightboxImg');
    const cap = document.getElementById('lightboxCaption');

    if (img) img.src = url;
    if (cap) cap.textContent = caption || '';
    if (modal) {
        modal.classList.remove('hidden', 'opacity-0', 'pointer-events-none');
        modal.classList.add('opacity-100');
    }
}

function closePhotoLightbox() {
    const modal = document.getElementById('modalPhotoLightbox');
    if (modal) {
        modal.classList.add('opacity-0', 'pointer-events-none');
        setTimeout(() => modal.classList.add('hidden'), 200);
    }
}

// Tutup lightbox jika tekan tombol Escape
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closePhotoLightbox();
    }
});

/**
 * Buka Modal Ubah Status Pencairan (Popup Mandiri Detail)
 */
function openStatusPencairanModal(p) {
    if (!p) return;
    const form = document.getElementById('formStatusPencairan');
    form.action = '<?= APP_URL ?>/paket/updateStatus/' + (p.kode_paket || p.id);

    const subtitle = document.getElementById('status_pencairan_subtitle');
    if (subtitle) {
        subtitle.textContent = (p.nama_paket || '') + (p.kode_paket ? ' [' + p.kode_paket + ']' : '');
    }

    const st = (p.status || 'Proses').toLowerCase();
    const optBelum = document.getElementById('status_opt_belum');
    const optProses = document.getElementById('status_opt_proses');
    const optSelesai = document.getElementById('status_opt_selesai');

    if (st === 'belum' && optBelum) {
        optBelum.checked = true;
    } else if ((st === 'selesai' || st === 'cair') && optSelesai) {
        optSelesai.checked = true;
    } else if (optProses) {
        optProses.checked = true;
    }

    window.ProModal.open('modalStatusPencairan');
}
</script>
