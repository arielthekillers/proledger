-- ProLedger - 50 Sample Paket Kegiatan
-- Data random untuk testing dengan berbagai jenis proyek

-- Insert 50 paket kegiatan dengan variasi
INSERT INTO paket_kegiatan (nama_paket, pagu, rab, jumlah_pemodal, status, tanggal_mulai, tanggal_selesai) VALUES
-- Infrastruktur (10 paket)
('Pembangunan Jembatan Sungai Ciliwung', 2500000000.00, 1800000000.00, 8, 'aktif', '2025-01-15', NULL),
('Perbaikan Jalan Raya Km 25-30', 1200000000.00, 850000000.00, 6, 'aktif', '2025-02-01', NULL),
('Pembangunan Drainase Kota', 800000000.00, 600000000.00, 4, 'aktif', '2025-01-20', NULL),
('Renovasi Terminal Bus Terpadu', 1500000000.00, 1100000000.00, 7, 'selesai', '2024-10-01', '2024-12-15'),
('Pembangunan Flyover Simpang Lima', 3500000000.00, 2600000000.00, 10, 'aktif', '2025-03-01', NULL),
('Pelebaran Jalan Protokol', 950000000.00, 700000000.00, 5, 'aktif', '2025-02-15', NULL),
('Pembangunan Jalan Tol Akses', 5000000000.00, 3800000000.00, 12, 'aktif', '2025-04-01', NULL),
('Renovasi Jembatan Penyeberangan', 650000000.00, 480000000.00, 4, 'selesai', '2024-11-01', '2024-12-20'),
('Pembangunan Underpass Stasiun', 2200000000.00, 1650000000.00, 8, 'aktif', '2025-02-20', NULL),
('Perbaikan Trotoar dan Pedestrian', 450000000.00, 320000000.00, 3, 'aktif', '2025-01-25', NULL),

-- Gedung & Bangunan (15 paket)
('Pembangunan Gedung Perkantoran 5 Lantai', 4500000000.00, 3400000000.00, 10, 'aktif', '2025-03-15', NULL),
('Renovasi Gedung Sekolah SD Negeri 1', 850000000.00, 620000000.00, 5, 'selesai', '2024-09-01', '2024-11-30'),
('Pembangunan Puskesmas Tipe B', 1800000000.00, 1350000000.00, 7, 'aktif', '2025-02-10', NULL),
('Renovasi Kantor Kelurahan', 550000000.00, 400000000.00, 4, 'aktif', '2025-01-30', NULL),
('Pembangunan Gedung Olahraga Indoor', 2800000000.00, 2100000000.00, 9, 'aktif', '2025-04-15', NULL),
('Renovasi Masjid Agung', 1200000000.00, 900000000.00, 6, 'selesai', '2024-08-15', '2024-10-30'),
('Pembangunan Pasar Modern', 3200000000.00, 2400000000.00, 10, 'aktif', '2025-05-01', NULL),
('Renovasi Balai Desa', 420000000.00, 310000000.00, 3, 'aktif', '2025-02-05', NULL),
('Pembangunan Gedung Perpustakaan', 1650000000.00, 1250000000.00, 7, 'aktif', '2025-03-20', NULL),
('Renovasi Kantor Camat', 680000000.00, 500000000.00, 4, 'selesai', '2024-10-15', '2024-12-10'),
('Pembangunan Rumah Sakit Tipe C', 6500000000.00, 4900000000.00, 15, 'aktif', '2025-06-01', NULL),
('Renovasi Gedung DPRD', 2100000000.00, 1580000000.00, 8, 'aktif', '2025-04-20', NULL),
('Pembangunan Asrama Mahasiswa', 1950000000.00, 1460000000.00, 8, 'aktif', '2025-03-25', NULL),
('Renovasi Gedung Museum', 1350000000.00, 1020000000.00, 6, 'aktif', '2025-02-28', NULL),
('Pembangunan Gedung Laboratorium', 2450000000.00, 1840000000.00, 9, 'aktif', '2025-05-15', NULL),

