            </div><!-- End Page Content -->

            <!-- Main Footer -->
            <div class="py-3 border-t border-slate-200 mt-auto bg-white/50">
                <p class="text-xs text-slate-400 text-center">
                    &copy; <?= date('Y') ?> PaketFlow By <a href="https://sintesacorp.id" target="_blank" class="text-blue-600 hover:text-blue-700 font-semibold hover:underline">Sintesa Corp</a>
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
    <!-- Global ProLedger App & Modal System -->
    <script src="<?= APP_URL ?>/js/app.js"></script>
</body>
</html>
