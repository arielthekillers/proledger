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
        
        $data = [
            'title' => 'Paket Kegiatan',
            'active' => 'paket',
            'paket' => array_values($paket),
            'groups' => $groups,
            'skemas' => $skemas,
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
            // Parse Rupiah & percentage values
            $pagu = parseRupiah($_POST['pagu']);
            $paguNego = !empty($_POST['pagu_nego']) ? parseRupiah($_POST['pagu_nego']) : null;

            // Dinamis: Cek status checkbox pasal & admin
            $adaPasal = !empty($_POST['ada_pasal']);
            $persenPasal = ($adaPasal && isset($_POST['persen_pasal']) && $_POST['persen_pasal'] !== '') ? (float)$_POST['persen_pasal'] : 0.0;
            
            $adaAdmin = !empty($_POST['ada_admin']);
            $persenAdmin = ($adaAdmin && isset($_POST['persen_admin']) && $_POST['persen_admin'] !== '') ? (float)$_POST['persen_admin'] : 0.0;
            
            $skemaId = !empty($_POST['skema_id']) ? (int)$_POST['skema_id'] : null;
            $tipePotongan = (!empty($_POST['tipe_potongan']) && $_POST['tipe_potongan'] === 'kontrak') ? 'kontrak' : 'dpp';

            $data = [
                'nama_paket' => sanitize($_POST['nama_paket']),
                'nomor_sp' => !empty($_POST['nomor_sp']) ? sanitize($_POST['nomor_sp']) : null,
                'kab_kota' => !empty($_POST['kab_kota']) ? sanitize($_POST['kab_kota']) : null,
                'perusahaan_id' => $activeCompany['id'],
                'group_id' => !empty($_POST['group_id']) ? (int)$_POST['group_id'] : null,
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
            redirect('/paket/detail/' . $paket['kode_paket']);
            return;
        }

        $role = $_SESSION['admin_role'] ?? 'user';
        
        $summary = $this->paketModel->getSummary($paket['id']);
        $items = $this->itemModel->getByPaketId($paket['id']);
        $itemSummary = $this->itemModel->getSummaryByPaketId($paket['id']);
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
                        'status' => $dist['status_penerimaan'] ?: ($dist['status'] ?? 'Menunggu')
                    ];
                    if (!empty($dist['nama_tujuan'])) {
                        $tujuanNames[trim($dist['nama_tujuan'])] = true;
                    }
                }
            }
            $it['tujuan_count'] = count($tujuanNames) > 0 ? count($tujuanNames) : count($it['existing_dists']);
        }
        unset($it);
        
        $data = [
            'title' => $paket['nama_paket'],
            'active' => 'paket',
            'paket' => $paket,
            'summary' => $summary,
            'items' => $items,
            'itemSummary' => $itemSummary,
            'distribusiList' => $distribusiList,
            'distribusiSummary' => $distribusiSummary,
            'groupedDistribusi' => $this->distribusiModel->getGroupedByTujuan($paket['id']),
            'lokasiList' => $lokasiList,
            'kabupatenList' => $kabupatenList,
            'dokumenList' => $dokumenList,
            'groups' => $groups,
            'skemas' => $skemas,
            'modal_detail' => [],
            'transaksi' => []
        ];
        
        $this->view('paket/detail', $data);
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

            // Parse Rupiah & percentage values
            $pagu = parseRupiah($_POST['pagu']);
            $paguNego = !empty($_POST['pagu_nego']) ? parseRupiah($_POST['pagu_nego']) : null;

            // Dinamis: Cek status checkbox pasal & admin
            $adaPasal = !empty($_POST['ada_pasal']);
            $persenPasal = ($adaPasal && isset($_POST['persen_pasal']) && $_POST['persen_pasal'] !== '') ? (float)$_POST['persen_pasal'] : 0.0;
            
            $adaAdmin = !empty($_POST['ada_admin']);
            $persenAdmin = ($adaAdmin && isset($_POST['persen_admin']) && $_POST['persen_admin'] !== '') ? (float)$_POST['persen_admin'] : 0.0;
            
            $skemaId = !empty($_POST['skema_id']) ? (int)$_POST['skema_id'] : null;
            $tipePotongan = (!empty($_POST['tipe_potongan']) && $_POST['tipe_potongan'] === 'kontrak') ? 'kontrak' : 'dpp';

            $perusahaanId = $existing['perusahaan_id'] ?? (!empty($_POST['perusahaan_id']) ? (int)$_POST['perusahaan_id'] : ($_SESSION['active_perusahaan_id'] ?? null));
            $perusahaanNama = $existing['perusahaan_nama'] ?? $existing['perusahaan'] ?? null;

            $data = [
                'nama_paket' => sanitize($_POST['nama_paket']),
                'nomor_sp' => !empty($_POST['nomor_sp']) ? sanitize($_POST['nomor_sp']) : ($existing['nomor_sp'] ?? null),
                'kab_kota' => !empty($_POST['kab_kota']) ? sanitize($_POST['kab_kota']) : null,
                'perusahaan_id' => $perusahaanId,
                'perusahaan' => $perusahaanNama,
                'group_id' => !empty($_POST['group_id']) ? (int)$_POST['group_id'] : null,
                'skema_id' => $skemaId,
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
            
            $this->paketModel->update($idOrKode, $data);
            $targetCode = $existing['kode_paket'] ?: $existing['id'];
            
            flash('success', 'Paket kegiatan (' . $targetCode . ') berhasil diupdate!');
            redirect('/paket/detail/' . $targetCode);
            
        } catch (Exception $e) {
            flash('error', 'Gagal mengupdate paket: ' . $e->getMessage());
            setOld($_POST);
            redirect('/paket/edit/' . $idOrKode);
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
            redirect('/paket/detail/' . ($paket['kode_paket'] ?: $paket['id']) . '#items');
            return;
        }

        $komponen = trim($_POST['komponen'] ?? '');
        if (empty($komponen)) {
            flash('error', 'Nama komponen barang wajib diisi.');
            redirect('/paket/detail/' . ($paket['kode_paket'] ?: $paket['id']) . '#items');
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

            flash('success', 'Item spesifikasi berhasil ditambahkan!');
        } catch (Exception $e) {
            flash('error', 'Gagal menambahkan item: ' . $e->getMessage());
        }

        redirect('/paket/detail/' . ($paket['kode_paket'] ?: $paket['id']) . '#items');
    }

    /**
     * Update item spesifikasi dalam paket
     */
    public function updateItem($idOrKode, $itemId) {
        $paket = $this->getAccessiblePaket($idOrKode);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/paket/detail/' . ($paket['kode_paket'] ?: $paket['id']) . '#items');
            return;
        }

        $item = $this->itemModel->getById($itemId);
        if (!$item || $item['paket_id'] != $paket['id']) {
            flash('error', 'Item tidak ditemukan.');
            redirect('/paket/detail/' . ($paket['kode_paket'] ?: $paket['id']) . '#items');
            return;
        }

        $komponen = trim($_POST['komponen'] ?? '');
        if (empty($komponen)) {
            flash('error', 'Nama komponen barang wajib diisi.');
            redirect('/paket/detail/' . ($paket['kode_paket'] ?: $paket['id']) . '#items');
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

            flash('success', 'Item spesifikasi berhasil diperbarui!');
        } catch (Exception $e) {
            flash('error', 'Gagal memperbarui item: ' . $e->getMessage());
        }

        redirect('/paket/detail/' . ($paket['kode_paket'] ?: $paket['id']) . '#items');
    }

    /**
     * Hapus item spesifikasi dalam paket
     */
    public function deleteItem($idOrKode, $itemId) {
        $paket = $this->getAccessiblePaket($idOrKode);

        try {
            $this->itemModel->delete($itemId);
            flash('success', 'Item spesifikasi berhasil dihapus.');
        } catch (Exception $e) {
            flash('error', 'Gagal menghapus item: ' . $e->getMessage());
        }

        redirect('/paket/detail/' . ($paket['kode_paket'] ?: $paket['id']) . '#items');
    }

    /**
     * Tambah alokasi distribusi paket
     */
    public function storeDistribusi($idOrKode) {
        $paket = $this->getAccessiblePaket($idOrKode);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/paket/detail/' . ($paket['kode_paket'] ?: $paket['id']) . '#distribusi');
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
            redirect('/paket/detail/' . ($paket['kode_paket'] ?: $paket['id']) . '#' . $redirectTab);
            return;
        }

        $item = $this->itemModel->getById($itemId);
        if (!$item || $item['paket_id'] != $paket['id']) {
            flash('error', 'Item komponen tidak ditemukan dalam paket ini.');
            redirect('/paket/detail/' . ($paket['kode_paket'] ?: $paket['id']) . '#' . $redirectTab);
            return;
        }

        // Kalkulasi kuota distribusi untuk item ini
        $allocatedMap = $this->distribusiModel->getAllocatedPerItem($paket['id']);
        $alreadyAllocated = (float)($allocatedMap[$itemId] ?? 0);
        $totalItemQty = (float)$item['jumlah'];
        $remainingQuota = max(0, $totalItemQty - $alreadyAllocated);

        if ($remainingQuota <= 0) {
            flash('error', 'Gagal: Seluruh kuota untuk item "' . $item['komponen'] . '" (' . $totalItemQty . ' ' . $item['satuan'] . ') sudah terdistribusi penuh. Tidak dapat menambah alokasi.');
            redirect('/paket/detail/' . ($paket['kode_paket'] ?: $paket['id']) . '#' . $redirectTab);
            return;
        }

        if ($jumlah <= 0) {
            flash('error', 'Jumlah alokasi harus lebih besar dari 0.');
            redirect('/paket/detail/' . ($paket['kode_paket'] ?: $paket['id']) . '#' . $redirectTab);
            return;
        }

        if ($jumlah > $remainingQuota) {
            flash('error', 'Gagal: Jumlah alokasi (' . $jumlah . ' ' . $item['satuan'] . ') melebihi sisa kuota yang tersedia (' . $remainingQuota . ' ' . $item['satuan'] . ').');
            redirect('/paket/detail/' . ($paket['kode_paket'] ?: $paket['id']) . '#' . $redirectTab);
            return;
        }

        $statusPenerimaan = trim($_POST['status_penerimaan'] ?? 'Menunggu Barang');
        $statusLegacy = ($statusPenerimaan === 'Diterima di Tujuan') ? 'Diterima' : (($statusPenerimaan === 'Dalam Pengiriman') ? 'Proses Kirim' : 'Belum Dikirim');
        $statusPemesanan = trim($_POST['status_pemesanan'] ?? 'Belum Dipesan');
        $rutePengiriman = trim($_POST['rute_pengiriman'] ?? 'kantor');

        // Otomatisasi tanggal terima jika status diterima
        $tanggalTerima = !empty($_POST['tanggal_terima']) ? $_POST['tanggal_terima'] : ($statusPenerimaan === 'Diterima di Tujuan' ? date('Y-m-d') : null);

        try {
            $this->distribusiModel->create([
                'paket_id' => $paket['id'],
                'item_id' => $itemId,
                'lokasi_id' => $lokasiId,
                'nama_tujuan' => $namaTujuan,
                'kategori' => $kategori,
                'jumlah' => $jumlah,
                'rute_pengiriman' => $rutePengiriman,
                'status_pemesanan' => $statusPemesanan,
                'tanggal_pesan' => !empty($_POST['tanggal_pesan']) ? $_POST['tanggal_pesan'] : null,
                'link_pembelian' => trim($_POST['link_pembelian'] ?? ''),
                'kurir_ekspedisi' => trim($_POST['kurir_ekspedisi'] ?? ''),
                'nama_pic' => $namaPic,
                'no_kontak' => $noKontak,
                'alamat' => $alamat,
                'kab_kota' => $kabKota,
                'status' => $statusLegacy,
                'status_penerimaan' => $statusPenerimaan,
                'tanggal_kirim' => !empty($_POST['tanggal_kirim']) ? $_POST['tanggal_kirim'] : null,
                'tanggal_terima' => $tanggalTerima,
                'no_resi' => trim($_POST['no_resi'] ?? ''),
                'catatan' => trim($_POST['catatan'] ?? '')
            ]);

            flash('success', 'Alokasi distribusi ' . htmlspecialchars($item['komponen']) . ' ke ' . htmlspecialchars($namaTujuan) . ' (' . $jumlah . ' ' . htmlspecialchars($item['satuan']) . ') berhasil ditambahkan!');
        } catch (Exception $e) {
            flash('error', 'Gagal menambahkan alokasi pengiriman: ' . $e->getMessage());
        }

        redirect('/paket/detail/' . ($paket['kode_paket'] ?: $paket['id']) . '#' . $redirectTab);
    }

    /**
     * Update alokasi distribusi paket
     */
    public function updateDistribusi($idOrKode, $distribusiId) {
        $this->requireUser();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/paket/detail/' . $idOrKode . '#distribusi');
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
            redirect('/paket/detail/' . ($paket['kode_paket'] ?: $paket['id']) . '#distribusi');
            return;
        }

        $itemId = (int)($_POST['item_id'] ?? $dist['item_id']);
        $namaTujuan = trim($_POST['nama_tujuan'] ?? '');
        $jumlah = (float)($_POST['jumlah'] ?? 1);

        if (!$itemId || empty($namaTujuan)) {
            flash('error', 'Item komponen dan nama tujuan penerima wajib diisi.');
            redirect('/paket/detail/' . ($paket['kode_paket'] ?: $paket['id']) . '#distribusi');
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
                redirect('/paket/detail/' . ($paket['kode_paket'] ?: $paket['id']) . '#distribusi');
                return;
            }
        }

        $lokasiId = !empty($_POST['lokasi_id']) ? (int)$_POST['lokasi_id'] : $dist['lokasi_id'];

        $statusPenerimaan = trim($_POST['status_penerimaan'] ?? ($dist['status_penerimaan'] ?: 'Menunggu Barang'));
        $statusLegacy = ($statusPenerimaan === 'Diterima di Tujuan') ? 'Diterima' : (($statusPenerimaan === 'Dalam Pengiriman') ? 'Proses Kirim' : 'Belum Dikirim');
        $statusPemesanan = trim($_POST['status_pemesanan'] ?? ($dist['status_pemesanan'] ?: 'Belum Dipesan'));
        $rutePengiriman = trim($_POST['rute_pengiriman'] ?? ($dist['rute_pengiriman'] ?: 'kantor'));

        $tanggalTerima = !empty($_POST['tanggal_terima']) ? $_POST['tanggal_terima'] : ($statusPenerimaan === 'Diterima di Tujuan' ? ($dist['tanggal_terima'] ?: date('Y-m-d')) : null);

        try {
            $this->distribusiModel->update($distribusiId, [
                'item_id' => $itemId,
                'lokasi_id' => $lokasiId,
                'nama_tujuan' => $namaTujuan,
                'kategori' => trim($_POST['kategori'] ?? ($dist['kategori'] ?: 'Sekolah')),
                'jumlah' => $jumlah,
                'rute_pengiriman' => $rutePengiriman,
                'status_pemesanan' => $statusPemesanan,
                'tanggal_pesan' => !empty($_POST['tanggal_pesan']) ? $_POST['tanggal_pesan'] : null,
                'link_pembelian' => trim($_POST['link_pembelian'] ?? ''),
                'kurir_ekspedisi' => trim($_POST['kurir_ekspedisi'] ?? ''),
                'nama_pic' => trim($_POST['nama_pic'] ?? ''),
                'no_kontak' => trim($_POST['no_kontak'] ?? ''),
                'alamat' => trim($_POST['alamat'] ?? ''),
                'kab_kota' => trim($_POST['kab_kota'] ?? ''),
                'status' => $statusLegacy,
                'status_penerimaan' => $statusPenerimaan,
                'tanggal_kirim' => !empty($_POST['tanggal_kirim']) ? $_POST['tanggal_kirim'] : null,
                'tanggal_terima' => $tanggalTerima,
                'no_resi' => trim($_POST['no_resi'] ?? ''),
                'catatan' => trim($_POST['catatan'] ?? '')
            ]);

            flash('success', 'Alokasi pengiriman berhasil diperbarui!');
        } catch (Exception $e) {
            flash('error', 'Gagal memperbarui alokasi pengiriman: ' . $e->getMessage());
        }

        redirect('/paket/detail/' . ($paket['kode_paket'] ?: $paket['id']) . '#distribusi');
    }

    /**
     * AJAX/POST: Update Rute Pengiriman Distribusi
     */
    public function updateRuteDistribusi($idOrKode, $distribusiId) {
        $paket = $this->getAccessiblePaket($idOrKode, true);

        $rute = trim($_POST['rute_pengiriman'] ?? 'kantor');
        try {
            $this->distribusiModel->updateRute($distribusiId, $rute);
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) || (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false)) {
                echo json_encode(['success' => true, 'message' => 'Rute pengiriman berhasil diperbarui!']);
                exit;
            }
            flash('success', 'Rute pengiriman berhasil diperbarui!');
        } catch (Exception $e) {
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) || (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false)) {
                echo json_encode(['success' => false, 'message' => 'Gagal memperbarui rute: ' . $e->getMessage()]);
                exit;
            }
            flash('error', 'Gagal memperbarui rute: ' . $e->getMessage());
        }

        redirect('/paket/detail/' . ($paket['kode_paket'] ?: $paket['id']) . '#distribusi');
    }

    /**
     * AJAX/POST: Update Status Pemesanan Logistik Distribusi
     */
    public function updatePemesananDistribusi($idOrKode, $distribusiId) {
        $paket = $this->getAccessiblePaket($idOrKode, true);

        $data = [
            'status_pemesanan' => trim($_POST['status_pemesanan'] ?? 'Belum Dipesan'),
            'tanggal_pesan' => !empty($_POST['tanggal_pesan']) ? $_POST['tanggal_pesan'] : null,
            'link_pembelian' => trim($_POST['link_pembelian'] ?? ''),
            'kurir_ekspedisi' => trim($_POST['kurir_ekspedisi'] ?? ''),
            'no_resi' => trim($_POST['no_resi'] ?? '')
        ];

        try {
            $this->distribusiModel->updatePemesanan($distribusiId, $data);
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) || (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false)) {
                echo json_encode(['success' => true, 'message' => 'Status pemesanan berhasil diperbarui!']);
                exit;
            }
            flash('success', 'Status pemesanan berhasil diperbarui!');
        } catch (Exception $e) {
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) || (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false)) {
                echo json_encode(['success' => false, 'message' => 'Gagal memperbarui pemesanan: ' . $e->getMessage()]);
                exit;
            }
            flash('error', 'Gagal memperbarui pemesanan: ' . $e->getMessage());
        }

        redirect('/paket/detail/' . ($paket['kode_paket'] ?: $paket['id']) . '#distribusi');
    }

    /**
     * AJAX/POST: Update Status Penerimaan Distribusi di Lokasi
     */
    public function updatePenerimaanDistribusi($idOrKode, $distribusiId) {
        $paket = $this->getAccessiblePaket($idOrKode, true);

        $data = [
            'status_penerimaan' => trim($_POST['status_penerimaan'] ?? 'Menunggu Barang'),
            'tanggal_terima' => !empty($_POST['tanggal_terima']) ? $_POST['tanggal_terima'] : null,
            'catatan' => trim($_POST['catatan'] ?? '')
        ];

        try {
            $this->distribusiModel->updatePenerimaan($distribusiId, $data);
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) || (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false)) {
                echo json_encode(['success' => true, 'message' => 'Status penerimaan berhasil diperbarui!']);
                exit;
            }
            flash('success', 'Status penerimaan berhasil diperbarui!');
        } catch (Exception $e) {
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) || (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false)) {
                echo json_encode(['success' => false, 'message' => 'Gagal memperbarui penerimaan: ' . $e->getMessage()]);
                exit;
            }
            flash('error', 'Gagal memperbarui penerimaan: ' . $e->getMessage());
        }

        redirect('/paket/detail/' . ($paket['kode_paket'] ?: $paket['id']) . '#distribusi');
    }

    /**
     * Cetak BAST per Sekolah / Lokasi Tujuan atau Cetak Semua BAST Sekaligus
     */
    public function cetakBast($idOrKode) {
        $paket = $this->getAccessiblePaket($idOrKode);
        $perusahaan = $this->perusahaanModel->getById($paket['perusahaan_id']);
        $isBulk = (isset($_GET['all']) && $_GET['all'] == '1');

        if ($isBulk) {
            $groupedList = $this->distribusiModel->getGroupedByTujuan($paket['id']);
            if (empty($groupedList)) {
                flash('error', 'Belum ada data alokasi barang untuk mencetak BAST.');
                redirect('/paket/detail/' . ($paket['kode_paket'] ?: $paket['id']) . '#dokumen');
                return;
            }
            $bastDate = !empty($_GET['tgl']) ? $_GET['tgl'] : date('Y-m-d');
            require_once VIEW_PATH . '/paket/cetak_bast.php';
            exit;
        }

        $namaTujuan = trim($_GET['tujuan'] ?? '');
        if (empty($namaTujuan)) {
            flash('error', 'Silakan pilih lokasi tujuan penerima untuk mencetak BAST.');
            redirect('/paket/detail/' . ($paket['kode_paket'] ?: $paket['id']) . '#distribusi');
            return;
        }

        $items = $this->distribusiModel->getItemsByTujuan($paket['id'], $namaTujuan);
        if (empty($items)) {
            flash('error', 'Tidak ada data barang yang dialokasikan untuk tujuan: ' . htmlspecialchars($namaTujuan));
            redirect('/paket/detail/' . ($paket['kode_paket'] ?: $paket['id']) . '#distribusi');
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

        try {
            $this->distribusiModel->delete($distribusiId);
            flash('success', 'Alokasi distribusi berhasil dihapus.');
        } catch (Exception $e) {
            flash('error', 'Gagal menghapus alokasi distribusi: ' . $e->getMessage());
        }

        redirect('/paket/detail/' . ($paket['kode_paket'] ?: $paket['id']) . '#distribusi');
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
            'RUTE PENGIRIMAN',
            'STATUS PEMESANAN',
            'TANGGAL PESAN',
            'KURIR / EKSPEDISI',
            'NO. RESI / SURAT JALAN',
            'STATUS PENERIMAAN DI LOKASI',
            'TANGGAL TERIMA',
            'CATATAN PENERIMAAN'
        ]);

        $no = 1;
        $totalQty = 0;
        foreach ($distribusiList as $d) {
            $ruteLabel = ($d['rute_pengiriman'] === 'kantor') ? 'Ke Rumah / Kantor' : (($d['rute_pengiriman'] === 'gudang') ? 'Ke Gudang Transit' : 'Langsung ke Tujuan');
            $qty = (float)$d['jumlah'];
            $totalQty += $qty;

            fputcsv($output, [
                $no++,
                $d['nama_tujuan'],
                $d['kab_kota'] ?: '-',
                $d['nama_komponen'] ?: '-',
                $qty,
                $d['satuan'] ?: 'Unit',
                $ruteLabel,
                $d['status_pemesanan'] ?: 'Belum Dipesan',
                $d['tanggal_pesan'] ?: '-',
                $d['kurir_ekspedisi'] ?: '-',
                $d['no_resi'] ?: '-',
                $d['status_penerimaan'] ?: 'Menunggu Barang',
                $d['tanggal_terima'] ?: '-',
                $d['catatan'] ?: '-'
            ]);
        }

        fputcsv($output, []);
        fputcsv($output, ['', 'TOTAL BARANG DIALOKASIKAN', '', '', $totalQty, '', '', '', '', '', '', '', '', '']);

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
            redirect('/paket/detail/' . ($paket['kode_paket'] ?: $paket['id']) . '#dokumen');
            return;
        }

        if (!isset($_FILES['file_dokumen']) || $_FILES['file_dokumen']['error'] !== UPLOAD_ERR_OK) {
            flash('error', 'Silakan pilih berkas dokumen yang valid untuk diupload.');
            redirect('/paket/detail/' . ($paket['kode_paket'] ?: $paket['id']) . '#dokumen');
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

        $uploadResult = Uploader::uploadFile($_FILES['file_dokumen'], 'dokumen', ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'csv', 'jpg', 'jpeg', 'png', 'webp', 'zip', 'rar'], 25);

        if (!$uploadResult['success']) {
            flash('error', $uploadResult['message']);
            redirect('/paket/detail/' . ($paket['kode_paket'] ?: $paket['id']) . '#dokumen');
            return;
        }

        try {
            $this->dokumenModel->create([
                'paket_id'        => $paket['id'],
                'jenis_dokumen'   => $jenis,
                'nama_dokumen'    => $namaDokumen,
                'nomor_dokumen'   => trim($_POST['nomor_dokumen'] ?? ''),
                'tanggal_dokumen' => !empty($_POST['tanggal_dokumen']) ? $_POST['tanggal_dokumen'] : null,
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

        redirect('/paket/detail/' . ($paket['kode_paket'] ?: $paket['id']) . '#dokumen');
    }

    /**
     * Download atau preview berkas dokumen
     */
    public function downloadDokumen($idOrKode, $dokumenId) {
        $paket = $this->getAccessiblePaket($idOrKode);

        $doc = $this->dokumenModel->getById($dokumenId);
        if (!$doc || $doc['paket_id'] != $paket['id']) {
            flash('error', 'Dokumen tidak ditemukan.');
            redirect('/paket/detail/' . ($paket['kode_paket'] ?: $paket['id']) . '#dokumen');
            return;
        }

        $filePath = PUBLIC_PATH . '/' . ltrim($doc['file_path'], '/');
        if (!file_exists($filePath) || !is_file($filePath)) {
            flash('error', 'Berkas fisik dokumen tidak ditemukan di server.');
            redirect('/paket/detail/' . ($paket['kode_paket'] ?: $paket['id']) . '#dokumen');
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

        try {
            $this->dokumenModel->delete($dokumenId);
            flash('success', 'Dokumen berhasil dihapus.');
        } catch (Exception $e) {
            flash('error', 'Gagal menghapus dokumen: ' . $e->getMessage());
        }

        redirect('/paket/detail/' . ($paket['kode_paket'] ?: $paket['id']) . '#dokumen');
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
