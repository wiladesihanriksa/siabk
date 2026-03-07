<?php 
include '../koneksi.php';
$id = $_GET['id'];

mysqli_query($koneksi, "delete from prestasi where prestasi_id='$id'");
mysqli_query($koneksi, "delete from input_prestasi where prestasi='$id'");
header("location:prestasi.php");