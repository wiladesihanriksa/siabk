<?php
// Script sederhana untuk cek status logo
include 'koneksi.php';
include 'functions_app_settings.php';

echo "<h2>🔍 Cek Status Logo</h2>";

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

echo "<h3>📁 File Status:</h3>";
echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
echo "<tr><th>File Path</th><th>Status</th><th>Size</th></tr>";

$files_to_check = [
    'gambar/sistem/logo.png',
    'gambar/sistem/favicon.ico', 
    'gambar/sistem/login_logo.png',
    'gambar/sistem/user.png'
];

foreach($files_to_check as $file) {
    if(file_exists($file)) {
        $size = filesize($file);
        echo "<tr><td>{$file}</td><td style='color: green;'>✅ ADA</td><td>{$size} bytes</td></tr>";
    } else {
        echo "<tr><td>{$file}</td><td style='color: red;'>❌ TIDAK ADA</td><td>-</td></tr>";
    }
}
echo "</table>";

echo "<h3>🧪 Test Logic Admin Header:</h3>";
$logo = getAppLogo($app_settings);
echo "<p><strong>Logo dari getAppLogo():</strong> {$logo}</p>";
echo "<p><strong>Kondisi:</strong> " . ($logo != 'gambar/sistem/user.png' ? 'TRUE - Logo akan ditampilkan' : 'FALSE - Logo tidak akan ditampilkan') . "</p>";

if($logo != 'gambar/sistem/user.png') {
    echo "<p style='color: green; font-size: 18px;'>🎉 LOGO AKAN MUNCUL DI ADMIN!</p>";
} else {
    echo "<p style='color: red; font-size: 18px;'>❌ Logo tidak akan muncul, akan menggunakan icon trophy</p>";
}

echo "<hr>";
echo "<h3>💡 Solusi:</h3>";
echo "<ol>";
echo "<li><strong>Gunakan halaman Pengaturan Aplikasi</strong> untuk upload logo</li>";
echo "<li><strong>Akses:</strong> <a href='admin/pengaturan_aplikasi.php' target='_blank'>admin/pengaturan_aplikasi.php</a></li>";
echo "<li><strong>Upload file logo</strong> melalui form yang sudah ada</li>";
echo "<li><strong>Clear cache browser</strong> setelah upload</li>";
echo "</ol>";

echo "<p><a href='admin/pengaturan_aplikasi.php' class='btn'>⚙️ Pengaturan Aplikasi</a> | <a href='admin/index.php' class='btn'>🔐 Dashboard Admin</a></p>";
?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; }
table { margin: 10px 0; }
.btn { padding: 10px 20px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; display: inline-block; margin: 5px; }
.btn:hover { background: #0056b3; }
</style>
