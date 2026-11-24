<?php
/**
 * ProLedger - Upload Controller
 * API Endpoint untuk menangani upload gambar asinkron (AJAX dengan Progress Bar)
 * dan otomatis menyimpan ke versi Large, Medium, Small
 */

require_once HELPER_PATH . '/Uploader.php';

class UploadController {

    public function __construct() {
        if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Sesi login tidak valid. Silakan login kembali.']);
            exit;
        }
    }

    /**
     * Endpoint: POST /upload/image
     */
    public function image() {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Metode request tidak diizinkan.']);
            exit;
        }

        $file = $_FILES['file'] ?? ($_FILES['image'] ?? null);
        if (!$file) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'File tidak ditemukan dalam request.']);
            exit;
        }

        $module = preg_replace('/[^a-zA-Z0-9_-]/', '', $_POST['module'] ?? 'general');
        $field = preg_replace('/[^a-zA-Z0-9_-]/', '', $_POST['field'] ?? 'image');
        $recordId = !empty($_POST['id']) ? (int)$_POST['id'] : null;

        // Validasi hak akses untuk modul perusahaan
        $userId = $_SESSION['admin_id'] ?? 0;
        $userRole = $_SESSION['admin_role'] ?? 'user';

        if ($module === 'perusahaan' && $recordId) {
            require_once MODEL_PATH . '/Perusahaan.php';
            $pModel = new Perusahaan();
            if (!$pModel->isOwnerOrAdmin($recordId, $userId, $userRole)) {
                http_response_code(403);
                echo json_encode(['success' => false, 'message' => 'Anda tidak memiliki hak akses untuk mengubah perusahaan ini.']);
                exit;
            }
        }

        // Khusus kopsurat: wajib PDF A4 agar hasil cetak tajam
        $origExt = strtolower(pathinfo($file['name'] ?? '', PATHINFO_EXTENSION));
        if ($field === 'kopsurat') {
            $result = Uploader::uploadFile($file, 'perusahaan/kopsurat', ['pdf'], 15, true);
            if ($result['success']) {
                $result['filename'] = basename($result['file_path']);
                $result['urls'] = [
                    'large'  => $result['url'],
                    'medium' => $result['url'],
                    'small'  => $result['url'],
                ];
            }
        } else {
            // Lakukan upload dan kompresi 3 ukuran
            $result = Uploader::uploadImage($file, $module, $field);
        }

        if (!$result['success']) {
            http_response_code(400);
            echo json_encode($result);
            exit;
        }

        // Jika ada record ID dan modul perusahaan, update database otomatis & hapus file lama
        if ($module === 'perusahaan' && $recordId && in_array($field, ['logo', 'avatar', 'kopsurat', 'ttd'])) {
            $db = Database::getInstance()->getConnection();
            
            // Ambil gambar lama untuk dihapus
            $stmtOld = $db->prepare("SELECT {$field} FROM perusahaan WHERE id = ?");
            $stmtOld->execute([$recordId]);
            $oldFilename = $stmtOld->fetchColumn();

            if (!empty($oldFilename)) {
                if (preg_match('/\.pdf$/i', $oldFilename)) {
                    Uploader::deleteFile("uploads/{$module}/{$field}/{$oldFilename}");
                } else {
                    Uploader::deleteImage($oldFilename, $module, $field);
                }
            }

            // Simpan gambar baru ke database
            $stmtUpdate = $db->prepare("UPDATE perusahaan SET {$field} = ? WHERE id = ?");
            $stmtUpdate->execute([$result['filename'], $recordId]);

            $result['updated_database'] = true;
        }

        echo json_encode([
            'success' => true,
            'message' => 'Gambar berhasil diupload dan diproses dalam versi Large, Medium, dan Small!',
            'filename' => $result['filename'],
            'module' => $module,
            'field' => $field,
            'urls' => $result['urls']
        ]);
        exit;
    }

    /**
     * Endpoint: POST /upload/delete
     */
    public function delete() {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Metode request tidak diizinkan.']);
            exit;
        }

        $module = preg_replace('/[^a-zA-Z0-9_-]/', '', $_POST['module'] ?? '');
        $field = preg_replace('/[^a-zA-Z0-9_-]/', '', $_POST['field'] ?? '');
        $recordId = !empty($_POST['id']) ? (int)$_POST['id'] : null;

        $userId = $_SESSION['admin_id'] ?? 0;
        $userRole = $_SESSION['admin_role'] ?? 'user';

        if ($module === 'perusahaan' && $recordId && in_array($field, ['logo', 'avatar', 'kopsurat', 'ttd'])) {
            require_once MODEL_PATH . '/Perusahaan.php';
            $pModel = new Perusahaan();
            if (!$pModel->isOwnerOrAdmin($recordId, $userId, $userRole)) {
                http_response_code(403);
                echo json_encode(['success' => false, 'message' => 'Akses ditolak.']);
                exit;
            }

            $db = Database::getInstance()->getConnection();
            $stmtOld = $db->prepare("SELECT {$field} FROM perusahaan WHERE id = ?");
            $stmtOld->execute([$recordId]);
            $oldFilename = $stmtOld->fetchColumn();

            if (!empty($oldFilename)) {
                Uploader::deleteImage($oldFilename, $module, $field);
                $db->prepare("UPDATE perusahaan SET {$field} = NULL WHERE id = ?")->execute([$recordId]);
            }

            echo json_encode(['success' => true, 'message' => 'Gambar berhasil dihapus.']);
            exit;
        }

        echo json_encode(['success' => false, 'message' => 'Parameter tidak valid.']);
        exit;
    }
}
