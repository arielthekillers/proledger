<?php
/**
 * ProLedger - Standard Image Uploader
 * Menangani upload gambar, validasi, dan otomatis membuat 3 versi ukuran:
 * - Large  (Ukuran HD / Tampilan Utama)
 * - Medium (Ukuran Sedang / Tampilan Halaman)
 * - Small  (Thumbnail / Avatar / Icon Preview)
 */

class Uploader {

    /**
     * Konfigurasi dimensi standar per field
     */
    private static $defaultDimensions = [
        'avatar' => [
            'large'  => ['width' => 500,  'height' => 500,  'crop' => true],
            'medium' => ['width' => 200,  'height' => 200,  'crop' => true],
            'small'  => ['width' => 80,   'height' => 80,   'crop' => true],
        ],
        'logo' => [
            'large'  => ['width' => 1200, 'height' => 1200, 'crop' => false],
            'medium' => ['width' => 500,  'height' => 500,  'crop' => false],
            'small'  => ['width' => 160,  'height' => 160,  'crop' => false],
        ],
        'kopsurat' => [
            'large'  => ['width' => 2480, 'height' => 3508, 'crop' => false],
            'medium' => ['width' => 1240, 'height' => 1754, 'crop' => false],
            'small'  => ['width' => 420,  'height' => 594,  'crop' => false],
        ],
        'ttd' => [
            'large'  => ['width' => 1000, 'height' => 500,  'crop' => false],
            'medium' => ['width' => 500,  'height' => 250,  'crop' => false],
            'small'  => ['width' => 200,  'height' => 100,  'crop' => false],
        ],
        'default' => [
            'large'  => ['width' => 1400, 'height' => 1400, 'crop' => false],
            'medium' => ['width' => 600,  'height' => 600,  'crop' => false],
            'small'  => ['width' => 180,  'height' => 180,  'crop' => false],
        ]
    ];

    /**
     * Proses upload file gambar
     */
    public static function uploadImage($file, $module, $field, $customDimensions = null) {
        if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
            $errorCode = $file['error'] ?? UPLOAD_ERR_NO_FILE;
            return ['success' => false, 'message' => self::getErrorMessage($errorCode)];
        }

