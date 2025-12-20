<div class="max-w-2xl mx-auto">
    <!-- Header -->
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Edit Transaksi</h1>
            <p class="text-slate-500 mt-1">Paket: <span class="font-semibold text-blue-600"><?= htmlspecialchars($paket['nama_paket']) ?></span></p>
        </div>
        <a href="<?= APP_URL ?>/paket/detail/<?= $paket['id'] ?>" class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-white border border-slate-200 text-slate-500 hover:text-blue-600 hover:border-blue-200 transition-colors shadow-sm">
            <ion-icon name="close-outline" class="text-2xl"></ion-icon>
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <form method="POST" action="<?= APP_URL ?>/transaksi/update/<?= $transaksi['id'] ?>" class="p-6 md:p-8">
            <?= csrfField() ?>
            
            <!-- Type Selection (Visual Toggle) -->
            <div class="mb-8">
                <label class="block text-xs font-bold text-slate-500 uppercase mb-3">Tipe Transaksi</label>
                <div class="grid grid-cols-2 gap-4">
                    <label class="relative cursor-pointer group">
                        <input type="radio" name="tipe" value="masuk" class="peer sr-only" <?= $transaksi['tipe'] === 'masuk' ? 'checked' : '' ?> onchange="updateKategoriOptions('masuk')">
                        <div class="p-4 rounded-xl border-2 border-slate-100 bg-slate-50 peer-checked:border-emerald-500 peer-checked:bg-emerald-50 transition-all text-center group-hover:border-emerald-200">
                            <div class="w-10 h-10 mx-auto rounded-full bg-white text-slate-400 peer-checked:text-emerald-500 peer-checked:shadow-md flex items-center justify-center mb-2 transition-all">
                                <ion-icon name="arrow-down-outline" class="text-2xl"></ion-icon>
                            </div>
                            <div class="font-bold text-slate-500 peer-checked:text-emerald-700">Pemasukan</div>
                        </div>
                    </label>
                    <label class="relative cursor-pointer group">
                        <input type="radio" name="tipe" value="keluar" class="peer sr-only" <?= $transaksi['tipe'] === 'keluar' ? 'checked' : '' ?> onchange="updateKategoriOptions('keluar')">
                        <div class="p-4 rounded-xl border-2 border-slate-100 bg-slate-50 peer-checked:border-rose-500 peer-checked:bg-rose-50 transition-all text-center group-hover:border-rose-200">
                            <div class="w-10 h-10 mx-auto rounded-full bg-white text-slate-400 peer-checked:text-rose-500 peer-checked:shadow-md flex items-center justify-center mb-2 transition-all">
                                <ion-icon name="arrow-up-outline" class="text-2xl"></ion-icon>
                            </div>
                            <div class="font-bold text-slate-500 peer-checked:text-rose-700">Pengeluaran</div>
                        </div>
                    </label>
                </div>
            </div>
            
            <!-- Category Selection (Pills) -->
            <div class="mb-6">
                <label class="block text-xs font-bold text-slate-500 uppercase mb-3">Kategori</label>
                <div id="kategori-container" class="flex flex-wrap gap-2">
                    <!-- Options injected via JS -->
                </div>
                <!-- Hidden input to store selected value -->
                <input type="hidden" name="kategori" id="kategori-input" value="<?= htmlspecialchars($transaksi['kategori'] ?? '') ?>" required>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Date -->
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Tanggal Transaksi</label>
                    <input type="date" name="tanggal" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all font-medium text-slate-700" required 
                           value="<?= $transaksi['tanggal'] ?>">
                </div>
                
                <!-- Amount -->
                <div>
                     <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Nominal (Rp)</label>
                     <div class="relative">
                         <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 font-bold">Rp</span>
                         <input type="text" name="jumlah" class="w-full pl-12 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all font-bold text-slate-800 text-lg rupiah-input" required 
                                placeholder="0"
                                value="<?= formatRupiah($transaksi['jumlah']) ?>">
                     </div>
                </div>
            </div>
            
            <!-- Description -->
            <div class="mb-8">
                <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Catatan / Keterangan</label>
                <textarea name="keterangan" rows="3" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all text-slate-700" placeholder="Tulis deskripsi singkat transaksi ini..."><?= htmlspecialchars($transaksi['keterangan'] ?? '') ?></textarea>
            </div>
            
            <!-- Actions -->
            <div class="flex items-center gap-4 pt-6 border-t border-slate-100">
                <button type="submit" class="flex-1 py-3.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-lg shadow-blue-600/30 hover:scale-[1.01] active:scale-[0.99] transition-all flex items-center justify-center gap-2">
                    <ion-icon name="save-outline" class="text-xl"></ion-icon>
                    <span>Update Data</span>
                </button>
                <a href="<?= APP_URL ?>/paket/detail/<?= $paket['id'] ?>" class="px-6 py-3.5 bg-white border border-slate-200 text-slate-600 font-bold rounded-xl hover:bg-slate-50 transition-colors">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>

