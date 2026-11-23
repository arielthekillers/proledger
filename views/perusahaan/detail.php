<?php
/**
 * ProLedger - Detail Profil Perusahaan & Branding Assets
 * Fokus murni pada identitas perusahaan, profil kontak, dan manajemen aset visual:
 * - Logo Perusahaan (Large, Medium, Small)
 * - Avatar Perusahaan (Large, Medium, Small)
 * - Kop Surat Resmi (Large, Medium, Small)
 */

$isActive = (($_SESSION['active_perusahaan_id'] ?? null) == $perusahaan['id']);

// Dapatkan URL gambar untuk ketiga versi jika sudah diupload
$logoUrls = !empty($perusahaan['logo']) ? Uploader::getAllUrls($perusahaan['logo'], 'perusahaan', 'logo') : null;
$avatarUrls = !empty($perusahaan['avatar']) ? Uploader::getAllUrls($perusahaan['avatar'], 'perusahaan', 'avatar') : null;
$kopsuratUrls = !empty($perusahaan['kopsurat']) ? Uploader::getAllUrls($perusahaan['kopsurat'], 'perusahaan', 'kopsurat') : null;
?>

<!-- ========================================== -->
<!-- 1. TOP BAR NAVIGATION & QUICK ACTIONS     -->
<!-- ========================================== -->
<div class="mb-5 flex flex-wrap items-center justify-between gap-3">
    <div class="flex items-center gap-2">
        <a href="<?= APP_URL ?>/perusahaan" 
           class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white hover:bg-slate-50 text-slate-700 font-semibold rounded-xl border border-slate-200 transition-all text-xs shadow-2xs hover:shadow-xs">
            <ion-icon name="arrow-back-outline" class="text-sm"></ion-icon>
            <span>Kembali ke Daftar</span>
        </a>

        <?php if ($isActive): ?>
            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span>Perusahaan Aktif</span>
            </span>
        <?php endif; ?>
    </div>

    <div class="flex items-center gap-2">
        <?php if (!$isActive): ?>
            <a href="<?= APP_URL ?>/perusahaan/switch/<?= $perusahaan['id'] ?>" 
               class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-50 text-blue-700 hover:bg-blue-100 rounded-xl text-xs font-semibold border border-blue-200 transition-colors">
                <ion-icon name="swap-horizontal-outline" class="text-sm"></ion-icon>
                <span>Jadikan Perusahaan Aktif</span>
            </a>
        <?php endif; ?>

        <!-- Edit Profile Trigger Modal -->
        <button type="button" 
                onclick='openEditPerusahaanModal(<?= json_encode($perusahaan, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)'
                class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-slate-900 hover:bg-slate-800 text-white rounded-xl transition-all text-xs font-semibold shadow-xs hover:scale-[1.01] active:scale-[0.98]">
            <ion-icon name="create-outline" class="text-sm"></ion-icon>
            <span>Edit Profil</span>
        </button>
    </div>
</div>

