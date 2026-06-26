<?php
// Configuration and Helpers

// Detect environment automatically (localhost vs production cPanel)
$isLocal = false;
if (isset($_SERVER['HTTP_HOST'])) {
    $host = $_SERVER['HTTP_HOST'];
    if ($host === 'localhost' || $host === '127.0.0.1' || strpos($host, '192.168.') === 0) {
        $isLocal = true;
    }
} else {
    // CLI mode: check path to determine if it is local development
    $dir = __DIR__;
    if (strpos($dir, 'd:\\wwwww') !== false || strpos($dir, 'C:\\') !== false || strpos($dir, 'D:\\') !== false) {
        $isLocal = true;
    }
}

// Load local override file if exists (should be added to .gitignore)
if (file_exists(__DIR__ . '/config.local.php')) {
    include_once __DIR__ . '/config.local.php';
}

if ($isLocal) {
    if (!defined('DB_HOST')) define('DB_HOST', 'localhost');
    if (!defined('DB_NAME')) define('DB_NAME', 'rsthb2025');
    if (!defined('DB_USER')) define('DB_USER', 'root');
    if (!defined('DB_PASS')) define('DB_PASS', '');
} else {
    // Production (cPanel)
    if (!defined('DB_HOST')) define('DB_HOST', 'localhost');
    if (!defined('DB_NAME')) define('DB_NAME', 'rsthbid_admin');
    if (!defined('DB_USER')) define('DB_USER', 'rsthbid_admin');
    if (!defined('DB_PASS')) define('DB_PASS', 'samboja90');
}


// Brand Colors (from logo)
define('BRAND_PRIMARY', '#0F766E');
define('BRAND_SECONDARY', '#10B981');
define('BRAND_ACCENT', '#F59E0B');

// Get Database Connection
function getDB() {
    static $pdo = null;
    if ($pdo === null) {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";charset=utf8mb4";
            $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]);
            $pdo->exec("CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            $pdo->exec("USE `" . DB_NAME . "`");
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }
    return $pdo;
}

// Check if table exists
function tableExists($table) {
    try {
        $pdo = getDB();
        $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
        return $stmt->rowCount() > 0;
    } catch (Exception $e) {
        return false;
    }
}

// ==================================================
// Web Settings
// ==================================================
function getSettings() {
    if (!tableExists('web_settings')) {
        return [
            'id' => 1,
            'hospital_name' => 'RS Taman Harapan Baru',
            'hospital_tagline' => 'Kesehatan Keluarga Anda Prioritas Kami',
            'logo_url' => null,
            'favicon_url' => null,
            'contact_phone' => '(021) 1234-5678',
            'contact_email' => 'info@rsthb2025.co.id',
            'contact_address' => 'Jl. Contoh No. 123, Jakarta',
            'wa_number' => '6281234567890',
            'wa_text' => 'Halo, saya ingin berkonsultasi',
            'theme_color_primary' => BRAND_PRIMARY,
            'theme_color_secondary' => BRAND_SECONDARY,
            'theme_color_accent' => BRAND_ACCENT,
            'navbar_sticky' => 1,
            'seo_meta_title' => 'RS Taman Harapan Baru - Rumah Sakit Terpercaya',
            'seo_meta_description' => 'RS Taman Harapan Baru menyediakan pelayanan kesehatan terbaik dengan dokter profesional dan fasilitas modern.',
            'seo_meta_keywords' => 'rumah sakit, dokter, kesehatan, rawat inap, rawat jalan'
        ];
    }
    $pdo = getDB();
    $stmt = $pdo->query("SELECT * FROM web_settings WHERE id=1");
    $row = $stmt->fetch();
    return $row ?: [
        'hospital_name' => 'RS Taman Harapan Baru',
        'theme_color_primary' => BRAND_PRIMARY,
        'theme_color_secondary' => BRAND_SECONDARY,
        'theme_color_accent' => BRAND_ACCENT,
        'seo_meta_title' => 'RS Taman Harapan Baru - Rumah Sakit Terpercaya',
        'seo_meta_description' => 'RS Taman Harapan Baru menyediakan pelayanan kesehatan terbaik dengan dokter profesional dan fasilitas modern.',
        'seo_meta_keywords' => 'rumah sakit, dokter, kesehatan, rawat inap, rawat jalan'
    ];
}

