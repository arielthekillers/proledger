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
 * Hitung modal pasal per orang
 * Formula: (Pagu × 13%) ÷ Jumlah Pemodal
 */
function calculateModalPasal($pagu, $jumlah_pemodal) {
    if ($jumlah_pemodal == 0) return 0;
    return ($pagu * MODAL_PASAL_PERCENTAGE) / $jumlah_pemodal;
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
function calculateTotalModal($pagu, $rab, $jumlah_pemodal) {
    $modal_pasal = calculateModalPasal($pagu, $jumlah_pemodal);
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
 * Get status badge HTML
 */
function getStatusBadge($status) {
    $colors = [
        'Kontrak' => 'text-blue-600 bg-blue-100',
        'Pemesanan' => 'text-amber-600 bg-amber-100',
        'Distribusi' => 'text-purple-600 bg-purple-100',
        'Selesai' => 'text-emerald-600 bg-emerald-100'
    ];
    $class = $colors[$status] ?? 'text-slate-600 bg-slate-100';
    return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ' . $class . '">' . htmlspecialchars($status) . '</span>';
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
