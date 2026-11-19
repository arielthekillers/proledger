/**
 * ProLedger - Global UI, Modal, Toast & Image Uploader Engine
 * Standar upload gambar multi-ukuran (Large, Medium, Small) dengan progress bar
 * Dapat digunakan di seluruh modul aplikasi
 */

// Format number to Rupiah
function formatRupiah(amount) {
    return new Intl.NumberFormat('id-ID', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    }).format(amount);
}

// Parse Rupiah string to number
function parseRupiah(rupiah) {
    return parseInt(rupiah.toString().replace(/[^0-9]/g, '')) || 0;
}

// ==========================================
// 1. FLOATING TOAST NOTIFICATION ENGINE
// ==========================================
window.ProToast = {
    show: function (type = 'success', message = '', title = '') {
        let container = document.getElementById('pro-toast-container');
        if (!container) {
            container = document.createElement('div');
            container.id = 'pro-toast-container';
            container.className = 'fixed top-5 right-5 z-[99999] flex flex-col gap-2.5 max-w-sm w-full pointer-events-none px-4 sm:px-0';
            document.body.appendChild(container);
        }

        const toast = document.createElement('div');
        toast.className = 'pointer-events-auto transform translate-x-full opacity-0 transition-all duration-300 ease-out bg-white rounded-xl shadow-xl border overflow-hidden text-xs flex items-start gap-3 p-3.5 relative';

        const configs = {
            success: {
                border: 'border-emerald-200/90 shadow-emerald-600/10',
                iconBg: 'bg-emerald-50 text-emerald-600 border border-emerald-100',
                icon: 'checkmark-circle',
                defaultTitle: 'Berhasil',
                barColor: 'bg-emerald-500'
            },
            error: {
                border: 'border-rose-200/90 shadow-rose-600/10',
                iconBg: 'bg-rose-50 text-rose-600 border border-rose-100',
                icon: 'alert-circle',
                defaultTitle: 'Terjadi Kesalahan',
                barColor: 'bg-rose-500'
            },
            warning: {
                border: 'border-amber-200/90 shadow-amber-600/10',
                iconBg: 'bg-amber-50 text-amber-600 border border-amber-100',
                icon: 'warning',
                defaultTitle: 'Peringatan',
                barColor: 'bg-amber-500'
            },
            info: {
                border: 'border-blue-200/90 shadow-blue-600/10',
                iconBg: 'bg-blue-50 text-blue-600 border border-blue-100',
                icon: 'information-circle',
                defaultTitle: 'Informasi',
                barColor: 'bg-blue-500'
            }
        };

        const config = configs[type] || configs.success;
        toast.className += ' ' + config.border;
        const finalTitle = title || config.defaultTitle;

        toast.innerHTML = `
            <div class="w-8 h-8 rounded-lg ${config.iconBg} flex items-center justify-center text-lg shrink-0 mt-0.5 shadow-xs">
                <ion-icon name="${config.icon}"></ion-icon>
            </div>
            <div class="flex-1 pr-3">
                <div class="font-bold text-slate-800 text-xs">${finalTitle}</div>
                <div class="text-[11px] text-slate-600 mt-0.5 leading-relaxed font-normal">${message}</div>
            </div>
            <button type="button" class="text-slate-400 hover:text-slate-600 p-1 -mr-1 transition-colors toast-close-btn" title="Tutup">
                <ion-icon name="close-outline" class="text-base"></ion-icon>
            </button>
            <div class="toast-progress absolute bottom-0 left-0 right-0 h-0.5 ${config.barColor} transition-all ease-linear" style="width: 100%;"></div>
        `;

        container.appendChild(toast);

        // Slide in
        requestAnimationFrame(() => {
            toast.classList.remove('translate-x-full', 'opacity-0');
            toast.classList.add('translate-x-0', 'opacity-100');
        });

        let remaining = 3500;
        const interval = 50;
        const total = remaining;
        let isPaused = false;
        const progressBar = toast.querySelector('.toast-progress');

        const timer = setInterval(() => {
            if (!isPaused) {
                remaining -= interval;
                if (progressBar) {
                    progressBar.style.width = ((remaining / total) * 100) + '%';
                }
                if (remaining <= 0) {
                    clearInterval(timer);
                    dismiss();
                }
            }
        }, interval);

        toast.addEventListener('mouseenter', () => isPaused = true);
        toast.addEventListener('mouseleave', () => isPaused = false);

        const closeBtn = toast.querySelector('.toast-close-btn');
        if (closeBtn) closeBtn.addEventListener('click', () => dismiss());

        function dismiss() {
            clearInterval(timer);
            toast.classList.remove('translate-x-0', 'opacity-100');
            toast.classList.add('translate-x-full', 'opacity-0');
            setTimeout(() => {
                if (toast.parentElement) toast.remove();
            }, 300);
        }
    },

    success: function (msg, title) { this.show('success', msg, title); },
    error: function (msg, title) { this.show('error', msg, title); },
    warning: function (msg, title) { this.show('warning', msg, title); },
    info: function (msg, title) { this.show('info', msg, title); }
};

