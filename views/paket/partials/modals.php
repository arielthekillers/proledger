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

            <!-- No Dokumen & Tanggal TTD -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">
                        Nomor Surat BAST (Opsional)
                    </label>
                    <input type="text" name="nomor_dokumen" id="upload_nomor_dokumen"
                           placeholder="Contoh: 028/BAST-DISDIK/XI/2026"
                           class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:border-amber-500 transition-all">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">
                        Tanggal Tanda Tangan
                    </label>
                    <input type="date" name="tanggal_dokumen" id="upload_tanggal_dokumen" value="<?= date('Y-m-d') ?>"
                           class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:border-amber-500 transition-all">
                </div>
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

            <!-- Link Acuan & Pembanding (Sama Lebar) -->
            <div class="pt-2 border-t border-slate-100 space-y-2.5">
                <label class="block text-xs font-bold text-slate-700">Tautan Katalog & Harga Pembanding (Opsional)</label>

                <!-- Link 1 (Asli) -->
                <div>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-blue-600 text-xs font-bold">Asli:</span>
                        <input type="url" name="link_asli" id="tambah_item_link_asli"
                               class="w-full pl-12 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-blue-500 text-[11px] focus:bg-white font-mono"
                               placeholder="https://katalog.inaproc.id/... (Tautan Katalog Acuan)">
                    </div>
                </div>

                <!-- Link 2 (P1) -->
                <div>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-emerald-600 text-xs font-bold">P1:</span>
                        <input type="url" name="link_pembanding_1" id="tambah_item_link_p1"
                               class="w-full pl-12 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-emerald-500 text-[11px] focus:bg-white font-mono"
                               placeholder="https://... (Tautan Harga Pembanding 1)">
                    </div>
                </div>

                <!-- Link 3 (P2) -->
                <div>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-purple-600 text-xs font-bold">P2:</span>
                        <input type="url" name="link_pembanding_2" id="tambah_item_link_p2"
                               class="w-full pl-12 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-purple-500 text-[11px] focus:bg-white font-mono"
                               placeholder="https://... (Tautan Harga Pembanding 2)">
                    </div>
                </div>
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

            <!-- Link Acuan & Pembanding (Sama Lebar) -->
            <div class="pt-2 border-t border-slate-100 space-y-2.5">
                <label class="block text-xs font-bold text-slate-700">Tautan Katalog & Harga Pembanding (Opsional)</label>

                <!-- Link 1 (Asli) -->
                <div>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-blue-600 text-xs font-bold">Asli:</span>
                        <input type="url" name="link_asli" id="edit_item_link_asli"
                               class="w-full pl-12 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-blue-500 text-[11px] focus:bg-white font-mono"
                               placeholder="https://katalog.inaproc.id/... (Tautan Katalog Acuan)">
                    </div>
                </div>

                <!-- Link 2 (P1) -->
                <div>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-emerald-600 text-xs font-bold">P1:</span>
                        <input type="url" name="link_pembanding_1" id="edit_item_link_p1"
                               class="w-full pl-12 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-emerald-500 text-[11px] focus:bg-white font-mono"
                               placeholder="https://... (Tautan Harga Pembanding 1)">
                    </div>
                </div>

                <!-- Link 3 (P2) -->
                <div>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-purple-600 text-xs font-bold">P2:</span>
                        <input type="url" name="link_pembanding_2" id="edit_item_link_p2"
                               class="w-full pl-12 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-purple-500 text-[11px] focus:bg-white font-mono"
                               placeholder="https://... (Tautan Harga Pembanding 2)">
                    </div>
                </div>
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
                <label class="block text-xs font-semibold text-slate-700 mb-1">Item Komponen <span class="text-red-500">*</span></label>
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
<!-- MODAL UPDATE RUTE PENGIRIMAN               -->
<!-- ========================================== -->
<div id="modalUpdateRute" class="app-modal fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 transition-all duration-200 opacity-0 pointer-events-none hidden">
    <div class="modal-card bg-white rounded-2xl shadow-2xl border border-slate-100 max-w-md w-full transform scale-95 opacity-0 transition-all duration-200 overflow-hidden">
        <div class="px-5 py-3.5 bg-slate-50 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-lg bg-blue-600 text-white flex items-center justify-center text-sm shadow-xs">
                    <ion-icon name="paper-plane-outline"></ion-icon>
                </div>
                <h3 class="font-bold text-slate-800 text-xs">Update Rute Pengiriman</h3>
            </div>
            <button type="button" data-modal-close="modalUpdateRute" class="p-1 text-slate-400 hover:text-slate-700 rounded-lg">
                <ion-icon name="close-outline" class="text-lg"></ion-icon>
            </button>
        </div>
        <form id="formUpdateRute" onsubmit="submitUpdateRute(event)" class="p-5 space-y-4">
            <input type="hidden" id="rute_dist_id" name="dist_id">
            <!-- Context Info Box -->
            <div class="p-3 bg-slate-50 rounded-xl border border-slate-200/80 text-xs">
                <div class="text-slate-400 text-[10px] font-medium uppercase tracking-wider">Tujuan & Komponen:</div>
                <div class="font-bold text-slate-800 mt-0.5" id="rute_modal_tujuan">-</div>
                <div class="text-slate-600 text-[11px]" id="rute_modal_item">-</div>
            </div>

            <!-- Radio Options -->
            <div class="space-y-2">
                <label class="block text-xs font-semibold text-slate-700">Pilih Rute Pengiriman:</label>
                
                <label class="flex items-center gap-3 p-3 rounded-xl border border-slate-200 hover:border-blue-400 cursor-pointer transition-all bg-white has-[:checked]:border-blue-600 has-[:checked]:bg-blue-50/50">
                    <input type="radio" name="rute_pengiriman" value="kantor" id="rute_opt_kantor" class="text-blue-600 focus:ring-blue-500">
                    <div class="flex-1">
                        <div class="font-bold text-slate-800 text-xs flex items-center gap-1.5">
                            <ion-icon name="home-outline" class="text-blue-600"></ion-icon>
                            <span>Ke Rumah / Kantor</span>
                        </div>
                        <div class="text-[11px] text-slate-400 mt-0.5">Transit penerimaan barang di rumah atau kantor pelaksana</div>
                    </div>
                </label>

                <label class="flex items-center gap-3 p-3 rounded-xl border border-slate-200 hover:border-amber-400 cursor-pointer transition-all bg-white has-[:checked]:border-amber-600 has-[:checked]:bg-amber-50/50">
                    <input type="radio" name="rute_pengiriman" value="gudang" id="rute_opt_gudang" class="text-amber-600 focus:ring-amber-500">
                    <div class="flex-1">
                        <div class="font-bold text-slate-800 text-xs flex items-center gap-1.5">
                            <ion-icon name="business-outline" class="text-amber-600"></ion-icon>
                            <span>Ke Gudang Transit</span>
                        </div>
                        <div class="text-[11px] text-slate-400 mt-0.5">Disimpan sementara di gudang logistik sebelum disalurkan</div>
                    </div>
                </label>

                <label class="flex items-center gap-3 p-3 rounded-xl border border-slate-200 hover:border-emerald-400 cursor-pointer transition-all bg-white has-[:checked]:border-emerald-600 has-[:checked]:bg-emerald-50/50">
                    <input type="radio" name="rute_pengiriman" value="langsung" id="rute_opt_langsung" class="text-emerald-600 focus:ring-emerald-500">
                    <div class="flex-1">
                        <div class="font-bold text-slate-800 text-xs flex items-center gap-1.5">
                            <ion-icon name="paper-plane-outline" class="text-emerald-600"></ion-icon>
                            <span>Langsung ke Tujuan</span>
                        </div>
                        <div class="text-[11px] text-slate-400 mt-0.5">Dikirim langsung dari supplier ke alamat sekolah/instansi</div>
                    </div>
                </label>
            </div>

            <div class="pt-2 border-t border-slate-100 flex justify-end gap-2">
                <button type="button" data-modal-close="modalUpdateRute" class="px-3 py-1.5 rounded-lg border border-slate-200 text-slate-600 font-semibold hover:bg-slate-50 text-xs transition-colors">
                    Batal
                </button>
                <button type="submit" id="btnSubmitRute" class="px-4 py-1.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg text-xs shadow-xs transition-colors flex items-center gap-1.5">
                    <ion-icon name="checkmark-outline" class="text-sm"></ion-icon>
                    <span>Simpan Rute</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================== -->
