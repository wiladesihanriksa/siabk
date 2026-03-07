<?php 
include '../koneksi.php';
$id = $_GET['id'];

mysqli_query($koneksi, "delete from input_pelanggaran where id='$id'");

header("location:input_pelanggaran.php");
