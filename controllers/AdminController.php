<?php
/**
 * ProLedger - Admin Controller
 * Mengelola Dashboard Admin, Direktori Perusahaan & Paket Global,
 * Pengawasan Kolaborasi, Audit Log, Monitor Storage, Pengaturan Global, dan Impersonate.
 */

require_once MODEL_PATH . '/Admin.php';
require_once MODEL_PATH . '/AdminDashboard.php';
require_once MODEL_PATH . '/ActivityLog.php';
require_once MODEL_PATH . '/AppSetting.php';

class AdminController {
    private $adminModel;
    private $dashModel;
    private $logModel;
    private $settingModel;

    public function __construct() {
        $this->adminModel   = new Admin();
        $this->dashModel    = new AdminDashboard();
        $this->logModel     = new ActivityLog();
        $this->settingModel = new AppSetting();
    }

    private function requireAdmin() {
        if (($_SESSION['admin_role'] ?? '') !== 'admin') {
            header('Location: ' . APP_URL);
            exit;
        }
    }

    private function view($view, $data = []) {
        extract($data);
        require_once VIEW_PATH . '/' . $view . '.php';
    }

    /**
     * Halaman utama Dashboard Admin (/admin)
     */
    public function index() {
        $this->requireAdmin();

        $stats        = $this->dashModel->getSystemStats();
        $pendingUsers = $this->dashModel->getPendingUsers(6);
        $recentLogs   = $this->logModel->getLogs([], 10, 0);
        $storageStats = $this->dashModel->getStorageStats();

        $this->view('admin/dashboard', [
            'title'        => 'Dashboard Admin',
            'active'       => 'admin_dashboard',
            'stats'        => $stats,
            'pendingUsers' => $pendingUsers,
            'recentLogs'   => $recentLogs,
            'storageStats' => $storageStats,
        ]);
    }

    /**
     * Direktori seluruh Perusahaan (/admin/perusahaan)
     */
    public function perusahaan() {
        $this->requireAdmin();

        $search = trim($_GET['search'] ?? '');
        $companies   = $this->dashModel->getPerusahaanList($search);
        $activeUsers = $this->dashModel->getActiveUsersForAssign();

        $this->view('admin/perusahaan', [
            'title'       => 'Direktori Perusahaan',
            'active'      => 'admin_perusahaan',
            'companies'   => $companies,
            'activeUsers' => $activeUsers,
            'search'      => $search,
        ]);
    }

