<?php 
include '../koneksi.php';
include 'functions_academic_year.php';

$id  = $_POST['id'];
$nama  = $_POST['nama'];
$status = $_POST['status'];

if($status == 1){
	mysqli_query($koneksi,"update ta set ta_status='0'");
}

mysqli_query($koneksi, "update ta set ta_nama='$nama', ta_status='$status' where ta_id='$id'");

// JANGAN update status otomatis setelah user mengubah secara manual
// Biarkan status sesuai dengan yang dipilih user
// updateAcademicYearStatus($koneksi);

header("location:ta.php");