<?php
/**
 * ProLedger - Rekap Controller
 * Controller for comprehensive financial recaps
 */

require_once MODEL_PATH . '/PaketKegiatan.php';
require_once MODEL_PATH . '/User.php';
require_once MODEL_PATH . '/Transaksi.php';
require_once MODEL_PATH . '/ModalDetail.php';

class RekapController {
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
    
    /**
     * Overview of all packages with summary statistics
     */
    public function index() {
        $status = $_GET['status'] ?? null;
        $paket = $this->paketModel->getAll($status);
        
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
        
        // Calculate summary for each package
        $paketWithSummary = [];
        foreach ($paket as $p) {
            $summary = $this->paketModel->getSummary($p['id']);
            $paketWithSummary[] = array_merge($p, $summary);
        }
        
        // Calculate grand totals
        $grandTotal = [
            'total_modal' => 0,
            'total_masuk' => 0,
            'total_keluar' => 0,
            'total_saldo' => 0,
            'total_laba_rugi' => 0
        ];
        
        foreach ($paketWithSummary as $p) {
            $grandTotal['total_modal'] += $p['total_modal'];
            $grandTotal['total_masuk'] += $p['total_masuk'];
            $grandTotal['total_keluar'] += $p['total_keluar'];
            $grandTotal['total_saldo'] += $p['saldo'];
            $grandTotal['total_laba_rugi'] += $p['laba_rugi'];
        }
        
        $data = [
            'title' => 'Rekap Keuangan',
            'active' => 'rekap',
            'paket' => $paketWithSummary,
            'grand_total' => $grandTotal,
            'filter_status' => $status
        ];
        
        $this->view('rekap/index', $data);
    }
    
    /**
     * Detailed recap for a specific package
     */
    public function paket($id) {
        $paket = $this->paketModel->getById($id);
        
        if (!$paket) {
            flash('error', 'Paket tidak ditemukan');
            redirect('/rekap');
            return;
        }

        // Access Check
        $role = $_SESSION['admin_role'] ?? 'admin';
        $refId = $_SESSION['admin_ref_id'] ?? null;

        if ($role === 'perusahaan' && $paket['perusahaan_id'] != $refId) {
            flash('error', 'Akses ditolak.');
            redirect('/rekap');
            return;
        }

        if ($role === 'pemodal') {
            $myInvestments = $this->modalModel->getByUser($refId);
            $myPaketIds = array_column($myInvestments, 'paket_id');
            if (!in_array($id, $myPaketIds)) {
                flash('error', 'Akses ditolak.');
                redirect('/rekap');
                return;
            }
        }
        
        $summary = $this->paketModel->getSummary($id);
        $modalDetail = $this->modalModel->getByPaket($id);
        $transaksi = $this->transaksiModel->getByPaket($id);
        $transaksiByKategori = $this->transaksiModel->getByKategori($id);
        
        // Group transactions by month for chart
        $transaksiByMonth = $this->groupTransaksiByMonth($transaksi);
        
        $data = [
            'title' => 'Rekap: ' . $paket['nama_paket'],
            'active' => 'rekap',
            'paket' => $paket,
            'summary' => $summary,
            'modal_detail' => $modalDetail,
            'transaksi' => $transaksi,
            'transaksi_by_kategori' => $transaksiByKategori,
            'transaksi_by_month' => $transaksiByMonth
        ];
        
        $this->view('rekap/paket', $data);
    }
    
    /**
     * Detailed recap for a specific investor
     */
    public function pemodal($id) {
        $user = $this->userModel->getById($id);
        
        if (!$user) {
            flash('error', 'Pemodal tidak ditemukan');
            redirect('/rekap');
            return;
        }

        // Access Check
        $role = $_SESSION['admin_role'] ?? 'admin';
        $refId = $_SESSION['admin_ref_id'] ?? null;

        // Pemodal can only see themselves
        if ($role === 'pemodal' && $id != $refId) {
            flash('error', 'Akses ditolak. Anda hanya dapat melihat laporan Anda sendiri.');
            redirect('/rekap');
            return;
        }

        // Perusahaan shouldn't see investor detailed financial recaps (strictly personal)
        if ($role === 'perusahaan') {
            flash('error', 'Akses ditolak.');
            redirect('/rekap');
            return;
        }
        
        $paket = $this->userModel->getPaketKegiatan($id);
        $modalDetail = $this->modalModel->getByUser($id);
        $totalModal = $this->userModel->getTotalModal($id);
        
        // Calculate estimated profit/loss for each package
        $portfolio = [];
        foreach ($paket as $p) {
            $summary = $this->paketModel->getSummary($p['id']);
            $modal = $this->modalModel->getByPaketAndUser($p['id'], $id);
            
            // Calculate investor's share of profit/loss
            $investorShare = $modal['total_modal'] / $summary['total_modal'];
            $estimatedLabaRugi = $summary['laba_rugi'] * $investorShare;
            
            $portfolio[] = [
                'paket' => $p,
                'modal' => $modal,
                'summary' => $summary,
                'estimated_laba_rugi' => $estimatedLabaRugi
            ];
        }
        
        // Calculate total estimated profit/loss
        $totalEstimatedLabaRugi = array_sum(array_column($portfolio, 'estimated_laba_rugi'));
        
        $data = [
            'title' => 'Rekap: ' . $user['nama'],
            'active' => 'rekap',
            'user' => $user,
            'portfolio' => $portfolio,
            'total_modal' => $totalModal,
            'total_estimated_laba_rugi' => $totalEstimatedLabaRugi
        ];
        
        $this->view('rekap/pemodal', $data);
    }
    
