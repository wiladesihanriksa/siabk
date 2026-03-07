<?php 
include '../koneksi.php';
$id  = $_POST['id'];
$nama  = $_POST['nama'];
$point  = $_POST['point'];

mysqli_query($koneksi, "update prestasi set prestasi_nama='$nama', prestasi_point='$point' where prestasi_id='$id'");
header("location:prestasi.php");