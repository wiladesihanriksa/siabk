<?php
// Set session untuk testing
session_start();
$_SESSION['level'] = 'administrator';
$_SESSION['id'] = 1;
$_SESSION['nama'] = 'Administrator';

echo "Session berhasil di-set!<br>";
echo "Level: " . $_SESSION['level'] . "<br>";
echo "ID: " . $_SESSION['id'] . "<br>";
echo "Nama: " . $_SESSION['nama'] . "<br>";
echo "<br>";
echo "<a href='kasus_siswa.php'>Ke Data Kasus Siswa</a><br>";
echo "<a href='kasus_siswa_tambah.php'>Ke Tambah Kasus Siswa</a><br>";
echo "<a href='index.php'>Ke Dashboard</a>";
?>