    /**
     * Export data to CSV/Excel
     */
    public function export($type, $id = null) {
        if ($type === 'paket' && $id) {
            $this->exportPaketCSV($id);
        } elseif ($type === 'pemodal' && $id) {
            $this->exportPemodalCSV($id);
        } elseif ($type === 'all') {
            $this->exportAllCSV();
        } else {
            flash('error', 'Invalid export type');
            redirect('/rekap');
        }
    }
    
    /**
     * Export package recap to CSV
     */
    private function exportPaketCSV($id) {
        $paket = $this->paketModel->getById($id);
        $transaksi = $this->transaksiModel->getByPaket($id);
        
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="rekap_paket_' . $id . '_' . date('Ymd') . '.csv"');
        
        $output = fopen('php://output', 'w');
        
        // Header
        fputcsv($output, ['Rekap Paket: ' . $paket['nama_paket']]);
        fputcsv($output, ['Tanggal Export: ' . date('d/m/Y H:i')]);
        fputcsv($output, []);
        
        // Transactions
        fputcsv($output, ['Tanggal', 'Tipe', 'Kategori', 'Jumlah', 'Keterangan']);
        foreach ($transaksi as $t) {
            fputcsv($output, [
                $t['tanggal'],
                $t['tipe'],
                $t['kategori'],
                $t['jumlah'],
                $t['keterangan']
            ]);
        }
        
        fclose($output);
        exit;
    }
    
    /**
     * Export investor recap to CSV
     */
    private function exportPemodalCSV($id) {
        $user = $this->userModel->getById($id);
        $modalDetail = $this->modalModel->getByUser($id);
        
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="rekap_pemodal_' . $id . '_' . date('Ymd') . '.csv"');
        
        $output = fopen('php://output', 'w');
        
        // Header
        fputcsv($output, ['Rekap Pemodal: ' . $user['nama']]);
        fputcsv($output, ['Tanggal Export: ' . date('d/m/Y H:i')]);
        fputcsv($output, []);
        
        // Modal details
        fputcsv($output, ['Paket', 'Modal Pasal', 'Modal Belanja', 'Total Modal']);
        foreach ($modalDetail as $m) {
            fputcsv($output, [
                $m['nama_paket'],
                $m['modal_pasal'],
                $m['modal_belanja'],
                $m['total_modal']
            ]);
        }
        
        fclose($output);
        exit;
    }
    
