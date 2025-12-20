            </div><!-- End Page Content -->

            <!-- Main Footer -->
            <div class="py-6 border-t border-slate-200 mt-auto">
                <p class="text-sm text-slate-500 text-center">
                    &copy; <?= date('Y') ?> ProLedger By <a href="https://sintesacorp.id" target="_blank" class="text-blue-600 hover:text-blue-700 font-semibold hover:underline">Sintesa Corp</a>
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
        const sidebarHead = sidebar.querySelector('div.h-20');

        // Mobile Toggle
        function toggleSidebar() {
            if (sidebar.classList.contains('-translate-x-full')) {
                // Open
                sidebar.classList.remove('-translate-x-full');
                overlay.classList.remove('hidden');
                setTimeout(() => overlay.classList.remove('opacity-0'), 10);
            } else {
                // Close
                sidebar.classList.add('-translate-x-full');
                overlay.classList.add('opacity-0');
                setTimeout(() => overlay.classList.add('hidden'), 300);
            }
        }

        // Desktop Toggle
        function toggleDesktopSidebar() {
            const isCollapsed = sidebar.classList.contains('w-20');
            
            if (isCollapsed) {
                expandSidebar();
                localStorage.setItem('sidebarCollapsed', 'false');
            } else {
                collapseSidebar();
                localStorage.setItem('sidebarCollapsed', 'true');
            }
        }

        function collapseSidebar() {
            // Adjust Widths
            sidebar.classList.replace('w-72', 'w-20');
            mainContent.classList.replace('md:ml-72', 'md:ml-20');
            
            // Text Handling
            sidebarTexts.forEach(el => el.classList.add('hidden'));
            
            // Alignment & Styling
            navLinks.forEach(link => {
                link.classList.remove('px-4', 'gap-3');
                link.classList.add('px-2', 'justify-center');
                // Center tooltips/icons
            });
            
            // Header padding adjustment
            sidebarHead.classList.remove('px-8');
            sidebarHead.classList.add('px-0', 'justify-center');
        }

        function expandSidebar() {
            // Adjust Widths
            sidebar.classList.replace('w-20', 'w-72');
            mainContent.classList.replace('md:ml-20', 'md:ml-72');
            
            // Text Handling
            sidebarTexts.forEach(el => el.classList.remove('hidden'));
            
            // Alignment & Styling
            navLinks.forEach(link => {
                link.classList.add('px-4', 'gap-3');
                link.classList.remove('px-2', 'justify-center');
            });
            
             // Header padding adjustment
             sidebarHead.classList.add('px-8');
             sidebarHead.classList.remove('px-0', 'justify-center');
        }

        // Initialize State on Load
        document.addEventListener('DOMContentLoaded', () => {
            if (localStorage.getItem('sidebarCollapsed') === 'true') {
                // Apply collapsed state immediately without transition if possible, 
                // but for now strictly calling the function is safer
                // Disable transition temporarily to prevent animation on load
                sidebar.classList.add('duration-0');
                mainContent.classList.add('duration-0');
                
                collapseSidebar();
                
                // Re-enable transitions after a tick
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

        // Close menus when clicking outside
        document.addEventListener('click', function(event) {
            // Close User Menu
            const userMenu = document.getElementById('userMenu');
            const userButton = event.target.closest('button[onclick="toggleUserMenu()"]');
            
            if (!userButton && userMenu && !userMenu.classList.contains('hidden')) {
                userMenu.classList.add('hidden');
            }
            
            // Close Mobile Sidebar Overlay (if click is on overlay, it's already handled by onclick="toggleSidebar()")
            // But if we want to handle clicks outside sidebar in mobile mode generally:
            // This is already handled by the overlay div's onclick
        });
    </script>
</body>
</html>
