<?php
require_once 'config.php';

try {
    $pdo = getDB();
    
    // Check and add all missing web_settings columns
    $webSettingsColumns = [
        'hospital_tagline' => "VARCHAR(255) DEFAULT 'Kesehatan Keluarga Anda Prioritas Kami' AFTER hospital_name",
        'logo_url' => "VARCHAR(255) NULL AFTER hospital_tagline",
        'favicon_url' => "VARCHAR(255) NULL AFTER logo_url",
        'wa_text' => "VARCHAR(255) DEFAULT 'Halo, saya ingin berkonsultasi' AFTER wa_number",
        'maps_embed' => "TEXT NULL AFTER contact_address",
        'social_facebook' => "VARCHAR(255) DEFAULT '#' AFTER maps_embed",
        'social_instagram' => "VARCHAR(255) DEFAULT '#' AFTER social_facebook",
        'social_tiktok' => "VARCHAR(255) DEFAULT '#' AFTER social_instagram",
        'social_twitter' => "VARCHAR(255) DEFAULT '#' AFTER social_instagram",
        'social_youtube' => "VARCHAR(255) DEFAULT '#' AFTER social_twitter",
        'seo_meta_title' => "VARCHAR(255) NULL AFTER theme_color_accent",
        'seo_meta_description' => "TEXT NULL AFTER seo_meta_title",
        'seo_meta_keywords' => "VARCHAR(500) NULL AFTER seo_meta_description"
    ];
    
    foreach ($webSettingsColumns as $col => $definition) {
        $check = $pdo->query("SHOW COLUMNS FROM web_settings LIKE '$col'");
        if (!$check->fetch()) {
            $pdo->exec("ALTER TABLE web_settings ADD COLUMN $col $definition");
            echo "Added $col to web_settings table<br>";
        } else {
            echo "$col already exists in web_settings table<br>";
        }
    }
    
    // Check and add cover_url to rooms if missing
    $checkCoverUrl = $pdo->query("SHOW COLUMNS FROM rooms LIKE 'cover_url'");
    if (!$checkCoverUrl->fetch()) {
        $pdo->exec("ALTER TABLE rooms ADD COLUMN cover_url VARCHAR(255) NULL AFTER category");
        echo "Added cover_url to rooms table<br>";
    } else {
        echo "cover_url already exists in rooms table<br>";
    }
    
    // Check and add main_image_url to hero_slides if missing
    $checkMainImageUrl = $pdo->query("SHOW COLUMNS FROM hero_slides LIKE 'main_image_url'");
    if (!$checkMainImageUrl->fetch()) {
        $pdo->exec("ALTER TABLE hero_slides ADD COLUMN main_image_url VARCHAR(255) NULL AFTER bg_url");
        echo "Added main_image_url to hero_slides table<br>";
    } else {
        echo "main_image_url already exists in hero_slides table<br>";
    }
    
    // Check and add color_primary to services if missing
    $checkColorPrimary = $pdo->query("SHOW COLUMNS FROM services LIKE 'color_primary'");
    if (!$checkColorPrimary->fetch()) {
        $pdo->exec("ALTER TABLE services ADD COLUMN color_primary VARCHAR(20) DEFAULT '#0F2747' AFTER image_url");
        echo "Added color_primary to services table<br>";
    } else {
        echo "color_primary already exists in services table<br>";
    }
    
    // Check and add color_secondary to services if missing
    $checkColorSecondary = $pdo->query("SHOW COLUMNS FROM services LIKE 'color_secondary'");
    if (!$checkColorSecondary->fetch()) {
        $pdo->exec("ALTER TABLE services ADD COLUMN color_secondary VARCHAR(20) DEFAULT '#0E6B73' AFTER color_primary");
        echo "Added color_secondary to services table<br>";
    } else {
        echo "color_secondary already exists in services table<br>";
    }
    
    echo "<br>Database update complete! <a href='index.php'>Go to website</a> | <a href='admin.php'>Go to admin</a>";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>