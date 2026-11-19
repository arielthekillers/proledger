<?php
/**
 * ProLedger - Helper Functions
 * Fungsi-fungsi utility untuk aplikasi
 */

/**
 * Format angka ke format Rupiah
 */
function formatRupiah($amount) {
    return 'Rp ' . number_format($amount, 0, ',', '.');
}

/**
 * Hitung DPP (Dasar Pengenaan Pajak) setelah dikurangi PPN 11%
 * Formula: Nilai Acuan / 1.11
 */
function calculateDPP($nilaiAcuan) {
    if ($nilaiAcuan <= 0) return 0;
    return round($nilaiAcuan / 1.11);
}

/**
 * Hitung Nilai Pasal dari DPP dengan persentase fleksibel (default 13%)
 */
function calculatePasal($dpp, $persen = 13) {
    if ($dpp <= 0 || $persen <= 0) return 0;
    return round($dpp * ($persen / 100));
}

/**
 * Backward compatibility alias untuk calculatePasal13
 */
function calculatePasal13($dpp, $persen = 13) {
    return calculatePasal($dpp, $persen);
}

/**
 * Hitung Biaya Admin dari DPP dengan persentase fleksibel (default 2%)
 */
function calculateAdmin($dpp, $persen = 2) {
    if ($dpp <= 0 || $persen <= 0) return 0;
    return round($dpp * ($persen / 100));
}

/**
 * Backward compatibility alias untuk calculateAdmin2
 */
function calculateAdmin2($dpp, $persen = 2) {
    return calculateAdmin($dpp, $persen);
}

/**
 * Hitung Total Pasal (Pasal + Admin dari DPP)
 */
function calculateTotalPasal($dpp, $persenPasal = 13, $persenAdmin = 2) {
    return calculatePasal($dpp, $persenPasal) + calculateAdmin($dpp, $persenAdmin);
}

/**
 * Hitung modal pasal per orang
 * Formula: Total Pasal ÷ Jumlah Pemodal
 */
function calculateModalPasal($dpp, $jumlah_pemodal) {
    if ($jumlah_pemodal == 0) return 0;
    $totalPasal = calculateTotalPasal($dpp);
    return round($totalPasal / $jumlah_pemodal);
}

/**
 * Hitung modal belanja per orang
 * Formula: RAB ÷ Jumlah Pemodal
 */
function calculateModalBelanja($rab, $jumlah_pemodal) {
    if ($jumlah_pemodal == 0) return 0;
    return $rab / $jumlah_pemodal;
}

/**
 * Hitung total modal per orang
 */
function calculateTotalModal($dpp, $rab, $jumlah_pemodal) {
    $modal_pasal = calculateModalPasal($dpp, $jumlah_pemodal);
    $modal_belanja = calculateModalBelanja($rab, $jumlah_pemodal);
    return $modal_pasal + $modal_belanja;
}

/**
 * Redirect helper
 */
function redirect($url) {
    header("Location: " . APP_URL . $url);
    exit;
}

/**
 * Flash message helper
 */
function flash($key, $message = null) {
    if ($message === null) {
        // Get flash message
        if (isset($_SESSION['flash'][$key])) {
            $msg = $_SESSION['flash'][$key];
            unset($_SESSION['flash'][$key]);
            return $msg;
        }
        return null;
    } else {
        // Set flash message
        $_SESSION['flash'][$key] = $message;
    }
}

/**
 * Get old input value
 */
function old($key, $default = '') {
    if (isset($_SESSION['old'][$key])) {
        $value = $_SESSION['old'][$key];
        return $value;
    }
    return $default;
}

/**
 * Set old input values
 */
function setOld($data) {
    $_SESSION['old'] = $data;
}

/**
 * Clear old input values
 */
function clearOld() {
    unset($_SESSION['old']);
}

/**
 * Sanitize input
 */
function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

/**
 * Format tanggal Indonesia
 */
function formatTanggal($date) {
    $bulan = [
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];
    
    $timestamp = strtotime($date);
    $day = date('d', $timestamp);
    $month = $bulan[(int)date('m', $timestamp)];
    $year = date('Y', $timestamp);
    
    return $day . ' ' . $month . ' ' . $year;
}

/**
 * Get status badge HTML (Pencairan / Status Proyek)
 */
function getStatusBadge($status) {
    $norm = strtoupper(trim($status ?? ''));
    if ($norm === 'CAIR' || $norm === 'SELESAI') {
        return '<span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-600 text-white shadow-2xs">CAIR</span>';
    } elseif ($norm === 'PROSES') {
        return '<span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-rose-700 text-white shadow-2xs">PROSES</span>';
    } elseif ($norm === 'BELUM') {
        return '<span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-slate-600 text-white shadow-2xs">BELUM</span>';
    }
    
    $colors = [
        'Kontrak' => 'text-blue-700 bg-blue-100 border border-blue-200',
        'Pemesanan' => 'text-amber-700 bg-amber-100 border border-amber-200',
        'Distribusi' => 'text-purple-700 bg-purple-100 border border-purple-200',
        'Selesai' => 'text-emerald-700 bg-emerald-100 border border-emerald-200'
    ];
    $class = $colors[$status] ?? 'text-slate-600 bg-slate-100 border border-slate-200';
    return '<span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold ' . $class . '">' . htmlspecialchars($status) . '</span>';
}

