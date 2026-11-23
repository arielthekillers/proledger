<?php
/**
 * ProLedger - Perusahaan Controller
 * Mengelola data perusahaan mitra dan pergantian perusahaan aktif (switch)
 */

require_once MODEL_PATH . '/Perusahaan.php';

class PerusahaanController {
    
    private $perusahaanModel;
    private $adminModel;
    
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

        $this->perusahaanModel = new Perusahaan();
        require_once MODEL_PATH . '/Admin.php';
        $this->adminModel = new Admin();
    }
    
    /**
     * Tampilkan daftar perusahaan
     */
    public function index() {
        $role = $_SESSION['admin_role'] ?? 'user';
        $userId = $_SESSION['admin_id'] ?? 0;

        if ($role === 'admin') {
            $perusahaan = $this->perusahaanModel->getAll();
        } else {
            $perusahaan = $this->perusahaanModel->getByUser($userId);
        }

        $data = [
            'title' => 'Daftar Perusahaan',
            'active' => 'perusahaan',
            'perusahaan' => $perusahaan,
            'role' => $role,
            'activeCompanyId' => $_SESSION['active_perusahaan_id'] ?? null
        ];
        $this->view('perusahaan/index', $data);
    }
    
    /**
     * Redirect ke halaman perusahaan & buka popup tambah perusahaan
     */
    public function create() {
        redirect('/perusahaan?action=create');
    }
    
    /**
     * Simpan perusahaan baru
     */
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/perusahaan');
            return;
        }

        // Verify CSRF
        if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            flash('error', 'Token keamanan CSRF tidak valid.');
            redirect('/perusahaan?action=create');
            return;
        }

        $nama = trim($_POST['nama'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $telepon = trim($_POST['telepon'] ?? '');
        $alamat = trim($_POST['alamat'] ?? '');
        $website = trim($_POST['website'] ?? '');
        $keterangan = trim($_POST['keterangan'] ?? '');

        if (empty($nama)) {
            flash('error', 'Nama perusahaan wajib diisi.');
            redirect('/perusahaan?action=create');
            return;
        }

        $userId = $_SESSION['admin_id'] ?? null;

        $data = [
            'user_id' => $userId,
            'nama' => $nama,
            'email' => $email ?: null,
            'telepon' => $telepon ?: null,
            'alamat' => $alamat ?: null,
            'website' => $website ?: null,
            'keterangan' => $keterangan ?: null
        ];
        
        $newId = $this->perusahaanModel->create($data);

        // Otomatis aktifkan perusahaan jika belum ada perusahaan aktif
        if (empty($_SESSION['active_perusahaan_id'])) {
            $_SESSION['active_perusahaan_id'] = $newId;
            $_SESSION['active_perusahaan_nama'] = $nama;
        }
        
        flash('success', "Perusahaan <strong>{$nama}</strong> berhasil ditambahkan!");
        header('Location: ' . APP_URL . '/perusahaan');
        exit;
    }
    
    /**
     * Form edit perusahaan
     */
    public function edit($id) {
        $role = $_SESSION['admin_role'] ?? 'user';
        $userId = $_SESSION['admin_id'] ?? 0;

        $perusahaan = $this->perusahaanModel->getById($id);
        if (!$perusahaan) {
            flash('error', 'Perusahaan tidak ditemukan.');
            redirect('/perusahaan');
            return;
        }

        if (!$this->perusahaanModel->isOwnerOrAdmin($id, $userId, $role)) {
            flash('error', 'Anda tidak memiliki hak akses untuk mengedit perusahaan ini.');
            redirect('/perusahaan');
            return;
        }

        $data = [
            'title' => 'Edit Perusahaan',
            'active' => 'perusahaan',
            'perusahaan' => $perusahaan
        ];
        $this->view('perusahaan/edit', $data);
    }
    
    /**
     * Simpan perubahan perusahaan
     */
    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/perusahaan');
            return;
        }

        // Verify CSRF
        if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            flash('error', 'Token keamanan CSRF tidak valid.');
            redirect('/perusahaan/edit/' . $id);
            return;
        }

        $role = $_SESSION['admin_role'] ?? 'user';
        $userId = $_SESSION['admin_id'] ?? 0;

        if (!$this->perusahaanModel->isOwnerOrAdmin($id, $userId, $role)) {
            flash('error', 'Anda tidak memiliki hak akses untuk mengubah perusahaan ini.');
            redirect('/perusahaan');
            return;
        }

        $nama = trim($_POST['nama'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $telepon = trim($_POST['telepon'] ?? '');
        $alamat = trim($_POST['alamat'] ?? '');
        $website = trim($_POST['website'] ?? '');
        $keterangan = trim($_POST['keterangan'] ?? '');

        if (empty($nama)) {
            flash('error', 'Nama perusahaan wajib diisi.');
            redirect('/perusahaan/edit/' . $id);
            return;
        }

        $data = [
            'nama' => $nama,
            'email' => $email ?: null,
            'telepon' => $telepon ?: null,
            'alamat' => $alamat ?: null,
            'website' => $website ?: null,
            'keterangan' => $keterangan ?: null
        ];
        
        $this->perusahaanModel->update($id, $data);

        // Update active company name in session if matches
        if (($SESSION['active_perusahaan_id'] ?? null) == $id) {
            $_SESSION['active_perusahaan_nama'] = $nama;
        }

        flash('success', 'Data perusahaan berhasil diperbarui!');
        header('Location: ' . APP_URL . '/perusahaan');
        exit;
    }
    
    /**
     * Detail perusahaan & proyek
     */
    public function detail($id) {
        $role = $_SESSION['admin_role'] ?? 'user';
        $userId = $_SESSION['admin_id'] ?? 0;

        $perusahaan = $this->perusahaanModel->getById($id);
        if (!$perusahaan) {
            flash('error', 'Perusahaan tidak ditemukan');
            redirect('/perusahaan');
            return;
        }

        if (!$this->perusahaanModel->isOwnerOrAdmin($id, $userId, $role)) {
            flash('error', 'Anda tidak memiliki akses ke perusahaan ini.');
            redirect('/perusahaan');
            return;
        }

        require_once HELPER_PATH . '/Uploader.php';

        // Check for existing login access (admin only)
        $loginAccess = ($role === 'admin') ? $this->adminModel->findByReference('perusahaan', $id) : null;
        
        $data = [
            'title' => $perusahaan['nama'],
            'active' => 'perusahaan',
            'perusahaan' => $perusahaan,
            'login_access' => $loginAccess,
            'role' => $role
        ];
        $this->view('perusahaan/detail', $data);
    }

    /**
     * Switch perusahaan aktif (Single Active Company)
     */
    public function switch($id = null) {
        $role = $_SESSION['admin_role'] ?? 'user';
        $userId = $_SESSION['admin_id'] ?? 0;

        if (!empty($id)) {
            $comp = $this->perusahaanModel->getById($id);
            if ($comp && $this->perusahaanModel->isOwnerOrAdmin($id, $userId, $role)) {
                $_SESSION['active_perusahaan_id'] = $comp['id'];
                $_SESSION['active_perusahaan_nama'] = $comp['nama'];
                flash('success', 'Beralih ke perusahaan: <strong>' . htmlspecialchars($comp['nama']) . '</strong>');
            } else {
                flash('error', 'Perusahaan tidak ditemukan atau tidak dapat diakses.');
            }
        }

        // Pastikan selalu ada 1 perusahaan aktif jika tersedia
        getActivePerusahaan();

        $redirectUrl = $_SERVER['HTTP_REFERER'] ?? (APP_URL . '/');
        if (strpos($redirectUrl, '/perusahaan/switch') !== false) {
            $redirectUrl = APP_URL . '/';
        }
        header('Location: ' . $redirectUrl);
        exit;
    }

    /**
     * Buat akses login terpisah untuk perusahaan (Admin Only)
     */
    public function generateAccess($id) {
        if (($_SESSION['admin_role'] ?? 'admin') !== 'admin') {
            flash('error', 'Hanya Administrator yang dapat membuat akses login.');
            redirect('/perusahaan/detail/' . $id);
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/perusahaan/detail/' . $id);
            return;
        }
        
        if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            flash('error', 'Invalid CSRF token');
            redirect('/perusahaan/detail/' . $id);
            return;
        }

        $perusahaan = $this->perusahaanModel->getById($id);
        if (!$perusahaan) {
            flash('error', 'Perusahaan tidak ditemukan');
            redirect('/perusahaan');
            return;
        }

        if ($this->adminModel->findByReference('perusahaan', $id)) {
            flash('error', 'Akses login sudah ada.');
            redirect('/perusahaan/detail/' . $id);
            return;
        }
        
        if ($this->adminModel->findByEmail($perusahaan['email'])) {
            flash('error', 'Email sudah digunakan untuk akses lain.');
            redirect('/perusahaan/detail/' . $id);
            return;
        }

        try {
            $password = bin2hex(random_bytes(4)); // 8 chars
            
            $this->adminModel->createLoginAccess(
                'perusahaan',
                $id,
                $perusahaan['nama'],
                $perusahaan['email'],
                $password
            );
            
            flash('success', "Akses berhasil dibuat! Password: <strong>{$password}</strong> (Silakan simpan ini)");
        } catch (Exception $e) {
            flash('error', 'Gagal membuat akses: ' . $e->getMessage());
        }
        
        redirect('/perusahaan/detail/' . $id);
    }

    /**
     * Hapus perusahaan
     */
    public function delete($id) {
        $role = $_SESSION['admin_role'] ?? 'user';
        $userId = $_SESSION['admin_id'] ?? 0;

        if (!$this->perusahaanModel->isOwnerOrAdmin($id, $userId, $role)) {
            flash('error', 'Anda tidak memiliki hak akses untuk menghapus perusahaan ini.');
            redirect('/perusahaan');
            return;
        }

        $packets = $this->perusahaanModel->getPaketByPerusahaan($id);
        if (!empty($packets)) {
            flash('error', 'Perusahaan tidak dapat dihapus karena masih memiliki ' . count($packets) . ' paket kegiatan terkait.');
            redirect('/perusahaan');
            return;
        }

        $this->perusahaanModel->delete($id);

        if (($SESSION['active_perusahaan_id'] ?? null) == $id) {
            unset($_SESSION['active_perusahaan_id']);
            unset($_SESSION['active_perusahaan_nama']);
        }

        flash('success', 'Perusahaan berhasil dihapus.');
        header('Location: ' . APP_URL . '/perusahaan');
        exit;
    }

    private function view($view, $data = []) {
        extract($data);
        require_once VIEW_PATH . '/layout/header.php';
        require_once VIEW_PATH . '/' . $view . '.php';
        require_once VIEW_PATH . '/layout/footer.php';
    }
}
