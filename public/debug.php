<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "Starting debug...\n";

// Define paths
define('ROOT_PATH', dirname(__DIR__));
define('APP_PATH', ROOT_PATH . '/app');
define('PUBLIC_PATH', ROOT_PATH . '/public');
define('VIEW_PATH', ROOT_PATH . '/views');
define('MODEL_PATH', ROOT_PATH . '/models');
define('CONTROLLER_PATH', ROOT_PATH . '/controllers');
define('HELPER_PATH', ROOT_PATH . '/helpers');

echo "Paths defined.\n";

// Load files
require_once ROOT_PATH . '/config/config.php';
echo "Config loaded.\n";
require_once ROOT_PATH . '/config/database.php';
echo "Database loaded.\n";
require_once ROOT_PATH . '/helpers/functions.php';
echo "Helpers loaded.\n";

// Load Controller
require_once CONTROLLER_PATH . '/DashboardController.php';
echo "DashboardController loaded.\n";

// Instantiate
try {
    $controller = new DashboardController();
    echo "Controller instantiated.\n";
    
    // Call index
    // We suppress output of the view to just check for fatal errors, 
    // or capture it.
    ob_start();
    $controller->index();
    $output = ob_get_clean();
    
    echo "Controller index executed. Output length: " . strlen($output) . "\n";
    if (empty($output)) {
        echo "WARNING: Output is empty!\n";
    } else {
        echo "Output start: " . substr($output, 0, 100) . "...\n";
    }
    
} catch (Exception $e) {
    echo "Caught exception: " . $e->getMessage() . "\n";
} catch (Error $e) {
    echo "Caught fatal error: " . $e->getMessage() . "\n";
}

echo "Debug finished.\n";
