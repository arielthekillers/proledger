<!-- ========================================== -->
<!-- 6. JAVASCRIPT LOGIC                        -->
<!-- ========================================== -->
<script>
/**
 * Tab Switcher (info, items, distribusi, dokumen)
 */
function switchDetailTab(tabName) {
    const tabs = ['info', 'items', 'distribusi', 'dokumen'];
    tabs.forEach(t => {
        const el = document.getElementById('tabContent_' + t);
        const btn = document.getElementById('tabBtn_' + t);
        if (t === tabName) {
            if (el) el.classList.remove('hidden');
            if (btn) {
                btn.className = 'tab-btn py-3 px-4 text-xs font-bold border-b-2 border-blue-600 text-blue-600 flex items-center gap-2 transition-all';
            }
        } else {
            if (el) el.classList.add('hidden');
            if (btn) {
                btn.className = 'tab-btn py-3 px-4 text-xs font-semibold border-b-2 border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300 flex items-center gap-2 transition-all';
            }
        }
    });

    window.location.hash = tabName;
}

// Check initial hash on load
window.addEventListener('DOMContentLoaded', () => {
    const h = window.location.hash.replace('#', '');
    if (h === 'items' || h === 'distribusi' || h === 'dokumen') {
        switchDetailTab(h);
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
    btn.innerHTML = '<ion-icon name="sync-outline" class="animate-spin text-sm"></ion-icon> <span>Menyimpan...</span>';

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
 * Update Rute Pengiriman Pop Up
 */
function openModalUpdateRute(dist) {
    if (!dist) return;
    document.getElementById('rute_dist_id').value = dist.id;
    document.getElementById('rute_modal_tujuan').textContent = dist.nama_tujuan || '-';
    document.getElementById('rute_modal_item').textContent = (dist.nama_komponen || '-') + ' (' + (parseFloat(dist.jumlah) || 1) + ' ' + (dist.satuan || 'Unit') + ')';

    const rute = (dist.rute_pengiriman || 'kantor').toLowerCase();
    const optKantor = document.getElementById('rute_opt_kantor');
    const optGudang = document.getElementById('rute_opt_gudang');
    const optLangsung = document.getElementById('rute_opt_langsung');

    if (optKantor) optKantor.checked = (rute === 'kantor');
    if (optGudang) optGudang.checked = (rute === 'gudang');
    if (optLangsung) optLangsung.checked = (rute === 'langsung');

    window.ProModal.open('modalUpdateRute');
}

function submitUpdateRute(e) {
    e.preventDefault();
    const distId = document.getElementById('rute_dist_id').value;
    const form = document.getElementById('formUpdateRute');
    const formData = new FormData(form);
    const btn = document.getElementById('btnSubmitRute');

    if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<span class="inline-block animate-spin mr-1">⌛</span> Menyimpan...';
    }

    fetch('<?= APP_URL ?>/paket/updateRuteDistribusi/<?= $paket['kode_paket'] ?: $paket['id'] ?>/' + distId, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            window.ProToast?.show?.(data.message || 'Rute berhasil disimpan!', 'success');
            window.location.hash = 'distribusi';
            window.location.reload();
        } else {
            alert(data.message || 'Gagal menyimpan rute.');
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = '<ion-icon name="checkmark-outline" class="text-sm"></ion-icon><span>Simpan Rute</span>';
            }
        }
    })
    .catch(err => {
        console.error(err);
        form.action = '<?= APP_URL ?>/paket/updateRuteDistribusi/<?= $paket['kode_paket'] ?: $paket['id'] ?>/' + distId;
        form.method = 'POST';
        form.submit();
    });
}

/**
 * Update Status Pemesanan Pop Up
 */