<!-- ========================================== -->
<!-- 2. COMPANY PROFILE & IDENTITY CARD        -->
<!-- ========================================== -->
<div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 p-5 mb-5">
    <div class="flex flex-col md:flex-row items-start gap-4">
        <!-- Avatar / Icon Display -->
        <div class="relative shrink-0">
            <?php if ($avatarUrls && !empty($avatarUrls['medium'])): ?>
                <img src="<?= $avatarUrls['medium'] ?>" alt="<?= htmlspecialchars($perusahaan['nama']) ?>" 
                     class="w-16 h-16 md:w-20 md:h-20 rounded-2xl object-cover bg-white shadow-xs border border-slate-200">
            <?php else: ?>
                <div class="w-16 h-16 md:w-20 md:h-20 rounded-2xl bg-gradient-to-br from-blue-600 to-indigo-700 text-white shadow-xs flex items-center justify-center text-xl md:text-2xl font-extrabold tracking-wider">
                    <?= strtoupper(substr($perusahaan['nama'], 0, 2)) ?>
                </div>
            <?php endif; ?>
            
            <!-- Quick Change Avatar Button -->
            <button type="button" 
                    data-upload-image 
                    data-module="perusahaan" 
                    data-field="avatar" 
                    data-id="<?= $perusahaan['id'] ?>" 
                    data-title="Upload Avatar Profil"
                    data-hint="Disarankan rasio 1:1 (PNG, JPG, WebP)"
                    class="absolute -bottom-1 -right-1 w-6 h-6 bg-white hover:bg-blue-50 text-blue-600 rounded-full border border-slate-200 shadow-sm flex items-center justify-center text-xs transition-all hover:scale-110"
                    title="Upload / Ganti Avatar">
                <ion-icon name="camera"></ion-icon>
            </button>
        </div>

        <!-- Company Text & Info -->
        <div class="flex-1 min-w-0">
            <div class="flex flex-wrap items-center justify-between gap-2 mb-1">
                <h1 class="text-lg md:text-xl font-extrabold text-slate-900 tracking-tight leading-tight">
                    <?= htmlspecialchars($perusahaan['nama']) ?>
                </h1>
            </div>

            <p class="text-xs text-slate-400 mb-3 flex items-center gap-1.5">
                <ion-icon name="calendar-outline"></ion-icon>
                <span>Terdaftar di SimPaket sejak <?= date('d M Y', strtotime($perusahaan['created_at'])) ?></span>
                <?php if (!empty($perusahaan['owner_name'])): ?>
                    <span class="text-slate-300">&bull;</span>
                    <span class="text-slate-500 font-medium">Pemilik: <?= htmlspecialchars($perusahaan['owner_name']) ?></span>
                <?php endif; ?>
            </p>

            <!-- Keterangan / Deskripsi -->
            <?php if (!empty($perusahaan['keterangan'])): ?>
                <p class="text-xs text-slate-600 bg-slate-50/80 p-3 rounded-xl border border-slate-100 mb-4 leading-relaxed">
                    <?= nl2br(htmlspecialchars($perusahaan['keterangan'])) ?>
                </p>
            <?php endif; ?>

            <!-- Contacts & Info Pills -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 pt-3 border-t border-slate-100">
                <!-- Email -->
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center text-sm shrink-0">
                        <ion-icon name="mail-outline"></ion-icon>
                    </div>
                    <div class="min-w-0">
                        <span class="block text-[10px] text-slate-400 font-semibold uppercase">Email</span>
                        <?php if (!empty($perusahaan['email'])): ?>
                            <a href="mailto:<?= htmlspecialchars($perusahaan['email']) ?>" class="text-xs font-semibold text-slate-700 hover:text-blue-600 truncate block transition-colors">
                                <?= htmlspecialchars($perusahaan['email']) ?>
                            </a>
                        <?php else: ?>
                            <span class="text-xs text-slate-400 italic">-</span>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Telepon -->
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center text-sm shrink-0">
                        <ion-icon name="call-outline"></ion-icon>
                    </div>
                    <div class="min-w-0">
                        <span class="block text-[10px] text-slate-400 font-semibold uppercase">Telepon / WA</span>
                        <?php if (!empty($perusahaan['telepon'])): ?>
                            <a href="tel:<?= htmlspecialchars($perusahaan['telepon']) ?>" class="text-xs font-semibold text-slate-700 hover:text-emerald-600 truncate block transition-colors">
                                <?= htmlspecialchars($perusahaan['telepon']) ?>
                            </a>
                        <?php else: ?>
                            <span class="text-xs text-slate-400 italic">-</span>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Website -->
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center text-sm shrink-0">
                        <ion-icon name="globe-outline"></ion-icon>
                    </div>
                    <div class="min-w-0">
                        <span class="block text-[10px] text-slate-400 font-semibold uppercase">Website</span>
                        <?php if (!empty($perusahaan['website'])): ?>
                            <a href="<?= htmlspecialchars($perusahaan['website']) ?>" target="_blank" rel="noopener noreferrer" class="text-xs font-semibold text-blue-600 hover:underline truncate block">
                                <?= htmlspecialchars(preg_replace('#^https?://#', '', $perusahaan['website'])) ?>
                            </a>
                        <?php else: ?>
                            <span class="text-xs text-slate-400 italic">-</span>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Alamat -->
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center text-sm shrink-0">
                        <ion-icon name="location-outline"></ion-icon>
                    </div>
                    <div class="min-w-0">
                        <span class="block text-[10px] text-slate-400 font-semibold uppercase">Alamat</span>
                        <span class="text-xs font-medium text-slate-700 truncate block" title="<?= htmlspecialchars($perusahaan['alamat'] ?? '') ?>">
                            <?= htmlspecialchars($perusahaan['alamat'] ?: '-') ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- 3. BRANDING & VISUAL IDENTITY ASSETS (CORE)-->