// ==================================================
// Hero Slides (Banner Slider)
// ==================================================
function getHeroSlides($activeOnly = true) {
    if (!tableExists('hero_slides')) {
        return [
            [
                'id' => 1,
                'title' => 'Kesehatan Keluarga Anda Prioritas Kami',
                'subtitle' => 'Pelayanan rumah sakit modern dengan dokter profesional.',
                'button1_text' => 'Buat Janji',
                'button1_link' => '#contact',
                'button2_text' => 'Lihat Dokter',
                'button2_link' => '#doctors',
                'sort_order' => 1,
                'is_active' => 1
            ]
        ];
    }
    $pdo = getDB();
    $sql = "SELECT * FROM hero_slides";
    if ($activeOnly) {
        $sql .= " WHERE is_active=1";
    }
    $sql .= " ORDER BY sort_order ASC, id ASC";
    $stmt = $pdo->query($sql);
    return $stmt->fetchAll();
}

// ==================================================
// Services
// ==================================================
function getServices($activeOnly = true) {
    if (!tableExists('services')) {
        return [
            ['id' => 1, 'name' => 'IGD 24 Jam', 'slug' => 'igd', 'short_description' => 'Layanan kegawatdaruratan medis respons cepat dengan tim medis siaga 24 jam.', 'icon' => 'bi bi-lightning-charge', 'sort_order' => 1, 'is_active' => 1, 'color_primary' => '#EF4444', 'color_secondary' => '#DC2626'],
            ['id' => 2, 'name' => 'Klinik Spesialis Dasar', 'slug' => 'klinik-spesialis', 'short_description' => 'Konsultasi ahli mencakup Spesialis Penyakit Dalam, Anak, Bedah, serta Kebidanan & Kandungan.', 'icon' => 'bi bi-heart-pulse', 'sort_order' => 2, 'is_active' => 1, 'color_primary' => '#D4AF37', 'color_secondary' => '#B8860B'],
            ['id' => 3, 'name' => 'Rawat Inap & ICU', 'slug' => 'rawat-inap-icu', 'short_description' => 'Fasilitas kamar perawatan yang nyaman serta ruang intensif (ICU) berstandar medis tinggi.', 'icon' => 'bi bi-hospital', 'sort_order' => 3, 'is_active' => 1, 'color_primary' => '#0F766E', 'color_secondary' => '#0D5D56'],
            ['id' => 4, 'name' => 'Fasilitas Operasi & Bersalin', 'slug' => 'operasi-bersalin', 'short_description' => 'Kamar bedah steril (OK) dan ruang bersalin (VK) untuk tindakan medis dan persalinan aman.', 'icon' => 'bi bi-scissors', 'sort_order' => 4, 'is_active' => 1, 'color_primary' => '#1E3A8A', 'color_secondary' => '#1E40AF'],
            ['id' => 5, 'name' => 'Laboratorium Klinik', 'slug' => 'laboratorium', 'short_description' => 'Pemeriksaan spesimen dan patologi klinik yang cepat, presisi, dan akurat.', 'icon' => 'bi bi-vial', 'sort_order' => 5, 'is_active' => 1, 'color_primary' => '#7C3AED', 'color_secondary' => '#8B5CF6'],
            ['id' => 6, 'name' => 'Radiologi & Pencitraan', 'slug' => 'radiologi', 'short_description' => 'Diagnosis visual akurat menggunakan teknologi Rontgen (X-Ray), USG, dan penunjang modern.', 'icon' => 'bi bi-broadcast-pin', 'sort_order' => 6, 'is_active' => 1, 'color_primary' => '#06B6D4', 'color_secondary' => '#0891B2'],
            ['id' => 7, 'name' => 'Farmasi 24 Jam', 'slug' => 'farmasi', 'short_description' => 'Penyediaan obat-obatan esensial dan racikan resep dokter yang siaga sepanjang waktu.', 'icon' => 'bi bi-capsule', 'sort_order' => 7, 'is_active' => 1, 'color_primary' => '#10B981', 'color_secondary' => '#059669'],
            ['id' => 8, 'name' => 'Medical Check Up (MCU)', 'slug' => 'mcu', 'short_description' => 'Pemeriksaan kesehatan preventif menyeluruh untuk deteksi dini dan gaya hidup sehat.', 'icon' => 'bi bi-clipboard2-pulse', 'sort_order' => 8, 'is_active' => 1, 'color_primary' => '#F59E0B', 'color_secondary' => '#D97706']
        ];
    }
    $pdo = getDB();
    $sql = "SELECT * FROM services";
    if ($activeOnly) {
        $sql .= " WHERE is_active=1";
    }
    $sql .= " ORDER BY sort_order ASC, id ASC";
    $stmt = $pdo->query($sql);
    return $stmt->fetchAll();
}

