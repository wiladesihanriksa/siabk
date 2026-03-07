<?php 
include '../koneksi.php';
$id  = $_POST['id'];

$kelas  = $_POST['kelas'];
$tanggal  = $_POST['tanggal'];
$jam  = $_POST['jam'];
$siswa = $_POST['siswa'];
$prestasi = $_POST['prestasi'];

// echo $jam;
$waktu = date("Y-m-d", strtotime($tanggal)) . " " . date("H:i:s", strtotime($jam));

mysqli_query($koneksi, "update input_prestasi set waktu='$waktu', siswa='$siswa', kelas='$kelas', prestasi='$prestasi' where id='$id'");
header("location:input_prestasi.php");