-- ProLedger Production Database Dump
-- Generated on 2026-11-19 22:27:42
-- Target: MySQL / MariaDB (Coolify / Docker / VPS)

SET FOREIGN_KEY_CHECKS = 0;
SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';
SET NAMES utf8mb4;

-- --------------------------------------------------------
-- Table structure for `master_provinsi`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `master_provinsi`;
CREATE TABLE `master_provinsi` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kode` varchar(10) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `kode` (`kode`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `master_provinsi`
INSERT INTO `master_provinsi` (`id`, `kode`, `nama`, `created_at`) VALUES
('1', '64', 'Kalimantan Timur', '2026-11-18 19:12:45');

-- --------------------------------------------------------
-- Table structure for `master_kabupaten`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `master_kabupaten`;
CREATE TABLE `master_kabupaten` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `provinsi_id` int(11) NOT NULL,
  `kode` varchar(10) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `tipe` enum('Kabupaten','Kota') NOT NULL DEFAULT 'Kabupaten',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `kode` (`kode`),
  KEY `idx_provinsi` (`provinsi_id`),
  CONSTRAINT `fk_kabupaten_provinsi` FOREIGN KEY (`provinsi_id`) REFERENCES `master_provinsi` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `master_kabupaten`
INSERT INTO `master_kabupaten` (`id`, `provinsi_id`, `kode`, `nama`, `tipe`, `created_at`) VALUES
('1', '1', '64.01', 'Kabupaten Paser', 'Kabupaten', '2026-11-18 19:12:45'),
('2', '1', '64.02', 'Kabupaten Kutai Kartanegara', 'Kabupaten', '2026-11-18 19:12:45'),
('3', '1', '64.03', 'Kabupaten Berau', 'Kabupaten', '2026-11-18 19:12:45'),
('4', '1', '64.07', 'Kabupaten Kutai Barat', 'Kabupaten', '2026-11-18 19:12:45'),
('5', '1', '64.08', 'Kabupaten Kutai Timur', 'Kabupaten', '2026-11-18 19:12:45'),
('6', '1', '64.09', 'Kabupaten Penajam Paser Utara', 'Kabupaten', '2026-11-18 19:12:45'),
('7', '1', '64.11', 'Kabupaten Mahakam Ulu', 'Kabupaten', '2026-11-18 19:12:45'),
('8', '1', '64.71', 'Kota Balikpapan', 'Kota', '2026-11-18 19:12:45'),
('9', '1', '64.72', 'Kota Samarinda', 'Kota', '2026-11-18 19:12:45'),
('10', '1', '64.74', 'Kota Bontang', 'Kota', '2026-11-18 19:12:45');

-- --------------------------------------------------------
-- Table structure for `admins`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `admins`;
CREATE TABLE `admins` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `role` enum('admin','pemodal','perusahaan','user') NOT NULL DEFAULT 'user',
  `status` enum('active','pending','rejected','inactive') NOT NULL DEFAULT 'active',
  `reference_id` int(11) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `idx_role_ref` (`role`,`reference_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `admins`
INSERT INTO `admins` (`id`, `name`, `email`, `phone`, `role`, `status`, `reference_id`, `password`, `created_at`, `updated_at`) VALUES
('1', 'Administrator', 'admin@sintesacorp.id', NULL, 'admin', 'active', NULL, '$2y$10$rv.jISfmbTCMEpRwL8BtA.rW1CImpYc5pi0hbVnxi5LGOAq5.YoY6', '2025-12-20 14:08:49', '2026-11-17 18:18:37'),
('4', 'Ahmad Darul Arqam', 'ahmaddarularqam@gmail.com', '081359774765', 'user', 'active', NULL, '$2y$10$o0n9SIp5K5IzuIk0GQG3LOB5VFB0WMDgHjExUzAmyjPG.8CQieOTC', '2026-11-17 18:26:37', '2026-11-17 23:47:25');

