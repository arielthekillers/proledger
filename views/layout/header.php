<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'ProLedger' ?> - Executive Dashboard</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS -->
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

    <!-- Ionicons -->
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    
    <!-- Custom Compatibility CSS -->
    <!-- <link rel="stylesheet" href="<?= APP_URL ?>/css/compat.css"> -->
    

    </head>
<body class="bg-slate-100 text-slate-800 antialiased font-sans">
    
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <aside id="sidebar" class="fixed inset-y-0 left-0 z-50 w-72 bg-slate-900 text-white transform transition-all duration-300 ease-in-out -translate-x-full md:translate-x-0 shadow-xl flex flex-col overflow-hidden">
            <!-- Brand -->
            <div class="h-20 flex items-center px-8 border-b border-white/10 whitespace-nowrap">
                <a href="<?= APP_URL ?>/" class="flex items-center gap-3 text-xl font-bold tracking-tight">
                    <div class="p-2 bg-blue-600 rounded-lg shadow-lg shadow-blue-600/20 shrink-0">
                        <ion-icon name="wallet" class="text-white text-xl md hydrated"></ion-icon>
                    </div>
                    <span class="sidebar-text transition-opacity duration-300">ProLedger</span>
                </a>
            </div>
            
            <!-- Nav -->
            <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto overflow-x-hidden">
                <?php 
                $role = $_SESSION['admin_role'] ?? 'admin';
                
                $navItemDetails = [
                    'dashboard' => ['icon' => 'grid-outline', 'label' => 'Dashboard', 'url' => APP_URL . '/', 'roles' => ['admin', 'pemodal', 'perusahaan']],
                    'paket' => ['icon' => 'cube-outline', 'label' => 'Paket Kegiatan', 'url' => APP_URL . '/paket', 'roles' => ['admin', 'pemodal', 'perusahaan']],
                    'perusahaan' => ['icon' => 'business-outline', 'label' => 'Perusahaan', 'url' => APP_URL . '/perusahaan', 'roles' => ['admin']],
                    'transaksi' => ['icon' => 'receipt-outline', 'label' => 'Transaksi', 'url' => APP_URL . '/transaksi', 'roles' => ['admin']],
                    'pemodal' => ['icon' => 'people-outline', 'label' => 'Pemodal', 'url' => APP_URL . '/pemodal', 'roles' => ['admin']],
                    'rekap' => ['icon' => 'pie-chart-outline', 'label' => 'Rekap Keuangan', 'url' => APP_URL . '/rekap', 'roles' => ['admin']],
                ];
                
                $activePage = $active ?? '';
                ?>
                
                <?php foreach($navItemDetails as $key => $nav): ?>
                    <?php if(in_array($role, $nav['roles'])): ?>
                    <a href="<?= $nav['url'] ?>" 
                       title="<?= $nav['label'] ?>"
                       class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 group whitespace-nowrap <?= $activePage === $key ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/25' : 'text-slate-400 hover:text-white hover:bg-white/5' ?>">
                        <ion-icon name="<?= $nav['icon'] ?>" class="text-xl shrink-0"></ion-icon>
                        <span class="font-medium sidebar-text transition-opacity duration-300"><?= $nav['label'] ?></span>
                        <?php if($activePage === $key): ?>
                            <ion-icon name="chevron-forward-outline" class="ml-auto opacity-70 sidebar-text"></ion-icon>
                        <?php endif; ?>
                    </a>
                    <?php endif; ?>
                <?php endforeach; ?>
            </nav>
            

        </aside>
        
        <!-- Overlay for mobile -->
        <div id="sidebarOverlay" class="fixed inset-0 bg-slate-900/50 z-40 hidden backdrop-blur-sm transition-opacity" onclick="toggleSidebar()"></div>

        <!-- Main Content -->
        <main id="mainContent" class="flex-1 md:ml-72 min-h-screen flex flex-col transition-all duration-300">
            <!-- Topbar -->
            <header class="h-20 bg-white/80 backdrop-blur-md sticky top-0 z-30 px-6 flex items-center justify-between border-b border-slate-200/60 support-backdrop-blur:bg-white/80">
                <div class="flex items-center gap-4">
                    <!-- Mobile Toggle -->
                    <button onclick="toggleSidebar()" class="md:hidden p-2 text-slate-600 hover:bg-slate-100 rounded-lg">
                        <ion-icon name="menu-outline" class="text-2xl"></ion-icon>
                    </button>
                    <!-- Desktop Toggle -->
                    <button onclick="toggleDesktopSidebar()" class="hidden md:block p-2 text-slate-600 hover:bg-slate-100 rounded-lg transition-transform active:scale-95">
                        <ion-icon name="menu-outline" class="text-2xl"></ion-icon>
                    </button>
                    
                    <div>
                        <h1 class="text-xl md:text-2xl font-bold text-slate-800"><?= $title ?? 'Dashboard' ?></h1>
                        <p class="text-xs text-slate-500 mt-0.5 hidden md:block"><?= date('l, d F Y') ?></p>
                    </div>
                </div>
                
                <!-- User Menu -->
                <div class="relative">
                    <button onclick="toggleUserMenu()" class="flex items-center gap-4 focus:outline-none group">
                        <div class="hidden md:block text-right">
                            <div class="text-sm font-semibold text-slate-800 group-hover:text-blue-600 transition-colors"><?= $_SESSION['admin_name'] ?? 'Administrator' ?></div>
                            <div class="text-xs text-slate-500 capitalize"><?= $_SESSION['admin_role'] ?? 'Admin' ?></div>
                        </div>
                        <div class="h-10 w-10 bg-gradient-to-br from-blue-500 to-blue-700 rounded-xl shadow-lg shadow-blue-500/20 flex items-center justify-center text-white font-bold group-hover:scale-105 transition-transform group-hover:shadow-blue-500/40">
                            <?= substr($_SESSION['admin_name'] ?? 'A', 0, 1) ?>
                        </div>
                    </button>
                    
                    <!-- Dropdown -->
                    <div id="userMenu" class="absolute right-0 mt-3 w-48 bg-white rounded-xl shadow-xl border border-slate-100 py-2 hidden origin-top-right transform transition-all z-50">
                        <div class="px-4 py-2 border-b border-slate-50 md:hidden bg-slate-50/50">
                            <div class="text-sm font-bold text-slate-800"><?= $_SESSION['admin_name'] ?? 'Administrator' ?></div>
                            <div class="text-xs text-slate-500 capitalize"><?= $_SESSION['admin_role'] ?? 'Admin' ?></div>
                        </div>
                        
                        <a href="<?= APP_URL ?>/" class="block px-4 py-2 text-sm text-slate-600 hover:bg-slate-50 hover:text-blue-600 flex items-center gap-2 transition-colors">
                            <ion-icon name="person-outline" class="text-lg"></ion-icon>
                            Profile
                        </a>
                        
                        <div class="h-px bg-slate-100 my-1"></div>
                        
                        <a href="<?= APP_URL ?>/logout" class="block px-4 py-2 text-sm text-red-600 hover:bg-red-50 hover:text-red-700 flex items-center gap-2 transition-colors">
                            <ion-icon name="log-out-outline" class="text-lg"></ion-icon>
                            Sign Out
                        </a>
                    </div>
                </div>
            </header>

            <!-- Page Content Wrapper -->
            <div class="p-4 md:p-8 flex-1">
                <?php if ($success = flash('success')): ?>
                    <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-lg flex items-center gap-3 shadow-sm" role="alert">
                        <ion-icon name="checkmark-circle" class="text-xl"></ion-icon>
                        <span class="block sm:inline"><?= $success ?></span>
                    </div>
                <?php endif; ?>
                
                <?php if ($error = flash('error')): ?>
                    <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg flex items-center gap-3 shadow-sm" role="alert">
                        <ion-icon name="alert-circle" class="text-xl"></ion-icon>
                        <span class="block sm:inline"><?= $error ?></span>
                    </div>
                <?php endif; ?>
