<?php
/**
 * ProLedger - Tujuan Controller (Admin Only)
 * Mengelola master direktori tujuan distribusi (Sekolah, Dinas, Instansi, dll.)
 */

require_once MODEL_PATH . '/LokasiPenerima.php';

class TujuanController {
    private $lokasiModel;

    public function __construct() {
        if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
            header('Location: ' . APP_URL . '/login');
            exit;
        }
        if (($_SESSION['admin_role'] ?? 'user') !== 'admin') {
            header('Location: ' . APP_URL);
            exit;
        }

        $this->lokasiModel = new LokasiPenerima();
    }

    public function index() {
        $lokasiList    = $this->lokasiModel->getAll();
        $kabupatenList = $this->lokasiModel->getAllKabupaten();

        $this->view('tujuan/index', [
            'title'         => 'Manajemen Tujuan Distribusi',
            'active'        => 'tujuan',
            'lokasiList'    => $lokasiList,
            'kabupatenList' => $kabupatenList,
        ]);
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/tujuan');
            return;
        }
        if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            flash('error', 'Token keamanan tidak valid.');
            redirect('/tujuan');
            return;
        }

        $nama = trim($_POST['nama_lokasi'] ?? '');
        if ($nama === '') {
            flash('error', 'Nama tujuan / instansi wajib diisi.');
            redirect('/tujuan');
            return;
        }

        if ($this->lokasiModel->getByName($nama)) {
            flash('error', 'Tujuan dengan nama tersebut sudah terdaftar.');
            redirect('/tujuan');
            return;
        }

        $this->lokasiModel->create([
            'nama_lokasi'    => $nama,
            'kategori'       => trim($_POST['kategori'] ?? 'Sekolah'),
            'nama_pic'       => trim($_POST['nama_pic'] ?? ''),
            'jabatan_pic'    => trim($_POST['jabatan_pic'] ?? 'Kepala Sekolah'),
            'no_kontak'      => trim($_POST['no_kontak'] ?? ''),
            'kab_kota'       => trim($_POST['kab_kota'] ?? ''),
            'alamat_lengkap' => trim($_POST['alamat_lengkap'] ?? ''),
        ]);

        flash('success', 'Tujuan distribusi <strong>' . htmlspecialchars($nama) . '</strong> berhasil ditambahkan.');
        redirect('/tujuan');
    }

    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/tujuan');
            return;
        }
        if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            flash('error', 'Token keamanan tidak valid.');
            redirect('/tujuan');
            return;
        }

        $nama = trim($_POST['nama_lokasi'] ?? '');
        if ($nama === '') {
            flash('error', 'Nama tujuan / instansi wajib diisi.');
            redirect('/tujuan');
            return;
        }

        $this->lokasiModel->update((int)$id, [
            'nama_lokasi'    => $nama,
            'kategori'       => trim($_POST['kategori'] ?? 'Sekolah'),
            'nama_pic'       => trim($_POST['nama_pic'] ?? ''),
            'jabatan_pic'    => trim($_POST['jabatan_pic'] ?? 'Kepala Sekolah'),
            'no_kontak'      => trim($_POST['no_kontak'] ?? ''),
            'kab_kota'       => trim($_POST['kab_kota'] ?? ''),
            'alamat_lengkap' => trim($_POST['alamat_lengkap'] ?? ''),
        ]);

        flash('success', 'Data tujuan distribusi berhasil diperbarui.');
        redirect('/tujuan');
    }

    public function delete($id) {
        $lokasi = $this->lokasiModel->getById((int)$id);
        if ($lokasi) {
            $this->lokasiModel->delete((int)$id);
            flash('success', 'Tujuan <strong>' . htmlspecialchars($lokasi['nama_lokasi']) . '</strong> berhasil dihapus.');
        } else {
            flash('error', 'Data tujuan tidak ditemukan.');
        }
        redirect('/tujuan');
    }

    private function view($view, $data = []) {
        extract($data);
        require_once VIEW_PATH . '/layout/header.php';
        require_once VIEW_PATH . '/' . $view . '.php';
        require_once VIEW_PATH . '/layout/footer.php';
    }
}