-- --------------------------------------------------------
-- Table structure for `skema_potongan`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `skema_potongan`;
CREATE TABLE `skema_potongan` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `perusahaan_id` int(11) DEFAULT NULL,
  `nama_skema` varchar(100) NOT NULL,
  `ada_pasal` tinyint(1) DEFAULT 1,
  `persen_pasal` decimal(5,2) DEFAULT 13.00,
  `ada_admin` tinyint(1) DEFAULT 1,
  `persen_admin` decimal(5,2) DEFAULT 2.00,
  `tipe_potongan` enum('dpp','kontrak') NOT NULL DEFAULT 'dpp',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_perusahaan` (`perusahaan_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `skema_potongan`
INSERT INTO `skema_potongan` (`id`, `perusahaan_id`, `nama_skema`, `ada_pasal`, `persen_pasal`, `ada_admin`, `persen_admin`, `tipe_potongan`, `created_at`, `updated_at`) VALUES
('4', '4', 'Skema 1', '1', '13.00', '1', '2.00', 'dpp', '2026-11-18 00:24:37', '2026-11-18 00:24:37'),
('5', '4', 'Skema 2', '1', '10.00', '1', '0.00', 'dpp', '2026-11-19 23:04:37', '2026-11-19 23:04:37');

-- --------------------------------------------------------
-- Table structure for `lokasi_penerima`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `lokasi_penerima`;
CREATE TABLE `lokasi_penerima` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama_lokasi` varchar(255) NOT NULL,
  `kategori` varchar(100) DEFAULT 'Sekolah',
  `nama_pic` varchar(255) DEFAULT NULL,
  `jabatan_pic` varchar(100) DEFAULT 'Kepala Sekolah',
  `no_kontak` varchar(100) DEFAULT NULL,
  `alamat_lengkap` text DEFAULT NULL,
  `kab_kota` varchar(100) DEFAULT NULL,
  `kabupaten_id` int(11) DEFAULT NULL,
  `no_rekening` varchar(100) DEFAULT NULL,
  `nama_rekening` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_nama_lokasi` (`nama_lokasi`),
  KEY `idx_kategori` (`kategori`),
  KEY `idx_kab_kota` (`kab_kota`),
  KEY `idx_kabupaten_id` (`kabupaten_id`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `lokasi_penerima`
INSERT INTO `lokasi_penerima` (`id`, `nama_lokasi`, `kategori`, `nama_pic`, `jabatan_pic`, `no_kontak`, `alamat_lengkap`, `kab_kota`, `kabupaten_id`, `no_rekening`, `nama_rekening`, `created_at`, `updated_at`) VALUES
('1', 'SMKN 1 BONTANG', 'Sekolah', 'Muhammad Sultan, S.Ag., M.Pd.I', 'Kepala Sekolah', '081347071619', 'Jl. Ciptomangunkusumo No. 2, Kel. Gn Elai Kec. Bontang Utara', 'Kota Bontang', '10', NULL, NULL, '2026-11-18 16:05:22', '2026-11-18 20:33:13'),
('2', 'SMKN 2 BONTANG', 'Sekolah', 'Nurwahidah, S.Pd, M.Pd', 'Kepala Sekolah', '081258348205', 'Jl. Cumi - Cumi No. 1 Kel. Tanjung Laut Indah Kec. Bontang Selatan', 'Kota Bontang', '10', NULL, NULL, '2026-11-18 16:05:22', '2026-11-18 20:33:13'),
('3', 'SMKN 3 BONTANG', 'Sekolah', 'Arif Munawar, S.Pd.I., M.Pd', 'Kepala Sekolah', '081250603288', 'Jl. Palembang RT 16 Kel. Telihan Kec. Bontang Barat', 'Kota Bontang', '10', NULL, NULL, '2026-11-18 16:05:22', '2026-11-18 20:33:13'),
('4', 'SMKN 4 BONTANG', 'Sekolah', 'Muhammad Yunani, S.Pd', 'Kepala Sekolah', '085252262833', 'Jalan Karya Bakti Kelurahan Bontang Lestari Kecamatan Bontang', 'Kota Bontang', '10', NULL, NULL, '2026-11-18 16:05:22', '2026-11-18 20:33:13'),
('5', 'SMKN 1 SANGATTA UTARA', 'Sekolah', 'Abu Bakar, S.Pd., M.M', 'Kepala Sekolah', '081255026665', 'Jl. Majay Sangatta Utara Kec. Sangatta Utara', 'Kabupaten Kutai Timur', '5', NULL, NULL, '2026-11-18 16:05:22', '2026-11-18 20:33:13'),
('6', 'SMKN 2 SANGATTA UTARA', 'Sekolah', 'Mustari, S.Pd., M.Pd', 'Kepala Sekolah', '082151189131', 'Jl. Padat Karya RT. 32 Sangatta Utara, Kec. Sangatta Utara', 'Kabupaten Kutai Timur', '5', NULL, NULL, '2026-11-18 16:05:22', '2026-11-18 20:33:13'),
('7', 'SMKN 1 KARANGAN', 'Sekolah', 'Ibus I., S.Sos.I., M.Pd', 'Kepala Sekolah', '085250994556', 'Jl. Ki Hajar Dewantara km.4 Desa Karangan Seberang', 'Kabupaten Kutai Timur', '5', NULL, NULL, '2026-11-18 16:05:22', '2026-11-18 20:33:13'),
('8', 'SMKN 1 BENGALON', 'Sekolah', 'A. Asriani, S.Pd., M.Pd', 'Kepala Sekolah', '085252390787', 'Jl. Sulawesi Desa Sepaso Barat Kec. Bengalon', 'Kabupaten Kutai Timur', '5', NULL, NULL, '2026-11-18 16:05:22', '2026-11-18 20:33:13'),
('9', 'SMKN 2 BENGALON', 'Sekolah', 'Patminingsih, S.Pd', 'Kepala Sekolah', '085250034350', 'Jl. Poros Sangatta -Ma. Wahau Km 106 Desa Tepian Indah', 'Kabupaten Kutai Timur', '5', NULL, NULL, '2026-11-18 16:05:22', '2026-11-18 20:33:13'),
('10', 'SMKN 1 MUARA BENGKAL', 'Sekolah', 'ADITYA PRATAMA PUTRA, M. Pd', 'Kepala Sekolah', '082151708573', 'Jl. Abdul Haib, RT.8, Desa Muara Bengkal Ulu', 'Kabupaten Kutai Timur', '5', NULL, NULL, '2026-11-18 16:05:22', '2026-11-18 20:33:13'),
('11', 'SMKN 1 RANTAU PULUNG', 'Sekolah', 'Dr. Khusnul Wardan, M. Pd', 'Kepala Sekolah', '085250058164', 'Jl. Pendidikan Rt 05 Rw 02 Ds. Margomulyo', 'Kabupaten Kutai Timur', '5', NULL, NULL, '2026-11-18 16:05:22', '2026-11-18 20:33:13'),
('12', 'SMKN 1 KALIORANG', 'Sekolah', 'Andi Hasniah Tenri Ola, S.Si., M.Si', 'Kepala Sekolah', '082190295817', 'Jalan Ery Soepardjan RT 12 / RW 01 Desa Bukit Makmur', 'Kabupaten Kutai Timur', '5', NULL, NULL, '2026-11-18 16:05:22', '2026-11-18 20:33:13'),
('13', 'SMKN 1 SANGKULIRANG', 'Sekolah', 'Panji Ongko Pramono, S.Pd., M.Pd', 'Kepala Sekolah', '085393304366', 'Jl. Mulawarman Desa RT III Desa Kerayaan Bual-Bual', 'Kabupaten Kutai Timur', '5', NULL, NULL, '2026-11-18 16:05:22', '2026-11-18 20:33:13'),
('14', 'SMKN 2 SANGKULIRANG', 'Sekolah', 'Maryatul Isnaaniah, S.P', 'Kepala Sekolah', '081346530146', 'Jl. Ahmad Yani RT 4, Desa Mandu Pantai Sejahtera', 'Kabupaten Kutai Timur', '5', NULL, NULL, '2026-11-18 16:05:22', '2026-11-18 20:33:13'),
('15', 'SMKN 1 TELEN', 'Sekolah', 'Eko Hadi Purwanto, S. Hut, M. M, M. Pd.', 'Kepala Sekolah', '081215138666', 'Jl. Poros Desa Muara Pantun Kecamatan Telen', 'Kabupaten Kutai Timur', '5', NULL, NULL, '2026-11-18 16:05:22', '2026-11-18 20:33:13'),
('16', 'SMKN 1 MUARA WAHAU', 'Sekolah', 'Avendi Perangin Angin, S.Pd., M.Pd', 'Kepala Sekolah', '082273369455', 'Jl. poros Muara Wahau-Sangatta, Desa karya bakti', 'Kabupaten Kutai Timur', '5', NULL, NULL, '2026-11-18 16:05:22', '2026-11-18 20:33:13'),
('17', 'SMKN 1 KONGBENG', 'Sekolah', 'Bambang Setiono, S.Pd.', 'Kepala Sekolah', '082353936330', 'Jalan Hamengkubuwono Desa Marga Mulia Kec. Kongbeng', 'Kabupaten Kutai Timur', '5', NULL, NULL, '2026-11-18 16:05:22', '2026-11-18 20:33:13'),
('18', 'SMAN 2 BONTANG', 'Sekolah', 'Kepala Sekolah SMAN 2 Bontang', 'Kepala Sekolah', '081234567890', 'Bontang, Kalimantan Timur', 'Kota Bontang', '10', NULL, NULL, '2026-11-18 16:05:22', '2026-11-18 20:33:13'),
('19', 'SMAN 1 SANDARAN', 'Sekolah', 'Kepala Sekolah SMAN 1 Sandaran', 'Kepala Sekolah', '081234567891', 'Kecamatan Sandaran, Kutai Timur', 'Kabupaten Kutai Timur', '5', NULL, NULL, '2026-11-18 16:05:22', '2026-11-18 20:33:13'),
('20', 'SMAN 1 LONG MESANGAT', 'Sekolah', 'Kepala Sekolah SMAN 1 Long Mesangat', 'Kepala Sekolah', '081234567892', 'Kecamatan Long Mesangat, Kutai Timur', 'Kabupaten Kutai Timur', '5', NULL, NULL, '2026-11-18 16:05:22', '2026-11-18 20:33:13'),
('21', 'SMAN 1 MUARA ANCALONG', 'Sekolah', 'Kepala Sekolah SMAN 1 Muara Ancalong', 'Kepala Sekolah', '081234567893', 'Kecamatan Muara Ancalong, Kutai Timur', 'Kabupaten Kutai Timur', '5', NULL, NULL, '2026-11-18 16:05:22', '2026-11-18 20:33:13'),
('22', 'SMAN 1 KARANGAN', 'Sekolah', 'Kepala Sekolah SMAN 1 Karangan', 'Kepala Sekolah', '081234567894', 'Kecamatan Karangan, Kutai Timur', 'Kabupaten Kutai Timur', '5', NULL, NULL, '2026-11-18 16:05:22', '2026-11-18 20:33:13'),
('23', 'Kantor Dinas Pendidikan & Kebudayaan', 'Instansi / Dinas', 'Kepala Dinas / Pengurus Barang', 'Kepala Sekolah', '081155001122', 'Gedung Kantor Dinas Pendidikan', 'Kota Samarinda', '9', NULL, NULL, '2026-11-18 16:05:22', '2026-11-18 20:33:13'),
('24', 'SMKN 1 Bontang Barat', 'Sekolah', NULL, 'Kepala Sekolah', NULL, NULL, 'Kota Bontang', '10', NULL, NULL, '2026-11-18 20:45:57', '2026-11-18 20:45:57');

-- --------------------------------------------------------
-- Table structure for `perusahaan`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `perusahaan`;
CREATE TABLE `perusahaan` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `nama` varchar(255) NOT NULL,
  `nama_direktur` varchar(255) DEFAULT NULL,
  `jabatan_direktur` varchar(100) DEFAULT 'Direktur',
  `email` varchar(255) DEFAULT NULL,
  `telepon` varchar(50) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `kopsurat` varchar(255) DEFAULT NULL,
  `stempel_ttd` varchar(255) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_perusahaan_user` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table `perusahaan`
INSERT INTO `perusahaan` (`id`, `user_id`, `nama`, `nama_direktur`, `jabatan_direktur`, `email`, `telepon`, `alamat`, `logo`, `avatar`, `kopsurat`, `stempel_ttd`, `website`, `keterangan`, `created_at`, `updated_at`) VALUES
('1', '2', 'KOLEGA JAYA', NULL, 'Direktur', 'arielthekillers@gmail.com', '081359774765', 'Jl. Cut Nyak Dien', NULL, NULL, NULL, NULL, NULL, NULL, '2025-12-20 13:14:21', '2026-11-17 18:37:44'),
('4', '4', 'test', NULL, 'Direktur', 'test@test.com', '0813456789', 'test', 'perusahaan_logo_1794916084_7c55c3035492.png', 'perusahaan_avatar_1794919581_89743d12d225.jpg', 'perusahaan_kopsurat_1794916120_0b0536244e7f.png', NULL, NULL, NULL, '2026-11-17 19:09:59', '2026-11-17 20:46:21'),
('5', NULL, 'CV. PIJAR TIMUR', NULL, 'Direktur', 'cvpijartimur@example.com', '08123456789', 'Alamat Perusahaan', NULL, NULL, NULL, NULL, NULL, NULL, '2026-11-17 20:54:09', '2026-11-17 20:54:09'),
('6', NULL, 'CV. KARSA KREASI BERDIKARI', 'Muhammad Rifki', 'Direktur', 'cvkarsakreasiberdikari@example.com', '08123456789', 'Alamat Perusahaan', NULL, NULL, NULL, NULL, NULL, NULL, '2026-11-17 20:54:09', '2026-11-18 17:30:51'),
('7', NULL, 'PT. PUTRA KALTIM BERSINERGI', NULL, 'Direktur', 'ptputrakaltimbersinergi@example.com', '08123456789', 'Alamat Perusahaan', NULL, NULL, NULL, NULL, NULL, NULL, '2026-11-17 20:54:09', '2026-11-17 20:54:09'),
('8', NULL, 'CV. KAYA PACE', NULL, 'Direktur', 'cvkayapace@example.com', '08123456789', 'Alamat Perusahaan', NULL, NULL, NULL, NULL, NULL, NULL, '2026-11-17 20:54:09', '2026-11-17 20:54:09'),
('9', NULL, 'CV. ARYA TIKVAH', NULL, 'Direktur', 'cvaryatikvah@example.com', '08123456789', 'Alamat Perusahaan', NULL, NULL, NULL, NULL, NULL, NULL, '2026-11-17 20:54:09', '2026-11-17 20:54:09');

-- --------------------------------------------------------
-- Table structure for `paket_group`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `paket_group`;
CREATE TABLE `paket_group` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `perusahaan_id` int(11) DEFAULT NULL,
  `nama_group` varchar(255) NOT NULL,
  `kode_group` varchar(50) DEFAULT NULL,
  `deskripsi` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_perusahaan` (`perusahaan_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `paket_group`
INSERT INTO `paket_group` (`id`, `perusahaan_id`, `nama_group`, `kode_group`, `deskripsi`, `created_at`, `updated_at`) VALUES
('1', NULL, 'Paket Dinas Pendidikan 2025', 'GRP-DIKNAS-2025', 'Pengadaan sarana dan prasarana sekolah tahun anggaran 2025', '2026-11-17 20:07:14', '2026-11-17 20:07:14');

-- --------------------------------------------------------
-- Table structure for `paket_kegiatan`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `paket_kegiatan`;
CREATE TABLE `paket_kegiatan` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kode_paket` varchar(10) DEFAULT NULL,
  `nama_paket` varchar(200) NOT NULL,
  `nomor_sp` varchar(100) DEFAULT NULL,
  `kab_kota` varchar(100) DEFAULT NULL,
  `perusahaan_id` int(11) DEFAULT NULL,
  `group_id` int(11) DEFAULT NULL,
  `skema_id` int(11) DEFAULT NULL,
  `perusahaan` varchar(255) DEFAULT NULL,
  `penanggung_jawab` varchar(100) DEFAULT NULL,
  `pagu` decimal(15,2) NOT NULL,
  `pagu_nego` decimal(15,2) DEFAULT NULL,
  `dpp` decimal(15,2) DEFAULT NULL,
  `persen_pasal` decimal(5,2) NOT NULL DEFAULT 13.00,
  `pasal_13` decimal(15,2) DEFAULT NULL,
  `persen_admin` decimal(5,2) NOT NULL DEFAULT 2.00,
  `admin_2` decimal(15,2) DEFAULT NULL,
  `total_pasal` decimal(15,2) DEFAULT NULL,
  `tipe_potongan` enum('dpp','kontrak') NOT NULL DEFAULT 'dpp',
  `setor_awal` decimal(15,2) DEFAULT NULL,
  `selisih` decimal(15,2) DEFAULT NULL,
  `rab` decimal(15,2) DEFAULT 0.00,
  `status` varchar(50) NOT NULL DEFAULT 'Proses',
  `progres_barang` varchar(50) NOT NULL DEFAULT 'Belum',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `kode_paket` (`kode_paket`),
  KEY `idx_status` (`status`),
  KEY `idx_nama_paket` (`nama_paket`),
  KEY `idx_group_id` (`group_id`),
  KEY `idx_perusahaan_id` (`perusahaan_id`),
  KEY `idx_skema_id` (`skema_id`)
) ENGINE=InnoDB AUTO_INCREMENT=64 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `paket_kegiatan`
INSERT INTO `paket_kegiatan` (`id`, `kode_paket`, `nama_paket`, `nomor_sp`, `kab_kota`, `perusahaan_id`, `group_id`, `skema_id`, `perusahaan`, `penanggung_jawab`, `pagu`, `pagu_nego`, `dpp`, `persen_pasal`, `pasal_13`, `persen_admin`, `admin_2`, `total_pasal`, `tipe_potongan`, `setor_awal`, `selisih`, `rab`, `status`, `progres_barang`, `created_at`, `updated_at`) VALUES
('62', 'XW9J5', 'Belanja Modal Alat Pendukung Pencarian', 'EP-01M1ZS80DH32WZR9NZ8JCWCA44', NULL, '4', '1', '4', 'test', NULL, '15000000.00', NULL, '13513514.00', '13.00', '1756757.00', '2.00', '270270.00', '2027027.00', 'dpp', NULL, NULL, '0.00', 'Proses', 'Belanja', '2026-11-18 00:26:15', '2026-11-19 23:06:28');

-- --------------------------------------------------------
-- Table structure for `paket_items`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `paket_items`;
CREATE TABLE `paket_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `paket_id` int(11) NOT NULL,
  `komponen` varchar(255) NOT NULL,
  `spesifikasi` text DEFAULT NULL,
  `satuan` varchar(50) DEFAULT 'Unit',
  `jumlah` decimal(12,2) NOT NULL DEFAULT 1.00,
  `harga_satuan` decimal(15,2) NOT NULL DEFAULT 0.00,
  `total_harga` decimal(15,2) NOT NULL DEFAULT 0.00,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_paket_id` (`paket_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `paket_items`
INSERT INTO `paket_items` (`id`, `paket_id`, `komponen`, `spesifikasi`, `satuan`, `jumlah`, `harga_satuan`, `total_harga`, `created_at`, `updated_at`) VALUES
('1', '62', 'Tenda Lipat', 'Uk. 3x3 (setara), Rangka besi hollow, Tanpa dinding', 'Unit', '4.00', '1689905.00', '6759620.00', '2026-11-18 15:47:41', '2026-11-18 15:47:41'),
('2', '62', 'Tenda Camping', 'Double layer dan bahan waterproof , Kapasitas 4-5 Orang', 'Unit', '4.00', '3167494.00', '12669976.00', '2026-11-18 15:47:41', '2026-11-18 15:47:41'),
('3', '62', 'Tenda 4x6', 'Tenda 4x6', 'Buah', '6.00', '5302453.00', '31814718.00', '2026-11-18 15:47:41', '2026-11-18 15:47:41');

-- --------------------------------------------------------
-- Table structure for `paket_distribusi`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `paket_distribusi`;
CREATE TABLE `paket_distribusi` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `paket_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `lokasi_id` int(11) DEFAULT NULL,
  `nama_tujuan` varchar(255) NOT NULL,
  `kategori` varchar(100) DEFAULT 'Sekolah',
  `jumlah` decimal(12,2) NOT NULL DEFAULT 1.00,
  `rute_pengiriman` varchar(50) DEFAULT 'kantor',
  `status_pemesanan` varchar(50) DEFAULT 'Belum Dipesan',
  `tanggal_pesan` date DEFAULT NULL,
  `link_pembelian` text DEFAULT NULL,
  `kurir_ekspedisi` varchar(100) DEFAULT NULL,
  `nama_pic` varchar(255) DEFAULT NULL,
  `no_kontak` varchar(100) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `kab_kota` varchar(100) DEFAULT NULL,
  `status` varchar(50) DEFAULT 'Belum Dikirim',
  `status_penerimaan` varchar(50) DEFAULT 'Menunggu Barang',
  `tanggal_kirim` date DEFAULT NULL,
  `tanggal_terima` date DEFAULT NULL,
  `no_resi` varchar(100) DEFAULT NULL,
  `catatan` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_distribusi_paket` (`paket_id`),
  KEY `idx_distribusi_item` (`item_id`),
  KEY `idx_distribusi_lokasi` (`lokasi_id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `paket_distribusi`
INSERT INTO `paket_distribusi` (`id`, `paket_id`, `item_id`, `lokasi_id`, `nama_tujuan`, `kategori`, `jumlah`, `rute_pengiriman`, `status_pemesanan`, `tanggal_pesan`, `link_pembelian`, `kurir_ekspedisi`, `nama_pic`, `no_kontak`, `alamat`, `kab_kota`, `status`, `status_penerimaan`, `tanggal_kirim`, `tanggal_terima`, `no_resi`, `catatan`, `created_at`, `updated_at`) VALUES
('1', '62', '1', NULL, 'SMAN 2 BONTANG', 'Sekolah', '4.00', 'kantor', 'Belum Dipesan', NULL, NULL, NULL, NULL, NULL, NULL, 'Kota Bontang', 'Belum Dikirim', 'Menunggu Barang', NULL, NULL, NULL, NULL, '2026-11-18 16:05:23', '2026-11-18 16:05:23'),
('3', '62', '3', NULL, 'SMAN 1 SANDARAN', 'Sekolah', '2.00', 'kantor', 'Belum Dipesan', NULL, NULL, NULL, NULL, NULL, NULL, 'Kab. Kutim', 'Belum Dikirim', 'Menunggu Barang', NULL, NULL, NULL, NULL, '2026-11-18 16:05:23', '2026-11-18 16:05:23'),
('4', '62', '3', NULL, 'SMAN 1 LONG MESANGAT', 'Sekolah', '1.00', 'kantor', 'Belum Dipesan', NULL, NULL, NULL, NULL, NULL, NULL, 'Kab. Kutim', 'Belum Dikirim', 'Menunggu Barang', NULL, NULL, NULL, NULL, '2026-11-18 16:05:23', '2026-11-18 16:05:23'),
('5', '62', '3', NULL, 'SMAN 1 MUARA ANCALONG', 'Sekolah', '1.00', 'kantor', 'Belum Dipesan', NULL, NULL, NULL, NULL, NULL, NULL, 'Kab. Kutim', 'Belum Dikirim', 'Menunggu Barang', NULL, NULL, NULL, NULL, '2026-11-18 16:05:23', '2026-11-18 16:05:23'),
('6', '62', '3', NULL, 'SMAN 1 KARANGAN', 'Sekolah', '2.00', 'gudang', 'Belum Dipesan', NULL, NULL, NULL, NULL, NULL, NULL, 'Kab. Kutim', 'Belum Dikirim', 'Menunggu Barang', NULL, NULL, NULL, NULL, '2026-11-18 16:05:23', '2026-11-18 21:26:30'),
('10', '62', '2', '2', 'SMKN 2 BONTANG', 'Sekolah', '2.00', 'kantor', 'Belum Dipesan', NULL, NULL, NULL, 'Nurwahidah, S.Pd, M.Pd', '081258348205', 'Jl. Cumi - Cumi No. 1 Kel. Tanjung Laut Indah Kec. Bontang Selatan', 'Kota Bontang', 'Belum Dikirim', 'Menunggu Barang', NULL, NULL, NULL, NULL, '2026-11-19 22:53:21', '2026-11-19 22:53:21'),
('11', '62', '2', '1', 'SMKN 1 BONTANG', 'Sekolah', '2.00', 'kantor', 'Belum Dipesan', NULL, NULL, NULL, 'Muhammad Sultan, S.Ag., M.Pd.I', '081347071619', 'Jl. Ciptomangunkusumo No. 2, Kel. Gn Elai Kec. Bontang Utara', 'Kota Bontang', 'Belum Dikirim', 'Menunggu Barang', NULL, NULL, NULL, NULL, '2026-11-19 22:53:38', '2026-11-19 22:53:38');

-- --------------------------------------------------------
-- Table structure for `paket_dokumen`
-- --------------------------------------------------------
DROP TABLE IF EXISTS `paket_dokumen`;
CREATE TABLE `paket_dokumen` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `paket_id` int(11) NOT NULL,
  `jenis_dokumen` varchar(100) NOT NULL DEFAULT 'BAST',
  `nama_dokumen` varchar(255) NOT NULL,
  `nomor_dokumen` varchar(100) DEFAULT NULL,
  `tanggal_dokumen` date DEFAULT NULL,
  `nama_file_asli` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_size` int(11) NOT NULL DEFAULT 0,
  `file_ext` varchar(20) NOT NULL DEFAULT '',
  `mime_type` varchar(100) DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  `uploaded_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_paket_id` (`paket_id`),
  KEY `idx_jenis_dokumen` (`jenis_dokumen`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table `paket_dokumen`
INSERT INTO `paket_dokumen` (`id`, `paket_id`, `jenis_dokumen`, `nama_dokumen`, `nomor_dokumen`, `tanggal_dokumen`, `nama_file_asli`, `file_path`, `file_size`, `file_ext`, `mime_type`, `keterangan`, `uploaded_by`, `created_at`, `updated_at`) VALUES
('1', '62', 'BAST', 'BAST SMAN 2 Bontang', '020/BAST-DISDIK/XI/2026', '2026-11-18', 'BAST_SMAN2_Bontang_Terkonfirmasi.pdf', 'uploads/dokumen/sample_bast_sman2_bontang.pdf', '40', 'pdf', 'application/pdf', 'Berita Acara Serah Terima Tenda Lipat & Tenda Camping', '1', '2026-11-18 17:00:35', '2026-11-18 17:00:35');

SET FOREIGN_KEY_CHECKS = 1;
