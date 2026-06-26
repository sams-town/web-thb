-- RS Taman Harapan Baru - Full CMS Database Schema
-- Created: 2026-05-27

CREATE DATABASE IF NOT EXISTS `rsthb2025` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `rsthb2025`;

-- Table: Web Settings
CREATE TABLE IF NOT EXISTS `web_settings` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `hospital_name` VARCHAR(255) NOT NULL DEFAULT 'RS Taman Harapan Baru',
  `hospital_tagline` VARCHAR(255) DEFAULT 'Kesehatan Keluarga Anda Prioritas Kami',
  `logo_url` VARCHAR(255) DEFAULT NULL,
  `favicon_url` VARCHAR(255) DEFAULT NULL,
  `contact_phone` VARCHAR(50) DEFAULT '(021) 1234-5678',
  `contact_email` VARCHAR(100) DEFAULT 'info@rsthb2025.co.id',
  `contact_address` TEXT,
  `wa_number` VARCHAR(20) DEFAULT '6281234567890',
  `wa_text` VARCHAR(255) DEFAULT 'Halo, saya ingin berkonsultasi',
  `maps_embed` TEXT,
  `social_instagram` VARCHAR(255) DEFAULT '#',
  `social_tiktok` VARCHAR(255) DEFAULT '#',
  `social_twitter` VARCHAR(255) DEFAULT '#',
  `theme_color_primary` VARCHAR(20) DEFAULT '#0F2747',
  `theme_color_secondary` VARCHAR(20) DEFAULT '#0E6B73',
  `theme_color_accent` VARCHAR(20) DEFAULT '#D8A24A',
  `navbar_sticky` TINYINT(1) NOT NULL DEFAULT 1,
  `seo_meta_title` VARCHAR(255) DEFAULT NULL,
  `seo_meta_description` TEXT DEFAULT NULL,
  `seo_meta_keywords` VARCHAR(500) DEFAULT NULL,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: SEO Settings
