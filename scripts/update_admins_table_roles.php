<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

try {
    $db = Database::getInstance()->getConnection();
    
    // Add columns if they don't exist
    // Using simple queries for this environment
    
    // 1. Add role column
    try {
        $db->exec("ALTER TABLE admins ADD COLUMN role ENUM('admin', 'pemodal', 'perusahaan') NOT NULL DEFAULT 'admin' AFTER email");
        echo "Column 'role' added.\n";
    } catch (PDOException $e) {
        // Ignore if exists (or check specific error code, but specific logic is harder in portable script)
        echo "Column 'role' might already exist or error: " . $e->getMessage() . "\n";
    }

    // 2. Add reference_id column
    try {
        $db->exec("ALTER TABLE admins ADD COLUMN reference_id INT NULL AFTER role");
        echo "Column 'reference_id' added.\n";
    } catch (PDOException $e) {
         echo "Column 'reference_id' might already exist or error: " . $e->getMessage() . "\n";
    }
    
    // 3. Add index for lookups
    try {
        $db->exec("CREATE INDEX idx_role_ref ON admins(role, reference_id)");
        echo "Index added.\n";
    } catch (PDOException $e) {
        echo "Index might already exist.\n";
    }

    echo "Migration completed.\n";

} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
