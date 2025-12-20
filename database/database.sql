-- ProLedger Database Schema
-- Aplikasi Manajemen Keuangan Kolaboratif untuk Paket Kegiatan

-- Drop tables if exists (for clean setup)
DROP TABLE IF EXISTS transaksi;
DROP TABLE IF EXISTS modal_detail;
DROP TABLE IF EXISTS paket_pemodal;
DROP TABLE IF EXISTS paket_kegiatan;
DROP TABLE IF EXISTS users;

-- 1. Table: users (Pemodal/Investor)
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nama VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    telepon VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_nama (nama)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. Table: admins (System Login)
CREATE TABLE IF NOT EXISTS admins (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default admin (password: password)
INSERT INTO admins (name, email, password) VALUES 
('Administrator', 'admin@proledger.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- 2. Table: paket_kegiatan (Kontrak/Paket)
CREATE TABLE paket_kegiatan (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nama_paket VARCHAR(200) NOT NULL,
    pagu DECIMAL(15,2) NOT NULL,
    rab DECIMAL(15,2) NOT NULL,
    jumlah_pemodal INT NOT NULL DEFAULT 0,
    status ENUM('aktif', 'selesai') DEFAULT 'aktif',
    tanggal_mulai DATE,
    tanggal_selesai DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_status (status),
    INDEX idx_nama_paket (nama_paket)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Table: paket_pemodal (Relasi Many-to-Many)
CREATE TABLE paket_pemodal (
    id INT PRIMARY KEY AUTO_INCREMENT,
    paket_id INT NOT NULL,
    user_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (paket_id) REFERENCES paket_kegiatan(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_paket_user (paket_id, user_id),
    INDEX idx_paket (paket_id),
    INDEX idx_user (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Table: modal_detail (Perhitungan Modal Otomatis)
CREATE TABLE modal_detail (
    id INT PRIMARY KEY AUTO_INCREMENT,
    paket_id INT NOT NULL,
    user_id INT NOT NULL,
    modal_pasal DECIMAL(15,2) NOT NULL COMMENT 'Pagu x 13% / Jumlah Pemodal',
    modal_belanja DECIMAL(15,2) NOT NULL COMMENT 'RAB / Jumlah Pemodal',
    total_modal DECIMAL(15,2) NOT NULL COMMENT 'Modal Pasal + Modal Belanja',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (paket_id) REFERENCES paket_kegiatan(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_modal (paket_id, user_id),
    INDEX idx_paket (paket_id),
    INDEX idx_user (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Table: transaksi (Uang Masuk & Keluar)
CREATE TABLE transaksi (
    id INT PRIMARY KEY AUTO_INCREMENT,
    paket_id INT NOT NULL,
    tanggal DATE NOT NULL,
    tipe ENUM('masuk', 'keluar') NOT NULL,
    kategori VARCHAR(50),
    jumlah DECIMAL(15,2) NOT NULL,
    keterangan TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (paket_id) REFERENCES paket_kegiatan(id) ON DELETE CASCADE,
    INDEX idx_paket (paket_id),
    INDEX idx_tanggal (tanggal),
    INDEX idx_tipe (tipe)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert sample data for testing

-- Sample users (pemodal)
INSERT INTO users (nama, email, telepon) VALUES
('Andi Prasetyo', 'andi@example.com', '081234567890'),
('Budi Santoso', 'budi@example.com', '081234567891'),
('Candra Wijaya', 'candra@example.com', '081234567892'),
('Dewi Lestari', 'dewi@example.com', '081234567893'),
('Eko Nugroho', 'eko@example.com', '081234567894');

-- Sample paket kegiatan
INSERT INTO paket_kegiatan (nama_paket, pagu, rab, jumlah_pemodal, status, tanggal_mulai) VALUES
('Pembangunan Jalan Raya Km 10-15', 1000000000.00, 600000000.00, 5, 'aktif', '2025-01-01'),
('Renovasi Gedung Perkantoran', 500000000.00, 300000000.00, 3, 'aktif', '2025-01-15');

-- Assign pemodal to paket 1 (5 orang)
INSERT INTO paket_pemodal (paket_id, user_id) VALUES
(1, 1), (1, 2), (1, 3), (1, 4), (1, 5);

-- Assign pemodal to paket 2 (3 orang)
INSERT INTO paket_pemodal (paket_id, user_id) VALUES
(2, 1), (2, 2), (2, 3);

-- Calculate and insert modal detail for paket 1
-- Pagu: 1.000.000.000, RAB: 600.000.000, Pemodal: 5
-- Modal Pasal per orang = (1.000.000.000 x 13%) / 5 = 26.000.000
-- Modal Belanja per orang = 600.000.000 / 5 = 120.000.000
-- Total Modal per orang = 146.000.000
INSERT INTO modal_detail (paket_id, user_id, modal_pasal, modal_belanja, total_modal) VALUES
(1, 1, 26000000.00, 120000000.00, 146000000.00),
(1, 2, 26000000.00, 120000000.00, 146000000.00),
(1, 3, 26000000.00, 120000000.00, 146000000.00),
(1, 4, 26000000.00, 120000000.00, 146000000.00),
(1, 5, 26000000.00, 120000000.00, 146000000.00);

-- Calculate and insert modal detail for paket 2
-- Pagu: 500.000.000, RAB: 300.000.000, Pemodal: 3
-- Modal Pasal per orang = (500.000.000 x 13%) / 3 = 21.666.666,67
-- Modal Belanja per orang = 300.000.000 / 3 = 100.000.000
-- Total Modal per orang = 121.666.666,67
INSERT INTO modal_detail (paket_id, user_id, modal_pasal, modal_belanja, total_modal) VALUES
(2, 1, 21666666.67, 100000000.00, 121666666.67),
(2, 2, 21666666.67, 100000000.00, 121666666.67),
(2, 3, 21666666.67, 100000000.00, 121666666.67);

-- Sample transaksi for paket 1
INSERT INTO transaksi (paket_id, tanggal, tipe, kategori, jumlah, keterangan) VALUES
(1, '2025-01-05', 'keluar', 'Material', 150000000.00, 'Pembelian semen dan besi'),
(1, '2025-01-10', 'keluar', 'Upah', 80000000.00, 'Upah pekerja minggu 1'),
(1, '2025-01-15', 'masuk', 'Termin 1', 300000000.00, 'Pembayaran termin pertama'),
(1, '2025-01-20', 'keluar', 'Material', 120000000.00, 'Pembelian aspal'),
(1, '2025-01-25', 'keluar', 'Operasional', 25000000.00, 'Biaya operasional dan transportasi');

-- Sample transaksi for paket 2
INSERT INTO transaksi (paket_id, tanggal, tipe, kategori, jumlah, keterangan) VALUES
(2, '2025-01-16', 'keluar', 'Material', 80000000.00, 'Pembelian cat dan bahan bangunan'),
(2, '2025-01-20', 'masuk', 'Termin 1', 150000000.00, 'Pembayaran termin pertama'),
(2, '2025-01-22', 'keluar', 'Upah', 50000000.00, 'Upah tukang dan pekerja');
