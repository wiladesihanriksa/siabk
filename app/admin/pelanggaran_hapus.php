<?php 
include '../koneksi.php';
$id = $_GET['id'];

mysqli_query($koneksi, "delete from pelanggaran where pelanggaran_id='$id'");
mysqli_query($koneksi, "delete from input_pelanggaran where pelanggaran='$id'");
header("location:pelanggaran.php");