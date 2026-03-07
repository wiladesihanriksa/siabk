<?php 
include '../koneksi.php';
$nama  = $_POST['nama'];
$point  = $_POST['point'];

mysqli_query($koneksi, "insert into pelanggaran values (NULL,'$nama','$point')");
header("location:pelanggaran.php");