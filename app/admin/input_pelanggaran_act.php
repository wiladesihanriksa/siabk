<?php 
include '../koneksi.php';
$kelas  = $_POST['kelas'];
$tanggal  = $_POST['tanggal'];
$jam  = $_POST['jam'];
$siswa = $_POST['siswa'];
$pelanggaran = $_POST['pelanggaran'];

// echo $jam;
$waktu = date("Y-m-d", strtotime($tanggal)) . " " . date("H:i:s", strtotime($jam));

mysqli_query($koneksi, "insert into input_pelanggaran values (NULL,'$waktu','$siswa','$kelas','$pelanggaran')");
header("location:input_pelanggaran.php");