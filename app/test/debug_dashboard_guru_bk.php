<?php
// Debug script untuk dashboard guru BK
include 'koneksi.php';
session_start();

echo "<h2>Debug Dashboard Guru BK</h2>";

// Cek session
if(!isset($_SESSION['id'])) {
    echo "<p style='color: red;'>❌ Tidak ada session ID</p>";
    exit();
}

$user_id = $_SESSION['id'];
$user_level = $_SESSION['level'];
$user_nama = $_SESSION['nama'];

echo "<h3>Session Info:</h3>";
echo "<ul>";
echo "<li><strong>User ID:</strong> $user_id</li>";
echo "<li><strong>User Level:</strong> $user_level</li>";
echo "<li><strong>User Nama:</strong> $user_nama</li>";
echo "</ul>";

// Cek data user
echo "<h3>Data User:</h3>";
$query_user = "SELECT * FROM user WHERE user_id = '$user_id'";
$result_user = mysqli_query($koneksi, $query_user);
if($user = mysqli_fetch_assoc($result_user)) {
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Field</th><th>Value</th></tr>";
    foreach($user as $key => $value) {
        echo "<tr><td>$key</td><td>$value</td></tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color: red;'>❌ User tidak ditemukan</p>";
}

// Cek data kasus
echo "<h3>Data Kasus Siswa:</h3>";
$query_kasus = "SELECT k.*, s.siswa_nama, u.user_nama as guru_nama 
                FROM kasus_siswa k 
                LEFT JOIN siswa s ON k.siswa_id = s.siswa_id 
                LEFT JOIN user u ON k.guru_bk_id = u.user_id 
                ORDER BY k.kasus_id";
$result_kasus = mysqli_query($koneksi, $query_kasus);

echo "<table border='1' cellpadding='5'>";
echo "<tr><th>ID</th><th>Kode</th><th>Siswa</th><th>Guru BK ID</th><th>Guru BK Nama</th><th>Status</th></tr>";

while($kasus = mysqli_fetch_assoc($result_kasus)) {
    echo "<tr>";
    echo "<td>" . $kasus['kasus_id'] . "</td>";
    echo "<td>" . $kasus['kasus_kode'] . "</td>";
    echo "<td>" . $kasus['siswa_nama'] . "</td>";
    echo "<td>" . $kasus['guru_bk_id'] . "</td>";
    echo "<td>" . $kasus['guru_nama'] . "</td>";
    echo "<td>" . $kasus['status_kasus'] . "</td>";
    echo "</tr>";
}
echo "</table>";

// Test query dashboard
echo "<h3>Test Query Dashboard:</h3>";

$query_test = "SELECT COUNT(*) as total FROM kasus_siswa WHERE guru_bk_id = '$user_id'";
$result_test = mysqli_query($koneksi, $query_test);
$total_test = mysqli_fetch_assoc($result_test)['total'];

echo "<p><strong>Query:</strong> $query_test</p>";
echo "<p><strong>Hasil:</strong> $total_test kasus</p>";

if($total_test > 0) {
    echo "<p style='color: green;'>✅ Query berhasil - ada $total_test kasus</p>";
} else {
    echo "<p style='color: red;'>❌ Query gagal - tidak ada kasus untuk user ID $user_id</p>";
    
    // Cek apakah ada kasus dengan guru_bk_id yang berbeda
    $query_all = "SELECT guru_bk_id, COUNT(*) as total FROM kasus_siswa GROUP BY guru_bk_id";
    $result_all = mysqli_query($koneksi, $query_all);
    
    echo "<h4>Semua Guru BK ID yang ada:</h4>";
    echo "<ul>";
    while($row = mysqli_fetch_assoc($result_all)) {
        echo "<li>Guru BK ID: " . $row['guru_bk_id'] . " - Total: " . $row['total'] . " kasus</li>";
    }
    echo "</ul>";
}

echo "<p><a href='admin/guru_bk_dashboard.php'>Kembali ke Dashboard</a></p>";
?>