<!-- MODAL UPDATE STATUS PEMESANAN              -->
<!-- ========================================== -->
<div id="modalUpdatePemesanan" class="app-modal fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 transition-all duration-200 opacity-0 pointer-events-none hidden">
    <div class="modal-card bg-white rounded-2xl shadow-2xl border border-slate-100 max-w-md w-full transform scale-95 opacity-0 transition-all duration-200 overflow-hidden">
        <div class="px-5 py-3.5 bg-slate-50 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-lg bg-sky-600 text-white flex items-center justify-center text-sm shadow-xs">
                    <ion-icon name="bag-check-outline"></ion-icon>
                </div>
                <h3 class="font-bold text-slate-800 text-xs">Update Status Pemesanan</h3>
            </div>
            <button type="button" data-modal-close="modalUpdatePemesanan" class="p-1 text-slate-400 hover:text-slate-700 rounded-lg">
                <ion-icon name="close-outline" class="text-lg"></ion-icon>
            </button>
        </div>
        <form id="formUpdatePemesanan" onsubmit="submitUpdatePemesanan(event)" class="p-5 space-y-3.5">
            <input type="hidden" id="pemesanan_dist_id" name="dist_id">
            <!-- Context Info Box -->
            <div class="p-3 bg-slate-50 rounded-xl border border-slate-200/80 text-xs">
                <div class="text-slate-400 text-[10px] font-medium uppercase tracking-wider">Tujuan & Komponen:</div>
                <div class="font-bold text-slate-800 mt-0.5" id="pemesanan_modal_tujuan">-</div>
                <div class="text-slate-600 text-[11px]" id="pemesanan_modal_item">-</div>
            </div>

            <!-- Status Pemesanan Dropdown -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Status Pemesanan <span class="text-red-500">*</span></label>
                <select id="pemesanan_status" name="status_pemesanan" required
                        class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-sky-500 text-xs font-semibold text-slate-800 focus:bg-white transition-all cursor-pointer">
                    <option value="Belum Dipesan">Belum Dipesan</option>
                    <option value="Sudah Dipesan">Sudah Dipesan</option>
                    <option value="Tiba di Transit">Tiba di Transit</option>
                </select>
            </div>

            <!-- Tanggal Pesan -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Tanggal Pesan / Pembelian</label>
                <input type="date" id="pemesanan_tanggal" name="tanggal_pesan"
                       class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-sky-500 text-xs focus:bg-white transition-all">
            </div>

            <!-- Kurir & No Resi Grid -->
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Kurir / Ekspedisi</label>
                    <input type="text" id="pemesanan_kurir" name="kurir_ekspedisi" placeholder="Contoh: JNE Cargo, J&T"
                           class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-sky-500 text-xs focus:bg-white transition-all">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">No. Resi / Tracking</label>
                    <input type="text" id="pemesanan_resi" name="no_resi" placeholder="No. resi pengiriman"
                           class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-sky-500 text-xs font-mono focus:bg-white transition-all">
                </div>
            </div>

            <!-- Tautan Pembelian -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Tautan Pembelian / Invoice (Opsional)</label>
                <input type="url" id="pemesanan_link" name="link_pembelian" placeholder="https://..."
                       class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-sky-500 text-xs font-mono focus:bg-white transition-all">
            </div>

            <div class="pt-2 border-t border-slate-100 flex justify-end gap-2">
                <button type="button" data-modal-close="modalUpdatePemesanan" class="px-3 py-1.5 rounded-lg border border-slate-200 text-slate-600 font-semibold hover:bg-slate-50 text-xs transition-colors">
                    Batal
                </button>
                <button type="submit" id="btnSubmitPemesanan" class="px-4 py-1.5 bg-sky-600 hover:bg-sky-700 text-white font-semibold rounded-lg text-xs shadow-xs transition-colors flex items-center gap-1.5">
                    <ion-icon name="checkmark-outline" class="text-sm"></ion-icon>
                    <span>Simpan Status</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================== -->
