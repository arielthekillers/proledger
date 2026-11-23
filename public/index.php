<?php
/**
 * ProLedger - Front Controller & Router
 * Entry point for the application
 */

// Load configuration
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/functions.php';

// Parse URL
$requestPath = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?? '';
$basePath = parse_url(APP_URL, PHP_URL_PATH) ?? '';
if (!empty($basePath) && strpos($requestPath, $basePath) === 0) {
    $requestPath = substr($requestPath, strlen($basePath));
}
$requestPath = trim($requestPath, '/');
$url = !empty($requestPath) ? explode('/', $requestPath) : [];

// Route Mapping
$controllerName = '';
$action = '';
$params = [];

// Custom Routes
if (in_array(strtolower($url[0] ?? ''), ['rekap', 'transaksi', 'pemodal'])) {
    // Obsolete modules removed
    http_response_code(404);
    echo '<!DOCTYPE html><html><head><meta charset="utf-8"><title>Modul Dihapus</title><script src="https://cdn.tailwindcss.com"></script></head><body class="bg-slate-100 flex items-center justify-center min-h-screen text-center p-4"><div class="bg-white p-8 rounded-2xl shadow-sm border border-slate-200 max-w-md"><div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4 text-slate-400 text-2xl font-bold">404</div><h1 class="text-xl font-bold text-slate-800 mb-2">Modul Tidak Tersedia</h1><p class="text-sm text-slate-500 mb-6">Modul ini sudah tidak digunakan lagi dan telah dihapus dari sistem.</p><a href="' . APP_URL . '/" class="px-5 py-2.5 bg-blue-600 text-white rounded-xl text-sm font-semibold hover:bg-blue-700 transition-colors shadow">Kembali ke Halaman Utama</a></div></body></html>';
    exit;
}

if (empty($url[0])) {
    // Check if user is logged in as admin
    if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true && ($_SESSION['admin_role'] ?? '') === 'admin') {
        header('Location: ' . APP_URL . '/user');
        exit;
    }
    // Home -> Dashboard
    $controllerName = 'DashboardController';
    $action = 'index';
} elseif ($url[0] === 'login') {
    $controllerName = 'AuthController';
    if (($url[1] ?? '') === 'auth') {
        $action = 'authenticate';
    } else {
        $action = 'login';
    }
} elseif ($url[0] === 'register') {
    $controllerName = 'AuthController';
    if (($url[1] ?? '') === 'store' || ($url[1] ?? '') === 'save') {
        $action = 'registerStore';
    } else {
        $action = 'register';
    }
} elseif ($url[0] === 'logout') {
    $controllerName = 'AuthController';
    $action = 'logout';
} elseif ($url[0] === 'monitoring') {
    $controllerName = 'MonitoringController';
    $action = 'index';
    $params = array_slice($url, 1);
} else {
    // Standard Pattern: /controller/action/param
    $controllerName = ucfirst($url[0]) . 'Controller';
    $action = $url[1] ?? 'index';
    $params = array_slice($url, 2);
}

// Authentication Check (AuthController & public MonitoringController do not require login)
if (!in_array($controllerName, ['AuthController', 'MonitoringController'], true)) {
    if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
        header('Location: ' . APP_URL . '/login');
        exit;
    }
}

// Controller file path
$controllerFile = CONTROLLER_PATH . '/' . $controllerName . '.php';

// Check if controller exists
if (file_exists($controllerFile)) {
    require_once $controllerFile;
    
    // Instantiate controller
    if (class_exists($controllerName)) {
        $controller = new $controllerName();
        
        // Check if action exists
        if (method_exists($controller, $action)) {
            // Call action with parameters
            call_user_func_array([$controller, $action], $params);
        } else {
            // Action not found
            http_response_code(404);
            echo '<h1>404 - Action Not Found</h1>';
            echo '<p>Action "' . htmlspecialchars($action) . '" not found in controller "' . htmlspecialchars($controllerName) . '"</p>';
        }
    } else {
        // Controller class not found
        http_response_code(404);
        echo '<h1>404 - Controller Class Not Found</h1>';
    }
} else {
    // Controller file not found
    http_response_code(404);
    echo '<h1>404 - Page Not Found</h1>';
    echo '<p>The page you are looking for does not exist.</p>';
    echo '<a href="' . APP_URL . '/">Go to Dashboard</a>';
}
