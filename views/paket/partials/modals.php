<!-- ========================================== -->
<!-- MODAL: UPLOAD BAST                          -->
<!-- ========================================== -->
<div id="modalUploadDokumen" class="app-modal fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 transition-all duration-200 opacity-0 pointer-events-none hidden">
    <div class="modal-card bg-white rounded-2xl shadow-2xl border border-slate-100 max-w-lg w-full transform scale-95 opacity-0 transition-all duration-200 overflow-hidden max-h-[92vh] flex flex-col">
        <div class="px-5 py-4 bg-slate-50 border-b border-slate-100 flex items-center justify-between shrink-0">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-amber-600 text-white flex items-center justify-center text-lg shadow-xs">
                    <ion-icon name="cloud-upload-outline"></ion-icon>
                </div>
                <div>
                    <h3 class="font-bold text-slate-800 text-sm" id="modalUploadTitle">Upload Berkas BAST</h3>
                    <p class="text-[11px] text-slate-400" id="modalUploadSubtitle">Upload lembar scan/foto BAST yang telah ditandatangani.</p>
                </div>
            </div>
            <button type="button" onclick="window.ProModal.close('modalUploadDokumen')" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg">
                <ion-icon name="close-outline" class="text-xl"></ion-icon>
            </button>
        </div>

        <form action="<?= APP_URL ?>/paket/storeDokumen/<?= $paket['kode_paket'] ?: $paket['id'] ?>" method="POST" enctype="multipart/form-data" class="overflow-y-auto p-5 space-y-4">
            <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
            <input type="hidden" name="jenis_dokumen" id="upload_jenis_dokumen" value="BAST">

            <!-- Nama / Judul Dokumen BAST -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">
                    Nama Dokumen BAST <span class="text-rose-500">*</span>
                </label>
                <input type="text" name="nama_dokumen" id="upload_nama_dokumen" required
                       placeholder="Misal: BAST SMAN 2 Bontang"
                       class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 transition-all font-semibold">
            </div>

            <!-- Berkas File -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1.5">
                    Pilih File Hasil Scan / Foto BAST <span class="text-rose-500">*</span>
                </label>
                <input type="file" name="file_dokumen" required
                       accept=".pdf,.jpg,.jpeg,.png,.webp"
                       class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:border-amber-500 transition-all file:mr-3 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-amber-100 file:text-amber-800 hover:file:bg-amber-200 cursor-pointer">
                <p class="text-[10px] text-slate-400 mt-1">Mendukung format: PDF, JPG, JPEG, PNG, WEBP. Maksimal 25MB.</p>
            </div>

            <!-- Keterangan / Catatan -->
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">
                    Catatan / Keterangan (Opsional)
                </label>
                <textarea name="keterangan" id="upload_keterangan" rows="2"
                          placeholder="Misal: Sudah ditandatangani basah oleh Kepala Sekolah dan bermaterai."
                          class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:border-amber-500 transition-all resize-none"></textarea>
            </div>

            <!-- Buttons -->
            <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2.5">
                <button type="button" onclick="window.ProModal.close('modalUploadDokumen')"
                        class="px-4 py-2 border border-slate-200 text-slate-600 hover:bg-slate-50 rounded-xl text-xs font-semibold transition-colors">
                    Batal
                </button>
                <button type="submit"
                        class="px-5 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-xl text-xs font-semibold shadow-sm transition-all flex items-center gap-1.5">
                    <ion-icon name="cloud-upload-outline"></ion-icon>
                    <span>Upload BAST</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================== -->
<!-- MODAL 1: TAMBAH ITEM SPESIFIKASI           -->
<!-- ========================================== -->
<div id="modalTambahItem" class="app-modal fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 transition-all duration-200 opacity-0 pointer-events-none hidden">
    <div class="modal-card bg-white rounded-2xl shadow-2xl border border-slate-100 max-w-xl w-full transform scale-95 opacity-0 transition-all duration-200 overflow-hidden max-h-[92vh] flex flex-col">
        <div class="px-5 py-4 bg-slate-50 border-b border-slate-100 flex items-center justify-between shrink-0">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-blue-600 text-white flex items-center justify-center text-lg shadow-xs">
                    <ion-icon name="cube-outline"></ion-icon>
                </div>
                <div>
                    <h3 class="font-bold text-slate-800 text-sm">Tambah Item / Spesifikasi</h3>
                    <p class="text-[11px] text-slate-400">Tambahkan barang ke paket: <?= htmlspecialchars($paket['nama_paket']) ?></p>
                </div>
            </div>
            <button type="button" data-modal-close="modalTambahItem" class="p-1.5 text-slate-400 hover:text-slate-700 rounded-lg">
                <ion-icon name="close-outline" class="text-xl"></ion-icon>
            </button>
        </div>

        <form method="POST" action="<?= APP_URL ?>/paket/storeItem/<?= $paket['kode_paket'] ?: $paket['id'] ?>" class="p-6 overflow-y-auto space-y-4 flex-1">
            <?= csrfField() ?>

            <!-- Nama Komponen -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Komponen / Barang <span class="text-red-500">*</span></label>
                <input type="text" name="komponen" id="tambah_item_komponen" required 
                       class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-blue-500 text-xs focus:bg-white transition-all"
                       placeholder="Contoh: Tenda Lipat, Mesin Pompa Air, dsb.">
            </div>

            <!-- Spesifikasi Teknis -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Spesifikasi Lengkap</label>
                <textarea name="spesifikasi" id="tambah_item_spesifikasi" rows="2.5"
                          class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-blue-500 text-xs focus:bg-white transition-all leading-relaxed"
                          placeholder="Ukuran, bahan, kapasitas, warna, tipe, atau keterangan teknis lainnya..."></textarea>
            </div>

            <!-- Satuan, Jumlah, Harga Satuan Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <!-- Satuan -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Satuan</label>
                    <select name="satuan" id="tambah_item_satuan" onchange="toggleCustomSatuan(this, 'tambah_item_satuan_custom')"
                            class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-blue-500 text-xs focus:bg-white transition-all font-medium">
                        <optgroup label="Barang & Peralatan Umum">
                            <option value="Unit" selected>Unit</option>
                            <option value="Buah">Buah</option>
                            <option value="Set">Set</option>
                            <option value="Paket">Paket</option>
                            <option value="Pcs">Pcs / Buah Kecil</option>
                            <option value="Pasang">Pasang</option>
                        </optgroup>
                        <optgroup label="ATK, Cetakan & Kertas">
                            <option value="Rim">Rim</option>
                            <option value="Kotak">Kotak / Box</option>
                            <option value="Dus">Dus / Karton</option>
                            <option value="Pak">Pak / Pack</option>
                            <option value="Buku">Buku / Eksemplar</option>
                            <option value="Lembar">Lembar</option>
                            <option value="Roll">Roll / Gulung</option>
                        </optgroup>
                        <optgroup label="Material & Bangunan">
                            <option value="Meter">Meter (m)</option>
                            <option value="Meter Persegi">Meter Persegi (m²)</option>
                            <option value="Meter Kubik">Meter Kubik (m³)</option>
                            <option value="Batang">Batang</option>
                            <option value="Kg">Kilogram (Kg)</option>
                            <option value="Zak">Zak / Sak</option>
                            <option value="Ton">Ton</option>
                        </optgroup>
                        <optgroup label="Cairan & Farmasi">
                            <option value="Liter">Liter</option>
                            <option value="Kaleng">Kaleng / Can</option>
                            <option value="Botol">Botol</option>
                            <option value="Jerigen">Jerigen / Galon</option>
                        </optgroup>
                        <optgroup label="Jasa / Layanan">
                            <option value="Orang Hari">Orang Hari (OH)</option>
                            <option value="Orang Bulan">Orang Bulan (OB)</option>
                            <option value="Kegiatan">Kegiatan / Kali</option>
                            <option value="Bulan">Bulan</option>
                        </optgroup>
                        <option value="CUSTOM">Lainnya (Ketik Manual)...</option>
                    </select>
                    <input type="text" name="satuan_custom" id="tambah_item_satuan_custom" placeholder="Ketik satuan manual..."
                           class="w-full mt-1.5 px-3 py-1.5 bg-white border border-blue-300 rounded-lg text-xs hidden focus:outline-none focus:border-blue-500">
                </div>

                <!-- Jumlah / Qty -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Jumlah (Qty) <span class="text-red-500">*</span></label>
                    <input type="number" step="any" min="0.01" name="jumlah" id="tambah_item_jumlah" value="1" required oninput="calcItemTotal('tambah')"
                           class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-blue-500 text-xs font-mono font-bold text-slate-800 focus:bg-white transition-all text-center">
                </div>

                <!-- Harga Satuan -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Harga Satuan <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <span class="absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-400 font-semibold text-xs">Rp</span>
                        <input type="text" name="harga_satuan" id="tambah_item_harga" required oninput="formatRupiahInput(this); calcItemTotal('tambah')"
                               class="w-full pl-8 pr-2.5 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-blue-500 text-xs font-mono font-medium focus:bg-white transition-all"
                               placeholder="1.689.905">
                    </div>
                </div>
            </div>

            <!-- Live Total Preview Box -->
            <div class="p-3 bg-blue-50/70 rounded-xl border border-blue-200/70 flex items-center justify-between text-xs">
                <span class="text-blue-900 font-semibold flex items-center gap-1.5">
                    <ion-icon name="calculator-outline" class="text-base text-blue-600"></ion-icon>
                    <span>Total Estimasi Harga Item:</span>
                </span>
                <span class="font-extrabold font-mono text-blue-800 text-sm" id="tambah_item_preview_total">Rp 0</span>
            </div>

            <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2 shrink-0">
                <button type="button" data-modal-close="modalTambahItem" 
                        class="px-3.5 py-2 rounded-xl border border-slate-200 text-slate-600 font-semibold hover:bg-slate-50 text-xs transition-colors">
                    Batal
                </button>
                <button type="submit" 
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl text-xs shadow-xs transition-all flex items-center gap-1.5">
                    <ion-icon name="checkmark-outline" class="text-sm"></ion-icon>
                    <span>Simpan Item</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================== -->
