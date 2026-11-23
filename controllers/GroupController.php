<?php
/**
 * ProLedger - Group Controller
 * Mengelola Group / Kelompok Paket Kegiatan (Payung Proyek / Program)
 */

require_once MODEL_PATH . '/PaketGroup.php';
require_once MODEL_PATH . '/PaketKegiatan.php';
require_once MODEL_PATH . '/Perusahaan.php';

class GroupController {
    private $groupModel;
    private $paketModel;
    private $perusahaanModel;

    public function __construct() {
        if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
            header('Location: ' . APP_URL . '/login');
            exit;
        }

        // Admin hanya mengelola manajemen pengguna
        if (($_SESSION['admin_role'] ?? 'user') === 'admin') {
            header('Location: ' . APP_URL . '/user');
            exit;
        }

        $this->groupModel = new PaketGroup();
        $this->paketModel = new PaketKegiatan();
        $this->perusahaanModel = new Perusahaan();
    }

    private function requireAdminOrUser() {
        $role = $_SESSION['admin_role'] ?? 'user';
        if ($role !== 'admin' && $role !== 'user') {
            flash('error', 'Akses ditolak.');
            redirect('/group');
            exit;
        }
    }

    /**
     * Tampilkan daftar group paket
     */
    public function index() {
        $role = $_SESSION['admin_role'] ?? 'user';
        $userId = $_SESSION['admin_id'] ?? 0;
        $activeCompId = $_SESSION['active_perusahaan_id'] ?? null;

        if ($activeCompId) {
            $groups = $this->groupModel->getAll($activeCompId);
        } elseif ($role === 'user') {
            $myComps = $this->perusahaanModel->getByUser($userId);
            $myCompIds = array_column($myComps, 'id');
            $allGroups = $this->groupModel->getAll();
            $groups = array_filter($allGroups, function($g) use ($myCompIds) {
                return empty($g['perusahaan_id']) || in_array($g['perusahaan_id'], $myCompIds);
            });
        } else {
            $groups = $this->groupModel->getAll();
        }

        // Ambil daftar perusahaan untuk modal tambah
        if ($role === 'admin') {
            $perusahaanList = $this->perusahaanModel->getAll();
        } else {
            $perusahaanList = $this->perusahaanModel->getByUser($userId);
        }

        $data = [
            'title' => 'Group Paket Kegiatan',
            'active' => 'group',
            'groups' => $groups,
            'perusahaanList' => $perusahaanList,
            'activeCompId' => $activeCompId,
            'role' => $role
        ];

        $this->view('group/index', $data);
    }

    /**
     * Detail Group & Roll-up Keuangan
     */
    public function detail($id) {
        $group = $this->groupModel->getById($id);
        if (!$group) {
            flash('error', 'Group paket tidak ditemukan.');
            redirect('/group');
            return;
        }

        $role = $_SESSION['admin_role'] ?? 'user';
        $userId = $_SESSION['admin_id'] ?? 0;
        $activeCompId = $_SESSION['active_perusahaan_id'] ?? null;

        $paketList = $this->groupModel->getPaketList($id);
        $stats = PaketGroup::computeStatsFromList($paketList);

        if ($role === 'admin') {
            $perusahaanList = $this->perusahaanModel->getAll();
        } else {
            $perusahaanList = $this->perusahaanModel->getByUser($userId);
        }

        $data = [
            'title' => 'Group: ' . $group['nama_group'],
            'active' => 'group',
            'group' => $group,
            'paketList' => $paketList,
            'stats' => $stats,
            'perusahaanList' => $perusahaanList,
            'activeCompId' => $activeCompId,
            'role' => $role
        ];

        $this->view('group/detail', $data);
    }

    /**
     * Simpan Group Baru
     */
    public function store() {
        $this->requireAdminOrUser();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/group');
            return;
        }

        if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            flash('error', 'Token keamanan tidak valid.');
            redirect('/group');
            return;
        }

        $nama = trim($_POST['nama_group'] ?? '');
        $kode = trim($_POST['kode_group'] ?? '');
        $deskripsi = trim($_POST['deskripsi'] ?? '');
        $perusahaanId = !empty($_POST['perusahaan_id']) ? (int)$_POST['perusahaan_id'] : ($_SESSION['active_perusahaan_id'] ?? null);

        if (empty($nama)) {
            flash('error', 'Nama group wajib diisi.');
            redirect('/group');
            return;
        }

        $newId = $this->groupModel->create([
            'perusahaan_id' => $perusahaanId,
            'nama_group' => $nama,
            'kode_group' => $kode ?: null,
            'deskripsi' => $deskripsi ?: null
        ]);

        flash('success', "Group <strong>" . htmlspecialchars($nama) . "</strong> berhasil dibuat!");
        redirect('/group');
    }

    /**
     * Endpoint AJAX untuk membuat group langsung dari form paket (tanpa reload)
     */
    public function ajaxStore() {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Metode request tidak diizinkan.']);
            exit;
        }

        $nama = trim($_POST['nama_group'] ?? '');
        $kode = trim($_POST['kode_group'] ?? '');
        $deskripsi = trim($_POST['deskripsi'] ?? '');
        $perusahaanId = !empty($_POST['perusahaan_id']) ? (int)$_POST['perusahaan_id'] : ($_SESSION['active_perusahaan_id'] ?? null);

        if (empty($nama)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Nama group wajib diisi.']);
            exit;
        }

        try {
            $newId = $this->groupModel->create([
                'perusahaan_id' => $perusahaanId,
                'nama_group' => $nama,
                'kode_group' => $kode ?: null,
                'deskripsi' => $deskripsi ?: null
            ]);

            echo json_encode([
                'success' => true,
                'message' => 'Group berhasil dibuat!',
                'group' => [
                    'id' => $newId,
                    'nama_group' => $nama,
                    'kode_group' => $kode
                ]
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Gagal membuat group: ' . $e->getMessage()]);
        }
        exit;
    }

    /**
     * Update Group
     */
    public function update($id) {
        $this->requireAdminOrUser();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/group');
            return;
        }

        if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            flash('error', 'Token CSRF tidak valid.');
            redirect('/group');
            return;
        }

        $nama = trim($_POST['nama_group'] ?? '');
        $kode = trim($_POST['kode_group'] ?? '');
        $deskripsi = trim($_POST['deskripsi'] ?? '');
        $perusahaanId = !empty($_POST['perusahaan_id']) ? (int)$_POST['perusahaan_id'] : null;

        if (empty($nama)) {
            flash('error', 'Nama group wajib diisi.');
            redirect('/group');
            return;
        }

        $data = [
            'nama_group' => $nama,
            'kode_group' => $kode ?: null,
            'deskripsi' => $deskripsi ?: null
        ];
        if ($perusahaanId !== null) {
            $data['perusahaan_id'] = $perusahaanId;
        }

        $this->groupModel->update($id, $data);

        flash('success', 'Group berhasil diperbarui!');
        redirect($_SERVER['HTTP_REFERER'] ?? (APP_URL . '/group'));
    }

    /**
     * Hapus Group
     */
    public function delete($id) {
        $this->requireAdminOrUser();

        $group = $this->groupModel->getById($id);
        if ($group) {
            $this->groupModel->delete($id);
            flash('success', "Group <strong>" . htmlspecialchars($group['nama_group']) . "</strong> berhasil dihapus. Paket anggota kini berstatus independen.");
        } else {
            flash('error', 'Group tidak ditemukan.');
        }

        redirect('/group');
    }

    private function view($view, $data = []) {
        extract($data);
        require_once VIEW_PATH . '/layout/header.php';
        require_once VIEW_PATH . '/' . $view . '.php';
        require_once VIEW_PATH . '/layout/footer.php';
    }
}