function openModalUpdatePemesanan(dist) {
    if (!dist) return;
    document.getElementById('pemesanan_dist_id').value = dist.id;
    document.getElementById('pemesanan_modal_tujuan').textContent = dist.nama_tujuan || '-';
    document.getElementById('pemesanan_modal_item').textContent = (dist.nama_komponen || '-') + ' (' + (parseFloat(dist.jumlah) || 1) + ' ' + (dist.satuan || 'Unit') + ')';

    document.getElementById('pemesanan_status').value = dist.status_pemesanan || 'Belum Dipesan';
    document.getElementById('pemesanan_tanggal').value = dist.tanggal_pesan || '';
    document.getElementById('pemesanan_kurir').value = dist.kurir_ekspedisi || '';
    document.getElementById('pemesanan_resi').value = dist.no_resi || '';
    document.getElementById('pemesanan_link').value = dist.link_pembelian || '';

    window.ProModal.open('modalUpdatePemesanan');
}

function submitUpdatePemesanan(e) {
    e.preventDefault();
    const distId = document.getElementById('pemesanan_dist_id').value;
    const form = document.getElementById('formUpdatePemesanan');
    const formData = new FormData(form);
    const btn = document.getElementById('btnSubmitPemesanan');

    if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<span class="inline-block animate-spin mr-1">⌛</span> Menyimpan...';
    }

    fetch('<?= APP_URL ?>/paket/updatePemesananDistribusi/<?= $paket['kode_paket'] ?: $paket['id'] ?>/' + distId, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            window.ProToast?.show?.(data.message || 'Status pemesanan berhasil diperbarui!', 'success');
            window.location.hash = 'distribusi';
            window.location.reload();
        } else {
            alert(data.message || 'Gagal menyimpan status pemesanan.');
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = '<ion-icon name="checkmark-outline" class="text-sm"></ion-icon><span>Simpan Status</span>';
            }
        }
    })
    .catch(err => {
        console.error(err);
        form.action = '<?= APP_URL ?>/paket/updatePemesananDistribusi/<?= $paket['kode_paket'] ?: $paket['id'] ?>/' + distId;
        form.method = 'POST';
        form.submit();
    });
}

/**
 * Update Status Penerimaan Pop Up
 */
function openModalUpdatePenerimaan(dist) {
    if (!dist) return;
    document.getElementById('penerimaan_dist_id').value = dist.id;
    document.getElementById('penerimaan_modal_tujuan').textContent = dist.nama_tujuan || '-';
    document.getElementById('penerimaan_modal_item').textContent = (dist.nama_komponen || '-') + ' (' + (parseFloat(dist.jumlah) || 1) + ' ' + (dist.satuan || 'Unit') + ')';

    const statusPem = dist.status_penerimaan || 'Menunggu Barang';
    document.getElementById('penerimaan_status').value = statusPem;
    document.getElementById('penerimaan_tanggal').value = dist.tanggal_terima || '';
    document.getElementById('penerimaan_catatan').value = dist.catatan || '';

    if (statusPem === 'Diterima di Tujuan' && !dist.tanggal_terima) {
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('penerimaan_tanggal').value = today;
    }

    window.ProModal.open('modalUpdatePenerimaan');
}

function onStatusPenerimaanChange(val) {
    const tglInput = document.getElementById('penerimaan_tanggal');
    if (val === 'Diterima di Tujuan' && tglInput && !tglInput.value) {
        tglInput.value = new Date().toISOString().split('T')[0];
    }
}

function submitUpdatePenerimaan(e) {
    e.preventDefault();
    const distId = document.getElementById('penerimaan_dist_id').value;
    const form = document.getElementById('formUpdatePenerimaan');
    const formData = new FormData(form);
    const btn = document.getElementById('btnSubmitPenerimaan');

    if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<span class="inline-block animate-spin mr-1">⌛</span> Menyimpan...';
    }

    fetch('<?= APP_URL ?>/paket/updatePenerimaanDistribusi/<?= $paket['kode_paket'] ?: $paket['id'] ?>/' + distId, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            window.ProToast?.show?.(data.message || 'Status penerimaan berhasil diperbarui!', 'success');
            window.location.hash = 'distribusi';
            window.location.reload();
        } else {
            alert(data.message || 'Gagal menyimpan status penerimaan.');
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = '<ion-icon name="checkmark-outline" class="text-sm"></ion-icon><span>Simpan Status</span>';
            }
        }
    })
    .catch(err => {
        console.error(err);
        form.action = '<?= APP_URL ?>/paket/updatePenerimaanDistribusi/<?= $paket['kode_paket'] ?: $paket['id'] ?>/' + distId;
        form.method = 'POST';
        form.submit();
    });
}

