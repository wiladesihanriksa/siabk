<?php 
include '../koneksi.php';
$id = $_GET['id'];

mysqli_query($koneksi, "delete from input_prestasi where id='$id'");

header("location:input_prestasi.php");