<!-- MODAL 2: EDIT ITEM SPESIFIKASI             -->
<!-- ========================================== -->
<div id="modalEditItem" class="app-modal fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 transition-all duration-200 opacity-0 pointer-events-none hidden">
    <div class="modal-card bg-white rounded-2xl shadow-2xl border border-slate-100 max-w-xl w-full transform scale-95 opacity-0 transition-all duration-200 overflow-hidden max-h-[92vh] flex flex-col">
        <div class="px-5 py-4 bg-slate-50 border-b border-slate-100 flex items-center justify-between shrink-0">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-amber-500 text-white flex items-center justify-center text-lg shadow-xs">
                    <ion-icon name="create-outline"></ion-icon>
                </div>
                <div>
                    <h3 class="font-bold text-slate-800 text-sm">Edit Item / Spesifikasi</h3>
                    <p class="text-[11px] text-slate-400" id="editItemSubtitle">Perbarui rincian item pengadaan</p>
                </div>
            </div>
            <button type="button" data-modal-close="modalEditItem" class="p-1.5 text-slate-400 hover:text-slate-700 rounded-lg">
                <ion-icon name="close-outline" class="text-xl"></ion-icon>
            </button>
        </div>

        <form id="formEditItem" method="POST" action="" class="p-6 overflow-y-auto space-y-4 flex-1">
            <?= csrfField() ?>

            <!-- Nama Komponen -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Komponen / Barang <span class="text-red-500">*</span></label>
                <input type="text" name="komponen" id="edit_item_komponen" required 
                       class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-amber-500 text-xs focus:bg-white transition-all">
            </div>

            <!-- Spesifikasi Teknis -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Spesifikasi Lengkap</label>
                <textarea name="spesifikasi" id="edit_item_spesifikasi" rows="2.5"
                          class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-amber-500 text-xs focus:bg-white transition-all leading-relaxed"></textarea>
            </div>

            <!-- Satuan, Jumlah, Harga Satuan Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <!-- Satuan -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Satuan</label>
                    <select name="satuan" id="edit_item_satuan" onchange="toggleCustomSatuan(this, 'edit_item_satuan_custom')"
                            class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-amber-500 text-xs focus:bg-white transition-all font-medium">
                        <optgroup label="Barang & Peralatan Umum">
                            <option value="Unit">Unit</option>
                            <option value="Buah">Buah</option>
                            <option value="Set">Set</option>
                            <option value="Paket">Paket</option>
                            <option value="Pcs">Pcs / Buah Kecil</option>
                            <option value="Pasang">Pasang</option>
                        </optgroup>
                        <optgroup label="ATK, Cetakan & Kertas">
                            <option value="Rim">Rim</option>
                            <option value="Kotak">Kotak / Box</option>
                            <option value="Dus">Dus / Karton</option>
                            <option value="Pak">Pak / Pack</option>
                            <option value="Buku">Buku / Eksemplar</option>
                            <option value="Lembar">Lembar</option>
                            <option value="Roll">Roll / Gulung</option>
                        </optgroup>
                        <optgroup label="Material & Bangunan">
                            <option value="Meter">Meter (m)</option>
                            <option value="Meter Persegi">Meter Persegi (m²)</option>
                            <option value="Meter Kubik">Meter Kubik (m³)</option>
                            <option value="Batang">Batang</option>
                            <option value="Kg">Kilogram (Kg)</option>
                            <option value="Zak">Zak / Sak</option>
                            <option value="Ton">Ton</option>
                        </optgroup>
                        <optgroup label="Cairan & Farmasi">
                            <option value="Liter">Liter</option>
                            <option value="Kaleng">Kaleng / Can</option>
                            <option value="Botol">Botol</option>
                            <option value="Jerigen">Jerigen / Galon</option>
                        </optgroup>
                        <optgroup label="Jasa / Layanan">
                            <option value="Orang Hari">Orang Hari (OH)</option>
                            <option value="Orang Bulan">Orang Bulan (OB)</option>
                            <option value="Kegiatan">Kegiatan / Kali</option>
                            <option value="Bulan">Bulan</option>
                        </optgroup>
                        <option value="CUSTOM">Lainnya (Ketik Manual)...</option>
                    </select>
                    <input type="text" name="satuan_custom" id="edit_item_satuan_custom" placeholder="Ketik satuan manual..."
                           class="w-full mt-1.5 px-3 py-1.5 bg-white border border-amber-300 rounded-lg text-xs hidden focus:outline-none focus:border-amber-500">
                </div>

                <!-- Jumlah / Qty -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Jumlah (Qty) <span class="text-red-500">*</span></label>
                    <input type="number" step="any" min="0.01" name="jumlah" id="edit_item_jumlah" required oninput="calcItemTotal('edit')"
                           class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-amber-500 text-xs font-mono font-bold text-slate-800 focus:bg-white transition-all text-center">
                </div>

                <!-- Harga Satuan -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Harga Satuan <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <span class="absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-400 font-semibold text-xs">Rp</span>
                        <input type="text" name="harga_satuan" id="edit_item_harga" required oninput="formatRupiahInput(this); calcItemTotal('edit')"
                               class="w-full pl-8 pr-2.5 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-amber-500 text-xs font-mono font-medium focus:bg-white transition-all">
                    </div>
                </div>
            </div>

            <!-- Live Total Preview Box -->
            <div class="p-3 bg-amber-50/70 rounded-xl border border-amber-200/70 flex items-center justify-between text-xs">
                <span class="text-amber-900 font-semibold flex items-center gap-1.5">
                    <ion-icon name="calculator-outline" class="text-base text-amber-600"></ion-icon>
                    <span>Total Estimasi Harga Item:</span>
                </span>
                <span class="font-extrabold font-mono text-amber-800 text-sm" id="edit_item_preview_total">Rp 0</span>
            </div>

            <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2 shrink-0">
                <button type="button" data-modal-close="modalEditItem" 
                        class="px-3.5 py-2 rounded-xl border border-slate-200 text-slate-600 font-semibold hover:bg-slate-50 text-xs transition-colors">
                    Batal
                </button>
                <button type="submit" 
                        class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white font-semibold rounded-xl text-xs shadow-xs transition-all flex items-center gap-1.5">
                    <ion-icon name="checkmark-outline" class="text-sm"></ion-icon>
                    <span>Simpan Perubahan</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================== -->
