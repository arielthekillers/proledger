<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - PaketFlow</title>
    <link rel="icon" type="image/svg+xml" href="<?= APP_URL ?>/favicon.svg">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= APP_URL ?>/css/app.min.css">
    <?php if (defined('APP_ENV') && APP_ENV === 'local'): ?>
        <script src="https://cdn.tailwindcss.com"></script>
    <?php endif; ?>
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-slate-900 flex items-center justify-center min-h-screen p-4">

    <div class="w-full max-w-sm bg-white rounded-2xl shadow-xl overflow-hidden">
        <div class="p-8">
            <div class="flex flex-col items-center mb-8">
                <div class="w-12 h-12 bg-gradient-to-tr from-blue-600 to-indigo-500 rounded-xl flex items-center justify-center text-white text-2xl mb-3 shadow-lg shadow-blue-500/30">
                    <ion-icon name="cube"></ion-icon>
                </div>
                <h1 class="text-2xl font-bold text-slate-800">Paket<span class="text-blue-600">Flow</span></h1>
                <p class="text-slate-500 text-sm">Masuk ke Sistem Manajemen Paket</p>
            </div>

            <?php 
            $errorMsg = flash('error') ?? ($_SESSION['error'] ?? null);
            if (isset($_SESSION['error'])) unset($_SESSION['error']);
            $successMsg = flash('success') ?? ($_SESSION['success'] ?? null);
            if (isset($_SESSION['success'])) unset($_SESSION['success']);
            ?>

            <?php if ($errorMsg): ?>
                <div class="mb-4 bg-red-50 border border-red-200 text-red-600 text-sm px-4 py-3 rounded-lg flex items-start gap-2">
                    <ion-icon name="alert-circle" class="text-lg shrink-0 mt-0.5"></ion-icon>
                    <span><?= htmlspecialchars($errorMsg) ?></span>
                </div>
            <?php endif; ?>

            <?php if ($successMsg): ?>
                <div class="mb-4 bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm px-4 py-3 rounded-lg flex items-start gap-2">
                    <ion-icon name="checkmark-circle" class="text-lg shrink-0 mt-0.5"></ion-icon>
                    <span><?= htmlspecialchars($successMsg) ?></span>
                </div>
            <?php endif; ?>

            <form action="<?= APP_URL ?>/login/auth" method="POST" class="space-y-4">
                <?= csrfField() ?>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Email Address</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-lg">
                            <ion-icon name="mail-outline"></ion-icon>
                        </span>
                        <input type="email" name="email" required 
                               class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors"
                               placeholder="nama@email.com">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Password</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-lg">
                            <ion-icon name="lock-closed-outline"></ion-icon>
                        </span>
                        <input type="password" name="password" required 
                               class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors"
                               placeholder="••••••••">
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2.5 rounded-lg shadow-lg shadow-blue-600/20 active:scale-[0.98] transition-all flex items-center justify-center gap-2">
                        <span>Sign In</span>
                        <ion-icon name="arrow-forward"></ion-icon>
                    </button>
                </div>
            </form>

            <div class="mt-6 pt-5 border-t border-slate-100 text-center">
                <p class="text-sm text-slate-600">
                    Belum punya akun? 
                    <a href="<?= APP_URL ?>/register" class="text-blue-600 font-semibold hover:underline hover:text-blue-700">Daftar Akun</a>
                </p>
            </div>
        </div>
        
        <div class="bg-slate-50 px-8 py-4 border-t border-slate-100 text-center">
            <p class="text-xs text-slate-500">
                &copy; <?= date('Y') ?> PaketFlow By <a href="https://sintesacorp.id" target="_blank" class="text-blue-600 hover:text-blue-700 font-semibold hover:underline">Sintesa Corp</a>
            </p>
        </div>
    </div>

</body>
</html>