<!-- ========================================== -->
<div class="mb-4">
    <div class="flex items-center justify-between mb-3">
        <div>
            <h2 class="text-base font-bold text-slate-800 flex items-center gap-2">
                <ion-icon name="color-palette-outline" class="text-blue-600"></ion-icon>
                <span>Identitas Visual & Berkas Branding</span>
            </h2>
            <p class="text-xs text-slate-400">
                Aset branding resmi perusahaan. Setiap file otomatis dikompresi & disimpan dalam 3 resolusi: <span class="font-semibold text-slate-600">Large</span>, <span class="font-semibold text-slate-600">Medium</span>, dan <span class="font-semibold text-slate-600">Small</span>.
            </p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        
        <!-- ========================================== -->
        <!-- CARD A: LOGO RESMI PERUSAHAAN             -->
        <!-- ========================================== -->
        <div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 p-5 flex flex-col justify-between hover:border-slate-300 transition-all">
            <div>
                <!-- Card Header -->
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-3.5">
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center text-base">
                            <ion-icon name="ribbon-outline"></ion-icon>
                        </div>
                        <h3 class="font-bold text-slate-800 text-xs uppercase tracking-wide">Logo Resmi</h3>
                    </div>
                    <?php if ($logoUrls): ?>
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                            <ion-icon name="checkmark-circle"></ion-icon>
                            <span>Terunggah</span>
                        </span>
                    <?php else: ?>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-slate-100 text-slate-500">
                            Belum Ada
                        </span>
                    <?php endif; ?>
                </div>

                <!-- Preview Area -->
                <div class="mb-4">
                    <div class="h-44 w-full rounded-xl border border-slate-200/80 bg-slate-50/60 flex items-center justify-center p-4 relative overflow-hidden group">
                        <?php if ($logoUrls && !empty($logoUrls['medium'])): ?>
                            <img src="<?= $logoUrls['medium'] ?>" alt="Logo <?= htmlspecialchars($perusahaan['nama']) ?>" 
                                 class="max-h-full max-w-full object-contain filter drop-shadow-xs group-hover:scale-105 transition-transform duration-200">
                            
                            <!-- Overlay on hover for quick view -->
                            <div class="absolute inset-0 bg-slate-900/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-2">
                                <button type="button" 
                                        onclick="previewImageModal('<?= $logoUrls['large'] ?>', 'Logo Resmi - Large (1200px)')"
                                        class="p-2 bg-white text-slate-800 rounded-lg hover:bg-slate-100 transition-colors shadow-sm text-xs font-semibold flex items-center gap-1">
                                    <ion-icon name="scan-outline" class="text-sm"></ion-icon>
                                    <span>Lihat HD</span>
                                </button>
                            </div>
                        <?php else: ?>
                            <div class="text-center p-4">
                                <div class="w-12 h-12 rounded-xl bg-slate-100 text-slate-400 flex items-center justify-center text-2xl mx-auto mb-2">
                                    <ion-icon name="image-outline"></ion-icon>
                                </div>
                                <p class="text-xs font-semibold text-slate-600 mb-0.5">Belum Ada Logo</p>
                                <p class="text-[11px] text-slate-400">Disarankan PNG transparan atau WebP</p>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- 3 Version Chips -->
                    <?php if ($logoUrls): ?>
                        <div class="mt-2.5 p-2 bg-slate-50 rounded-xl border border-slate-100">
                            <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">3 Versi Resolusi Tersedia:</span>
                            <div class="flex items-center gap-1.5 flex-wrap">
                                <a href="<?= $logoUrls['small'] ?>" target="_blank" 
                                   class="px-2 py-0.5 bg-white hover:bg-blue-50 text-slate-600 hover:text-blue-600 rounded text-[11px] font-semibold border border-slate-200 transition-colors flex items-center gap-1">
                                    <ion-icon name="open-outline" class="text-[10px]"></ion-icon> Small (160px)
                                </a>
                                <a href="<?= $logoUrls['medium'] ?>" target="_blank" 
                                   class="px-2 py-0.5 bg-white hover:bg-blue-50 text-slate-600 hover:text-blue-600 rounded text-[11px] font-semibold border border-slate-200 transition-colors flex items-center gap-1">
                                    <ion-icon name="open-outline" class="text-[10px]"></ion-icon> Med (500px)
                                </a>
                                <a href="<?= $logoUrls['large'] ?>" target="_blank" 
                                   class="px-2 py-0.5 bg-white hover:bg-blue-50 text-slate-600 hover:text-blue-600 rounded text-[11px] font-semibold border border-slate-200 transition-colors flex items-center gap-1">
                                    <ion-icon name="open-outline" class="text-[10px]"></ion-icon> Large (1200px)
                                </a>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Card Action Buttons -->
            <div class="pt-3 border-t border-slate-100 flex items-center gap-2">
                <button type="button" 
                        data-upload-image 
                        data-module="perusahaan" 
                        data-field="logo" 
                        data-id="<?= $perusahaan['id'] ?>" 
                        data-title="Upload Logo Perusahaan"
                        data-hint="Disarankan gambar beresolusi tinggi rasio fleksibel / transparan (PNG, JPG, WebP)"
                        class="flex-1 px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-semibold shadow-xs transition-all flex items-center justify-center gap-1.5 hover:scale-[1.01] active:scale-[0.98]">
                    <ion-icon name="cloud-upload-outline" class="text-sm"></ion-icon>
                    <span><?= $logoUrls ? 'Ganti Logo' : 'Upload Logo' ?></span>
                </button>

                <?php if ($logoUrls): ?>
                    <button type="button" 
                            data-delete-image 
                            data-module="perusahaan" 
                            data-field="logo" 
                            data-id="<?= $perusahaan['id'] ?>"
                            data-label="Logo Perusahaan"
                            class="p-2 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-xl border border-slate-200 hover:border-rose-200 transition-colors text-base"
                            title="Hapus Logo">
                        <ion-icon name="trash-outline"></ion-icon>
                    </button>
                <?php endif; ?>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- CARD B: AVATAR / ICON PROFIL              -->
        <!-- ========================================== -->
        <div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 p-5 flex flex-col justify-between hover:border-slate-300 transition-all">
            <div>
                <!-- Card Header -->
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-3.5">
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center text-base">
                            <ion-icon name="person-circle-outline"></ion-icon>
                        </div>
                        <h3 class="font-bold text-slate-800 text-xs uppercase tracking-wide">Avatar / Icon</h3>
                    </div>
                    <?php if ($avatarUrls): ?>
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                            <ion-icon name="checkmark-circle"></ion-icon>
                            <span>Terunggah</span>
                        </span>
                    <?php else: ?>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-slate-100 text-slate-500">
                            Belum Ada
                        </span>
                    <?php endif; ?>
                </div>

                <!-- Preview Area -->
                <div class="mb-4">
                    <div class="h-44 w-full rounded-xl border border-slate-200/80 bg-slate-50/60 flex items-center justify-center p-4 relative overflow-hidden group">
                        <?php if ($avatarUrls && !empty($avatarUrls['medium'])): ?>
                            <img src="<?= $avatarUrls['medium'] ?>" alt="Avatar <?= htmlspecialchars($perusahaan['nama']) ?>" 
                                 class="w-28 h-28 rounded-full object-cover shadow-md ring-4 ring-white border border-slate-200 group-hover:scale-105 transition-transform duration-200">
                            
                            <!-- Overlay on hover for quick view -->
                            <div class="absolute inset-0 bg-slate-900/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-2">
                                <button type="button" 
                                        onclick="previewImageModal('<?= $avatarUrls['large'] ?>', 'Avatar Profil - Large (500x500)')"
                                        class="p-2 bg-white text-slate-800 rounded-lg hover:bg-slate-100 transition-colors shadow-sm text-xs font-semibold flex items-center gap-1">
                                    <ion-icon name="scan-outline" class="text-sm"></ion-icon>
                                    <span>Lihat HD</span>
                                </button>
                            </div>
                        <?php else: ?>
                            <div class="text-center p-4">
                                <div class="w-16 h-16 rounded-full bg-gradient-to-br from-blue-600 to-indigo-700 text-white flex items-center justify-center text-xl font-bold mx-auto mb-2 shadow-sm">
                                    <?= strtoupper(substr($perusahaan['nama'], 0, 2)) ?>
                                </div>
                                <p class="text-xs font-semibold text-slate-600 mb-0.5">Avatar Standar</p>
                                <p class="text-[11px] text-slate-400">Otomatis dicrop rasio 1:1</p>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- 3 Version Chips -->
                    <?php if ($avatarUrls): ?>
                        <div class="mt-2.5 p-2 bg-slate-50 rounded-xl border border-slate-100">
                            <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">3 Versi Resolusi Tersedia:</span>
                            <div class="flex items-center gap-1.5 flex-wrap">
                                <a href="<?= $avatarUrls['small'] ?>" target="_blank" 
                                   class="px-2 py-0.5 bg-white hover:bg-blue-50 text-slate-600 hover:text-blue-600 rounded text-[11px] font-semibold border border-slate-200 transition-colors flex items-center gap-1">
                                    <ion-icon name="open-outline" class="text-[10px]"></ion-icon> Small (80x80)
                                </a>
                                <a href="<?= $avatarUrls['medium'] ?>" target="_blank" 
                                   class="px-2 py-0.5 bg-white hover:bg-blue-50 text-slate-600 hover:text-blue-600 rounded text-[11px] font-semibold border border-slate-200 transition-colors flex items-center gap-1">
                                    <ion-icon name="open-outline" class="text-[10px]"></ion-icon> Med (200x200)
                                </a>
                                <a href="<?= $avatarUrls['large'] ?>" target="_blank" 
                                   class="px-2 py-0.5 bg-white hover:bg-blue-50 text-slate-600 hover:text-blue-600 rounded text-[11px] font-semibold border border-slate-200 transition-colors flex items-center gap-1">
                                    <ion-icon name="open-outline" class="text-[10px]"></ion-icon> Large (500x500)
                                </a>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Card Action Buttons -->
            <div class="pt-3 border-t border-slate-100 flex items-center gap-2">
                <button type="button" 
                        data-upload-image 
                        data-module="perusahaan" 
                        data-field="avatar" 
                        data-id="<?= $perusahaan['id'] ?>" 
                        data-title="Upload Avatar Profil"
                        data-hint="Foto profil / icon persegi, otomatis dicrop rasio 1:1 (Maks 10MB)"
                        class="flex-1 px-3 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-semibold shadow-xs transition-all flex items-center justify-center gap-1.5 hover:scale-[1.01] active:scale-[0.98]">
                    <ion-icon name="cloud-upload-outline" class="text-sm"></ion-icon>
                    <span><?= $avatarUrls ? 'Ganti Avatar' : 'Upload Avatar' ?></span>
                </button>

                <?php if ($avatarUrls): ?>
                    <button type="button" 
                            data-delete-image 
                            data-module="perusahaan" 
                            data-field="avatar" 
                            data-id="<?= $perusahaan['id'] ?>"
                            data-label="Avatar Profil"
                            class="p-2 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-xl border border-slate-200 hover:border-rose-200 transition-colors text-base"
                            title="Hapus Avatar">
                        <ion-icon name="trash-outline"></ion-icon>
                    </button>
                <?php endif; ?>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- CARD C: KOP SURAT RESMI                   -->
        <!-- ========================================== -->
        <div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 p-5 flex flex-col justify-between hover:border-slate-300 transition-all">
            <div>
                <!-- Card Header -->
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-3.5">
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center text-base">
                            <ion-icon name="document-text-outline"></ion-icon>
                        </div>
                        <h3 class="font-bold text-slate-800 text-xs uppercase tracking-wide">Kop Surat Resmi</h3>
                    </div>
                    <?php if ($kopsuratUrls): ?>
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                            <ion-icon name="checkmark-circle"></ion-icon>
                            <span>Terunggah</span>
                        </span>
                    <?php else: ?>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-slate-100 text-slate-500">
                            Belum Ada
                        </span>
                    <?php endif; ?>
                </div>

                <!-- Preview Area -->
                <div class="mb-4">
                    <div class="h-44 w-full rounded-xl border border-slate-200/80 bg-slate-50/60 flex items-center justify-center p-3 relative overflow-hidden group">
                        <?php if ($kopsuratUrls && !empty($kopsuratUrls['medium'])): ?>
                            <img src="<?= $kopsuratUrls['medium'] ?>" alt="Kop Surat <?= htmlspecialchars($perusahaan['nama']) ?>" 
                                 class="max-h-full max-w-full object-contain shadow-xs border border-slate-200 bg-white group-hover:scale-105 transition-transform duration-200">
                            
                            <!-- Overlay on hover for quick view -->
                            <div class="absolute inset-0 bg-slate-900/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-2">
                                <button type="button" 
                                        onclick="previewImageModal('<?= $kopsuratUrls['large'] ?>', 'Kop Surat Resmi - Large (1800px)')"
                                        class="p-2 bg-white text-slate-800 rounded-lg hover:bg-slate-100 transition-colors shadow-sm text-xs font-semibold flex items-center gap-1">
                                    <ion-icon name="scan-outline" class="text-sm"></ion-icon>
                                    <span>Lihat HD</span>
                                </button>
                            </div>
                        <?php else: ?>
                            <div class="text-center p-4">
                                <div class="w-12 h-12 rounded-xl bg-slate-100 text-slate-400 flex items-center justify-center text-2xl mx-auto mb-2">
                                    <ion-icon name="document-attach-outline"></ion-icon>
                                </div>
                                <p class="text-xs font-semibold text-slate-600 mb-0.5">Belum Ada Kop Surat</p>
                                <p class="text-[11px] text-slate-400">Header resmi persuratan & invoice (rasio lebar 3:1)</p>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- 3 Version Chips -->
                    <?php if ($kopsuratUrls): ?>
                        <div class="mt-2.5 p-2 bg-slate-50 rounded-xl border border-slate-100">
                            <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1.5">3 Versi Resolusi Tersedia:</span>
                            <div class="flex items-center gap-1.5 flex-wrap">
                                <a href="<?= $kopsuratUrls['small'] ?>" target="_blank" 
                                   class="px-2 py-0.5 bg-white hover:bg-blue-50 text-slate-600 hover:text-blue-600 rounded text-[11px] font-semibold border border-slate-200 transition-colors flex items-center gap-1">
                                    <ion-icon name="open-outline" class="text-[10px]"></ion-icon> Small (350x120)
                                </a>
                                <a href="<?= $kopsuratUrls['medium'] ?>" target="_blank" 
                                   class="px-2 py-0.5 bg-white hover:bg-blue-50 text-slate-600 hover:text-blue-600 rounded text-[11px] font-semibold border border-slate-200 transition-colors flex items-center gap-1">
                                    <ion-icon name="open-outline" class="text-[10px]"></ion-icon> Med (900x300)
                                </a>
                                <a href="<?= $kopsuratUrls['large'] ?>" target="_blank" 
                                   class="px-2 py-0.5 bg-white hover:bg-blue-50 text-slate-600 hover:text-blue-600 rounded text-[11px] font-semibold border border-slate-200 transition-colors flex items-center gap-1">
                                    <ion-icon name="open-outline" class="text-[10px]"></ion-icon> Large (1800x600)
                                </a>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Card Action Buttons -->
            <div class="pt-3 border-t border-slate-100 flex items-center gap-2">
                <button type="button" 
                        data-upload-image 
                        data-module="perusahaan" 
                        data-field="kopsurat" 
                        data-id="<?= $perusahaan['id'] ?>" 
                        data-title="Upload Kop Surat Resmi"
                        data-hint="Header resmi untuk persuratan, invoice, dan dokumen resmi (Lebar hingga 1800px)"
                        class="flex-1 px-3 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-semibold shadow-xs transition-all flex items-center justify-center gap-1.5 hover:scale-[1.01] active:scale-[0.98]">
                    <ion-icon name="cloud-upload-outline" class="text-sm"></ion-icon>
                    <span><?= $kopsuratUrls ? 'Ganti Kop Surat' : 'Upload Kop Surat' ?></span>
                </button>

                <?php if ($kopsuratUrls): ?>
                    <button type="button" 
                            data-delete-image 
                            data-module="perusahaan" 
                            data-field="kopsurat" 
                            data-id="<?= $perusahaan['id'] ?>"
                            data-label="Kop Surat Resmi"
                            class="p-2 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-xl border border-slate-200 hover:border-rose-200 transition-colors text-base"
                            title="Hapus Kop Surat">
                        <ion-icon name="trash-outline"></ion-icon>
                    </button>
                <?php endif; ?>
            </div>
        </div>

    </div>
