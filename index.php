<?php
require_once 'config.php';

$settings = getSettings();
$slides = getHeroSlides();
$services = getServices();
$doctors = getDoctors();
$rooms = getRooms();
$outpatientBanners = getOutpatientBanners();
$insurances = getInsurances();
$websiteSections = getWebsiteSections();

$waUrlAppointment = 'https://wa.me/' . $settings['wa_number'] . '?text=' . urlencode($settings['wa_text'] ?? 'Halo, saya ingin membuat janji temu');
$waUrlEmergency = !empty($settings['wa_number']) ? 'https://wa.me/' . $settings['wa_number'] : '#';
$primary = $settings['theme_color_primary'] ?? '#0F2747';
$secondary = $settings['theme_color_secondary'] ?? '#0E6B73';
$accent = $settings['theme_color_accent'] ?? '#D8A24A';
$bg = '#F7FBFC';

function isSectionVisible($key, $sections) {
    foreach ($sections as $sec) {
        if ($sec['section_key'] === $key) {
            return (bool)$sec['is_visible'];
        }
    }
    return true;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($settings['seo_meta_title'] ?? $settings['hospital_name']); ?></title>
    <meta name="description" content="<?php echo htmlspecialchars($settings['seo_meta_description'] ?? ''); ?>">
    <meta name="keywords" content="<?php echo htmlspecialchars($settings['seo_meta_keywords'] ?? ''); ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: <?php echo $primary; ?>;
            --secondary: <?php echo $secondary; ?>;
            --accent: <?php echo $accent; ?>;
            --bg: <?php echo $bg; ?>;
            --text: #18202C;
            --radius-md: 12px;
            --radius-lg: 20px;
            --radius-xl: 28px;
            --shadow-soft: 0 4px 24px rgba(15,39,71,0.06);
            --shadow-medium: 0 8px 36px rgba(15,39,71,0.10);
            --shadow-strong: 0 16px 56px rgba(15,39,71,0.16);
            --transition-normal: 0.4s cubic-bezier(0.4,0,0.2,1);
        }
        * { font-family: 'Poppins', sans-serif; }
        body { background-color: var(--bg); color: var(--text); }
        a { text-decoration: none; }
        
        /* Doctor Card Hover Effects */
        .doctor-card:hover {
            transform: translateY(-8px);
            box-shadow: var(--shadow-strong);
        }
        .doctor-card:hover .doctor-avatar img {
            transform: scale(1.1);
        }
        
        /* Insurance Card Hover Effects */
        .insurance-card:hover {
            box-shadow: var(--shadow-soft);
        }
        
        /* Premium Service Cards */
        .service-card {
            background: white;
            border-radius: 24px;
            padding: 32px 24px;
            box-shadow: 0 10px 40px rgba(15,39,71,0.08);
            transition: all 0.5s ease-in-out;
            cursor: pointer;
            position: relative;
            overflow: hidden;
            border: 2px solid transparent;
        }
        .service-card:hover {
            transform: translateY(-12px);
            box-shadow: 0 25px 70px rgba(197,160,89,0.25);
            border-color: rgba(197,160,89,0.3);
        }
        .service-image-box {
            transition: transform 0.5s ease-in-out;
        }
        .service-card:hover .service-image-box {
            transform: scale(1.12);
        }
        .service-icon-wrapper {
            width: 80px;
            height: 80px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
            transition: all 0.5s ease-in-out;
            position: relative;
            z-index: 1;
        }
        .service-card:hover .service-icon-wrapper {
            transform: scale(1.12);
        }
        .service-icon {
            font-size: 40px;
            color: white;
            transition: all 0.5s ease-in-out;
            position: relative;
            z-index: 1;
        }
        .service-card:hover .service-icon {
            filter: drop-shadow(0 0 12px rgba(255,255,255,0.8));
        }
        .service-title {
            font-size: 1.1rem;
            font-weight: 700;
            margin-bottom: 8px;
            color: var(--text);
            transition: all 0.5s ease-in-out;
            position: relative;
            z-index: 1;
        }
        .service-card:hover .service-title {
            color: #c5a059;
        }
        .service-desc {
            font-size: 0.875rem;
            color: #64748B;
            line-height: 1.7;
            position: relative;
            z-index: 1;
        }
        
        /* Keyframe Animations for Service Icons */
        @keyframes pulse-soft {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-6px); }
        }
        @keyframes rotate-slow {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        @keyframes pulse-wave {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.6; transform: scale(1.1); }
        }
        .pulse-soft {
            animation: pulse-soft 2s ease-in-out infinite;
        }
        .float {
            animation: float 3s ease-in-out infinite;
        }
        .rotate-slow {
            animation: rotate-slow 10s linear infinite;
        }
        .pulse-wave {
            animation: pulse-wave 1.5s ease-in-out infinite;
        }

        /* Topbar */
        .topbar {
            background: linear-gradient(90deg, #064E3B, #059669, #10B981);
            height: 44px;
            display: flex;
            align-items: center;
            color: white;
            font-size: 0.9rem;
        }
        .topbar a {
            color: white;
            opacity: 0.9;
            transition: opacity 0.3s;
        }
        .topbar a:hover { opacity: 1; }
        .badge-premium {
            background: linear-gradient(135deg, #F59E0B, #D97706);
            padding: 6px 14px;
            border-radius: 50px;
            font-weight: 700;
            font-size: 0.8rem;
            text-transform: uppercase;
        }

        /* Main Header */
        .main-header {
            background: white;
            box-shadow: 0 10px 40px rgba(0,0,0,0.08);
            position: sticky;
            top: 0;
            z-index: 999;
        }
        .logo-wrapper {
            display: flex;
            align-items: center;
            gap: 16px;
        }
        .logo-img {
            max-height: 90px;
            object-fit: contain;
            transition: transform 0.3s;
        }
        .logo-wrapper:hover .logo-img { transform: scale(1.03); }
        .hospital-name {
            font-size: 1.25rem;
            font-weight: 800;
            color: var(--primary);
            line-height: 1.2;
        }
        .nav-link-main {
            color: #444;
            font-weight: 600;
            padding: 10px 18px;
            border-radius: 12px;
        }
        .nav-link-main:hover, .nav-link-main.active {
            color: var(--primary);
            background: rgba(15,39,71,0.06);
        }
        .btn-gold {
            background: linear-gradient(135deg, var(--accent) 0%, #c69438 100%);
            color: var(--text);
            border: none;
            font-weight: 700;
            padding: 10px 26px;
            border-radius: var(--radius-lg);
        }
        .btn-wa {
            background: linear-gradient(135deg, #25d366 0%, #12b74c 100%);
            color: white;
            border: none;
            font-weight: 700;
            padding: 10px 26px;
            border-radius: var(--radius-lg);
        }
        .btn-wa:hover { color: white; }
        .btn-emergency {
            background: linear-gradient(135deg, #D52B1E 0%, #FF5A3D 100%);
            color: white;
            border: none;
            font-weight: 700;
            padding: 14px 28px;
            border-radius: 999px;
            transition: all 0.3s ease;
        }
        .btn-emergency:hover {
            filter: brightness(1.1);
            box-shadow: 0 8px 24px rgba(213, 43, 30, 0.4);
            color: white;
        }

        /* Responsive Header */
        @media (max-width: 992px) {
            .logo-img { max-height: 70px; }
            .hospital-name { font-size: 1.1rem; }
        }
        @media (max-width: 768px) {
            .logo-img { max-height: 56px; }
            .hospital-name { display: none; }
        }

        /* CTA Buttons */
        .btn-cta {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            border: none;
            font-weight: 700;
            padding: 12px 28px;
            border-radius: var(--radius-lg);
            box-shadow: 0 6px 26px rgba(15,39,71,0.35);
        }
        .btn-cta:hover { transform: translateY(-2px); }
        .btn-outline-cta {
            border: 2px solid var(--primary);
            color: var(--primary);
            font-weight: 700;
            padding: 12px 28px;
            border-radius: var(--radius-lg);
        }
        .btn-accent {
            background: linear-gradient(135deg, var(--accent) 0%, #c69438 100%);
            color: var(--text);
            border: none;
            font-weight: 700;
            padding: 12px 32px;
            border-radius: var(--radius-lg);
            box-shadow: 0 6px 26px rgba(216,162,74,0.4);
        }

        /* Hero Section */
        .hero-section {
            background: linear-gradient(135deg, rgba(15,39,71,0.05) 0%, rgba(14,107,115,0.05) 100%);
            padding: 80px 0;
            position: relative;
            overflow: hidden;
            min-height: 720px;
        }
        .hero-section::before {
            content: '';
            position: absolute;
            top: -150px;
            right: -200px;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(14,107,115,0.08) 0%, transparent 70%);
            border-radius: 50%;
        }
        .hero-badge {
            display: inline-block;
            background: rgba(216,162,74,0.12);
            color: var(--accent);
            padding: 8px 20px;
            border-radius: 50px;
            font-weight: 700;
            font-size: 0.85rem;
            margin-bottom: 20px;
        }
        .hero-title {
            font-size: clamp(2.2rem, 4.5vw, 3.5rem);
            font-weight: 800;
            line-height: 1.1;
            color: var(--text);
        }
        .hero-title span { color: var(--primary); }
        .hero-subtitle {
            font-size: 1.1rem;
            color: #666;
            margin-top: 18px;
            margin-bottom: 36px;
        }
        .hero-stats {
            display: flex;
            flex-wrap: wrap;
            gap: 32px;
            margin-top: 40px;
        }
        .stat-item {
            display: flex;
            gap: 14px;
            align-items: center;
        }
        .stat-icon {
            width: 52px;
            height: 52px;
            border-radius: var(--radius-md);
            background: linear-gradient(135deg, rgba(15,39,71,0.08), rgba(14,107,115,0.08));
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary);
            font-size: 1.3rem;
        }
        .stat-number {
            font-size: 1.8rem;
            font-weight: 800;
            color: var(--text);
        }
        .hero-visual {
            position: relative;
        }
        .hero-main-card {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            border-radius: var(--radius-xl);
            height: 520px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 6rem;
            box-shadow: var(--shadow-strong);
        }
        .float-info {
            position: absolute;
            bottom: 30px;
            left: -30px;
            background: white;
            border-radius: var(--radius-lg);
            padding: 20px 24px;
            box-shadow: var(--shadow-medium);
        }
        .float-rating {
            position: absolute;
            top: 30px;
            right: -30px;
            background: white;
            border-radius: var(--radius-lg);
            padding: 20px 24px;
            box-shadow: var(--shadow-medium);
        }

        /* Sections */
        .section { padding: 100px 0; }
        .section-header { text-align: center; margin-bottom: 60px; }
        .section-kicker {
            color: var(--secondary);
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
            font-size: 0.85rem;
            margin-bottom: 12px;
        }
        .section-title {
            font-size: 2.4rem;
            font-weight: 800;
        }

        /* Service Cards */
        .service-card {
            background: white;
            border-radius: var(--radius-lg);
            padding: 36px 30px;
            box-shadow: var(--shadow-soft);
            transition: all var(--transition-normal);
        }
        .service-card:hover {
            transform: translateY(-8px);
            box-shadow: var(--shadow-medium);
        }
        .service-icon-box {
            width: 76px;
            height: 76px;
            border-radius: var(--radius-md);
            background: linear-gradient(135deg, rgba(15,39,71,0.08), rgba(14,107,115,0.08));
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary);
            font-size: 1.9rem;
            margin-bottom: 22px;
        }

        /* Doctor Cards */
        .doctor-card {
            background: white;
            border-radius: var(--radius-lg);
            overflow: hidden;
            box-shadow: var(--shadow-soft);
            transition: all var(--transition-normal);
        }
        .doctor-card:hover {
            transform: translateY(-8px);
            box-shadow: var(--shadow-medium);
        }
        .doctor-avatar {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            height: 280px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 4.5rem;
        }
        .doctor-info { padding: 28px; }

        /* Room Cards */
        .room-card {
            background: white;
            border-radius: var(--radius-lg);
            overflow: hidden;
            box-shadow: var(--shadow-soft);
        }
        .room-badge {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            padding: 6px 14px;
            border-radius: 50px;
            font-size: 0.8rem;
            font-weight: 700;
        }

        /* Insurance Slider */
        .insurance-card {
            background: white;
            border-radius: var(--radius-lg);
            padding: 32px;
            box-shadow: var(--shadow-soft);
            text-align: center;
        }
        .insurance-logo {
            width: 120px;
            height: 120px;
            border-radius: var(--radius-lg);
            background: linear-gradient(135deg, rgba(15,39,71,0.05), rgba(14,107,115,0.05));
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 16px;
            color: var(--primary);
            font-size: 2.5rem;
        }

        /* Stats Section */
        .stats-premium {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            padding: 80px 0;
        }
        .stat-card-gold {
            background: rgba(255,255,255,0.12);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255,255,255,0.2);
            border-radius: var(--radius-lg);
            padding: 36px 28px;
            text-align: center;
            color: white;
        }
        .stat-number-gold {
            font-size: 3rem;
            font-weight: 800;
            color: var(--accent);
        }

        /* Footer */
        .footer {
            background: linear-gradient(180deg, #0F766E 0%, #064E3B 100%);
            color: white;
            padding: 80px 0 30px;
        }
        .footer .text-white-50 {
            color: rgba(255,255,255,0.8) !important;
        }

        /* Floating WhatsApp */
        .wa-float {
            position: fixed;
            right: 28px;
            bottom: 28px;
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, #25d366 0%, #12b74c 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 2.2rem;
            box-shadow: 0 12px 40px rgba(37,211,102,0.5);
            z-index: 1000;
            transition: all 0.4s cubic-bezier(0.4,0,0.2,1);
            opacity: 1;
            transform: translateY(0) scale(1);
        }
        .wa-float:hover {
            transform: translateY(-4px) scale(1.12);
            box-shadow: 0 16px 50px rgba(37,211,102,0.6);
        }
        .wa-float.scroll-effect {
            transform: translateY(0) scale(0.95);
        }
        .igd-float {
            background: linear-gradient(135deg, #DC2626 0%, #EF4444 100%);
            box-shadow: 0 12px 40px rgba(239,68,68,0.5);
        }
        .igd-float:hover {
            box-shadow: 0 16px 50px rgba(239,68,68,0.6);
        }
        .ambulance-float {
            background: linear-gradient(135deg, #0F766E 0%, #10B981 100%);
            box-shadow: 0 12px 40px rgba(16,185,129,0.5);
        }
        .ambulance-float:hover {
            box-shadow: 0 16px 50px rgba(16,185,129,0.6);
        }

        /* Responsive */
        @media (max-width: 992px) {
            .float-info, .float-rating { display: none; }
        }
        @media (max-width: 768px) {
            /* Topbar Mobile */
            .topbar { height: auto; padding: 10px 0; }
            .topbar .container { padding: 0 12px; }
            .topbar > .container > .d-flex { 
                flex-direction: column; 
                gap: 8px;
                align-items: flex-start;
            }
            .topbar .d-flex > div:first-child { 
                display: flex;
                gap: 16px;
            }
            .topbar .d-flex > div:first-child a { 
                font-size: 0.85rem;
                display: flex;
                align-items: center;
                gap: 4px;
            }
            .topbar .d-flex > div:last-child { 
                display: flex;
                flex-wrap: wrap;
                gap: 10px;
                align-items: center;
                width: 100%;
            }
            .topbar .d-flex > div:last-child > div,
            .topbar .d-flex > div:last-child > a {
                font-size: 0.85rem;
            }
            .badge-premium { 
                font-size: 0.7rem; 
                padding: 4px 12px; 
                white-space: nowrap;
            }

            /* Hero Section Mobile */
            .hero-section { padding: 40px 0; min-height: auto; }
            .hero-title { font-size: 1.8rem; }
            .hero-subtitle { font-size: 0.95rem; }
            .hero-stats { gap: 16px; margin-top: 24px; }
            .stat-number { font-size: 1.3rem; }
            .stat-icon { width: 44px; height: 44px; font-size: 1.1rem; }
            .hero-main-card { height: 300px; font-size: 4rem; }
            .hero-badge { font-size: 0.75rem; padding: 6px 16px; }

            /* Sections Mobile */
            .section { padding: 50px 0; }
            .section-header { margin-bottom: 32px; }
            .section-title { font-size: 1.6rem; }
            .section-kicker { font-size: 0.75rem; }

            /* Cards Mobile */
            .doctor-avatar { height: 220px; }
            .doctor-info { padding: 18px; }
            .room-card .p-5 { padding: 20px; }
            .room-card > div:first-child { height: 220px; }
            .service-card { padding: 24px 20px; }
            .insurance-card { padding: 24px; }
            .insurance-logo { width: 90px; height: 90px; font-size: 2rem; }

            /* Stats Section Mobile */
            .stats-premium { padding: 40px 0; }
            .stat-card-gold { padding: 24px 20px; }
            .stat-number-gold { font-size: 2rem; }

            /* Footer Mobile */
            .footer { padding: 40px 0 20px; }

            /* Floating WhatsApp Mobile */
            .wa-float { width: 58px; height: 58px; right: 18px; bottom: 18px; font-size: 1.8rem; }
            .igd-float { bottom: 88px !important; }
            .ambulance-float { bottom: 158px !important; }
        }
        @media (max-width: 576px) {
            /* Extra Small Screens */
            .topbar .d-flex > div:first-child { 
                gap: 12px;
            }
            .topbar .d-flex > div:last-child { 
                flex-direction: column;
                align-items: flex-start;
                gap: 8px;
            }
            .hero-title { font-size: 1.5rem; }
            .btn-cta, .btn-outline-cta { padding: 10px 20px; font-size: 0.9rem; }
            .doctor-avatar { height: 200px; }
        }
    </style>
</head>
<body>
    <!-- Topbar -->
    <div class="topbar">
        <div class="container" style="max-width: 1440px;">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div class="d-flex align-items-center gap-4">
                    <a href="<?php echo htmlspecialchars($settings['social_instagram'] ?? '#'); ?>" target="_blank">
                        <i class="bi bi-instagram me-1"></i> @rsthb
                    </a>
                    <a href="<?php echo htmlspecialchars($settings['social_tiktok'] ?? '#'); ?>" target="_blank">
                        <i class="bi bi-tiktok me-1"></i> @rsthb.id
                    </a>
                </div>
                <div class="d-flex align-items-center gap-4">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-telephone-fill"></i>
                        <span><?php echo htmlspecialchars($settings['contact_phone']); ?></span>
                    </div>
                    <a href="<?php echo $waUrlAppointment; ?>" target="_blank" class="d-flex align-items-center gap-2 text-white">
                        <i class="bi bi-whatsapp"></i>
                        <span><?php echo htmlspecialchars(substr($settings['wa_number'], 2)); ?></span>
                    </a>
                    <span class="badge-premium">Core Value Life</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Header -->
    <header class="main-header">
        <div class="container" style="max-width: 1440px;">
            <div class="d-flex justify-content-between align-items-center py-3">
                <div class="logo-wrapper">
                    <?php if (!empty($settings['logo_url'])): ?>
                        <img src="<?php echo htmlspecialchars($settings['logo_url']); ?>" alt="Logo" class="logo-img">
                    <?php else: ?>
                        <i class="bi bi-hospital fs-1" style="color: var(--primary);"></i>
                    <?php endif; ?>
                    <div class="hospital-name">
                        RS TAMAN<br>HARAPAN BARU
                    </div>
                </div>
                <nav class="d-none d-lg-flex align-items-center gap-1">
                    <a href="#home" class="nav-link-main active">Beranda</a>
                    <a href="#services" class="nav-link-main">Layanan</a>
                    <a href="#doctors" class="nav-link-main">Dokter</a>
                    <a href="#rooms" class="nav-link-main">Rawat Inap</a>
                    <a href="#insurances" class="nav-link-main">Asuransi</a>
                    <a href="#contact" class="nav-link-main">Kontak</a>
                </nav>
                <div class="d-none d-lg-flex align-items-center gap-2">
                    <a href="<?php echo $waUrlEmergency; ?>" class="btn-emergency" target="_blank" <?php echo empty($settings['wa_number']) ? 'disabled' : ''; ?>>
                        <i class="bi bi-truck-medical me-2"></i> Emergency 24 Jam
                    </a>
                    <a href="<?php echo $waUrlAppointment; ?>" class="btn-wa" target="_blank">
                        <i class="bi bi-whatsapp me-2"></i> WhatsApp
                    </a>
                </div>
                <button class="navbar-toggler d-lg-none border-0" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileMenu">
                    <i class="bi bi-list fs-2" style="color: var(--primary);"></i>
                </button>
            </div>
        </div>
    </header>

    <!-- Mobile Menu Offcanvas -->
    <div class="offcanvas offcanvas-end" tabindex="-1" id="mobileMenu">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title fw-bold" style="color: var(--primary);">Menu</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body">
            <a href="#home" class="nav-link-main d-block mb-2" data-bs-dismiss="offcanvas">Beranda</a>
            <a href="#services" class="nav-link-main d-block mb-2" data-bs-dismiss="offcanvas">Layanan</a>
            <a href="#doctors" class="nav-link-main d-block mb-2" data-bs-dismiss="offcanvas">Dokter</a>
            <a href="#rooms" class="nav-link-main d-block mb-2" data-bs-dismiss="offcanvas">Rawat Inap</a>
            <a href="#insurances" class="nav-link-main d-block mb-2" data-bs-dismiss="offcanvas">Asuransi</a>
            <a href="#contact" class="nav-link-main d-block mb-4" data-bs-dismiss="offcanvas">Kontak</a>
            <a href="<?php echo $waUrlEmergency; ?>" class="btn-emergency w-100 mb-2" target="_blank" <?php echo empty($settings['wa_number']) ? 'disabled' : ''; ?>>
                <i class="bi bi-truck-medical me-2"></i> Emergency 24 Jam
            </a>
            <a href="<?php echo $waUrlAppointment; ?>" class="btn-wa w-100" target="_blank">
                <i class="bi bi-whatsapp me-2"></i> WhatsApp
            </a>
        </div>
    </div>

    <!-- Hero Section -->
    <?php if (isSectionVisible('hero', $websiteSections)): ?>
    <section id="home" class="hero-section">
        <div class="swiper hero-swiper" style="width:100%;">
            <div class="swiper-wrapper">
                <?php foreach ($slides as $slide): ?>
                <div class="swiper-slide">
                    <div class="container" style="max-width:1440px;">
                        <div class="row align-items-center g-5" style="min-height:700px;">
                            <div class="col-lg-6">
                                <span class="hero-badge"><i class="bi bi-shield-check me-1"></i> Terpercaya Sejak 2010</span>
                                <h1 class="hero-title">
                                    <?php echo htmlspecialchars($slide['title'] ?? $settings['hospital_tagline'] ?? 'Kesehatan Keluarga Anda Prioritas Utama Kami'); ?>
                                </h1>
                                <p class="hero-subtitle">
                                    <?php echo htmlspecialchars($slide['subtitle'] ?? 'Rumah sakit modern dengan fasilitas lengkap dan tim dokter spesialis berpengalaman.'); ?>
                                </p>
                                <div class="d-flex flex-wrap gap-3">
                                    <?php if (!empty($slide['button1_text']) && !empty($slide['button1_link'])): ?>
                                        <a href="<?php echo htmlspecialchars($slide['button1_link']); ?>" class="btn-cta btn-lg">
                                            <?php echo htmlspecialchars($slide['button1_text']); ?>
                                        </a>
                                    <?php endif; ?>
                                    <?php if (!empty($slide['button2_text']) && !empty($slide['button2_link'])): ?>
                                        <a href="<?php echo htmlspecialchars($slide['button2_link']); ?>" class="btn-outline-cta btn-lg">
                                            <?php echo htmlspecialchars($slide['button2_text']); ?>
                                        </a>
                                    <?php endif; ?>
                                </div>
                                <div class="hero-stats">
                                    <div class="stat-item">
                                        <div class="stat-icon"><i class="bi bi-people"></i></div>
                                        <div><div class="stat-number"><?php echo count($doctors); ?>+</div><div class="text-muted small">Dokter Spesialis</div></div>
                                    </div>
                                    <div class="stat-item">
                                        <div class="stat-icon"><i class="bi bi-heart-pulse"></i></div>
                                        <div><div class="stat-number">24 Jam</div><div class="text-muted small">Layanan IGD</div></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="hero-visual">
                                    <?php if (!empty($slide['main_image_url'])): ?>
                                        <img src="<?php echo htmlspecialchars($slide['main_image_url']); ?>" alt="Hero Image" style="width:100%; height:auto; border-radius: var(--radius-xl);">
                                    <?php else: ?>
                                        <div class="hero-main-card">
                                            <i class="bi bi-hospital"></i>
                                        </div>
                                        <div class="float-info">
                                            <div class="d-flex gap-3 align-items-center">
                                                <i class="bi bi-check-circle-fill text-success fs-3"></i>
                                                <div><h6 class="fw-bold mb-0">Pelayanan Cepat</h6><small class="text-muted">Antrian Singkat</small></div>
                                            </div>
                                        </div>
                                        <div class="float-rating">
                                            <div class="d-flex gap-3 align-items-center">
                                                <i class="bi bi-star-fill" style="color: var(--accent); font-size: 1.9rem;"></i>
                                                <div><h6 class="fw-bold mb-0">Rating 4.9</h6><small class="text-muted">Dari ribuan pasien</small></div>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <div class="swiper-button-prev hero-swiper-button-prev" style="color: var(--primary);"></div>
            <div class="swiper-button-next hero-swiper-button-next" style="color: var(--primary);"></div>
            <div class="swiper-pagination hero-swiper-pagination"></div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Services Section -->
    <section id="services" class="section bg-white">
        <div class="container" style="max-width: 1440px;">
            <div class="section-header">
                <p class="section-kicker">LAYANAN UNGGULAN</p>
                <h2 class="section-title">Layanan Medis Kami</h2>
            </div>
            
            <!-- Desktop View -->
            <div class="row g-4 d-none d-lg-flex">
                <?php foreach ($services as $service): ?>
                    <div class="col-lg-3">
                        <div class="service-card" style="display: flex; flex-direction: column; align-items: center; text-align: center; padding: 32px;">
                            <?php if (!empty($service['image_url'])): ?>
                                <div class="service-image-box" style="width: 120px; height: 120px; display: flex; align-items: center; justify-content: center; box-shadow: 0 10px 30px rgba(15,39,71,0.12); border-radius: 24px; margin-bottom: 20px; overflow: hidden;">
                                    <img src="<?php echo htmlspecialchars($service['image_url']); ?>" alt="<?php echo htmlspecialchars($service['name']); ?>" style="width: 100%; height: 100%; object-fit: cover;">
                                </div>
                            <?php else: ?>
                                <div class="service-icon-wrapper" style="background: linear-gradient(135deg, <?php echo htmlspecialchars($service['color_primary'] ?? '#0F2747'); ?>, <?php echo htmlspecialchars($service['color_secondary'] ?? '#0E6B73'); ?>);">
                                    <i class="bi <?php echo htmlspecialchars(str_replace('bi ', '', $service['icon'] ?? 'bi-star')); ?> service-icon"></i>
                                </div>
                            <?php endif; ?>
                            <h5 class="service-title" style="text-align: center;"><?php echo htmlspecialchars($service['name']); ?></h5>
                            <p class="service-desc" style="text-align: center;"><?php echo htmlspecialchars($service['short_description'] ?? ''); ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <!-- Mobile Slider View -->
            <div class="d-lg-none">
                <div class="swiper service-swiper">
                    <div class="swiper-wrapper">
                        <?php foreach ($services as $service): ?>
                            <div class="swiper-slide">
                                <div class="service-card" style="display: flex; flex-direction: column; align-items: center; text-align: center; padding: 32px;">
                                    <?php if (!empty($service['image_url'])): ?>
                                        <div class="service-image-box" style="width: 100px; height: 100px; display: flex; align-items: center; justify-content: center; box-shadow: 0 10px 30px rgba(15,39,71,0.12); border-radius: 20px; margin-bottom: 18px; overflow: hidden;">
                                            <img src="<?php echo htmlspecialchars($service['image_url']); ?>" alt="<?php echo htmlspecialchars($service['name']); ?>" style="width: 100%; height: 100%; object-fit: cover;">
                                        </div>
                                    <?php else: ?>
                                        <div class="service-icon-wrapper" style="background: linear-gradient(135deg, <?php echo htmlspecialchars($service['color_primary'] ?? '#0F2747'); ?>, <?php echo htmlspecialchars($service['color_secondary'] ?? '#0E6B73'); ?>);">
                                            <i class="bi <?php echo htmlspecialchars(str_replace('bi ', '', $service['icon'] ?? 'bi-star')); ?> service-icon"></i>
                                        </div>
                                    <?php endif; ?>
                                    <h5 class="service-title" style="text-align: center;"><?php echo htmlspecialchars($service['name']); ?></h5>
                                    <p class="service-desc" style="text-align: center;"><?php echo htmlspecialchars(substr($service['short_description'] ?? '', 0, 60)) . '...'; ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="swiper-pagination service-swiper-pagination"></div>
                </div>
            </div>
        </div>
    </section>

    <!-- Doctors Section -->
    <?php if (isSectionVisible('doctors', $websiteSections) && !empty($doctors)): ?>
    <section id="doctors" class="section">
        <div class="container" style="max-width: 1440px;">
            <div class="section-header">
                <p class="section-kicker">DOKTER UNGGULAN</p>
                <h2 class="section-title">DOKTER SPESIALIS KAMI</h2>
            </div>
            <div class="swiper doctor-swiper">
                <div class="swiper-wrapper">
                    <?php foreach ($doctors as $doc): ?>
                    <div class="swiper-slide">
                        <div class="doctor-card" style="background: white; border-radius: var(--radius-xl); overflow: hidden; box-shadow: var(--shadow-soft); transition: all 0.4s cubic-bezier(0.4,0,0.2,1);">
                            <div class="doctor-avatar" style="height: 280px; overflow: hidden;">
                                <?php if (!empty($doc['photo_url'])): ?>
                                    <img src="<?php echo htmlspecialchars($doc['photo_url']); ?>" alt="<?php echo htmlspecialchars($doc['name']); ?>" style="width:100%;height:100%;object-fit:cover; transition: transform 0.6s cubic-bezier(0.4,0,0.2,1);">
                                <?php else: ?>
                                    <div style="display:flex;align-items:center;justify-content:center;width:100%;height:100%;background:linear-gradient(135deg,var(--primary),var(--secondary));"><i class="bi bi-person-heart" style="font-size:4rem;color:white;"></i></div>
                                <?php endif; ?>
                            </div>
                            <div class="doctor-info" style="padding: 24px;">
                                <h5 class="fw-bold mb-1" style="color: var(--primary);"><?php echo htmlspecialchars($doc['name']); ?></h5>
                                <p class="mb-2" style="color: var(--secondary);"><?php echo htmlspecialchars($doc['specialization']); ?></p>
                                <p class="text-muted mb-4" style="font-size: 0.9rem;"><?php echo htmlspecialchars($doc['schedule'] ?? '-'); ?></p>
                                <a href="#doctors" class="btn btn-outline-primary w-100" style="border-radius: var(--radius-lg);"><i class="bi bi-person-lines-fill me-2"></i> Lihat Profil</a>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <div class="swiper-button-prev doctor-swiper-button-prev"></div>
                <div class="swiper-button-next doctor-swiper-button-next"></div>
                <div class="swiper-pagination doctor-swiper-pagination"></div>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Rooms (Rawat Inap) Section -->
    <?php if (isSectionVisible('rooms', $websiteSections) && !empty($rooms)): ?>
    <section id="rooms" class="section bg-white">
        <div class="container" style="max-width: 1440px;">
            <div class="section-header">
                <p class="section-kicker">RAWAT INAP</p>
                <h2 class="section-title">Kamar Rawat Inap</h2>
            </div>
            <div class="row g-4">
                <?php foreach ($rooms as $room): 
                    $facilities = getRoomFacilities($room['id']);
                    $gallery = getRoomGallery($room['id']);
                ?>
                <div class="col-md-6 col-lg-6">
                    <div class="room-card" style="border-radius: var(--radius-xl); overflow: hidden;">
                        <div style="height: 320px; background: <?php echo (!empty($room['cover_url'])) ? ('url(\''.htmlspecialchars($room['cover_url']).'\') center/cover') : ('linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%)'); ?>; display:flex; align-items:center; justify-content:center; color:white; font-size:4rem;">
                            <?php if (empty($room['cover_url'])): ?>
                                <i class="bi bi-house-heart"></i>
                            <?php endif; ?>
                        </div>
                        <div class="p-5">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="room-badge"><?php echo htmlspecialchars($room['category']); ?></span>
                            </div>
                            <h4 class="fw-bold mb-2"><?php echo htmlspecialchars($room['name']); ?></h4>
                            <p class="text-muted mb-3"><?php echo htmlspecialchars($room['description'] ?? ''); ?></p>
                            
                            <?php if (!empty($facilities)): ?>
                            <div class="mb-4">
                                <h6 class="fw-bold mb-2">Fasilitas:</h6>
                                <div class="d-flex flex-wrap gap-2">
                                    <?php foreach ($facilities as $fac): ?>
                                        <span class="badge text-bg-light" style="border-radius: 50px; padding: 8px 14px;">
                                            <i class="<?php echo htmlspecialchars($fac['facility_icon'] ?? 'bi bi-check'); ?> me-1"></i>
                                            <?php echo htmlspecialchars($fac['facility_name']); ?>
                                        </span>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <?php endif; ?>

                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Outpatient Banners (Rawat Jalan) Section -->
    <?php if (isSectionVisible('outpatient', $websiteSections) && !empty($outpatientBanners)): ?>
    <section id="outpatient" class="section">
        <div class="container" style="max-width: 1440px;">
            <div class="section-header">
                <p class="section-kicker">RAWAT JALAN</p>
                <h2 class="section-title">Layanan Rawat Jalan</h2>
            </div>
            <div class="row g-4">
                <?php foreach ($outpatientBanners as $banner): ?>
                <div class="col-12">
                    <div class="outpatient-banner" style="border-radius: var(--radius-xl); overflow: hidden; position: relative; height: 380px; background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);">
                        <?php if (!empty($banner['banner_url'])): ?>
                            <img src="<?php echo htmlspecialchars($banner['banner_url']); ?>" alt="<?php echo htmlspecialchars($banner['title']); ?>" style="position:absolute; inset:0; width:100%; height:100%; object-fit:cover; opacity: 0.95;">
                        <?php endif; ?>
                        <div style="position:absolute; inset:0; background: linear-gradient(135deg, rgba(15,39,71,0.5) 0%, rgba(14,107,115,0.5) 100%); display:flex; align-items:center;">
                            <div class="container" style="max-width: 1440px;">
                                <div class="row justify-content-center">
                                    <div class="col-lg-8 text-center text-white">
                                        <h2 class="fw-bold mb-3" style="font-size: 2.5rem;"><?php echo htmlspecialchars($banner['title']); ?></h2>
                                        <p class="mb-4" style="font-size: 1.2rem; opacity: 0.95;"><?php echo htmlspecialchars($banner['description'] ?? ''); ?></p>
                                        <?php if (!empty($banner['cta_text'])): ?>
                                            <a href="<?php echo htmlspecialchars($banner['cta_link'] ?? '#contact'); ?>" class="btn-gold btn-lg">
                                                <?php echo htmlspecialchars($banner['cta_text']); ?>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Insurances Section -->
    <?php if (isSectionVisible('insurances', $websiteSections) && !empty($insurances)): ?>
    <section class="section">
        <div class="container" style="max-width: 1440px;">
            <div class="section-header">
                <p class="section-kicker">KERJASAMA</p>
                <h2 class="section-title">Asuransi Rekanan</h2>
            </div>
            <div class="swiper insurance-swiper">
                <div class="swiper-wrapper">
                    <?php foreach ($insurances as $ins): ?>
                    <div class="swiper-slide">
                        <div class="insurance-card" style="background: white; border:1px solid rgba(0,0,0,0.05); border-radius: var(--radius-lg); padding:32px; display:flex; align-items:center; justify-content:center; min-height:150px;">
                            <div class="insurance-logo">
                                <?php if (!empty($ins['logo_url'])): ?>
                                    <img src="<?php echo htmlspecialchars($ins['logo_url']); ?>" alt="<?php echo htmlspecialchars($ins['name']); ?>" style="width:120px; max-height:80px; object-fit:contain; transition: filter 0.4s ease;">
                                <?php else: ?>
                                    <i class="bi bi-shield-check" style="font-size:3rem; transition: filter 0.4s ease;"></i>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Stats Section -->
    <section class="stats-premium">
        <div class="container" style="max-width: 1440px;">
            <div class="row g-4">
                <div class="col-md-3">
                    <div class="stat-card-gold">
                        <h3 class="stat-number-gold"><?php echo count($doctors); ?>+</h3>
                        <h6 class="fw-bold">Dokter Spesialis</h6>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card-gold">
                        <h3 class="stat-number-gold"><?php echo count($services); ?></h3>
                        <h6 class="fw-bold">Layanan</h6>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card-gold">
                        <h3 class="stat-number-gold">24/7</h3>
                        <h6 class="fw-bold">Layanan IGD</h6>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card-gold">
                        <h3 class="stat-number-gold">14</h3>
                        <h6 class="fw-bold">Tahun Pengalaman</h6>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <?php if (isSectionVisible('contact', $websiteSections)): ?>
    <section id="contact" class="section">
        <div class="container" style="max-width: 1440px;">
            <div class="section-header">
                <p class="section-kicker">KONTAK KAMI</p>
                <h2 class="section-title">Siap Melayani Anda</h2>
            </div>
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="p-5" style="background: white; border-radius: var(--radius-xl); box-shadow: var(--shadow-soft);">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <h6 class="fw-bold mb-2"><i class="bi bi-telephone me-2" style="color: var(--primary);"></i> Telepon</h6>
                                <p class="text-muted mb-0"><?php echo htmlspecialchars($settings['contact_phone']); ?></p>
                            </div>
                            <div class="col-md-6">
                                <h6 class="fw-bold mb-2"><i class="bi bi-envelope me-2" style="color: var(--primary);"></i> Email</h6>
                                <p class="text-muted mb-0"><?php echo htmlspecialchars($settings['contact_email']); ?></p>
                            </div>
                            <div class="col-12">
                                <h6 class="fw-bold mb-2"><i class="bi bi-geo-alt me-2" style="color: var(--primary);"></i> Alamat</h6>
                                <p class="text-muted mb-4"><?php echo htmlspecialchars($settings['contact_address'] ?? ''); ?></p>
                            </div>
                            <div class="col-12">
                                <a href="<?php echo $waUrlAppointment; ?>" class="btn-cta btn-lg w-100">
                                    <i class="bi bi-whatsapp me-2"></i> Hubungi via WhatsApp
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Footer -->
    <footer class="footer">
        <div class="container" style="max-width: 1440px;">
            <div class="row g-4 mb-5">
                <div class="col-lg-4 text-center">
                    <div class="mb-3">
                        <?php if (!empty($settings['logo_url'])): ?>
                            <img src="<?php echo htmlspecialchars($settings['logo_url']); ?>" alt="Logo" style="height:90px; filter: drop-shadow(0 0 20px rgba(255,255,255,0.3));">
                        <?php else: ?>
                            <i class="bi bi-hospital" style="font-size: 5rem; color: #c5a059; filter: drop-shadow(0 0 20px rgba(197,160,89,0.4));"></i>
                        <?php endif; ?>
                    </div>
                    <p class="text-white-50">Rumah sakit modern dengan komitmen penuh untuk kesehatan Anda dan keluarga.</p>
                </div>
                <div class="col-lg-2 col-md-4">
                    <h6 class="fw-bold mb-3">Navigasi</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="#home" class="text-white-50 text-decoration-none">Beranda</a></li>
                        <li class="mb-2"><a href="#services" class="text-white-50 text-decoration-none">Layanan</a></li>
                        <li class="mb-2"><a href="#doctors" class="text-white-50 text-decoration-none">Dokter</a></li>
                    </ul>
                </div>
                <div class="col-lg-3 col-md-4">
                    <h6 class="fw-bold mb-3">Kontak</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2 text-white-50"><i class="bi bi-telephone me-2"></i><?php echo htmlspecialchars($settings['contact_phone']); ?></li>
                        <li class="mb-2 text-white-50"><i class="bi bi-envelope me-2"></i><?php echo htmlspecialchars($settings['contact_email']); ?></li>
                    </ul>
                </div>
                <div class="col-lg-3 col-md-4">
                    <h6 class="fw-bold mb-3">Aksi Cepat</h6>
                    <a href="<?php echo $waUrlAppointment; ?>" class="btn-accent w-100 mb-2"><i class="bi bi-whatsapp me-2"></i> WhatsApp</a>
                </div>
            </div>
            <div class="text-center pt-4" style="border-top:1px solid rgba(255,255,255,0.1);">
                <p class="text-white-50 mb-0">&copy; <?php echo date('Y'); ?> <?php echo htmlspecialchars($settings['hospital_name']); ?>. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Floating Buttons -->
    <a href="<?php echo $waUrlAppointment; ?>" class="wa-float" target="_blank" title="WhatsApp">
        <i class="bi bi-whatsapp"></i>
    </a>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script>
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                const href = this.getAttribute('href');
                if (href !== '#') {
                    e.preventDefault();
                    const target = document.querySelector(href);
                    if (target) target.scrollIntoView({ behavior: 'smooth' });
                }
            });
        });

        // Floating Buttons Scroll Effect
        const floatBtns = document.querySelectorAll('.wa-float');
        let lastScroll = 0;
        window.addEventListener('scroll', () => {
            const currentScroll = window.pageYOffset;
            if (currentScroll > 100) {
                floatBtns.forEach(btn => {
                    btn.classList.add('scroll-effect');
                });
            } else {
                floatBtns.forEach(btn => {
                    btn.classList.remove('scroll-effect');
                });
            }
            lastScroll = currentScroll;
        });

        // Hero Slider
        const heroSwiper = new Swiper('.hero-swiper', {
            slidesPerView: 1,
            spaceBetween: 0,
            loop: true,
            autoplay: {
                delay: 5000,
                disableOnInteraction: false,
            },
            pauseOnMouseEnter: true,
            navigation: {
                nextEl: '.hero-swiper-button-next',
                prevEl: '.hero-swiper-button-prev',
            },
            pagination: {
                el: '.hero-swiper-pagination',
                clickable: true,
            },
        });

        // Doctor Slider
        const doctorSwiper = new Swiper('.doctor-swiper', {
            slidesPerView: 1,
            spaceBetween: 30,
            loop: true,
            autoplay: {
                delay: 4000,
                disableOnInteraction: false,
            },
            pauseOnMouseEnter: true,
            navigation: {
                nextEl: '.doctor-swiper-button-next',
                prevEl: '.doctor-swiper-button-prev',
            },
            pagination: {
                el: '.doctor-swiper-pagination',
                clickable: true,
            },
            breakpoints: {
                768: {
                    slidesPerView: 2,
                },
                1200: {
                    slidesPerView: 4,
                },
            },
        });

        // Insurance Slider
        const insuranceSwiper = new Swiper('.insurance-swiper', {
            slidesPerView: 3,
            spaceBetween: 20,
            loop: true,
            autoplay: {
                delay: 3000,
                disableOnInteraction: false,
            },
            pauseOnMouseEnter: true,
            breakpoints: {
                768: {
                    slidesPerView: 6,
                },
            },
        });

        // Service Slider (Mobile)
        const serviceSwiper = new Swiper('.service-swiper', {
            slidesPerView: 1,
            spaceBetween: 24,
            loop: true,
            autoplay: {
                delay: 3500,
                disableOnInteraction: false,
            },
            pauseOnMouseEnter: true,
            pagination: {
                el: '.service-swiper-pagination',
                clickable: true,
            },
        });
    </script>
</body>
</html>
