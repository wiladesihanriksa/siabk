<?php 
include '../koneksi.php';
$id  = mysqli_real_escape_string($koneksi, $_POST['id']);

$nama  = mysqli_real_escape_string($koneksi, $_POST['nama']);
$nis = mysqli_real_escape_string($koneksi, $_POST['nis']);
$jurusan = mysqli_real_escape_string($koneksi, $_POST['jurusan']);
$pwd = $_POST['password'];
$password = md5($pwd);
$status = mysqli_real_escape_string($koneksi, $_POST['status']);

if(empty($pwd)){
	mysqli_query($koneksi, "update siswa set siswa_nama='$nama', siswa_nis='$nis', siswa_jurusan='$jurusan', siswa_status='$status' where siswa_id='$id'");
	header("location:siswa.php");
}else{
	mysqli_query($koneksi, "update siswa set siswa_nama='$nama', siswa_nis='$nis', siswa_jurusan='$jurusan', siswa_password='$password', siswa_status='$status' where siswa_id='$id'");
	header("location:siswa.php");
}
