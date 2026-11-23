<?php
/**
 * ProLedger - Profile Controller
 * Mengelola profil akun pengguna & ganti password
 */

require_once MODEL_PATH . '/Admin.php';

class ProfileController {
    private $adminModel;

    public function __construct() {
        if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
            header('Location: ' . APP_URL . '/login');
            exit;
        }
        $this->adminModel = new Admin();
    }

    public function index() {
        $userId = (int)($_SESSION['admin_id'] ?? 0);
        $user = $this->adminModel->findById($userId);
        if (!$user) {
            flash('error', 'Data pengguna tidak ditemukan.');
            redirect('/');
            return;
        }

        $this->view('profile/index', [
            'title'  => 'Profil Saya',
            'active' => 'profile',
            'user'   => $user,
        ]);
    }

    public function updateProfile() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/profile');
            return;
        }
        if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            flash('error', 'Token keamanan tidak valid.');
            redirect('/profile');
            return;
        }

        $userId = (int)($_SESSION['admin_id'] ?? 0);
        $name   = trim($_POST['name'] ?? '');
        $email  = trim($_POST['email'] ?? '');
        $phone  = trim($_POST['phone'] ?? '');

        if ($name === '' || $email === '') {
            flash('error', 'Nama lengkap dan email wajib diisi.');
            redirect('/profile');
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            flash('error', 'Format email tidak valid.');
            redirect('/profile');
            return;
        }

        $existing = $this->adminModel->findByEmail($email);
        if ($existing && (int)$existing['id'] !== $userId) {
            flash('error', 'Email tersebut sudah digunakan oleh akun lain.');
            redirect('/profile');
            return;
        }

        $this->adminModel->updateProfile($userId, $name, $email, $phone);
        $_SESSION['admin_name']  = $name;
        $_SESSION['admin_email'] = $email;

        flash('success', 'Profil akun berhasil diperbarui.');
        redirect('/profile');
    }

    public function updatePassword() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/profile');
            return;
        }
        if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            flash('error', 'Token keamanan tidak valid.');
            redirect('/profile');
            return;
        }

        $userId = (int)($_SESSION['admin_id'] ?? 0);
        $user = $this->adminModel->findById($userId);
        if (!$user) {
            flash('error', 'Akun tidak ditemukan.');
            redirect('/profile');
            return;
        }

        $current = $_POST['current_password'] ?? '';
        $newPass = $_POST['new_password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';

        if (!$this->adminModel->verifyPassword($current, $user['password'])) {
            flash('error', 'Password saat ini tidak sesuai.');
            redirect('/profile');
            return;
        }

        if (strlen($newPass) < 6) {
            flash('error', 'Password baru minimal 6 karakter.');
            redirect('/profile');
            return;
        }

        if ($newPass !== $confirm) {
            flash('error', 'Konfirmasi password baru tidak cocok.');
            redirect('/profile');
            return;
        }

        $this->adminModel->updatePassword($userId, $newPass);
        flash('success', 'Password berhasil diubah.');
        redirect('/profile');
    }

    private function view($view, $data = []) {
        extract($data);
        require_once VIEW_PATH . '/layout/header.php';
        require_once VIEW_PATH . '/' . $view . '.php';
        require_once VIEW_PATH . '/layout/footer.php';
    }
}
