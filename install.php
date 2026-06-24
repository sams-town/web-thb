<?php
require_once 'config.php';

$error = null;
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $schemaPath = __DIR__ . '/database/schema.sql';
        $schema = file_get_contents($schemaPath);
        if ($schema === false) {
            throw new Exception("Tidak dapat membaca schema.sql");
        }

        $pdo = getDB();
        $pdo->exec($schema);

        $success = true;
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Install - RS Taman Harapan Baru</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg, #0F2747 0%, #0E6B73 100%); min-height: 100vh; display:flex; align-items:center; }
        .card { border: none; border-radius: 20px; box-shadow: 0 20px 60px rgba(0,0,0,0.3); }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="card p-5">
                    <h2 class="fw-bold text-center mb-4" style="color:#0F2747;">
                        <i class="bi bi-hospital"></i> Install Database
                    </h2>
                    <?php if ($success): ?>
                        <div class="alert alert-success text-center">
                            <h5 class="fw-bold">Install Berhasil!</h5>
                            <p>Database telah dibuat dengan sukses.</p>
                            <a href="index.php" class="btn btn-primary mt-2" style="background:linear-gradient(135deg,#0F2747,#0E6B73); border:none;">
                                Ke Website
                            </a>
                            <a href="admin.php" class="btn btn-outline-primary mt-2 ms-2">
                                Ke Admin Panel
                            </a>
                        </div>
                    <?php else: ?>
                        <p class="text-muted text-center mb-4">
                            Klik tombol dibawah untuk menginstall database.
                        </p>
                        <form method="POST">
                            <button type="submit" class="btn btn-primary w-100 btn-lg" style="background:linear-gradient(135deg,#0F2747,#0E6B73); border:none;">
                                <i class="bi bi-database-fill-gear me-2"></i> Install Sekarang
                            </button>
                        </form>
                        <?php if ($error): ?>
                            <div class="alert alert-danger mt-4">
                                <i class="bi bi-exclamation-triangle"></i> Error: <?php echo htmlspecialchars($error); ?>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
</body>
</html>
