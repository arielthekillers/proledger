<?php
/**
 * ProLedger - User Controller
 * Mengelola pengguna sistem dan persetujuan pendaftaran (Admin Only)
 */

require_once MODEL_PATH . '/Admin.php';

class UserController {
    private $adminModel;
    
    public function __construct() {
        // Enforce Admin Only
        if (($_SESSION['admin_role'] ?? 'admin') !== 'admin') {
            header('Location: ' . APP_URL);
            exit;
        }

        $this->adminModel = new Admin();
    }
    
    /**
     * Tampilkan daftar pengguna & permohonan pendaftaran
     */
    public function index() {
        $tab = $_GET['tab'] ?? 'all';
        $search = trim($_GET['search'] ?? '');
        $role = $_GET['role'] ?? '';
        $status = $_GET['status'] ?? '';

        $filter = [];
        if (!empty($search)) $filter['search'] = $search;
        if (!empty($role)) $filter['role'] = $role;

        if ($tab === 'pending') {
            $filter['status'] = 'pending';
        } elseif (!empty($status)) {
            $filter['status'] = $status;
        }

        $users = $this->adminModel->getAll($filter);
        $pendingCount = $this->adminModel->getPendingCount();

        $data = [
            'title' => 'Manajemen Pengguna',
            'active' => 'users',
            'users' => $users,
            'tab' => $tab,
            'pendingCount' => $pendingCount,
            'filter' => [
                'search' => $search,
                'role' => $role,
                'status' => $status
            ]
        ];
        
        $this->view('user/index', $data);
    }
    
    /**
     * Form tambah pengguna baru oleh admin
     */
    public function create() {
        $data = [
            'title' => 'Tambah Pengguna Baru',
            'active' => 'users'
        ];
        
        $this->view('user/create', $data);
    }
    
