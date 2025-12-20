<?php
require_once MODEL_PATH . '/Perusahaan.php';

class PerusahaanController {
    
    private $perusahaanModel;
    private $adminModel;
    
    public function __construct() {
        // Enforce Admin Only
        if (($_SESSION['admin_role'] ?? 'admin') !== 'admin') {
            header('Location: ' . APP_URL);
            exit;
        }

        $this->perusahaanModel = new Perusahaan();
        require_once MODEL_PATH . '/Admin.php';
        $this->adminModel = new Admin();
    }
    
    public function index() {
        $perusahaan = $this->perusahaanModel->getAll();
        $data = [
            'title' => 'Daftar Perusahaan',
            'active' => 'perusahaan',
            'perusahaan' => $perusahaan
        ];
        $this->view('perusahaan/index', $data);
    }
    
    public function create() {
        $data = [
            'title' => 'Tambah Perusahaan',
            'active' => 'perusahaan'
        ];
        $this->view('perusahaan/create', $data);
    }
    
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Verify CSRF
            if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
                flash('error', 'Invalid CSRF token');
                redirect('/perusahaan/create');
                return;
            }

            $data = [
                'nama' => $_POST['nama'],
                'email' => $_POST['email'],
                'telepon' => $_POST['telepon'],
                'alamat' => $_POST['alamat']
            ];
            
            $this->perusahaanModel->create($data);
            
            flash('success', 'Perusahaan berhasil ditambahkan');
            header('Location: ' . APP_URL . '/perusahaan');
            exit;
        }
    }
    
    public function edit($id) {
        $perusahaan = $this->perusahaanModel->getById($id);
        if (!$perusahaan) {
            header('Location: ' . APP_URL . '/perusahaan');
            exit;
        }
        $data = [
            'title' => 'Edit Perusahaan',
            'active' => 'perusahaan',
            'perusahaan' => $perusahaan
        ];
        $this->view('perusahaan/edit', $data);
    }
    
    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
             // Verify CSRF
             if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
                flash('error', 'Invalid CSRF token');
                redirect('/perusahaan/edit/' . $id);
                return;
            }

            $data = [
                'nama' => $_POST['nama'],
                'email' => $_POST['email'],
                'telepon' => $_POST['telepon'],
                'alamat' => $_POST['alamat']
            ];
            
            $this->perusahaanModel->update($id, $data);
            flash('success', 'Data perusahaan berhasil diperbarui');
            header('Location: ' . APP_URL . '/perusahaan');
            exit;
        }
    }
    
    public function detail($id) {
        $perusahaan = $this->perusahaanModel->getById($id);
        if (!$perusahaan) {
            flash('error', 'Perusahaan tidak ditemukan');
            redirect('/perusahaan');
            return;
        }

        $paket = $this->perusahaanModel->getPaketByPerusahaan($id);
        
        // Check for existing login access
        $loginAccess = $this->adminModel->findByReference('perusahaan', $id);
        
        $data = [
            'title' => $perusahaan['nama'],
            'active' => 'perusahaan',
            'perusahaan' => $perusahaan,
            'paket' => $paket,
            'login_access' => $loginAccess
        ];
        $this->view('perusahaan/detail', $data);
    }

    public function generateAccess($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/perusahaan/detail/' . $id);
            return;
        }
        
        // Verify CSRF
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

        // Check if access already exists
        if ($this->adminModel->findByReference('perusahaan', $id)) {
            flash('error', 'Akses login sudah ada.');
            redirect('/perusahaan/detail/' . $id);
            return;
        }
        
        // Check if email already used
        if ($this->adminModel->findByEmail($perusahaan['email'])) {
            flash('error', 'Email sudah digunakan untuk akses lain.');
            redirect('/perusahaan/detail/' . $id);
            return;
        }

        try {
            // Generate Random Password
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

    public function delete($id) {
        $this->perusahaanModel->delete($id);
        flash('success', 'Perusahaan berhasil dihapus');
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
