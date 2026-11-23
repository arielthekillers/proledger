<?php
/**
 * Export production database SQL with structure and data
 */
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

$pdo = Database::getInstance()->getConnection();
$tables = [
    'master_provinsi',
    'master_kabupaten',
    'users',
    'skema_potongan',
    'lokasi_penerima',
    'perusahaan',
    'paket_group',
    'paket_kegiatan',
    'paket_items',
    'paket_distribusi',
    'paket_dokumen'
];

$output = "-- ProLedger Production Database Dump\n";
$output .= "-- Generated on " . date('Y-m-d H:i:s') . "\n";
$output .= "-- Target: MySQL / MariaDB (Coolify / Docker / VPS)\n\n";
$output .= "SET FOREIGN_KEY_CHECKS = 0;\n";
$output .= "SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';\n";
$output .= "SET NAMES utf8mb4;\n\n";

foreach ($tables as $table) {
    // Drop Table
    $output .= "-- --------------------------------------------------------\n";
    $output .= "-- Table structure for `$table`\n";
    $output .= "-- --------------------------------------------------------\n";
    $output .= "DROP TABLE IF EXISTS `$table`;\n";
    
    // Create Table
    $row = $pdo->query("SHOW CREATE TABLE `$table`")->fetch(PDO::FETCH_NUM);
    $createSql = $row[1];
    $output .= $createSql . ";\n\n";
    
    // Dump Data
    $rows = $pdo->query("SELECT * FROM `$table`")->fetchAll(PDO::FETCH_ASSOC);
    if (!empty($rows)) {
        $output .= "-- Dumping data for table `$table`\n";
        
        $cols = array_keys($rows[0]);
        $colNames = implode('`, `', $cols);
        
        $valueRows = [];
        foreach ($rows as $r) {
            $vals = [];
            foreach ($r as $val) {
                if ($val === null) {
                    $vals[] = "NULL";
                } else {
                    $vals[] = $pdo->quote($val);
                }
            }
            $valueRows[] = "(" . implode(', ', $vals) . ")";
        }
        
        // Split into chunks of 50 for clean SQL
        $chunks = array_chunk($valueRows, 50);
        foreach ($chunks as $chunk) {
            $output .= "INSERT INTO `$table` (`$colNames`) VALUES\n" . implode(",\n", $chunk) . ";\n";
        }
        $output .= "\n";
    }
}

$output .= "SET FOREIGN_KEY_CHECKS = 1;\n";

$targetPath = __DIR__ . '/../database/proledger_production.sql';
file_put_contents($targetPath, $output);
echo "Database successfully exported to: database/proledger_production.sql (" . strlen($output) . " bytes)\n";
