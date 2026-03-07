<?php 
// menghubungkan dengan koneksi
include 'koneksi.php';

// menangkap data yang dikirim dari form
$nis = $_POST['nis'];
$password = md5($_POST['password']);

$login = mysqli_query($koneksi, "SELECT * FROM siswa WHERE siswa_nis='$nis' AND siswa_password='$password'");
$cek = mysqli_num_rows($login);

if($cek > 0){
	session_start();
	$data = mysqli_fetch_assoc($login);
	$_SESSION['id'] = $data['siswa_id'];
	$_SESSION['nama'] = $data['siswa_nama'];
	$_SESSION['nis'] = $data['siswa_nis'];
	$_SESSION['level'] = "siswa";

	header("location:siswa/");
}else{
	header("location:index.php?alert=gagal");
}
