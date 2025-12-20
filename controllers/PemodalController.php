<?php
/**
 * ProLedger - Pemodal Controller
 */

require_once MODEL_PATH . '/User.php';
require_once MODEL_PATH . '/ModalDetail.php';

class PemodalController {
    private $userModel;
    private $modalModel;
    private $adminModel;
    
    public function __construct() {
        // Enforce Admin Only
        if (($_SESSION['admin_role'] ?? 'admin') !== 'admin') {
            header('Location: ' . APP_URL);
            exit;
        }

        $this->userModel = new User();
        $this->modalModel = new ModalDetail();
        require_once MODEL_PATH . '/Admin.php';
        $this->adminModel = new Admin();
    }
    
    public function index() {
        $users = $this->userModel->getAll();
        
        $data = [
            'title' => 'Pemodal',
            'active' => 'pemodal',
            'users' => $users
        ];
        
        $this->view('pemodal/index', $data);
    }
    
    public function create() {
        $data = [
            'title' => 'Tambah Pemodal',
            'active' => 'pemodal'
        ];
        
        $this->view('pemodal/create', $data);
    }
    
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/pemodal');
            return;
        }
        
        // Verify CSRF
        if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            flash('error', 'Invalid CSRF token');
            redirect('/pemodal/create');
            return;
        }
        
        try {
            $data = [
                'nama' => sanitize($_POST['nama']),
                'email' => sanitize($_POST['email']),
                'telepon' => sanitize($_POST['telepon'])
            ];
            
            $this->userModel->create($data);
            
            flash('success', 'Pemodal berhasil ditambahkan!');
            redirect('/pemodal');
            
        } catch (Exception $e) {
            flash('error', 'Gagal menambahkan pemodal: ' . $e->getMessage());
            setOld($_POST);
            redirect('/pemodal/create');
        }
    }
    
    public function edit($id) {
        $user = $this->userModel->getById($id);
        
        if (!$user) {
            flash('error', 'Pemodal tidak ditemukan');
            redirect('/pemodal');
            return;
        }
        
        $data = [
            'title' => 'Edit Pemodal',
            'active' => 'pemodal',
            'user' => $user
        ];
        
        $this->view('pemodal/edit', $data);
    }
    
    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/pemodal');
            return;
        }
        
        // Verify CSRF
        if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            flash('error', 'Invalid CSRF token');
            redirect('/pemodal/edit/' . $id);
            return;
        }
        
        try {
            $data = [
                'nama' => sanitize($_POST['nama']),
                'email' => sanitize($_POST['email']),
                'telepon' => sanitize($_POST['telepon'])
            ];
            
            $this->userModel->update($id, $data);
            
            flash('success', 'Pemodal berhasil diupdate!');
            redirect('/pemodal');
            
        } catch (Exception $e) {
            flash('error', 'Gagal mengupdate pemodal: ' . $e->getMessage());
            setOld($_POST);
            redirect('/pemodal/edit/' . $id);
        }
    }
    
    public function delete($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/pemodal');
            return;
        }
        
        // Verify CSRF
        if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            flash('error', 'Invalid CSRF token');
            redirect('/pemodal');
            return;
        }
        
        try {
            $this->userModel->delete($id);
            flash('success', 'Pemodal berhasil dihapus!');
        } catch (Exception $e) {
            flash('error', 'Gagal menghapus pemodal: ' . $e->getMessage());
        }
        
        redirect('/pemodal');
    }
    
    public function detail($id) {
        $user = $this->userModel->getById($id);
        
        if (!$user) {
            flash('error', 'Pemodal tidak ditemukan');
            redirect('/pemodal');
            return;
        }
        
        $paket = $this->userModel->getPaketKegiatan($id);
        $modalDetail = $this->modalModel->getByUser($id);
        $totalModal = $this->userModel->getTotalModal($id);
        
        // Check for existing login access
        $loginAccess = $this->adminModel->findByReference('pemodal', $id);
        
        $data = [
            'title' => $user['nama'],
            'active' => 'pemodal',
            'user' => $user,
            'paket' => $paket,
            'modal_detail' => $modalDetail,
            'total_modal' => $totalModal,
            'login_access' => $loginAccess
        ];
        
        $this->view('pemodal/detail', $data);
    }

    public function generateAccess($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/pemodal/detail/' . $id);
            return;
        }
        
        // Verify CSRF
        if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            flash('error', 'Invalid CSRF token');
            redirect('/pemodal/detail/' . $id);
            return;
        }

        $user = $this->userModel->getById($id);
        if (!$user) {
            flash('error', 'Pemodal tidak ditemukan');
            redirect('/pemodal');
            return;
        }

        // Check if access already exists
        if ($this->adminModel->findByReference('pemodal', $id)) {
            flash('error', 'Akses login sudah ada.');
            redirect('/pemodal/detail/' . $id);
            return;
        }
        
        // Check if email already used
        if ($this->adminModel->findByEmail($user['email'])) {
            flash('error', 'Email sudah digunakan untuk akses lain.');
            redirect('/pemodal/detail/' . $id);
            return;
        }

        try {
            // Generate Random Password
            $password = bin2hex(random_bytes(4)); // 8 chars
            
            $this->adminModel->createLoginAccess(
                'pemodal',
                $id,
                $user['nama'],
                $user['email'],
                $password
            );
            
            // In a real app, send email here. For now, show password.
            flash('success', "Akses berhasil dibuat! Password: <strong>{$password}</strong> (Silakan simpan ini)");
            
        } catch (Exception $e) {
            flash('error', 'Gagal membuat akses: ' . $e->getMessage());
        }
        
        redirect('/pemodal/detail/' . $id);
    }
    
    private function view($view, $data = []) {
        extract($data);
        require_once VIEW_PATH . '/layout/header.php';
        require_once VIEW_PATH . '/' . $view . '.php';
        require_once VIEW_PATH . '/layout/footer.php';
    }
}
