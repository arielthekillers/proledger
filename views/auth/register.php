<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun - ProLedger</title>
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

    <div class="w-full max-w-md bg-white rounded-2xl shadow-xl overflow-hidden my-8">
        <div class="p-8">
            <div class="flex flex-col items-center mb-6">
                <div class="w-12 h-12 bg-blue-600 rounded-xl flex items-center justify-center text-white text-2xl mb-3 shadow-lg shadow-blue-600/30">
                    <ion-icon name="person-add"></ion-icon>
                </div>
                <h1 class="text-2xl font-bold text-slate-800">Daftar Akun Baru</h1>
                <p class="text-slate-500 text-sm text-center">Buat akun untuk mengakses sistem ProLedger</p>
            </div>

            <?php if ($errorMsg = flash('error')): ?>
                <div class="mb-4 bg-red-50 border border-red-200 text-red-600 text-sm px-4 py-3 rounded-lg flex items-start gap-2">
                    <ion-icon name="alert-circle" class="text-lg shrink-0 mt-0.5"></ion-icon>
                    <span><?= htmlspecialchars($errorMsg) ?></span>
                </div>
            <?php endif; ?>

            <form action="<?= APP_URL ?>/register/store" method="POST" class="space-y-4">
                <?= csrfField() ?>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-lg">
                            <ion-icon name="person-outline"></ion-icon>
                        </span>
                        <input type="text" name="name" required value="<?= htmlspecialchars(old('name')) ?>"
                               class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors"
                               placeholder="Nama Anda">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Email <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-lg">
                            <ion-icon name="mail-outline"></ion-icon>
                        </span>
                        <input type="email" name="email" required value="<?= htmlspecialchars(old('email')) ?>"
                               class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors"
                               placeholder="email@contoh.com">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Nomor WhatsApp / Telepon</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-lg">
                            <ion-icon name="call-outline"></ion-icon>
                        </span>
                        <input type="tel" name="phone" value="<?= htmlspecialchars(old('phone')) ?>"
                               class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors"
                               placeholder="081234567890">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Password <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-lg">
                            <ion-icon name="lock-closed-outline"></ion-icon>
                        </span>
                        <input type="password" name="password" required minlength="6"
                               class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors"
                               placeholder="Minimal 6 karakter">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Konfirmasi Password <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-lg">
                            <ion-icon name="shield-checkmark-outline"></ion-icon>
                        </span>
                        <input type="password" name="confirm_password" required minlength="6"
                               class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors"
                               placeholder="Ulangi password">
                    </div>
                </div>

                <div class="p-3 bg-amber-50 rounded-lg border border-amber-200/70 text-xs text-amber-800 flex items-start gap-2">
                    <ion-icon name="information-circle-outline" class="text-base shrink-0 mt-0.5 text-amber-600"></ion-icon>
                    <span>Setelah mendaftar, akun Anda akan diverifikasi dan disetujui terlebih dahulu oleh Administrator sebelum dapat digunakan.</span>
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2.5 rounded-lg shadow-lg shadow-blue-600/20 active:scale-[0.98] transition-all flex items-center justify-center gap-2">
                        <span>Daftar Sekarang</span>
                        <ion-icon name="arrow-forward"></ion-icon>
                    </button>
                </div>
            </form>

            <div class="mt-6 pt-5 border-t border-slate-100 text-center">
                <p class="text-sm text-slate-600">
                    Sudah memiliki akun? 
                    <a href="<?= APP_URL ?>/login" class="text-blue-600 font-semibold hover:underline hover:text-blue-700">Masuk di sini</a>
                </p>
            </div>
        </div>
        
        <div class="bg-slate-50 px-8 py-4 border-t border-slate-100 text-center">
            <p class="text-xs text-slate-500">
                &copy; <?= date('Y') ?> ProLedger By <a href="https://sintesacorp.id" target="_blank" class="text-blue-600 hover:text-blue-700 font-semibold hover:underline">Sintesa Corp</a>
            </p>
        </div>
    </div>

</body>
</html>