// ==================================================
// Doctors
// ==================================================
function getDoctors($activeOnly = true) {
    if (!tableExists('doctors')) {
        return [
            ['id' => 1, 'name' => 'Dr. Andi Wijaya, Sp.KK', 'specialization' => 'Spesialis Kulit', 'schedule' => 'Senin: 09:00-14:00', 'sort_order' => 1, 'is_active' => 1],
            ['id' => 2, 'name' => 'Dr. Siti Nurhaliza, Sp.A', 'specialization' => 'Spesialis Anak', 'schedule' => 'Selasa: 08:00-13:00', 'sort_order' => 2, 'is_active' => 1],
            ['id' => 3, 'name' => 'Dr. Rizky Pratama, Sp.JP', 'specialization' => 'Spesialis Jantung', 'schedule' => 'Senin: 13:00-18:00', 'sort_order' => 3, 'is_active' => 1],
            ['id' => 4, 'name' => 'Dr. Dewi Lestari, Sp.PD', 'specialization' => 'Spesialis Dalam', 'schedule' => 'Selasa: 14:00-19:00', 'sort_order' => 4, 'is_active' => 1]
        ];
    }
    $pdo = getDB();
    $sql = "SELECT * FROM doctors";
    if ($activeOnly) {
        $sql .= " WHERE is_active=1";
    }
    $sql .= " ORDER BY sort_order ASC, id ASC";
    $stmt = $pdo->query($sql);
    return $stmt->fetchAll();
}

// ==================================================
// Rooms (Rawat Inap)
// ==================================================
function getRooms($activeOnly = true) {
    if (!tableExists('rooms')) {
        return [
            ['id' => 1, 'name' => 'Kamar VIP', 'category' => 'VIP', 'cover_url' => null, 'description' => 'Kamar VIP dengan fasilitas lengkap dan nyaman.', 'sort_order' => 1, 'is_active' => 1]
        ];
    }
    $pdo = getDB();
    $sql = "SELECT * FROM rooms";
    if ($activeOnly) {
        $sql .= " WHERE is_active=1";
    }
    $sql .= " ORDER BY sort_order ASC, id ASC";
    $stmt = $pdo->query($sql);
    return $stmt->fetchAll();
}

// ==================================================
// Room Facilities
// ==================================================
function getRoomFacilities($roomId) {
    if (!tableExists('room_facilities')) {
        return [];
    }
    $pdo = getDB();
    $stmt = $pdo->prepare("SELECT * FROM room_facilities WHERE room_id=? ORDER BY sort_order ASC");
    $stmt->execute([$roomId]);
    return $stmt->fetchAll();
}

