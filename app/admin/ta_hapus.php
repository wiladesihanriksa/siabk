<?php 
include '../koneksi.php';
$id = $_GET['id'];

mysqli_query($koneksi, "delete from ta where ta_id='$id'");

// Hapus kelas yang terkait dengan tahun ajaran ini
$x = mysqli_query($koneksi, "select * from kelas where kelas_ta='$id'");
while($xx = mysqli_fetch_array($x)){
	$id_kelas = $xx['kelas_id']; // Perbaikan: gunakan kelas_id bukan siswa_id
	
	// Hapus data terkait kelas
	mysqli_query($koneksi, "delete from input_prestasi where kelas='$id_kelas'");
	mysqli_query($koneksi, "delete from input_pelanggaran where kelas='$id_kelas'");
	mysqli_query($koneksi, "delete from kelas_siswa where ks_kelas='$id_kelas'");
}

// Hapus kelas setelah data terkait dihapus
mysqli_query($koneksi, "delete from kelas where kelas_ta='$id'");




header("location:ta.php");
