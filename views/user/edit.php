<div class="max-w-xl mx-auto">
    <!-- Header -->
    <div class="flex items-center justify-between mb-4">
        <div>
            <h1 class="text-lg md:text-xl font-bold text-slate-800">Edit Pengguna</h1>
            <p class="text-xs text-slate-500">Perbarui profil, peran, dan hak akses pengguna</p>
        </div>
        <a href="<?= APP_URL ?>/user" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-lg transition-colors text-xs">
            <ion-icon name="arrow-back-outline" class="text-sm"></ion-icon>
            <span>Kembali</span>
        </a>
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-xl border border-slate-200/80 shadow-xs p-5">
        <form action="<?= APP_URL ?>/user/update/<?= $user['id'] ?>" method="POST" class="space-y-3.5">
            <?= csrfField() ?>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-base">
                        <ion-icon name="person-outline"></ion-icon>
                    </span>
                    <input type="text" name="name" required value="<?= htmlspecialchars($user['name']) ?>"
                           class="w-full pl-9 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:border-blue-500 text-xs focus:bg-white transition-all">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Email Address <span class="text-red-500">*</span></label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-base">
                        <ion-icon name="mail-outline"></ion-icon>
                    </span>
                    <input type="email" name="email" required value="<?= htmlspecialchars($user['email']) ?>"
                           class="w-full pl-9 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:border-blue-500 text-xs focus:bg-white transition-all">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Nomor Telepon / WhatsApp</label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-base">
                        <ion-icon name="call-outline"></ion-icon>
                    </span>
                    <input type="tel" name="phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>"
                           class="w-full pl-9 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:border-blue-500 text-xs focus:bg-white transition-all"
                           placeholder="081234567890">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Peran (Role) <span class="text-red-500">*</span></label>
                    <select name="role" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:border-blue-500 text-xs focus:bg-white transition-all" <?= ($user['id'] == ($_SESSION['admin_id'] ?? null)) ? 'disabled' : '' ?>>
                        <option value="user" <?= ($user['role'] === 'user') ? 'selected' : '' ?>>User (Biasa)</option>
                        <option value="admin" <?= ($user['role'] === 'admin') ? 'selected' : '' ?>>Admin</option>
                        <option value="pemodal" <?= ($user['role'] === 'pemodal') ? 'selected' : '' ?>>Pemodal</option>
                        <option value="perusahaan" <?= ($user['role'] === 'perusahaan') ? 'selected' : '' ?>>Perusahaan</option>
                    </select>
                    <?php if ($user['id'] == ($_SESSION['admin_id'] ?? null)): ?>
                        <input type="hidden" name="role" value="admin">
                        <p class="text-[10px] text-slate-400 mt-0.5">Tidak dapat mengubah peran akun Anda sendiri.</p>
                    <?php endif; ?>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Status Akun <span class="text-red-500">*</span></label>
                    <select name="status" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:border-blue-500 text-xs focus:bg-white transition-all" <?= ($user['id'] == ($_SESSION['admin_id'] ?? null)) ? 'disabled' : '' ?>>
                        <option value="active" <?= ($user['status'] === 'active') ? 'selected' : '' ?>>Aktif</option>
                        <option value="pending" <?= ($user['status'] === 'pending') ? 'selected' : '' ?>>Pending</option>
                        <option value="inactive" <?= ($user['status'] === 'inactive') ? 'selected' : '' ?>>Nonaktif</option>
                        <option value="rejected" <?= ($user['status'] === 'rejected') ? 'selected' : '' ?>>Ditolak</option>
                    </select>
                    <?php if ($user['id'] == ($_SESSION['admin_id'] ?? null)): ?>
                        <input type="hidden" name="status" value="active">
                        <p class="text-[10px] text-slate-400 mt-0.5">Tidak dapat menonaktifkan akun sendiri.</p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="pt-3 border-t border-slate-100">
                <label class="block text-xs font-semibold text-slate-700 mb-0.5">Ganti Password Baru</label>
                <p class="text-[10px] text-slate-400 mb-1.5">Kosongkan jika tidak ingin mengubah password.</p>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-base">
                        <ion-icon name="lock-closed-outline"></ion-icon>
                    </span>
                    <input type="password" name="password" minlength="6"
                           class="w-full pl-9 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:border-blue-500 text-xs focus:bg-white transition-all"
                           placeholder="Password baru (opsional)">
                </div>
            </div>

            <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                <a href="<?= APP_URL ?>/user" class="px-3.5 py-1.5 rounded-lg border border-slate-200 text-slate-600 font-semibold hover:bg-slate-50 text-xs transition-colors">
                    Batal
                </a>
                <button type="submit" class="px-4 py-1.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg text-xs shadow-sm transition-all flex items-center gap-1.5">
                    <ion-icon name="checkmark-outline" class="text-sm"></ion-icon>
                    <span>Simpan Perubahan</span>
                </button>
            </div>
        </form>
    </div>
</div>
