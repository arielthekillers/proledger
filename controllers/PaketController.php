<?php
/**
 * ProLedger - Paket Controller
 */

require_once MODEL_PATH . '/PaketKegiatan.php';
require_once MODEL_PATH . '/PaketGroup.php';
require_once MODEL_PATH . '/Perusahaan.php';
require_once MODEL_PATH . '/SkemaPotongan.php';
require_once MODEL_PATH . '/PaketItem.php';
require_once MODEL_PATH . '/LokasiPenerima.php';
require_once MODEL_PATH . '/PaketDistribusi.php';
require_once MODEL_PATH . '/PaketDokumen.php';
require_once MODEL_PATH . '/PaketDokumentasi.php';
require_once HELPER_PATH . '/Uploader.php';

class PaketController {
    private $paketModel;
    private $groupModel;
    private $perusahaanModel;
    private $skemaModel;
    private $itemModel;
    private $lokasiModel;
    private $distribusiModel;
    private $dokumenModel;
    private $dokumentasiModel;
    
    public function __construct() {
        // Admin hanya mengelola manajemen user
        if (($_SESSION['admin_role'] ?? 'user') === 'admin') {
            header('Location: ' . APP_URL . '/user');
            exit;
        }

        $this->paketModel = new PaketKegiatan();
        $this->groupModel = new PaketGroup();
        $this->perusahaanModel = new Perusahaan();
        $this->skemaModel = new SkemaPotongan();
        $this->itemModel = new PaketItem();
        $this->lokasiModel = new LokasiPenerima();
        $this->distribusiModel = new PaketDistribusi();
        $this->dokumenModel = new PaketDokumen();
        $this->dokumentasiModel = new PaketDokumentasi();
    }
    
    // Helper to enforce user access
    private function requireUser() {
        $role = $_SESSION['admin_role'] ?? 'user';
        if ($role !== 'user' && $role !== 'perusahaan') {
            flash('error', 'Akses ditolak.');
            header('Location: ' . APP_URL . '/user');
            exit;
        }
    }

    /**
     * Memeriksa hak akses terhadap paket kegiatan (IDOR & Multi-tenancy Protection).
     * Memastikan paket hanya dapat diakses oleh user pemilik perusahaan paket tersebut.
     */
    private function getAccessiblePaket($idOrKode, $isAjax = false) {
        $this->requireUser();

        $paket = $this->paketModel->getById($idOrKode);
        if (!$paket) {
            if ($isAjax) {
                header('Content-Type: application/json');
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Paket kegiatan tidak ditemukan.']);
                exit;
            }
            flash('error', 'Paket kegiatan tidak ditemukan.');
            redirect('/paket');
            exit;
        }

        $role = $_SESSION['admin_role'] ?? 'user';
        $userId = $_SESSION['admin_id'] ?? 0;

        // Validasi kepemilikan paket: perusahaan paket harus dimiliki oleh user yang login (kecuali superadmin)
        if (!$this->perusahaanModel->isOwnerOrAdmin($paket['perusahaan_id'], $userId, $role)) {
            if ($isAjax) {
                header('Content-Type: application/json');
                http_response_code(403);
                echo json_encode(['success' => false, 'message' => 'Akses ditolak. Anda tidak memiliki izin mengakses paket ini.']);
                exit;
            }
            flash('error', 'Akses ditolak. Paket ini bukan milik perusahaan yang Anda kelola.');
            redirect('/paket');
            exit;
        }

        return $paket;
    }
    
    public function index() {
        $role = $_SESSION['admin_role'] ?? 'user';
        
        // Single Active Company
        $activeCompany = getActivePerusahaan();
        $activeCompId = $activeCompany ? $activeCompany['id'] : null;

        $filters = [
            'perusahaan_id' => $activeCompId,
            'status' => $_GET['status'] ?? null,
            'search' => $_GET['search'] ?? null,
            'group_id' => $_GET['group_id'] ?? null,
            'min_pagu' => $_GET['min_pagu'] ?? null,
            'max_pagu' => $_GET['max_pagu'] ?? null
        ];
        
        $paket = $activeCompId ? $this->paketModel->getAll($filters) : [];
        $groups = $this->groupModel->getAll($activeCompId);
        $skemas = $this->skemaModel->getAll($activeCompId);
        
        $allTags = !empty($activeCompany['id']) ? $this->paketModel->getAllTags($activeCompany['id']) : [];

        $data = [
            'title' => 'Paket Kegiatan',
            'active' => 'paket',
            'activeTab' => $_GET['tab'] ?? 'info',
            'paket' => array_values($paket),
            'groups' => $groups,
            'skemas' => $skemas,
            'allTags' => $allTags,
            'users' => [],
            'activeCompany' => $activeCompany,
            'has_perusahaan' => !empty($activeCompany),
            'filters' => $filters,
            'role' => $role
        ];
        
        $this->view('paket/index', $data);
    }
    
    public function create() {
        $this->requireUser();

        $activeCompany = getActivePerusahaan();
        if (!$activeCompany) {
            flash('error', 'Perusahaan masih kosong. Silakan buat perusahaan terlebih dahulu sebelum menambahkan paket.');
            redirect('/perusahaan/create');
            return;
        }
        
        $groups = $this->groupModel->getAll($activeCompany['id']);
        $skemas = $this->skemaModel->getAll($activeCompany['id']);
        $kabupatenList = $this->lokasiModel->getAllKabupaten();
        
        $data = [
            'title' => 'Tambah Paket Kegiatan',
            'active' => 'paket',
            'activeTab' => $_GET['tab'] ?? 'info',
            'users' => [],
            'activeCompany' => $activeCompany,
            'groups' => $groups,
            'skemas' => $skemas,
            'kabupatenList' => $kabupatenList
        ];
        
        $this->view('paket/create', $data);
    }
    
    public function store() {
        $this->requireUser();

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

        $activeCompany = getActivePerusahaan();
        if (!$activeCompany) {
            flash('error', 'Perusahaan masih kosong. Silakan buat perusahaan terlebih dahulu sebelum menambahkan paket.');
            redirect('/perusahaan/create');
            return;
        }
        
        try {
            // Parse Rupiah & Validasi
            if (empty($_POST['pagu'])) {
                throw new Exception("Nilai Kontrak / Pagu Awal wajib diisi.");
            }
            if (empty($_POST['pagu_nego'])) {
                throw new Exception("Pagu Negosiasi wajib diisi. Semua paket memerlukan nilai hasil negosiasi.");
            }
            $pagu = parseRupiah($_POST['pagu']);
            $paguNego = parseRupiah($_POST['pagu_nego']);
            $tahunAnggaran = !empty($_POST['tahun_anggaran']) ? (int)$_POST['tahun_anggaran'] : (int)date('Y');

            // Potongan standar bawaan saat buat paket baru
            $persenPasal = isset($_POST['persen_pasal']) && $_POST['persen_pasal'] !== '' ? (float)$_POST['persen_pasal'] : 13.0;
            $persenAdmin = isset($_POST['persen_admin']) && $_POST['persen_admin'] !== '' ? (float)$_POST['persen_admin'] : 2.0;
            $skemaId = !empty($_POST['skema_id']) ? (int)$_POST['skema_id'] : null;
            $tipePotongan = (!empty($_POST['tipe_potongan']) && $_POST['tipe_potongan'] === 'kontrak') ? 'kontrak' : 'dpp';

            $data = [
                'nama_paket' => sanitize($_POST['nama_paket']),
                'nomor_sp' => !empty($_POST['nomor_sp']) ? sanitize($_POST['nomor_sp']) : null,
                'kab_kota' => !empty($_POST['kab_kota']) ? sanitize($_POST['kab_kota']) : null,
                'perusahaan_id' => $activeCompany['id'],
                'group_id' => !empty($_POST['group_id']) ? (int)$_POST['group_id'] : null,
                'tahun_anggaran' => $tahunAnggaran,
                'tags' => !empty($_POST['tags']) ? sanitize($_POST['tags']) : null,
                'skema_id' => $skemaId,
                'perusahaan' => $activeCompany['nama'],
                'penanggung_jawab' => !empty($_POST['penanggung_jawab']) ? sanitize($_POST['penanggung_jawab']) : null,
                'progres_barang' => $_POST['progres_barang'] ?? 'Belum',
                'pagu' => $pagu,
                'pagu_nego' => $paguNego,
                'tipe_potongan' => $tipePotongan,
                'persen_pasal' => $persenPasal,
                'persen_admin' => $persenAdmin,
                'setor_awal' => null,
                'rab' => 0,
                'status' => $_POST['status'] ?? 'Proses'
            ];
            
            $created = $this->paketModel->create($data);
            $kode = is_array($created) ? $created['kode_paket'] : $created;
            
            flash('success', 'Paket kegiatan (' . $kode . ') berhasil ditambahkan!');
            redirect('/paket/detail/' . $kode);
            
        } catch (Exception $e) {
            flash('error', 'Gagal menambahkan paket: ' . $e->getMessage());
            setOld($_POST);
            redirect('/paket');
        }
    }
    
    public function detail($idOrKode) {
        $paket = $this->getAccessiblePaket($idOrKode);

        // Jika diakses menggunakan ID numerik, redirect ke kode 5-digit acak
        if (is_numeric($idOrKode) && !empty($paket['kode_paket'])) {
            $qs = !empty($_SERVER['QUERY_STRING']) ? ('?' . $_SERVER['QUERY_STRING']) : '';
            redirect('/paket/detail/' . $paket['kode_paket'] . $qs);
            return;
        }

        $data = $this->prepareDetailData($idOrKode);
        $this->view('paket/detail', $data);
    }

