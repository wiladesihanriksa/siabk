<?php
include 'koneksi.php';

echo "<h2>Instalasi Fitur Layanan BK</h2>";

// Check if tables already exist
$check_layanan = mysqli_query($koneksi, "SHOW TABLES LIKE 'layanan_bk'");
$check_peserta = mysqli_query($koneksi, "SHOW TABLES LIKE 'layanan_bk_peserta'");

if(mysqli_num_rows($check_layanan) > 0 && mysqli_num_rows($check_peserta) > 0) {
    echo "<p style='color: orange;'>⚠️ Tabel layanan BK sudah ada. Apakah Anda ingin menginstall ulang?</p>";
    echo "<a href='?reinstall=1' style='background: red; color: white; padding: 5px 10px; text-decoration: none;'>Reinstall</a>";
    
    if(!isset($_GET['reinstall'])) {
        exit();
    }
}

// Read SQL file
$sql_file = 'sql_layanan_bk.sql';
if(!file_exists($sql_file)) {
    echo "<p style='color: red;'>❌ File sql_layanan_bk.sql tidak ditemukan!</p>";
    exit();
}

$sql = file_get_contents($sql_file);

// Split SQL into individual statements
$statements = array_filter(array_map('trim', explode(';', $sql)));

echo "<h3>Menjalankan SQL Statements:</h3>";

$success_count = 0;
$error_count = 0;

foreach($statements as $statement) {
    if(!empty($statement) && !preg_match('/^--/', $statement)) {
        echo "<p>Executing: " . substr($statement, 0, 80) . "...</p>";
        
        if(mysqli_query($koneksi, $statement)) {
            echo "<p style='color: green;'>✅ Success</p>";
            $success_count++;
        } else {
            echo "<p style='color: red;'>❌ Error: " . mysqli_error($koneksi) . "</p>";
            $error_count++;
        }
    }
}

echo "<h3>Hasil Instalasi:</h3>";
echo "<p style='color: green;'>✅ Berhasil: $success_count statements</p>";
echo "<p style='color: red;'>❌ Error: $error_count statements</p>";

if($error_count == 0) {
    echo "<h3 style='color: green;'>🎉 Instalasi Berhasil!</h3>";
    echo "<p>Fitur Layanan BK sudah siap digunakan.</p>";
    echo "<p><a href='admin/layanan_bk.php'>Klik di sini untuk mengakses fitur Layanan BK</a></p>";
} else {
    echo "<h3 style='color: red;'>⚠️ Ada error dalam instalasi</h3>";
    echo "<p>Silakan periksa error di atas dan jalankan ulang.</p>";
}

// Create uploads directory if not exists
$uploads_dir = 'uploads/layanan/';
if (!is_dir($uploads_dir)) {
    if (mkdir($uploads_dir, 0755, true)) {
        echo "<p style='color: green;'>✅ Folder uploads/layanan/ berhasil dibuat</p>";
    } else {
        echo "<p style='color: red;'>❌ Gagal membuat folder uploads/layanan/</p>";
    }
} else {
    echo "<p style='color: green;'>✅ Folder uploads/layanan/ sudah ada</p>";
}

echo "<hr>";
echo "<p><strong>File yang perlu diupload ke server:</strong></p>";
echo "<ul>";
echo "<li>sql_layanan_bk.sql</li>";
echo "<li>functions_academic_year.php</li>";
echo "<li>functions_app_settings.php</li>";
echo "<li>admin/ (semua file layanan_bk*.php)</li>";
echo "<li>siswa/ (file layanan BK siswa)</li>";
echo "<li>library/fpdf185/ (jika belum ada)</li>";
echo "</ul>";
?>