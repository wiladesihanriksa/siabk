<?php 
include '../koneksi.php';
$id  = $_POST['id'];
$nama  = $_POST['nama'];
$point  = $_POST['point'];

mysqli_query($koneksi, "update pelanggaran set pelanggaran_nama='$nama', pelanggaran_point='$point' where pelanggaran_id='$id'");
header("location:pelanggaran.php");