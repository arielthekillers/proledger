<!-- Header -->
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
    <div>
        <h1 class="text-lg md:text-xl font-bold text-slate-800">Manajemen Pengguna</h1>
        <p class="text-xs text-slate-500">Kelola akun pengguna sistem dan persetujuan pendaftaran</p>
    </div>
    <div>
        <a href="<?= APP_URL ?>/user/create" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition-all shadow-sm text-xs hover:scale-[1.01] active:scale-[0.98]">
            <ion-icon name="person-add-outline" class="text-base"></ion-icon>
            <span>Tambah Pengguna</span>
        </a>
    </div>
</div>

<!-- Tabs & Filter Bar -->
<div class="flex flex-wrap items-center justify-between gap-3 mb-4 border-b border-slate-200/80 pb-3">
    <div class="flex items-center gap-1.5">
        <a href="<?= APP_URL ?>/user?tab=all" 
           class="px-3 py-1.5 rounded-lg font-semibold text-xs transition-all flex items-center gap-1.5 <?= ($tab !== 'pending') ? 'bg-blue-600 text-white shadow-xs' : 'bg-white text-slate-600 hover:bg-slate-50 border border-slate-200' ?>">
            <ion-icon name="people-outline" class="text-sm"></ion-icon>
            <span>Semua Pengguna</span>
        </a>
        <a href="<?= APP_URL ?>/user?tab=pending" 
           class="px-3 py-1.5 rounded-lg font-semibold text-xs transition-all flex items-center gap-1.5 relative <?= ($tab === 'pending') ? 'bg-amber-500 text-white shadow-xs' : 'bg-white text-slate-600 hover:bg-slate-50 border border-slate-200' ?>">
            <ion-icon name="time-outline" class="text-sm"></ion-icon>
            <span>Pendaftaran Masuk</span>
            <?php if ($pendingCount > 0): ?>
                <span class="<?= ($tab === 'pending') ? 'bg-white text-amber-600' : 'bg-amber-500 text-white' ?> text-[10px] font-bold px-1.5 py-0.2 rounded-full">
                    <?= $pendingCount ?>
                </span>
            <?php endif; ?>
        </a>
    </div>

    <!-- Search & Filter Form -->
    <form action="<?= APP_URL ?>/user" method="GET" class="flex flex-wrap items-center gap-2 w-full sm:w-auto">
        <?php if ($tab === 'pending'): ?>
            <input type="hidden" name="tab" value="pending">
        <?php endif; ?>
        
        <div class="relative flex-1 sm:w-52">
            <span class="absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm">
                <ion-icon name="search-outline"></ion-icon>
            </span>
            <input type="text" name="search" value="<?= htmlspecialchars($filter['search'] ?? '') ?>" 
                   placeholder="Cari pengguna..." 
                   class="w-full pl-8 pr-3 py-1.5 bg-white border border-slate-200 rounded-lg text-xs focus:outline-none focus:border-blue-500">
        </div>

        <?php if ($tab !== 'pending'): ?>
        <select name="status" class="py-1.5 px-2.5 bg-white border border-slate-200 rounded-lg text-xs focus:outline-none focus:border-blue-500 text-slate-600">
            <option value="">Semua Status</option>
            <option value="active" <?= ($filter['status'] === 'active') ? 'selected' : '' ?>>Aktif</option>
            <option value="pending" <?= ($filter['status'] === 'pending') ? 'selected' : '' ?>>Pending</option>
            <option value="inactive" <?= ($filter['status'] === 'inactive') ? 'selected' : '' ?>>Nonaktif</option>
            <option value="rejected" <?= ($filter['status'] === 'rejected') ? 'selected' : '' ?>>Ditolak</option>
        </select>
        <?php endif; ?>

        <select name="role" class="py-1.5 px-2.5 bg-white border border-slate-200 rounded-lg text-xs focus:outline-none focus:border-blue-500 text-slate-600">
            <option value="">Semua Peran</option>
            <option value="admin" <?= ($filter['role'] === 'admin') ? 'selected' : '' ?>>Admin</option>
            <option value="user" <?= ($filter['role'] === 'user') ? 'selected' : '' ?>>User</option>
            <option value="pemodal" <?= ($filter['role'] === 'pemodal') ? 'selected' : '' ?>>Pemodal</option>
            <option value="perusahaan" <?= ($filter['role'] === 'perusahaan') ? 'selected' : '' ?>>Perusahaan</option>
        </select>

        <button type="submit" class="px-3 py-1.5 bg-slate-800 hover:bg-slate-900 text-white rounded-lg text-xs font-semibold transition-colors">
            Filter
        </button>
        <?php if (!empty($filter['search']) || !empty($filter['role']) || !empty($filter['status'])): ?>
            <a href="<?= APP_URL ?>/user<?= $tab === 'pending' ? '?tab=pending' : '' ?>" class="p-1.5 text-slate-400 hover:text-red-500 transition-colors" title="Reset Filter">
                <ion-icon name="close-circle-outline" class="text-lg"></ion-icon>
            </a>
        <?php endif; ?>
    </form>