<script>
    const kategoriInput = document.getElementById('kategori-input');
    const container = document.getElementById('kategori-container');
    
    // Categories definition
    const categories = {
        masuk: [
            { id: 'Pasal', label: 'Pasal', icon: 'document-text-outline' },
            { id: 'Modal Belanja', label: 'Modal Belanja', icon: 'cart-outline' },
            { id: 'Administrasi', label: 'Administrasi', icon: 'folder-open-outline' },
            { id: 'Lainnya', label: 'Lainnya', icon: 'ellipsis-horizontal-circle-outline' }
        ],
        keluar: [
            { id: 'Pasal', label: 'Pasal', icon: 'document-text-outline' },
            { id: 'Modal Belanja', label: 'Modal Belanja', icon: 'cart-outline' },
            { id: 'Pengeluaran', label: 'Pengeluaran Umum', icon: 'wallet-outline' },
            { id: 'Administrasi', label: 'Administrasi', icon: 'folder-open-outline' }
        ]
    };

    function updateKategoriOptions(type) {
        container.innerHTML = '';
        const options = categories[type] || [];
        
        options.forEach(opt => {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = `px-4 py-2 rounded-lg border text-sm font-medium transition-all flex items-center gap-2 ${
                kategoriInput.value === opt.id 
                ? 'bg-blue-600 text-white border-blue-600 shadow-md transform scale-105' 
                : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50 hover:border-blue-300'
            }`;
            btn.innerHTML = `<ion-icon name="${opt.icon}"></ion-icon> ${opt.label}`;
            
            btn.onclick = () => {
                // Deselect all
                Array.from(container.children).forEach(c => {
                    c.className = 'px-4 py-2 rounded-lg border border-slate-200 bg-white text-slate-600 text-sm font-medium transition-all flex items-center gap-2 hover:bg-slate-50 hover:border-blue-300';
                });
                // Select clicked
                btn.className = 'px-4 py-2 rounded-lg border border-blue-600 bg-blue-600 text-white text-sm font-medium transition-all shadow-md transform scale-105 flex items-center gap-2';
                kategoriInput.value = opt.id;
            };
            
            container.appendChild(btn);
        });
        
        // Auto select first if none selected or type switched
        if (!options.find(o => o.id === kategoriInput.value)) {
           // Keep empty or let user choose.
        } else {
             // Re-highlight existing selection
             Array.from(container.children).forEach(btn => {
                 if(btn.textContent.trim().includes(kategoriInput.value)) {
                     btn.click();
                 }
             });
        }
    }

    // Initial load
    const initialType = document.querySelector('input[name="tipe"]:checked').value;
    updateKategoriOptions(initialType);
    
    // Formatting Rupiah
    const rupiahInputs = document.querySelectorAll('.rupiah-input');
    rupiahInputs.forEach(input => {
        input.addEventListener('keyup', function(e) {
            input.value = formatRupiah(this.value);
        });
    });

    function formatRupiah(angka, prefix) {
        var number_string = angka.replace(/[^,\d]/g, '').toString(),
            split = number_string.split(','),
            sisa = split[0].length % 3,
            rupiah = split[0].substr(0, sisa),
            ribuan = split[0].substr(sisa).match(/\d{3}/gi);

        if (ribuan) {
            separator = sisa ? '.' : '';
            rupiah += separator + ribuan.join('.');
        }

        rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
        return prefix == undefined ? rupiah : (rupiah ? 'Rp ' + rupiah : '');
    }
</script>
