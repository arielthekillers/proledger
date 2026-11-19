<?php
/**
 * ProLedger - Dashboard Controller
 * Mengelola tampilan statistik utama dan ringkasan paket untuk perusahaan aktif
 */

require_once MODEL_PATH . '/PaketKegiatan.php';
require_once MODEL_PATH . '/Perusahaan.php';

class DashboardController {
    private $paketModel;
    private $perusahaanModel;
    
    public function __construct() {
        $this->paketModel = new PaketKegiatan();
        $this->perusahaanModel = new Perusahaan();
    }
    
    public function index() {
        $role = $_SESSION['admin_role'] ?? 'user';
        $userId = $_SESSION['admin_id'] ?? null;
        
        // Admin hanya mengelola manajemen user, tidak mengelola proyek
        if ($role === 'admin') {
            header('Location: ' . APP_URL . '/user');
            exit;
        }

        // 1. Ambil daftar perusahaan milik user
        $userCompanies = $this->perusahaanModel->getByUser($userId);

        // 2. Pastikan selalu tepat 1 perusahaan aktif terpilih (Single Active Company)
        $activeCompany = getActivePerusahaan();
        $activeCompanyId = $activeCompany ? $activeCompany['id'] : null;

        // 3. Ambil paket kegiatan HANYA untuk 1 perusahaan aktif
        if (!empty($activeCompanyId)) {
            $allPaket = $this->paketModel->getAll(['perusahaan_id' => $activeCompanyId]);
        } else {
            $allPaket = [];
        }
        
        $paketAktif = array_filter($allPaket, function($p) {
            return ($p['status'] ?? '') !== 'Selesai';
        });
        
        // Hitung total nilai pagu dan total potongan pasal untuk perusahaan aktif
        $totalPagu = 0;
        $totalPasal = 0;
        
        foreach ($allPaket as $p) {
            $hasNego = (!empty($p['pagu_nego']) && (float)$p['pagu_nego'] > 0);
            $acuan = $hasNego ? (float)$p['pagu_nego'] : (float)$p['pagu'];
            $totalPagu += $acuan;
            $totalPasal += (!empty($p['total_pasal']) ? (float)$p['total_pasal'] : 0);
        }
        
        $recentPaket = array_slice($allPaket, 0, 8);
        
        $data = [
            'title' => 'Dashboard',
            'active' => 'dashboard',
            'stats' => [
                'total_paket' => count($allPaket),
                'paket_aktif' => count($paketAktif),
                'total_pagu' => $totalPagu,
                'total_pasal' => $totalPasal,
                'total_perusahaan' => count($userCompanies)
            ],
            'recent_paket' => $recentPaket,
            'userCompanies' => $userCompanies,
            'activeCompany' => $activeCompany,
            'activeCompanyId' => $activeCompanyId,
            'role' => $role
        ];
        
        $this->view('dashboard/index', $data);
    }
    
    private function view($view, $data = []) {
        extract($data);
        require_once VIEW_PATH . '/layout/header.php';
        require_once VIEW_PATH . '/' . $view . '.php';
        require_once VIEW_PATH . '/layout/footer.php';
    }
}
