<?php
// Start output buffering
ob_start();

// Include index.php to get the final HTML
require __DIR__ . '/index.php';

// Get the buffered content
$html = ob_get_clean();

// Create dist folder if not exists
$distDir = __DIR__ . '/dist';
if (!is_dir($distDir)) {
    mkdir($distDir, 0777, true);
}

// Save to dist/index.html
file_put_contents($distDir . '/index.html', $html);

// Copy assets folder
function recurse_copy($src, $dst) {
    $dir = opendir($src);
    @mkdir($dst);
    while(false !== ( $file = readdir($dir)) ) {
        if (( $file != '.' ) && ( $file != '..' )) {
            if ( is_dir($src . '/' . $file) ) {
                recurse_copy($src . '/' . $file,$dst . '/' . $file);
            }
            else {
                copy($src . '/' . $file,$dst . '/' . $file);
            }
        }
    }
    closedir($dir);
}

// Copy assets
if (is_dir(__DIR__ . '/assets')) {
    recurse_copy(__DIR__ . '/assets', $distDir . '/assets');
}

// Copy uploads
if (is_dir(__DIR__ . '/uploads')) {
    recurse_copy(__DIR__ . '/uploads', $distDir . '/uploads');
}

echo "✅ Static site generated successfully!\n";
echo "📁 Output folder: $distDir\n";
?>