<?php
/**
 * ProLedger - ModalDetail Model
 * Model untuk detail modal per pemodal
 */

class ModalDetail {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Get modal detail by paket
     */
    public function getByPaket($paket_id) {
        $stmt = $this->db->prepare("
            SELECT md.*, u.nama, u.email, u.telepon
            FROM modal_detail md
            JOIN users u ON md.user_id = u.id
            WHERE md.paket_id = ?
            ORDER BY u.nama ASC
        ");
        $stmt->execute([$paket_id]);
        return $stmt->fetchAll();
    }
    
    /**
     * Get modal detail by user
     */
    public function getByUser($user_id) {
        $stmt = $this->db->prepare("
            SELECT md.*, pk.nama_paket, pk.status, pk.pagu, pk.jumlah_pemodal, pk.rab
            FROM modal_detail md
            JOIN paket_kegiatan pk ON md.paket_id = pk.id
            WHERE md.user_id = ?
            ORDER BY md.created_at DESC
        ");
        $stmt->execute([$user_id]);
        return $stmt->fetchAll();
    }
    
    /**
     * Get modal detail for specific paket and user
     */
    public function getByPaketAndUser($paket_id, $user_id) {
        $stmt = $this->db->prepare("
            SELECT * FROM modal_detail 
            WHERE paket_id = ? AND user_id = ?
        ");
        $stmt->execute([$paket_id, $user_id]);
        return $stmt->fetch();
    }
    
    /**
     * Get total modal for a paket
     */
    public function getTotalByPaket($paket_id) {
        $stmt = $this->db->prepare("
            SELECT 
                SUM(modal_pasal) as total_modal_pasal,
                SUM(modal_belanja) as total_modal_belanja,
                SUM(total_modal) as total_modal
            FROM modal_detail
            WHERE paket_id = ?
        ");
        $stmt->execute([$paket_id]);
        return $stmt->fetch();
    }
}
