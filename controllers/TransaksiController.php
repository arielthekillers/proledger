<?php
/**
 * ProLedger - Transaksi Controller
 */

require_once MODEL_PATH . '/Transaksi.php';
require_once MODEL_PATH . '/PaketKegiatan.php';

class TransaksiController {
    private $transaksiModel;
    private $paketModel;
    
    public function __construct() {
        $this->transaksiModel = new Transaksi();
        $this->paketModel = new PaketKegiatan();
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
        // Global Transaction History
        $filters = [
            'paket_id' => $_GET['paket_id'] ?? null,
            'search' => $_GET['search'] ?? null,
            'tipe' => $_GET['tipe'] ?? null,
            'kategori' => $_GET['kategori'] ?? null,
            'start_date' => $_GET['start_date'] ?? null,
            'end_date' => $_GET['end_date'] ?? null
        ];
        
        // We need a method in Transaksi model to get all with packet info
        // Let's assume getByPaket can be adapted or we add a new one.
        // For now, let's use a new method we will add to model: getAllWithPaket($filters)
        $transaksi = $this->transaksiModel->getAllWithPaket($filters);
        
        // Get all active packets for filter dropdown
        $allPaket = $this->paketModel->getAll();

        // Role Filtering
        $role = $_SESSION['admin_role'] ?? 'admin';
        $refId = $_SESSION['admin_ref_id'] ?? null;

        if ($role === 'pemodal') {
            // Filter Transactions
            $myInvestments = (new ModalDetail())->getByUser($refId); // Need to instantiate or reuse
            $myPaketIds = array_column($myInvestments, 'paket_id');
            
            $transaksi = array_filter($transaksi, function($t) use ($myPaketIds) {
                return in_array($t['paket_id'], $myPaketIds);
            });

            // Filter Dropdown
            $allPaket = array_filter($allPaket, function($p) use ($myPaketIds) {
                return in_array($p['id'], $myPaketIds);
            });

        } elseif ($role === 'perusahaan') {
            // Filter Transactions
            $transaksi = array_filter($transaksi, function($t) use ($refId) {
                return isset($t['perusahaan_id']) && $t['perusahaan_id'] == $refId; // Assuming join provides perusahaan_id, otherwise simpler check needed
            });
             // Note: getAllWithPaket might not return perusahaan_id unless joined. 
             // Safest is to rely on Paket model filtering first if possible, but let's do array filter for now 
             // assuming $transaksi contains basic info. Actually, getAllWithPaket probably joins paket table.

             // Let's safe filter the dropdown at least
             $allPaket = array_filter($allPaket, function($p) use ($refId) {
                 return $p['perusahaan_id'] == $refId;
             });
             
             // Double check transactions against visible packets
             $visiblePaketIds = array_column($allPaket, 'id');
             $transaksi = array_filter($transaksi, function($t) use ($visiblePaketIds) {
                 return in_array($t['paket_id'], $visiblePaketIds);
             });
        }
        
        $data = [
            'title' => 'Riwayat Transaksi',
            'active' => 'transaksi',
            'transaksi' => $transaksi,
            'all_paket' => $allPaket,
            'filters' => $filters
        ];
        
        $this->view('transaksi/index', $data);
    }

    public function create($paket_id = null) {
        $this->requireAdmin();

        $paket = null;
        
        if ($paket_id) {
            $paket = $this->paketModel->getById($paket_id);
            if (!$paket) {
                flash('error', 'Paket tidak ditemukan');
                redirect('/transaksi');
                return;
            }
        }
        
        // Get all active packets for dropdown if no packet selected
        $allPaket = $this->paketModel->getAll();
        
        $data = [
            'title' => 'Tambah Transaksi',
            'active' => 'transaksi',
            'paket' => $paket,
            'all_paket' => $allPaket
        ];
        
        $this->view('transaksi/create', $data);
    }
    
    public function store() {
        $this->requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/transaksi');
            return;
        }
        
        // Verify CSRF
        if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            flash('error', 'Invalid CSRF token');
            redirect('/paket');
            return;
        }
        
        try {
            $jumlah = parseRupiah($_POST['jumlah']);
            
            $data = [
                'paket_id' => (int)$_POST['paket_id'],
                'tanggal' => $_POST['tanggal'],
                'tipe' => $_POST['tipe'],
                'kategori' => sanitize($_POST['kategori']),
                'jumlah' => $jumlah,
                'keterangan' => sanitize($_POST['keterangan'])
            ];
            
            $this->transaksiModel->create($data);
            
            flash('success', 'Transaksi berhasil ditambahkan!');
            redirect('/paket/detail/' . $data['paket_id']);
            
        } catch (Exception $e) {
            flash('error', 'Gagal menambahkan transaksi: ' . $e->getMessage());
            setOld($_POST);
            redirect('/transaksi/create/' . ($_POST['paket_id'] ?? ''));
        }
    }
    
    public function edit($id) {
        $this->requireAdmin();

        $transaksi = $this->transaksiModel->getById($id);
        
        if (!$transaksi) {
            flash('error', 'Transaksi tidak ditemukan');
            redirect('/paket');
            return;
        }
        
        $paket = $this->paketModel->getById($transaksi['paket_id']);
        
        $data = [
            'title' => 'Edit Transaksi',
            'active' => 'paket',
            'transaksi' => $transaksi,
            'paket' => $paket
        ];
        
        $this->view('transaksi/edit', $data);
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
            redirect('/transaksi/edit/' . $id);
            return;
        }
        
        try {
            $transaksi = $this->transaksiModel->getById($id);
            $jumlah = parseRupiah($_POST['jumlah']);
            
            $data = [
                'tanggal' => $_POST['tanggal'],
                'tipe' => $_POST['tipe'],
                'kategori' => sanitize($_POST['kategori']),
                'jumlah' => $jumlah,
                'keterangan' => sanitize($_POST['keterangan'])
            ];
            
            $this->transaksiModel->update($id, $data);
            
            flash('success', 'Transaksi berhasil diupdate!');
            redirect('/paket/detail/' . $transaksi['paket_id']);
            
        } catch (Exception $e) {
            flash('error', 'Gagal mengupdate transaksi: ' . $e->getMessage());
            setOld($_POST);
            redirect('/transaksi/edit/' . $id);
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
            $transaksi = $this->transaksiModel->getById($id);
            $paket_id = $transaksi['paket_id'];
            
            $this->transaksiModel->delete($id);
            
            flash('success', 'Transaksi berhasil dihapus!');
            redirect('/paket/detail/' . $paket_id);
            
        } catch (Exception $e) {
            flash('error', 'Gagal menghapus transaksi: ' . $e->getMessage());
            redirect('/paket');
        }
    }
    
    private function view($view, $data = []) {
        extract($data);
        require_once VIEW_PATH . '/layout/header.php';
        require_once VIEW_PATH . '/' . $view . '.php';
        require_once VIEW_PATH . '/layout/footer.php';
    }
}
