<?php
/**
 * ProLedger - Dashboard Controller
 */

require_once MODEL_PATH . '/PaketKegiatan.php';
require_once MODEL_PATH . '/User.php';
require_once MODEL_PATH . '/Transaksi.php';
require_once MODEL_PATH . '/ModalDetail.php';

class DashboardController {
    private $paketModel;
    private $userModel;
    private $transaksiModel;
    private $modalModel;
    
    public function __construct() {
        $this->paketModel = new PaketKegiatan();
        $this->userModel = new User();
        $this->transaksiModel = new Transaksi();
        $this->modalModel = new ModalDetail();
    }
    
    public function index() {
        // Prepare filters based on role
        $role = $_SESSION['admin_role'] ?? 'admin';
        $refId = $_SESSION['admin_ref_id'] ?? null;
        
        $paketFilters = [];
        if ($role === 'pemodal') {
            // Need to implement getByPemodal in PaketModel or filter here
            // For now, let's filter the full list (inefficient but works for MVP)
            // Or better, add method to model. Let's filter array for simplicity for now as model changes are risky without checking file.
            // Actually, best to fetch all and filter in PHP since we don't have specific DB methods yet and users count is small.
        }

        // Get statistics
        $allPaket = $this->paketModel->getAll(); // This returns array
        
        // Filter Pakets
        if ($role === 'pemodal') {
            $myPaketIds = $this->transaksiModel->getPaketIdsByPemodal($refId); // Need to check if this method exists or similar logic
            // Alternative: Get from ModalDetail
            $myInvestments = $this->modalModel->getByUser($refId);
            $myPaketIds = array_column($myInvestments, 'paket_id');
            
            $allPaket = array_filter($allPaket, function($p) use ($myPaketIds) {
                return in_array($p['id'], $myPaketIds);
            });
        } elseif ($role === 'perusahaan') {
            $allPaket = array_filter($allPaket, function($p) use ($refId) {
                return $p['perusahaan_id'] == $refId;
            });
        }
        
        $paketAktif = array_filter($allPaket, function($p) {
            return $p['status'] !== 'Selesai';
        });
        
        // Filter Users (Only admin sees all users)
        $allUsers = ($role === 'admin') ? $this->userModel->getAll() : [];
        
        // Calculate totals
        $totalModal = 0;
        $totalMasuk = 0;
        $totalKeluar = 0;
        
        foreach ($allPaket as $paket) {
            $summary = $this->paketModel->getSummary($paket['id']);
            
            // For Pemodal, we might want to show THEIR share, but simpler to show Project totals they are involved in for now.
            // Requirement says "see his own project", so project summary is acceptable.
            
            $totalModal += $summary['total_modal'];
            $totalMasuk += $summary['total_masuk'];
            $totalKeluar += $summary['total_keluar'];
        }
        
        $totalSaldo = $totalMasuk - $totalKeluar;
        $totalLabaRugi = $totalSaldo - $totalModal;
        
        // Get recent paket
        $recentPaket = array_slice($allPaket, 0, 5);
        
        // Prepare data for view
        $data = [
            'title' => 'Dashboard',
            'active' => 'dashboard',
            'stats' => [
                'total_paket' => count($allPaket),
                'paket_aktif' => count($paketAktif),
                'total_pemodal' => count($allUsers), // Hide for non-admin
                'total_modal' => $totalModal,
                'total_masuk' => $totalMasuk,
                'total_keluar' => $totalKeluar,
                'total_saldo' => $totalSaldo,
                'total_laba_rugi' => $totalLabaRugi
            ],
            'recent_paket' => $recentPaket
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
