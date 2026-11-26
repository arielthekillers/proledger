<?php
/**
 * ProLedger - Auth Controller
 * Menangani login, pendaftaran pengguna (register), dan logout
 */

require_once __DIR__ . '/../models/Admin.php';

class AuthController
{

    /**
     * Tampilkan halaman login
     */
    public function login()
    {
        if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
            header('Location: ' . APP_URL);
            exit;
        }

        $title = 'Login';
        require_once __DIR__ . '/../views/auth/login.php';
    }

    /**
     * Tampilkan halaman pendaftaran
     */
    public function register()
    {
        if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
            header('Location: ' . APP_URL);
            exit;
        }

        $title = 'Pendaftaran Pengguna';
        require_once __DIR__ . '/../views/auth/register.php';
    }

    /**
     * Proses pendaftaran user baru
     */
    public function registerStore()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('/register');
            return;
        }

        // Verify CSRF
        if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            flash('error', 'Token keamanan tidak valid. Silakan coba kembali.');
            redirect('/register');
            return;
        }

        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        setOld([
            'name' => $name,
            'email' => $email,
            'phone' => $phone
        ]);

        if (empty($name) || empty($email) || empty($password)) {
            flash('error', 'Nama lengkap, email, dan password wajib diisi.');
            redirect('/register');
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            flash('error', 'Format email tidak valid.');
            redirect('/register');
            return;
        }

        if (strlen($password) < 6) {
            flash('error', 'Password minimal 6 karakter.');
            redirect('/register');
            return;
        }

        if ($password !== $confirmPassword) {
            flash('error', 'Konfirmasi password tidak cocok.');
            redirect('/register');
            return;
        }

        $adminModel = new Admin();
        if ($adminModel->findByEmail($email)) {
            flash('error', 'Email sudah terdaftar. Silakan gunakan email lain atau login.');
            redirect('/register');
            return;
        }

        try {
            $adminModel->register($name, $email, $phone, $password, 'user');
            clearOld();
            flash('success', 'Pendaftaran berhasil! Akun Anda sedang menunggu persetujuan (approval) dari Administrator.');
            redirect('/login');
            return;
        } catch (Exception $e) {
            flash('error', 'Gagal mendaftar: ' . $e->getMessage());
            redirect('/register');
            return;
        }
    }

    /**
     * Proses login
     */
    public function authenticate()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . APP_URL . '/login');
            exit;
        }

        // Verifikasi CSRF Token
        if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            flash('error', 'Sesi keamanan login kadaluarsa. Silakan refresh halaman dan coba kembali.');
            header('Location: ' . APP_URL . '/login');
            exit;
        }

        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        $adminModel = new Admin();
        $admin = $adminModel->findByEmail($email);

        if ($admin && $adminModel->verifyPassword($password, $admin['password'])) {
            // Check status
            $status = $admin['status'] ?? 'active';

            if ($status === 'pending') {
                flash('error', 'Akun Anda sedang menunggu persetujuan (approval) dari Administrator.');
                header('Location: ' . APP_URL . '/login');
                exit;
            }

            if ($status === 'rejected') {
                flash('error', 'Pendaftaran akun Anda ditolak oleh Administrator.');
                header('Location: ' . APP_URL . '/login');
                exit;
            }

            if ($status === 'inactive') {
                flash('error', 'Akun Anda dinonaktifkan. Silakan hubungi Administrator.');
                header('Location: ' . APP_URL . '/login');
                exit;
            }

            // Regenerate Session ID untuk mencegah session fixation
            session_regenerate_id(true);

            // Login sukses
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_name'] = $admin['name'];
            $_SESSION['admin_email'] = $admin['email'];
            $_SESSION['admin_role'] = $admin['role'] ?? 'user';
            $_SESSION['admin_ref_id'] = $admin['reference_id'] ?? null;

            logActivity('login', 'auth', 'User login ke sistem (' . $admin['email'] . ')', $admin['id'], $admin['id'], $admin['name'], $admin['role'] ?? 'user');

            if (($admin['role'] ?? 'user') === 'admin') {
                header('Location: ' . APP_URL . '/admin');
            } else {
                header('Location: ' . APP_URL);
            }
            exit;
        } else {
            // Login gagal
            flash('error', 'Email atau password salah.');
            header('Location: ' . APP_URL . '/login');
            exit;
        }
    }

    /**
     * Proses logout
     */
    public function logout()
    {
        if (!empty($_SESSION['admin_id'])) {
            logActivity('logout', 'auth', 'User logout dari sistem.');
        }
        session_destroy();
        header('Location: ' . APP_URL . '/login');
        exit;
    }
}
