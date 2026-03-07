<?php
// Debug script khusus untuk admin header
include 'koneksi.php';
include 'functions_app_settings.php';

echo "<h2>🔍 Debug Logo Admin</h2>";

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

echo "<h3>🔗 Function Test:</h3>";
echo "<p><strong>getAppLogo():</strong> " . getAppLogo($app_settings) . "</p>";
echo "<p><strong>getAppFavicon():</strong> " . getAppFavicon($app_settings) . "</p>";
echo "<p><strong>getLoginLogo():</strong> " . getLoginLogo($app_settings) . "</p>";

echo "<h3>📁 File Status (Path Relatif):</h3>";
echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
echo "<tr><th>File Path</th><th>Status</th><th>Size</th><th>Modified</th></tr>";

$files_to_check = [
    'gambar/sistem/logo.png',
    'gambar/sistem/favicon.ico', 
    'gambar/sistem/login_logo.png',
    'gambar/sistem/user.png'
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

echo "<h3>🧪 Test Logic Admin Header:</h3>";
$logo = getAppLogo($app_settings);
echo "<p><strong>Logo dari getAppLogo():</strong> {$logo}</p>";
echo "<p><strong>Kondisi:</strong> " . ($logo != 'gambar/sistem/user.png' ? 'TRUE - Logo akan ditampilkan' : 'FALSE - Logo tidak akan ditampilkan') . "</p>";

if($logo != 'gambar/sistem/user.png') {
    echo "<p style='color: green;'>✅ Logo akan ditampilkan di admin header</p>";
    echo "<p><strong>HTML yang akan dihasilkan:</strong></p>";
    echo "<pre>&lt;img src=\"{$logo}\" style=\"height: 30px; width: 30px;\"&gt;</pre>";
} else {
    echo "<p style='color: red;'>❌ Logo tidak akan ditampilkan, akan menggunakan icon trophy</p>";
}

echo "<h3>📂 Directory Structure:</h3>";
echo "<p><strong>Current Directory:</strong> " . __DIR__ . "</p>";
echo "<p><strong>Document Root:</strong> " . $_SERVER['DOCUMENT_ROOT'] . "</p>";

// Cek apakah directory ada
$dirs_to_check = ['gambar', 'gambar/sistem'];
foreach($dirs_to_check as $dir) {
    if(is_dir($dir)) {
        $perms = substr(sprintf('%o', fileperms($dir)), -4);
        echo "<p style='color: green;'>✅ Directory '{$dir}' ada - Permission: {$perms}</p>";
    } else {
        echo "<p style='color: red;'>❌ Directory '{$dir}' tidak ada</p>";
    }
}

echo "<h3>🌐 Test URL Access:</h3>";
$base_url = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']);
echo "<p><strong>Base URL:</strong> {$base_url}</p>";

foreach($files_to_check as $file) {
    $url = $base_url . '/' . $file;
    echo "<p><a href='{$url}' target='_blank'>{$file}</a></p>";
}

echo "<hr>";
echo "<h3>💡 Solusi:</h3>";
echo "<ol>";
echo "<li>Pastikan file logo ada di folder <code>gambar/sistem/</code></li>";
echo "<li>Pastikan permission folder <code>gambar/sistem/</code> adalah 777</li>";
echo "<li>Upload file logo yang benar: <code>logo.png</code></li>";
echo "<li>Clear cache browser (Ctrl+F5)</li>";
echo "</ol>";

echo "<p><a href='admin.php' class='btn'>Test Login Admin</a> | <a href='admin/index.php' class='btn'>Test Dashboard Admin</a></p>";
?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; }
table { margin: 10px 0; }
.btn { padding: 10px 20px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; display: inline-block; margin: 5px; }
.btn:hover { background: #0056b3; }
</style>
