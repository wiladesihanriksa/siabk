<?php 
include '../koneksi.php';
$siswa  = $_GET['siswa'];
$kelas  = $_GET['kelas'];

mysqli_query($koneksi, "insert into kelas_siswa values (NULL,'$siswa','$kelas')");
header("location:kelas_siswa.php?id=$kelas");