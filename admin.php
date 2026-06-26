<?php
require_once 'config.php';

$defaultFacilities = [
    'AC' => 'bi bi-snow',
    'TV' => 'bi bi-tv',
    'WiFi' => 'bi bi-wifi',
    'Kamar Mandi Dalam' => 'bi bi-droplet',
    'Air Panas' => 'bi bi-thermometer-sun',
    'Nurse Call' => 'bi bi-bell',
    'Sofa' => 'bi bi-couch',
    'Sofa Penunggu' => 'bi bi-person-plus'
];

$page = $_GET['page'] ?? 'dashboard';
$action = $_GET['action'] ?? '';
$editId = isset($_GET['id']) ? intval($_GET['id']) : 0;

$folders = ['banner','doctor','service','insurance','logo','room','gallery','other'];
foreach ($folders as $f) {
    $dir = __DIR__ . '/uploads/' . $f;
    if (!is_dir($dir)) mkdir($dir, 0755, true);
}

$isLoggedIn = isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$isLoggedIn) {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    if ($username === 'admin' && $password === 'admin123') {
        $_SESSION['admin_logged_in'] = true;
        $isLoggedIn = true;
    }
}

if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: admin.php');
    exit;
}

$pdo = getDB();
$message = '';
$messageType = 'success';