<!-- MODAL 3: TAMBAH DISTRIBUSI BARANG          -->
<!-- ========================================== -->
<div id="modalTambahDistribusi" class="app-modal fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 transition-all duration-200 opacity-0 pointer-events-none hidden">
    <div class="modal-card bg-white rounded-2xl shadow-2xl border border-slate-100 max-w-2xl w-full transform scale-95 opacity-0 transition-all duration-200 overflow-hidden max-h-[92vh] flex flex-col">
        <div class="px-5 py-4 bg-slate-50 border-b border-slate-100 flex items-center justify-between shrink-0">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-indigo-600 text-white flex items-center justify-center text-lg shadow-xs">
                    <ion-icon name="navigate-outline"></ion-icon>
                </div>
                <div>
                    <h3 class="font-bold text-slate-800 text-sm" id="tambah_dist_modal_title">Tambah Alokasi Distribusi</h3>
                    <p class="text-[11px] text-slate-400" id="tambah_dist_modal_subtitle">Pilih tujuan penerima dan tentukan jumlah barang</p>
                </div>
            </div>
            <button type="button" data-modal-close="modalTambahDistribusi" class="p-1.5 text-slate-400 hover:text-slate-700 rounded-lg">
                <ion-icon name="close-outline" class="text-xl"></ion-icon>
            </button>
        </div>

        <form method="POST" action="<?= APP_URL ?>/paket/storeDistribusi/<?= $paket['kode_paket'] ?: $paket['id'] ?>" class="p-5 overflow-y-auto space-y-4 flex-1" id="formTambahDistribusi">
            <?= csrfField() ?>
            <input type="hidden" name="lokasi_id" id="tambah_dist_lokasi_id" value="" required>
            <input type="hidden" name="nama_tujuan" id="tambah_dist_nama_tujuan" value="">
            <input type="hidden" name="redirect_tab" id="tambah_dist_redirect_tab" value="distribusi">
            <input type="hidden" name="item_id" id="tambah_dist_item_id_hidden" value="">

            <!-- Dropdown Pilihan Item (Hanya tampil jika dibuka dari tab Distribusi Umum) -->
            <div id="tambah_dist_item_dropdown_wrapper" class="hidden">
                <label class="block text-xs font-semibold text-slate-700 mb-1">Komponen <span class="text-red-500">*</span></label>
                <select id="tambah_dist_item_id_select" onchange="onDistribusiItemSelectChange(this.value)"
                        class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-indigo-500 text-xs font-semibold text-slate-800 focus:bg-white transition-all cursor-pointer">
                    <option value="">-- Pilih Item Barang --</option>
                    <?php foreach ($items as $it): ?>
                        <option value="<?= $it['id'] ?>">
                            <?= htmlspecialchars($it['komponen']) ?> (Total: <?= (float)$it['jumlah'] ?> <?= htmlspecialchars($it['satuan'] ?: 'Unit') ?><?= ($it['remaining_qty'] > 0) ? ', Sisa: ' . (float)$it['remaining_qty'] : ' - Lengkap' ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Card Ringkas Info Kuota Item & Input Jumlah -->
            <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-center p-3.5 rounded-xl border border-indigo-100 bg-indigo-50/50">
                <!-- Info Item & Sisa Kuota (Tanpa Redundansi Kata) -->
                <div class="sm:col-span-7">
                    <div class="flex items-center gap-2 mb-1.5">
                        <span class="w-6 h-6 rounded-md bg-indigo-600 text-white flex items-center justify-center text-xs font-bold shadow-2xs">
                            <ion-icon name="cube-outline"></ion-icon>
                        </span>
                        <div class="min-w-0">
                            <span class="font-bold text-slate-800 text-xs block leading-tight truncate" id="dist_card_item_name">Item Barang</span>
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

                <!-- Input Jumlah Alokasi (Integer step, Suffix terpisah rapi) -->
                <div class="sm:col-span-5 bg-white p-2.5 rounded-xl border border-slate-200/90 shadow-2xs">
                    <label class="block text-xs font-bold text-slate-700 mb-1">Jumlah Alokasi <span class="text-red-500">*</span></label>
                    <div class="flex rounded-xl overflow-hidden border border-slate-200 focus-within:border-indigo-500 focus-within:ring-2 focus-within:ring-indigo-100 bg-white transition-all">
                        <input type="number" step="1" min="1" name="jumlah" id="tambah_dist_jumlah" value="1" required 
                               oninput="validateDistribusiQty()"
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
                            Pilih Tujuan Penerima <span class="text-red-500">*</span>
                        </label>
                        <span class="text-[11px] text-slate-400">
                            Wilayah: <strong class="text-slate-600"><?= htmlspecialchars($paket['kab_kota'] ?: 'Semua Wilayah') ?></strong>
                            &bull; <span id="tujuan_count_label"><?= count($lokasiList) ?> tujuan</span>
                        </span>
                    </div>
                    <button type="button" onclick="openQuickTambahTujuanModal()" 
                            class="px-2.5 py-1 rounded-lg bg-indigo-50 border border-indigo-200 text-indigo-700 hover:bg-indigo-100 text-xs font-bold flex items-center gap-1 transition-colors">
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

                <!-- Scrollable Table: Hanya 2 kolom data (Nama Tujuan & Kota) -->
                <div class="border border-slate-200 rounded-xl overflow-hidden max-h-56 overflow-y-auto bg-white shadow-2xs">
                    <table class="w-full text-left text-xs" id="tableListTujuan">
                        <thead class="bg-slate-100 text-[10px] uppercase font-bold text-slate-600 sticky top-0 z-10 border-b border-slate-200">
                            <tr>
                                <th class="py-2.5 px-3 w-12 text-center">PILIH</th>
                                <th class="py-2.5 px-3">NAMA TUJUAN / INSTANSI</th>
                                <th class="py-2.5 px-3 w-48">KOTA / KABUPATEN</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100" id="tbodyListTujuan">
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

            <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2 shrink-0">
                <button type="button" data-modal-close="modalTambahDistribusi" 
                        class="px-3.5 py-2 rounded-xl border border-slate-200 text-slate-600 font-semibold hover:bg-slate-50 text-xs transition-colors">
                    Batal
                </button>
                <button type="submit" id="btnSubmitDistribusi"
                        class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl text-xs shadow-xs transition-all flex items-center gap-1.5 opacity-40 cursor-not-allowed pointer-events-none" disabled>
                    <ion-icon name="checkmark-outline" class="text-sm"></ion-icon>
                    <span>Simpan Alokasi</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================== -->
<!-- MODAL QUICK TAMBAH TUJUAN (AJAX)           -->
<!-- ========================================== -->
<div id="modalQuickTambahTujuan" class="app-modal fixed inset-0 z-[60] bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 transition-all duration-200 opacity-0 pointer-events-none hidden">
    <div class="modal-card bg-white rounded-2xl shadow-2xl border border-slate-100 max-w-md w-full transform scale-95 opacity-0 transition-all duration-200 overflow-hidden">
        <div class="px-5 py-3.5 bg-slate-50 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-bold text-slate-800 text-xs flex items-center gap-2">
                <ion-icon name="location" class="text-indigo-600 text-base"></ion-icon>
                <span>Tambah Tujuan Baru Cepat</span>
            </h3>
            <button type="button" onclick="closeQuickTambahTujuanModal()" class="p-1 text-slate-400 hover:text-slate-700 rounded-lg">
                <ion-icon name="close-outline" class="text-lg"></ion-icon>
            </button>
        </div>
        <form id="formQuickTujuan" onsubmit="handleQuickTambahTujuan(event)" class="p-5 space-y-3.5">
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">
                    Nama Tujuan / Instansi <span class="text-red-500">*</span>
                </label>
                <input type="text" id="quick_nama_lokasi" required 
                       class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-indigo-500 text-xs focus:bg-white transition-all font-medium"
                       placeholder="Contoh: SMAN 2 Bontang / Kantor Kelurahan...">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">
                    Kota / Kabupaten <span class="text-red-500">*</span>
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
            <div class="pt-2 border-t border-slate-100 flex justify-end gap-2">
                <button type="button" onclick="closeQuickTambahTujuanModal()" class="px-3 py-1.5 rounded-lg border border-slate-200 text-slate-600 font-semibold hover:bg-slate-50 text-xs transition-colors">
                    Batal
                </button>
                <button type="submit" id="btnSubmitQuickTujuan" class="px-4 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg text-xs shadow-xs transition-colors flex items-center gap-1.5">
                    <ion-icon name="checkmark-outline" class="text-sm"></ion-icon>
                    <span>Simpan & Pilih Tujuan</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================== -->
<!-- MODAL UPDATE STATUS BARANG                 -->
<!-- ========================================== -->
<div id="modalUpdateStatusBarang" class="app-modal fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 transition-all duration-200 opacity-0 pointer-events-none hidden">
    <div class="modal-card bg-white rounded-2xl shadow-2xl border border-slate-100 max-w-lg w-full transform scale-95 opacity-0 transition-all duration-200 overflow-hidden max-h-[90vh] flex flex-col">
        <div class="px-5 py-3.5 bg-slate-50 border-b border-slate-100 flex items-center justify-between shrink-0">
            <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-lg bg-emerald-600 text-white flex items-center justify-center text-sm shadow-xs">
                    <ion-icon name="bag-check-outline"></ion-icon>
                </div>
                <h3 class="font-bold text-slate-800 text-xs">Update Status Barang</h3>
            </div>
            <button type="button" data-modal-close="modalUpdateStatusBarang" class="p-1 text-slate-400 hover:text-slate-700 rounded-lg">
                <ion-icon name="close-outline" class="text-lg"></ion-icon>
            </button>
        </div>
        <form id="formUpdateStatusBarang" onsubmit="submitUpdateStatusBarang(event)" class="p-5 space-y-4 overflow-y-auto flex-1">
            <input type="hidden" id="status_barang_dist_id" name="dist_id">
            
            <!-- Context Info Box -->
            <div class="p-3 bg-slate-50 rounded-xl border border-slate-200/80 text-xs">
                <div class="text-slate-400 text-[10px] font-medium uppercase tracking-wider">Tujuan Penerima:</div>
                <div class="font-bold text-slate-800 mt-0.5" id="status_barang_modal_tujuan">-</div>
                <div class="text-slate-600 text-[11px] mt-0.5" id="status_barang_modal_item">-</div>
                <div class="text-[11px] text-slate-500 mt-1 pt-1 border-t border-slate-200/60 flex items-center justify-between">
                    <span>Harga Satuan Kontrak:</span>
                    <strong class="font-mono text-slate-800" id="status_barang_modal_harga_kontrak">-</strong>
                </div>
            </div>

            <!-- Radio Pilihan Status Barang -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-2">Pilih Status Barang <span class="text-red-500">*</span></label>
                <div class="grid grid-cols-2 gap-2.5">
                    <label class="flex items-center gap-2.5 p-3 rounded-xl border border-slate-200 hover:border-slate-400 cursor-pointer transition-all bg-white has-[:checked]:border-slate-600 has-[:checked]:bg-slate-50">
                        <input type="radio" name="status_barang" value="Belum Dipesan" id="opt_status_belum_pesan" onchange="toggleStatusBarangInputs()" class="text-slate-600 focus:ring-slate-500">
                        <div>
                            <div class="font-bold text-slate-800 text-xs">Belum Dipesan</div>
                            <div class="text-[10px] text-slate-400">Barang belum diorder</div>
                        </div>
                    </label>

                    <label class="flex items-center gap-2.5 p-3 rounded-xl border border-slate-200 hover:border-emerald-400 cursor-pointer transition-all bg-white has-[:checked]:border-emerald-600 has-[:checked]:bg-emerald-50/50">
                        <input type="radio" name="status_barang" value="Sudah Dipesan" id="opt_status_sudah_pesan" onchange="toggleStatusBarangInputs()" class="text-emerald-600 focus:ring-emerald-500">
                        <div>
                            <div class="font-bold text-emerald-800 text-xs">Sudah dipesan</div>
                            <div class="text-[10px] text-emerald-600">Lengkapi data pesanan</div>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Form Rincian Pemesanan (HANYA MUNCUL JIKA SUDAH DIPESAN) -->
            <div id="section_detail_pesanan" class="space-y-3 pt-3 border-t border-slate-100 hidden">
                <div class="text-[11px] font-bold text-slate-600 uppercase tracking-wider flex items-center gap-1.5">
                    <ion-icon name="receipt-outline" class="text-emerald-600"></ion-icon>
                    <span>Rincian Pembelian Barang</span>
                </div>

                <!-- Nama Barang -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Barang yang Dipesan <span class="text-red-500">*</span></label>
                    <input type="text" id="status_barang_nama" name="nama_barang" placeholder="Contoh: Asus Zenbook 14 OLED 16GB 512GB SSD"
                           class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-emerald-500 text-xs font-semibold text-slate-800 focus:bg-white transition-all">
                    <span class="text-[10px] text-slate-400 mt-1 block">Tuliskan tipe/merk spesifik barang riil yang dibeli di toko.</span>
                </div>

                <!-- Harga Satuan & Ongkos Kirim -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Harga Satuan Beli (Rp) <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <span class="absolute left-3 top-2 text-xs font-bold text-slate-400">Rp</span>
                            <input type="text" id="status_barang_harga" name="harga_satuan" oninput="formatRupiahInput(this)" placeholder="0"
                                   class="w-full pl-9 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-emerald-500 text-xs font-mono font-bold text-slate-800 focus:bg-white transition-all">
                        </div>
                        <span class="text-[10px] text-slate-400 mt-1 block">Harga modal riil per unit (acuan HPP).</span>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Ongkos Kirim (Rp)</label>
                        <div class="relative">
                            <span class="absolute left-3 top-2 text-xs font-bold text-slate-400">Rp</span>
                            <input type="text" id="status_barang_ongkir" name="ongkos_kirim" oninput="formatRupiahInput(this)" placeholder="0"
                                   class="w-full pl-9 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-emerald-500 text-xs font-mono font-bold text-slate-800 focus:bg-white transition-all">
                        </div>
                        <span class="text-[10px] text-slate-400 mt-1 block">Opsional, otomatis masuk hitungan HPP.</span>
                    </div>
                </div>

                <!-- Ekspedisi & No. Resi -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Ekspedisi</label>
                        <input type="text" id="status_barang_ekspedisi" name="ekspedisi" placeholder="Contoh: JNE, J&T, SiCepat"
                               class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-emerald-500 text-xs focus:bg-white transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">No. Resi</label>
                        <input type="text" id="status_barang_resi" name="no_resi" placeholder="No. resi pengiriman"
                               class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-emerald-500 text-xs font-mono focus:bg-white transition-all">
                    </div>
                </div>

                <!-- Link (opsional) -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Link (Opsional)</label>
                    <input type="url" id="status_barang_link" name="link_pembelian" placeholder="https://..."
                           class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-emerald-500 text-xs font-mono focus:bg-white transition-all">
                </div>
            </div>

            <div class="pt-3 border-t border-slate-100 flex justify-end gap-2">
                <button type="button" data-modal-close="modalUpdateStatusBarang" class="px-3.5 py-1.5 rounded-lg border border-slate-200 text-slate-600 font-semibold hover:bg-slate-50 text-xs transition-colors">
                    Batal
                </button>
                <button type="submit" id="btnSubmitStatusBarang" class="px-4 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-lg text-xs shadow-xs transition-colors flex items-center gap-1.5">
                    <ion-icon name="checkmark-outline" class="text-sm"></ion-icon>
                    <span>Simpan Status Barang</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================== -->
<!-- MODAL KELOLA RINCIAN ONGKOS KIRIM          -->
<!-- ========================================== -->
<div id="modalKelolaOngkir" class="app-modal fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 transition-all duration-200 opacity-0 pointer-events-none hidden">
    <div class="modal-card bg-white rounded-2xl shadow-2xl border border-slate-100 max-w-md w-full transform scale-95 opacity-0 transition-all duration-200 overflow-hidden">
        <div class="px-5 py-3.5 bg-amber-50/70 border-b border-amber-100 flex items-center justify-between">
            <div class="flex items-center gap-2.5 min-w-0">
                <div class="w-8 h-8 rounded-lg bg-amber-500 text-white flex items-center justify-center text-base shadow-xs shrink-0">
                    <ion-icon name="cash-outline"></ion-icon>
                </div>
                <div class="min-w-0">
                    <h3 class="font-bold text-slate-800 text-sm">Rincian Ongkos Kirim</h3>
                    <p class="text-[11px] text-slate-500 truncate" id="ongkir_modal_subtitle">-</p>
                </div>
            </div>
            <button type="button" data-modal-close="modalKelolaOngkir" class="p-1.5 text-slate-400 hover:text-slate-700 rounded-lg cursor-pointer">
                <ion-icon name="close-outline" class="text-xl"></ion-icon>
            </button>
        </div>

        <form id="formKelolaOngkir" onsubmit="submitKelolaOngkir(event)" class="p-5 space-y-3.5">
            <input type="hidden" id="ongkir_dist_id" value="">

            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-slate-700">Daftar Ongkos Kirim</span>
                <button type="button" onclick="addOngkirRow()" class="inline-flex items-center gap-1 px-2.5 py-1 bg-amber-50 hover:bg-amber-100 text-amber-700 border border-amber-200 rounded-lg text-[11px] font-semibold cursor-pointer">
                    <ion-icon name="add-outline" class="text-sm"></ion-icon>
                    <span>Tambah Ongkir</span>
                </button>
            </div>

            <div id="ongkir_rows_container" class="space-y-2 max-h-60 overflow-y-auto pr-1"></div>

            <div class="p-3 bg-slate-50 rounded-xl border border-slate-200 flex items-center justify-between">
                <span class="text-xs font-bold text-slate-600">Total Ongkos Kirim (Masuk HPP)</span>
                <span id="ongkir_modal_total" class="font-mono font-extrabold text-sm text-amber-700">Rp 0</span>
            </div>

            <div class="pt-2 border-t border-slate-100 flex justify-end gap-2">
                <button type="button" data-modal-close="modalKelolaOngkir" class="px-3.5 py-1.5 rounded-lg border border-slate-200 text-slate-600 font-semibold hover:bg-slate-50 text-xs cursor-pointer">Batal</button>
                <button type="submit" id="btnSubmitKelolaOngkir" class="px-4 py-1.5 bg-amber-600 hover:bg-amber-700 text-white font-semibold rounded-lg text-xs shadow-xs cursor-pointer flex items-center gap-1.5">
                    <ion-icon name="checkmark-outline" class="text-sm"></ion-icon>
                    <span>Simpan Ongkir</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================== -->
<!-- MODAL UPDATE STATUS DISTRIBUSI             -->
<!-- ========================================== -->
<div id="modalUpdateStatusDistribusi" class="app-modal fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 transition-all duration-200 opacity-0 pointer-events-none hidden">
    <div class="modal-card bg-white rounded-2xl shadow-2xl border border-slate-100 max-w-md w-full transform scale-95 opacity-0 transition-all duration-200 overflow-hidden">
        <div class="px-5 py-3.5 bg-slate-50 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-lg bg-blue-600 text-white flex items-center justify-center text-sm shadow-xs">
                    <ion-icon name="navigate-outline"></ion-icon>
                </div>
                <h3 class="font-bold text-slate-800 text-xs">Update Status Distribusi</h3>
            </div>
            <button type="button" data-modal-close="modalUpdateStatusDistribusi" class="p-1 text-slate-400 hover:text-slate-700 rounded-lg">
                <ion-icon name="close-outline" class="text-lg"></ion-icon>
            </button>
        </div>
        <form id="formUpdateStatusDistribusi" onsubmit="submitUpdateStatusDistribusi(event)" class="p-5 space-y-4">
            <input type="hidden" id="status_distribusi_dist_id" name="dist_id">
            
            <!-- Context Info Box -->
            <div class="p-3 bg-slate-50 rounded-xl border border-slate-200/80 text-xs">
                <div class="text-slate-400 text-[10px] font-medium uppercase tracking-wider">Tujuan Penerima:</div>
                <div class="font-bold text-slate-800 mt-0.5" id="status_distribusi_modal_tujuan">-</div>
                <div class="text-slate-600 text-[11px] mt-0.5" id="status_distribusi_modal_item">-</div>
            </div>

            <!-- Radio Options Distribusi -->
            <div class="space-y-2">
                <label class="block text-xs font-semibold text-slate-700">Pilih Status Distribusi:</label>
                
                <label class="flex items-center gap-3 p-3 rounded-xl border border-slate-200 hover:border-blue-400 cursor-pointer transition-all bg-white has-[:checked]:border-blue-600 has-[:checked]:bg-blue-50/50">
                    <input type="radio" name="status_distribusi" value="Tiba di Rumah" id="dist_opt_kantor" class="text-blue-600 focus:ring-blue-500">
                    <div class="flex-1">
                        <div class="font-bold text-slate-800 text-xs flex items-center gap-1.5">
                            <ion-icon name="home-outline" class="text-blue-600"></ion-icon>
                            <span>Tiba di Rumah (Transit)</span>
                        </div>
                        <div class="text-[11px] text-slate-400 mt-0.5">Barang transit di rumah sebelum menuju gudang atau titik tujuan</div>
                    </div>
                </label>

                <label class="flex items-center gap-3 p-3 rounded-xl border border-slate-200 hover:border-amber-400 cursor-pointer transition-all bg-white has-[:checked]:border-amber-600 has-[:checked]:bg-amber-50/50">
                    <input type="radio" name="status_distribusi" value="Tiba di Gudang" id="dist_opt_gudang" class="text-amber-600 focus:ring-amber-500">
                    <div class="flex-1">
                        <div class="font-bold text-slate-800 text-xs flex items-center gap-1.5">
                            <ion-icon name="business-outline" class="text-amber-600"></ion-icon>
                            <span>Tiba di Gudang</span>
                        </div>
                        <div class="text-[11px] text-slate-400 mt-0.5">Barang telah tiba dan tersimpan di gudang transit</div>
                    </div>
                </label>

                <label class="flex items-center gap-3 p-3 rounded-xl border border-slate-200 hover:border-emerald-400 cursor-pointer transition-all bg-white has-[:checked]:border-emerald-600 has-[:checked]:bg-emerald-50/50">
                    <input type="radio" name="status_distribusi" value="Tiba di Tujuan" id="dist_opt_tujuan" class="text-emerald-600 focus:ring-emerald-500">
                    <div class="flex-1">
                        <div class="font-bold text-slate-800 text-xs flex items-center gap-1.5">
                            <ion-icon name="checkmark-done-circle-outline" class="text-emerald-600"></ion-icon>
                            <span>Tiba di Tujuan</span>
                        </div>
                        <div class="text-[11px] text-slate-400 mt-0.5">Barang telah sampai di lokasi akhir tujuan (BAST siap dicetak)</div>
                    </div>
                </label>
            </div>

            <div class="pt-2 border-t border-slate-100 flex justify-end gap-2">
                <button type="button" data-modal-close="modalUpdateStatusDistribusi" class="px-3.5 py-1.5 rounded-lg border border-slate-200 text-slate-600 font-semibold hover:bg-slate-50 text-xs transition-colors">
                    Batal
                </button>
                <button type="submit" id="btnSubmitStatusDistribusi" class="px-4 py-1.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg text-xs shadow-xs transition-colors flex items-center gap-1.5">
                    <ion-icon name="checkmark-outline" class="text-sm"></ion-icon>
                    <span>Simpan Status</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================== -->
<!-- MODAL 5: EDIT DATA PAKET (POPUP DI DETAIL) -->
<!-- ========================================== -->
<div id="modalEditPaket" class="app-modal fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 transition-all duration-200 opacity-0 pointer-events-none hidden">
    <div class="modal-card bg-white rounded-2xl shadow-2xl border border-slate-100 max-w-lg w-full transform scale-95 opacity-0 transition-all duration-200 overflow-hidden max-h-[92vh] flex flex-col">
        <!-- Header -->
        <div class="px-5 py-3.5 bg-slate-50/80 border-b border-slate-100 flex items-center justify-between shrink-0">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-amber-500 text-white flex items-center justify-center text-lg shadow-xs">
                    <ion-icon name="create-outline"></ion-icon>
                </div>
                <div>
                    <h3 class="font-bold text-slate-800 text-sm">Edit Paket Kegiatan</h3>
                    <p class="text-[11px] text-slate-400">Kode: #<?= htmlspecialchars($paket['kode_paket'] ?: $paket['id']) ?></p>
                </div>
            </div>
            <button type="button" data-modal-close="modalEditPaket" class="p-1.5 text-slate-400 hover:text-slate-700 rounded-lg transition-colors">
                <ion-icon name="close-outline" class="text-xl"></ion-icon>
            </button>
        </div>

        <!-- Body Form -->
        <form id="formEditPaket" method="POST" action="<?= APP_URL ?>/paket/update/<?= $paket['kode_paket'] ?: $paket['id'] ?>" class="p-5 overflow-y-auto space-y-3.5 flex-1">
            <?= csrfField() ?>
            <input type="hidden" name="perusahaan_id" value="<?= $paket['perusahaan_id'] ?? '' ?>">
            <input type="hidden" name="redirect_to" value="/paket/detail/<?= $paket['kode_paket'] ?: $paket['id'] ?>">

            <!-- Nama Paket -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Paket Kegiatan <span class="text-red-500">*</span></label>
                <input type="text" id="edit_nama_paket" name="nama_paket" required value="<?= htmlspecialchars($paket['nama_paket']) ?>"
                       class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-blue-500 text-xs focus:bg-white transition-all">
            </div>

            <!-- Tags Paket -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">
                    Tags Paket <span class="text-[10px] text-slate-400 font-normal">(Pisahkan dengan koma)</span>
                </label>
                <div class="relative">
                    <ion-icon name="pricetags-outline" class="absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></ion-icon>
                    <input type="text" name="tags" id="edit_tags" value="<?= htmlspecialchars($paket['tags'] ?? '') ?>" placeholder="Contoh: Disdik, APBD-P, Bontang, Urgent"
                           class="w-full pl-8 pr-2.5 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-blue-500 text-xs focus:bg-white transition-all">
                </div>
            </div>

            <!-- Perusahaan, Group & Tahun Anggaran Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Perusahaan</label>
                    <div class="flex items-center gap-1.5 px-2.5 py-2 bg-slate-100/90 border border-slate-200 rounded-xl text-xs">
                        <ion-icon name="business" class="text-blue-600 text-sm shrink-0"></ion-icon>
                        <span class="font-bold text-slate-800 truncate"><?= htmlspecialchars($paket['perusahaan'] ?? '-') ?></span>
                    </div>
                </div>

                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="block text-xs font-semibold text-slate-700">Group Paket</label>
                    </div>
                    <select name="group_id" id="edit_group_id" 
                            class="w-full px-2.5 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-blue-500 text-xs focus:bg-white transition-all cursor-pointer">
                        <option value="">-- Mandiri --</option>
                        <?php foreach ($groups as $grp): ?>
                            <option value="<?= $grp['id'] ?>" <?= ($paket['group_id'] == $grp['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($grp['nama_group']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Tahun Anggaran</label>
                    <select name="tahun_anggaran" id="edit_tahun_anggaran" 
                            class="w-full px-2.5 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-blue-500 text-xs focus:bg-white transition-all cursor-pointer font-bold text-slate-800">
                        <?php
                        $curY = (int)date('Y');
                        $pkgY = !empty($paket['tahun_anggaran']) ? (int)$paket['tahun_anggaran'] : $curY;
                        $optYears = [$curY - 1, $curY, $curY + 1];
                        if (!in_array($pkgY, $optYears)) $optYears[] = $pkgY;
                        sort($optYears);
                        foreach ($optYears as $y):
                        ?>
                            <option value="<?= $y ?>" <?= ($y === $pkgY) ? 'selected' : '' ?>><?= $y ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- Financials Grid: Pagu Awal & Pagu Nego (Wajib) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <!-- Pagu Awal -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Nilai Kontrak / Pagu Awal <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <span class="absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-400 font-semibold text-xs">Rp</span>
                        <input type="text" name="pagu" id="edit_pagu" required oninput="formatRupiahInput(this); updateEditCalc();"
                               value="<?= number_format((float)$paket['pagu'], 0, ',', '.') ?>"
                               class="w-full pl-8 pr-2.5 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-blue-500 text-xs font-mono font-medium focus:bg-white transition-all">
                    </div>
                </div>

                <!-- Pagu Akhir / Nego -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">
                        Pagu Nego (Hasil Negosiasi) <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-400 font-semibold text-xs">Rp</span>
                        <input type="text" name="pagu_nego" id="edit_pagu_nego" required oninput="formatRupiahInput(this)"
                               value="<?= ($hasNego) ? number_format((float)$paket['pagu_nego'], 0, ',', '.') : '' ?>"
                               class="w-full pl-8 pr-2.5 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-emerald-500 text-xs font-mono font-medium focus:bg-white transition-all text-emerald-700 font-bold"
                               placeholder="14.850.000">
                    </div>
                    <p class="text-[10px] text-emerald-600 font-medium mt-1 flex items-center gap-1">
                        <ion-icon name="checkmark-circle-outline"></ion-icon>
                        <span>Acuan utama DPP & potongan pasal</span>
                    </p>
                </div>
            </div>

            <!-- Footer -->
            <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2 shrink-0">
                <button type="button" data-modal-close="modalEditPaket" 
                        class="px-3.5 py-2 rounded-xl border border-slate-200 text-slate-600 font-semibold hover:bg-slate-50 text-xs transition-colors">
                    Batal
                </button>
                <button type="submit" 
                        class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white font-semibold rounded-xl text-xs shadow-xs transition-all flex items-center gap-1.5">
                    <ion-icon name="checkmark-outline" class="text-sm"></ion-icon>
                    <span>Simpan Perubahan</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================== -->
<!-- MODAL: SKEMA POTONGAN (POPUP MANDIRI)       -->
<!-- ========================================== -->
<div id="modalSkemaPotongan" class="app-modal fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 transition-all duration-200 opacity-0 pointer-events-none hidden">
    <div class="modal-card bg-white rounded-2xl shadow-2xl border border-slate-100 max-w-xl w-full transform scale-95 opacity-0 transition-all duration-200 overflow-hidden max-h-[92vh] flex flex-col">
        <div class="px-5 py-3.5 bg-slate-50 border-b border-slate-100 flex items-center justify-between shrink-0">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-amber-500 text-white flex items-center justify-center text-base shadow-xs">
                    <ion-icon name="options-outline"></ion-icon>
                </div>
                <div>
                    <h3 class="font-bold text-slate-800 text-xs">Skema Potongan</h3>
                    <p class="text-[10px] text-slate-400">Atur potongan pasal, admin fee, dan dasar perhitungan pajak/DPP</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <button type="button" onclick="openQuickAddSkema('potongan')" class="text-xs text-amber-600 hover:text-amber-700 font-semibold flex items-center gap-1 hover:underline">
                    <ion-icon name="add-circle-outline"></ion-icon>
                    <span>Skema Baru</span>
                </button>
                <button type="button" data-modal-close="modalSkemaPotongan" class="p-1 text-slate-400 hover:text-slate-700 rounded-lg">
                    <ion-icon name="close-outline" class="text-lg"></ion-icon>
                </button>
            </div>
        </div>

        <form id="formSkemaPotongan" action="<?= APP_URL ?>/paket/updatePotongan/<?= $paket['kode_paket'] ?: $paket['id'] ?>" method="POST" class="overflow-y-auto p-5 space-y-4">
            <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
            <input type="hidden" name="redirect_tab" id="potongan_redirect_tab" value="<?= htmlspecialchars($_GET['tab'] ?? '') ?>">

            <!-- Dropdown Pilih Skema -->
            <div>
                <select name="skema_id" id="potongan_skema_id" onchange="onSkemaSelectChange('potongan', this)"
                        class="w-full px-3 py-2 bg-white border border-slate-200 rounded-xl focus:outline-none focus:border-amber-500 text-xs font-semibold text-slate-700 transition-all cursor-pointer">
                    <option value="">-- Kustom / Atur Sendiri --</option>
                    <?php if (!empty($skemas)): ?>
                        <?php foreach ($skemas as $sk): ?>
                            <option value="<?= $sk['id'] ?>" 
                                    data-ada-pasal="<?= $sk['ada_pasal'] ?>" 
                                    data-pasal="<?= (float)$sk['persen_pasal'] ?>" 
                                    data-ada-admin="<?= $sk['ada_admin'] ?>" 
                                    data-admin="<?= (float)$sk['persen_admin'] ?>"
                                    data-tipe="<?= htmlspecialchars($sk['tipe_potongan'] ?? 'dpp') ?>"
                                    <?= ($paket['skema_id'] == $sk['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($sk['nama_skema']) ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>

            <!-- Checkbox & Input Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                <!-- Box 1: Potongan Pasal -->
                <div id="potongan_card_pasal" class="p-2.5 rounded-xl border border-amber-200 bg-white shadow-2xs transition-all">
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="flex items-center gap-2 cursor-pointer select-none">
                            <input type="checkbox" name="ada_pasal" id="potongan_ada_pasal" value="1" <?= ($persenPasal > 0) ? 'checked' : '' ?> onchange="togglePasalCheck('potongan')" class="w-4 h-4 rounded text-amber-600 focus:ring-0 cursor-pointer">
                            <span class="text-xs font-bold text-slate-800">Potongan Pasal</span>
                        </label>
                        <span id="potongan_badge_pasal" class="text-[10px] <?= ($persenPasal > 0) ? 'text-emerald-600 font-semibold' : 'text-slate-400 font-medium' ?>">
                            <?= ($persenPasal > 0) ? 'Aktif' : 'Nonaktif' ?>
                        </span>
                    </div>
                    <div class="relative" id="potongan_pasal_wrap">
                        <input type="number" step="0.1" min="0" max="100" name="persen_pasal" id="potongan_persen_pasal" value="<?= (float)$persenPasal ?>" oninput="updatePotonganCalc()"
                               class="w-full px-2.5 py-1.5 bg-slate-50 border border-slate-200 rounded-lg text-xs font-bold text-slate-800 focus:outline-none focus:border-amber-500 font-mono transition-all">
                        <span class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs font-bold">%</span>
                    </div>
                </div>

                <!-- Box 2: Biaya Admin Fee -->
                <div id="potongan_card_admin" class="p-2.5 rounded-xl border border-amber-200 bg-white shadow-2xs transition-all">
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="flex items-center gap-2 cursor-pointer select-none">
                            <input type="checkbox" name="ada_admin" id="potongan_ada_admin" value="1" <?= ($persenAdmin > 0) ? 'checked' : '' ?> onchange="toggleAdminCheck('potongan')" class="w-4 h-4 rounded text-amber-600 focus:ring-0 cursor-pointer">
                            <span class="text-xs font-bold text-slate-800">Biaya Admin (Fee)</span>
                        </label>
                        <span id="potongan_badge_admin" class="text-[10px] <?= ($persenAdmin > 0) ? 'text-emerald-600 font-semibold' : 'text-slate-400 font-medium' ?>">
                            <?= ($persenAdmin > 0) ? 'Aktif' : 'Nonaktif' ?>
                        </span>
                    </div>
                    <div class="relative" id="potongan_admin_wrap">
                        <input type="number" step="0.1" min="0" max="100" name="persen_admin" id="potongan_persen_admin" value="<?= (float)$persenAdmin ?>" oninput="updatePotonganCalc()"
                               class="w-full px-2.5 py-1.5 bg-slate-50 border border-slate-200 rounded-lg text-xs font-bold text-slate-800 focus:outline-none focus:border-amber-500 font-mono transition-all">
                        <span class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs font-bold">%</span>
                    </div>
                </div>
            </div>

            <!-- Pilihan Dasar Potongan: Kontrak vs DPP -->
            <div class="p-2.5 rounded-xl border border-slate-200 bg-white/70">
                <label class="flex items-start gap-2 cursor-pointer select-none">
                    <input type="checkbox" name="tipe_potongan" id="potongan_tipe_potongan" value="kontrak" <?= (($paket['tipe_potongan'] ?? 'dpp') === 'kontrak') ? 'checked' : '' ?> onchange="updatePotonganCalc()" class="w-4 h-4 rounded text-amber-600 focus:ring-0 mt-0.5 cursor-pointer">
                    <div>
                        <span class="text-xs font-bold text-slate-800 block">Potongan Diambil dari Pagu Negosiasi</span>
                        <span class="text-[10px] text-slate-500 block leading-tight">Tidak dicentang: potongan dihitung dari DPP (setelah kena pajak). Centang ini jika potongan dihitung dari Pagu Negosiasi (sebelum pajak).</span>
                    </div>
                </label>
            </div>

            <div class="flex items-center justify-between pt-1">
                <button type="button" onclick="promptSaveCurrentAsSkema('potongan')" class="text-[11px] text-amber-600 hover:text-amber-700 font-semibold flex items-center gap-1 hover:underline">
                    <ion-icon name="bookmark-outline" class="text-xs"></ion-icon>
                    <span>Simpan nilai ini sebagai skema baru</span>
                </button>
                <span class="text-[10px] text-slate-400">Total Potongan: <strong id="potongan_lbl_sum_persen" class="text-amber-700 font-mono"><?= (float)$totalPersen ?>%</strong></span>
            </div>

            <!-- LIVE CALCULATION BREAKDOWN BOX -->
            <div class="p-3 bg-slate-50 rounded-xl border border-slate-200/80 space-y-2">
                <div class="flex items-center justify-between pb-2 border-b border-slate-200/60 text-xs">
                    <span class="font-bold text-slate-700 flex items-center gap-1.5">
                        <ion-icon name="calculator-outline" class="text-amber-600 text-sm"></ion-icon>
                        <span>Rincian Perhitungan DPP & Potongan</span>
                    </span>
                    <span class="text-[11px] text-slate-500">
                        Acuan Kontrak: <strong id="potongan_preview_acuan" class="font-mono text-slate-800"><?= formatRupiah($acuan) ?></strong>
                    </span>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 pt-1 text-xs">
                    <div class="p-2 bg-white rounded-lg border border-slate-100 shadow-2xs">
                        <span class="block text-[10px] font-bold text-slate-400 uppercase">DPP (-11% PPN)</span>
                        <span class="font-mono font-bold text-slate-800 text-xs" id="potongan_preview_dpp"><?= formatRupiah($dpp) ?></span>
                    </div>

                    <div class="p-2 bg-white rounded-lg border border-slate-100 shadow-2xs">
                        <span class="block text-[10px] font-bold text-slate-400 uppercase">Pasal (<span id="potongan_lbl_pasal"><?= (float)$persenPasal ?></span>%)</span>
                        <span class="font-mono font-bold text-slate-800 text-xs" id="potongan_preview_pasal13"><?= formatRupiah($pasalVal) ?></span>
                    </div>

                    <div class="p-2 bg-white rounded-lg border border-slate-100 shadow-2xs">
                        <span class="block text-[10px] font-bold text-slate-400 uppercase">Admin (<span id="potongan_lbl_admin"><?= (float)$persenAdmin ?></span>%)</span>
                        <span class="font-mono font-bold text-slate-800 text-xs" id="potongan_preview_admin2"><?= formatRupiah($adminVal) ?></span>
                    </div>

                    <div class="p-2 bg-amber-50/80 rounded-lg border border-amber-200/60 shadow-2xs">
                        <span class="block text-[10px] font-bold text-amber-800 uppercase">Total Pasal (<span id="potongan_lbl_total_pasal"><?= (float)$totalPersen ?></span>%)</span>
                        <span class="font-mono font-bold text-amber-700 text-xs" id="potongan_preview_total_pasal"><?= formatRupiah($totalPasal) ?></span>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2 shrink-0">
                <button type="button" data-modal-close="modalSkemaPotongan" 
                        class="px-3.5 py-2 rounded-xl border border-slate-200 text-slate-600 font-semibold hover:bg-slate-50 text-xs transition-colors">
                    Batal
                </button>
                <button type="submit" 
                        class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white font-semibold rounded-xl text-xs shadow-xs transition-all flex items-center gap-1.5">
                    <ion-icon name="checkmark-outline" class="text-sm"></ion-icon>
                    <span>Simpan Perubahan</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================== -->
<!-- MODAL: UBAH STATUS PENCAIRAN (POPUP)       -->
<!-- ========================================== -->
<div id="modalStatusPencairan" class="app-modal fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 transition-all duration-200 opacity-0 pointer-events-none hidden">
    <div class="modal-card bg-white rounded-2xl shadow-2xl border border-slate-100 max-w-sm w-full transform scale-95 opacity-0 transition-all duration-200 overflow-hidden">
        <!-- Header -->
        <div class="px-5 py-3.5 bg-slate-50 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-emerald-600 text-white flex items-center justify-center text-base shadow-xs">
                    <ion-icon name="wallet-outline"></ion-icon>
                </div>
                <div>
                    <h3 class="font-bold text-slate-800 text-xs">Ubah Status Pencairan</h3>
                    <p class="text-[10px] text-slate-400 truncate max-w-[200px]" id="status_pencairan_subtitle">-</p>
                </div>
            </div>
            <button type="button" data-modal-close="modalStatusPencairan" class="p-1 text-slate-400 hover:text-slate-700 rounded-lg">
                <ion-icon name="close-outline" class="text-lg"></ion-icon>
            </button>
        </div>

        <!-- Form -->
        <form id="formStatusPencairan" action="" method="POST" class="p-5 space-y-3">
            <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
            <input type="hidden" name="redirect_to" value="detail">

            <div class="space-y-2">
                <!-- Option 1: Belum -->
                <label class="flex items-center justify-between p-3 rounded-xl border border-slate-200 hover:border-slate-300 hover:bg-slate-50/60 cursor-pointer transition-all has-[:checked]:border-slate-600 has-[:checked]:bg-slate-50/80">
                    <div class="flex items-center gap-2.5">
                        <input type="radio" name="status" value="Belum" id="status_opt_belum" class="w-4 h-4 text-slate-700 focus:ring-0 cursor-pointer">
                        <div>
                            <div class="text-xs font-bold text-slate-800">Belum Cair</div>
                            <div class="text-[10px] text-slate-400">Berkas/termin belum diajukan</div>
                        </div>
                    </div>
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-slate-600 text-white shadow-2xs">BELUM</span>
                </label>

                <!-- Option 2: Proses -->
                <label class="flex items-center justify-between p-3 rounded-xl border border-slate-200 hover:border-slate-300 hover:bg-slate-50/60 cursor-pointer transition-all has-[:checked]:border-rose-600 has-[:checked]:bg-rose-50/30">
                    <div class="flex items-center gap-2.5">
                        <input type="radio" name="status" value="Proses" id="status_opt_proses" class="w-4 h-4 text-rose-700 focus:ring-0 cursor-pointer">
                        <div>
                            <div class="text-xs font-bold text-slate-800">Sedang Proses</div>
                            <div class="text-[10px] text-slate-400">Pengajuan berkas dalam proses</div>
                        </div>
                    </div>
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-rose-700 text-white shadow-2xs">PROSES</span>
                </label>

                <!-- Option 3: Selesai -->
                <label class="flex items-center justify-between p-3 rounded-xl border border-slate-200 hover:border-slate-300 hover:bg-slate-50/60 cursor-pointer transition-all has-[:checked]:border-emerald-600 has-[:checked]:bg-emerald-50/30">
                    <div class="flex items-center gap-2.5">
                        <input type="radio" name="status" value="Selesai" id="status_opt_selesai" class="w-4 h-4 text-emerald-600 focus:ring-0 cursor-pointer">
                        <div>
                            <div class="text-xs font-bold text-slate-800">Selesai</div>
                            <div class="text-[10px] text-slate-400">Dana pencairan telah selesai/cair</div>
                        </div>
                    </div>
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-600 text-white shadow-2xs">SELESAI</span>
                </label>
            </div>

            <!-- Footer -->
            <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2 shrink-0">
                <button type="button" data-modal-close="modalStatusPencairan" 
                        class="px-3.5 py-2 rounded-xl border border-slate-200 text-slate-600 font-semibold hover:bg-slate-50 text-xs transition-colors">
                    Batal
                </button>
                <button type="submit" 
                        class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-xl text-xs shadow-xs transition-all flex items-center gap-1.5">
                    <ion-icon name="checkmark-outline" class="text-sm"></ion-icon>
                    <span>Simpan Perubahan</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================== -->
<!-- MODAL 6: QUICK TAMBAH GROUP (AJAX)         -->
<!-- ========================================== -->
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

<!-- ========================================== -->
<!-- MODAL 7: QUICK TAMBAH SKEMA (AJAX)         -->
<!-- ========================================== -->
<div id="modalQuickTambahSkema" class="app-modal fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 transition-all duration-200 opacity-0 pointer-events-none hidden">
    <div class="modal-card bg-white rounded-2xl shadow-2xl border border-slate-100 max-w-sm w-full transform scale-95 opacity-0 transition-all duration-200 overflow-hidden">
        <div class="px-5 py-3.5 bg-slate-50 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-bold text-slate-800 text-xs flex items-center gap-2">
                <ion-icon name="options-outline" class="text-blue-600 text-base"></ion-icon>
                <span>Buat Skema Potongan Baru</span>
            </h3>
            <button type="button" data-modal-close="modalQuickTambahSkema" class="p-1 text-slate-400 hover:text-slate-700 rounded-lg">
                <ion-icon name="close-outline" class="text-lg"></ion-icon>
            </button>
        </div>
        <form id="formQuickSkema" onsubmit="handleQuickTambahSkema(event)" class="p-5 space-y-3.5">
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Skema <span class="text-red-500">*</span></label>
                <input type="text" id="quick_nama_skema" required 
                       class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-blue-500 text-xs focus:bg-white"
                       placeholder="Misal: Skema 1, atau 20% Non-Admin">
            </div>

            <!-- Toggle Pasal -->
            <div class="p-2.5 rounded-xl border border-slate-200 bg-slate-50/50 space-y-2">
                <label class="flex items-center gap-2 cursor-pointer select-none">
                    <input type="checkbox" id="quick_ada_pasal" value="1" checked onchange="toggleQuickModalFields()" class="w-4 h-4 rounded text-blue-600 focus:ring-0 cursor-pointer">
                    <span class="text-xs font-bold text-slate-800">Aktifkan Potongan Pasal</span>
                </label>
                <div class="relative" id="quick_pasal_wrap">
                    <input type="number" step="0.1" min="0" max="100" id="quick_persen_pasal" value="13" oninput="updateQuickModalTotal()"
                           class="w-full px-2.5 py-1.5 bg-white border border-slate-200 rounded-lg text-xs font-bold text-slate-800 focus:outline-none focus:border-blue-500 font-mono transition-all">
                    <span class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs font-bold">%</span>
                </div>
            </div>

            <!-- Toggle Admin -->
            <div class="p-2.5 rounded-xl border border-slate-200 bg-slate-50/50 space-y-2">
                <label class="flex items-center gap-2 cursor-pointer select-none">
                    <input type="checkbox" id="quick_ada_admin" value="1" checked onchange="toggleQuickModalFields()" class="w-4 h-4 rounded text-blue-600 focus:ring-0 cursor-pointer">
                    <span class="text-xs font-bold text-slate-800">Aktifkan Biaya Admin (Fee)</span>
                </label>
                <div class="relative" id="quick_admin_wrap">
                    <input type="number" step="0.1" min="0" max="100" id="quick_persen_admin" value="2" oninput="updateQuickModalTotal()"
                           class="w-full px-2.5 py-1.5 bg-white border border-slate-200 rounded-lg text-xs font-bold text-slate-800 focus:outline-none focus:border-blue-500 font-mono transition-all">
                    <span class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs font-bold">%</span>
                </div>
            </div>

            <!-- Pilihan Dasar Potongan: Kontrak vs DPP -->
            <div class="p-2.5 rounded-xl border border-slate-200 bg-slate-50/50">
                <label class="flex items-start gap-2 cursor-pointer select-none">
                    <input type="checkbox" id="quick_tipe_potongan" value="kontrak" onchange="updateQuickModalTotal()" class="w-4 h-4 rounded text-blue-600 focus:ring-0 mt-0.5 cursor-pointer">
                    <div>
                        <span class="text-xs font-bold text-slate-800 block">Potongan Diambil dari Pagu Negosiasi</span>
                        <span class="text-[11px] text-slate-500 block leading-tight">Tidak dicentang: potongan dihitung dari DPP (setelah kena pajak). Centang ini jika potongan dihitung dari Pagu Negosiasi (sebelum pajak).</span>
                    </div>
                </label>
            </div>

            <div class="p-2 rounded-lg bg-blue-50 border border-blue-100 flex items-center justify-between text-xs">
                <span class="text-slate-600">Total Potongan Skema:</span>
                <strong id="quick_lbl_total_persen" class="font-mono text-blue-700 font-bold">15%</strong>
            </div>

            <div class="pt-2 border-t border-slate-100 flex justify-end gap-2">
                <button type="button" data-modal-close="modalQuickTambahSkema" class="px-3 py-1.5 rounded-lg border border-slate-200 text-slate-600 font-semibold hover:bg-slate-50 text-xs">
                    Batal
                </button>
                <button type="submit" id="btnSubmitQuickSkema" class="px-4 py-1.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg text-xs shadow-xs flex items-center gap-1.5">
                    <ion-icon name="checkmark-outline" class="text-sm"></ion-icon>
                    <span>Simpan & Gunakan</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================== -->
<!-- MODAL: DOKUMENTASI FOTO (UPLOAD & GALERI)  -->
<!-- ========================================== -->
<div id="modalDokumentasi" class="app-modal fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4 transition-all duration-200 opacity-0 pointer-events-none hidden">
    <div class="modal-card bg-white rounded-2xl shadow-2xl border border-slate-100 max-w-2xl w-full transform scale-95 opacity-0 transition-all duration-200 overflow-hidden max-h-[92vh] flex flex-col">
        <!-- Modal Header -->
        <div class="px-5 py-4 bg-slate-50 border-b border-slate-100 flex items-center justify-between shrink-0">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-teal-600 text-white flex items-center justify-center text-lg shadow-xs">
                    <ion-icon name="camera-outline"></ion-icon>
                </div>
                <div>
                    <h3 class="font-bold text-slate-800 text-sm flex items-center gap-1.5">
                        <span>Dokumentasi Foto</span>
                        <span class="text-slate-400 font-normal">|</span>
                        <span id="modalDokTujuan" class="text-teal-700">Nama Tujuan</span>
                    </h3>
                    <p class="text-[11px] text-slate-500 mt-0.5" id="modalDokBarang">Barang: -</p>
                </div>
            </div>
            <button type="button" onclick="window.ProModal.close('modalDokumentasi')" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-lg hover:bg-slate-100 transition-colors">
                <ion-icon name="close-outline" class="text-xl"></ion-icon>
            </button>
        </div>

        <div class="overflow-y-auto p-5 space-y-5">
            <!-- SECTION 1: UPLOAD FOTO BARU -->
            <div class="bg-slate-50/70 p-4 rounded-xl border border-slate-200/90 space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-700 uppercase tracking-wider flex items-center gap-1.5">
                        <ion-icon name="cloud-upload-outline" class="text-teal-600 text-sm"></ion-icon>
                        <span>Upload Foto Baru (Bisa Lebih Dari 1)</span>
                    </span>
                    <span class="text-[10px] text-slate-400">JPG, PNG, WebP (Otomatis Di-compress)</span>
                </div>

                <!-- Dropzone / File Picker Area -->
                <div id="dokDropzone" 
                     onclick="document.getElementById('dokFileInput').click()"
                     ondragover="event.preventDefault(); this.classList.add('border-teal-500', 'bg-teal-50/50')"
                     ondragleave="this.classList.remove('border-teal-500', 'bg-teal-50/50')"
                     ondrop="handleDokDrop(event)"
                     class="border-2 border-dashed border-slate-300 hover:border-teal-500 hover:bg-teal-50/30 rounded-xl p-4 text-center cursor-pointer transition-all">
                    <input type="file" id="dokFileInput" multiple accept="image/jpeg,image/png,image/webp,image/jpg,.heic,.heif,image/heic,image/heif" class="hidden" onchange="handleDokFileSelect(this.files)">
                    <div class="w-10 h-10 bg-teal-50 rounded-full flex items-center justify-center text-teal-600 text-xl mx-auto mb-2 shadow-2xs">
                        <ion-icon name="images-outline"></ion-icon>
                    </div>
                    <p class="text-xs font-semibold text-slate-700">
                        Klik untuk memilih foto atau seret foto ke sini
                    </p>
                    <p class="text-[11px] text-slate-400 mt-0.5">
                        Bisa pilih banyak foto sekaligus dari HP atau Komputer
                    </p>
                </div>

                <!-- Queue Selected Files Preview -->
                <div id="dokSelectedQueue" class="hidden space-y-2">
                    <div class="flex items-center justify-between text-xs text-slate-600 font-semibold">
                        <span>Foto yang akan diupload (<span id="dokSelectedCount">0</span> foto):</span>
                        <button type="button" onclick="clearDokSelectedQueue()" class="text-rose-500 hover:underline text-[11px]">Batal Semua</button>
                    </div>
                    <div id="dokQueueList" class="grid grid-cols-2 sm:grid-cols-4 gap-2 max-h-48 overflow-y-auto p-1 bg-white rounded-lg border border-slate-200">
                        <!-- Preview Cards will be inserted here dynamically -->
                    </div>
                </div>

                <!-- Keterangan Opsional -->
                <div>
                    <input type="text" id="dokKeteranganInput" placeholder="Keterangan / Catatan foto dokumentasi (opsional)..."
                           class="w-full px-3 py-2 bg-white border border-slate-200 rounded-xl text-xs focus:border-teal-500 focus:ring-2 focus:ring-teal-500/20 transition-all">
                </div>

                <!-- Progress Bar Container -->
                <div id="dokProgressContainer" class="hidden space-y-1.5 pt-1">
                    <div class="flex items-center justify-between text-xs">
                        <span id="dokProgressStatus" class="font-semibold text-teal-700 flex items-center gap-1.5">
                            <svg class="animate-spin h-3.5 w-3.5 text-teal-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            <span id="dokProgressStatusText">Mengompres & Mengunggah...</span>
                        </span>
                        <span id="dokProgressPercent" class="font-mono font-bold text-teal-800 text-xs">0%</span>
                    </div>
                    <div class="w-full bg-slate-200 rounded-full h-2.5 overflow-hidden">
                        <div id="dokProgressBar" class="bg-teal-600 h-2.5 rounded-full transition-all duration-150" style="width: 0%"></div>
                    </div>
                </div>

                <!-- Action Button -->
                <div class="flex justify-end pt-1">
                    <button type="button" id="btnUploadDokumentasi" onclick="startUploadDokumentasi()" disabled
                            class="px-4 py-2 bg-slate-200 text-slate-400 font-semibold rounded-xl text-xs shadow-xs transition-all cursor-not-allowed flex items-center gap-1.5">
                        <ion-icon name="cloud-upload-outline" class="text-sm"></ion-icon>
                        <span id="btnUploadDokumentasiText">Mulai Upload Foto</span>
                    </button>
                </div>
            </div>

            <!-- SECTION 2: GALERI FOTO TERSIMPAN -->
            <div class="space-y-3">
                <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                    <h4 class="text-xs font-bold text-slate-800 uppercase tracking-wider flex items-center gap-1.5">
                        <ion-icon name="images-outline" class="text-teal-600 text-sm"></ion-icon>
                        <span>Galeri Foto Tersimpan (<span id="dokGalleryCount">0</span>)</span>
                    </h4>
                    <span class="text-[11px] text-slate-400">Klik foto untuk memperbesar</span>
                </div>

                <!-- Foto Grid Container -->
                <div id="dokGalleryLoading" class="py-8 text-center text-slate-400 text-xs">
                    <svg class="animate-spin h-5 w-5 text-teal-600 mx-auto mb-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    <span>Memuat galeri foto...</span>
                </div>

                <div id="dokGalleryEmpty" class="hidden py-8 text-center text-slate-400 bg-slate-50/50 rounded-xl border border-dashed border-slate-200">
                    <ion-icon name="image-outline" class="text-2xl text-slate-300 mb-1"></ion-icon>
                    <p class="text-xs font-medium text-slate-600">Belum ada foto dokumentasi tersimpan</p>
                    <p class="text-[11px] text-slate-400">Gunakan form di atas untuk mengupload foto barang atau serah terima.</p>
                </div>

                <div id="dokGalleryGrid" class="hidden grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                    <!-- Photo cards loaded dynamically -->
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="px-5 py-3 bg-slate-50 border-t border-slate-100 flex items-center justify-end shrink-0">
            <button type="button" onclick="window.ProModal.close('modalDokumentasi')"
                    class="px-4 py-2 bg-white border border-slate-200 text-slate-700 hover:bg-slate-100 rounded-xl text-xs font-semibold transition-colors">
                Tutup
            </button>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- MODAL: LIGHTBOX FULL IMAGE PREVIEW         -->
<!-- ========================================== -->
<div id="modalPhotoLightbox" class="app-modal fixed inset-0 z-[60] bg-black/85 backdrop-blur-sm flex items-center justify-center p-3 sm:p-5 transition-all duration-200 opacity-0 pointer-events-none hidden">
    <div class="relative max-w-4xl w-full flex flex-col items-center">
        <!-- Close Button -->
        <button type="button" onclick="closePhotoLightbox()" 
                class="absolute -top-10 right-0 sm:-right-4 text-white hover:text-rose-400 p-2 text-2xl transition-colors cursor-pointer"
                title="Tutup (Esc)">
            <ion-icon name="close-circle-outline"></ion-icon>
        </button>

        <img id="lightboxImg" src="" alt="Preview Foto" 
             class="max-h-[82vh] max-w-full rounded-2xl object-contain shadow-2xl border border-white/10">

        <div id="lightboxCaption" class="mt-2.5 text-center text-xs text-white/90 bg-black/50 px-4 py-1.5 rounded-full backdrop-blur-md">
            Caption
        </div>
    </div>
</div>
