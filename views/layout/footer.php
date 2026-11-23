            </div><!-- End Page Content -->

            <!-- Main Footer -->
            <div class="py-3 border-t border-slate-200 mt-auto bg-white/50">
                <p class="text-xs text-slate-400 text-center">
                    &copy; <?= date('Y') ?> SimPaket By <a href="https://sintesacorp.id" target="_blank" class="text-blue-600 hover:text-blue-700 font-semibold hover:underline">Sintesa Corp</a>
                </p>
            </div>
        </main>
    </div><!-- End Flex Wrapper -->

    <script>
        // Mobile Navigation Toggle
        function toggleMobileNav() {
            const mobileMenu = document.getElementById('mobileNavMenu');
            if (mobileMenu) {
                mobileMenu.classList.toggle('hidden');
            }
        }

        // User Menu Logic
        function toggleUserMenu() {
            const menu = document.getElementById('userMenu');
            menu.classList.toggle('hidden');
        }

        // Company Dropdown Logic
        function toggleCompanyDropdown() {
            const menu = document.getElementById('companyDropdown');
            if (menu) menu.classList.toggle('hidden');
        }

        // Beautiful SweetAlert Confirmation Helper
        function confirmAction(url, options = {}) {
            const title = options.title || 'Konfirmasi Tindakan';
            const text = options.text || 'Apakah Anda yakin ingin melanjutkan tindakan ini?';
            const icon = options.icon || 'warning';
            const confirmButtonText = options.confirmText || 'Ya, Lanjutkan';
            const confirmButtonColor = options.confirmColor || '#2563eb';
            
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: title,
                    html: text,
                    icon: icon,
                    showCancelButton: true,
                    confirmButtonColor: confirmButtonColor,
                    cancelButtonColor: '#94a3b8',
                    confirmButtonText: confirmButtonText,
                    cancelButtonText: 'Batal',
                    reverseButtons: true,
                    customClass: {
                        popup: 'rounded-2xl font-sans',
                        confirmButton: 'rounded-xl px-4 py-2 font-semibold text-xs shadow-sm',
                        cancelButton: 'rounded-xl px-4 py-2 font-semibold text-xs'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = url;
                    }
                });
            } else {
                if (confirm(title + '\n' + text.replace(/<[^>]*>?/gm, ''))) {
                    window.location.href = url;
                }
            }
            return false;
        }

        // Close menus when clicking outside
        document.addEventListener('click', function(event) {
            const userMenu = document.getElementById('userMenu');
            const userButton = event.target.closest('button[onclick="toggleUserMenu()"]');
            if (!userButton && userMenu && !userMenu.classList.contains('hidden')) {
                userMenu.classList.add('hidden');
            }

            const compMenu = document.getElementById('companyDropdown');
            const compButton = event.target.closest('button[onclick="toggleCompanyDropdown()"]');
            if (!compButton && compMenu && !compMenu.classList.contains('hidden')) {
                compMenu.classList.add('hidden');
            }
        });
    </script>
    <!-- Global Photo Lightbox Viewer -->
    <div id="proPhotoViewer" class="fixed inset-0 z-[90] bg-slate-950/90 backdrop-blur-sm flex items-center justify-center p-4 hidden">
        <div class="relative max-w-4xl w-full flex flex-col items-center">
            <div class="w-full flex items-center justify-between text-white mb-3 px-1">
                <div class="min-w-0">
                    <div id="ppvCaption" class="text-xs sm:text-sm font-bold truncate"></div>
                    <div id="ppvCounter" class="text-[11px] text-slate-300 font-mono"></div>
                </div>
                <button type="button" onclick="closeProPhotoViewer()"
                        class="w-9 h-9 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center text-xl transition-colors cursor-pointer shrink-0">
                    <ion-icon name="close-outline"></ion-icon>
                </button>
            </div>
            <div class="relative w-full flex items-center justify-center bg-slate-900/60 rounded-2xl border border-white/10 p-2 min-h-[260px] max-h-[78vh]">
                <button type="button" id="ppvPrevBtn" onclick="stepProPhoto(-1)"
                        class="absolute left-3 z-10 w-10 h-10 rounded-full bg-black/50 hover:bg-black/80 text-white flex items-center justify-center text-xl transition-colors cursor-pointer hidden">
                    <ion-icon name="chevron-back-outline"></ion-icon>
                </button>
                <img id="ppvImage" src="" alt="Preview Foto" class="max-w-full max-h-[74vh] object-contain rounded-xl shadow-2xl">
                <button type="button" id="ppvNextBtn" onclick="stepProPhoto(1)"
                        class="absolute right-3 z-10 w-10 h-10 rounded-full bg-black/50 hover:bg-black/80 text-white flex items-center justify-center text-xl transition-colors cursor-pointer hidden">
                    <ion-icon name="chevron-forward-outline"></ion-icon>
                </button>
            </div>
        </div>
    </div>

    <script>
        let ppvPhotos = [], ppvIndex = 0;
        function openProPhotoViewer(photos, startIdx, caption) {
            const list = Array.isArray(photos) ? photos : (photos ? [photos] : []);
            if (list.length === 0) return;
            ppvPhotos = list;
            ppvIndex = Math.max(0, Math.min(startIdx || 0, list.length - 1));
            document.getElementById('ppvCaption').textContent = caption || 'Foto Dokumentasi';
            renderProPhoto();
            document.getElementById('proPhotoViewer').classList.remove('hidden');
        }
        function renderProPhoto() {
            document.getElementById('ppvImage').src = ppvPhotos[ppvIndex];
            document.getElementById('ppvCounter').textContent = (ppvIndex + 1) + ' / ' + ppvPhotos.length;
            const multi = ppvPhotos.length > 1;
            document.getElementById('ppvPrevBtn').classList.toggle('hidden', !multi);
            document.getElementById('ppvNextBtn').classList.toggle('hidden', !multi);
        }
        function stepProPhoto(dir) {
            if (ppvPhotos.length <= 1) return;
            ppvIndex = (ppvIndex + dir + ppvPhotos.length) % ppvPhotos.length;
            renderProPhoto();
        }
        function closeProPhotoViewer() {
            document.getElementById('proPhotoViewer')?.classList.add('hidden');
            const img = document.getElementById('ppvImage');
            if (img) img.src = '';
        }
        function openPhotoPreview(urlOrList, caption) {
            openProPhotoViewer(urlOrList, 0, caption);
        }
        function openPhotoLightbox(urlOrList, caption) {
            openProPhotoViewer(urlOrList, 0, caption);
        }
        document.getElementById('proPhotoViewer')?.addEventListener('click', function (e) {
            if (e.target === this) closeProPhotoViewer();
        });
        document.addEventListener('keydown', function (e) {
            const v = document.getElementById('proPhotoViewer');
            if (!v || v.classList.contains('hidden')) return;
            if (e.key === 'Escape') closeProPhotoViewer();
            else if (e.key === 'ArrowLeft') stepProPhoto(-1);
            else if (e.key === 'ArrowRight') stepProPhoto(1);
        });
    </script>

    <!-- Global ProLedger App & Modal System -->
    <script src="<?= APP_URL ?>/js/app.js?v=<?= APP_VERSION ?>"></script>
</body>
</html>