// ==========================================
// 2. MODAL & CONFIRMATION ENGINE
// ==========================================
window.ProModal = {
    open: function (modalId) {
        const id = modalId.replace('#', '');
        const modal = document.getElementById(id);
        if (!modal) return;

        modal.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');

        requestAnimationFrame(() => {
            modal.classList.remove('opacity-0', 'pointer-events-none');
            const card = modal.querySelector('.modal-card') || modal.children[0];
            if (card) {
                card.classList.remove('scale-95', 'opacity-0');
                card.classList.add('scale-100', 'opacity-100');
            }
        });

        setTimeout(() => {
            const firstInput = modal.querySelector('input:not([type="hidden"]), select, textarea');
            if (firstInput) firstInput.focus();
        }, 100);
    },

    close: function (modalId) {
        const id = modalId.replace('#', '');
        const modal = document.getElementById(id);
        if (!modal) return;

        const card = modal.querySelector('.modal-card') || modal.children[0];
        if (card) {
            card.classList.remove('scale-100', 'opacity-100');
            card.classList.add('scale-95', 'opacity-0');
        }

        modal.classList.add('opacity-0', 'pointer-events-none');

        setTimeout(() => {
            modal.classList.add('hidden');
            if (!document.querySelector('.app-modal:not(.hidden)')) {
                document.body.classList.remove('overflow-hidden');
            }
        }, 200);
    },

    confirmDelete: function (url, itemName = 'data ini', itemType = 'Data') {
        if (typeof Swal === 'undefined') {
            if (confirm(`Apakah Anda yakin ingin menghapus ${itemName}?`)) {
                window.location.href = url;
            }
            return false;
        }

        Swal.fire({
            title: `Hapus ${itemType}?`,
            html: `Apakah Anda yakin ingin menghapus <strong>${itemName}</strong>?<br><span class="text-xs text-slate-400 mt-1 block">Tindakan ini permanen dan tidak dapat dibatalkan.</span>`,
            icon: 'warning',
            iconColor: '#f43f5e',
            showCancelButton: true,
            confirmButtonColor: '#e11d48',
            cancelButtonColor: '#94a3b8',
            confirmButtonText: '<span class="flex items-center gap-1.5 justify-center"><ion-icon name="trash-outline" class="text-base"></ion-icon> Ya, Hapus</span>',
            cancelButtonText: 'Batal',
            reverseButtons: true,
            focusCancel: true,
            buttonsStyling: false,
            customClass: {
                popup: 'rounded-2xl font-sans text-sm p-6 shadow-2xl border border-slate-100',
                title: 'text-base font-bold text-slate-800',
                htmlContainer: 'text-xs text-slate-600',
                confirmButton: 'px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white font-semibold rounded-xl text-xs transition-all shadow-sm hover:scale-[1.02] active:scale-[0.98] ml-2',
                cancelButton: 'px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-xl text-xs transition-all'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Menghapus...',
                    text: 'Mohon tunggu sebentar',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                        window.location.href = url;
                    }
                });
            }
        });

        return false;
    },

    confirmAction: function (url, options = {}) {
        const title = options.title || 'Konfirmasi Tindakan';
        const text = options.text || 'Apakah Anda yakin ingin melanjutkan tindakan ini?';
        const icon = options.icon || 'question';
        const iconColor = options.iconColor || '#2563eb';
        const confirmButtonText = options.confirmText || 'Ya, Lanjutkan';
        const confirmButtonColorClass = options.btnClass || 'bg-blue-600 hover:bg-blue-700 text-white';

        if (typeof Swal === 'undefined') {
            if (confirm(title + '\n' + text.replace(/<[^>]*>?/gm, ''))) {
                window.location.href = url;
            }
            return false;
        }

        Swal.fire({
            title: title,
            html: text,
            icon: icon,
            iconColor: iconColor,
            showCancelButton: true,
            confirmButtonText: confirmButtonText,
            cancelButtonText: 'Batal',
            reverseButtons: true,
            buttonsStyling: false,
            customClass: {
                popup: 'rounded-2xl font-sans text-sm p-6 shadow-2xl border border-slate-100',
                title: 'text-base font-bold text-slate-800',
                htmlContainer: 'text-xs text-slate-600',
                confirmButton: `px-4 py-2 font-semibold rounded-xl text-xs transition-all shadow-sm hover:scale-[1.02] active:scale-[0.98] ml-2 ${confirmButtonColorClass}`,
                cancelButton: 'px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-xl text-xs transition-all'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = url;
            }
        });

        return false;
    }
};

