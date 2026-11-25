<?php
/**
 * ProLedger - Dashboard Controller
 * Mengelola tampilan statistik utama, analitik grafik, progres operasional,
 * group paket, dan kolaborasi untuk perusahaan aktif.
 */

require_once MODEL_PATH . '/PaketKegiatan.php';
require_once MODEL_PATH . '/PaketGroup.php';
require_once MODEL_PATH . '/Perusahaan.php';
require_once MODEL_PATH . '/Kolaborasi.php';

class DashboardController {
    private $paketModel;
    private $groupModel;
    private $perusahaanModel;
    private $kolabModel;

    public function __construct() {
        $this->paketModel      = new PaketKegiatan();
        $this->groupModel      = new PaketGroup();
        $this->perusahaanModel = new Perusahaan();
        $this->kolabModel      = new Kolaborasi();
    }

    public function index() {
        $role   = $_SESSION['admin_role'] ?? 'user';
        $userId = (int)($_SESSION['admin_id'] ?? 0);

        if ($role === 'admin') {
            header('Location: ' . APP_URL . '/admin');
            exit;
        }

        // 1. Daftar perusahaan milik user & perusahaan aktif
        $userCompanies   = $this->perusahaanModel->getByUser($userId);
        $activeCompany   = getActivePerusahaan();
        $activeCompanyId = $activeCompany ? (int)$activeCompany['id'] : null;

        // 2. Seluruh paket untuk perusahaan aktif + metrik akurat
        $allPaket = !empty($activeCompanyId)
            ? $this->paketModel->getAll(['perusahaan_id' => $activeCompanyId])
            : [];

        $stats = PaketGroup::computeStatsFromList($allPaket);
        $stats['total_perusahaan'] = count($userCompanies);

        // Siapkan data terhitung untuk recent paket & chart
        $enrichedPaket = [];
        foreach ($allPaket as $p) {
            $p['_m'] = PaketGroup::calculatePaketMetrics($p);
            $enrichedPaket[] = $p;
        }

        // Chart 1: Komposisi Finansial per Paket (maks 6 paket terbaru)
        $chartPakets = array_slice($enrichedPaket, 0, 6);
        $chartLabels = [];
        $chartKontrak = [];
        $chartBersih = [];
        $chartHpp = [];
        $chartProfit = [];
        foreach (array_reverse($chartPakets) as $cp) {
            $m = $cp['_m'];
            $shortName = mb_strlen($cp['nama_paket']) > 22
                ? mb_substr($cp['nama_paket'], 0, 20) . '…'
                : $cp['nama_paket'];
            $chartLabels[]  = ($cp['kode_paket'] ? $cp['kode_paket'] . ' • ' : '') . $shortName;
            $chartKontrak[] = round($m['acuan']);
            $chartBersih[]  = round($m['nilai_bersih']);
            $chartHpp[]     = round($m['hpp']);
            $chartProfit[]  = round($m['profit']);
        }

        // 3. Group Paket untuk perusahaan aktif
        $groups = !empty($activeCompanyId)
            ? $this->groupModel->getAll($activeCompanyId, $role === 'admin' ? null : (int)$userId)
            : $this->groupModel->getAll(null, $role === 'admin' ? null : (int)$userId);

        // 4. Kolaborasi milik/diikuti user
        $kolabList      = $this->kolabModel->getForUser($userId);
        $pendingInvites = array_values(array_filter($kolabList, fn($k) => ($k['my_status'] ?? '') === 'pending'));
        $joinedKolabs   = array_values(array_filter($kolabList, fn($k) => ($k['my_status'] ?? '') === 'joined'));

        $this->view('dashboard/index', [
            'title'           => 'Dashboard',
            'active'          => 'dashboard',
            'stats'           => $stats,
            'recent_paket'    => array_slice($enrichedPaket, 0, 7),
            'groups'          => array_slice($groups, 0, 5),
            'total_groups'    => count($groups),
            'joinedKolabs'    => array_slice($joinedKolabs, 0, 4),
            'total_kolab'     => count($joinedKolabs),
            'pendingInvites'  => $pendingInvites,
            'userCompanies'   => $userCompanies,
            'activeCompany'   => $activeCompany,
            'activeCompanyId' => $activeCompanyId,
            'role'            => $role,
            'chartData'       => [
                'labels'  => $chartLabels,
                'kontrak' => $chartKontrak,
                'bersih'  => $chartBersih,
                'hpp'     => $chartHpp,
                'profit'  => $chartProfit,
                'status'  => [
                    'cair'   => $stats['paket_selesai'],
                    'proses' => $stats['paket_proses'],
                    'belum'  => $stats['paket_belum'],
                ],
                'proporsi' => [
                    'pasal'  => round($stats['total_pasal']),
                    'hpp'    => round($stats['total_hpp']),
                    'profit' => max(0, round($stats['total_profit'])),
                ],
            ],
        ]);
    }

    private function view($view, $data = []) {
        extract($data);
        require_once VIEW_PATH . '/layout/header.php';
        require_once VIEW_PATH . '/' . $view . '.php';
        require_once VIEW_PATH . '/layout/footer.php';
    }
}