</div>

<!-- ========================================== -->
<!-- 4. MODAL: EDIT PERUSAHAAN (POPUP)          -->
<!-- ========================================== -->
<div id="modalEditPerusahaan" class="app-modal fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 transition-all duration-200 opacity-0 pointer-events-none hidden">
    <div class="modal-card bg-white rounded-2xl shadow-2xl border border-slate-100 max-w-lg w-full transform scale-95 opacity-0 transition-all duration-200 overflow-hidden">
        <!-- Modal Header -->
        <div class="px-6 py-4 bg-slate-50/75 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-slate-900 text-white flex items-center justify-center text-lg shadow-sm">
                    <ion-icon name="create-outline"></ion-icon>
                </div>
                <div>
                    <h3 class="font-bold text-slate-800 text-sm">Edit Profil Perusahaan</h3>
                    <p class="text-[11px] text-slate-400" id="editPerusahaanSubtitle">Perbarui data profil & kontak perusahaan</p>
                </div>
            </div>
            <button type="button" data-modal-close="modalEditPerusahaan" class="p-1.5 text-slate-400 hover:text-slate-700 rounded-lg transition-colors">
                <ion-icon name="close-outline" class="text-xl"></ion-icon>
            </button>
        </div>

        <!-- Modal Form -->
        <form id="formEditPerusahaan" method="POST" action="<?= APP_URL ?>/perusahaan/update/<?= $perusahaan['id'] ?>" class="p-6 space-y-4">
            <?= csrfField() ?>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Perusahaan / CV / PT <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <ion-icon name="business-outline" class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-base"></ion-icon>
                        <input type="text" id="edit_nama" name="nama" value="<?= htmlspecialchars($perusahaan['nama']) ?>" required 
                               class="w-full pl-9 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-blue-500 text-xs focus:bg-white transition-all">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Direktur / Pimpinan</label>
                    <div class="relative">
                        <ion-icon name="person-outline" class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-base"></ion-icon>
                        <input type="text" id="edit_nama_direktur" name="nama_direktur" value="<?= htmlspecialchars($perusahaan['nama_direktur'] ?? '') ?>"
                               class="w-full pl-9 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-blue-500 text-xs focus:bg-white transition-all"
                               placeholder="Untuk tanda tangan BAST">
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Email Perusahaan</label>
                    <div class="relative">
                        <ion-icon name="mail-outline" class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-base"></ion-icon>
                        <input type="email" id="edit_email" name="email" value="<?= htmlspecialchars($perusahaan['email'] ?? '') ?>"
                               class="w-full pl-9 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-blue-500 text-xs focus:bg-white transition-all">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Nomor Telepon / WA</label>
                    <div class="relative">
                        <ion-icon name="call-outline" class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-base"></ion-icon>
                        <input type="text" id="edit_telepon" name="telepon" value="<?= htmlspecialchars($perusahaan['telepon'] ?? '') ?>"
                               class="w-full pl-9 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-blue-500 text-xs focus:bg-white transition-all">
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Website Perusahaan</label>
                <div class="relative">
                    <ion-icon name="globe-outline" class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-base"></ion-icon>
                    <input type="url" id="edit_website" name="website" value="<?= htmlspecialchars($perusahaan['website'] ?? '') ?>"
                           class="w-full pl-9 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-blue-500 text-xs focus:bg-white transition-all"
                           placeholder="https://perusahaan.co.id">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Alamat Domisili Kantor</label>
                <textarea id="edit_alamat" name="alamat" rows="2" 
                          class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-blue-500 text-xs focus:bg-white transition-all"><?= htmlspecialchars($perusahaan['alamat'] ?? '') ?></textarea>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Keterangan / Deskripsi Singkat</label>
                <textarea id="edit_keterangan" name="keterangan" rows="2" 
                          class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:border-blue-500 text-xs focus:bg-white transition-all"><?= htmlspecialchars($perusahaan['keterangan'] ?? '') ?></textarea>
            </div>

            <!-- Modal Footer -->
            <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                <button type="button" data-modal-close="modalEditPerusahaan" 
                        class="px-3.5 py-2 rounded-xl border border-slate-200 text-slate-600 font-semibold hover:bg-slate-50 text-xs transition-colors">
                    Batal
                </button>
                <button type="submit" 
                        class="px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white font-semibold rounded-xl text-xs shadow-sm transition-all flex items-center gap-1.5 hover:scale-[1.01] active:scale-[0.98]">
                    <ion-icon name="checkmark-outline" class="text-sm"></ion-icon>
                    <span>Simpan Perubahan</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================== -->
