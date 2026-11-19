<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

try {
    $db = Database::getInstance()->getConnection();
    echo "Starting user management migration...\n";

    // 1. Update role enum to include 'user'
    try {
        $db->exec("ALTER TABLE admins MODIFY COLUMN role ENUM('admin', 'pemodal', 'perusahaan', 'user') NOT NULL DEFAULT 'user'");
        echo "Role column modified with 'user'.\n";
    } catch (PDOException $e) {
        echo "Role modification info: " . $e->getMessage() . "\n";
    }

    // 2. Add phone column
    $stmt = $db->query("SHOW COLUMNS FROM admins LIKE 'phone'");
    if ($stmt->rowCount() == 0) {
        $db->exec("ALTER TABLE admins ADD COLUMN phone VARCHAR(30) NULL AFTER email");
        echo "Column 'phone' added.\n";
    } else {
        echo "Column 'phone' already exists.\n";
    }

    // 3. Add status column
    $stmt = $db->query("SHOW COLUMNS FROM admins LIKE 'status'");
    if ($stmt->rowCount() == 0) {
        $db->exec("ALTER TABLE admins ADD COLUMN status ENUM('active', 'pending', 'rejected', 'inactive') NOT NULL DEFAULT 'active' AFTER role");
        echo "Column 'status' added.\n";
    } else {
        echo "Column 'status' already exists.\n";
    }

    // 4. Add updated_at column
    $stmt = $db->query("SHOW COLUMNS FROM admins LIKE 'updated_at'");
    if ($stmt->rowCount() == 0) {
        $db->exec("ALTER TABLE admins ADD COLUMN updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP");
        echo "Column 'updated_at' added.\n";
    } else {
        echo "Column 'updated_at' already exists.\n";
    }

    // Ensure existing rows are marked active
    $db->exec("UPDATE admins SET status = 'active' WHERE status IS NULL OR status = ''");

    echo "Migration completed successfully!\n";
} catch (Exception $e) {
    die("Migration failed: " . $e->getMessage() . "\n");
}
