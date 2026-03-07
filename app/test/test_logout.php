<?php
// Test script untuk melihat redirect logout
echo "<h2>Test Logout Redirect</h2>";

echo "<h3>File Logout yang Ada:</h3>";
echo "<ul>";
echo "<li><strong>admin/logout.php</strong> → " . file_get_contents('admin/logout.php') . "</li>";
echo "<li><strong>siswa/logout.php</strong> → " . file_get_contents('siswa/logout.php') . "</li>";
echo "</ul>";

echo "<h3>Test Redirect:</h3>";
echo "<p><a href='admin/logout.php'>Test Admin Logout</a></p>";
echo "<p><a href='siswa/logout.php'>Test Siswa Logout</a></p>";

echo "<h3>Hasil yang Diharapkan:</h3>";
echo "<ul>";
echo "<li>✅ Admin logout → redirect ke index.php?alert=logout</li>";
echo "<li>✅ Siswa logout → redirect ke index.php?alert=logout</li>";
echo "<li>✅ Guru BK logout → redirect ke index.php?alert=logout (menggunakan admin/logout.php)</li>";
echo "</ul>";

echo "<p><a href='index.php'>Kembali ke Beranda</a></p>";
?>
