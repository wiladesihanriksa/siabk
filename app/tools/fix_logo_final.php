<?php
// Script final untuk fix logo
echo "<h2>🔧 Fix Logo Final</h2>";

include 'koneksi.php';
include 'functions_app_settings.php';

// 1. Pastikan directory ada
echo "<h3>📁 Membuat Directory:</h3>";
if (!is_dir('gambar')) {
    if(mkdir('gambar', 0777, true)) {
        echo "<p style='color: green;'>✅ Directory 'gambar' dibuat</p>";
    } else {
        echo "<p style='color: red;'>❌ Gagal membuat directory 'gambar'</p>";
    }
} else {
    echo "<p style='color: green;'>✅ Directory 'gambar' sudah ada</p>";
}

if (!is_dir('gambar/sistem')) {
    if(mkdir('gambar/sistem', 0777, true)) {
        echo "<p style='color: green;'>✅ Directory 'gambar/sistem' dibuat</p>";
    } else {
        echo "<p style='color: red;'>❌ Gagal membuat directory 'gambar/sistem'</p>";
    }
} else {
    echo "<p style='color: green;'>✅ Directory 'gambar/sistem' sudah ada</p>";
}

// 2. Set permission
chmod('gambar', 0777);
chmod('gambar/sistem', 0777);
echo "<p style='color: green;'>✅ Permission directory diatur ke 777</p>";

// 3. Buat file placeholder jika tidak ada
echo "<h3>📄 Membuat File Placeholder:</h3>";
$placeholder_files = [
    'gambar/sistem/user.png' => 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg==',
    'gambar/sistem/logo.png' => 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg==',
    'gambar/sistem/login_logo.png' => 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg=='
];

foreach($placeholder_files as $file => $content) {
    if(!file_exists($file)) {
        $decoded = base64_decode($content);
        if(file_put_contents($file, $decoded)) {
            chmod($file, 0644);
            echo "<p style='color: orange;'>⚠️ File placeholder dibuat: {$file}</p>";
        } else {
            echo "<p style='color: red;'>❌ Gagal membuat placeholder: {$file}</p>";
        }
    } else {
        echo "<p style='color: green;'>✅ File sudah ada: {$file}</p>";
    }
}

// 4. Update database
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
            echo "<p style='color: red;'>❌ Failed to update {$key}: " . mysqli_error($koneksi) . "</p>";
        }
    } else {
        // Insert new
        $insert_query = "INSERT INTO app_settings (setting_key, setting_value, setting_type, description) VALUES ('{$key}', '{$value}', 'text', 'Logo aplikasi')";
        if(mysqli_query($koneksi, $insert_query)) {
            echo "<p style='color: green;'>✅ Inserted {$key} = {$value}</p>";
        } else {
            echo "<p style='color: red;'>❌ Failed to insert {$key}: " . mysqli_error($koneksi) . "</p>";
        }
    }
}

// 5. Test fungsi
echo "<h3>🧪 Test Functions:</h3>";
$app_settings = getAppSettings($koneksi);

echo "<p><strong>getAppLogo():</strong> " . getAppLogo($app_settings) . "</p>";
echo "<p><strong>getAppFavicon():</strong> " . getAppFavicon($app_settings) . "</p>";
echo "<p><strong>getLoginLogo():</strong> " . getLoginLogo($app_settings) . "</p>";

// 6. Test logic admin header
echo "<h3>🔍 Test Logic Admin Header:</h3>";
$logo = getAppLogo($app_settings);
echo "<p><strong>Logo dari getAppLogo():</strong> {$logo}</p>";
echo "<p><strong>Kondisi:</strong> " . ($logo != 'gambar/sistem/user.png' ? 'TRUE - Logo akan ditampilkan' : 'FALSE - Logo tidak akan ditampilkan') . "</p>";

if($logo != 'gambar/sistem/user.png') {
    echo "<p style='color: green;'>✅ Logo akan ditampilkan di admin header</p>";
} else {
    echo "<p style='color: red;'>❌ Logo tidak akan ditampilkan, akan menggunakan icon trophy</p>";
}

// 7. Test file access
echo "<h3>📁 Test File Access:</h3>";
$test_files = [
    'gambar/sistem/logo.png',
    'gambar/sistem/favicon.ico',
    'gambar/sistem/login_logo.png',
    'gambar/sistem/user.png'
];

foreach($test_files as $file) {
    if(@fopen($file, 'r')) {
        fclose(@fopen($file, 'r'));
        echo "<p style='color: green;'>✅ {$file} - Dapat diakses</p>";
    } else {
        echo "<p style='color: red;'>❌ {$file} - Tidak dapat diakses</p>";
    }
}

echo "<hr>";
echo "<h3>✅ Selesai!</h3>";
echo "<p>Logo sudah diperbaiki. Silakan:</p>";
echo "<ol>";
echo "<li><strong>Upload file logo yang benar</strong> ke folder <code>gambar/sistem/</code></li>";
echo "<li><strong>Clear cache browser</strong> (Ctrl+F5)</li>";
echo "<li><strong>Test halaman admin</strong> <a href='admin/index.php' target='_blank'>Dashboard Admin</a></li>";
echo "</ol>";

echo "<p><a href='debug_logo_admin.php' class='btn'>🔍 Debug Logo Admin</a> | <a href='admin.php' class='btn'>🔐 Test Login Admin</a></p>";
?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; }
.btn { padding: 10px 20px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; display: inline-block; margin: 5px; }
.btn:hover { background: #0056b3; }
</style>
