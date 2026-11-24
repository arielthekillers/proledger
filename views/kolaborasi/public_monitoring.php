<?php
/**
 * ProLedger - Public Read-Only Monitoring View (Tanpa Login)
 * Ditujukan untuk Dinas / Stakeholder memantau progres paket kolaborasi.
 * STRICT PRIVACY: Tidak menampilkan HPP, belanja toko, profit, atau potongan internal.
 */

$renderRing = function (int $pct, string $label, string $strokeHex, string $textClass) {
    $r = 15;
    $c = round(2 * M_PI * $r, 2);
    $offset = round($c * (1 - max(0, min(100, $pct)) / 100), 2);
    $dur = max(0.35, round(($pct / 100) * 1.1, 2));
    ?>
    <div class="flex flex-col items-center">
        <div class="relative w-11 h-11 flex items-center justify-center">
            <svg class="w-11 h-11 -rotate-90" viewBox="0 0 38 38">
                <circle cx="19" cy="19" r="<?= $r ?>" fill="none" stroke="#e2e8f0" stroke-width="3.2"></circle>
                <circle cx="19" cy="19" r="<?= $r ?>" fill="none" stroke="<?= $strokeHex ?>" stroke-width="3.2"
                        stroke-linecap="round"
                        stroke-dasharray="<?= $c ?>"
                        stroke-dashoffset="<?= $c ?>"
                        data-ring-target="<?= $offset ?>"
                        style="transition: stroke-dashoffset <?= $dur ?>s cubic-bezier(0.22, 1, 0.36, 1);"></circle>
            </svg>
            <span class="absolute text-[9px] font-extrabold <?= $textClass ?>"><?= $pct ?>%</span>
        </div>
        <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider mt-0.5"><?= $label ?></span>
    </div>
    <?php
};

