<?php
// Script untuk upload dan fix logo - V2
echo "<h2>📤 Upload & Fix Logo V2</h2>";

// Pastikan directory ada dengan path yang benar
$upload_dir = 'gambar/sistem/';

// Buat direktori jika belum ada
if (!is_dir('gambar')) {
    if(mkdir('gambar', 0777, true)) {
        echo "<p style='color: green;'>✅ Directory 'gambar' dibuat</p>";
    } else {
        echo "<p style='color: red;'>❌ Gagal membuat directory 'gambar'</p>";
    }
}

if (!is_dir($upload_dir)) {
    if(mkdir($upload_dir, 0777, true)) {
        echo "<p style='color: green;'>✅ Directory '{$upload_dir}' dibuat</p>";
    } else {
        echo "<p style='color: red;'>❌ Gagal membuat directory '{$upload_dir}'</p>";
    }
} else {
    echo "<p style='color: green;'>✅ Directory '{$upload_dir}' sudah ada</p>";
}

echo "<h3>📁 Upload Logo Baru:</h3>";
?>

<form method="post" enctype="multipart/form-data">
    <div class="form-group">
        <label>Logo Aplikasi (PNG, max 2MB):</label>
        <input type="file" name="app_logo" accept="image/png" class="form-control">
        <small class="text-muted">Akan disimpan sebagai logo.png</small>
    </div>
    
    <div class="form-group">
        <label>Favicon (ICO, max 1MB):</label>
        <input type="file" name="app_favicon" accept="image/x-icon" class="form-control">
        <small class="text-muted">Akan disimpan sebagai favicon.ico</small>
    </div>
    
    <div class="form-group">
        <label>Logo Login (PNG, max 2MB):</label>
        <input type="file" name="login_logo" accept="image/png" class="form-control">
        <small class="text-muted">Akan disimpan sebagai login_logo.png</small>
    </div>
    
    <button type="submit" name="upload" class="btn btn-primary">
        <i class="fa fa-upload"></i> Upload Logo
    </button>
</form>

<?php
if(isset($_POST['upload'])) {
    include 'koneksi.php';
    
    echo "<h3>🔄 Processing Upload...</h3>";
    
    // Upload logo aplikasi
    if(isset($_FILES['app_logo']) && $_FILES['app_logo']['error'] == 0) {
        $logo_file = $upload_dir . 'logo.png';
        if(move_uploaded_file($_FILES['app_logo']['tmp_name'], $logo_file)) {
            chmod($logo_file, 0644);
            echo "<p style='color: green;'>✅ Logo aplikasi berhasil diupload ke {$logo_file}</p>";
            
            // Update database
            $update_query = "UPDATE app_settings SET setting_value = 'logo.png' WHERE setting_key = 'app_logo'";
            if(!mysqli_query($koneksi, $update_query)) {
                $insert_query = "INSERT INTO app_settings (setting_key, setting_value, setting_type, description) VALUES ('app_logo', 'logo.png', 'text', 'Logo aplikasi')";
                mysqli_query($koneksi, $insert_query);
            }
        } else {
            echo "<p style='color: red;'>❌ Gagal upload logo aplikasi</p>";
        }
    }
    
    // Upload favicon
    if(isset($_FILES['app_favicon']) && $_FILES['app_favicon']['error'] == 0) {
        $favicon_file = $upload_dir . 'favicon.ico';
        if(move_uploaded_file($_FILES['app_favicon']['tmp_name'], $favicon_file)) {
            chmod($favicon_file, 0644);
            echo "<p style='color: green;'>✅ Favicon berhasil diupload ke {$favicon_file}</p>";
            
            // Update database
            $update_query = "UPDATE app_settings SET setting_value = 'favicon.ico' WHERE setting_key = 'app_favicon'";
            if(!mysqli_query($koneksi, $update_query)) {
                $insert_query = "INSERT INTO app_settings (setting_key, setting_value, setting_type, description) VALUES ('app_favicon', 'favicon.ico', 'text', 'Favicon aplikasi')";
                mysqli_query($koneksi, $insert_query);
            }
        } else {
            echo "<p style='color: red;'>❌ Gagal upload favicon</p>";
        }
    }
    
    // Upload logo login
    if(isset($_FILES['login_logo']) && $_FILES['login_logo']['error'] == 0) {
        $login_logo_file = $upload_dir . 'login_logo.png';
        if(move_uploaded_file($_FILES['login_logo']['tmp_name'], $login_logo_file)) {
            chmod($login_logo_file, 0644);
            echo "<p style='color: green;'>✅ Logo login berhasil diupload ke {$login_logo_file}</p>";
            
            // Update database
            $update_query = "UPDATE app_settings SET setting_value = 'login_logo.png' WHERE setting_key = 'login_logo'";
            if(!mysqli_query($koneksi, $update_query)) {
                $insert_query = "INSERT INTO app_settings (setting_key, setting_value, setting_type, description) VALUES ('login_logo', 'login_logo.png', 'text', 'Logo halaman login')";
                mysqli_query($koneksi, $insert_query);
            }
        } else {
            echo "<p style='color: red;'>❌ Gagal upload logo login</p>";
        }
    }
    
    echo "<hr>";
    echo "<h3>✅ Upload Selesai!</h3>";
    
    // Test fungsi setelah upload
    include 'functions_app_settings.php';
    $app_settings = getAppSettings($koneksi);
    
    echo "<h3>🧪 Test Functions Setelah Upload:</h3>";
    echo "<p><strong>getAppLogo():</strong> " . getAppLogo($app_settings) . "</p>";
    echo "<p><strong>getAppFavicon():</strong> " . getAppFavicon($app_settings) . "</p>";
    echo "<p><strong>getLoginLogo():</strong> " . getLoginLogo($app_settings) . "</p>";
    
    echo "<p>Silakan:</p>";
    echo "<ol>";
    echo "<li><strong>Clear cache browser</strong> (Ctrl+F5 atau Cmd+Shift+R)</li>";
    echo "<li><strong>Test halaman login</strong> <a href='admin.php' target='_blank'>Admin</a> | <a href='index.php' target='_blank'>Siswa</a></li>";
    echo "<li><strong>Test halaman admin</strong> <a href='admin/index.php' target='_blank'>Dashboard Admin</a></li>";
    echo "</ol>";
    
    echo "<div style='background: #f0f8ff; padding: 15px; border-left: 4px solid #007bff; margin: 20px 0;'>";
    echo "<h4>💡 Tips:</h4>";
    echo "<ul>";
    echo "<li>Jika logo masih belum muncul, coba <strong>hard refresh</strong> (Ctrl+Shift+R)</li>";
    echo "<li>Pastikan file logo memiliki format yang benar (PNG untuk logo, ICO untuk favicon)</li>";
    echo "<li>Ukuran file tidak terlalu besar (max 2MB untuk logo, 1MB untuk favicon)</li>";
    echo "<li>File akan disimpan di: <code>{$upload_dir}</code></li>";
    echo "</ul>";
    echo "</div>";
}
?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; }
.form-group { margin-bottom: 15px; }
.btn { padding: 10px 20px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; }
.btn:hover { background: #0056b3; }
.form-control { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; }
</style>
