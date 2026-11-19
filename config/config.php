<?php
/**
 * ProLedger - Configuration File
 * Aplikasi Manajemen Keuangan Kolaboratif
 */

// Path Configuration
define('ROOT_PATH', dirname(__DIR__));
define('APP_PATH', ROOT_PATH . '/app');
define('PUBLIC_PATH', ROOT_PATH . '/public');
define('VIEW_PATH', ROOT_PATH . '/views');
define('MODEL_PATH', ROOT_PATH . '/models');
define('CONTROLLER_PATH', ROOT_PATH . '/controllers');
define('HELPER_PATH', ROOT_PATH . '/helpers');

// Load Environment Variables (.env)
if (file_exists(ROOT_PATH . '/.env')) {
    $lines = file(ROOT_PATH . '/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        if (strpos($line, '=') === false) continue;
        
        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);
        
        if (!array_key_exists($name, $_SERVER) && !array_key_exists($name, $_ENV)) {
            putenv(sprintf('%s=%s', $name, $value));
            $_ENV[$name] = $value;
            $_SERVER[$name] = $value;
        }
    }
}

// Database Configuration
define('DB_HOST', getenv('DB_HOST') ?: ($_ENV['DB_HOST'] ?? ($_SERVER['DB_HOST'] ?? 'localhost')));
define('DB_PORT', getenv('DB_PORT') ?: ($_ENV['DB_PORT'] ?? ($_SERVER['DB_PORT'] ?? '3306')));
define('DB_NAME', getenv('DB_DATABASE') ?: ($_ENV['DB_DATABASE'] ?? ($_SERVER['DB_DATABASE'] ?? 'proledger')));
define('DB_USER', getenv('DB_USERNAME') ?: ($_ENV['DB_USERNAME'] ?? ($_SERVER['DB_USERNAME'] ?? 'root')));
define('DB_PASS', getenv('DB_PASSWORD') ?: ($_ENV['DB_PASSWORD'] ?? ($_SERVER['DB_PASSWORD'] ?? '')));
define('DB_CHARSET', 'utf8mb4');

// Application Configuration
define('APP_NAME', getenv('APP_NAME') ?: ($_ENV['APP_NAME'] ?? ($_SERVER['APP_NAME'] ?? 'ProLedger')));
define('APP_VERSION', '1.0.0');
define('APP_URL', getenv('APP_URL') ?: ($_ENV['APP_URL'] ?? ($_SERVER['APP_URL'] ?? 'http://localhost:8000')));
define('APP_ENV', getenv('APP_ENV') ?: ($_ENV['APP_ENV'] ?? ($_SERVER['APP_ENV'] ?? 'production')));

// Timezone
date_default_timezone_set('Asia/Jakarta');

// Error Reporting
if (APP_ENV === 'local' || getenv('APP_DEBUG') === 'true') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Session Configuration & Hardening
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.use_strict_mode', 1);
    ini_set('session.cookie_httponly', 1);

    $isSecure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443);
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'domain' => '',
        'secure' => $isSecure,
        'httponly' => true,
        'samesite' => 'Lax'
    ]);

    session_start();
}

// Constants
define('MODAL_PASAL_PERCENTAGE', 0.13); // 13% dari pagu

return [];
