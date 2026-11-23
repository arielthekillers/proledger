<?php
/**
 * ProLedger - Profil Pengguna & Ganti Password
 */
$roleLabel = ucfirst($user['role'] ?? 'user');
$initial = strtoupper(substr($user['name'] ?? 'U', 0, 1));
?>

<div class="max-w-4xl mx-auto">
    <!-- Header -->
    <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-lg md:text-xl font-extrabold text-slate-900 tracking-tight">Profil &amp; Keamanan Akun</h1>
            <p class="text-xs text-slate-400">Kelola informasi pribadi dan perbarui password akun Anda.</p>
        </div>
        <a href="<?= ($role === 'admin') ? APP_URL . '/user' : APP_URL . '/' ?>"
           class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white hover:bg-slate-50 text-slate-700 font-semibold rounded-xl border border-slate-200 text-xs shadow-2xs">
            <ion-icon name="arrow-back-outline" class="text-sm"></ion-icon>
            <span>Kembali</span>
        </a>
    </div>

    <!-- Summary Banner -->
    <div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 p-5 mb-5 flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center gap-4 min-w-0">
            <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-blue-600 to-indigo-700 text-white flex items-center justify-center text-xl font-extrabold shadow-sm shrink-0">
                <?= htmlspecialchars($initial) ?>
            </div>
            <div class="min-w-0">
                <div class="flex items-center gap-2 flex-wrap">
                    <h2 class="text-base font-extrabold text-slate-900 truncate"><?= htmlspecialchars($user['name']) ?></h2>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                        <?= htmlspecialchars($roleLabel) ?>
                    </span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                        Aktif
                    </span>
                </div>
                <p class="text-xs text-slate-500 mt-0.5 truncate"><?= htmlspecialchars($user['email']) ?></p>
                <?php if (!empty($user['created_at'])): ?>
                    <p class="text-[11px] text-slate-400 mt-0.5">Terdaftar sejak <?= date('d M Y', strtotime($user['created_at'])) ?></p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- 2-Column Forms -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        <!-- Card 1: Update Profile -->
        <div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 overflow-hidden">
            <div class="px-5 py-3.5 bg-slate-50/80 border-b border-slate-100 flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center text-base">
                    <ion-icon name="person-outline"></ion-icon>
                </div>
                <div>
                    <h3 class="font-bold text-slate-800 text-xs uppercase tracking-wider">Informasi Profil</h3>
                    <p class="text-[11px] text-slate-400">Nama, email login, dan nomor telepon</p>
                </div>
            </div>

            <form method="POST" action="<?= APP_URL ?>/profile/updateProfile" class="p-5 space-y-3.5">
                <?= csrfField() ?>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
                    <input type="text" name="name" required value="<?= htmlspecialchars($user['name']) ?>"
                           class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-blue-500 text-xs focus:bg-white transition-all">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Email <span class="text-red-500">*</span></label>
                    <input type="email" name="email" required value="<?= htmlspecialchars($user['email']) ?>"
                           class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-blue-500 text-xs focus:bg-white transition-all">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Nomor Telepon / WhatsApp</label>
                    <input type="text" name="phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>"
                           placeholder="08xxxxxxxxxx"
                           class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-blue-500 text-xs focus:bg-white transition-all">
                </div>

                <div class="pt-2 flex justify-end">
                    <button type="submit"
                            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-semibold shadow-xs transition-all inline-flex items-center gap-1.5 cursor-pointer">
                        <ion-icon name="checkmark-outline" class="text-sm"></ion-icon>
                        <span>Simpan Profil</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Card 2: Change Password -->
        <div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 overflow-hidden">
            <div class="px-5 py-3.5 bg-slate-50/80 border-b border-slate-100 flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center text-base">
                    <ion-icon name="lock-closed-outline"></ion-icon>
                </div>
                <div>
                    <h3 class="font-bold text-slate-800 text-xs uppercase tracking-wider">Ganti Password</h3>
                    <p class="text-[11px] text-slate-400">Minimal 6 karakter demi keamanan akun</p>
                </div>
            </div>

            <form method="POST" action="<?= APP_URL ?>/profile/updatePassword" class="p-5 space-y-3.5">
                <?= csrfField() ?>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Password Saat Ini <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <input type="password" name="current_password" id="prof_cur_pass" required
                               class="w-full pl-3 pr-9 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-amber-500 text-xs focus:bg-white transition-all">
                        <button type="button" onclick="togglePassField('prof_cur_pass', this)"
                                class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 cursor-pointer">
                            <ion-icon name="eye-outline"></ion-icon>
                        </button>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Password Baru <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <input type="password" name="new_password" id="prof_new_pass" required minlength="6"
                               class="w-full pl-3 pr-9 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-amber-500 text-xs focus:bg-white transition-all">
                        <button type="button" onclick="togglePassField('prof_new_pass', this)"
                                class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 cursor-pointer">
                            <ion-icon name="eye-outline"></ion-icon>
                        </button>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Konfirmasi Password Baru <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <input type="password" name="confirm_password" id="prof_conf_pass" required minlength="6"
                               class="w-full pl-3 pr-9 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-amber-500 text-xs focus:bg-white transition-all">
                        <button type="button" onclick="togglePassField('prof_conf_pass', this)"
                                class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 cursor-pointer">
                            <ion-icon name="eye-outline"></ion-icon>
                        </button>
                    </div>
                </div>

                <div class="pt-2 flex justify-end">
                    <button type="submit"
                            class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-xl text-xs font-semibold shadow-xs transition-all inline-flex items-center gap-1.5 cursor-pointer">
                        <ion-icon name="key-outline" class="text-sm"></ion-icon>
                        <span>Ubah Password</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function togglePassField(inputId, btn) {
    const inp = document.getElementById(inputId);
    if (!inp) return;
    const isPass = inp.type === 'password';
    inp.type = isPass ? 'text' : 'password';
    const icon = btn.querySelector('ion-icon');
    if (icon) icon.setAttribute('name', isPass ? 'eye-off-outline' : 'eye-outline');
}
</script>
