<?php
// Debug script untuk cek status logo
include 'koneksi.php';
include 'functions_app_settings.php';

echo "<h2>🔍 Debug Status Logo</h2>";

// Ambil pengaturan aplikasi
$app_settings = getAppSettings($koneksi);

echo "<h3>📊 Database Settings:</h3>";
echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
echo "<tr><th>Setting Key</th><th>Setting Value</th></tr>";

$logo_keys = ['app_logo', 'app_favicon', 'login_logo'];
foreach($logo_keys as $key) {
    $value = getSetting($app_settings, $key, 'TIDAK ADA');
    echo "<tr><td>{$key}</td><td>{$value}</td></tr>";
}
echo "</table>";

echo "<h3>📁 File Status:</h3>";
echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
echo "<tr><th>File Path</th><th>Status</th><th>Size</th><th>Modified</th></tr>";

$files_to_check = [
    '../gambar/sistem/logo.png',
    '../gambar/sistem/favicon.ico', 
    '../gambar/sistem/login_logo.png',
    '../gambar/sistem/user.png'
];

foreach($files_to_check as $file) {
    $exists = @fopen($file, 'r');
    if($exists) {
        fclose($exists);
        $size = filesize($file);
        $modified = date('Y-m-d H:i:s', filemtime($file));
        echo "<tr><td>{$file}</td><td style='color: green;'>✅ ADA</td><td>{$size} bytes</td><td>{$modified}</td></tr>";
    } else {
        echo "<tr><td>{$file}</td><td style='color: red;'>❌ TIDAK ADA</td><td>-</td><td>-</td></tr>";
    }
}
echo "</table>";

echo "<h3>🔗 Function Test:</h3>";
echo "<p><strong>getAppLogo():</strong> " . getAppLogo($app_settings) . "</p>";
echo "<p><strong>getAppFavicon():</strong> " . getAppFavicon($app_settings) . "</p>";
echo "<p><strong>getLoginLogo():</strong> " . getLoginLogo($app_settings) . "</p>";

echo "<h3>📂 Directory Permissions:</h3>";
$dir = '../gambar/sistem/';
if(is_dir($dir)) {
    $perms = substr(sprintf('%o', fileperms($dir)), -4);
    echo "<p><strong>Directory:</strong> {$dir}</p>";
    echo "<p><strong>Permissions:</strong> {$perms}</p>";
    echo "<p><strong>Writable:</strong> " . (is_writable($dir) ? "✅ YA" : "❌ TIDAK") . "</p>";
} else {
    echo "<p style='color: red;'>❌ Directory tidak ada: {$dir}</p>";
}

echo "<h3>🌐 Server Info:</h3>";
echo "<p><strong>PHP Version:</strong> " . phpversion() . "</p>";
echo "<p><strong>Server:</strong> " . $_SERVER['SERVER_SOFTWARE'] . "</p>";
echo "<p><strong>Document Root:</strong> " . $_SERVER['DOCUMENT_ROOT'] . "</p>";
echo "<p><strong>Current Path:</strong> " . __DIR__ . "</p>";

// Test upload directory
echo "<h3>📤 Upload Test:</h3>";
$upload_dir = '../gambar/sistem/';
if (!file_exists($upload_dir)) {
    echo "<p style='color: orange;'>⚠️ Directory tidak ada, mencoba membuat...</p>";
    if(mkdir($upload_dir, 0777, true)) {
        echo "<p style='color: green;'>✅ Directory berhasil dibuat</p>";
    } else {
        echo "<p style='color: red;'>❌ Gagal membuat directory</p>";
    }
} else {
    echo "<p style='color: green;'>✅ Directory sudah ada</p>";
}

echo "<hr>";
echo "<p><strong>💡 Solusi:</strong></p>";
echo "<ol>";
echo "<li>Pastikan file logo sudah diupload ke folder <code>gambar/sistem/</code></li>";
echo "<li>Clear cache browser (Ctrl+F5)</li>";
echo "<li>Cek permission folder <code>gambar/sistem/</code> harus 777</li>";
echo "<li>Pastikan file logo memiliki nama yang benar: <code>logo.png</code>, <code>favicon.ico</code>, <code>login_logo.png</code></li>";
echo "</ol>";
?>