<!-- MODAL UPDATE STATUS PENERIMAAN             -->
<!-- ========================================== -->
<div id="modalUpdatePenerimaan" class="app-modal fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 transition-all duration-200 opacity-0 pointer-events-none hidden">
    <div class="modal-card bg-white rounded-2xl shadow-2xl border border-slate-100 max-w-md w-full transform scale-95 opacity-0 transition-all duration-200 overflow-hidden">
        <div class="px-5 py-3.5 bg-slate-50 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-lg bg-emerald-600 text-white flex items-center justify-center text-sm shadow-xs">
                    <ion-icon name="checkmark-done-circle-outline"></ion-icon>
                </div>
                <h3 class="font-bold text-slate-800 text-xs">Update Status Penerimaan di Lokasi</h3>
            </div>
            <button type="button" data-modal-close="modalUpdatePenerimaan" class="p-1 text-slate-400 hover:text-slate-700 rounded-lg">
                <ion-icon name="close-outline" class="text-lg"></ion-icon>
            </button>
        </div>
        <form id="formUpdatePenerimaan" onsubmit="submitUpdatePenerimaan(event)" class="p-5 space-y-3.5">
            <input type="hidden" id="penerimaan_dist_id" name="dist_id">
            <!-- Context Info Box -->
            <div class="p-3 bg-slate-50 rounded-xl border border-slate-200/80 text-xs">
                <div class="text-slate-400 text-[10px] font-medium uppercase tracking-wider">Tujuan & Komponen:</div>
                <div class="font-bold text-slate-800 mt-0.5" id="penerimaan_modal_tujuan">-</div>
                <div class="text-slate-600 text-[11px]" id="penerimaan_modal_item">-</div>
            </div>

            <!-- Status Penerimaan Dropdown -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Status Penerimaan di Tujuan <span class="text-red-500">*</span></label>
                <select id="penerimaan_status" name="status_penerimaan" required onchange="onStatusPenerimaanChange(this.value)"
                        class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-emerald-500 text-xs font-semibold text-slate-800 focus:bg-white transition-all cursor-pointer">
                    <option value="Menunggu Barang">Menunggu Barang</option>
                    <option value="Dalam Pengantaran ke Tujuan">Dalam Pengantaran ke Tujuan</option>
                    <option value="Siap Disalurkan / Diambil">Siap Disalurkan / Diambil</option>
                    <option value="Diterima di Tujuan">Diterima di Tujuan (BAST Siap Cetak)</option>
                </select>
            </div>

            <!-- Tanggal Terima di Lokasi -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Tanggal Terima di Lokasi</label>
                <input type="date" id="penerimaan_tanggal" name="tanggal_terima"
                       class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-emerald-500 text-xs focus:bg-white transition-all">
            </div>

            <!-- Catatan Serah Terima -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Catatan Serah Terima / Kondisi Barang (Opsional)</label>
                <textarea id="penerimaan_catatan" name="catatan" rows="2" placeholder="Catatan kondisi serah terima barang..."
                          class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-emerald-500 text-xs focus:bg-white transition-all leading-relaxed"></textarea>
            </div>

            <div class="pt-2 border-t border-slate-100 flex justify-end gap-2">
                <button type="button" data-modal-close="modalUpdatePenerimaan" class="px-3 py-1.5 rounded-lg border border-slate-200 text-slate-600 font-semibold hover:bg-slate-50 text-xs transition-colors">
                    Batal
                </button>
                <button type="submit" id="btnSubmitPenerimaan" class="px-4 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-lg text-xs shadow-xs transition-colors flex items-center gap-1.5">
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
    <div class="modal-card bg-white rounded-2xl shadow-2xl border border-slate-100 max-w-2xl w-full transform scale-95 opacity-0 transition-all duration-200 overflow-hidden max-h-[92vh] flex flex-col">
        <!-- Header -->
        <div class="px-6 py-4 bg-slate-50/80 border-b border-slate-100 flex items-center justify-between shrink-0">
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
        <form id="formEditPaket" method="POST" action="<?= APP_URL ?>/paket/update/<?= $paket['kode_paket'] ?: $paket['id'] ?>" class="p-6 overflow-y-auto space-y-4 flex-1">
            <?= csrfField() ?>
            <input type="hidden" name="perusahaan_id" value="<?= $paket['perusahaan_id'] ?? '' ?>">

            <!-- Nama Paket -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Paket Kegiatan <span class="text-red-500">*</span></label>
                <input type="text" id="edit_nama_paket" name="nama_paket" required value="<?= htmlspecialchars($paket['nama_paket']) ?>"
                       class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-blue-500 text-xs focus:bg-white transition-all">
            </div>

            <!-- Nomor Surat Pesanan (No. SP / SPK / Kontrak) -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">
                    Nomor Surat Pesanan (No. SP / SPK / Kontrak)
                </label>
                <input type="text" id="edit_nomor_sp" name="nomor_sp" value="<?= htmlspecialchars($paket['nomor_sp'] ?? '') ?>"
                       placeholder="Contoh: EP-01M1ZS80DH32WZR9NZ8JCWCA44"
                       class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-blue-500 text-xs focus:bg-white transition-all font-mono">
                <p class="text-[10px] text-slate-400 mt-0.5">Nomor ini tercetak otomatis pada lembar Berita Acara Serah Terima (BAST).</p>
            </div>

            <!-- Kota / Kabupaten Wilayah Pengadaan -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">
                    Kota / Kabupaten Wilayah Pengadaan
                </label>
                <select name="kab_kota" id="edit_kab_kota" 
                        class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-blue-500 text-xs focus:bg-white transition-all cursor-pointer font-medium">
                    <option value="">-- Pilih Kota / Kabupaten --</option>
                    <?php if (!empty($kabupatenList)): ?>
                        <?php foreach ($kabupatenList as $kab): ?>
                            <option value="<?= htmlspecialchars($kab['nama']) ?>" <?= (($paket['kab_kota'] ?? '') === $kab['nama']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($kab['nama']) ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
                <p class="text-[10px] text-slate-400 mt-0.5">Digunakan untuk memfilter tujuan penerima distribusi pada paket ini.</p>
            </div>

            <!-- Perusahaan & Group Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Perusahaan</label>
                    <div class="flex items-center gap-2 px-3 py-2 bg-slate-100/90 border border-slate-200 rounded-xl text-xs">
                        <ion-icon name="business" class="text-blue-600 text-sm shrink-0"></ion-icon>
                        <span class="font-bold text-slate-800 truncate"><?= htmlspecialchars($paket['perusahaan'] ?? '-') ?></span>
                        <span class="text-[10px] text-slate-400 ml-auto shrink-0 font-medium">Terkunci</span>
                    </div>
                </div>

                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="block text-xs font-semibold text-slate-700">Group Paket (Opsional)</label>
                        <button type="button" onclick="openQuickAddGroup('edit_group_id')" class="text-[11px] text-blue-600 hover:underline font-semibold flex items-center gap-0.5">
                            <ion-icon name="add-circle-outline"></ion-icon> Group Baru
                        </button>
                    </div>
                    <select name="group_id" id="edit_group_id" 
                            class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-blue-500 text-xs focus:bg-white transition-all cursor-pointer">
                        <option value="">-- Tanpa Group (Paket Mandiri) --</option>
                        <?php foreach ($groups as $grp): ?>
                            <option value="<?= $grp['id'] ?>" <?= ($paket['group_id'] == $grp['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($grp['nama_group']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- Penanggung Jawab & Progres Barang Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Penanggung Jawab (PJ)</label>
                    <input type="text" list="list_pj" name="penanggung_jawab" id="edit_penanggung_jawab" value="<?= htmlspecialchars($paket['penanggung_jawab'] ?? '') ?>"
                           class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-blue-500 text-xs focus:bg-white transition-all uppercase tracking-wide"
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
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Progres Barang</label>
                    <select name="progres_barang" id="edit_progres_barang" 
                            class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-blue-500 text-xs focus:bg-white transition-all cursor-pointer font-semibold">
                        <option value="Belum" <?= ($paket['progres_barang'] ?? '') === 'Belum' ? 'selected' : '' ?> class="text-rose-700 font-bold">BELUM</option>
                        <option value="Belanja" <?= ($paket['progres_barang'] ?? '') === 'Belanja' ? 'selected' : '' ?> class="text-blue-700 font-bold">BELANJA</option>
                        <option value="Distribusi" <?= ($paket['progres_barang'] ?? '') === 'Distribusi' ? 'selected' : '' ?> class="text-emerald-700 font-bold">DISTRIBUSI</option>
                        <option value="Selesai" <?= ($paket['progres_barang'] ?? '') === 'Selesai' ? 'selected' : '' ?> class="text-teal-700 font-bold">SELESAI</option>
                    </select>
                </div>
            </div>

            <!-- Financials Grid: Pagu Awal, Pagu Nego, Status -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <!-- Pagu Awal -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Nilai Pagu Awal <span class="text-red-500">*</span></label>
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
                        Pagu Nego <span class="text-[10px] font-normal text-emerald-600 font-semibold">(Acuan Akhir)</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-400 font-semibold text-xs">Rp</span>
                        <input type="text" name="pagu_nego" id="edit_pagu_nego" oninput="formatRupiahInput(this); updateEditCalc();"
                               value="<?= ($hasNego) ? number_format((float)$paket['pagu_nego'], 0, ',', '.') : '' ?>"
                               class="w-full pl-8 pr-2.5 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-emerald-500 text-xs font-mono font-medium focus:bg-white transition-all text-emerald-700"
                               placeholder="Opsional">
                    </div>
                </div>

                <!-- Status Kas -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Status Kas</label>
                    <select id="edit_status" name="status" class="w-full px-2.5 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-blue-500 text-xs focus:bg-white transition-all cursor-pointer font-bold">
                        <option value="Proses" <?= ($paket['status'] ?? '') === 'Proses' ? 'selected' : '' ?> class="text-rose-700 font-bold">PROSES</option>
                        <option value="Cair" <?= ($paket['status'] ?? '') === 'Cair' ? 'selected' : '' ?> class="text-emerald-700 font-bold">CAIR</option>
                        <option value="Belum" <?= ($paket['status'] ?? '') === 'Belum' ? 'selected' : '' ?> class="text-slate-700 font-bold">BELUM</option>
                    </select>
                </div>
            </div>

            <!-- Skema Fleksibel Potongan Pasal & Admin (Dinamis & Reusable) -->
            <div class="p-3.5 bg-slate-50/80 rounded-xl border border-slate-200/80 space-y-3">
                <div class="flex items-center justify-between">
                    <label class="text-xs font-bold text-slate-700 flex items-center gap-1.5">
                        <ion-icon name="options-outline" class="text-amber-600 text-sm"></ion-icon>
                        <span>Skema Potongan</span>
                    </label>
                    <button type="button" onclick="openQuickAddSkema('edit')" class="text-[11px] text-amber-600 hover:underline font-semibold flex items-center gap-0.5">
                        <ion-icon name="add-circle-outline"></ion-icon> Skema Baru
                    </button>
                </div>

                <!-- Dropdown Pilih Skema -->
                <div>
                    <select name="skema_id" id="edit_skema_id" onchange="onSkemaSelectChange('edit', this)"
                            class="w-full px-3 py-2 bg-white border border-slate-200 rounded-xl focus:outline-none focus:border-amber-500 text-xs font-semibold text-slate-700 transition-all cursor-pointer">
                        <option value="">-- Kustom / Atur Sendiri --</option>
                        <?php if (!empty($skemas)): ?>
                            <?php foreach ($skemas as $sk): ?>
                                <option value="<?= $sk['id'] ?>" 
                                        data-ada-pasal="<?= $sk['ada_pasal'] ?>" 
                                        data-pasal="<?= (float)$sk['persen_pasal'] ?>" 
                                        data-ada-admin="<?= $sk['ada_admin'] ?>" 
                                        data-admin="<?= (float)$sk['persen_admin'] ?>"
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
                    <div id="edit_card_pasal" class="p-2.5 rounded-xl border border-amber-200 bg-white shadow-2xs transition-all">
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="flex items-center gap-2 cursor-pointer select-none">
                                <input type="checkbox" name="ada_pasal" id="edit_ada_pasal" value="1" <?= ($persenPasal > 0) ? 'checked' : '' ?> onchange="togglePasalCheck('edit')" class="w-4 h-4 rounded text-amber-600 focus:ring-0 cursor-pointer">
                                <span class="text-xs font-bold text-slate-800">Potongan Pasal</span>
                            </label>
                            <span id="edit_badge_pasal" class="text-[10px] <?= ($persenPasal > 0) ? 'text-emerald-600 font-semibold' : 'text-slate-400 font-medium' ?>">
                                <?= ($persenPasal > 0) ? 'Aktif' : 'Nonaktif' ?>
                            </span>
                        </div>
                        <div class="relative" id="edit_pasal_wrap">
                            <input type="number" step="0.1" min="0" max="100" name="persen_pasal" id="edit_persen_pasal" value="<?= (float)$persenPasal ?>" oninput="updateEditCalc()"
                                   class="w-full px-2.5 py-1.5 bg-slate-50 border border-slate-200 rounded-lg text-xs font-bold text-slate-800 focus:outline-none focus:border-amber-500 font-mono transition-all">
                            <span class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs font-bold">%</span>
                        </div>
                    </div>

                    <!-- Box 2: Biaya Admin Fee -->
                    <div id="edit_card_admin" class="p-2.5 rounded-xl border border-amber-200 bg-white shadow-2xs transition-all">
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="flex items-center gap-2 cursor-pointer select-none">
                                <input type="checkbox" name="ada_admin" id="edit_ada_admin" value="1" <?= ($persenAdmin > 0) ? 'checked' : '' ?> onchange="toggleAdminCheck('edit')" class="w-4 h-4 rounded text-amber-600 focus:ring-0 cursor-pointer">
                                <span class="text-xs font-bold text-slate-800">Biaya Admin (Fee)</span>
                            </label>
                            <span id="edit_badge_admin" class="text-[10px] <?= ($persenAdmin > 0) ? 'text-emerald-600 font-semibold' : 'text-slate-400 font-medium' ?>">
                                <?= ($persenAdmin > 0) ? 'Aktif' : 'Nonaktif' ?>
                            </span>
                        </div>
                        <div class="relative" id="edit_admin_wrap">
                            <input type="number" step="0.1" min="0" max="100" name="persen_admin" id="edit_persen_admin" value="<?= (float)$persenAdmin ?>" oninput="updateEditCalc()"
                                   class="w-full px-2.5 py-1.5 bg-slate-50 border border-slate-200 rounded-lg text-xs font-bold text-slate-800 focus:outline-none focus:border-amber-500 font-mono transition-all">
                            <span class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs font-bold">%</span>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-between pt-1">
                    <button type="button" onclick="promptSaveCurrentAsSkema('edit')" class="text-[11px] text-amber-600 hover:text-amber-700 font-semibold flex items-center gap-1 hover:underline">
                        <ion-icon name="bookmark-outline" class="text-xs"></ion-icon>
                        <span>Simpan nilai ini sebagai skema baru</span>
                    </button>
                    <span class="text-[10px] text-slate-400">Total Potongan: <strong id="edit_lbl_sum_persen" class="text-amber-700 font-mono"><?= (float)$totalPersen ?>%</strong></span>
                </div>
            </div>

            <!-- LIVE CALCULATION BREAKDOWN BOX -->
            <div class="p-3 bg-slate-50 rounded-xl border border-slate-200/80 space-y-2">
                <div class="flex items-center justify-between pb-2 border-b border-slate-200/60 text-xs">
                    <span class="font-bold text-slate-700 flex items-center gap-1.5">
                        <ion-icon name="calculator-outline" class="text-amber-600 text-sm"></ion-icon>
                        <span>Rincian Perhitungan DPP & Potongan</span>
                    </span>
                    <span class="text-[11px] text-slate-500">
                        Acuan Kontrak: <strong id="edit_preview_acuan" class="font-mono text-slate-800"><?= formatRupiah($acuan) ?></strong>
                    </span>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 pt-1 text-xs">
                    <div class="p-2 bg-white rounded-lg border border-slate-100 shadow-2xs">
                        <span class="block text-[10px] font-bold text-slate-400 uppercase">DPP (-11% PPN)</span>
                        <span class="font-mono font-bold text-slate-800 text-xs" id="edit_preview_dpp"><?= formatRupiah($dpp) ?></span>
                    </div>

                    <div class="p-2 bg-white rounded-lg border border-slate-100 shadow-2xs">
                        <span class="block text-[10px] font-bold text-slate-400 uppercase">Pasal (<span id="edit_lbl_pasal"><?= (float)$persenPasal ?></span>%)</span>
                        <span class="font-mono font-bold text-slate-800 text-xs" id="edit_preview_pasal13"><?= formatRupiah($pasalVal) ?></span>
                    </div>

                    <div class="p-2 bg-white rounded-lg border border-slate-100 shadow-2xs">
                        <span class="block text-[10px] font-bold text-slate-400 uppercase">Admin (<span id="edit_lbl_admin"><?= (float)$persenAdmin ?></span>%)</span>
                        <span class="font-mono font-bold text-slate-800 text-xs" id="edit_preview_admin2"><?= formatRupiah($adminVal) ?></span>
                    </div>

                    <div class="p-2 bg-amber-50/80 rounded-lg border border-amber-200/60 shadow-2xs">
                        <span class="block text-[10px] font-bold text-amber-800 uppercase">Total Pasal (<span id="edit_lbl_total_pasal"><?= (float)$totalPersen ?></span>%)</span>
                        <span class="font-mono font-bold text-amber-700 text-xs" id="edit_preview_total_pasal"><?= formatRupiah($totalPasal) ?></span>
                    </div>
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
