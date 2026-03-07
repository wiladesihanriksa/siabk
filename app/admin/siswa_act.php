<?php 
include '../koneksi.php';
$nama  = mysqli_real_escape_string($koneksi, $_POST['nama']);
$nis = mysqli_real_escape_string($koneksi, $_POST['nis']);
$jurusan = mysqli_real_escape_string($koneksi, $_POST['jurusan']);
$password = md5($_POST['password']);
$status = mysqli_real_escape_string($koneksi, $_POST['status']);

mysqli_query($koneksi, "INSERT INTO siswa (siswa_nama, siswa_nis, siswa_jurusan, siswa_status, siswa_password) VALUES ('$nama', '$nis', '$jurusan', '$status', '$password')");
header("location:siswa.php");