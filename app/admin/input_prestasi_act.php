<?php 
include '../koneksi.php';
$kelas  = $_POST['kelas'];
$tanggal  = $_POST['tanggal'];
$jam  = $_POST['jam'];
$siswa = $_POST['siswa'];
$prestasi = $_POST['prestasi'];

// echo $jam;
$waktu = date("Y-m-d", strtotime($tanggal)) . " " . date("H:i:s", strtotime($jam));
echo $waktu;
mysqli_query($koneksi, "insert into input_prestasi values (NULL,'$waktu','$siswa','$kelas','$prestasi')");
header("location:input_prestasi.php");