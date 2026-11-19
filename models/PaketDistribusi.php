<?php
/**
 * ProLedger - PaketDistribusi Model
 * Model untuk alokasi pengiriman/distribusi barang paket ke titik-titik penerima
 */

class PaketDistribusi {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Ambil seluruh alokasi distribusi paket
     */
    public function getByPaketId($paketId) {
        $stmt = $this->db->prepare("
            SELECT pd.*, 
                   pi.komponen as nama_komponen, 
                   pi.spesifikasi, 
                   pi.satuan, 
                   pi.harga_satuan as item_harga_satuan,
                   pi.jumlah as item_total_qty,
                   lp.nama_pic as master_pic,
                   lp.jabatan_pic as master_jabatan,
                   lp.no_kontak as master_kontak,
                   lp.alamat_lengkap as master_alamat
            FROM paket_distribusi pd
            LEFT JOIN paket_items pi ON pd.item_id = pi.id
            LEFT JOIN lokasi_penerima lp ON pd.lokasi_id = lp.id
            WHERE pd.paket_id = ?
            ORDER BY pd.nama_tujuan ASC, pd.id ASC
        ");
        $stmt->execute([(int)$paketId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Ambil distribusi berdasarkan ID
     */
    public function getById($id) {
        $stmt = $this->db->prepare("
            SELECT pd.*, pi.komponen as nama_komponen, pi.satuan
            FROM paket_distribusi pd
            LEFT JOIN paket_items pi ON pd.item_id = pi.id
            WHERE pd.id = ?
            LIMIT 1
        ");
        $stmt->execute([(int)$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Ambil rekap alokasi per item dalam paket
     * Mengembalikan array dengan key = item_id, berisi total kuantitas yang sudah dialokasikan
     */
    public function getAllocatedPerItem($paketId) {
        $stmt = $this->db->prepare("
            SELECT item_id, COALESCE(SUM(jumlah), 0) as allocated_qty
            FROM paket_distribusi
            WHERE paket_id = ?
            GROUP BY item_id
        ");
        $stmt->execute([(int)$paketId]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $result = [];
        foreach ($rows as $r) {
            $result[$r['item_id']] = (float)$r['allocated_qty'];
        }
        return $result;
    }
    
    /**
     * Ringkasan distribusi untuk paket
     */
    public function getSummaryByPaketId($paketId) {
        $stmt = $this->db->prepare("
            SELECT 
                COUNT(*) as total_baris,
                COUNT(DISTINCT nama_tujuan) as total_tujuan,
                COALESCE(SUM(jumlah), 0) as total_qty_dialokasikan,
                SUM(CASE WHEN status_distribusi = 'Tiba di Tujuan' THEN 1 ELSE 0 END) as total_diterima,
                SUM(CASE WHEN status_barang = 'Sudah Dipesan' THEN 1 ELSE 0 END) as total_dipesan,
                SUM(CASE WHEN status_barang = 'Belum Dipesan' OR status_barang IS NULL THEN 1 ELSE 0 END) as total_belum_pesan
            FROM paket_distribusi
            WHERE paket_id = ?
        ");
        $stmt->execute([(int)$paketId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Tambah alokasi distribusi baru
     */
    public function create($data) {
        $statusBarang = !empty($data['status_barang']) ? trim($data['status_barang']) : 'Belum Dipesan';
        $namaBarang = !empty($data['nama_barang']) ? trim($data['nama_barang']) : null;
        $hargaSatuan = isset($data['harga_satuan']) && $data['harga_satuan'] !== '' ? (float)$data['harga_satuan'] : null;
        $ekspedisi = !empty($data['ekspedisi']) ? trim($data['ekspedisi']) : null;
        $statusDistribusi = !empty($data['status_distribusi']) ? trim($data['status_distribusi']) : null;

        $stmt = $this->db->prepare("
            INSERT INTO paket_distribusi (
                paket_id, item_id, lokasi_id, nama_tujuan, kategori, jumlah,
                status_barang, nama_barang, harga_satuan, ekspedisi, status_distribusi,
                tanggal_pesan, link_pembelian, nama_pic, no_kontak, alamat, kab_kota,
                tanggal_terima, no_resi, catatan
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->execute([
            (int)$data['paket_id'],
            (int)$data['item_id'],
            !empty($data['lokasi_id']) ? (int)$data['lokasi_id'] : null,
            trim($data['nama_tujuan']),
            !empty($data['kategori']) ? trim($data['kategori']) : 'Sekolah',
            (float)($data['jumlah'] ?? 1),
            $statusBarang,
            $namaBarang,
            $hargaSatuan,
            $ekspedisi,
            $statusDistribusi,
            !empty($data['tanggal_pesan']) ? $data['tanggal_pesan'] : null,
            !empty($data['link_pembelian']) ? trim($data['link_pembelian']) : null,
            !empty($data['nama_pic']) ? trim($data['nama_pic']) : null,
            !empty($data['no_kontak']) ? trim($data['no_kontak']) : null,
            !empty($data['alamat']) ? trim($data['alamat']) : null,
            !empty($data['kab_kota']) ? trim($data['kab_kota']) : null,
            !empty($data['tanggal_terima']) ? $data['tanggal_terima'] : null,
            !empty($data['no_resi']) ? trim($data['no_resi']) : null,
            !empty($data['catatan']) ? trim($data['catatan']) : null
        ]);
        
        return $this->db->lastInsertId();
    }

    /**
     * Update Status Barang (Belum Dipesan vs Sudah Dipesan beserta detail pembelian)
     */
    public function updateStatusBarang($id, $data) {
        $statusBarang = !empty($data['status_barang']) ? trim($data['status_barang']) : 'Belum Dipesan';
        $isOrdered = ($statusBarang === 'Sudah Dipesan');

        if (!$isOrdered) {
            $stmt = $this->db->prepare("
                UPDATE paket_distribusi SET
                    status_barang = 'Belum Dipesan',
                    nama_barang = NULL,
                    harga_satuan = NULL,
                    ekspedisi = NULL,
                    no_resi = NULL,
                    link_pembelian = NULL,
                    tanggal_pesan = NULL,
                    status_distribusi = NULL,
                    updated_at = NOW()
                WHERE id = ?
            ");
            return $stmt->execute([(int)$id]);
        } else {
            $namaBarang = !empty($data['nama_barang']) ? trim($data['nama_barang']) : null;
            $hargaSatuan = isset($data['harga_satuan']) && $data['harga_satuan'] !== '' ? (float)$data['harga_satuan'] : null;
            $ekspedisi = !empty($data['ekspedisi']) ? trim($data['ekspedisi']) : null;
            $noResi = !empty($data['no_resi']) ? trim($data['no_resi']) : null;
            $link = !empty($data['link_pembelian']) ? trim($data['link_pembelian']) : null;
            $tanggalPesan = !empty($data['tanggal_pesan']) ? $data['tanggal_pesan'] : date('Y-m-d');

            $stmt = $this->db->prepare("
                UPDATE paket_distribusi SET
                    status_barang = 'Sudah Dipesan',
                    nama_barang = ?,
                    harga_satuan = ?,
                    ekspedisi = ?,
                    no_resi = ?,
                    link_pembelian = ?,
                    tanggal_pesan = COALESCE(tanggal_pesan, ?),
                    updated_at = NOW()
                WHERE id = ?
            ");
            return $stmt->execute([
                $namaBarang,
                $hargaSatuan,
                $ekspedisi,
                $noResi,
                $link,
                $tanggalPesan,
                (int)$id
            ]);
        }
    }

    /**
     * Update Status Distribusi (Tiba dirumah/kantor, Tiba di Gudang, Tiba di Tujuan)
     */
    public function updateStatusDistribusi($id, $statusDistribusi) {
        $statusDistribusi = trim($statusDistribusi);
        $isTujuan = ($statusDistribusi === 'Tiba di Tujuan');
        $tanggalTerima = $isTujuan ? date('Y-m-d') : null;

        $stmt = $this->db->prepare("
            UPDATE paket_distribusi SET
                status_distribusi = ?,
                tanggal_terima = COALESCE(tanggal_terima, ?),
                updated_at = NOW()
            WHERE id = ?
        ");
        return $stmt->execute([
            $statusDistribusi,
            $tanggalTerima,
            (int)$id
        ]);
    }

    /**
     * Perbarui alokasi distribusi
     */
    public function update($id, $data) {
        $stmt = $this->db->prepare("
            UPDATE paket_distribusi SET
                item_id = ?,
                lokasi_id = ?,
                nama_tujuan = ?,
                kategori = ?,
                jumlah = ?,
                status_barang = ?,
                nama_barang = ?,
                harga_satuan = ?,
                ekspedisi = ?,
                status_distribusi = ?,
                tanggal_pesan = ?,
                link_pembelian = ?,
                nama_pic = ?,
                no_kontak = ?,
                alamat = ?,
                kab_kota = ?,
                tanggal_terima = ?,
                no_resi = ?,
                catatan = ?,
                updated_at = NOW()
            WHERE id = ?
        ");
        
        return $stmt->execute([
            (int)$data['item_id'],
            !empty($data['lokasi_id']) ? (int)$data['lokasi_id'] : null,
            trim($data['nama_tujuan']),
            !empty($data['kategori']) ? trim($data['kategori']) : 'Sekolah',
            (float)($data['jumlah'] ?? 1),
            !empty($data['status_barang']) ? trim($data['status_barang']) : 'Belum Dipesan',
            !empty($data['nama_barang']) ? trim($data['nama_barang']) : null,
            isset($data['harga_satuan']) && $data['harga_satuan'] !== '' ? (float)$data['harga_satuan'] : null,
            !empty($data['ekspedisi']) ? trim($data['ekspedisi']) : null,
            !empty($data['status_distribusi']) ? trim($data['status_distribusi']) : null,
            !empty($data['tanggal_pesan']) ? $data['tanggal_pesan'] : null,
            !empty($data['link_pembelian']) ? trim($data['link_pembelian']) : null,
            !empty($data['nama_pic']) ? trim($data['nama_pic']) : null,
            !empty($data['no_kontak']) ? trim($data['no_kontak']) : null,
            !empty($data['alamat']) ? trim($data['alamat']) : null,
            !empty($data['kab_kota']) ? trim($data['kab_kota']) : null,
            !empty($data['tanggal_terima']) ? $data['tanggal_terima'] : null,
            !empty($data['no_resi']) ? trim($data['no_resi']) : null,
            !empty($data['catatan']) ? trim($data['catatan']) : null,
            (int)$id
        ]);
    }
    
    /**
     * Hapus alokasi distribusi
     */
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM paket_distribusi WHERE id = ?");
        return $stmt->execute([(int)$id]);
    }

    /**
     * Ambil data distribusi yang dikelompokkan per tujuan (Sekolah / Instansi)
     * Digunakan untuk monitoring penyaluran dan pencetakan BAST
     */
    public function getGroupedByTujuan($paketId) {
        $all = $this->getByPaketId($paketId);
        $grouped = [];

        foreach ($all as $row) {
            $key = trim($row['nama_tujuan']);
            if (!isset($grouped[$key])) {
                $grouped[$key] = [
                    'nama_tujuan' => $row['nama_tujuan'],
                    'kategori'    => $row['kategori'],
                    'lokasi_id'   => $row['lokasi_id'],
                    'nama_pic'    => $row['nama_pic'] ?: ($row['master_pic'] ?? ''),
                    'jabatan_pic' => $row['master_jabatan'] ?? 'Kepala Sekolah',
                    'no_kontak'   => $row['no_kontak'] ?: ($row['master_kontak'] ?? ''),
                    'alamat'      => $row['alamat'] ?: ($row['master_alamat'] ?? ''),
                    'kab_kota'    => $row['kab_kota'] ?? '',
                    'items'       => [],
                    'total_qty'   => 0,
                    'is_bast_ready' => true,
                    'status_count' => [
                        'diterima' => 0,
                        'total'    => 0
                    ]
                ];
            }

            $grouped[$key]['items'][] = $row;
            $grouped[$key]['total_qty'] += (float)$row['jumlah'];
            $grouped[$key]['status_count']['total']++;

            $isReceived = (($row['status_distribusi'] ?? '') === 'Tiba di Tujuan' || ($row['status_penerimaan'] ?? '') === 'Diterima di Tujuan' || ($row['status'] ?? '') === 'Diterima');
            if ($isReceived) {
                $grouped[$key]['status_count']['diterima']++;
            } else {
                $grouped[$key]['is_bast_ready'] = false;
            }
        }

        return $grouped;
    }

    /**
     * Ambil rincian item pengadaan untuk 1 tujuan tertentu dalam paket
     */
    public function getItemsByTujuan($paketId, $namaTujuan) {
        $stmt = $this->db->prepare("
            SELECT pd.*, 
                   pi.komponen as nama_komponen, 
                   pi.spesifikasi, 
                   pi.satuan, 
                   pi.harga_satuan,
                   lp.nama_pic as master_pic,
                   lp.jabatan_pic as master_jabatan,
                   lp.no_kontak as master_kontak,
                   lp.alamat_lengkap as master_alamat
            FROM paket_distribusi pd
            LEFT JOIN paket_items pi ON pd.item_id = pi.id
            LEFT JOIN lokasi_penerima lp ON pd.lokasi_id = lp.id
            WHERE pd.paket_id = ? AND TRIM(pd.nama_tujuan) = ?
            ORDER BY pd.id ASC
        ");
        $stmt->execute([(int)$paketId, trim($namaTujuan)]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
