<!-- Header & Back -->
<div class="mb-6 flex items-center justify-between">

    <a href="<?= APP_URL ?>/paket" class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-slate-200 rounded-xl text-slate-600 hover:bg-slate-50 hover:text-blue-600 transition-colors shadow-sm">
        <ion-icon name="arrow-back-outline"></ion-icon>
        <span>Kembali</span>
    </a>
</div>

<form method="POST" action="<?= APP_URL ?>/paket/store" id="paketForm">
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
                           required placeholder="Contoh: Pembangunan Jalan Raya Km 10-15" value="<?= old('nama_paket') ?>">
                </div>
                
                <!-- Perusahaan & Status Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Perusahaan / CV <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <ion-icon name="business-outline" class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-lg"></ion-icon>
                            <select name="perusahaan_id" class="w-full pl-11 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-colors appearance-none text-slate-700" required>
                                <option value="">Pilih Perusahaan...</option>
                                <?php if(isset($all_perusahaan)): foreach($all_perusahaan as $p): ?>
                                    <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['nama']) ?></option>
                                <?php endforeach; endif; ?>
                            </select>
                            <ion-icon name="chevron-down-outline" class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none"></ion-icon>
                        </div>
                        <div class="mt-1 text-right">
                            <a href="<?= APP_URL ?>/perusahaan/create" class="text-xs text-blue-600 hover:underline">+ Tambah Perusahaan Baru</a>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Status Paket</label>
                        <div class="relative">
                            <ion-icon name="flag-outline" class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-lg"></ion-icon>
                            <select name="status" class="w-full pl-11 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-colors appearance-none">
                                <option value="Kontrak" <?= old('status') === 'Kontrak' ? 'selected' : '' ?>>Kontrak</option>
                                <option value="Pemesanan" <?= old('status') === 'Pemesanan' ? 'selected' : '' ?>>Pemesanan</option>
                                <option value="Distribusi" <?= old('status') === 'Distribusi' ? 'selected' : '' ?>>Distribusi</option>
                                <option value="Selesai" <?= old('status') === 'Selesai' ? 'selected' : '' ?>>Selesai</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Financials Card -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                <h3 class="text-lg font-bold text-slate-800 mb-6 flex items-center gap-2">
                    <ion-icon name="cash-outline" class="text-blue-500"></ion-icon>
                    Rincian Anggaran & Pasal
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Nilai Pagu <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 font-semibold">Rp</span>
                            <input type="text" name="pagu" id="pagu" class="rupiah-input w-full pl-12 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-colors font-mono font-medium text-slate-800" 
                                   required placeholder="0" value="<?= old('pagu') ?>">
                        </div>
                        <p class="text-xs text-slate-500 mt-2">Dasar perhitungan Pasal (13%)</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Nilai RAB <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 font-semibold">Rp</span>
                            <input type="text" name="rab" id="rab" class="rupiah-input w-full pl-12 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-colors font-mono font-medium text-slate-800" 
                                   required placeholder="0" value="<?= old('rab') ?>">
                        </div>
                        <p class="text-xs text-slate-500 mt-2">Modal belanja operasional</p>
                    </div>
                </div>

                <!-- Live Calculation Preview -->
                <div class="space-y-3 bg-slate-50 rounded-xl p-4 border border-slate-100">
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-600">Total Pasal (13% Pagu)</span>
                        <span class="font-semibold text-slate-800" id="previewTotalPasal">Rp 0</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-600">Jumlah Investor</span>
                        <span class="font-semibold text-slate-800"><span id="previewCount">0</span> orang</span>
                    </div>
                    <div class="h-px bg-slate-200 my-2"></div>
                    
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-600">Pasal Per Orang</span>
                        <span class="font-semibold text-orange-600" id="previewPasalPerOrang">Rp 0</span>
                    </div>
                     <div class="flex justify-between text-sm">
                        <span class="text-slate-600">RAB Per Orang</span>
                        <span class="font-semibold text-blue-600" id="previewRabPerOrang">Rp 0</span>
                    </div>
                    
                    <div class="h-px bg-slate-200 my-2"></div>
                    
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-bold text-slate-800">Total Modal Per Orang</span>
                        <span class="text-lg font-bold text-emerald-600" id="previewTotalPerOrang">Rp 0</span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Sidebar Selection (1 Column) -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Pemodal Selection -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 flex flex-col h-full max-h-[600px]">
                <div class="p-6 border-b border-slate-100">
                    <h3 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                        <ion-icon name="people-outline" class="text-blue-500"></ion-icon>
                        Pilih Pemodal
                    </h3>
                    <p class="text-sm text-slate-500 mt-1">Minimal 1 pemodal wajib dipilih</p>
                </div>
                
                <div class="flex-1 overflow-y-auto p-4 space-y-2 custom-scrollbar">
                    <?php if (empty($users)): ?>
                        <div class="text-center py-8">
                            <div class="w-12 h-12 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-3">
                                <ion-icon name="person-add-outline" class="text-2xl text-slate-400"></ion-icon>
                            </div>
                            <p class="text-sm text-slate-500 mb-3">Belum ada data pemodal</p>
                            <a href="<?= APP_URL ?>/pemodal/create" class="text-sm font-medium text-blue-600 hover:underline">Tambah Baru +</a>
                        </div>
                    <?php else: ?>
                        <?php foreach ($users as $user): ?>
                            <label class="flex items-center gap-3 p-3 rounded-xl border border-transparent hover:bg-slate-50 hover:border-slate-200 cursor-pointer transition-all group">
                                <div class="relative flex items-center">
                                    <input type="checkbox" name="pemodal[]" value="<?= $user['id'] ?>" 
                                           class="pemodal-checkbox peer h-5 w-5 text-blue-600 border-slate-300 rounded focus:ring-blue-500"
                                           <?= in_array($user['id'], old('pemodal', [])) ? 'checked' : '' ?>>
                                </div>
                                <div class="flex-1">
                                    <div class="font-semibold text-slate-700 peer-checked:text-blue-700"><?= htmlspecialchars($user['nama']) ?></div>
                                    <?php if ($user['email']): ?>
                                        <div class="text-xs text-slate-500"><?= htmlspecialchars($user['email']) ?></div>
                                    <?php endif; ?>
                                </div>
                                <div class="w-8 h-8 rounded-full bg-slate-100 text-slate-500 flex items-center justify-center group-hover:bg-blue-100 group-hover:text-blue-600 transition-colors">
                                    <span class="text-xs font-bold"><?= strtoupper(substr($user['nama'], 0, 1)) ?></span>
                                </div>
                            </label>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                
                <div class="p-4 bg-slate-50 border-t border-slate-100 rounded-b-2xl">
                    <div class="flex items-center justify-between text-sm font-medium text-slate-600">
                        <span>Terpilih:</span>
                        <span class="bg-blue-600 text-white px-2 py-0.5 rounded text-xs font-bold" id="countBadge">0</span>
                    </div>
                    <!-- Hidden input for quantity logic -->
                    <input type="hidden" name="jumlah_pemodal" id="jumlah_pemodal" value="0">
                </div>
            </div>

            <!-- Submit Button (Sticky on Mobile) -->
            <button type="submit" class="w-full py-4 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-lg shadow-blue-600/30 transition-all hover:scale-[1.02] active:scale-[0.98] flex items-center justify-center gap-2">
                <ion-icon name="save-outline" class="text-xl"></ion-icon>
                <span>Simpan Paket Kegiatan</span>
            </button>
        </div>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const checkboxes = document.querySelectorAll('.pemodal-checkbox');
    const jumlahInput = document.getElementById('jumlah_pemodal');
    const countBadge = document.getElementById('countBadge');
    
    // Format helpers
    const formatRupiah = (num) => 'Rp ' + new Intl.NumberFormat('id-ID').format(Math.floor(num));
    const parseRupiah = (str) => parseInt(str.replace(/[^0-9]/g, '') || 0);

    function updateCalculations() {
        const count = document.querySelectorAll('.pemodal-checkbox:checked').length;
        jumlahInput.value = count;
        countBadge.textContent = count;
        document.getElementById('previewCount').textContent = count;
        
        const pagu = parseRupiah(document.getElementById('pagu').value);
        const rab = parseRupiah(document.getElementById('rab').value);
        
        if (pagu > 0) {
            const totalPasal = pagu * 0.13;
            document.getElementById('previewTotalPasal').textContent = formatRupiah(totalPasal);
            
            if (count > 0) {
                const pasalPerOrang = totalPasal / count;
                const rabPerOrang = rab / count;
                const totalPerOrang = pasalPerOrang + rabPerOrang;
                
                document.getElementById('previewPasalPerOrang').textContent = formatRupiah(pasalPerOrang);
                document.getElementById('previewRabPerOrang').textContent = formatRupiah(rabPerOrang);
                document.getElementById('previewTotalPerOrang').textContent = formatRupiah(totalPerOrang);
            } else {
                document.getElementById('previewPasalPerOrang').textContent = 'Rp 0';
                document.getElementById('previewRabPerOrang').textContent = 'Rp 0';
                document.getElementById('previewTotalPerOrang').textContent = 'Rp 0';
            }
        } else {
            document.getElementById('previewTotalPasal').textContent = 'Rp 0';
            document.getElementById('previewPasalPerOrang').textContent = 'Rp 0';
            document.getElementById('previewRabPerOrang').textContent = 'Rp 0'; 
            document.getElementById('previewTotalPerOrang').textContent = 'Rp 0';
        }
    }
    
    // Listeners
    checkboxes.forEach(cb => cb.addEventListener('change', updateCalculations));
    document.getElementById('pagu').addEventListener('input', updateCalculations);
    document.getElementById('rab').addEventListener('input', updateCalculations);
    
    // Initial
    updateCalculations();
});
</script>
