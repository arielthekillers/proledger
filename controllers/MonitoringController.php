<?php
/**
 * ProLedger - MonitoringController
 * Endpoint publik read-only (tanpa login) untuk pemantauan paket kolaborasi oleh Dinas/Stakeholder.
 * Route: /monitoring/{share_token}
 */

require_once MODEL_PATH . '/Kolaborasi.php';

class MonitoringController {
    private $kolabModel;

    public function __construct() {
        $this->kolabModel = new Kolaborasi();
    }

    /**
     * Redirect /monitoring ke halaman login/error jika tanpa token
     */
    public function index($token = null) {
        if (empty($token)) {
            $this->renderNotFound('Token monitoring tidak ditemukan.');
            return;
        }
        $this->show((string)$token);
    }

    /**
     * Tangani /monitoring/{token} via magic __call jika token langsung menjadi $action
     */
    public function __call($name, $arguments) {
        $this->show((string)$name);
    }

    private function show(string $token) {
        $kolab = $this->kolabModel->getByShareToken($token);
        if (!$kolab) {
            $this->renderNotFound('Link monitoring tidak valid, sudah dinonaktifkan, atau ruang kolaborasi telah dihapus.');
            return;
        }

        $dashboard = $this->kolabModel->getDashboardData((int)$kolab['id']);
        $linkedGroups = $this->kolabModel->getLinkedGroups((int)$kolab['id']);

        $data = [
            'title'        => 'Monitoring: ' . $kolab['nama'],
            'kolab'        => $kolab,
            'pakets'       => $dashboard['pakets'],
            'companyStats' => $dashboard['companyStats'],
            'summary'      => $dashboard['summary'],
            'linkedGroups' => $linkedGroups,
        ];

        extract($data);
        require VIEW_PATH . '/kolaborasi/public_monitoring.php';
    }

    private function renderNotFound(string $message) {
        http_response_code(404);
        echo '<!DOCTYPE html><html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Link Tidak Tersedia - SimPaket</title><script src="https://cdn.tailwindcss.com"></script></head><body class="bg-slate-100 flex items-center justify-center min-h-screen p-4 text-center"><div class="bg-white p-8 rounded-2xl shadow-sm border border-slate-200 max-w-md w-full"><div class="w-14 h-14 bg-amber-50 text-amber-500 rounded-2xl flex items-center justify-center mx-auto mb-4 text-2xl font-bold">!</div><h1 class="text-lg font-bold text-slate-800 mb-2">Link Monitoring Tidak Tersedia</h1><p class="text-xs text-slate-500 leading-relaxed">' . htmlspecialchars($message) . '</p></div></body></html>';
    }
}