    /**
     * Assign pemilik akun ke perusahaan yatim
     */
    public function assignPerusahaanOwner($id = null) {
        $this->requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !$id) {
            redirect('/admin/perusahaan');
            return;
        }
        if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            flash('error', 'Token CSRF tidak valid.');
            redirect('/admin/perusahaan');
            return;
        }

        $userId = (int)($_POST['user_id'] ?? 0);
        $user = $this->adminModel->findById($userId);
        if (!$user || ($user['role'] ?? '') === 'admin') {
            flash('error', 'User pemilik yang dipilih tidak valid.');
            redirect('/admin/perusahaan');
            return;
        }

        $this->dashModel->assignPerusahaanOwner((int)$id, $userId);
        logActivity('assign_perusahaan_owner', 'perusahaan', "Menghubungkan perusahaan #" . (int)$id . " ke user {$user['name']} ({$user['email']})", $id);
        flash('success', 'Pemilik perusahaan berhasil dihubungkan ke ' . htmlspecialchars($user['name']) . '.');
        redirect('/admin/perusahaan');
    }

    /**
     * Hapus perusahaan kosong (0 paket) tanpa pemilik
     */
    public function deletePerusahaan($id = null) {
        $this->requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !$id) {
            redirect('/admin/perusahaan');
            return;
        }
        if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            flash('error', 'Token CSRF tidak valid.');
            redirect('/admin/perusahaan');
            return;
        }

        if (!$this->dashModel->deleteOrphanPerusahaan((int)$id)) {
            flash('error', 'Perusahaan tidak dapat dihapus karena masih memiliki paket.');
            redirect('/admin/perusahaan');
            return;
        }

        logActivity('delete_orphan_perusahaan', 'perusahaan', "Menghapus perusahaan yatim kosong #" . (int)$id, $id);
        flash('success', 'Perusahaan kosong tanpa pemilik berhasil dihapus.');
        redirect('/admin/perusahaan');
    }

    /**
     * Pencarian Paket Global lintas perusahaan (/admin/paket)
     */
    public function paket() {
        $this->requireAdmin();

        $search       = trim($_GET['search'] ?? '');
        $status       = trim($_GET['status'] ?? '');
        $perusahaanId = (int)($_GET['perusahaan_id'] ?? 0);

        $pakets    = $this->dashModel->getGlobalPakets($search, $status, $perusahaanId, 150);
        $companies = $this->dashModel->getPerusahaanList('');

        $this->view('admin/paket', [
            'title'        => 'Pencarian Paket Global',
            'active'       => 'admin_paket',
            'pakets'       => $pakets,
            'companies'    => $companies,
            'search'       => $search,
            'status'       => $status,
            'perusahaanId' => $perusahaanId,
        ]);
    }

    /**
     * Pengawasan Ruang Kolaborasi & Link Monitoring Publik (/admin/kolaborasi)
     */
    public function kolaborasi() {
        $this->requireAdmin();

        $search = trim($_GET['search'] ?? '');
        $kolabs = $this->dashModel->getKolaborasiList($search);

        $this->view('admin/kolaborasi', [
            'title'  => 'Pengawasan Kolaborasi',
            'active' => 'admin_kolaborasi',
            'kolabs' => $kolabs,
            'search' => $search,
        ]);
    }

    /**
     * Toggle paksa link monitoring publik kolaborasi
     */
    public function toggleKolabShare($id = null) {
        $this->requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !$id) {
            redirect('/admin/kolaborasi');
            return;
        }
        if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            flash('error', 'Token CSRF tidak valid.');
            redirect('/admin/kolaborasi');
            return;
        }

        $enable = (int)($_POST['enable'] ?? 0) === 1;
        $this->dashModel->toggleKolabPublicLink((int)$id, $enable);
        logActivity(
            $enable ? 'enable_public_link' : 'disable_public_link',
            'kolaborasi',
            'Admin ' . ($enable ? 'mengaktifkan' : 'menonaktifkan paksa') . ' link monitoring publik kolaborasi #' . (int)$id,
            $id
        );

        flash('success', 'Status link monitoring publik berhasil diperbarui.');
        redirect('/admin/kolaborasi');
    }

    /**
     * Audit / Activity Log (/admin/logs)
     */
    public function logs() {
        $this->requireAdmin();

        $filter = [
            'module' => trim($_GET['module'] ?? ''),
            'action' => trim($_GET['action'] ?? ''),
            'search' => trim($_GET['search'] ?? ''),
        ];

        $logs    = $this->logModel->getLogs($filter, 150, 0);
        $modules = $this->logModel->getModules();

        $this->view('admin/logs', [
            'title'   => 'Log Aktivitas Sistem',
            'active'  => 'admin_logs',
            'logs'    => $logs,
            'modules' => $modules,
            'filter'  => $filter,
        ]);
    }

    /**
     * Bersihkan log lama (> 90 hari)
     */
    public function clearOldLogs() {
        $this->requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/admin/logs');
            return;
        }
        if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            flash('error', 'Token CSRF tidak valid.');
            redirect('/admin/logs');
            return;
        }

        $deleted = $this->logModel->clearOlderThan(90);
        logActivity('clear_logs', 'admin', "Membersihkan {$deleted} log aktivitas lebih dari 90 hari.");
        flash('success', "{$deleted} baris log aktivitas (> 90 hari) berhasil dibersihkan.");
        redirect('/admin/logs');
    }

    /**
     * Monitor Storage & Pembersihan File Yatim (/admin/storage)
     */
    public function storage() {
        $this->requireAdmin();

        $storageStats = $this->dashModel->getStorageStats();
        $orphans      = isset($_GET['scan']) ? $this->dashModel->findOrphanFiles() : null;

        $this->view('admin/storage', [
            'title'        => 'Monitor Storage Upload',
            'active'       => 'admin_storage',
            'storageStats' => $storageStats,
            'orphans'      => $orphans,
        ]);
    }

    /**
     * Hapus file orphan terpilih
     */
    public function cleanOrphans() {
        $this->requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/admin/storage');
            return;
        }
        if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            flash('error', 'Token CSRF tidak valid.');
            redirect('/admin/storage');
            return;
        }

        $files = $_POST['files'] ?? [];
        if (!is_array($files) || empty($files)) {
            flash('warning', 'Tidak ada file yang dipilih untuk dihapus.');
            redirect('/admin/storage?scan=1');
            return;
        }

        $deleted = $this->dashModel->deleteOrphanFiles($files);
        logActivity('clean_orphans', 'storage', "Menghapus {$deleted} file yatim (orphan) dari folder uploads.");
        flash('success', "{$deleted} file tidak terpakai berhasil dihapus dari storage.");
        redirect('/admin/storage?scan=1');
    }

    /**
     * Pengaturan Global & Banner Pengumuman (/admin/settings)
     */
    public function settings() {
        $this->requireAdmin();

        $settings = $this->settingModel->getAll();

        $this->view('admin/settings', [
            'title'    => 'Pengaturan Global',
            'active'   => 'admin_settings',
            'settings' => $settings,
        ]);
    }

    /**
     * Simpan Pengaturan Global
     */
    public function saveSettings() {
        $this->requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/admin/settings');
            return;
        }
        if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            flash('error', 'Token CSRF tidak valid.');
            redirect('/admin/settings');
            return;
        }

        $allowedTypes = ['info', 'warning', 'success', 'danger'];
        $annType = $_POST['announcement_type'] ?? 'info';
        if (!in_array($annType, $allowedTypes, true)) {
            $annType = 'info';
        }

        $pairs = [
            'announcement_enabled'   => !empty($_POST['announcement_enabled']) ? '1' : '0',
            'announcement_type'      => $annType,
            'announcement_title'     => trim($_POST['announcement_title'] ?? ''),
            'announcement_message'   => trim($_POST['announcement_message'] ?? ''),
            'default_ppn_persen'     => (string)max(0, min(100, (float)($_POST['default_ppn_persen'] ?? 11))),
            'default_tahun_anggaran' => preg_replace('/[^0-9]/', '', $_POST['default_tahun_anggaran'] ?? '2026') ?: '2026',
            'tahun_anggaran_list'    => trim($_POST['tahun_anggaran_list'] ?? '2024,2025,2026,2027'),
        ];

        $this->settingModel->setMany($pairs, $_SESSION['admin_id'] ?? null);
        logActivity('update_settings', 'settings', 'Admin memperbarui pengaturan global & pengumuman.');
        flash('success', 'Pengaturan global berhasil disimpan.');
        redirect('/admin/settings');
    }

    /**
     * Login sebagai User (Impersonate) untuk keperluan support
     */
    public function impersonate($userId = null) {
        $this->requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !$userId) {
            redirect('/user');
            return;
        }
        if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            flash('error', 'Token CSRF tidak valid.');
            redirect('/user');
            return;
        }

        $target = $this->adminModel->findById((int)$userId);
        if (!$target || ($target['role'] ?? '') === 'admin') {
            flash('error', 'Target user tidak ditemukan atau tidak dapat di-impersonate.');
            redirect('/user');
            return;
        }

        // Simpan sesi admin asli (jika belum sedang impersonate)
        if (empty($_SESSION['impersonator_admin'])) {
            $_SESSION['impersonator_admin'] = [
                'id'     => $_SESSION['admin_id'],
                'name'   => $_SESSION['admin_name'],
                'email'  => $_SESSION['admin_email'],
                'role'   => $_SESSION['admin_role'],
                'ref_id' => $_SESSION['admin_ref_id'] ?? null,
            ];
        }

        logActivity('impersonate_start', 'admin', "Admin masuk sebagai user {$target['name']} ({$target['email']})", $target['id']);

        $_SESSION['admin_id']     = $target['id'];
        $_SESSION['admin_name']   = $target['name'];
        $_SESSION['admin_email']  = $target['email'];
        $_SESSION['admin_role']   = $target['role'] ?? 'user';
        $_SESSION['admin_ref_id'] = $target['reference_id'] ?? null;
        unset($_SESSION['active_perusahaan_id'], $_SESSION['active_perusahaan_nama']);

        if (!empty($_POST['perusahaan_id'])) {
            $_SESSION['active_perusahaan_id'] = (int)$_POST['perusahaan_id'];
        }

        flash('info', 'Mode Support: Anda sekarang melihat aplikasi sebagai ' . $target['name'] . '.');
        redirect('/');
    }

    /**
     * Kembali ke akun Admin setelah Impersonate
     */
    public function stopImpersonate() {
        if (empty($_SESSION['impersonator_admin'])) {
            redirect('/');
            return;
        }

        $orig = $_SESSION['impersonator_admin'];
        $impersonatedName = $_SESSION['admin_name'] ?? '';
        unset($_SESSION['impersonator_admin'], $_SESSION['active_perusahaan_id'], $_SESSION['active_perusahaan_nama']);

        $_SESSION['admin_id']     = $orig['id'];
        $_SESSION['admin_name']   = $orig['name'];
        $_SESSION['admin_email']  = $orig['email'];
        $_SESSION['admin_role']   = $orig['role'];
        $_SESSION['admin_ref_id'] = $orig['ref_id'] ?? null;

        logActivity('impersonate_stop', 'admin', "Admin kembali dari mode support ({$impersonatedName}).");
        flash('success', 'Berhasil kembali ke akun Administrator.');
        redirect('/admin');
    }
}
