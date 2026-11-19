<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

try {
    $db = Database::getInstance()->getConnection();
    echo "Starting Perusahaan user_id migration...\n";

    // 1. Check if user_id exists in perusahaan
    $stmt = $db->query("SHOW COLUMNS FROM perusahaan LIKE 'user_id'");
    if ($stmt->rowCount() == 0) {
        $db->exec("ALTER TABLE perusahaan ADD COLUMN user_id INT NULL AFTER id");
        $db->exec("CREATE INDEX idx_perusahaan_user ON perusahaan(user_id)");
        echo "Column 'user_id' added to 'perusahaan'.\n";
    } else {
        echo "Column 'user_id' already exists in 'perusahaan'.\n";
    }

    // 2. Link existing companies by email match or default admin
    $companies = $db->query("SELECT id, email FROM perusahaan WHERE user_id IS NULL")->fetchAll();
    foreach ($companies as $comp) {
        if (!empty($comp['email'])) {
            $stmtUser = $db->prepare("SELECT id FROM admins WHERE email = ?");
            $stmtUser->execute([$comp['email']]);
            $u = $stmtUser->fetch();
            if ($u) {
                $db->prepare("UPDATE perusahaan SET user_id = ? WHERE id = ?")->execute([$u['id'], $comp['id']]);
                echo "Linked perusahaan #{$comp['id']} to user #{$u['id']}.\n";
                continue;
            }
        }
        // Fallback to first admin
        $firstAdmin = $db->query("SELECT id FROM admins ORDER BY id ASC LIMIT 1")->fetchColumn();
        if ($firstAdmin) {
            $db->prepare("UPDATE perusahaan SET user_id = ? WHERE id = ?")->execute([$firstAdmin, $comp['id']]);
            echo "Linked perusahaan #{$comp['id']} to default admin #{$firstAdmin}.\n";
        }
    }

    echo "Migration completed successfully!\n";
} catch (Exception $e) {
    die("Migration failed: " . $e->getMessage() . "\n");
}