    /**
     * Simpan pengguna baru
     */
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/user');
            return;
        }

        if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            flash('error', 'Token CSRF tidak valid.');
            redirect('/user/create');
            return;
        }

        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $password = $_POST['password'] ?? '';
        $role = $_POST['role'] ?? 'user';
        $status = $_POST['status'] ?? 'active';

        setOld([
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'role' => $role,
            'status' => $status
        ]);

        if (empty($name) || empty($email) || empty($password)) {
            flash('error', 'Nama, email, dan password wajib diisi.');
            redirect('/user/create');
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            flash('error', 'Format email tidak valid.');
            redirect('/user/create');
            return;
        }

        if (strlen($password) < 6) {
            flash('error', 'Password minimal 6 karakter.');
            redirect('/user/create');
            return;
        }

        if ($this->adminModel->findByEmail($email)) {
            flash('error', 'Email sudah digunakan oleh akun lain.');
            redirect('/user/create');
            return;
        }

        try {
            $this->adminModel->create([
                'name' => $name,
                'email' => $email,
                'phone' => $phone,
                'password' => $password,
                'role' => $role,
                'status' => $status
            ]);

            clearOld();
            flash('success', 'Pengguna baru berhasil ditambahkan!');
            redirect('/user');
        } catch (Exception $e) {
            flash('error', 'Gagal menambahkan pengguna: ' . $e->getMessage());
            redirect('/user/create');
        }
    }

    /**
     * Form edit pengguna
     */
    public function edit($id) {
        $user = $this->adminModel->findById($id);
        if (!$user) {
            flash('error', 'Pengguna tidak ditemukan.');
            redirect('/user');
            return;
        }

        $data = [
            'title' => 'Edit Pengguna - ' . $user['name'],
            'active' => 'users',
            'user' => $user
        ];

        $this->view('user/edit', $data);
    }

    /**
     * Update data pengguna
     */
    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/user');
            return;
        }

        if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            flash('error', 'Token CSRF tidak valid.');
            redirect('/user/edit/' . $id);
            return;
        }

        $user = $this->adminModel->findById($id);
        if (!$user) {
            flash('error', 'Pengguna tidak ditemukan.');
            redirect('/user');
            return;
        }

        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $role = $_POST['role'] ?? $user['role'];
        $status = $_POST['status'] ?? $user['status'];
        $password = $_POST['password'] ?? '';

        if (empty($name) || empty($email)) {
            flash('error', 'Nama dan email wajib diisi.');
            redirect('/user/edit/' . $id);
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            flash('error', 'Format email tidak valid.');
            redirect('/user/edit/' . $id);
            return;
        }

        // Check email conflict
        $existing = $this->adminModel->findByEmail($email);
        if ($existing && $existing['id'] != $id) {
            flash('error', 'Email sudah digunakan oleh akun lain.');
            redirect('/user/edit/' . $id);
            return;
        }

        // Prevent self role/status demotion if current user
        if ($id == ($_SESSION['admin_id'] ?? null)) {
            if ($status !== 'active') {
                flash('error', 'Anda tidak dapat menonaktifkan akun Anda sendiri.');
                redirect('/user/edit/' . $id);
                return;
            }
            if ($role !== 'admin') {
                flash('error', 'Anda tidak dapat mengubah peran akun Anda sendiri.');
                redirect('/user/edit/' . $id);
                return;
            }
        }

        try {
            $data = [
                'name' => $name,
                'email' => $email,
                'phone' => $phone,
                'role' => $role,
                'status' => $status
            ];

            if (!empty($password)) {
                if (strlen($password) < 6) {
                    flash('error', 'Password baru minimal 6 karakter.');
                    redirect('/user/edit/' . $id);
                    return;
                }
                $data['password'] = $password;
            }

            $this->adminModel->update($id, $data);

            // Update session name if editing self
            if ($id == ($_SESSION['admin_id'] ?? null)) {
                $_SESSION['admin_name'] = $name;
                $_SESSION['admin_email'] = $email;
            }

            flash('success', 'Data pengguna berhasil diperbarui!');
            redirect('/user');
        } catch (Exception $e) {
            flash('error', 'Gagal memperbarui pengguna: ' . $e->getMessage());
            redirect('/user/edit/' . $id);
        }
    }

    /**
     * Setujui pendaftaran (Approve)
     */
    public function approve($id) {
        $user = $this->adminModel->findById($id);
        if (!$user) {
            flash('error', 'Pengguna tidak ditemukan.');
            redirect('/user?tab=pending');
            return;
        }

        $this->adminModel->updateStatus($id, 'active');
        flash('success', "Pendaftaran untuk <strong>" . htmlspecialchars($user['name']) . "</strong> berhasil disetujui! Pengguna kini dapat login.");
        redirect('/user?tab=pending');
    }

    /**
     * Tolak pendaftaran (Reject)
     */
    public function reject($id) {
        $user = $this->adminModel->findById($id);
        if (!$user) {
            flash('error', 'Pengguna tidak ditemukan.');
            redirect('/user?tab=pending');
            return;
        }

        $this->adminModel->updateStatus($id, 'rejected');
        flash('success', "Pendaftaran untuk <strong>" . htmlspecialchars($user['name']) . "</strong> telah ditolak.");
        redirect('/user?tab=pending');
    }

    /**
     * Aktifkan / Nonaktifkan status pengguna
     */
    public function toggleStatus($id) {
        if ($id == ($_SESSION['admin_id'] ?? null)) {
            flash('error', 'Anda tidak dapat mengubah status akun Anda sendiri.');
            redirect('/user');
            return;
        }

        $user = $this->adminModel->findById($id);
        if (!$user) {
            flash('error', 'Pengguna tidak ditemukan.');
            redirect('/user');
            return;
        }

        $newStatus = ($user['status'] === 'active') ? 'inactive' : 'active';
        $this->adminModel->updateStatus($id, $newStatus);

        $statusLabel = ($newStatus === 'active') ? 'diaktifkan' : 'dinonaktifkan';
        flash('success', "Akun " . htmlspecialchars($user['name']) . " berhasil {$statusLabel}.");
        redirect('/user');
    }

    /**
     * Hapus pengguna
     */
    public function delete($id) {
        if ($id == ($_SESSION['admin_id'] ?? null)) {
            flash('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
            redirect('/user');
            return;
        }

        $user = $this->adminModel->findById($id);
        if (!$user) {
            flash('error', 'Pengguna tidak ditemukan.');
            redirect('/user');
            return;
        }

        try {
            $this->adminModel->delete($id);
            flash('success', 'Pengguna berhasil dihapus.');
        } catch (Exception $e) {
            flash('error', 'Gagal menghapus pengguna: ' . $e->getMessage());
        }

        redirect('/user');
    }

    private function view($view, $data = []) {
        extract($data);
        require_once VIEW_PATH . '/layout/header.php';
        require_once VIEW_PATH . '/' . $view . '.php';
        require_once VIEW_PATH . '/layout/footer.php';
    }
}
