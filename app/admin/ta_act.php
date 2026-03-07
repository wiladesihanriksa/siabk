<?php 
include '../koneksi.php';
include 'functions_academic_year.php';

$nama  = $_POST['nama'];
$status = $_POST['status'];

// Jika status aktif, nonaktifkan yang lain
if($status == 1){
	mysqli_query($koneksi,"update ta set ta_status='0'");
}

mysqli_query($koneksi, "insert into ta values (NULL,'$nama ','$status')");

// Update status tahun ajaran berdasarkan tanggal saat ini
updateAcademicYearStatus($koneksi);

header("location:ta.php");