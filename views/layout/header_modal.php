<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'PaketFlow' ?> - Detail Modal</title>
    <link rel="icon" type="image/svg+xml" href="<?= APP_URL ?>/favicon.svg">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Compiled Static Tailwind CSS -->
    <link rel="stylesheet" href="<?= APP_URL ?>/css/app.min.css">
    
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
    <style>
        /* Sembunyikan scrollbar tapi tetap bisa scroll */
        .scrollbar-none::-webkit-scrollbar { display: none; }
        .scrollbar-none { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased font-sans text-sm p-2 sm:p-4 min-h-screen">
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
