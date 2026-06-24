<?php
require_once 'config.php';

$rooms = getRooms(false);

echo "<h1>Debug Rooms</h1>";
echo "<table border='1' cellpadding='8'>";
echo "<tr><th>ID</th><th>Nama</th><th>cover_url</th><th>File Exists?</th></tr>";

foreach ($rooms as $room) {
    $fileExists = !empty($room['cover_url']) && file_exists(__DIR__ . '/' . $room['cover_url']);
    $filePath = !empty($room['cover_url']) ? (__DIR__ . '/' . $room['cover_url']) : 'N/A';
    echo "<tr>";
    echo "<td>{$room['id']}</td>";
    echo "<td>{$room['name']}</td>";
    echo "<td>" . htmlspecialchars($room['cover_url'] ?? 'NULL') . "</td>";
    echo "<td>" . ($fileExists ? '✅ Yes' : '❌ No') . "</td>";
    echo "</tr>";
}

echo "</table>";

echo "<hr>";
echo "<h2>Uploads Folder Contents:</h2>";
$uploadDir = __DIR__ . '/uploads/room';
if (is_dir($uploadDir)) {
    $files = scandir($uploadDir);
    echo "<ul>";
    foreach ($files as $file) {
        if ($file !== '.' && $file !== '..') {
            echo "<li>$file</li>";
        }
    }
    echo "</ul>";
} else {
    echo "<p>uploads/room directory does not exist!</p>";
}
?>