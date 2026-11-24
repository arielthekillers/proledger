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
$ttdUrls = !empty($perusahaan['ttd']) ? Uploader::getAllUrls($perusahaan['ttd'], 'perusahaan', 'ttd') : null;
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
<!-- 2. UNIFIED COMPANY PROFILE & BRANDING CARD -->
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
        <div class="flex-1 min-w-0 w-full">
            <div class="flex flex-wrap items-center justify-between gap-2 mb-1">
                <h1 class="text-lg md:text-xl font-extrabold text-slate-900 tracking-tight leading-tight">
                    <?= htmlspecialchars($perusahaan['nama']) ?>
                </h1>
            </div>

            <p class="text-xs text-slate-400 mb-3 flex flex-wrap items-center gap-1.5">
                <ion-icon name="calendar-outline"></ion-icon>
                <span>Terdaftar di SimPaket sejak <?= date('d M Y', strtotime($perusahaan['created_at'])) ?></span>
                <?php if (!empty($perusahaan['owner_name'])): ?>
                    <span class="text-slate-300">&bull;</span>
                    <span class="text-slate-500 font-medium">Pemilik: <?= htmlspecialchars($perusahaan['owner_name']) ?></span>
                <?php endif; ?>
                <?php if (!empty($perusahaan['nama_direktur'])): ?>
                    <span class="text-slate-300">&bull;</span>
                    <span class="text-slate-600 font-medium">Direktur: <?= htmlspecialchars($perusahaan['nama_direktur']) ?></span>
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

    <!-- ========================================== -->
    <!-- COMPACT BRANDING & VISUAL ASSETS SECTION   -->
    <!-- ========================================== -->
    <div class="mt-5 pt-4 border-t border-slate-100">
        <div class="flex items-center justify-between mb-3">
            <h2 class="text-xs font-bold text-slate-700 uppercase tracking-wider flex items-center gap-1.5">
                <ion-icon name="color-palette-outline" class="text-blue-600 text-sm"></ion-icon>
                <span>Identitas Visual & Berkas Branding</span>
            </h2>
            <span class="text-[11px] text-slate-400 hidden sm:inline">Logo, Avatar, Kop Surat &amp; TTD</span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3">
            <!-- ITEM A: LOGO RESMI -->
            <div class="flex items-center gap-3 p-3 rounded-xl border border-slate-200/80 bg-slate-50/60 hover:bg-slate-50 transition-colors">
                <div class="w-14 h-14 rounded-lg border border-slate-200 bg-white flex items-center justify-center p-1.5 shrink-0 overflow-hidden relative group">
                    <?php if ($logoUrls && !empty($logoUrls['medium'])): ?>
                        <img src="<?= $logoUrls['medium'] ?>" alt="Logo"
                             onclick="previewImageModal('<?= $logoUrls['large'] ?>', 'Logo Resmi - Large (1200px)')"
                             class="max-h-full max-w-full object-contain cursor-pointer group-hover:scale-105 transition-transform"
                             title="Klik untuk lihat HD">
                    <?php else: ?>
                        <ion-icon name="ribbon-outline" class="text-xl text-slate-300"></ion-icon>
                    <?php endif; ?>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between gap-1 mb-0.5">
                        <span class="text-xs font-bold text-slate-800 truncate">Logo Resmi</span>
                        <?php if ($logoUrls): ?>
                            <span class="w-2 h-2 rounded-full bg-emerald-500 shrink-0" title="Terunggah"></span>
                        <?php endif; ?>
                    </div>
                    <p class="text-[10px] text-slate-400 mb-2 truncate">
                        <?= $logoUrls ? 'PNG / WebP (3 ukuran)' : 'Belum diunggah' ?>
                    </p>
                    <div class="flex items-center gap-1.5">
                        <button type="button"
                                data-upload-image
                                data-module="perusahaan"
                                data-field="logo"
                                data-id="<?= $perusahaan['id'] ?>"
                                data-title="Upload Logo Perusahaan"
                                data-hint="Disarankan gambar beresolusi tinggi rasio fleksibel / transparan (PNG, JPG, WebP)"
                                class="px-2.5 py-1 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-[11px] font-semibold transition-colors inline-flex items-center gap-1 cursor-pointer">
                            <ion-icon name="cloud-upload-outline" class="text-xs"></ion-icon>
                            <span><?= $logoUrls ? 'Ganti' : 'Upload' ?></span>
                        </button>
                        <?php if ($logoUrls): ?>
                            <button type="button"
                                    onclick="previewImageModal('<?= $logoUrls['large'] ?>', 'Logo Resmi - Large (1200px)')"
                                    class="p-1 text-slate-500 hover:text-blue-600 hover:bg-white rounded-lg border border-slate-200 transition-colors text-xs cursor-pointer"
                                    title="Lihat HD">
                                <ion-icon name="scan-outline"></ion-icon>
                            </button>
                            <button type="button"
                                    data-delete-image
                                    data-module="perusahaan"
                                    data-field="logo"
                                    data-id="<?= $perusahaan['id'] ?>"
                                    data-label="Logo Perusahaan"
                                    class="p-1 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg border border-slate-200 hover:border-rose-200 transition-colors text-xs cursor-pointer"
                                    title="Hapus Logo">
                                <ion-icon name="trash-outline"></ion-icon>
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- ITEM B: AVATAR / ICON -->
            <div class="flex items-center gap-3 p-3 rounded-xl border border-slate-200/80 bg-slate-50/60 hover:bg-slate-50 transition-colors">
                <div class="w-14 h-14 rounded-lg border border-slate-200 bg-white flex items-center justify-center p-1.5 shrink-0 overflow-hidden relative group">
                    <?php if ($avatarUrls && !empty($avatarUrls['medium'])): ?>
                        <img src="<?= $avatarUrls['medium'] ?>" alt="Avatar"
                             onclick="previewImageModal('<?= $avatarUrls['large'] ?>', 'Avatar Profil - Large (500x500)')"
                             class="w-11 h-11 rounded-full object-cover cursor-pointer group-hover:scale-105 transition-transform"
                             title="Klik untuk lihat HD">
                    <?php else: ?>
                        <ion-icon name="person-circle-outline" class="text-xl text-slate-300"></ion-icon>
                    <?php endif; ?>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between gap-1 mb-0.5">
                        <span class="text-xs font-bold text-slate-800 truncate">Avatar / Icon</span>
                        <?php if ($avatarUrls): ?>
                            <span class="w-2 h-2 rounded-full bg-emerald-500 shrink-0" title="Terunggah"></span>
                        <?php endif; ?>
                    </div>
                    <p class="text-[10px] text-slate-400 mb-2 truncate">
                        <?= $avatarUrls ? 'Rasio 1:1 (3 ukuran)' : 'Belum diunggah' ?>
                    </p>
                    <div class="flex items-center gap-1.5">
                        <button type="button"
                                data-upload-image
                                data-module="perusahaan"
                                data-field="avatar"
                                data-id="<?= $perusahaan['id'] ?>"
                                data-title="Upload Avatar Profil"
                                data-hint="Foto profil / icon persegi, otomatis dicrop rasio 1:1 (Maks 10MB)"
                                class="px-2.5 py-1 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-[11px] font-semibold transition-colors inline-flex items-center gap-1 cursor-pointer">
                            <ion-icon name="cloud-upload-outline" class="text-xs"></ion-icon>
                            <span><?= $avatarUrls ? 'Ganti' : 'Upload' ?></span>
                        </button>
                        <?php if ($avatarUrls): ?>
                            <button type="button"
                                    onclick="previewImageModal('<?= $avatarUrls['large'] ?>', 'Avatar Profil - Large (500x500)')"
                                    class="p-1 text-slate-500 hover:text-emerald-600 hover:bg-white rounded-lg border border-slate-200 transition-colors text-xs cursor-pointer"
                                    title="Lihat HD">
                                <ion-icon name="scan-outline"></ion-icon>
                            </button>
                            <button type="button"
                                    data-delete-image
                                    data-module="perusahaan"
                                    data-field="avatar"
                                    data-id="<?= $perusahaan['id'] ?>"
                                    data-label="Avatar Profil"
                                    class="p-1 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg border border-slate-200 hover:border-rose-200 transition-colors text-xs cursor-pointer"
                                    title="Hapus Avatar">
                                <ion-icon name="trash-outline"></ion-icon>
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- ITEM C: KOP SURAT RESMI (PDF A4) -->
            <?php
            $kopFile = $perusahaan['kopsurat'] ?? '';
            $isKopPdf = !empty($kopFile) && preg_match('/\.pdf$/i', $kopFile);
            $kopPdfHref = $isKopPdf ? (APP_URL . '/uploads/perusahaan/kopsurat/' . $kopFile) : null;
            ?>
            <div class="flex items-center gap-3 p-3 rounded-xl border border-slate-200/80 bg-slate-50/60 hover:bg-slate-50 transition-colors">
                <div class="w-20 h-14 rounded-lg border border-slate-200 bg-white flex items-center justify-center p-1 shrink-0 overflow-hidden relative group">
                    <?php if ($isKopPdf): ?>
                        <a href="<?= htmlspecialchars($kopPdfHref) ?>" target="_blank" class="flex flex-col items-center justify-center text-rose-600 hover:text-rose-700" title="Klik untuk buka PDF Kop Surat">
                            <ion-icon name="document-text" class="text-2xl"></ion-icon>
                            <span class="text-[9px] font-bold uppercase">PDF A4</span>
                        </a>
                    <?php elseif ($kopsuratUrls && !empty($kopsuratUrls['medium'])): ?>
                        <img src="<?= $kopsuratUrls['medium'] ?>" alt="Kop Surat"
                             onclick="previewImageModal('<?= $kopsuratUrls['large'] ?>', 'Kop Surat Resmi - Large')"
                             class="max-h-full max-w-full object-contain cursor-pointer group-hover:scale-105 transition-transform"
                             title="Klik untuk lihat HD">
                    <?php else: ?>
                        <ion-icon name="document-text-outline" class="text-xl text-slate-300"></ion-icon>
                    <?php endif; ?>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between gap-1 mb-0.5">
                        <span class="text-xs font-bold text-slate-800 truncate">Kop Surat Resmi (PDF A4)</span>
                        <?php if (!empty($kopFile)): ?>
                            <span class="w-2 h-2 rounded-full bg-emerald-500 shrink-0" title="Terunggah"></span>
                        <?php endif; ?>
                    </div>
                    <p class="text-[10px] text-slate-400 mb-2 truncate">
                        <?= !empty($kopFile) ? ($isKopPdf ? 'PDF A4 Full Background' : 'Gambar (disarankan ganti ke PDF A4)') : 'Belum diunggah' ?>
                    </p>
                    <div class="flex items-center gap-1.5">
                        <button type="button"
                                data-upload-image
                                data-module="perusahaan"
                                data-field="kopsurat"
                                data-id="<?= $perusahaan['id'] ?>"
                                data-accept="application/pdf,.pdf"
                                data-title="Upload Kop Surat Resmi (PDF A4)"
                                data-hint="Upload file PDF ukuran A4 agar hasil cetak BAST tajam & presisi"
                                class="px-2.5 py-1 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-[11px] font-semibold transition-colors inline-flex items-center gap-1 cursor-pointer">
                            <ion-icon name="cloud-upload-outline" class="text-xs"></ion-icon>
                            <span><?= !empty($kopFile) ? 'Ganti' : 'Upload' ?></span>
                        </button>
                        <?php if ($isKopPdf): ?>
                            <a href="<?= htmlspecialchars($kopPdfHref) ?>" target="_blank"
                               class="p-1 text-slate-500 hover:text-indigo-600 hover:bg-white rounded-lg border border-slate-200 transition-colors text-xs"
                               title="Lihat PDF">
                                <ion-icon name="open-outline"></ion-icon>
                            </a>
                        <?php elseif ($kopsuratUrls): ?>
                            <button type="button"
                                    onclick="previewImageModal('<?= $kopsuratUrls['large'] ?>', 'Kop Surat Resmi')"
                                    class="p-1 text-slate-500 hover:text-indigo-600 hover:bg-white rounded-lg border border-slate-200 transition-colors text-xs cursor-pointer"
                                    title="Lihat HD">
                                <ion-icon name="scan-outline"></ion-icon>
                            </button>
                        <?php endif; ?>
                        <?php if (!empty($kopFile)): ?>
                            <button type="button"
                                    data-delete-image
                                    data-module="perusahaan"
                                    data-field="kopsurat"
                                    data-id="<?= $perusahaan['id'] ?>"
                                    data-label="Kop Surat Resmi"
                                    class="p-1 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg border border-slate-200 hover:border-rose-200 transition-colors text-xs cursor-pointer"
                                    title="Hapus Kop Surat">
                                <ion-icon name="trash-outline"></ion-icon>
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- ITEM D: TANDA TANGAN (PNG) -->
            <div class="flex items-center gap-3 p-3 rounded-xl border border-slate-200/80 bg-slate-50/60 hover:bg-slate-50 transition-colors">
                <div class="w-16 h-14 rounded-lg border border-slate-200 bg-white flex items-center justify-center p-1 shrink-0 overflow-hidden relative group">
                    <?php if ($ttdUrls && !empty($ttdUrls['medium'])): ?>
                        <img src="<?= $ttdUrls['medium'] ?>" alt="TTD"
                             onclick="previewImageModal('<?= $ttdUrls['large'] ?>', 'Tanda Tangan PNG')"
                             class="max-h-full max-w-full object-contain cursor-pointer group-hover:scale-105 transition-transform"
                             title="Klik untuk lihat HD">
                    <?php else: ?>
                        <ion-icon name="create-outline" class="text-xl text-slate-300"></ion-icon>
                    <?php endif; ?>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between gap-1 mb-0.5">
                        <span class="text-xs font-bold text-slate-800 truncate">Tanda Tangan</span>
                        <?php if ($ttdUrls): ?>
                            <span class="w-2 h-2 rounded-full bg-emerald-500 shrink-0" title="Terunggah"></span>
                        <?php endif; ?>
                    </div>
                    <p class="text-[10px] text-slate-400 mb-2 truncate">
                        <?= $ttdUrls ? 'PNG Transparan (BAST)' : 'Belum diunggah' ?>
                    </p>
                    <div class="flex items-center gap-1.5">
                        <button type="button"
                                data-upload-image
                                data-module="perusahaan"
                                data-field="ttd"
                                data-id="<?= $perusahaan['id'] ?>"
                                data-title="Upload Tanda Tangan (PNG)"
                                data-hint="Disarankan format PNG transparan untuk disematkan otomatis pada dokumen BAST"
                                class="px-2.5 py-1 bg-amber-500 hover:bg-amber-600 text-white rounded-lg text-[11px] font-semibold transition-colors inline-flex items-center gap-1 cursor-pointer">
                            <ion-icon name="cloud-upload-outline" class="text-xs"></ion-icon>
                            <span><?= $ttdUrls ? 'Ganti' : 'Upload' ?></span>
                        </button>
                        <?php if ($ttdUrls): ?>
                            <button type="button"
                                    onclick="previewImageModal('<?= $ttdUrls['large'] ?>', 'Tanda Tangan PNG')"
                                    class="p-1 text-slate-500 hover:text-amber-600 hover:bg-white rounded-lg border border-slate-200 transition-colors text-xs cursor-pointer"
                                    title="Lihat HD">
                                <ion-icon name="scan-outline"></ion-icon>
                            </button>
                            <button type="button"
                                    data-delete-image
                                    data-module="perusahaan"
                                    data-field="ttd"
                                    data-id="<?= $perusahaan['id'] ?>"
                                    data-label="Tanda Tangan PNG"
                                    class="p-1 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg border border-slate-200 hover:border-rose-200 transition-colors text-xs cursor-pointer"
                                    title="Hapus Tanda Tangan">
                                <ion-icon name="trash-outline"></ion-icon>
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/_modal_edit.php'; ?>

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
