<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'PaketFlow' ?> - Manajemen Paket & Distribusi</title>
    <link rel="icon" type="image/svg+xml" href="<?= APP_URL ?>/favicon.svg">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Compiled Static Tailwind CSS -->
    <link rel="stylesheet" href="<?= APP_URL ?>/css/app.min.css">
    
    <!-- Tailwind Play CDN (Development Fallback) -->
    <?php if (defined('APP_ENV') && APP_ENV === 'local'): ?>
        <script src="https://cdn.tailwindcss.com"></script>
        <script>
            tailwind.config = {
                theme: {
                    extend: {
                        fontFamily: {
                            sans: ['Plus Jakarta Sans', 'sans-serif'],
                        },
                        colors: {
                            primary: {
                                DEFAULT: '#2563EB',
                                dark: '#1E40AF',
                                light: '#EFF6FF',
                            },
                            slate: {
                                850: '#1e293b', 
                                900: '#0F172A',
                            }
                        }
                    }
                }
            }
        </script>
    <?php endif; ?>

    <!-- Ionicons -->
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>window.APP_URL = '<?= APP_URL ?>';</script>
</head>
<body class="bg-slate-100 text-slate-800 antialiased font-sans text-sm">
    
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <aside id="sidebar" class="fixed inset-y-0 left-0 z-50 w-60 bg-slate-900 text-white transform transition-all duration-300 ease-in-out -translate-x-full md:translate-x-0 shadow-lg flex flex-col overflow-hidden">
            <!-- Brand -->
            <div class="h-14 flex items-center px-5 border-b border-white/10 whitespace-nowrap">
                <?php $role = $_SESSION['admin_role'] ?? 'user'; ?>
                <a href="<?= ($role === 'admin') ? APP_URL . '/user' : APP_URL . '/' ?>" class="flex items-center gap-2.5 text-lg font-bold tracking-tight">
                    <div class="w-8 h-8 rounded-lg bg-gradient-to-tr from-blue-600 to-indigo-500 shadow-md shadow-blue-500/30 shrink-0 flex items-center justify-center">
                        <ion-icon name="cube" class="text-white text-lg md hydrated"></ion-icon>
                    </div>
                    <span class="sidebar-text font-bold text-lg tracking-tight transition-opacity duration-300">Paket<span class="text-blue-400">Flow</span></span>
                </a>
            </div>
            
            <!-- Nav -->
            <nav class="flex-1 px-3 py-3 space-y-1 overflow-y-auto overflow-x-hidden">
                <?php 
                $pendingBadge = 0;
                if ($role === 'admin') {
                    require_once MODEL_PATH . '/Admin.php';
                    $navAdminModel = new Admin();
                    $pendingBadge = $navAdminModel->getPendingCount();

                    // Admin KHUSUS Management User saja (Tidak ada menu proyek)
                    $navItemDetails = [
                        'users' => ['icon' => 'people-circle-outline', 'label' => 'Kelola Pengguna', 'url' => APP_URL . '/user', 'roles' => ['admin'], 'badge' => $pendingBadge],
                    ];
                } else {
                    // Menu untuk User / Pelaksana Proyek
                    $navItemDetails = [
                        'dashboard' => ['icon' => 'grid-outline', 'label' => 'Dashboard', 'url' => APP_URL . '/', 'roles' => ['user', 'perusahaan']],
                        'paket' => ['icon' => 'cube-outline', 'label' => 'Paket Kegiatan', 'url' => APP_URL . '/paket', 'roles' => ['user', 'perusahaan']],
                        'group' => ['icon' => 'folder-open-outline', 'label' => 'Group Paket', 'url' => APP_URL . '/group', 'roles' => ['user', 'perusahaan']],
                        'perusahaan' => ['icon' => 'business-outline', 'label' => 'Perusahaan', 'url' => APP_URL . '/perusahaan', 'roles' => ['user', 'perusahaan']],
                    ];
                }
                
                $activePage = $active ?? '';
                ?>
                
                <?php foreach($navItemDetails as $key => $nav): ?>
                    <?php if(in_array($role, $nav['roles'])): ?>
                    <a href="<?= $nav['url'] ?>" 
                       title="<?= $nav['label'] ?>"
                       class="flex items-center gap-2.5 px-3 py-2 rounded-lg transition-all duration-200 group whitespace-nowrap text-sm <?= $activePage === $key ? 'bg-blue-600 text-white shadow-md shadow-blue-600/25 font-semibold' : 'text-slate-400 hover:text-white hover:bg-white/5 font-medium' ?>">
                        <ion-icon name="<?= $nav['icon'] ?>" class="text-lg shrink-0"></ion-icon>
                        <span class="sidebar-text transition-opacity duration-300"><?= $nav['label'] ?></span>
                        <?php if(!empty($nav['badge'])): ?>
                            <span class="ml-auto bg-amber-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full sidebar-text shadow-sm"><?= $nav['badge'] ?></span>
                        <?php elseif($activePage === $key): ?>
                            <ion-icon name="chevron-forward-outline" class="ml-auto opacity-70 text-xs sidebar-text"></ion-icon>
                        <?php endif; ?>
                    </a>
                    <?php endif; ?>
                <?php endforeach; ?>
            </nav>
        </aside>
        
        <!-- Overlay for mobile -->
        <div id="sidebarOverlay" class="fixed inset-0 bg-slate-900/50 z-40 hidden backdrop-blur-sm transition-opacity" onclick="toggleSidebar()"></div>

        <!-- Main Content -->
        <main id="mainContent" class="flex-1 md:ml-60 min-h-screen flex flex-col transition-all duration-300">
            <!-- Topbar -->
            <header class="h-14 bg-white/90 backdrop-blur-md sticky top-0 z-30 px-5 flex items-center justify-between border-b border-slate-200/70 support-backdrop-blur:bg-white/90">
                <div class="flex items-center gap-3">
                    <!-- Mobile Toggle -->
                    <button onclick="toggleSidebar()" class="md:hidden p-1.5 text-slate-600 hover:bg-slate-100 rounded-lg">
                        <ion-icon name="menu-outline" class="text-xl"></ion-icon>
                    </button>
                    <!-- Desktop Toggle -->
                    <button onclick="toggleDesktopSidebar()" class="hidden md:block p-1.5 text-slate-600 hover:bg-slate-100 rounded-lg transition-transform active:scale-95">
                        <ion-icon name="menu-outline" class="text-xl"></ion-icon>
                    </button>
                    
                    <div>
                        <h1 class="text-base md:text-lg font-bold text-slate-800 leading-tight"><?= $title ?? 'Dashboard' ?></h1>
                        <p class="text-[11px] text-slate-400 hidden md:block"><?= date('l, d F Y') ?></p>
                    </div>
                </div>
                
                <!-- Right Controls: Company Switcher & User Profile -->
                <div class="flex items-center gap-3">
                    <?php if ($role !== 'admin'): ?>
                    <!-- Company Switcher Dropdown (Single Active Company) -->
                    <?php
                    $hdrActiveComp = getActivePerusahaan();
                    $hdrUserId = $_SESSION['admin_id'] ?? 0;
                    require_once MODEL_PATH . '/Perusahaan.php';
                    $hdrPerusahaanModel = new Perusahaan();
                    $hdrCompanies = $hdrPerusahaanModel->getByUser($hdrUserId);
                    $currentActiveCompId = $hdrActiveComp['id'] ?? null;
                    $currentActiveCompName = $hdrActiveComp['nama'] ?? 'Belum Ada Perusahaan';
                    ?>

                    <div class="relative">
                        <button onclick="toggleCompanyDropdown()" class="flex items-center gap-1.5 px-3 py-1.5 <?= $currentActiveCompId ? 'bg-slate-50 hover:bg-slate-100 text-slate-700' : 'bg-amber-50 hover:bg-amber-100 text-amber-800 border-amber-200' ?> border border-slate-200/90 rounded-lg text-xs font-semibold transition-all focus:outline-none">
                            <ion-icon name="business" class="<?= $currentActiveCompId ? 'text-blue-600' : 'text-amber-600' ?> text-sm shrink-0"></ion-icon>
                            <span class="max-w-[110px] sm:max-w-[170px] truncate text-left">
                                <?= htmlspecialchars($currentActiveCompName) ?>
                            </span>
                            <ion-icon name="chevron-down-outline" class="text-slate-400 text-xs shrink-0"></ion-icon>
                        </button>
                        
                        <!-- Dropdown Menu -->
                        <div id="companyDropdown" class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-xl border border-slate-100 py-1.5 hidden origin-top-right z-50 text-xs">
                            <div class="px-3 py-1.5 text-[10px] font-bold text-slate-400 uppercase tracking-wider flex items-center justify-between">
                                <span>Perusahaan Aktif</span>
                                <span class="bg-slate-100 text-slate-500 px-1.5 py-0.2 rounded font-bold"><?= count($hdrCompanies) ?></span>
                            </div>

                            <!-- Daftar Perusahaan Milik User -->
                            <div class="max-h-52 overflow-y-auto divide-y divide-slate-50">
                                <?php if (empty($hdrCompanies)): ?>
                                    <div class="px-3 py-3 text-slate-400 text-[11px] italic text-center">
                                        Belum ada perusahaan terdaftar
                                    </div>
                                <?php else: ?>
                                    <?php foreach ($hdrCompanies as $hc): ?>
                                        <a href="<?= APP_URL ?>/perusahaan/switch/<?= $hc['id'] ?>" 
                                           class="flex items-center justify-between px-3 py-2 text-slate-700 hover:bg-slate-50 hover:text-blue-600 transition-colors <?= ($currentActiveCompId == $hc['id']) ? 'font-bold bg-blue-50/50 text-blue-600' : '' ?>">
                                            <div class="flex items-center gap-2 truncate pr-2">
                                                <span class="w-2 h-2 rounded-full shrink-0 <?= ($currentActiveCompId == $hc['id']) ? 'bg-emerald-500' : 'bg-slate-300' ?>"></span>
                                                <span class="truncate"><?= htmlspecialchars($hc['nama']) ?></span>
                                            </div>
                                            <?php if ($currentActiveCompId == $hc['id']): ?>
                                                <ion-icon name="checkmark" class="text-blue-600 text-sm shrink-0"></ion-icon>
                                            <?php endif; ?>
                                        </a>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>

                            <div class="h-px bg-slate-100 my-1"></div>

                            <!-- Tambah Perusahaan -->
                            <a href="<?= APP_URL ?>/perusahaan/create" 
                               class="flex items-center gap-2 px-3 py-2 text-blue-600 font-semibold hover:bg-blue-50 transition-colors">
                                <ion-icon name="add-circle-outline" class="text-base"></ion-icon>
                                <span>+ Tambah Perusahaan Baru</span>
                            </a>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- User Menu -->
                    <div class="relative">
                        <button onclick="toggleUserMenu()" class="flex items-center gap-2.5 focus:outline-none group">
                            <div class="hidden md:block text-right">
                                <div class="text-xs font-bold text-slate-800 group-hover:text-blue-600 transition-colors"><?= $_SESSION['admin_name'] ?? 'Administrator' ?></div>
                                <div class="text-[10px] text-slate-400 capitalize"><?= $_SESSION['admin_role'] ?? 'Admin' ?></div>
                            </div>
                            <div class="h-8 w-8 bg-gradient-to-br from-blue-500 to-blue-700 rounded-lg shadow-sm flex items-center justify-center text-white text-xs font-bold group-hover:scale-105 transition-transform">
                                <?= strtoupper(substr($_SESSION['admin_name'] ?? 'A', 0, 1)) ?>
                            </div>
                        </button>
                        
                        <!-- Dropdown -->
                        <div id="userMenu" class="absolute right-0 mt-2 w-44 bg-white rounded-xl shadow-xl border border-slate-100 py-1.5 hidden origin-top-right transform transition-all z-50 text-xs">
                            <div class="px-3.5 py-2 border-b border-slate-50 md:hidden bg-slate-50/50">
                                <div class="text-xs font-bold text-slate-800"><?= $_SESSION['admin_name'] ?? 'Administrator' ?></div>
                                <div class="text-[10px] text-slate-400 capitalize"><?= $_SESSION['admin_role'] ?? 'Admin' ?></div>
                            </div>
                            
                            <a href="<?= ($role === 'admin') ? APP_URL . '/user' : APP_URL . '/' ?>" class="block px-3.5 py-2 text-slate-600 hover:bg-slate-50 hover:text-blue-600 flex items-center gap-2 transition-colors">
                                <ion-icon name="person-outline" class="text-base"></ion-icon>
                                Profile
                            </a>
                            
                            <div class="h-px bg-slate-100 my-1"></div>
                            
                            <a href="<?= APP_URL ?>/logout" class="block px-3.5 py-2 text-red-600 hover:bg-red-50 hover:text-red-700 flex items-center gap-2 transition-colors">
                                <ion-icon name="log-out-outline" class="text-base"></ion-icon>
                                Sign Out
                            </a>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content Wrapper -->
            <div class="p-4 md:p-5 flex-1">
                <?php 
                $flashSuccess = flash('success');
                $flashError = flash('error');
                $flashWarning = flash('warning');
                $flashInfo = flash('info');
                ?>
                <?php if ($flashSuccess || $flashError || $flashWarning || $flashInfo): ?>
                    <script>
                        document.addEventListener('DOMContentLoaded', function () {
                            if (window.ProToast) {
                                <?php if ($flashSuccess): ?>
                                    window.ProToast.success(<?= json_encode($flashSuccess) ?>);
                                <?php endif; ?>
                                <?php if ($flashError): ?>
                                    window.ProToast.error(<?= json_encode($flashError) ?>);
                                <?php endif; ?>
                                <?php if ($flashWarning): ?>
                                    window.ProToast.warning(<?= json_encode($flashWarning) ?>);
                                <?php endif; ?>
                                <?php if ($flashInfo): ?>
                                    window.ProToast.info(<?= json_encode($flashInfo) ?>);
                                <?php endif; ?>
                            }
                        });
                    </script>
                <?php endif; ?>