// ==========================================
// 3. STANDARD IMAGE UPLOADER WITH PROGRESS
// ==========================================
window.ProUploader = {
    currentOptions: null,
    selectedFile: null,

    /**
     * Buka Popup Modal Upload Gambar
     */
    open: function (options = {}) {
        this.currentOptions = Object.assign({
            title: 'Upload Gambar',
            hint: 'Mendukung format JPG, PNG, atau WebP (Maks 10MB)',
            module: 'general',
            field: 'image',
            id: null,
            uploadUrl: (window.APP_URL || '') + '/upload/image',
            onSuccess: null
        }, options);

        this.selectedFile = null;
        this.ensureModalExists();

        // Set labels & reset UI
        document.getElementById('prouploader-title').textContent = this.currentOptions.title;
        document.getElementById('prouploader-hint').textContent = this.currentOptions.hint;
        document.getElementById('prouploader-file-input').value = '';
        
        // Reset states
        this.showState('select');
        window.ProModal.open('pro-uploader-modal');
    },

    ensureModalExists: function () {
        if (document.getElementById('pro-uploader-modal')) return;

        const modalHtml = `
        <div id="pro-uploader-modal" class="app-modal fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 transition-all duration-200 opacity-0 pointer-events-none hidden">
            <div class="modal-card bg-white rounded-2xl shadow-2xl border border-slate-100 max-w-md w-full transform scale-95 opacity-0 transition-all duration-200 overflow-hidden">
                <!-- Header -->
                <div class="px-5 py-4 bg-slate-50/80 border-b border-slate-100 flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-blue-600 text-white flex items-center justify-center text-lg shadow-xs">
                            <ion-icon name="cloud-upload-outline"></ion-icon>
                        </div>
                        <div>
                            <h3 id="prouploader-title" class="font-bold text-slate-800 text-sm">Upload Gambar</h3>
                            <p id="prouploader-hint" class="text-[11px] text-slate-400">Pilih file gambar dari perangkat Anda</p>
                        </div>
                    </div>
                    <button type="button" data-modal-close="pro-uploader-modal" class="p-1.5 text-slate-400 hover:text-slate-700 rounded-lg transition-colors">
                        <ion-icon name="close-outline" class="text-xl"></ion-icon>
                    </button>
                </div>

                <!-- Body Content -->
                <div class="p-5">
                    <!-- State 1: Dropzone / Select -->
                    <div id="prouploader-dropzone" class="border-2 border-dashed border-slate-200 hover:border-blue-500 rounded-xl p-6 text-center transition-all bg-slate-50/50 hover:bg-blue-50/30 cursor-pointer group">
                        <input type="file" id="prouploader-file-input" class="hidden" accept="image/png, image/jpeg, image/webp, image/gif">
                        <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-2xl mx-auto mb-2 group-hover:scale-110 transition-transform">
                            <ion-icon name="image-outline"></ion-icon>
                        </div>
                        <p class="font-bold text-slate-700 text-xs mb-0.5">Klik atau Seret Gambar ke Sini</p>
                        <p class="text-[11px] text-slate-400">PNG, JPG, WebP hingga 10MB</p>
                    </div>

                    <!-- State 2: Selected & Preview -->
                    <div id="prouploader-preview-container" class="hidden space-y-3">
                        <div class="flex items-center gap-3 p-3 bg-slate-50 rounded-xl border border-slate-200/80">
                            <img id="prouploader-preview-img" src="" class="w-16 h-16 rounded-lg object-contain bg-white border border-slate-200 shadow-2xs">
                            <div class="flex-1 min-w-0">
                                <h4 id="prouploader-file-name" class="font-bold text-slate-800 text-xs truncate">nama-file.jpg</h4>
                                <p id="prouploader-file-info" class="text-[11px] text-slate-400 mt-0.5">1.2 MB &bull; 1920x1080</p>
                                <span class="inline-flex items-center gap-1 text-[10px] text-blue-600 font-medium mt-1">
                                    <ion-icon name="layers-outline"></ion-icon>
                                    <span>Akan di-generate 3 versi (Small, Med, Large)</span>
                                </span>
                            </div>
                            <button type="button" id="prouploader-btn-change" class="text-xs text-slate-500 hover:text-blue-600 p-1.5 hover:bg-slate-200/60 rounded-lg transition-colors" title="Ganti File">
                                <ion-icon name="sync-outline" class="text-lg"></ion-icon>
                            </button>
                        </div>
                    </div>

                    <!-- State 3: Upload Progress Bar -->
                    <div id="prouploader-progress-container" class="hidden space-y-2 pt-2">
                        <div class="flex items-center justify-between text-xs">
                            <span id="prouploader-status-text" class="font-semibold text-slate-700 flex items-center gap-1.5">
                                <svg class="animate-spin -ml-0.5 mr-1.5 h-3.5 w-3.5 inline-block text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                <span>Mengunggah file...</span>
                            </span>
                            <span id="prouploader-percent-text" class="font-bold text-blue-600 font-mono">0%</span>
                        </div>
                        <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden shadow-inner">
                            <div id="prouploader-progress-bar" class="bg-gradient-to-r from-blue-500 to-indigo-600 h-2 rounded-full transition-all duration-150" style="width: 0%;"></div>
                        </div>
                        <p class="text-[10px] text-slate-400 text-center">Menyimpan dan mengoptimasi resolusi gambar...</p>
                    </div>

                    <!-- State 4: Success Message -->
                    <div id="prouploader-success-container" class="hidden py-4 text-center space-y-2">
                        <div class="w-12 h-12 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center text-2xl mx-auto shadow-xs">
                            <ion-icon name="checkmark-circle"></ion-icon>
                        </div>
                        <h4 class="font-bold text-slate-800 text-sm">Upload Berhasil!</h4>
                        <p class="text-xs text-slate-500">Gambar telah berhasil disimpan dalam 3 ukuran preview (Large, Medium, Small).</p>
                    </div>
                </div>

                <!-- Footer -->
                <div class="px-5 py-3.5 bg-slate-50/50 border-t border-slate-100 flex items-center justify-end gap-2">
                    <button type="button" data-modal-close="pro-uploader-modal" class="px-3.5 py-1.5 rounded-lg border border-slate-200 text-slate-600 font-semibold hover:bg-slate-100 text-xs transition-colors">
                        Batal
                    </button>
                    <button type="button" id="prouploader-btn-start" disabled 
                            class="px-4 py-1.5 bg-blue-600 hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed text-white font-semibold rounded-lg text-xs shadow-xs transition-all flex items-center gap-1.5">
                        <ion-icon name="cloud-upload-outline" class="text-sm"></ion-icon>
                        <span>Mulai Upload</span>
                    </button>
                    <button type="button" id="prouploader-btn-done" class="hidden px-4 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-lg text-xs shadow-xs transition-all">
                        Selesai
                    </button>
                </div>
            </div>
        </div>
        `;

        document.body.insertAdjacentHTML('beforeend', modalHtml);
        this.bindModalEvents();
    },

    bindModalEvents: function () {
        const dropzone = document.getElementById('prouploader-dropzone');
        const fileInput = document.getElementById('prouploader-file-input');
        const btnChange = document.getElementById('prouploader-btn-change');
        const btnStart = document.getElementById('prouploader-btn-start');
        const btnDone = document.getElementById('prouploader-btn-done');

        // Click to open file dialog
        dropzone.addEventListener('click', () => fileInput.click());
        btnChange.addEventListener('click', () => fileInput.click());

        // File selected handler
        fileInput.addEventListener('change', (e) => {
            const file = e.target.files[0];
            if (file) this.handleFileSelection(file);
        });

        // Drag & drop handlers
        ['dragenter', 'dragover'].forEach(eventName => {
            dropzone.addEventListener(eventName, (e) => {
                e.preventDefault();
                dropzone.classList.add('border-blue-500', 'bg-blue-50/50');
            });
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropzone.addEventListener(eventName, (e) => {
                e.preventDefault();
                dropzone.classList.remove('border-blue-500', 'bg-blue-50/50');
            });
        });

        dropzone.addEventListener('drop', (e) => {
            const dt = e.dataTransfer;
            const file = dt.files[0];
            if (file && file.type.startsWith('image/')) {
                this.handleFileSelection(file);
            }
        });

        // Start Upload button
        btnStart.addEventListener('click', () => this.startUpload());

        // Done button
        btnDone.addEventListener('click', () => {
            window.ProModal.close('pro-uploader-modal');
            if (typeof this.currentOptions.onSuccess === 'function') {
                this.currentOptions.onSuccess();
            } else {
                window.location.reload();
            }
        });
    },

    handleFileSelection: function (file) {
        this.selectedFile = file;

        // Size in MB
        const sizeMb = (file.size / (1024 * 1024)).toFixed(2);
        document.getElementById('prouploader-file-name').textContent = file.name;

        // Preview with FileReader
        const reader = new FileReader();
        reader.onload = (e) => {
            const img = document.getElementById('prouploader-preview-img');
            img.src = e.target.result;

            const tempImg = new Image();
            tempImg.src = e.target.result;
            tempImg.onload = () => {
                document.getElementById('prouploader-file-info').textContent = `${sizeMb} MB • ${tempImg.naturalWidth}x${tempImg.naturalHeight}px`;
            };

            this.showState('preview');
        };
        reader.readAsDataURL(file);
    },

    startUpload: function () {
        if (!this.selectedFile) return;

        this.showState('uploading');

        const formData = new FormData();
        formData.append('file', this.selectedFile);
        formData.append('module', this.currentOptions.module);
        formData.append('field', this.currentOptions.field);
        if (this.currentOptions.id) {
            formData.append('id', this.currentOptions.id);
        }

        const xhr = new XMLHttpRequest();
        xhr.open('POST', this.currentOptions.uploadUrl, true);

        // Upload progress event
        xhr.upload.addEventListener('progress', (e) => {
            if (e.lengthComputable) {
                const percent = Math.round((e.loaded / e.total) * 100);
                document.getElementById('prouploader-progress-bar').style.width = percent + '%';
                document.getElementById('prouploader-percent-text').textContent = percent + '%';

                if (percent >= 100) {
                    document.getElementById('prouploader-status-text').innerHTML = `
                        <svg class="animate-spin -ml-0.5 mr-1.5 h-3.5 w-3.5 inline-block text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        <span>Memproses ukuran Small, Medium, Large...</span>
                    `;
                }
            }
        });

        // Request complete
        xhr.addEventListener('load', () => {
            if (xhr.status >= 200 && xhr.status < 300) {
                try {
                    const response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        this.showState('success');
                        window.ProToast.success(response.message || 'Gambar berhasil diupload!');
                        
                        setTimeout(() => {
                            window.ProModal.close('pro-uploader-modal');
                            if (typeof this.currentOptions.onSuccess === 'function') {
                                this.currentOptions.onSuccess(response);
                            } else {
                                window.location.reload();
                            }
                        }, 1200);
                    } else {
                        this.showState('preview');
                        window.ProToast.error(response.message || 'Upload gagal.');
                    }
                } catch (err) {
                    this.showState('preview');
                    window.ProToast.error('Respon server tidak valid.');
                }
            } else {
                this.showState('preview');
                try {
                    const errRes = JSON.parse(xhr.responseText);
                    window.ProToast.error(errRes.message || 'Gagal mengunggah gambar.');
                } catch (e) {
                    window.ProToast.error('Terjadi kesalahan server saat upload.');
                }
            }
        });

        xhr.addEventListener('error', () => {
            this.showState('preview');
            window.ProToast.error('Koneksi terputus saat upload gambar.');
        });

        xhr.send(formData);
    },

    showState: function (state) {
        const dropzone = document.getElementById('prouploader-dropzone');
        const preview = document.getElementById('prouploader-preview-container');
        const progress = document.getElementById('prouploader-progress-container');
        const success = document.getElementById('prouploader-success-container');
        const btnStart = document.getElementById('prouploader-btn-start');
        const btnDone = document.getElementById('prouploader-btn-done');

        dropzone.classList.add('hidden');
        preview.classList.add('hidden');
        progress.classList.add('hidden');
        success.classList.add('hidden');
        btnStart.classList.remove('hidden');
        btnDone.classList.add('hidden');

        if (state === 'select') {
            dropzone.classList.remove('hidden');
            btnStart.disabled = true;
        } else if (state === 'preview') {
            preview.classList.remove('hidden');
            btnStart.disabled = false;
        } else if (state === 'uploading') {
            preview.classList.remove('hidden');
            progress.classList.remove('hidden');
            btnStart.disabled = true;
        } else if (state === 'success') {
            success.classList.remove('hidden');
            btnStart.classList.add('hidden');
            btnDone.classList.remove('hidden');
        }
    },

    deleteImage: function (module, field, id, label = 'gambar ini', onSuccess = null) {
        if (typeof Swal === 'undefined') {
            if (confirm(`Apakah Anda yakin ingin menghapus ${label}?`)) {
                this._sendDeleteRequest(module, field, id, onSuccess);
            }
            return;
        }

        Swal.fire({
            title: `Hapus ${label}?`,
            text: 'File gambar dan seluruh versi ukuran akan dihapus permanen.',
            icon: 'warning',
            iconColor: '#f43f5e',
            showCancelButton: true,
            confirmButtonText: '<span class="flex items-center gap-1.5 justify-center"><ion-icon name="trash-outline" class="text-base"></ion-icon> Ya, Hapus</span>',
            cancelButtonText: 'Batal',
            reverseButtons: true,
            focusCancel: true,
            buttonsStyling: false,
            customClass: {
                popup: 'rounded-2xl font-sans text-sm p-6 shadow-2xl border border-slate-100',
                title: 'text-base font-bold text-slate-800',
                confirmButton: 'px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white font-semibold rounded-xl text-xs transition-all shadow-sm hover:scale-[1.02] active:scale-[0.98] ml-2',
                cancelButton: 'px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-xl text-xs transition-all'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                this._sendDeleteRequest(module, field, id, onSuccess);
            }
        });
    },

    _sendDeleteRequest: function (module, field, id, onSuccess) {
        const formData = new FormData();
        formData.append('module', module);
        formData.append('field', field);
        formData.append('id', id);

        fetch((window.APP_URL || '') + '/upload/delete', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                window.ProToast.success(data.message || 'Gambar berhasil dihapus.');
                if (typeof onSuccess === 'function') {
                    onSuccess(data);
                } else {
                    setTimeout(() => window.location.reload(), 600);
                }
            } else {
                window.ProToast.error(data.message || 'Gagal menghapus gambar.');
            }
        })
        .catch(err => {
            window.ProToast.error('Koneksi terputus saat menghapus gambar.');
        });
    }
};