$renderTableRing = function (int $pct, string $strokeHex, string $textClass) {
    $r = 12;
    $c = round(2 * M_PI * $r, 2);
    $offset = round($c * (1 - max(0, min(100, $pct)) / 100), 2);
    $dur = max(0.35, round(($pct / 100) * 1.1, 2));
    ?>
    <div class="relative w-8 h-8 inline-flex items-center justify-center">
        <svg class="w-8 h-8 -rotate-90" viewBox="0 0 32 32">
            <circle cx="16" cy="16" r="<?= $r ?>" fill="none" stroke="#e2e8f0" stroke-width="2.8"></circle>
            <circle cx="16" cy="16" r="<?= $r ?>" fill="none" stroke="<?= $strokeHex ?>" stroke-width="2.8"
                    stroke-linecap="round"
                    stroke-dasharray="<?= $c ?>"
                    stroke-dashoffset="<?= $c ?>"
                    data-ring-target="<?= $offset ?>"
                    style="transition: stroke-dashoffset <?= $dur ?>s cubic-bezier(0.22, 1, 0.36, 1);"></circle>
        </svg>
        <span class="absolute text-[8px] font-extrabold <?= $textClass ?>"><?= $pct ?>%</span>
    </div>
    <?php
};
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title) ?> - SimPaket Monitoring</title>
    <link rel="icon" type="image/svg+xml" href="<?= APP_URL ?>/favicon.svg">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= APP_URL ?>/css/app.min.css?v=<?= APP_VERSION ?>">
    <?php if (defined('APP_ENV') && APP_ENV === 'local'): ?>
        <script src="https://cdn.tailwindcss.com"></script>
    <?php endif; ?>
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="bg-slate-100 text-slate-800 antialiased text-sm min-h-screen">
    <!-- Top Public Bar -->
    <header class="bg-white/95 backdrop-blur-md border-b border-slate-200 sticky top-0 z-30">
        <div class="max-w-7xl mx-auto px-4 md:px-6 h-14 flex items-center justify-between gap-3">
            <div class="flex items-center gap-2.5">
                <img src="<?= APP_URL ?>/favicon.svg" alt="SimPaket" class="w-8 h-8 rounded-lg shadow-xs">
                <div>
                    <span class="font-bold text-base text-slate-800">Sim<span class="text-blue-600">Paket</span></span>
                    <span class="ml-1.5 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                        Monitoring Publik (Read-Only)
                    </span>
                </div>
            </div>
            <div class="text-[11px] text-slate-400 hidden sm:block">
                Diperbarui otomatis &bull; <?= date('d M Y H:i') ?>
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 md:px-6 py-6">
        <!-- Hero Card (Compact) -->
        <div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 px-4 py-3 mb-3.5">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3">
                <div class="flex items-center gap-3 flex-1 min-w-0">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500 to-blue-600 text-white flex items-center justify-center text-xl shadow-xs shrink-0">
                        <ion-icon name="analytics"></ion-icon>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <h1 class="text-base md:text-lg font-extrabold text-slate-900 tracking-tight leading-tight">
                                <?= htmlspecialchars($kolab['nama']) ?>
                            </h1>
                            <span class="px-2 py-0.2 rounded-full text-[10px] font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                TA <?= (int)$kolab['tahun_anggaran'] ?>
                            </span>
                        </div>
                        <p class="text-[11px] text-slate-400 mt-0.5 flex items-center gap-1.5 flex-wrap">
                            <?php if (!empty($kolab['instansi'])): ?>
                                <span class="text-slate-600 font-semibold"><?= htmlspecialchars($kolab['instansi']) ?></span>
                                <span class="text-slate-300">&bull;</span>
                            <?php endif; ?>
                            <span><?= $summary['total_perusahaan'] ?> Perusahaan</span>
                            <span class="text-slate-300">&bull;</span>
                            <span><?= $summary['total_paket'] ?> Paket</span>
                            <span class="text-slate-300">&bull;</span>
                            <span class="font-mono font-bold text-slate-700"><?= formatRupiah($summary['total_kontrak']) ?></span>
                        </p>
                        <?php if (!empty($kolab['deskripsi'])): ?>
                            <p class="text-[11px] text-slate-500 mt-1 truncate" title="<?= htmlspecialchars($kolab['deskripsi']) ?>">
                                <?= htmlspecialchars($kolab['deskripsi']) ?>
                            </p>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-4 pt-2 lg:pt-0 border-t lg:border-t-0 lg:border-l border-slate-100 lg:pl-5 shrink-0">
                    <div class="flex items-center gap-3">
                        <?php $renderRing($summary['dist_pct'], 'Dist', '#6366f1', 'text-indigo-600'); ?>
                        <?php $renderRing($summary['order_pct'], 'Order', '#f59e0b', 'text-amber-600'); ?>
                        <?php $renderRing($summary['dok_pct'], 'Dok', '#10b981', 'text-emerald-600'); ?>
                        <?php $renderRing($summary['bast_pct'], 'BAST', '#a855f7', 'text-purple-600'); ?>
                    </div>
                    <div class="flex items-center gap-1.5 text-[10px]">
                        <span class="px-2 py-0.5 rounded-full font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                            <?= $summary['paket_selesai'] ?> Cair
                        </span>
                        <span class="px-2 py-0.5 rounded-full font-semibold bg-rose-50 text-rose-700 border border-rose-200">
                            <?= $summary['paket_proses'] ?> Proses
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table + Read-Only Modals -->
        <?php
        $modalBaseUrl = APP_URL . '/monitoring/' . rawurlencode($kolab['share_token']) . '/modalView';
        require __DIR__ . '/_paket_table.php';
        ?>

        <!-- Per-Company Cards -->
        <?php if (!empty($companyStats)): ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-3 mt-4">
                <?php foreach ($companyStats as $cs): ?>
                    <button type="button" onclick="filterByCompany('<?= htmlspecialchars(strtolower($cs['nama']), ENT_QUOTES) ?>')"
                            class="text-left bg-white rounded-2xl shadow-xs border border-slate-200/80 p-3.5 hover:border-blue-300 transition-all cursor-pointer">
                        <div class="flex items-center justify-between gap-2 mb-1.5">
                            <span class="font-bold text-slate-800 text-xs truncate"><?= htmlspecialchars($cs['nama']) ?></span>
                            <span class="px-1.5 py-0.5 rounded-md bg-blue-50 text-blue-700 text-[10px] font-bold shrink-0">
                                <?= $cs['total_paket'] ?> Paket
                            </span>
                        </div>
                        <div class="font-mono font-bold text-slate-900 text-xs mb-2"><?= formatRupiah($cs['total_kontrak']) ?></div>
                        <div class="w-full h-1.5 bg-slate-100 rounded-full overflow-hidden mb-1.5">
                            <div class="h-full bg-gradient-to-r from-blue-500 to-indigo-600 rounded-full" style="width: <?= $cs['avg_pct'] ?>%"></div>
                        </div>
                        <div class="flex items-center justify-between text-[10px] text-slate-400">
                            <span>Progres <?= $cs['avg_pct'] ?>%</span>
                            <span>BAST <?= $cs['bast_pct'] ?>%</span>
                        </div>
                    </button>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        requestAnimationFrame(() => {
            document.querySelectorAll('circle[data-ring-target]').forEach(el => {
                el.style.strokeDashoffset = el.getAttribute('data-ring-target');
            });
            setTimeout(() => {
                document.querySelectorAll('.pro-progress-trigger').forEach(trigger => {
                    const pct = parseInt(trigger.getAttribute('data-prog-pct') || '0', 10);
                    const duration = Math.max(350, Math.round(250 + (pct / 100) * 1100));

                    const circle = trigger.querySelector('.pro-progress-ring[data-target-offset]');
                    if (circle) {
                        circle.style.transitionDuration = duration + 'ms';
                        circle.style.strokeDashoffset = circle.getAttribute('data-target-offset');
                    }

                    const pctEl = trigger.querySelector('.pro-progress-pct[data-target-pct]');
                    if (pctEl) {
                        if (pct <= 0) {
                            pctEl.textContent = '0%';
                            return;
                        }
                        const start = performance.now();
                        function step(now) {
                            const progress = Math.min(1, (now - start) / duration);
                            const eased = 1 - Math.pow(1 - progress, 3);
                            pctEl.textContent = Math.round(pct * eased) + '%';
                            if (progress < 1) requestAnimationFrame(step);
                        }
                        requestAnimationFrame(step);
                    }
                });
            }, 60);
        });

        // Dark floating tooltip (.pro-dark-tooltip)
        const darkTooltip = document.createElement('div');
        darkTooltip.id = 'proGenericDarkTooltip';
        darkTooltip.className = 'fixed z-[95] pointer-events-none hidden max-w-xs p-2.5 bg-slate-800 text-white rounded-xl shadow-xl text-[11px] leading-relaxed border border-slate-700/80';
        document.body.appendChild(darkTooltip);

        document.addEventListener('mouseover', function (e) {
            const el = e.target.closest('.pro-dark-tooltip');
            if (!el) return;
            const title = el.getAttribute('data-tooltip-title') || '';
            const body = el.getAttribute('data-tooltip-body') || '';
            if (!title && !body) return;
            darkTooltip.innerHTML = (title ? `<div class="font-bold text-white border-b border-slate-700 pb-1 mb-1">${title}</div>` : '') +
                                    (body ? `<div class="text-slate-300">${body}</div>` : '');
            darkTooltip.classList.remove('hidden');
            const rect = el.getBoundingClientRect();
            const tw = darkTooltip.offsetWidth || 200;
            const th = darkTooltip.offsetHeight || 60;
            let left = rect.left + (rect.width / 2) - (tw / 2);
            let top = rect.top - th - 8;
            if (left < 10) left = 10;
            if (left + tw > window.innerWidth - 10) left = window.innerWidth - tw - 10;
            if (top < 10) top = rect.bottom + 8;
            darkTooltip.style.left = left + 'px';
            darkTooltip.style.top = top + 'px';
        });

        document.addEventListener('mouseout', function (e) {
            if (e.target.closest('.pro-dark-tooltip')) {
                darkTooltip.classList.add('hidden');
            }
        });
    });
    </script>
</body>
</html>