/**
 * Get progres barang badge HTML (Sesuai Spreadsheet: BELUM, BELANJA, DISTRIBUSI, SELESAI)
 */
function getProgresBarangBadge($progres) {
    $norm = strtoupper(trim($progres ?? 'BELUM'));
    switch ($norm) {
        case 'DISTRIBUSI':
            return '<span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-50 text-emerald-700 border border-emerald-500 tracking-wider">DISTRIBUSI</span>';
        case 'BELANJA':
            return '<span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-blue-50 text-blue-700 border border-blue-500 tracking-wider">BELANJA</span>';
        case 'BELUM':
            return '<span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-rose-50 text-rose-700 border border-rose-500 tracking-wider">BELUM</span>';
        case 'SELESAI':
            return '<span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-teal-50 text-teal-700 border border-teal-500 tracking-wider">SELESAI</span>';
        default:
            return '<span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-slate-50 text-slate-700 border border-slate-300 tracking-wider">' . htmlspecialchars(strtoupper($progres)) . '</span>';
    }
}

/**
 * Get penanggung jawab badge HTML (Warna pastel khas tim)
 */
function getPenanggungJawabBadge($nama) {
    if (empty($nama)) {
        return '<span class="text-slate-300 text-[11px] italic">-</span>';
    }
    
    $cleanName = strtoupper(trim($nama));
    
    // Check multiple names (e.g. "RAHMAN, RIFKI")
    if (strpos($cleanName, ',') !== false) {
        $parts = explode(',', $cleanName);
        $html = '<div class="flex flex-wrap gap-1">';
        foreach ($parts as $p) {
            $html .= getPenanggungJawabBadge(trim($p));
        }
        $html .= '</div>';
        return $html;
    }
    
    $palettes = [
        'RAHMAN' => 'bg-emerald-50 text-emerald-800 border-emerald-200',
        'RIFKI' => 'bg-rose-50 text-rose-800 border-rose-200',
        'DARMAN' => 'bg-slate-100 text-slate-800 border-slate-300',
        'SULIS' => 'bg-purple-50 text-purple-800 border-purple-200',
        'RIZAL' => 'bg-amber-50 text-amber-900 border-amber-200',
        'ASHARI' => 'bg-cyan-50 text-cyan-900 border-cyan-200',
        'BUDI' => 'bg-blue-50 text-blue-800 border-blue-200',
        'ANDI' => 'bg-indigo-50 text-indigo-800 border-indigo-200',
    ];
    
    $class = $palettes[$cleanName] ?? 'bg-slate-50 text-slate-800 border-slate-200';
    return '<span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold border ' . $class . ' tracking-wide">' . htmlspecialchars($cleanName) . '</span>';
}

/**
 * Get tipe transaksi badge
 */
function getTipeBadge($tipe) {
    $class = $tipe === 'masuk' ? 'badge-success' : 'badge-danger';
    $icon = $tipe === 'masuk' ? '↓' : '↑';
    return '<span class="badge ' . $class . '">' . $icon . ' ' . ucfirst($tipe) . '</span>';
}

/**
 * Generate CSRF Token
 */
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF Token
 */
function verifyCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Get CSRF input field
 */
function csrfField() {
    $token = generateCSRFToken();
    return '<input type="hidden" name="csrf_token" value="' . $token . '">';
}

/**
 * Dapatkan data perusahaan aktif saat ini, dan pastikan selalu ada 1 perusahaan aktif
 * jika sistem memiliki minimal 1 perusahaan (single active company architecture).
 */
function getActivePerusahaan() {
    if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
        return null;
    }

    require_once MODEL_PATH . '/Perusahaan.php';
    $perusahaanModel = new Perusahaan();
    $role = $_SESSION['admin_role'] ?? 'user';
    $userId = $_SESSION['admin_id'] ?? 0;
    
    $companies = ($role === 'admin') 
        ? $perusahaanModel->getAll() 
        : $perusahaanModel->getByUser($userId);

    if (empty($companies)) {
        unset($_SESSION['active_perusahaan_id']);
        unset($_SESSION['active_perusahaan_nama']);
        return null;
    }

    $activeId = $_SESSION['active_perusahaan_id'] ?? null;
    $activeComp = null;

    if (!empty($activeId)) {
        foreach ($companies as $c) {
            if ($c['id'] == $activeId) {
                $activeComp = $c;
                break;
            }
        }
    }

    // Jika belum ada perusahaan aktif yang valid, otomatis tetapkan perusahaan pertama
    if (!$activeComp) {
        $activeComp = $companies[0];
        $_SESSION['active_perusahaan_id'] = $activeComp['id'];
        $_SESSION['active_perusahaan_nama'] = $activeComp['nama'];
    } else {
        $_SESSION['active_perusahaan_nama'] = $activeComp['nama'];
    }

    return $activeComp;
}
