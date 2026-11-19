<?php
/**
 * ProLedger - LokasiPenerima Model
 * Model untuk master direktori tujuan / titik penerima distribusi
 * Universal untuk Sekolah, Kantor Instansi / Dinas, Puskesmas, Kantor Desa, dll.
 */

class LokasiPenerima {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Ambil seluruh lokasi tujuan penerima
     */
    public function getAll($search = null, $kategori = null, $kabKota = null) {
        $sql = "SELECT * FROM lokasi_penerima WHERE 1=1";
        $params = [];
        
        if (!empty($search)) {
            $sql .= " AND (nama_lokasi LIKE ? OR nama_pic LIKE ? OR kab_kota LIKE ?)";
            $params[] = "%{$search}%";
            $params[] = "%{$search}%";
            $params[] = "%{$search}%";
        }
        
        if (!empty($kategori)) {
            $sql .= " AND kategori = ?";
            $params[] = $kategori;
        }

        if (!empty($kabKota)) {
            $sql .= " AND (kab_kota = ? OR kab_kota LIKE ?)";
            $params[] = $kabKota;
            $params[] = "%{$kabKota}%";
        }
        
        $sql .= " ORDER BY nama_lokasi ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Ambil lokasi berdasarkan ID
     */
    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM lokasi_penerima WHERE id = ? LIMIT 1");
        $stmt->execute([(int)$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Cari berdasarkan nama lokasi
     */
    public function getByName($name) {
        $stmt = $this->db->prepare("SELECT * FROM lokasi_penerima WHERE LOWER(nama_lokasi) = LOWER(?) LIMIT 1");
        $stmt->execute([trim($name)]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Ambil master kabupaten / kota
     */
    public function getAllKabupaten() {
        $stmt = $this->db->query("SELECT * FROM master_kabupaten ORDER BY nama ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Tambah lokasi penerima baru
     */
    public function create($data) {
        $kabupatenId = !empty($data['kabupaten_id']) ? (int)$data['kabupaten_id'] : null;
        $kabKota = !empty($data['kab_kota']) ? trim($data['kab_kota']) : null;

        if (!$kabupatenId && $kabKota) {
            $stmtK = $this->db->prepare("SELECT id, nama FROM master_kabupaten WHERE nama = ? OR nama LIKE ? LIMIT 1");
            $stmtK->execute([$kabKota, "%{$kabKota}%"]);
            $kRow = $stmtK->fetch(PDO::FETCH_ASSOC);
            if ($kRow) {
                $kabupatenId = (int)$kRow['id'];
                $kabKota = $kRow['nama'];
            }
        } elseif ($kabupatenId && empty($kabKota)) {
            $stmtK = $this->db->prepare("SELECT nama FROM master_kabupaten WHERE id = ? LIMIT 1");
            $stmtK->execute([$kabupatenId]);
            $kRow = $stmtK->fetch(PDO::FETCH_ASSOC);
            if ($kRow) {
                $kabKota = $kRow['nama'];
            }
        }

        $stmt = $this->db->prepare("
            INSERT INTO lokasi_penerima (
                nama_lokasi, kategori, nama_pic, no_kontak, alamat_lengkap, kab_kota, kabupaten_id, no_rekening, nama_rekening
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->execute([
            trim($data['nama_lokasi']),
            !empty($data['kategori']) ? trim($data['kategori']) : 'Sekolah',
            !empty($data['nama_pic']) ? trim($data['nama_pic']) : null,
            !empty($data['no_kontak']) ? trim($data['no_kontak']) : null,
            !empty($data['alamat_lengkap']) ? trim($data['alamat_lengkap']) : null,
            $kabKota,
            $kabupatenId,
            !empty($data['no_rekening']) ? trim($data['no_rekening']) : null,
            !empty($data['nama_rekening']) ? trim($data['nama_rekening']) : null
        ]);
        
        return $this->db->lastInsertId();
    }
    
    /**
     * Update lokasi penerima
     */
    public function update($id, $data) {
        $stmt = $this->db->prepare("
            UPDATE lokasi_penerima SET 
                nama_lokasi = ?,
                kategori = ?,
                nama_pic = ?,
                no_kontak = ?,
                alamat_lengkap = ?,
                kab_kota = ?,
                no_rekening = ?,
                nama_rekening = ?
            WHERE id = ?
        ");
        
        return $stmt->execute([
            trim($data['nama_lokasi']),
            !empty($data['kategori']) ? trim($data['kategori']) : 'Sekolah',
            !empty($data['nama_pic']) ? trim($data['nama_pic']) : null,
            !empty($data['no_kontak']) ? trim($data['no_kontak']) : null,
            !empty($data['alamat_lengkap']) ? trim($data['alamat_lengkap']) : null,
            !empty($data['kab_kota']) ? trim($data['kab_kota']) : null,
            !empty($data['no_rekening']) ? trim($data['no_rekening']) : null,
            !empty($data['nama_rekening']) ? trim($data['nama_rekening']) : null,
            (int)$id
        ]);
    }
    
    /**
     * Hapus lokasi
     */
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM lokasi_penerima WHERE id = ?");
        return $stmt->execute([(int)$id]);
    }
    
    /**
     * Cari atau buat otomatis jika nama tujuan belum ada di master
     */
    public function findOrCreateByName($name, $extra = []) {
        $existing = $this->getByName($name);
        if ($existing) {
            return $existing['id'];
        }
        
        return $this->create([
            'nama_lokasi' => $name,
            'kategori' => $extra['kategori'] ?? 'Sekolah',
            'nama_pic' => $extra['nama_pic'] ?? null,
            'no_kontak' => $extra['no_kontak'] ?? null,
            'alamat_lengkap' => $extra['alamat'] ?? null,
            'kab_kota' => $extra['kab_kota'] ?? null
        ]);
    }
}
