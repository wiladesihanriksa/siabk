<?php
// Script untuk memperbaiki logo di server online
include 'koneksi.php';

echo "<h2>🔧 Fix Logo Online</h2>";

// Pastikan directory ada
$upload_dir = 'gambar/sistem/';
if (!file_exists($upload_dir)) {
    if(mkdir($upload_dir, 0777, true)) {
        echo "<p style='color: green;'>✅ Directory {$upload_dir} berhasil dibuat</p>";
    } else {
        echo "<p style='color: red;'>❌ Gagal membuat directory {$upload_dir}</p>";
        exit;
    }
} else {
    echo "<p style='color: green;'>✅ Directory {$upload_dir} sudah ada</p>";
}

// Set permission directory
chmod($upload_dir, 0777);
echo "<p style='color: green;'>✅ Permission directory diatur ke 777</p>";

// Cek apakah ada file logo default
$default_files = [
    'gambar/sistem/user.png' => 'user.png',
    'gambar/sistem/logo.png' => 'logo.png', 
    'gambar/sistem/login_logo.png' => 'login_logo.png',
    'gambar/sistem/favicon.ico' => 'favicon.ico'
];

echo "<h3>📁 Status File Logo:</h3>";
foreach($default_files as $file_path => $filename) {
    if(file_exists($file_path)) {
        $size = filesize($file_path);
        echo "<p style='color: green;'>✅ {$filename} - {$size} bytes</p>";
    } else {
        echo "<p style='color: red;'>❌ {$filename} - TIDAK ADA</p>";
        
        // Buat file placeholder jika tidak ada
        if($filename == 'user.png') {
            // Buat file placeholder untuk user.png
            $placeholder_content = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg==');
            file_put_contents($file_path, $placeholder_content);
            echo "<p style='color: orange;'>⚠️ File placeholder dibuat untuk {$filename}</p>";
        }
    }
}

// Update database dengan path yang benar
echo "<h3>🗄️ Update Database:</h3>";

$logo_settings = [
    'app_logo' => 'logo.png',
    'app_favicon' => 'favicon.ico', 
    'login_logo' => 'login_logo.png'
];

foreach($logo_settings as $key => $value) {
    // Cek apakah setting sudah ada
    $check_query = "SELECT id FROM app_settings WHERE setting_key = '{$key}'";
    $result = mysqli_query($koneksi, $check_query);
    
    if(mysqli_num_rows($result) > 0) {
        // Update existing
        $update_query = "UPDATE app_settings SET setting_value = '{$value}' WHERE setting_key = '{$key}'";
        if(mysqli_query($koneksi, $update_query)) {
            echo "<p style='color: green;'>✅ Updated {$key} = {$value}</p>";
        } else {
            echo "<p style='color: red;'>❌ Failed to update {$key}</p>";
        }
    } else {
        // Insert new
        $insert_query = "INSERT INTO app_settings (setting_key, setting_value, setting_type, description) VALUES ('{$key}', '{$value}', 'text', 'Logo aplikasi')";
        if(mysqli_query($koneksi, $insert_query)) {
            echo "<p style='color: green;'>✅ Inserted {$key} = {$value}</p>";
        } else {
            echo "<p style='color: red;'>❌ Failed to insert {$key}</p>";
        }
    }
}

echo "<h3>🧪 Test Functions:</h3>";
include 'functions_app_settings.php';
$app_settings = getAppSettings($koneksi);

echo "<p><strong>getAppLogo():</strong> " . getAppLogo($app_settings) . "</p>";
echo "<p><strong>getAppFavicon():</strong> " . getAppFavicon($app_settings) . "</p>";
echo "<p><strong>getLoginLogo():</strong> " . getLoginLogo($app_settings) . "</p>";

echo "<hr>";
echo "<h3>✅ Selesai!</h3>";
echo "<p>Logo sudah diperbaiki. Silakan:</p>";
echo "<ol>";
echo "<li>Upload file logo yang benar ke folder <code>gambar/sistem/</code></li>";
echo "<li>Clear cache browser (Ctrl+F5)</li>";
echo "<li>Test halaman login dan admin</li>";
echo "</ol>";

echo "<p><a href='admin.php' class='btn btn-primary'>Test Login Admin</a> | <a href='index.php' class='btn btn-info'>Test Login Siswa</a></p>";
?>
