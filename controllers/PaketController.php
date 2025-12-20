<?php
/**
 * ProLedger - Paket Controller
 */

require_once MODEL_PATH . '/PaketKegiatan.php';
require_once MODEL_PATH . '/User.php';
require_once MODEL_PATH . '/Transaksi.php';
require_once MODEL_PATH . '/ModalDetail.php';
require_once MODEL_PATH . '/Perusahaan.php';

class PaketController {
    private $paketModel;
    private $userModel;
    private $transaksiModel;
    private $modalModel;
    private $perusahaanModel;
    
    public function __construct() {
        $this->paketModel = new PaketKegiatan();
        $this->userModel = new User();
        $this->transaksiModel = new Transaksi();
        $this->modalModel = new ModalDetail();
        $this->perusahaanModel = new Perusahaan();
    }
    
    // Helper to enforce admin access
    private function requireAdmin() {
        if (($_SESSION['admin_role'] ?? 'admin') !== 'admin') {
            flash('error', 'Akses ditolak. Anda tidak memiliki izin untuk melakukan tindakan ini.');
            header('Location: ' . APP_URL . '/paket');
            exit;
        }
    }
    
    public function index() {
        $filters = [
            'status' => $_GET['status'] ?? null,
            'search' => $_GET['search'] ?? null,
            'min_pagu' => $_GET['min_pagu'] ?? null,
            'max_pagu' => $_GET['max_pagu'] ?? null
        ];
        
        $paket = $this->paketModel->getAll($filters);
        
        // Role Filtering
        $role = $_SESSION['admin_role'] ?? 'admin';
        $refId = $_SESSION['admin_ref_id'] ?? null;
        
        if ($role === 'pemodal') {
            $myInvestments = $this->modalModel->getByUser($refId);
            $myPaketIds = array_column($myInvestments, 'paket_id');
            $paket = array_filter($paket, function($p) use ($myPaketIds) {
                return in_array($p['id'], $myPaketIds);
            });
        } elseif ($role === 'perusahaan') {
            $paket = array_filter($paket, function($p) use ($refId) {
                return $p['perusahaan_id'] == $refId;
            });
        }
        
        $data = [
            'title' => 'Paket Kegiatan',
            'active' => 'paket',
            'paket' => $paket,
            'filters' => $filters
        ];
        
        $this->view('paket/index', $data);
    }
    
    public function create() {
        $this->requireAdmin();
        
        $users = $this->userModel->getAll();
        $all_perusahaan = $this->perusahaanModel->getAll();
        
        $data = [
            'title' => 'Tambah Paket Kegiatan',
            'active' => 'paket',
            'users' => $users,
            'all_perusahaan' => $all_perusahaan
        ];
        
        $this->view('paket/create', $data);
    }
    
    public function store() {
        $this->requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/paket');
            return;
        }
        