        // 1. Validasi MIME Type & Ekstensi
        $allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mime, $allowedTypes)) {
            return ['success' => false, 'message' => 'Format file tidak didukung. Harap upload gambar JPG, PNG, atau WebP.'];
        }

        // Batas ukuran 10MB
        if ($file['size'] > 10 * 1024 * 1024) {
            return ['success' => false, 'message' => 'Ukuran file terlalu besar. Maksimum 10MB.'];
        }

        // 2. Tentukan Direktori Penyimpanan
        $baseDir = PUBLIC_PATH . "/uploads/{$module}/{$field}";
        $sizes = ['large', 'medium', 'small'];

        foreach ($sizes as $s) {
            $dir = "{$baseDir}/{$s}";
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
        }

        // 3. Buat Nama File Unik
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if ($extension === 'jpeg') $extension = 'jpg';
        $randomToken = bin2hex(random_bytes(6));
        $filename = "{$module}_{$field}_" . time() . "_{$randomToken}.{$extension}";

        // 4. Buka resource gambar sumber menggunakan GD
        $sourceImage = self::createImageFromSource($file['tmp_name'], $mime);
        if (!$sourceImage) {
            return ['success' => false, 'message' => 'Gagal membaca data gambar yang diupload.'];
        }

        $sourceWidth = imagesx($sourceImage);
        $sourceHeight = imagesy($sourceImage);

        // Ambil konfigurasi dimensi
        $dims = $customDimensions ?? (self::$defaultDimensions[$field] ?? self::$defaultDimensions['default']);

        // 5. Generate ketiga versi: Large, Medium, Small
        foreach ($sizes as $s) {
            $targetWidth = $dims[$s]['width'] ?? 600;
            $targetHeight = $dims[$s]['height'] ?? 600;
            $crop = $dims[$s]['crop'] ?? false;

            $resizedImage = self::resizeImage($sourceImage, $sourceWidth, $sourceHeight, $targetWidth, $targetHeight, $crop);
            $destinationPath = "{$baseDir}/{$s}/{$filename}";

            self::saveImage($resizedImage, $destinationPath, $extension, $mime);
            imagedestroy($resizedImage);
        }

        imagedestroy($sourceImage);

        $urls = [
            'large'  => APP_URL . "/uploads/{$module}/{$field}/large/{$filename}",
            'medium' => APP_URL . "/uploads/{$module}/{$field}/medium/{$filename}",
            'small'  => APP_URL . "/uploads/{$module}/{$field}/small/{$filename}",
        ];

        return [
            'success'  => true,
            'filename' => $filename,
            'module'   => $module,
            'field'    => $field,
            'urls'     => $urls
        ];
    }

    /**
     * Hapus gambar di semua ukuran (Large, Medium, Small)
     */
    public static function deleteImage($filename, $module, $field) {
        if (empty($filename)) return;

        $baseDir = PUBLIC_PATH . "/uploads/{$module}/{$field}";
        $sizes = ['large', 'medium', 'small'];

        foreach ($sizes as $s) {
            $file = "{$baseDir}/{$s}/{$filename}";
            if (file_exists($file)) {
                @unlink($file);
            }
        }
    }

    /**
     * Upload berkas dokumen umum (PDF, DOC, DOCX, XLS, XLSX, JPG, PNG, ZIP, dll)
     */
    public static function uploadFile($file, $folder = 'dokumen', $allowedExtensions = null, $maxSizeMb = 25, $pureRandomName = false) {
        if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
            $errorCode = $file['error'] ?? UPLOAD_ERR_NO_FILE;
            return ['success' => false, 'message' => self::getErrorMessage($errorCode)];
        }

        if ($allowedExtensions === null) {
            $allowedExtensions = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'csv', 'jpg', 'jpeg', 'png', 'webp', 'zip', 'rar'];
        }

        $origName = $file['name'];
        $ext = strtolower(pathinfo($origName, PATHINFO_EXTENSION));

        if (!in_array($ext, $allowedExtensions)) {
            return [
                'success' => false, 
                'message' => 'Format file .' . htmlspecialchars($ext) . ' tidak didukung. Format yang diizinkan: ' . implode(', ', $allowedExtensions)
            ];
        }

        if ($file['size'] > ($maxSizeMb * 1024 * 1024)) {
            return [
                'success' => false, 
                'message' => 'Ukuran file terlalu besar. Maksimum ' . $maxSizeMb . 'MB.'
            ];
        }

        $baseDir = PUBLIC_PATH . "/uploads/{$folder}";
        if (!is_dir($baseDir)) {
            mkdir($baseDir, 0755, true);
        }

        if ($pureRandomName) {
            $randomToken = bin2hex(random_bytes(16));
            $filename = "{$randomToken}.{$ext}";
        } else {
            $randomToken = bin2hex(random_bytes(6));
            $cleanBaseName = preg_replace('/[^a-zA-Z0-9_-]/', '_', pathinfo($origName, PATHINFO_FILENAME));
            $cleanBaseName = substr($cleanBaseName, 0, 30);
            $filename = "{$cleanBaseName}_" . time() . "_{$randomToken}.{$ext}";
        }
        $targetPath = "{$baseDir}/{$filename}";

        if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
            return ['success' => false, 'message' => 'Gagal memindahkan file upload ke server.'];
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $targetPath);
        finfo_close($finfo);

        return [
            'success'   => true,
            'filename'  => $filename,
            'file_path' => "uploads/{$folder}/{$filename}",
            'orig_name' => $origName,
            'file_size' => $file['size'],
            'file_ext'  => $ext,
            'mime_type' => $mime,
            'url'       => APP_URL . "/uploads/{$folder}/{$filename}"
        ];
    }

    /**
     * Upload foto dokumentasi dengan otomatis kompresi dan nama acak murni
     */
    public static function uploadDocumentationImage($file, $folder = 'dokumentasi', $maxWidth = 1600, $maxHeight = 1600, $quality = 82) {
        if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
            $errorCode = $file['error'] ?? UPLOAD_ERR_NO_FILE;
            return ['success' => false, 'message' => self::getErrorMessage($errorCode)];
        }

        // 1. Validasi MIME Type & Ekstensi gambar
        $allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        $heicTypes = ['image/heic', 'image/heif', 'image/heic-sequence', 'image/heif-sequence'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        $origExt = strtolower(pathinfo($file['name'] ?? '', PATHINFO_EXTENSION));
        $isHeic = in_array($mime, $heicTypes) || (in_array($origExt, ['heic', 'heif']) && $mime === 'application/octet-stream');

        $tempConvertedJpg = null;
        $sourceTmpPath = $file['tmp_name'];

        if ($isHeic) {
            $tmpOut = sys_get_temp_dir() . '/heic_' . bin2hex(random_bytes(8)) . '.jpg';
            $converted = false;
            if (function_exists('exec')) {
                $cmd = 'heif-convert -q 90 ' . escapeshellarg($file['tmp_name']) . ' ' . escapeshellarg($tmpOut) . ' 2>&1';
                @exec($cmd, $outLines, $retCode);
                if ($retCode === 0 && file_exists($tmpOut) && filesize($tmpOut) > 0) {
                    $converted = true;
                } else {
                    // Fallback ImageMagick convert/magick jika tersedia
                    $cmdIm = 'convert ' . escapeshellarg($file['tmp_name']) . ' -quality 90 ' . escapeshellarg($tmpOut) . ' 2>&1';
                    @exec($cmdIm, $outIm, $retIm);
                    if ($retIm === 0 && file_exists($tmpOut) && filesize($tmpOut) > 0) {
                        $converted = true;
                    }
                }
            }
            if (!$converted) {
                return ['success' => false, 'message' => 'Format HEIC/HEIF gagal dikonversi. Pastikan browser mengonversi atau server memiliki libheif-examples.'];
            }
            $tempConvertedJpg = $tmpOut;
            $sourceTmpPath = $tmpOut;
            $mime = 'image/jpeg';
        } elseif (!in_array($mime, $allowedTypes)) {
            return ['success' => false, 'message' => 'Format file tidak didukung. Harap upload gambar JPG, PNG, atau WebP.'];
        }

        // Batas ukuran 20MB
        if ($file['size'] > 20 * 1024 * 1024) {
            return ['success' => false, 'message' => 'Ukuran file terlalu besar. Maksimum 20MB.'];
        }

        // 2. Direktori Penyimpanan
        $baseDir = PUBLIC_PATH . "/uploads/{$folder}";
        $largeDir = "{$baseDir}/large";
        $thumbDir = "{$baseDir}/thumb";

        if (!is_dir($largeDir)) mkdir($largeDir, 0755, true);
        if (!is_dir($thumbDir)) mkdir($thumbDir, 0755, true);

        // 3. Nama File Acak Murni
        $ext = $isHeic ? 'jpg' : strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if ($ext === 'jpeg') $ext = 'jpg';
        $randomName = bin2hex(random_bytes(16)) . ".{$ext}";

        $destLarge = "{$largeDir}/{$randomName}";
        $destThumb = "{$thumbDir}/{$randomName}";

        // 4. Kompresi & Resize menggunakan GD
        $sourceImage = self::createImageFromSource($sourceTmpPath, $mime);
        if ($sourceImage) {
            $srcW = imagesx($sourceImage);
            $srcH = imagesy($sourceImage);

            // Large version (maks $maxWidth x $maxHeight, proporsional)
            $largeImg = self::resizeImage($sourceImage, $srcW, $srcH, $maxWidth, $maxHeight, false);
            self::saveImage($largeImg, $destLarge, $ext, $mime);
            imagedestroy($largeImg);

            // Thumbnail version (320 x 320, crop atau fit rapi)
            $thumbImg = self::resizeImage($sourceImage, $srcW, $srcH, 320, 320, false);
            self::saveImage($thumbImg, $destThumb, $ext, $mime);
            imagedestroy($thumbImg);

            imagedestroy($sourceImage);
        } else {
            // Fallback jika GD gagal baca file
            if ($tempConvertedJpg) {
                @rename($tempConvertedJpg, $destLarge);
                $tempConvertedJpg = null;
            } elseif (!move_uploaded_file($sourceTmpPath, $destLarge)) {
                return ['success' => false, 'message' => 'Gagal memproses dan menyimpan file ke server.'];
            }
            @copy($destLarge, $destThumb);
        }
        if ($tempConvertedJpg && file_exists($tempConvertedJpg)) {
            @unlink($tempConvertedJpg);
        }

        $finalSize = file_exists($destLarge) ? filesize($destLarge) : $file['size'];

        return [
            'success'    => true,
            'filename'   => $randomName,
            'file_path'  => "uploads/{$folder}/large/{$randomName}",
            'thumb_path' => "uploads/{$folder}/thumb/{$randomName}",
            'orig_name'  => $file['name'],
            'file_size'  => $finalSize,
            'file_ext'   => $ext,
            'mime_type'  => $mime,
            'url'        => APP_URL . "/uploads/{$folder}/large/{$randomName}",
            'thumb_url'  => APP_URL . "/uploads/{$folder}/thumb/{$randomName}"
        ];
    }

    /**
     * Hapus foto dokumentasi dari disk (large dan thumb)
     */
    public static function deleteDocumentationImage($filePath, $thumbPath = null) {
        if (!empty($filePath)) {
            self::deleteFile($filePath);
        }
        if (!empty($thumbPath)) {
            self::deleteFile($thumbPath);
        }
    }

    /**
     * Hapus berkas file umum
     */
    public static function deleteFile($relativePath) {
        if (empty($relativePath)) return;
        $fullPath = PUBLIC_PATH . '/' . ltrim($relativePath, '/');
        if (file_exists($fullPath) && is_file($fullPath)) {
            @unlink($fullPath);
        }
    }

    /**
     * Dapatkan URL gambar berdasarkan ukuran (small, medium, large)
     */
    public static function getUrl($filename, $module, $field, $size = 'medium', $default = null) {
        if (empty($filename)) return $default;

        $path = PUBLIC_PATH . "/uploads/{$module}/{$field}/{$size}/{$filename}";
        if (file_exists($path)) {
            return APP_URL . "/uploads/{$module}/{$field}/{$size}/{$filename}";
        }

        // Coba ukuran lain jika ukuran diminta belum ada
        $fallback = PUBLIC_PATH . "/uploads/{$module}/{$field}/large/{$filename}";
        if (file_exists($fallback)) {
            return APP_URL . "/uploads/{$module}/{$field}/large/{$filename}";
        }

        return $default;
    }

    /**
     * Dapatkan daftar URL semua versi ukuran gambar (large, medium, small)
     */
    public static function getAllUrls($filename, $module, $field) {
        if (empty($filename)) return null;
        return [
            'large'  => self::getUrl($filename, $module, $field, 'large'),
            'medium' => self::getUrl($filename, $module, $field, 'medium'),
            'small'  => self::getUrl($filename, $module, $field, 'small'),
        ];
    }

    /**
     * Helper membuat GD image resource
     */
    private static function createImageFromSource($path, $mime) {
        switch ($mime) {
            case 'image/jpeg':
                return @imagecreatefromjpeg($path);
            case 'image/png':
                return @imagecreatefrompng($path);
            case 'image/webp':
                return function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($path) : null;
            case 'image/gif':
                return @imagecreatefromgif($path);
            default:
                return null;
        }
    }

    /**
     * Resize / Resample gambar dengan mempertahankan rasio atau crop
     */
    private static function resizeImage($srcImg, $srcW, $srcH, $maxW, $maxH, $crop = false) {
        if ($crop) {
            // Rasio Crop (center-cropped seperti avatar)
            $srcRatio = $srcW / $srcH;
            $targetRatio = $maxW / $maxH;

            if ($srcRatio > $targetRatio) {
                // Gambar lebih lebar
                $cropW = (int)($srcH * $targetRatio);
                $cropH = $srcH;
                $srcX = (int)(($srcW - $cropW) / 2);
                $srcY = 0;
            } else {
                // Gambar lebih tinggi
                $cropW = $srcW;
                $cropH = (int)($srcW / $targetRatio);
                $srcX = 0;
                $srcY = (int)(($srcH - $cropH) / 2);
            }

            $dstImg = imagecreatetruecolor($maxW, $maxH);
            self::preserveTransparency($dstImg);
            imagecopyresampled($dstImg, $srcImg, 0, 0, $srcX, $srcY, $maxW, $maxH, $cropW, $cropH);
            return $dstImg;
        } else {
            // Proportional Fit (tanpa crop)
            $ratio = min($maxW / $srcW, $maxH / $srcH);
            if ($ratio > 1) $ratio = 1; // Jangan perbesar jika asli lebih kecil

            $newW = (int)($srcW * $ratio);
            $newH = (int)($srcH * $ratio);

            $dstImg = imagecreatetruecolor($newW, $newH);
            self::preserveTransparency($dstImg);
            imagecopyresampled($dstImg, $srcImg, 0, 0, 0, 0, $newW, $newH, $srcW, $srcH);
            return $dstImg;
        }
    }

    /**
     * Mempertahankan transparansi untuk PNG dan WebP
     */
    private static function preserveTransparency($img) {
        imagealphablending($img, false);
        imagesavealpha($img, true);
        $transparent = imagecolorallocatealpha($img, 255, 255, 255, 127);
        imagefilledrectangle($img, 0, 0, imagesx($img), imagesy($img), $transparent);
    }

    /**
     * Simpan gambar ke disk dengan kualitas optimal
     */
    private static function saveImage($img, $path, $ext, $mime) {
        if ($ext === 'png' || $mime === 'image/png') {
            imagepng($img, $path, 8);
        } elseif ($ext === 'webp' || $mime === 'image/webp') {
            if (function_exists('imagewebp')) {
                imagewebp($img, $path, 85);
            } else {
                imagejpeg($img, $path, 88);
            }
        } else {
            imagejpeg($img, $path, 88);
        }
    }

    private static function getErrorMessage($code) {
        switch ($code) {
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                return 'Ukuran file melebihi batas server.';
            case UPLOAD_ERR_PARTIAL:
                return 'File hanya terupload sebagian.';
            case UPLOAD_ERR_NO_FILE:
                return 'Tidak ada file yang dipilih.';
            case UPLOAD_ERR_NO_TMP_DIR:
                return 'Folder temporary server tidak ditemukan.';
            case UPLOAD_ERR_CANT_WRITE:
                return 'Gagal menyimpan file ke disk server.';
            default:
                return 'Terjadi kesalahan saat upload file.';
        }
    }
}