    /**
     * Export all packages recap to Excel (HTML format)
     */
    private function exportAllCSV() {
        $paket = $this->paketModel->getAll();
        
        $filename = "rekap_semua_paket_" . date('Ymd') . ".xls";
        
        header("Content-Type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Pragma: no-cache");
        header("Expires: 0");
        
        echo '<!DOCTYPE html>';
        echo '<html>';
        echo '<head>';
        echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
        echo '<style>
            body { font-family: Arial, sans-serif; }
            table { border-collapse: collapse; width: 100%; }
            th { background-color: #4a5568; color: #ffffff; font-weight: bold; border: 1px solid #000000; padding: 10px; text-align: center; }
            td { border: 1px solid #000000; padding: 5px; vertical-align: middle; }
            .header-title { font-size: 18px; font-weight: bold; margin-bottom: 20px; }
            .text-center { text-align: center; }
            .text-right { text-align: right; }
            .bg-gray { background-color: #f7fafc; }
            .text-emerald { color: #059669; }
            .text-rose { color: #e11d48; }
        </style>';
        echo '</head>';
        echo '<body>';
        
        echo '<div class="header-title">Laporan Rekapitulasi Keuangan Proyek</div>';
        echo '<p>Tanggal Export: ' . date('d F Y H:i') . '</p>';
        
        echo '<table>';
        echo '<thead>';
        echo '<tr>
            <th width="50">No</th>
            <th width="200">Nama Paket Kegiatan</th>
            <th width="150">Nilai Pagu</th>
            <th width="150">Nilai RAB</th>
            <th width="100">Jml Pemodal</th>
            <th width="150">Total Modal</th>
            <th width="150">Total Masuk</th>
            <th width="150">Total Keluar</th>
            <th width="150">Saldo Akhir</th>
            <th width="150">Est. Laba/Rugi</th>
            <th width="100">Status</th>
        </tr>';
        echo '</thead>';
        echo '<tbody>';
        
        $no = 1;
        $grandTotal = [
            'pagu' => 0, 'rab' => 0, 'modal' => 0, 'masuk' => 0, 'keluar' => 0, 'saldo' => 0, 'laba' => 0
        ];
        
        foreach ($paket as $p) {
            $summary = $this->paketModel->getSummary($p['id']);
            
            // Accumulate totals
            $grandTotal['pagu'] += $p['pagu'];
            $grandTotal['rab'] += $p['rab'];
            $grandTotal['modal'] += $summary['total_modal'];
            $grandTotal['masuk'] += $summary['total_masuk'];
            $grandTotal['keluar'] += $summary['total_keluar'];
            $grandTotal['saldo'] += $summary['saldo'];
            $grandTotal['laba'] += $summary['laba_rugi'];
            
            $statusColor = ($p['status'] == 'Selesai') ? '#10b981' : '#3b82f6';
            $labaColor = ($summary['laba_rugi'] >= 0) ? '#059669' : '#e11d48';
            
            echo '<tr>';
            echo '<td class="text-center">' . $no++ . '</td>';
            echo '<td>' . htmlspecialchars($p['nama_paket']) . '</td>';
            echo '<td class="text-right">' . number_format($p['pagu'], 0, ',', '.') . '</td>';
            echo '<td class="text-right">' . number_format($p['rab'], 0, ',', '.') . '</td>';
            echo '<td class="text-center">' . $p['jumlah_pemodal'] . '</td>';
            echo '<td class="text-right">' . number_format($summary['total_modal'], 0, ',', '.') . '</td>';
            echo '<td class="text-right">' . number_format($summary['total_masuk'], 0, ',', '.') . '</td>';
            echo '<td class="text-right">' . number_format($summary['total_keluar'], 0, ',', '.') . '</td>';
            echo '<td class="text-right" style="font-weight:bold;">' . number_format($summary['saldo'], 0, ',', '.') . '</td>';
            echo '<td class="text-right" style="color:'.$labaColor.'; font-weight:bold;">' . number_format($summary['laba_rugi'], 0, ',', '.') . '</td>';
            echo '<td class="text-center" style="color:'.$statusColor.';">' . $p['status'] . '</td>';
            echo '</tr>';
        }
        
        // Grand Total Row
        echo '<tr style="background-color: #e2e8f0; font-weight: bold;">';
        echo '<td colspan="2" class="text-center">GRAND TOTAL</td>';
        echo '<td class="text-right">' . number_format($grandTotal['pagu'], 0, ',', '.') . '</td>';
        echo '<td class="text-right">' . number_format($grandTotal['rab'], 0, ',', '.') . '</td>';
        echo '<td></td>';
        echo '<td class="text-right">' . number_format($grandTotal['modal'], 0, ',', '.') . '</td>';
        echo '<td class="text-right">' . number_format($grandTotal['masuk'], 0, ',', '.') . '</td>';
        echo '<td class="text-right">' . number_format($grandTotal['keluar'], 0, ',', '.') . '</td>';
        echo '<td class="text-right">' . number_format($grandTotal['saldo'], 0, ',', '.') . '</td>';
        echo '<td class="text-right">' . number_format($grandTotal['laba'], 0, ',', '.') . '</td>';
        echo '<td></td>';
        echo '</tr>';
        
        echo '</tbody>';
        echo '</table>';
        echo '</body>';
        echo '</html>';
        exit;
    }
    
    /**
     * Group transactions by month for charting
     */
    private function groupTransaksiByMonth($transaksi) {
        $grouped = [];
        
        foreach ($transaksi as $t) {
            $month = date('Y-m', strtotime($t['tanggal']));
            
            if (!isset($grouped[$month])) {
                $grouped[$month] = [
                    'month' => $month,
                    'masuk' => 0,
                    'keluar' => 0
                ];
            }
            
            if ($t['tipe'] === 'masuk') {
                $grouped[$month]['masuk'] += $t['jumlah'];
            } else {
                $grouped[$month]['keluar'] += $t['jumlah'];
            }
        }
        
        ksort($grouped);
        return array_values($grouped);
    }
    
    private function view($view, $data = []) {
        extract($data);
        require_once VIEW_PATH . '/layout/header.php';
        require_once VIEW_PATH . '/' . $view . '.php';
        require_once VIEW_PATH . '/layout/footer.php';
    }
}
