<?php
// ============================================
// GRIT FIT NUTRI - Configuration
// ============================================

// Database
define('DB_HOST', 'localhost');
define('DB_NAME', 'u582313683_gritfitnutri');
define('DB_USER', 'u582313683_gritfitnutri');
define('DB_PASS', 'KiratveerGF!@#123');

// Site
define('SITE_URL', 'https://gritfitnutri.com');
define('SITE_NAME', 'Grit Fit Nutri');
define('SITE_TAGLINE', 'Superfoods for Super You.');
define('SITE_EMAIL', 'contact@gritfitnutri.in');
define('SITE_PHONE', '+91 9811242068');
define('SITE_ADDRESS', 'B-161, 3rd Floor, Lok Vihar, Pitampura, New Delhi, 110034');
define('WHATSAPP_NUMBER', '919811242068');
// Social Media
define('SOCIAL_FACEBOOK', 'https://www.facebook.com/gritfitnutri');
define('SOCIAL_INSTAGRAM', 'https://www.instagram.com/gritfitnutri');
define('SOCIAL_TWITTER', 'https://x.com/gritfitnutri');

// Admin
define('ADMIN_USER', 'admin');
define('ADMIN_PASS_DEFAULT', 'admin123');

// Upload
define('MAX_IMAGE_SIZE', 5 * 1024 * 1024); // 5MB
define('UPLOAD_DIR', __DIR__ . '/uploads/');

// Database connection
function getDB() {
    static $pdo = null;
    if ($pdo === null) {
        try {
            $pdo = new PDO(
                "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
                DB_USER, DB_PASS,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
        } catch (PDOException $e) {
            die("DB Error: " . $e->getMessage());
        }
    }
    return $pdo;
}

// Helper functions
function e($str) {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

function redirect($url) {
    header("Location: " . SITE_URL . $url);
    exit;
}

function slugify($text) {
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    $text = trim($text, '-');
    $text = strtolower($text);
    return preg_replace('~-+~', '-', $text);
}

function uploadImage($file, $subfolder = 'products') {
    if (!is_array($file) || ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        return false;
    }
    if (($file['size'] ?? 0) > MAX_IMAGE_SIZE) {
        return false;
    }
    if (empty($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
        return false;
    }

    $allowed = ['image/jpeg', 'image/png', 'image/webp', 'image/avif'];
    $mime = '';
    if (class_exists('finfo')) {
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = (string) $finfo->file($file['tmp_name']);
    }
    if ($mime === '' || !in_array($mime, $allowed, true)) {
        $ext = strtolower(pathinfo($file['name'] ?? '', PATHINFO_EXTENSION));
        $mime = match ($ext) {
            'jpg', 'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'webp' => 'image/webp',
            'avif' => 'image/avif',
            default => '',
        };
    }
    if ($mime === '' || !in_array($mime, $allowed, true)) {
        return false;
    }
    
    $dir = UPLOAD_DIR . $subfolder . '/';
    if (!is_dir($dir)) mkdir($dir, 0755, true);
    
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid() . '_' . time() . '.' . $ext;
    $path = $dir . $filename;
    
    if (!move_uploaded_file($file['tmp_name'], $path)) {
        return false;
    }
    try {
        optimizeImage($path, $mime);
    } catch (Throwable $e) {
        error_log('GritFitNutri optimizeImage: ' . $e->getMessage());
    }
    return 'uploads/' . $subfolder . '/' . $filename;
}

function optimizeImage($path, $mime) {
    $maxW = 1200;
    switch ($mime) {
        case 'image/jpeg': $img = @imagecreatefromjpeg($path); break;
        case 'image/png': $img = @imagecreatefrompng($path); break;
        case 'image/webp': $img = @imagecreatefromwebp($path); break;
        default: return;
    }
    if (!$img) return;
    $w = imagesx($img); $h = imagesy($img);
    if ($w > $maxW) {
        $newH = intval($h * ($maxW / $w));
        $resized = imagecreatetruecolor($maxW, $newH);
        imagealphablending($resized, false);
        imagesavealpha($resized, true);
        imagecopyresampled($resized, $img, 0, 0, 0, 0, $maxW, $newH, $w, $h);
        imagedestroy($img);
        $img = $resized;
    }
    imagejpeg($img, $path, 82);
    imagedestroy($img);
}

function generateCSRFToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifyCSRF($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// CMS helper layer (settings, content lists, URL helpers). Loaded after the
// constants above so its defaults can fall back to them.
require_once __DIR__ . '/includes/cms.php';
