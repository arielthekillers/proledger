<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

try {
    $db = Database::getInstance()->getConnection();
    echo "Starting Perusahaan branding columns migration...\n";

    $columnsToAdd = [
        'logo' => "VARCHAR(255) NULL AFTER alamat",
        'avatar' => "VARCHAR(255) NULL AFTER logo",
        'kopsurat' => "VARCHAR(255) NULL AFTER avatar",
        'website' => "VARCHAR(255) NULL AFTER kopsurat",
        'keterangan' => "TEXT NULL AFTER website"
    ];

    foreach ($columnsToAdd as $col => $definition) {
        $stmt = $db->query("SHOW COLUMNS FROM perusahaan LIKE '{$col}'");
        if ($stmt->rowCount() == 0) {
            $db->exec("ALTER TABLE perusahaan ADD COLUMN {$col} {$definition}");
            echo "Column '{$col}' added.\n";
        } else {
            echo "Column '{$col}' already exists.\n";
        }
    }

    echo "Migration completed successfully!\n";
} catch (Exception $e) {
    die("Migration failed: " . $e->getMessage() . "\n");
}