    /**
     * AJAX Native Modal View Endpoint
     * Mengembalikan potongan HTML murni untuk modul tertentu tanpa iframe
     */
    public function modalView($idOrKode, $module = 'info') {
        $data = $this->prepareDetailData($idOrKode);
        $data['activeTab'] = $module;
        extract($data);

        $validModules = [
            'info' => 'modal_info.php',
            'items' => 'modal_items.php',
            'pemesanan' => 'modal_pemesanan.php',
            'distribusi' => 'modal_distribusi.php',
            'dokumentasi' => 'modal_dokumentasi.php',
            'dokumen' => 'modal_dokumen.php',
            'profit' => 'modal_profit.php'
        ];

        $viewName = $validModules[$module] ?? 'modal_info.php';
        $file = VIEW_PATH . '/paket/modals/' . $viewName;
        if (file_exists($file)) {
            require $file;
        } else {
            require VIEW_PATH . '/paket/modals/modal_info.php';
        }
        exit;
    }

    private function prepareDetailData($idOrKode) {
        $paket = $this->getAccessiblePaket($idOrKode);

        $role = $_SESSION['admin_role'] ?? 'user';
        
        $summary = $this->paketModel->getSummary($paket['id']);
        $items = $this->itemModel->getByPaketId($paket['id']);
        $itemSummary = $this->itemModel->getSummaryByPaketId($paket['id']);
        
        // Pastikan pagu paket selalu sinkron dengan total nilai item di Tab 2
        if (!empty($itemSummary['grand_total']) && (float)$itemSummary['grand_total'] > 0 && abs((float)$paket['pagu'] - (float)$itemSummary['grand_total']) > 0.01) {
            $this->paketModel->syncPaguFromItems($paket['id']);
            $paket = $this->paketModel->getById($paket['id']);
        }
        $groups = $this->groupModel->getAll($paket['perusahaan_id'] ?? ($_SESSION['active_perusahaan_id'] ?? null));
        $skemas = $this->skemaModel->getAll($paket['perusahaan_id'] ?? ($_SESSION['active_perusahaan_id'] ?? null));
        
        // Distribusi Data
        $distribusiList = $this->distribusiModel->getByPaketId($paket['id']);
        $distribusiSummary = $this->distribusiModel->getSummaryByPaketId($paket['id']);
        $allocatedMap = $this->distribusiModel->getAllocatedPerItem($paket['id']);
        $paketKota = trim($paket['kab_kota'] ?? '');
        $lokasiList = !empty($paketKota) ? $this->lokasiModel->getAll(null, null, $paketKota) : $this->lokasiModel->getAll();
        $kabupatenList = $this->lokasiModel->getAllKabupaten();
        $dokumenList = $this->dokumenModel->getByPaketId($paket['id']);

        // Calculate item allocation metrics
        foreach ($items as &$it) {
            $allocated = (float)($allocatedMap[$it['id']] ?? 0);
            $it['allocated_qty'] = $allocated;
            $it['remaining_qty'] = max(0, (float)$it['jumlah'] - $allocated);
            $it['is_fully_allocated'] = ($allocated >= (float)$it['jumlah']);
            $it['percent_allocated'] = ((float)$it['jumlah'] > 0) ? min(100, round(($allocated / (float)$it['jumlah']) * 100)) : 100;

            // Kumpulkan daftar alokasi sebelumnya untuk item ini
            $it['existing_dists'] = [];
            $tujuanNames = [];
            foreach ($distribusiList as $dist) {
                if ((int)$dist['item_id'] === (int)$it['id']) {
                    $it['existing_dists'][] = [
                        'id' => (int)$dist['id'],
                        'nama_tujuan' => $dist['nama_tujuan'],
                        'jumlah' => (float)$dist['jumlah'],
                        'satuan' => $it['satuan'] ?: 'Unit',
                        'status' => $dist['status_distribusi'] ?: ($dist['status_barang'] ?? 'Belum Dipesan')
                    ];
                    if (!empty($dist['nama_tujuan'])) {
                        $tujuanNames[trim($dist['nama_tujuan'])] = true;
                    }
                }
            }
            $it['tujuan_count'] = count($tujuanNames) > 0 ? count($tujuanNames) : count($it['existing_dists']);
        }
        unset($it);
        
        $groupedDistribusi = $this->distribusiModel->getGroupedByTujuan($paket['id']);
        $dokumentasiMap = $this->dokumentasiModel->getGroupedByDistribusi($paket['id']);
        $totalDokumentasiFoto = $this->dokumentasiModel->countByPaketId($paket['id']);

        // Progress Metrics untuk Badge Tab (Format: Selesai / Total)
        // 1. Item: Item yang kuantitasnya sudah 100% dialokasikan
        $totalItems = count($items);
        $itemsDone = 0;
        foreach ($items as $it) {
            if (!empty($it['is_fully_allocated'])) {
                $itemsDone++;
            }
        }

        // 2. Pemesanan: Alokasi barang yang sudah dipesan di toko/supplier
        $pesanDone = 0;
        foreach ($distribusiList as $dist) {
            if (($dist['status_barang'] ?? '') === 'Sudah Dipesan') {
                $pesanDone++;
            }
        }

        // 3. Distribusi: Alokasi yang status distribusinya sudah tiba di tujuan
        $totalDist = count($distribusiList);
        $distDone = 0;
        foreach ($distribusiList as $dist) {
            if (($dist['status_distribusi'] ?? '') === 'Tiba di Tujuan') {
                $distDone++;
            }
        }

        // 4. Dokumentasi: Alokasi tujuan yang sudah memiliki minimal 1 foto dokumentasi
        $dokDone = 0;
        foreach ($distribusiList as $dist) {
            if (!empty($dokumentasiMap[$dist['id']])) {
                $dokDone++;
            }
        }

        // 5. BAST: Titik sekolah penerima yang berkas BAST-nya sudah diupload
        $totalBast = count($groupedDistribusi);
        $bastDone = 0;
        foreach ($groupedDistribusi as $tujuanKey => $grpVal) {
            foreach ($dokumenList as $doc) {
                if (stripos($doc['nama_dokumen'], $tujuanKey) !== false || stripos($doc['nama_file_asli'], $tujuanKey) !== false) {
                    $bastDone++;
                    break;
                }
            }
        }

        // Financial & Purchase Calculation
        $hasNego = (!empty($paket['pagu_nego']) && (float)$paket['pagu_nego'] > 0);
        $acuanPagu = $hasNego ? (float)$paket['pagu_nego'] : (float)$paket['pagu'];
        $dppNilai = !empty($paket['dpp']) ? (float)$paket['dpp'] : calculateDPP($acuanPagu);
        $persenPasal = isset($paket['persen_pasal']) && $paket['persen_pasal'] !== '' ? (float)$paket['persen_pasal'] : 13.0;
        $persenAdmin = isset($paket['persen_admin']) && $paket['persen_admin'] !== '' ? (float)$paket['persen_admin'] : 2.0;
        $basisPotongan = (($paket['tipe_potongan'] ?? 'dpp') === 'kontrak') ? $acuanPagu : $dppNilai;
        $pasalVal = !empty($paket['pasal_13']) ? (float)$paket['pasal_13'] : calculatePasal($basisPotongan, $persenPasal);
        $adminVal = isset($paket['admin_2']) ? (float)$paket['admin_2'] : calculateAdmin($basisPotongan, $persenAdmin);
        $totalPasalVal = !empty($paket['total_pasal']) ? (float)$paket['total_pasal'] : ($pasalVal + $adminVal);

        $totalBeliBarang = 0;
        $statusDistBreakdown = [
            'tiba_tujuan' => 0,
            'tiba_gudang' => 0,
            'tiba_kantor' => 0,
            'belum_dipesan' => 0,
            'dalam_pengiriman' => 0
        ];

        foreach ($distribusiList as $dist) {
            $isOrdered = (($dist['status_barang'] ?? '') === 'Sudah Dipesan');
            $sDist = $dist['status_distribusi'] ?? '';

            // Hitung total harga beli barang (HPP) jika harga satuan toko terisi
            if ((float)($dist['harga_satuan'] ?? 0) > 0) {
                $totalBeliBarang += ((float)$dist['harga_satuan'] * (float)$dist['jumlah']);
            }

            if (!$isOrdered) {
                $statusDistBreakdown['belum_dipesan']++;
            } elseif ($sDist === 'Tiba di Tujuan') {
                $statusDistBreakdown['tiba_tujuan']++;
            } elseif ($sDist === 'Tiba di Gudang') {
                $statusDistBreakdown['tiba_gudang']++;
            } elseif ($sDist === 'Tiba dirumah/kantor' || $sDist === 'Tiba di Rumah/Kantor') {
                $statusDistBreakdown['tiba_kantor']++;
            } else {
                $statusDistBreakdown['dalam_pengiriman']++;
            }
        }

        // Kalkulasi HPP & Estimasi Untung per Item Kontrak
        $itemsWithProfit = [];
        $totalHppAllItems = 0;
        $totalNilaiItemsAll = 0;
        $totalQtyAllItems = 0;

        foreach ($items as $it) {
            $itemId = $it['id'];
            $itQty = (float)$it['jumlah'];
            $itHargaSatuan = (float)$it['harga_satuan'];
            $itTotalKontrak = (float)($it['total_harga'] ?? round($itQty * $itHargaSatuan));
            
            // Hitung HPP item dari distribusi
            $itemHpp = 0;
            $distsForItem = [];
            foreach ($distribusiList as $dist) {
                if ($dist['item_id'] == $itemId) {
                    $distsForItem[] = $dist;
                    $dHppSatuan = (float)($dist['harga_satuan'] ?? 0);
                    $dQty = (float)$dist['jumlah'];
                    if ($dHppSatuan > 0) {
                        $itemHpp += ($dQty * $dHppSatuan);
                    }
                }
            }
            
            $itUntung = $itTotalKontrak - $itemHpp;
            $itMarginPct = ($itTotalKontrak > 0) ? round(($itUntung / $itTotalKontrak) * 100, 2) : 0;
            
            $it['hpp'] = $itemHpp;
            $it['total_kontrak'] = $itTotalKontrak;
            $it['estimasi_untung'] = $itUntung;
            $it['margin_pct'] = $itMarginPct;
            $it['dists'] = $distsForItem;
            
            $itemsWithProfit[] = $it;
            $totalHppAllItems += $itemHpp;
            $totalNilaiItemsAll += $itTotalKontrak;
            $totalQtyAllItems += $itQty;
        }

        $nilaiBersihProfit = $acuanPagu - $totalPasalVal - $totalBeliBarang;
        $profitMarginPct = ($acuanPagu > 0) ? round(($nilaiBersihProfit / $acuanPagu) * 100, 2) : 0;
        $roiPct = ($totalBeliBarang > 0) ? round(($nilaiBersihProfit / $totalBeliBarang) * 100, 2) : 0;

        $itemsPricedCount = 0;
        $itemsUnpricedCount = 0;
        foreach ($distribusiList as $dist) {
            if ((float)($dist['harga_satuan'] ?? 0) > 0) {
                $itemsPricedCount++;
            } else {
                $itemsUnpricedCount++;
            }
        }

        $tabProgress = [
            'items'       => ['done' => $itemsDone, 'total' => $totalItems],
            'pemesanan'   => ['done' => $pesanDone, 'total' => $totalDist],
            'distribusi'  => ['done' => $distDone, 'total' => $totalDist],
            'dokumentasi' => ['done' => $dokDone, 'total' => $totalDist],
            'dokumen'     => ['done' => $bastDone, 'total' => $totalBast],
            'profit'      => ['profit' => $nilaiBersihProfit, 'margin' => $profitMarginPct]
        ];
        
        return [
            'title' => $paket['nama_paket'],
            'active' => 'paket',
            'activeTab' => $_GET['tab'] ?? 'info',
            'paket' => $paket,
            'summary' => $summary,
            'items' => $items,
            'itemsWithProfit' => $itemsWithProfit,
            'totalHppAllItems' => $totalHppAllItems,
            'totalNilaiItemsAll' => $totalNilaiItemsAll,
            'totalQtyAllItems' => $totalQtyAllItems,
            'itemSummary' => $itemSummary,
            'distribusiList' => $distribusiList,
            'distribusiSummary' => $distribusiSummary,
            'groupedDistribusi' => $groupedDistribusi,
            'dokumentasiMap' => $dokumentasiMap,
            'totalDokumentasiFoto' => $totalDokumentasiFoto,
            'tabProgress' => $tabProgress,
            'statusDistBreakdown' => $statusDistBreakdown,
            'acuanPagu' => $acuanPagu,
            'totalPasalVal' => $totalPasalVal,
            'totalBeliBarang' => $totalBeliBarang,
            'estimasiProfit' => $nilaiBersihProfit,
            'nilaiBersihProfit' => $nilaiBersihProfit,
            'profitMarginPct' => $profitMarginPct,
            'roiPct' => $roiPct,
            'itemsPricedCount' => $itemsPricedCount,
            'itemsUnpricedCount' => $itemsUnpricedCount,
            'lokasiList' => $lokasiList,
            'kabupatenList' => $kabupatenList,
            'dokumenList' => $dokumenList,
            'groups' => $groups,
            'skemas' => $skemas,
            'modal_detail' => [],
            'transaksi' => []
        ];
    }
    