// ==================================================
// Room Gallery
// ==================================================
function getRoomGallery($roomId) {
    if (!tableExists('room_gallery')) {
        return [];
    }
    $pdo = getDB();
    $stmt = $pdo->prepare("SELECT * FROM room_gallery WHERE room_id=? ORDER BY sort_order ASC");
    $stmt->execute([$roomId]);
    return $stmt->fetchAll();
}

// ==================================================
// Outpatient Banners (Rawat Jalan)
// ==================================================
function getOutpatientBanners($activeOnly = true) {
    if (!tableExists('outpatient_banners')) {
        return [];
    }
    $pdo = getDB();
    $sql = "SELECT * FROM outpatient_banners";
    if ($activeOnly) {
        $sql .= " WHERE is_active=1";
    }
    $sql .= " ORDER BY sort_order ASC, id ASC";
    $stmt = $pdo->query($sql);
    return $stmt->fetchAll();
}

// ==================================================
// Insurances
// ==================================================
function getInsurances($activeOnly = true) {
    if (!tableExists('insurances')) {
        return [
            ['id' => 1, 'name' => 'BPJS Kesehatan', 'sort_order' => 1, 'is_active' => 1],
            ['id' => 2, 'name' => 'Prudential', 'sort_order' => 2, 'is_active' => 1]
        ];
    }
    $pdo = getDB();
    $sql = "SELECT * FROM insurances";
    if ($activeOnly) {
        $sql .= " WHERE is_active=1";
    }
    $sql .= " ORDER BY sort_order ASC, id ASC";
    $stmt = $pdo->query($sql);
    return $stmt->fetchAll();
}

// ==================================================
// Website Sections
// ==================================================
function getWebsiteSections() {
    if (!tableExists('website_sections')) {
        return [
            ['section_key' => 'hero', 'section_name' => 'Hero', 'sort_order' => 1, 'is_visible' => 1],
            ['section_key' => 'banners', 'section_name' => 'Banner Slider', 'sort_order' => 2, 'is_visible' => 1],
            ['section_key' => 'services', 'section_name' => 'Layanan', 'sort_order' => 3, 'is_visible' => 1],
            ['section_key' => 'doctors', 'section_name' => 'Dokter', 'sort_order' => 4, 'is_visible' => 1],
            ['section_key' => 'rooms', 'section_name' => 'Rawat Inap', 'sort_order' => 5, 'is_visible' => 1],
            ['section_key' => 'outpatient', 'section_name' => 'Rawat Jalan', 'sort_order' => 6, 'is_visible' => 1],
            ['section_key' => 'insurances', 'section_name' => 'Asuransi', 'sort_order' => 7, 'is_visible' => 1],
            ['section_key' => 'contact', 'section_name' => 'Kontak', 'sort_order' => 8, 'is_visible' => 1]
        ];
    }
    $pdo = getDB();
    $stmt = $pdo->query("SELECT * FROM website_sections ORDER BY sort_order ASC");
    return $stmt->fetchAll();
}

// ==================================================
// Helpers: Upload File
// ==================================================
function uploadFile($file, $folder = 'other') {
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
        return null;
    }

    // Validate file type
    $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
    $fileType = mime_content_type($file['tmp_name']);
    if (!in_array($fileType, $allowedTypes)) {
        return null;
    }

    // Validate file size (max 10MB)
    $maxSize = 10 * 1024 * 1024; // 10MB
    if ($file['size'] > $maxSize) {
        return null;
    }

    $uploadDir = __DIR__ . '/uploads/' . $folder . '/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $fileName = uniqid() . '_' . basename($file['name']);
    $filePath = $uploadDir . $fileName;

    if (move_uploaded_file($file['tmp_name'], $filePath)) {
        return 'uploads/' . $folder . '/' . $fileName;
    }

    return null;
}

// Session Start
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