        // Verify CSRF
        if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            flash('error', 'Invalid CSRF token');
            redirect('/paket/create');
            return;
        }
        
        try {
            // Parse Rupiah values
            $pagu = parseRupiah($_POST['pagu']);
            $rab = parseRupiah($_POST['rab']);
            
            $data = [
                'nama_paket' => sanitize($_POST['nama_paket']),
                'perusahaan_id' => !empty($_POST['perusahaan_id']) ? (int)$_POST['perusahaan_id'] : null,
                'perusahaan' => sanitize($_POST['perusahaan'] ?? ''), // Keep simpler text as fallback or legacy
                'pagu' => $pagu,
                'rab' => $rab,
                'jumlah_pemodal' => (int)$_POST['jumlah_pemodal'],
                'status' => $_POST['status'] ?? 'Kontrak',
                'pemodal' => $_POST['pemodal'] ?? []
            ];
            
            $paket_id = $this->paketModel->create($data);
            
            flash('success', 'Paket kegiatan berhasil ditambahkan!');
            redirect('/paket/detail/' . $paket_id);
            
        } catch (Exception $e) {
            flash('error', 'Gagal menambahkan paket: ' . $e->getMessage());
            setOld($_POST);
            redirect('/paket/create');
        }
    }
    
    public function detail($id) {
        $paket = $this->paketModel->getById($id);
        
        if (!$paket) {
            flash('error', 'Paket tidak ditemukan');
            redirect('/paket');
            return;
        }

        // Access Check
        $role = $_SESSION['admin_role'] ?? 'admin';
        $refId = $_SESSION['admin_ref_id'] ?? null;

        if ($role === 'perusahaan' && $paket['perusahaan_id'] != $refId) {
            flash('error', 'Akses ditolak.');
            redirect('/paket');
            return;
        }

        if ($role === 'pemodal') {
            $myInvestments = $this->modalModel->getByUser($refId);
            $myPaketIds = array_column($myInvestments, 'paket_id');
            if (!in_array($id, $myPaketIds)) {
                flash('error', 'Akses ditolak.');
                redirect('/paket');
                return;
            }
        }
        
        $summary = $this->paketModel->getSummary($id);
        $modalDetail = $this->modalModel->getByPaket($id);
        $transaksi = $this->transaksiModel->getByPaket($id);
        
        $data = [
            'title' => $paket['nama_paket'],
            'active' => 'paket',
            'paket' => $paket,
            'summary' => $summary,
            'modal_detail' => $modalDetail,
            'transaksi' => $transaksi
        ];
        
        $this->view('paket/detail', $data);
    }
    
    public function edit($id) {
        $this->requireAdmin();

        $paket = $this->paketModel->getById($id);
        
        if (!$paket) {
            flash('error', 'Paket tidak ditemukan');
            redirect('/paket');
            return;
        }
        
        $users = $this->userModel->getAll();
        $all_perusahaan = $this->perusahaanModel->getAll();
        
        $data = [
            'title' => 'Edit Paket Kegiatan',
            'active' => 'paket',
            'paket' => $paket,
            'users' => $users,
            'all_perusahaan' => $all_perusahaan
        ];
        
        $this->view('paket/edit', $data);
    }
    
    public function update($id) {
        $this->requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/paket');
            return;
        }
        
        // Verify CSRF
        if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            flash('error', 'Invalid CSRF token');
            redirect('/paket/edit/' . $id);
            return;
        }
        
        try {
            // Parse Rupiah values
            $pagu = parseRupiah($_POST['pagu']);
            $rab = parseRupiah($_POST['rab']);
            
            $data = [
                'nama_paket' => sanitize($_POST['nama_paket']),
                'perusahaan_id' => !empty($_POST['perusahaan_id']) ? (int)$_POST['perusahaan_id'] : null,
                'perusahaan' => sanitize($_POST['perusahaan'] ?? ''),
                'pagu' => $pagu,
                'rab' => $rab,
                'jumlah_pemodal' => (int)$_POST['jumlah_pemodal'],
                'status' => $_POST['status'] ?? 'Kontrak',
                'pemodal' => $_POST['pemodal'] ?? []
            ];
            
            $this->paketModel->update($id, $data);
            
            flash('success', 'Paket kegiatan berhasil diupdate!');
            redirect('/paket/detail/' . $id);
            
        } catch (Exception $e) {
            flash('error', 'Gagal mengupdate paket: ' . $e->getMessage());
            setOld($_POST);
            redirect('/paket/edit/' . $id);
        }
    }
    
    public function delete($id) {
        $this->requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/paket');
            return;
        }
        
        // Verify CSRF
        if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            flash('error', 'Invalid CSRF token');
            redirect('/paket');
            return;
        }
        
        try {
            $this->paketModel->delete($id);
            flash('success', 'Paket kegiatan berhasil dihapus!');
        } catch (Exception $e) {
            flash('error', 'Gagal menghapus paket: ' . $e->getMessage());
        }
        
        redirect('/paket');
    }
    
    private function view($view, $data = []) {
        extract($data);
        require_once VIEW_PATH . '/layout/header.php';
        require_once VIEW_PATH . '/' . $view . '.php';
        require_once VIEW_PATH . '/layout/footer.php';
    }
}

// Helper function to parse Rupiah
function parseRupiah($rupiah) {
    return (float)preg_replace('/[^0-9]/', '', $rupiah);
}
