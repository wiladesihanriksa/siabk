<?php
$DB_HOST = 'localhost';           // atau 'sqlXXX.yourhost.com'
$DB_USER = 'root';   // user MySQL di hosting
$DB_PASS = '';       // password MySQL di hosting
$DB_NAME = 'siabk'; // nama DB di hosting
$DB_PORT = 3306;                  // ubah jika host memberi port lain

// Gunakan zona waktu WIB (GMT+7) dan locale tanggal Indonesia
date_default_timezone_set('Asia/Jakarta');
setlocale(LC_TIME, 'id_ID.UTF-8', 'id_ID', 'id');

$koneksi = mysqli_init();
mysqli_options($koneksi, MYSQLI_OPT_CONNECT_TIMEOUT, 10);
if (!mysqli_real_connect($koneksi, $DB_HOST, $DB_USER, $DB_PASS, $DB_NAME, $DB_PORT)) {
  die('Koneksi database gagal: ' . mysqli_connect_error());
}
// Set charset ke UTF-8 untuk mendukung karakter khusus seperti apostrof
mysqli_set_charset($koneksi, "utf8");
?>