// Aliases for convenience
window.openModal = window.ProModal.open;
window.closeModal = window.ProModal.close;
window.confirmDelete = window.ProModal.confirmDelete;
window.confirmAction = window.ProModal.confirmAction;
window.showToast = window.ProToast.show;
window.uploadImage = function (options) { window.ProUploader.open(options); };

// Event Listeners for Declarative Data Attributes
document.addEventListener('DOMContentLoaded', function () {
    // 1. Data-modal-open
    document.addEventListener('click', function (e) {
        const trigger = e.target.closest('[data-modal-open]');
        if (trigger) {
            e.preventDefault();
            const targetId = trigger.getAttribute('data-modal-open');
            window.ProModal.open(targetId);
        }
    });

    // 2. Data-modal-close
    document.addEventListener('click', function (e) {
        const closeBtn = e.target.closest('[data-modal-close]');
        if (closeBtn) {
            e.preventDefault();
            const targetId = closeBtn.getAttribute('data-modal-close');
            if (targetId) {
                window.ProModal.close(targetId);
            } else {
                const parentModal = closeBtn.closest('.app-modal');
                if (parentModal) window.ProModal.close(parentModal.id);
            }
        }
    });

    // 3. Close on backdrop click
    document.addEventListener('click', function (e) {
        if (e.target.classList.contains('app-modal')) {
            window.ProModal.close(e.target.id);
        }
    });

    // 4. Close on ESC key
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' || e.key === 'Esc') {
            const openModal = document.querySelector('.app-modal:not(.hidden)');
            if (openModal) {
                window.ProModal.close(openModal.id);
            }
        }
    });

    // 5. Declarative Confirm Delete: <a href="..." data-confirm-delete="Nama Item" data-type="Perusahaan">
    document.addEventListener('click', function (e) {
        const delTrigger = e.target.closest('[data-confirm-delete]');
        if (delTrigger) {
            e.preventDefault();
            const url = delTrigger.getAttribute('href');
            const itemName = delTrigger.getAttribute('data-confirm-delete') || 'data ini';
            const itemType = delTrigger.getAttribute('data-type') || 'Data';
            window.ProModal.confirmDelete(url, itemName, itemType);
        }
    });

    // 6. Declarative Confirm Action: <a href="..." data-confirm-action="Setujui pendaftaran?" data-text="...">
    document.addEventListener('click', function (e) {
        const actionTrigger = e.target.closest('[data-confirm-action]');
        if (actionTrigger) {
            e.preventDefault();
            const url = actionTrigger.getAttribute('href');
            const title = actionTrigger.getAttribute('data-confirm-action');
            const text = actionTrigger.getAttribute('data-text') || '';
            const icon = actionTrigger.getAttribute('data-icon') || 'question';
            const confirmText = actionTrigger.getAttribute('data-btn-text') || 'Ya, Lanjutkan';
            const btnClass = actionTrigger.getAttribute('data-btn-class') || 'bg-blue-600 hover:bg-blue-700 text-white';

            window.ProModal.confirmAction(url, {
                title: title,
                text: text,
                icon: icon,
                confirmText: confirmText,
                btnClass: btnClass
            });
        }
    });

    // 7. Declarative Upload Image: <button data-upload-image data-module="perusahaan" data-field="logo" data-id="4" data-title="Upload Logo">
    document.addEventListener('click', function (e) {
        const uploadBtn = e.target.closest('[data-upload-image]');
        if (uploadBtn) {
            e.preventDefault();
            window.ProUploader.open({
                title: uploadBtn.getAttribute('data-title') || 'Upload Gambar',
                hint: uploadBtn.getAttribute('data-hint') || 'Format JPG, PNG, atau WebP (Maks 10MB)',
                module: uploadBtn.getAttribute('data-module') || 'general',
                field: uploadBtn.getAttribute('data-field') || 'image',
                id: uploadBtn.getAttribute('data-id') || null
            });
        }
    });

    // 8. Declarative Delete Image: <button data-delete-image data-module="perusahaan" data-field="logo" data-id="4" data-label="Logo Perusahaan">
    document.addEventListener('click', function (e) {
        const delBtn = e.target.closest('[data-delete-image]');
        if (delBtn) {
            e.preventDefault();
            const module = delBtn.getAttribute('data-module') || 'perusahaan';
            const field = delBtn.getAttribute('data-field') || 'image';
            const id = delBtn.getAttribute('data-id');
            const label = delBtn.getAttribute('data-label') || 'gambar ini';
            window.ProUploader.deleteImage(module, field, id, label);
        }
    });

    // 9. Check URL params for auto-open modal (e.g. ?action=create)
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('action') === 'create') {
        const autoModal = document.querySelector('.app-modal-auto-create') || document.querySelector('[id*="modalTambah"]');
        if (autoModal) {
            window.ProModal.open(autoModal.id);
        }
    }

    // 10. Auto-loading spinner animasi melingkar pada semua popup/modal saat simpan data
    document.addEventListener('submit', function (e) {
        if (e.defaultPrevented) return;
        const form = e.target;
        if (!form || !(form instanceof HTMLFormElement)) return;
        const modal = form.closest('.app-modal');
        if (!modal) return;

        // Validasi HTML5 bawaan sebelum memicu loading spinner
        if (typeof form.checkValidity === 'function' && !form.checkValidity()) {
            return;
        }

        const submitBtn = form.querySelector('button[type="submit"]') || modal.querySelector('button[type="submit"]');
        if (submitBtn && !submitBtn.dataset.noLoading) {
            submitBtn.dataset.originalHtml = submitBtn.innerHTML;
            const currentWidth = submitBtn.offsetWidth;
            if (currentWidth > 0) {
                submitBtn.style.minWidth = currentWidth + 'px';
            }
            submitBtn.disabled = true;
            submitBtn.classList.add('opacity-85', 'cursor-not-allowed');
            submitBtn.innerHTML = `
                <svg class="animate-spin -ml-0.5 mr-1.5 h-3.5 w-3.5 inline-block text-current" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span>Menyimpan...</span>
            `;
        }
    });
});
