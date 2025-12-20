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
$url = $_SERVER['REQUEST_URI'];
$basePath = parse_url(APP_URL, PHP_URL_PATH) ?? '';
$url = str_replace($basePath, '', $url);
$url = trim($url, '/');
$url = filter_var($url, FILTER_SANITIZE_URL);
$url = explode('/', $url);

// Remove query string
if (strpos($url[0], '?') !== false) {
    $url[0] = substr($url[0], 0, strpos($url[0], '?'));
}

// Route Mapping
$controllerName = '';
$action = '';
$params = [];

// Custom Routes
if (empty($url[0])) {
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
} elseif ($url[0] === 'logout') {
    $controllerName = 'AuthController';
    $action = 'logout';
} else {
    // Standard Pattern: /controller/action/param
    $controllerName = ucfirst($url[0]) . 'Controller';
    $action = $url[1] ?? 'index';
    $params = array_slice($url, 2);
}

// Authentication Check
if ($controllerName !== 'AuthController') {
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