/**
 * Buka Modal Edit Paket Pop Up di Halaman Detail
 */
function openDetailEditPaketModal() {
    updateEditCalc();
    window.ProModal.open('modalEditPaket');
}

/**
 * Logika Edit Paket (Checkboxes, Dropdown, Perhitungan Live)
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
    updateEditCalc();
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
    updateEditCalc();
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
    updateEditCalc();
}

function updateEditCalc() {
    const pagu = parseRupiahNumber(document.getElementById('edit_pagu').value);
    const paguNego = parseRupiahNumber(document.getElementById('edit_pagu_nego').value);
    
    const chkPasal = document.getElementById('edit_ada_pasal');
    const adaPasal = chkPasal ? chkPasal.checked : true;
    const persenPasal = adaPasal ? (parseFloat(document.getElementById('edit_persen_pasal').value) || 0) : 0;
    
    const chkAdmin = document.getElementById('edit_ada_admin');
    const adaAdmin = chkAdmin ? chkAdmin.checked : true;
    const persenAdmin = adaAdmin ? (parseFloat(document.getElementById('edit_persen_admin').value) || 0) : 0;

    const acuan = (paguNego > 0) ? paguNego : pagu;
    const dpp = acuan > 0 ? Math.round(acuan / 1.11) : 0;
    const chkKontrak = document.getElementById('edit_tipe_potongan');
    const isKontrak = chkKontrak ? chkKontrak.checked : false;
    const basis = isKontrak ? acuan : dpp;

    const pasal = Math.round(basis * (persenPasal / 100));
    const admin = Math.round(basis * (persenAdmin / 100));
    const totalPasal = pasal + admin;
    const totalPersen = Math.round((persenPasal + persenAdmin) * 100) / 100;

    document.getElementById('edit_preview_acuan').textContent = formatRupiahString(acuan);
    document.getElementById('edit_preview_dpp').textContent = formatRupiahString(dpp);
    document.getElementById('edit_preview_pasal13').textContent = formatRupiahString(pasal);
    document.getElementById('edit_preview_admin2').textContent = formatRupiahString(admin);
    document.getElementById('edit_preview_total_pasal').textContent = formatRupiahString(totalPasal);

    document.getElementById('edit_lbl_pasal').textContent = persenPasal;
    document.getElementById('edit_lbl_admin').textContent = persenAdmin;
    document.getElementById('edit_lbl_total_pasal').textContent = totalPersen;

    const lblSum = document.getElementById('edit_lbl_sum_persen');
    if (lblSum) lblSum.textContent = totalPersen + '%';
}

/**
 * Quick Tambah Group & Skema Helpers
 */
let targetGroupSelectId = null;
let targetSkemaPrefix = 'edit';

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
    btn.innerHTML = '<ion-icon name="sync-outline" class="animate-spin text-sm"></ion-icon> <span>Menyimpan...</span>';

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

function openQuickAddSkema(prefix = 'edit') {
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

function promptSaveCurrentAsSkema(prefix = 'edit') {
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
    btn.innerHTML = '<ion-icon name="sync-outline" class="animate-spin text-sm"></ion-icon> <span>Menyimpan...</span>';

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
            const sel = document.getElementById('edit_skema_id');
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
                onSkemaSelectChange('edit', sel);
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
</script>
