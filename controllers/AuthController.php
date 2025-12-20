<?php
/**
 * ProLedger - Auth Controller
 * Menangani login dan logout admin
 */

require_once __DIR__ . '/../models/Admin.php';

class AuthController {
    
    /**
     * Tampilkan halaman login
     */
    public function login() {
        // Jika sudah login, redirect ke dashboard
        if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
            header('Location: ' . APP_URL);
            exit;
        }
        
        $title = 'Login';
        require_once __DIR__ . '/../views/auth/login.php';
    }
    
    /**
     * Proses login
     */
    public function authenticate() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . APP_URL . '/login');
            exit;
        }
        
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        
        $adminModel = new Admin();
        $admin = $adminModel->findByEmail($email);
        
        if ($admin && $adminModel->verifyPassword($password, $admin['password'])) {
            // Login sukses
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_name'] = $admin['name'];
            $_SESSION['admin_email'] = $admin['email'];
            $_SESSION['admin_role'] = $admin['role'] ?? 'admin';
            $_SESSION['admin_ref_id'] = $admin['reference_id'] ?? null;
            
            // Log access time if needed
            
            header('Location: ' . APP_URL);
            exit;
        } else {
            // Login gagal
            $_SESSION['error'] = 'Email atau password salah';
            header('Location: ' . APP_URL . '/login');
            exit;
        }
    }
    
    /**
     * Proses logout
     */
    public function logout() {
        session_destroy();
        header('Location: ' . APP_URL . '/login');
        exit;
    }
}