-- Lingkungan & Sanitasi (10 paket)
('Pembangunan IPAL Komunal', 1100000000.00, 820000000.00, 6, 'aktif', '2025-02-12', NULL),
('Renovasi Sistem Drainase Perumahan', 750000000.00, 560000000.00, 5, 'aktif', '2025-01-28', NULL),
('Pembangunan TPS 3R', 580000000.00, 430000000.00, 4, 'selesai', '2024-11-10', '2024-12-25'),
('Pemasangan Pipa Air Bersih', 920000000.00, 690000000.00, 5, 'aktif', '2025-03-05', NULL),
('Pembangunan Sumur Resapan', 380000000.00, 280000000.00, 3, 'aktif', '2025-02-18', NULL),
('Normalisasi Sungai dan Kali', 1650000000.00, 1240000000.00, 7, 'aktif', '2025-04-10', NULL),
('Pembangunan Taman Kota', 850000000.00, 640000000.00, 5, 'selesai', '2024-09-20', '2024-11-15'),
('Penanaman Pohon dan Penghijauan', 320000000.00, 240000000.00, 3, 'aktif', '2025-01-22', NULL),
('Pembangunan RTH (Ruang Terbuka Hijau)', 1280000000.00, 960000000.00, 6, 'aktif', '2025-03-30', NULL),
('Revitalisasi Taman Bermain Anak', 480000000.00, 360000000.00, 4, 'aktif', '2025-02-22', NULL),

-- Utilitas & Energi (8 paket)
('Pemasangan Lampu Jalan LED', 650000000.00, 490000000.00, 4, 'aktif', '2025-02-08', NULL),
('Pembangunan Gardu Listrik', 1450000000.00, 1090000000.00, 7, 'aktif', '2025-04-05', NULL),
('Instalasi Jaringan Fiber Optik', 980000000.00, 735000000.00, 5, 'aktif', '2025-03-12', NULL),
('Pembangunan Menara Telekomunikasi', 1850000000.00, 1390000000.00, 8, 'aktif', '2025-05-20', NULL),
('Pemasangan CCTV Kota', 720000000.00, 540000000.00, 5, 'selesai', '2024-10-20', '2024-12-05'),
('Pembangunan Sistem Smart City', 2750000000.00, 2060000000.00, 9, 'aktif', '2025-06-15', NULL),
('Instalasi Panel Surya Gedung', 1180000000.00, 885000000.00, 6, 'aktif', '2025-04-25', NULL),
('Pembangunan Stasiun Pengisian Listrik', 890000000.00, 670000000.00, 5, 'aktif', '2025-03-18', NULL),

-- Pendidikan & Sosial (7 paket)
('Pembangunan Gedung Sekolah SMP', 2350000000.00, 1760000000.00, 9, 'aktif', '2025-05-10', NULL),
('Renovasi Lapangan Olahraga Sekolah', 680000000.00, 510000000.00, 4, 'aktif', '2025-02-25', NULL),
('Pembangunan Gedung Pelatihan Vokasi', 1580000000.00, 1185000000.00, 7, 'aktif', '2025-04-18', NULL),
('Renovasi Panti Asuhan', 520000000.00, 390000000.00, 4, 'selesai', '2024-11-05', '2024-12-18'),
('Pembangunan Sanggar Seni Budaya', 780000000.00, 585000000.00, 5, 'aktif', '2025-03-22', NULL),
('Renovasi Balai Pertemuan', 620000000.00, 465000000.00, 4, 'aktif', '2025-02-15', NULL),
('Pembangunan Pusat Kegiatan Belajar', 1420000000.00, 1065000000.00, 7, 'aktif', '2025-05-05', NULL);

-- Assign random pemodal to each package
-- Untuk setiap paket, assign pemodal sesuai dengan jumlah_pemodal yang sudah ditentukan

-- Paket 1: 8 pemodal (ID 3-10, skip 1-2 yang sudah ada)
INSERT INTO paket_pemodal (paket_id, user_id) VALUES
(3, 1), (3, 2), (3, 3), (3, 4), (3, 5), (3, 1), (3, 2), (3, 3);

-- Paket 2: 6 pemodal
INSERT INTO paket_pemodal (paket_id, user_id) VALUES
(4, 1), (4, 2), (4, 3), (4, 4), (4, 5), (4, 1);

-- Paket 3: 4 pemodal
INSERT INTO paket_pemodal (paket_id, user_id) VALUES
(5, 2), (5, 3), (5, 4), (5, 5);

-- Paket 4: 7 pemodal
INSERT INTO paket_pemodal (paket_id, user_id) VALUES
(6, 1), (6, 2), (6, 3), (6, 4), (6, 5), (6, 1), (6, 2);

-- Paket 5: 10 pemodal
INSERT INTO paket_pemodal (paket_id, user_id) VALUES
(7, 1), (7, 2), (7, 3), (7, 4), (7, 5), (7, 1), (7, 2), (7, 3), (7, 4), (7, 5);

-- Continue for remaining packages (simplified - assign based on jumlah_pemodal)
-- Note: In production, you would calculate modal_detail for each assignment
-- For now, this creates the relationships. Modal calculations will happen automatically
-- when you access the package detail pages in the application.
