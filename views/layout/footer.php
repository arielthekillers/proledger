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
        // DOM Elements
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');
        const mainContent = document.getElementById('mainContent');
        const sidebarTexts = document.querySelectorAll('.sidebar-text');
        const navLinks = sidebar.querySelectorAll('nav a');
        const sidebarHead = sidebar.querySelector('div.h-14') || sidebar.firstElementChild;

        // Mobile Toggle
        function toggleSidebar() {
            if (sidebar.classList.contains('-translate-x-full')) {
                sidebar.classList.remove('-translate-x-full');
                overlay.classList.remove('hidden');
                setTimeout(() => overlay.classList.remove('opacity-0'), 10);
            } else {
                sidebar.classList.add('-translate-x-full');
                overlay.classList.add('opacity-0');
                setTimeout(() => overlay.classList.add('hidden'), 300);
            }
        }

        // Desktop Toggle
        function toggleDesktopSidebar() {
            const isCollapsed = sidebar.classList.contains('w-16');
            
            if (isCollapsed) {
                expandSidebar();
                localStorage.setItem('sidebarCollapsed', 'false');
            } else {
                collapseSidebar();
                localStorage.setItem('sidebarCollapsed', 'true');
            }
        }

        function collapseSidebar() {
            sidebar.classList.replace('w-60', 'w-16');
            mainContent.classList.replace('md:ml-60', 'md:ml-16');
            
            sidebarTexts.forEach(el => el.classList.add('hidden'));
            
            navLinks.forEach(link => {
                link.classList.remove('px-3', 'gap-2.5');
                link.classList.add('px-2', 'justify-center');
            });
            
            if (sidebarHead) {
                sidebarHead.classList.remove('px-5');
                sidebarHead.classList.add('px-0', 'justify-center');
            }
        }

        function expandSidebar() {
            sidebar.classList.replace('w-16', 'w-60');
            mainContent.classList.replace('md:ml-16', 'md:ml-60');
            
            sidebarTexts.forEach(el => el.classList.remove('hidden'));
            
            navLinks.forEach(link => {
                link.classList.add('px-3', 'gap-2.5');
                link.classList.remove('px-2', 'justify-center');
            });
            
            if (sidebarHead) {
                sidebarHead.classList.add('px-5');
                sidebarHead.classList.remove('px-0', 'justify-center');
            }
        }

        // Initialize State on Load
        document.addEventListener('DOMContentLoaded', () => {
            if (localStorage.getItem('sidebarCollapsed') === 'true') {
                sidebar.classList.add('duration-0');
                mainContent.classList.add('duration-0');
                
                collapseSidebar();
                
                requestAnimationFrame(() => {
                    setTimeout(() => {
                        sidebar.classList.remove('duration-0');
                        mainContent.classList.remove('duration-0');
                    }, 50);
                });
            }
        });

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