</div>

<!-- Compact Table Card -->
<div class="bg-white rounded-xl border border-slate-200/80 shadow-xs overflow-hidden">
    <?php if (empty($users)): ?>
        <div class="py-10 text-center">
            <div class="w-12 h-12 bg-slate-50 text-slate-400 rounded-lg flex items-center justify-center mx-auto mb-2 text-xl">
                <ion-icon name="people-outline"></ion-icon>
            </div>
            <h3 class="text-xs font-bold text-slate-800">Tidak ada data pengguna</h3>
            <p class="text-[11px] text-slate-400 mt-0.5"><?= ($tab === 'pending') ? 'Tidak ada pendaftaran pengguna baru yang menunggu persetujuan.' : 'Belum ada data yang cocok dengan kriteria pencarian.' ?></p>
        </div>
    <?php else: ?>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50/75 border-b border-slate-200 text-[11px] uppercase font-bold text-slate-500 tracking-wider">
                    <tr>
                        <th class="py-2.5 px-4">Pengguna</th>
                        <th class="py-2.5 px-4">Kontak</th>
                        <th class="py-2.5 px-4">Peran (Role)</th>
                        <th class="py-2.5 px-4">Status</th>
                        <th class="py-2.5 px-4">Tanggal Daftar</th>
                        <th class="py-2.5 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php foreach ($users as $u): ?>
                    <tr class="hover:bg-slate-50/60 transition-colors">
                        <!-- User Name & Avatar -->
                        <td class="py-2.5 px-4">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-blue-500 to-indigo-600 text-white font-bold flex items-center justify-center shrink-0 text-xs shadow-xs">
                                    <?= strtoupper(substr($u['name'], 0, 1)) ?>
                                </div>
                                <div>
                                    <div class="font-bold text-slate-800 flex items-center gap-1.5">
                                        <?= htmlspecialchars($u['name']) ?>
                                        <?php if ($u['id'] == ($_SESSION['admin_id'] ?? null)): ?>
                                            <span class="text-[9px] bg-blue-100 text-blue-700 font-bold px-1.5 py-0.2 rounded">Anda</span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="text-[10px] text-slate-400">ID: #<?= $u['id'] ?></div>
                                </div>
                            </div>
                        </td>

                        <!-- Contact -->
                        <td class="py-2.5 px-4">
                            <div class="space-y-0.5 text-[11px]">
                                <div class="flex items-center gap-1 text-slate-700">
                                    <ion-icon name="mail-outline" class="text-slate-400 text-xs"></ion-icon>
                                    <span><?= htmlspecialchars($u['email']) ?></span>
                                </div>
                                <?php if (!empty($u['phone'])): ?>
                                <div class="flex items-center gap-1 text-slate-500 text-[10px]">
                                    <ion-icon name="call-outline" class="text-slate-400 text-xs"></ion-icon>
                                    <span><?= htmlspecialchars($u['phone']) ?></span>
                                </div>
                                <?php endif; ?>
                            </div>
                        </td>

                        <!-- Role -->
                        <td class="py-2.5 px-4">
                            <?php 
                            $roleColors = [
                                'admin' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                                'user' => 'bg-slate-100 text-slate-700 border-slate-200',
                                'pemodal' => 'bg-blue-50 text-blue-700 border-blue-200',
                                'perusahaan' => 'bg-purple-50 text-purple-700 border-purple-200',
                            ];
                            $roleColor = $roleColors[$u['role']] ?? 'bg-slate-100 text-slate-700 border-slate-200';
                            ?>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-semibold border <?= $roleColor ?> capitalize">
                                <?= htmlspecialchars($u['role']) ?>
                            </span>
                        </td>

                        <!-- Status -->
                        <td class="py-2.5 px-4">
                            <?php if ($u['status'] === 'active'): ?>
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    <span class="w-1 h-1 rounded-full bg-emerald-500"></span>
                                    Aktif
                                </span>
                            <?php elseif ($u['status'] === 'pending'): ?>
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-amber-50 text-amber-700 border border-amber-200 animate-pulse">
                                    <span class="w-1 h-1 rounded-full bg-amber-500"></span>
                                    Pending
                                </span>
                            <?php elseif ($u['status'] === 'rejected'): ?>
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-red-50 text-red-700 border border-red-200">
                                    <span class="w-1 h-1 rounded-full bg-red-500"></span>
                                    Ditolak
                                </span>
                            <?php else: ?>
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-slate-100 text-slate-600 border border-slate-200">
                                    <span class="w-1 h-1 rounded-full bg-slate-400"></span>
                                    Nonaktif
                                </span>
                            <?php endif; ?>
                        </td>

                        <!-- Created At -->
                        <td class="py-2.5 px-4 text-[11px] text-slate-500">
                            <?= date('d/m/Y H:i', strtotime($u['created_at'])) ?>
                        </td>

                        <!-- Actions -->
                        <td class="py-2.5 px-4 text-right">
                            <div class="flex items-center justify-end gap-1">
                                <?php if ($u['status'] === 'pending'): ?>
                                    <a href="<?= APP_URL ?>/user/approve/<?= $u['id'] ?>" 
                                       data-confirm-action="Setujui Pendaftaran Pengguna?"
                                       data-text="Akun <strong><?= htmlspecialchars($u['name']) ?></strong> akan diaktifkan dan dapat langsung login."
                                       data-icon="success"
                                       data-btn-text="Ya, Setujui"
                                       data-btn-class="bg-emerald-600 hover:bg-emerald-700 text-white"
                                       class="px-2.5 py-1 bg-emerald-600 hover:bg-emerald-700 text-white rounded text-[11px] font-semibold inline-flex items-center gap-1 shadow-xs transition-all"
                                       title="Setujui">
                                        <ion-icon name="checkmark-outline"></ion-icon>
                                        <span>Setujui</span>
                                    </a>
                                    <a href="<?= APP_URL ?>/user/reject/<?= $u['id'] ?>" 
                                       data-confirm-action="Tolak Pendaftaran Pengguna?"
                                       data-text="Pendaftaran untuk <strong><?= htmlspecialchars($u['name']) ?></strong> akan ditolak."
                                       data-icon="warning"
                                       data-btn-text="Ya, Tolak"
                                       data-btn-class="bg-rose-600 hover:bg-rose-700 text-white"
                                       class="px-2.5 py-1 bg-rose-50 hover:bg-rose-100 text-rose-600 border border-rose-200 rounded text-[11px] font-semibold inline-flex items-center gap-1 transition-all"
                                       title="Tolak">
                                        <ion-icon name="close-outline"></ion-icon>
                                        <span>Tolak</span>
                                    </a>
                                <?php else: ?>
                                    <?php if (($u['role'] ?? '') !== 'admin' && ($u['status'] ?? '') === 'active'): ?>
                                        <form method="POST" action="<?= APP_URL ?>/admin/impersonate/<?= (int)$u['id'] ?>" class="inline"
                                              onsubmit="return confirm('Login sebagai <?= htmlspecialchars(addslashes($u['name'])) ?> (Mode Support)?')">
                                            <input type="hidden" name="csrf_token" value="<?= generateCSRFToken() ?>">
                                            <button type="submit" class="p-1.5 text-slate-400 hover:text-amber-600 hover:bg-amber-50 rounded transition-colors text-base cursor-pointer" title="Login Sebagai User Ini (Support)">
                                                <ion-icon name="log-in-outline"></ion-icon>
                                            </button>
                                        </form>
                                    <?php endif; ?>

                                    <a href="<?= APP_URL ?>/user/edit/<?= $u['id'] ?>" 
                                       class="p-1.5 text-slate-500 hover:text-blue-600 hover:bg-blue-50 rounded transition-colors text-base"
                                       title="Edit">
                                        <ion-icon name="create-outline"></ion-icon>
                                    </a>

                                    <?php if ($u['id'] != ($_SESSION['admin_id'] ?? null)): ?>
                                        <a href="<?= APP_URL ?>/user/toggleStatus/<?= $u['id'] ?>" 
                                           data-confirm-action="Ubah Status Pengguna?"
                                           data-text="Ubah status akun <strong><?= htmlspecialchars($u['name']) ?></strong> menjadi <?= ($u['status'] === 'active') ? 'Nonaktif' : 'Aktif' ?>?"
                                           data-icon="question"
                                           data-btn-text="Ya, Ubah Status"
                                           class="p-1.5 <?= ($u['status'] === 'active') ? 'text-slate-400 hover:text-amber-600 hover:bg-amber-50' : 'text-slate-400 hover:text-emerald-600 hover:bg-emerald-50' ?> rounded transition-colors text-base"
                                           title="<?= ($u['status'] === 'active') ? 'Nonaktifkan' : 'Aktifkan' ?>">
                                            <ion-icon name="<?= ($u['status'] === 'active') ? 'pause-circle-outline' : 'play-circle-outline' ?>"></ion-icon>
                                        </a>

                                        <a href="<?= APP_URL ?>/user/delete/<?= $u['id'] ?>" 
                                           data-confirm-delete="<?= htmlspecialchars($u['name']) ?>"
                                           data-type="Pengguna"
                                           class="p-1.5 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded transition-colors text-base"
                                           title="Hapus">
                                            <ion-icon name="trash-outline"></ion-icon>
                                        </a>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