CREATE TABLE IF NOT EXISTS `seo_settings` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `page_key` VARCHAR(100) NOT NULL,
  `meta_title` VARCHAR(255),
  `meta_description` TEXT,
  `meta_keywords` VARCHAR(500),
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `page_key` (`page_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: Hero Slides / Banner Slider
CREATE TABLE IF NOT EXISTS `hero_slides` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(255) NOT NULL,
  `subtitle` TEXT,
  `button1_text` VARCHAR(100),
  `button1_link` VARCHAR(255),
  `button2_text` VARCHAR(100),
  `button2_link` VARCHAR(255),
  `bg_url` VARCHAR(255),
  `main_image_url` VARCHAR(255),
  `sort_order` INT NOT NULL DEFAULT 0,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: Services
CREATE TABLE IF NOT EXISTS `services` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(150) NOT NULL,
  `slug` VARCHAR(150) NOT NULL,
  `short_description` TEXT,
  `full_description` TEXT,
  `icon` VARCHAR(100),
  `image_url` VARCHAR(255),
  `color_primary` VARCHAR(20) DEFAULT '#0F2747',
  `color_secondary` VARCHAR(20) DEFAULT '#0E6B73',
  `sort_order` INT NOT NULL DEFAULT 0,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: Doctors
CREATE TABLE IF NOT EXISTS `doctors` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(150) NOT NULL,
  `specialization` VARCHAR(150) NOT NULL,
  `photo_url` VARCHAR(255),
  `description` TEXT,
  `schedule` TEXT,
  `phone` VARCHAR(50),
  `sort_order` INT NOT NULL DEFAULT 0,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: Rooms (Rawat Inap) - NEW STRUCTURE
CREATE TABLE IF NOT EXISTS `rooms` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(150) NOT NULL,
  `category` ENUM('VIP', 'Kelas 1', 'Kelas 2', 'Kelas 3') NOT NULL DEFAULT 'Kelas 1',
  `cover_url` VARCHAR(255),
  `description` TEXT,
  `sort_order` INT NOT NULL DEFAULT 0,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: Room Facilities
CREATE TABLE IF NOT EXISTS `room_facilities` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `room_id` INT NOT NULL,
  `facility_name` VARCHAR(255) NOT NULL,
  `facility_icon` VARCHAR(100),
  `is_custom` TINYINT(1) DEFAULT 0,
  `sort_order` INT DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `room_id` (`room_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: Room Gallery
CREATE TABLE IF NOT EXISTS `room_gallery` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `room_id` INT NOT NULL,
  `image_url` VARCHAR(255) NOT NULL,
  `sort_order` INT NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `room_id` (`room_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: Outpatient Banners (Rawat Jalan) - NEW STRUCTURE
CREATE TABLE IF NOT EXISTS `outpatient_banners` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(255) NOT NULL,
  `banner_url` VARCHAR(255) NOT NULL,
  `description` TEXT,
  `cta_text` VARCHAR(100),
  `cta_link` VARCHAR(255),
  `sort_order` INT NOT NULL DEFAULT 0,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: Insurances - NEW STRUCTURE (no website)
CREATE TABLE IF NOT EXISTS `insurances` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(150) NOT NULL,
  `logo_url` VARCHAR(255),
  `sort_order` INT NOT NULL DEFAULT 0,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: Media Library
CREATE TABLE IF NOT EXISTS `media` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `file_name` VARCHAR(255) NOT NULL,
  `file_path` VARCHAR(255) NOT NULL,
  `file_type` VARCHAR(50),
  `file_size` BIGINT,
  `folder` ENUM('banner','doctor','service','insurance','logo','room','gallery','other') DEFAULT 'other',
  `uploaded_by` VARCHAR(100),
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: Website Sections (Order & Visibility)
CREATE TABLE IF NOT EXISTS `website_sections` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `section_key` VARCHAR(100) NOT NULL,
  `section_name` VARCHAR(150) NOT NULL,
  `section_title` VARCHAR(255),
  `section_subtitle` TEXT,
  `sort_order` INT NOT NULL DEFAULT 0,
  `is_visible` TINYINT(1) NOT NULL DEFAULT 1,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `section_key` (`section_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert Default Data
INSERT IGNORE INTO `web_settings` (`id`) VALUES (1);

-- Insert Default SEO Settings
INSERT IGNORE INTO `seo_settings` (`page_key`, `meta_title`, `meta_description`, `meta_keywords`) VALUES
('home', 'RS Taman Harapan Baru - Rumah Sakit Modern', 'Rumah sakit modern dengan fasilitas lengkap dan tim dokter spesialis berpengalaman.', 'rumah sakit, dokter, kesehatan, rs, harapan baru');

-- Insert Default Website Sections
INSERT IGNORE INTO `website_sections` (`section_key`, `section_name`, `section_title`, `section_subtitle`, `sort_order`, `is_visible`) VALUES
('hero', 'Hero', NULL, NULL, 1, 1),
('banners', 'Banner Slider', 'Promo & Pengumuman', NULL, 2, 1),
('services', 'Layanan', 'Layanan Medis Kami', NULL, 3, 1),
('doctors', 'Dokter', 'Tim Dokter Spesialis', NULL, 4, 1),
('rooms', 'Rawat Inap', 'Kamar Rawat Inap', NULL, 5, 1),
('outpatient', 'Rawat Jalan', 'Layanan Rawat Jalan', NULL, 6, 1),
('insurances', 'Asuransi', 'Asuransi yang Bekerja Sama', NULL, 7, 1),
('contact', 'Kontak', 'Siap Melayani Anda', NULL, 8, 1);

-- Insert Default Hero Slides
INSERT IGNORE INTO `hero_slides` (`id`, `title`, `subtitle`, `button1_text`, `button1_link`, `button2_text`, `button2_link`, `sort_order`, `is_active`) VALUES
(1, 'Kesehatan Keluarga Anda Prioritas Kami', 'Pelayanan rumah sakit modern dengan dokter profesional.', 'Buat Janji', '#contact', 'Lihat Dokter', '#doctors', 1, 1),
(2, 'Teknologi Medis Modern', 'Layanan cepat dan terpercaya dengan peralatan medis terkini.', 'Konsultasi', '#contact', 'Layanan', '#services', 2, 1),
(3, 'Tim Dokter Spesialis', 'Dokter profesional dan berpengalaman dengan pelayanan nyaman.', 'Cari Dokter', '#doctors', 'Hubungi Kami', '#contact', 3, 1);

-- Insert Default Services
INSERT IGNORE INTO `services` (`id`, `name`, `slug`, `short_description`, `icon`, `color_primary`, `color_secondary`, `sort_order`, `is_active`) VALUES
(1, 'IGD 24 Jam', 'igd', 'Layanan kegawatdaruratan medis respons cepat dengan tim medis siaga 24 jam.', 'bi bi-lightning-charge', '#EF4444', '#DC2626', 1, 1),
(2, 'Klinik Spesialis Dasar', 'klinik-spesialis', 'Konsultasi ahli mencakup Spesialis Penyakit Dalam, Anak, Bedah, serta Kebidanan & Kandungan.', 'bi bi-heart-pulse', '#D4AF37', '#B8860B', 2, 1),
(3, 'Rawat Inap & ICU', 'rawat-inap-icu', 'Fasilitas kamar perawatan yang nyaman serta ruang intensif (ICU) berstandar medis tinggi.', 'bi bi-hospital', '#0F766E', '#0D5D56', 3, 1),
(4, 'Fasilitas Operasi & Bersalin', 'operasi-bersalin', 'Kamar bedah steril (OK) dan ruang bersalin (VK) untuk tindakan medis dan persalinan aman.', 'bi bi-scissors', '#1E3A8A', '#1E40AF', 4, 1),
(5, 'Laboratorium Klinik', 'laboratorium', 'Pemeriksaan spesimen dan patologi klinik yang cepat, presisi, dan akurat.', 'bi bi-vial', '#7C3AED', '#8B5CF6', 5, 1),
(6, 'Radiologi & Pencitraan', 'radiologi', 'Diagnosis visual akurat menggunakan teknologi Rontgen (X-Ray), USG, dan penunjang modern.', 'bi bi-broadcast-pin', '#06B6D4', '#0891B2', 6, 1),
(7, 'Farmasi 24 Jam', 'farmasi', 'Penyediaan obat-obatan esensial dan racikan resep dokter yang siaga sepanjang waktu.', 'bi bi-capsule', '#10B981', '#059669', 7, 1),
(8, 'Medical Check Up (MCU)', 'mcu', 'Pemeriksaan kesehatan preventif menyeluruh untuk deteksi dini dan gaya hidup sehat.', 'bi bi-clipboard2-pulse', '#F59E0B', '#D97706', 8, 1);

-- Insert Default Doctors
INSERT IGNORE INTO `doctors` (`id`, `name`, `specialization`, `schedule`, `sort_order`, `is_active`) VALUES
(1, 'Dr. Andi Wijaya, Sp.KK', 'Spesialis Kulit dan Kelamin', 'Senin: 09:00-14:00', 1, 1),
(2, 'Dr. Siti Nurhaliza, Sp.A', 'Spesialis Anak', 'Selasa: 08:00-13:00', 2, 1),
(3, 'Dr. Rizky Pratama, Sp.JP', 'Spesialis Jantung', 'Senin: 13:00-18:00', 3, 1),
(4, 'Dr. Dewi Lestari, Sp.PD', 'Spesialis Penyakit Dalam', 'Selasa: 14:00-19:00', 4, 1);

-- Insert Default Rooms
INSERT IGNORE INTO `rooms` (`id`, `name`, `category`, `description`, `sort_order`, `is_active`) VALUES
(1, 'Kamar VIP', 'VIP', 'Kamar VIP dengan fasilitas lengkap dan nyaman.', 1, 1),
(2, 'Kamar Kelas 1', 'Kelas 1', 'Kamar Kelas 1 dengan fasilitas modern.', 2, 1),
(3, 'Kamar Kelas 2', 'Kelas 2', 'Kamar Kelas 2 dengan fasilitas standar.', 3, 1),
(4, 'Kamar Kelas 3', 'Kelas 3', 'Kamar Kelas 3 dengan fasilitas dasar.', 4, 1);

-- Insert Default Room Facilities
INSERT IGNORE INTO `room_facilities` (`room_id`, `facility_name`, `facility_icon`, `is_custom`, `sort_order`) VALUES
(1, 'AC', 'bi bi-snow', 0, 1),
(1, 'TV', 'bi bi-tv', 0, 2),
(1, 'WiFi', 'bi bi-wifi', 0, 3),
(1, 'Sofa', 'bi bi-couch', 0, 4),
(1, 'Kamar Mandi Dalam', 'bi bi-droplet', 0, 5),
(1, 'Air Panas', 'bi bi-thermometer-sun', 0, 6),
(1, 'Nurse Call', 'bi bi-bell', 0, 7),
(1, 'Sofa Penunggu', 'bi bi-person-plus', 0, 8),
(2, 'AC', 'bi bi-snow', 0, 1),
(2, 'TV', 'bi bi-tv', 0, 2),
(2, 'WiFi', 'bi bi-wifi', 0, 3),
(2, 'Kamar Mandi Dalam', 'bi bi-droplet', 0, 4),
(2, 'Air Panas', 'bi bi-thermometer-sun', 0, 5),
(3, 'AC', 'bi bi-snow', 0, 1),
(3, 'TV', 'bi bi-tv', 0, 2),
(3, 'WiFi', 'bi bi-wifi', 0, 3),
(3, 'Kamar Mandi Dalam', 'bi bi-droplet', 0, 4),
(4, 'Kipas Angin', 'bi bi-fan', 0, 1),
(4, 'WiFi', 'bi bi-wifi', 0, 2);

-- Insert Default Outpatient Banners
INSERT IGNORE INTO `outpatient_banners` (`id`, `title`, `banner_url`, `description`, `cta_text`, `cta_link`, `sort_order`, `is_active`) VALUES
(1, 'Poli Umum', 'https://coresg-normal.trae.ai/api/ide/v1/text_to_image?prompt=modern%20hospital%20outpatient%20clinic%20banner%20blue%20white&image_size=landscape_16_9', 'Layanan pemeriksaan umum untuk keluarga Anda.', 'Daftar Sekarang', '#contact', 1, 1),
(2, 'Poli Gigi', 'https://coresg-normal.trae.ai/api/ide/v1/text_to_image?prompt=dental%20clinic%20banner%20professional%20hospital&image_size=landscape_16_9', 'Perawatan gigi oleh dokter spesialis berpengalaman.', 'Konsultasi', '#contact', 2, 1);

-- Insert Default Insurances
INSERT IGNORE INTO `insurances` (`id`, `name`, `sort_order`, `is_active`) VALUES
(1, 'BPJS Kesehatan', 1, 1),
(2, 'Prudential', 2, 1),
(3, 'AIA', 3, 1),
(4, 'Allianz', 4, 1);
