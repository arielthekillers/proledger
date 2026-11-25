<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'SimPaket' ?> - Sistem Manajemen Paket</title>
    <link rel="icon" type="image/svg+xml" href="<?= APP_URL ?>/favicon.svg">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Compiled Static Tailwind CSS -->
    <link rel="stylesheet" href="<?= APP_URL ?>/css/app.min.css?v=<?= APP_VERSION ?>">
    
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

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>window.APP_URL = '<?= APP_URL ?>';</script>
</head>
<body class="bg-slate-100 text-slate-800 antialiased font-sans text-sm">
    <?php
    $role = $_SESSION['admin_role'] ?? 'user';
    $pendingBadge = 0;
    if ($role === 'admin') {
        require_once MODEL_PATH . '/Admin.php';
        $navAdminModel = new Admin();
        $pendingBadge = $navAdminModel->getPendingCount();

        $navItemDetails = [
            'admin_dashboard'  => ['icon' => 'grid-outline', 'label' => 'Dashboard', 'url' => APP_URL . '/admin', 'roles' => ['admin']],
            'users'            => ['icon' => 'people-circle-outline', 'label' => 'Pengguna', 'url' => APP_URL . '/user', 'roles' => ['admin'], 'badge' => $pendingBadge],
            'admin_perusahaan' => ['icon' => 'business-outline', 'label' => 'Perusahaan', 'url' => APP_URL . '/admin/perusahaan', 'roles' => ['admin']],
            'admin_paket'      => ['icon' => 'cube-outline', 'label' => 'Paket', 'url' => APP_URL . '/admin/paket', 'roles' => ['admin']],
            'admin_kolaborasi' => ['icon' => 'people-outline', 'label' => 'Kolaborasi', 'url' => APP_URL . '/admin/kolaborasi', 'roles' => ['admin']],
            'tujuan'           => ['icon' => 'location-outline', 'label' => 'Tujuan', 'url' => APP_URL . '/tujuan', 'roles' => ['admin']],
            'admin_logs'       => ['icon' => 'receipt-outline', 'label' => 'Log', 'url' => APP_URL . '/admin/logs', 'roles' => ['admin']],
            'admin_storage'    => ['icon' => 'server-outline', 'label' => 'Storage', 'url' => APP_URL . '/admin/storage', 'roles' => ['admin']],
            'admin_settings'   => ['icon' => 'settings-outline', 'label' => 'Pengaturan', 'url' => APP_URL . '/admin/settings', 'roles' => ['admin']],
        ];
    } else {
        $navItemDetails = [
            'dashboard'  => ['icon' => 'grid-outline', 'label' => 'Dashboard', 'url' => APP_URL . '/', 'roles' => ['user', 'perusahaan']],
            'paket'      => ['icon' => 'cube-outline', 'label' => 'Paket Kegiatan', 'url' => APP_URL . '/paket', 'roles' => ['user', 'perusahaan']],
            'group'      => ['icon' => 'folder-open-outline', 'label' => 'Group Paket', 'url' => APP_URL . '/group', 'roles' => ['user', 'perusahaan']],
            'kolaborasi' => ['icon' => 'people-outline', 'label' => 'Kolaborasi', 'url' => APP_URL . '/kolaborasi', 'roles' => ['user', 'perusahaan']],
            'perusahaan' => ['icon' => 'business-outline', 'label' => 'Perusahaan', 'url' => APP_URL . '/perusahaan', 'roles' => ['user', 'perusahaan']],
        ];
    }
    $activePage = $active ?? '';
    ?>
    
    <div class="min-h-screen flex flex-col">
        <?php if (!empty($_SESSION['impersonator_admin'])): ?>
            <div class="bg-amber-600 text-white px-4 py-1.5 text-xs flex flex-wrap items-center justify-between gap-2 z-50">
                <div class="flex items-center gap-2 font-medium">
                    <ion-icon name="shield-half-outline" class="text-base shrink-0"></ion-icon>
                    <span>Mode Support Admin: Anda sedang login sebagai <strong><?= htmlspecialchars($_SESSION['admin_name'] ?? 'User') ?></strong> (<?= htmlspecialchars($_SESSION['admin_email'] ?? '') ?>)</span>
                </div>
                <a href="<?= APP_URL ?>/admin/stopImpersonate"
                   class="px-2.5 py-1 bg-white text-amber-800 hover:bg-amber-50 rounded-lg font-bold text-[11px] transition-colors shadow-xs">
                    Kembali ke Akun Admin
                </a>
            </div>
        <?php endif; ?>
        <!-- Top Navigation Bar -->
        <header class="bg-white/95 backdrop-blur-md sticky top-0 z-40 border-b border-slate-200/80 shadow-2xs">
            <div class="h-14 px-4 md:px-6 flex items-center justify-between gap-4">
                <!-- Left: Brand + Horizontal Nav Links -->
                <div class="flex items-center gap-4 min-w-0">
                    <!-- Mobile Nav Toggle -->
                    <button type="button" onclick="toggleMobileNav()" class="md:hidden p-1.5 text-slate-600 hover:bg-slate-100 rounded-lg cursor-pointer">
                        <ion-icon name="menu-outline" class="text-xl"></ion-icon>
                    </button>

                    <!-- Brand -->
                    <a href="<?= ($role === 'admin') ? APP_URL . '/admin' : APP_URL . '/' ?>" class="flex items-center gap-2.5 shrink-0">
                        <img src="<?= APP_URL ?>/favicon.svg" alt="SimPaket" class="w-8 h-8 rounded-lg shadow-sm">
                        <span class="font-bold text-lg tracking-tight text-slate-800">Sim<span class="text-blue-600">Paket</span></span>
                    </a>

                    <div class="hidden md:block h-5 w-px bg-slate-200"></div>

                    <!-- Desktop Horizontal Nav -->
                    <nav class="hidden md:flex items-center gap-1">
                        <?php foreach ($navItemDetails as $key => $nav): ?>
                            <?php if (in_array($role, $nav['roles'])): ?>
                                <a href="<?= $nav['url'] ?>"
                                   class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs transition-all whitespace-nowrap <?= $activePage === $key ? 'bg-blue-50 text-blue-700 font-semibold border border-blue-200/80 shadow-2xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100/80 font-medium border border-transparent' ?>">
                                    <ion-icon name="<?= $nav['icon'] ?>" class="text-base shrink-0"></ion-icon>
                                    <span><?= $nav['label'] ?></span>
                                    <?php if (!empty($nav['badge'])): ?>
                                        <span class="ml-1 bg-amber-500 text-white text-[10px] font-bold px-1.5 py-0.2 rounded-full"><?= $nav['badge'] ?></span>
                                    <?php endif; ?>
                                </a>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </nav>
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
                            <a href="<?= APP_URL ?>/perusahaan?action=create" 
                               class="flex items-center gap-2 px-3 py-2 text-blue-600 font-semibold hover:bg-blue-50 transition-colors">
                                <ion-icon name="add-circle-outline" class="text-base"></ion-icon>
                                <span>+ Tambah Perusahaan Baru</span>
                            </a>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- User Menu -->
                    <div class="relative">
                        <button onclick="toggleUserMenu()" class="flex items-center gap-2.5 focus:outline-none group cursor-pointer">
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
                            
                            <a href="<?= APP_URL ?>/profile" class="block px-3.5 py-2 text-slate-600 hover:bg-slate-50 hover:text-blue-600 flex items-center gap-2 transition-colors">
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
            </div>

            <!-- Mobile Nav Dropdown -->
            <div id="mobileNavMenu" class="hidden md:hidden border-t border-slate-100 bg-white px-4 py-2.5 space-y-1">
                <?php foreach ($navItemDetails as $key => $nav): ?>
                    <?php if (in_array($role, $nav['roles'])): ?>
                        <a href="<?= $nav['url'] ?>"
                           class="flex items-center justify-between px-3 py-2 rounded-lg text-xs transition-all <?= $activePage === $key ? 'bg-blue-50 text-blue-700 font-semibold' : 'text-slate-600 hover:bg-slate-50 font-medium' ?>">
                            <span class="flex items-center gap-2">
                                <ion-icon name="<?= $nav['icon'] ?>" class="text-base"></ion-icon>
                                <span><?= $nav['label'] ?></span>
                            </span>
                            <?php if (!empty($nav['badge'])): ?>
                                <span class="bg-amber-500 text-white text-[10px] font-bold px-1.5 py-0.2 rounded-full"><?= $nav['badge'] ?></span>
                            <?php endif; ?>
                        </a>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </header>

        <!-- Main Content (Full Width) -->
        <main id="mainContent" class="flex-1 flex flex-col w-full">
            <!-- Page Content Wrapper -->
            <div class="p-4 md:p-5 flex-1 w-full">
                <?php
                if ($role !== 'admin') {
                    require_once MODEL_PATH . '/AppSetting.php';
                    $annEnabled = AppSetting::get('announcement_enabled', '0');
                    $annMsg = trim((string)AppSetting::get('announcement_message', ''));
                    if ($annEnabled === '1' && $annMsg !== '') {
                        $annType  = AppSetting::get('announcement_type', 'info');
                        $annTitle = trim((string)AppSetting::get('announcement_title', ''));
                        $annColors = [
                            'info'    => 'bg-blue-50 border-blue-200 text-blue-800',
                            'warning' => 'bg-amber-50 border-amber-200 text-amber-800',
                            'success' => 'bg-emerald-50 border-emerald-200 text-emerald-800',
                            'danger'  => 'bg-rose-50 border-rose-200 text-rose-800',
                        ];
                        $annStyle = $annColors[$annType] ?? $annColors['info'];
                        ?>
                        <div class="mb-4 px-4 py-3 rounded-xl border <?= $annStyle ?> flex items-start gap-3 text-xs shadow-2xs">
                            <ion-icon name="megaphone-outline" class="text-lg shrink-0 mt-0.5"></ion-icon>
                            <div class="min-w-0">
                                <?php if ($annTitle !== ''): ?>
                                    <div class="font-bold mb-0.5"><?= htmlspecialchars($annTitle) ?></div>
                                <?php endif; ?>
                                <div class="leading-relaxed"><?= nl2br(htmlspecialchars($annMsg)) ?></div>
                            </div>
                        </div>
                        <?php
                    }
                }
                ?>
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