<!-- 5. LIGHTBOX / IMAGE PREVIEW MODAL         -->
<!-- ========================================== -->
<div id="modalImagePreview" class="app-modal fixed inset-0 z-50 bg-slate-900/80 backdrop-blur-xs flex items-center justify-center p-4 transition-all duration-200 opacity-0 pointer-events-none hidden">
    <div class="modal-card bg-white rounded-2xl shadow-2xl border border-slate-100 max-w-3xl w-full transform scale-95 opacity-0 transition-all duration-200 overflow-hidden">
        <div class="px-5 py-3.5 bg-slate-50 border-b border-slate-100 flex items-center justify-between">
            <h3 id="imagePreviewTitle" class="font-bold text-slate-800 text-xs">Preview Gambar</h3>
            <button type="button" data-modal-close="modalImagePreview" class="p-1 text-slate-400 hover:text-slate-700 rounded-lg transition-colors">
                <ion-icon name="close-outline" class="text-xl"></ion-icon>
            </button>
        </div>
        <div class="p-4 bg-slate-900 flex items-center justify-center min-h-[300px] max-h-[75vh] overflow-auto">
            <img id="imagePreviewSrc" src="" alt="Preview" class="max-w-full max-h-[70vh] object-contain rounded-lg">
        </div>
        <div class="px-5 py-3 bg-slate-50 border-t border-slate-100 flex items-center justify-between text-xs">
            <span class="text-slate-400 text-[11px]">Resolusi Asli / Versi Large</span>
            <div class="flex items-center gap-2">
                <a id="imagePreviewDownload" href="#" target="_blank" 
                   class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-semibold flex items-center gap-1 transition-colors">
                    <ion-icon name="open-outline"></ion-icon>
                    <span>Buka Tab Baru</span>
                </a>
            </div>
        </div>
    </div>
</div>

<script>
/**
 * Helper untuk membuka modal preview gambar beresolusi penuh
 */
function previewImageModal(url, title) {
    document.getElementById('imagePreviewSrc').src = url;
    document.getElementById('imagePreviewTitle').textContent = title || 'Preview Gambar';
    document.getElementById('imagePreviewDownload').href = url;
    window.ProModal.open('modalImagePreview');
}
</script>
