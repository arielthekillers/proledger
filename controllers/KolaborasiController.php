<?php
/**
 * ProLedger - KolaborasiController
 * Mengelola ruang kolaborasi antar perusahaan, undangan anggota, link group paket,
 * serta dashboard monitoring bersama (tanpa data privat HPP/Profit).
 */

require_once MODEL_PATH . '/Kolaborasi.php';
require_once MODEL_PATH . '/PaketGroup.php';
require_once MODEL_PATH . '/Perusahaan.php';

class KolaborasiController {
    private $kolabModel;
    private $groupModel;
    private $perusahaanModel;

    public function __construct() {
        if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
            header('Location: ' . APP_URL . '/login');
            exit;
        }
        if (($_SESSION['admin_role'] ?? 'user') === 'admin') {
            header('Location: ' . APP_URL . '/user');
            exit;
        }

        $this->kolabModel = new Kolaborasi();
        $this->groupModel = new PaketGroup();
        $this->perusahaanModel = new Perusahaan();
    }

    /**
     * Daftar Ruang Kolaborasi & Undangan Masuk
     */
    public function index() {
        $userId = (int)($_SESSION['admin_id'] ?? 0);
        $kolabList = $this->kolabModel->getForUser($userId);

        $pendingInvites = array_values(array_filter($kolabList, fn($k) => $k['my_status'] === 'pending'));
        $joinedKolabs   = array_values(array_filter($kolabList, fn($k) => $k['my_status'] === 'joined'));

        $this->view('kolaborasi/index', [
            'title'          => 'Kolaborasi Paket Bersama',
            'active'         => 'kolaborasi',
            'pendingInvites' => $pendingInvites,
            'joinedKolabs'   => $joinedKolabs,
        ]);
    }

    /**
     * Buat Ruang Kolaborasi Baru
     */
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/kolaborasi');
            return;
        }
        if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            flash('error', 'Token keamanan tidak valid.');
            redirect('/kolaborasi');
            return;
        }

        $nama = trim($_POST['nama'] ?? '');
        if ($nama === '') {
            flash('error', 'Nama kolaborasi wajib diisi.');
            redirect('/kolaborasi');
            return;
        }

        $userId = (int)($_SESSION['admin_id'] ?? 0);
        $newId = $this->kolabModel->create([
            'owner_id'       => $userId,
            'nama'           => $nama,
            'instansi'       => trim($_POST['instansi'] ?? ''),
            'tahun_anggaran' => (int)($_POST['tahun_anggaran'] ?? date('Y')),
            'deskripsi'      => trim($_POST['deskripsi'] ?? ''),
        ]);

        flash('success', 'Ruang kolaborasi <strong>' . htmlspecialchars($nama) . '</strong> berhasil dibuat! Undang rekan perusahaan atau bagikan kode undangan.');
        redirect('/kolaborasi/detail/' . $newId);
    }

    /**
     * Gabung Kolaborasi via Kode Undangan
     */
    public function join() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/kolaborasi');
            return;
        }
        if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            flash('error', 'Token keamanan tidak valid.');
            redirect('/kolaborasi');
            return;
        }

        $code = trim($_POST['invite_code'] ?? '');
        if ($code === '') {
            flash('error', 'Kode undangan wajib diisi.');
            redirect('/kolaborasi');
            return;
        }

        $userId = (int)($_SESSION['admin_id'] ?? 0);
        $res = $this->kolabModel->joinByCode($code, $userId);
        if ($res['success']) {
            flash('success', htmlspecialchars($res['message']));
            redirect('/kolaborasi/detail/' . $res['kolab_id']);
        } else {
            flash('error', htmlspecialchars($res['message']));
            redirect('/kolaborasi');
        }
    }

    /**
     * Terima atau Tolak Undangan Masuk
     */
    public function respond($id, $decision = 'accept') {
        $userId = (int)($_SESSION['admin_id'] ?? 0);
        $kolabId = (int)$id;
        $membership = $this->kolabModel->getMembership($kolabId, $userId);
        if (!$membership) {
            flash('error', 'Undangan kolaborasi tidak ditemukan.');
            redirect('/kolaborasi');
            return;
        }

        $this->kolabModel->respondInvite($kolabId, $userId, $decision === 'accept' ? 'accept' : 'decline');
        if ($decision === 'accept') {
            flash('success', 'Anda telah bergabung! Silakan hubungkan Group Paket perusahaan Anda ke kolaborasi ini.');
            redirect('/kolaborasi/detail/' . $kolabId);
        } else {
            flash('info', 'Undangan kolaborasi ditolak.');
            redirect('/kolaborasi');
        }
    }

    /**
     * Dashboard Monitoring Kolaborasi
     */
    public function detail($id) {
        $kolabId = (int)$id;
        $userId = (int)($_SESSION['admin_id'] ?? 0);

        $kolab = $this->kolabModel->getById($kolabId);
        if (!$kolab) {
            flash('error', 'Ruang kolaborasi tidak ditemukan.');
            redirect('/kolaborasi');
            return;
        }

        $membership = $this->kolabModel->getMembership($kolabId, $userId);
        if (!$membership || $membership['status'] !== 'joined') {
            flash('error', 'Anda tidak memiliki akses ke ruang kolaborasi ini.');
            redirect('/kolaborasi');
            return;
        }

        $isOwner = ($membership['role'] === 'owner' || (int)$kolab['owner_id'] === $userId);
        $members = $this->kolabModel->getMembers($kolabId);
        $linkedGroups = $this->kolabModel->getLinkedGroups($kolabId);
        $dashboard = $this->kolabModel->getDashboardData($kolabId);

        // Group milik user sendiri yang bisa di-share ke kolaborasi ini
        $myCompanies = $this->perusahaanModel->getByUser($userId);
        $myCompIds = array_map('intval', array_column($myCompanies, 'id'));
        $allGroups = $this->groupModel->getAll(null, $userId);
        $linkedIds = array_map('intval', array_column($linkedGroups, 'group_id'));

        $myAvailableGroups = array_values(array_filter($allGroups, function ($g) use ($userId, $myCompIds, $linkedIds) {
            return $this->groupModel->userCanAccess($g, $userId, $myCompIds, 'user')
                && !in_array((int)$g['id'], $linkedIds, true);
        }));

        $myLinkedGroups = array_values(array_filter($linkedGroups, fn($lg) => (int)$lg['user_id'] === $userId));

        $this->view('kolaborasi/detail', [
            'title'             => 'Kolaborasi: ' . $kolab['nama'],
            'active'            => 'kolaborasi',
            'kolab'             => $kolab,
            'isOwner'           => $isOwner,
            'userId'            => $userId,
            'members'           => $members,
            'linkedGroups'      => $linkedGroups,
            'myAvailableGroups' => $myAvailableGroups,
            'myLinkedGroups'    => $myLinkedGroups,
            'pakets'            => $dashboard['pakets'],
            'companyStats'      => $dashboard['companyStats'],
            'summary'           => $dashboard['summary'],
        ]);
    }

    /**
     * Laporan Keuangan Koordinator Kolaborasi (khusus Owner/Koordinator)
     */
    public function laporan($id) {
        $kolabId = (int)$id;
        $userId = (int)($_SESSION['admin_id'] ?? 0);
        $kolab = $this->kolabModel->getById($kolabId);
        if (!$kolab || (int)$kolab['owner_id'] !== $userId) {
            flash('error', 'Hanya koordinator ruang kolaborasi yang dapat mengakses laporan keuangan.');
            redirect('/kolaborasi/detail/' . $kolabId);
            return;
        }

        $dashboard = $this->kolabModel->getDashboardData($kolabId);

        $this->view('kolaborasi/laporan_keuangan', [
            'title'        => 'Laporan Keuangan Koordinator: ' . $kolab['nama'],
            'active'       => 'kolaborasi',
            'kolab'        => $kolab,
            'pakets'       => $dashboard['pakets'],
            'companyStats' => $dashboard['companyStats'],
            'summary'      => $dashboard['summary'],
        ]);
    }

    /**
     * Analisa Distribusi Per Tujuan Lintas Perusahaan
     */
    public function distribusi($id) {
        $kolabId = (int)$id;
        $userId = (int)($_SESSION['admin_id'] ?? 0);
        $kolab = $this->kolabModel->getById($kolabId);
        if (!$kolab) {
            flash('error', 'Ruang kolaborasi tidak ditemukan.');
            redirect('/kolaborasi');
            return;
        }

        $membership = $this->kolabModel->getMembership($kolabId, $userId);
        if (!$membership || $membership['status'] !== 'joined') {
            flash('error', 'Anda tidak memiliki akses ke ruang kolaborasi ini.');
            redirect('/kolaborasi');
            return;
        }

        $analisa = $this->kolabModel->getDistribusiPerTujuan($kolabId);

        $this->view('kolaborasi/distribusi_tujuan', [
            'title'      => 'Analisa Distribusi Per Tujuan: ' . $kolab['nama'],
            'active'     => 'kolaborasi',
            'kolab'      => $kolab,
            'tujuanList' => $analisa['tujuanList'],
            'totals'     => $analisa['totals'],
        ]);
    }

    /**
     * Update Pengaturan Ruang Kolaborasi (Owner)
     */
    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/kolaborasi');
            return;
        }
        $kolabId = (int)$id;
        $userId = (int)($_SESSION['admin_id'] ?? 0);
        $kolab = $this->kolabModel->getById($kolabId);
        if (!$kolab || (int)$kolab['owner_id'] !== $userId) {
            flash('error', 'Hanya koordinator yang dapat mengubah pengaturan kolaborasi.');
            redirect('/kolaborasi');
            return;
        }
        if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            flash('error', 'Token keamanan tidak valid.');
            redirect('/kolaborasi/detail/' . $kolabId);
            return;
        }

        $nama = trim($_POST['nama'] ?? '');
        if ($nama === '') {
            flash('error', 'Nama kolaborasi wajib diisi.');
            redirect('/kolaborasi/detail/' . $kolabId);
            return;
        }

        $this->kolabModel->update($kolabId, [
            'nama'           => $nama,
            'instansi'       => trim($_POST['instansi'] ?? ''),
            'tahun_anggaran' => (int)($_POST['tahun_anggaran'] ?? date('Y')),
            'deskripsi'      => trim($_POST['deskripsi'] ?? ''),
        ]);

        flash('success', 'Pengaturan ruang kolaborasi diperbarui.');
        redirect('/kolaborasi/detail/' . $kolabId);
    }

    /**
     * Undang Anggota via Email (Owner)
     */
    public function invite($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/kolaborasi');
            return;
        }
        $kolabId = (int)$id;
        $userId = (int)($_SESSION['admin_id'] ?? 0);
        $kolab = $this->kolabModel->getById($kolabId);
        if (!$kolab || (int)$kolab['owner_id'] !== $userId) {
            flash('error', 'Hanya koordinator yang dapat mengundang anggota.');
            redirect('/kolaborasi/detail/' . $kolabId);
            return;
        }
        if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            flash('error', 'Token keamanan tidak valid.');
            redirect('/kolaborasi/detail/' . $kolabId);
            return;
        }

        $email = trim($_POST['email'] ?? '');
        $res = $this->kolabModel->inviteByEmail($kolabId, $email, $userId);
        flash($res['success'] ? 'success' : 'error', htmlspecialchars($res['message']));
        redirect('/kolaborasi/detail/' . $kolabId);
    }

    /**
     * Hubungkan Group Paket ke Kolaborasi
     */
    public function linkGroup($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/kolaborasi');
            return;
        }
        $kolabId = (int)$id;
        $userId = (int)($_SESSION['admin_id'] ?? 0);
        $membership = $this->kolabModel->getMembership($kolabId, $userId);
        if (!$membership || $membership['status'] !== 'joined') {
            flash('error', 'Akses ditolak.');
            redirect('/kolaborasi');
            return;
        }
        if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            flash('error', 'Token keamanan tidak valid.');
            redirect('/kolaborasi/detail/' . $kolabId);
            return;
        }

        $rawIds = $_POST['group_ids'] ?? [];
        if (!is_array($rawIds)) {
            $rawIds = [$rawIds];
        }
        if (!empty($_POST['group_id'])) {
            $rawIds[] = $_POST['group_id'];
        }
        $groupIds = array_values(array_unique(array_filter(array_map('intval', $rawIds), fn($v) => $v > 0)));

        if (empty($groupIds)) {
            flash('error', 'Pilih minimal satu Group Paket untuk dihubungkan.');
            redirect('/kolaborasi/detail/' . $kolabId);
            return;
        }

        // Validasi kepemilikan group oleh user
        $myCompanies = $this->perusahaanModel->getByUser($userId);
        $myCompIds = array_map('intval', array_column($myCompanies, 'id'));

        $linkedNames = [];
        foreach ($groupIds as $gid) {
            $group = $this->groupModel->getById($gid);
            if (!$group || !$this->groupModel->userCanAccess($group, $userId, $myCompIds, 'user')) {
                continue;
            }
            $this->kolabModel->linkGroup($kolabId, $gid, $userId);
            $linkedNames[] = $group['nama_group'];
        }

        if (empty($linkedNames)) {
            flash('error', 'Tidak ada Group Paket valid yang dapat dihubungkan.');
        } elseif (count($linkedNames) === 1) {
            flash('success', 'Group <strong>' . htmlspecialchars($linkedNames[0]) . '</strong> berhasil dihubungkan ke kolaborasi.');
        } else {
            flash('success', count($linkedNames) . ' Group Paket berhasil dihubungkan ke kolaborasi.');
        }
        redirect('/kolaborasi/detail/' . $kolabId);
    }

    /**
     * Lepas Group Paket dari Kolaborasi
     */
    public function unlinkGroup($id, $groupId) {
        $kolabId = (int)$id;
        $userId = (int)($_SESSION['admin_id'] ?? 0);
        $kolab = $this->kolabModel->getById($kolabId);
        if (!$kolab) {
            redirect('/kolaborasi');
            return;
        }

        $isOwner = ((int)$kolab['owner_id'] === $userId);
        $this->kolabModel->unlinkGroup($kolabId, (int)$groupId, $userId, $isOwner);
        flash('success', 'Group paket dilepas dari ruang kolaborasi.');
        redirect('/kolaborasi/detail/' . $kolabId);
    }

    /**
     * Keluarkan Anggota (Owner)
     */
    public function removeMember($id, $targetUserId) {
        $kolabId = (int)$id;
        $userId = (int)($_SESSION['admin_id'] ?? 0);
        $kolab = $this->kolabModel->getById($kolabId);
        if (!$kolab || (int)$kolab['owner_id'] !== $userId) {
            flash('error', 'Akses ditolak.');
            redirect('/kolaborasi/detail/' . $kolabId);
            return;
        }

        $this->kolabModel->removeMember($kolabId, (int)$targetUserId);
        flash('success', 'Anggota dan group terkait telah dikeluarkan dari kolaborasi.');
        redirect('/kolaborasi/detail/' . $kolabId);
    }

    /**
     * Toggle / Regenerate Link Read-Only Publik
     */
    public function toggleShare($id) {
        $kolabId = (int)$id;
        $userId = (int)($_SESSION['admin_id'] ?? 0);
        $kolab = $this->kolabModel->getById($kolabId);
        if (!$kolab || (int)$kolab['owner_id'] !== $userId) {
            flash('error', 'Hanya koordinator yang dapat mengatur link monitoring.');
            redirect('/kolaborasi/detail/' . $kolabId);
            return;
        }

        $newState = ((int)$kolab['share_enabled'] === 1) ? 0 : 1;
        $this->kolabModel->toggleShare($kolabId, $newState);
        logActivity($newState ? 'enable_public_link' : 'disable_public_link', 'kolaborasi', ($newState ? 'Mengaktifkan' : 'Menonaktifkan') . ' link monitoring publik untuk kolaborasi ' . $kolab['nama'], $kolabId);
        flash('success', $newState ? 'Link monitoring publik diaktifkan.' : 'Link monitoring publik dinonaktifkan.');
        redirect('/kolaborasi/detail/' . $kolabId);
    }

    public function regenerateShare($id) {
        $kolabId = (int)$id;
        $userId = (int)($_SESSION['admin_id'] ?? 0);
        $kolab = $this->kolabModel->getById($kolabId);
        if (!$kolab || (int)$kolab['owner_id'] !== $userId) {
            flash('error', 'Akses ditolak.');
            redirect('/kolaborasi/detail/' . $kolabId);
            return;
        }

        $this->kolabModel->regenerateShareToken($kolabId);
        flash('success', 'Token link monitoring diperbarui. Link lama tidak lagi berlaku.');
        redirect('/kolaborasi/detail/' . $kolabId);
    }

    /**
     * Hapus Ruang Kolaborasi (Owner)
     */
    public function delete($id) {
        $kolabId = (int)$id;
        $userId = (int)($_SESSION['admin_id'] ?? 0);
        $kolab = $this->kolabModel->getById($kolabId);
        if (!$kolab || (int)$kolab['owner_id'] !== $userId) {
            flash('error', 'Hanya koordinator yang dapat menghapus ruang kolaborasi.');
            redirect('/kolaborasi');
            return;
        }

        $this->kolabModel->delete($kolabId);
        flash('success', 'Ruang kolaborasi <strong>' . htmlspecialchars($kolab['nama']) . '</strong> berhasil dihapus.');
        redirect('/kolaborasi');
    }

    /**
     * AJAX Read-Only Modal Content untuk paket di dalam kolaborasi
     */
    public function modalView($kolabId, $paketIdOrKode, $module = 'info') {
        $userId = (int)($_SESSION['admin_id'] ?? 0);
        $membership = $this->kolabModel->getMembership((int)$kolabId, $userId);
        if (!$membership || $membership['status'] !== 'joined') {
            http_response_code(403);
            echo '<div class="p-6 text-center text-xs text-rose-600">Akses ditolak.</div>';
            exit;
        }

        $allowedModules = ['info', 'items', 'pemesanan', 'distribusi', 'dokumentasi', 'dokumen', 'dokumen_paket'];
        $module = in_array($module, $allowedModules, true) ? $module : 'info';

        $modalData = $this->kolabModel->getReadonlyModalData((int)$kolabId, (string)$paketIdOrKode);
        if (!$modalData) {
            http_response_code(404);
            echo '<div class="p-6 text-center text-xs text-slate-500">Data paket tidak ditemukan di kolaborasi ini.</div>';
            exit;
        }

        require VIEW_PATH . '/kolaborasi/_readonly_modal.php';
        exit;
    }

    private function view($view, $data = []) {
        extract($data);
        require_once VIEW_PATH . '/layout/header.php';
        require_once VIEW_PATH . '/' . $view . '.php';
        require_once VIEW_PATH . '/layout/footer.php';
    }
}