if (($_SERVER['REQUEST_METHOD'] === 'POST' || $action === 'delete' || $action === 'toggle_active') && $isLoggedIn) {
    try {
        if ($page === 'settings') {
            $hospitalName = $_POST['hospital_name'] ?? '';
            $contactPhone = $_POST['contact_phone'] ?? '';
            $contactEmail = $_POST['contact_email'] ?? '';
            $contactAddress = $_POST['contact_address'] ?? '';
            $waNumber = $_POST['wa_number'] ?? '';
            $primaryColor = $_POST['primary_color'] ?? '#0F2747';
            $secondaryColor = $_POST['secondary_color'] ?? '#0E6B73';
            $accentColor = $_POST['accent_color'] ?? '#D8A24A';
            $mapsEmbed = $_POST['maps_embed'] ?? '';
            $ig = $_POST['social_instagram'] ?? '#';
            $tiktok = $_POST['social_tiktok'] ?? '#';
            $tw = $_POST['social_twitter'] ?? '#';

            // Clean up accidental '#' prefix (e.g. #https://...)
            if (strpos($ig, '#http') === 0) $ig = substr($ig, 1);
            if (strpos($tiktok, '#http') === 0) $tiktok = substr($tiktok, 1);
            if (strpos($tw, '#http') === 0) $tw = substr($tw, 1);

            $navbarSticky = isset($_POST['navbar_sticky']) ? 1 : 0;

            $seoMetaTitle = $_POST['seo_meta_title'] ?? '';
            $seoMetaDescription = $_POST['seo_meta_description'] ?? '';
            $seoMetaKeywords = $_POST['seo_meta_keywords'] ?? '';

            $logoUrl = uploadFile($_FILES['logo_image'] ?? null, 'logo');
            $faviconUrl = uploadFile($_FILES['favicon_image'] ?? null, 'logo');

            $sql = "UPDATE web_settings SET hospital_name=?, contact_phone=?, contact_email=?, contact_address=?, wa_number=?, theme_color_primary=?, theme_color_secondary=?, theme_color_accent=?, navbar_sticky=?, maps_embed=?, social_instagram=?, social_tiktok=?, social_twitter=?, seo_meta_title=?, seo_meta_description=?, seo_meta_keywords=?";
            $params = [$hospitalName, $contactPhone, $contactEmail, $contactAddress, $waNumber, $primaryColor, $secondaryColor, $accentColor, $navbarSticky, $mapsEmbed, $ig, $tiktok, $tw, $seoMetaTitle, $seoMetaDescription, $seoMetaKeywords];

            if ($logoUrl) {
                $sql .= ", logo_url=?";
                $params[] = $logoUrl;
            }
            if ($faviconUrl) {
                $sql .= ", favicon_url=?";
                $params[] = $faviconUrl;
            }
            $sql .= " WHERE id=1";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $message = 'Pengaturan berhasil disimpan';
        }

        if ($page === 'banners') {
            if ($action === 'add') {
                $title = $_POST['title'] ?? '';
                $subtitle = $_POST['subtitle'] ?? '';
                $btn1Text = $_POST['button1_text'] ?? '';
                $btn1Link = $_POST['button1_link'] ?? '';
                $btn2Text = $_POST['button2_text'] ?? '';
                $btn2Link = $_POST['button2_link'] ?? '';
                $sortOrder = intval($_POST['sort_order'] ?? 0);
                $isActive = isset($_POST['is_active']) ? 1 : 0;
                $bgUrl = uploadFile($_FILES['bg_image'] ?? null, 'banner');
                $mainImageUrl = uploadFile($_FILES['main_image'] ?? null, 'banner');
                $stmt = $pdo->prepare("INSERT INTO hero_slides (title, subtitle, button1_text, button1_link, button2_text, button2_link, bg_url, main_image_url, sort_order, is_active) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$title, $subtitle, $btn1Text, $btn1Link, $btn2Text, $btn2Link, $bgUrl, $mainImageUrl, $sortOrder, $isActive]);
                $message = 'Banner berhasil ditambahkan';
            } elseif ($action === 'edit' && $editId) {
                $title = $_POST['title'] ?? '';
                $subtitle = $_POST['subtitle'] ?? '';
                $btn1Text = $_POST['button1_text'] ?? '';
                $btn1Link = $_POST['button1_link'] ?? '';
                $btn2Text = $_POST['button2_text'] ?? '';
                $btn2Link = $_POST['button2_link'] ?? '';
                $sortOrder = intval($_POST['sort_order'] ?? 0);
                $isActive = isset($_POST['is_active']) ? 1 : 0;
                $bgUrl = uploadFile($_FILES['bg_image'] ?? null, 'banner');
                $mainImageUrl = uploadFile($_FILES['main_image'] ?? null, 'banner');
                $sql = "UPDATE hero_slides SET title=?, subtitle=?, button1_text=?, button1_link=?, button2_text=?, button2_link=?, sort_order=?, is_active=?";
                $params = [$title, $subtitle, $btn1Text, $btn1Link, $btn2Text, $btn2Link, $sortOrder, $isActive];
                if ($bgUrl) {
                    $sql .= ", bg_url=?";
                    $params[] = $bgUrl;
                }
                if ($mainImageUrl) {
                    $sql .= ", main_image_url=?";
                    $params[] = $mainImageUrl;
                }
                $sql .= " WHERE id=?";
                $params[] = $editId;
                $stmt = $pdo->prepare($sql);
                $stmt->execute($params);
                $message = 'Banner berhasil diperbarui';
            } elseif ($action === 'toggle_active' && $editId) {
                $stmt = $pdo->prepare("UPDATE hero_slides SET is_active = NOT is_active WHERE id=?");
                $stmt->execute([$editId]);
                $message = 'Status banner berhasil diubah';
            } elseif ($action === 'delete' && $editId) {
                $stmt = $pdo->prepare("DELETE FROM hero_slides WHERE id=?");
                $stmt->execute([$editId]);
                $message = 'Banner berhasil dihapus';
            }
        }

        if ($page === 'doctors') {
            if ($action === 'add') {
                $name = $_POST['name'] ?? '';
                $specialization = $_POST['specialization'] ?? '';
                $schedule = $_POST['schedule'] ?? '';
                $phone = $_POST['phone'] ?? '';
                $description = $_POST['description'] ?? '';
                $sortOrder = intval($_POST['sort_order'] ?? 0);
                $isActive = isset($_POST['is_active']) ? 1 : 0;
                $photoUrl = uploadFile($_FILES['photo'] ?? null, 'doctor');
                $stmt = $pdo->prepare("INSERT INTO doctors (name, specialization, photo_url, description, schedule, phone, sort_order, is_active) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$name, $specialization, $photoUrl, $description, $schedule, $phone, $sortOrder, $isActive]);
                $message = 'Dokter berhasil ditambahkan';
            } elseif ($action === 'edit' && $editId) {
                $name = $_POST['name'] ?? '';
                $specialization = $_POST['specialization'] ?? '';
                $schedule = $_POST['schedule'] ?? '';
                $phone = $_POST['phone'] ?? '';
                $description = $_POST['description'] ?? '';
                $sortOrder = intval($_POST['sort_order'] ?? 0);
                $isActive = isset($_POST['is_active']) ? 1 : 0;
                $photoUrl = uploadFile($_FILES['photo'] ?? null, 'doctor');
                $sql = "UPDATE doctors SET name=?, specialization=?, description=?, schedule=?, phone=?, sort_order=?, is_active=?";
                $params = [$name, $specialization, $description, $schedule, $phone, $sortOrder, $isActive];
                if ($photoUrl) {
                    $sql .= ", photo_url=?";
                    $params[] = $photoUrl;
                }
                $sql .= " WHERE id=?";
                $params[] = $editId;
                $stmt = $pdo->prepare($sql);
                $stmt->execute($params);
                $message = 'Dokter berhasil diperbarui';
            } elseif ($action === 'toggle_active' && $editId) {
                $stmt = $pdo->prepare("UPDATE doctors SET is_active = NOT is_active WHERE id=?");
                $stmt->execute([$editId]);
                $message = 'Status dokter berhasil diubah';
            } elseif ($action === 'delete' && $editId) {
                $stmt = $pdo->prepare("DELETE FROM doctors WHERE id=?");
                $stmt->execute([$editId]);
                $message = 'Dokter berhasil dihapus';
            }
        }

        if ($page === 'services') {
            if ($action === 'add') {
                $name = $_POST['name'] ?? '';
                $slug = strtolower(str_replace(' ', '-', $name));
                $shortDesc = $_POST['short_description'] ?? '';
                $fullDesc = $_POST['full_description'] ?? '';
                $icon = $_POST['icon'] ?? 'bi bi-star';
                $sortOrder = intval($_POST['sort_order'] ?? 0);
                $isActive = isset($_POST['is_active']) ? 1 : 0;
                $colorPrimary = $_POST['color_primary'] ?? '#0F2747';
                $colorSecondary = $_POST['color_secondary'] ?? '#0E6B73';
                $imageUrl = uploadFile($_FILES['image'] ?? null, 'service');
                $stmt = $pdo->prepare("INSERT INTO services (name, slug, short_description, full_description, icon, image_url, sort_order, is_active, color_primary, color_secondary) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$name, $slug, $shortDesc, $fullDesc, $icon, $imageUrl, $sortOrder, $isActive, $colorPrimary, $colorSecondary]);
                $message = 'Layanan berhasil ditambahkan';
            } elseif ($action === 'edit' && $editId) {
                $name = $_POST['name'] ?? '';
                $slug = strtolower(str_replace(' ', '-', $name));
                $shortDesc = $_POST['short_description'] ?? '';
                $fullDesc = $_POST['full_description'] ?? '';
                $icon = $_POST['icon'] ?? 'bi bi-star';
                $sortOrder = intval($_POST['sort_order'] ?? 0);
                $isActive = isset($_POST['is_active']) ? 1 : 0;
                $colorPrimary = $_POST['color_primary'] ?? '#0F2747';
                $colorSecondary = $_POST['color_secondary'] ?? '#0E6B73';
                
                $newImageUrl = uploadFile($_FILES['image'] ?? null, 'service');
                
                $stmt = $pdo->prepare("SELECT image_url FROM services WHERE id=?");
                $stmt->execute([$editId]);
                $existing = $stmt->fetch();
                $imageUrl = $newImageUrl ?: ($existing['image_url'] ?? null);
                
                $sql = "UPDATE services SET name=?, slug=?, short_description=?, full_description=?, icon=?, sort_order=?, is_active=?, color_primary=?, color_secondary=?, image_url=?";
                $params = [$name, $slug, $shortDesc, $fullDesc, $icon, $sortOrder, $isActive, $colorPrimary, $colorSecondary, $imageUrl];
                $sql .= " WHERE id=?";
                $params[] = $editId;
                $stmt = $pdo->prepare($sql);
                $stmt->execute($params);
                $message = 'Layanan berhasil diperbarui';
            } elseif ($action === 'toggle_active' && $editId) {
                $stmt = $pdo->prepare("UPDATE services SET is_active = NOT is_active WHERE id=?");
                $stmt->execute([$editId]);
                $message = 'Status layanan berhasil diubah';
            } elseif ($action === 'delete' && $editId) {
                $stmt = $pdo->prepare("DELETE FROM services WHERE id=?");
                $stmt->execute([$editId]);
                $message = 'Layanan berhasil dihapus';
            }
        }

        if ($page === 'rooms') {
            if ($action === 'add') {
                $name = $_POST['name'] ?? '';
                $category = $_POST['category'] ?? 'Kelas 1';
                $description = $_POST['description'] ?? '';
                $sortOrder = intval($_POST['sort_order'] ?? 0);
                $isActive = isset($_POST['is_active']) ? 1 : 0;
                $coverUrl = uploadFile($_FILES['cover_image'] ?? null, 'room');
                $stmt = $pdo->prepare("INSERT INTO rooms (name, category, cover_url, description, sort_order, is_active) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([$name, $category, $coverUrl, $description, $sortOrder, $isActive]);
                $roomId = $pdo->lastInsertId();
                
                // Save facilities
                $selectedFacilities = $_POST['facilities'] ?? [];
                $stmt = $pdo->prepare("DELETE FROM room_facilities WHERE room_id=?");
                $stmt->execute([$roomId]);
                foreach ($selectedFacilities as $i => $facilityName) {
                    $icon = $defaultFacilities[$facilityName] ?? 'bi bi-star';
                    $stmt = $pdo->prepare("INSERT INTO room_facilities (room_id, facility_name, facility_icon, sort_order) VALUES (?, ?, ?, ?)");
                    $stmt->execute([$roomId, $facilityName, $icon, $i+1]);
                }
                
                $message = 'Kamar berhasil ditambahkan';
            } elseif ($action === 'edit' && $editId) {
                $name = $_POST['name'] ?? '';
                $category = $_POST['category'] ?? 'Kelas 1';
                $description = $_POST['description'] ?? '';
                $sortOrder = intval($_POST['sort_order'] ?? 0);
                $isActive = isset($_POST['is_active']) ? 1 : 0;
                $coverUrl = uploadFile($_FILES['cover_image'] ?? null, 'room');
                $sql = "UPDATE rooms SET name=?, category=?, description=?, sort_order=?, is_active=?";
                $params = [$name, $category, $description, $sortOrder, $isActive];
                if ($coverUrl) {
                    $sql .= ", cover_url=?";
                    $params[] = $coverUrl;
                }
                $sql .= " WHERE id=?";
                $params[] = $editId;
                $stmt = $pdo->prepare($sql);
                $stmt->execute($params);
                
                // Save facilities
                $selectedFacilities = $_POST['facilities'] ?? [];
                $stmt = $pdo->prepare("DELETE FROM room_facilities WHERE room_id=?");
                $stmt->execute([$editId]);
                foreach ($selectedFacilities as $i => $facilityName) {
                    $icon = $defaultFacilities[$facilityName] ?? 'bi bi-star';
                    $stmt = $pdo->prepare("INSERT INTO room_facilities (room_id, facility_name, facility_icon, sort_order) VALUES (?, ?, ?, ?)");
                    $stmt->execute([$editId, $facilityName, $icon, $i+1]);
                }
                
                $message = 'Kamar berhasil diperbarui';
            } elseif ($action === 'toggle_active' && $editId) {
                $stmt = $pdo->prepare("UPDATE rooms SET is_active = NOT is_active WHERE id=?");
                $stmt->execute([$editId]);
                $message = 'Status kamar berhasil diubah';
            } elseif ($action === 'delete' && $editId) {
                $stmt = $pdo->prepare("DELETE FROM rooms WHERE id=?");
                $stmt->execute([$editId]);
                $message = 'Kamar berhasil dihapus';
            }
        }

        if ($page === 'outpatient') {
            if ($action === 'add') {
                $title = $_POST['title'] ?? '';
                $description = $_POST['description'] ?? '';
                $ctaText = $_POST['cta_text'] ?? '';
                $ctaLink = $_POST['cta_link'] ?? '#contact';
                $sortOrder = intval($_POST['sort_order'] ?? 0);
                $isActive = isset($_POST['is_active']) ? 1 : 0;
                $bannerUrl = uploadFile($_FILES['banner'] ?? null, 'service');
                $stmt = $pdo->prepare("INSERT INTO outpatient_banners (title, banner_url, description, cta_text, cta_link, sort_order, is_active) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$title, $bannerUrl, $description, $ctaText, $ctaLink, $sortOrder, $isActive]);
                $message = 'Banner rawat jalan berhasil ditambahkan';
            } elseif ($action === 'edit' && $editId) {
                $title = $_POST['title'] ?? '';
                $description = $_POST['description'] ?? '';
                $ctaText = $_POST['cta_text'] ?? '';
                $ctaLink = $_POST['cta_link'] ?? '#contact';
                $sortOrder = intval($_POST['sort_order'] ?? 0);
                $isActive = isset($_POST['is_active']) ? 1 : 0;
                $bannerUrl = uploadFile($_FILES['banner'] ?? null, 'service');
                $sql = "UPDATE outpatient_banners SET title=?, description=?, cta_text=?, cta_link=?, sort_order=?, is_active=?";
                $params = [$title, $description, $ctaText, $ctaLink, $sortOrder, $isActive];
                if ($bannerUrl) {
                    $sql .= ", banner_url=?";
                    $params[] = $bannerUrl;
                }
                $sql .= " WHERE id=?";
                $params[] = $editId;
                $stmt = $pdo->prepare($sql);
                $stmt->execute($params);
                $message = 'Banner rawat jalan berhasil diperbarui';
            } elseif ($action === 'toggle_active' && $editId) {
                $stmt = $pdo->prepare("UPDATE outpatient_banners SET is_active = NOT is_active WHERE id=?");
                $stmt->execute([$editId]);
                $message = 'Status banner rawat jalan berhasil diubah';
            } elseif ($action === 'delete' && $editId) {
                $stmt = $pdo->prepare("DELETE FROM outpatient_banners WHERE id=?");
                $stmt->execute([$editId]);
                $message = 'Banner rawat jalan berhasil dihapus';
            }
        }

        if ($page === 'insurances') {
            if ($action === 'add') {
                $name = $_POST['name'] ?? '';
                $sortOrder = intval($_POST['sort_order'] ?? 0);
                $isActive = isset($_POST['is_active']) ? 1 : 0;
                $logoUrl = uploadFile($_FILES['logo'] ?? null, 'insurance');
                $stmt = $pdo->prepare("INSERT INTO insurances (name, logo_url, sort_order, is_active) VALUES (?, ?, ?, ?)");
                $stmt->execute([$name, $logoUrl, $sortOrder, $isActive]);
                $message = 'Asuransi berhasil ditambahkan';
            } elseif ($action === 'edit' && $editId) {
                $name = $_POST['name'] ?? '';
                $sortOrder = intval($_POST['sort_order'] ?? 0);
                $isActive = isset($_POST['is_active']) ? 1 : 0;
                $logoUrl = uploadFile($_FILES['logo'] ?? null, 'insurance');
                $sql = "UPDATE insurances SET name=?, sort_order=?, is_active=?";
                $params = [$name, $sortOrder, $isActive];
                if ($logoUrl) {
                    $sql .= ", logo_url=?";
                    $params[] = $logoUrl;
                }
                $sql .= " WHERE id=?";
                $params[] = $editId;
                $stmt = $pdo->prepare($sql);
                $stmt->execute($params);
                $message = 'Asuransi berhasil diperbarui';
            } elseif ($action === 'toggle_active' && $editId) {
                $stmt = $pdo->prepare("UPDATE insurances SET is_active = NOT is_active WHERE id=?");
                $stmt->execute([$editId]);
                $message = 'Status asuransi berhasil diubah';
            } elseif ($action === 'delete' && $editId) {
                $stmt = $pdo->prepare("DELETE FROM insurances WHERE id=?");
                $stmt->execute([$editId]);
                $message = 'Asuransi berhasil dihapus';
            }
        }

        if ($page === 'media') {
            $folder = $_POST['folder'] ?? 'other';
            if (isset($_FILES['files']) && !empty($_FILES['files']['name'][0])) {
                $total = count($_FILES['files']['name']);
                for ($i = 0; $i < $total; $i++) {
                    if ($_FILES['files']['error'][$i] === UPLOAD_ERR_OK) {
                        $file = [
                            'name' => $_FILES['files']['name'][$i],
                            'type' => $_FILES['files']['type'][$i],
                            'tmp_name' => $_FILES['files']['tmp_name'][$i],
                            'error' => $_FILES['files']['error'][$i],
                            'size' => $_FILES['files']['size'][$i]
                        ];
                        $path = uploadFile($file, $folder);
                        if ($path) {
                            $stmt = $pdo->prepare("INSERT INTO media (file_name, file_path, file_type, file_size, folder, uploaded_by) VALUES (?, ?, ?, ?, ?, 'admin')");
                            $stmt->execute([$file['name'], $path, $file['type'], $file['size'], $folder]);
                        }
                    }
                }
                $message = 'File berhasil diupload';
            }
            if ($action === 'delete' && $editId) {
                $stmt = $pdo->prepare("DELETE FROM media WHERE id=?");
                $stmt->execute([$editId]);
                $message = 'File berhasil dihapus';
            }
        }

        if ($page === 'website') {
            if (isset($_POST['sections']) && is_array($_POST['sections'])) {
                foreach ($_POST['sections'] as $secId => $data) {
                    $isVisible = isset($data['is_visible']) ? 1 : 0;
                    $sortOrder = intval($data['sort_order'] ?? 0);
                    $title = $data['section_title'] ?? '';
                    $subtitle = $data['section_subtitle'] ?? '';
                    $stmt = $pdo->prepare("UPDATE website_sections SET sort_order=?, is_visible=?, section_title=?, section_subtitle=? WHERE id=?");
                    $stmt->execute([$sortOrder, $isVisible, $title, $subtitle, $secId]);
                }
                $message = 'Website sections berhasil disimpan';
            }
        }

    } catch (Exception $e) {
        $message = 'Error: ' . $e->getMessage();
        $messageType = 'danger';
    }
}

$settings = getSettings();
$heroSlides = getHeroSlides(false);
$services = getServices(false);
$doctors = getDoctors(false);
$rooms = getRooms(false);
$outpatientBanners = getOutpatientBanners(false);
$insurances = getInsurances(false);
$websiteSections = getWebsiteSections();

// Get current room facilities for edit
$currentRoomFacilities = [];
if ($page === 'rooms' && $action === 'edit' && $editId) {
    $currentRoomFacilities = array_column(getRoomFacilities($editId), 'facility_name');
}

// Fetch single item for edit
$editItem = null;
if ($action === 'edit' && $editId) {
    $stmt = null;
    switch ($page) {
        case 'banners':
            $stmt = $pdo->prepare("SELECT * FROM hero_slides WHERE id=?");
            break;
        case 'doctors':
            $stmt = $pdo->prepare("SELECT * FROM doctors WHERE id=?");
            break;
        case 'services':
            $stmt = $pdo->prepare("SELECT * FROM services WHERE id=?");
            break;
        case 'rooms':
            $stmt = $pdo->prepare("SELECT * FROM rooms WHERE id=?");
            break;
        case 'outpatient':
            $stmt = $pdo->prepare("SELECT * FROM outpatient_banners WHERE id=?");
            break;
        case 'insurances':
            $stmt = $pdo->prepare("SELECT * FROM insurances WHERE id=?");
            break;
    }
    if ($stmt) {
        $stmt->execute([$editId]);
        $editItem = $stmt->fetch();
    }
}

$mediaFolder = $_GET['folder'] ?? 'all';
$mediaQuery = "SELECT * FROM media";
$mediaParams = [];
if ($mediaFolder !== 'all') {
    $mediaQuery .= " WHERE folder = ?";
    $mediaParams[] = $mediaFolder;
}
$mediaQuery .= " ORDER BY created_at DESC";
$mediaStmt = $pdo->prepare($mediaQuery);
$mediaStmt->execute($mediaParams);
$mediaFiles = $mediaStmt->fetchAll();

if (!$isLoggedIn) {
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - RS Taman Harapan Baru</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg, #0F2747 0%, #0E6B73 100%); min-height: 100vh; display:flex; align-items:center; }
        .card { border: none; border-radius: 24px; box-shadow: 0 20px 60px rgba(0,0,0,0.35); }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-5">
                <div class="card p-5">
                    <div class="text-center mb-4">
                        <?php if (!empty($settings['logo_url'])): ?>
                            <img src="<?php echo htmlspecialchars($settings['logo_url']); ?>" alt="Logo" style="height: 120px; object-fit: contain;">
                        <?php else: ?>
                            <i class="bi bi-hospital" style="font-size:4rem;color:#0F766E;"></i>
                        <?php endif; ?>
                        <h3 class="fw-bold mt-3" style="color:#0F766E;">Admin Panel</h3>
                        <p class="text-muted">RS Taman Harapan Baru</p>
                    </div>
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Username</label>
                            <input type="text" class="form-control" name="username" placeholder="Username" required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-bold">Password</label>
                            <input type="password" class="form-control" name="password" placeholder="Password" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 btn-lg" style="background:linear-gradient(135deg,#0F766E,#10B981); border:none;">
                            <i class="bi bi-box-arrow-in-right me-2"></i> Masuk
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php
exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - RS Taman Harapan Baru</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: <?php echo $settings['theme_color_primary'] ?? '#0F2747'; ?>;
            --secondary: <?php echo $settings['theme_color_secondary'] ?? '#0E6B73'; ?>;
            --accent: <?php echo $settings['theme_color_accent'] ?? '#D8A24A'; ?>;
        }
        * { font-family: 'Poppins', sans-serif; }
        body { background: #F1F5F9; }
        .sidebar {
            background: linear-gradient(180deg, #0F2747 0%, #0E6B73 100%);
            min-height: 100vh;
            color: white;
            position: sticky;
            top: 0;
        }
        .sidebar-brand { padding:24px; border-bottom:1px solid rgba(255,255,255,0.1); }
        .nav-link-sidebar {
            color: rgba(255,255,255,0.85);
            padding: 12px 20px;
            display: block;
            border-radius: 12px;
            margin: 6px 12px;
            font-weight: 500;
        }
        .nav-link-sidebar:hover, .nav-link-sidebar.active {
            background: rgba(255,255,255,0.12);
            color: white;
        }
        .card { border: none; border-radius: 16px; box-shadow: 0 2px 18px rgba(0,0,0,0.06); }
        .btn-primary { background: linear-gradient(135deg, var(--primary), var(--secondary)); border: none; }
        .btn-accent { background: linear-gradient(135deg, var(--accent), #c69438); color: #18202C; border: none; font-weight: 700; }
        .table thead { background: linear-gradient(135deg, rgba(15,39,71,0.05), rgba(14,107,115,0.05)); }
        .grid-item { position: relative; border-radius: 12px; overflow: hidden; aspect-ratio: 1; background: #f0f0f0; }
        .grid-item img { width:100%; height:100%; object-fit: cover; }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-2 col-md-3 sidebar p-0">
                <div class="sidebar-brand">
                    <h5 class="fw-bold mb-0"><i class="bi bi-hospital me-2"></i> RS Admin</h5>
                    <small class="text-white-50">Taman Harapan Baru</small>
                </div>
                <nav class="mt-2">
                    <a href="?page=dashboard" class="nav-link-sidebar <?php echo $page === 'dashboard' ? 'active' : ''; ?>"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a>
                    <a href="?page=website" class="nav-link-sidebar <?php echo $page === 'website' ? 'active' : ''; ?>"><i class="bi bi-browser-chrome me-2"></i> Website</a>
                    <a href="?page=banners" class="nav-link-sidebar <?php echo $page === 'banners' ? 'active' : ''; ?>"><i class="bi bi-images me-2"></i> Banner Slider</a>
                    <a href="?page=doctors" class="nav-link-sidebar <?php echo $page === 'doctors' ? 'active' : ''; ?>"><i class="bi bi-person-gear me-2"></i> Dokter</a>
                    <a href="?page=services" class="nav-link-sidebar <?php echo $page === 'services' ? 'active' : ''; ?>"><i class="bi bi-stethoscope me-2"></i> Layanan</a>
                    <a href="?page=rooms" class="nav-link-sidebar <?php echo $page === 'rooms' ? 'active' : ''; ?>"><i class="bi bi-house-heart me-2"></i> Rawat Inap</a>
                    <a href="?page=outpatient" class="nav-link-sidebar <?php echo $page === 'outpatient' ? 'active' : ''; ?>"><i class="bi bi-person-check me-2"></i> Rawat Jalan</a>
                    <a href="?page=insurances" class="nav-link-sidebar <?php echo $page === 'insurances' ? 'active' : ''; ?>"><i class="bi bi-shield-check me-2"></i> Asuransi</a>
                    <a href="?page=media" class="nav-link-sidebar <?php echo $page === 'media' ? 'active' : ''; ?>"><i class="bi bi-folder2-open me-2"></i> Media</a>
                    <a href="?page=settings" class="nav-link-sidebar <?php echo $page === 'settings' ? 'active' : ''; ?>"><i class="bi bi-gear me-2"></i> Pengaturan</a>
                    <a href="?page=seo" class="nav-link-sidebar <?php echo $page === 'seo' ? 'active' : ''; ?>"><i class="bi bi-search me-2"></i> SEO</a>
                </nav>
            </div>
            <div class="col-lg-10 col-md-9 p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="fw-bold text-dark mb-0">
                        <?php
                        $pageTitles = [
                            'dashboard' => 'Dashboard',
                            'website' => 'Website Builder',
                            'banners' => 'Banner Slider',
                            'doctors' => 'Dokter',
                            'services' => 'Layanan',
                            'rooms' => 'Rawat Inap',
                            'outpatient' => 'Rawat Jalan',
                            'insurances' => 'Asuransi',
                            'media' => 'Media Library',
                            'settings' => 'Pengaturan',
                            'seo' => 'SEO'
                        ];
                        echo $pageTitles[$page] ?? 'Dashboard';
                        ?>
                    </h4>
                    <div class="d-flex gap-2">
                        <a href="index.php" target="_blank" class="btn btn-outline-primary"><i class="bi bi-eye me-2"></i> Lihat Website</a>
                        <a href="?logout=1" class="btn btn-danger"><i class="bi bi-box-arrow-right me-2"></i> Keluar</a>
                    </div>
                </div>
                <?php if ($message): ?>
                    <div class="alert alert-<?php echo $messageType; ?>"><?php echo htmlspecialchars($message); ?></div>
                <?php endif; ?>

                <?php if ($page === 'dashboard'): ?>
                    <div class="row g-4 mb-4">
                        <div class="col-lg-3 col-md-6">
                            <div class="card p-4">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="text-muted mb-0">Dokter</h6>
                                    <i class="bi bi-person-gear fs-2" style="color: var(--primary);"></i>
                                </div>
                                <h3 class="fw-bold mt-2 mb-0" style="color: var(--primary);"><?php echo count($doctors); ?></h3>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="card p-4">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="text-muted mb-0">Layanan</h6>
                                    <i class="bi bi-stethoscope fs-2" style="color: var(--secondary);"></i>
                                </div>
                                <h3 class="fw-bold mt-2 mb-0" style="color: var(--secondary);"><?php echo count($services); ?></h3>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="card p-4">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="text-muted mb-0">Banner</h6>
                                    <i class="bi bi-images fs-2" style="color: var(--accent);"></i>
                                </div>
                                <h3 class="fw-bold mt-2 mb-0" style="color: var(--accent);"><?php echo count($heroSlides); ?></h3>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="card p-4">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="text-muted mb-0">Kamar</h6>
                                    <i class="bi bi-house-heart fs-2" style="color: #2FA86B;"></i>
                                </div>
                                <h3 class="fw-bold mt-2 mb-0" style="color: #2FA86B;"><?php echo count($rooms); ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="card p-4">
                        <h6 class="fw-bold mb-3">Aksi Cepat</h6>
                        <div class="row g-2">
                            <div class="col-md-3"><a href="?page=banners" class="btn btn-primary w-100"><i class="bi bi-plus-lg me-2"></i> Tambah Banner</a></div>
                            <div class="col-md-3"><a href="?page=doctors" class="btn btn-outline-primary w-100"><i class="bi bi-person-plus me-2"></i> Tambah Dokter</a></div>
                            <div class="col-md-3"><a href="index.php" class="btn btn-accent w-100" target="_blank"><i class="bi bi-eye me-2"></i> Lihat Website</a></div>
                            <div class="col-md-3"><a href="?page=settings" class="btn btn-outline-secondary w-100"><i class="bi bi-gear me-2"></i> Pengaturan</a></div>
                        </div>
                    </div>

                <?php elseif ($page === 'website'): ?>
                    <form method="POST">
                        <div class="card p-4 mb-4">
                            <h6 class="fw-bold mb-3">Urutan & Visibilitas Section</h6>
                            <div class="table-responsive">
                                <table class="table align-middle">
                                    <thead>
                                        <tr>
                                            <th>Section</th>
                                            <th>Judul Section</th>
                                            <th>Subtitle</th>
                                            <th>Urutan</th>
                                            <th>Tampilkan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($websiteSections as $sec): ?>
                                            <tr>
                                                <td class="fw-bold"><?php echo htmlspecialchars($sec['section_name']); ?></td>
                                                <td><input type="text" class="form-control" name="sections[<?php echo $sec['id']; ?>][section_title]" value="<?php echo htmlspecialchars($sec['section_title'] ?? ''); ?>"></td>
                                                <td><input type="text" class="form-control" name="sections[<?php echo $sec['id']; ?>][section_subtitle]" value="<?php echo htmlspecialchars($sec['section_subtitle'] ?? ''); ?>"></td>
                                                <td><input type="number" class="form-control" name="sections[<?php echo $sec['id']; ?>][sort_order]" value="<?php echo $sec['sort_order']; ?>"></td>
                                                <td>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="sections[<?php echo $sec['id']; ?>][is_visible]" <?php echo $sec['is_visible'] ? 'checked' : ''; ?>>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <button type="submit" class="btn btn-primary mt-3"><i class="bi bi-save me-2"></i> Simpan</button>
                        </div>
                    </form>

                <?php elseif ($page === 'banners'): ?>
                    <?php if ($action === 'edit' && $editItem): ?>
                        <div class="card p-4 mb-4">
                            <h6 class="fw-bold mb-3"><i class="bi bi-pencil-square me-2"></i> Edit Banner</h6>
                            <form method="POST" action="?page=banners&action=edit&id=<?php echo $editItem['id']; ?>" enctype="multipart/form-data">
                                <div class="row g-3">
                                    <div class="col-md-6"><input type="text" class="form-control" name="title" placeholder="Judul" required value="<?php echo htmlspecialchars($editItem['title'] ?? ''); ?>"></div>
                                    <div class="col-md-6"><input type="text" class="form-control" name="subtitle" placeholder="Subtitle" value="<?php echo htmlspecialchars($editItem['subtitle'] ?? ''); ?>"></div>
                                    <div class="col-md-3"><input type="text" class="form-control" name="button1_text" placeholder="Button 1 Text" value="<?php echo htmlspecialchars($editItem['button1_text'] ?? ''); ?>"></div>
                                    <div class="col-md-3"><input type="text" class="form-control" name="button1_link" placeholder="Button 1 Link" value="<?php echo htmlspecialchars($editItem['button1_link'] ?? ''); ?>"></div>
                                    <div class="col-md-3"><input type="text" class="form-control" name="button2_text" placeholder="Button 2 Text" value="<?php echo htmlspecialchars($editItem['button2_text'] ?? ''); ?>"></div>
                                    <div class="col-md-3"><input type="text" class="form-control" name="button2_link" placeholder="Button 2 Link" value="<?php echo htmlspecialchars($editItem['button2_link'] ?? ''); ?>"></div>
                                    <div class="col-md-4"><input type="file" class="form-control" name="bg_image" accept=".jpg,.jpeg,.png,.webp"></div>
                                    <div class="col-md-4"><input type="file" class="form-control" name="main_image" accept=".jpg,.jpeg,.png,.webp"></div>
                                    <div class="col-md-2"><input type="number" class="form-control" name="sort_order" placeholder="Urutan" value="<?php echo $editItem['sort_order'] ?? 0; ?>"></div>
                                    <div class="col-md-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="is_active" <?php echo $editItem['is_active'] ? 'checked' : ''; ?>>
                                            <label class="form-check-label">Aktif</label>
                                        </div>
                                    </div>
                                    <?php if (!empty($editItem['bg_url'])): ?>
                                        <div class="col-md-12">
                                            <label class="form-label fw-bold">Preview Background</label>
                                            <img src="<?php echo htmlspecialchars($editItem['bg_url']); ?>" alt="Background" style="max-width: 100%; height: auto; border-radius: 16px; max-height: 300px;">
                                        </div>
                                    <?php endif; ?>
                                    <?php if (!empty($editItem['main_image_url'])): ?>
                                        <div class="col-md-12">
                                            <label class="form-label fw-bold">Preview Gambar Utama</label>
                                            <img src="<?php echo htmlspecialchars($editItem['main_image_url']); ?>" alt="Main Image" style="max-width: 100%; height: auto; border-radius: 16px; max-height: 300px;">
                                        </div>
                                    <?php endif; ?>
                                    <div class="col-md-4">
                                        <button type="submit" class="btn btn-primary w-100"><i class="bi bi-save me-2"></i> Update</button>
                                    </div>
                                    <div class="col-md-12">
                                        <a href="?page=banners" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-2"></i> Kembali</a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    <?php else: ?>
                        <div class="card p-4 mb-4">
                            <h6 class="fw-bold mb-3"><i class="bi bi-plus-circle me-2"></i> Tambah Banner</h6>
                            <form method="POST" action="?page=banners&action=add" enctype="multipart/form-data">
                                <div class="row g-3">
                                    <div class="col-md-6"><input type="text" class="form-control" name="title" placeholder="Judul" required></div>
                                    <div class="col-md-6"><input type="text" class="form-control" name="subtitle" placeholder="Subtitle"></div>
                                    <div class="col-md-3"><input type="text" class="form-control" name="button1_text" placeholder="Button 1 Text"></div>
                                    <div class="col-md-3"><input type="text" class="form-control" name="button1_link" placeholder="Button 1 Link"></div>
                                    <div class="col-md-3"><input type="text" class="form-control" name="button2_text" placeholder="Button 2 Text"></div>
                                    <div class="col-md-3"><input type="text" class="form-control" name="button2_link" placeholder="Button 2 Link"></div>
                                    <div class="col-md-4"><input type="file" class="form-control" name="bg_image" accept=".jpg,.jpeg,.png,.webp"></div>
                                    <div class="col-md-4"><input type="file" class="form-control" name="main_image" accept=".jpg,.jpeg,.png,.webp"></div>
                                    <div class="col-md-2"><input type="number" class="form-control" name="sort_order" placeholder="Urutan" value="0"></div>
                                    <div class="col-md-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="is_active" checked>
                                            <label class="form-check-label">Aktif</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4"><button type="submit" class="btn btn-primary w-100"><i class="bi bi-save me-2"></i> Simpan</button></div>
                                </div>
                            </form>
                        </div>
                        <div class="card p-4">
                            <div class="table-responsive">
                                <table class="table align-middle">
                                    <thead>
                                        <tr><th>#</th><th>Judul</th><th>Preview</th><th>Urutan</th><th>Aktif</th><th>Aksi</th></tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($heroSlides as $i => $slide): ?>
                                            <tr>
                                                <td><?php echo $i+1; ?></td>
                                                <td><strong><?php echo htmlspecialchars($slide['title']); ?></strong></td>
                                                <td>
                                                    <?php if (!empty($slide['bg_url'])): ?>
                                                        <img src="<?php echo htmlspecialchars($slide['bg_url']); ?>" alt="Preview" style="max-width: 150px; max-height: 80px; border-radius: 8px; object-fit: cover;">
                                                    <?php else: ?>
                                                        <span class="text-muted">Tidak ada gambar</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?php echo $slide['sort_order']; ?></td>
                                                <td><?php echo $slide['is_active'] ? '<span class="badge bg-success">Ya</span>' : '<span class="badge bg-secondary">Tidak</span>'; ?></td>
                                                <td>
                                                    <a href="?page=banners&action=edit&id=<?php echo $slide['id']; ?>" class="btn btn-sm btn-primary me-1"><i class="bi bi-pencil"></i></a>
                                                    <a href="?page=banners&action=toggle_active&id=<?php echo $slide['id']; ?>" class="btn btn-sm btn-warning me-1"><i class="bi bi-power"></i></a>
                                                    <a href="?page=banners&action=delete&id=<?php echo $slide['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus banner ini?');"><i class="bi bi-trash"></i></a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                        <?php if (empty($heroSlides)): ?>
                                            <tr><td colspan="6" class="text-center text-muted">Belum ada banner</td></tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php endif; ?>

                <?php elseif ($page === 'doctors'): ?>
                    <?php if ($action === 'edit' && $editItem): ?>
                        <div class="card p-4 mb-4">
                            <h6 class="fw-bold mb-3"><i class="bi bi-pencil-square me-2"></i> Edit Dokter</h6>
                            <form method="POST" action="?page=doctors&action=edit&id=<?php echo $editItem['id']; ?>" enctype="multipart/form-data">
                                <div class="row g-3">
                                    <div class="col-md-4"><input type="text" class="form-control" name="name" placeholder="Nama Dokter" required value="<?php echo htmlspecialchars($editItem['name'] ?? ''); ?>"></div>
                                    <div class="col-md-4"><input type="text" class="form-control" name="specialization" placeholder="Spesialis" required value="<?php echo htmlspecialchars($editItem['specialization'] ?? ''); ?>"></div>
                                    <div class="col-md-4"><input type="text" class="form-control" name="phone" placeholder="Nomor Telepon" value="<?php echo htmlspecialchars($editItem['phone'] ?? ''); ?>"></div>
                                    <div class="col-md-6"><input type="text" class="form-control" name="schedule" placeholder="Jadwal" value="<?php echo htmlspecialchars($editItem['schedule'] ?? ''); ?>"></div>
                                    <div class="col-md-3"><input type="file" class="form-control" name="photo" accept="image/*"></div>
                                    <div class="col-md-1"><input type="number" class="form-control" name="sort_order" placeholder="Urutan" value="<?php echo $editItem['sort_order'] ?? 0; ?>"></div>
                                    <div class="col-md-2">
                                        <div class="form-check mt-2">
                                            <input class="form-check-input" type="checkbox" name="is_active" <?php echo $editItem['is_active'] ? 'checked' : ''; ?>>
                                            <label class="form-check-label">Aktif</label>
                                        </div>
                                    </div>
                                    <div class="col-12"><textarea class="form-control" name="description" placeholder="Deskripsi" rows="2"><?php echo htmlspecialchars($editItem['description'] ?? ''); ?></textarea></div>
                                    <div class="col-md-3">
                                        <button type="submit" class="btn btn-primary"><i class="bi bi-save me-2"></i> Update</button>
                                    </div>
                                    <div class="col-md-12">
                                        <a href="?page=doctors" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-2"></i> Kembali</a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    <?php else: ?>
                        <div class="card p-4 mb-4">
                            <h6 class="fw-bold mb-3"><i class="bi bi-plus-circle me-2"></i> Tambah Dokter</h6>
                            <form method="POST" action="?page=doctors&action=add" enctype="multipart/form-data">
                                <div class="row g-3">
                                    <div class="col-md-4"><input type="text" class="form-control" name="name" placeholder="Nama Dokter" required></div>
                                    <div class="col-md-4"><input type="text" class="form-control" name="specialization" placeholder="Spesialis" required></div>
                                    <div class="col-md-4"><input type="text" class="form-control" name="phone" placeholder="Nomor Telepon"></div>
                                    <div class="col-md-6"><input type="text" class="form-control" name="schedule" placeholder="Jadwal"></div>
                                    <div class="col-md-3"><input type="file" class="form-control" name="photo" accept="image/*"></div>
                                    <div class="col-md-1"><input type="number" class="form-control" name="sort_order" placeholder="Urutan" value="0"></div>
                                    <div class="col-md-2">
                                        <div class="form-check mt-2">
                                            <input class="form-check-input" type="checkbox" name="is_active" checked>
                                            <label class="form-check-label">Aktif</label>
                                        </div>
                                    </div>
                                    <div class="col-12"><textarea class="form-control" name="description" placeholder="Deskripsi" rows="2"></textarea></div>
                                    <div class="col-md-3"><button type="submit" class="btn btn-primary"><i class="bi bi-save me-2"></i> Simpan</button></div>
                                </div>
                            </form>
                        </div>
                        <div class="card p-4">
                            <div class="table-responsive">
                                <table class="table align-middle">
                                    <thead><tr><th>#</th><th>Nama</th><th>Spesialis</th><th>Jadwal</th><th>Aksi</th></tr></thead>
                                    <tbody>
                                        <?php foreach ($doctors as $i => $doc): ?>
                                            <tr>
                                                <td><?php echo $i+1; ?></td>
                                                <td><strong><?php echo htmlspecialchars($doc['name']); ?></strong></td>
                                                <td><?php echo htmlspecialchars($doc['specialization']); ?></td>
                                                <td><?php echo htmlspecialchars($doc['schedule'] ?? '-'); ?></td>
                                                <td>
                                                    <a href="?page=doctors&action=edit&id=<?php echo $doc['id']; ?>" class="btn btn-sm btn-primary me-1"><i class="bi bi-pencil"></i></a>
                                                    <a href="?page=doctors&action=toggle_active&id=<?php echo $doc['id']; ?>" class="btn btn-sm btn-warning me-1"><i class="bi bi-power"></i></a>
                                                    <a href="?page=doctors&action=delete&id=<?php echo $doc['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus dokter ini?');"><i class="bi bi-trash"></i></a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                        <?php if (empty($doctors)): ?>
                                            <tr><td colspan="5" class="text-center text-muted">Belum ada dokter</td></tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php endif; ?>

                <?php elseif ($page === 'services'): ?>
                    <?php if ($action === 'edit' && $editItem): ?>
                        <div class="card p-4 mb-4">
                            <h6 class="fw-bold mb-3"><i class="bi bi-pencil-square me-2"></i> Edit Layanan</h6>
                            <form method="POST" action="?page=services&action=edit&id=<?php echo $editItem['id']; ?>" enctype="multipart/form-data">
                                <div class="row g-3">
                                    <div class="col-md-3"><input type="text" class="form-control" name="name" placeholder="Nama Layanan" required value="<?php echo htmlspecialchars($editItem['name'] ?? ''); ?>"></div>
                                    <div class="col-md-3"><input type="text" class="form-control" name="icon" placeholder="Icon (cth: bi bi-star)" value="<?php echo htmlspecialchars($editItem['icon'] ?? ''); ?>"></div>
                                    <div class="col-md-4"><input type="text" class="form-control" name="short_description" placeholder="Deskripsi Singkat" value="<?php echo htmlspecialchars($editItem['short_description'] ?? ''); ?>"></div>
                                    <div class="col-md-3"><input type="file" class="form-control" name="image" accept="image/*"></div>
                                    <div class="col-md-2"><input type="number" class="form-control" name="sort_order" placeholder="Urutan" value="<?php echo $editItem['sort_order'] ?? 0; ?>"></div>
                                    <div class="col-md-2">
                                        <div class="form-check mt-2">
                                            <input class="form-check-input" type="checkbox" name="is_active" <?php echo $editItem['is_active'] ? 'checked' : ''; ?>>
                                            <label class="form-check-label">Aktif</label>
                                        </div>
                                    </div>
                                    <div class="col-md-3"><label class="form-label">Warna Utama</label><input type="color" class="form-control" name="color_primary" value="<?php echo htmlspecialchars($editItem['color_primary'] ?? '#0F2747'); ?>"></div>
                                    <div class="col-md-3"><label class="form-label">Warna Sekunder</label><input type="color" class="form-control" name="color_secondary" value="<?php echo htmlspecialchars($editItem['color_secondary'] ?? '#0E6B73'); ?>"></div>
                                    <div class="col-md-4"><button type="submit" class="btn btn-primary w-100 mt-4"><i class="bi bi-save me-2"></i> Update</button></div>
                                    <div class="col-12"><textarea class="form-control" name="full_description" placeholder="Deskripsi Lengkap" rows="2"><?php echo htmlspecialchars($editItem['full_description'] ?? ''); ?></textarea></div>
                                    <div class="col-md-12">
                                        <a href="?page=services" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-2"></i> Kembali</a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    <?php else: ?>
                        <div class="card p-4 mb-4">
                            <h6 class="fw-bold mb-3"><i class="bi bi-plus-circle me-2"></i> Tambah Layanan</h6>
                            <form method="POST" action="?page=services&action=add" enctype="multipart/form-data">
                                <div class="row g-3">
                                    <div class="col-md-3"><input type="text" class="form-control" name="name" placeholder="Nama Layanan" required></div>
                                    <div class="col-md-3"><input type="text" class="form-control" name="icon" placeholder="Icon (cth: bi bi-star)"></div>
                                    <div class="col-md-4"><input type="text" class="form-control" name="short_description" placeholder="Deskripsi Singkat"></div>
                                    <div class="col-md-3"><input type="file" class="form-control" name="image" accept="image/*"></div>
                                    <div class="col-md-2"><input type="number" class="form-control" name="sort_order" placeholder="Urutan" value="0"></div>
                                    <div class="col-md-2">
                                        <div class="form-check mt-2">
                                            <input class="form-check-input" type="checkbox" name="is_active" checked>
                                            <label class="form-check-label">Aktif</label>
                                        </div>
                                    </div>
                                    <div class="col-md-3"><label class="form-label">Warna Utama</label><input type="color" class="form-control" name="color_primary" value="#0F2747"></div>
                                    <div class="col-md-3"><label class="form-label">Warna Sekunder</label><input type="color" class="form-control" name="color_secondary" value="#0E6B73"></div>
                                    <div class="col-md-4"><button type="submit" class="btn btn-primary w-100 mt-4"><i class="bi bi-save me-2"></i> Simpan</button></div>
                                    <div class="col-12"><textarea class="form-control" name="full_description" placeholder="Deskripsi Lengkap" rows="2"></textarea></div>
                                </div>
                            </form>
                        </div>
                        <div class="card p-4">
                            <div class="table-responsive">
                                <table class="table align-middle">
                                    <thead><tr><th>#</th><th>Gambar</th><th>Nama</th><th>Icon</th><th>Urutan</th><th>Aksi</th></tr></thead>
                                    <tbody>
                                        <?php foreach ($services as $i => $svc): ?>
                                            <tr>
                                                <td><?php echo $i+1; ?></td>
                                                <td>
                                                    <?php if (!empty($svc['image_url'])): ?>
                                                        <img src="<?php echo htmlspecialchars($svc['image_url']); ?>" alt="" style="width: 80px; height: 60px; object-fit: cover; border-radius: 8px;">
                                                    <?php else: ?>
                                                        <span class="text-muted">-</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td><strong><?php echo htmlspecialchars($svc['name']); ?></strong></td>
                                                <td><i class="<?php echo htmlspecialchars($svc['icon']); ?>"></i></td>
                                                <td><?php echo $svc['sort_order']; ?></td>
                                                <td>
                                                    <a href="?page=services&action=edit&id=<?php echo $svc['id']; ?>" class="btn btn-sm btn-primary me-1"><i class="bi bi-pencil"></i></a>
                                                    <a href="?page=services&action=toggle_active&id=<?php echo $svc['id']; ?>" class="btn btn-sm btn-warning me-1"><i class="bi bi-power"></i></a>
                                                    <a href="?page=services&action=delete&id=<?php echo $svc['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus layanan ini?');"><i class="bi bi-trash"></i></a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                        <?php if (empty($services)): ?>
                                            <tr><td colspan="5" class="text-center text-muted">Belum ada layanan</td></tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php endif; ?>

                <?php elseif ($page === 'rooms'): ?>
                    <?php if ($action === 'edit' && $editItem): ?>
                        <div class="card p-4 mb-4">
                            <h6 class="fw-bold mb-3"><i class="bi bi-pencil-square me-2"></i> Edit Kamar</h6>
                            <form method="POST" action="?page=rooms&action=edit&id=<?php echo $editItem['id']; ?>" enctype="multipart/form-data">
                                <div class="row g-3">
                                    <div class="col-md-4"><input type="text" class="form-control" name="name" placeholder="Nama Kamar" required value="<?php echo htmlspecialchars($editItem['name'] ?? ''); ?>"></div>
                                    <div class="col-md-4">
                                        <select class="form-select" name="category">
                                            <option value="VVIP" <?php echo (($editItem['category'] ?? '') === 'VVIP') ? 'selected' : ''; ?>>VVIP</option>
                                            <option value="VIP" <?php echo (($editItem['category'] ?? '') === 'VIP') ? 'selected' : ''; ?>>VIP</option>
                                            <option value="Kelas 1" <?php echo (($editItem['category'] ?? '') === 'Kelas 1') ? 'selected' : ''; ?>>Kelas 1</option>
                                            <option value="Kelas 2" <?php echo (($editItem['category'] ?? '') === 'Kelas 2') ? 'selected' : ''; ?>>Kelas 2</option>
                                            <option value="Kelas 3" <?php echo (($editItem['category'] ?? '') === 'Kelas 3') ? 'selected' : ''; ?>>Kelas 3</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2"><input type="number" class="form-control" name="sort_order" placeholder="Urutan" value="<?php echo $editItem['sort_order'] ?? 0; ?>"></div>
                                    <div class="col-md-2">
                                        <div class="form-check mt-2">
                                            <input class="form-check-input" type="checkbox" name="is_active" <?php echo $editItem['is_active'] ? 'checked' : ''; ?>>
                                            <label class="form-check-label">Aktif</label>
                                        </div>
                                    </div>
                                    <div class="col-md-12"><input type="file" class="form-control" name="cover_image" accept="image/*"></div>
                                    <div class="col-12"><textarea class="form-control" name="description" placeholder="Deskripsi" rows="2"><?php echo htmlspecialchars($editItem['description'] ?? ''); ?></textarea></div>
                                    
                                    <div class="col-12">
                                        <h6 class="fw-bold mb-2"><i class="bi bi-check-circle me-2"></i> Fasilitas</h6>
                                        <div class="row g-2">
                                            <?php foreach ($defaultFacilities as $facilityName => $icon): ?>
                                                <div class="col-md-3">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="facilities[]" id="facility_<?php echo md5($facilityName); ?>" value="<?php echo htmlspecialchars($facilityName); ?>" <?php echo in_array($facilityName, $currentRoomFacilities) ? 'checked' : ''; ?>>
                                                        <label class="form-check-label" for="facility_<?php echo md5($facilityName); ?>">
                                                            <i class="<?php echo $icon; ?> me-1"></i> <?php echo htmlspecialchars($facilityName); ?>
                                                        </label>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-3">
                                        <button type="submit" class="btn btn-primary"><i class="bi bi-save me-2"></i> Update</button>
                                    </div>
                                    <div class="col-md-12">
                                        <a href="?page=rooms" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-2"></i> Kembali</a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    <?php else: ?>
                        <div class="card p-4 mb-4">
                            <h6 class="fw-bold mb-3"><i class="bi bi-plus-circle me-2"></i> Tambah Kamar</h6>
                            <form method="POST" action="?page=rooms&action=add" enctype="multipart/form-data">
                                <div class="row g-3">
                                    <div class="col-md-4"><input type="text" class="form-control" name="name" placeholder="Nama Kamar" required></div>
                                    <div class="col-md-4">
                                        <select class="form-select" name="category">
                                            <option value="VVIP">VVIP</option>
                                            <option value="VIP">VIP</option>
                                            <option value="Kelas 1">Kelas 1</option>
                                            <option value="Kelas 2">Kelas 2</option>
                                            <option value="Kelas 3">Kelas 3</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2"><input type="number" class="form-control" name="sort_order" placeholder="Urutan" value="0"></div>
                                    <div class="col-md-2">
                                        <div class="form-check mt-2">
                                            <input class="form-check-input" type="checkbox" name="is_active" checked>
                                            <label class="form-check-label">Aktif</label>
                                        </div>
                                    </div>
                                    <div class="col-md-12"><input type="file" class="form-control" name="cover_image" accept="image/*"></div>
                                    <div class="col-12"><textarea class="form-control" name="description" placeholder="Deskripsi" rows="2"></textarea></div>
                                    
                                    <div class="col-12">
                                        <h6 class="fw-bold mb-2"><i class="bi bi-check-circle me-2"></i> Fasilitas</h6>
                                        <div class="row g-2">
                                            <?php foreach ($defaultFacilities as $facilityName => $icon): ?>
                                                <div class="col-md-3">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="facilities[]" id="facility_add_<?php echo md5($facilityName); ?>" value="<?php echo htmlspecialchars($facilityName); ?>">
                                                        <label class="form-check-label" for="facility_add_<?php echo md5($facilityName); ?>">
                                                            <i class="<?php echo $icon; ?> me-1"></i> <?php echo htmlspecialchars($facilityName); ?>
                                                        </label>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-3"><button type="submit" class="btn btn-primary"><i class="bi bi-save me-2"></i> Simpan</button></div>
                                </div>
                            </form>
                        </div>
                        <div class="card p-4">
                            <div class="table-responsive">
                                <table class="table align-middle">
                                    <thead><tr><th>#</th><th>Nama</th><th>Kategori</th><th>Aksi</th></tr></thead>
                                    <tbody>
                                        <?php foreach ($rooms as $i => $room): ?>
                                            <tr>
                                                <td><?php echo $i+1; ?></td>
                                                <td><strong><?php echo htmlspecialchars($room['name']); ?></strong></td>
                                                <td><span class="badge bg-primary"><?php echo htmlspecialchars($room['category']); ?></span></td>
                                                <td>
                                                    <a href="?page=rooms&action=edit&id=<?php echo $room['id']; ?>" class="btn btn-sm btn-primary me-1"><i class="bi bi-pencil"></i></a>
                                                    <a href="?page=rooms&action=toggle_active&id=<?php echo $room['id']; ?>" class="btn btn-sm btn-warning me-1"><i class="bi bi-power"></i></a>
                                                    <a href="?page=rooms&action=delete&id=<?php echo $room['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus kamar ini?');"><i class="bi bi-trash"></i></a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                        <?php if (empty($rooms)): ?>
                                            <tr><td colspan="4" class="text-center text-muted">Belum ada kamar</td></tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php endif; ?>

                <?php elseif ($page === 'outpatient'): ?>
                    <?php if ($action === 'edit' && $editItem): ?>
                        <div class="card p-4 mb-4">
                            <h6 class="fw-bold mb-3"><i class="bi bi-pencil-square me-2"></i> Edit Banner Rawat Jalan</h6>
                            <form method="POST" action="?page=outpatient&action=edit&id=<?php echo $editItem['id']; ?>" enctype="multipart/form-data">
                                <div class="row g-3">
                                    <div class="col-md-4"><input type="text" class="form-control" name="title" placeholder="Judul" required value="<?php echo htmlspecialchars($editItem['title'] ?? ''); ?>"></div>
                                    <div class="col-md-4"><input type="file" class="form-control" name="banner" accept="image/*"></div>
                                    <div class="col-md-2"><input type="text" class="form-control" name="cta_text" placeholder="CTA Text" value="<?php echo htmlspecialchars($editItem['cta_text'] ?? ''); ?>"></div>
                                    <div class="col-md-2"><input type="text" class="form-control" name="cta_link" placeholder="#contact" value="<?php echo htmlspecialchars($editItem['cta_link'] ?? ''); ?>"></div>
                                    <div class="col-md-1"><input type="number" class="form-control" name="sort_order" placeholder="Urutan" value="<?php echo $editItem['sort_order'] ?? 0; ?>"></div>
                                    <div class="col-md-1">
                                        <div class="form-check mt-2">
                                            <input class="form-check-input" type="checkbox" name="is_active" <?php echo $editItem['is_active'] ? 'checked' : ''; ?>>
                                            <label class="form-check-label">Aktif</label>
                                        </div>
                                    </div>
                                    <div class="col-12"><textarea class="form-control" name="description" placeholder="Deskripsi" rows="2"><?php echo htmlspecialchars($editItem['description'] ?? ''); ?></textarea></div>
                                    <div class="col-md-3">
                                        <button type="submit" class="btn btn-primary"><i class="bi bi-save me-2"></i> Update</button>
                                    </div>
                                    <div class="col-md-12">
                                        <a href="?page=outpatient" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-2"></i> Kembali</a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    <?php else: ?>
                        <div class="card p-4 mb-4">
                            <h6 class="fw-bold mb-3"><i class="bi bi-plus-circle me-2"></i> Tambah Banner Rawat Jalan</h6>
                            <form method="POST" action="?page=outpatient&action=add" enctype="multipart/form-data">
                                <div class="row g-3">
                                    <div class="col-md-4"><input type="text" class="form-control" name="title" placeholder="Judul" required></div>
                                    <div class="col-md-4"><input type="file" class="form-control" name="banner" accept="image/*"></div>
                                    <div class="col-md-2"><input type="text" class="form-control" name="cta_text" placeholder="CTA Text"></div>
                                    <div class="col-md-2"><input type="text" class="form-control" name="cta_link" placeholder="#contact"></div>
                                    <div class="col-md-1"><input type="number" class="form-control" name="sort_order" placeholder="Urutan" value="0"></div>
                                    <div class="col-md-1">
                                        <div class="form-check mt-2">
                                            <input class="form-check-input" type="checkbox" name="is_active" checked>
                                            <label class="form-check-label">Aktif</label>
                                        </div>
                                    </div>
                                    <div class="col-12"><textarea class="form-control" name="description" placeholder="Deskripsi" rows="2"></textarea></div>
                                    <div class="col-md-3"><button type="submit" class="btn btn-primary"><i class="bi bi-save me-2"></i> Simpan</button></div>
                                </div>
                            </form>
                        </div>
                        <div class="card p-4">
                            <div class="table-responsive">
                                <table class="table align-middle">
                                    <thead><tr><th>#</th><th>Judul</th><th>CTA</th><th>Aktif</th><th>Aksi</th></tr></thead>
                                    <tbody>
                                        <?php foreach ($outpatientBanners as $i => $banner): ?>
                                            <tr>
                                                <td><?php echo $i+1; ?></td>
                                                <td><strong><?php echo htmlspecialchars($banner['title']); ?></strong></td>
                                                <td><?php echo htmlspecialchars($banner['cta_text'] ?? '-'); ?></td>
                                                <td><?php echo $banner['is_active'] ? '<span class="badge bg-success">Ya</span>' : '<span class="badge bg-secondary">Tidak</span>'; ?></td>
                                                <td>
                                                    <a href="?page=outpatient&action=edit&id=<?php echo $banner['id']; ?>" class="btn btn-sm btn-primary me-1"><i class="bi bi-pencil"></i></a>
                                                    <a href="?page=outpatient&action=toggle_active&id=<?php echo $banner['id']; ?>" class="btn btn-sm btn-warning me-1"><i class="bi bi-power"></i></a>
                                                    <a href="?page=outpatient&action=delete&id=<?php echo $banner['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus banner ini?');"><i class="bi bi-trash"></i></a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                        <?php if (empty($outpatientBanners)): ?>
                                            <tr><td colspan="5" class="text-center text-muted">Belum ada banner rawat jalan</td></tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php endif; ?>

                <?php elseif ($page === 'insurances'): ?>
                    <?php if ($action === 'edit' && $editItem): ?>
                        <div class="card p-4 mb-4">
                            <h6 class="fw-bold mb-3"><i class="bi bi-pencil-square me-2"></i> Edit Asuransi</h6>
                            <form method="POST" action="?page=insurances&action=edit&id=<?php echo $editItem['id']; ?>" enctype="multipart/form-data">
                                <div class="row g-3">
                                    <div class="col-md-5"><input type="text" class="form-control" name="name" placeholder="Nama Asuransi" required value="<?php echo htmlspecialchars($editItem['name'] ?? ''); ?>"></div>
                                    <div class="col-md-4"><input type="file" class="form-control" name="logo" accept="image/*"></div>
                                    <div class="col-md-1"><input type="number" class="form-control" name="sort_order" placeholder="Urutan" value="<?php echo $editItem['sort_order'] ?? 0; ?>"></div>
                                    <div class="col-md-1">
                                        <div class="form-check mt-2">
                                            <input class="form-check-input" type="checkbox" name="is_active" <?php echo $editItem['is_active'] ? 'checked' : ''; ?>>
                                            <label class="form-check-label">Aktif</label>
                                        </div>
                                    </div>
                                    <?php if (!empty($editItem['logo_url'])): ?>
                                        <div class="col-md-12">
                                            <label class="form-label fw-bold">Preview Logo</label>
                                            <img src="<?php echo htmlspecialchars($editItem['logo_url']); ?>" alt="<?php echo htmlspecialchars($editItem['name']); ?>" style="max-width: 200px; max-height: 100px; border: 1px solid #eee; border-radius: 8px; padding: 8px;">
                                        </div>
                                    <?php endif; ?>
                                    <div class="col-md-3">
                                        <button type="submit" class="btn btn-primary"><i class="bi bi-save me-2"></i> Update</button>
                                    </div>
                                    <div class="col-md-12">
                                        <a href="?page=insurances" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-2"></i> Kembali</a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    <?php else: ?>
                        <div class="card p-4 mb-4">
                            <h6 class="fw-bold mb-3"><i class="bi bi-plus-circle me-2"></i> Tambah Asuransi</h6>
                            <form method="POST" action="?page=insurances&action=add" enctype="multipart/form-data">
                                <div class="row g-3">
                                    <div class="col-md-5"><input type="text" class="form-control" name="name" placeholder="Nama Asuransi" required></div>
                                    <div class="col-md-4"><input type="file" class="form-control" name="logo" accept="image/*"></div>
                                    <div class="col-md-1"><input type="number" class="form-control" name="sort_order" placeholder="Urutan" value="0"></div>
                                    <div class="col-md-1">
                                        <div class="form-check mt-2">
                                            <input class="form-check-input" type="checkbox" name="is_active" checked>
                                            <label class="form-check-label">Aktif</label>
                                        </div>
                                    </div>
                                    <div class="col-md-3"><button type="submit" class="btn btn-primary"><i class="bi bi-save me-2"></i> Simpan</button></div>
                                </div>
                            </form>
                        </div>
                        <div class="card p-4">
                            <div class="table-responsive">
                                <table class="table align-middle">
                                    <thead><tr><th>#</th><th>Nama</th><th>Urutan</th><th>Aktif</th><th>Aksi</th></tr></thead>
                                    <tbody>
                                        <?php foreach ($insurances as $i => $ins): ?>
                                            <tr>
                                                <td><?php echo $i+1; ?></td>
                                                <td><strong><?php echo htmlspecialchars($ins['name']); ?></strong></td>
                                                <td><?php echo $ins['sort_order']; ?></td>
                                                <td><?php echo $ins['is_active'] ? '<span class="badge bg-success">Ya</span>' : '<span class="badge bg-secondary">Tidak</span>'; ?></td>
                                                <td>
                                                    <a href="?page=insurances&action=edit&id=<?php echo $ins['id']; ?>" class="btn btn-sm btn-primary me-1"><i class="bi bi-pencil"></i></a>
                                                    <a href="?page=insurances&action=toggle_active&id=<?php echo $ins['id']; ?>" class="btn btn-sm btn-warning me-1"><i class="bi bi-power"></i></a>
                                                    <a href="?page=insurances&action=delete&id=<?php echo $ins['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus asuransi ini?');"><i class="bi bi-trash"></i></a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                        <?php if (empty($insurances)): ?>
                                            <tr><td colspan="5" class="text-center text-muted">Belum ada asuransi</td></tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php endif; ?>

                <?php elseif ($page === 'media'): ?>
                    <div class="card p-4 mb-4">
                        <h6 class="fw-bold mb-3"><i class="bi bi-upload me-2"></i> Upload File</h6>
                        <form method="POST" enctype="multipart/form-data">
                            <div class="row g-3">
                                <div class="col-md-4"><select class="form-select" name="folder">
                                    <?php foreach (['banner','doctor','service','insurance','logo','room','gallery','other'] as $f): ?>
                                        <option value="<?php echo $f; ?>" <?php echo $mediaFolder === $f ? 'selected' : ''; ?>><?php echo ucfirst($f); ?></option>
                                    <?php endforeach; ?>
                                </select></div>
                                <div class="col-md-5"><input type="file" class="form-control" name="files[]" multiple accept="image/*"></div>
                                <div class="col-md-3"><button type="submit" class="btn btn-primary w-100"><i class="bi bi-upload me-2"></i> Upload</button></div>
                            </div>
                        </form>
                    </div>
                    <div class="card p-4">
                        <div class="mb-3">
                            <a href="?page=media&folder=all" class="btn btn-outline-primary btn-sm me-1">All</a>
                            <?php foreach (['banner','doctor','service','insurance','logo','room','gallery','other'] as $f): ?>
                                <a href="?page=media&folder=<?php echo $f; ?>" class="btn btn-outline-secondary btn-sm me-1"><?php echo ucfirst($f); ?></a>
                            <?php endforeach; ?>
                        </div>
                        <div class="row g-3">
                            <?php foreach ($mediaFiles as $file): ?>
                                <div class="col-md-3 col-lg-2">
                                    <div class="grid-item">
                                        <?php if (str_starts_with($file['file_type'], 'image/')): ?>
                                            <img src="<?php echo htmlspecialchars($file['file_path']); ?>" alt="">
                                        <?php else: ?>
                                            <div class="d-flex align-items-center justify-content-center h-100">
                                                <i class="bi bi-file-earmark fs-1 text-muted"></i>
                                            </div>
                                        <?php endif; ?>
                                        <div class="p-2 small text-center">
                                            <small><?php echo htmlspecialchars($file['file_name']); ?></small><br>
                                            <a href="?page=media&action=delete&id=<?php echo $file['id']; ?>" class="text-danger small" onclick="return confirm('Hapus file ini?');">Hapus</a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            <?php if (empty($mediaFiles)): ?>
                                <div class="col-12 text-center text-muted py-4">Belum ada file di folder ini</div>
                            <?php endif; ?>
                        </div>
                    </div>

                <?php elseif ($page === 'settings'): ?>
                    <form method="POST" enctype="multipart/form-data">
                        <div class="card p-4 mb-4">
                            <h6 class="fw-bold mb-3"><i class="bi bi-building me-2"></i> Branding</h6>
                            <div class="row g-3">
                                <div class="col-md-6"><label class="form-label fw-bold">Nama Rumah Sakit</label><input type="text" class="form-control" name="hospital_name" value="<?php echo htmlspecialchars($settings['hospital_name']); ?>" required></div>
                                <div class="col-md-3"><label class="form-label fw-bold">Logo</label><input type="file" class="form-control" name="logo_image" accept="image/*"></div>
                                <div class="col-md-3"><label class="form-label fw-bold">Favicon</label><input type="file" class="form-control" name="favicon_image" accept="image/*"></div>
                            </div>
                        </div>
                        <div class="card p-4 mb-4">
                            <h6 class="fw-bold mb-3"><i class="bi bi-telephone me-2"></i> Kontak</h6>
                            <div class="row g-3">
                                <div class="col-md-4"><label class="form-label fw-bold">Telepon</label><input type="text" class="form-control" name="contact_phone" value="<?php echo htmlspecialchars($settings['contact_phone']); ?>"></div>
                                <div class="col-md-4"><label class="form-label fw-bold">Email</label><input type="email" class="form-control" name="contact_email" value="<?php echo htmlspecialchars($settings['contact_email']); ?>"></div>
                                <div class="col-md-4"><label class="form-label fw-bold">No. WhatsApp</label><input type="text" class="form-control" name="wa_number" value="<?php echo htmlspecialchars($settings['wa_number']); ?>"></div>
                                <div class="col-12"><label class="form-label fw-bold">Alamat</label><textarea class="form-control" name="contact_address" rows="2"><?php echo htmlspecialchars($settings['contact_address'] ?? ''); ?></textarea></div>
                                <div class="col-12"><label class="form-label fw-bold">Maps Embed</label><textarea class="form-control" name="maps_embed" rows="2"><?php echo htmlspecialchars($settings['maps_embed'] ?? ''); ?></textarea></div>
                            </div>
                        </div>
                        <div class="card p-4 mb-4">
                            <h6 class="fw-bold mb-3"><i class="bi bi-globe me-2"></i> Social Media</h6>
                            <div class="row g-3">
                                <div class="col-md-4"><label class="form-label fw-bold">Instagram</label><input type="text" class="form-control" name="social_instagram" value="<?php echo htmlspecialchars($settings['social_instagram'] ?? '#'); ?>"></div>
                                <div class="col-md-4"><label class="form-label fw-bold">TikTok</label><input type="text" class="form-control" name="social_tiktok" value="<?php echo htmlspecialchars($settings['social_tiktok'] ?? '#'); ?>"></div>
                                <div class="col-md-4"><label class="form-label fw-bold">Twitter</label><input type="text" class="form-control" name="social_twitter" value="<?php echo htmlspecialchars($settings['social_twitter'] ?? '#'); ?>"></div>
                            </div>
                        </div>
                        <div class="card p-4 mb-4">
                            <h6 class="fw-bold mb-3"><i class="bi bi-palette me-2"></i> Warna Tema</h6>
                            <div class="row g-3">
                                <div class="col-md-4"><label class="form-label fw-bold">Primary</label><input type="color" class="form-control form-control-color" name="primary_color" value="<?php echo htmlspecialchars($settings['theme_color_primary']); ?>"></div>
                                <div class="col-md-4"><label class="form-label fw-bold">Secondary</label><input type="color" class="form-control form-control-color" name="secondary_color" value="<?php echo htmlspecialchars($settings['theme_color_secondary']); ?>"></div>
                                <div class="col-md-4"><label class="form-label fw-bold">Accent</label><input type="color" class="form-control form-control-color" name="accent_color" value="<?php echo htmlspecialchars($settings['theme_color_accent']); ?>"></div>
                            </div>
                            <div class="form-check mt-3">
                                <input class="form-check-input" type="checkbox" name="navbar_sticky" id="stickyNav" <?php echo ($settings['navbar_sticky'] ?? 1) ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="stickyNav">Navbar Sticky</label>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary btn-lg"><i class="bi bi-save me-2"></i> Simpan Pengaturan</button>
                    </form>

                <?php elseif ($page === 'seo'): ?>
                    <form method="POST">
                        <div class="card p-4 mb-4">
                            <h6 class="fw-bold mb-3"><i class="bi bi-search me-2"></i> SEO Settings</h6>
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label fw-bold">Meta Title</label>
                                    <input type="text" class="form-control" name="seo_meta_title" value="<?php echo htmlspecialchars($settings['seo_meta_title'] ?? ''); ?>">
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-bold">Meta Description</label>
                                    <textarea class="form-control" name="seo_meta_description" rows="3"><?php echo htmlspecialchars($settings['seo_meta_description'] ?? ''); ?></textarea>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-bold">Meta Keywords</label>
                                    <input type="text" class="form-control" name="seo_meta_keywords" value="<?php echo htmlspecialchars($settings['seo_meta_keywords'] ?? ''); ?>">
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary btn-lg"><i class="bi bi-save me-2"></i> Simpan Pengaturan SEO</button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