    public function edit($idOrKode) {
        $paket = $this->getAccessiblePaket($idOrKode);

        // Jika diakses menggunakan ID numerik, redirect ke kode 5-digit acak
        if (is_numeric($idOrKode) && !empty($paket['kode_paket'])) {
            redirect('/paket/edit/' . $paket['kode_paket']);
            return;
        }
        
        $groups = $this->groupModel->getAll($paket['perusahaan_id'] ?? ($_SESSION['active_perusahaan_id'] ?? null));
        $skemas = $this->skemaModel->getAll($paket['perusahaan_id'] ?? ($_SESSION['active_perusahaan_id'] ?? null));
        $kabupatenList = $this->lokasiModel->getAllKabupaten();
        
        $data = [
            'title' => 'Edit Paket Kegiatan',
            'active' => 'paket',
            'activeTab' => $_GET['tab'] ?? 'info',
            'paket' => $paket,
            'users' => [],
            'groups' => $groups,
            'skemas' => $skemas,
            'kabupatenList' => $kabupatenList
        ];
        
        $this->view('paket/edit', $data);
    }
    
    public function update($idOrKode) {
        $existing = $this->getAccessiblePaket($idOrKode);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/paket');
            return;
        }
        
        // Verify CSRF
        if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            flash('error', 'Invalid CSRF token');
            redirect('/paket/edit/' . $idOrKode);
            return;
        }
        
        try {

            // Parse Rupiah & Validasi
            if (empty($_POST['pagu'])) {
                throw new Exception("Nilai Kontrak / Pagu Awal wajib diisi.");
            }
            if (empty($_POST['pagu_nego'])) {
                throw new Exception("Pagu Negosiasi wajib diisi. Semua paket memerlukan nilai hasil negosiasi.");
            }
            $pagu = parseRupiah($_POST['pagu']);
            $paguNego = parseRupiah($_POST['pagu_nego']);
            $tahunAnggaran = !empty($_POST['tahun_anggaran']) ? (int)$_POST['tahun_anggaran'] : (!empty($existing['tahun_anggaran']) ? (int)$existing['tahun_anggaran'] : (int)date('Y'));

            // Potongan: pertahankan nilai paket yang sudah ada jika tidak dikirim dalam form edit dasar
            $persenPasal = isset($_POST['persen_pasal']) && $_POST['persen_pasal'] !== '' ? (float)$_POST['persen_pasal'] : (float)($existing['persen_pasal'] ?? 13.0);
            $persenAdmin = isset($_POST['persen_admin']) && $_POST['persen_admin'] !== '' ? (float)$_POST['persen_admin'] : (float)($existing['persen_admin'] ?? 2.0);
            $skemaId = isset($_POST['skema_id']) ? (!empty($_POST['skema_id']) ? (int)$_POST['skema_id'] : null) : ($existing['skema_id'] ?? null);
            $tipePotongan = isset($_POST['tipe_potongan']) ? (($_POST['tipe_potongan'] === 'kontrak') ? 'kontrak' : 'dpp') : ($existing['tipe_potongan'] ?? 'dpp');

            $perusahaanId = $existing['perusahaan_id'] ?? (!empty($_POST['perusahaan_id']) ? (int)$_POST['perusahaan_id'] : ($_SESSION['active_perusahaan_id'] ?? null));
            $perusahaanNama = $existing['perusahaan_nama'] ?? $existing['perusahaan'] ?? null;

            $data = [
                'nama_paket' => sanitize($_POST['nama_paket']),
                'nomor_sp' => !empty($_POST['nomor_sp']) ? sanitize($_POST['nomor_sp']) : ($existing['nomor_sp'] ?? null),
                'kab_kota' => !empty($_POST['kab_kota']) ? sanitize($_POST['kab_kota']) : null,
                'perusahaan_id' => $perusahaanId,
                'perusahaan' => $perusahaanNama,
                'group_id' => !empty($_POST['group_id']) ? (int)$_POST['group_id'] : null,
                'tahun_anggaran' => $tahunAnggaran,
                'tags' => !empty($_POST['tags']) ? sanitize($_POST['tags']) : null,
                'skema_id' => $skemaId,
                'penanggung_jawab' => !empty($_POST['penanggung_jawab']) ? sanitize($_POST['penanggung_jawab']) : ($existing['penanggung_jawab'] ?? null),
                'progres_barang' => $_POST['progres_barang'] ?? ($existing['progres_barang'] ?? 'Belum'),
                'pagu' => $pagu,
                'pagu_nego' => $paguNego,
                'tipe_potongan' => $tipePotongan,
                'persen_pasal' => $persenPasal,
                'persen_admin' => $persenAdmin,
                'setor_awal' => null,
                'rab' => 0,
                'status' => $_POST['status'] ?? ($existing['status'] ?? 'Proses')
            ];
            
            $this->paketModel->update($idOrKode, $data);
            $targetCode = $existing['kode_paket'] ?: $existing['id'];
            
            flash('success', 'Paket kegiatan (' . $targetCode . ') berhasil diupdate!');
            $redirectTo = !empty($_POST['redirect_to']) ? $_POST['redirect_to'] : ('/paket/detail/' . $targetCode);
            redirect($redirectTo);
            
        } catch (Exception $e) {
            flash('error', 'Gagal mengupdate paket: ' . $e->getMessage());
            setOld($_POST);
            $fallback = !empty($_POST['redirect_to']) ? $_POST['redirect_to'] : ('/paket/detail/' . $idOrKode);
            redirect($fallback);
        }
    }

    /**
     * Update khusus Skema Potongan Paket Kegiatan (Standalone Modal / Popup Mandiri)
     */
    public function updatePotongan($idOrKode) {
        $existing = $this->getAccessiblePaket($idOrKode);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/paket/detail/' . ($existing['kode_paket'] ?: $existing['id']));
            return;
        }

        if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            flash('error', 'Invalid CSRF token');
            redirect('/paket/detail/' . ($existing['kode_paket'] ?: $existing['id']));
            return;
        }

        try {
            $adaPasal = !empty($_POST['ada_pasal']);
            $persenPasal = ($adaPasal && isset($_POST['persen_pasal']) && $_POST['persen_pasal'] !== '') ? (float)$_POST['persen_pasal'] : 0.0;
            
            $adaAdmin = !empty($_POST['ada_admin']);
            $persenAdmin = ($adaAdmin && isset($_POST['persen_admin']) && $_POST['persen_admin'] !== '') ? (float)$_POST['persen_admin'] : 0.0;
            
            $skemaId = !empty($_POST['skema_id']) ? (int)$_POST['skema_id'] : null;
            $tipePotongan = (!empty($_POST['tipe_potongan']) && $_POST['tipe_potongan'] === 'kontrak') ? 'kontrak' : 'dpp';

            $this->paketModel->updatePotongan($existing['id'], [
                'skema_id'      => $skemaId,
                'tipe_potongan' => $tipePotongan,
                'persen_pasal'  => $persenPasal,
                'persen_admin'  => $persenAdmin
            ]);

            flash('success', 'Skema potongan paket (' . ($existing['kode_paket'] ?: $existing['id']) . ') berhasil diperbarui!');
            
            if (!empty($_POST['redirect_to']) && $_POST['redirect_to'] === 'index') {
                redirect('/paket');
                return;
            }

            $redirectTab = !empty($_POST['redirect_tab']) ? ('?tab=' . sanitize($_POST['redirect_tab'])) : '';
            redirect('/paket/detail/' . ($existing['kode_paket'] ?: $existing['id']) . $redirectTab);

        } catch (Exception $e) {
            flash('error', 'Gagal memperbarui skema potongan: ' . $e->getMessage());
            redirect('/paket/detail/' . ($existing['kode_paket'] ?: $existing['id']));
        }
    }
    
    public function delete($idOrKode) {
        $paket = $this->getAccessiblePaket($idOrKode);

        try {
            $this->paketModel->delete($paket['id']);
            flash('success', 'Paket kegiatan beserta item, alokasi distribusi, dan dokumennya berhasil dihapus!');
        } catch (Exception $e) {
            flash('error', 'Gagal menghapus paket: ' . $e->getMessage());
        }
        
        redirect('/paket');
    }

    /**
     * Update Status Pencairan Paket Kegiatan (Belum, Proses, Selesai) via Popup Modal
     */
    public function updateStatus($idOrKode) {
        $this->requireUser();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/paket');
            return;
        }

        $paket = $this->getAccessiblePaket($idOrKode, true);
        $status = trim($_POST['status'] ?? 'Proses');
        $validStatuses = ['Belum', 'Proses', 'Selesai'];
        $statusNorm = ucfirst(strtolower($status));
        if (!in_array($statusNorm, $validStatuses)) {
            $statusNorm = 'Proses';
        }

        try {
            $this->paketModel->updateStatus($paket['id'], $statusNorm);
            
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) || (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false)) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Status pencairan berhasil diperbarui menjadi ' . $statusNorm,
                    'status' => $statusNorm,
                    'badge' => getStatusBadge($statusNorm)
                ]);
                exit;
            }

            flash('success', 'Status pencairan berhasil diubah menjadi ' . $statusNorm . '!');
        } catch (Exception $e) {
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) || (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false)) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Gagal mengubah status pencairan: ' . $e->getMessage()
                ]);
                exit;
            }
            flash('error', 'Gagal mengubah status pencairan: ' . $e->getMessage());
        }

        $redirect = $_POST['redirect_to'] ?? 'index';
        if ($redirect === 'detail') {
            redirect('/paket/detail/' . ($paket['kode_paket'] ?: $paket['id']));
        } else {
            redirect('/paket');
        }
    }

    /**
     * Endpoint AJAX untuk menyimpan skema potongan baru langsung dari form paket
     */
    public function ajaxStoreSkema() {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Metode request tidak diizinkan.']);
            exit;
        }

        $nama = trim($_POST['nama_skema'] ?? '');
        $adaPasal = !empty($_POST['ada_pasal']) ? 1 : 0;
        $persenPasal = $adaPasal ? (float)($_POST['persen_pasal'] ?? 0) : 0;
        $adaAdmin = !empty($_POST['ada_admin']) ? 1 : 0;
        $persenAdmin = $adaAdmin ? (float)($_POST['persen_admin'] ?? 0) : 0;
        $tipePotongan = (!empty($_POST['tipe_potongan']) && $_POST['tipe_potongan'] === 'kontrak') ? 'kontrak' : 'dpp';
        $perusahaanId = !empty($_POST['perusahaan_id']) ? (int)$_POST['perusahaan_id'] : ($_SESSION['active_perusahaan_id'] ?? null);

        if (empty($nama)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Nama skema potongan wajib diisi.']);
            exit;
        }

        try {
            $newId = $this->skemaModel->create([
                'perusahaan_id' => $perusahaanId,
                'nama_skema' => $nama,
                'ada_pasal' => $adaPasal,
                'persen_pasal' => $persenPasal,
                'ada_admin' => $adaAdmin,
                'persen_admin' => $persenAdmin,
                'tipe_potongan' => $tipePotongan
            ]);

            echo json_encode([
                'success' => true,
                'message' => 'Skema potongan berhasil disimpan!',
                'skema' => [
                    'id' => $newId,
                    'nama_skema' => $nama,
                    'ada_pasal' => $adaPasal,
                    'persen_pasal' => $persenPasal,
                    'ada_admin' => $adaAdmin,
                    'persen_admin' => $persenAdmin,
                    'tipe_potongan' => $tipePotongan
                ]
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Gagal menyimpan skema: ' . $e->getMessage()]);
        }
        exit;
    }
    
    /**
     * Tambah item spesifikasi ke dalam paket
     */
    public function storeItem($idOrKode) {
        $paket = $this->getAccessiblePaket($idOrKode);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/paket/detail/' . ($paket['kode_paket'] ?: $paket['id']) . '?tab=items#items');
            return;
        }

        $komponen = trim($_POST['komponen'] ?? '');
        if (empty($komponen)) {
            flash('error', 'Nama komponen barang wajib diisi.');
            redirect('/paket/detail/' . ($paket['kode_paket'] ?: $paket['id']) . '?tab=items#items');
            return;
        }

        $jumlah = (float)($_POST['jumlah'] ?? 1);
        if ($jumlah <= 0) $jumlah = 1;

        $hargaSatuan = parseRupiah($_POST['harga_satuan'] ?? '0');
        $totalHarga = round($jumlah * $hargaSatuan);

        try {
            $this->itemModel->create([
                'paket_id' => $paket['id'],
                'komponen' => $komponen,
                'spesifikasi' => trim($_POST['spesifikasi'] ?? ''),
                'satuan' => trim($_POST['satuan'] ?? 'Unit'),
                'jumlah' => $jumlah,
                'harga_satuan' => $hargaSatuan,
                'total_harga' => $totalHarga
            ]);

            // Sinkronisasi pagu & potongan paket dengan total komponen item (Tab 2)
            $this->paketModel->syncPaguFromItems($paket['id']);

            flash('success', 'Item spesifikasi berhasil ditambahkan!');
        } catch (Exception $e) {
            flash('error', 'Gagal menambahkan item: ' . $e->getMessage());
        }

        redirect('/paket/detail/' . ($paket['kode_paket'] ?: $paket['id']) . '?tab=items#items');
    }

    /**
     * Update item spesifikasi dalam paket
     */
    public function updateItem($idOrKode, $itemId) {
        $paket = $this->getAccessiblePaket($idOrKode);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/paket/detail/' . ($paket['kode_paket'] ?: $paket['id']) . '?tab=items#items');
            return;
        }

        $item = $this->itemModel->getById($itemId);
        if (!$item || $item['paket_id'] != $paket['id']) {
            flash('error', 'Item tidak ditemukan.');
            redirect('/paket/detail/' . ($paket['kode_paket'] ?: $paket['id']) . '?tab=items#items');
            return;
        }

        $komponen = trim($_POST['komponen'] ?? '');
        if (empty($komponen)) {
            flash('error', 'Nama komponen barang wajib diisi.');
            redirect('/paket/detail/' . ($paket['kode_paket'] ?: $paket['id']) . '?tab=items#items');
            return;
        }

        $jumlah = (float)($_POST['jumlah'] ?? 1);
        if ($jumlah <= 0) $jumlah = 1;

        $hargaSatuan = parseRupiah($_POST['harga_satuan'] ?? '0');
        $totalHarga = round($jumlah * $hargaSatuan);

        try {
            $this->itemModel->update($itemId, [
                'komponen' => $komponen,
                'spesifikasi' => trim($_POST['spesifikasi'] ?? ''),
                'satuan' => trim($_POST['satuan'] ?? 'Unit'),
                'jumlah' => $jumlah,
                'harga_satuan' => $hargaSatuan,
                'total_harga' => $totalHarga
            ]);

            // Sinkronisasi pagu & potongan paket dengan total komponen item (Tab 2)
            $this->paketModel->syncPaguFromItems($paket['id']);

            flash('success', 'Item spesifikasi berhasil diperbarui!');
        } catch (Exception $e) {
            flash('error', 'Gagal memperbarui item: ' . $e->getMessage());
        }

        redirect('/paket/detail/' . ($paket['kode_paket'] ?: $paket['id']) . '?tab=items#items');
    }

    /**
     * Hapus item spesifikasi dalam paket
     */
    public function deleteItem($idOrKode, $itemId) {
        $paket = $this->getAccessiblePaket($idOrKode);

        $isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') || 
                  (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false) ||
                  (isset($_GET['ajax']) && $_GET['ajax'] == '1');

        try {
            $this->itemModel->delete($itemId);

            // Sinkronisasi pagu & potongan paket dengan total komponen item (Tab 2)
            $this->paketModel->syncPaguFromItems($paket['id']);

            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => true, 'message' => 'Item spesifikasi berhasil dihapus.']);
                exit;
            }
            flash('success', 'Item spesifikasi berhasil dihapus.');
        } catch (Exception $e) {
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Gagal menghapus item: ' . $e->getMessage()]);
                exit;
            }
            flash('error', 'Gagal menghapus item: ' . $e->getMessage());
        }

        $fallback = !empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : ('/paket?open_modal=' . ($paket['kode_paket'] ?: $paket['id']) . '&tab=items');
        redirect($fallback);
    }

    /**
     * Tambah alokasi distribusi paket
     */
    public function storeDistribusi($idOrKode) {
        $paket = $this->getAccessiblePaket($idOrKode);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/paket/detail/' . ($paket['kode_paket'] ?: $paket['id']) . '?tab=distribusi#distribusi');
            return;
        }

        $redirectTab = trim($_POST['redirect_tab'] ?? 'distribusi');
        if (!in_array($redirectTab, ['distribusi', 'items'])) {
            $redirectTab = 'distribusi';
        }

        $itemId = (int)($_POST['item_id'] ?? 0);
        $jumlah = (float)($_POST['jumlah'] ?? 0);
        $namaTujuan = trim($_POST['nama_tujuan'] ?? '');

        $lokasiId = null;
        $lokasi = null;
        if (!empty($_POST['lokasi_id'])) {
            $lokasiId = (int)$_POST['lokasi_id'];
            $lokasi = $this->lokasiModel->getById($lokasiId);
        } elseif (!empty($namaTujuan)) {
            $lokasi = $this->lokasiModel->getByName($namaTujuan);
            if ($lokasi) {
                $lokasiId = (int)$lokasi['id'];
            }
        }

        if ($lokasi) {
            $namaTujuan = $lokasi['nama_lokasi'];
            $kategori = trim($_POST['kategori'] ?? ($lokasi['kategori'] ?: 'Sekolah'));
            $kabKota = trim($_POST['kab_kota'] ?? ($lokasi['kab_kota'] ?: ''));
            $namaPic = trim($_POST['nama_pic'] ?? ($lokasi['nama_pic'] ?: ''));
            $noKontak = trim($_POST['no_kontak'] ?? ($lokasi['no_kontak'] ?: ''));
            $alamat = trim($_POST['alamat'] ?? ($lokasi['alamat_lengkap'] ?: ''));
        } else {
            $kategori = trim($_POST['kategori'] ?? 'Sekolah');
            $kabKota = trim($_POST['kab_kota'] ?? '');
            $namaPic = trim($_POST['nama_pic'] ?? '');
            $noKontak = trim($_POST['no_kontak'] ?? '');
            $alamat = trim($_POST['alamat'] ?? '');
        }

        if (!$itemId || empty($namaTujuan)) {
            flash('error', 'Item komponen dan pilihan tujuan penerima wajib ditentukan.');
            redirect('/paket/detail/' . ($paket['kode_paket'] ?: $paket['id']) . '?tab=' . $redirectTab . '#' . $redirectTab);
            return;
        }

        $item = $this->itemModel->getById($itemId);
        if (!$item || $item['paket_id'] != $paket['id']) {
            flash('error', 'Item komponen tidak ditemukan dalam paket ini.');
            redirect('/paket/detail/' . ($paket['kode_paket'] ?: $paket['id']) . '?tab=' . $redirectTab . '#' . $redirectTab);
            return;
        }

        // Kalkulasi kuota distribusi untuk item ini
        $allocatedMap = $this->distribusiModel->getAllocatedPerItem($paket['id']);
        $alreadyAllocated = (float)($allocatedMap[$itemId] ?? 0);
        $totalItemQty = (float)$item['jumlah'];
        $remainingQuota = max(0, $totalItemQty - $alreadyAllocated);

        if ($remainingQuota <= 0) {
            flash('error', 'Gagal: Seluruh kuota untuk item "' . $item['komponen'] . '" (' . $totalItemQty . ' ' . $item['satuan'] . ') sudah terdistribusi penuh. Tidak dapat menambah alokasi.');
            redirect('/paket/detail/' . ($paket['kode_paket'] ?: $paket['id']) . '?tab=' . $redirectTab . '#' . $redirectTab);
            return;
        }

        if ($jumlah <= 0) {
            flash('error', 'Jumlah alokasi harus lebih besar dari 0.');
            redirect('/paket/detail/' . ($paket['kode_paket'] ?: $paket['id']) . '?tab=' . $redirectTab . '#' . $redirectTab);
            return;
        }

        if ($jumlah > $remainingQuota) {
            flash('error', 'Gagal: Jumlah alokasi (' . $jumlah . ' ' . $item['satuan'] . ') melebihi sisa kuota yang tersedia (' . $remainingQuota . ' ' . $item['satuan'] . ').');
            redirect('/paket/detail/' . ($paket['kode_paket'] ?: $paket['id']) . '?tab=' . $redirectTab . '#' . $redirectTab);
            return;
        }

        try {
            $this->distribusiModel->create([
                'paket_id' => $paket['id'],
                'item_id' => $itemId,
                'lokasi_id' => $lokasiId,
                'nama_tujuan' => $namaTujuan,
                'kategori' => $kategori,
                'jumlah' => $jumlah,
                'status_barang' => 'Belum Dipesan',
                'nama_barang' => null,
                'harga_satuan' => null,
                'ekspedisi' => null,
                'status_distribusi' => null,
                'tanggal_pesan' => null,
                'link_pembelian' => null,
                'nama_pic' => $namaPic,
                'no_kontak' => $noKontak,
                'alamat' => $alamat,
                'kab_kota' => $kabKota,
                'tanggal_terima' => null,
                'no_resi' => null,
                'catatan' => trim($_POST['catatan'] ?? '')
            ]);

            flash('success', 'Alokasi distribusi ' . htmlspecialchars($item['komponen']) . ' ke ' . htmlspecialchars($namaTujuan) . ' (' . $jumlah . ' ' . htmlspecialchars($item['satuan']) . ') berhasil ditambahkan!');
        } catch (Exception $e) {
            flash('error', 'Gagal menambahkan alokasi pengiriman: ' . $e->getMessage());
        }

        redirect('/paket/detail/' . ($paket['kode_paket'] ?: $paket['id']) . '?tab=' . $redirectTab . '#' . $redirectTab);
    }

    /**
     * Update alokasi distribusi paket
     */
    public function updateDistribusi($idOrKode, $distribusiId) {
        $this->requireUser();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/paket/detail/' . $idOrKode . '?tab=distribusi#distribusi');
            return;
        }

        $paket = $this->paketModel->getById($idOrKode);
        if (!$paket) {
            flash('error', 'Paket tidak ditemukan');
            redirect('/paket');
            return;
        }

        $dist = $this->distribusiModel->getById($distribusiId);
        if (!$dist || $dist['paket_id'] != $paket['id']) {
            flash('error', 'Data alokasi tidak ditemukan.');
            redirect('/paket/detail/' . ($paket['kode_paket'] ?: $paket['id']) . '?tab=distribusi#distribusi');
            return;
        }

        $itemId = (int)($_POST['item_id'] ?? $dist['item_id']);
        $namaTujuan = trim($_POST['nama_tujuan'] ?? '');
        $jumlah = (float)($_POST['jumlah'] ?? 1);

        if (!$itemId || empty($namaTujuan)) {
            flash('error', 'Item komponen dan nama tujuan penerima wajib diisi.');
            redirect('/paket/detail/' . ($paket['kode_paket'] ?: $paket['id']) . '?tab=distribusi#distribusi');
            return;
        }

        if ($jumlah <= 0) $jumlah = 1;

        $item = $this->itemModel->getById($itemId);
        if ($item) {
            $allocatedMap = $this->distribusiModel->getAllocatedPerItem($paket['id']);
            $alreadyAllocatedOther = (float)($allocatedMap[$itemId] ?? 0) - ((int)$dist['item_id'] === $itemId ? (float)$dist['jumlah'] : 0);
            $maxAllowed = max(0, (float)$item['jumlah'] - $alreadyAllocatedOther);
            if ($jumlah > $maxAllowed) {
                flash('error', 'Gagal: Jumlah alokasi (' . $jumlah . ' ' . $item['satuan'] . ') melebihi batas sisa kuota yang tersedia (' . $maxAllowed . ' ' . $item['satuan'] . ').');
                redirect('/paket/detail/' . ($paket['kode_paket'] ?: $paket['id']) . '?tab=distribusi#distribusi');
                return;
            }
        }

        $lokasiId = !empty($_POST['lokasi_id']) ? (int)$_POST['lokasi_id'] : $dist['lokasi_id'];

        try {
            $this->distribusiModel->update($distribusiId, [
                'item_id' => $itemId,
                'lokasi_id' => $lokasiId,
                'nama_tujuan' => $namaTujuan,
                'kategori' => trim($_POST['kategori'] ?? ($dist['kategori'] ?: 'Sekolah')),
                'jumlah' => $jumlah,
                'status_barang' => $dist['status_barang'] ?? 'Belum Dipesan',
                'nama_barang' => $dist['nama_barang'] ?? null,
                'harga_satuan' => $dist['harga_satuan'] ?? null,
                'ekspedisi' => $dist['ekspedisi'] ?? null,
                'status_distribusi' => $dist['status_distribusi'] ?? null,
                'tanggal_pesan' => $dist['tanggal_pesan'] ?? null,
                'link_pembelian' => $dist['link_pembelian'] ?? null,
                'nama_pic' => trim($_POST['nama_pic'] ?? ''),
                'no_kontak' => trim($_POST['no_kontak'] ?? ''),
                'alamat' => trim($_POST['alamat'] ?? ''),
                'kab_kota' => trim($_POST['kab_kota'] ?? ''),
                'tanggal_terima' => $dist['tanggal_terima'] ?? null,
                'no_resi' => $dist['no_resi'] ?? null,
                'catatan' => trim($_POST['catatan'] ?? '')
            ]);

            flash('success', 'Alokasi pengiriman berhasil diperbarui!');
        } catch (Exception $e) {
            flash('error', 'Gagal memperbarui alokasi pengiriman: ' . $e->getMessage());
        }

        redirect('/paket/detail/' . ($paket['kode_paket'] ?: $paket['id']) . '?tab=distribusi#distribusi');
    }

    /**
     * AJAX/POST: Update Status Barang (Belum Dipesan vs Sudah Dipesan beserta detail pembelian)
     */
    public function updateStatusBarang($idOrKode, $distribusiId) {
        $paket = $this->getAccessiblePaket($idOrKode, true);

        $statusBarang = trim($_POST['status_barang'] ?? 'Belum Dipesan');
        $data = [
            'status_barang' => $statusBarang,
            'nama_barang' => trim($_POST['nama_barang'] ?? ''),
            'harga_satuan' => isset($_POST['harga_satuan']) && $_POST['harga_satuan'] !== '' ? parseRupiah($_POST['harga_satuan']) : null,
            'ekspedisi' => trim($_POST['ekspedisi'] ?? ''),
            'no_resi' => trim($_POST['no_resi'] ?? ''),
            'link_pembelian' => trim($_POST['link_pembelian'] ?? '')
        ];

        try {
            $this->distribusiModel->updateStatusBarang($distribusiId, $data);
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) || (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false)) {
                echo json_encode(['success' => true, 'message' => 'Status barang berhasil diperbarui!']);
                exit;
            }
            flash('success', 'Status barang berhasil diperbarui!');
        } catch (Exception $e) {
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) || (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false)) {
                echo json_encode(['success' => false, 'message' => 'Gagal memperbarui status barang: ' . $e->getMessage()]);
                exit;
            }
            flash('error', 'Gagal memperbarui status barang: ' . $e->getMessage());
        }

        redirect('/paket/detail/' . ($paket['kode_paket'] ?: $paket['id']) . '?tab=pemesanan#pemesanan');
    }

    /**
     * AJAX/POST: Update Status Distribusi (Tiba dirumah/kantor, Tiba di Gudang, Tiba di Tujuan)
     */
    public function updateStatusDistribusi($idOrKode, $distribusiId) {
        $paket = $this->getAccessiblePaket($idOrKode, true);
        $dist = $this->distribusiModel->getById($distribusiId);

        if (!$dist) {
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) || (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false)) {
                echo json_encode(['success' => false, 'message' => 'Data alokasi distribusi tidak ditemukan.']);
                exit;
            }
            flash('error', 'Data alokasi distribusi tidak ditemukan.');
            redirect('/paket/detail/' . ($paket['kode_paket'] ?: $paket['id']) . '?tab=distribusi#distribusi');
        }

        // Validasi: Status Barang harus 'Sudah Dipesan'
        if (($dist['status_barang'] ?? '') !== 'Sudah Dipesan') {
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) || (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false)) {
                echo json_encode(['success' => false, 'message' => 'Barang belum dipesan! Status distribusi hanya dapat diubah jika status barang sudah "Sudah Dipesan".']);
                exit;
            }
            flash('error', 'Barang belum dipesan! Status distribusi hanya dapat diubah jika status barang sudah "Sudah Dipesan".');
            redirect('/paket/detail/' . ($paket['kode_paket'] ?: $paket['id']) . '?tab=distribusi#distribusi');
        }

        $statusDistribusi = trim($_POST['status_distribusi'] ?? 'Tiba dirumah/kantor');

        try {
            $this->distribusiModel->updateStatusDistribusi($distribusiId, $statusDistribusi);
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) || (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false)) {
                echo json_encode(['success' => true, 'message' => 'Status distribusi berhasil diperbarui!']);
                exit;
            }
            flash('success', 'Status distribusi berhasil diperbarui!');
        } catch (Exception $e) {
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) || (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false)) {
                echo json_encode(['success' => false, 'message' => 'Gagal memperbarui status distribusi: ' . $e->getMessage()]);
                exit;
            }
            flash('error', 'Gagal memperbarui status distribusi: ' . $e->getMessage());
        }

        redirect('/paket/detail/' . ($paket['kode_paket'] ?: $paket['id']) . '?tab=distribusi#distribusi');
    }

    /**
     * Cetak Lembar BAST per Sekolah / Lokasi Tujuan
     */
    public function cetakBast($idOrKode) {
        $paket = $this->getAccessiblePaket($idOrKode);
        $perusahaan = $this->perusahaanModel->getById($paket['perusahaan_id']);

        $namaTujuan = trim($_GET['tujuan'] ?? '');
        if (empty($namaTujuan)) {
            flash('error', 'Silakan pilih lokasi tujuan penerima untuk mencetak BAST.');
            redirect('/paket/detail/' . ($paket['kode_paket'] ?: $paket['id']) . '?tab=dokumen#dokumen');
            return;
        }

        $items = $this->distribusiModel->getItemsByTujuan($paket['id'], $namaTujuan);
        if (empty($items)) {
            flash('error', 'Tidak ada data barang yang dialokasikan untuk tujuan: ' . htmlspecialchars($namaTujuan));
            redirect('/paket/detail/' . ($paket['kode_paket'] ?: $paket['id']) . '?tab=dokumen#dokumen');
            return;
        }

        // Validasi: seluruh barang untuk tujuan ini harus sudah Tiba di Gudang atau Tiba di Tujuan
        $notReady = false;
        foreach ($items as $it) {
            $sDist = trim($it['status_distribusi'] ?? '');
            $sBarang = trim($it['status_barang'] ?? '');
            $arrived = ($sBarang === 'Sudah Dipesan') && in_array($sDist, ['Tiba di Tujuan', 'Tiba di Gudang', 'Tiba di Gudang Transit', 'gudang']);
            if (!$arrived) {
                $notReady = true;
                break;
            }
        }

        if ($notReady) {
            flash('error', 'BAST belum dapat dicetak/didownload karena belum semua barang tiba di gudang atau tujuan.');
            redirect('/paket/detail/' . ($paket['kode_paket'] ?: $paket['id']) . '?tab=dokumen#dokumen');
            return;
        }

        $first = $items[0];
        $tujuan = [
            'nama_tujuan' => $first['nama_tujuan'],
            'kategori'    => $first['kategori'],
            'nama_pic'    => $first['nama_pic'] ?: ($first['master_pic'] ?? ''),
            'jabatan_pic' => $first['master_jabatan'] ?? 'Kepala Sekolah',
            'no_kontak'   => $first['no_kontak'] ?: ($first['master_kontak'] ?? ''),
            'alamat'      => $first['alamat'] ?: ($first['master_alamat'] ?? ''),
            'kab_kota'    => $first['kab_kota'] ?? ''
        ];

        $bastDate = !empty($_GET['tgl']) ? $_GET['tgl'] : ($first['tanggal_terima'] ?: date('Y-m-d'));

        // Load standalone print view
        require_once VIEW_PATH . '/paket/cetak_bast.php';
        exit;
    }

    /**
     * Hapus alokasi distribusi paket
     */
    public function deleteDistribusi($idOrKode, $distribusiId) {
        $paket = $this->getAccessiblePaket($idOrKode);

        $isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') || 
                  (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false) ||
                  (isset($_GET['ajax']) && $_GET['ajax'] == '1');

        try {
            $this->dokumentasiModel->deleteByDistribusiId($distribusiId);
            $this->distribusiModel->delete($distribusiId);
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => true, 'message' => 'Alokasi distribusi berhasil dihapus.']);
                exit;
            }
            flash('success', 'Alokasi distribusi berhasil dihapus.');
        } catch (Exception $e) {
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Gagal menghapus alokasi distribusi: ' . $e->getMessage()]);
                exit;
            }
            flash('error', 'Gagal menghapus alokasi distribusi: ' . $e->getMessage());
        }

        $fallback = !empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : ('/paket?open_modal=' . ($paket['kode_paket'] ?: $paket['id']) . '&tab=distribusi');
        redirect($fallback);
    }

    /**
     * Export Rekap Alokasi Distribusi ke file CSV (Excel-ready dengan UTF-8 BOM)
     */
    public function exportDistribusi($idOrKode) {
        $paket = $this->getAccessiblePaket($idOrKode);
        $distribusiList = $this->distribusiModel->getByPaketId($paket['id']);

        $cleanKode = preg_replace('/[^a-zA-Z0-9_-]/', '_', $paket['kode_paket'] ?: $paket['id']);
        $filename = 'Rekap_Distribusi_' . $cleanKode . '_' . date('Ymd_His') . '.csv';

        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        $output = fopen('php://output', 'w');

        // Output UTF-8 Byte Order Mark (BOM) agar Excel membaca karakter Indonesia dengan sempurna
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

        // Header Metadata Paket
        fputcsv($output, ['REKAP ALOKASI & MONITORING DISTRIBUSI LOGISTIK']);
        fputcsv($output, ['Nama Paket Kegiatan', $paket['nama_paket']]);
        fputcsv($output, ['Kode Paket', $paket['kode_paket'] ?: $paket['id']]);
        fputcsv($output, ['Perusahaan Rekanan', $paket['perusahaan'] ?: '-']);
        fputcsv($output, ['Nomor Surat Pesanan (SPK)', $paket['nomor_sp'] ?: '-']);
        fputcsv($output, ['Kota / Kabupaten', $paket['kab_kota'] ?: '-']);
        fputcsv($output, ['Waktu Download', date('d/m/Y H:i:s')]);
        fputcsv($output, []); // Baris kosong

        // Kolom Rincian
        fputcsv($output, [
            'NO',
            'TUJUAN PENERIMA',
            'KABUPATEN / KOTA',
            'KOMPONEN BARANG',
            'JUMLAH (QTY)',
            'SATUAN',
            'STATUS BARANG',
            'NAMA BARANG PESANAN',
            'HARGA SATUAN (RP)',
            'EKSPEDISI',
            'NO. RESI / SURAT JALAN',
            'LINK PEMBELIAN',
            'STATUS DISTRIBUSI'
        ]);

        $no = 1;
        $totalQty = 0;
        foreach ($distribusiList as $d) {
            $qty = (float)$d['jumlah'];
            $totalQty += $qty;
            $isOrdered = (($d['status_barang'] ?? '') === 'Sudah Dipesan');

            fputcsv($output, [
                $no++,
                $d['nama_tujuan'],
                $d['kab_kota'] ?: '-',
                $d['nama_komponen'] ?: '-',
                $qty,
                $d['satuan'] ?: 'Unit',
                $isOrdered ? 'Sudah Dipesan' : 'Belum Dipesan',
                $isOrdered ? ($d['nama_barang'] ?: ($d['nama_komponen'] ?: '-')) : '-',
                ($isOrdered && !empty($d['harga_satuan'])) ? number_format((float)$d['harga_satuan'], 0, ',', '.') : '-',
                $isOrdered ? ($d['ekspedisi'] ?: ($d['kurir_ekspedisi'] ?: '-')) : '-',
                $isOrdered ? ($d['no_resi'] ?: '-') : '-',
                $isOrdered ? ($d['link_pembelian'] ?: '-') : '-',
                $isOrdered ? ($d['status_distribusi'] ?: 'Dalam Pengiriman') : 'Belum Dipesan'
            ]);
        }

        fputcsv($output, []);
        fputcsv($output, ['', 'TOTAL BARANG DIALOKASIKAN', '', '', $totalQty, '', '', '', '', '', '', '', '']);

        fclose($output);
        exit;
    }

    /**
     * Tambah lokasi tujuan baru cepat via AJAX
     */
    public function storeLokasiQuick($idOrKode) {
        $paket = $this->getAccessiblePaket($idOrKode, true);

        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Method tidak diizinkan']);
            exit;
        }

        $namaLokasi = trim($_POST['nama_lokasi'] ?? '');
        $kabKota = trim($_POST['kab_kota'] ?? '');
        $kabupatenId = !empty($_POST['kabupaten_id']) ? (int)$_POST['kabupaten_id'] : null;

        if (empty($namaLokasi)) {
            echo json_encode(['success' => false, 'message' => 'Nama tujuan / instansi wajib diisi']);
            exit;
        }

        if (empty($kabKota) && empty($kabupatenId)) {
            echo json_encode(['success' => false, 'message' => 'Kota / Kabupaten wajib dipilih']);
            exit;
        }

        try {
            $newId = $this->lokasiModel->create([
                'nama_lokasi' => $namaLokasi,
                'kab_kota' => $kabKota,
                'kabupaten_id' => $kabupatenId,
                'kategori' => 'Sekolah'
            ]);

            $newLokasi = $this->lokasiModel->getById($newId);

            $lokasiData = [
                'id' => (int)$newId,
                'nama_lokasi' => $newLokasi['nama_lokasi'],
                'kab_kota' => $newLokasi['kab_kota'] ?: '',
                'kategori' => $newLokasi['kategori'] ?: 'Sekolah'
            ];

            echo json_encode([
                'success' => true,
                'message' => 'Tujuan berhasil ditambahkan ke database',
                'lokasi' => $lokasiData,
                'data' => $lokasiData
            ]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Gagal menyimpan: ' . $e->getMessage()]);
        }
        exit;
    }

    /**
     * Hapus lokasi master (Hanya Admin)
     */
    public function deleteLokasi($id) {
        if (($_SESSION['admin_role'] ?? 'user') !== 'admin') {
            flash('error', 'Akses ditolak. Hanya admin yang memiliki izin menghapus master tujuan.');
            redirect('/paket');
            return;
        }

        try {
            $this->lokasiModel->delete((int)$id);
            flash('success', 'Master tujuan berhasil dihapus.');
        } catch (Exception $e) {
            flash('error', 'Gagal menghapus tujuan: ' . $e->getMessage());
        }

        redirect('/paket');
    }
    
    /**
     * Upload dan simpan dokumen baru (BAST, Kontrak, dll.)
     */
    public function storeDokumen($idOrKode) {
        $paket = $this->getAccessiblePaket($idOrKode);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/paket/detail/' . ($paket['kode_paket'] ?: $paket['id']) . '?tab=dokumen#dokumen');
            return;
        }

        if (!isset($_FILES['file_dokumen']) || $_FILES['file_dokumen']['error'] !== UPLOAD_ERR_OK) {
            flash('error', 'Silakan pilih berkas dokumen yang valid untuk diupload.');
            redirect('/paket/detail/' . ($paket['kode_paket'] ?: $paket['id']) . '?tab=dokumen#dokumen');
            return;
        }

        $jenis = trim($_POST['jenis_dokumen'] ?? 'BAST');
        if ($jenis === 'Lainnya' && !empty($_POST['jenis_dokumen_custom'])) {
            $jenis = trim($_POST['jenis_dokumen_custom']);
        }

        $namaDokumen = trim($_POST['nama_dokumen'] ?? '');
        if (empty($namaDokumen)) {
            // Default ke nama file asli tanpa ekstensi
            $namaDokumen = pathinfo($_FILES['file_dokumen']['name'], PATHINFO_FILENAME);
        }

        $uploadResult = Uploader::uploadFile($_FILES['file_dokumen'], 'dokumen', ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'csv', 'jpg', 'jpeg', 'png', 'webp', 'zip', 'rar'], 25, true);

        if (!$uploadResult['success']) {
            flash('error', $uploadResult['message']);
            redirect('/paket/detail/' . ($paket['kode_paket'] ?: $paket['id']) . '?tab=dokumen#dokumen');
            return;
        }

        try {
            $this->dokumenModel->create([
                'paket_id'        => $paket['id'],
                'jenis_dokumen'   => $jenis,
                'nama_dokumen'    => $namaDokumen,
                'nama_file_asli'  => $uploadResult['orig_name'],
                'file_path'       => $uploadResult['file_path'],
                'file_size'       => $uploadResult['file_size'],
                'file_ext'        => $uploadResult['file_ext'],
                'mime_type'       => $uploadResult['mime_type'],
                'keterangan'      => trim($_POST['keterangan'] ?? ''),
                'uploaded_by'     => $_SESSION['admin_id'] ?? null
            ]);

            flash('success', 'Dokumen ' . htmlspecialchars($namaDokumen) . ' berhasil diupload!');
        } catch (Exception $e) {
            // Rollback uploaded file if DB insert fails
            Uploader::deleteFile($uploadResult['file_path']);
            flash('error', 'Gagal menyimpan dokumen: ' . $e->getMessage());
        }

        $redirectTarget = !empty($_POST['redirect_to']) ? $_POST['redirect_to'] : ('/paket?open_modal=' . ($paket['kode_paket'] ?: $paket['id']) . '&tab=dokumen');
        redirect($redirectTarget);
    }

    /**
     * Download atau preview berkas dokumen
     */
    public function downloadDokumen($idOrKode, $dokumenId) {
        $paket = $this->getAccessiblePaket($idOrKode);

        $doc = $this->dokumenModel->getById($dokumenId);
        if (!$doc || $doc['paket_id'] != $paket['id']) {
            flash('error', 'Dokumen tidak ditemukan.');
            redirect('/paket/detail/' . ($paket['kode_paket'] ?: $paket['id']) . '?tab=dokumen#dokumen');
            return;
        }

        $filePath = PUBLIC_PATH . '/' . ltrim($doc['file_path'], '/');
        if (!file_exists($filePath) || !is_file($filePath)) {
            flash('error', 'Berkas fisik dokumen tidak ditemukan di server.');
            redirect('/paket/detail/' . ($paket['kode_paket'] ?: $paket['id']) . '?tab=dokumen#dokumen');
            return;
        }

        $fileName = $doc['nama_file_asli'] ?: basename($filePath);
        $mime = $doc['mime_type'] ?: 'application/octet-stream';

        // Check if user requested inline view or force download
        $disposition = (isset($_GET['view']) && $_GET['view'] === '1' && in_array($doc['file_ext'], ['pdf', 'jpg', 'jpeg', 'png', 'webp'])) ? 'inline' : 'attachment';

        header('Content-Description: File Transfer');
        header('Content-Type: ' . $mime);
        header('Content-Disposition: ' . $disposition . '; filename="' . rawurlencode($fileName) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filePath));
        
        // Clean output buffer before sending file
        if (ob_get_level()) {
            ob_end_clean();
        }
        readfile($filePath);
        exit;
    }

    /**
     * Hapus berkas dokumen
     */
    public function deleteDokumen($idOrKode, $dokumenId) {
        $paket = $this->getAccessiblePaket($idOrKode);

        $isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') || 
                  (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false) ||
                  (isset($_GET['ajax']) && $_GET['ajax'] == '1');

        try {
            $this->dokumenModel->delete($dokumenId);
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => true, 'message' => 'Dokumen berhasil dihapus.']);
                exit;
            }
            flash('success', 'Dokumen berhasil dihapus.');
        } catch (Exception $e) {
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Gagal menghapus dokumen: ' . $e->getMessage()]);
                exit;
            }
            flash('error', 'Gagal menghapus dokumen: ' . $e->getMessage());
        }

        $fallback = !empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : ('/paket?open_modal=' . ($paket['kode_paket'] ?: $paket['id']) . '&tab=dokumen');
        redirect($fallback);
    }

    /**
     * Upload foto dokumentasi (bisa multiple) untuk item alokasi distribusi
     */
    public function uploadDokumentasi($idOrKode) {
        $paket = $this->getAccessiblePaket($idOrKode, true);

        $isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') || 
                  (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false) ||
                  (isset($_POST['is_ajax']) && $_POST['is_ajax'] == '1');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Metode request tidak valid.']);
                exit;
            }
            redirect('/paket/detail/' . ($paket['kode_paket'] ?: $paket['id']) . '?tab=dokumentasi#dokumentasi');
            return;
        }

        $distribusiId = (int)($_POST['distribusi_id'] ?? 0);
        $dist = $this->distribusiModel->getById($distribusiId);
        if (!$dist || (int)$dist['paket_id'] !== (int)$paket['id']) {
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Data alokasi distribusi tidak ditemukan pada paket ini.']);
                exit;
            }
            flash('error', 'Data alokasi distribusi tidak ditemukan.');
            redirect('/paket/detail/' . ($paket['kode_paket'] ?: $paket['id']) . '?tab=dokumentasi#dokumentasi');
            return;
        }

        if (!isset($_FILES['fotos']) || empty($_FILES['fotos']['name'])) {
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Silakan pilih minimal satu file foto.']);
                exit;
            }
            flash('error', 'Silakan pilih foto yang akan diupload.');
            redirect('/paket/detail/' . ($paket['kode_paket'] ?: $paket['id']) . '?tab=dokumentasi#dokumentasi');
            return;
        }

        $keterangan = trim($_POST['keterangan'] ?? '');
        $uploadedList = [];
        $errors = [];

        // Normalisasi array $_FILES['fotos'] untuk multiple file upload
        $files = [];
        if (is_array($_FILES['fotos']['name'])) {
            $count = count($_FILES['fotos']['name']);
            for ($i = 0; $i < $count; $i++) {
                if ($_FILES['fotos']['error'][$i] === UPLOAD_ERR_OK) {
                    $files[] = [
                        'name'     => $_FILES['fotos']['name'][$i],
                        'type'     => $_FILES['fotos']['type'][$i],
                        'tmp_name' => $_FILES['fotos']['tmp_name'][$i],
                        'error'    => $_FILES['fotos']['error'][$i],
                        'size'     => $_FILES['fotos']['size'][$i]
                    ];
                } elseif ($_FILES['fotos']['error'][$i] !== UPLOAD_ERR_NO_FILE) {
                    $errors[] = $_FILES['fotos']['name'][$i] . ' gagal diupload (Error code: ' . $_FILES['fotos']['error'][$i] . ')';
                }
            }
        } elseif ($_FILES['fotos']['error'] === UPLOAD_ERR_OK) {
            $files[] = $_FILES['fotos'];
        }

        if (empty($files)) {
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Tidak ada file foto valid yang diterima server.']);
                exit;
            }
            flash('error', 'Tidak ada foto yang valid untuk diupload.');
            redirect('/paket/detail/' . ($paket['kode_paket'] ?: $paket['id']) . '?tab=dokumentasi#dokumentasi');
            return;
        }

        foreach ($files as $file) {
            $uploadResult = Uploader::uploadDocumentationImage($file, 'dokumentasi', 1600, 1600, 82);
            if ($uploadResult['success']) {
                $docId = $this->dokumentasiModel->create([
                    'paket_id'       => $paket['id'],
                    'distribusi_id'  => $distribusiId,
                    'nama_file_asli' => $uploadResult['orig_name'],
                    'file_path'      => $uploadResult['file_path'],
                    'thumb_path'     => $uploadResult['thumb_path'],
                    'file_size'      => $uploadResult['file_size'],
                    'file_ext'       => $uploadResult['file_ext'],
                    'mime_type'      => $uploadResult['mime_type'],
                    'keterangan'     => $keterangan,
                    'uploaded_by'    => $_SESSION['admin_id'] ?? null
                ]);
                $uploadResult['id'] = $docId;
                $uploadedList[] = $uploadResult;
            } else {
                $errors[] = $file['name'] . ': ' . $uploadResult['message'];
            }
        }

        if ($isAjax) {
            header('Content-Type: application/json');
            if (!empty($uploadedList)) {
                echo json_encode([
                    'success'  => true,
                    'message'  => count($uploadedList) . ' foto dokumentasi berhasil diunggah!',
                    'count'    => count($uploadedList),
                    'uploaded' => $uploadedList,
                    'errors'   => $errors
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Gagal mengupload foto: ' . implode(', ', $errors)
                ]);
            }
            exit;
        }

        if (!empty($uploadedList)) {
            flash('success', count($uploadedList) . ' foto dokumentasi berhasil diupload.');
        }
        if (!empty($errors)) {
            flash('warning', 'Beberapa foto gagal diupload: ' . implode(', ', $errors));
        }

        redirect('/paket/detail/' . ($paket['kode_paket'] ?: $paket['id']) . '?tab=dokumentasi#dokumentasi');
    }

    /**
     * Ambil daftar JSON foto dokumentasi untuk alokasi distribusi tertentu
     */
    public function getDokumentasiList($idOrKode, $distribusiId) {
        $paket = $this->getAccessiblePaket($idOrKode, true);
        $dist = $this->distribusiModel->getById((int)$distribusiId);

        if (!$dist || (int)$dist['paket_id'] !== (int)$paket['id']) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Distribusi tidak ditemukan.']);
            exit;
        }

        $photos = $this->dokumentasiModel->getByDistribusiId((int)$distribusiId);
        foreach ($photos as &$p) {
            $p['url'] = APP_URL . '/' . ltrim($p['file_path'], '/');
            $p['thumb_url'] = !empty($p['thumb_path']) ? (APP_URL . '/' . ltrim($p['thumb_path'], '/')) : $p['url'];
            $bytes = (int)($p['file_size'] ?? 0);
            $p['formatted_size'] = ($bytes >= 1048576) ? (round($bytes / 1048576, 1) . ' MB') : (round($bytes / 1024, 0) . ' KB');
            $p['formatted_date'] = !empty($p['created_at']) ? date('d M Y, H:i', strtotime($p['created_at'])) : '-';
        }
        unset($p);

        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'distribusi' => [
                'id' => (int)$dist['id'],
                'nama_tujuan' => $dist['nama_tujuan'],
                'nama_komponen' => $dist['nama_komponen'] ?? '-',
                'jumlah' => (float)$dist['jumlah'],
                'satuan' => $dist['satuan'] ?? 'Unit',
                'status_distribusi' => $dist['status_distribusi'] ?? 'Belum Dipesan'
            ],
            'photos' => $photos
        ]);
        exit;
    }

    /**
     * Hapus satu foto dokumentasi
     */
    public function deleteDokumentasi($idOrKode, $fotoId) {
        $paket = $this->getAccessiblePaket($idOrKode, true);
        $foto = $this->dokumentasiModel->getById((int)$fotoId);

        $isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') || 
                  (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false) ||
                  (isset($_POST['is_ajax']) && $_POST['is_ajax'] == '1');

        if (!$foto || (int)$foto['paket_id'] !== (int)$paket['id']) {
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Foto tidak ditemukan atau bukan milik paket ini.']);
                exit;
            }
            flash('error', 'Foto dokumentasi tidak ditemukan.');
            redirect('/paket/detail/' . ($paket['kode_paket'] ?: $paket['id']) . '?tab=dokumentasi#dokumentasi');
            return;
        }

        try {
            $this->dokumentasiModel->delete((int)$fotoId);
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => true, 'message' => 'Foto dokumentasi berhasil dihapus.']);
                exit;
            }
            flash('success', 'Foto dokumentasi berhasil dihapus.');
        } catch (Exception $e) {
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Gagal menghapus: ' . $e->getMessage()]);
                exit;
            }
            flash('error', 'Gagal menghapus foto dokumentasi: ' . $e->getMessage());
        }

        redirect('/paket/detail/' . ($paket['kode_paket'] ?: $paket['id']) . '?tab=dokumentasi#dokumentasi');
    }
    
    private function view($view, $data = []) {
        extract($data);
        if (!empty($_GET['modal']) && $_GET['modal'] === '1') {
            require_once VIEW_PATH . '/layout/header_modal.php';
            require_once VIEW_PATH . '/' . $view . '.php';
            require_once VIEW_PATH . '/layout/footer_modal.php';
            return;
        }
        require_once VIEW_PATH . '/layout/header.php';
        require_once VIEW_PATH . '/' . $view . '.php';
        require_once VIEW_PATH . '/layout/footer.php';
    }
}

// Helper function to parse Rupiah
function parseRupiah($rupiah) {
    return (float)preg_replace('/[^0-9]/', '', $rupiah);
}
